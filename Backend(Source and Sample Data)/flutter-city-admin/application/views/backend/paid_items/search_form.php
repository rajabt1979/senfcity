<?php
	$attributes = array('id' => 'search-form', 'enctype' => 'multipart/form-data');
	echo form_open( $module_site_url .'/search', $attributes);
?>

<div class='row my-3'>
	<div class="col-12">
		<div class='form-inline'>
			<div class="form-group" style="padding-top: 3px;">

				<?php echo form_input(array(
					'name' => 'searchterm',
					'value' => set_value( 'searchterm', $searchterm ),
					'class' => 'form-control form-control-sm mr-3',
					'placeholder' => get_msg( 'btn_search' )
				)); ?>

		  	</div>

		  	<div class="form-group mr-3" style="padding-top: 3px;">

				<?php
					$options=array();
					$options[0]=get_msg('Prd_search_cat');
					
					$categories = $this->Category->get_all( );
					foreach($categories->result() as $cat) {
						
							$options[$cat->id]=$cat->name;
					}
					
					echo form_dropdown(
						'cat_id',
						$options,
						set_value( 'cat_id', show_data( $cat_id ), false ),
						'class="form-control form-control-sm mr-3" id="cat_id"'
					);
				?> 
				

		  	</div>

	  		<div class="form-group" style="padding-top: 3px;">

				<?php
					if($selected_cat_id != "") {

						$options=array();
						$options[0]=get_msg('Prd_search_subcat');
						$conds['cat_id'] = $selected_cat_id;
						$sub_cat = $this->Subcategory->get_all_by($conds);
						foreach($sub_cat->result() as $subcat) {
							$options[$subcat->id]=$subcat->name;
						}
						echo form_dropdown(
							'sub_cat_id',
							$options,
							set_value( 'sub_cat_id', show_data( $sub_cat_id ), false ),
							'class="form-control form-control-sm mr-3" id="sub_cat_id"'
						);

					} else {

						$conds['cat_id'] = $selected_cat_id;
						$options=array();
						$options[0]=get_msg('Prd_search_subcat');

						echo form_dropdown(
							'sub_cat_id',
							$options,
							set_value( 'sub_cat_id', show_data( $sub_cat_id ), false ),
							'class="form-control form-control-sm mr-3" id="sub_cat_id"'
						);
					}
				?>

		  	</div>

		  	<div class="form-group" style="padding-top: 10px;">

				<?php
					$options=array();
					$options[0] = get_msg('select_status_label');
					
					$itemstatus = $this->Itemstatus->get_all();
					foreach($itemstatus->result() as $stu) {
						$options[$stu->id]=$stu->title;
					}
					
					echo form_dropdown(
						'item_status_id',
						$options,
						set_value( 'item_status_id', show_data( $item_status_id ), false ),
						'class="form-control form-control-sm mr-3" id="item_status_id"'
					);
				?> 
				

		  	</div>

		</div>
	</div>
</div>

<div class="row my-3">	
	<!-- end form-inline -->
	<div class="col-9">
		<div class="form-inline">
		
			<div class="form-group">
				<div class="form-check">

					<label class="form-check-label">
					
					<?php 
						echo form_checkbox( array(
							'name' => 'is_featured',
							'id' => 'is_featured',
							'value' => 'is_featured',
							'checked' => ($is_featured == 1 )? true: false ,
							'class' => 'form-check-input'
						));	
					?>

					<?php echo get_msg( 'is_featured' ); ?>

					</label>
				</div>
			</div>

			<div class="form-group" style="padding-left: 10px;">
				<div class="form-check">

					<label class="form-check-label">
					
					<?php 
						echo form_checkbox( array(
							'name' => 'is_promotion',
							'id' => 'is_promotion',
							'value' => 'is_promotion',
							'checked' => ($is_promotion == 1 )? true: false ,
							'class' => 'form-check-input'
						));	
					?>

					<?php echo get_msg( 'is_promotion' ); ?>

					</label>
				</div>
			</div>

			<div class="form-group mr-3" style="padding-left: 5px;">
				
				<?php echo get_msg( 'order_by' ); ?>
			
				<?php
					
					//echo $order_by . " #####";

					$options=array();
					$options[0]=get_msg('select_order');

					foreach ($this->Order->get_all()->result() as $ord) {

						$options[$ord->id]=$ord->name;
									
					}
					echo form_dropdown(
						'order_by',
						$options,
						set_value( 'order_by', show_data( $order_by), false ),
						'class="form-control form-control-sm mr-3 ml-2" id="order_by"'
					);
				?>

		  	</div>

		  	<div class="form-group mr-3" style="padding-right: 5px;">
			  	<button type="submit" class="btn btn-sm btn-primary" value="submit" name="submit">
			  		<?php echo get_msg( 'btn_search' )?>
			  	</button>
		  	</div>
		
			<div class="form-group">
			  	<a href='<?php echo $module_site_url .'/index';?>' class='btn btn-sm btn-primary'>
					<?php echo get_msg( 'btn_reset' )?>
				</a>
		  	</div>

		</div>
	</div>

</div>
<?php echo form_close(); ?>

<script>
	
<?php if ( $this->config->item( 'client_side_validation' ) == true ): ?>
	function jqvalidate() {
	$('#cat_id').on('change', function() {

			var catId = $(this).val();
			
			$.ajax({
				url: '<?php echo $module_site_url . '/get_all_sub_categories/';?>' + catId,
				method: 'GET',
				dataType: 'JSON',
				success:function(data){
					$('#sub_cat_id').html("");
					$.each(data, function(i, obj){
					    $('#sub_cat_id').append('<option value="'+ obj.id +'">' + obj.name + '</option>');
					});
					$('#name').val($('#name').val() + " ").blur();
				}
			});
		});
}
	<?php endif; ?>
</script>