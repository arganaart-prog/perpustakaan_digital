<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { background: #059669; color: white; padding: 20px; border-radius: 10px 10px 0 0; text-align: center; }
        .content { padding: 30px; }
        .footer { text-align: center; font-size: 12px; color: #999; margin-top: 20px; }
        .button { display: inline-block; padding: 12px 24px; background: #059669; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .details { background: #f9fafb; padding: 20px; border-radius: 10px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Buku Siap Diambil!</h2>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $bookQueue->user->name }}</strong>,</p>
            <p>Kabar gembira! Buku yang Anda antre sudah tersedia dan siap untuk diambil di perpustakaan.</p>
            
            <div class="details">
                <p><strong>Judul Buku:</strong> {{ $bookQueue->book->title }}</p>
                <p><strong>Kode Buku:</strong> {{ $bookQueue->book->code }}</p>
                <p><strong>Batas Waktu Pengambilan:</strong> {{ $bookQueue->deadline ? $bookQueue->deadline->format('d-M-Y H:i') : '-' }}</p>
            </div>

            <p>Mohon segera datang ke perpustakaan sebelum batas waktu berakhir. Jika melewati batas waktu, antrean Anda akan otomatis dibatalkan.</p>
            
            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/member/books') }}" class="button">Lihat Koleksi Buku</a>
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Skarifta Perpus. Sistem Perpustakaan Digital.</p>
        </div>
    </div>
</body>
</html>
