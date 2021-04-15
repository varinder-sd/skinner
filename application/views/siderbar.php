      
     
      <?php if (isset($_SESSION['username'])){?>
        
  <aside class="main-sidebar fixed offcanvas shadow col-sm-3" data-toggle='offcanvas'>
    <section class="sidebar">
        <div class="w-80px mt-3 mb-3 ml-3">
            <img src="<?= base_url('assets/img/logo.png') ?> "alt="">
        </div>
        <div class="relative">
            <a data-toggle="collapse" href="#userSettingsCollapse" role="button" aria-expanded="false"
               aria-controls="userSettingsCollapse" class="btn-fab btn-fab-sm absolute fab-right-bottom fab-top btn-primary shadow1 ">
                <i class="icon icon-cogs"></i>
            </a>
            <div class="user-panel p-3 light mb-2">
                <div>
                    <div class="float-left image">
                        <img class="user_avatar" src="http://product-key.websitevalley.us/Admindashboard/dist/img/user2-160x160.jpg" alt="User Image">
                    </div>
                    <div class="float-left info">
                        <h6 class="font-weight-light mt-2 mb-1"><?=$_SESSION['username']?></h6>
                        <a href="#"><i class="icon-circle text-primary blink"></i> Online</a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="collapse multi-collapse" id="userSettingsCollapse">
                    <div class="list-group mt-3 shadow">
                        <!-- <a href="#" class="list-group-item list-group-item-action ">
                            <i class="mr-2 icon-umbrella text-blue"></i>Profile
                        </a>
                        <a href="#" class="list-group-item list-group-item-action"><i
                                class="mr-2 icon-cogs text-yellow"></i>Settings</a> -->
                        <a href="<?=base_url('index.php/user/changePassword') ?>" class="list-group-item list-group-item-action"><i
                                class="mr-2 icon-security text-purple"></i>Change Password</a>
                    </div>
                </div>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li class="header"><!--<strong>MAIN USER</strong>--></li>
            
           
            <li class="treeview"><a href="#"><i class="icon icon-account_box blue-text s-18"></i>Users<i
                    class="icon icon-angle-left s-18 pull-right"></i></a>
                <ul class="treeview-menu treeview1">
                    </li>
                    <li><a href="<?=base_url('index.php/user/register') ?>"><i class="icon icon-user-plus"></i>Add Member</a>
                    </li>
                    <li><a href="<?= base_url('index.php/user/specialist') ?>"><i class="icon icon-user-md
"></i>Specialist</a>
                    </li>
                    <li><a href="<?= base_url('index.php/user/patientlist') ?>"><i class="icon icon-accessible"></i>Patient</a>
                    </li>
                    <li><a href="<?= base_url('index.php/user/subadminlist') ?>"><i class="icon icon-user"></i>Member</a>
                    </li>
                </ul>
          <li class="treeview no-b"><a href="#">
                <i class="icon icon-package light-green-text s-18"></i>
                <span>Inbox</span>
                <span class="badge r-3 badge-success pull-right" id="inboxcount"></span>
            </a>
                <ul class="treeview-menu" style="display: none;">
                    <li><a href="<?= base_url('index.php/user/inbox') ?>"><i class="icon icon-circle-o"></i>All Messages</a>
                    </li>
                    
                </ul>
            </li>
           
            <li class="header light mt-3"><strong>PRODUCT DETAILS</strong></li>
             <li class="treeview ">

                <a href="#"><i class="icon icon icon-package blue-text s-18"></i>Products<i
                    class="icon icon-angle-left s-18 pull-right"></i></a>

                


                <ul class="treeview-menu treeview1">
                    <li><a href="<?= base_url('index.php/user/allproduct') ?>"><i class="icon icon-circle-o"></i>All product </a>
                    </li>
                     <li><a href="<?= base_url('index.php/user/allbrand') ?>"><i class="icon icon-circle-o"></i>Brands </a>
                    </li>
                    <li><a href="<?= base_url('index.php/user/addproduct') ?>"><i class="icon icon-add"></i>Add Explore Product</a>
                    </li>
                    <li><a href="<?= base_url('index.php/user/adminProduct') ?>"><i class="icon icon-circle-o"></i>Explore Product</a>
                    </li>
                    <li ><a href="<?= base_url('index.php/user/exportview') ?>"><i class="icon icon-circle-o"></i>import/export </a>
                    </li>
                   
                </ul>
            </li>


            <li class="treeview ">
                <a href="#"><i class="icon icon icon-package blue-text s-18"></i>Products For Approvel<i
                    class="icon icon-angle-left s-18 pull-right"></i></a>

                
                <ul class="treeview-menu treeview1">
                    <li><a href="<?= base_url('index.php/user/productForApprovel') ?>"><i class="icon icon-circle-o"></i>Product List </a>
                    </li>
                    
                </ul>
            </li>



            <li class="header light mt-3"><strong>ADDITIONAL</strong></li>
             <li class="treeview treeview1">

                <a href="#"><i class="icon-box2  icon blue-text s-18"></i>CONCERNS<i
                    class="icon icon-angle-left s-18 pull-right"></i></a>

                <ul class="treeview-menu treeview1">
                    <li><a href="<?= base_url('index.php/user/concern') ?>"><i class="icon icon-add"></i>Add Concern</a>
                    </li>
                    <li><a href="<?= base_url('index.php/user/concernList') ?>"><i class="icon icon-circle-o"></i>Concern List</a>
                    </li>
                </ul>
            </li>
            <li class="treeview treeview1"><a href="#">

                <a href="#"><i class=" icon icon-picture-o blue-text s-18"></i>Dummy  Images<i
                    class="icon icon-angle-left s-18 pull-right"></i></a>


                <ul class="treeview-menu treeview1">
                    <li><a href="<?= base_url('index.php/user/addDummyImage') ?>"><i class=" icon icon-address-book-o"></i>Add Image</a>
                    </li>
                    <li><a href="<?= base_url('index.php/user/dummyImage') ?>"><i class="icon icon-image"></i>Image List</a>
                    </li>
                </ul>
            </li>
             <!-- <li class="treeview treeview1"><a href="#">

                <a href="#"><i class=" icon icon-picture-o blue-text s-18"></i>Api Alert Message<i
                    class="icon icon-angle-left s-18 pull-right"></i></a>


                <ul class="treeview-menu treeview1">
                    <li><a href="<?= base_url('index.php/user/alertmessage') ?>"><i class=" icon icon-address-book-o"></i>Message</a>
                    </li>
                   
                </ul>
            </li> -->
        </ul>
    </section>
</aside>
<!--Sidebar End-->

        

		<?php } ?>
        
