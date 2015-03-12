(function ($){

	SS6 = SS6 || {};
	SS6.categoryTree = SS6.categoryTree || {};
	SS6.categoryTree.FormItem = SS6.categoryTree.FormItem || {};

	SS6.categoryTree.FormItem = function ($item, parent) {
		var self = this;
		self.STATUS_OPENED = 'opened';
		self.STATUS_CLOSED = 'closed';
		self.STATUS_NONE = 'none';

		var status = null;
		var $statusIcon = $item.find('.js-category-tree-form-item-icon:first');
		var $checkbox = $item.find('.js-category-tree-form-item-checkbox:first');
		self.parent = parent;
		self.children = [];

		var $childrenContainer = $item.find('.js-category-tree-form-children-container:first');

		this.init = function () {
			initChildren();
			initStatus();

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
			// status could be set to "opened" by children
			if (status === null) {
				if (self.children.length > 0) {
					self.close(true);
				} else {
					setStatus(self.STATUS_NONE);
				}

				if ($checkbox.is(':checked')) {
					if (self.parent instanceof SS6.categoryTree.FormItem) {
						self.parent.openForceWithParentCascade();
					}
				}
			}
		};

		this.statusToggle = function () {
			if (status === self.STATUS_CLOSED) {
				self.open();
			} else if (status === self.STATUS_OPENED) {
				self.close();
			}
		};

		this.open = function () {
			if (status === self.STATUS_CLOSED) {
				$childrenContainer.slideDown(function () {
					setStatus(self.STATUS_OPENED);
				});
			}
		};

		this.openForceWithParentCascade = function () {
			$childrenContainer.show();
			setStatus(self.STATUS_OPENED);
			if (self.parent instanceof SS6.categoryTree.FormItem) {
				self.parent.openForceWithParentCascade();
			}
		};

		this.close = function (force) {
			if (force === true) {
				$childrenContainer.hide();
				setStatus(self.STATUS_CLOSED);
			} else if (status === self.STATUS_OPENED) {
				$childrenContainer.slideUp(function () {
					setStatus(self.STATUS_CLOSED);
				});
			}
		};

		var setStatus = function (newStatus) {
			status = newStatus;
			updateStatusIcon();
		};

		var updateStatusIcon = function () {
			$statusIcon.removeClass('fa-plus-square-o fa-minus-square-o fa-square-o');
			switch (status) {
				case self.STATUS_OPENED:
					$statusIcon.addClass('fa-minus-square-o');
					break;
				case self.STATUS_CLOSED:
					$statusIcon.addClass('fa-plus-square-o');
					break;
				case self.STATUS_NONE:
					$statusIcon.addClass('fa-square-o');
					break;
			}
		};

	};

})(jQuery);
