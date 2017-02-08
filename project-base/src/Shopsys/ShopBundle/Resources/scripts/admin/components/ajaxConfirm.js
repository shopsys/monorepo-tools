(function ($) {

	SS6 = window.SS6 || {};
	SS6.ajaxConfirm = SS6.ajaxConfirm || {};

	SS6.ajaxConfirm.init = function ($container) {
		$container.filterAllNodes('a.js-ajax-confirm').each(SS6.ajaxConfirm.bind);
	};

	SS6.ajaxConfirm.bind = function () {
		$(this)
			.unbind('click.ajaxConfirm')
			.bind('click.ajaxConfirm', function () {
				SS6.ajax({
					url: $(this).attr('href'),
					context: this,
					success: function(data) {
						SS6.window({
							content: data
						});
						var onOpen = $(this).data('ajax-confirm-on-open');
						if (onOpen) {
							eval(onOpen);
						}
					}
				});

				return false;
			});
	};

	SS6.register.registerCallback(SS6.ajaxConfirm.init);

})(jQuery);
