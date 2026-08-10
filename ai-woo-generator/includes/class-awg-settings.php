<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AWG_Settings {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_admin_menu() {
        // صفحه اصلی برای ساخت محصول
        add_menu_page(
            'تولید محصول با هوش مصنوعی',
            'هوش مصنوعی ووکامرس',
            'edit_products',
            'awg-generator',
            array( $this, 'render_generator_page' ),
            'dashicons-robot',
            56
        );

        // زیرمنو برای تنظیمات
        add_submenu_page(
            'awg-generator',
            'تنظیمات هوش مصنوعی',
            'تنظیمات API',
            'manage_options',
            'awg-settings',
            array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting( 'awg_settings_group', 'awg_api_provider' );
        register_setting( 'awg_settings_group', 'awg_api_url' );
        register_setting( 'awg_settings_group', 'awg_api_key' );
        register_setting( 'awg_settings_group', 'awg_api_model' );
        register_setting( 'awg_settings_group', 'awg_system_prompt' );
    }

    public function render_generator_page() {
        if ( ! current_user_can( 'edit_products' ) ) {
            return;
        }
        require_once AWG_PLUGIN_DIR . 'admin/views/view-generator.php';
    }

    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        require_once AWG_PLUGIN_DIR . 'admin/views/view-settings.php';
    }
}
