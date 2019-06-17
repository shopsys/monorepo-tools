# API Authentication - OAuth2

Our [Backend API](/docs/backend-api/introduction-to-backend-api.md) is secured by [OAuth2](https://oauth.net/2/) authorization and we use [Trikoder oauth2-bundle](https://github.com/trikoder/oauth2-bundle) for its implementation.

We use [Bearer token](https://oauth.net/2/bearer-tokens/) authorization and it means that each API call must have `Authorization: Bearer TOKEN` header.

## Configuration

To configure the OAuth2, you have to run [backend-api-oauth-keys-generate](/docs/introduction/console-commands-for-application-management-phing-targets.md#backend-api-oauth-keys-generate) phing target.
This phing target generates private and public keys for OAuth2 and also required parameters. The private key is used to sign tokens and public key is used to verify the signatures.

### Client Credentials

OAuth2 has it's own users and when you want to use the API, you have to create users first.

Run this SQL command to create a user `alan` with secret `xxx`

```sql
INSERT INTO "oauth2_client" ("identifier", "secret", "grants", "active")
VALUES ('alan', 'xxx', 'client_credentials password', '1');
```

> Never use password `xxx` in production, always use secure passwords!

## Generate your API token

Run following code in bash

```bash
curl -X POST \
  'http://127.0.0.1:8000/api/token' \
  -d 'grant_type=client_credentials' \
  -d 'client_id=alan' \
  -d 'client_secret=xxx'
```

When everything goes right, you'll get a similar response with token that is valid for one hour.
You will need to generate new token after this one expires.
```json
{"token_type":"Bearer","expires_in":3600,"access_token":"eyJ...lKQ"}
```

The bearer token is the value of the `access_token`, eg. `eyJ...lKQ`.

*Now you can continue with your first [API call](/docs/backend-api/introduction-to-backend-api.md)*