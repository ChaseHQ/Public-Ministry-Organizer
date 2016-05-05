<?php
    require_once ('./lib/Security.php');
    Security::SecurePage();
?>
<?php
foreach ($this->_allModules as $module) {
    $module->getModuleOverview();
    ?>
    <div id="center_text"><input type="button" value="Edit <?= $module->getModuleName() ?>" 
                                 onclick="window.location.href = '?action=modedit&mod=<?= $module->getModuleID() ?>'"/></div>
    <?php
}
?>
