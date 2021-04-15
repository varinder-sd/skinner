
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>

<div class="page has-sidebar-left">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h3>
                        
                      Add Product
                    </h3>
                </div>
            </div>
            
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce my-3 add_product">
        <div class="animated fadeInUpShort">
            <div class="row">
                <div class="col-md-12">
                    <div class="card no-b shadow">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                               <form class="form-horizontal" method="post" action="<?php echo base_url('index.php/user/addbarcode'); ?>" enctype="multipart/form-data">
											<fieldset>


                     <?php if(isset($error)){

                        print_r($error);
                         }?> 
											<!-- Text input-->
											<div class="form-group">
											  <label class=" control-label" for="product_id">Barcode Srno.</label>  
											  <div class="add_prdct">

											  <input id="product_id" name="barcodesrno" placeholder="Barcode Srno." class="form-control input-md" required="" type="text">
											    
											  </div>
											</div>

											<!-- Text input-->
											<div class="form-group">
											  <label class=" control-label" for="product_name">Product Name</label>  
											  <div class="add_prdct">
                          
											  <input id="product" name="productName" placeholder="Product Name" class="form-control input-md" required="" type="text">
											    
											  </div>
											</div>

											<!-- Text input-->
											<div class="form-group ">
											  <label class=" control-label" for="product_name_fr">Brand Name</label>  
											  <div class="add_prdct">
                          
											  <input id="brand" name="brandName" placeholder="Brand Name" class="form-control input-md" required="" type="text">
											    
											  </div>
											</div>

											<!-- Select Basic -->
											

											
											    
											 <!-- File Button --> 
											<div class="form-group col-md-4">
											  <label class=" control-label" for="filebutton">main_image</label>
											  <div class="">
                          <div id="forshow"></div>
                        <div id="forhide">
											    <input  name="file" class="input-file" type="file" accept="image/*">
                        </div>
											  </div>
											</div>
											

											<!-- Button -->
											<div class="form-group">
											  
											  <div class="col-md-4">
											    <button type="submit" class="btn btn-primary" value="submit" name="submit">next</button>
											  </div>
											  </div>

											</fieldset>
											</form>
                                
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
<script type="text/javascript">

	
    $('#product_id').on('blur', function() {
                  
                   $("#forshow").html(' ');
         jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "index.php/user/searchproduct",
                dataType: 'json',
                data: {barcode: $('#product_id').val()},
                success: function(res) {
                    
                     // alert(res[0]['barcodeId']);
                        console.log(res);
                   if(res=='no data') {
                    $("#product").val(' ');
                    $("#brand").val(' '); 
                    $("#forhide").show();
                  $("#forshow").html(' ');
                  $("#product").removeAttr('disabled');
                 $("#brand").removeAttr('disabled');
                 } else{   
                 $("#product").val(res[0]['productName']);
                 $("#brand").val(res[0]['brandName']);
                  $("#forhide").hide();
                  $("#forshow").append("<img src='<?=base_url()?>"+res[0]['img']+"' style='height:164px;width:164px'>");
                 $("#product").prop('disabled', true);
                 $("#brand").prop('disabled', true);
                  }  
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });
    });

</script>

