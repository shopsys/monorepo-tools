(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.contactForm = Shopsys.contactForm || {};

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('form[name="contact_form"]').bind('contactFormAjaxSubmit', Shopsys.contactForm.ajaxSubmit);
    });

    Shopsys.contactForm.ajaxSubmit = function (event) {
        Shopsys.ajax({
            loaderElement: '#js-contact-form-container',
            url: $(this).attr('action'),
            method: 'post',
            data: $(this).serialize(),
            dataType: 'json',
            success: onSuccess
        });
        event.preventDefault();
    };

    var onSuccess = function (data) {
        $('#js-contact-form-container').html(data['contactFormHtml']);
        Shopsys.register.registerNewContent($('#js-contact-form-container'));
        Shopsys.window({
            content: data['message']
        });
    };

})(jQuery);
