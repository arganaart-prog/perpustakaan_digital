<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Display member's chat conversations list & student search.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $q = trim((string) $request->get('q', ''));

        // Active conversations of user
        $conversations = ChatConversation::where(function ($query) use ($user) {
            $query->where('user_one_id', $user->id)
                  ->orWhere('user_two_id', $user->id);
        })
        ->with(['userOne', 'userTwo', 'latestMessage'])
        ->orderByDesc('last_message_at')
        ->get();

        // Search students
        $students = collect();
        if ($q !== '') {
            $students = User::role('member')
                ->where('id', '!=', $user->id)
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                          ->orWhere('kelas', 'like', "%{$q}%")
                          ->orWhere('jurusan', 'like', "%{$q}%");
                })
                ->take(10)
                ->get();
        }

        return view('member.chats.index', compact('conversations', 'students', 'q'));
    }

    /**
     * Start or open a conversation with another student.
     * Includes automatic first template intro.
     */
    public function startWithUser(User $targetUser)
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $targetUser->id) {
            return redirect()->route('member.chats.index')->withErrors(['chat' => 'Tidak dapat mengirim pesan ke diri sendiri.']);
        }

        if (!$targetUser->hasRole('member')) {
            return redirect()->route('member.chats.index')->withErrors(['chat' => 'Chat saat ini hanya tersedia antar siswa.']);
        }

        // Find existing conversation
        $conversation = ChatConversation::where(function ($q) use ($currentUser, $targetUser) {
            $q->where('user_one_id', $currentUser->id)->where('user_two_id', $targetUser->id);
        })->orWhere(function ($q) use ($currentUser, $targetUser) {
            $q->where('user_one_id', $targetUser->id)->where('user_two_id', $currentUser->id);
        })->first();

        if (!$conversation) {
            DB::transaction(function () use ($currentUser, $targetUser, &$conversation) {
                $conversation = ChatConversation::create([
                    'user_one_id' => $currentUser->id,
                    'user_two_id' => $targetUser->id,
                    'starter_id' => $currentUser->id,
                    'is_accepted' => false,
                    'last_message_at' => now(),
                ]);

                // Auto template first message
                $introText = "Halo, perkenalkan aku {$currentUser->name}" . ($currentUser->kelas ? " dari kelas {$currentUser->kelas} ({$currentUser->jurusan})" : "") . " 👋";

                ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $currentUser->id,
                    'receiver_id' => $targetUser->id,
                    'message' => $introText,
                    'is_read' => false,
                ]);
            });
        }

        return redirect()->route('member.chats.show', $conversation);
    }

    /**
     * Display a specific chat conversation.
     */
    public function show(ChatConversation $conversation)
    {
        $currentUser = Auth::user();

        if ($conversation->user_one_id !== $currentUser->id && $conversation->user_two_id !== $currentUser->id) {
            abort(403, 'Akses obrolan tidak diizinkan.');
        }

        // Mark unread messages as read
        ChatMessage::where('conversation_id', $conversation->id)
            ->where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $otherUser = $conversation->getOtherUser($currentUser);
        $messages = $conversation->messages()->with('sender')->get();
        $canSend = $conversation->canSendMessage($currentUser);

        return view('member.chats.show', compact('conversation', 'otherUser', 'messages', 'canSend'));
    }

    /**
     * Send a message.
     */
    public function sendMessage(Request $request, ChatConversation $conversation)
    {
        $currentUser = Auth::user();

        if ($conversation->user_one_id !== $currentUser->id && $conversation->user_two_id !== $currentUser->id) {
            abort(403);
        }

        if (!$conversation->canSendMessage($currentUser)) {
            return back()->withErrors(['message' => 'Menunggu balasan dari lawan bicara sebelum dapat mengirim pesan berikutnya.']);
        }

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $otherUser = $conversation->getOtherUser($currentUser);

        DB::transaction(function () use ($conversation, $currentUser, $otherUser, $request) {
            ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $currentUser->id,
                'receiver_id' => $otherUser->id,
                'message' => trim($request->message),
                'is_read' => false,
            ]);

            // If receiver replied to starter, unlock conversation
            if ($conversation->starter_id !== $currentUser->id && !$conversation->is_accepted) {
                $conversation->is_accepted = true;
            }

            $conversation->last_message_at = now();
            $conversation->save();
        });

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    /**
     * JSON polling endpoint for live messages.
     */
    public function fetchMessages(ChatConversation $conversation)
    {
        $currentUser = Auth::user();

        if ($conversation->user_one_id !== $currentUser->id && $conversation->user_two_id !== $currentUser->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mark incoming messages as read
        ChatMessage::where('conversation_id', $conversation->id)
            ->where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $messages = $conversation->messages()
            ->with('sender')
            ->get()
            ->map(function ($msg) use ($currentUser) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'is_mine' => $msg->sender_id === $currentUser->id,
                    'sender_name' => $msg->sender->name,
                    'time' => $msg->created_at->format('H:i'),
                    'is_read' => $msg->is_read,
                ];
            });

        return response()->json([
            'messages' => $messages,
            'can_send' => $conversation->canSendMessage($currentUser),
            'is_accepted' => $conversation->is_accepted,
        ]);
    }

    /**
     * Librarian & Admin Monitoring Dashboard.
     */
    public function monitoringIndex(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $conversations = ChatConversation::with(['userOne', 'userTwo', 'latestMessage'])
            ->withCount('messages')
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('userOne', fn($u) => $u->where('name', 'like', "%{$q}%"))
                      ->orWhereHas('userTwo', fn($u) => $u->where('name', 'like', "%{$q}%"));
            })
            ->orderByDesc('last_message_at')
            ->paginate(15);

        return view('petugas.chats.monitoring-index', compact('conversations', 'q'));
    }

    /**
     * Librarian & Admin Chat Transcript View.
     */
    public function monitoringShow(ChatConversation $conversation)
    {
        $conversation->load(['userOne', 'userTwo']);
        $messages = $conversation->messages()->with(['sender', 'receiver'])->get();

        return view('petugas.chats.monitoring-show', compact('conversation', 'messages'));
    }

    /**
     * Delete an inappropriate message by Librarian/Admin.
     */
    public function deleteMessage(ChatMessage $message)
    {
        $conversationId = $message->conversation_id;
        $message->delete();

        return back()->with('success', 'Pesan berhasil dihapus oleh moderator.');
    }
}
