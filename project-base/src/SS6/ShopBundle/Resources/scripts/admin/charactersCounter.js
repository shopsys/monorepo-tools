(function ($){

	SS6 = window.SS6 || {};
	SS6.charactersCounter = SS6.charactersCounter || {};

	SS6.charactersCounter.Counter = function ($counter) {
		var self = this;
		var $input = $counter.find('.js-characters-counter-input input, input.js-characters-counter-input, textarea.js-characters-counter-input');
		var $info = $counter.find('.js-characters-counter-info');
		var recommendedLength = $info.data('recommended-length');

		this.init = function() {
			if ($input.length > 0) {
				$input.bind('keyup placeholderChange', countCharacters);
				countCharacters();
			}
		};

		var countCharacters = function() {
			var currentLength = $input.val().length;
			var placeholder = $input.attr('placeholder');
			if (currentLength === 0 && placeholder) {
				currentLength = placeholder.length;
			}
			$info.text(SS6.translator.trans(
				'Využito: %currentLength% znaků. Doporučeno max. %recommendedLength%',
				{
					'%currentLength%': currentLength,
					'%recommendedLength%': recommendedLength
				}
			));
		};

	};

	$(document).ready(function () {
		$('.js-characters-counter').each(function () {
			var instance = new SS6.charactersCounter.Counter($(this));
			instance.init();
		});
	});

})(jQuery);