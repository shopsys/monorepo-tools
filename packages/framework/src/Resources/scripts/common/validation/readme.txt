Client-side validation requires:
* subform must have HTML container with attribute id="{form_name}_{subform_name}" eg.: customer_deliveryAddressData
* collection of inputs or subforms must have HTML container with attribute id="{form_name}_{collection_name}" eg.: product_parameters
* when dynamically adding new collection item, this code must be added:
	Shopsys.validation.addNewItemToCollection('#{form_name}_{collection_name}', index);
* when dynamically removing new collection item, this code must be added:
  Shopsys.validation.removeItemFromCollection('#{form_name}_{collection_name}', index);
