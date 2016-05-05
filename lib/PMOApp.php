<?php

require_once('Security.php');
require_once('Database.php');

// Modules
require_once ('modules/Schedule/Schedule.php');

class PMOApp {
    protected $_sec;
    protected $_allModules = array();
    
    function __construct() {
        $this->_sec = new Security(); // Session Has started
    }
    
    function run() {
        if ($_GET['action'] == 'logout') {
            $this->_sec->logOutUser();
        }
        if (!$this->_sec->isUserLoggedIn()) {
            $this->_sec->requestCredentials();
            exit(); // Stop executing scripts no one logged in
        }
        
        array_push($this->_allModules, new Schedule());
        
        include('pages/_header.php');
        // Check for Routing
        switch($_GET['action']) {
            case 'main':
            case 'modedit':
            default:
                $module = $this->getModuleFromID(/*$_GET['mod']*/"sch");
                $module->getModuleEditPage();
                break;
            /* -- Modified Engine to use just SCHEDULER
            case 'main':
            default:
                include('pages/mainPage.php');
                break;*/
        }
        include('pages/_footer.php');
    }
    
    function getModuleFromID ($moduleID) {
        foreach($this->_allModules as $module) {            
            if ($module->getModuleID() == $moduleID) {
                return $module;
            }
        }
    }
}

?>
