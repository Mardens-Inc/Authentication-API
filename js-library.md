# Authentication Library

This library provides an easy way to interact with the authentication API. It includes methods for logging in with a username and password, and for logging in with a token.

## Installation

You can include the `authentication.js` file in your project either as a module or with a script tag. You can use either the full or minified version.

### As a Module

```javascript
import Authentication from 'https://auth.mardens.com/js/authentication.js';
// or
import Authentication from 'https://auth.mardens.com/js/authentication.min.js';
```

### As a Script Tag

```html
<script src="https://auth.mardens.com/js/authentication.js"></script>
<!-- or -->
<script src="https://auth.mardens.com/js/authentication.min.js"></script>
```

## Usage

First, create a new instance of the `Authentication` class. You can pass `true` to the constructor to use the local API URL, or `false` (or nothing) to use the production API URL.

```javascript
const auth = new Authentication();
```

### Login with Username and Password

To login with a username and password, use the `login` method. This method returns a JSON object if the login is successful, or `false` if the login failed.

```javascript
const response = auth.login('username', 'password');
if (response) {
    console.log(response);
} else {
    console.log('Login failed');
}
```

### Login with Token

To login with a token, use the `loginWithToken` method. This method returns a JSON object if the login is successful, or `false` if the login failed.

```javascript
const response = auth.loginWithToken('token');
if (response) {
    console.log(response);
} else {
    console.log('Login failed');
}
```

### Login with Token from Cookie

To login with a token stored in a cookie, use the `loginWithTokenFromCookie` method. This method returns a JSON object if the login is successful, or `false` if no token was found.

```javascript
const response = auth.loginWithTokenFromCookie();
if (response) {
    console.log(response);
} else {
    console.log('No token found in cookies');
}
```