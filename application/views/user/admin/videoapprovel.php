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
                </div>
            </div>
            <div class="row">
                <ul class="nav responsive-tab nav-material nav-material-white">
                    <li>
                        <a class="nav-link " href="<?=base_url('/index.php/user/allproduct')?>"><i class="icon icon-list"></i>All Products</a>
                    </li>
                    <li>
                       <a class="nav-link" href="<?=base_url('/index.php/user/explorproduct')?>"><i
                                class="icon icon-plus-circle"></i>add explore product<span class="badge badge-danger badge-mini rounded-circle" id="notifExplore" style="margin-top: -10px;"></span></a>
                    </li>
                    <li>
                        <a class="nav-link" href="<?=base_url('/index.php/user/videoapprovel')?>"><i class="icon icon-insert-can"></i>videos for approval<span class="badge badge-danger badge-mini rounded-circle" id="notifvedio" style="margin-top: -10px;"></a>
                    </li>
                </ul>
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
                                            <th>Vedio</th>
                                            <th></th>
                                            
                                            
                                            <th></th>
                                        </tr>
                                        </thead>
                                    <tbody>
                                        <?php if(sizeof($allproduct)>0){ for($i=0;$i<sizeof($allproduct);$i++){?>

                                    <tr >
                                        <td class="w-10">
                                            <img src="<?=base_url($allproduct[$i]->img)?>" alt="">
                                        </td>
                                        <td>
                                            
                                            <div  class="text-muted"><label>Product: </label><?=$allproduct[$i]->productName?></div><h6><label>Brand:</label><?=$allproduct[$i]->brandName?></h6>
                                        </td>
                                        <td><?=$allproduct[$i]->barcodeSrNo?></td>
                                        <td><button onclick="showmedia(<?=$allproduct[$i]->productId ?>)">media</button></td>
                                        
                                        <td>
                                                <a href="javascript:productDetail(<?=$allproduct[$i]->productId ?>)"><i class="icon-eye mr-3" ></i></a>
                                              
                                            </td>
                                            
                                        
                                    </tr>
                                   <?php } } else{ echo "<tr class='no-b'><td><h3>No Data</h3></td></tr>" ;} ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
        </div>

    </div>
    
            
</div>



   



<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>