(function ($) {

	$.fn.SS6 = $.fn.SS6 || {};
	$.fn.SS6.order = $.fn.SS6.order || {};
	
	$.fn.SS6.order.paymentTransportRelations = [];
	
	$.fn.SS6.order.init = function () {
		$('input.transport').change($.fn.SS6.order.onTransportChange);
		$('input.payment').change($.fn.SS6.order.onPaymentChange);
		$.fn.SS6.order.updateTransports();
		$.fn.SS6.order.updatePayments();
		
		$('input.transport').change($.fn.SS6.order.updateContinueButton);
		$('input.payment').change($.fn.SS6.order.updateContinueButton);
		$.fn.SS6.order.updateContinueButton();
	};
	
	$.fn.SS6.order.addPaymentTransportRelation = function(paymentId, transportId) {
		if ($.fn.SS6.order.paymentTransportRelations[paymentId] === undefined) {
			$.fn.SS6.order.paymentTransportRelations[paymentId] = [];
		}
		$.fn.SS6.order.paymentTransportRelations[paymentId][transportId] = true;
	};
	
	$.fn.SS6.order.paymentTransportRelationExists = function(paymentId, transportId) {
		if ($.fn.SS6.order.paymentTransportRelations[paymentId] !== undefined) {
			if ($.fn.SS6.order.paymentTransportRelations[paymentId][transportId] !== undefined) {
				return $.fn.SS6.order.paymentTransportRelations[paymentId][transportId];
			}
		}
		
		return false;
	};
	
	$.fn.SS6.order.updateTransports = function () {
		var $checkedPayment = $('input.payment:checked');
		if ($checkedPayment.size()) {
			var checkedPaymentId = $checkedPayment.data('id');
			$('input.transport').each(function (i, checkbox) {
				var $checkbox = $(checkbox);
				var id = $checkbox.data('id');
				if ($.fn.SS6.order.paymentTransportRelationExists(checkedPaymentId, id)) {
					$checkbox.prop('disabled', false);
					$checkbox.closest('label.chooser__item').removeClass('chooser__item--inactive');
				} else {
					$checkbox.prop('disabled', true);
					$checkbox.prop('checked', false);
					$checkbox.closest('label.chooser__item').addClass('chooser__item--inactive');
				}
			});
		} else {
			$('input.transport').each(function (i, checkbox) {
				var $checkbox = $(checkbox);
				$checkbox.prop('disabled', false);
				$checkbox.closest('label.chooser__item').removeClass('chooser__item--active').removeClass('chooser__item--inactive');
			});
		}
		
		var $checkedTransport = $('input.transport:checked');
		if ($checkedTransport.size()) {
			$checkedTransport.closest('label.chooser__item').removeClass('chooser__item--inactive').addClass('chooser__item--active');
		}
	};
	
	$.fn.SS6.order.updatePayments = function () {
		var $checkedTransport = $('input.transport:checked');
		if ($checkedTransport.size()) {
			var checkedTransportId = $checkedTransport.data('id');
			$('input.payment').each(function (i, checkbox) {
				var $checkbox = $(checkbox);
				var id = $checkbox.data('id');
				if ($.fn.SS6.order.paymentTransportRelationExists(id, checkedTransportId)) {
					$checkbox.prop('disabled', false);
					$checkbox.closest('label.chooser__item').removeClass('chooser__item--inactive');
				} else {
					$checkbox.prop('disabled', true);
					$checkbox.prop('checked', false);
					$checkbox.closest('label.chooser__item').addClass('chooser__item--inactive');
				}
			});
		} else {
			$('input.payment').each(function (i, checkbox) {
				var $checkbox = $(checkbox);
				$checkbox.prop('disabled', false);
				$checkbox.closest('label.chooser__item').removeClass('chooser__item--active').removeClass('chooser__item--inactive');
			});
		}
		
		var $checkedPayment = $('input.payment:checked');
		if ($checkedPayment.size()) {
			$checkedPayment.closest('label.chooser__item').removeClass('chooser__item--inactive').addClass('chooser__item--active');
		}
	};
	
	$.fn.SS6.order.onTransportChange = function () {
		var checked = $(this).prop('checked');
		var checkedId = $(this).data('id');
		
		if (checked) {
			// uncheckOtherTransports
			$('input.transport:checked').each(function (i, checkbox) {
				var $checkbox = $(checkbox);
				var id = $checkbox.data('id');
				if (id !== checkedId) {
					$checkbox.prop('checked', false);
					$(this).closest('label.chooser__item').removeClass('chooser__item--active');
				}
			});
			
			$(this).closest('label.chooser__item').addClass('chooser__item--active');
		} else {
			$(this).closest('label.chooser__item').removeClass('chooser__item--active');
		}
		
		$.fn.SS6.order.updatePayments();
	};
	
	$.fn.SS6.order.onPaymentChange = function () {
		var checked = $(this).prop('checked');
		var checkedId = $(this).data('id');
		
		if (checked) {
			// uncheckOtherPayments
			$('input.payment:checked').each(function (i, checkbox) {
				var $checkbox = $(checkbox);
				var id = $checkbox.data('id');
				if (id !== checkedId) {
					$checkbox.prop('checked', false);
					$(this).closest('label.chooser__item').removeClass('chooser__item--active');
				}
			});
			
			$(this).closest('label.chooser__item').addClass('chooser__item--active');
		} else {
			$(this).closest('label.chooser__item').removeClass('chooser__item--active');
		}
		
		$.fn.SS6.order.updateTransports();
	};
	
	$.fn.SS6.order.updateContinueButton = function () {
		var checkedTransport = $('input.transport:checked');
		var checkedPayment = $('input.payment:checked');
		
		if (checkedTransport.length === 1 && checkedPayment.length === 1) {
			$('#transportAndPayment_submit').removeClass('button--alter');
		} else {
			$('#transportAndPayment_submit').addClass('button--alter');
		}
	};
	
	$(document).ready(function () {
		$.fn.SS6.order.init();
	});
	
})(jQuery);
