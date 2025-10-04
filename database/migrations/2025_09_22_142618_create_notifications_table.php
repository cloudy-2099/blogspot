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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();                // ID unik pakai UUID
            $table->string('type');                      // class notif, contoh App\Notifications\PostLikedNotification
            $table->morphs('notifiable');                // notifiable_type + notifiable_id (penerima notif)
            $table->text('data');                        // isi notif dalam bentuk JSON
            $table->timestamp('read_at')->nullable();    // kapan notif dibaca
            $table->timestamps();                        // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
