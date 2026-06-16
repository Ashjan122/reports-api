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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('lab_name')->nullable();
            $table->string('lab_address')->nullable();
            $table->string('lab_phone')->nullable();
            $table->string('lab_email')->nullable();
            $table->string('header_image')->nullable();
            $table->string('footer_image')->nullable();
            $table->string('stamp_image')->nullable();
            $table->string('signature_image')->nullable();
            $table->string('authorized_name')->nullable();
            $table->string('authorized_title')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
