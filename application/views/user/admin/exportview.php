<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>
<style type="text/css">
    
.excel_div {
    width: 75%;
    margin: auto;
    /* margin-top: 15%; */
    background: #fff;
    padding: 63px 30px;
}
.controls_top_div {
   /* text-align: center;*/
    padding-bottom: 25px;
}
.loadingg {
    position: absolute;
    left: 0;
    right: 0;
    text-align: center;
    background: #ffffffd9;
    z-index: 1;
    top: 0;
    height: 100%;
   
}
.excel_div_main {
    padding-top: 79px;
    height: 100%;
    position: relative;
    min-height: 493px;
}
.zip_file_div_inner {
    padding-bottom: 30px;
}
.button_zip {
    text-align: center;
}
</style>
<div class="page has-sidebar-left">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon-package"></i>
                       IMPORT/EXPORT
                    </h4>
                </div>
            </div>
            
        </div>
    </header>
     <!-- start -->
<div class="excel_div_main">
      <div id="loadingg"  class="loadingg" style="display: none;"></div>
     <div class="excel_div">
      
       <!--  <div class="row">
            <div class="col-md-12">
               <form action="<? echo base_url('/index.php/user/export')?>" method="post" name="export_excel">
 
            <div class="control-group">
                <div class="controls controls_top_div">
                    <button type="submit" id="export" name="export" class="btn btn-primary button-loading" data-loading-text="Loading..."> CSV/Excel File</button>
                </div>
            </div>
        </form>
        </div>
        </div> -->
   
             
 <form enctype="multipart/form-data" method="post" action="<? //echo base_url('/index.php/user/uploadzip')?>">
    <div class="row zip_file_div_inner">
        
        <div class="col-md-6">
<label class="zip_file_div">Choose a video zip file to upload: <input type="file" name="zip_file" id="zip_file"/></label>
</div>
<div class="col-md-6">
    <div class="button_zip">
<button type="button"  class="btn btn-primary button-loading" onclick="uploadzip()" > upload/zip</button>
</div>
</div>
</div>
</form>


<form enctype="multipart/form-data" method="post" action="<? //echo base_url('/index.php/user/uploadzip')?>">
    <div class="row zip_file_div_inner">
        
        <div class="col-md-6">
<label class="zip_file_div">Choose a Image zip file to upload: <input type="file" name="zip_image" id="zip_image"/></label>
</div>
<div class="col-md-6">
    <div class="button_zip">
<button type="button"  class="btn btn-primary button-loading" onclick="imageuploadzip()" > upload/zip</button>
</div>
</div>
</div>
</form>


       <form  action="" method="post" enctype="multipart/form-data" id="w0">
        <div class="row zip_file_div_inner">
        
        <div class="col-md-6 ">
        <label class="zip_file_div">Choose a csv file to upload:</label>

                <input type="hidden" name="upload">
              
            <div class="control-group">
                <div class="controls">
                    <input type="file" name="csv_file"  id="fileupload" accept="application/vnd.ms-excel">
 </div>
                    </div>
                    </div>
              <div class="col-md-6">
                <div class="button_zip">
                    <button type="button" class="btn btn-primary button-loading" onclick="importadmin()" > import File/CSV</button>
                  </div>
                
           
          </div>
        </div>
        </form>


    </div>

</div>
</div>
        <!-- end -->



</div>


  




<?php $this->load->view('footer')?>

<script>

 
 
  function importadmin(){
        $("#loadingg").show();
  //alert(_("file1").files[0]);
    document.getElementById("loadingg").innerHTML = '<img src="<?= base_url('assets/loading.gif'); ?>" />';
     
          var   formData = new FormData(document.getElementById("w0"));
          // console.log('sumit');
          // console.log(formData);

    //       var fileSelect = document.getElementById("fileSelect");
    // if(fileSelect.files && fileSelect.files.length == 1){
    //  var file = fileSelect.files[0]
    //  formData.set("file", file , file.name);
    // }
    // Http Request  
    var request = new XMLHttpRequest();

     request.onreadystatechange = function(e) {
                if ( 4 == this.readyState ) {
                    //console.log(this.responseText.status);
                     console.log(JSON.parse(this.responseText));

                     var obj=JSON.parse(this.responseText);
                     $("#loadingg").css("display", "none");

                   if(obj.status==0){
                              Swal({
                                  position: 'center',
                                  type: 'success',
                                  title: 'Your product has been upload',
                                  showConfirmButton: false,
                                  timer: 1500
                                })
                           }
                            if(obj.status==1){

                                
                                
                                    swal({
                                          title: 'Are you sure to mearge ?',
                                            text: obj.insert+" number of product inserted and "+obj.number+" number of product duplicate  found",
                                          icon: "warning",
                                          buttons: true,
                                          dangerMode: true,
                                        })
                                        .then((willDelete) => {
                                          if (willDelete) {
                                                jQuery.ajax({
                                              type: "POST",
                                              url: "<?php echo base_url(); ?>" + "index.php/user/meargeproduct",
                                              dataType: 'json',
                                              data: {data:obj.result},

                                              success: function(res) {
                                                
                                                if(res==1){

                                                     Swal({
                                                      position: 'center',
                                                      type: 'success',
                                                      title: 'migrate successfully !',
                                                      showConfirmButton: false,
                                                      timer: 1500
                                                    })
                                                }
                                               
                                              },error:function() {
                                                  
                                                    // alert(res[0]->productId);
                                                      console.log("error");
                                        
                                                  
                                              }
                                          });
                                          } else {
                                            swal("Your imaginary file is safe!");
                                          }
                                        });


                            }
                            if(obj.status==2){

                                  Swal(
                                      'File ?',
                                      'Please Choose A File',
                                      'question'
                                    )
                            }
                            if(obj.status==3){

                              Swal({
                                      type: 'error',
                                      title: 'Oops...',
                                      text: 'invalid file format !',
                                      
                                    })
                            }
                }
            };
    request.open('POST', "<?php echo base_url(); ?>index.php/user/adminImport");
    request.send(formData);


   }  

   function uploadzip(){
                $("#loadingg").show();
  //alert(_("file1").files[0]);
          var file =$('#zip_file')[0].files[0];
          // alert(file.name+" | "+file.size+" | "+file.type);
          var formdata = new FormData();
          formdata.append("zip_file", file);
          var ajax = new XMLHttpRequest();
          ajax.upload.addEventListener("progress", progressHandler, false);
          ajax.addEventListener("load", completeHandler, false);
          ajax.addEventListener("error", errorHandler, true);
          ajax.addEventListener("abort", abortHandler, false);
          ajax.open("POST", "<?php echo base_url(); ?>"+"index.php/user/uploadzip");
           document.getElementById("loadingg").innerHTML = '<img src="<?= base_url('assets/loading.gif'); ?>" />';
          ajax.send(formdata);
          ajax.onreadystatechange = function(data) {
            if (ajax.readyState === 4) {
                //console.log(this.responseText);
                $("#loadingg").css("display", "none");
                 // document.getElementById("loadingg").innerHTML = '';
                var obj = JSON.parse(this.responseText);
                //console.log(obj.status);
                  swal(obj.message);
                       
            }
          }


   }  
   function imageuploadzip(){
                $("#loadingg").show();
  //alert(_("file1").files[0]);
          var file =$('#zip_image')[0].files[0];
          // alert(file.name+" | "+file.size+" | "+file.type);
          var formdata = new FormData();
          formdata.append("zip_image", file);
          var ajax = new XMLHttpRequest();
          ajax.upload.addEventListener("progress", progressHandler, false);
          ajax.addEventListener("load", completeHandler, false);
          ajax.addEventListener("error", errorHandler, true);
          ajax.addEventListener("abort", abortHandler, false);
          ajax.open("POST", "<?php echo base_url(); ?>"+"index.php/user/imageuploadzip");
           document.getElementById("loadingg").innerHTML = '<img src="<?= base_url('assets/loading.gif'); ?>" />';
          ajax.send(formdata);
          ajax.onreadystatechange = function(data) {
            if (ajax.readyState === 4) {
                //console.log(this.responseText);
                $("#loadingg").css("display", "none");
                 // document.getElementById("loadingg").innerHTML = '';
                var obj = JSON.parse(this.responseText);
                //console.log(obj.status);
                  swal(obj.message);
                       
            }
          }


   }
</script>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>