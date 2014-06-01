<div class="content">
    <h1>Do this plan</h1>
    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    
    <script type="text/javascript">
	    $(document).ready( function() {
		    var wordList = <?php echo json_encode($this->currentWords); ?>;
		    var plan_id = <?php echo $this->plan_id; ?>;
		    var sendData = {};
		    sendData.plan_id = plan_id;
		    var current_step;
		    var max_number_of_steps;
		    function display() {
			    if (wordList.length > 0) {
				    var currentWordRow = wordList.shift();
				    current_step = 1;
					if (currentWordRow['root']) {
						max_number_of_steps = 5;
						$('.content').append('<div id="step1"></div>');
						$('.content').append('<div id="step2"></div>');
						$('.content').append('<div id="step3"></div>');
						$('.content').append('<div id="step4"></div>');
						$('.content').append('<div id="step5"></div>');
						$('#step1').text(currentWordRow['word']);
						$('#step2').text(currentWordRow['root']);
						$('#step3').text(currentWordRow['example']);
						$('#step4').text('picurl');
						$('#step5').text(currentWordRow['definition']);
						$('#step2').hide();
						$('#step3').hide();
						$('#step4').hide();
						$('#step5').hide();
						sendData.hasRoot = 1;
						}
						
		    		else {
						max_number_of_steps = 4;
						$('.content').append('<div id="step1"></div>');
						$('.content').append('<div id="step2"></div>');
						$('.content').append('<div id="step3"></div>');
						$('.content').append('<div id="step4"></div>');
						$('#step1').text(currentWordRow['word']);
						$('#step2').text(currentWordRow['example']);
						$('#step3').text('picurl');
						$('#step4').text(currentWordRow['definition']);
						$('#step2').hide();
						$('#step3').hide();
						$('#step4').hide();
						sendData.hasRoot = 0;
						}
					
					$('#redobtn').hide();
					$('#submitbtn').hide();
					$('#stepbtn').show();
					$('#knowbtn').show();
					sendData.word_id = currentWordRow['word_id'];
			    }
			    
			    else {
			    	$('#redobtn').remove();
					$('#submitbtn').remove();
					$('#stepbtn').remove();
					$('#knowbtn').remove();
			    	$('.content').append('<div>Finished!</div>')
			    	$('.content').append('<a href="<?php echo URL; ?>recite/index">Back</a>')
			    }
			}

			function clearAll() {
				$('#step1').remove();
				$('#step2').remove();
				$('#step3').remove();
				$('#step4').remove();
				$('#step5').remove();
			}
			
			display();
			 
			$('#stepbtn').click( function() {  
				var next_step = current_step + 1;
				$('#step'+next_step).show();
			    current_step++;
				if (current_step == max_number_of_steps) {
					$('#stepbtn').hide();
					$('#knowbtn').hide();
					$('#submitbtn').show();
					sendData.steps = current_step;
					}
			});

			$('#knowbtn').click( function() {
				sendData.steps = current_step;
				while (current_step <= max_number_of_steps) {
					$('#step'+current_step).show();
					current_step++;
					}
					$('#stepbtn').hide();
					$('#knowbtn').hide();
					$('#redobtn').show();
					$('#submitbtn').show();
					
			});   
					 
			$('#redobtn').click( function() {  
				 while (current_step != 1) {
					$('#step'+current_step).hide();
					current_step--;
					} 
		            $('#stepbtn').show(); 
		            $('#knowbtn').show();
		            $('#redobtn').hide();
		            $('#submitbtn').hide();
			}); 
				 
			$('#submitbtn').click( function() {
				$.ajax({
					type: "POST",
					url: "<?php echo URL; ?>recite/saveWeight",
					data: sendData
					})
					.done(function( msg ) {
						
						clearAll()
						display();
					});
			  });
		});
    </script>

	<button id="stepbtn">Don't Know</button>
	<button id="knowbtn">Know</button>
	<button id="redobtn">Redo</button>
	<button id="submitbtn">Next Word</button>

</div>
