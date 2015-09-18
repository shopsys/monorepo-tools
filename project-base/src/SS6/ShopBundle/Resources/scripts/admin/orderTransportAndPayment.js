(function ($) {

	SS6 = window.SS6 || {};
	SS6.order = SS6.order || {};
	SS6.order.transportAndPayment = SS6.order.transportAndPayment || {};

	SS6.order.transportAndPayment.Prefiller = function () {
		var self = this;

		var orderCurrencyId = $('#js-order-items').data('order-currency-id');

		var $transportSelect = $('#order_form_orderTransport_transport');
		var $transportPricesByTransportIdAndCurrencyId = $transportSelect.closest('.js-order-transport-row').data('transport-prices-by-transport-id-and-currency-id');
		var $transportVatPercentsByTransportId = $transportSelect.closest('.js-order-transport-row').data('transport-vat-percents-by-transport-id');

		var $paymentSelect = $('#order_form_orderPayment_payment');
		var $paymentPricesByPaymentIdAndCurrencyId = $paymentSelect.closest('.js-order-payment-row').data('payment-prices-by-payment-id-and-currency-id');
		var $paymentVatPercentsByPaymentId = $paymentSelect.closest('.js-order-payment-row').data('payment-vat-percents-by-payment-id');

		this.init = function () {
			$transportSelect.on('change', onOrderTransportChange);
			$paymentSelect.on('change', onOrderPaymentChange);
		};

		var onOrderTransportChange = function() {
			var selectedTransportId = $transportSelect.val();
			$('#order_form_orderTransport_priceWithVat').val($transportPricesByTransportIdAndCurrencyId[selectedTransportId][orderCurrencyId]);
			$('#order_form_orderTransport_vatPercent').val($transportVatPercentsByTransportId[selectedTransportId]);
		};

		var onOrderPaymentChange = function() {
			var selectedPaymentId = $paymentSelect.val();
			$('#order_form_orderPayment_priceWithVat').val($paymentPricesByPaymentIdAndCurrencyId[selectedPaymentId][orderCurrencyId]);
			$('#order_form_orderPayment_vatPercent').val($paymentVatPercentsByPaymentId[selectedPaymentId]);
		};
	};

	$(document).ready(function () {
		var instance = new SS6.order.transportAndPayment.Prefiller();
		instance.init();
	});

})(jQuery);
