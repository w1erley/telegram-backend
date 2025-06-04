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
        Schema::create('messages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('chat_id')->constrained()->cascadeOnDelete();
            $t->foreignId('sender_id')->constrained('users');
            $t->enum('type', [
                'plain','media','file','audio','voice','gif','circle'
            ])->default('plain');

            $t->longText('body')->nullable();
            $t->longText('caption')->nullable();
            $t->foreignId('reply_to_id')->nullable()
                ->constrained('messages')->nullOnDelete();

            $t->unsignedBigInteger('thread_root_id')->nullable();
            $t->timestamp('pinned_at')->nullable();
            $t->timestamp('edited_at')->nullable();
            $t->timestamp('deleted_at')->nullable();
            $t->timestamps();

            // Індекси для швидкої стрічки
            $t->index(['chat_id','created_at']);
            $t->index('thread_root_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
