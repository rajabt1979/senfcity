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
					'placeholder' => get_msg( 'btn_search' )
				)); ?>

		  	</div>

			<div class="form-group mr-3">
			  	<button type="submit" class="btn btn-sm btn-primary" value="submit" name="submit">
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
				<i class='fa fa-plus'></i> 
				<?php echo get_msg( 'btn_add_new' ); ?>
			</a>
		</div>

	<?php endif;  ?>
</div>