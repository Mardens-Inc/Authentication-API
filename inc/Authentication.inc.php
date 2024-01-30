<?php

class Authentication
{

    private mysqli $conn;

    function __construct()
    {
        require_once "connections.inc.php";
        $this->conn = DB_Connect::connect();

        // create table if one doesn't exist
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            password VARCHAR(1024) NOT NULL
        )";
        $this->conn->query($sql);
    }

    public function login(string $username, string $password): string|false
    {
        // Active Directory server details
        $ldapServer = "ldap://192.168.21.215";
        $ldapUsername = $username . "@mss.local"; // Use the user's full AD username

        // Connect to Active Directory
        $ldapConn = ldap_connect($ldapServer);
        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

        if ($ldapConn) {
            try {
                // Bind to Active Directory using the provided username and password
                $ldapBind = @ldap_bind($ldapConn, $ldapUsername, $password);
            } catch (Exception $e) {
                $ldapBind = false;
            }

            if ($ldapBind) {
                // Authentication successful
                ldap_unbind($ldapConn);
                // $password = password_hash($password, PASSWORD_DEFAULT);
                $password = hash('sha256', $password);

                // Check if user exists in database
                if (!self::checkIfUserExistsInDatabase($username)) {
                    // User does not exist, create user and generate token
                    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param("ss", $username, $password);
                    $stmt->execute();
                    $stmt->close();
                }

                return self::generateToken($username, $password, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
            } else {
                // Authentication failed
                ldap_unbind($ldapConn);
                return false;
            }
        } else {
            // Unable to connect to Active Directory
            return false;
        }
    }



    public function loginWithToken(string $token): bool
    {
        // decode uri component
        $token = urldecode($token);
        $decoded_token = json_decode(base64_decode($token), true);
        if (!isset($decoded_token['username'])) {
            return false;
        }
        $username = $decoded_token['username'];
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $password = $row['password'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (self::validate($username, $password, $ip, $user_agent, $token)) {
                return true;
            }
        }
        return false;
    }

    private function checkIfUserExistsInDatabase(string $username): bool
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->num_rows > 0;
    }

    private function generateToken(string $username, string $password, string $ip, string $user_agent): string
    {
        $json = json_encode([
            'username' => $username,
            'password' => $password,
            'ip' => $ip,
            'user_agent' => $user_agent
        ]);
        $token = hash('sha256', $json);

        $token = base64_encode(json_encode(["username" => $username, "token" => $token]));

        return $token;
    }

    private function validate(string $username, string $password, string $ip, string $user_agent, string $token): bool
    {
        $valid = hash_equals(self::generateToken($username, $password, $ip, $user_agent), $token);
        return $valid;
    }

    /**
     * Retrieves the user's information from Active Directory.
     *
     * @param string $username The username of the user.
     * @param string $password The password of the user.
     *
     * @return array|false Returns an array with the user's details if the authentication is successful.
     *                    Returns false if the authentication fails or if unable to connect to Active Directory.
     */
    public function getUserInfo(string $username, string $password): array|false
    {
        // Active Directory server details
        $ldapServer = "ldap://192.168.21.215";
        $ldapUsername = $username . "@mss.local"; // Use the user's full AD username
        // Connect to Active Directory
        $ldapConn = ldap_connect($ldapServer);
        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

        if ($ldapConn) {
            try {
                // Bind to Active Directory using the provided username and password
                $ldapBind = @ldap_bind($ldapConn, $ldapUsername, $password);
            } catch (Exception $e) {
                $ldapBind = false;
            }

            if ($ldapBind) {
                // Search AD for the user's details
                $searchFilter = "(samaccountname=$username)";
                $searchAttributes = array("displayName", "mail", "memberOf"); // Add or remove required attributes
                $searchResults = ldap_search($ldapConn, "DC=mss,DC=local", $searchFilter, $searchAttributes);
                $userDetails = [];

                if($searchResults) {
                    $entry = ldap_first_entry($ldapConn, $searchResults);
                    $userDetails = ldap_get_attributes($ldapConn, $entry); // This will contain user's details
                    unset($userDetails["count"]); // Remove the 'count' element from the results
                }

                ldap_unbind($ldapConn);
                return $userDetails; // Convert array to JSON and return
            } else {
                // Authentication failed
                ldap_unbind($ldapConn);
                return false;
            }
        } else {
            // Unable to connect to Active Directory
            return false;
        }
    }
}
