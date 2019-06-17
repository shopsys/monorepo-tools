# API Versioning

[Shopsys Backend API](/docs/backend-api/introduction-to-backend-api.md) is versioned for the same [reasons](/docs/contributing/backward-compatibility-promise.md) as the rest of the Shopsys Framework - to provide the users the certainty what kind of changes they can expect.
If you are interested in the topic of API versioning, you can read more e.g. on [restfulapi.net](https://restfulapi.net/versioning/).

Our API is versioned in the URL so you are able to recognize the version easily (e.g. `/api/v1/products`).
The API does not adhere to [Semantic Versioning](http://semver.org/spec/v2.0.0.html), we are using simple integer prefixed with `v` that is incrementing for each new version (i.e. `v1`, `v2`, `v3`, etc.).

Currently, there is only one released version - `v1` - and the API is marked as `@experimental` at the moment, so there might be some BC breaking changes in the near future.
Once the `@experimental` tag is removed, you can be sure what kind of changes you can expect in the API that won't break anything, and what kind of changes you can expect in the next version only as they are considered to be breaking the backward compatibility.

## What changes do we consider to be BC breaks in our backend API:
* removal of an endpoint
* removal of an attribute from an endpoint's response
* change of the data structure
* change of the data format
* adding a new required request parameter for an endpoint

## What changes are not considered as BC break:
* adding a new endpoint
* adding a new attribute to an endpoint's response
* adding a new optional parameter for an endpoint

## Using multiple API versions
It is probable that there will be multiple API versions supported at once and you will be able to use them next to each other in your project.
To enable the endpoints for a particular version, you just need to add them explicitly in your routing setting.
You can expect such instructions in the upgrading notes.
```diff
# /app/config/routing.yml
shopsys_api:
    resource: "@ShopsysBackendApiBundle/Resources/config/v1/routing.yml"
+ shopsys_api_v2:
+     resource: "@ShopsysBackendApiBundle/Resources/config/v2/routing.yml"
```
