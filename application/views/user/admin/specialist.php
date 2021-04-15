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
                       Specialist
                    </h4>
                    <form method="post" action="<? echo base_url('/index.php/user/searchbyspecialist')?>"><span style="float:right; " class="skinnerapp_search"><input type="text" name="search" placeholder="Search"><button type="submit">Search</button></span></form>
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
                                            <th>CITY</th>
                                            <th>COUNTARY</th>
                                            <th>PHONE</th>
                                            <!-- <th>STATUS</th> -->
                                            <th>LAST LOGIN</th>
                                            <th>EXPERIENCE</th>
                                            <th>COUNT PRODUCT</th>
											
                                            <th></th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                      <?php for($i=0;$i<sizeof($sepcialuserlist);$i++):?>
                                        <tr>
                                           
                                           
                                            
                                            <td>
                                                <div class="avatar avatar-md mr-3 mt-1 float-left user_name">
                                                    <span class="avatar-letter avatar-letter-a  avatar-md circle"><img src="<?= base_url($sepcialuserlist[$i]->proPicName) ;?>" alt=""></span>
                                                </div>
                                              <div style="clear:both;"></div>
                                                <div>
                                                    <div class="username_div">
                                                        <?=$sepcialuserlist[$i]->userName?>
                                                    </div>
                                                    <div style="padding-left: 42px;"> <?=$sepcialuserlist[$i]->email ?></div>
                                                </div>
                                            </td>


                                            <td><?=$sepcialuserlist[$i]->age ?></td>
                                            <td style="text-align:center;"><?=$sepcialuserlist[$i]->city ?></td>
                                            <td><?=$sepcialuserlist[$i]->countary ?></td>
                                            <td><?=$sepcialuserlist[$i]->phone ?></td>
                                            <!-- <td><?php if($sepcialuserlist[$i]->status== 1):?>
                                              <span class="icon icon-circle s-12  mr-2 text-success"></span> Active
                                              <?php else :?>
                                            <span class="icon icon-circle s-12  mr-2 text-warning"></span> Inactive <?php endif ;?></td> -->
                                            <td><?=$sepcialuserlist[$i]->lastLogin ?></td>
                                            <td style="text-align:center;"><?=$sepcialuserlist[$i]->yearOfExperience ?></td>
                                            <td style="text-align:center;"><?=$sepcialuserlist[$i]->total ?></td>

										


                                            
                                            <td>
											<a href="<?= base_url('index.php/user/gallery?id='.$sepcialuserlist[$i]->userId) ?>"><span class="gallery2"><i  title="Gallery" class="icon-file-picture-o"></i></span></a>
                                                <a title="Delete" href="javascript:specialistdelete(<?=$sepcialuserlist[$i]->userId ?>)" style="margin-right: 11px;" >
                                                  <i class="icon-close2 text-danger-o text-danger"></i></a>
                                                <a  title="View" href="<?= base_url('index.php/user/showProfile1?count='.$sepcialuserlist[$i]->total.'&id='.$sepcialuserlist[$i]->userId.'&data=sepcial_users') ?>"  class="view_data"  target="_blank"><i class="icon-eye mr-3"></i></a>
                                                <a  title="Edit" href="<?= base_url('index.php/user/editprofile?id='.$sepcialuserlist[$i]->userId.'&data=sepcial_users') ?>"><i class="icon-pencil"></i></a>
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

<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>