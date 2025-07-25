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
            $table->uuid('id')->primary(); // Laravel uses UUIDs for notifications
            $table->morphs('notifiable'); // Creates notifiable_type + notifiable_id
            $table->string('type'); // Notification class path
            $table->string('title')->nullable(); // Optional short title
            $table->json('data')->nullable(); // Optional: related payload (e.g. invoice_id)
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
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
