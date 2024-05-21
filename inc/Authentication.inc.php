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
            password TEXT NOT NULL,
            reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_online TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_ip VARCHAR(255),
            last_user_agent VARCHAR(255),
            admin BOOLEAN DEFAULT FALSE
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
                // Authentication successful
                ldap_unbind($ldapConn);
            } catch (Exception $e) {
                $ldapBind = false;
            }

            if(!$ldapBind) {
                return false;
            }

            // $password = password_hash($password, PASSWORD_DEFAULT);
            $password = hash('sha256', $password);

            $ip = $_SERVER['REMOTE_ADDR'];
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $admin = $ldapBind ? 1 : 0;

            // Check if user exists in database
            if (!self::checkIfUserExistsInDatabase($username)) {
                // User does not exist, create user and generate token
                $sql = "INSERT INTO users (username, password, last_ip, last_user_agent, admin) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ssssi", $username, $password, $ip, $user_agent, $admin);
                $stmt->execute();
                $stmt->close();
            } else {
                // validate user
                $sql = "SELECT * FROM users WHERE username = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                $row = $result->fetch_assoc();
                if ($row['password'] !== $password) {
                    return false;
                }
                // update last_online, last_ip, last_user_agent
                $sql = "UPDATE users SET last_online = CURRENT_TIMESTAMP, last_ip = ?, last_user_agent = ? WHERE username = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("sss", $ip, $user_agent, $username);
                $stmt->execute();
            }

            return self::generateToken($username, $password, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $admin);
        } else {
            // Unable to connect to Active Directory
            return false;
        }
    }

    public function register(string $username, string $password): bool|string
    {
        $password = hash('sha256', $password);
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $admin = 0;

        if (self::checkIfUserExistsInDatabase($username)) {
            return false;
        }

        $sql = "INSERT INTO users (username, password, last_ip, last_user_agent, admin) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $username, $password, $ip, $user_agent, $admin);
        $stmt->execute();
        $stmt->close();

        return self::generateToken($username, $password, $ip, $user_agent, $admin);
    }


    public function loginWithToken(string $token): bool
    {

        try {

            // decode uri component
            $token = urldecode($token);
            $decoded_token = json_decode(base64_decode($token), true);
            if (!isset($decoded_token['username'])) {
                return false;
            }
            $username = $decoded_token['username'];
            $admin = $decoded_token['admin'];
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
                if (self::validate($username, $password, $ip, $user_agent, $admin, $token)) {

                    $sql = "UPDATE users SET last_online = CURRENT_TIMESTAMP, last_ip = ?, last_user_agent = ? WHERE username = ?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param("sss", $ip, $user_agent, $username);
                    $stmt->execute();

                    return true;
                }
            }
        } catch (Exception) {
            return false;
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

    private function generateToken(string $username, string $password, string $ip, string $user_agent, bool $admin): string
    {
        $json = json_encode([
            'username' => $username,
            'password' => $password,
            'ip' => $ip,
            'user_agent' => $user_agent,
            'admin' => $admin
        ]);
        $token = hash('sha256', $json);
        return base64_encode(json_encode(["username" => $username, "admin" => $admin, "token" => $token]));
    }

    private function validate(string $username, string $password, string $ip, string $user_agent, bool $admin, string $token): bool
    {
        return hash_equals(self::generateToken($username, $password, $ip, $user_agent, $admin), $token);
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
                $searchAttributes = array("displayName", "mail", "memberOf");
                $searchResults = ldap_search($ldapConn, "DC=mss,DC=local", $searchFilter, $searchAttributes);
                $userDetails = [];

                if ($searchResults) {
                    $entry = ldap_first_entry($ldapConn, $searchResults);
                    $userDetails = ldap_get_attributes($ldapConn, $entry); // This will contain user's details
                    unset($userDetails["count"]); // Remove the 'count' element from the results
                }

                ldap_unbind($ldapConn);

                $userDetails["displayName"] = @$userDetails["displayName"][0];
                $userDetails["mail"] = @$userDetails["mail"][0];
                unset($userDetails["0"]);
                unset($userDetails["1"]);
                unset($userDetails["2"]);

                // loop through the memberOf array and remove the count element and format the array
                $memberOf = [];
                foreach ($userDetails["memberOf"] as $key => $group) {
                    if ($key !== "count") {
                        $groupData = explode(",", $group);
                        $groupSize = count($groupData);
                        $groupDetails = [];
                        for ($i = 0; $i < $groupSize; $i++) {
                            $groupParts = explode("=", $groupData[$i]);
                            $groupKey = $groupParts[0];
                            $groupValue = $groupParts[1] ?? null; // prevent errors when there's no second element
                            switch ($groupKey) {
                                case "CN":
                                    $groupDetails["Group"] = $groupValue;
                                    break;
                                case "OU":
                                    $groupDetails["Permissions"] = $groupValue;
                                    break;
                                case "DC":
                                    $dcs = explode(".", $groupValue);
                                    $groupDetails["DomainControllers"] = $dcs;
                                    break;
                            }
                        }
                        $memberOf[$key] = $groupDetails;
                    }
                }
                $userDetails["memberOf"] = $memberOf;

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
