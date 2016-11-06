## API documentation

### Auth & register

#### POST /v1/login
Return your API token.
**Request**

- username - your username
- password - your password

**Response**

- success - Auth was successful
- token - Your API token (only if success === true)
- error_message - Error text for user (only if success === false)

#### POST /v1/register
Register new user.
**Request**

- username - your username, between 4 and 32 symbols
- password - your password, between 6 and 64 symbols

**Response**

- success - Auth was successful
- token - Your API token (only if success === true)
- error_message - Error text for user (only if success === false)

### Methods

#### Auth with header
You must provide token form /v1/login or /v1/register in X-AUTH-TOKEN header or in token post/get parameter



