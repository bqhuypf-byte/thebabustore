<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('content_sections')->nullable();
            $table->json('specification_groups')->nullable();
            $table->json('option_group_order')->nullable();
        });
        Schema::table('product_variants', fn (Blueprint $table) => $table->json('specifications')->nullable());
    }

    public function down(): void
    {
        Schema::table('product_variants', fn (Blueprint $table) => $table->dropColumn('specifications'));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['content_sections', 'specification_groups', 'option_group_order']));
    }
};
