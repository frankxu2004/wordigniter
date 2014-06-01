<div class="content">
    <h1>Select your plan</h1>

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>
    
	<table>
    <?php
        if ($this->plans) {
            foreach($this->plans as $key => $value) {
                echo '<tr>';
                echo '<td>' . htmlentities($value->field) . '</td>';
                echo '<td>' . htmlentities($value->due) . '</td>';
                echo '<td><a href="'. URL . 'recite/doPlan/' . $value->plan_id.'">Select</a></td>';
                echo '</tr>';
            }
        } else {
            echo 'No plans yet. Create one!';
        }
    ?>
    </table>
</div>
