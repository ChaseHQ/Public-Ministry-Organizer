<?php
require_once '../../lib/Security.php';

Security::ExtendedPageLoginCheck();

// Ajax Routing Here
switch ($_GET['a']) {
    case 'getSched':
        if (empty($_GET['startDate']) || !isset($_GET['startDate'])) {
            $_GET['startDate'] = date("Y-m-d 0:0:0");
        }
        getSchedulePage($_GET['lid'],$_GET['startDate']);
        break;
    case 'scheduleMe':
        echo(scheduleMe($_POST['shiftId'], $_POST['dateTime']));
        break;
    case 'unscheduleMe':
        echo(unscheduleMe($_POST['shiftId'], $_POST['dateTime']));
        break;
    case 'postNote':
        echo(postNote($_POST['shiftId'],$_POST['dateTime'],$_POST['note']));
        break;
}

function postNote($shiftId, $dateTime, $note) {
    $pubId = Security::GetCurrentUser()['id'];
    if (!Database::getDB()->postNote($pubId, $shiftId, $dateTime, strip_tags(addslashes($note))))
            return '1';
    return '0';
}

function scheduleMe($shiftId, $dateTime, $note = '') {
    // Do Some Error Check Here
    $pubId = Security::GetCurrentUser()['id'];
    if (Database::getDB()->getPersonSchedule($pubId,$shiftId,$dateTime)) {
        return '1';
    } else {
        Database::getDB()->schedulePerson($pubId,$shiftId,$dateTime,$note);
    }
    return '0'; // 0 Is A Success
}

function unscheduleMe($shiftId, $dateTime) {
    // Do Some Error Check Here
    $pubId = Security::GetCurrentUser()['id'];
    Database::getDB()->unschedulePerson($pubId,$shiftId,$dateTime);
    return '0';
}

function getSchedulePage($schLocationId, $startDate) {
    
    $timestamp = strtotime($startDate);
    $startDayOfWeek = date("N",$timestamp);
    $_DB = Database::getDB();
    $weekArray = $_DB->getPackedAssignmentAndShiftsByWeek(Security::GetCurrentUser()['congId'],$schLocationId);
    
    $schedColumn = array();
    $refStartDay = $startDayOfWeek;
    $refDayCount = 0;
    while (count($schedColumn) < 3) {
        if (isset($weekArray[$refStartDay])) {
            $addWeek = $weekArray[$refStartDay];
            $dayTs = strtotime("+$refDayCount day",$timestamp);
            $nowTs = strtotime(date("Y-m-d 0:0:0",time()));
            $addWeek['_refWeekDate'] = date("Y-m-d 0:0:0", $dayTs);
            $addWeek['_refDayEnglish'] = date("l m/d/Y", $dayTs);
            if ($dayTs < $nowTs) {
                $addWeek['_inPast'] = true;
            } else {
                $addWeek['_inPast'] = false;
            }
            for ($x = 0; $x < count($addWeek['_shifts']); ++$x) {
                $filledShiftPositions = filledShiftPositionLoggedInDetails($_DB->getPublishersForShiftAtTime($addWeek['_shifts'][$x]['shiftId'], $addWeek['_refWeekDate']));
                $addWeek['_shifts'][$x]['_assignedPersons'] = $filledShiftPositions;
                $addWeek['_shifts'][$x]['_filledPositions'] = $filledShiftPositions['_personCount'];
                if ($addWeek['_shifts'][$x]['_filledPositions'] >= $addWeek['_shifts'][$x]['slots']) {
                    $addWeek['_shifts'][$x]['_hasAvailability'] = false;
                } else {
                    $addWeek['_shifts'][$x]['_hasAvailability'] = true;
                }
            }
            array_push($schedColumn, $addWeek);
        }
        if (++$refStartDay > 7) {
            $refStartDay = 1;
        }
        $refDayCount++;
    }
    
    $schedColumn['_maxShifts'] = $weekArray[0]['_maxShifts'];
    
    include_once 'pages/schedule_inner.php';
}

function createShiftInfo($startTime, $endTime) {
    return date("g:i A", strtotime($startTime)) . ' to ' . date("g:i A", strtotime($endTime));
}

function dateToNormal($dateTime) {
    return date("m/d/Y",strtotime($dateTime));
}

function normalToDate($normal) {
    return date("Y-m-d 0:0:0", strtotime($normal));
}

function buildPersonLink ($person){
    return $person['firstName'] . ' ' . $person['lastName'];
}

function getPreviousXScheduleDate($locId,$startDate,$schedToGoBack) {
    $timestamp = strtotime($startDate);
    $startDayOfWeek = date("N",$timestamp);
    
    $_DB = Database::getDB();
    $weekArray = $_DB->getPackedAssignmentAndShiftsByWeek(Security::GetCurrentUser()['congId'],$locId);
    
    $dateFound = 0;
    $refDayCount = 0;
    while ($dateFound < 3) {
        $refDayCount++;
        if (--$startDayOfWeek == 0) {
            $startDayOfWeek = 7;
        }
        
        if (isset($weekArray[$startDayOfWeek])) {
            $dateFound++;
        }
    }
    
    return date("Y-m-d 0:0:0",strtotime("-$refDayCount day", $timestamp));
}

function filledShiftPositionLoggedInDetails($filledShiftPositionsArray) {
    
    $containsMe = false;
    $personCount = 0;
    
    foreach ($filledShiftPositionsArray as &$person) {
        if ($person['pubId'] == Security::GetCurrentUser()['id']) {
            $person['_isMe'] = true;
            $containsMe = true;
        } else {
            $person['_isMe'] = false;
        }
        ++$personCount;
    }
    
    $filledShiftPositionsArray['_containsMe'] = $containsMe;
    $filledShiftPositionsArray['_personCount'] = $personCount;
    
    return $filledShiftPositionsArray;
}

?>
