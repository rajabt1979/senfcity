 <div class="content">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"> Welcome, <?php echo $this->ps_auth->get_user_info()->user_name;?>!</h1>
            <?php flash_msg(); ?>

          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
</div>
<!-- /.content -->
<div class="container-fluid">
  <div class="card-body">
    <div class="row">
      <div class="col-lg-3">
        <!-- small box -->
        <?php 
              $data = array(
                'url' => site_url() . "/admin/comments" ,
                'total_count' => $this->Commentheader->count_all_by($conds1),
                'label' => get_msg( 'total_comment_count_label'),
                'icon' => "fa fa-comment",
                'color' => "bg-danger",
                'data' => $this->Commentheader->get_all_by($conds1)->result()
              );
          $this->load->view( $template_path .'/components/ps_badge_count', $data ); 
        ?>
      </div>
      <!-- ./col -->
      <div class="col-lg-3">
        <!-- small box -->
         <?php 
                $data = array(
                'url' => site_url() . "/admin/contacts" ,
                'total_count' => $this->Contact->count_all_by($conds1),
                'label' => get_msg( 'total_contact_count_label'),
                'icon' => "fa fa-envelope",
                'color' => "bg-danger",
                'data' => $this->Contact->get_all_by($conds1)->result()
              );
          $this->load->view( $template_path .'/components/ps_badge_count', $data ); 
        ?>
      </div>
      <!-- ./col -->
       <div class="col-lg-3">
        <!-- small box -->
        <?php 
                $data = array(
                'url' => site_url() . "/admin/popularitems" ,
                'total_count' => $this->Popularitem->count_item_by($conds1),
                'label' => get_msg( 'total_contact_count_label'),
                'icon' => "fa fa-thumbs-up",
                'color' => "bg-danger",
                'data' => $this->Popularitem->get_item_by($conds1)->result()
              );
          $this->load->view( $template_path .'/components/ps_badge_count', $data ); 
        ?>
      </div>
      <!-- ./col -->
      <div class="col-lg-3">
        <!-- small box -->
        <?php 
                $data = array(
                'url' => site_url() . "/admin/ratings" ,
                'total_count' => $this->Rate->count_item_by($conds1),
                'label' => get_msg( 'total_contact_count_label'),
                'icon' => "fa fa-star",
                'color' => "bg-danger",
                'data' => $this->Rate->get_item_by($conds1)->result()
              );
          $this->load->view( $template_path .'/components/ps_badge_count', $data ); 
        ?>
      </div>
      <!-- ./col -->
      <div class="col-md-12">
        <div class="card">
        <!-- small box -->
          <?php 
            
            $data = array(
              'panel_title' => get_msg('total_revenue'),
              'module_name' => 'favourite' ,
              'total_count' => $this->Favourite->count_all( ),
              'data' => $this->Favourite->get_favourite_by_month(5)->result()
            );
            $this->load->view( $template_path .'/components/d2_total_revenue_panel', $data ); 
          ?>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-12">
        <div class="card">
          <?php
            $data = array(
              'panel_title' => get_msg('item_label'),
              'module_name' => 'items' ,
              'total_count' => $this->Item->count_all(),
              'data' => $this->Item->get_all_by($conds1,5)->result()
            );

            $this->load->view( $template_path .'/components/d2_item_panel', $data ); 
          ?>
        </div>
      </div>
      <!-- /.col -->
      <div class="col-md-4 col-xlg-3">
        <div class="card earning-widget">
          <?php 
            $data = array(
              'panel_title' => get_msg('cat_label'),
              'total_count' => $this->Category->count_all(),
              'data' => $this->Category->get_all_by($conds1,5)->result()
            );

            $this->load->view( $template_path .'/components/d2_category_panel', $data ); 
          ?>
        </div>
      </div>

      <div class="col-md-4 col-xlg-3">
        <div class="card card-widget widget-user-2">
          <?php 
            $data = array(
              'panel_title' => get_msg('sub_cat_label'),
              'total_count' => $this->Subcategory->count_all(),
              'data' => $this->Subcategory->get_all_by($conds1,5)->result()
            );

            $this->load->view( $template_path .'/components/d2_sub_cat_panel', $data ); 
          ?>
        </div>
      </div>
      <!-- /.col -->
      <div class="col-md-4 col-xlg-3">
        <div class="card card-widget widget-user-2">
          <?php 
            $data = array(
              'panel_title' => get_msg('new_feed_label'),
              'total_count' => $this->Feed->count_all(),
              'data' => $this->Feed->get_all_by($conds1,5)->result()
            );

            $this->load->view( $template_path .'/components/d2_new_feed_panel', $data ); 
          ?>
        </div>
      </div>
    </div>
    <!-- /.row -->
  </div>
<!-- /. card-body -->
</div>
<!-- /.container fluid -->