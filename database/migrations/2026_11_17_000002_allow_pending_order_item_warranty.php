<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', fn (Blueprint $table) => $table->dateTime('warranty')->nullable()->default(null)->change());
    }

    public function down(): void
    { /* Preserve pending rows instead of inventing expiry dates. */
    }
};
