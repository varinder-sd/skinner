<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>
<div id="wrapper">

        <!-- Navigation -->
<?php //$this->load->view('header')?>
<div class="page has-sidebar-left">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10  register">
                <div class="col">
                    <h3>
                        
                      Add Member
                    </h3>
                </div>
            </div>
            
        </div>
    </header>
<div id="page-wrapper1">
<div class="container">
	<div class="row">
		<?php if (validation_errors()) : ?>
			<div class="col-md-12">
				<div class="alert alert-danger" role="alert">
					<?= validation_errors() ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if (isset($error)) : ?>
			<div class="col-md-12">
				<div class="alert alert-danger" role="alert">
					<?= $error ?>
				</div>
			</div>
		<?php endif; ?>
		<div class="col-md-12">
			<div class="Member_add_div">
			<form action="<?base_url('user/admin/register')?>" method="post">
				<div class="form-group">
					<label for="username">UserName</label>
					<input type="text" class="form-control" id="username" name="username" placeholder="Enter a username">
					
				</div>
				<div class="form-group">
					<label for="username">Full Name</label>
					<input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter a Name">
					
				</div>
				<div class="form-group">
					<label for="age">Age</label>
					<input type="number" class="form-control" id="age" name="age" placeholder="Enter your age">
					
				</div>
				<div class="form-group">
					<label for="City">City</label>
					<input type="text" class="form-control" id="city" name="city" placeholder="Enter your city">
					
				</div>
				<div class="form-group">
					<label for="Country">Countary</label>
					<input type="text" class="form-control" id="countary" name="countary" placeholder="Enter your countary">
					
				</div>
				<div class="form-group">
					<label for="email">Email</label>
					<input type="email" class="form-control" id="email" name="email" placeholder="Enter a email">
					
				</div>
				<div class="form-group">
					<label for="phone">Phone</label>
					<input type="number" class="form-control" id="phone" name="phone" placeholder="Enter Your Number">
					
				</div>
					
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" class="form-control" id="password" name="password" placeholder="Enter a password">
					
				</div>
				<div class="form-group">
					<label for="password_confirm">Confirm password</label>
					<input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirm your password">
					
				</div>
				<div class="form-group">
				<div>
				<label for="password_confirm">Member Role</label>
				</div>
				<label class="radio_box_div"  style="padding-left: 35px;display: inline-block;">
					<input type="radio" name="member" value="1" > <span class="checkmark"></span>Admin</label>
					<label class="radio_box_div"  style="padding-left: 35px;display: inline-block;">
                     <input type="radio" name="member" value="0" checked> <span class="checkmark"></span>Sum-Admin</label>

					
				</div>
				<div class="form-group_submit">
					<input type="submit" class="btn btn-default" value="Register">
				</div>
			</form>
			</div>
		</div>
	</div><!-- .row -->
</div><!-- .container -->
</div>
</div>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>