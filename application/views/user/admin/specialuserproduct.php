<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>
<style type="text/css">
  .mybuttom{

        background-color: #f0f5f9;
    padding: 7px;
    border-radius: 52px;
  }
  .myradiobutton{

        color: white;
    border: solid 1px #03a9f4;
    border-radius: 19px;
  }
  span.bmw {
    margin: 6px;
}
</style>
<div class="page has-sidebar-left">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h3>
                        
                       <?php echo "Product of ". $name; ?>
                    </h3>
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
                                            <th>VIEW</th>
                                            
                                        
                                        </tr>
                                        </thead>
                                    <tbody>
                                        <?php  if($product!=="NO Product Add"){
                                            
                                        for($i=0;$i<sizeof($product);$i++){ ?>

                                    <tr class="no-b">
                                        <td class="w-10">
                                            <img src="<?=$product[$i]->img?>" alt="">
                                        </td>
                                        <td>
                                            
                                            <div class="text-muted"><label>Name: </label><?=$product[$i]->productName?></small><h6><label>brand Name: </label><?=$product[$i]->brandName?></h6>
                                        </td>
                                        <td><?=$product[$i]->barcodeSrNo?></td>
                                       <td> <a href="javascript:productDetail(<?=$product[$i]->productId ?>)"><i class="icon-eye mr-3" ></i></a>
                                        </td>
                                    </tr>



  
                                   <?php }}else{?>
                                    <tr><td><?php echo $product ;?></td></tr>
                                   <?php }?>
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
  
