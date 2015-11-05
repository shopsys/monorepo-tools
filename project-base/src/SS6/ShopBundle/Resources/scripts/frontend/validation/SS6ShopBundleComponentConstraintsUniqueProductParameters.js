(function ($) {

	SS6ShopBundleComponentConstraintsUniqueProductParameters = function() {
		var self = this;
		this.message = '';

		/**
		 * This method is required
		 * Should return an error message or an array of messages
		 */
		this.validate = function (value) {
			var uniqueCollectionValidator = new SS6ShopBundleComponentConstraintsUniqueCollection();
			uniqueCollectionValidator.message = this.message;
			uniqueCollectionValidator.fields = new Array('parameter', 'locale');
			uniqueCollectionValidator.allowEmpty = false;

			return uniqueCollectionValidator.validate(value);
		};

	};

})(jQuery);