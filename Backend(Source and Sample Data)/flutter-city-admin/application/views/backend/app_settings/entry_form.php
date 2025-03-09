<?php
$attributes = array('id' => 'app-form','enctype' => 'multipart/form-data');
echo form_open( '', $attributes);
?>

<section class="content animated fadeInRight">
	<div class="card card-info">
		 <div class="card-header">
	        <h3 class="card-title"><?php echo get_msg('app_setting_lable')?></h3>
	    </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
              	<div class="col-md-6">
					<div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							
							<?php echo form_checkbox( array(
								'name' => 'is_approval_enabled',
								'id' => 'is_approval_enabled',
								'value' => 'accept',
								'checked' => set_checkbox('is_approval_enabled', 1, ( @$app->is_approval_enabled == 1 )? true: false ),
								'class' => 'form-check-input'
							));	?>

							<?php echo get_msg( 'app_is_approval_enabled' ); ?>

							</label>
						</div>
					</div>
				</div>
            </div>
        </div>

            <div class="card-footer">
				<button type="submit" name="save" class="btn btn-sm btn-primary">
					<?php echo get_msg('btn_save')?>
				</button>

				<a href="<?php echo $module_site_url; ?>" class="btn btn-sm btn-primary">
					<?php echo get_msg('btn_cancel')?>
				</a>
			</div>
        </div>
    </div>
</section>