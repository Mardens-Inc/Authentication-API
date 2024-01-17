# Authentication Library Project

This project provides an easy way to interact with the authentication API. It includes methods for logging in with a username and password, and for logging in with a token.

## Project Structure

- [.gitignore](.gitignore)
- [api.md](api.md)
- inc/
    - [Authentication.inc.php](inc/Authentication.inc.php)
    - [config.inc.php](inc/config.inc.php)
    - [connections.inc.php](inc/connections.inc.php)
- [index.php](index.php)
- js/
    - [authentication.js](js/authentication.js)
    - [authentication.min.js](js/authentication.min.js)
- [js-library.md](js-library.md)

## Installation

1. Clone the repository to your local machine.
2. Navigate to the project directory.

## Usage

### PHP

The PHP files are located in the `inc/` directory. 

- [`Authentication.inc.php`](inc/Authentication.inc.php): This file contains the Authentication class with methods for interacting with the authentication API.
- [`config.inc.php`](inc/config.inc.php): This file contains the configuration settings for the project.
- [`connections.inc.php`](inc/connections.inc.php): This file contains the database connection settings.

### JavaScript

The JavaScript files are located in the `js/` directory. 

- [`authentication.js`](js/authentication.js): This file contains the Authentication class with methods for interacting with the authentication API.
- [`authentication.min.js`](js/authentication.min.js): This is the minified version of `authentication.js`.

You can include the `authentication.js` file in your project either as a module or with a script tag. You can use either the full or minified version.
#### As a Module

```javascript
import Authentication from './js/authentication.js';
// or
import Authentication from './js/authentication.min.js';
```

#### As a Script Tag

```html
<script src="./js/authentication.js"></script>
<!-- or -->
<script src="./js/authentication.min.js"></script>
```

## Documentation

For more detailed information on how to use the JavaScript library, refer to the [js-library.md](js-library.md) file. For information on the API, refer to the [api.md](api.md) file.