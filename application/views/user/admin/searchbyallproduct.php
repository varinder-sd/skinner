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
                        All Products
                    </h4>
                    <span style="float:right;"><form method="post" action="<? echo base_url('/index.php/user/searchbyallproduct')?>"><input type="text" name="search"><button type="submit">Search</button><a href="<?=base_url('/index.php/user/allproduct')?>" style="margin-left: 14px;">back</a></span>
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
                                            <img src="<?=$allproduct[$i]->img?>" alt="">
                                        </td>
                                        <td>
                                            
                                            <small class="text-muted"><?=$allproduct[$i]->productName?></small><h6><?=$allproduct[$i]->brandName?></h6>
                                        </td>
                                        <td><?=$allproduct[$i]->barcodeSrNo?></td>
                                        <td><button onclick="showmedia(<?=$allproduct[$i]->productId ?>)">media</button></td>
                                        
                                        <td>
                                                <a href="javascript:productDetail(<?=$allproduct[$i]->productId ?>)"><i class="icon-eye mr-3" ></i></a>
                                              
                                            </td>
                                            <td>
                                          <?php if($allproduct[$i]->addToExplore==0){?>

                                          <button onclick="productEditForm(<?=$allproduct[$i]->productId ?>,'special')" >Add To Explore</button>
                                        <?php }else{
                                          echo "<h5>Product is added</h5>"; }?>

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