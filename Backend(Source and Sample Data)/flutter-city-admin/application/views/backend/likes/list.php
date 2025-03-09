<div class="table-responsive animated fadeInRight">
	<table class="table m-0 table-striped">
	
	<tr>
		<th><?php echo get_msg('no'); ?></th>
		<th><?php echo get_msg('item_name'); ?></th>
		<th><?php echo get_msg('like_user_name'); ?></th>		
	</tr>

	<?php $count = $this->uri->segment(4) or $count = 0; ?>

	<?php if ( !empty( $likes ) && count( $likes->result()) > 0 ): ?>

		<?php foreach($likes->result() as $like): ?>
			
			<tr>
				<td><?php echo ++$count;?></td>
				<td><?php echo $this->Item->get_one($like->item_id)->name ?></td>
				<td><?php echo $this->User->get_one($like->user_id)->user_name?></td>
			</tr>
		<?php endforeach; ?>

		<?php else: ?>
			
		<?php $this->load->view( $template_path .'/partials/no_data' ); ?>

	<?php endif; ?>
</table>
</div>
