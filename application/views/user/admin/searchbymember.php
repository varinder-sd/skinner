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
                        Member
                    </h4>
                   <span style="float:right;"><form method="post" action="<? echo base_url('/index.php/user/searchbymember')?>"><input type="text" name="search"><button type="submit">Search</button><a href="<?=base_url('/index.php/user/subadminlist')?>" style="margin-left: 14px;">back</a></span>
                    
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
                              <?php if($subadminlist==1){
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
                                            <th>ROLE</th>
                                            <th></th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                      <?php for($i=0;$i<sizeof($subadminlist);$i++):

                                       // print_r($subadminlist)?>
                                        <tr>
                                           
                                            <td>
                                                <div class="avatar avatar-md mr-3 mt-1 float-left">
                                                    <span class="avatar-letter avatar-letter-a  avatar-md circle"></span>
                                                </div>
                                                <div>
                                                    <div>
                                                        <strong><?=$subadminlist[$i]->userName?></strong>
                                                    </div>
                                                    <small> <?=$subadminlist[$i]->email?></small>
                                                </div>
                                            </td>

                                             <td><?=$subadminlist[$i]->age?></td>
                                              <td><?=$subadminlist[$i]->city?></td>
                                               <td><?=$subadminlist[$i]->countary?></td>
                                            <td><?=$subadminlist[$i]->phone?></td>
                                            <td><?php if($subadminlist[$i]->status== 1):?>
                                              <span class="icon icon-circle s-12  mr-2 text-success"></span> Active
                                              <?php else :?>
                                            <span class="icon icon-circle s-12  mr-2 text-warning"></span> Inactive <?php endif ;?></td>
                                            


                                              <td><?=$subadminlist[$i]->lastLogin?></td>


                                            <td><?php if($subadminlist[$i]->userRole==1):?><span class="r-3 badge badge-success ">Administrator</span>
                                              <?php else :?>
                                                <span class="r-3 badge badge-warning">Sub-Admin</span> <?php endif ;?>
                                            </td>
                                            <td>
                                                <a href="javascript:subadmindelete(<?=$subadminlist[$i]->userId ?>)" style="margin-right: 11px;" >
                                                  <span class="glyphicon glyphicon-trash"></span>
                                                </a>
                                                <a href="<?= base_url('index.php/user/showProfile?id='.$subadminlist[$i]->userId.'&data=user_admin') ?>"><i class="icon-eye mr-3"></i></a>
                                                <a href="<?= base_url('index.php/user/editprofile?id='.$subadminlist[$i]->userId.'&data=user_admin') ?>"><i class="icon-pencil"></i></a>
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


<script>
   
function subadmindelete(id) {
  // alert(id);
  
   var r = confirm("Are You Sure to Delete!");
    if (r == true) {
       
      window.location.href = '<?php echo base_url("index.php/user/subadmindelete?id=") ?>'+id;
    } else {
      
       window.location.href = '<?php echo base_url("index.php/user/subadminlist") ?>';
    } 
    
    
}
</script>
   



<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>