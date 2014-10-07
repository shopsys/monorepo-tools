(function ($){
	$(document).ready(function () {
		
		var $transportAndPaymentForm = $('#transportAndPayment');
		$transportAndPaymentForm.jsFormValidator({
			callbacks: {
				validateTransportPaymentRelation: function () {
					// validation combination of transport and payment is in ../../order.js
				}
			}
		});
		
	});
})(jQuery);
