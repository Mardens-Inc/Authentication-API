# Mardens Auth API

## End-point: Login
### Authentication Endpoint

This API endpoint is used to authenticate users by sending a POST request to the specified URL.

#### Request Body

- `username` (text) - The username of the user.
- `password` (text) - The password of the user.
    

#### Response

- `success` (boolean) - Indicates whether the authentication was successful.
- `message` (string) - Provides a message related to the authentication status.
    

#### Example Response

``` json
{
    "success": true,
    "message": "Logged in."
}

 ```

``` json
{
    "success": false,
    "message": "Invalid username or password."
}

 ```

``` json
{
    "success": false,
    "message": "Missing username or password."
}

 ```
### Method: POST
>```
>/auth.php
>```
### Body formdata

|Param|value|Type|
|---|---|---|
|username|user123|text|
|password|super_secret_password|text|


### Response: 200
```json
{
    "success": true,
    "message": "Logged in."
}
```

### Response: 200
```json
{
    "success": false,
    "message": "Invalid username or password."
}
```

### Response: 200
```json
{
    "success": false,
    "message": "Missing username or password."
}
```


⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃ ⁃

## End-point: Login with Token
This endpoint is used to authenticate a user via HTTP POST request to /auth.php. The request payload does not have a defined body type.

### Response

- Status: 400
- Body:
    
    ``` json
      {
          "success": true,
          "message": ""
      }
    
     ```
    

#### Example Response

``` json
{
    "success": true,
    "message": "Logged in with token."
}

 ```
### Method: POST
>```
>/auth.php
>```
### Headers

|Content-Type|Value|
|---|---|
|Authorization|super_secret_token|


### 🔑 Authentication noauth

|Param|value|Type|
|---|---|---|


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
