<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>

<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 " style="padding-top:0px !important;">
                <div class="col">
                    <h4 style="float: left;">
                        <i class="icon-box"></i>
                       Brand
                    </h4>
                    <!-- <form method="post" action="<? echo base_url('/index.php/user/searchbyspecialist')?>"><span style="float:right; " class="skinnerapp_search"><input type="text" name="search" placeholder="Search"><button type="submit">Search</button></span></form> -->
                </div>
            </div>
           
        </div>
    </header>
       <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="v-pills-all" role="tabpanel" aria-labelledby="v-pills-all-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="table-responsive">
                                <form>
                                    <table class="table table-striped table-hover r-0">
                                        <thead>
                                        <tr class="no-b">
                                            
                                            <th>Image</th>
                                            <th>Brand Name</th>
                                            <th>edit</th>
                                            
											
                                            <th></th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                      <?php for($i=0;$i<sizeof($allbrand);$i++):?>
                                        <tr>
                                           
                                           
                                            
                                            <td>
                                                <div class="avatar avatar-md mr-3 mt-1 float-left user_name">
                                                    <span class="avatar-letter avatar-letter-a  avatar-md circle"><img src="<?= base_url($allbrand[$i]->img) ;?>" alt=""></span>
                                                </div>
                                              
                                            </td>


                                            <td><?=$allbrand[$i]->brandName ?></td>
                                            <td ><a title="edit" href="javascript:editbrand('<?=$allbrand[$i]->brandName ?>','<?=$allbrand[$i]->img ?>')">
                                                  <i class="icon-pencil"></i></a></td>
                                            
                                        </tr>
                                        
                                       <?php endfor;?>
                                       </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

               
            </div>
             
               <div class="pagination-link"> <a  href="#"><?php echo $this->pagination->create_links();?></a></div>
            
            </div>
            </div>
        </div>
    </div>

<!-- Modal -->
<div id="showprofile" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background: #2979ff;">
        <h3 style="color:white;"> User Profile</h3>
        
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

<?php $this->load->view('footer')?>

<script type="text/javascript">
    function editbrand(name,img){
       //alert(id);
       
      $("#editbarcodeform").html(' ');
      $(".editbarcode-modal-footer").html(' ');
    
          $("#editbarcodeform").append('<div class="card-body"><input type="hidden" id="img" value="'+img+'" name="img"><input type="hidden" id="oldbrand" value="'+name+'" name="oldbrand"> '
                            +'<div class="form-row">'
                                +' <div class="col-md-6">'
                               
                                                                                       
                                        +'</div>'
                                        +'</div>'
                                        +'<div class="col-md-6">'
                                        +'<div class="dz-image dz-image11 ">'
                                         +'<input type="file" id="file1" name="file" accept="image/*" capture style="display:none"/>'
                                    +'<div class="upfile_img1">'
                                   +'<div class="upfile_img"><img src="<?=base_url();?>'+img+'" id="upfile1" style="cursor:pointer;width: 200;height: 200;border-radius: 50%;" /></div><span class="fa-camera_span"><i class="fa fa-camera"></i></span></div></div>'
                                       +' </div>'
                                      
                                    +'<div class="col-md-12">'
                                        +' <div class="form-row ">'

                                    

                                  +' <div class="form-group col-6 m-0">'
                                        +'<label for="email" class="col-form-label s-12">Brand Name</label>'
                                        +'<input  class="form-control r-0 light s-12 " type="text" value="'+name+'" id="brandName" name="brandName" required >'
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
                    $(".editbarcode-modal-footer").append('<a href="javascript:" onclick="editbrandnamesubmit();" class="edit_product_add">Update</a><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
                 
                  $('#editbarcode').modal({show:true});
                
               
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

function editbrandnamesubmit(){

     var formData = new FormData(document.getElementById("barcodeform"));
      $.ajax({
                type: "post",
                url: "<?php echo base_url('index.php/user/editbrandnamesubmit'); ?>",
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

</script>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>