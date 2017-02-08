(function ($) {

	SS6 = SS6 || {};
	SS6.massActionConfirm = SS6.massActionConfirm || {};

	var isConfirmed = false;

	SS6.massActionConfirm.init = function ($container) {
		$container.filterAllNodes('.js-mass-action-submit').click(function () {
			var $button = $(this);
			if (!isConfirmed) {
				var action = $('.js-mass-action-value option:selected').text().toLowerCase();
				var selectType = $('.js-mass-action-select-type').val();
				var count;
				switch (selectType) {
					case SS6.constant('SS6\\ShopBundle\\Model\\Product\\MassAction\\ProductMassActionData::SELECT_TYPE_CHECKED'):
						count = $('.js-grid-mass-action-select-row:checked').length;
						break;
					case SS6.constant('SS6\\ShopBundle\\Model\\Product\\MassAction\\ProductMassActionData::SELECT_TYPE_ALL_RESULTS'):
						count = $('.js-grid').data('total-count');
						break;
				}
				SS6.window({
					content: SS6.translator.trans('Do you really want to %action% %count% product?', {'%action%': action, '%count%': count }),
					buttonCancel: true,
					buttonContinue: true,
					eventContinue: function () {
						isConfirmed = true;
						$button.trigger('click');
					}
				});

				return false;
			}
		});

	};

	SS6.register.registerCallback(SS6.massActionConfirm.init);

})(jQuery);