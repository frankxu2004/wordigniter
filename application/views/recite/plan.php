<div class="content">
    <h1>Create a new plan</h1>
	<form method="post" action="<?php echo URL;?>recite/createPlan">
		Field:<select name="field">
			<option value="toefl">TOEFL</option>
			<option value="gre">GRE</option>
			<option value="cet4">CET4</option>
			<option value="cet6">CET6</option>
			</select>
			<br>
		Due:<input type="date" name="due" />
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
                echo '<td>' . htmlentities($value->due) . '</td>';
                echo '<td><a href="'. URL . 'recite/deletePlan/' . $value->plan_id.'">Delete</a></td>';
                echo '</tr>';
            }
        } else {
            echo 'No plans yet. Create one!';
        }
    ?>
    </table>
</div>
