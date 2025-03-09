<script>
	<?php if ( $this->config->item( 'client_side_validation' ) == true ): ?>

	function jqvalidate() {

		$('#item-form').validate({
			rules:{
				name:{
					blankCheck : "",
					minlength: 3,
					remote: "<?php echo $module_site_url .'/ajx_exists/'.@$item->id; ?>"
				},
				"images[]":{
					required: true
				},
				cat_id: {
		       		indexCheck : ""
		      	},
		      	sub_cat_id: {
		       		indexCheck : ""
		      	}
			},
			messages:{
				name:{
					blankCheck : "<?php echo get_msg( 'err_item_name' ) ;?>",
					minlength: "<?php echo get_msg( 'err_item_len' ) ;?>",
					remote: "<?php echo get_msg( 'err_item_exist' ) ;?>."
				},
				"images[]":{
					required: "<?php echo $this->lang->line('err_file_upload'); ?>"
				},
				cat_id:{
			       indexCheck: "<?php echo $this->lang->line('f_item_cat_required'); ?>"
			    },
			    sub_cat_id:{
			       indexCheck: "<?php echo $this->lang->line('f_item_subcat_required'); ?>"
			    }
			},

			submitHandler: function(form) {
		        if ($("#item-form").valid()) {
		            form.submit();
		        }
		    }

		});
		
		jQuery.validator.addMethod("indexCheck",function( value, element ) {
			
			   if(value == 0) {
			    	return false;
			   } else {
			    	return true;
			   };
			   
		});
			jQuery.validator.addMethod("blankCheck",function( value, element ) {
			
			   if(value == "") {
			    	return false;
			   } else {
			   	 	return true;
			   }
		});

	}

	<?php endif; ?>
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
						$('#sub_cat_id').html("<option>Select Subcategory Name</option>");
						$.each(data, function(i, obj){
						    $('#sub_cat_id').append('<option value="'+ obj.id +'">' + obj.name+ '</option>');
						});
						$('#name').val($('#name').val() + " ").blur();
						$('#sub_cat_id').trigger('change');
					}
				});
			});
		var edit_product_check = $('#edit_product').val();

	    if(edit_product_check == 1) {
			 $('#us3').locationpicker({
	            location: {latitude: <?php echo $popularitem->lat;?>, longitude: <?php echo $popularitem->lng;?>},
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
		}

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
	     		.attr("class",'col-md-6',"id", 'TextBoxDiv' + counter);

	     		newTextBoxDiv.after().html(
	      		'<div class="form-group"><label>Title : '+counter+'</label><input class="form-control form-control-sm" type="text" name="prd_spec_title' + counter + 
	      		'" id="prd_spec_title' + counter + '" value="" ></div><div class="form-group"><label>Description : '+counter+'</label><input class="form-control form-control-sm" type="text" name="prd_spec_desc' + counter + 
	      		'" id="prd_spec_desc' + counter + '" value="" ></div>');
    
	      		newTextBoxDiv.appendTo("#spec_data");
	      		counter++;
	      		
	      		$( "#CounterTextBoxDiv" ).remove();
				var newCounterTextBoxDiv = $(document.createElement('div'))
	     		.attr("id", 'CounterTextBoxDiv' + counter);

	     		newCounterTextBoxDiv.after().html(
	      		'<input type="hidden" name="spec_total" id="spec_total" value=" '+ counter +'" >');

	      		newCounterTextBoxDiv.appendTo("#spec_data");

	      		
      		});
      	});
	   	//Timepicker
	   	$('#sample1 input').ptTimeSelect();
		
	}

</script>
<?php 
	// replace cover photo modal
	$data = array(
		'title' => get_msg('upload_photo'),
		'img_type' => 'item',
		'img_parent_id' => @$item->id
	);

	$this->load->view( $template_path .'/components/photo_upload_modal', $data );

	// delete cover photo modal
	$this->load->view( $template_path .'/components/delete_cover_photo_modal' ); 
?>