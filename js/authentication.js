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
        if(!window.$)
        {
            throw new Error("jQuery is required for this library to work.");
        }
    }

    /**
     * Login with a username and password.
     * @param {string} username - The username to login with.
     * @param {string} password - The password to login with.
     * @return {JSON|boolean} The server's response as a JSON object, or false if the login failed.
     */
    async login(username, password) {
        // Send a POST request to the API with the username and password
        return await $.ajax({
            url: this.apiUrl,
            method: "POST",
            dataType: "json",
            data: { username, password },
            success: (data) => {
                // Return the data from the server on successful request
                return data;
            },
            error: (err) => {
                // Return false on error
                return false;
            },
        });
    }

    /**
     * Login with a token.
     * @param {string} token - The token to login with.
     * @return {JSON|boolean} The server's response as a JSON object, or false if the login failed.
     */
    async loginWithToken(token) {
        // Send a POST request to the API with the token in the Authorization header
        return await $.ajax({
            url: this.apiUrl,
            method: "POST",
            dataType: "json",
            headers: { Authorization: token },
            success: (data) => {
                // Return the data from the server on successful request
                return data;
            },
            error: (err) => {
                // Return false on error
                return false;
            },
        });
    }

    /**
     * Login with a token stored in a cookie.
     * @return {JSON|boolean} The server's response, or false if no token was found.
     */
    async loginWithTokenFromCookie() {
        // Send a POST request to the API with the token in the Authorization header
        return await $.ajax({
            url: this.apiUrl,
            method: "POST",
            dataType: "json",
            success: (data) => {
                // Return the data from the server on successful request
                return data;
            },
            error: (err) => {
                // Return false on error
                return false;
            },
        });
    }
}
