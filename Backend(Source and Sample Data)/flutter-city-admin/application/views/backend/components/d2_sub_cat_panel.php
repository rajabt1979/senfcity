 <div class="card-header"  style="border-top: 2px solid red;">
    <h3 class="card-title"><?php echo $panel_title; ?></h3>

    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-widget="collapse">
        <i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-tool" data-widget="remove">
        <i class="fa fa-times"></i>
      </button>
    </div>
</div>
<!-- /.card-header -->
<div class="card-body p-0">
    <ul class="products-list product-list-in-card pl-2 pr-2">
        <?php $count = $this->uri->segment(4) or $count = 0; ?>
            <?php if ( ! empty( $data )): ?>
                <?php foreach($data as $d): ?>
                    <li class="item">
                        <div class="product-img">
                            <?php $default_photo = get_default_photo( $d->id, 'sub_category' ); ?>
                            <img src="<?php echo img_url( '/thumbnail/'. $default_photo->img_path ); ?>" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                            <a href="javascript:void(0)" class="product-title"><?php echo $d->name; ?>
                            </a>
                        </div>
                    </li>
                    <!-- /.item -->
                <?php endforeach; ?>
            <?php endif; ?>
    </ul>
</div>
<?php 
    //get login user id
    $login_user = $this->ps_auth->get_user_info();
    $conds['user_id'] = $login_user->user_id;

    // get count from permission table
    $permission_data_count = $this->Permission->count_all_by($conds);

    if ($permission_data_count > 0) {
      /* for super admin and shop admin */
      //get module id
      $conds_moudle['module_name'] = "subcategories";
      $cond_permission['module_id'] = $this->Module->get_one_by($conds_moudle)->module_id;
      $cond_permission['user_id'] = $login_user->user_id;

      $allowed_module_data = $this->Permission->get_one_by($cond_permission);

      if ($allowed_module_data->is_empty_object == 0) { ?>
        <!-- allowed this module to login user -->
        <div class="card-footer text-center">
          <a href="<?php echo site_url('admin/subcategories'); ?>"><?php echo get_msg('view_all_subcats'); ?></a>
        </div>
        <!-- /.card-footer -->
      <?php } ?>
    <?php } else { ?>  
        <div class="card-footer text-center">
          <a href="<?php echo site_url('admin/subcategories'); ?>"><?php echo get_msg('view_all_subcats'); ?></a>
        </div>
        <!-- /.card-footer -->
<?php } ?> 