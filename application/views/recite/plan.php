<div class="content">
    <h1>Create a new plan</h1>
	<form method="post" action="<?php echo URL;?>recite/createPlan">
	<label>Field:</label>  
	<select name="field">
		<option value=1>TOEFL</option>
		<option value=2>GRE</option>
		<option value=3>CET4</option>
		<option value=4>CET6</option>
	
	<input type="submit" value='Create Plan'/>
	</form>
    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <h1 style="margin-top: 50px;">Your Plans</h1>
	<table>
    <?php
        if ($this->plans) {
            foreach($this->plans as $key => $value) {
                echo '<tr>';
                echo '<td>' . htmlentities($value->field) . '</td>';
                echo '<td><a href="'. URL . 'recite/editPlan/' . $value->plan_id.'">Edit</a></td>';
                echo '<td><a href="'. URL . 'recite/deletePlan/' . $value->plan_id.'">Delete</a></td>';
                echo '</tr>';
            }
        } else {
            echo 'No plans yet. Create one!';
        }
    ?>
    </table>
</div>
