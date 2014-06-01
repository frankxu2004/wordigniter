<?php

/**
 * NoteModel
 * This is basically a simple CRUD (Create/Read/Update/Delete) demonstration.
 */
class ReciteModel
{
    /**
     * Constructor, expects a Database connection
     * @param Database $db The Database object
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Getter for all reciteplans (reciteplans are an implementation of example data, in a real world application this
     * would be data that the user has created)
     * @return array an array with several objects (the results)
     */
    public function getAllPlans()
    {
        $sql = "SELECT user_id, plan_id, field, due FROM reciteplans WHERE user_id = :user_id";
        $query = $this->db->prepare($sql);
        $query->execute(array(':user_id' => $_SESSION['user_id']));
        // fetchAll() is the PDO method that gets all result rows
        return $query->fetchAll();
    }

    /**
     * Getter for a single note
     * @param int $plan_id id of the specific note
     * @return object a single object (the result)
     */
    public function getPlan($plan_id)
    {
        $sql = "SELECT user_id, plan_id, field, due FROM reciteplans WHERE user_id = :user_id AND plan_id = :plan_id";
        $query = $this->db->prepare($sql);
        $query->execute(array(':user_id' => $_SESSION['user_id'], ':plan_id' => $plan_id));

        // fetch() is the PDO method that gets a single result
        return $query->fetch();
    }

    /**
     * Setter for a note (create)
     * @param string $field note text that will be created
     * @return bool feedback (was the note created properly ?)
     */
    public function createPlan($field, $due)
    {    	
        $sql = "INSERT INTO reciteplans (field, due, user_id) VALUES (:field, :due, :user_id)";
        $query = $this->db->prepare($sql);
        $query->execute(array(':field' => $field, ':due' => $due, ':user_id' => $_SESSION['user_id']));
        $count =  $query->rowCount();
        if ($count == 1) {
        	$plan_id = $this->db->lastInsertId();
        	$tablename = 'reciteplan'.$plan_id;
        	$this->createPlanTable($tablename);
        	$this->initPlanTable($field, $tablename);
            return true;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_NOTE_CREATION_FAILED;
        }
        
        // default return
        return false;
    }
    
    /**
     * Deletes a specific note
     * @param int $plan_id id of the note
     * @return bool feedback (was the note deleted properly ?)
     */
    public function deletePlan($plan_id)
    {
        $sql = "DELETE FROM reciteplans WHERE plan_id = :plan_id AND user_id = :user_id";
        $query = $this->db->prepare($sql);
        $query->execute(array(':plan_id' => $plan_id, ':user_id' => $_SESSION['user_id']));
        $count =  $query->rowCount();
        if ($count == 1) {
        	$tablename = 'reciteplan'.$plan_id;
        	$this->deletePlanTable($tablename);
            return true;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_NOTE_DELETION_FAILED;
        }
        // default return
        return false;
    }
    
    public function createPlanTable($tablename)
    {
    	$sql = "CREATE TABLE $tablename (word_id INT PRIMARY KEY, w0 DECIMAL(4,3), w DECIMAL(4,3))";
    	$query = $this->db->prepare($sql);
    	$query->execute();
    }
    
    public function initPlanTable($field, $tablename)
    {
    	$sql = "INSERT INTO $tablename (word_id) SELECT word_id FROM $field";
    	$query = $this->db->prepare($sql);
    	$query->execute();
    }
    
    public function deletePlanTable($tablename)
    {
    	$sql = "DROP TABLE $tablename";
    	$query = $this->db->prepare($sql);
    	$query->execute();
    }
    
    public function retrieveWords($plan_id)
    {
    	$sql = "SELECT field, due FROM reciteplans WHERE plan_id = :plan_id";
    	$query = $this->db->prepare($sql);
    	$query->execute(array(':plan_id' => $plan_id));
    	$result = $query->fetch();
    	$dueDate = new DateTime($result->due);
    	$field = $result->field;
    	$nowDate = new DateTime();
    	$interval = $nowDate->diff($dueDate);
    	$intervalDays = $interval->days+1;
    	$tablename = 'reciteplan'.$plan_id;
    	$sql = "SELECT COUNT(*) FROM $tablename";
    	$query = $this->db->prepare($sql);
    	$query->execute();
    	$numWords = $query->fetchColumn();
    	$num = ceil($numWords/$intervalDays);
    	
    	$sql = "SELECT $field.word_id, $field.word, $field.definition, $field.root, $field.example 
    			FROM $field INNER JOIN $tablename ON $field.word_id = $tablename.word_id 
    			ORDER BY $tablename.w, $tablename.word_id 
    			LIMIT $num";
    	$query = $this->db->prepare($sql);
    	$query->execute();
    	return($query->fetchAll());
    }
    
    public function saveWeight($plan_id, $word_id, $steps, $hasRoot) {
    	$tablename = 'reciteplan'.$plan_id;
    	$sql = "SELECT w FROM $tablename WHERE word_id = $word_id";
    	$query = $this->db->prepare($sql);
    	$query->execute();
    	$w = $query->fetchColumn();
    	if ($w != NULL) {
    		$w = $this->calculateWeight($w, $hasRoot, $steps);
    		if ($w<0.9) {
	    		$sql = "UPDATE $tablename SET w = $w WHERE word_id = $word_id";
	    		$query = $this->db->prepare($sql);
	    		$query->execute();
    		}
    		else {
    			$sql = "DELETE FROM $tablename WHERE word_id = $word_id";
    			$query = $this->db->prepare($sql);
    			$query->execute();
    		}
    	}
    	else {
    		$w = $this->initWeight($hasRoot, $steps);
    		if ($w<0.9) {
	    		$sql = "UPDATE $tablename SET w0 = $w, w = $w WHERE word_id = $word_id";
	    		$query = $this->db->prepare($sql);
	    		$query->execute();
    		}
    		else {
	    		$sql = "DELETE FROM $tablename WHERE word_id = $word_id";
	    		$query = $this->db->prepare($sql);
	    		$query->execute();
    		}
    	}
    	
    }
	
    public function initWeight($hasRoot, $steps) {
    	if ($hasRoot == 1) {
    		switch ($steps) {
    			case 1: $w=0.9; break;
    			case 2: $w=0.6; break;
    			case 3: $w=0.4; break;
    			case 4: $w=0.3; break;
    			case 5: $w=0; break;
    		}
    	}
    	else {
    		switch ($steps) {
    			case 1: $w=0.9; break;
    			case 2: $w=0.5; break;
    			case 3: $w=0.35; break;
    			case 4: $w=0; break;
    		}
    	}
    	return $w;
    }
    
    public function calculateWeight($w, $hasRoot, $steps){
    	if ($w<0.5) {
    		if ($hasRoot == 1) {
    			switch ($steps) {
    				case 1: $w=0.8; break;
    				case 2: $w=0.5; break;
    				case 3: $w=0.4; break;
    				case 4: $w=0.3; break;
    				case 5: $w=0; break;
    			}
    		}
    		else {
    			switch ($steps) {
    				case 1: $w=0.8; break;
    				case 2: $w=0.5; break;
    				case 3: $w=0.35; break;
    				case 4: $w=0; break;
    			}
    		}
    	}
    	elseif ($w>=0.5 && $w<0.8) {
    		if ($hasRoot == 1) {
    			switch ($steps) {
    				case 1: $w=0.81; break;
    				case 2: $w=0.5+0.2*$w; break;
    				case 3: $w=0.4; break;
    				case 4: $w=0.3; break;
    				case 5: $w=0; break;
    			}
    		}
    		else {
    			switch ($steps) {
    				case 1: $w=0.81; break;
    				case 2: $w=0.5; break;
    				case 3: $w=0.35; break;
    				case 4: $w=0; break;
    			}
    		}
    	}
    	elseif ($w>=0.8 && $w<0.9) {
    		if ($hasRoot == 1) {
    			switch ($steps) {
    				case 1: $w=1.1*$w; break;
    				case 2: $w=0.6; break;
    				case 3: $w=0.4; break;
    				case 4: $w=0.3; break;
    				case 5: $w=0; break;
    			}
    		}
    		else {
    			switch ($steps) {
    				case 1: $w=1.1*$w; break;
    				case 2: $w=0.5; break;
    				case 3: $w=0.35; break;
    				case 4: $w=0; break;
    			}
    		}
    	}
    	return $w;
    }

}
