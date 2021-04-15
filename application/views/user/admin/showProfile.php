<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>	
<?php if (isset($_SESSION['username'])){?>
<?php $this->load->view('header')?>


<div class="page has-sidebar-left height-full">
    
    <div class="container-fluid relative animatedParent animateOnce admin_detail_box">
 		<div class="card-box col-sm-12">
 			<div class="row">
 				<div class="admin_name_mail_div1"><div class="admin_name_mail_div "><img src="<?=base_url($profile[0]->proPicName);?>" alt=""></div></div>
 				<div class="col-sm-10">
 					
 					<h1> <?=$profile[0]->userName ?></h1>
					</div>
					
 			
 				</div>
				<div class="row">
					<div class="col-sm-6">
						<p><label>Email id: </label><?=$profile[0]->email ?></p>
					</div>
					<div class="col-sm-6"><p><label>City: </label> <?=$profile[0]->city ?></p></div>
					</div>
					<div class="row">
					<div class="col-sm-6">
							<p><label>Age: </label> <?=$profile[0]->city ?></p>
					</div>
					<div class="col-sm-6"><p><label>Phone: </label><?=$profile[0]->phone ?></p></div>
					</div>
					<div class="row">
					<div class="col-sm-6">
					<p><label>old: </label> <?=$profile[0]->age ?></p>
							
					</div>
					<div class="col-sm-6"><p><label>Countary: </label> <?=$profile[0]->countary ?></p></div>
					</div>
 					<!--<div class="row">
					<div class="col-sm-6">
							<p><label>your of Experince: </label>35</p>
					</div>
					<div class="col-sm-6">	<p><label>Total Product of User: </label>35</p></div>
					</div>-->
 					
 			</div>
 		</div>
    </div>
</div>










 <?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>