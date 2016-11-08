## Task
Write an application for the input of calories

- User must be able to create an account and log in
- When logged in, user can see a list of his meals and calories (user enters calories manually, no auto calculations!), also he should be able to edit and delete
- Implement at least three roles with different permission levels: a regular user would only be able to CRUD on their owned records, a user manager would be able to CRUD users, and an admin would be able to CRUD all records and users.
- Each entry has a date, time, text, and num of calories
- Filter by dates from-to, time from-to (e.g. how much calories have I had for lunch each day in the last month, if lunch is between 12 and 15h)
- User setting – Expected number of calories per day
- When displayed, it goes green if the total for that day is less than expected number of calories per day, otherwise goes red
- Minimal UI/UX design is needed.
- All actions need to be done client side using AJAX, refreshing the page is not acceptable. (If a mobile app, disregard this)
- REST API. Make it possible to perform all user actions via the API, including authentication (If a mobile application and you don’t know how to create your own backend you can use Firebase.com or similar services to create the API).
- In any case you should be able to explain how a REST API works and demonstrate that by creating functional tests that use the REST Layer directly. Please be prepared to use REST clients like Postman, cURL, etc for this purpose.
- Bonus: unit and e2e tests!
- You will not be marked on graphic design, however, do try to keep it as tidy as possible.

NOTE: Please keep in mind that this is the project that will be used to evaluate your skills. The project will be evaluated as if you were delivering it to a customer. We expect you to make sure that the app is fully functional and doesn’t have any obvious missing pieces. The deadline for the project is 2 weeks from today.

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
- error_type - error type constant (only if success === false)


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
- error_type - error type constant (only if success === false)


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
- error_type - error type constant (only if success === false)


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
- error_type - error type constant (only if success === false)

#### DELETE /notes/{id}
Delete note by ID. 
Works for ROLE_ADMIN or ROLE_USER(for owner)

**Response**

- success - Note has found or not
- error_message - Error text for user (only if success === false)
- error_type - error type constant (only if success === false)

#### GET /notes/{id}
Get note by ID. 
Works for ROLE_ADMIN or ROLE_USER(for owner)

**Response**

- success - Note has found or not
- error_message - Error text for user (only if success === false)
- error_type - error type constant (only if success === false)
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
- page - Optional page number starting from 1. Default = 1. 500 items per page
- user_id - Optional user filter. Available only for ROLE_ADMIN

**Response**

- success - Note has found or not
- error_message - Error text for user (only if success === false)
- error_type - error type constant (only if success === false)
- notes - array, contains:
    - id - Note id
    - text - Users comment to note
    - calories - Amount of calories in the note
    - user_id - Who posted the note. Works only for ROLE_ADMIN. 
    - time - Time of note in format "hh:mm", for example "23:57"
    - date - Date of note in format "dd.mm.yyyy", for example "31.12.2016"
    - daily_normal - if the total for that day is less than expected number of calories per day 
- total_calories -  summ of calories in all notes

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

