<?php
require_once 'lib/Security.php';
Security::SecurePage();
?>

<link rel="stylesheet" href="/modules/Schedule/Schedule.css" />
<script type="text/javascript" src="modules/Schedule/Schedule.js"></script>
<div id="sch_Container">
    <div id="sch_TabSched">
        <ul>
            <?php
            foreach ( $_DB->getAssignmentLocationsByCongregationId(Security::GetCurrentUser()['congId']) as $location) {
                ?>
            <li><a href="/modules/Schedule/ScheduleAjax.php?a=getSched&lid=<?=$location['alId']?>"><?=$location['name']?></a></li>
            <?php
            }
            ?>
        </ul>
    </div>
</div>
<div id="sch_NotePad" class="ui-corner-all">
    <div id="sch_NotePadTitle">Note</div>
    <div id="sch_NotePadTextArea"><textarea id="sch_NoteText"></textarea></div>
    <div id="sch_NotePadSave">
        <input type="button" id="sch_NotePadBtnCancel" value="Cancel" onclick="cancelNote()" />
        <input type="button" id="sch_NotePadBtnSave" value="Save Note" onclick="saveNote(this)" />
    </div>
</div>