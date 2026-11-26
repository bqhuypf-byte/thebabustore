<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('settings', function (Blueprint $table) {
            $table->json('repair_services')->nullable();
            $table->json('repair_parts')->nullable();
        });
    }
    public function down(): void {
        Schema::table('settings', fn (Blueprint $table) => $table->dropColumn(['repair_services', 'repair_parts']));
    }
};
