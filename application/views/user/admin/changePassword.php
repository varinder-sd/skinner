<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>
<?php $this->load->view('header')?>

<style>
.costum_api_inner_div {
    width: 45%;
    background: #fff;
    margin: auto;
    padding: 15px;
	margin-top: 30px;
}
.costum_api_inner_div input[type="password"] {
    padding: 10px;
    width: 100%;
    margin-top: 9px;
    border: 1px solid #ddd;
}
.costum_api_inner_div  input[type="button"] {
    width: 100%;
    background: #2979ff;
    border: none;
    color: #fff;
    padding: 9px;
    font-size: 20px;
    margin-top: 7px;
}
</style>

<div class="page has-sidebar-left height-full">
<div class="costum_api_inner_div">



	<div class="row">
		<div class="col-md-12">
		<h2 class="reset_password">Reset Password </h2>
	</div>
	</div>
    <div id="mystatus">
        
    </div>
	
		<form action="">
		
		<div class="row">
			<div class="col-md-12">
				    <input type="password" name="currentpass" id="oldpassword" placeholder="Current password">
				</div>
				 <div class="col-md-12">
				  <input type="password" name="newpass" placeholder="New password" id="password">
				  </div>
				  <div class="col-md-12">
				  <input type="password" name="Confirmpass" placeholder="Confirm new password" id="Confirmpass">
				 </div>
				  <div class="col-md-12">
				  <input type="button" value="Change password" onclick="setnewpass();">  
				  </div>
				  </div>
				  </form>
				 
	</div>

</div>

<script type="text/javascript">
	function setnewpass(){ 
         $("#mystatus").html(' ');
		       jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "index.php/user/setnewpass",
                dataType: 'json',
                data: {oldpassword:$('#oldpassword').val(),password:$('#password').val(),password_confirm:$('#Confirmpass').val() },
                
                success: function(res) {
                  
                        //console.log(res);

                        if(res['status']==1){

                             $("#mystatus").append('<div class="alert alert-success"> <strong>Success!</strong> '+res['response']+'</div>');
                             $('input[type="password"],textarea').val('');

                        }else{

                             $("#mystatus").append('<div class="alert alert-warning"><strong>Warning!</strong> '+res['response']+'</div>');
                        }
                 
                },error:function() {
                    
                      // alert(res[0]->productId);
                        console.log("error");
          
                    
                }
            });

}
</script>






 <?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>





