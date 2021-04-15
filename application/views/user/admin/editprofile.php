<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>

<div class="page has-sidebar-left  height-full">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon-database"></i>
                        Edit Profile
                    </h4>
                </div>
            </div>
            
        </div>
    </header>
<?php   ini_set("upload_max_filesize","30MB");?>
    <div class="container-fluid animatedParent animateOnce">
        <div class="animated fadeInUpShort">
            <div class="row my-3">
                <div class="col-md-7  offset-md-2">
                    <form action="<?=base_url('index.php/user/edituser')?>" method="post" enctype="multipart/form-data">

                        <div class="card no-b  no-r editprofile">
                            <div class="card-body">
                                
                                <div class="form-row">
								 <div class="col-md-6">
								<h5 class="card-title  card-title1">About User</h5>
                                           <div class="form-group m-0 about_div">
                                            <label for="name" class="col-form-label s-12">USER NAME</label>
                                             <label for="name" class="col-form-label s-12"><?=$profile[0]->userName;?></label>
                                            
                                        </div>
										</div>
										<div class="col-md-6">
										<div class="dz-image dz-image11 ">
                                         <input type="file" id="file1" name="file" accept="image/*" capture style="display:none"/>
									<div class="upfile_img1">
                                   <div class="upfile_img"><img src="<?=base_url($profile[0]->proPicName);?>" id="upfile1" style="cursor:pointer;width: 200;height: 200;border-radius: 50%;" /></div><span><i class="fa fa-camera"></i></span></div></div>
										</div>
										
											<div class="col-md-12">
                                        <div class="form-row">
                                            
                                            <div class="form-group col-6 m-0">
                                                <label for="dob" class="col-form-label s-12"><i class="icon-calendar mr-2"></i>AGE</label>
                                                <input id="age" name="age" placeholder="Select Date of Birth" class="form-control r-0 light s-12 datePicker" data-time-picker="false"
                                                       type="number" value="<?=$profile[0]->age;?>" required >
                                            </div>

                                             <div class="form-group col-6 m-0">
                                        <label for="phone" class="col-form-label s-12"><i class="icon-phone mr-2"></i>Phone</label>
                                        <input id="phone" name="phone"  class="form-control r-0 light s-12 " type="text" value="<?=$profile[0]->phone;?>">
                                    </div>
                                        </div>

                                       
                                    </div>
									<div class="col-md-12">
										 <div class="form-row ">

                                    <div class="form-group col-6 m-0">
                                        <label for="inputCity" class="col-form-label s-12">City</label>
                                        <input type="text" class="form-control r-0 light s-12" value="<?=$profile[0]->city;?>" id="city" name="city" required >
                                    </div>

                                   <div class="form-group col-6 m-0">
                                        <label for="email" class="col-form-label s-12">COUNTRY</label>
                                        <input  class="form-control r-0 light s-12 " type="text" value="<?=$profile[0]->countary;?>" id="countary" name="countary" required >
                                    </div>
                                    
                                    <input type="hidden" name="table" id="table" value="<?=$table ?>">
                                    <input type="hidden" name="userId" id="userId" value="<?=$profile[0]->userId;?>">
                                     <input type="hidden" name="proPicName" id="proPicName" value="<?=$profile[0]->proPicName;?>">
                                </div>
								</div>
									<div class="col-12">
									 <div class="form-group profile_editor1 m-0">
                                            <label for="dob" class="col-form-label s-12">GENDER</label>
                                            <br>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="male"  value="1" name="gender" class="custom-control-input message_pri" <?if($profile[0]->gender==1){echo "checked";}?>>
                                                <label class="custom-control-label m-0" for="male" >Male</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" value="0" id="female" name="gender" class="custom-control-input message_pri" <?if($profile[0]->gender==0){echo "checked";}?>>
                                                <label class="custom-control-label m-0" for="female">Female</label>
                                            </div>
                                        </div>
									</div>
								
                                    <!--<div class="col-md-3 offset-md-1">
                                        <div class="dz-image upfile_img1">
                                         <input type="file" id="file1" name="file" accept="image/*" capture style="display:none"/>

                                   <div class="upfile_img"><img src="<?=$profile[0]->proPicName?>" id="upfile1" style="cursor:pointer;width: 200;height: 200;border-radius: 50%;" /></div><span><i class="fa fa-camera"></i></span></div>
                                        
                                       
                                    </div>-->

                                </div>

                               
                               <hr>
                            
                            
                            <div class="card-body_inner">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary btn-lg"></i>Udate User</button>
                            </div> 
							</div>
                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
    </div>
    </div>
</div>


<?php $this->load->view('footer')?>


<script type="text/javascript">

    

    $(document).ready(function(e) {

        $(".showonhover").click(function(){

            $("#selectfile").trigger('click');

        });

    });





var input = document.querySelector('input[type=file]'); // see Example 4



input.onchange = function () {

  var file = input.files[0];



  drawOnCanvas(file);   // see Example 6

  displayAsImage(file); // see Example 7

};



function drawOnCanvas(file) {

  var reader = new FileReader();



  reader.onload = function (e) {

    var dataURL = e.target.result,

        c = document.querySelector('canvas'), // see Example 4

        ctx = c.getContext('2d'),

        img = new Image();



    img.onload = function() {

      c.width = img.width;

      c.height = img.height;

      ctx.drawImage(img, 0, 0);

    };



    img.src = dataURL;

  };



  reader.readAsDataURL(file);

}



function displayAsImage(file) {

  var imgURL = URL.createObjectURL(file),

      img = document.createElement('img');



  img.onload = function() {

    URL.revokeObjectURL(imgURL);

  };



  img.src = imgURL;

  //alert(img.src);

  $("#upfile1").attr("src",img.src);

  //document.body.appendChild(img);

}



$("#upfile1").click(function () {

    $("#file1").trigger('click');

});



</script>

<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>