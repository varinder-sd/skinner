<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
 <?php if (isset($_SESSION['username'])) {?>
<div id="wrapper">

        <!-- Navigation -->
<?php $this->load->view('siderbar')?>
<?php// $this->load->view('subadminlist')?>

     
        <!-- /#page-wrapper -->

    </div>
	<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>