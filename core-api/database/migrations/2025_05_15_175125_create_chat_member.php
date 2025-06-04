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
        Schema::create('chat_member', function (Blueprint $t) {
            $t->id();
            $t->foreignId('chat_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->enum('role', ['owner','admin','member'])->default('member');
            $t->json('permissions')->nullable();
            $t->boolean('is_muted')->default(false);
            $t->timestamp('joined_at')->useCurrent();
            $t->unique(['chat_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_member');
    }
};
