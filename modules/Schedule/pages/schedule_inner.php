<?php
require_once '../../lib/Security.php';
Security::SecurePage();
?>
<div id="sch_DatePickerDiv">
    <img src="/img/leftArrow.png" onclick="goToDate('<?=$schedColumn[0]['locId']?>','<?=getPreviousXScheduleDate($schedColumn[0]['locId'],$schedColumn[0]['_refWeekDate'],3)?>')" />
    <input type="text" class="sch_DatePicker" disabled="disabled" data-locid="<?=$schedColumn[0]['locId']?>" id="datePickLoc-<?=$schedColumn[0]['locId']?>" value="<?=dateToNormal($startDate)?>"></input>
    <img src="/img/rightArrow.png" onclick="goToDate('<?=$schedColumn[0]['locId']?>','<?=date("Y-m-d 0:0:0", strtotime("+1 day",strtotime($schedColumn[2]['_refWeekDate'])))?>')" />
</div>
<div id="sch_SingleSched">
    <div id="sch_Row">
        <div id="sch_DayAbout" class="ui-corner-top">
            <?=$schedColumn[0]['_refDayEnglish']?>
        </div>
        <div id="sch_DayAbout" class="ui-corner-top">
            <?=$schedColumn[1]['_refDayEnglish']?>
        </div>
        <div id="sch_DayAbout" class="ui-corner-top">
            <?=$schedColumn[2]['_refDayEnglish']?>
        </div>
    </div>
    <?php
    $curShiftRow = 0;
    while ($curShiftRow < $schedColumn['_maxShifts']) {
        ?>
    <div id="sch_Row" >
        <?php
            for ($x = 0; $x < 3; ++$x) {
                if (isset($schedColumn[$x]['_shifts'][$curShiftRow])) {
                    // Schedule is available, Check if slots are available, or in past
                    if (!$schedColumn[$x]['_shifts'][$curShiftRow]['_hasAvailability'] || $schedColumn[$x]['_inPast']) {
                        // Schedule is full
                        ?>
        <div id="sch_DayInfo" class="sch_no-avail <?= (($curShiftRow +1) == $schedColumn['_maxShifts']) ? 'ui-corner-bottom' : ""  ?>" >
                        <?php
                    } else {
                        // Schedule is not full
                        ?>
        <div id="sch_DayInfo" <?= (($curShiftRow +1) == $schedColumn['_maxShifts']) ? 'class="ui-corner-bottom"' : ""  ?> >
                <?php
                    }
                    ?>
            <div id="sch_ShiftInfo"><?= createShiftInfo($schedColumn[$x]['_shifts'][$curShiftRow]['shiftStart'], $schedColumn[$x]['_shifts'][$curShiftRow]['shiftEnd']) ?></div>
            <?php
            for ($i = 0; $i < $schedColumn[$x]['_shifts'][$curShiftRow]['slots']; ++$i) {
                if (isset($schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons'][$i]))
                {
                ?>
            <span id="sch_SlotNumber">Slot <?=$i+1?>:</span><span id="sch_SlotName" data-publisher="<?=buildPersonLink($schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons'][$i])?>" data-pubPhone="<?=$schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons'][$i]['phone']?>" data-pubEmail="<?=$schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons'][$i]['email']?>"><?=buildPersonLink($schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons'][$i])?></span>
            <?php
                if ($schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons'][$i]['note'] != '') {
                    ?>
            <div id="pubNote-<?=$x?>-<?=$schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons'][$i]['pubId']?>-<?=$schedColumn[$x]['_shifts'][$curShiftRow]['shiftId']?>" class="sch_ImgNotePad" alt="Note..." data-note="<?=htmlspecialchars($schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons'][$i]['note'])?>" data-name="<?=buildPersonLink($schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons'][$i])?>"></div>
            <?php
                }
                if ($schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons'][$i]['_isMe'] && !$schedColumn[$x]['_inPast']) {
                    ?>
            <div id="editNote-<?=$x?>-<?=$schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons'][$i]['pubId']?>-<?=$schedColumn[$x]['_shifts'][$curShiftRow]['shiftId']?>" class="sch_ImgPencil" alt="Note..." data-note="<?=htmlspecialchars($schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons'][$i]['note'])?>" onClick="editNote(this,'<?=$schedColumn[$x]['_shifts'][$curShiftRow]['shiftId']?>','<?=$schedColumn[$x]['_refWeekDate']?>')" data-idsuffix="<?=$x?>-<?=$schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons'][$i]['pubId']?>-<?=$schedColumn[$x]['_shifts'][$curShiftRow]['shiftId']?>"></div>
            <?php
                }
            ?>
            <br/>
            <?php 
                } else {
                    ?>
            <span id="sch_SlotNumber">Slot <?=$i+1?>:</span><span id="sch_SlotNameEmpty">Available</span><br/>
            <?php
                }
            }
            if (($schedColumn[$x]['_shifts'][$curShiftRow]['_hasAvailability'] || $schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons']['_containsMe']) && !$schedColumn[$x]['_inPast']) {
                if (!$schedColumn[$x]['_shifts'][$curShiftRow]['_assignedPersons']['_containsMe']) {
                    ?>
                    <div id="sch_SchMeBtn"><input id="sch_Button" type="button" value="Schedule Me" onClick="scheduleMe('<?=$schedColumn[$x]['_shifts'][$curShiftRow]['shiftId']?>','<?=$schedColumn[$x]['_refWeekDate']?>')" /></div>
                    <?php
                } else {
                    ?>
                    <div id="sch_SchMeBtn"><input id="sch_Button" type="button" value="Unschedule Me" onClick="unscheduleMe('<?=$schedColumn[$x]['_shifts'][$curShiftRow]['shiftId']?>','<?=$schedColumn[$x]['_refWeekDate']?>')" /></div>
                    <?php
                }
            } else if($schedColumn[$x]['_inPast']) {
                ?>
                <div id="sch_SchMeBtn"><span id="sch_DatePassed">Date Passed</span></div>
                <?php
            } else {
                ?>
                <div id="sch_SchMeBtn"><span id="sch_NoAvail">No Availability</span></div>
                <?php
            }
            ?>
        <?php
                } else {
                    // Schedule is not available
                    ?>
        <div id="sch_DayInfo" class="sch_no-avail <?= (($curShiftRow +1) == $schedColumn['_maxShifts']) ? 'ui-corner-bottom' : ""  ?>">
            <div id="sch_NoShift">No Shift</div>
        <?php
                }
                ?>
        </div>
            <?php
            }
        ?>
    </div>
    <?php
        ++$curShiftRow;
    }
    ?>
</div>