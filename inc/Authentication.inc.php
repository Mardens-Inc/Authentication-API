<?php
class Authentication
{
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
            // Bind to Active Directory using the provided username and password
            $ldapBind = ldap_bind($ldapConn, $ldapUsername, $password);

            if ($ldapBind) {
                // Authentication successful
                ldap_unbind($ldapConn);

                $token = self::generateToken($username, $password, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);

                return true;
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
    private function generateToken(string $username, string $password, string $ip, string $user_agent): string
    {
        $token = hash('sha256', json_encode([
            'username' => $username,
            'password' => $password,
            'ip' => $ip,
            'user_agent' => $user_agent
        ]));

        return $token;
    }

    private function validate(string $username, string $password, string $ip, string $user_agent, string $token): bool
    {
        $valid = hash_equals(self::generateToken($username, $password, $ip, $user_agent), $token);
        return $valid;
    }
}
