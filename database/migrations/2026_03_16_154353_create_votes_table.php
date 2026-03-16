<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamp('voted_at')->useCurrent();
            
            // Ensure one vote per user per category
            $table->unique(['user_id', 'category_id'], 'votes_user_category_unique');
            
            // Index for faster counting
            $table->index(['category_id', 'candidate_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('votes');
    }
};
