<?php

/**
 * File which holds task class
 */

require_once('TDLDeadline.php');

class TDLTaskClass {

    private $taskName;
    private $project;
    private $labels;
    private $deadline;
    private $repeat;
    
    /**
     * 
     * @param String $rawToProcess
     * @param String $rawDeadLine optional
     */
    function __construct($rawToProcess, $rawDeadLine = "") {
        $toProcess = explode(" ", $rawToProcess);
        $project = "";
        $labels = [];
        $taskName = "";
        for ($i = 0; $i < count($toProcess); $i++) {
            if (strpos($toProcess[$i], "#") === 0) {
                // labels			
                if (!is_numeric(substr($toProcess[$i], 1))) { // allowing to write "I'm #1"
                    $labels[] = substr($toProcess[$i], 1);
                }
            } else if (strpos($toProcess[$i], "@") === 0) {
                // project					
                $project = substr($toProcess[$i], 1);
            } else if (strpos($toProcess[$i], "d+") === 0) {
                $this->deadline = $toProcess[$i];
                $this->repeat = "";
            } else {
                $taskName .= " " . $toProcess[$i];
            }
        }
        if (strlen($rawDeadLine) > 0) {
            $deadline = new TDLDeadline();
            $deadline->fromForm($rawDeadLine);
            $this->deadline = $deadline->getDeadline();
            $this->repeat = $deadline->getRepeat();
        }
        $this->taskName = $taskName;
        $this->project = $project;
        $this->labels = $labels;        
    }

    public function getTaskName() {
        return $this->taskName;
    }

    public function getProject() {
        return $this->project;
    }

    public function getLabels() {
        return $this->labels;
    }

    public function getDeadline() {
        return $this->deadline;
    }

    public function getRepeat() {
        return $this->repeat;
    }
    
    /**
     * 
     * @param MySQLi connection $mysqli
     * @param String $username
     */
    public function testAndAddProjectLabels($mysqli, $username) {
        $toAdd = array();
        if (strlen($this->project) > 0) {
            if ($stmt = $mysqli->prepare("SELECT * FROM " . $username . "_probels WHERE name=? AND type='project';")) {
                $stmt->bind_param("s", $this->project);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows == 0) {
                    $toAdd[] = array("project", $this->project);
                }
                $stmt->free_result();
                $stmt->close();
            }
        }
            foreach ($this->labels as $value) {
                if ($stmt = $mysqli->prepare("SELECT * FROM " . $username . "_probels WHERE name=? AND type='label';")) {
                    $stmt->bind_param("s", $value);
                    $stmt->execute();
                    $stmt->store_result();
                    if ($stmt->num_rows == 0) {
                        $toAdd[] = array("label", $value);
                    }
                    $stmt->free_result();
                    $stmt->close();
                }
            }        
        foreach ($toAdd as $value) {
            if ($stmt = $mysqli->prepare("INSERT INTO " . $username . "_probels (type, name) VALUES (?,?);")) {
                    $stmt->bind_param("ss", $value[0], $value[1]);
                    $stmt->execute();
                    $stmt->close();
                }
        }
    }

}

?>