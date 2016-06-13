(function($) {

	SS6 = SS6 || {};
	SS6.promoCode = SS6.promoCode || {};

	SS6.promoCode.PromoCode = function() {
		var self = this;
		var $promoCodeSubmitButton = $('#js-promo-code-submit-button');
		var $promoCodeInput = $('#js-promo-code-input');

		this.init = function() {
			$promoCodeSubmitButton.click(applyPromoCode);
			$promoCodeInput.keypress(function(event){
				if (event.keyCode === SS6.keyCodes.ENTER) {
					applyPromoCode();
					return false;
				}
			});
		};

		var applyPromoCode = function() {
			var code = $promoCodeInput.val();
			if (code !== '') {
				var data = {};
				data[SS6.constant('SS6\\ShopBundle\\Controller\\Front\\PromoCodeController::PROMO_CODE_PARAMETER')] = code;
				SS6.ajax({
					loaderElement: '#js-promo-code-submit-button',
					url: $promoCodeInput.data('apply-code-url'),
					dataType: 'json',
					method: 'post',
					data: data,
					success: onApplyPromoCode
				});
			} else {
				SS6.window({
					content: SS6.translator.trans('Zadejte, prosím, slevový kód.')
				});
			}
		};

		var onApplyPromoCode = function(response) {
			if (response.result === true) {
				document.location = document.location;
			} else {
				SS6.window({
					content: response.message
				});
			}
		};
	};

	$(document).ready(function(){
		var promoCode = new SS6.promoCode.PromoCode();
		promoCode.init();
	});

})(jQuery);
