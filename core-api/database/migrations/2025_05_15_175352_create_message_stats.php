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
        Schema::create('message_stats', function (Blueprint $t) {
            $t->foreignId('message_id')->constrained('messages')->cascadeOnDelete();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $t->timestamp('read_at')->nullable();
            $t->string('reaction', 16)->nullable();
            $t->timestamp('reacted_at')->nullable();

            $t->primary(['message_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_stats');
    }
};
