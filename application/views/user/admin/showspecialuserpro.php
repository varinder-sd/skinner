<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>
<?php $this->load->view('header')?>



<div class="container-fluid well span6">
    <div class="row-fluid">
        <div class="span2" >
            <img src="https://secure.gravatar.com/avatar/de9b11d0f9c0569ba917393ed5e5b3ab?s=140&r=g&d=mm" class="img-circle">
        </div>
        
        <div class="span8">
            <h3>User Name</h3>
            <h6>Email: MyEmail@servidor.com</h6>
            <h6>Ubication: Colombia</h6>
            <h6>Old: 1 Year</h6>
            <h6><a href="#">More... </a></h6>
        </div>
        
        <div class="span2">
            <div class="btn-group">
                <a class="btn dropdown-toggle btn-info" data-toggle="dropdown" href="#">
                    Action 
                    <span class="icon-cog icon-white"></span><span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="#"><span class="icon-wrench"></span> Modify</a></li>
                    <li><a href="#"><span class="icon-trash"></span> Delete</a></li>
                </ul>
            </div>
        </div>
</div>
</div> 











 <?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>