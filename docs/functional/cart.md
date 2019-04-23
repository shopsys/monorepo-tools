# Cart
This article describes functional behavior of a cart. Article shortly summarizes its promo codes, behavior of cart content when user logs in and others.

## Add to cart
- a user can add the product to cart from the product detail or from the product listing (except for the product with variants as described in [another article](./behavior-of-product-variants.md))
- after adding the product to the cart, a pop up window is displayed.
There is information about adding the product to the cart together with the button “Go to cart”.
When the product is no longer available there is information about that in pop up window.
## Cart overview in header
- shows the amount and total price with VAT of products in the cart
- shows the information about empty cart
- shows current information about cart and changes even without page refresh - the update is done by AJAX
## Page cart
### URL
- URL has format www.youreshopurl.com/cart (in English version)
### The cart can be in these states:
- **empty** - nothing was added to the cart yet, cart shows an image of empty cart and the user is informed that the cart is empty
- **contains products** - the user can see a list of products which have been added to the cart, user can change quantity of products or remove product from the cart - these changes are modified by Ajax
### Price
- total price including VAT is automatically recalculated (without page refresh) if the product is removed or the amount is changed
### Promo code
- the input for entering the promo code is in the cart on the Front-end
- after entering the promo code in the cart, total price with VAT is recalculated using the button "Apply"
- the discount is applied for every cart item individually
- promo code can be removed from the cart and total price with VAT will be recalculated automatically
## Order flow
- the cart is the first step of order followed by a choice of shipping and payment
## Persistence
- the cart, including its content, is stored in the database for:
- 60 days for unregistered user
- 120 days for registered user
## Notification about product changes
- if the product is added to the cart and its price is changed or is not available anymore, the user is informed of the changes by a flash message
- the information is displayed on any page after page refresh
## Cart behavior when logging in, logging out and register the user
- if an unregistered user adds the product to the cart and makes the registration (the system logs the user in) then the contents of the cart will be retained
- if an unsigned user adds the product to the cart and then logs in, the contents of both unsigned and signed carts will merge
- after the user logs out, the contents of the cart will be removed
- if the logged in user adds the product to the cart and logs out, the contents of the cart will be retained for next login
