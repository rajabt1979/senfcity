<?php
	$attributes = array( 'id' => 'history-form', 'enctype' => 'multipart/form-data');
	echo form_open( '', $attributes);
?>
	
<section class="content animated fadeInRight">
	<div class="card card-info">
	    <div class="card-header">
	        <h3 class="card-title"><?php echo get_msg('trans_history_info')?></h3>
	    </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
            	<div class="col-md-6">
            		
            		<div class="form-group">
                   		<label>
                   			<span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('start_date_label')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('start_date_label')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<?php echo form_input( array(
							'name' => 'start_date',
							'value' => set_value( 'start_date', show_data( @$trans->start_date ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'start_date_label' ),
							'id' => 'start_date',
							'readonly' => 'true'
						)); ?>
              		</div>

              		<div class="form-group">
                   		<label>
                   			<span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('amount_label')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('amount_label')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<?php echo form_input( array(
							'name' => 'amount',
							'value' => set_value( 'amount', show_data( @$trans->amount ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'amount_label' ),
							'id' => 'amount',
							'readonly' => 'true'
						)); ?>
              		</div>

            	</div>

            	<div class="col-md-6">

            		<div class="form-group">
                   		<label>
                   			<span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('end_date_label')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('end_date_label')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<?php echo form_input( array(
							'name' => 'end_date',
							'value' => set_value( 'end_date', show_data( @$trans->end_date ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'end_date_label' ),
							'id' => 'end_date',
							'readonly' => 'true'
						)); ?>
              		</div>

              		<div class="form-group">
                   		<label>
                   			<span style="font-size: 17px; color: red;">*</span>
							<?php echo get_msg('payment_method_label')?>
							<a href="#" class="tooltip-ps" data-toggle="tooltip" title="<?php echo get_msg('payment_method_label')?>">
								<span class='glyphicon glyphicon-info-sign menu-icon'>
							</a>
						</label>

						<?php echo form_input( array(
							'name' => 'payment_method',
							'value' => set_value( 'payment_method', show_data( @$trans->payment_method ), false ),
							'class' => 'form-control form-control-sm',
							'placeholder' => get_msg( 'payment_method_label' ),
							'id' => 'payment_method',
							'readonly' => 'true'
						)); ?>
              		</div>
            	</div>
            </div>
        </div>

        <hr>
        <div class="card-header">
	    	<h3 class="card-title"><?php echo get_msg('prd_info')?></h3>
	  	</div>

	  	<div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('item_name')?>
            </label>

            <?php echo form_input( array(
              'name' => 'name',
              'value' => set_value( 'name', show_data( @$item->name), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('item_name'),
              'id' => 'name',
              'readonly' => 'true'
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
                set_value( 'cat_id', show_data( @$item->cat_id), false ),
                'class="form-control form-control-sm mr-3" disabled="disabled" id="cat_id"'
              );
            ?>
          </div>

          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('Prd_search_subcat')?>
            </label>

            <?php
              if(isset($item)) {
                $options=array();
                $options[0]=get_msg('Prd_search_subcat');
                $conds['cat_id'] = $item->cat_id;
                $sub_cat = $this->Subcategory->get_all_by($conds);
                foreach($sub_cat->result() as $subcat) {
                  $options[$subcat->id]=$subcat->name;
                }
                echo form_dropdown(
                  'sub_cat_id',
                  $options,
                  set_value( 'sub_cat_id', show_data( @$item->sub_cat_id), false ),
                  'class="form-control form-control-sm mr-3" disabled="disabled" id="sub_cat_id"'
                );

              } else {
                $conds['cat_id'] = $selected_cat_id;
                $options=array();
                $options[0]=get_msg('Prd_search_subcat');

                echo form_dropdown(
                  'sub_cat_id',
                  $options,
                  set_value( 'sub_cat_id', show_data( @$item->sub_cat_id), false ),
                  'class="form-control form-control-sm mr-3" disabled="disabled" id="sub_cat_id"'
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
              'value' => set_value( 'info', show_data( @$item->highlight_information), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('prd_high_info'),
              'id' => 'info',
              'rows' => "3",
              'readonly' => 'true'
            )); ?>

          </div>

          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('prd_desc')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'description',
              'value' => set_value( 'desc', show_data( @$item->description), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('prd_desc'),
              'id' => 'desc',
              'rows' => "5",
              'readonly' => 'true'
            )); ?>

          </div>

          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('Prd_select_status')?>
            </label>

            <?php
              $options=array();
              $options[0]=get_msg('Prd_select_status');
              $status = $this->Itemstatus->get_all( );
              foreach($status->result() as $stu) {
                  $options[$stu->id]=$stu->title;
              }

              echo form_dropdown(
                'item_status_id',
                $options,
                set_value( 'item_status_id', show_data( @$item->item_status_id), false ),
                'class="form-control form-control-sm mr-3" disabled="disabled"
 id="item_status_id"'
              );
            ?>
          </div>

          

          

          

          <!-- <div class="form-group">

            <label>
              Close time:
            </label>
            <br>
            <input type="text" id="time_end" name="closing_hour" value="<?php echo $item->closing_hour; ?>"  style="width: 50%;"/>
          </div> -->

              </div>

          <div class="col-md-6">
                
          <div id="transcation_map" style="width: 100%; height: 300px; pointer-events: none;"></div>

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
                      'value' => set_value( 'lat', show_data( @$item->lat ), false ),
                      'readonly' => 'true'
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
                      'value' => set_value( 'lat', show_data( @$item->lng ), false ),
                      'readonly' => 'true'
                    ));
                  ?>
                </div>
            </div>

          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('search_tag')?>
            </label>

            <?php echo form_input( array(
              'name' => 'search_tag',
              'value' => set_value( 'search_tag', show_data( @$item->search_tag), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('search_tag'),
              'id' => 'search_tag',
              'readonly' => 'true'
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
                'checked' => set_checkbox('is_featured', 1, ( @$item->is_featured == 1 )? true: false ),
                'class' => 'form-check-input',
                'readonly' => 'true'
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
                'checked' => set_checkbox('is_promotion', 1, ( @$item->is_promotion == 1 )? true: false ),
                'class' => 'form-check-input',
                'readonly' => 'true'
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
            <input id="timestart" type="text" name="opening_hour" disabled="disabled" value="<?php echo $item->opening_hour; ?>" style="width: 50%;" />
            <br><br>
            <label>
              Close time:
            </label>
            <br>
            <input type="text" id="time_end" name="closing_hour" disabled="disabled" value="<?php echo $item->closing_hour; ?>"  style="width: 50%;"/>
          
          </div>

        </div>

        <div class="col-md-6">
          
          <div class="form-group">
            <label> <span style="font-size: 17px; color: red;">*</span>
              <?php echo get_msg('time_remark')?>
            </label>

            <?php echo form_textarea( array(
              'name' => 'time_remark',
              'value' => set_value( 'time_remark', show_data( @$item->time_remark), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('time_remark'),
              'id' => 'time_remark',
              'rows' => "5",
              'readonly' => 'true'
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
              'value' => set_value( 'phone1', show_data( @$item->phone1), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('phone_no_1'),
              'id' => 'phone1',
              'readonly' => 'true'
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
              'value' => set_value( 'phone2', show_data( @$item->phone2), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('phone_no_2'),
              'id' => 'phone2',
              'readonly' => 'true'
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
              'value' => set_value( 'phone3', show_data( @$item->phone3), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('phone_no_3'),
              'id' => 'phone3',
              'readonly' => 'true'
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
              'value' => set_value( 'email', show_data( @$item->email), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('email'),
              'id' => 'email',
              'readonly' => 'true'
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
              'value' => set_value( 'address', show_data( @$item->address), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('address'),
              'id' => 'address',
              'readonly' => 'true'
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
              'value' => set_value( 'facebook', show_data( @$item->facebook), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('facebook'),
              'id' => 'facebook',
              'rows' => "3",
              'readonly' => 'true'
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
              'value' => set_value( 'google_plus', show_data( @$item->google_plus), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('google_plus'),
              'id' => 'google_plus',
              'rows' => "3",
              'readonly' => 'true'
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
              'value' => set_value( 'twitter', show_data( @$item->twitter), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('twitter'),
              'id' => 'twitter',
              'rows' => "3",
              'readonly' => 'true'
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
              'value' => set_value( 'youtube', show_data( @$item->youtube), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('youtube'),
              'id' => 'youtube',
              'rows' => "3",
              'readonly' => 'true'
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
              'value' => set_value( 'instagram', show_data( @$item->instagram), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('instagram'),
              'id' => 'instagram',
              'rows' => "3",
              'readonly' => 'true'
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
              'value' => set_value( 'pinterest', show_data( @$item->pinterest), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('pinterest'),
              'id' => 'pinterest',
              'rows' => "3",
              'readonly' => 'true'
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
              'value' => set_value( 'website', show_data( @$item->website), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('website'),
              'id' => 'Website',
              'rows' => "3",
              'readonly' => 'true'
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
              'value' => set_value( 'whatsapp', show_data( @$item->whatsapp), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('whatsapp'),
              'id' => 'whatsapp',
              'rows' => "3",
              'readonly' => 'true'
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
              'value' => set_value( 'messenger', show_data( @$item->messenger), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('messenger'),
              'id' => 'messenger',
              'rows' => "3",
              'readonly' => 'true'
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
              'value' => set_value( 'terms', show_data( @$item->terms), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('terms'),
              'id' => 'terms',
              'rows' => "3",
              'readonly' => 'true'
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
              'value' => set_value( 'cancelation_policy', show_data( @$item->cancelation_policy), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('cancelation_policy'),
              'id' => 'cancelation_policy',
              'rows' => "3",
              'readonly' => 'true'
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
              'value' => set_value( 'additional_info', show_data( @$item->additional_info), false ),
              'class' => 'form-control form-control-sm',
              'placeholder' => get_msg('additional_info'),
              'id' => 'additional_info',
              'rows' => "3",
              'readonly' => 'true'
            )); ?>

          </div>
        </div>
      </div>  


            
      <label><?php echo get_msg('item_specification')?></label>
        <!-- product specification for edit -->
      <?php if ( isset( $item )){ ?>

        <?php 

          $spec_data = $this->Specification->get_all_by( array( 'item_id' => @$item->id ))->result(); 
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
                  'readonly' => 'true'
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
                  'readonly' => 'true'
                )); ?>
                
              </div>
            </div>    
            </div>
            <?php endforeach; ?>

        <?php endif; ?>
        <!-- Edit Default save -->
        <div id="spec_data1">
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
                'readonly' => 'true'
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
                'readonly' => 'true'
              )); ?>
              
            </div>
            </div>
          </div>

      <!-- product specification for save -->
      <?php } else { ?>
          <div id="spec_data1">
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
                'readonly' => 'true'
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
                  'readonly' => 'true'
                )); ?>
              </div>
            </div>
          </div>
          </div>
        <?php } ?>
          <div class="col-6">
          <div class="form-group">
            <div class="mt-2">
              <a id="addspec" class="pull-right">
                <i class="fa fa-plus" aria-hidden="true"></i>
              <?php echo get_msg('add_more_spec'); ?>
              </a>
            </div>
          </div>
        </div>
    </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-sm btn-primary">
				<?php echo get_msg('btn_save')?>
			</button>

			<a href="<?php echo $module_site_url; ?>" class="btn btn-sm btn-primary">
				<?php echo get_msg('btn_cancel')?>
			</a>
        </div>
       
    </div>
    <!-- card info -->
</section>
				
<?php echo form_close(); ?>


<!-- transcation item map-->
<script>

<?php
    if (isset($transcation)) {
        $lat = $transcation->lat;
        $lng = $transcation->lng;
?>
        var transcation_map = L.map('transcation_map').setView([<?php echo $lat;?>, <?php echo $lng;?>], 5);
<?php
    } else {
?>
        var transcation_map = L.map('transcation_map').setView([0, 0], 5);
<?php
    }
?>

const transcation_attribution =
'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
const transcation_tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
const transcation_tiles = L.tileLayer(transcation_tileUrl, { transcation_attribution });
transcation_tiles.addTo(transcation_map);
<?php if(isset($transcation)) {?>
    var transcation_marker = new L.Marker(new L.LatLng(<?php echo $lat;?>, <?php echo $lng;?>));
    transcation_map.addLayer(transcation_marker);
    // results = L.marker([<?php echo $lat;?>, <?php echo $lng;?>]).addTo(mymap);

<?php } else { ?>
    var transcation_marker = new L.Marker(new L.LatLng(0, 0));
    //mymap.addLayer(marker2);
<?php } ?>
var results = L.layerGroup().addTo(transcation_map);

var popup = L.popup();

function onMapClick(e) {

    var transcation = e.latlng.toString();
    var popularitem_res = transcation.substring(transcation.indexOf("(") + 1, transcation.indexOf(")"));
    transcation_map.removeLayer(transcation_marker);
    results.clearLayers();
    results.addLayer(L.marker(e.latlng));   

    var transcation_tmpArr = new Array();
    transcation_tmpArr = popularitem_res.split(",");

    document.getElementById("lat").value = transcation_tmpArr[0].toString(); 
    document.getElementById("lng").value = transcation_tmpArr[1].toString();
}

transcation_map.on('click', onMapClick);
</script>
