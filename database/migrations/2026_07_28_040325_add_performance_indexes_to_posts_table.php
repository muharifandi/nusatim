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
        Schema::table('posts', function (Blueprint $table) {
            // Covers scopePublished()'s WHERE is_published + ORDER BY
            // published_at - the query every blog page runs.
            $table->index(['is_published', 'published_at'], 'posts_published_index');
            // Covers the category filter and the distinct-category lookup.
            $table->index('category', 'posts_category_index');
            // Covers the featured-post lookup on the blog index.
            $table->index('is_featured', 'posts_is_featured_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_published_index');
            $table->dropIndex('posts_category_index');
            $table->dropIndex('posts_is_featured_index');
        });
    }
};
