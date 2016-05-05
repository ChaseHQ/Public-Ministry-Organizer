<?php

require_once 'Database.php';

class Security {
    private $_isUserLoggedIn = false;
    
    function __construct() {
        $this->secureGlobals();
        session_start();
        if ($_SESSION['__sec_loggedin'] == true) $this->_isUserLoggedIn = true;
        
        $_SESSION['__lfp'] = true;
    }
    
    function isUserLoggedIn() {
        return $this->_isUserLoggedIn;
    }
    
    function requestCredentials($error = "") {
        include("./pages/loginPage.php");
    }
    
    function login($pid,$pin) {
        $db = Database::getDB();
        $pub = $db->getPublisher($pid);
        if (strcasecmp($pub['pin'], $pin) == 0) {
            // Save Session Credentials Here
            $_SESSION['__sec_user'] = $pub;
            $_SESSION['__sec_user']['pin'] = ''; // Clear the PIN for Security
            $_SESSION['__sec_loggedin'] = true;
            return true;
        }
        return false;
    }
    
    public function logOutUser() {
        session_destroy();
        $this->_isUserLoggedIn = false;
    }
    
    static public function SecurePage() {
        if ($_SESSION['__lfp'] != true) {
            die("YOU CANNOT ACCESS THIS PAGE DIRECTLY");
        }
    }
    
    static public function ExtendedPageLoginCheck() {
        Security::secureGlobals();
        session_start();
        if ($_SESSION['__sec_loggedin'] != true) {
            die("YOU MUST BE LOGGED IN TO ACCESS THIS PAGE!");
        }
    }
    
    static public function GetCurrentUser() {
        if ($_SESSION['__sec_loggedin']) {
            return $_SESSION['__sec_user'];
        }
    }
    
    static public function LogOut() {
        session_destroy();
    }
    
    static public function secureInput(&$value, $key) {
        $value = htmlspecialchars(stripslashes($value));
        $value = str_ireplace("script","blocked",$value);
        $value = mysql_real_escape_string($value);

        return $value;
    }

    static public function secureGlobals() {
         array_walk($_GET, 'secureInput');
         array_walk($_POST, 'secureInput');
    }

    
}

?>
