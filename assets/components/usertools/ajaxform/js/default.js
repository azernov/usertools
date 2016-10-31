var CsAjaxForm = {

    initialize: function() {
        if(!jQuery().ajaxForm) {
            document.write('<script src="'+afConfig.assetsUrl+'js/lib/jquery.form.min.js"><\/script>');
        }

        if (!jQuery().jGrowl) {
            document.write('<script src="' + afConfig['assetsUrl'] + 'js/lib/jquery.jgrowl.min.js"><\/script>');
        }

        $(document).ready(function () {
            $.jGrowl.defaults.closerTemplate = '<div>[ ' + afConfig['closeMessage'] + ' ]</div>';
        });
        
        $(document).on('submit', afConfig.formSelector, function(e) {
            $(this).ajaxSubmit({
                dataType: 'json'
                ,url: afConfig.actionUrl
                ,beforeSerialize: function(form, options) {
                    form.find(':submit').each(function() {
                        if (!form.find('input[type="hidden"][name = "' + $(this).attr('name') + '"]').length) {
                            $(form).append(
                                $("<input type='hidden'>").attr({
                                    name: $(this).attr('name'),
                                    value: $(this).attr('value')
                                })
                            );
                        }
                    })
                }
                ,beforeSubmit: function(fields, form) {
                    form.find('.error').html('');
                    form.find('.input_error').removeClass('input_error');
                    form.find('input,textarea,select,button').attr('disabled', true);
                    return true;
                }
                ,success: function(response, status, xhr, form) {
                    form.find('input,textarea,select,button').attr('disabled', false);
                    response.form=form;
                    $(document).trigger("af_complete", response);
                    if (!response.success) {
                        CsAjaxForm.Message.error(response.message, form);
                        form.find('.error_mod').removeClass('error_mod');
                        if (response.data) {
                            var key, value;
                            for (key in response.data) {
                                var fieldName = key.split('.');
                                if(typeof fieldName[1] !== "undefined")
                                {
                                    fieldName = fieldName[0] + '[' + fieldName[1] + ']';
                                }

                                if (response.data.hasOwnProperty(key)) {
                                    value = response.data[key];

                                    form.find('.error_' + key).html(value).addClass('error');
                                    form.find('[name="' + key + '"]').addClass('error_mod');
                                }
                            }
                        }
                    }
                    else {
                        CsAjaxForm.Message.success(response.message, form);

                        form.find('.error_mod').removeClass('error_mod');
                        if(form.hasClass('reset_on_success'))
                        {
                            form[0].reset();
                        }

                        //Редиректим, если в опциях лежит redirect_url
                        if(response.options && response.options.redirect_url)
                        {
                            var timeout = response.options.redirect_timeout ? response.options.redirect_timeout : 2000;
                            setTimeout(function(){
                                window.location = response.options.redirect_url;
                            },timeout);
                        }
                    }
                }
            });
            e.preventDefault();
            return false;
        });

        $(document).on('keypress change', '.error_mod', function () {
            var key = $(this).attr('name');
            $(this).removeClass('error_mod');
            $('.error_' + key).html('').removeClass('error');
        });

        $(document).on('reset', afConfig['formSelector'], function () {
            $(this).find('.error').html('');
            CsAjaxForm.Message.close();
        });
    }

};

//noinspection JSUnusedGlobalSymbols
CsAjaxForm.Message = {
    success: function (message, $form) {
        if (message) {
            $.jGrowl(message, {theme: 'af-message-success', sticky: false});
        }
    },
    error: function (message, $form) {
        if (message) {
            $.jGrowl(message, {theme: 'af-message-error', sticky: false});
        }
    },
    info: function (message, $form) {
        if (message) {
            $.jGrowl(message, {theme: 'af-message-info', sticky: false});
        }
    },
    close: function () {
        $.jGrowl('close');
    },
};

CsAjaxForm.initialize();
