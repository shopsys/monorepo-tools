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

If you want to see example of extending one of these forms please check this [link](https://github.com/shopsys/shopsys/commit/d6b84bf54c0b47c72eacc82d540987dd8078fa13).

## What can we use for creating our own forms
We created some form types which can help you with creating your own form types. You can find them in [separate article](../introduction/using-form-types.md)

## Adding fields into already existing form types in administration
Imagine that you have added new property into `Product entity` and you want this property to be set in administration
using forms.

For this cases you can use `FormExtensions` in namespace `ShopBundle/Form/Admin` that extends `Symfony\Component\Form\AbstractTypeExtension`, which has function called `getExtendedType()`.
Implement this function and return `class` of `ProductFormType` and add your fields into form.
If you create new extension you need to register it in [`forms.yml`](../../project-base/src/Shopsys/ShopBundle/Resources/config/forms.yml)

## Changing rendering of already existing form type
If you want to change way the form is rendered or if you want to add your own classes you need to follow few steps.
Many of our forms have their own theme which describe on how to render form row. These files are located in `Shopsys/FrameworkBundle/Resources/views/Admin/Form` folder.
Copy theme of form type you want to change into your project namespace and replace file you want to
change with your own file in `twig/form_themes` in `app/config/packages/twig.yml` file. Now you can change whatever you want.

Remember that files that you copy into your project cannot be automatically upgraded with newer versions of Shopsys Framework.

If you want to change the whole style of rendering forms in administration you need to copy whole [`theme.html.twig`](../../packages/framework/src/Resources/views/Admin/Form/theme.html.twig) which defines the style of
rendering default symfony rows.
You can read more about `theme.html.twig` below.

## Adding your own form type
You can add your own form type if you want. Just create your own FormType, for example `MyAmazingFormType`, if you want
to influence how this form type will be displayed you need to create theme for this form type.

Create new file into `Shopsys/ShopBundle/Resources/views/Front/Form` directory, name it for example `myAmazingFields.html.twig` and register
this theme into `twig/form_themes` in `app/config/packages/twig.yml`.

Now you can define how form type will be rendered. There are two options with which you can influence rendering of form type.

### `form_row`
Form row is used for rendering whole row, including the label of form, your icons etc. `form_row` should call `form_widget`, `form_errors` and `form_label`.

### `form_widget`
Form widget defines rendering of actual input.

Just remember that you need to let `Symfony` know which form type you are defining. If you want to define
rendering of `MyAmazingFormType` your `form_widget` and `form_row` should be named `my_amazing_widget` and `my_amazing_row`.

## `theme.html.twig`
This template is used for custom rendering of forms and form fields and it extends `form_div_layout.html.twig` from Symfony.
There are two `theme.html.twig` files as one is used for [administration](../../packages/framework/src/Resources/views/Admin/Form/theme.html.twig) and the other for [front-end](../../project-base/src/Shopsys/ShopBundle/Resources/views/Front/Form/theme.html.twig).
It contains definition of blocks that are used for rendering forms
- `form_start` - renders the start tag of the form
- `form_end` - renders the end tag of the form
- `form_row` - renders the label, any errors, and the HTML form widget for the given field
    - `form_widget` - renders HTML form widget for the given field
    - `form_errors` - renders block with list of validation errors for the given field
    - `form_label` - renders label for the given field including red asterisk if the field is required

and blocks of custom form widgets for various [FormTypes](../introduction/using-form-types.md) eg.:
- `date_picker_widget` - is rendered as `form_widget` for [`DatePickerType`](../../packages/framework/src/Form/DatePickerType.php)

## Changing order of groups and fields
All form types contain option called `position`. With this option you can specify position of your group or field **on the same hierarchical layer**.

Option `position` can contain four different values:

```php
$builder
    ->add('g', TextType::class, ['position' => 'last'])
    ->add('a', TextType::class, ['position' => 'first'])
    ->add('c', TextType::class)
    ->add('f', TextType::class)
    ->add('e', TextType::class, ['position' => ['before' => 'f']])
    ->add('d', TextType::class, ['position' => ['after' => 'c']])
    ->add('b', TextType::class, ['position' => 'first']);
```

The output will be: A => B => C => D => E => F => G.

*Note: More examples can be found [here](https://github.com/egeloen/ivory-ordered-form/blob/master/doc/usage.md#position).*

### Changing order of existing groups and fields

Implementation of `FormBuilderInterface` contains method `setPosition` that can change order of existing field.

```php
$builder->get('a')->setPosition('first');
$builder->get('c')->setPosition(['after' => 'b']);
```
*Note: Because `FormBuilderInterface` doesn't declare method `setPosition`, your IDE will warn you that method doesn't exist.*
