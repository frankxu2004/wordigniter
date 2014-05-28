<?php

/**
 * Class Recite
 * The recite controller.
 */
class Recite extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // VERY IMPORTANT: All controllers/areas that should only be usable by logged-in users
        // need this line! Otherwise not-logged in users could do actions. If all of your pages should only
        // be usable by logged-in users: Put this line into libs/Controller->__construct
        Auth::handleLogin();
    }

    /**
     * This method controls what happens when you move to /note/index in your app.
     * Gets all notes (of the user).
     */
    public function index()
    {
        $this->view->render('recite/index');
    }

    public function plan()
    {
    	$recite_model = $this->loadModel('Recite');
    	$this->view->plans = $recite_model->getAllPlans();
    	$this->view->render('recite/plan');
    }
    
    public function createPlan()
    {
    	if (isset($_POST['field']) AND !empty($_POST['field'])) {
    		$recite_model = $this->loadModel('Recite');
    		$recite_model->createPlan($_POST['field'], $_POST['due']);
    	}
    	header('location: ' . URL . 'recite/plan');
    }
    
    /**
     * This method controls what happens when you move to /note/delete(/XX) in your app.
     * Deletes a note. In a real application a deletion via GET/URL is not recommended, but for demo purposes it's
     * totally okay.
     * @param int $plan_id id of the note
     */
    public function deletePlan($plan_id)
    {
    	if (isset($plan_id)) {
    		$recite_model = $this->loadModel('Recite');
    		$recite_model->delete($plan_id);
    	}
    	header('location: ' . URL . 'recite/plan');
    }

}
