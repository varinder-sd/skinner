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
                       Specialist
                    </h4>
                    <span style="float:right;"><form method="post" action="<? echo base_url('/index.php/user/searchbyspecialist')?>"><input type="text" name="search"><button type="submit">Search</button><a href="<?=base_url('/index.php/user/specialist')?>" style="margin-left: 14px;">back</a></span>
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
                                 <?php if($sepcialuserlist==1){
                                      echo "<h3>No Data Match !</h3>";
                                    }else{ ?>
                                <form>
                                    <table class="table table-striped table-hover r-0">
                                        <thead>
                                        <tr class="no-b">
                                            
                                            <th>USER NAME</th>
                                            <th>AGE</th>
                                            <th>CITY</th>
                                            <th>COUNTARY</th>
                                            <th>PHONE</th>
                                            <th>STATUS</th>
                                            <th>LAST LOGIN</th>
                                            <th>YEAR OF EXPERIENCE</th>
                                            <th>Total Product Of User</th>
                                            <th></th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                      <?php for($i=0;$i<sizeof($sepcialuserlist);$i++):?>
                                        <tr>
                                           
                                           
                                            
                                            <td>
                                                <div class="avatar avatar-md mr-3 mt-1 float-left">
                                                    <span class="avatar-letter avatar-letter-a  avatar-md circle"><img src="<?= base_url($sepcialuserlist[$i]->proPicName) ;?>" alt=""></span>
                                                </div>
                                                <div>
                                                    <div>
                                                        <strong><?=$sepcialuserlist[$i]->userName?></strong>
                                                    </div>
                                                    <small> <?=$sepcialuserlist[$i]->email ?></small>
                                                </div>
                                            </td>


                                            <td><?=$sepcialuserlist[$i]->age ?></td>
                                            <td><?=$sepcialuserlist[$i]->city ?></td>
                                            <td><?=$sepcialuserlist[$i]->countary ?></td>
                                            <td><?=$sepcialuserlist[$i]->phone ?></td>
                                            <td><?php if($sepcialuserlist[$i]->status== 1):?>
                                              <span class="icon icon-circle s-12  mr-2 text-success"></span> Active
                                              <?php else :?>
                                            <span class="icon icon-circle s-12  mr-2 text-warning"></span> Inactive <?php endif ;?></td>
                                            <td><?=$sepcialuserlist[$i]->lastLogin ?></td>
                                            <td><?=$sepcialuserlist[$i]->yearOfExperience ?></td>
                                            <td><?=$sepcialuserlist[$i]->total ?></td>





                                            
                                            <td>
                                                <a href="javascript:specialistdelete(<?=$sepcialuserlist[$i]->userId ?>)" style="margin-right: 11px;" >
                                                  <i class="icon-close2 text-danger-o text-danger"></i>
                                                <a href="javascript:showProfile(<?php echo $sepcialuserlist[$i]->userId ;?>)"  class="view_data" ><i class="icon-eye mr-3"></i></a>
                                                <a href="<?= base_url('index.php/user/editprofile?id='.$sepcialuserlist[$i]->userId.'&data=sepcial_users') ?>"><i class="icon-pencil"></i></a>
                                            </td>
                                        </tr>
                                        
                                       <?php endfor;?>
                                       </tbody>
                                    </table>
                                </form>
                            </div>
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
<div id="showprofile" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background: #5ecaca;">
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


<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>