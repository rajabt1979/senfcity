<?php
	$attributes = array( 'id' => 'tag-form', 'enctype' => 'multipart/form-data');
	echo form_open( '', $attributes);
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  	<a class="navbar-brand" href="#">
  		<!-- Brand Logo -->
	    <img src="<?php echo img_url( "cd_backend_logo.png" ); ?>" class="img-circle img-sm" alt="User Image">
  	</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
		<li class="nav-item" style="margin-left: 10px;">
        	<a class="btn btn-block btn-outline-primary" href="<?php echo site_url('admin/citys/');?>"> 
	  			<span class='fa fa-exchange'></span> <?php echo get_msg('back_city_list'); ?>
	  		</a>
	    </li>
      	<li class="nav-item dropdown" style="margin-left: 10px;">
      	    <a class="btn btn-block btn-outline-primary dropdown-toggle" data-toggle="dropdown" href="#">
        		<span class='fa fa-shopping-cart'></span> &nbsp; <?php echo get_msg('city_label'); ?>
        	</a>
        	<div class="dropdown-menu" aria-labelledby="navbarDropdown">
        		<a class="dropdown-item" href="<?php echo $module_site_url .'/cityadd';?>"> 
	  					&#10148;<?php echo get_msg('btn_create_new_city'); ?>
	  			</a>
          		<div class="dropdown-divider"></div>
          		<a class="dropdown-item" href='<?php echo $module_site_url .'/citylist';?>'> 
				  		&#10148; <?php echo get_msg('btn_city_list'); ?>
				</a>
				<div class="dropdown-divider"></div>
          		<a class="dropdown-item" href='<?php echo site_url('/admin/tags'); ?>'> 
				  		&#10148; <?php echo get_msg('btn_city_tag'); ?>
				</a>
        	</div>
      	</li>
      <li class="nav-item" style="margin-left: 10px;">
        <a class="btn btn-block btn-outline-primary" href="<?php echo site_url('/admin/notis');?>"> 
		  	&#10148; <?php echo get_msg('btn_push_notification'); ?>
		</a>
      </li>
      <li class="nav-item" style="margin-left: 10px;">
        <a class="btn btn-block btn-outline-primary" href="<?php echo $module_site_url .'/exports';?>"> 
	  		&#10148;<?php echo get_msg('btn_export_database'); ?>
	  	</a>
      </li>
      <li class="nav-item" style="margin-left: 10px;">
        <a class="btn btn-block btn-outline-primary" href="<?php echo site_url('/admin/system_users');?>"> 
		  	&#10148;<?php echo get_msg('btn_system_user'); ?>
		</a>
      </li>
      <li class="nav-item dropdown" style="margin-left: 10px;">
        <a class="btn btn-block btn-outline-primary dropdown-toggle" data-toggle="dropdown" href="#">
        	<span class='fa fa-gear'></span> &nbsp; <?php echo get_msg('setting_label'); ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        	<a class="dropdown-item" href="<?php echo site_url('admin/apis');?>"> 
	  					&#10148;<?php echo get_msg('api_info'); ?>
	  		</a>
          	<div class="dropdown-divider"></div>
          	<a class="dropdown-item" href="<?php echo site_url('/admin/abouts');?>"> 
				  		&#10148; <?php echo get_msg('btn_about_app'); ?>
			</a>
        </div>
      </li>
    </ul>
   	<ul class="navbar-nav ml-auto">

       <li class="user user-menu">
            <a href="<?php echo site_url ( $be_url . '/profile');?>" class="d-block">
		        <?php $logged_in_user = $this->ps_auth->get_user_info(); 
		        	if( $logged_in_user->user_profile_photo  != "") {
		        ?>
		        	<img src="<?php echo img_url( 'thumbnail/'. $logged_in_user->user_profile_photo ); ?>" class="user-image" alt="User Image">
		        <?php } else { ?>
		        	 <img src="<?php echo img_url( 'thumbnail/avatar.png'); ?>" class="user-image" alt="User Image">
		        <?php } ?>
		        <span class="hidden-xs" style="color: black; font-weight: bold;"><?php echo $logged_in_user->user_name;?></span>
            </a>
      	</li>

      	<li class="messages-menu open" style="padding-left: 30px;">
	        <a href="<?php echo site_url('logout');?>" aria-expanded="true">
	        	<i class="fa fa-sign-out" style="font-size: 1.5em; color: #000;"></i>
	        </a>
      	</li>

    </ul>
  </div>
</nav>
	
<div class="container-fluid">
	<div class="col-12"  style="padding: 30px 20px 20px 20px;">
		<div class="card earning-widget">
		    <div class="card-header">
	        	<h3 class="card-title"><?php echo get_msg('tag_info')?></h3>
	    	</div>
        	<!-- /.card-header -->
	        <div class="card-body">
	            <div class="row">
	             	<div class="col-md-6">
	                		<div class="form-group">
		                   		<label>
									<?php echo get_msg('tag_name')?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('name_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>

								<?php echo form_input( array(
									'name' => 'name',
									'value' => set_value( 'name', show_data( @$tag->name ), false ),
									'class' => 'form-control form-control-sm',
									'placeholder' => get_msg( 'tag_name' ),
									'id' => 'name'
								)); ?>
	                  		</div>

	                  		<div class="form-group">
								<div class="form-check">

									<label class="form-check-label">
									
										<?php echo form_checkbox( array(
											'name' => 'status',
											'id' => 'status',
											'value' => 'accept',
											'checked' => set_checkbox('status', 1, ( @$tag->status == 1 )? true: false ),
											'class' => 'form-check-input'
										));	?>
										<label><?php echo get_msg( 'status' ); ?></label>
									</label>
								</div>
							</div>

	                  	</div>

	                  	<div class="col-md-6"  style="padding-left: 50px;">
			                <?php if ( !isset( $tag )): ?>

							<div class="form-group">
							
								<label><?php echo get_msg('tag_img')?>
									<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('tag_photo_tooltips')?>">
										<span class='glyphicon glyphicon-info-sign menu-icon'>
									</a>
								</label>

								<br/>

								<input class="btn btn-sm" type="file" name="images1">
							</div>

							<?php else: ?>

							<label><?php echo get_msg('tag_img')?>
								<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('tag_photo_tooltips')?>">
									<span class='glyphicon glyphicon-info-sign menu-icon'>
								</a>
							</label> 
							
							<div class="btn btn-sm btn-primary btn-upload pull-right" data-toggle="modal" data-target="#uploadImage">
								<?php echo get_msg('btn_replace_photo')?>
							</div>
							
							<hr/>
						
							<?php
								$conds = array( 'img_type' => 'tag', 'img_parent_id' => $tag->id );
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

								<?php $i++; endforeach; ?>

								</div>
							
							<?php endif; ?>

						<?php endif; ?>	
						<!-- End tag cover photo -->
						<?php if ( !isset( $tag )): ?>

							<div class="form-group">
								
								<label>
									<?php echo get_msg('tag_icon')?> 
								</label>

								<br/>

								<input class="btn btn-sm" type="file" name="icon" id="icon">
							</div>

						<?php else: ?>

							<label><?php echo get_msg('tag_icon')?></label> 
							
							
							<div class="btn btn-sm btn-primary btn-upload pull-right" data-toggle="modal" data-target="#uploadIcon">
								<?php echo get_msg('btn_replace_icon')?>
							</div>
							
							<hr/>
							
							<?php

								$conds = array( 'img_type' => 'tag-icon', 'img_parent_id' => $tag->id );
								
								//print_r($conds); die;
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

								<?php endforeach; ?>

								</div>
							
							<?php endif; ?>

						<?php endif; ?>	
				
	                  	</div>
	                  	<!--  col-md-6  -->

	            </div>
	            <!-- /.row -->
	        </div>
	        <!-- /.card-body -->

			<div class="card-footer">
	            <button type="submit" class="btn btn-sm btn-primary">
					<?php echo get_msg('btn_save')?>
				</button>

				<a href="<?php echo $module_site_url; ?>" class="btn btn-sm btn-primary">
					<?php echo get_msg('btn_cancel')?>
				</a>
	        </div>
       	</div>
    </div>
    <!-- card info -->
</div>
				

	
	

<?php echo form_close(); ?>