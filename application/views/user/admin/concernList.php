<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>

<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon-box"></i>
                       Concerns List
                    </h4>
                </div>
            </div>
           
        </div>
    </header>
       <div class="container-fluid animatedParent animateOnce  concerns-list">
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
                                           
                                           
                                            <th>Concern Type</th>
                                            <th>Create By</th>
                                            
                                            <th></th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                      <?php for($i=0;$i<sizeof($concernList);$i++):  ?>
                                        <tr>
                                            
                                            <td><?=$concernList[$i]->concernType?></td>

                                            


                                            <td><?=$concernList[$i]->userName ?></td>
                                            



                                            
                                            <td>
                                                <a href="javascript:concerndelete(<?=$concernList[$i]->concernId ?>)" style="margin-right: 11px;"  >
                                                  <span class="glyphicon glyphicon-trash" title="delete"></span>
                                                </a>

                                                
                                            </td>

                                            <td>
                                                <a href="javascript:deleteconcern(<?=$concernList[$i]->concernId?>)" style="margin-right: 11px;" >
                                                  <i class="icon-close2 text-danger-o text-danger" title="delete"></i>
                                                </a>
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
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
<script>
   
function deleteconcern(id) {
  // alert(id);
  
   var r = confirm("Are You Sure to Delete!");
    if (r == true) {
       
      window.location.href = '<?php echo base_url("index.php/user/concerndelete?id=") ?>'+id;
    } else {
      
      // window.location.href = '<?php echo base_url("index.php/user/concernList") ?>';
    } 
    
    
}
</script>

<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>