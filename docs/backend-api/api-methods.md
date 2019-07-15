# API Methods
All API methods calls except for token generation are secured via [OAuth2](/docs/backend-api/api-authentication-oauth2.md).
You need to provide token via `Authorization` header to access requested data like this: ```Authorization: Bearer eyJ0eXAiOiJKV...s3SKg```

## List of methods
- [Retrive access token](#retrieve-access-token)
- [Retrieve a product](#retrieve-a-product)
- [Retrieve a list of products](#retrieve-a-list-of-products)

**Retrieve access token**
----

* **URL**

  `/api/token`

* **Method**

  `POST`

*  **Data Params**

   **Required**

   `grant_type=client_credentials`
   `client_id=[string]`
   `client_secret=[string]`

* **Success Response**

  * **Code** `200` <br />
     **Content**
        ```
        {
            "token_type": "Bearer",
            "expires_in": 3600,
            "access_token": "eyJ0eXAiOiJKV...s3SKg"
        }
        ```

* **Error Response**

  * **Code** `401 UNAUTHORIZED` <br />
    **Content**
    ```
    {
        "error": "invalid_client",
        "message": "Client authentication failed"
    }
    ```

  OR

  * **Code** `400 Bad Request` <br />

* **Notes**
Token is valid for one hour.
You will need to generate new token after the first one expires.

**Retrieve a product**
----

* **URL**

  `/api/v1/products/{uuid}`

* **Method**

  `GET`

*  **URL Params**

   **Required**

   `uuid=[string]`

* **Success Response**

  * **Code** `200` <br />
    **Content**
    ```
    {
      "uuid": "2ad32b16-bfb4-4cfa-8bbe-331e630769c5",
      "name": {
          "en": "22\" Sencor SLE 22F46DM4 HELLO KITTY",
          "cs": "22\" Sencor SLE 22F46DM4 HELLO KITTY"
      },
      "hidden": false,
      "sellingDenied": false,
      "sellingFrom": "2000-01-16T00:00:00+00:00",
      "catnum": "9177759",
      "ean": "8845781245930",
      "partno": "SLE 22F46DM4",
      "shortDescription": {
          "1": "Television LED, 55 cm diagonal ...",
          "2": "Sencor SLE 22F46DM4 Hello Kitty je ..."
      },
      "longDescription": {
          "1": "Television LED, 55 cm diagonal ...",
          "2": "<p><strong>Sencor SLE 22F46DM4 ..."
      }
    }
    ```

* **Error Response**

  * **Code** `400 Bad Request` <br />
    **Content**
    ```
    {
        "code": 400,
        "message": "This UUID is not valid: f564da76-6d51-4ecd-b004-895c8019a23"
    }
    ```

  OR

  * **Code** `401 UNAUTHORIZED` <br />

  OR

  * **Code** `404 Not Found` <br />
    **Content**
    ```
    {
        "code": 404,
        "message": "Product with UUID 877a07d5-a276-49f8-9b73-6ac0edef83be does not exist."
    }
    ```

**Retrieve a list of products**
----

* **URL**

  `/api/v1/products`

* **Method**

  `GET`

*  **URL Params**

   **Optional**

   `uuids=[array of strings]`
   `page=[integer]`

* **Success Response**

  * **Code** `200` <br />
    **Content**
    ```
    [
       {
          "uuid": "2ad32b16-bfb4-4cfa-8bbe-331e630769c5",
          "name": {
              "en": "22\" Sencor SLE 22F46DM4 HELLO KITTY",
              "cs": "22\" Sencor SLE 22F46DM4 HELLO KITTY"
          },
          "hidden": false,
          "sellingDenied": false,
          "sellingFrom": "2000-01-16T00:00:00+00:00",
          "catnum": "9177759",
          "ean": "8845781245930",
          "partno": "SLE 22F46DM4",
          "shortDescription": {
              "1": "Television LED, 55 cm diagonal ...",
              "2": "Sencor SLE 22F46DM4 Hello Kitty je ..."
          },
          "longDescription": {
              "1": "Television LED, 55 cm diagonal ...",
              "2": "<p><strong>Sencor SLE 22F46DM4 ..."
          }
       },
       # next products here
    ]
    ```
    **Headers**
    In headers there is list of links to another pages with products, it can be found under Link header
    `<http://127.0.0.1:8000/api/v1/products?page=2>; rel="next", <http://127.0.0.1:8000/api/v1/products?page=2>; rel="last"`

* **Error Response**

  * **Code** `400 Bad Request` <br />
    **Content**
    ```
    {
        "code": 400,
        "message": "This UUID is not valid: f564da76-6d51-4ecd-b004-895c8019a23"
    }
    ```

  OR

  * **Code** `401 UNAUTHORIZED` <br />

  OR

  * **Code** `422 Unprocessable Entity` <br />
    **Content**
    ```
    {
        "code": 422,
        "message": "There are no products on provided page."
    }
    ```

* **Sample Call**

    `http://127.0.0.1:8000/api/v1/products?uuids[]=877a07d5-a276-49f8-9b73-6ac0edef83be&uuids[]=f564da76-6d51-4ecd-b004-895c8019a235x   `
