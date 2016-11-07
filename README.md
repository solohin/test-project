## API documentation

### Auth & register

#### Auth with header
You must provide token in X-AUTH-TOKEN header or in token post/get parameter.
You should obtain token from /v1/login or /v1/register methods.

#### POST /v1/login
Return your API token.
Works for all roles

**Request**

- username - Your username
- password - Your password

**Response**

- success - Auth was successful
- token - Your API token (only if success === true)
- error_message - Error text for user (only if success === false)

#### POST /v1/register
Register new user.

**Request**

- username - Your username, between 4 and 32 symbols
- password - Your password, between 6 and 64 symbols

**Response**

- success - Auth was successful
- roles - Array of roles, ['ROLE_USER'] by default
- token - Your API token (only if success === true)
- error_message - Error text for user (only if success === false)

### Methods

#### POST /notes
Creates new note.
Works for ROLE_ADMIN or ROLE_USER(for owner)

**Request**

- text - Users comment to note
- calories - Amount of calories in the note
- user_id - Who posted the note. Works only for ROLE_ADMIN. 
- time - Time of note in format "hh:mm", for example "23:57"
- date - Date of note in format "dd.mm.yyyy", for example "31.12.2016"

**Response**

- success - Note has been added or not
- id - Id of newly added note
- error_message - Error text for user (only if success === false)

#### PUT /notes/{id}
Update note. 
Works for ROLE_ADMIN or ROLE_USER(for owner)

**Request**

- text - Users comment to note
- calories - Amount of calories in the note
- user_id - Who posted the note. Works only for ROLE_ADMIN. 
- time - Time of note in format "hh:mm", for example "23:57"
- date - Date of note in format "dd.mm.yyyy", for example "31.12.2016"

**Response**

- success - Note has been added or not
- error_message - Error text for user (only if success === false)

#### GET /notes/{id}
Get note by ID. 
Works for ROLE_ADMIN or ROLE_USER(for owner)

**Response**

- success - Note has found or not
- error_message - Error text for user (only if success === false)
- note[id] - Note id
- note[text] - Users comment to note
- note[calories] - Amount of calories in the note
- note[user_id] - Who posted the note. Works only for ROLE_ADMIN. 
- note[time] - Time of note in format "hh:mm", for example "23:57"
- note[date] - Date of note in format "dd.mm.yyyy", for example "31.12.2016"

#### GET /notes/
Get filtered notes list. 
Works for ROLE_ADMIN or ROLE_USER(for owner)

**Request**
- from_date - Optional filter note[date]. The format is "dd.mm.yyyy", for example "31.12.2016".
- to_date - Optional filter note[date]. The same format.
- from_time - Optional filter note[time]. The format is "hh:mm", for example "23:57". Midnight is 00:00. 
- to_time - Optional filter note[time]. The same format. Midnight is 23:59. 

**Response**

- success - Note has found or not
- error_message - Error text for user (only if success === false)
- notes - array, contains on elements:
    - id - Note id
    - text - Users comment to note
    - calories - Amount of calories in the note
    - user_id - Who posted the note. Works only for ROLE_ADMIN. 
    - time - Time of note in format "hh:mm", for example "23:57"
    - date - Date of note in format "dd.mm.yyyy", for example "31.12.2016"

### Constants

#### Roles
- ROLE_ADMIN - CRUD users and notes
- ROLE_USER - CRUD only his own notes and his own user
- ROLE_MANAGER - CRUD only users

#### Error types
Too obvious to explain.

- wrong_password
- wrong_username
- username_exists
- short_username
- long_username
- short_password
- long_password
- method_not_found
- wrong_token
- no_token

