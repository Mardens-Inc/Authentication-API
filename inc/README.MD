# Authentication Library Documentation

The authentication library allows developers to authenticate and manage users in an Active Directory environment, and to use token-based authentication for subsequent user sessions.

## Table of Contents
1. [Installation](#installation)
2. [Methods](#methods)
    - [login](#login-method)
    - [loginWithToken](#loginWithToken-method)
    - [getUserInfo](#getUserInfo-method)
3. [Examples](#examples)

## Installation

You need to include the Authentication class in your project. The Authentication class is contained in the 'Authentication.php' file.

```php
include_once 'path/to/Authentication.php';
```

## Methods

### login method

**Syntax**

```php
$result = login(string $username, string $password)
```

**Parameters**

- `$username`: The username of the user.
- `$password`: The password of the user.

**Return**

- Returns an authenticated token if the authentication is successful.
- Returns `false` if the authentication fails or if unable to connect to Active Directory.

### loginWithToken method

**Syntax**

```php
$result = loginWithToken(string $token)
```

**Parameters**

- `$token`: The token acquired from the `login` method.

**Return**

- Returns `true` if the token is successfully validated.
- Returns `false` if the token validation fails.

### getUserInfo method

**Syntax**

```php
$result = getUserInfo(string $username, string $password)
```

**Parameters**

- `$username`: The username of the user.
- `$password`: The password of the user.

**Return**

- Returns an array with the user's details if the authentication is successful.
- Returns `false` if the authentication fails or if unable to connect to Active Directory.

## Examples

#### Using the login method:

```php
$auth = new Authentication();
$token = $auth->login('username_here', 'password_here');
```

#### Using the loginWithToken method:

```php
$auth = new Authentication();
$is_valid = $auth->loginWithToken($token);
```

#### Using the getUserInfo method:

```php
$auth = new Authentication();
$userdata = $auth->getUserInfo('username_here', 'password_here');
```

**Notice:** Replace the `'username_here'` and `'password_here'` with actual username and password values. Also, the `$token` is acquired from the previous `login` method call.

