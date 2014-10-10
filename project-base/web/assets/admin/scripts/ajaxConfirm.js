(function ($) {

	SS6 = window.SS6 || {};
	SS6.ajaxConfirm = SS6.ajaxConfirm || {};
	
	SS6.ajaxConfirm.init = function () {
		$('a.js-ajax-confirm').each(SS6.ajaxConfirm.bind);
	}
	
	SS6.ajaxConfirm.bind = function () {
		$(this).bind('click', function () {
			$.ajax({
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
		})
	};
	
	$(document).ready(function () {
		SS6.ajaxConfirm.init();
	});

})(jQuery);
