# Behavior of Product Variants
Product variants are specific type of product and this article describes their specific behavior.
Variants are used for products that are suitable to associate. These are products that vary for example by size, pattern, color, etc. A typical example of using variants can be clothing where one model of t-shirt differs only in size.

## Dictionary of terms
**Main product** - An abstract product that groups specific variations into one product detail. The main product is for example T-shirt. This product is displayed in the product list and can’t be purchased. You can buy a specific variant on the detail of the main product.

**Variant** - It is a specific product that can be purchased on the detail of the main product. Variants for the main product T-shirt are e.g.T-shirt size S, T-shirt size M, T-shirt size L.
Specific variants are not displayed in the product list.

## How to create a variation
1. Within the administration in the products overview is a button to create a variant.
2. In the form for creating a variant is necessary to choose main product and then its variant from list of existing products.
3. The “create” button confirms the creation of the product with variants.
4. Information from main product are copied from the main product, which is the main product for the selected variants.
5. The original main product is also added to the list of variants and can be purchased.
6. We have created the main product, which is abstract and specific variants assigned to that product.

## Behavior of product variants
- ### Administration - main product:
  - there is the icon "H" for the main product on the products overview
  - it is possible to add/remove variants on the main product card
  - all attributes (except the catalogue number, PartNo, EAN, inventory and price) can be edited on the main product card
- ### Administration - variants:
  - there is the icon "V" for the variant on the products overview
  - the main product is listed on the card of the variant
  - all attributes (except the short description, description and assignment to the category) can be edited on the card of the variant
  - on the card of variant, it is possible to fill in the alias of the variant which replaces the name of the variant on the Front-end (for example, instead of the name “T-shirt size S” will be displayed only “Size S” in the variants table)
- ### Front-end - product overview:
  - only main product is displayed in the product overview
- ### Front-end - price:
  - for the main product, the customer sees the cheapest price as the price “From” (if the variants have a different price)
- ### Front-end - add to cart:
  - the customer cannot add the main product to the cart
  - the customer can add only individual variants to the cart
  - variant can be added to the cart from product detail of main variant
- ### Front-end - search:
  - search functionality and search autocomplete search only for the main products, not variants
  - the results show only the main products, not the individual variants
- ### Front-end - filtering:
  - only the main product parameters are filtered
- ### Front-end - availability:
  - the main product shows the best availability from the assigned variants
- ### Front end - product detail:
  - the main product displays the name, image, flag, availability, manufacturer, description, technical parameters and product variants
  - each variant displays the image, name, price, option to enter the number of pieces and button to buy
- ### Front-end -  visibility of variants:
  - conditions are same as for a regular product are applied ([Product Visibility and Exclude from Sale](./product-visibility-and-exclude-from-sale.md)) + these cases:
      - if the main product is set as hidden, its variants are hidden too
      - if all product variants are hidden, then the main product is hidden too
- ### Front-end - exclude from sale:
  - conditions are same as for a regular product are applied ([Product Visibility and Exclude from Sale](./product-visibility-and-exclude-from-sale.md)) + these cases:
      - if the main product is excluded from sale, then variants are not shown on the detail of the main product either
      - if all variants are excluded from the sale, then the behavior is the same to the previous point
- ### XML feeds:
  - for feeds Heureka.cz and Zboží.cz: variants are generated as separate items with a common tag <ITEMGROUP_ID>
  - feed for Google purchases the variants does not take into account
