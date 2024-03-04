/**
 * Authentication class for interacting with the authentication API.
 * @class
 */
export default class Authentication {
    /**
     * Create a new Authentication instance.
     * @param {boolean} debug - If true, use the local API URL. Otherwise, use the production API URL.
     */
    constructor(debug = false) {
        // Set the API URL based on the debug parameter
        this.apiUrl = debug ? "http://auth.local/" : "https://lib.mardens.com/auth/";
        this.debugMode = debug;
        this.hasJQuery = window.$ !== undefined;

        // Set the initial login state
        this.isLoggedIn = false;
        if (this.hasJQuery) {
            $(this).on("logged-in", () => {
                this.isLoggedIn = true;
            });
            $(this).on("logout", () => {
                this.isLoggedIn = false;
            });
        }

        // Get the token from the cookie
        try {
            this.token = document.cookie
                .split(";")
                .find((row) => row.trim().startsWith("token="))
                .trim()
                .slice(6);
        } catch (e) {
            this.token = null;
        }
    }

    /**
     * Login with a username and password.
     * @param {string} username - The username to login with.
     * @param {string} password - The password to login with.
     * @return {Promise<JSON>} The server's response as a JSON object.
     */
    async login(username, password) {
        // Send a POST request to the API with the username and password
        const apiURL = this.apiUrl;

        let response, data;
        const formData = new FormData();
        formData.append("username", username);
        formData.append("password", password);

        try {
            // Make a POST request to the API
            response = await fetch(apiURL, {
                method: 'POST',
                body: formData,
            });

            // Try to get data from the response in JSON format
            data = await response.json();
        } catch (err) {
            if (this.hasJQuery)
                // An error occurred, trigger the error event
                $(this).trigger("error", [{success: false, message: "An unknown error occurred."}]);
            return err;
        }

        // Check if the request was successful
        if (data.success && data.token) {
            this.generateCookies(data.token);
            return data;
        } else {
            if (this.hasJQuery)
                // The request was not successful, trigger the error event
                $(this).trigger("error", [{success: false, message: data.message}]);
            return data;
        }
    }

    /**
     * Login with a token.
     * @param {string} token - The token to login with.
     * @return {Promise<JSON>} The server's response as a JSON object.
     */
    async loginWithToken(token) {


        const formData = new FormData();
        formData.append("token", token);

        // Send a POST request to the API with the token in the Authorization header
        try {
            const response = await fetch(this.apiUrl, {
                method: "POST",
                body: formData,
            });

            // assuming server always sends valid json in response
            const data = await response.json();

            if (response.ok) {
                // The request was handled successfully
                if (data.success) {
                    this.generateCookies(token);
                }
            } else {
                // The request was handled with an error
                if (!data.message) {
                    data.message = "An unknown error occurred.";
                }
                // here you have to take care of "trigger" replacement as 'fetch' doesn't support it
                throw new Error(JSON.stringify(data));
            }

            return data;
        } catch (error) {
            // handle error if fetch throws an exception
            console.error(error);
            throw error;
        }
    }

    /**
     * Login with a token stored in a cookie.
     * @return {Promise<JSON>} The server's response.
     */
    async loginWithTokenFromCookie() {
        return this.token == null ? false : await this.loginWithToken(this.token);
    }

    /**
     * Logout of the current session.
     */
    logout() {
        document.cookie = `token=; path=/; domain=.${window.location.hostname}; samesite=strict; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
        if (this.hasJQuery)
            $(this).trigger("log-out");
        this.isLoggedIn = false;
    }

    /**
     * Generate cookies for the token.
     * @param {string} token - The token to generate cookies for.
     */
    generateCookies(token) {
        let expire = new Date();
        expire.setDate(expire.getDate() + 2); // 2 days
        document.cookie = `token=${token}; path=/; domain=.${window.location.hostname}; samesite=strict; expires=${expire.toGMTString()}`;
        this.token = token;
        this.isLoggedIn = true;
        if (this.hasJQuery)
            $(this).trigger("logged-in");
    }
}
