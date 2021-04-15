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
                        
                       <?php echo $name; ?>
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
                               <table class="table table-hover ">
                                    <tbody>
                                        <?php if($product!=="NO Product Add"){
                                            
                                        for($i=0;$i<sizeof($product);$i++){?>

                                    <tr class="no-b">
                                        <td class="w-10">
                                            <img src="<?=base_url($product[$i]->img)?>" alt="">
                                        </td>
                                        <td>
                                            
                                            <small class="text-muted"><?=$product[$i]->productName?></small><h6><?=$product[$i]->brandName?></h6>
                                        </td>
                                        <td><?=$product[$i]->barcodeSrNo?></td>
                                       <!-- <td> <a href="javascript:patientproductDetail(<?=$product[$i]->id ?>)"><i class="icon-eye mr-3" ></i></a>
                                        </td> -->
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
<!-- Modal -->
<div id="productdetail" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background: #5ecaca;">
        <h3 style="color:white;"> Product Instructions</h3>
        
      </div>
      <div class="modal-body ">
        <div id="tablebody">
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<script type="text/javascript">
  function patientproductDetail(id){
    $("#tablebody").html(" ");
     jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "index.php/user/productDetail",
                dataType: 'json',
                data: {id: id, usertype:"patient"},
                success: function(res) {
                    
                      //alert(res[0]['productId']);
                        console.log(res);
                 $("#tablebody").append(''
                 +' <div class="row"> '
                 +'<div class="col-md-4 col-xs-4"></div>'
        +' <div class="col-md-4 col-xs-4"><span class="bmw">Manufacture Date: '+res[0]['manufactureDate']+'</span></div>'
         +'<div class="col-md-4 col-xs-4"><span class="bmw">Expiry Date:  '+res[0]['expiryDate']+'</span></div>'
         +'</div>');
                  $('#productdetail').modal({show:true});
                    
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });
      //jQuery.('#myModal').modal('show');
          // jQuery.('#myModal').modal('show');



  }


</script>>

 
<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>
  
