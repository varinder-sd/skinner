

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>
        
<style type="text/css">
    .fa-camera_span i {
    font-size: 13px;
    color: #ffffff;
    margin-right: 4px;
    background: transparent;
    padding: 2px 9px;
    margin-bottom: 5px;
}
.barcode_div11 {
    max-width: 580px;
}
</style>
<div class="page has-sidebar-left">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon-package"></i>
                        Product List
                    </h4>
                   
                     <span style="float:right;" class="skinnerapp_search"><a href="<?=base_url('index.php/user/disapproved')?>">Show All</a><input type="text" name="search" placeholder="Products" class="myTextInput"><button id="target">GO</button></span>
                </div>
            </div>

            <div class="row">
                <ul class="nav responsive-tab nav-material nav-material-white">
                    <li>
                        <a class="nav-link " href="<?=base_url('index.php/user/productForApprovel')?>">Pandding</a>
                    </li>
                    <li>
                        <a class="nav-link" href="<?=base_url('index.php/user/approved')?>">Approved</a>
                    </li>
                    <li>
                        <a class="nav-link active" href="<?=base_url('index.php/user/disapproved')?>">Dis-Approved</a>
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

                                <table class="table table-hover ">
                                  <div id="loadingg"  class="loadingg" style="display: none; text-align: center;"></div>
                                    <thead>
                                        <tr class="no-b">
                                             <th>PRODUCT </th>
                                            <th></th>
                                            <th>BARCODE</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                    
                                    <tbody class="new-data">

                                   
                                      
                                       <? foreach ($pass as $value) {
                                         echo $value;
                                       } ?>
                                     
                                    
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

<div id="dialog1" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      
      <div class="modal-body">
       <div id="showresponse">
        
      </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal for edit form-->

<div id="editbarcode" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg barcode_div11">

    <!-- Modal content-->
    <form method="post" enctype="multipart/form-data" id="barcodeform">
    <div class="modal-content">
      <div class="modal-header" style="background: #2979ff;">
        <h3 style="color:white;">Edit Barcode</h3>
        
      </div>
      <div class="modal-body ">
        <div id="editbarcodeform">
        </div>
        
      </div>
      <div class="editbarcode-modal-footer modal-footer">
        
      </div>
    </div>
</form>
  </div>
</div>

    <!-- Modal  for media-->
<?php $this->load->view('footer')?>


<script>
  $( "#target" ).click(function() {
          $("#loadingg").show();
          document.getElementById("loadingg").innerHTML = '<img src="<?= base_url('assets/loading.gif'); ?>" />';
          $(".lovepreet").css("display", "none");
            $('.new-data').html(' ');
          search=$( ".myTextInput" ).val();
          //alert(search);
    jQuery.ajax({
                type: "post",
                url: "<?php echo base_url(); ?>index.php/user/searchdisapproved",
                dataType: 'json',
                data: {search:search},
                success: function(res) {
                  $("#loadingg").css("display", "none");
                  //console.log(res);

                  $.each( res, function( key, value ) {
                   // alert( key + ": " + value );
                      $('.new-data').append(value);
                    });
                 
                  }
                })
});
    
function editbarcode(id){
       //alert(id);
       
      $("#editbarcodeform").html(' ');
      $(".editbarcode-modal-footer").html(' ');
    
          jQuery.ajax({
                type: "post",
                url: "<?php echo base_url(); ?>index.php/user/editbarcode",
                dataType: 'json',
                data: {id:id},
                success: function(res) {
                       console.log(res);
                        
                        $("#editbarcodeform").append('<div class="card-body"><input type="hidden" id="productimage" value="'+res[0]['img']+'" name="img"><input type="hidden" id="barcodeSrNo" value="'+res[0]['barcodeSrNo']+'" name="barcodeSrNo">'
                            +'<div class="form-row">'
                                +' <div class="col-md-6">'
                               +' <h5 class="card-title  card-title1">Products detail</h5>'
                                           +'<div class="form-group m-0 about_div">'
                                            +'<label for="name" class="col-form-label s-12">BARCODE : '+res[0]['barcodeSrNo']+'</label>'
                                                                                       
                                        +'</div>'
                                        +'</div>'
                                        +'<div class="col-md-6">'
                                        +'<div class="dz-image dz-image11 ">'
                                         +'<input type="file" id="file1" name="file" accept="image/*" capture style="display:none"/>'
                                    +'<div class="upfile_img1">'
                                   +'<div class="upfile_img"><img src="<?=base_url();?>'+res[0]['img']+'" id="upfile1" style="cursor:pointer;width: 200;height: 200;border-radius: 50%;" /></div><span class="fa-camera_span"><i class="fa fa-camera"></i></span></div></div>'
                                       +' </div>'
                                      
                                    +'<div class="col-md-12">'
                                        +' <div class="form-row ">'

                                    +'<div class="form-group col-6 m-0">'
                                        +'<label for="inputCity" class="col-form-label s-12"> Product name</label>'
                                       +' <input type="text" class="form-control r-0 light s-12" value="'+res[0]['productName']+'" id="productName" name="productName" required >'
                                    +'</div>'

                                  +' <div class="form-group col-6 m-0">'
                                        +'<label for="email" class="col-form-label s-12">Brand Name</label>'
                                        +'<input  class="form-control r-0 light s-12 " type="text" value="'+res[0]['brandName']+'" id="brandName" name="brandName" required >'
                                   +' </div>'
                                    
                                   +' <input type="hidden" name="table" id="table" value="">'
                                    +'<input type="hidden" name="userId" id="userId" value=">">'
                                    +' <input type="hidden" name="proPicName" id="proPicName" value="">'
                                +'</div>'
                                +'</div>'
                                                                 
                                    +'<!--<div class="col-md-3 offset-md-1">'
                                       +' <div class="dz-image upfile_img1">'
                                        +' <input type="file" id="file1" name="file" accept="image/*" capture style="display:none"/>'

                                  +' <div class="upfile_img"><img src="" id="upfile1" style="cursor:pointer;width: 200;height: 200;border-radius: 50%;" /></div><span><i class="fa fa-camera"></i></span></div>'
                                        
                                       
                                    +'</div>-->'

                                +'</div>');
                 
                  
                  //  var media=res[0]['media'];
                    $(".editbarcode-modal-footer").append('<a href="javascript:" onclick="editbarcodesubmit();" class="edit_product_add">Update</a><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
                 
                  $('#editbarcode').modal({show:true});
                  /* $('#editbarcode').on('hidden.bs.modal', function () {
                            $("video").each(function () { this.pause() });

                })*/
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });
      
   } 
 


</script>
<script>

    

    $(document).ready(function() {

       $('body').on('change',".checkedit", function () {

              
                var checkid= $(this).attr('id');
     // alert(checkid); //console.log(checkid);
     var barcode = $(this).data('barcode');
    // alert(barcode);
      var fieldvalue = $(this).data('value');
         //alert(fieldvalue);
          $.ajax({
            url:"<?php echo base_url(); ?>" + "index.php/user/status",
            data: {fieldvalue:fieldvalue,checkid:barcode},
            method: "POST",
            dataType: 'json',
            success: function(res) {
              //  ... do something with the data...
                console.log(data['new_data_id']);
                swal("Good job!", "Task Done!", "success");
             //$(this).find('.checkvalue').data('value',data['new_data_id']);
              $('#preet_'+res).hide();
            //  $(this).find('.checkvalue').data('value',1);
              //console.log($('#'+checkid+'').data('value'));
            }
      });

       });

      editbarcodesubmit=function(){
    
            var formData = new FormData(document.getElementById("barcodeform"));
      $.ajax({
                type: "post",
                url: "<?php echo base_url('index.php/user/editbarcodesubmit'); ?>",
                dataType: 'json',
                data:formData,
                cache: false,
               contentType: false,
              processData: false,
              
                success: function(res) {

                 
                     window.location.reload();
                                    
                  

                },error:function(ress) {
                    
                      // alert(res[0]->productId);
                        console.log(ress);
          
                    
                }
            });


   }

        $(".showonhover").click(function(){

            $("#selectfile").trigger('click');

        });

        $('body').on('click',"#upfile1", function () {
       // alert('fsd');
    $("#file1").trigger('click');

});

        // var input = document.querySelector('input[id=file1]'); 


//input.onchange = function () {
$('body').on('change',"#file1", function () {
       // alert('fsd');
        var input = document.querySelector('input[id=file1]'); 
  var file = input.files[0];



  drawOnCanvas(file);   // see Example 6

  displayAsImage(file); // see Example 7

});

    });









function drawOnCanvas(file) {

  var reader = new FileReader();



  reader.onload = function (e) {

    var dataURL = e.target.result,

        c = document.querySelector('canvas'), // see Example 4

        ctx = c.getContext('2d'),

        img = new Image();



    img.onload = function() {

      c.width = img.width;

      c.height = img.height;

      ctx.drawImage(img, 0, 0);

    };



    img.src = dataURL;

  };



  reader.readAsDataURL(file);

}



function displayAsImage(file) {

  var imgURL = URL.createObjectURL(file),

      img = document.createElement('img');



  img.onload = function() {

    URL.revokeObjectURL(imgURL);

  };



  img.src = imgURL;

  //alert(img.src);

  $("#upfile1").attr("src",img.src);

  //document.body.appendChild(img);

}






</script>

<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>
 