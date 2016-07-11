(function ($) {

	SS6 = window.SS6 || {};
	SS6.order = SS6.order || {};

	SS6.order.paymentTransportRelations = [];

	SS6.order.init = function () {
		$('input.transport').change(SS6.order.onTransportChange);
		$('input.payment').change(SS6.order.onPaymentChange);
		SS6.order.updateTransports();
		SS6.order.updatePayments();

		$('input.transport').change(SS6.order.updateContinueButton);
		$('input.payment').change(SS6.order.updateContinueButton);
		SS6.order.updateContinueButton();
	};

	SS6.order.addPaymentTransportRelation = function(paymentId, transportId) {
		if (SS6.order.paymentTransportRelations[paymentId] === undefined) {
			SS6.order.paymentTransportRelations[paymentId] = [];
		}
		SS6.order.paymentTransportRelations[paymentId][transportId] = true;
	};

	SS6.order.paymentTransportRelationExists = function(paymentId, transportId) {
		if (SS6.order.paymentTransportRelations[paymentId] !== undefined) {
			if (SS6.order.paymentTransportRelations[paymentId][transportId] !== undefined) {
				return SS6.order.paymentTransportRelations[paymentId][transportId];
			}
		}

		return false;
	};

	SS6.order.updateTransports = function () {
		var $checkedPayment = $('input.payment:checked');
		if ($checkedPayment.length) {
			var checkedPaymentId = $checkedPayment.data('id');
			$('input.transport').each(function (i, checkbox) {
				var $checkbox = $(checkbox);
				var id = $checkbox.data('id');
				if (SS6.order.paymentTransportRelationExists(checkedPaymentId, id)) {
					$checkbox.prop('disabled', false);
					$checkbox.closest('label.box-chooser__item').removeClass('box-chooser__item--inactive');
				} else {
					$checkbox.prop('disabled', true);
					$checkbox.prop('checked', false);
					$checkbox.closest('label.box-chooser__item').addClass('box-chooser__item--inactive');
				}
			});
		} else {
			$('input.transport').each(function (i, checkbox) {
				var $checkbox = $(checkbox);
				$checkbox.prop('disabled', false);
				$checkbox.closest('label.box-chooser__item').removeClass('box-chooser__item--active').removeClass('box-chooser__item--inactive');
			});
		}

		var $checkedTransport = $('input.transport:checked');
		if ($checkedTransport.length) {
			$checkedTransport.closest('label.box-chooser__item').removeClass('box-chooser__item--inactive').addClass('box-chooser__item--active');
		}
	};

	SS6.order.updatePayments = function () {
		var $checkedTransport = $('input.transport:checked');
		if ($checkedTransport.length) {
			var checkedTransportId = $checkedTransport.data('id');
			$('input.payment').each(function (i, checkbox) {
				var $checkbox = $(checkbox);
				var id = $checkbox.data('id');
				if (SS6.order.paymentTransportRelationExists(id, checkedTransportId)) {
					$checkbox.prop('disabled', false);
					$checkbox.closest('label.box-chooser__item').removeClass('box-chooser__item--inactive');
				} else {
					$checkbox.prop('disabled', true);
					$checkbox.prop('checked', false);
					$checkbox.closest('label.box-chooser__item').addClass('box-chooser__item--inactive');
				}
			});
		} else {
			$('input.payment').each(function (i, checkbox) {
				var $checkbox = $(checkbox);
				$checkbox.prop('disabled', false);
				$checkbox.closest('label.box-chooser__item').removeClass('box-chooser__item--active').removeClass('box-chooser__item--inactive');
			});
		}

		var $checkedPayment = $('input.payment:checked');
		if ($checkedPayment.length) {
			$checkedPayment.closest('label.box-chooser__item').removeClass('box-chooser__item--inactive').addClass('box-chooser__item--active');
		}
	};

	SS6.order.onTransportChange = function () {
		var checked = $(this).prop('checked');
		var checkedId = $(this).data('id');

		if (checked) {
			// uncheckOtherTransports
			$('input.transport:checked').each(function (i, checkbox) {
				var $checkbox = $(checkbox);
				var id = $checkbox.data('id');
				if (id !== checkedId) {
					$checkbox.prop('checked', false);
					$(this).closest('label.box-chooser__item').removeClass('box-chooser__item--active');
				}
			});

			$(this).closest('label.box-chooser__item').addClass('box-chooser__item--active');
		} else {
			$(this).closest('label.box-chooser__item').removeClass('box-chooser__item--active');
		}

		SS6.order.updatePayments();
	};

	SS6.order.onPaymentChange = function () {
		var checked = $(this).prop('checked');
		var checkedId = $(this).data('id');

		if (checked) {
			// uncheckOtherPayments
			$('input.payment:checked').each(function (i, checkbox) {
				var $checkbox = $(checkbox);
				var id = $checkbox.data('id');
				if (id !== checkedId) {
					$checkbox.prop('checked', false);
					$(this).closest('label.box-chooser__item').removeClass('box-chooser__item--active');
				}
			});

			$(this).closest('label.box-chooser__item').addClass('box-chooser__item--active');
		} else {
			$(this).closest('label.box-chooser__item').removeClass('box-chooser__item--active');
		}

		SS6.order.updateTransports();
	};

	SS6.order.updateContinueButton = function () {
		var checkedTransport = $('input.transport:checked');
		var checkedPayment = $('input.payment:checked');

		if (checkedTransport.length === 1 && checkedPayment.length === 1) {
			$('#transport_and_payment_form_save').removeClass('btn--disabled');
		} else {
			$('#transport_and_payment_form_save').addClass('btn--disabled');
		}
	};

	$(document).ready(function () {
		SS6.order.init();
	});

})(jQuery);
