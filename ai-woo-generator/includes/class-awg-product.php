<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AWG_Product {

    public function __construct() {
        add_action( 'wp_ajax_awg_generate_product', array( $this, 'process_generation' ) );
    }

    public function process_generation() {
        check_ajax_referer( 'awg_generate_nonce', 'security' );

        if ( ! current_user_can( 'edit_products' ) ) {
            wp_send_json_error( array( 'message' => 'شما اجازه ساخت محصول را ندارید.' ) );
        }

        $raw_specs = isset( $_POST['raw_specs'] ) ? sanitize_textarea_field( wp_unslash( $_POST['raw_specs'] ) ) : '';

        if ( empty( $raw_specs ) ) {
            wp_send_json_error( array( 'message' => 'لطفاً مشخصات محصول را وارد کنید.' ) );
        }

        // فراخوانی API
        $parsed_data = AWG_API::call_llm( $raw_specs );

        if ( is_wp_error( $parsed_data ) ) {
            wp_send_json_error( array( 'message' => $parsed_data->get_error_message() ) );
        }

        // ایجاد محصول در ووکامرس
        $result = $this->create_woocommerce_product( $parsed_data );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array(
            'message'  => 'محصول با موفقیت ساخته شد.',
            'edit_url' => get_edit_post_link( $result, '' )
        ) );
    }

    private function create_woocommerce_product( $data ) {
        if ( empty( $data['title'] ) ) {
            return new WP_Error( 'missing_title', 'عنوان محصول در خروجی یافت نشد.' );
        }

        $allowed_html = wp_kses_allowed_html( 'post' );

        $description = isset( $data['description'] ) ? wp_kses( $data['description'], $allowed_html ) : '';
        $review      = isset( $data['review'] ) ? wp_kses( $data['review'], $allowed_html ) : '';

        // ترکیب توضیحات و نقد و بررسی (یا می‌توان نقد را در فیلد دیگری ذخیره کرد)
        $post_content = $description;
        if ( ! empty( $review ) ) {
            $post_content .= "\n\n<h3>نقد و بررسی</h3>\n" . $review;
        }

        $post_data = array(
            'post_title'   => sanitize_text_field( $data['title'] ),
            'post_content' => $post_content,
            'post_status'  => 'draft',
            'post_type'    => 'product',
        );

        $post_id = wp_insert_post( $post_data, true );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        // تنظیم دسته‌بندی‌ها
        if ( ! empty( $data['categories'] ) && is_array( $data['categories'] ) ) {
            $cat_ids = array();
            foreach ( $data['categories'] as $cat_name ) {
                $term = term_exists( $cat_name, 'product_cat' );
                if ( $term !== 0 && $term !== null ) {
                    $cat_ids[] = intval( $term['term_id'] );
                } else {
                    $new_term = wp_insert_term( $cat_name, 'product_cat' );
                    if ( ! is_wp_error( $new_term ) ) {
                        $cat_ids[] = intval( $new_term['term_id'] );
                    }
                }
            }
            if ( ! empty( $cat_ids ) ) {
                wp_set_object_terms( $post_id, $cat_ids, 'product_cat' );
            }
        }

        // تنظیم ویژگی‌ها
        if ( ! empty( $data['attributes'] ) && is_array( $data['attributes'] ) ) {
            $product_attributes = array();
            $position = 0;
            foreach ( $data['attributes'] as $key => $value ) {
                $product_attributes[ sanitize_title( $key ) ] = array(
                    'name'         => sanitize_text_field( $key ),
                    'value'        => sanitize_text_field( $value ),
                    'position'     => $position++,
                    'is_visible'   => 1,
                    'is_variation' => 0,
                    'is_taxonomy'  => 0
                );
            }
            update_post_meta( $post_id, '_product_attributes', $product_attributes );
        }

        // تنظیمات Rank Math
        if ( ! empty( $data['seo_keywords'] ) ) {
            update_post_meta( $post_id, 'rank_math_focus_keyword', sanitize_text_field( $data['seo_keywords'] ) );
        }

        if ( ! empty( $data['seo_description'] ) ) {
            update_post_meta( $post_id, 'rank_math_description', sanitize_textarea_field( $data['seo_description'] ) );
        }

        if ( ! empty( $data['title'] ) ) {
            update_post_meta( $post_id, 'rank_math_title', sanitize_text_field( $data['title'] ) );
        }

        return $post_id;
    }
}
