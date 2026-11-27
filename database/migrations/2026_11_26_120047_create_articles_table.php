<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->uuid('translation_group_id');
            $table->string('locale', 5);
            $table->string('slug');
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body_html')->nullable();
            $table->longText('body_rendered')->nullable();
            $table->json('toc')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedSmallInteger('reading_time_min')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->boolean('is_machine_translated')->default(false);
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('translated_from_id')->nullable()->constrained('articles')->nullOnDelete();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedBigInteger('like_count')->default(0);
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['locale', 'slug']);
            $table->unique(['translation_group_id', 'locale']);
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
