<?php
    require_once './lib/Security.php';
    Security::secureGlobals();
    
    $db = Database::getDB();
    switch ($_GET['a']) {
        case 'getPub':
                echo (json_encode($db->getPublishersFromCongregationId($_GET['pid'], true)));
            break;
        case 'login':
            $sec = new Security();
            if ($sec->isUserLoggedIn()) {
                $sec->logOutUser();
            }
            if ($sec->login($_GET['pid'], $_POST['pin'])) {
                echo "TRUE";
            } else {
                echo "FALSE";
            }
            break;
    }
?>