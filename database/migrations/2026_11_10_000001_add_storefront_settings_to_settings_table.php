<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Cấu hình mã đơn hàng (Admin quản lý)
            $table->string('order_code_prefix')->default('BB')->after('support_hours');
            $table->unsignedTinyInteger('order_code_digits')->default(6)->after('order_code_prefix');

            // Cấu hình Announcement Bar (Header đếm ngược)
            $table->boolean('announcement_enabled')->default(true)->after('order_code_digits');
            $table->string('announcement_text')->default('Ưu đãi thành viên BABU: Miễn phí vận chuyển cho đơn từ 800k • Đăng ký nhận quà')->after('announcement_enabled');
            $table->string('announcement_link')->default('/dang-ky')->after('announcement_text');
            $table->string('announcement_button_text')->default('Chi tiết')->after('announcement_link');
            $table->timestamp('announcement_countdown_end')->nullable()->after('announcement_button_text');

            // Cấu hình phân tầng giỏ hàng (Cart Drawer Tier Thresholds)
            $table->unsignedInteger('cart_tier_1_amount')->default(300000)->after('announcement_countdown_end');
            $table->string('cart_tier_1_reward')->default('Tặng dụng cụ vệ sinh phím')->after('cart_tier_1_amount');
            $table->unsignedInteger('cart_tier_2_amount')->default(800000)->after('cart_tier_1_reward');
            $table->string('cart_tier_2_reward')->default('Freeship toàn quốc')->after('cart_tier_2_amount');
            $table->unsignedInteger('cart_tier_3_amount')->default(1500000)->after('cart_tier_2_reward');
            $table->string('cart_tier_3_reward')->default('Tặng lót chuột Gaming BABU')->after('cart_tier_3_amount');

            // Cấu hình Tiện ích Thành viên / Streak Loyalty Widget
            $table->boolean('streak_widget_enabled')->default(true)->after('cart_tier_3_reward');
            $table->string('streak_widget_title')->default('Thành viên BABU')->after('streak_widget_enabled');
            $table->string('streak_widget_subtitle')->default('Đăng nhập nhận ưu đãi & tích lũy đổi quà')->after('streak_widget_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'order_code_prefix',
                'order_code_digits',
                'announcement_enabled',
                'announcement_text',
                'announcement_link',
                'announcement_button_text',
                'announcement_countdown_end',
                'cart_tier_1_amount',
                'cart_tier_1_reward',
                'cart_tier_2_amount',
                'cart_tier_2_reward',
                'cart_tier_3_amount',
                'cart_tier_3_reward',
                'streak_widget_enabled',
                'streak_widget_title',
                'streak_widget_subtitle',
            ]);
        });
    }
};
