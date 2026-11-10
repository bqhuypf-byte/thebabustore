<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->json('classification')->nullable();
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->string('support_hours')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', fn (Blueprint $table) => $table->dropColumn('classification'));
        Schema::table('settings', fn (Blueprint $table) => $table->dropColumn('support_hours'));
    }
};
