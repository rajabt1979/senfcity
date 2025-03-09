<div class='row my-3'>
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
			  	<button type="submit" class="btn btn-sm btn-primary" name="submit" value="submit">
			  		<?php echo get_msg( 'btn_search' ); ?>
			  	</button>
		  	</div>

		  	<div class="form-group">
			  	<?php if( isset( $system_users ) && $system_users == true ) : ?>
				  	
				  	<a href="<?php echo $module_site_url . "/system_users"; ?>" class="btn btn-sm btn-primary">
				  		<?php echo get_msg( 'btn_reset' ); ?>
				  	</a>

			  	<?php else: ?>

				  	<a href="<?php echo $module_site_url ; ?>" class="btn btn-sm btn-primary">
				  		<?php echo get_msg( 'btn_reset' ); ?>
				  	</a>

			  	<?php endif; ?>
		  	</div>
		
		<?php echo form_close(); ?>

	</div>
</div>