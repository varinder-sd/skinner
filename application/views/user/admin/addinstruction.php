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
                        Add Instructon
                    </h4>
                </div>
            </div>
            
            
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce my-3">
        <div class="animated fadeInUpShort">
            <form method="post" action="<?= base_url('index.php/user/addinstruction') ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-12">
                    <div class="card no-b shadow">
                        <div class="card-body p-0">
                            <input type="hidden" name="barcodeId" value="<?=$data[0]->barcodeId ?>">
                            <input type="hidden" name="productName" value="<?=$data[0]->productName ?>">
                            <input type="hidden" name="brandName" value="<?=$data[0]->brandName ?>">
                             <input type="hidden" name="barcodeSrNo" value="<?=$data[0]->barcodeSrNo ?>">
                            <input type="hidden" name="img" value="<?=$data[0]->img ?>">
                                <div class="row area-section">
                                <div class="container">
                                <h3>Area of use</h3>
                                <div class="col-sm-2 img-wrap">
                                <img src="<?=base_url($data[0]->img) ?>">
                                <span>Product Name:<?=$data[0]->productName ?> </span><span>Brand Name:<?=$data[0]->brandName ?></span>
                                </div>
                                <div class="col-sm-10">
                                <ul class="area-wrap row">
                                <li class="col-sm-3">
                                <input type="checkbox" name="eyes" />
                                <span class="ch-value">
                                <label>Eyes</label>
                                </span>
                                </li>
                                <li class="col-sm-3">
                                <input type="checkbox" name="forhead"/>
                                <span class="ch-value">
                                <label>Forhead</label>
                                </span>
                                </li>
                                <li class="col-sm-3">
                                <input type="checkbox" name="neck"/>
                                <span class="ch-value">
                                <label>Neck</label>
                                </span>
                                </li>
                                </ul>
                                <ul class="area-wrap row">
                                <li class="col-sm-3">
                                <input type="checkbox" name="allFace"/>
                                <span class="ch-value">
                                <label>All Face</label>
                                </span>
                                </li>
                                </ul>

                                </div>
                                </div>
                                </div>
                                <div class="row area-section">
                                <div class="container">
                                <h3>Frequncy</h3>

                                <div class="col-sm-12">
                                <ul class="area-wrap row">
                                <li class="col-sm-3">
                                <input type="radio" name="time" value="everyday" checked/>
                                <span class="ch-value">
                                <label>Every Day</label>
                                </span>
                                </li>
                                <li class="col-sm-3">
                                <input type="radio" name="time" value="twiceAweek"/>
                                <span class="ch-value">
                                <label>Twice A Week</label>
                                </span>
                                </li>
                                <li class="col-sm-3">
                                <input type="radio" name="time" value="onceAweek"/>
                                <span class="ch-value">
                                <label>Once A Week</label>
                                </span>
                                </li>
                                </ul>
                                 <ul class="area-wrap row">
                                <li class="col-sm-3">
                                <input type="radio" name="time" value="onceAmonth"/>
                                <span class="ch-value">
                                <label>Once A Month</label>
                                </span>
                                </li>
                                </ul>   
                                </div>
                                </div>
                                </div>

                                <div class="row text-section">
                                <div class="container">
                                <h3>Instruction</h3>

                                <div class="col-sm-12">
                                <textarea class="form-control rounded-0" id="" rows="10" placeholder="Type your instruction here" name="instruction"></textarea>

                                <span class="">
                                <input type="radio" name="inturval" value="am" checked/>
                                <span class="">
                                <label>AM</label>
                                </span>
                                </span>
                                <span class="">
                                <input type="radio" name="inturval" value="pm"/>
                                <span class="">
                                <label>PM</label>
                                </span>
                                </span>
                                <div class="col-sm-12">
                                <span class="">
                                <input type="number" name="minute" />
                                <span class="">
                                <label>Minutes</label>
                                </span>
                                </span>
                                </div>
                                </div>
                                <div class="col-sm-12">
                                
                               <input type="file" name="fileToUpload" accept="video/*">
                              
                                </div>
                                 <div class="col-sm-12"><input type="submit" value="submit"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           </form>
        </div>

    </div>
   
            
</div>





<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>