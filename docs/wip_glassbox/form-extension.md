# Form Extension
In this document we will be explaining the actual state of our forms and their extensions. Right now,
solution of extending forms is not complete and there will be several tasks that will assure better extending.

At this time we do not have every single form in our application ready for extension, the list of not prepared
forms for extensions is below:

* `ProductMassActionFormType`
* `VariantFormType`
* `OrderItemFormType`
* `OrderPaymentFormType`
* `OrderTransportFormType`

If you want to see example of extending one this forms please check this [link](https://github.com/shopsys/shopsys/commit/d6b84bf54c0b47c72eacc82d540987dd8078fa13).

## What can we use for creating our own forms
We created some form types which can help you with creating your own form types. Let's take a look at them

### [GroupType](../../packages/framework/src/Form/GroupType.php)
Group type is used for creating groups of containers. It is not mapped onto any property and it inherits data into group
so you can work with forms in this group same as you were before. `GroupType` makes sure to render your fields into nicely
styled `div` wrap.

`GroupType` comes with few options that you can use for even more comfortable work.

#### `label`
Label is used for displaying heading of section for example in `CustomerFormType` all user data like name, last name or
email address are all in section with label 'Personal Data'

#### `is_group_container_to_render_as_the_last_one`
Right now we need to tell form type which GroupType is the last one to render form correctly, just remember to add this
option if it is last group of fields.

### [DisplayOnlyCustomerType](../../packages/framework/src/Form/DisplayOnlyCustomerType.php)
Displays name of a registered customer along with a link to his/her detail.
If there is no customer set, `unregistered customer` text will be displayed instead.

### [DisplayOnlyType](../../packages/framework/src/Form/DisplayOnlyType.php)
Sometimes form needs to only display information but does not need to change and persist this data, for this usages
there is `DisplayOnlyType` which does not map property onto `entity` and let you to display your own data.

### [DisplayOnlyUrlType](../../packages/framework/src/Form/DisplayOnlyUrlType.php)
For displaying custom URL based on routing system, there can be used `DisplayOnlyUrlType`.

### [LocalizedFullWidthType](../../packages/framework/src/Form/LocalizedFullWidthType.php)
For displaying localized field in vertical order of full-width label and inputs, there can be used `LocalizedFullWidthType`.

### [OrderItemsType](../../packages/framework/src/Form/OrderItemsType.php)
Displays editable table of OrderItems from provided Order.

### [WarningMessageType](../../packages/framework/src/Form/WarningMessageType.php)
Sometimes the form needs to contain some information that is important for viewer, for this usage there is  `WarningMessageType`
that shows highlighted message with warning icon.

## Adding fields into already existing form types
Imagine that you have added new property into `Product entity` and you want this property to be set in administration
using forms.

For this cases you can use `FormExtensions` which extends `AbstractTypeExtension`, which has function called `getParrent()`,
implement this function and return `class` of `ProductFormType` and add your fields into form.

## Adding your own form type
If you want to display form type differently or you just want to create your own form type.

### Changing existing form type
If you want to change way the form is rendered or if you want to add your own classes you need to follow few steps.
Many of our forms have their own
theme which describe on how to render form row. These files are situated in `Resource/views/Form` folder.
Copy theme of form type you want to change into `project-base` directory and replace file you want to
change with your own file in `twig/form_themes` in `app/config/packages/twig.yml` file. Now you can change whatever you want.

Remember that files that you copy into `project-base` cannot be automatically upgraded with newer versions of Shopsys.

If you want to change the whole style of forms you need to copy whole `theme.html.twig` which defines the style of
rendering default symfony rows

### Adding your own form type
You can add your own form type if you want. Just create your own FormType, for example `AmazingFormType`, if you want
to influence how this form type will be displayed you need to create theme for this form type.

Create new file into `Resource/views/Form` directory, Name it for example `amazingFields.html.twig` and register
this theme into `twig/form_themes` in `app/config/packages/twig.yml`.

Now you can define how form type will be rendered. There are two options with which you can influence rendering of form type.

### `form_row`
Form row is used for rendering whole row, including the label of form, your icons etc. `form_row` should call `form_widget`.

### `form_widget`
Form widget defines rendering of actual input.

Just remember that you need to let `Symfony` know which form type you are defining. If i would try to define
rendering of my `AmazingFormType` my `form_widget` and `form_row` should be named `amazing_widget` and `amazing_row`.
