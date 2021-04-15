<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>

<div class="page has-sidebar-left height-full">
 <header class="blue accent-3 relative">
            <div class="container-fluid text-white">
                <div class="row p-t-b-10 ">
                    <div class="col">
                        <div class="pb-3">
                            <div class="image mr-3  float-left">
                                <img class="user_avatar no-b no-p" src="<?= base_url($data[0]->proPicName) ;?>" alt="User Image">
                            </div>
                            <div>
                                <h6 class="p-t-10"><?=$data[0]->userName;?></h6>
                               <?=$data[0]->email;?>
                            </div>
                        </div>
                    </div>
                </div>
<!--  -->       <input type="hidden" Id="userId" value="<?=$data[0]->userId;?>" />

            </div>
        </header>


    <div class="container-fluid animatedParent animateOnce my-3">
            <div class="animated fadeInUpShort">
           <div class="tab-content" id="v-pills-tabContent">
               <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                   <div class="row">
                       <div class="col-md-4">
                           <div class="card ">

                               <ul class="list-group list-group-flush">
                                   <li class="list-group-item"><i class="icon icon-mobile text-primary"></i><strong class="s-12">Phone</strong> <span class="float-right s-12"><?=$data[0]->phone;?></span></li>
                                   <li class="list-group-item"><i class="icon icon-mail text-success"></i><strong class="s-12">Email</strong> <span class="float-right s-12"><?=$data[0]->email;?></span></li>
                                   <li class="list-group-item"><i class="icon icon-address-card-o text-warning"></i><strong class="s-12">Address</strong> <span class="float-right s-12"><?=$data[0]->city;?>, <?=$data[0]->countary;?></span></li>
                                    <li class="list-group-item"><span class="float-left s-12  profile_detail_about1"> <i class="icon icon-address-card-o text-warning"></i><strong class="s-12">About Me</strong></span> <span class="float-right s-12  profile_detail_about2"><?=$data[0]->aboutMe;?></span> 
                                    </li>
                                   
                               </ul>
                           </div>
                          <!-- product list -->
                            <div class="card mt-3 mb-3  productName_listing_div">
                               

                               <div class="card-header bg-white">
                                   <strong class="card-title">Product</strong>
                               </div>
                               <div>
                                   <ul class="list-group list-group-flush">
                                     <?php for($i=0;$i<sizeof($product);$i++):?>
                                       <li class="list-group-item">
                                           <div class="image mr-3  float-left">
                                               <img class="user_avatar" src="<?= base_url($product[$i]->img) ;?>" alt="User Image">
                                           </div>
                                           <h6 class="p-t-10"><?=$product[$i]->productName;?></h6>
                                           <span><?=$product[$i]->brandName;?></span>
                                       </li>
                                       
                                       <?php endfor;?>
                                   </ul>
                               </div>

                           </div>
                           <!-- end -->
                       </div>
                       <div class="col-md-8">
                        
                           <div class="row">
                               <div class="col-lg-4">
                                   <div class="card r-3">
                                       <div class="p-4">
                                           <div class="float-right">
                                               <span class="icon-award text-light-blue s-48"></span>
                                           </div>
                                           <div class="counter-title">Total Patient</div>
                                           <h5 class="sc-counter mt-3"><?=$count;?></h5>
                                       </div>
                                   </div>
                               </div>
                               <div class="col-lg-4">
                                   <div class="card r-3">
                                       <div class="p-4">
                                           <div class="float-right"><span class="icon-stop-watch3 s-48"></span>
                                           </div>
                                           <div class="counter-title ">Experience</div>
                                           <h5 class="sc-counter mt-3"><?=$data[0]->yearOfExperience;?></h5>
                                       </div>
                                   </div>
                               </div>
                               <div class="col-lg-4">
                                   <div class="white card">
                                       <div class="p-4">
                                           <div class="float-right"><span class="icon-orders s-48"></span>
                                           </div>
                                           <div class="counter-title">Product</div>
                                           <h5 class="sc-counter mt-3"><?=$count[0];?></h5>
                                       </div>
                                   </div>
                               </div>
                           </div>

                           <div class="row my-3">
                               <!-- bar charts group -->
                               <div class="col-md-12">
                                   <div class="card">
                                      
          <div class="tab_inner_div">
            <div class="row">
              <div class="col-md-3" style="padding-right:  0px;">
                <div class="img_video_left">
               <button id="images_div_button">Images</button>
          <button id="video_div_button">Videos</button>
            </div>
              </div>

               <div class="col-md-9" style="padding-left:  0;">
                  <div class="images_div">

                  <button class="tablink" onclick="openPage('forhead', this)" id="defaultOpen">Body</button>
                  <button class="tablink" onclick="openPage('eyes', this)" >Eyes</button>
                  <button class="tablink" onclick="openPage('neck', this)">Neck</button>
                  <button class="tablink" onclick="openPage('allface', this )">Allface</button>

                  <div  class="tabcontent">
                   <div class="row"  id="div_image">
                    
                   </div>
                   <div class="tab_pagination "> 
                    <div class="pagination-link ajaxpage">
                    
                   </div></div>
                   
                  </div>

                  
                </div>
                 <div class="video_div">

                <button class="tablink" onclick="openPage2('forhead', this)" id="defaultOpen2">Body</button>
                  <button class="tablink" onclick="openPage2('eyes', this)" >Eyes</button>
                  <button class="tablink" onclick="openPage2('neck', this)">Neck</button>
                  <button class="tablink" onclick="openPage2('allface', this )">Allface</button>

                  <div  class="tabcontent2">
                   
                    <div class="row" id="div_video">
                    

                  </div>
                  <div class="tab_pagination"> 
                    <div class="pagination-link ajaxpage" >
                    
                   </div></div>
                  </div>

                  
                </div>
                </div>
          </div>
        </div>
                                   </div>
                               </div>
                               <!-- /bar charts group -->


                           </div>
                        
                       </div>
                   </div>
               </div>
               <div class="tab-pane fade" id="v-pills-payments" role="tabpanel" aria-labelledby="v-pills-payments-tab">
                   <div class="row">
                       <div class="col-md-12">
                           <div class="card no-b">
                               <div class="card-header white b-0 p-3">
                                   <h4 class="card-title">Invoices</h4>
                                   <small class="card-subtitle mb-2 text-muted">Items purchase by users.</small>
                               </div>
                               <div class="collapse show" id="invoiceCard">
                                   <div class="card-body p-0">
                                       <div class="table-responsive">
                                           <table id="recent-orders"
                                                  class="table table-hover mb-0 ps-container ps-theme-default">
                                               <thead class="bg-light">
                                               <tr>
                                                   <th>SKU</th>
                                                   <th>Invoice#</th>
                                                   <th>Customer Name</th>
                                                   <th>Status</th>
                                                   <th>Amount</th>
                                               </tr>
                                               </thead>
                                               <tbody>
                                               <tr>
                                                   <td>PAP-10521</td>
                                                   <td><a href="#">INV-281281</a></td>
                                                   <td>Baja Khan</td>
                                                   <td><span class="badge badge-success">Paid</span></td>
                                                   <td>$ 1228.28</td>
                                               </tr>
                                               <tr>
                                                   <td>PAP-532521</td>
                                                   <td><a href="#">INV-01112</a></td>
                                                   <td>Khan Sab</td>
                                                   <td><span class="badge badge-warning">Overdue</span>
                                                   </td>
                                                   <td>$ 5685.28</td>
                                               </tr>
                                               <tr>
                                                   <td>PAP-05521</td>
                                                   <td><a href="#">INV-281012</a></td>
                                                   <td>Bin Ladin</td>
                                                   <td><span class="badge badge-success">Paid</span></td>
                                                   <td>$ 152.28</td>
                                               </tr>
                                               <tr>
                                                   <td>PAP-15521</td>
                                                   <td><a href="#">INV-281401</a></td>
                                                   <td>Zoor Shoor</td>
                                                   <td><span class="badge badge-success">Paid</span></td>
                                                   <td>$ 1450.28</td>
                                               </tr>
                                               <tr>
                                                   <td>PAP-532521</td>
                                                   <td><a href="#">INV-01112</a></td>
                                                   <td>Khan Sab</td>
                                                   <td><span class="badge badge-warning">Overdue</span>
                                                   </td>
                                                   <td>$ 5685.28</td>
                                               </tr>
                                               <tr>
                                                   <td>PAP-05521</td>
                                                   <td><a href="#">INV-281012</a></td>
                                                   <td>Bin Ladin</td>
                                                   <td><span class="badge badge-success">Paid</span></td>
                                                   <td>$ 152.28</td>
                                               </tr>
                                               <tr>
                                                   <td>PAP-15521</td>
                                                   <td><a href="#">INV-281401</a></td>
                                                   <td>Zoor Shoor</td>
                                                   <td><span class="badge badge-success">Paid</span></td>
                                                   <td>$ 1450.28</td>
                                               </tr>
                                               <tr>
                                                   <td>PAP-32521</td>
                                                   <td><a href="#">INV-288101</a></td>
                                                   <td>Walter R.</td>
                                                   <td><span class="badge badge-warning">Overdue</span>
                                                   </td>
                                                   <td>$ 685.28</td>
                                               </tr>
                                               </tbody>
                                           </table>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>

               </div>
               <div class="tab-pane fade" id="v-pills-timeline" role="tabpanel" aria-labelledby="v-pills-timeline-tab">

                   <div class="row">
                       <div class="col-md-12">
                           <!-- The time line -->
                           <ul class="timeline">
                               <!-- timeline time label -->
                               <li class="time-label">
                  <span class="badge badge-danger r-3">
                    10 Feb. 2014
                  </span>
                               </li>
                               <!-- /.timeline-label -->
                               <!-- timeline item -->
                               <li>
                                   <i class="ion icon-envelope bg-primary"></i>
                                   <div class="timeline-item card">
                                       <div class="card-header white"><a href="#">Support Team</a> sent you an email    <span class="time float-right"><i class="ion icon-clock-o"></i> 12:05</span></div>
                                       <div class="card-body">
                                           Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                                           weebly ning heekya handango imeem plugg dopplr jibjab, movity
                                           jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                                           quora plaxo ideeli hulu weebly balihoo...
                                       </div>
                                       <div class="card-footer">
                                           <a class="btn btn-primary btn-xs">Read more</a>
                                           <a class="btn btn-danger btn-xs">Delete</a>
                                       </div>
                                   </div>
                               </li>
                               <!-- END timeline item -->
                               <!-- timeline item -->
                               <li>
                                   <i class="ion icon-user yellow"></i>

                                   <div class="timeline-item  card">

                                       <div class="card-header white"><h6><a href="#">Sarah Young</a> accepted your friend request<span class="float-right"><i class="ion icon-clock-o"></i> 5 mins ago</span></h6></div>


                                   </div>
                               </li>
                               <!-- END timeline item -->
                               <!-- timeline item -->
                               <li>
                                   <i class="ion icon-comments bg-danger"></i>

                                   <div class="timeline-item  card">


                                       <div class="card-header white"><h6><a href="#">Jay White</a> commented on your post   <span class="float-right"><i class="ion icon-clock-o"></i> 27 mins ago</span></h6></div>

                                       <div class="card-body">
                                           Take me to your leader!
                                           Switzerland is small and neutral!
                                           We are more like Germany, ambitious and misunderstood!
                                       </div>
                                       <div class="card-footer">
                                           <a class="btn btn-warning btn-flat btn-xs">View comment</a>
                                       </div>
                                   </div>
                               </li>
                               <!-- END timeline item -->
                               <!-- timeline time label -->
                               <li class="time-label">
                  <span class="badge badge-success r-3">
                    3 Jan. 2014
                  </span>
                               </li>
                               <!-- /.timeline-label -->
                               <!-- timeline item -->
                               <li>
                                   <i class="ion icon-camera indigo"></i>

                                   <div class="timeline-item  card">

                                       <div class="card-header white"><a href="#">Mina Lee</a> uploaded new photos<span class="time float-right"><i class="ion icon-clock-o"></i> 2 days ago</span></div>


                                       <div class="card-body">
                                           <img src="http://placehold.it/150x100" alt="..." class="margin">
                                           <img src="http://placehold.it/150x100" alt="..." class="margin">
                                           <img src="http://placehold.it/150x100" alt="..." class="margin">
                                           <img src="http://placehold.it/150x100" alt="..." class="margin">
                                       </div>
                                   </div>
                               </li>
                               <!-- END timeline item -->
                               <!-- timeline item -->
                               <li>
                                   <i class="ion icon-video-camera bg-maroon"></i>

                                   <div class="timeline-item  card">
                                       <div class="card-header white"><a href="#">Mr. Doe</a> shared a video<span class="time float-right"><i class="ion icon-clock-o"></i> 5 days ago</span></div>


                                       <div class="card-body">
                                           <div class="embed-responsive embed-responsive-16by9">
                                               <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/tMWkeBIohBs" allowfullscreen="" frameborder="0"></iframe>
                                           </div>
                                       </div>
                                       <div class="card-footer">
                                           <a href="#" class="btn btn-xs bg-maroon">See comments</a>
                                       </div>
                                   </div>
                               </li>
                               <!-- END timeline item -->
                               <li>
                                   <i class="ion icon-clock-o bg-gray"></i>
                               </li>
                           </ul>
                       </div>
                       <!-- /.col -->
                   </div>
               </div>
               <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                   <form class="form-horizontal">
                       <div class="form-group">
                           <label for="inputName" class="col-sm-2 control-label">Name</label>

                           <div class="col-sm-10">
                               <input class="form-control" id="inputName" placeholder="Name" type="email">
                           </div>
                       </div>
                       <div class="form-group">
                           <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                           <div class="col-sm-10">
                               <input class="form-control" id="inputEmail" placeholder="Email" type="email">
                           </div>
                       </div>
                       <div class="form-group">
                           <label for="inputName" class="col-sm-2 control-label">Name</label>

                           <div class="col-sm-10">
                               <input class="form-control" id="inputName" placeholder="Name" type="text">
                           </div>
                       </div>
                       <div class="form-group">
                           <label for="inputExperience" class="col-sm-2 control-label">Experience</label>

                           <div class="col-sm-10">
                               <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                           </div>
                       </div>
                       <div class="form-group">
                           <label for="inputSkills" class="col-sm-2 control-label">Skills</label>

                           <div class="col-sm-10">
                               <input class="form-control" id="inputSkills" placeholder="Skills" type="text">
                           </div>
                       </div>
                       <div class="form-group">
                           <div class="col-sm-offset-2 col-sm-10">
                               <div class="checkbox">
                                   <label>
                                       <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                                   </label>
                               </div>
                           </div>
                       </div>
                       <div class="form-group">
                           <div class="col-sm-offset-2 col-sm-10">
                               <button type="submit" class="btn btn-danger">Submit</button>
                           </div>
                       </div>
                   </form>
               </div>
           </div>
       </div>
        </div>
</div>

<?php $this->load->view('footer')?>

<script>
 
$(document).ready(function(){
  $("#images_div_button").click(function(){
    $( "#images_div_button" ).addClass( "selected_img" );
    $( "#video_div_button" ).removeClass( "selected_video" );
     $("#div_image").html('');
     $('.ajaxpage').html("");
     $(".video_div").hide();
    $(".images_div").show();
     $.ajax({
                type: "post",
                url: "<?php echo base_url('index.php/user/showgallery'); ?>",
                dataType: 'json',
                data:{userId:$('#userId').val(),datatype:"image",search:"eyes"},
                
                success: function(res) {
                  if(res['response']=="true"){
               
              $.each( res['data'], function( key, value ) {
                
                  $("#div_image").append('<div class="col-md-4">'
                     
                     +' <div class="tab_inner_img_div"><img src="<?php echo base_url(); ?>'+value['media']+'" alt=""></div>'
                    +'</div>');

                    });
              $.each(res['page'], function( key, value ) {  

                $('.ajaxpage').append(value);    

                  })

                }else{

                    $("#div_image").append('<div class="col-md-12"><h4> No Data </h4></div>');

                }

                  

                    }

                })
    
  });
  $("#video_div_button").click(function(){
    $( "#video_div_button" ).addClass( "selected_video" );
    $( "#images_div_button" ).removeClass( "selected_img" );
    $("#div_video").html('');
    $('.ajaxpage').html("");
     $(".video_div").show();
    $(".images_div").hide();
      $.ajax({
                type: "post",
                url: "<?php echo base_url('index.php/user/showgallery'); ?>",
                dataType: 'json',
                data:{userId:$('#userId').val(),datatype:"video",search:"eyes"},
                
                success: function(res) {
                  console.log(res);
                    if(res['response']=="true"){
               
              $.each( res['data'], function( key, value ) {
                
                  $("#div_video").append('<div class="col-md-6">'
                    +' <div class="tab_inner_video_div"><video width="400" controls>'
                          +'  <source src="<?php echo base_url(); ?>'+value['media']+'" type="video/mp4">'
                           
                         +' </video></div>'
                    +'</div>');

                    });
              $.each(res['page'], function( key, value ) {  

                $('.ajaxpage').append(value);    

                  })


                }else{

                    $("#div_video").append('<div class="col-md-12"><h4> No Data </h4></div>');

                }
                    }
                   
                })


    
  });
});
</script>
<script>
function openPage(pageName,elmnt) {
  //alert(pageName);
  $('.ajaxpage').html("");
   $("#div_image").html('');
    $( "#images_div_button" ).addClass( "selected_img" );
    $( "#video_div_button" ).removeClass( "selected_video" );
     $.ajax({
                type: "post",
                url: "<?php echo base_url('index.php/user/showgallery'); ?>",
                dataType: 'json',
                data:{userId:$('#userId').val(),datatype:"image",search:pageName},
                
                success: function(res) {
                    console.log(res['response']);
              if(res['response']=="true"){
               
              $.each( res['data'], function( key, value ) {
                
                  $("#div_image").append('<div class="col-md-4">'
                     
                     +' <div class="tab_inner_img_div"><img src="<?php echo base_url(); ?>'+value['media']+'" alt=""></div>'
                    +'</div>');

                    });
              $.each(res['page'], function( key, value ) {  

                $('.ajaxpage').append(value);    

                  })

                }else{

                    $("#div_image").append('<div class="col-md-12"><h4> No Data </h4></div>');

                }

      }
  })
}

document.getElementById("defaultOpen").click();
</script>
 <script>
function openPage2(pageName,elmnt) {
  $('.ajaxpage').html("");
   $("#div_video").html('');
   $.ajax({
                type: "post",
                url: "<?php echo base_url('index.php/user/showgallery'); ?>",
                dataType: 'json',
                data:{userId:$('#userId').val(),datatype:"video",search:pageName},
                
                success: function(res) {
                  console.log(res);
                  if(res['response']=="true"){
               
              $.each( res['data'], function( key, value ) {
                
                  $("#div_video").append('<div class="col-md-6">'
                    +'<div class="tab_inner_img_div"> <video width="400" controls>'
                          +'  <source src="<?php echo base_url(); ?>'+value['media']+'" type="video/mp4">'
                           
                         +' </video></div>'
                    +'</div>');

                    });
              $.each(res['page'], function( key, value ) {  

                $('.ajaxpage').append(value);    

                  })

                }else{

                    $("#div_video").append('<div class="col-md-12"><h4> No Data </h4></div>');

                }

                    }

                })

}
function openPageajax(siteurl,type,search){
           $('.ajaxpage').html("");
         $("#div_image").html('');
          $( "#images_div_button" ).addClass( "selected_img" );
          $( "#video_div_button" ).removeClass( "selected_video" );
      if(type=='image'){

   
       $.ajax({
                type: "post",
                url:siteurl,
                dataType: 'json',
                data:{userId:$('#userId').val(),datatype:"image",search:search},
                
                success: function(res) {
                    console.log(res['response']);
              if(res['response']=="true"){
               
              $.each( res['data'], function( key, value ) {
                
                  $("#div_image").append('<div class="col-md-4">'
                     
                     +' <div class="tab_inner_img_div"><img src="<?php echo base_url(); ?>'+value['media']+'" alt=""></div>'
                    +'</div>');

                    });
              $.each(res['page'], function( key, value ) {  

                $('.ajaxpage').append(value);    

                  })

                }else{

                    $("#div_image").append('<div class="col-md-12"><h4> No Data </h4></div>');

                }

      }
  })

      }else{

                  $('.ajaxpage').html("");
                 $("#div_video").html('');
                 $.ajax({
                              type: "post",
                              url:siteurl ,
                              dataType: 'json',
                              data:{userId:$('#userId').val(),datatype:"video",search:search},
                        
                        success: function(res) {
                          console.log(res);
                          if(res['response']=="true"){
                       
                      $.each( res['data'], function( key, value ) {
                        
                          $("#div_video").append('<div class="col-md-6">'
                            +'<div class="tab_inner_img_div"> <video width="400" controls>'
                                  +'  <source src="<?php echo base_url(); ?>'+value['media']+'" type="video/mp4">'
                                   
                                 +' </video></div>'
                            +'</div>');

                            });
                      $.each(res['page'], function( key, value ) {  

                        $('.ajaxpage').append(value);    

                          })

                }else{

                    $("#div_video").append('<div class="col-md-12"><h4> No Data </h4></div>');

                }

                    }

                })





      }






}
// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen2").click();
</script>  

<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>
