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
        Schema::table('banners', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable()->after('button_link'); // Número WhatsApp ej: 51999000000
            $table->string('contact_email')->nullable()->after('whatsapp_number');
            $table->string('contact_address')->nullable()->after('contact_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'contact_email', 'contact_address']);
        });
    }
};
