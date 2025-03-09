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
<div class='row my-3'  style="padding: 10px 30px 10px 30px;">
	<div class='col-9'>
		<?php
			$attributes = array('class' => 'form-inline');

			echo form_open( $module_site_url . '/search', $attributes );
		?>
			<div class="form-group mr-3">
				
				<?php echo form_input(array(
					'name' => 'searchterm',
					'value' => set_value( 'searchterm' ),
					'class' => 'form-control form-control-sm',
					'placeholder' => get_msg( 'btn_search' ),
					'id' => ''
				)); ?>

		  	</div>

			<div class="form-group mr-3">
			  	<button type="submit" class="btn btn-sm btn-primary">
			  		<?php echo get_msg( 'btn_search' ); ?>
			  	</button>
		  	</div>

		  	<div class="form-group">
			  	<a href='<?php echo $module_site_url .'/index';?>' class='btn btn-sm btn-primary'>
					<?php echo get_msg( 'btn_reset' )?>
				</a>
		  	</div>
		
		<?php echo form_close(); ?>

	</div>

	<?php if ( !( isset( $system_users ) && $system_users == false )): ?>

		<div class='col-3'>
			<a href='<?php echo $module_site_url .'/add';?>' class='btn btn-sm btn-primary pull-right'>
				<span class='fa fa-plus'></span> 
				<?php echo get_msg( 'tag_add' )?>
			</a>
		</div>

	<?php endif;  ?>
</div>
<div class="table-responsive animated fadeInRight" style="padding: 10px 30px 10px 30px;">
	<table class="table m-0 table-striped">
		<tr>
			<th><?php echo get_msg('no'); ?></th>
			<th><?php echo get_msg('tag_name'); ?></th>
			<th><?php echo get_msg('tag_img'); ?></th>
			
			<?php if ( $this->ps_auth->has_access( EDIT )): ?>
				
				<th><span class="th-title"><?php echo get_msg('btn_edit')?></span></th>
			
			<?php endif; ?>
			
			<?php if ( $this->ps_auth->has_access( DEL )): ?>
				
				<th><span class="th-title"><?php echo get_msg('btn_delete')?></span></th>
			
			<?php endif; ?>
			
			<?php if ( $this->ps_auth->has_access( PUBLISH )): ?>
				
				<th><span class="th-title"><?php echo get_msg('btn_publish')?></span></th>
			
			<?php endif; ?>

		</tr>
		
	
	<?php $count = $this->uri->segment(4) or $count = 0; ?>

	<?php if ( !empty( $tags ) && count( $tags->result()) > 0 ): ?>

		<?php foreach($tags->result() as $tag): ?>
			
			<tr>
				<td><?php echo ++$count;?></td>
				<td><?php echo $tag->name;?></td>

				<?php $default_photo = get_default_photo( $tag->id, 'tag' ); ?>	

				<td><img src="<?php echo img_url( '/thumbnail/'. $default_photo->img_path ); ?>"/ width="40%"; height="60%";></td>

				<?php if ( $this->ps_auth->has_access( EDIT )): ?>
			
					<td>
						<a href='<?php echo $module_site_url .'/edit/'. $tag->id; ?>'>
							<i class='fa fa-pencil-square-o'></i>
						</a>
					</td>
				
				<?php endif; ?>
				
				<?php if ( $this->ps_auth->has_access( DEL )): ?>
					
					<td>
						<a herf='#' class='btn-delete' data-toggle="modal" data-target="#tagmodal" id="<?php echo "$tag->id";?>">
							<i class='fa fa-trash-o'></i>
						</a>
					</td>
				
				<?php endif; ?>
				
				<?php if ( $this->ps_auth->has_access( PUBLISH )): ?>
					
					<td>
						<?php if ( @$tag->status == 1): ?>
							<button class="btn btn-sm btn-success unpublish" id='<?php echo $tag->id;?>'>
							<?php echo get_msg('btn_yes'); ?></button>
						<?php else:?>
							<button class="btn btn-sm btn-danger publish" id='<?php echo $tag->id;?>'>
							<?php echo get_msg('btn_no'); ?></button><?php endif;?>
					</td>
				
				<?php endif; ?>

			</tr>

		<?php endforeach; ?>

	<?php else: ?>
			
		<?php $this->load->view( $template_path .'/partials/no_data' ); ?>

	<?php endif; ?>

</table>
</div>

