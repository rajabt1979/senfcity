<script>
	<?php if ( $this->config->item( 'client_side_validation' ) == true ): ?>

	function jqvalidate() {

		$('#item-form').validate({
			rules:{
				
				cat_id: {
		       		indexCheck : ""
		      	},
		      	sub_cat_id: {
		       		indexCheck : ""
		      	}
			},
			messages:{
				
				cat_id:{
			       indexCheck: "<?php echo $this->lang->line('f_item_cat_required'); ?>"
			    },
			    sub_cat_id:{
			       indexCheck: "<?php echo $this->lang->line('f_item_subcat_required'); ?>"
			    }
			},

			// submitHandler: function(form) {
		 //        if ($("#item-form").valid()) {
		 //            form.submit();
		 //        }
		 //    }

		});
		
		jQuery.validator.addMethod("indexCheck",function( value, element ) {
			
			   if(value == 0) {
			    	return false;
			   } else {
			    	return true;
			   };
			   
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
			// alert("adfasd"+ catId);
			 
			$.ajax({
				url: '<?php echo site_url() . '/admin/items/get_all_sub_categories/';?>' + catId,
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

	    if(edit_product_check == 0) {
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

	   	//For File Upload 
		$("#fileUpload").on('change', function () {

		     //Get count of selected files
		     var countFiles = $(this)[0].files.length;

		     var imgPath = $(this)[0].value;
		     var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
		     var image_holder = $("#image-holder");
		     image_holder.empty();

		     if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
		         if (typeof (FileReader) != "undefined") {

		             //loop for each file selected for uploaded.
		             for (var i = 0; i < countFiles; i++) {

		                 var reader = new FileReader();
		                 reader.onload = function (e) {
		                     $("<img />", {
		                         "src": e.target.result,
		                             "class": "thumb-image"
		                     }).appendTo(image_holder);
		                 }

		                 image_holder.show();
		                 reader.readAsDataURL($(this)[0].files[i]);
		             }

		         } else {
		             alert("This browser does not support FileReader.");
		         }
		     } else {
		         alert("Pls select only images");
		     }
		});
		
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