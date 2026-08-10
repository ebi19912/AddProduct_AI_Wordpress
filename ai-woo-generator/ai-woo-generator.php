<?php
/**
 * Plugin Name: AI Woo Generator
 * Description: یک افزونه وردپرسی برای تولید سریع محصولات ووکامرس با استفاده از هوش مصنوعی (LLM) و هماهنگ با Rank Math.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: ai-woo-generator
 */

// جلوگیری از دسترسی مستقیم
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// تعریف ثابت‌ها
define( 'AWG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AWG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'AWG_VERSION', '1.0.0' );

// فراخوانی فایل‌ها
require_once AWG_PLUGIN_DIR . 'includes/class-awg-settings.php';
require_once AWG_PLUGIN_DIR . 'includes/class-awg-api.php';
require_once AWG_PLUGIN_DIR . 'includes/class-awg-product.php';

class AI_Woo_Generator {

    public function __construct() {
        // بارگذاری کلاس‌ها
        new AWG_Settings();
        new AWG_Product();

        // افزودن اسکریپت‌ها
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }

    public function enqueue_admin_scripts( $hook ) {
        // فقط در صفحات افزونه لود شود
        if ( strpos( $hook, 'awg-generator' ) === false && strpos( $hook, 'awg-settings' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'awg-admin-js',
            AWG_PLUGIN_URL . 'admin/js/awg-admin.js',
            array( 'jquery' ),
            AWG_VERSION,
            true
        );

        wp_localize_script( 'awg-admin-js', 'awg_ajax_obj', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'awg_generate_nonce' )
        ) );
    }
}

// راه‌اندازی افزونه
function run_ai_woo_generator() {
    new AI_Woo_Generator();
}
add_action( 'plugins_loaded', 'run_ai_woo_generator' );
