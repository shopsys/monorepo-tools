(function ($) {

	SS6 = window.SS6 || {};
	SS6.translator = SS6.translator || {};

	SS6.translator.trans = function (id, parameters, domain) {
		// Message ID is translated by JS compiler to translated message
		// in corresponding domain. So the only thing left is to replace
		// parameters' placeholders by actual values.

		return SS6.translator.replaceParameters(id, parameters);
	};

	SS6.translator.transChoice = function (id, number, parameters, domain) {
		var pluralized = Translator.pluralize(id, number, document.documentElement.lang.replace('-', '_'));
		if (pluralized === undefined) {
			pluralized = id;
		}

		return SS6.translator.replaceParameters(pluralized, parameters);
	};

	SS6.translator.replaceParameters = function (message, parameters) {
		for (var parameterName in parameters) {
			var parameterValue = parameters[parameterName];

			// replace all occurrences
			message = message.split(parameterName).join(parameterValue);
		}

		return message;
	};

})(jQuery);
