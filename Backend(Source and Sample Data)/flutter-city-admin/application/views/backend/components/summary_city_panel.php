<div class="container-fluid">
    <div class="row">
        <?php $count = $this->uri->segment(4) or $count = 0; ?>
        <?php if ( !empty( $cities ) && count( $cities->result()) > 0 ): ?>
        <?php 
            foreach ($cities->result() as $city) :
        ?>
        <div class="col-xs-12 col-md-4 full-card" style="padding-top: 20px;">
            <div class="card">
                  <div class="img-responsive img-portfolio img-hover" style="justify-content: center;padding: 10px 10px 20px 10px">
                        <a href="<?php echo site_url('admin/dashboard/index/'. $city->id);?>">
                            <?php $default_photo = get_default_photo( $city->id, 'city' ); ?>
                            <img alt="image" class="img-responsive img-portfolio img-hover" src="<?php echo img_url( '/'. $default_photo->img_path ); ?>" style="max-height: 100%;max-width: 100%;">  

                        </a>
                    </div>
                <?php if($city->is_featured == 1) { ?>
                    <div class="card-img-overlay">
                        <h3 class="card-title m-b-0 dl">Featured</h3>
                    </div>
                <?php } ?>
                    <div class="card-header">
                        <h4> 
                            <a href="<?php echo site_url('admin/dashboard/index/'. $city->id);?>"><?php echo $city->name; ?></a>
                        </h4>
                        <div class="row">
                            <div class="col-md-4 border-right">
                                <div class="description-block">
                                    <p><span data-toggle="tooltip" class="badge badge-warning" style="font-size: 12px;">
                                        <?php 
                                            $conds['no_publish_filter'] = 1;
                                            $conds['city_id'] = $city->id;
                                            echo $this->Category->count_all_by( $conds ) . " "; 
                                        ?>
                                    </span>
                                    Categories</p>
                                </div>
                              <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-md-4 border-right">
                              <div class="description-block">
                                <p><span data-toggle="tooltip" class="badge badge-warning" style="font-size: 12px;">
                                    <?php 
                                        $conds['no_publish_filter'] = 1;
                                        $conds['city_id'] = $city->id;
                                        echo $this->Subcategory->count_all_by( $conds ) . " "; 
                                    ?>
                                </span>
                                Subcategories</p>
                              </div>
                              <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-md-4">
                              <div class="description-block">
                                <p><span data-toggle="tooltip" class="badge badge-warning" style="font-size: 14px;">
                                    <?php 
                                        $conds['no_publish_filter'] = 1;
                                        $conds['city_id'] = $city->id;
                                        echo $this->Item->count_all_by( $conds ) . " "; 
                                    ?>
                                </span>
                                Items</p>
                              </div>
                              <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <p>
                            <?php 
                                                
                                $cityDesc = strip_tags($city->description);
                                
                                if (strlen($cityDesc) > 200) {
                                
                                    $stringCut = substr($cityDesc, 0, 200);
                        
                                    $cityDesc = substr($stringCut, 0, strrpos($stringCut, ' ')).'...'; 
                                }
                                
                                echo $cityDesc;
                                
                            ?>
                        </p>
                        <div class="venue">
                            <span>
                                <i class="fa fa-map-marker"></i>
                            </span><?php echo $city->address; ?>
                        </div>
                    </div>
                    <div class="card-footer" style="padding: 10px 10px 10px 80px;">
                        <a href="<?php echo site_url('admin/dashboard/index/'. $city->id);?>" class="btn btn-primary" style="width: 25%;">Dashboard</a>
                        <a href="<?php echo site_url('admin/cities/edit/'. $city->id);?>" style="margin-left:10px;width: 25%;" class="btn btn-primary"><?php echo get_msg('btn_edit'); ?></a>
                        <input type="submit" value="<?php echo get_msg('btn_delete')?>" class="btn btn-primary delete-city" id="<?php echo $city->id; ?>" style="margin-left: 10px;width: 25%;" data-toggle="modal" data-target="#deletecity"/>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif ?>
        </div>
    </div>
</div>


<div class="modal fade"  id="deletecity">
        
    <div class="modal-dialog">
        
        <div class="modal-content">
        
            <div class="modal-header">
                <h4 class="modal-title"><?php echo get_msg('delete_city_label')?></h4>

                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>

            </div>

            <div class="modal-body">
                <p><?php echo get_msg('delete_city_confirm_message')?></p>
                <p>1. Categories</p>
                <p>2. Sub-Categories</p>
                <p>3. Items and Collection</p>
                <p>4. News Feeds</p>
                <p>5. Watch List</p>
                <p>6. Comments</p>
                <p>7. Contact Us Message</p>
                <p>8. Transactions</p>
                <p>9. Reports</p>
                <p>10. User city</p>
                <p>11. Likes</p>
                <p>12. Favourites</p>
            </div>

            <div class="modal-footer">
                <a type="button" class="btn btn-default btn-delete-city">Yes</a>
                <a type="button" class="btn btn-default" data-dismiss="modal">Cancel</a>
            </div>

        </div>
    
    </div>          
        
</div>

<script>
    $('.delete-city').click(function(e){
        e.preventDefault();
        var id = $(this).attr('id');
        var image = $(this).attr('image');
        var action = '<?php echo site_url('/admin/cities/delete/');?>';
        $('.btn-delete-city').attr('href', action + id);
    });
</script>

