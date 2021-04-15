<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){




  ?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>

<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 " style="padding-top:0px !important;">
                <div class="col">
                  <h4 style="float: left;">
                        <i class="icon-box"></i>
                        Patient
                    </h4>
                   <form method="post" action="<? echo base_url('/index.php/user/searchbypatient')?>"><span style="float:right;"  class="skinnerapp_search" ><input type="text" name="search" placeholder="Search"><button type="submit">Search</button></span></form>
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
                                            
                                            <th>USER NAME</th>
                                            <th>AGE</th>
                                            <th>PHONE</th>
                                            <!-- <th>STATUS</th> -->
                                            <th>LAST LOGIN</th> 
                                            <th>Concern Type</th> 
                                            <th>Specialist</th> 
                                            <th></th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                      <?php for($i=0;$i<sizeof($patient);$i++):?>
                                        <tr>
                                            

                                            <td>
                                                <div class="avatar avatar-md mr-3 mt-1 float-left">
                                                    <span class="avatar-letter avatar-letter-a  avatar-md circle"><img src="<?=base_url($patient[$i]['data']->proPicName);?>" alt=""></span>
                                                </div>
												<div style="clear:both;"></div>
                                                <div>
                                                    <div class="username_div">
                                                        <strong><?=$patient[$i]['data']->userName?></strong>
                                                    </div>
                                                    <div style="text-align:center;"> <?=$patient[$i]['data']->email ?></div>
                                                </div>
                                            </td>


                                            <td><?=$patient[$i]['data']->age ?></td>
                                            
                                            <td><?=$patient[$i]['data']->phone ?></td>
                                            <!-- <td><?php if($patient[$i]['data']->status== 1):?>
                                              <span class="icon icon-circle s-12  mr-2 text-success"></span> Active
                                              <?php else :?>
                                            <span class="icon icon-circle s-12  mr-2 text-warning"></span> Inactive <?php endif ;?></td> -->
                                            <td><?=$patient[$i]['data']->lastLogin ?></td>
                                            <td><?=$patient[$i]['concern'] ?></td>
                                            <td><?=$patient[$i]['specialist'] ?></td>
                                           




                                            
                                            <td>
                                                <a href="javascript:patientdelete(<?=$patient[$i]['data']->userId ?>)" style="margin-right: 11px;" >
                                                  <i class="icon-close2 text-danger-o text-danger"></i>
                                                </a>
                                                <a href="javascript:showprofile(<?php echo $patient[$i]['data']->userId ;?>)"  class="view_data" ><i class="icon-eye mr-3"></i></a>
                                                <a href="<?= base_url('index.php/user/editprofile?id='.$patient[$i]['data']->userId.'&data=user_patient') ?>"><i class="icon-pencil"></i></a>
                                            </td>
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


<!-- Modal -->
<div id="Dshowprofile" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background: #2979ff;">
        <h3 style="color:white;"> User Profile</h3>
        
      </div>
      <div class="modal-body ">
        <div id="Dtablebody">
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<script>
   
function patientdelete(id) {
  // alert(id);
  
   var r = confirm("Are You Sure to Delete!");
    if (r == true) {
       
      window.location.href = '<?php echo base_url("index.php/user/patientdelete?id=") ?>'+id;
    } else {
      
       window.location.href = '<?php echo base_url("index.php/user/patientlist") ?>';
    } 
    
    
}

function showprofile(id){
   //alert(id);
    $("#Dtablebody").html(" ");
     jQuery.ajax({
                type: "get",
                url: "<?php echo base_url(); ?>" + "index.php/user/showProfile2",
               dataType: 'json',
                data: {id: id, data:"user_patient"},
                success: function(res) {
                    
                      //alert(res[0]['productId']);
                        console.log(res);
                 $("#Dtablebody").append('<div class="container-fluid relative animatedParent animateOnce">'
        +'<div class="card-box">'
		+'<div class="row">'
		+'<div class="col-md-2"><div class="user_detail_img" style="margin-top: 10px;">'
			  +'<img src="<?=base_url()?>'+res[0]['proPicName']+'"/>'
		+'</div> </div>'
    +'<div class="col-md-8">'
        +'   <h1 style="padding:20px 0px 0;margin:0;">'+res[0]['userName']+'</h1>'
    +'</div>'
		+'</div>'
             
				  +'<div class="col-md-12">'
				 +'<div class="row specialist_popup_detail">'
				 +' <p class="col-sm-6 specialist_popup"><label>Email :</label>'+res[0]['email']+'</p>'
                     +' <p class="col-sm-6 specialist_popup"><label>Age :</label> '+res[0]['age']+'</p>'
					 +' <p class="col-sm-6 specialist_popup1"><label>City : </label> '+res[0]['city']+'</p>'
					 +' <p class="col-sm-6 specialist_popup1"><label> Countary:</label>'+res[0]['countary']+'</p>'
					 
					 +' <p class="col-sm-6 specialist_popup"><label>Phone :</label>'+res[0]['phone']+'</p>'
					 +' <p class="col-sm-6 specialist_popup"><label>Specialist :</label>'+res[0]['specialist']+'</p>'
					 
					 +' <p class="col-sm-6 specialist_popup1"><label>Concern Type :</label>'+res[0]['concernType']+'</p>'
            +' <p class="col-sm-6 specialist_popup1"></p>'        
				
				
				  +'<div class="col-md-12 specialist_popup">'
				 +' <p class="specialist_popup_submit"><a href="<?php echo base_url(); ?>'+'index.php/user/patientuserproduct?id='+res[0]['userId']+'&name='+res[0]['userName']+'" title="my product detail" target="_blank">My Product</a></p>'
           +'  </div>'
		    +'</div>'
		    +'  </div>'
				 
				 
           +'  </div>'
        +' </div>'
     +'</div>');
                  $('#Dshowprofile').modal({show:true});
                    
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });
      //jQuery.('#myModal').modal('show');
          // jQuery.('#myModal').modal('show');



  }
</script>

   



<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>