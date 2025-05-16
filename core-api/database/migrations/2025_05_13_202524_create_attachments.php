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
        Schema::create('attachments', function (Blueprint $t) {
            $t->id();
            $t->string('upload_key')->unique();
            $t->enum('kind', [
                'image','video','file','audio','voice','gif','circle'
            ]);
            $t->string('status')->default('init');      // init|completed|failed
            $t->unsignedBigInteger('size')->nullable();
            $t->string('mime')->nullable();
            $t->json('meta')->nullable();
            $t->string('path')->nullable();             // chat-files/<key>
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
