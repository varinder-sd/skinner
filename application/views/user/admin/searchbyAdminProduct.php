<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>

<div class="page has-sidebar-left">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon-package"></i>
                        Admin Products
                    </h4>
                   <span style="float:right;"><form method="post" action="<? echo base_url('/index.php/user/searchbyAdminProduct')?>"><input type="text" name="search"><button type="submit">Search</button><a href="<?=base_url('/index.php/user/adminProduct')?>" style="margin-left: 14px;">All</a></span>
                </div>
            </div>
            
            
        </div>
    </header>
     <?php if($allproduct==1){
      echo "<h3>No Data Match !</h3>";
        }else{ ?>
    <div class="container-fluid animatedParent animateOnce my-3">
        <div class="animated fadeInUpShort">
            <div class="row">
                <div class="col-md-12">
                    <div class="card no-b shadow">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <tbody>
                                        <?php for($i=0;$i<sizeof($allproduct);$i++): ?>

                                    <tr class="no-b">
                                        <td class="w-10">
                                            <img src="<?=base_url($allproduct[$i]->img)?>" alt="">
                                        </td>
                                        <td>
                                            
                                            <small class="text-muted"><?=$allproduct[$i]->productName?></small><h6><?=$allproduct[$i]->brandName?></h6>
                                        </td>
                                        <td><?=$allproduct[$i]->barcodeSrNo?></td>
                                        
                                        <td><a href="javascript:showmediafromadmin(<?=$allproduct[$i]->productId ?>)"  class="skinnerapp_product"><span class=""><i class="fa fa-play-circle"></i></span></a></td> 
                                      
                                        
                                        
                                        <td>
                                                <a href="javascript:adminProductDetail(<?=$allproduct[$i]->productId ?>)"><i class="icon-eye mr-3" ></i></a>
                                              
                                            </td>
                                            <td>
                                                <a href="javascript:productEditForm(<?=$allproduct[$i]->productId ?>,'admin')"><i class="icon-pencil"></i></a>
                                              
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
         <?php }  ?>      
</div>





<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>