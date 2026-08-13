<div class="wrap">
    <h1>تنظیمات افزونه هوش مصنوعی ووکامرس</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'awg_settings_group' ); ?>
        <?php do_settings_sections( 'awg_settings_group' ); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">API Provider</th>
                <td><input type="text" name="awg_api_provider" value="<?php echo esc_attr( get_option( 'awg_api_provider', 'OpenRouter' ) ); ?>" class="regular-text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">API URL</th>
                <td><input type="text" name="awg_api_url" value="<?php echo esc_attr( get_option( 'awg_api_url', 'https://openrouter.ai/api/v1/chat/completions' ) ); ?>" class="regular-text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">API Key</th>
                <td><input type="password" name="awg_api_key" value="<?php echo esc_attr( get_option( 'awg_api_key' ) ); ?>" class="regular-text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Model Name</th>
                <td><input type="text" name="awg_api_model" value="<?php echo esc_attr( get_option( 'awg_api_model', 'openai/gpt-4o-mini' ) ); ?>" class="regular-text" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">System Prompt</th>
                <td>
                    <textarea name="awg_system_prompt" rows="15" class="large-text code"><?php echo esc_textarea( get_option( 'awg_system_prompt', 'شما یک دستیار حرفه‌ای تولید محتوا برای فروشگاه ووکامرس هستید.
کاربر مشخصات خام یک محصول را به شما می‌دهد. شما باید این اطلاعات را تحلیل کرده و دقیقاً یک شیء JSON با ساختار زیر تولید کنید.
هیچ متن اضافه‌ای قبل یا بعد از JSON خروجی ندهید. مقادیر متنی باید به زبان فارسی روان و کاملاً طبیعی باشند.
توجه بسیار ویژه به سئو (SEO) برای زبان فارسی داشته باشید. از تکرار بیش از حد کلمات کلیدی (Keyword Stuffing) پرهیز کنید، محتوا را غنی و جذاب بنویسید، و از کلمات کلیدی مرتبط (LSI) استفاده کنید تا سئو به بهترین شکل ممکن رعایت شود.

ساختار JSON الزامی:
{
  "title": "عنوان جذاب و سئو شده محصول",
  "short_description": "توضیح کوتاه درباره محصول (خلاصه جذاب و ترغیب‌کننده برای خرید)",
  "description": "توضیحات کامل و جامع محصول با استفاده از تگ‌های HTML مناسب (مثل <h2>، <p>، و <ul>)، با رعایت اصول نگارشی فارسی و سئوی محتوایی قوی",
  "categories": ["دسته بندی 1", "دسته بندی 2"],
  "tags": ["برچسب 1", "برچسب 2", "برچسب 3"],
  "attributes": {
    "ویژگی 1": "مقدار 1",
    "ویژگی 2": "مقدار 2"
  },
  "seo_keywords": "کلمات کلیدی اصلی و مرتبط، حداکثر ۵ کلمه",
  "seo_description": "توضیحات متای سئو با حداکثر ۱۶۰ کاراکتر، جذاب برای کلیک کاربر و با استفاده طبیعی از کلمه کلیدی اصلی",
  "review": "یک نقد و بررسی کوتاه، صادقانه و جذاب از محصول (HTML)"
}' ) ); ?></textarea>
                    <p class="description">این دستورالعمل به هوش مصنوعی می‌گوید که چگونه داده‌ها را تحلیل کرده و خروجی JSON را شکل دهد.</p>
                </td>
            </tr>
        </table>

        <?php submit_button( 'ذخیره تنظیمات' ); ?>
    </form>
</div>
