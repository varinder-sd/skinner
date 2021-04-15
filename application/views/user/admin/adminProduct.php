<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>

<div class="page has-sidebar-left">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4 style="float:left;">
                        <i class="icon-package"></i>
                        Admin Products
                    </h4>
                    <form method="post" action="<? echo base_url('/index.php/user/searchbyAdminProduct')?>"><span style="float:right;" class="skinnerapp_search"><input type="text" name="search" placeholder="Products"><button type="submit" >Search</button></span></form>
                </div>
            </div>
            
            
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce my-3">
       

        <div class="animated fadeInUpShort">
            <div class="row">
                <div class="col-md-12">
                    <div class="card no-b shadow">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover allproduct_img">
								<thead>
                                        <tr class="no-b">
                                             <th>PRODUCT </th>
                                            <th></th>
                                            <th>BARCODE</th>
                                            <th>VIDEO</th>
                                            <th>VIEW</th>
                                            <th>EDIT</th>
                                            
											
                                            <th></th>
                                        </tr>
                                        </thead>
                                    <tbody>
                                        <?php for($i=0;$i<sizeof($allproduct);$i++): ?>

                                    <tr>
                                        <td class="w-10">
                                            <img src="<?=base_url($allproduct[$i]->img)?>" alt="">
                                        </td>
                                        <td>
                                            
                                            <div class="text-muted"><label>Name: </label><?=$allproduct[$i]->productName?></small><h6><label>brand Name:</label><?=$allproduct[$i]->brandName?></h6>
                                        </td>
                                        <td><?=$allproduct[$i]->barcodeSrNo?></td>
										
										<td><?
                                        //echo $allproduct[$i]->productId;
                                        if($allproduct[$i]->media==null){
                                                
                                          ?>  <button onclick="showmediafromadmin(<?=$allproduct[$i]->productId ?>)"  class="skinnerapp_product">Upload media</button>
                                       <? }else{


                                            ?>
										<button onclick="showmediafromadmin(<?=$allproduct[$i]->productId ?>)"  class="skinnerapp_product"><span class="play_media_link"><i class="fa fa-play-circle"></i></span></button>
										<?}?>
										</td>
										
                                       
                                        
                                        <td>
                                                <a title="View" href="javascript:adminProductDetail(<?=$allproduct[$i]->productId ?>)"><i class="icon-eye mr-3" ></i></a>
                                              
                                            </td>
                                            <td>
                                                <a title="Edit" href="javascript:productEditForm(<?=$allproduct[$i]->productId ?>,'admin')"><i class="icon-pencil"></i></a>
                                              
                                            </td>
                                           
                                        
                                    </tr>
                                   <?php endfor;?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
        </div>

    </div>
    <div class="pagination-link"> <a  href="#"><?php echo $this->pagination->create_links();?></a></div>
         
</div>





<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>