 <!-- TABLE: LATEST ORDERS -->
<div class="card-header" style="border-top: 2px solid red;">
  <h3 class="card-title" style="margin-left: 10px;padding-top: 10px;text-transform: uppercase;font-weight: bold;">
    <?php echo $panel_title; ?>
  </h3>

  <div class="card-tools">
    <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i>
    </button>
    <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-times"></i>
    </button>
  </div>
</div>
<div class="card-body table-responsive p-0">
  
 <table class="table m-0 table-striped">
    <tr>
      <th><?php echo get_msg('no_label'); ?></th>
      <th><?php echo get_msg('item_name_label'); ?></th>
      <th><?php echo get_msg('category_name_label'); ?></th>
      <th><?php echo get_msg('sub_category_name_label'); ?></th>
      <th><?php echo get_msg('owner_name'); ?></th>
      
    </tr>
    
  
  <?php $count = $this->uri->segment(4) or $count = 0; ?>

  <?php if ( ! empty( $data )): ?>
    <?php foreach($data as $d): ?>
      
      <tr>
        <td><?php echo ++$count;?></td>
        <td><?php echo $d->name;?></td>
        <td><?php echo $this->Category->get_one( $d->cat_id )->name; ?></td>
        <td><?php echo $this->Subcategory->get_one( $d->sub_cat_id )->name; ?></td>
        <td><?php echo $this->User->get_one($d->added_user_id)->user_name;?></td>
       
      </tr>

    <?php endforeach; ?>

  <?php else: ?>
      
    <?php $this->load->view( $template_path .'/partials/no_data' ); ?>

  <?php endif; ?>

</table>
</div>
  <!-- /.table-responsive -->

<?php 
    //get login user id
    $login_user = $this->ps_auth->get_user_info();
    $conds['user_id'] = $login_user->user_id;

    // get count from permission table
    $permission_data_count = $this->Permission->count_all_by($conds);

    if ($permission_data_count > 0) {
      /* for super admin and shop admin */
      //get module id
      $conds_moudle['module_name'] = "items";
      $cond_permission['module_id'] = $this->Module->get_one_by($conds_moudle)->module_id;
      $cond_permission['user_id'] = $login_user->user_id;

      $allowed_module_data = $this->Permission->get_one_by($cond_permission);

      if ($allowed_module_data->is_empty_object == 0) { ?>
        <!-- allowed this module to login user -->
        <div class="card-footer text-center">
          <a href="<?php echo site_url('admin/items'); ?>"><?php echo get_msg('view_all_products'); ?></a>
        </div>
        <!-- /.card-footer -->
      <?php } ?>
    <?php } else { ?>  
        <div class="card-footer text-center">
          <a href="<?php echo site_url('admin/items'); ?>"><?php echo get_msg('view_all_products'); ?></a>
        </div>
        <!-- /.card-footer -->
<?php } ?>   
           
           