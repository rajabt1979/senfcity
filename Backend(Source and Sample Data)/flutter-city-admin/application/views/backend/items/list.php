<div class="table-responsive animated fadeInRight">
	<table class="table m-0 table-striped">
		<tr>
			<th><?php echo get_msg('no_label'); ?></th>
			<th><?php echo get_msg('item_name'); ?></th>
			<th><?php echo get_msg('cat_name'); ?></th>
			<th><?php echo get_msg('subcat_name'); ?></th>
			<th><?php echo get_msg('owner_name'); ?></th>
			
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

	<?php if ( !empty( $items ) && count( $items->result()) > 0 ): ?>

		<?php foreach($items->result() as $item): ?>
			
			<tr>
				<td><?php echo ++$count;?></td>
				<?php if($item->is_featured == 1 ) { ?>
				<td><span class="fa fa-diamond" style="color:red;"></span><?php echo $item->name;?></td>
				<?php } else { ?>
				<td><?php echo $item->name;?></td>
				<?php } ?>
				<td><?php echo $this->Category->get_one( $item->cat_id )->name; ?></td>
				<td><?php echo $this->Subcategory->get_one( $item->sub_cat_id )->name; ?></td>
				<td><?php echo $this->User->get_one( $item->added_user_id )->user_name; ?></td>
				<?php if ( $this->ps_auth->has_access( EDIT )): ?>
			
					<td>
						<a href='<?php echo $module_site_url .'/edit/'. $item->id; ?>'>
							<i class='fa fa-pencil-square-o'></i>
						</a>
					</td>
				
				<?php endif; ?>
				
				<?php if ( $this->ps_auth->has_access( DEL )): ?>
					
					<td>
						<a herf='#' class='btn-delete' data-toggle="modal" data-target="#myModal" id="<?php echo "$item->id";?>">
							<i class='fa fa-trash-o'></i>
						</a>
					</td>
				
				<?php endif; ?>
				
				<?php if ( $this->ps_auth->has_access( PUBLISH )): ?>
					
					<td>
						
						<?php if ( @$item->item_status_id== 1){ ?>
							<button class="btn btn-sm btn-success unpublish" id='<?php echo $item->id;?>'>
								<?php echo $this->Itemstatus->get_one($item->item_status_id)->title; ?>
							</button>
						<?php } ?>	
					</td>
				
				<?php endif; ?>

			</tr>

		<?php endforeach; ?>

	<?php else: ?>
			
		<?php $this->load->view( $template_path .'/partials/no_data' ); ?>

	<?php endif; ?>

</table>
</div>

