<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A service with its own page, e.g. /diensten/snoeien
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('summary', 300);
            $table->text('intro')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->foreignId('calculator_service_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('show_on_home')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Free pages like "Het bedrijf"
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('intro')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->text('quote');
            $table->string('name')->nullable();
            $table->string('place')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbreviation')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('show_in_timeline')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Messages from the contact form, optionally with the answers from the quote calculator
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->json('calculator_summary')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('services');
    }
};
