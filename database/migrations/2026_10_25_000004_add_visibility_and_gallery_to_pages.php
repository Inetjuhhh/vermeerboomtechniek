<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // Off: the page gives a 404 and all links to it disappear
            $table->boolean('is_published')->default(true)->after('meta_description');
            // On: link in the main menu and footer, e.g. the optional Houtbewerking page
            $table->boolean('show_in_menu')->default(false)->after('is_published');
            $table->string('menu_label', 40)->nullable()->after('show_in_menu');
            // Photos below the text: [{image, caption, credit}]
            $table->json('gallery')->nullable()->after('image_alt');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['is_published', 'show_in_menu', 'menu_label', 'gallery']);
        });
    }
};
