<?php

class Database {
    private static $singleton = null;
    function __construct (){
        if (Database::$singleton != null) return; 
        mysql_connect("localhost","craigve1_pmo", "pmorganizer") or die("Cannot Connect to Database");
        mysql_select_db("craigve1_pmorganizer") or die ("Cannot Select Local Database");
    }
    
    static public function getDB() {
        if (Database::$singleton == null) {
            Database::$singleton = new Database();
        }
        return Database::$singleton;
    }
    
    public function getCongregations() {
        return $this->get2DArrayFromQuery("SELECT * FROM `congregations` ORDER BY `congName` ASC");
    }
       
    public function getPublishersFromCongregationId($congID, $hideElements = false) {
        if ($hideElements) 
            return $this->get2DArrayFromQuery("SELECT id,firstName,lastName FROM `publishers` WHERE congId = '$congID' ORDER BY `lastName` ASC");
        else
            return $this->get2DArrayFromQuery("SELECT * FROM `publishers` WHERE congId = '$congID' ORDER BY `lastName` ASC");
    }
    
    public function getPublisher($pid) {
        return mysql_fetch_assoc(mysql_query("SELECT * FROM `publishers` WHERE id = '$pid'"));
    }
    
    public function getAssignmentLocationsByCongregationId($congId) {
        return $this->get2DArrayFromQuery("SELECT * FROM `view_CongregationAssignmentsLocations` WHERE congId = '$congId'");
    }
    
    public function getCongregationAssignmentDaysByLocationId($congId, $locId) {
        return $this->get2DArrayFromQuery("SELECT * FROM `view_CongregationAssignmentDay` WHERE congId = '$congId' and locId = '$locId' order by dayOfWeek ASC");
    }
    
    public function getShiftsAtLocationByDayId($congId, $dayId) {
        return $this->get2DArrayFromQuery("SELECT * FROM `view_CongregationAssignmentsShifts` WHERE adId = '$dayId' AND congId = '$congId' ORDER BY shiftStart ASC");
    }
    
    public function getShiftScheduleByShiftId($sId, $date) {
        return $this->get2DArrayFromQuery("SELECT * FROM `assignmentPerson` WHERE shiftId = '$sId' AND `date` = '$date' ORDER BY keyMan DESC, id ASC");
    }
    
    public function schedulePerson($pubId,$shiftId,$dateTime,$note) {
        mysql_query("INSERT INTO `assignmentPerson` (`pubId`, `shiftId`, `date`, `note`) VALUES ('$pubId','$shiftId','$dateTime','$note')");
        return mysql_insert_id();
    }
    
    public function unschedulePerson($pubId,$shiftId,$dateTime) {
        mysql_query("DELETE FROM `assignmentPerson` WHERE pubId = '$pubId' AND shiftId = '$shiftId' AND `date` = '$dateTime'");
    }
    
    public function getPersonSchedule($pubId, $shiftId, $dateTime) {
        return @mysql_fetch_assoc(mysql_query("SELECT * FROM `assignmentPerson` WHERE pubId = '$pubId' AND shiftId = '$shiftId' AND `date` = '$dateTime'"));
    }
    
    public function postNote($pubId, $shiftId, $dateTime, $note) {
        return mysql_query("UPDATE `assignmentPerson` SET note = '$note' WHERE `pubId` = '$pubId' AND `shiftId` = '$shiftId' AND `date` = '$dateTime'");
    }
    
    public function getPackedAssignmentAndShifts($congId, $locId) {
        $locationsArray = $this->getCongregationAssignmentDaysByLocationId($congId, $locId);
        $maxShifts = 0;
        for ($x = 0; $x < count($locationsArray); ++$x) {
            $shifts = $this->getShiftsAtLocationByDayId($congId, $locationsArray[$x]['adId']);
            $locationsArray[$x]['_shifts'] = $shifts;
            if (count($shifts) > $maxShifts) {
                $maxShifts = count($shifts);
            }
        }
        
        $locationsArray['_maxShifts'] = $maxShifts;
        
        return $locationsArray;
    }
    
    public function getPackedAssignmentAndShiftsByWeek($congId, $locId) {
        $locationArray = $this->getPackedAssignmentAndShifts($congId,$locId);
        $weekArray = array();
        $weekArray[0] = $locationArray; // Original For Reference
        for ($x = 1; $x <= 7; ++$x) {
            foreach ($locationArray as $day) {
                if ($day['dayOfWeek'] == $x) {
                    $weekArray[$x] = $day;
                }
            }
        }
        return $weekArray;
    }
    
    public function getPublishersForShiftAtTime($shiftId, $date) {
        return $this->get2DArrayFromQuery("SELECT * FROM `view_PersonShift` WHERE shiftId = '$shiftId' AND `date` = '$date' ORDER BY keyMan DESC, apId ASC");
    }
    
    private function get2DArrayFromQuery($queryString) {
        $returnArray = array();
        $returnQuery = mysql_query($queryString);
        if (@mysql_num_rows($returnQuery) == 0) return array();
        $i = 0;
        while ($row = mysql_fetch_assoc($returnQuery)) {
            //iterate thru each row
            foreach($row as $key => $value) {
                $returnArray[$i][$key] = $value;
            }
            ++$i;
        }
        return $returnArray;
    }
    
    function __destruct() {
        mysql_close();
    }
}
?>
