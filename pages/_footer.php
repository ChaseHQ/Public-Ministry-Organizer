<?php
    require_once ('./lib/Security.php');
    Security::SecurePage();
?>
<div id="version"><a href="?action=logout">Logout <?php $_d = Security::GetCurrentUser(); echo($_d['lastName'] . ", " . $_d['firstName']) ?> </a></div>
        </div>
    </body>
</html>
