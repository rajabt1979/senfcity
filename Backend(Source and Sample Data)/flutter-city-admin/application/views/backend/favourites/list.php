<div class="table-responsive animated fadeInRight">
	<table class="table m-0 table-striped">
	
	<tr>
		<th><?php echo get_msg('no_label'); ?></th>
		<th><?php echo get_msg('item_name'); ?></th>
		<th><?php echo get_msg('fav_user_name'); ?></th>		
	</tr>

	<?php $count = $this->uri->segment(4) or $count = 0; ?>

	<?php if ( !empty( $favourites ) && count( $favourites->result()) > 0 ): ?>

		<?php foreach($favourites->result() as $favourite): ?>
			
			<tr>
				<td><?php echo ++$count;?></td>
				<td><?php echo $this->Item->get_one($favourite->item_id)->name?></td>
				<td><?php echo $this->User->get_one($favourite->user_id)->user_name?></td>
			</tr>
		<?php endforeach; ?>

		<?php else: ?>
			
		<?php $this->load->view( $template_path .'/partials/no_data' ); ?>

	<?php endif; ?>
</table>
</div>
		
