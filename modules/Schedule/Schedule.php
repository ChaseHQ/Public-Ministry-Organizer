<?php
require_once ('lib/Module.php');
require_once ('lib/Security.php');

class Schedule extends Module {
    public function __construct() {
        parent::__construct("Schedule", "sch");
    }
    
    public function getModuleOverview() {
        // Intentionally left blank as I Heavily Modified this engine to use only one module
    }
    
    public function getModuleEditPage() {
        // Immeadiate Main Entry Point
        switch ($this->getCurrentModAction()) {
            default:
                $this->getDefaultPage();
        }
    }
    
    private function getDefaultPage() {
        $_DB = Database::getDB();
        include_once ('pages/schedule_main.php');
    }
}

?>