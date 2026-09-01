<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('eyebrow')->nullable();
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->json('scope_groups')->nullable();
            $table->json('devices')->nullable();
            $table->json('process_steps')->nullable();
            $table->json('faq')->nullable();
            $table->string('cta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('technical_services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->json('devices')->nullable();
            $table->json('service_steps')->nullable();
            $table->json('advantages')->nullable();
            $table->json('faq')->nullable();
            $table->string('cta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->string('image')->nullable();
            $table->json('aliases')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->string('logo')->nullable();
            $table->json('aliases')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_category_brand', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_brand_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_category_id', 'product_brand_id'], 'category_brand_unique');
        });

        Schema::create('product_category_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_category_id', 'service_id'], 'category_service_unique');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_brand_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('model')->nullable();
            $table->string('sku')->nullable()->index();
            $table->string('wp_id')->nullable()->index();
            $table->string('old_url')->nullable();
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->json('features')->nullable();
            $table->json('metadata')->nullable();
            $table->json('specs')->nullable();
            $table->string('status')->default('draft')->index();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['product_category_id', 'slug'], 'category_product_slug_unique');
        });

        Schema::create('product_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('type')->default('catalog');
            $table->string('path')->nullable();
            $table->string('url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default('draft')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->nullable()->index();
            $table->string('category_slug')->nullable()->index();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('group_key')->nullable()->index();
            $table->nullableMorphs('faqable');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('technical_service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->longText('message');
            $table->string('source_url')->nullable();
            $table->string('source_type')->nullable()->index();
            $table->json('utm')->nullable();
            $table->json('payload')->nullable();
            $table->string('status')->default('new')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('seo_entries', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('seoable');
            $table->string('route_name')->nullable()->index();
            $table->string('path')->nullable()->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_image')->nullable();
            $table->string('robots')->default('index,follow');
            $table->string('schema_type')->nullable();
            $table->json('schema_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('source_path')->unique();
            $table->string('target_path');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('seo_entries');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('product_documents');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_category_service');
        Schema::dropIfExists('product_category_brand');
        Schema::dropIfExists('product_brands');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('technical_services');
        Schema::dropIfExists('services');
    }
};
