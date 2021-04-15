<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (isset($_SESSION['username'])){?>


        <!-- Navigation -->
        <?php $this->load->view('header')?>
        <style>
.container {
    position: relative;
    width: 100%;
}

.image {
  opacity: 1;
  display: block;
  width: 100%;
  height: auto;
  transition: .5s ease;
  backface-visibility: hidden;
}

.middle {
  transition: .5s ease;
  opacity: 0;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  -ms-transform: translate(-50%, -50%);
  text-align: center;
}

.container:hover .image {
  opacity: 0.3;
}

.container:hover .middle {
  opacity: 1;
}

.text {
  background-color: #4CAF50;
  color: white;
  font-size: 16px;
  padding: 16px 32px;
}
div.gallery {
    margin: 5px;
    //border: 1px solid #ccc;
    float: inherit;
    width: 180px;
    display: inline-block;
}



div.gallery img {
    width: 100%;
    height: auto;
}

div.desc {
    padding: 15px;
    text-align: center;
}

</style>

<div class="page has-sidebar-left">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon-package"></i>
                        Admin Products
                    </h4>
                </div>
            </div>
            
            
        </div>
    </header>
   



<div class="gallery_inner_img">
<?php 
        for($i=0;$i<sizeof($data);$i++){
?>
<div class="gallery">
  
 <div class="container">
  <img src="<?=base_url($data[$i]->imgName) ?>" alt="Avatar" class="image" style="width:100%">
  <div class="middle">
    <button type="button" class="btn btn-outline-secondary" onclick="deldumimg(<?=$data[$i]->imgId ?>)">Delete</button>
  </div>
</div>
</div>


<?php 
    }
?>

</div>
</div>

<script>
   
function deldumimg(id) {
  // alert(id);
  
   var r = confirm("Are You Sure to Delete!");
    if (r == true) {
       
      window.location.href = '<?php echo base_url("index.php/user/deldumimg?id=") ?>'+id;
    } else {
      
       window.location.href = '<?php echo base_url("index.php/user/dummyImage") ?>';
    } 
    
    
}
</script>


<?php $this->load->view('footer')?>
<?php }else{redirect(base_url('/index.php'), 'refresh');} ?>