# Upgrade Instructions for Backend API

There is a new feature of the Shopsys Framework, and that is a [backend API](/docs/backend-api/introduction-to-backend-api.md).
The backend api package is marked as *experimental* at the moment so there is a possibility we might introduce some BC breaking changes there.
You have to follow these upgrade instructions only if you need a backend API.

To start using the backend API, follow the instructions (you can also find inspiration in [#1055](https://github.com/shopsys/shopsys/pull/1055)):
- require following packages in `composer.json`
    ```diff
    "require": {
    +     "shopsys/backend-api": "^8.0.0",
    +     "trikoder/oauth2-bundle": "^1.1",
    ```
- create configuration of FOS Rest bundle to `app/config/packages/fos_rest.yml`
    ```yaml
    fos_rest:
        serializer:
            serialize_null: true
        param_fetcher_listener: true
        view:
            view_response_listener:  false
        format_listener:
            rules:
                - { path: '^/api', prefer_extension: true, fallback_format: 'json', priorities: [ 'json', 'xml' ] }
                - { path: '^/', stop: true }
    ```
- create configuration for Trikoder OAuth2 bundle in `app/config/packages/trikoder_oauth2.yml`
    ```yaml
    trikoder_oauth2:
        authorization_server:
            private_key: '%shopsys.root_dir%/app/config/packages/oauth2/private.key'
            encryption_key: '%oauth2_encryption_key%'
            # How long the issued access token should be valid for.
            # The value should be a valid interval: http://php.net/manual/en/dateinterval.construct.php#refsect1-dateinterval.construct-parameters
            access_token_ttl: PT1H
            # How long the issued refresh token should be valid for.
            # The value should be a valid interval: http://php.net/manual/en/dateinterval.construct.php#refsect1-dateinterval.construct-parameters
            refresh_token_ttl: P1M

        resource_server:
            public_key: '%shopsys.root_dir%/app/config/packages/oauth2/public.key'

        # Scopes that you wish to utilize in your application.
        # This should be a simple array of strings.
        scopes: []
        persistence:
            doctrine:
                entity_manager: default
    ```
- create configuration for OAuth2 keys and parameters
    - create directory `app/config/packages/oauth2`
    - create `app/config/packages/oauth2/.gitignore` as private keys must not be ever pushed
        ```
        private.key
        public.key
        parameters_oauth.yml
        ```
    - create parameters template `app/config/packages/oauth2/parameters_oauth.yml.dist`, this template is used by phing target [backend-api-oauth-keys-generate](/docs/introduction/console-commands-for-application-management-phing-targets.md#backend-api-oauth-keys-generate)
        ```yaml
        parameters:
            oauth2_encryption_key: %%encryption_key%%
        ```
    - add default `oauth2_encryption_key` parameter to `app/config/parameters_common.yml`
        ```diff
        + oauth2_encryption_key: "0" #placeholder for correctly running composer post script, replaced by app/config/packages/oauth2/parameters_oauth.yml
        ```
- add API routes to `app/config/routing.yml`
    ```diff
    + shopsys_api_v1:
    +     resource: "@ShopsysBackendApiBundle/Resources/config/v1/routing.yml"
    + oauth2:
    +     prefix: /api
    +     resource: '@TrikoderOAuth2Bundle/Resources/config/routes.xml'
    ```
- add routes for your [custom API](/docs/cookbook/backend-api/creating-custom-api-endpoint.md) to `src/Shopsys/ShopBundle/Resources/config/routing.yml`
    ```diff
    + shopsys_shop_api_v1:
    +     resource: "@ShopsysShopBundle/Controller/Api/V1"
    +     prefix: /api/v1
    +     type: annotation
    ```
- add security configuration for API routes to `app/config/packages/security.yml`. Be careful, the configuration should be after `administration` and before `frontend`
    ```diff
    + backend_api_token:
    +     pattern: ^/api/token$
    +     security: false
    + backend_api:
    +     pattern: ^/api
    +     security: true
    +     stateless: true
    +     oauth2: true
    frontend:
        pattern: ^/
    ```
- add following bundles to `app/AppKernel.php`
    ```diff
    $bundles = [
    +     new FOS\RestBundle\FOSRestBundle(),
    +     new JMS\SerializerBundle\JMSSerializerBundle(),
    +     new Shopsys\BackendApiBundle\ShopsysBackendApiBundle(),
    +     new Trikoder\Bundle\OAuth2Bundle\TrikoderOAuth2Bundle(),
    ```
    and loading of OAuth2 parameters
    ```diff
    if (file_exists(__DIR__ . '/config/parameters_version.yml')) {
        $configs[] = __DIR__ . '/config/parameters_version.yml';
    }

    + if (file_exists(__DIR__ . '/config/packages/oauth2/parameters_oauth.yml')) {
    +     $configs[] = __DIR__ . '/config/packages/oauth2/parameters_oauth.yml';
    + }
    ```
- run [db-create](/docs/introduction/console-commands-for-application-management-phing-targets.md#db-create) (this one even on production) and `test-db-create` phing targets to install extension for UUID
- read [Introduction to Backend API](/docs/backend-api/introduction-to-backend-api.md) to learn how to start using API in your project
