<?php

require_once ('IModule.php');

abstract class Module implements IModule {
    private $moduleName;
    private $moduleID;
    private $moduleAction;
    
    function __construct($moduleName,$moduleID) {
        $this->moduleName = $moduleName;
        $this->moduleID = $moduleID;
        $this->moduleAction = $_GET['modaction'];
    }
    
    function getModuleName() {
        return $this->moduleName;
    }
    
    function getModuleID() {
        return $this->moduleID;
    }
    
    protected function getMyEditBase() {
        return "?action=modedit&mod=" . $this->getModuleID();
    }
    
    protected function createModAction($action) {
        return $this->getMyEditBase() . "&modaction=" . $action;
    } 
    
    protected function getCurrentModAction() {
        return $this->moduleAction;
    }
}

?>
