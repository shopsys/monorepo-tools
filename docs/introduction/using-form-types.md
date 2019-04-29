# Using Form Types
In this article we will show what types you can use when creating or editing forms, what options they have and what they do.

We use two types of naming for form types:
- `*FormType` - This type is used in Controllers to create forms and their views on front-end or they are used in other FormTypes as sub-forms
- `*Type` - This type is used only in other FormTypes to ease adding fields with their own widgets and to add to their reusability

## Default options
Every form type has some default options that can be used for various things.

### macro
Defaults to `null`.  
This option is used as array with two options (`name`, `recommended_length`) and its currently used for working with SEO:
- `name` - this option has two values that can be used
    - `seoFormRowMacros` - this value shows single domain `TextType` field with information about current length and recommended length
    - `seoFormRowMacros.multidomainRow` - this value shows multidomain `TextType` field with information about current length and recommended length
- `recommended_length` - it's an integer value that is used to show recommended maximal length of a text

### icon_title
Defaults to `null`.  
This option is used to add information icon after form field that shows tooltip with message after hovering.
To show message you need to fill this option with string that you want to show in the tooltip.

### display_format
Defaults to `null`.  
You can use this option if you want to have no padding in your rendered form_row.
To achieve this you need to fill this option with constant `FormRenderingConfigurationExtension::DISPLAY_FORMAT_MULTIDOMAIN_ROWS_NO_PADDING`

### js_container
Defaults to `null`.  
This option is used as array with two options (`container_class`, `data_type`) and can be used to wrap your form_row in `<div class="{{ js_container.container_class }}" data-type="{{ js_container.data_type }}">`

### is_plugin_data_group
*Using the `is_plugin_data_group` option in forms has been deprecated since Shopsys Framework 7.2 and it will be removed eventually.*  
Defaults to `false`.  
This option can be set to `true` in empty FormType if you want to add some part of a form from some plugin.
You can learn more about this in [separate article](../extensibility/extending-form-from-plugin.md)

### render_form_row
Defaults to `true`.  
If you want to add your FormType and don't render its `form_label` and `form_errors` you can set this option to false.
This can be set to create FormTypes that can be used in other FormTypes like the ones that are listed below.

## Form types
We created some form types which can help you with creating your own form types.
Here you can find an information about what they do and what options they have.

### [GroupType](../../packages/framework/src/Form/GroupType.php)
`GroupType` is used for creating groups of containers.
It is not mapped onto any property and it inherits data into group so you can work with forms in this group the same way as you were before.
`GroupType` makes sure to render your fields into nicely styled `div` wrapper.
`GroupType` comes with a few options that you can use for even more comfortable work.

#### label
This option is used for displaying heading of section, for example in `CustomerFormType` all user data like name, last name or email address are all in section with label `Personal Data`.

### [DisplayOnlyCustomerType](../../packages/framework/src/Form/DisplayOnlyCustomerType.php)
Displays name of a registered customer along with a link to his/her detail.
If there is no customer set, `unregistered customer` text will be displayed instead.

### [DisplayOnlyType](../../packages/framework/src/Form/DisplayOnlyType.php)
Sometimes form needs to only display information but does not need to change and persist this data, for this usages
there is `DisplayOnlyType` which does not map property onto `entity` and let you to display your own data.

### [DisplayOnlyUrlType](../../packages/framework/src/Form/DisplayOnlyUrlType.php)
Displays custom URL based on routing system.

### [LocalizedFullWidthType](../../packages/framework/src/Form/LocalizedFullWidthType.php)
Displays localized field in vertical order of full-width label and inputs.

### [OrderItemsType](../../packages/framework/src/Form/OrderItemsType.php)
Displays editable table of `OrderItems` from provided `Order`.

### [WarningMessageType](../../packages/framework/src/Form/WarningMessageType.php)
Displays highlighted message with warning icon.

### [LocalizedType](../../packages/framework/src/Form/Locale/LocalizedType.php)
Compound type that renders one form of given type for each locale.
Returns array indexed by locale.

#### entry_type
Defaults to `TextType::class`.  
It’s used to define what FormType should be used for the inner forms.

#### entry_options
An array of options that is used for every inner form.

#### main_constraints
An array of constraints that is used for field with the same locale as administration.

### [CategoriesType](../../packages/framework/src/Form/CategoriesType.php)
Displays a tree of all categories for given `domain_id` with checkboxes for each category created by `CategoryCheckBoxType` and returns array indexed by checked category ids.

#### domain_id
Required option that defines for what domain should the categories be listed.

### [CategoryCheckboxType](../../packages/framework/src/Form/CategoryCheckboxType.php)
Creates checkbox and label with category name if name of the form is the same as category id.
Adds `visible`, `category_name`, `has_children` and `level` vars from category on domain for given `domain_id` to `FormView` so you can easily work with the checkbox.

#### domain_id
Required option that defines from what domain should the category be listed.

### [ColorPickerType](../../packages/framework/src/Form/ColorPickerType.php)
Displays text field with box of given color that shows color picker when clicked.

### [DatePickerType](../../packages/framework/src/Form/DatePickerType.php)
Displays field that shows date picker when clicked.

#### format
Defaults to `DatePickerType::FORMAT_PHP`.  
Defines in what format should be the date shown.
DatePickerType has 2 constants that can be used:
- FORMAT_PHP = 'dd.MM.yyyy'
- FORMAT_JS = 'dd.mm.yy'

### [DisplayOnlyDomainIconType](../../packages/framework/src/Form/DisplayOnlyDomainIconType.php)
Displays domain icon with the domains name for given domain ID in `data` option.

### [DomainsType](../../packages/framework/src/Form/DomainsType.php)
Displays list of non-required checkboxes for every domain with the domain name as label.

### [DomainType](../../packages/framework/src/Form/DomainType.php)
Displays select box with all domain names or domain URLs.

#### displayUrl
Defaults to `false`.  
If you set this option to `true`, domain url will be shown instead of domain name.

### [FileUploadType](../../packages/framework/src/Form/FileUploadType.php)
Displays a widget that lets you to upload files by dragging them to the widget or selecting them from your computer.
After a file or files are uploaded it shows box for every file and lets you to download the files or delete them.

#### file_constraints
An array of constraints that should be applied for the uploaded file.

#### multiple
Boolean option that defines if you are able to upload more than one file.

#### info_text
Required option that needs to be string or null and is a text that is shown under the upload icon.

### [FriendlyUrlType](../../packages/framework/src/Form/FriendlyUrlType.php)
Displays a select box with domain urls and text field that lets you to create friendly url on selected domain with your valid slug.
Uses `DomainType` to display select box with domain urls.

### [UrlListType](../../packages/framework/src/Form/UrlListType.php)
Uses `FriendlyUrlType` to display a list of friendly URLs for each domain that lets you delete and create friendly URLs with unique slugs and select which URL should be the main for the domain.

#### route_name
Defines which route should the URLs go into.

#### entity_id
Defines what is the entity ID that the URLs are assigned to.

### [ImageUploadType](../../packages/framework/src/Form/ImageUploadType.php)
Uses `FileUploadType` displays a widget to upload images and to work with them (same as `FileUploadType`).

#### entity
Defines which entity should the images be assigned to.

### [OrderListType](../../packages/framework/src/Form/OrderListType.php)
Displays a list of orders for given `user`.
The list contains `order number`, `created on`, `billing address`, `shipping address`, `total price including VAT`, `status` and a link to order detail.
Displays text `Customer has no orders so far.` if User doesn't have any orders.

#### user
Required option that defines for what user should the orders be listed.

### [PriceTableType](../../packages/framework/src/Form/PriceTableType.php)
Displays a table with prices for each currency that lets you to add input price without Vat.
After saving widget the price that you added (which you can edit) and final prices with and without Vat (that you cannot edit) are updated.
Price without Vat is not the same as you added so the final price with Vat can be an integer.

### [ProductCalculatedPricesType](../../packages/framework/src/Form/ProductCalculatedPricesType.php)
Adds a widget that shows a table with prices of `product` that are calculated for every pricing group and every domain.
If `product` is null, it shows table with every pricing group for every domain.

#### product
Required option that defines for what product should the prices be listed.

### [ProductsType](../../packages/framework/src/Form/ProductsType.php)
Displays a list of products.
The widget adds button that after clicking opens a popup window that lets you to search and to pick products except for `main_product`.
After picking you can sort or delete picked products.

#### allow_add
Boolean option that defines if products can be added to the list.

#### allow_delete
Boolean option that defines if products can be removed from the list.

#### sortable
Boolean option that defines if products in the list can be sorted.

#### allow_main_variants
Boolean option that defines if main variants can be added.

#### allow_variants
Boolean option that defines if variants can be added.

#### main_product
Option that defines for what product are you picking products. `main_product` can't be picked.

#### label_button_add
Option that defines text of button for adding products.

### [ProductType](../../packages/framework/src/Form/ProductType.php)
Displays a widget that lets you to pick one product.
It has one `input` with `placeholder` or name of picked product and a `button` that after clicking opens a popup window that lets you to search and to pick a product.

#### placeholder
Option that lets you to add a placeholder text when you dont have picked product.

#### allow_delete
Boolean option that defines if product can be deleted.

#### allow_main_variants
Boolean option that defines if main variants can be picked.

#### allow_variants
Boolean option that defines if variants can be picked.

### [SingleCheckboxChoiceType](../../packages/framework/src/Form/SingleCheckboxChoiceType.php)
Displays a list of choices shown as checkboxes.

### [SortableValuesType](../../packages/framework/src/Form/SortableValuesType.php)
Displays a list of values that lets you add values from a select box, remove them form the list or sort them as you like.
Returns array with sorted IDs that have been picked and sorted.

#### allow_add
Boolean option that defines if products can be added to the list.

#### allow_delete
Boolean option that defines if products can be removed from the list.

### [MultidomainType](../../packages/form-types-bundle/src/MultidomainType.php)
Compound type that renders one form of given type for each domain.
The data of the inner forms are returned as an array indexed by the domain ID.

#### entry_type
Defaults to `TextType::class`.  
The type of the inner form.

#### entry_options
Defaults to `[]`.  
The options of the inner forms.

#### options_by_domain_id
Defaults to `[]`.  
The options of the inner forms based on the domain ID.
Provide arrays indexed by the domain ID, values are merged with the `entry_options`.

### [YesNoType](../../packages/form-types-bundle/src/YesNoType.php)
Natural looking choice type for boolean value inputs.
A boolean value is accepted/returned as data.
A null value can be accepted/returned when no radio button is checked.
