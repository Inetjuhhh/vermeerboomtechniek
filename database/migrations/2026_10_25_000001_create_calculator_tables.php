<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A service in the quote calculator, e.g. "Boom verwijderen"
        Schema::create('calculator_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->boolean('asks_tree_count')->default(true);
            // Storm damage: no estimate, only "call us"
            $table->boolean('contact_only')->default(false);
            $table->text('contact_message')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // How the price is built up. A service with several methods shows an estimate for each.
        Schema::create('calculator_price_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculator_service_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('base_min', 10, 2)->default(0);
            $table->decimal('base_max', 10, 2)->default(0);
            $table->decimal('per_tree_min', 10, 2)->default(0);
            $table->decimal('per_tree_max', 10, 2)->default(0);
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->decimal('hours_min', 6, 2)->default(0);
            $table->decimal('hours_max', 6, 2)->default(0);
            $table->decimal('hours_per_tree_min', 6, 2)->default(0);
            $table->decimal('hours_per_tree_max', 6, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('calculator_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculator_service_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('hint', 500)->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // An answer and what it does to the price (see App\Enums\OptionEffect)
        Schema::create('calculator_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculator_question_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('effect')->default('none');
            $table->decimal('factor', 5, 2)->nullable();
            $table->decimal('amount_min', 10, 2)->nullable();
            $table->decimal('amount_max', 10, 2)->nullable();
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Costs on top of the work, e.g. voorrijkosten
        Schema::create('calculator_other_costs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('amount_min', 10, 2)->default(0);
            $table->decimal('amount_max', 10, 2)->default(0);
            $table->boolean('per_tree')->default(false);
            $table->boolean('applies_to_all')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('calculator_other_cost_service', function (Blueprint $table) {
            $table->foreignId('calculator_other_cost_id')->constrained()->cascadeOnDelete();
            $table->foreignId('calculator_service_id')->constrained()->cascadeOnDelete();
            $table->primary(['calculator_other_cost_id', 'calculator_service_id']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->json('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('calculator_other_cost_service');
        Schema::dropIfExists('calculator_other_costs');
        Schema::dropIfExists('calculator_options');
        Schema::dropIfExists('calculator_questions');
        Schema::dropIfExists('calculator_price_models');
        Schema::dropIfExists('calculator_services');
    }
};
