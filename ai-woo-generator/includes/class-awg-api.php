<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AWG_API {

    public static function call_llm( $raw_specs ) {
        $api_url       = get_option( 'awg_api_url', 'https://openrouter.ai/api/v1/chat/completions' );
        $api_key       = get_option( 'awg_api_key', '' );
        $api_model     = get_option( 'awg_api_model', 'openai/gpt-4o-mini' );

        $default_prompt = 'شما یک دستیار حرفه‌ای تولید محتوا برای فروشگاه ووکامرس هستید.
کاربر مشخصات خام یک محصول را به شما می‌دهد. شما باید این اطلاعات را تحلیل کرده و دقیقاً یک شیء JSON با ساختار زیر تولید کنید.
هیچ متن اضافه‌ای قبل یا بعد از JSON خروجی ندهید. مقادیر متنی باید به زبان فارسی باشند.

ساختار JSON الزامی:
{
  "title": "عنوان جذاب محصول",
  "description": "توضیحات کامل محصول با استفاده از تگ‌های HTML مناسب مانند <h2>، <p>، و <ul>",
  "categories": ["دسته بندی 1", "دسته بندی 2"],
  "attributes": {
    "ویژگی 1": "مقدار 1",
    "ویژگی 2": "مقدار 2"
  },
  "seo_keywords": "کلمه کلیدی 1, کلمه کلیدی 2, کلمه کلیدی 3",
  "seo_description": "توضیحات متای سئو با حداکثر 160 کاراکتر و جذاب برای کلیک",
  "review": "یک نقد و بررسی کوتاه و جذاب از محصول (HTML)"
}';

        $system_prompt = get_option( 'awg_system_prompt', $default_prompt );

        if ( empty( $api_key ) ) {
            return new WP_Error( 'no_api_key', 'لطفاً ابتدا API Key را در تنظیمات وارد کنید.' );
        }

        $body = array(
            'model'    => $api_model,
            'messages' => array(
                array(
                    'role'    => 'system',
                    'content' => $system_prompt
                ),
                array(
                    'role'    => 'user',
                    'content' => "مشخصات خام محصول:\n" . $raw_specs
                )
            ),
            'temperature' => 0.7
        );

        $args = array(
            'body'        => wp_json_encode( $body ),
            'headers'     => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
                'HTTP-Referer'  => home_url(),
                'X-Title'       => 'AI Woo Generator Plugin'
            ),
            'timeout'     => 60,
            'data_format' => 'body',
        );

        $response = wp_remote_post( $api_url, $args );

        if ( is_wp_error( $response ) ) {
            error_log( 'AWG API Error: ' . $response->get_error_message() );
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );

        if ( $response_code !== 200 ) {
            error_log( 'AWG API Error (Code ' . $response_code . '): ' . $response_body );
            return new WP_Error( 'api_error', 'خطا در ارتباط با API. کد خطا: ' . $response_code );
        }

        $data = json_decode( $response_body, true );

        if ( empty( $data['choices'][0]['message']['content'] ) ) {
            error_log( 'AWG API Error: No content in response: ' . $response_body );
            return new WP_Error( 'api_invalid_response', 'پاسخ نامعتبر از API دریافت شد.' );
        }

        $content = $data['choices'][0]['message']['content'];

        // استخراج JSON از متن خروجی (در صورتی که با ```json ... ``` برگشت داده شود)
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);
        $content = trim($content);

        $parsed_data = json_decode( $content, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            error_log( 'AWG JSON Parse Error: ' . json_last_error_msg() . "\nRaw Content: " . $content );
            return new WP_Error( 'json_parse_error', 'مدل زبانی خروجی JSON معتبری تولید نکرد.' );
        }

        return $parsed_data;
    }
}
