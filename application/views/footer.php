<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
	 <!-- /.panel-footer -->
                    

    <!-- /#wrapper -->
    
    <!-- jQuery -->
   



<!-- Modal for detail-->
<div id="productdetail" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background: #2979ff;">
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
<!-- Modal for edit form-->

<div id="editproduct" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background: #2979ff;">
        <h3 style="color:white;">Edit Product Instructions</h3>
        
      </div>
      <div class="modal-body ">
       
        <div id="tableform">
        </div>
        
      </div>
      <div class="editproduct-modal-footer modal-footer">
        
      </div>
    
    </div>

  </div>
</div>

    <!-- Modal  for media-->
<div id="showvideo" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header video_heading_div" style="background: #2979ff;">
       <h3>product Video</h3>
      </div>
      <div class="modal-body">
       <div id="video-body">
       
      </div>
      </div>
      <div class="modal-footer">
        <div id="video-footer" style="background: #2979ff;">
        
      </div>
      </div>
    </div>

  </div>
</div>
   
   

	<script src="<?= base_url('assets/dist/sweetalert2.all.min.js')?>"></script> 
   <script src="<?= base_url('assets/dist/sweetalert2.min.js')?>"></script> 
<!-- Optional: include a polyfill for ES6 Promises for IE11 and Android browser -->

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/promise-polyfill"></script>
	
	  <script src="<?= base_url('assets/js/app.js')?>"></script>
    <!-- for img -->

      
    <script type="text/javascript">
      
var abc = 0;      // Declaring and defining global increment variable.
$(document).ready(function() {


    $('.pagination-link').children('a').each(function(){

      if($(this).attr("href")=='#' ){
        $(this).hide();
      }

    });





//  To add new input file field dynamically, on click of "Add More Files" button below function will be executed.
$('#add_more').click(function() {
$(this).before($("<div/>", {
id: 'filediv'
}).fadeIn('slow').append($("<input/>", {
name: 'file[]',
type: 'file',
id: 'file',
accept: 'image/*'
}), $("<br/><br/>")));
});
// Following function will executes on change event of file input to select different file.
$('body').on('change', '#file', function() {
if (this.files && this.files[0]) {
abc += 1; // Incrementing global variable by 1.
var z = abc - 1;
var x = $(this).parent().find('#previewimg' + z).remove();
$(this).before("<div id='abcd" + abc + "' class='abcd'><img id='previewimg" + abc + "' src=''/></div>");
var reader = new FileReader();
reader.onload = imageIsLoaded;
reader.readAsDataURL(this.files[0]);
$(this).hide();
$("#abcd" + abc).append($("<img/>", {
id: 'img',
src: "<?=base_url('assets/x.png')?>",
alt: 'delete'
}).click(function() {
$(this).parent().parent().remove();
}));
}
});
// To Preview Image
function imageIsLoaded(e) {
$('#previewimg' + abc).attr('src', e.target.result);
};
$('#upload').click(function(e) {
var name = $(":file").val();
if (!name) {
alert("First Image Must Be Selected");
e.preventDefault();
}
});
});




    </script>
 <script type="text/javascript">
  function productDetail(id){
    $("#tablebody").html(" ");
     jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "index.php/user/productDetail",
                dataType: 'json',
                data: {id: id, usertype:"specialist"},
                success: function(res) {
                    
                      //alert(res[0]['productId']);
                        console.log(res);
                 $("#tablebody").append('<h4>Area Of Use</h4>'
                 +' <div class="row"> '
                 +'<div class="col-md-3 col-xs-3"><img src="<?php echo base_url(); ?>'+res[0]['img']+'" alt=""></div>'
         +'<div class="col-md-4 col-xs-4">'
        +'<div class="col-md-12 col-xs-12">'+ ( res[0]['eyes']> 0 ? '<i class="fa fa-angle-double-right text-primary"></i>': '<i class="fa fa-angle-double-right myradiobutton"></i>')+'<span class="bmw">Eyes</span></div>'
         +'<div class="col-md-12 col-xs-12">'+ ( res[0]['allFace']> 0 ? '<i class="fa fa-angle-double-right text-primary"></i>': '<i class="fa fa-angle-double-right myradiobutton"></i>')+'<span class="bmw">All Face</span></div>'
        +' </div>'
        +' <div class="col-md-3 col-xs-3">'+ ( res[0]['forhead']> 0 ? '<i class="fa fa-angle-double-right text-primary"></i>': '<i class="fa fa-angle-double-right myradiobutton"></i>')+'<span class="bmw">Forhead</span></div>'
         +'<div class="col-md-2 col-xs-2">'+ ( res[0]['neck']> 0 ? '<i class="fa fa-angle-double-right text-primary"></i>': '<i class="fa fa-angle-double-right myradiobutton"></i>')+'<span class="bmw">Neck</span></div>'
         +'</div>'
         +'<hr/>'
         +'<h4>Frequency</h4>'
          +'<div class="row" >'
          +'<div class="col-md-5 col-xs-5">'
         +'<div class="col-md-12 col-xs-12">'+ ( res[0]['everyday']> 0 ? '<i class="fa fa-angle-double-right  text-primary"></i>': '<i class="fa fa-angle-double-right myradiobutton"></i>')+'<span class="bmw">Every Day</span></div>'
         +'<div class="col-md-12 col-xs-12">'+ ( res[0]['onceAweek']> 0 ? '<i class="fa fa-angle-double-right text-primary"></i>': '<i class="fa fa-angle-double-right myradiobutton"></i>')+'<span class="bmw">Once A Month</span></div>'
         +'</div>'
         +'<div class="col-md-4 col-xs-4">'+ ( res[0]['twiceAweek']> 0 ? '<i class="fa fa-angle-double-right text-primary"></i>': '<i class="fa fa-angle-double-right myradiobutton"></i>')+'<span class="bmw">Twice A Week</span></div>'
         +'<div class="col-md-3 col-xs-3">'+ ( res[0]['onceAmonth']> 0 ? '<i class="fa fa-angle-double-right text-primary"></i>': '<i class="fa fa-angle-double-right myradiobutton"></i>')+'<span class="bmw">Once A Week</span></div>'
         +'</div>'
         +'<hr/>'
         +'<h4>Instructions</h4>'
          +'<div class="row clock" >'
          +'<div class="col-md-12 col-xs-12"><p>'+res[0]['instruction']+'</p></div>'
           +'<div class="col-md-6"><div class="instructions_div1"<video width="100%" height="165" controls=""><source src="http://213.136.88.137/~skinner/assets/videos/5c303ef56da3a.mp4" type="video/mp4"></video></div></div>'
         +'<div class="col-md-12 col-xs-12">'
         +'<div class="col-md-6 col-xs-6">'
         +''+ ( res[0]['am']> 0 ? '<i class="fa fa-clock-o text-primary"></i>': '<i class="fa fa-clock-o myradiobutton"></i>')+'<span class="bmw">am</span>'
         +''+ ( res[0]['pm']> 0 ? '<i class="fa fa-clock-o text-primary"></i>': '<i class="fa fa-clock-o myradiobutton"></i>')+'<span class="bmw">pm</span>'
         +'</div>'
         +'</div>'
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


</script>  
   <script type="text/javascript">


    jQuery( document ).ready(function() {
      $("#myclass").html(' ');
       $("#notifExplore").html(' ');
        $("#notifvedio").html(' ');
        $("#inboxcount").html(' ');
        
     jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "index.php/user/totalcount",
                dataType: 'json',
                
                success: function(res) {
                  
                        console.log(res);
                 $("#myclass").append('<li class="dropdown custom-dropdown notifications-menu"><a href="#" class=" nav-link" data-toggle="dropdown" aria-expanded="false"> <i class="icon-notifications "></i> <span class="badge badge-danger badge-mini rounded-circle">'+res['total']+'</span> </a> <ul class="dropdown-menu dropdown-menu-right"><li><!-- inner menu: contains the actual data --><ul class="menu"><li><a href="<?=base_url('index.php/user/videoapprovel')?>"><i class="icon icon-data_usage text-success"></i>'+res['video']+' video for approvel</a></li><li><a href="<?=base_url('index.php/user/explorproduct')?>""><i class="icon icon-data_usage text-danger"></i> '+res['addToExplore']+' new product for add </a></li></ul></li></ul></li>');
                 $("#inboxcount").append(res['inbox']);
                 $("#notifExplore").append(res['addToExplore']);
                $("#notifvedio").append(res['video']);
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });
});
     
function showmedia(id){
      $("#video-body").html(" ");
      $("#video-footer").html(" ");
     jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "index.php/user/showmedia",
                dataType: 'json',
                data: {id: id},
                success: function(res) {
                    
                      //alert(res[0]['productId']);
                        console.log(res);
                 $("#video-body").append('<video width="480" height="240"  controls><source src="<?php echo base_url(); ?>'+res[0]['media']+'" type="video/mp4"></video>');
                 if(res[0]['status']==0){
                  //  alert("true");
                  //  var media=res[0]['media'];
                    $("#video-footer").append('<button onclick="forapprovel('+res[0]['productId']+',\'' +res[0]['media'] + '\')">Approve</button><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
                 }else{
                    $("#video-footer").append('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
                 }
                  $('#showvideo').modal({show:true});
                  $('#showvideo').on('hidden.bs.modal', function () {
                            $("video").each(function () { this.pause() });

                        })
                    
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });

   }
   function forapprovel(productId,media){
  //  alert(media);

     jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "index.php/user/forapprovel",
                dataType: 'json',
                data: {id:productId,media:media},
                success: function(res) {
                    
             $(function () {
                           $('#showvideo').modal('toggle');
                           location.reload();
                        });
                                
                    
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });

   }
   function productEditForm(id,type){
       //alert(type);
       var newtype=type;
      $("#tableform").html(' ');
      $(".editproduct-modal-footer").html(' ');
    
          jQuery.ajax({
                type: "GET",
                url: "<?php echo base_url(); ?>" + "index.php/user/productEditForm",
                dataType: 'json',
                data: {id:id,type:type},
                success: function(res) {
                       console.log(res);
                        
                        $("#tableform").append('<div class="row "><div class="col-md-12"><div class="area-section"><input type="hidden"  id="img" value="'+res[0]['img']+'"><input type="hidden"  id="media" value="'+res[0]['media']+'"><input type="hidden" id="barcodeId" value="'+res[0]['barcodeId']+'"><input type="hidden"  id="barcodeSrNo" value="'+res[0]['barcodeSrNo']+'"><input type="hidden" id="brandName" value="'+res[0]['brandName']+'"><input type="hidden"  id="productName" value="'+res[0]['productName']+'"><input type="hidden"  id="datatype" value="'+res[0]['type']+'"><input type="hidden"  id="productId" value="'+res[0]['productId']+'">'
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
 +'<input type="checkbox"  '+ ( res[0]['eyes']== 1 ? 'checked' : 'unchecked')+' id="eyes"/>'
 +' <span class="checkmark"></span>'
 +'</label>'
 +'</li>'
 +'<li class="col-sm-3">'
 +'<label class="checkbox_div"> Forhead'
 +'<input type="checkbox"  '+ ( res[0]['forhead']== 1 ? 'checked' : 'unchecked')+' id="forhead"/>'
 +' <span class="checkmark"></span>'
 +'</label>'
 +'</li>'
 +'<li class="col-sm-3">'
 +'<label class="checkbox_div"> Neck'
 +'<input type="checkbox"  '+ ( res[0]['neck']== 1 ? 'checked' : 'unchecked')+' id="neck"/>'
 +' <span class="checkmark"></span>'
 +'</label>'
 +'</li>'
 +'<li class="col-sm-3">'
 +'<label class="checkbox_div"> All Face'
 +'<input type="checkbox"  '+ ( res[0]['allFace']== 1 ? 'checked' : 'unchecked')+' id="allFace"/>'
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
 +'<input type="radio" name="time"  '+ ( res[0]['everyday']== 1 ? 'checked' : 'unchecked')+' id="everyday"/>'
 +'<span class="checkmark"></span>'
 +'</label>'
 +'</li>'
 +'<li class="col-sm-3">'
 +'<label class="radio_box_div">Twice A Week'
 +'<input type="radio" name="time"  '+ ( res[0]['twiceAweek']== 1 ? 'checked' : 'unchecked')+' id="twiceAweek"/>'
 +'<span class="checkmark"></span>'
 +'</label>'
 +'</li>'
 +'<li class="col-sm-3">'
 +'<label class="radio_box_div">Twice A Week'
 +'<input type="radio" name="time"  '+ ( res[0]['onceAweek']== 1 ? 'checked' : 'unchecked')+' id="onceAweek"/>'
 +'<span class="checkmark"></span>'
 +'</label>'
 +'</li>'
 +'<li class="col-sm-3">'
 +'<label class="radio_box_div">Twice A Week'
 +'<input type="radio" name="time"  '+ ( res[0]['onceAmonth']== 1 ? 'checked' : 'unchecked')+' id="onceAmonth"/>'
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
 +'<textarea class="form-control rounded-0" rows="7" id="instruction">'+res[0]['instruction']+'</textarea>'
 +'<div class="col-md-6"><div class="instructions_div1"><video width="100%" height="165" controls=""><source src="<?=base_url()?>'+res[0]['media']+'" type="video/mp4"></video></div></div> '
 +'<span class="clock_div_radio">'
 +'<label class="radio_box_div">AM'
 +'<input type="radio" name="am" '+ ( res[0]['am']== 1 ? 'checked' : 'unchecked')+' id="am"/>'
 +'<span class="checkmark"></span>'
 +'</label>'
 +'</span>'
  +'<span class="clock_div_radio">'
 +'<label class="radio_box_div">PM'
 +'<input type="radio" name="am" '+ ( res[0]['pm']== 1 ? 'checked' : 'unchecked')+' id="pm"/>'
 +'<span class="checkmark"></span>'
 +'</label>'
 +'</span>'
 +'</div>'
 +'</div>'
 +'</div>'
 +'<div class="container">'
                             +'<div class="row checkbox_div_inner1">'
                             +'<div class="col-sm-4"><div class="upload_video">'
                             +'<span class="heading_timing">Step</span><label class="radio_box_div">'
                           +'<input type="checkbox" name="time"  id="open_div" '+ ( res[0]['numberOfStep']== 1 ? 'checked' : 'unchecked')+'/>'
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
                    $(".editproduct-modal-footer").append('<button onclick="addToExplore()" class="edit_product_add">Edit</button><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
                 
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
   function _(el){
  return document.getElementById(el);
}
function _(el){
  return document.getElementById(el);
}
function uploadFile(id){
 
  //alert(_("file1").files[0]);
  var file =$('#file1')[0].files[0];
  // alert(file.name+" | "+file.size+" | "+file.type);
  var formdata = new FormData();
  formdata.append("fileToUpload", file);
  var ajax = new XMLHttpRequest();
  ajax.upload.addEventListener("progress", progressHandler, false);
  ajax.addEventListener("load", completeHandler, false);
  ajax.addEventListener("error", errorHandler, true);
  ajax.addEventListener("abort", abortHandler, false);
  ajax.open("POST", "<?php echo base_url(); ?>"+"index.php/user/upload?id="+id);
  ajax.send(formdata);
  ajax.onreadystatechange = function() {
    if (ajax.readyState === 4) {
      jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "index.php/user/updatevideo",
                dataType: 'json',
                data: {id:id},
                success: function(res) {
                      
                  //console.log($(this));
                 //console.log('Video Id'+$('video').attr('id'));
                // $(this).find('source').hide();
               $('video').find('source').attr('src','<?=base_url();?>'+res['link'][0]['media']);
               $("video")[0].load();
                //console.log('New Src : '+abcdef);

                      //  console.log(res['link'][0]['media']);
                        
                //});
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });
      
    }
  }
   
   

   
    
}
function progressHandler(event){
  _("loaded_n_total").innerHTML = "Uploaded "+event.loaded+" bytes of "+event.total;
  var percent = (event.loaded / event.total) * 100;
  _("progressBar").value = Math.round(percent);
  _("status").innerHTML = Math.round(percent)+"% uploaded... please wait";
}
function completeHandler(event){
  _("status").innerHTML = event.target.responseText;
  _("progressBar").value = 0;
  
}
function errorHandler(event){
  _("status").innerHTML = "Upload Failed";
}
function abortHandler(event){
  _("status").innerHTML = "Upload Aborted";
}


function addToExplore(){
    // alert($('#everyday').prop('checked'));

      jQuery.ajax({
                type: "post",
                url: "<?php echo base_url(); ?>" + "index.php/user/addToExplore",
                dataType: 'json',
                data: {img:$( "#img" ).val(),type:$( "#datatype" ).val(),id:$( "#productId" ).val(),barcodeId:$( "#barcodeId" ).val(),barcodeSrNo:$( "#barcodeSrNo" ).val(),productName:$( "#productName" ).val(),brandName:$( "#brandName" ).val(),media:$( "#media" ).val(),eyes:$( "#eyes" ).prop('checked'),forhead:$( "#forhead" ).prop('checked'),neck:$( "#neck" ).prop('checked'),allFace:$( "#allFace" ).prop('checked'),everyday:$( "#everyday" ).prop('checked'),twiceAweek:$( "#twiceAweek" ).prop('checked'),onceAweek:$( "#onceAweek" ).prop('checked'),onceAmonth:$( "#onceAmonth" ).prop('checked'),instruction:$( "#instruction" ).val(),am:$( "#am" ).prop('checked'),pm:$( "#pm" ).prop('checked')},
                success: function(res) {
                     location.reload();
                     //alert('res');
                      console.log(responseText);
                     // location.reload();
                     
                          //  location.reload();
                      
                   
             
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log(responseText);
          
                    
                }
            });

   } 
  
   function showmediafromadmin(id){
   // alert(id);
      $("#video-body").html(" ");
      $("#video-footer").html(" ");
     jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "index.php/user/showAdminMedia",
                dataType: 'json',
                data: {id: id},
                success: function(res) {
                    
                     // alert(res[0]['media']);
                        //console.log(res);
                 $("#video-body").append('<div class="row"><div class="col-md-12"><video  width="457" height="240" controls id="updatevideo"><source  src="<?=base_url()?>'+res[0]['media']+'" type="video/mp4"></video></div><div class="col-md-12"><form id="upload_form" enctype="multipart/form-data" method="post"><input style="margin:4px;" type="file" name="file1" id="file1" accept="video/*"><br><input style="margin:4px;" type="button" value="Upload File" onclick="uploadFile('+id+')"><progress id="progressBar" value="0" max="100" style="width:300px;"></progress><h3 id="status"></h3><p id="loaded_n_total"></p></form></div></div>');
                 
                    
                    $("#video-footer").append('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
                    $('#showvideo').modal({show:true});
                    $('#showvideo').on('hidden.bs.modal', function () {
                            $("video").each(function () { this.pause() });

                        })
                    
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });

   }

    function adminProductDetail(id){
    $("#tablebody").html(" ");
     jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "index.php/user/adminProductDetail",
                dataType: 'json',
                data: {id:id },
                success: function(res) {
                    
                      //alert(res[0]['productId']);
                        console.log(res);
                 $("#tablebody").append('<h4>Area Of Use</h4>'
                 +' <div class="row"> '
                 +'<div class="col-md-4 col-xs-4"><div class="Area_product_div"><img src="<?php echo base_url(); ?>'+res[0]['img']+'" alt=""></div></div>'
         +'<div class="col-md-2 col-xs-2">'
        +'<div class="div">'+ ( res[0]['eyes']> 0 ? '<i class=" fa fa-angle-double-right text-primary"></i>': '<i class=" fa fa-angle-double-right  myradiobutton "></i>')+'<span class="bmw">Eyes</span></div>'
		
        
        +' </div>'
		+'<div class="col-md-2 col-xs-2">'
			 +'<div class="div">'+ ( res[0]['allFace']> 0 ? '<i class=" fa fa-angle-double-right text-primary"></i>': '<i class=" fa fa-angle-double-right  myradiobutton "></i>')+'<span class="bmw">All Face</span></div>'
		+'</div>'
        +' <div class="col-md-2 col-xs-2">'+ ( res[0]['forhead']> 0 ? '<i class=" fa fa-angle-double-right text-primary"></i>': '<i class=" fa fa-angle-double-right  myradiobutton "></i>')+'<span class="bmw">Forhead</span></div>'
         +'<div class="col-md-2 col-xs-2">'+ ( res[0]['neck']> 0 ? '<i class=" fa fa-angle-double-right text-primary"></i>': '<i class=" fa fa-angle-double-right  myradiobutton "></i>')+'<span class="bmw">Neck</span></div>'
         +'</div>'
         +'<hr/>'
         +'<h4>Frequency</h4>'
          +'<div class="row" >'
          +'<div class="col-md-3 col-xs-3">'
         +'<div class="col-md-12 col-xs-12">'+ ( res[0]['everyday']> 0 ? '<i class=" fa fa-angle-double-right text-primary"></i>': '<i class=" fa fa-angle-double-right  myradiobutton "></i>')+'<span class="bmw">Every Day</span></div>'
         
         +'</div>'
		   +'<div class="col-md-3 col-xs-3">'
        
         +'<div class="col-md-12 col-xs-12">'+ ( res[0]['onceAweek']> 0 ? '<i class=" fa fa-angle-double-right text-primary"></i>': '<i class=" fa fa-angle-double-right  myradiobutton "></i>')+'<span class="bmw">Once A Month</span></div>'
         +'</div>'
         +'<div class="col-md-3 col-xs-3">'+ ( res[0]['twiceAweek']> 0 ? '<i class=" fa fa-angle-double-right text-primary"></i>': '<i class=" fa fa-angle-double-right  myradiobutton "></i>')+'<span class="bmw">Twice A Week</span></div>'
         +'<div class="col-md-3 col-xs-3">'+ ( res[0]['onceAmonth']> 0 ? '<i class=" fa fa-angle-double-right text-primary"></i>': '<i class=" fa fa-angle-double-right  myradiobutton "></i>')+'<span class="bmw">Once A Week</span></div>'
         +'</div>'
         +'<hr/>'
         +'<h4 >Instructions</h4>'
          +'<div class="row clock" >'
          +'<div class="col-md-12 col-xs-12"><p>'+res[0]['instruction']+'</p></div>'
         
         +'<div class="col-md-12 col-xs-12">'
         +'<div class="col-md-6 col-xs-6 ">' 
         +''+ ( res[0]['am']> 0 ? '<i class="fa fa-clock-o text-primary"></i>': '<i class="fa fa-clock-o  myradiobutton "></i>')+'<span class="bmw">am</span>'
         +''+ ( res[0]['pm']> 0 ? '<i class=" fa fa-clock-o text-primary"></i>': '<i class=" fa fa-clock-o  myradiobutton "></i>')+'<span class="bmw">pm</span>'
         +'</div>'
         +'</div>'
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
  function searchbylist(type){
    //alert($( "#searchByList" ).val());
     
     jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "index.php/user/searchbylist",
               //dataType: 'json',
                data: {type: type,search:$( "#searchByList" ).val()},
                success: function(res) {
                    
                     // alert(res[0]['media']);
                        console.log(res);
                
                    
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });

   }

</script>
<script>
   
function specialistdelete(id) {
  // alert(id);
  
   var r = confirm("Are You Sure to Delete!");
    if (r == true) {
       
      window.location.href = '<?php echo base_url("index.php/user/specialistdelete?id=") ?>'+id;
    } else {
      
       window.location.href = '<?php echo base_url("index.php/user/specialist") ?>';
    } 
    
    
}
function showProfile(id,count){
    //alert(data);
    $("#tablebody").html(" ");
     jQuery.ajax({
                type: "post",
                url: "<?php echo base_url(); ?>" + "index.php/user/showProfile1",
               dataType: 'json',
                data: {id: id, data:"sepcial_users"},
                success: function(res) {
                    
                      //alert(res[0]['productId']);
                        console.log(res);
                 $("#tablebody").append('<div class="container-fluid relative animatedParent animateOnce">'
        +'<div class="card-box col-sm-12">'
		+'<div class="row">'
		+'<div class="col-md-12">'
			  +'   <h1>'+res[0]['userName']+'</h1>'
		+'</div>'
		+'</div>'
             +'<div class="row">'
                +' <div class="col-sm-2"><div class="user_detail_img"><img src="<?=base_url()?>'+res[0]['proPicName']+'" alt=""></div></div>' 
                +' <div class="col-sm-10">'
                
                    +' <p>'+res[0]['aboutMe']+'</p>'
					
                 +'</div>'
				 +'<div class="col-md-12">'
				 +'<div class="row specialist_popup_detail">'
				 +' <p class="col-sm-6 specialist_popup"><label>Email :</label>'+res[0]['email']+'</p>'
                     +' <p class="col-sm-6 specialist_popup"><label>Age :</label>'+res[0]['age']+'</p>'
					 +' <p class="col-sm-6 specialist_popup1"><label>City : </label>'+res[0]['city']+'</p>'
					 +' <p class="col-sm-6 specialist_popup1"><label> Countary:</label>'+res[0]['countary']+'</p>'
					 
					 +' <p class="col-sm-6 specialist_popup"><label>Phone :</label>'+res[0]['phone']+'</p>'
					 +' <p class="col-sm-6 specialist_popup"><label>Total patient :</label>'+res[0]['totalpatient']+'</p>'
					 +' <p class="col-sm-6 specialist_popup1"><label>Total Product :</label>'+count+'</p>'
					 +' <p class="col-sm-6 specialist_popup1"><label>Experience :</label>'+res[0]['yearOfExperience']+'</p>'
                    
				
				
				  +'<div class="col-md-12 specialist_popup">'
				 +' <p class="specialist_popup_submit"><a href="<?php echo base_url(); ?>'+'index.php/user/specialuserproduct?id='+res[0]['userId']+'&name='+res[0]['userName']+'" title="my product detail" target="_blank">My Product</a></p>'
           +'  </div>'
		    +'</div>'
		    +'  </div>'
        +' </div>'
     +'</div>');
                  $('#showprofile').modal({show:true});
                    
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });
      //jQuery.('#myModal').modal('show');
          // jQuery.('#myModal').modal('show');



  }
$(document).ready(function() {
    //console.log('sumit');

    $(".checkedit").change(function() {
       // alert('wdqd');
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
             // alert("ds");
             swal("Good job!", "Task Done!", "success");
              $('#preet_'+res).hide();
              //  ... do something with the data...
                console.log(data['new_data_id']);
             
            //  $(this).find('.checkvalue').data('value',1);
              //console.log($('#'+checkid+'').data('value'));
            }
      });
    });

 });
 
 
</script>


</body>

</html>
