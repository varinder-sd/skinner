<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>

<div class="page has-sidebar-left height-full">
  <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h4>
                        <i class="icon-package"></i>
                        Inbox
                    </h4>
                </div>
            </div>
            <!-- <div class="row">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="icon icon-list"></i>All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#modalCreateMessage" data-toggle="modal"
                           data-target="#modalCreateMessage">
                            <i class="icon icon-clipboard-add"></i> Compose New Message
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#modalCreateMessage" data-toggle="modal"
                           data-target="#modalCreateMessage"><i class="icon icon-trash-can"></i>Trash</a>
                    </li>
                </ul>
            </div> -->
        </div>
    </header>
   <div class="container-fluid animatedParent animateOnce p-0">
  
        <div class="animated fadeInUpShort">
            <div class="row no-gutters">
                <div class="col-md-3 white sticky">
                    <div class="sticky white">
                        <ul class="nav nav-tabs nav-material">
                            <li class="nav-item">
                                <a class="nav-link p-3 active show" id="w2--tab1" data-toggle="tab" href="#w2-tab1"><i
                                        class="icon icon-mail-envelope-closed s-18 text-success"></i>Patient</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link p-3" id="w2--tab2" data-toggle="tab" href="#w2-tab2"><i class="icon icon-mail-envelope-closed s-18 text-success"></i>Specialist</a>
                            </li>
                            
                        </ul>
                    </div>
                    <div class="slimScroll">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="w2-tab1" role="tabpanel"
                                 aria-labelledby="w2-tab1">
                                <ul class="list-unstyled ">
                                   <?php for($i=0;$i<sizeof($patient);$i++):?>

                                    <li class="">
                                         <a class="media p-3 b-b has-hover"  href="javascript:mail_record(<?=$patient[$i][0][0]->userId;?>,'patient','<?=$patient[$i][0][0]->proPicName;?>','<?=$patient[$i][0][0]->userName;?>','<?=$patient[$i][0][0]->email;?>')"  >
                                        
                                        <img  class="d-flex mr-3 height-50 img_doctor_name" src="<?= base_url($patient[$i][0][0]->proPicName) ;?>"
                                             alt="Generic placeholder image">
                                        <div class="media-body text-truncate"><?php if($patient[$i][1]>0){?><span class="badge r-3 badge-success pull-right" id="inboxcount"> <?=$patient[$i][1]?></span><?}?>
                                            <h6 class="mt-0 mb-1 font-weight-normal"><?=$patient[$i][0][0]->userName?></h6>
                                            <span><?=$patient[$i][0][0]->email?></span>
                                            <br>
                                            
                                        </div></a>
                                    </li>
                                    <?php endfor;?>
                                    
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="w2-tab2" role="tabpanel" aria-labelledby="w2-tab2">
                                <ul>
                                   <?php for($i=0;$i<sizeof($specialist);$i++):?>
                                    <li class="">
                                        <a  class="media p-3 b-b has-hover" href="javascript:mail_record(<?=$specialist[$i][0][0]->userId ;?>,'specialist','<?=$specialist[$i][0][0]->proPicName;?>','<?=$specialist[$i][0][0]->userName;?>','<?=$specialist[$i][0][0]->email;?>')"  >
                                        <img class="d-flex mr-3 height-50 img_doctor_name" src="<?= base_url($specialist[$i][0][0]->proPicName) ;?>"
                                             alt="Generic placeholder image">
                                        <div class="media-body text-truncate"><?php if($specialist[$i][1]>0){?><span class="badge r-3 badge-success pull-right" id="inboxcount"> <?=$specialist[$i][1]?></span><?}?>
                                            <h6 class="mt-0 mb-1 font-weight-normal"><?=$specialist[$i][0][0]->userName?></h6>
                                            <span><?=$specialist[$i][0][0]->email?></span>
                                            <br>
                                            
                                        </div></a>
                                    </li>
                                    <?php endfor;?>
                                </ul>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <!-- start the div -->
                <div class="col-md-9 b-l"  id="chatbox"></div>

                   <!-- end the div -->
            </div>
        </div>
    </div>
    <!--Add New Message Fab Button-->

    <div id="sendbutton"></div>
    
</div>
<!--Message Modal-->
<div class="modal fade" id="modalCreateMessage" tabindex="-1" role="dialog" aria-labelledby="modalCreateMessage">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content b-0">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Compose A  Message</h6>
                <a href="#" data-dismiss="modal" aria-label="Close"
                   class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
            <div class="modal-body no-p no-b" id="mailform">
                
            </div>
            
        </div>
    </div>
</div>
 
</div>
<?php $this->load->view('footer')?>
<script>
    
function mail_record(id,type,img,name,email){
     $("#chatbox").html('');
     $("#sendbutton").html('');
   // alert(img);
    jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "index.php/user/getmail",
                dataType: 'json',
                data: {id: id, type:type},
                success: function(res) {
                   /* $.each( res, function( key, value ) {
                        alert(value['message']);
                      })*/
                        console.log(res);
                  $("#sendbutton").append('<a href="javascript:sendmailbox(`'+email+'`,`'+id+'`,`'+type+'`)"'
       +' class="btn-fab btn-fab-md fab-right fab-right-bottom-fixed shadow btn-primary">'
            +'<i class="icon-add"></i></a>');       
                 $("#chatbox").append('<div class="m-md-3">');
                 $.each( res, function( key, value ) {
                    if(value['forAdmin']=='admin'){
                     $("#chatbox").append('<div class="card b-0  m-2">'
                            +'<div class="card-body">'
                               +' <div data-toggle="collapse" data-target="#message2" >'
                                   +' <div class="media">'
                                       +' <img class="d-flex mr-3 height-50 img_doctor_name" src="<?php  echo base_url($_SESSION['proPicName']) ;?>"'
                                             +'alt="Generic placeholder image">'
                                      +'  <div class="media-body">'
                                          +'  <h6 class="mt-0 mb-1 font-weight-normal"><?php echo $_SESSION['username']; ?></h6>'
                                          +'  <span>Message sent via <?php echo $_SESSION['email']; ?></span>'
                                           +' <br>'
                                          +'  <small>'+value['time']+'</small>'
                                           +' <div class="collapse my-3 show" id="message2">'
                                               +' <div>'
                                                  +'  <h4>'+value['subject']+'</h4>'
                                                 +'   <p>         </p>'
                                                  +'  <p>'+value['message']+'</p>'
                                               +' </div>'
                                              
                                            +'</div>'
                                       +' </div>'
                                   +' </div>'
                                +'</div>'
                            +'</div>'
                        +'</div>');
                    }else{


                        $("#chatbox").append('<div class="card b-0  m-2">'
                            +'<div class="card-body">'
                               +' <div data-toggle="collapse" data-target="#message2" >'
                                   +' <div class="media">'
                                       +' <img class="d-flex mr-3 height-50 img_doctor_name" src="<?php echo base_url(); ?>'+img+'"'
                                             +'alt="Generic placeholder image">'
                                      +'  <div class="media-body">'
                                          +'  <h6 class="mt-0 mb-1 font-weight-normal">'+name+'</h6>'
                                          +'  <span>Message sent via '+email+'</span>'
                                           +' <br>'
                                          +'  <small>'+value['time']+'</small>'
                                           +' <div class="collapse my-3 show" id="message2">'
                                               +' <div>'
                                                  +'  <h4>'+value['subject']+'</h4>'
                                                 +'   <p>         </p>'
                                                  +'  <p>'+value['message']+'</p>'
                                               +' </div>'
                                              
                                            +'</div>'
                                       +' </div>'
                                   +' </div>'
                                +'</div>'
                            +'</div>'
                        +'</div>');


                    }

                 });
                 $("#chatbox").append( '</div>');
                 
                    
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });
}
function sendmailbox(email,userId,type){
    $("#mailform").html('');
     $("#mailform").append('<form method="post" id="mailform" enctype="multipart/form-data">'
                    +'<div class="form-group has-icon m-0"><input type="hidden" name="usertype" id="usertype" value="'+type+'"><input type="hidden" name="userId" id="userId" value="'+userId+'"><input type="hidden" name="email" id="email" value="'+email+'">'
                       +' <i class="icon-envelope-o"></i>'
                        +'<span class="form-control form-control-lg r-0 b-0" style="padding-left: 68px;">'+email+'</span>'
                    +'</div>'
                   +' <div class="b-b"></div>'
                   +' <div class="form-group has-icon m-0">'
                      +'  <i class="icon-subject"></i>'
                        +'<input class="form-control form-control-lg r-0 b-0" type="text"'
                             +'   name="subject" id="subject" placeholder="Subject">'
                    +'</div>'
                   +' <textarea class="form-control r-0 b-0 p-t-40 editor" placeholder="Write Something..."'
                            +'  rows="6"name="message" id="message" ></textarea></form>'
               +' <div class="modal-footer">'
                +'<button class="btn btn-primary l-s-1 s-12 text-uppercase" onclick="sendformvalue()" >  Send Message</button>'
            +'</div>');
     $('#modalCreateMessage').modal({show:true});
}
function sendformvalue(){
    

      $.ajax({
                type: "post",
                url: "<?php echo base_url('index.php/user/sendmail'); ?>",
                dataType: 'json',
                data:{userId:$('#userId').val(),usertype:$('#usertype').val(),email:$('#email').val(),message:$('#message').val(),subject:$('#subject').val()},
                
                success: function(res) {
                   
                        //alert(res[0]['message']);

                        $('#chatbox').append('<div class="card b-0  m-2">'
                            +'<div class="card-body">'
                               +' <div data-toggle="collapse" data-target="#message2" >'
                                   +' <div class="media">'
                                       +' <img class="d-flex mr-3 height-50 img_doctor_name" src="<?php  echo base_url($_SESSION['proPicName']) ;?>"'
                                             +'alt="Generic placeholder image">'
                                      +'  <div class="media-body">'
                                          +'  <h6 class="mt-0 mb-1 font-weight-normal"><?php echo $_SESSION['username']; ?></h6>'
                                          +'  <span>Message sent via <?php echo $_SESSION['email']; ?></span>'
                                           +' <br>'
                                          +'  <small>'+res[0]['time']+'</small>'
                                           +' <div class="collapse my-3 show" id="message2">'
                                               +' <div>'
                                                  +'  <h4>'+res[0]['subject']+'</h4>'
                                                 +'   <p>         </p>'
                                                  +'  <p>'+res[0]['message']+'</p>'
                                               +' </div>'
                                              
                                            +'</div>'
                                       +' </div>'
                                   +' </div>'
                                +'</div>'
                            +'</div>'
                        +'</div>');
                        swal("Mail sent!", "Successfully", "success");

                 $('#modalCreateMessage').modal('toggle');
                 
                    
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });

}

</script>



<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>