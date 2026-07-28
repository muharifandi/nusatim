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
        Schema::table('site_settings', function (Blueprint $table) {
            // Real Google Maps "Embed a map" URLs encode a lot of state in
            // their `pb=` query parameter and routinely exceed 255 chars -
            // string() was truncating/erroring on real-world values.
            $table->text('google_maps_embed_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('google_maps_embed_url')->nullable()->change();
        });
    }
};
