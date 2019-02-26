# Configuring image sizes for individual devices width
In Shopsys Framework you can configure image sizes for individual devices width. This is allowed by HTML element [Picture element (see more in Mozilla official documentation)](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/picture).

## Introduction
This document serves for introducing you with the process of managing images on Shopsys Framework.

*Note: In order to get correct image sizes of additional images then your original image must be larger than highest size in additional image size.*

## Configuration file
In order to set right sizes for individual devices width you have to configure `src/Shopsys/ShopBundle/Resources/config/images.yml`.

Let us explain example code in configuration file `images.yml` of attribute `additionalSizes`. The following code show section `product` with types `gallery` and `main`.
```yml
-   name: product
    class: Shopsys\FrameworkBundle\Model\Product\Product
    types:
        -   name: gallery
            multiple: true
            sizes:
                -   name: detail
                    width: 200
                    height: 300
                    crop: false
                    occurrence: 'Front-end: Product detail, when selected'
                    additionalSizes:
                       - {width: 1100, height: ~, media: "(min-width: 1200px)"}
                       - {width: 275, height: ~, media: "(max-width: 480px)"}
                -   name: list
                    width: 100
                    height: 100
                    crop: true
                    occurrence: 'Front-end: Product detail'
        -   name: main
            sizes:
                -   name: ~
                    width: 200
                    height: 300
                    crop: false
                    occurrence: 'Front-end: Product detail, Product list'
```
For type `gallery` and size `detail` are set two additional sizes. First is image width `1100px` for devices with minimal width `1200px`. Second one is image width `275px` for devices with maximal width `480px`. In other cases there is image with width `200px`.

## How to generate images with modified sizes
In case you modified image sizes in file `images.yml`, then would be needed to remove yet generated images. You approach that by removing images for modified size name of appropriate section. Folder path would look like `web/content/images/<section-name>/<type-name>`.

*Note: Be aware of not removing folder `original` in path `web/content/images/<section-name>`.*

### Example
Assume we want to add new image size for the main image above gallery at the product detail page. We want affect only devices with maximal browser width `480px`. Image size for new device width should be `410px`. You can achieve that by following steps.

1. Add new value for attribute `product.gallery.detail.additionalSizes` in configuration file `images.yml`.
```diff
    -   name: product
        class: Shopsys\FrameworkBundle\Model\Product\Product
        types:
            -   name: gallery
                multiple: true
                sizes:
                    -   name: detail
                        width: 200
                        height: 300
                        crop: false
                        occurrence: 'Front-end: Product detail, when selected'
+                       additionalSizes:
+                           - {width: 410, height: ~, media: "(max-width: 480px)"}
```
2. Remove all images in `web/content/images/product/default`. This step is applied only if there already exist some generated images for additional sizes.
