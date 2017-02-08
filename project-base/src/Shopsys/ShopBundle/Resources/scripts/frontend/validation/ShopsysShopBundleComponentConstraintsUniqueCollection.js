(function ($) {

	ShopsysShopBundleComponentConstraintsUniqueCollection = function() {
		var self = this;
		this.message = '';
		this.fields = null;
		this.allowEmpty = false;

		/**
		 * This method is required
		 * Should return an error message or an array of messages
		 */
		this.validate = function (value) {
			var result = [];

			$.each(value, function(key1, value1) {
				$.each(value, function(key2, value2) {
					if (key1 !== key2 && areValuesEqual(value1, value2)) {
						result = self.message;
					}
				});
			});

			return result;
		};

		function areValuesEqual(value1, value2) {
			if (self.allowEmpty) {
				if (value1 === null || value1 === '' || value2 === null || value2 === '') {
					return false;
				}
			}

			if (self.fields === null) {
				return value1 === value2;
			} else {
				return areValuesEqualInFields(value1, value2);
			}
		}

		function areValuesEqualInFields(value1, value2) {
			for (var i = 0; i < self.fields.length; i++) {
				var field = self.fields[i];
				if (!areValuesSame(value1[field], value2[field])) {
					return false;
				}
			}

			return true;
		}

		function areValuesSame(value1, value2) {
			if (value1 instanceof Array && value2 instanceof Array) {
				return (value1.length === value2.length) && value1.every(function(element, index) {
					return element === value2[index];
				});
			} else {
				return value1 === value2;
			}
		}
	};

})(jQuery);