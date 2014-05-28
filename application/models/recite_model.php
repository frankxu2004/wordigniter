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
        $sql = "SELECT user_id, plan_id, field FROM reciteplans WHERE user_id = :user_id";
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
        $sql = "SELECT user_id, plan_id, field FROM reciteplans WHERE user_id = :user_id AND plan_id = :plan_id";
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
    public function create($field)
    {
        $sql = "INSERT INTO reciteplans (field, user_id) VALUES (:field, :user_id)";
        $query = $this->db->prepare($sql);
        $query->execute(array(':field' => $field, ':user_id' => $_SESSION['user_id']));

        $count =  $query->rowCount();
        if ($count == 1) {
            return true;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_NOTE_CREATION_FAILED;
        }
        // default return
        return false;
    }

    /**
     * Setter for a note (update)
     * @param int $plan_id id of the specific note
     * @param string $field new text of the specific note
     * @return bool feedback (was the update successful ?)
     */
    public function editSave($plan_id, $field)
    {

        $sql = "UPDATE reciteplans SET field = :field WHERE plan_id = :plan_id AND user_id = :user_id";
        $query = $this->db->prepare($sql);
        $query->execute(array(':plan_id' => $plan_id, ':field' => $field, ':user_id' => $_SESSION['user_id']));

        $count =  $query->rowCount();
        if ($count == 1) {
            return true;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_NOTE_EDITING_FAILED;
        }
        // default return
        return false;
    }

    /**
     * Deletes a specific note
     * @param int $plan_id id of the note
     * @return bool feedback (was the note deleted properly ?)
     */
    public function delete($plan_id)
    {
        $sql = "DELETE FROM reciteplans WHERE plan_id = :plan_id AND user_id = :user_id";
        $query = $this->db->prepare($sql);
        $query->execute(array(':plan_id' => $plan_id, ':user_id' => $_SESSION['user_id']));

        $count =  $query->rowCount();

        if ($count == 1) {
            return true;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_NOTE_DELETION_FAILED;
        }
        // default return
        return false;
    }
}
