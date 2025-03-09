
<?php
	$attributes = array( 'id' => 'product-form', 'enctype' => 'multipart/form-data');
	echo form_open( '', $attributes);
	
?>

<style type="text/css">
	.thumb-image{
	 float:left;width:100px;
	 position:relative;
	 padding:5px;
	}
</style>	

<section class="content animated fadeInRight">
			
	<div class="card card-info">
      	<div class="card-header">
        	<h3 class="card-title"><?php echo get_msg('prd_info')?></h3>
      	</div>

    <form role="form">
        <div class="card-body">
        	<div class="row">
        		<div class="col-md-6">
	        		<div class="form-group">
						<label> <span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('item_name')?>
						</label>

						<?php echo form_input( array(
							'name' => 'name',
							'value' => set_value( 'name', show_data( @$item->name), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('item_name'),
							'id' => 'name'
						)); ?>

					</div>

					<div class="form-group">
						<label>
							<?php echo get_msg('product_name_count')?>
						</label>

						<?php echo form_input( array(
							'name' => 'counter',
							'value' => set_value( 'counter', show_data( @$item->counter), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('product_name_count'),
							'id' => 'counter'
						)); ?>

					</div>

					<div class="form-group">
						<label> <span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('Prd_search_cat')?>
						</label>

						<?php
							$options=array();
							$options[0]=get_msg('Prd_search_cat');
							$categories = $this->Category->get_all_by($conds);
							foreach($categories->result() as $cat) {
									$options[$cat->id]=$cat->name;
							}

							echo form_dropdown(
								'cat_id',
								$options,
								set_value( 'cat_id', show_data( @$item->cat_id), false ),
								'class="form-control form-control-sm mr-3" id="cat_id"'
							);
						?>
					</div>

					<div class="form-group">
						<label> <span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('Prd_search_subcat')?>
						</label>

						<?php
							if(isset($item)) {
								$options=array();
								$options[0]=get_msg('Prd_search_subcat');
								$conds['cat_id'] = $item->cat_id;
								$sub_cat = $this->Subcategory->get_all_by($conds);
								foreach($sub_cat->result() as $subcat) {
									$options[$subcat->id]=$subcat->name;
								}
								echo form_dropdown(
									'sub_cat_id',
									$options,
									set_value( 'sub_cat_id', show_data( @$item->sub_cat_id), false ),
									'class="form-control form-control-sm mr-3" id="sub_cat_id"'
								);

							} else {
								$conds['cat_id'] = $selected_cat_id;
								$options=array();
								$options[0]=get_msg('Prd_search_subcat');

								echo form_dropdown(
									'sub_cat_id',
									$options,
									set_value( 'sub_cat_id', show_data( @$item->sub_cat_id), false ),
									'class="form-control form-control-sm mr-3" id="sub_cat_id"'
								);
							}
							
						?>

					</div>

					<div class="form-group">
						<label> <span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('prd_high_info')?>
						</label>

						<?php echo form_textarea( array(
							'name' => 'highlight_information',
							'value' => set_value( 'info', show_data( @$item->highlight_information), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('prd_high_info'),
							'id' => 'info',
							'rows' => "3"
						)); ?>

					</div>

					<div class="form-group">
						<label> <span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('prd_desc')?>
						</label>

						<?php echo form_textarea( array(
							'name' => 'description',
							'value' => set_value( 'desc', show_data( @$item->description), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('prd_desc'),
							'id' => 'desc',
							'rows' => "5"
						)); ?>

					</div>

					<div class="form-group">
						<label> <span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('Prd_select_status')?>
						</label>

						<?php
							$options=array();
							$options[0]=get_msg('Prd_select_status');
							$status = $this->Itemstatus->get_all( );
							foreach($status->result() as $stu) {
									$options[$stu->id]=$stu->title;
							}

							echo form_dropdown(
								'item_status_id',
								$options,
								set_value( 'item_status_id', show_data( @$item->item_status_id), false ),
								'class="form-control form-control-sm mr-3" id="item_status_id"'
							);
						?>
					</div>


					<!-- <div class="form-group">

						<label>
							Close time:
						</label>
						<br>
						<input type="text" id="time_end" name="closing_hour" value="<?php echo $item->closing_hour; ?>"  style="width: 50%;"/>
					</div> -->

            	</div>

            	<div class="col-md-6">
					<div class="form-group">
				        <label>
				        	<?php echo get_msg('find_location_label')?>
				        	<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('find_location_tooltips')?>">
				        		<span class='glyphicon glyphicon-info-sign menu-icon'>
				        	</a>
				        </label><br>
				
				        <?php 
				        	echo form_input( array(
				        		'type' => 'text',
				        		'name' => 'find_location',
				        		'id' => 'find_location',
				        		'class' => 'form-control',
				        		'placeholder' => get_msg('find_location_label'),
				        		'value' => ''
				        	));
				        ?>
				    </div>

				    <div id="us3" style="width: 550px; height: 300px;"></div>
					<div class="clearfix">&nbsp;</div>
				    <div class="m-t-small">
				        <div class="form-group">
					        <label><?php echo get_msg('city_lat_label')?>
					        	<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('city_lat_tooltips')?>">
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
					        		'value' => ''
					        	));
					        ?>
				        </div>
				        
				        <div class="form-group">
					        <label><?php echo get_msg('city_lng_label')?>
					        	<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('city_lng_tooltips')?>">
					        		<span class='glyphicon glyphicon-info-sign menu-icon'>
					        	</a>
					        </label><br>
					        <?php 
					        	echo form_input( array(
					        		'type' => 'text',
					        		'name' => 'lng',
					        		'id' => 'lng',
					        		'class' => 'form-control',
					        		'placeholder' => '',
					        		'value' => ''
					        	));
					        ?>
				        </div>
				    </div>

					<div class="form-group">
						<label> <span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('search_tag')?>
						</label>

						<?php echo form_input( array(
							'name' => 'search_tag',
							'value' => set_value( 'search_tag', show_data( @$item->search_tag), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('search_tag'),
							'id' => 'search_tag'
						)); ?>

					</div>

					
					

					<?php if ( !isset( $item )): ?>

					<div class="form-group">
					
						<label>
							<span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('select_multiple_images')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('wallpaper_photo_tooltips')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<br/>

						<input id="fileUpload" type="file" name="images[]"  multiple/>
						<br>
						<div id="image-holder"></div>
					</div>

					<?php else: ?>

					<label>
						<span style="font-size: 17px; color: red;">*</span>
						<?php echo get_msg('product_img')?>
						<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('product_photo_tooltips')?>">
							<span class='glyphicon glyphicon-info-sign menu-icon'>
						</a>
					</label> 
					
					<div class="btn btn-sm btn-primary btn-upload pull-right" data-toggle="modal" data-target="#uploadImage">
						<?php echo get_msg('btn_replace_photo')?>
					</div>
					
					<hr/>
					
					<?php
						$conds = array( 'img_type' => 'item', 'img_parent_id' => $item->id );
						$images = $this->Image->get_all_by( $conds )->result();
					?>
						
					<?php if ( count($images) > 0 ): ?>
						
						<div class="row">

						<?php $i = 0; foreach ( $images as $img ) :?>

							<?php if ($i>0 && $i%3==0): ?>
									
							</div><div class='row'>
							
							<?php endif; ?>
								
							<div class="col-md-4" style="height:100">

									<a  href="<?php echo $this->ps_image->upload_url . $img->img_path; ?>">
										<img style="width: <?php echo $width ?>;height: <?php echo $height ?>;" src="<?php echo $this->ps_image->upload_thumbnail_url . $img->img_path; ?>">
									</a>
									<br/>
									
									<p class="text-center">
										
										<a data-toggle="modal" data-target="#deletePhoto" class="delete-img" id="<?php echo $img->img_id; ?>"   
											image="<?php echo $img->img_path; ?>">
											<?php echo get_msg('remove_label'); ?>
										</a>
									</p>

							</div>

						<?php $i++; endforeach; ?>

						</div>
					
						<?php endif; ?>

					<?php endif; ?>		
				</div>
    		</div>

    		<div class="row">
    			<div class="col-md-6">
    				<div class="form-group">
						<div class="form-check">
							<label>
							
							<?php echo form_checkbox( array(
								'name' => 'is_featured',
								'id' => 'is_featured',
								'value' => 'accept',
								'checked' => set_checkbox('is_featured', 1, ( @$item->is_featured == 1 )? true: false ),
								'class' => 'form-check-input'
							));	?>

							<?php echo get_msg( 'is_featured' ); ?>

							</label>
						</div>
					</div>

    			</div>

    			<div class="col-md-6">
    				<div class="form-group">
						<div class="form-check">
							<label>
							
							<?php echo form_checkbox( array(
								'name' => 'is_promotion',
								'id' => 'is_promotion',
								'value' => 'accept',
								'checked' => set_checkbox('is_promotion', 1, ( @$item->is_promotion == 1 )? true: false ),
								'class' => 'form-check-input'
							));	?>

							<?php echo get_msg( 'is_promotion' ); ?>

							</label>
						</div>
					</div>
    			</div>
    		</div>

    		<label>Schedule</label>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group" id="sample1">
							<label>
								Open time:
							</label>
							<br>
							<input id="timestart" type="text" name="opening_hour" value="<?php echo $item->opening_hour; ?>" style="width: 50%;" />
							<br><br>
							<label>
								Close time:
							</label>
							<br>
							<input type="text" id="time_end" name="closing_hour" value="<?php echo $item->closing_hour; ?>"  style="width: 50%;"/>
						
						</div>

					</div>

					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('time_remark')?>
							</label>

							<?php echo form_textarea( array(
								'name' => 'time_remark',
								'value' => set_value( 'time_remark', show_data( @$item->time_remark), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('time_remark'),
								'id' => 'time_remark',
								'rows' => "5"
							)); ?>

						</div>
					</div>
				</div>

				<label>Contact</label>
				<div class="row">
					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('phone_no_1')?>
							</label>

							<?php echo form_input( array(
								'name' => 'phone1',
								'value' => set_value( 'phone1', show_data( @$item->phone1), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('phone_no_1'),
								'id' => 'phone1'
								
							)); ?>

						</div>
					</div>

					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('phone_no_2')?>
							</label>

							<?php echo form_input( array(
								'name' => 'phone2',
								'value' => set_value( 'phone2', show_data( @$item->phone2), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('phone_no_2'),
								'id' => 'phone2'
								
							)); ?>

						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('phone_no_3')?>
							</label>

							<?php echo form_input( array(
								'name' => 'phone3',
								'value' => set_value( 'phone3', show_data( @$item->phone3), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('phone_no_3'),
								'id' => 'phone3'
							
							)); ?>

						</div>
					</div>

					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('email')?>
							</label>

							<?php echo form_input( array(
								'name' => 'email',
								'value' => set_value( 'email', show_data( @$item->email), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('email'),
								'id' => 'email'
								
							)); ?>

						</div>

					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('address')?>
							</label>

							<?php echo form_input( array(
								'name' => 'address',
								'value' => set_value( 'address', show_data( @$item->address), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('address'),
								'id' => 'address'
								
							)); ?>

						</div>
					</div>
				</div>

				<label>Social Information</label>
				<div class="row">
					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('facebook')?>
							</label>

							<?php echo form_textarea( array(
								'name' => 'facebook',
								'value' => set_value( 'facebook', show_data( @$item->facebook), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('facebook'),
								'id' => 'facebook',
								'rows' => "3"
							)); ?>

						</div>
					</div>

					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('google_plus')?>
							</label>

							<?php echo form_textarea( array(
								'name' => 'google_plus',
								'value' => set_value( 'google_plus', show_data( @$item->google_plus), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('google_plus'),
								'id' => 'google_plus',
								'rows' => "3"
							)); ?>

						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('twitter')?>
							</label>

							<?php echo form_textarea( array(
								'name' => 'twitter',
								'value' => set_value( 'twitter', show_data( @$item->twitter), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('twitter'),
								'id' => 'twitter',
								'rows' => "3"
							)); ?>

						</div>
					</div>

					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('youtube')?>
							</label>

							<?php echo form_textarea( array(
								'name' => 'youtube',
								'value' => set_value( 'youtube', show_data( @$item->youtube), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('youtube'),
								'id' => 'youtube',
								'rows' => "3"
							)); ?>

						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('instagram')?>
							</label>

							<?php echo form_textarea( array(
								'name' => 'instagram',
								'value' => set_value( 'instagram', show_data( @$item->instagram), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('instagram'),
								'id' => 'instagram',
								'rows' => "3"
							)); ?>

						</div>
					</div>

					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('pinterest')?>
							</label>

							<?php echo form_textarea( array(
								'name' => 'pinterest',
								'value' => set_value( 'pinterest', show_data( @$item->pinterest), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('pinterest'),
								'id' => 'pinterest',
								'rows' => "3"
							)); ?>

						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('website')?>
							</label>

							<?php echo form_textarea( array(
								'name' => 'website',
								'value' => set_value( 'website', show_data( @$item->website), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('website'),
								'id' => 'Website',
								'rows' => "3"
							)); ?>

						</div>
					</div>
				</div>	

				<label>Chatting</label>

				<div class="row">
					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('whatsapp')?>
							</label>

							<?php echo form_textarea( array(
								'name' => 'whatsapp',
								'value' => set_value( 'whatsapp', show_data( @$item->whatsapp), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('whatsapp'),
								'id' => 'whatsapp',
								'rows' => "3"
							)); ?>

						</div>
					</div>

					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('messenger')?>
							</label>

							<?php echo form_textarea( array(
								'name' => 'messenger',
								'value' => set_value( 'messenger', show_data( @$item->messenger), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('messenger'),
								'id' => 'messenger',
								'rows' => "3"
							)); ?>

						</div>
					</div>
				</div>

				<label>Policy</label>

				<div class="row">
					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('terms')?>
							</label>

							<?php echo form_textarea( array(
								'name' => 'terms',
								'value' => set_value( 'terms', show_data( @$item->terms), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('terms'),
								'id' => 'terms',
								'rows' => "3"
							)); ?>

						</div>
					</div>

					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('cancelation_policy')?>
							</label>

							<?php echo form_textarea( array(
								'name' => 'cancelation_policy',
								'value' => set_value( 'cancelation_policy', show_data( @$item->cancelation_policy), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('cancelation_policy'),
								'id' => 'cancelation_policy',
								'rows' => "3"
							)); ?>

						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('additional_info')?>
							</label>

							<?php echo form_textarea( array(
								'name' => 'additional_info',
								'value' => set_value( 'additional_info', show_data( @$item->additional_info), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('additional_info'),
								'id' => 'additional_info',
								'rows' => "3"
							)); ?>

						</div>
					</div>
				</div>	
    		
    		<label><?php echo get_msg('item_specification')?></label>
    		<div class="row">
    			<!-- product specification for edit -->
    				<?php if ( isset( $product )){ ?>
		
						<?php 

							$spec_data = $this->Specification->get_all_by( array( 'product_id' => @$product->id ))->result(); 
							$specs_count = count($spec_data);

							//counter need to plus one for edit default 
							$spec_data_count = $specs_count + 1;
						?>

							

						<?php if ( !empty( $spec_data )): ?>

							<?php 
								$i = 0;
								foreach( $spec_data as $spec ): 
								$i++;
							?>
				    			<div class="col-md-6">
				    				<div class="form-group">
				    					<?php if($i == 1) { ?>
											<label>
												<?php echo get_msg('item_title')?>
											</label>
										<?php } ?>
										<div class="form-group">
											<?php echo form_input( array(
												'name' => 'prd_spec_title' . $i,
												'value' => $spec->name,
												'class' => 'form-control form-control-sm',
												'id' => 'prd_spec_title' . $i,
											)); ?>
										</div>
									</div>
				    			</div>

				    			<div class="col-md-6">
				    				<div class="form-group">
				    					<?php if($i == 1) { ?>
											<label>
												<?php echo get_msg('item_desc')?>
											</label>
										<?php } ?>
										<div class="form-group">
											<?php echo form_input( array(
												'name' => 'prd_spec_desc' . $i,
												'value' => $spec->description,
												'class' => 'form-control form-control-sm',
												'id' => 'prd_spec_desc' . $i,
											)); ?>
										</div>
									</div>
				    			</div>
				    		<?php endforeach; ?>

						<?php endif; ?>
						<!-- Edit Default save -->
							<div class="col-md-6">
			    				<div class="form-group">
			    					<?php if($i == 0) { ?>
										<label>
											<?php echo get_msg('item_title')?>
										</label>
									<?php } ?>
									
									<div class="form-group title-url-input">
										<?php echo form_input( array(
											'name' => 'prd_spec_title'.$spec_data_count,
											'value' => set_value( 'prd_spec_title1', show_data( @$product->prd_spec_title1), false ),
											'class' => 'form-control form-control-sm',
											'id' => 'prd_spec_title'.$spec_data_count,
										)); ?>
									</div>
								</div>
			    			</div>

			    			<div class="col-md-6">
			    				<div class="form-group">
			    					<?php if($i == 0) { ?>
										<label>
											<?php echo get_msg('item_desc')?>
										</label>
									<?php } ?>

									<div class="form-group desc-url-input">
										<?php echo form_input( array(
											'name' => 'prd_spec_desc'.$spec_data_count,
											'value' => set_value( 'prd_spec_desc1', show_data( @$product->prd_spec_desc1), false ),
											'class' => 'form-control form-control-sm',
											'id' => 'prd_spec_desc'.$spec_data_count,
										)); ?>
									</div>
								</div>
			    			</div>
					<!-- product specification for save -->
					<?php } else { ?>
						<div class="col-md-6">
		    				<div class="form-group">
								<label>
									<?php echo get_msg('item_title')?>
								</label>
								
								<div class="form-group title-url-input">
									<?php echo form_input( array(
										'name' => 'prd_spec_title1',
										'value' => set_value( 'prd_spec_title1', show_data( @$product->prd_spec_title1), false ),
										'class' => 'form-control form-control-sm',
										'id' => 'prd_spec_title1',
									)); ?>
								</div>
							</div>
		    			</div>

		    			<div class="col-md-6">
		    				<div class="form-group">
								<label>
									<?php echo get_msg('item_desc')?>
								</label>

								<div class="form-group desc-url-input">
									<?php echo form_input( array(
										'name' => 'prd_spec_desc1',
										'value' => set_value( 'prd_spec_desc1', show_data( @$product->prd_spec_desc1), false ),
										'class' => 'form-control form-control-sm',
										'id' => 'prd_spec_desc1',
									)); ?>
								</div>
							</div>
		    			</div>
					<?php } ?>
    			<div class="col-12">
					<div class="form-group">
						<div class="mt-2">
							<a id="addspec" class="pull-right">
								<i class="fa fa-plus" aria-hidden="true"></i>
							<?php echo get_msg('add_more_spec'); ?>
							</a>
						</div>
					</div>
				</div>
    		</div>
				
		</div>

		<?php 
			if (isset($spec_data)) {
				$specs_count = $specs_count;
			} else {
				$specs_count = 0;
			} 
		?>
		<input type="hidden" id="spec_total_existing" name="spec_total_existing" value="<?php echo $specs_count; ?>">

		<?php 
			if ( isset( $product )) { 
		?>
			<input type="hidden" id="edit_product" name="edit_product" value="1">
		<?php		
			} else {
		?>
			<input type="hidden" id="edit_product" name="edit_product" value="0">
		<?php } ?> 

		<input type="hidden" id="is_featured_stage" name="is_featured_stage" value="<?php echo @$product->is_featured; ?>">

			<div class="card-footer">
	            <button type="submit" class="btn btn-sm btn-primary">
					<?php echo get_msg('btn_save')?>
				</button>

				<a href="<?php echo $module_site_url; ?>" class="btn btn-sm btn-primary">
					<?php echo get_msg('btn_cancel')?>
				</a>
	        </div>

		</div>
</section>
<?php echo form_close(); ?>
