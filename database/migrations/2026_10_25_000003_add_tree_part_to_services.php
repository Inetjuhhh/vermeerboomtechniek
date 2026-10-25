<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Where in the tree the work happens: kroon, stam or wortels (see App\Enums\TreePart)
        Schema::table('services', function (Blueprint $table) {
            $table->string('tree_part')->default('kroon')->after('summary');
        });

        foreach (['kroon' => ['snoeien', 'rooien', 'stormschade'], 'stam' => ['veiligheidscontrole', 'boomverankering'],
            'wortels' => ['planten', 'boomtechnisch-advies', 'boom-effect-analyse']] as $part => $slugs) {
            DB::table('services')->whereIn('slug', $slugs)->update(['tree_part' => $part]);
        }
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('tree_part');
        });
    }
};
