<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>
 <style type="text/css">
     


@import "http://fonts.googleapis.com/css?family=Droid+Sans";
form{
background-color:#fff
}
#maindiv{
width:960px;
margin:10px auto;
padding:10px;
font-family:'Droid Sans',sans-serif
}
#formdiv {
    width: 620px;
    float: inherit;
    text-align: center;
    margin: auto;
}
div#filediv {
    padding-bottom: 16px;
}
form{
padding:40px 20px;
box-shadow:0 0 10px;
border-radius:2px
}
h2{
margin-left:30px
}
.upload {
    background-color: #03a9f4;
    border: 1px solid #03a9f4;
    color: #fff;
    border-radius: 5px;
    padding: 8px 17px;
    /* text-shadow: 1px 1px 0 #040404; */
    box-shadow: 2px 2px 15px rgba(255, 255, 255, 0.75);
}
.upload:hover{
cursor:pointer;

box-shadow:0 0 5px rgba(0,0,0,.75)
}
#file{
color:#848484;
padding:5px;
border:1px dashed #9d9fa1;
background-color:#f5f8fa
}
#upload{
margin-left:45px
}
#noerror{
color:green;
text-align:left
}
#error{
color:red;
text-align:left
}
#img{
width:26px;
border:none;
height:26px;
margin-left:-20px;
margin-bottom:91px
}
.abcd{
text-align:center
}
.abcd img{
height:100px;
width:100px;
padding:5px;
border:1px solid #e8debd
}
b{
color:red
}
.page.has-sidebar-left {
    /* margin-bottom: -14px; */
    margin-top: -22px;
 </style>>
    
<div class="page has-sidebar-left">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon-package"></i>
                       ADD DUMMY IMAGES
                    </h4>
                </div>
            </div>
            
            
        </div>
    </header>


<div id="maindiv">
<div id="formdiv">
<h2> Image Upload </h2>
<form enctype="multipart/form-data" action="<?php echo base_url("index.php/user/uploadImages"); ?>" method="post">

<div id="filediv"><input name="file[]" type="file" id="file" accept='image/*'
/></div>
<input type="button" id="add_more" class="upload" value="Add More Files"/>
<input type="submit" value="Upload File" name="submit" id="upload" class="upload"/>
</form>
<!------- Including PHP Script here ------>

</div>
</div>

</div>
<!-- <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT" crossorigin="anonymous"></script> -->
   
    




<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>