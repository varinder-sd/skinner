<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>
<style>
ul.area-wrap {
display: inline-block;
padding: 0;
width: 100%;
}
ul.area-wrap li{
float: left;
padding: 10px 20px;
background: #ebebeb;
border:1px solid #e0e0e0;
border-radius: 2px;
margin-right: 20px;
list-style:none;
}
.img-wrap img{
width: 100px;height: 100px;
border-radius:50%;
border:2px solid #6bafdc;
}
.area-section{ border-bottom: 1px solid #eee; padding-bottom: 30px; }
.area-section h3{ margin-bottom: 30px; }
.ch-value{ padding-left: 10px; }
.ch-value label{ padding-left: 15px; font-weight: 400; }
.container{    margin-left: 19px;    margin-top: 6px;}
</style>
<div class="page has-sidebar-left">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h3>
                        
                      PRODUCT INSTRUCTION
                    </h3>
                </div>
            </div>
            
        </div>
    </header>
   <!--  <div class="container-fluid animatedParent animateOnce my-3">
        <div class="animated fadeInUpShort">
            <div class="row">
                <div class="col-md-12">
                    <div class="card no-b shadow">
                        <div class="card-body p-0"> -->
                            <!-- <div class="table-responsive"> -->
                                <div class="row area-section" >
                                  <div class="container">
                                  <h3>Area of use</h3>
                                  <div class="col-sm-2 img-wrap">
                                 <!--  <img src="resume_photo.jpg"> -->
                                  </div>
                                  <div class="col-sm-10">
                                  <ul class="area-wrap row">
                                  <li class="col-sm-3">
                                  <input type="checkbox" />
                                  <span class="ch-value">
                                  <label>Eyes</label>
                                  </span>
                                  </li>
                                  <li class="col-sm-3">
                                  <input type="checkbox" />
                                  <span class="ch-value">
                                  <label>Forhead</label>
                                  </span>
                                  </li>
                                  <li class="col-sm-3">
                                  <input type="checkbox" />
                                  <span class="ch-value">
                                  <label>Neck</label>
                                  </span>
                                  </li>
                                  </ul>
                                  <ul class="area-wrap row">
                                  <li class="col-sm-3">
                                  <input type="checkbox" />
                                  <span class="ch-value">
                                  <label>Eyes</label>
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
                                  <input type="checkbox" />
                                  <span class="ch-value">
                                  <label>Eyes</label>
                                  </span>
                                  </li>
                                  <li class="col-sm-3">
                                  <input type="checkbox" />
                                  <span class="ch-value">
                                  <label>Forhead</label>
                                  </span>
                                  </li>
                                  <li class="col-sm-3">
                                  <input type="checkbox" />
                                  <span class="ch-value">
                                  <label>Neck</label>
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
                                  <textarea class="form-control rounded-0" id="" rows="10" placeholder="Type your instruction here"></textarea>

                                  <span class="">
                                  <input type="checkbox" />
                                  <span class="">
                                  <label>AM</label>
                                  </span>
                                  </span>
                                  <span class="">
                                  <input type="checkbox" />
                                  <span class="">
                                  <label>PM</label>
                                  </span>
                                  </span>
                                  </div>
                                  </div>
                                  </div>
                            <!-- </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div> -->
</div>



 
<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>

  
