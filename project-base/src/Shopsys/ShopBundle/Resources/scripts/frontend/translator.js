(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.translator = Shopsys.translator || {};

	Shopsys.translator.trans = function (id, parameters, domain) {
		// Message ID is translated by JS compiler to translated message
		// in corresponding domain. So the only thing left is to replace
		// parameters' placeholders by actual values.

		return Shopsys.translator.replaceParameters(id, parameters);
	};

	Shopsys.translator.transChoice = function (id, number, parameters, domain) {
		var pluralized = Translator.pluralize(id, number, document.documentElement.lang.replace('-', '_'));
		if (pluralized === undefined) {
			pluralized = id;
		}

		return Shopsys.translator.replaceParameters(pluralized, parameters);
	};

	Shopsys.translator.replaceParameters = function (message, parameters) {
		for (var parameterName in parameters) {
			var parameterValue = parameters[parameterName];

			// replace all occurrences
			message = message.split(parameterName).join(parameterValue);
		}

		return message;
	};

})(jQuery);
