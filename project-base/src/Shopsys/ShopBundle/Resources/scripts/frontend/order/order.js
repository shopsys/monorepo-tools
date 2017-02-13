(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.order = Shopsys.order || {};

    Shopsys.order.paymentTransportRelations = [];

    Shopsys.order.init = function ($container) {
        var $transportInputs = $container.filterAllNodes('.js-order-transport-input');
        var $paymentInputs = $container.filterAllNodes('.js-order-payment-input');

        $transportInputs.change(Shopsys.order.onTransportChange);
        $paymentInputs.change(Shopsys.order.onPaymentChange);
        Shopsys.order.updateTransports();
        Shopsys.order.updatePayments();

        $transportInputs.change(Shopsys.order.updateContinueButton);
        $paymentInputs.change(Shopsys.order.updateContinueButton);
        Shopsys.order.updateContinueButton();
    };

    Shopsys.order.addPaymentTransportRelation = function(paymentId, transportId) {
        if (Shopsys.order.paymentTransportRelations[paymentId] === undefined) {
            Shopsys.order.paymentTransportRelations[paymentId] = [];
        }
        Shopsys.order.paymentTransportRelations[paymentId][transportId] = true;
    };

    Shopsys.order.paymentTransportRelationExists = function(paymentId, transportId) {
        if (Shopsys.order.paymentTransportRelations[paymentId] !== undefined) {
            if (Shopsys.order.paymentTransportRelations[paymentId][transportId] !== undefined) {
                return Shopsys.order.paymentTransportRelations[paymentId][transportId];
            }
        }

        return false;
    };

    Shopsys.order.updateTransports = function () {
        var $checkedPayment = $('.js-order-payment-input:checked');
        if ($checkedPayment.length > 0) {
            var checkedPaymentId = $checkedPayment.data('id');
            $('.js-order-transport-input').each(function (i, checkbox) {
                var $checkbox = $(checkbox);
                var id = $checkbox.data('id');
                if (Shopsys.order.paymentTransportRelationExists(checkedPaymentId, id)) {
                    $checkbox.prop('disabled', false);
                    $checkbox.closest('label.box-chooser__item').removeClass('box-chooser__item--inactive');
                } else {
                    $checkbox.prop('disabled', true);
                    $checkbox.prop('checked', false);
                    $checkbox.closest('label.box-chooser__item').addClass('box-chooser__item--inactive');
                }
            });
        } else {
            $('.js-order-transport-input').each(function (i, checkbox) {
                var $checkbox = $(checkbox);
                $checkbox.prop('disabled', false);
                $checkbox.closest('label.box-chooser__item').removeClass('box-chooser__item--active').removeClass('box-chooser__item--inactive');
            });
        }

        var $checkedTransport = $('.js-order-transport-input:checked');
        if ($checkedTransport.length > 0) {
            $checkedTransport.closest('label.box-chooser__item').removeClass('box-chooser__item--inactive').addClass('box-chooser__item--active');
        }
    };

    Shopsys.order.updatePayments = function () {
        var $checkedTransport = $('.js-order-transport-input:checked');
        if ($checkedTransport.length > 0) {
            var checkedTransportId = $checkedTransport.data('id');
            $('.js-order-payment-input').each(function (i, checkbox) {
                var $checkbox = $(checkbox);
                var id = $checkbox.data('id');
                if (Shopsys.order.paymentTransportRelationExists(id, checkedTransportId)) {
                    $checkbox.prop('disabled', false);
                    $checkbox.closest('label.box-chooser__item').removeClass('box-chooser__item--inactive');
                } else {
                    $checkbox.prop('disabled', true);
                    $checkbox.prop('checked', false);
                    $checkbox.closest('label.box-chooser__item').addClass('box-chooser__item--inactive');
                }
            });
        } else {
            $('.js-order-payment-input').each(function (i, checkbox) {
                var $checkbox = $(checkbox);
                $checkbox.prop('disabled', false);
                $checkbox.closest('label.box-chooser__item').removeClass('box-chooser__item--active').removeClass('box-chooser__item--inactive');
            });
        }

        var $checkedPayment = $('.js-order-payment-input:checked');
        if ($checkedPayment.length > 0) {
            $checkedPayment.closest('label.box-chooser__item').removeClass('box-chooser__item--inactive').addClass('box-chooser__item--active');
        }
    };

    Shopsys.order.onTransportChange = function () {
        var $this = $(this);
        var checked = $this.prop('checked');
        var checkedId = $this.data('id');

        if (checked) {
            // uncheckOtherTransports
            $('.js-order-transport-input:checked').each(function (i, checkbox) {
                var $checkbox = $(checkbox);
                var id = $checkbox.data('id');
                if (id !== checkedId) {
                    $checkbox.prop('checked', false);
                    $(this).closest('label.box-chooser__item').removeClass('box-chooser__item--active');
                }
            });

            $this.closest('label.box-chooser__item').addClass('box-chooser__item--active');
        } else {
            $this.closest('label.box-chooser__item').removeClass('box-chooser__item--active');
        }

        Shopsys.order.updatePayments();
    };

    Shopsys.order.onPaymentChange = function () {
        var $this = $(this);
        var checked = $this.prop('checked');
        var checkedId = $this.data('id');

        if (checked) {
            // uncheckOtherPayments
            $('.js-order-payment-input:checked').each(function (i, checkbox) {
                var $checkbox = $(checkbox);
                var id = $checkbox.data('id');
                if (id !== checkedId) {
                    $checkbox.prop('checked', false);
                    $(this).closest('label.box-chooser__item').removeClass('box-chooser__item--active');
                }
            });

            $this.closest('label.box-chooser__item').addClass('box-chooser__item--active');
        } else {
            $this.closest('label.box-chooser__item').removeClass('box-chooser__item--active');
        }

        Shopsys.order.updateTransports();
    };

    Shopsys.order.updateContinueButton = function () {
        var checkedTransport = $('.js-order-transport-input:checked');
        var checkedPayment = $('.js-order-payment-input:checked');

        if (checkedTransport.length === 1 && checkedPayment.length === 1) {
            $('#transport_and_payment_form_save').removeClass('btn--disabled');
        } else {
            $('#transport_and_payment_form_save').addClass('btn--disabled');
        }
    };

    Shopsys.register.registerCallback(Shopsys.order.init);

})(jQuery);
