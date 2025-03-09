<?php
  $attributes = array( 'id' => 'pending-form', 'enctype' => 'multipart/form-data');
  echo form_open( '', $attributes);
?>

<section class="content animated fadeInRight">
      
  <div class="card card-info">
        <div class="card-header">
          <h3 class="card-title"><?php echo get_msg('prd_info')?></h3>
        </div>

    <form role="form">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('item_name')?>
            </label>

            <?php echo form_input( array(
              'name' => 'name',
              'value' => set_value( 'name', show_data( @$pending->name), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('pls_item_name'),
              'id' => 'name',
              'readonly' => "true"
            )); ?>

          </div>

          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('Prd_search_cat')?>
            </label>

            <?php
              $options=array();
              $options[0]=get_msg('Prd_search_cat');
              $categories = $this->Category->get_all_by($conds);
              foreach($categories->result() as $cat) {
                  $options[$cat->id]=$cat->name;
              }

              echo form_dropdown(
                'cat_id',
                $options,
                set_value( 'cat_id', show_data( @$pending->cat_id), false ),
                'class="form-control form-control-sm mr-3" pendingd="pendingd" id="cat_id"'
              );
            ?>
          </div>

          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('Prd_search_subcat')?>
            </label>

            <?php
              if(isset($pending)) {
                $options=array();
                $options[0]=get_msg('Prd_search_subcat');
                $conds['cat_id'] = $pending->cat_id;
                $sub_cat = $this->Subcategory->get_all_by($conds);
                foreach($sub_cat->result() as $subcat) {
                  $options[$subcat->id]=$subcat->name;
                }
                echo form_dropdown(
                  'sub_cat_id',
                  $options,
                  set_value( 'sub_cat_id', show_data( @$pending->sub_cat_id), false ),
                  'class="form-control form-control-sm mr-3" pendingd="pendingd" id="sub_cat_id"'
                );

              } else {
                $conds['cat_id'] = $selected_cat_id;
                $options=array();
                $options[0]=get_msg('Prd_search_subcat');

                echo form_dropdown(
                  'sub_cat_id',
                  $options,
                  set_value( 'sub_cat_id', show_data( @$pending->sub_cat_id), false ),
                  'class="form-control form-control-sm mr-3" pendingd="pendingd" id="sub_cat_id"'
                );
              }
              
            ?>

          </div>

          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('prd_high_info')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'highlight_information',
              'value' => set_value( 'info', show_data( @$pending->highlight_information), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('prd_high_info'),
              'id' => 'info',
              'rows' => "3",
              'readonly' => "true"
            )); ?>

          </div>

          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('prd_desc')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'description',
              'value' => set_value( 'desc', show_data( @$pending->description), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('prd_desc'),
              'id' => 'desc',
              'rows' => "5",
              'readonly' => "true"
            )); ?>

          </div>

        </div>

        <div class="col-md-6">
          <?php if (  @$pending->lat !='0' && @$pending->lng !='0' ):?>
          <div id="pending_map" style="width: 100%; height: 400px;"></div>
          
          <div class="clearfix">&nbsp;</div>
            <div class="m-t-small">
                <div class="form-group">
                  <label><?php echo get_msg('city_lat_label')?>
                    <a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('city_lat_tooltips')?>">
                      <span class='glyphicon glyphicon-info-sign menu-icon'>
                    </a>
                  </label>
                  <br>
                  <?php 
                    echo form_input( array(
                      'type' => 'text',
                      'name' => 'lat',
                      'id' => 'lat',
                      'class' => 'form-control',
                      'placeholder' => '',
                      'value' => set_value( 'lat', show_data( @$pending->lat ), false ),
                      'readonly' => "true"
                    ));
                  ?>
                </div>
                
                <div class="form-group">
                  <label><?php echo get_msg('city_lng_label')?>
                    <a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('city_lng_tooltips')?>">
                      <span class='glyphicon glyphicon-info-sign menu-icon'>
                    </a>
                  </label><br>
                  <?php 
                    echo form_input( array(
                      'type' => 'text',
                      'name' => 'lng',
                      'id' => 'lng',
                      'class' => 'form-control',
                      'placeholder' => '',
                      'value' => set_value( 'lng', show_data( @$pending->lng ), false ),
                      'readonly' => "true"
                    ));
                  ?>
                </div>
            </div>
          <?php endif; ?>  

          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('search_tag')?>
            </label>

            <?php echo form_input( array(
              'name' => 'search_tag',
              'value' => set_value( 'search_tag', show_data( @$pending->search_tag), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('search_tag'),
              'id' => 'search_tag',
              'readonly' => "true"
            )); ?>

          </div>

        </div>
      </div>
      
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <div class="form-check">
              <label>
              
              <?php echo form_checkbox( array(
                'name' => 'is_featured',
                'id' => 'is_featured',
                'value' => 'accept',
                'checked' => set_checkbox('is_featured', 1, ( @$pending->is_featured == 1 )? true: false ),
                'class' => 'form-check-input',
                'onclick' => 'return false'
              )); ?>

              <?php echo get_msg( 'is_featured' ); ?>

              </label>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <div class="form-check">
              <label>
              
              <?php echo form_checkbox( array(
                'name' => 'is_promotion',
                'id' => 'is_promotion',
                'value' => 'accept',
                'checked' => set_checkbox('is_promotion', 1, ( @$pending->is_promotion == 1 )? true: false ),
                'class' => 'form-check-input',
                'onclick' => 'return false'
              )); ?>

              <?php echo get_msg( 'is_promotion' ); ?>

              </label>
            </div>
          </div>
        </div>
      </div>
        

          <label>Schedule</label>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group" id="sample1">
            <label>
              Open time:
            </label>
            <br>
            <input id="timestart" type="text" name="opening_hour" value="<?php echo $pending->opening_hour; ?>" style="width: 50%;" pendingd/>
            <br><br>
            <label>
              Close time:
            </label>
            <br>
            <input type="text" id="time_end" name="closing_hour" value="<?php echo $pending->closing_hour; ?>"  style="width: 50%;" pendingd/>
          
          </div>

        </div>

        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('time_remark')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'time_remark',
              'value' => set_value( 'time_remark', show_data( @$pending->time_remark), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('time_remark'),
              'id' => 'time_remark',
              'rows' => "5",
              'readonly' => "true"
            )); ?>

          </div>
        </div>
      </div>

      <label>Contact</label>
      <div class="row">
        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('phone_no_1')?>
            </label>

            <?php echo form_input( array(
              'name' => 'phone1',
              'value' => set_value( 'phone1', show_data( @$pending->phone1), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('phone_no_1'),
              'id' => 'phone1',
              'readonly' => "true"
            )); ?>

          </div>
        </div>

        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('phone_no_2')?>
            </label>

            <?php echo form_input( array(
              'name' => 'phone2',
              'value' => set_value( 'phone2', show_data( @$pending->phone2), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('phone_no_2'),
              'id' => 'phone2',
              'readonly' => "true"
            )); ?>

          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('phone_no_3')?>
            </label>

            <?php echo form_input( array(
              'name' => 'phone3',
              'value' => set_value( 'phone3', show_data( @$pending->phone3), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('phone_no_3'),
              'id' => 'phone3',
              'readonly' => "true"
            )); ?>

          </div>
        </div>

        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('email')?>
            </label>

            <?php echo form_input( array(
              'name' => 'email',
              'value' => set_value( 'email', show_data( @$pending->email), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('email'),
              'id' => 'email',
              'readonly' => "true"
            )); ?>

          </div>

        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('address')?>
            </label>

            <?php echo form_input( array(
              'name' => 'address',
              'value' => set_value( 'address', show_data( @$pending->address), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('address'),
              'id' => 'address',
              'readonly' => "true"
            )); ?>

          </div>
        </div>
      </div>

      <label>Social Information</label>
      <div class="row">
        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('facebook')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'facebook',
              'value' => set_value( 'facebook', show_data( @$pending->facebook), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('facebook'),
              'id' => 'facebook',
              'rows' => "3",
              'readonly' => "true"
            )); ?>

          </div>
        </div>

        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('google_plus')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'google_plus',
              'value' => set_value( 'google_plus', show_data( @$pending->google_plus), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('google_plus'),
              'id' => 'google_plus',
              'rows' => "3",
              'readonly' => "true"
            )); ?>

          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('twitter')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'twitter',
              'value' => set_value( 'twitter', show_data( @$pending->twitter), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('twitter'),
              'id' => 'twitter',
              'rows' => "3",
              'readonly' => "true"
            )); ?>

          </div>
        </div>

        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('youtube')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'youtube',
              'value' => set_value( 'youtube', show_data( @$pending->youtube), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('youtube'),
              'id' => 'youtube',
              'rows' => "3",
              'readonly' => "true"
            )); ?>

          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('instagram')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'instagram',
              'value' => set_value( 'instagram', show_data( @$pending->instagram), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('instagram'),
              'id' => 'instagram',
              'rows' => "3",
              'readonly' => "true"
            )); ?>

          </div>
        </div>

        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('pinterest')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'pinterest',
              'value' => set_value( 'pinterest', show_data( @$pending->pinterest), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('pinterest'),
              'id' => 'pinterest',
              'rows' => "3",
              'readonly' => "true"
            )); ?>

          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('website')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'website',
              'value' => set_value( 'website', show_data( @$pending->website), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('website'),
              'id' => 'Website',
              'rows' => "3",
              'readonly' => "true"
            )); ?>

          </div>
        </div>
      </div>  

      <label>Chatting</label>

      <div class="row">
        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('whatsapp')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'whatsapp',
              'value' => set_value( 'whatsapp', show_data( @$pending->whatsapp), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('whatsapp'),
              'id' => 'whatsapp',
              'rows' => "3",
              'readonly' => "true"
            )); ?>

          </div>
        </div>

        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('messenger')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'messenger',
              'value' => set_value( 'messenger', show_data( @$pending->messenger), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('messenger'),
              'id' => get_msg('messenger'),
              'rows' => "3",
              'readonly' => "true"
            )); ?>

          </div>
        </div>
      </div>

      <label>Policy</label>

      <div class="row">
        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('terms')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'terms',
              'value' => set_value( 'terms', show_data( @$pending->terms), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('terms'),
              'id' => 'terms',
              'rows' => "3",
              'readonly' => "true"
            )); ?>

          </div>
        </div>

        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('cancelation_policy')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'cancelation_policy',
              'value' => set_value( 'cancelation_policy', show_data( @$pending->cancelation_policy), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('cancelation_policy'),
              'id' => 'cancelation_policy',
              'rows' => "3",
              'readonly' => "true"
            )); ?>

          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('additional_info')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'additional_info',
              'value' => set_value( 'additional_info', show_data( @$pending->additional_info), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('additional_info'),
              'id' => 'additional_info',
              'rows' => "3",
              'readonly' => "true"
            )); ?>

          </div>
        </div>
      </div>  


            
      <label><?php echo get_msg('item_specification')?></label>
        <!-- product specification for edit -->
      <?php if ( isset( $pending )){ ?>

        <?php 
        
          $spec_data = $this->Specification->get_all_by( array( 'item_id' => @$pending->id ))->result(); 
          $specs_count = count($spec_data);

          //counter need to plus one for edit default 
          $spec_data_count = $specs_count + 1;
        ?>

          

        <?php if ( !empty( $spec_data )): ?>

          <?php 
            $i = 0;
            foreach( $spec_data as $spec ): 
            $i++;
          ?>

          <div id="spec_data">
              <div class="col-md-6">
                <div class="form-group">
                <label>
                  <?php echo get_msg('item_title') . " : " . $i?>
                </label>
                
                <?php echo form_input( array(
                  'name' => 'prd_spec_title' . $i,
                  'value' => $spec->name,
                  'class' => 'form-control form-control-sm',
                  'id' => 'prd_spec_title' . $i,
                  'readonly' => "true"
                )); ?>
              
              </div>

              <div class="form-group">
                <label>
                  <?php echo get_msg('item_desc') . " : "  .$i?>
                </label>
                
                <?php echo form_input( array(
                  'name' => 'prd_spec_desc' . $i,
                  'value' => $spec->description,
                  'class' => 'form-control form-control-sm',
                  'id' => 'prd_spec_desc' . $i,
                  'readonly' => "true"
                )); ?>
                
              </div>
            </div>    
            </div>
            <?php endforeach; ?>

        <?php endif; ?>
        <!-- Edit Default save -->
        <div id="spec_data">
          <div class="col-md-6">
              <div class="form-group">
              <label>
                <?php echo get_msg('item_title') . " : " . $spec_data_count ?>
              </label>
            
              <?php echo form_input( array(
                'name' => 'prd_spec_title'.$spec_data_count,
                'value' => set_value( 'prd_spec_title1', show_data( @$product->prd_spec_title1), false ),
                'class' => 'form-control form-control-sm',
                'id' => 'prd_spec_title'.$spec_data_count,
                'readonly' => "true"
              )); ?>
            
            </div>

            <div class="form-group">
              <label>
                <?php echo get_msg('item_desc') . " : " . $spec_data_count ?>
              </label>
              
              <?php echo form_input( array(
                'name' => 'prd_spec_desc'.$spec_data_count,
                'value' => set_value( 'prd_spec_desc1', show_data( @$product->prd_spec_desc1), false ),
                'class' => 'form-control form-control-sm',
                'id' => 'prd_spec_desc'.$spec_data_count,
                'readonly' => "true"
              )); ?>
              
            </div>
            </div>
          </div>

      <!-- product specification for save -->
      <?php } else { ?>
          <div id="spec_data">
            <div class="col-md-6">
            <div class="form-group">
              <label>
                <?php echo get_msg('item_title') . " : 1"?>
              </label>
              
              <?php echo form_input( array(
                'name' => 'prd_spec_title1',
                'value' => set_value( 'prd_spec_title1', show_data( @$product->prd_spec_title1), false ),
                'class' => 'form-control form-control-sm',
                'id' => 'prd_spec_title1',
                'readonly' => "true"
              )); ?>
              
            </div>

            <div class="form-group">
              <label>
                <?php echo get_msg('item_desc') . " : 1"?>
              </label>

              <div class="form-group">
                <?php echo form_input( array(
                  'name' => 'prd_spec_desc1',
                  'value' => set_value( 'prd_spec_desc1', show_data( @$product->prd_spec_desc1), false ),
                  'class' => 'form-control form-control-sm',
                  'id' => 'prd_spec_desc1',
                  'readonly' => "true"
                )); ?>
              </div>
            </div>
          </div>
          </div>
        <?php } ?>

    <?php 
      if (isset($spec_data)) {
        $specs_count = $specs_count;
      } else {
        $specs_count = 0;
      } 
    ?>
    <input type="hidden" id="spec_total_existing" name="spec_total_existing" value="<?php echo $specs_count; ?>">
    <?php 
      if ( isset( $pending )) { 
    ?>
      <input type="hidden" id="edit_product" name="edit_product" value="1">
    <?php   
      } else {
    ?>
      <input type="hidden" id="edit_product" name="edit_product" value="0">
    <?php } ?> 


    <hr>
      <div class="form-group" style="background-color: #edbbbb; padding: 20px;">
        <label>
          <strong><?php echo get_msg('select_status')?></strong>
        </label>

        <select id="item_status_id" name="item_status_id" class="form-control">
           <option value="1">Approved</option>
           <option value="2">Disable</option>
           <option value="3">Reject</option>
        </select>
       
      </div>
    </div>

    <!-- Grid row --> 
      <?php if ( isset( $pending )): ?>
        <div class="gallery" id="gallery" style="margin-left: 15px; margin-bottom: 15px;">
          <?php
              $conds = array( 'img_type' => 'item', 'img_parent_id' => $pending->id );
              $images = $this->Image->get_all_by( $conds )->result();
          ?>
          <?php $i = 0; foreach ( $images as $img ) :?>
            <!-- Grid column -->
            <div class="mb-3 pics animation all 2">
              <a href="#<?php echo $i;?>"><img class="img-fluid" src="<?php echo img_url('/' . $img->img_path); ?>" alt="Card image cap" width= "30%" height="20%"></a>
            </div>
            <!-- Grid column -->
          <?php $i++; endforeach; ?>
          
        </div>
        <!-- Grid row -->
      <?php endif; ?>

    <div class="card-footer">
        <button type="submit" name="submit" value="submit" class="btn btn-sm btn-primary">
          <?php echo get_msg('btn_save')?>
        </button>

        <a href="<?php echo $module_site_url; ?>" class="btn btn-sm btn-primary">
          <?php echo get_msg('btn_cancel')?>
        </a>
    </div>

  </div>
</section>
<?php echo form_close(); ?>


         <!-- pending map -->

         <script>

<?php
    if (isset($pending)) {
        $lat = $pending->lat;
        $lng = $pending->lng;
?>
        var pending_map = L.map('pending_map').setView([<?php echo $lat;?>, <?php echo $lng;?>], 5);
<?php
    } else {
?>
        var pending_map = L.map('pending_map').setView([0, 0], 5);
<?php
    }
?>

const pending_attribution =
'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
const pending_tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
const pending_tiles = L.tileLayer(pending_tileUrl, { pending_attribution });
pending_tiles.addTo(pending_map);
<?php if(isset($pending)) {?>
    var pending_marker = new L.Marker(new L.LatLng(<?php echo $lat;?>, <?php echo $lng;?>));
    pending_map.addLayer(pending_marker);
    // results = L.marker([<?php echo $lat;?>, <?php echo $lng;?>]).addTo(mymap);

<?php } else { ?>
    var pending_marker = new L.Marker(new L.LatLng(0, 0));
    //mymap.addLayer(marker2);
<?php } ?>
var results = L.layerGroup().addTo(pending_map);

var popup = L.popup();

function onMapClick(e) {

    var pending = e.latlng.toString();
    var pending_res = pending.substring(pending.indexOf("(") + 1, pending.indexOf(")"));
    pending_map.removeLayer(pending_marker);
    results.clearLayers();
    results.addLayer(L.marker(e.latlng));   

    var pending_tmpArr = new Array();
    pending_tmpArr = pending_res.split(",");

    document.getElementById("lat").value = pending_tmpArr[0].toString(); 
    document.getElementById("lng").value = pending_tmpArr[1].toString();
}

pending_map.on('click', onMapClick);
</script>
