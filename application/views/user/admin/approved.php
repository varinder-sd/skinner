

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
                   
                     <span style="float:right;" class="skinnerapp_search"><a href="<?=base_url('index.php/user/approved')?>">Show All</a><input type="text" name="search" placeholder="Products" class="myTextInput"><button id="target">GO</button></span>
                </div>
            </div>

            <div class="row">
                <ul class="nav responsive-tab nav-material nav-material-white">
                    <li>
                        <a class="nav-link " href="<?=base_url('index.php/user/productForApprovel')?>">Pandding</a>
                    </li>
                    <li>
                        <a class="nav-link active" href="<?=base_url('index.php/user/approved')?>">Approved</a>
                    </li>
                    <li>
                        <a class="nav-link" href="<?=base_url('index.php/user/disapproved')?>">Dis-Approved</a>
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
                                            <th>Approved/Dis-Aproved</th>
                                            <th>Add</th>
                                            <th>Edit</th>
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
                url: "<?php echo base_url(); ?>index.php/user/searchapproved",
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

  function addToAdmin(id){
       //alert(type);
       
      $("#tableform").html(' ');
      $(".editproduct-modal-footer").html(' ');
    
          jQuery.ajax({
                type: "post",
                url: "<?php echo base_url(); ?>" + "index.php/user/addToAdmin",
                dataType: 'json',
                data: {id:id},
                success: function(res) {
                       console.log(res);
                        
                        $("#tableform").append('<div class="row "><div class="col-md-12"><div class="area-section"><input type="hidden"  id="img" value="'+res[0]['img']+'"><input type="hidden" id="barcodeId" value="'+res[0]['barcodeId']+'"><input type="hidden"  id="barcodeSrNo" value="'+res[0]['barcodeSrNo']+'"><input type="hidden" id="brandName" value="'+res[0]['brandName']+'"><input type="hidden"  id="productName" value="'+res[0]['productName']+'"> <div id="loadingg"  class="loadingg" style="display: none; text-align: center;"></div>'
                           +'<div class="container">'
                           +'<h3>Area of use</h3>'
                           +'<div class="row">'
                           +' <div class="col-sm-2 img-wrap">'
                           +'<img src="<?=base_url()?>'+res[0]['img']+'" style="margin-bottom: 15px;">'
                           +'</div>'
                           +'<div class="col-sm-10">'
                           +'<ul class="area-wrap checkbox_div_inner">'
                           +'<li class="col-sm-3">'
                           +'<label class="checkbox_div"> Eyes'
                           +'<input type="checkbox"   id="eyes"/>'
                           +' <span class="checkmark"></span>'
                           +'</label>'
                           +'</li>'
                           +'<li class="col-sm-3">'
                           +'<label class="checkbox_div"> Forhead'
                           +'<input type="checkbox"   id="forhead"/>'
                           +' <span class="checkmark"></span>'
                           +'</label>'
                           +'</li>'
                           +'<li class="col-sm-3">'
                           +'<label class="checkbox_div"> Neck'
                           +'<input type="checkbox"   id="neck"/>'
                           +' <span class="checkmark"></span>'
                           +'</label>'
                           +'</li>'
                           +'<li class="col-sm-3">'
                           +'<label class="checkbox_div"> All Face'
                           +'<input type="checkbox"   id="allFace"/>'
                           +' <span class="checkmark"></span>'
                           +'</label>'
                           +'</li>'
                           +'</ul>'
                           +'</div>'
                           +'</div>'
                           +'</div>'
                           +'</div>'
                           +'</div>'
                           +'</div>'
                           +'<div class="row  checkbox_div_inner1"><div class="col-md-12"><div class="area-section">'
                           +'<div class="container">'
                           +'<h3>Frequncy</h3>'
                           +'<div class="col-sm-12">'
                           +'<ul class="area-wrap row">'
                           +'<li class="col-sm-3">'
                           +'<label class="radio_box_div">Every Day'
                           +'<input type="radio" name="time"   id="everyday"/>'
                           +'<span class="checkmark"></span>'
                           +'</label>'
                           +'</li>'
                           +'<li class="col-sm-3">'
                           +'<label class="radio_box_div">Twice A Week'
                           +'<input type="radio" name="time"   id="twiceAweek"/>'
                           +'<span class="checkmark"></span>'
                           +'</label>'
                           +'</li>'
                           +'<li class="col-sm-3">'
                           +'<label class="radio_box_div">Twice A Week'
                           +'<input type="radio" name="time"  id="onceAweek"/>'
                           +'<span class="checkmark"></span>'
                           +'</label>'
                           +'</li>'
                           +'<li class="col-sm-3">'
                           +'<label class="radio_box_div">Twice A Week'
                           +'<input type="radio" name="time"   id="onceAmonth"/>'
                           +'<span class="checkmark"></span>'
                           +'</label>'
                           +'</li>'
                           +'</ul>'
                           +'</div>'
                           +'</div>'
                           +'</div>'
                            +'</div>'
                           +'</div>'


                           +'<div class="row text-section checkbox_div_inner1">'
                           +'<div class="container">'
                           +'<h3>Instruction</h3>'

                           +'<div class="col-sm-12">'
                            +'<textarea class="form-control rounded-0" rows="7" id="instruction"></textarea>'
                                                                             
                           +'</div>'
                            +'<div class="col-sm-12">'
                           +'<div class="upload_video">'
                           +'<span class="heading_timing" style="padding-right: 10px;">Choose Media File</span><input type="file" name="file"  id="filedf"/ accept="video/*">'
                           +'</div>'
                       
                           +'</div>'
                            
                             +'<div class="col-sm-12">'
                             +'<div class="upload_video">'
                                 +'<span class="clock_div_radio"><span class="heading_timing" style="padding-right: 10px;">interval</span>'
                           +'<label class="radio_box_div">AM'
                           +'<input type="radio" name="am"  id="am"/>'
                           +'<span class="checkmark"></span>'
                           +'</label>'
                           +'</span>'
                            +'<span class="clock_div_radio">'
                           +'<label class="radio_box_div">PM'
                           +'<input type="radio" name="am"  id="pm"/>'
                           +'<span class="checkmark"></span>'
                           +'</label>'
                           +'</span>'
                           +'</div>'  
                             +'</div>'
                             +'<div class="container">'
                             +'<div class="row checkbox_div_inner1">'
                             +'<div class="col-sm-4"><div class="upload_video">'
                             +'<span class="heading_timing">Step</span><label class="radio_box_div">'
                           +'<input type="checkbox" name="time"  id="open_div"/>'
                           +'<span class="checkmark"></span>'
                           +'</label>'
                             +' </div>'
                            +'</div>'
                             +'<div class="col-sm-8" id="m_k_b" >'
                            +'</div>'
                            +'<div class="col-sm-12"><div class="upload_video upload_video_select "><span class="heading_timing">minutes</span><select class="minutes">  <option value="1">1</option>  <option value="2">2</option> <option value="3">3</option>  <option value="4">4</option> <option value="5">5</option> <option value="6">6</option> <option value="7">7</option> <option value="8">8</option> <option value="9">9</option></select> </div>'
                            +'</div>'
                             +'</div>'
                              +'</div>'


                           +'</div>'
                           +'</div>');
                 
                  
                  //  var media=res[0]['media'];
                    $(".editproduct-modal-footer").append('<button onclick="addToExp()" class="edit_product_add">Add</button><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
                 
                  $('#editproduct').modal({show:true});
                   $('#editproduct').on('hidden.bs.modal', function () {
                            $("video").each(function () { this.pause() });

                        })
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });
      
   } 
   
   $('body').on('click',"#open_div", function () {
    $('#open_div').change(function() {
        if($(this).is(":checked")) {
           $("#m_k_b").html('<div class="upload_video">'
                              +'<label class="radio_box_div">1'
                           +'<input type="radio" name="numberOfStep" id="step1" checked/>'
                           +'<span class="checkmark"></span>'
                           +'</label>'
                           +'</span>'
                            +'<label class="radio_box_div">2'
                           +'<input type="radio" name="numberOfStep" id="step2"/>'
                           +'<span class="checkmark"></span>'
                           +'</label>'
                           +'</span>'
                            +'<label class="radio_box_div">3'
                           +'<input type="radio" name="numberOfStep" id="step3"/>'
                           +'<span class="checkmark"></span>'
                           +'</label>'
                           +'</span>'                     
                              +' </div>');
        }
        
               
    });
  })
   $('body').on('click',"#open_div", function () {
    $('#open_div').change(function() {


       var ischecked= $(this).is(':checked');
    if(!ischecked)
      $("#m_k_b").html('');
      
               
    });
  })

   
    function addToExp(){
      //alert('feds');
        $("#app").find("#editproduct").find("#loadingg").html('<img src="<?= base_url('assets/loading.gif'); ?>" />');
         $("#app").find("#editproduct").find("#loadingg").show();
          
          document.getElementById("loadingg").innerHTML = '<img src="<?= base_url('assets/loading.gif'); ?>" />';
    // alert($('#everyday').prop('checked'));
            var myFormData = new FormData();
            myFormData.append('fileToUpload', $('#filedf').prop('files')[0]);
            myFormData.append('img',$("#img" ).val());
            myFormData.append('type','new');
            myFormData.append('barcodeId',$( "#barcodeId" ).val());
            myFormData.append('barcodeSrNo',$( "#barcodeSrNo" ).val());
            myFormData.append('productName',$( "#productName" ).val());
            myFormData.append('minutes',$( ".minutes" ).val());
            myFormData.append('brandName',$( "#brandName" ).val());
            myFormData.append('eyes',$( "#eyes" ).prop('checked'));
            myFormData.append('forhead',$( "#forhead" ).prop('checked'));
            myFormData.append('neck',$( "#neck" ).prop('checked'));
            myFormData.append('allFace',$( "#allFace" ).prop('checked'));
            myFormData.append('everyday',$( "#everyday" ).prop('checked'));
            myFormData.append('twiceAweek',$( "#twiceAweek" ).prop('checked'));
            myFormData.append('onceAweek',$( "#onceAweek" ).prop('checked'));
            myFormData.append('onceAmonth',$( "#onceAmonth" ).prop('checked'));
            myFormData.append('instruction',$( "#instruction" ).val());
            myFormData.append('numberOfStep',$( "#open_div" ).prop('checked'));
            myFormData.append('step1',$( "#step1" ).prop('checked'));
             myFormData.append('am',$( "#am" ).prop('checked'));
              myFormData.append('pm',$( "#pm" ).prop('checked'));
            myFormData.append('step2',$( "#step2" ).prop('checked'));
            myFormData.append('step3',$( "#step3" ).prop('checked'));
           
                

      jQuery.ajax({
                type: "post",
                url: "<?php echo base_url(); ?>" + "index.php/user/addToExplore",
                dataType: 'json',
                 processData: false, // important
                 contentType: false, 
                 data:myFormData ,
                success: function(res) {
                $("#app").find("#editproduct").find("#loadingg").css("display", "none");

                  $('#editproduct').modal('toggle');
                   swal("Good job!", "Add Product In Explore List!", "success");
                 //$(this).find('.checkvalue').data('value',data['new_data_id']);
                  $('#kbc_'+res).hide();
                      
                   $('#kvc_'+res).show();
             
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("responseText");
          
                    
                }
            });

   } 
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
 