<?php
	$attributes = array( 'id' => 'popularitem-form', 'enctype' => 'multipart/form-data');
	echo form_open( '', $attributes);
?>

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
							'value' => set_value( 'name', show_data( @$popularitem->name), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('item_name'),
							'id' => 'name',
							'readonly' => "true"
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
								set_value( 'cat_id', show_data( @$popularitem->cat_id), false ),
								'class="form-control form-control-sm mr-3" id="cat_id" disabled'
							);
						?>
					</div>

					<div class="form-group">
						<label> <span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('Prd_search_subcat')?>
						</label>

						<?php
							if(isset($popularitem)) {
								$options=array();
								$options[0]=get_msg('Prd_search_subcat');
								$conds['cat_id'] = $popularitem->cat_id;
								$sub_cat = $this->Subcategory->get_all_by($conds);
								foreach($sub_cat->result() as $subcat) {
									$options[$subcat->id]=$subcat->name;
								}
								echo form_dropdown(
									'sub_cat_id',
									$options,
									set_value( 'sub_cat_id', show_data( @$popularitem->sub_cat_id), false ),
									'class="form-control form-control-sm mr-3" id="sub_cat_id" disabled'
								);

							} else {
								$conds['cat_id'] = $selected_cat_id;
								$options=array();
								$options[0]=get_msg('Prd_search_subcat');

								echo form_dropdown(
									'sub_cat_id',
									$options,
									set_value( 'sub_cat_id', show_data( @$popularitem->sub_cat_id), false ),
									'class="form-control form-control-sm mr-3" id="sub_cat_id" disabled'
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
							'value' => set_value( 'info', show_data( @$popularitem->highlight_information), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('prd_high_info'),
							'id' => 'info',
							'rows' => "3",
							'readonly' => "true"
						)); ?>

					</div>

					<div class="form-group">
						<label> <span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('prd_desc')?>
						</label>

						<?php echo form_textarea( array(
							'name' => 'description',
							'value' => set_value( 'desc', show_data( @$popularitem->description), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('prd_desc'),
							'id' => 'desc',
							'rows' => "5",
							'readonly' => "true"
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
								set_value( 'item_status_id', show_data( @$popularitem->item_status_id), false ),
								'class="form-control form-control-sm mr-3" id="item_status_id" disabled'
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
	            	<?php if (  @$popularitem->lat !='0' && @$popularitem->lng !='0' ):?>
			          <!-- <div class="col-md-6"> -->
			            <div id="popularitem_map" style="width: 100%; height: 300px; pointer-events: none;"></div>
			            <div class="clearfix">&nbsp;</div>
			          <!-- </div> -->

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
			                  'value' => set_value( 'lat', show_data( @$popularitem->lat), false ),
			                  'readonly' => 'true'
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
			                  'value' => set_value( 'lng', show_data( @$popularitem->lng), false ),
			                  'readonly' => 'true'
			                ));
			              ?>
			            </div>

			            <div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('search_tag')?>
							</label>

							<?php echo form_input( array(
								'name' => 'search_tag',
								'value' => set_value( 'search_tag', show_data( @$popularitem->search_tag), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('search_tag'),
								'id' => 'search_tag',
								'readonly' => "true"
							)); ?>

						</div>
			            <!-- form group -->
			          </div>
			          <?php endif ?>
	                  <!-- <div class="col-md-6">
						<div class="form-group">
							<label> <span style="font-size: 17px; color: red;">*</span>
								<?php echo get_msg('search_tag')?>
							</label>

							<?php echo form_input( array(
								'name' => 'search_tag',
								'value' => set_value( 'search_tag', show_data( @$popularitem->search_tag), false ),
								'class' => 'form-control form-control-sm',
								'placeholder' => get_msg('search_tag'),
								'id' => 'search_tag',
								'readonly' => "true"
							)); ?>

						</div>
					</div> -->

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
								'checked' => set_checkbox('is_featured', 1, ( @$popularitem->is_featured == 1 )? true: false ),
								'class' => 'form-check-input',
								'onclick' => 'return false'
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
								'checked' => set_checkbox('is_promotion', 1, ( @$popularitem->is_promotion == 1 )? true: false ),
								'class' => 'form-check-input',
								'onclick' => 'return false'
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
						<input id="timestart" type="text" name="opening_hour" value="<?php echo $popularitem->opening_hour; ?>" style="width: 50%;" disabled />
						<br><br>
						<label>
							Close time:
						</label>
						<br>
						<input type="text" id="time_end" name="closing_hour" value="<?php echo $popularitem->closing_hour; ?>"  style="width: 50%;" disabled />
					
					</div>

				</div>

				<div class="col-md-6">
					
					<div class="form-group">
						<label> <span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('time_remark')?>
						</label>

						<?php echo form_textarea( array(
							'name' => 'time_remark',
							'value' => set_value( 'time_remark', show_data( @$popularitem->time_remark), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('time_remark'),
							'id' => 'time_remark',
							'rows' => "5",
							'readonly' => "true"
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
							'value' => set_value( 'phone1', show_data( @$popularitem->phone1), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('phone_no_1'),
							'id' => 'phone1',
							'readonly' => "true"
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
							'value' => set_value( 'phone2', show_data( @$popularitem->phone2), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('phone_no_2'),
							'id' => 'phone2',
							'readonly' => "true"
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
							'value' => set_value( 'phone3', show_data( @$popularitem->phone3), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('phone_no_3'),
							'id' => 'phone3',
							'readonly' => "true"
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
							'value' => set_value( 'email', show_data( @$popularitem->email), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('email'),
							'id' => 'email',
							'readonly' => "true"
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
							'value' => set_value( 'address', show_data( @$popularitem->address), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('address'),
							'id' => 'address',
							'readonly' => "true"
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
							'value' => set_value( 'facebook', show_data( @$popularitem->facebook), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('facebook'),
							'id' => 'facebook',
							'rows' => "3",
							'readonly' => "true"
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
							'value' => set_value( 'google_plus', show_data( @$popularitem->google_plus), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('google_plus'),
							'id' => 'google_plus',
							'rows' => "3",
							'readonly' => "true"
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
							'value' => set_value( 'twitter', show_data( @$popularitem->twitter), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('twitter'),
							'id' => 'twitter',
							'rows' => "3",
							'readonly' => "true"
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
							'value' => set_value( 'youtube', show_data( @$popularitem->youtube), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('youtube'),
							'id' => 'youtube',
							'rows' => "3",
							'readonly' => "true"
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
							'value' => set_value( 'instagram', show_data( @$popularitem->instagram), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('instagram'),
							'id' => 'instagram',
							'rows' => "3",
							'readonly' => "true"
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
							'value' => set_value( 'pinterest', show_data( @$popularitem->pinterest), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('pinterest'),
							'id' => 'pinterest',
							'rows' => "3",
							'readonly' => "true"
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
							'value' => set_value( 'website', show_data( @$popularitem->website), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('website'),
							'id' => 'Website',
							'rows' => "3",
							'readonly' => "true"
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
							'value' => set_value( 'whatsapp', show_data( @$popularitem->whatsapp), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('whatsapp'),
							'id' => 'whatsapp',
							'rows' => "3",
							'readonly' => "true"
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
							'value' => set_value( 'messenger', show_data( @$popularitem->messenger), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('messenger'),
							'id' => 'messenger',
							'rows' => "3",
							'readonly' => "true"
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
							'value' => set_value( 'terms', show_data( @$popularitem->terms), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('terms'),
							'id' => 'terms',
							'rows' => "3",
							'readonly' => "true"
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
							'value' => set_value( 'cancelation_policy', show_data( @$popularitem->cancelation_policy), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('cancelation_policy'),
							'id' => 'cancelation_policy',
							'rows' => "3",
							'readonly' => "true"
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
							'value' => set_value( 'additional_info', show_data( @$popularitem->additional_info), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg('additional_info'),
							'id' => 'additional_info',
							'rows' => "3",
							'readonly' => "true"
						)); ?>

					</div>
				</div>
			</div>	


            
			<label><?php echo get_msg('item_specification')?></label>
    		<!-- product specification for edit -->
			<?php if ( isset( $product )){ ?>

				<?php 

					$spec_data = $this->Specification->get_all_by( array( 'product_id' => @$popularitem->id ))->result(); 
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

					<div id="spec_data">
		    			<div class="col-md-6">
		    				<div class="form-group">
								<label>
									<?php echo get_msg('item_title') . " : " . $i?>
								</label>
								
								<?php echo form_input( array(
									'name' => 'prd_spec_title' . $i,
									'value' => $spec->name,
									'class' => 'form-control form-control-sm',
									'id' => 'prd_spec_title' . $i,
									'readonly' => "true"
								)); ?>
							
							</div>

							<div class="form-group">
								<label>
									<?php echo get_msg('item_desc') . " : "  .$i?>
								</label>
								
								<?php echo form_input( array(
									'name' => 'prd_spec_desc' . $i,
									'value' => $spec->description,
									'class' => 'form-control form-control-sm',
									'id' => 'prd_spec_desc' . $i,
									'readonly' => "true"
								)); ?>
								
							</div>
						</div>		
		    		</div>
		    		<?php endforeach; ?>

				<?php endif; ?>
				<!-- Edit Default save -->
				<div id="spec_data">
					<div class="col-md-6">
    					<div class="form-group">
							<label>
								<?php echo get_msg('item_title') . " : " . $spec_data_count ?>
							</label>
						
							<?php echo form_input( array(
								'name' => 'prd_spec_title'.$spec_data_count,
								'value' => set_value( 'prd_spec_title1', show_data( @$popularitem->prd_spec_title1), false ),
								'class' => 'form-control form-control-sm',
								'id' => 'prd_spec_title'.$spec_data_count,
								'readonly' => "true"
							)); ?>
						
						</div>

						<div class="form-group">
							<label>
								<?php echo get_msg('item_desc') . " : " . $spec_data_count ?>
							</label>
							
							<?php echo form_input( array(
								'name' => 'prd_spec_desc'.$spec_data_count,
								'value' => set_value( 'prd_spec_desc1', show_data( @$popularitem->prd_spec_desc1), false ),
								'class' => 'form-control form-control-sm',
								'id' => 'prd_spec_desc'.$spec_data_count,
								'readonly' => "true"
							)); ?>
							
						</div>
    				</div>
    			</div>

			<!-- product specification for save -->
			<?php } else { ?>
	    		<div id="spec_data">
	    			<div class="col-md-6">
						<div class="form-group">
							<label>
								<?php echo get_msg('item_title') . " : 1"?>
							</label>
							
							<?php echo form_input( array(
								'name' => 'prd_spec_title1',
								'value' => set_value( 'prd_spec_title1', show_data( @$popularitem->prd_spec_title1), false ),
								'class' => 'form-control form-control-sm',
								'id' => 'prd_spec_title1',
								'readonly' => "true"
							)); ?>
							
						</div>

						<div class="form-group">
							<label>
								<?php echo get_msg('item_desc') . " : 1"?>
							</label>

							<div class="form-group">
								<?php echo form_input( array(
									'name' => 'prd_spec_desc1',
									'value' => set_value( 'prd_spec_desc1', show_data( @$popularitem->prd_spec_desc1), false ),
									'class' => 'form-control form-control-sm',
									'id' => 'prd_spec_desc1',
									'readonly' => "true"
								)); ?>
							</div>
						</div>
					</div>
	    		</div>
	    	<?php } ?>
	    		<div class="col-6">
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

		<?php 
			if (isset($spec_data)) {
				$specs_count = $specs_count;
			} else {
				$specs_count = 0;
			} 
		?>
		<input type="hidden" id="spec_total_existing" name="spec_total_existing" value="<?php echo $specs_count; ?>">
		<?php 
			if ( isset( $popularitem )) { 
		?>
			<input type="hidden" id="edit_product" name="edit_product" value="1">
		<?php		
			} else {
		?>
			<input type="hidden" id="edit_product" name="edit_product" value="0">
		<?php } ?> 

	</div>
</section>
<?php echo form_close(); ?>


        <!-- popular item map-->

        <script>

            <?php
                if (isset($popularitem)) {
                    $lat = $popularitem->lat;
                    $lng = $popularitem->lng;
            ?>
                    var popularitem_map = L.map('popularitem_map').setView([<?php echo $lat;?>, <?php echo $lng;?>], 5);
            <?php
                } else {
            ?>
                    var popularitem_map = L.map('popularitem_map').setView([0, 0], 5);
            <?php
                }
            ?>

            const popularitem_attribution =
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
            const popularitem_tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            const popularitem_tiles = L.tileLayer(popularitem_tileUrl, { popularitem_attribution });
            popularitem_tiles.addTo(popularitem_map);
            <?php if(isset($popularitem)) {?>
                var popularitem_marker = new L.Marker(new L.LatLng(<?php echo $lat;?>, <?php echo $lng;?>));
                popularitem_map.addLayer(popularitem_marker);
                // results = L.marker([<?php echo $lat;?>, <?php echo $lng;?>]).addTo(mymap);

            <?php } else { ?>
                var popularitem_marker = new L.Marker(new L.LatLng(0, 0));
                //mymap.addLayer(marker2);
            <?php } ?>
            var results = L.layerGroup().addTo(popularitem_map);
            
            var popup = L.popup();

            function onMapClick(e) {

                var popularitem = e.latlng.toString();
                var popularitem_res = popularitem.substring(popularitem.indexOf("(") + 1, popularitem.indexOf(")"));
                popularitem_map.removeLayer(popularitem_marker);
                results.clearLayers();
                results.addLayer(L.marker(e.latlng));   

                var popularitem_tmpArr = new Array();
                popularitem_tmpArr = popularitem_res.split(",");

                document.getElementById("lat").value = popularitem_tmpArr[0].toString(); 
                document.getElementById("lng").value = popularitem_tmpArr[1].toString();
            }

            popularitem_map.on('click', onMapClick);
        </script>
