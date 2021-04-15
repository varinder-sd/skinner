<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>

<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                  <h4 style="float: left;">
                        <i class="icon-box"></i>
                        Patient
                    </h4>
                   <span style="float:right;"><form method="post" action="<? echo base_url('/index.php/user/searchbypatient')?>"><input type="text" name="search"><button type="submit">Search</button><a href="<?=base_url('/index.php/user/patientlist')?>" style="margin-left: 14px;">back</a></span>
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
                               <?php if($patient==1){
                                      echo "<h3>No Data Match !</h3>";
                                    }else{ ?>
                                <form>
                                    <table class="table table-striped table-hover r-0">
                                        <thead>
                                        <tr class="no-b">
                                            
                                            <th>USER NAME</th>
                                            <th>AGE</th>
                                            <th>PHONE</th>
                                            <th>STATUS</th>
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
                                                    <span class="avatar-letter avatar-letter-s  avatar-md circle"><img src="<?base_url($patient[$i]->proPicName)?>" alt=""></span>
                                                </div>
                                                <div>
                                                    <div>
                                                        <strong><?=$patient[$i]->userName?></strong>
                                                    </div>
                                                    <small> <?=$patient[$i]->email ?></small>
                                                </div>
                                            </td>


                                            <td><?=$patient[$i]->age ?></td>
                                            
                                            <td><?=$patient[$i]->phone ?></td>
                                            <td><?php if($patient[$i]->status== 1):?>
                                              <span class="icon icon-circle s-12  mr-2 text-success"></span> Active
                                              <?php else :?>
                                            <span class="icon icon-circle s-12  mr-2 text-warning"></span> Inactive <?php endif ;?></td>
                                            <td><?=$patient[$i]->lastLogin ?></td>
                                            <td><?=$patient[$i]->concernType ?></td>
                                            <td><?=$patient[$i]->specialist ?></td>
                                           




                                            
                                            <td>
                                                <a href="javascript:patientdelete(<?=$patient[$i]->userId ?>)" style="margin-right: 11px;" >
                                                  <i class="icon-close2 text-danger-o text-danger"></i>
                                                </a>
                                                <a href="javascript:showprofile(<?php echo $patient[$i]->userId ;?>)"  class="view_data" ><i class="icon-eye mr-3"></i></a>
                                                <a href="<?= base_url('index.php/user/editprofile?id='.$patient[$i]->userId.'&data=user_patient') ?>"><i class="icon-pencil"></i></a>
                                            </td>
                                        </tr>
                                        
                                       <?php endfor;?>
                                       </tbody>
                                    </table>
                                </form>
                                <?php }  ?>
                            </div>

                        </div>
                    </div>
                </div>

               
                    
                       
                       
                        
                      
                    
                
            </div>
           
            
            </div>
        </div>
    </div>


<!-- Modal -->
<div id="Dshowprofile" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background: #5ecaca;">
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
  //  alert(data);
    $("#Dtablebody").html(" ");
     jQuery.ajax({
                type: "post",
                url: "<?php echo base_url(); ?>" + "index.php/user/showProfile1",
               dataType: 'json',
                data: {id: id, data:"user_patient"},
                success: function(res) {
                    
                      //alert(res[0]['productId']);
                        console.log(res);
                 $("#Dtablebody").append('<div class="container-fluid relative animatedParent animateOnce">'
        +'<div class="card-box col-sm-12">'
             +'<div class="row">'
                +' <div class="col-sm-2"><img src="http://leaselance.com/skinnerapp/assets/profilepic/'+res[0]['proPicName']+'" alt=""></div>'
                +' <div class="col-sm-10">'
                  +'   <h1>'+res[0]['userName']+'</h1>'
                    
                    +' <p><a href="<?php echo base_url(); ?>'+'index.php/user/patientuserproduct?id='+res[0]['userId']+'&name='+res[0]['userName']+'" title="my product detail" target="_blank">My Product</a></p>'
                 +'</div>'
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