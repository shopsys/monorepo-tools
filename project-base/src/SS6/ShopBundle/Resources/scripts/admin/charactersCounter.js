(function ($){

	SS6 = window.SS6 || {};
	SS6.charactersCounter = SS6.charactersCounter || {};

	SS6.charactersCounter.Counter = function ($counter) {
		var self = this;
		var $input = $counter.find('.js-characters-counter-input input, textarea.js-characters-counter-input');
		var $info = $counter.find('.js-characters-counter-info');
		var recommendedLength = $info.data('recommended-length');

		this.init = function() {
			$input.keyup(self.countCharacters);
			self.countCharacters();
		};

		this.countCharacters = function() {
			var currentLength = $input.val().length;
			if (currentLength === 0) {
				currentLength = $input.attr('placeholder').length;
			}
			$info.text(SS6.translator.trans(
				'Je využito %currentLength% znaků. Doporučujeme maximálně %recommendedLength%',
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