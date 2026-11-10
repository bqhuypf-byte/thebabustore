<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('products', function(Blueprint $table) {
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->longText('warranty_content')->nullable();
            $table->timestamp('content_standardized_at')->nullable();
        });
    }
    public function down(): void {
        Schema::table('products', fn(Blueprint $table) => $table->dropColumn(['seo_title','seo_description','warranty_content','content_standardized_at']));
    }
};
