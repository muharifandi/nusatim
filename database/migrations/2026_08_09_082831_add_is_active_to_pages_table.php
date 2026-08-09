<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('slug');
        });

        // Explicit backfill rather than relying on the column DEFAULT alone
        // to backfill pre-existing rows - observed inconsistent behavior in
        // testing (existing rows ended up with is_active=0 instead of the
        // declared default=true, silently 404ing every already-live page
        // except the one row that happened to get 1). Belt-and-suspenders
        // fix so a fresh deploy can never 404 the whole site on this alone.
        DB::table('pages')->update(['is_active' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
