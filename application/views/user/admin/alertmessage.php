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
                        
                       Api Alert Message
                    </h4>
                    
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
                                            
                                            <th>SrNo#</th>
                                            <th>For Api</th>
                                            <th>Message</th>
                                            
											
                                            <th></th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                      <?php $count=1; for($i=0;$i<sizeof($data);$i++):?>
                                        <tr>
                                           
                                            <td><?=$count; $count++; ?></td>
                                            
                                            <td><?=$data[$i]->apiFor ?></td>
                                            <td><?=$data[$i]->message ?></td>
                                            
                                            <td>
											
                                                <a  title="Edit" href="#" onclick="updatemessage('<?=$data[$i]->id ?>','<?=$data[$i]->message ?>');"><i class="icon-pencil"></i></a>
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
            
            </div>
            </div>
        </div>
    </div>

 

<!-- Modal -->
<div id="updatemessage" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      
      <div class="modal-body">
        <div id="form_data"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<?php $this->load->view('footer')?>

<script>
    function updatemessage(id,message) {

        $("#form_data").html(" ");
         $("#form_data").append("<form action='<?= base_url('index.php/user/updatemessage') ?>' method='post'><input type='text' value='"+message+"' name='message'/><input type='hidden' value='"+id+"' name='id'><input type='submit' value='update'></form>");
         $('#updatemessage').modal({show:true});
      
}
    
</script>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>