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
        Schema::table('legal_pages', function (Blueprint $table) {
            // 'policy' = kebijakan/syarat & ketentuan biasa (tampilan artikel
            // sederhana). 'document' = dokumen resmi berkekuatan hukum
            // (kontrak kerja sama dsb) - tampil dengan kop surat (logo +
            // alamat + nomor surat) dan bisa diekspor PDF dengan layout sama.
            $table->string('type')->default('policy')->after('slug');
            $table->string('document_number')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legal_pages', function (Blueprint $table) {
            $table->dropColumn(['type', 'document_number']);
        });
    }
};
