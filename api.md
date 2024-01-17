# Mardens Auth API

The "Authentication Library Project" provides PHP and JavaScript  
solutions for user authentication, including API interaction,  
configuration, and detailed documentation.  
EndFragment

## End-point: Login

### Authentication Endpoint

This API endpoint is used to authenticate users by sending a POST request to the specified URL.

#### Request Body

-   `username` (text) - The username of the user.
-   `password` (text) - The password of the user.

#### Response

-   `success` (boolean) - Indicates whether the authentication was successful.
-   `message` (string) - Provides a message related to the authentication status.

### Method: POST

> ```
> https://auth.mardens.com
> ```

### Body formdata

| Param    | value                 | Type |
| -------- | --------------------- | ---- |
| username | user123               | text |
| password | super_secret_password | text |

### Response: 400

```json
{
    "success": false,
    "message": "Invalid username or password."
}
```

### Response: 400

```json
{
    "success": false,
    "message": "Invalid request."
}
```

### Response: 200

```json
{
    "success": true,
    "message": "Logged in.",
    "token": "<<TOKEN_HERE>>"
}
```

⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃

## End-point: Login with Token

This HTTP POST request is used to authenticate a user by sending a payload to the specified endpoint. The request body type is not defined. Upon successful authentication, the response will include a JSON object with a "success" key set to true, and a "message" key providing additional information about the authentication status.

### Method: POST

> ```
> https://auth.mardens.com
> ```

### Headers

| Content-Type  | Value              |
| ------------- | ------------------ |
| Authorization | super_secret_token |

### 🔑 Authentication noauth

| Param | value | Type |
| ----- | ----- | ---- |

### Response: 200

```json
{
    "success": true,
    "message": "Logged in with token."
}
```

### Response: 400

```json
{
    "success": false,
    "message": "Invalid token."
}
```

⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃
