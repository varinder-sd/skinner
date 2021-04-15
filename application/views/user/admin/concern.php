<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


<!-- Navigation -->
<?php $this->load->view('header')?>
<style>
    h5.card-title {
        font-size: 22px;
    }
</style>
<div class="page has-sidebar-left  height-full">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon-database"></i>
                        ADD CONCERN
                    </h4>
                </div>
            </div>

        </div>
    </header>

    <div class="container-fluid animatedParent animateOnce">
        <div class="animated fadeInUpShort">
            <div class="row my-3">
                <div class="col-md-7  offset-md-2">
                    <form action="<?=base_url('index.php/user/addConcern')?>" method="post"
                        enctype="multipart/form-data">

                        <div class="card no-b  no-r">
                            <div class="card-body">
                                <h5 class="card-title">About Concer</h5>




                                <input type="hidden" name="username" value="<?=$_SESSION['username'];?>">


                                <div class="form-group col-12 m-0">
                                    <label for="Concern" class="col-form-label s-12"></label>
                                    <input name="concern" class="form-control r-0 light s-12 " type="text"
                                        placeholder="Add Concern">
                                    <span class="btn btn-default btn-file">
                                        Browse <input type="file" name="concernpic">
                                    </span>

                                </div>






                                <div class="card-body">
                                    <button type="submit" class="btn btn-primary btn-lg"></i>ADD Concern</button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>