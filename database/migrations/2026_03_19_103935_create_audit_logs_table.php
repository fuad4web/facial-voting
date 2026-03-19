<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // 'vote_attempt', 'login_attempt', 'face_verify'
            $table->string('status'); // 'success', 'failure'
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('fingerprint')->nullable();
            $table->json('metadata')->nullable(); // additional data (category_id, candidate_id, similarity)
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
