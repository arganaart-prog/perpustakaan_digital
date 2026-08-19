<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->string('punishment_type')->nullable()->after('fine_paid_at'); // 'fine', 'social'
            $table->string('fine_type')->default('late')->after('punishment_type'); // 'late', 'lost'
            $table->string('payment_method')->nullable()->after('fine_type'); // 'cash', 'transfer'
            $table->string('payment_proof')->nullable()->after('payment_method');
            $table->string('payment_status')->default('unpaid')->after('payment_proof'); // 'unpaid', 'pending_verification', 'paid'
            $table->text('late_reason')->nullable()->after('payment_status');
            $table->string('late_evidence')->nullable()->after('late_reason');
            $table->text('social_punishment_description')->nullable()->after('late_evidence');
            $table->string('social_punishment_status')->nullable()->after('social_punishment_description'); // 'assigned', 'completed'
            $table->timestamp('social_punishment_completed_at')->nullable()->after('social_punishment_status');
        });

        Schema::table('summaries', function (Blueprint $table) {
            $table->integer('late_days')->default(0)->after('review_note');
            $table->integer('extra_pages_required')->default(0)->after('late_days');
            $table->text('late_reason')->nullable()->after('extra_pages_required');
            $table->string('late_evidence')->nullable()->after('late_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropColumn([
                'punishment_type',
                'fine_type',
                'payment_method',
                'payment_proof',
                'payment_status',
                'late_reason',
                'late_evidence',
                'social_punishment_description',
                'social_punishment_status',
                'social_punishment_completed_at',
            ]);
        });

        Schema::table('summaries', function (Blueprint $table) {
            $table->dropColumn([
                'late_days',
                'extra_pages_required',
                'late_reason',
                'late_evidence',
            ]);
        });
    }
};
