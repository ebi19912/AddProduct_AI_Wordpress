jQuery(document).ready(function($) {
    $('#awg-generate-btn').on('click', function(e) {
        e.preventDefault();

        var rawSpecs = $('#awg-raw-specs').val();
        var $btn = $(this);
        var $resultMsg = $('#awg-result-message');

        if (!rawSpecs) {
            $resultMsg.html('<div class="notice notice-error"><p>لطفاً مشخصات محصول را وارد کنید.</p></div>');
            return;
        }

        $btn.prop('disabled', true).text('در حال پردازش...');
        $resultMsg.html('');

        $.ajax({
            url: awg_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'awg_generate_product',
                security: awg_ajax_obj.nonce,
                raw_specs: rawSpecs
            },
            success: function(response) {
                if (response.success) {
                    $resultMsg.html('<div class="notice notice-success"><p>' + response.data.message + ' <a href="' + response.data.edit_url + '" target="_blank">ویرایش محصول</a></p></div>');
                    $('#awg-raw-specs').val(''); // پاک کردن فرم پس از موفقیت
                } else {
                    $resultMsg.html('<div class="notice notice-error"><p>خطا: ' + response.data.message + '</p></div>');
                }
            },
            error: function(xhr, status, error) {
                $resultMsg.html('<div class="notice notice-error"><p>خطا در برقراری ارتباط با سرور.</p></div>');
            },
            complete: function() {
                $btn.prop('disabled', false).text('تولید محصول');
            }
        });
    });
});
