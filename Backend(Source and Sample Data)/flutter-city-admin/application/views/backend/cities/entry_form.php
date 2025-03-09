<?php
$attributes = array('id' => 'city-form','enctype' => 'multipart/form-data');
echo form_open( '', $attributes);
?>

<section class="content animated fadeInRight">
	<div class="card card-info">
	    <div class="card-header">
	        <h3 class="card-title"><?php echo get_msg('city_info_label')?></h3>
	    </div>

        <!-- /.card-header -->
        <div class="card-body">
    		<div class="row">
				<div class="col-md-6">
					
					<br>

					<div class="form-group">
						<label>
							<?php echo get_msg('city_name_label') ?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" 
								title="<?php echo get_msg('city_name_tooltips')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<input class="form-control" type="text" placeholder="<?php echo get_msg('name_label'); ?>" name='name' id='name'
						value="<?php echo $city->name;?>">
					</div>

					<div class="form-group">
						<label><?php echo get_msg('description_label') ?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('city_desc_tooltips')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<textarea class="form-control" name="description" placeholder="<?php echo get_msg('description_label'); ?>" rows="9"><?php echo $city->description;?></textarea>
					</div>


					<div class="form-group">
						<label>
							<?php echo get_msg('city_email_label') ?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" 
								title="<?php echo get_msg('city_email_tooltips')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<input class="form-control" type="text" placeholder="<?php echo get_msg('city_email_label'); ?>" name='email' id='email'
						value="<?php echo $city->email;?>">
					</div>

					<div class="form-group">
						<label><input type="checkbox" name="status" value="1" <?php if($city->status == 1) echo "checked";?> >&nbsp;&nbsp;Status For Publish</label>
					</div>

					<div class="form-group">
						<div class="form-check">
							<label>
							
							<?php echo form_checkbox( array(
								'name' => 'is_featured',
								'id' => 'is_featured',
								'value' => 'accept',
								'checked' => set_checkbox('is_featured', 1, ( @$city->is_featured == 1 )? true: false ),
								'class' => 'form-check-input'
							));	?>

							<?php echo get_msg( 'is_featured' ); ?>

							</label>
						</div>
					</div>

					<?php if ( !isset( $city )): ?>
						<div class="form-group">
						
							<label><?php echo get_msg('city_cover_photo_label')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('city_photo_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label>

							<br>

							<?php echo get_msg('city_image_recommended_size')?>

							<input class="btn btn-sm" type="file" name="images1" id="images1">
						</div>

						<?php else: ?>

							<label><?php echo get_msg('city_cover_photo_label')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('city_photo_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label>

							<br>

							<?php echo get_msg('city_image_recommended_size')?>

							<a class="btn btn-primary btn-upload pull-right" data-toggle="modal" data-target="#uploadImage">
								<?php echo get_msg('btn_replace_photo')?> 
							</a>

							<hr/>	
							<?php
								$conds = array( 'img_type' => 'city', 'img_parent_id' => $city->id );
		
								$images = $this->Image->get_all_by( $conds )->result();
							?>
		
							<?php if ( count($images) > 0 ): ?>
		
								<div class="row">

								<?php $i = 0; foreach ( $images as $img ) :?>

									<?php if ($i>0 && $i%3==0): ?>
											
								</div><div class='row'>
									
									<?php endif; ?>
										
									<div class="col-md-4" style="height:100">

										<div class="thumbnail">

											<img src="<?php echo $this->ps_image->upload_thumbnail_url . $img->img_path; ?>">

											<br/>
											
											<p class="text-center">
												
												<a data-toggle="modal" data-target="#deletePhoto" class="delete-img" id="<?php echo $img->img_id; ?>"   
													image="<?php echo $img->img_path; ?>">
													<?php echo get_msg('remove_label'); ?>
												</a>
											</p>

										</div>

									</div>

								<?php endforeach;?>

								</div>

							
							<?php endif; ?>
						<?php endif; ?>	
					</div>

					<div class="col-md-6">

						<div id="city_map" style="width: 100%; height: 400px;"></div>

			          	<div class="clearfix">&nbsp;</div>
							
						<div class="form-group">
							<label><?php echo get_msg('city_lat_label') ?>
				              	<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('city_lat_label')?>">
				              		<span class='glyphicon glyphicon-info-sign menu-icon'>
				              	</a>
			              	</label>

							<br>

							<?php 
								echo form_input( array(
									'type' => 'text',
									'name' => 'lat',
									'id' => 'lat',
									'class' => 'form-control',
									'placeholder' => '',
									'value' => set_value( 'lat', show_data( @$city->lat ), false ),
								));
							?>
						</div>

						<div class="form-group">
							<label><?php echo get_msg('city_lng_label') ?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" 
									title="<?php echo get_msg('city_lng_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label>

							<br>

							<?php 
								echo form_input( array(
									'type' => 'text',
									'name' => 'lng',
									'id' => 'lng',
									'class' => 'form-control',
									'placeholder' => '',
									'value' =>  set_value( 'lat', show_data( @$city->lng ), false ),
								));
							?>
							</div>
						</div>
							
						<div class="form-group">
							<label><?php echo get_msg('address_label')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('city_address_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label>
							<textarea class="form-control" name="address" placeholder="Address" rows="9"><?php echo $city->address;?></textarea>
						</div>

					</div>

			</div>
        </div>
        

		<div class="card-footer">
           <button type="submit" name="save" class="btn btn-primary">
				<?php echo get_msg('btn_save')?>
			</button>
				
			<a href="<?php echo site_url('admin/cities');?>" class="btn btn-primary"><?php echo get_msg('btn_cancel'); ?></a>
        </div>
       
    </div>

    <!-- card info -->

</section>
<?php echo form_close(); ?>

<div class="modal fade"  id="deletecity">
		
	<div class="modal-dialog">
		
		<div class="modal-content">
		
			<div class="modal-header">
				<h4 class="modal-title"><?php echo get_msg('delete_city_label')?></h4>

				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
				</button>

			</div>

			<div class="modal-body">
				<p><?php echo get_msg('delete_city_confirm_message')?></p>
				 <p>1. Categories</p>
                <p>2. Sub-Categories</p>
                <p>3. Items and Collection</p>
                <p>4. News Feeds</p>
                <p>5. Watch List</p>
                <p>6. Comments</p>
                <p>7. Contact Us Message</p>
                <p>8. Transactions</p>
                <p>9. Reports</p>
                <p>10. User city</p>
                <p>11. Likes</p>
                <p>12. Favourites</p>
			</div>

			<div class="modal-footer">
				<a type="button" class="btn btn-default btn-delete-city">Yes</a>
				<a type="button" class="btn btn-default" data-dismiss="modal">Cancel</a>
			</div>

		</div>
	
	</div>			
		
</div>


<script>
	function runAfterJQ() {
		$('.delete-img').click(function(e){
			e.preventDefault();

			// get id and image
			var id = $(this).attr('id');

			// do action
			var action = '<?php echo $module_site_url .'/delete_cover_photo/'; ?>' + id + '/<?php echo @$city->id; ?>';
			console.log( action );
			$('.btn-delete-image').attr('href', action);
			
		});

		$('.delete-city').click(function(e){
			e.preventDefault();
			var id = $(this).attr('id');
			var image = $(this).attr('image');
			var action = '<?php echo site_url('/admin/cities/delete/');?>';
			$('.btn-delete-city').attr('href', action + id);
		});

		$('#city-form').validate({
			rules:{
				lat:{
                    blankCheck : "",
                    indexCheck : "",
                    validChecklat : ""
			    },
			    lng:{
			     	blankCheck : "",
			     	indexCheck : "",
			     	validChecklng : ""
			    }
			},
			messages:{
				lat:{
			     	blankCheck : "<?php echo get_msg( 'err_lat' ) ;?>",
			     	indexCheck : "<?php echo get_msg( 'err_lat_lng' ) ;?>",
			     	validChecklat : "<?php echo get_msg( 'lat_invlaid' ) ;?>"
			    },
			    lng:{
			     	blankCheck : "<?php echo get_msg( 'err_lng' ) ;?>",
			     	indexCheck : "<?php echo get_msg( 'err_lat_lng' ) ;?>",
			     	validChecklng : "<?php echo get_msg( 'lng_invlaid' ) ;?>"
			    }
			}

		});

		
		// custom validation
		jQuery.validator.addMethod("blankCheck",function( value, element ) {
			
			   if(value == "") {
			    	return false;
			   } else {
			    	return true;
			   }
		});
		
		jQuery.validator.addMethod("indexCheck",function( value, element ) {
			
			   if(value == 0) {
			    	return false;
			   } else {
			    	return true;
			   };
			   
		});

	    jQuery.validator.addMethod("validChecklat",function( value, element ) {
			    if (value < -90 || value > 90) {
			    	return false;
			    } else {
			   	 	return true;
			    }
		});

		jQuery.validator.addMethod("validChecklng",function( value, element ) {
			    if (value < -180 || value > 180) {
			    	return false;
			   } else {
			   	 	return true;
			   }
		});
		
	}
</script>
<?php 
	// replace cover photo modal
	$data = array(
		'title' => get_msg('upload_photo'),
		'img_type' => 'city',
		'img_parent_id' => @$city->id
	);
	
	$this->load->view( $template_path .'/components/city_photo_upload_modal', $data );
	
	// delete cover photo modal
	$this->load->view( $template_path .'/components/delete_cover_photo_modal' ); 
?>

<script>
	<?php
		if (isset($city)) {
			$lat = $city->lat;
			$lng = $city->lng;
	?>
			var citymap = L.map('city_map').setView([<?php echo $lat;?>, <?php echo $lng;?>], 5);
	<?php
		} else {
	?>
			var citymap = L.map('city_map').setView([0, 0], 5);
	<?php
		}
	?>

	const city_attribution =
	'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
	const city_tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	const city_tiles = L.tileLayer(city_tileUrl, { city_attribution });
	city_tiles.addTo(citymap);
	<?php if(isset($city)) {?>
		var marker2 = new L.Marker(new L.LatLng(<?php echo $lat;?>, <?php echo $lng;?>));
		citymap.addLayer(marker2);
		// results = L.marker([<?php echo $lat;?>, <?php echo $lng;?>]).addTo(mymap);

	<?php } else { ?>
		var marker2 = new L.Marker(new L.LatLng(0, 0));
		//mymap.addLayer(marker2);
	<?php } ?>
	var searchControl = L.esri.Geocoding.geosearch().addTo(citymap);
	var results = L.layerGroup().addTo(citymap);

	searchControl.on('results',function(data){
		results.clearLayers();

		for(var i= data.results.length -1; i>=0; i--) {
			citymap.removeLayer(marker2);
			results.addLayer(L.marker(data.results[i].latlng));
			var search_str = data.results[i].latlng.toString();
			var search_res = search_str.substring(search_str.indexOf("(") + 1, search_str.indexOf(")"));
			var searchArr = new Array();
			searchArr = search_res.split(",");

			document.getElementById("lat").value = searchArr[0].toString();
			document.getElementById("lng").value = searchArr[1].toString(); 
			
		}
	})
	var popup = L.popup();

	function onMapClick(e) {

		var str = e.latlng.toString();
		var str_res = str.substring(str.indexOf("(") + 1, str.indexOf(")"));
		citymap.removeLayer(marker2);
		results.clearLayers();
		results.addLayer(L.marker(e.latlng));   

		var tmpArr = new Array();
		tmpArr = str_res.split(",");

		document.getElementById("lat").value = tmpArr[0].toString(); 
		document.getElementById("lng").value = tmpArr[1].toString();
	}

	citymap.on('click', onMapClick);
</script>
