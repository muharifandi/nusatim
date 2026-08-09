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
            // Google Search Console property identifier - either a
            // URL-prefix property ("https://nusatim.com/") or a
            // domain property ("sc-domain:nusatim.com"). Used to build the
            // "inspect this URL in Search Console" deep link on Post's edit
            // page (see PostResource\Pages\EditPost).
            $table->string('search_console_resource_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('search_console_resource_id');
        });
    }
};
