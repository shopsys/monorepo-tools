# Domain, Multidomain, Multilanguage

During the development with the Shopsys Framework, it is possible to meet concepts - domain, multidomain, multilanguage.
This document explains what these terms mean.

How to work with them during the development of your project is described in the [How to set up domains and locales article](how-to-set-up-domains-and-locales.md).

## Domain
Domain can be understood as one instance of eshop data.
For example, just furniture can be bough on the domain shopsys-furniture.com while only electronics can be found on the domain shopsys-electro.com.
It is still one application with one product catalogue.
Access to these individual domains is provided through individual url addresses.
All domains share one common administration.

## Multidomain attribute
A distinct value of this attribute can be set for each domain.
An example of a multidomain attribute is a default pricing group for not logged customer.
This pricing group can be different for each domain.
An example of a not multidomain attribute is an EAN on the product.
This attribute is the same for each domain.
Please read more about implementation of multidomain attributes in [domain entities](../model/entities.md#domain-entity).

## Multilanguage attribute
A distinct value of this attribute can be set for each locale.
An example of a multilanguage attribute is a name of the product.
An example of a not multilanguage attribute is an EAN of the product.
Please read more about implementation of multilanguage attributes in [translation entities](../model/entities.md#translation-entity).

## Difference between multidomain and multilanguage attribute
A value of some multilanguage attribute will be the same for each domain with the same locale.
For example, when a name of the product is set as *A4tech mouse* for the locale *en* , this name of this product will be the same for each domain with the locale *en*.
While multidomain attribute can be set to different values for different domains regardless of the locale of the domain.
For example SEO headline of a product can be *A4tech mouse* on first domain and *A4tech mouse for professionals* on second domain.

It might be unclear whether an attribute is multidomain or multilanguage when filling data objects.
The decision matters because multidomain attributes are indexed by `domain ID` and multilanguage attributes are indexed by `locale` as is described in [data for multidomain or multilanguage fields](../model/entities.md#data-for-multidomain-or-multilanguage-field).

If you are not sure, we suggest to take a look into an appropriate entity (whether there is an array of *EntityDomain* or *EntityTranlsation* objects in a given field), form type (whether there is `LocalizedType` or `MultidomainType` used for a given field) or into administration (whether there are locale flags or domain icons for a given field).
