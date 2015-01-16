function SS6ShopBundleComponentTransformersInverseArrayValuesTransformer() {
	this.reverseTransform = function(value, ele) {
		$.each(ele.children, function(){
			var thisValue = parseInt(this.domNode.value);
			if ($.inArray(thisValue, value) !== -1) {
				var index = value.indexOf(thisValue);
				value.splice(index, 1);
			} else {
				value.push(thisValue);
			}
		});
		return value;
	};
}