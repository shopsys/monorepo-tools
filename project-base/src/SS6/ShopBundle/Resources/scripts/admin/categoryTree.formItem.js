(function ($){

	SS6 = SS6 || {};
	SS6.categoryTree = SS6.categoryTree || {};
	SS6.categoryTree.FormItem = SS6.categoryTree.FormItem || {};

	SS6.categoryTree.FormItem = function ($item, parent) {
		var self = this;
		var status = null;
		var $statusIcon = $item.find('.js-category-tree-form-item-icon:first');
		var $checkbox = $item.find('.js-category-tree-form-item-checkbox:first');
		self.parent = parent;
		self.children = [];

		var $childrenContainer = $item.find('.js-category-tree-form-children-container:first');

		this.init = function () {
			initChildren();
			initStatus();
			updateStatusIcon();

			$statusIcon.click(self.statusToggle);
		};

		var initChildren = function () {
			$childrenContainer.find('> .js-category-tree-form-item').each(function () {
				var childItem = new SS6.categoryTree.FormItem($(this), self);
				childItem.init();
				self.children.push(childItem);
			});
		};

		var initStatus = function () {
			// status could be set to "open" by children
			if (status === null) {
				if (self.children.length > 0) {
					self.close(true);
				} else {
					status = 'none';
				}

				if ($checkbox.is(':checked')) {
					if (self.parent instanceof SS6.categoryTree.FormItem) {
						self.parent.openForceWithParentCascade();
					}
				}
			}
		};

		this.statusToggle = function () {
			if (status === 'close') {
				self.open();
			} else if (status === 'open') {
				self.close();
			}
		};

		this.open = function () {
			if (status === 'close') {
				$childrenContainer.slideDown(function () {
					status = 'open';
					updateStatusIcon();
				});
			}
		};

		this.openForceWithParentCascade = function () {
			$childrenContainer.show();
			status = 'open';
			updateStatusIcon();
			if (self.parent instanceof SS6.categoryTree.FormItem) {
				self.parent.openForceWithParentCascade();
			}
		};

		this.close = function (force) {
			if (force === true) {
				$childrenContainer.hide();
				status = 'close';
			} else if (status === 'open') {
				$childrenContainer.slideUp(function () {
					status = 'close';
					updateStatusIcon();
				});
			}
		};

		var updateStatusIcon = function () {
			$statusIcon.removeClass('fa-plus-square-o fa-minus-square-o fa-square-o');
			switch (status) {
				case 'open':
					$statusIcon.addClass('fa-minus-square-o');
					break;
				case 'close':
					$statusIcon.addClass('fa-plus-square-o');
					break;
				case 'none':
					$statusIcon.addClass('fa-square-o');
					break;
			}
		};

	};

})(jQuery);
