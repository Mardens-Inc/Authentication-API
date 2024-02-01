# Authentication Library Documentation

This is a fact guide to make use of Authentication Library, an API interaction class designed to simplify the management of user authentication.

## Table of Contents

1. [Class Usage](#class-usage)
2. [Method Overview](#method-overview)
3. [Methods](#methods)
    - [login](#login)
    - [loginWithToken](#loginwithtoken)
    - [loginWithTokenFromCookie](#loginwithtokenfromcookie)
    - [logout](#logout)
    - [generateCookies](#generatecookies)

-------

## Class Usage

The Authentication library is an ES6 class. The class constructor expects an optional boolean parameter which should be set to `true` for local debug mode and `false` for production.

```javascript
import Authentication from './Authentication';

// create a new instance for local testing
let auth = new Authentication(true);

// create a new instance for production
let authProd = new Authentication();
```

## Method Overview

| Methods                      | Description                                                        |
|------------------------------|--------------------------------------------------------------------|
| `login(username, password)`  | Authenticate using a given username and password.                  |
| `loginWithToken(token)`      | Authenticate using a previously obtained token.                    |
| `loginWithTokenFromCookie()` | Use an existing token stored in the user's cookie to authenticate. |
| `logout()`                   | Logout the user by clearing the stored tokens.                     |
| `generateCookies(token)`     | Generate cookies for a provided token.                             |

## Methods

### login

The `login` method accepts two string parameters: a username and a password. It makes a POST request to the authentication server and returns a Promise that resolves to the server's response as a JSON Object.

```javascript
auth.login('yourUsername', 'yourPassword')
    .then(response => {
        console.log(response);
    });
```

### loginWithToken

The `loginWithToken` method accepts a token as a parameter, and uses it to authenticate the user by making a POST request to the Auth Server.

```javascript
auth.loginWithToken('yourToken')
    .then(response => {
        console.log(response);
    });
```

### loginWithTokenFromCookie

The `loginWithTokenFromCookie` method allows you to authenticate a user using a token stored in a cookie. If no token is found, the method returns false.

```javascript
auth.loginWithTokenFromCookie()
    .then(response => {
        console.log(response);
    });
```

### logout

The `logout` method logs out the user and clears any tokens stored in the cookies.

```javascript
auth.logout()
```

### generateCookies

The `generateCookies` method accepts a token as a string parameter and stores it as a cookie in the user's browser. This cookie expires after a period defined by the backend.

```javascript
auth.generateCookies('authToken')
```

The library relies on jQuery, and includes a number of custom jQuery events that can be subscribed to, including `login`, `logout`, and `error`, allowing you to handle these events within your application's logic.

Please note: The library requires jQuery, and will throw an error upon instantiation if it is not available.


