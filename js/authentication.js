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
        this.apiUrl = debug ? "http://auth.local/" : "https://auth.mardens.com/";
        this.debugMode = debug;
        try {
            this.token = document.cookie
                .split(";")
                .find((row) => row.trim().startsWith("token="))
                .trim()
                .slice(6);
        } catch (e) {
            this.token = null;
        }
        if (!window.$) {
            throw new Error("jQuery is required for this library to work.");
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
        return await $.ajax({
            url: this.apiUrl,
            method: "POST",
            dataType: "json",
            data: {username, password},
            success: (data) => {
                // Return the data from the server on successful request
                if (data.success && data.token) {
                    this.generateCookies(data.token);
                }
                return data;
            },
            error: (err) => {
                $(this).trigger("error", [err]);
                // Return false on error
                return err;
            },
        });
    }

    /**
     * Login with a token.
     * @param {string} token - The token to login with.
     * @return {Promise<JSON>} The server's response as a JSON object.
     */
    async loginWithToken(token) {
        // Send a POST request to the API with the token in the Authorization header
        return await $.ajax({
            url: this.apiUrl,
            method: "POST",
            dataType: "json",
            data: {token},
            success: (data) => {
                // Return the data from the server on successful request
                if (data.success) {
                    this.generateCookies(token); // Extend the cookies expiration
                }
                return data;
            },
            error: (err) => {
                $(this).trigger("error", [err]);
                // Return false on error
                return err;
            },
        });
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
        $(this).trigger("logout");
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
        $(this).trigger("login");
    }
}
