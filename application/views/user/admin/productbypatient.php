<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>

<div class="page has-sidebar-left">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon-package"></i>
                        Products By Specialist
                    </h4>
                </div>
            </div>
            
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce my-3">
        <div class="animated fadeInUpShort">
            <div class="row">
                <div class="col-md-12">
                    <div class="card no-b shadow">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <tbody>
                                        <?php for($i=0;$i<sizeof($productbypatient);$i++):?>
                                    <tr class="no-b">
                                        <td class="w-10">
                                            <img src="<?base_url($productbypatient[$i]->imgName)?>" alt="">
                                        </td>
                                        <td>
                                            
                                            <small class="text-muted"><?=$productbypatient[$i]->productName?></small><h6><?=$productbypatient[$i]->userName?></h6>
                                        </td>
                                        <td><?=$productbypatient[$i]->productDatial?></td>
                                        <td>
                                        <?=$productbypatient[$i]->createDate?>
                                       </td>
                                        
                                    </tr>
                                   <?php endfor;?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="my-3" aria-label="Page navigation">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link" href="#"><?php echo $this->pagination->create_links();?></a>
                        </li>
                        
                    </ul>
                </nav>
        </div>
    </div>
</div>


   



<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>