(function ($) {

	// Enable close popup from iframe https://github.com/dimsemenov/Magnific-Popup/pull/608
	$.magnificPopup._close = $.magnificPopup.close;
	$.magnificPopup.close = function () {
		if (window.parent !== window
			&& window.parent.$
			&& window.parent.$.magnificPopup
		) {
			window.parent.$.magnificPopup.close();
		} else {
			$.magnificPopup._close.call(this);
		}
	};

	})(jQuery);


