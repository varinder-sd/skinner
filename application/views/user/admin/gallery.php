<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>


<div class="page has-sidebar-left">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 " style="padding-top:0px !important;">
                <div class="col">
                    <h4 style="float: left;">
                        <i class="icon-package"></i> Gallery
                       
                    </h4>
                    
                </div>
            </div>
            
            
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce my-3">
	        <div class="animated fadeInUpShort">
            <div class="row">
                 <div class="box_section_gallery">
		<div class="row1">
			<div class="col-md-12">
				<?php if(sizeof($gallery)>0){?>
				 <div class="tab" role="tabpanel">
					<!-- Nav tabs -->
					 <ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active">
							<a href="#Section1" aria-controls="home" role="tab" data-toggle="tab">Images</a></li>
						<li role="presentation">
							<a href="#Section2" aria-controls="profile" role="tab" data-toggle="tab">Video</a>
							</li>
					</ul>
				<!-- Tab panes -->
				<div class="tab-content tabs">
				<div role="tabpanel" class="tab-pane fade in active show" id="Section1">
					<!--<h3>Images</h3>-->
					<div class="row">
						<?php 
						if(sizeof($gallery['image'])>0){
						for($i=0;$i<sizeof($gallery['image']);$i++){
						  ?>
						<div class="col-md-3">
							<div class="gallery_pageimg">
								<a href="#" target="_blank"><img src="<?=base_url($gallery['image'][$i]->media)?>"  ></a>
							</div>
						</div><?}}else{echo "<h4>No Image</h4>";}?>
					</div>
			</div>
			<div role="tabpanel" class="tab-pane fade" id="Section2">
			<!--<h3>Video</h3>-->

			<div class="row">
				<?php
				if(sizeof($gallery['vedio'])>0){
						for($i=0;$i<sizeof($gallery['vedio']);$i++){
				  ?>
				<div class="col-md-4">
					<div class="gallery_pageimg">
					
					<video width="100%" height="215"  controls>
  <source src="<?=base_url($gallery['vedio'][$i]->media)?>" type="video/mp4">
  <source src="<?=base_url($gallery['vedio'][$i]->media)?>" type="video/ogg">
  
</video>
					
						<!--<iframe width="560" height="215" src="<?=$gallery['vedio'][$i]->media?>" autoplay="false"  frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>-->
					</div>
					</div><?}}else{echo "<h4>No Vedio</h4>";}?>
					
				
			</div>
			
			

</div></div></div>
<?
}else{

	echo "<h3>No Data</h3>";
}
?>


</div>

</div>
</div>


                
				
				
				
				<? //  print_r($gallery);?>


               
            </div>
           
        </div>

    </div>
    
            
</div>





<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>