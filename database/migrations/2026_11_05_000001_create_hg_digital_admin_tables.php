<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_super_admin')->default(false);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('is_admin')->constrained()->nullOnDelete();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('discount_type')->default('percentage');
            $table->decimal('value', 15, 2);
            $table->decimal('max_discount', 15, 2)->nullable();
            $table->decimal('minimum_order', 15, 2)->default(0);
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_limit_per_customer')->default(1);
            $table->unsignedInteger('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('scope')->default('all');
            $table->timestamps();
        });

        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->string('alt_text')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();
        });

        Schema::create('content_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('content_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('content_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->foreignId('content_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('content_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
        });

        Schema::create('site_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('path')->nullable()->unique();
            $table->json('content')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_pages');

        Schema::table('blogs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('content_category_id');
            $table->dropConstrainedForeignId('content_type_id');
            $table->dropColumn(['status', 'published_at', 'seo_title', 'seo_description']);
        });

        Schema::dropIfExists('content_categories');
        Schema::dropIfExists('content_types');
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('coupons');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('roles');
    }
};
