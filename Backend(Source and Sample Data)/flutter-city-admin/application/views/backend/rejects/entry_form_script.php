<script>
	
function runAfterJQ() {


		$('.delete-img').click(function(e){
			e.preventDefault();

			// get id and image
			var id = $(this).attr('id');

			// do action
			var action = '<?php echo $module_site_url .'/delete_cover_photo/'; ?>' + id + '/<?php echo @$item->id; ?>';
			console.log( action );
			$('.btn-delete-image').attr('href', action);
			
		});


		$('#cat_id').on('change', function() {

				var value = $('option:selected', this).text().replace(/Value\s/, '');

				var catId = $(this).val();
				 
				$.ajax({
					url: '<?php echo $module_site_url . '/get_all_sub_categories/';?>' + catId,
					method: 'GET',
					dataType: 'JSON',
					success:function(data){
						$('#sub_cat_id').html("");
						$.each(data, function(i, obj){
						    $('#sub_cat_id').append('<option value="'+ obj.id +'">' + obj.name+ '</option>');
						});
						$('#name').val($('#name').val() + " ").blur();
						$('#sub_cat_id').trigger('change');
					}
				});
			});
		var edit_product_check = $('#edit_product').val();

	    
			 $('#us3').locationpicker({
	            location: {latitude: 0.0, longitude: 0.0},
	            radius: 300,
	            inputBinding: {
	                latitudeInput: $('#lat'),
	                longitudeInput: $('#lng'),
	                radiusInput: $('#us3-radius'),
	                locationNameInput: $('#find_location')
	            },
	            enableAutocomplete: true,
	            onchanged: function (currentLocation, radius, isMarkerDropped) {
	                // Uncomment line below to show alert on each Location Changed event
	                //alert("Location changed. New location (" + currentLocation.latitude + ", " + currentLocation.longitude + ")");
	            }
	        });
		

		// add specification
      	$(document).ready(function () {
     		//add new product
      		var edit_product_check = $('#edit_product').val();


     		if(edit_product_check == 0) {
     			//new product
     			var counter = 2;
     		} else {
     			//edit product
     			var counter =  parseInt($('#spec_total_existing').val())+2;
     		}

      		$('#spec_total_existing').val(counter);

      		$('#addspec').click(function () {
      			
      			var newTextBoxDiv = $(document.createElement('div'))
	     		.attr("id", 'TextBoxDiv' + counter);

	     		newTextBoxDiv.after().html(
	      		'<input class="form-control form-control-sm mt-3" type="text" name="prd_spec_title' + counter + 
	      		'" id="prd_spec_title' + counter + '" value="" >');

	      		newTextBoxDiv.appendTo(".title-url-input");
	      		
	      		var newTextBoxDiv2 = $(document.createElement('div'))
	     		.attr("id", 'TextBoxDiv2' + counter);
	      		newTextBoxDiv2.after().html(
	      		'<input class="form-control form-control-sm mt-3" type="text" name="prd_spec_desc' + counter + 
	      		'" id="prd_spec_desc' + counter + '" value="" >');

	      		newTextBoxDiv2.appendTo(".desc-url-input");
	      		counter++;
	      		
	      		$( "#CounterTextBoxDiv" ).remove();
				var newCounterTextBoxDiv = $(document.createElement('div'))
	     		.attr("id", 'CounterTextBoxDiv' + counter);

	     		newCounterTextBoxDiv.after().html(
	      		'<input type="hidden" name="spec_total" id="spec_total" value=" '+ counter +'" >');

	      		newCounterTextBoxDiv.appendTo(".title-url-input");

	      		
      		});
      	});
	   	//Timepicker
	   	$('#sample1 input').ptTimeSelect();
		
	}

</script>