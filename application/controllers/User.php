<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 /*error_reporting(E_ALL);
ini_set("display_errors", 1);*/
/**
 * User class.
 * 
 * @extends CI_Controller
 */
class User extends CI_Controller {

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		parent::__construct();
		$this->load->library(array('session'));
		$this->load->helper(array('url'));
		$this->load->model('user_model');
		
	}
	
	
	public function index() {
			
			$this->load->view('user/admin/login');
		
	}
	
	/**
	 * register function.
	 * 
	 * @access public
	 * @return void
	 */
	public function register(){
		//print_r($_POST);die();
		// create the data object
		$data = new stdClass();
		
		// load form helper and validation library
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		// set validation rules
		$this->form_validation->set_rules('username', 'Username', 'trim|required|alpha_numeric|min_length[4]|is_unique[user_admin.username]', array('is_unique' => 'This username already exists. Please choose another one.'));
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[user_admin.email]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
		$this->form_validation->set_rules('password_confirm', 'Confirm Password', 'trim|required|min_length[6]|matches[password]');
		
		if ($this->form_validation->run() === false) {
			
			// validation not ok, send validation errors to the view
			$this->load->view('header');
			$this->load->view('user/admin/register', $data);
			$this->load->view('footer');
			
		} else {
			
			// set variables from the form
			$username = $this->input->post('username');
			$age= $this->input->post('age');
			$phone= $this->input->post('phone');
			$name= $this->input->post('fullname');
			$city = $this->input->post('city');
			$countary = $this->input->post('countary');
			$email = $this->input->post('email');
			$member = $this->input->post('member');
			//$yearOfExperience = $this->input->post('yearOfExperience');
			$password = $this->input->post('password');
			
			if ($this->user_model->create_user($username,$age,$city,$countary, $email, $password,$name,$phone,$member)) {
				
				// user creation ok
				redirect(base_url('/index.php/user/subadminlist'), 'refresh');
				
			} else {
				
				// user creation failed, this should never happen
				$data->error = 'There was a problem creating your new account. Please try again.';
				
				// send error to the view
				$this->load->view('header');
				$this->load->view('user/admin/register', $data);
				$this->load->view('footer');
				
			}
			
		}
		
	}
		
	/**
	 * login function.
	 * 
	 * @access public
	 * @return void
	 */
	public function login() {
		//print_r($_POST);die();
		// create the data object
		$data = new stdClass();
		
		// load form helper and validation library
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		// set validation rules
		$this->form_validation->set_rules('username', 'userName', 'required|alpha_numeric');
		$this->form_validation->set_rules('password', 'password', 'required');
		
		if ($this->form_validation->run() == false) {
			
			// validation not ok, send validation errors to the view
			$this->load->view('header');
			$this->load->view('user/admin/login');
			$this->load->view('footer');
			
		} else {
			
			// set variables from the form
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			
			if ($this->user_model->resolve_user_login($username, $password)) {
				
				$user_id = $this->user_model->get_user_id_from_username($username);
				$user    = $this->user_model->get_user($user_id);
				
				// set session user datas
				$_SESSION['user_id']      = (int)$user->userId;
				$_SESSION['username']     = (string)$user->userName;
				$_SESSION['proPicName']     = (string)$user->proPicName;
				$_SESSION['email']     = (string)$user->email;
				$_SESSION['name']     = (string)$user->fullName;
				$_SESSION['logged_in']    = (bool)true;
				//$_SESSION['is_confirmed'] = (bool)$user->is_confirmed;
				//$_SESSION['is_admin']     = (bool)$user->userRole;
				$status=1;
				
				$this->user_model->active($_SESSION['user_id']);
				// user login ok
				$this->subadminlist();
				
				
			} else {
				
				// login failed
				$data->error = 'Wrong username or password.';
				
				// send error to the view
				$this->load->view('header');
				$this->load->view('user/admin/login', $data);
				$this->load->view('footer');
				
			}
			
		}
		
	}
	
	/**
	 * logout function.
	 * 
	 * @access public
	 * @return void
	 */
	public function logout() {
		
		// create the data object
		$data = new stdClass();
		
		if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
			
			// remove session datas
			/* foreach ($_SESSION as $key => $value) {
				unset($_SESSION[$key]);
			} */
			$this->user_model->deactive($_SESSION['user_id']);
			$this->session->sess_destroy();
			    
				
			
			
			redirect('/index.php');
			
		} else {
			
			// there user was not logged in, we cannot logged him out,
			// redirect him to site root
			redirect('/index.php');
			
		}
		
	}
	public function subadminlist(){
		$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/subadminlist'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->total_num()
		];
				$config['cur_tag_open'] = '&nbsp;<a class="current">';

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data['subadminlist'] = $this->user_model->sub_admin_list($config['per_page'],$page);
	  	//print_r($data);

		$this->load->view('/user/admin/subadminlist',$data);
	}
	public function patientlist(){
		$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/patientlist'),
		 'per_page'=>15,
		'total_rows'=>$this->user_model->patient_user_total_rows()
		 ];


		 $config['cur_tag_open'] = '&nbsp;<a class="current">';

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';


		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data['patient'] = $this->user_model->patient_list($config['per_page'],$page);
	  	

		$this->load->view('/user/admin/patient',$data);
	}
	public function specialist(){
	
		$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/specialist'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->sepcial_user_total_rows()
		];
		$config['cur_tag_open'] = '&nbsp;<a class="current">';

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data['sepcialuserlist'] = $this->user_model->sepcial_user_list($config['per_page'],$page);
	  	//print_r($data);

		$this->load->view('/user/admin/specialist',$data);
	}
	public function allproduct(){
	
		$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/allproduct'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->product_rows()
		];


		$config['cur_tag_open'] = '&nbsp;<a class="current">';

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';



		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data['allproduct'] = $this->user_model->all_product($config['per_page'],$page);
	  

		$this->load->view('/user/admin/allproduct',$data);
	}
	public function productbypatient(){
	
		$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/productbypatient'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->product_by_patient_rows()
		];
		$config['cur_tag_open'] = '&nbsp;<a class="current">';

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data['productbypatient'] = $this->user_model->product_by_patient($config['per_page'],$page);
	  	//print_r($data);

		$this->load->view('/user/admin/productbypatient',$data);
	}
	public function showProfile(){

		  //print_r($_GET);
			$id= $this->input->get('id');

           $table= $this->input->get('data');
           //$this->load->show_profile($id);

		$data['profile']=$this->user_model->show_profile($id,$table);

			$this->load->view('/user/admin/showProfile',$data);
		
		
	}
	public function showProfile2(){

		  //print_r($_GET);
			$id= $this->input->get('id');

           $table= $this->input->get('data');
           //$this->load->show_profile($id);

		$data=$this->user_model->show_profile2($id,$table);

			echo json_encode($data);
		
		
	}
	public function showProfile1(){

		// print_r($_POST);
		$result=array();
			$id= $this->input->get('id');

           $table= $this->input->get('data');
           $count= $this->input->get('count');
           //$this->load->show_profile($id);

		$result['data']=$this->user_model->show_profile1($id,$table);
		$result['count']= $count;
		$result['product']=$this->user_model->Special_product($id);


		




	    $this->load->view('/user/admin/specialistProfile',$result);
		

		
	}
	public function sendmail(){

		$userId= $this->input->post('userId');
		$usertype= $this->input->post('usertype');
		$email= $this->input->post('email');
		$message= $this->input->post('message');
		$subject= $this->input->post('subject');

		$this->load->library('email');
	 			$config['protocol']    = 'smtp';
			    $config['smtp_host']    = 'ssl://smtp.gmail.com';
			    $config['smtp_port']    = '465';
			    $config['smtp_timeout'] = '7';
			    $config['smtp_user']    = 'skinnerapp@gmail.com';
			    $config['smtp_pass']    = 'lihbjqteihpyxcbs';
			    $config['charset']    = 'utf-8';
			    $config['newline']    = "\r\n";
			    $config['mailtype'] = 'text'; // or html
			    $config['validation'] = TRUE; // bool whether to validate email or not      

        $this->email->initialize($config);



        

		$this->email->from('Skinner', 'Skinner');
		$this->email->to($email);
		

		$this->email->subject($subject);
		$this->email->message($message);

		$this->email->send();
		   
			$data=$this->user_model->insert_mail($userId,$usertype,$email,$message,$subject);


			echo json_encode($data);

	
	}
	 
	public function inbox(){


		$result=$this->user_model->inbox_users_list();
		//print_r($result);die;
		
	    $this->load->view('/user/admin/inbox',$result);
		

		
	}
	public function getmail(){

		$id= $this->input->post('id');
		$type= $this->input->post('type');
		$result=$this->user_model->get_mail($id,$type);
		echo json_encode($result) ;

	}

	public function editprofile(){

		
			$id= $this->input->get('id');

           $table= $this->input->get('data');
           //$this->load->show_profile($id);

		$data['profile']=$this->user_model->show_profile($id,$table);
		$data['table']=$table;
			$this->load->view('/user/admin/editprofile',$data);
		
		
	}
	public function edituser(){
		   ini_set("upload_max_filesize","300MB");
		
		 	$id = $this->input->post('userId');
			$age = $this->input->post('age');
			$gender = $this->input->post('gender');
			//$file = $_FILES['file'];
			$city = $this->input->post('city');
			$phone = $this->input->post('phone');
			$countary = $this->input->post('countary');
		    $table = $this->input->post('table');
		    //print_r($_FILES);
		   if($_FILES['file']['size']>0){
		 
						      $errors= array();
						      $file_name =uniqid().".png";
						      $file_size =$_FILES['file']['size'];
						      $file_tmp =$_FILES['file']['tmp_name'];
						      $file_type=$_FILES['file']['type'];
						      $tmp = explode('.', $_FILES['file']['name']);

						      $file_ext=end($tmp);;
						      
						      $expensions= array("jpeg","jpg","png");
						      
						      if(in_array($file_ext,$expensions)=== false){
						         $errors[]="extension not allowed, please choose a JPEG or PNG file.";
						      }
						      
						      if($file_size > 2097152){
						         $errors[]='File size must be excately 2 MB';
						      }
						      
						      if(empty($errors)==true){
						      	 $uploadfile = "/home/skinner/public_html//assets/profilepic/".$file_name;
						        // move_uploaded_file($file_tmp,"http://leaselance.com/skinnerapp/assets/profilepic/".$file_name);


										if (move_uploaded_file($_FILES["file"]["tmp_name"], $uploadfile)) {
											$imgname="/assets/profilepic/".$file_name;

											$this->user_model->edit_user($id,$age,$gender,$city,$countary,$table,$imgname,$phone);
										       // echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
										        echo "Success";
										    } else {
										        echo "Sorry, there was an error uploading your file.";
										    }

						         
						      }
  }else{
  		$this->user_model->edit_user_witoutpic($id,$age,$gender,$city,$countary,$table,$phone);
  }
  			
		

		$data['profile']=$this->user_model->show_profile($id,$table);

		$this->load->view('/user/admin/showProfile',$data);
	}
	public function concern(){

		$this->load->view('/user/admin/concern');
	}
	public function addConcern(){

		print_r($_POST);
		$concern = $this->input->post('concern');
		$username = $this->input->post('username');
		
		echo "<pre>";
		print_r ($this->input->post());
		echo "</pre>";
		

		$this->user_model->add_Concern($concern,$username);

		$this->concernList();
		
	}
	public function concernList(){

		$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/concernList'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->concern_rows()
		];

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
			
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data['concernList'] = $this->user_model->concern_List($config['per_page'],$page);
	  //	print_r($data);

		$this->load->view('/user/admin/concernList',$data);
	}
	public function concerndelete(){

		
		$id = $this->input->get('id');
		$this->user_model->concern_delete($id);
		
		$this->concernList();
     	

     	
	}
	public function specialistdelete(){

		
		$id = $this->input->get('id');
		$this->user_model->specialist_delete($id);
		
		$this->specialist();
     	

     	
	}
	public function patientdelete(){

		
		$id = $this->input->get('id');
		$this->user_model->patient_delete($id);
		
		$this->patientlist();
     	

     	
	}
	public function subadmindelete(){

		
		$id = $this->input->get('id');
		$this->user_model->subadmin_delete($id);
		
		$this->subadminlist();
     	

     	
	}
	public function deleteProduct(){

		
		$id = $this->input->get('id');
		$data['product']=$this->user_model->delete_Product($id);
		
		
			$this->load->view('/user/admin/deleteProduct',$data);

		
	}
	public function specialuserproduct(){
	$id = $this->input->get('id');
	$data['name'] = $this->input->get('name');
	$data['product']=$this->user_model->special_user_product($id);
	//print_r($data);
	$this->load->view('/user/admin/specialuserproduct',$data);
  }
  public function productDetail(){
  	//print_r($_POST);
    $id = $this->input->post('id');
    $usertype = $this->input->post('usertype');
    $data=$this->user_model->product_detail($id,$usertype);
   // print_r($data);
     return $data;
  }
  public function patientuserproduct(){

  		$id = $this->input->get('id');
	$data['name'] = $this->input->get('name');
	$data['product']=$this->user_model->patient_user_product($id);
	//print_r($data);
	$this->load->view('/user/admin/patientuserproduct',$data);


  }
  public function addproduct(){

  				$this->load->view('/user/admin/addproduct');


  }
  public function searchproduct(){
			  	$barcode = $this->input->post('barcode');
			  	
			  	$data=$this->user_model->search_product($barcode);
			  	if(sizeof($data)>0){

			  		$data;
			  	}else{
			  		$data="no data";
			  	}
			  		echo json_encode($data) ;
  	}
  	
  public function instruction(){
  	$this->load->view('/user/admin/instruction');
  }
  
	public function showmedia(){

		$id = $this->input->post('id');
		$data=$this->user_model->show_media($id);
			
		echo json_encode($data) ;

	}
	public function forapprovel(){

     $id = $this->input->post('id');
     $media = $this->input->post('media');
     $data=$this->user_model->insert_media($id,$media);

     echo json_encode(1) ;
	}	
	public function addToExplore(){
		//print_r($_POST);die;
		$type = $this->input->Post('type');
		$id = $this->input->Post('id');
		$img = $this->input->Post('img');
		$barcodeId = $this->input->Post('barcodeId');
		$barcodeSrNo = $this->input->Post('barcodeSrNo');
		$productName = $this->input->Post('productName');
		$brandName = $this->input->Post('brandName');
		$media = 0;
		$minutes = $this->input->Post('minutes');
		$eyes = $this->input->Post('eyes');
		$instruction = $this->input->Post('instruction');
		if($eyes=='false'){
			$eyes=0;

		}else{
			$eyes=1;

		}
		$forhead = $this->input->Post('forhead');
		if($forhead=='false'){
			$forhead=0;

		}else{
			$forhead=1;

		}
		$neck = $this->input->Post('neck');
		if($neck=='false'){
			$neck=0;

		}else{
			$neck=1;

		}
		$allFace = $this->input->Post('allFace');
		if($allFace=='false'){
			$allFace=0;

		}else{
			$allFace=1;

		}
		$everyday = $this->input->Post('everyday');
		if($everyday=='false'){
			$everyday=0;

		}else{
			$everyday=1;

		}
		$twiceAweek = $this->input->Post('twiceAweek');
		if($twiceAweek=='false'){
			$twiceAweek=0;

		}else{
			$twiceAweek=1;

		}
		$onceAweek = $this->input->Post('onceAweek');
		if($onceAweek=='false'){
			$onceAweek=0;

		}else{
			$onceAweek=1;

		}
		$onceAmonth = $this->input->Post('onceAmonth');
		if($onceAmonth=='false'){
			$onceAmonth=0;

		}else{
			$onceAmonth=1;

		}
		$am = $this->input->Post('am');
		if($am=='false'){
			$am=0;

		}else{
			$am=1;

		}
		$pm = $this->input->Post('pm');
		if($pm=='false'){
			$pm=0;

		}else{
			$pm=1;

		}
		$numberOfStep = $this->input->Post('numberOfStep');
		if($numberOfStep=='true'){
			$numberOfStep=1;

		}else{
			$numberOfStep=0;

		}
		if($numberOfStep==1){
					$step1 = $this->input->Post('step1');
					if($step1=='true'){
						$step=1;

					}
					$step2 = $this->input->Post('step2');
					if($step2=='true'){
						$step=2;

					}
					$step3 = $this->input->Post('step3');
					if($step3=='true'){
						$step=3;

					}
			}else{

				$step=0;
			}
		if(isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']!=null){
		 
						      $errors= array();
						      $file_name =uniqid().".mp4";
						      $file_size =$_FILES['fileToUpload']['size'];
						      $file_tmp =$_FILES['fileToUpload']['tmp_name'];
						      $file_type=$_FILES['fileToUpload']['type'];
						      $tmp = explode('.', $_FILES['fileToUpload']['name']);

						      $file_ext=end($tmp);;
						      
						      $expensions= array("mp4","avi","mov","3gp");
						      
						      if(in_array($file_ext,$expensions)=== false){
						         $errors[]="extension not allowed, please choose a  valid formt.";
						      }
						      
						      if($file_size > 209715255){
						         $errors[]='File size large';
						      }
						      
						      if(empty($errors)==true){
						      	 $uploadfile =    "/home/skinner/public_html/assets/videos/".$file_name;
						        // move_uploaded_file($file_tmp,"http://leaselance.com/skinnerapp/assets/profilepic/".$file_name);


										if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $uploadfile)) {

											 $media="assets/videos/".$file_name;
                                             $data=$this->user_model->add_To_Explore($img,$barcodeId,$barcodeSrNo,$productName,$brandName,$media,$eyes,$forhead,$neck,$allFace,$everyday,$twiceAweek,$onceAweek,$onceAmonth,$am,$pm,$id,$instruction,$type,$numberOfStep,$step,$minutes);

										       
										    } 

						         
						      }
           }else{
		
		$data=$this->user_model->add_To_Explore($img,$barcodeId,$barcodeSrNo,$productName,$brandName,$media,$eyes,$forhead,$neck,$allFace,$everyday,$twiceAweek,$onceAweek,$onceAmonth,$am,$pm,$id,$instruction,$type,$numberOfStep,$step,$minutes);

		}
		echo json_encode($barcodeId) ;
		 
	}  
	public function totalcount(){
		$data=$this->user_model->total_count();
		 echo json_encode($data) ;
	}
	public function videoapprovel(){


		$data =array();
		$data['allproduct'] = $this->user_model->video_approvel();
	  //	print_r($data);

		$this->load->view('/user/admin/videoapprovel',$data);

	}
	public function explorproduct(){

		$data =array();
		$data['allproduct'] = $this->user_model->explore_product();
	  //	print_r($data);

	      $this->load->view('/user/admin/explorproduct',$data);
	}
	public function productEditForm(){

		$id = $this->input->get('id');
		$type = $this->input->get('type');
		$data=$this->user_model->product_Edit_Form($id,$type);
		 echo json_encode($data) ;
	}
	public function adminProduct(){
		
		$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/adminProduct'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->admin_product_rows()
		];
		$config['cur_tag_open'] = '&nbsp;<a class="current">';

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data['allproduct'] = $this->user_model->admin_product($config['per_page'],$page);
	  	//print_r($this->user_model->admin_product_rows());

		$this->load->view('/user/admin/adminProduct',$data);
	}
	public function showAdminMedia(){
		$id = $this->input->post('id');
		$data=$this->user_model->show_Admin_Media($id);
			
		echo json_encode($data) ;

	}
	public function adminProductDetail(){

			//print_r($_POST);
    $id = $this->input->post('id');
    $data=$this->user_model->admin_product_detail($id);
   // print_r($data);
    echo json_encode($data) ;

	}
	public function upload(){

		   ini_set("upload_max_filesize","300MB");
		 	$id = $this->input->get('id');
 		    

		   if(isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']!=null){
		 
						      $errors= array();
						      $file_name =uniqid().".mp4";
						      $file_size =$_FILES['fileToUpload']['size'];
						      $file_tmp =$_FILES['fileToUpload']['tmp_name'];
						      $file_type=$_FILES['fileToUpload']['type'];
						      $tmp = explode('.', $_FILES['fileToUpload']['name']);

						      $file_ext=end($tmp);;
						      
						      $expensions= array("mp4","avi","mov","3gp");
						      
						      if(in_array($file_ext,$expensions)=== false){
						         $errors[]="extension not allowed, please choose a  valid formt.";
						      }
						      
						      if($file_size > 209715255){
						         $errors[]='File size large';
						      }
						      
						      if(empty($errors)==true){
						      	 $uploadfile =    "/home/skinner/public_html/assets/videos/".$file_name;
						        // move_uploaded_file($file_tmp,"http://leaselance.com/skinnerapp/assets/profilepic/".$file_name);


										if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $uploadfile)) {

											 $name="assets/videos/".$file_name;
                                              $this->user_model->upload_video($id, $name);
										       // echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
										        echo "Success";
										    } else {
										        echo "Sorry, there was an error uploading your file.";
										    }

						         
						      }
           }

	}

	public function updatevideo(){
		//print_r($_POST);die();
		$id = $this->input->post('id');
	$data['link']=$this->user_model->update_video($id);
	echo json_encode($data);
	}
	public function addinstruction(){
					//print_r($_POST);
					//print_r($_FILES);

				$img = $this->input->Post('img');
				$barcodeId = $this->input->Post('barcodeId');
				$barcodeSrNo = $this->input->Post('barcodeSrNo');
				$productName = $this->input->Post('productName');
				$brandName = $this->input->Post('brandName');	
				$minute = $this->input->Post('minute');
				if($this->input->Post('eyes')!==null){
				$eyes=1;

			    }else{
				$eyes=0;

			     }
		
				if($this->input->Post('forhead')!==null){
					$forhead=1;

				}else{
					$forhead=0;

				}
				
				if($this->input->Post('neck')!==null){
					$neck=1;

				}else{
					$neck=0;

				}
				
				if($this->input->Post('allFace')!==null){
					$allFace=1;

				}else{
					$allFace=0;

				}
				
				if($this->input->Post('time')=='everyday'){
					$everyday=1;

				}else{
					$everyday=0;

				}
				
				if($this->input->Post('time')=='twiceAweek'){
					$twiceAweek=1;

				}else{
					$twiceAweek=0;

				}
				if($this->input->Post('time')=='onceAweek'){
					$onceAweek=1;

				}else{
					$onceAweek=0;

				}
				if($this->input->Post('time')=='onceAmonth'){
					$onceAmonth=1;

				}else{
					$onceAmonth=0;

				}
				if($this->input->Post('inturval')=='am'){
					$am=1;

				}else{
					$am=0;

				}
				if($this->input->Post('inturval')=='pm'){
					$pm=1;

				}else{
					$pm=0;

				}
				$instruction=$this->input->Post('instruction');		
				

				 ini_set("upload_max_filesize","300MB");
				

		   if($_FILES['fileToUpload']['size']>100){
		 		
						      $errors= array();
						      $file_name =uniqid().".mp4";
						      $file_size =$_FILES['fileToUpload']['size'];
						      $file_tmp =$_FILES['fileToUpload']['tmp_name'];
						      $file_type=$_FILES['fileToUpload']['type'];
						      $tmp = explode('.', $_FILES['fileToUpload']['name']);

						      $file_ext=end($tmp);;
						      
						      $expensions= array("mp4","avi","mov","3gp");
						      
						      if(in_array($file_ext,$expensions)=== false){
						         $errors[]="extension not allowed, please choose a  valid formt.";
						      }
						      
						      if($file_size > 209715255){
						         $errors[]='File size large';
						      }
						      
						      if(empty($errors)==true){
						      	 $uploadfile = "/home/skinner/public_html/assets/videos/".$file_name;
						        // move_uploaded_file($file_tmp,"http://leaselance.com/skinnerapp/assets/profilepic/".$file_name);


										if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $uploadfile)) {

											 $name="assets/videos/".$file_name ;
											  $id=$this->user_model->add_instruction($img,$barcodeId,$barcodeSrNo,$productName,$brandName,$eyes,$forhead,$neck,$allFace,$everyday,$twiceAweek,$onceAweek,$onceAmonth,$am,$pm,$instruction,$minute, $name);
                                              $this->user_model->upload_video($id, $name);
										       // echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
										       redirect(base_url('index.php/user/adminProduct'), 'refresh');
										    } else {
										        echo "Sorry, there was an error uploading your file.";
										    }

						         
						      }
           }else{
         
             	    $this->user_model->add_instruction($img,$barcodeId,$barcodeSrNo,$productName,$brandName,$eyes,$forhead,$neck,$allFace,$everyday,$twiceAweek,$onceAweek,$onceAmonth,$am,$pm,$instruction,$minute,"empty");
             	    	redirect(base_url('index.php/user/adminProduct'), 'refresh');
				}
	}
	public function addbarcode(){
		
  
		 
		//print_r($_POST);die();
		$barcode = $this->input->post('barcodesrno');
		$productName = $this->input->post('productName');
		$brandName = $this->input->post('brandName');
		$data=array();
		$data['data']=$this->user_model->search_product($barcode);
		//print_r(sizeof($data['data']));die();
		if(sizeof($data['data'])>0){

			$this->load->view('user/admin/addinstruction',$data);
		}else{
				
				$error=array();
				if(empty($productName)){
					
					$error['error']="enter product name !";
					$this->load->view('user/admin/addproduct',$error);
				}
				else if(empty($brandName)){
					$error['error']="enter brand name !";
				return	$this->load->view('user/admin/addproduct',$error);
				}
				else if(empty($_FILES['file'])){
					$error['error']=" upload image!";
				return	$this->load->view('user/admin/addproduct',$error);
				}
				else{

					if(isset($_FILES['file']) && $_FILES['file']!=null){
		 					//echo"rfgweg";die();
						      $errors= array();
						      $file_name =uniqid().".png";
						      $file_size =$_FILES['file']['size'];
						      $file_tmp =$_FILES['file']['tmp_name'];
						      $file_type=$_FILES['file']['type'];
						      $tmp = explode('.', $_FILES['file']['name']);

						      $file_ext=end($tmp);;
						      
						      $expensions= array("jpeg","jpg","png");
						      
						      if(in_array($file_ext,$expensions)=== false){
						         $errors[]="extension not allowed, please choose a JPEG or PNG file.";
						      }
						      
						      if($file_size > 2097152){
						         $errors[]='File size must be excately 2 MB';
						      }
						      
						      if(empty($errors)==true){
						      	 $uploadfile = "/home/skinner/public_html/assets/productImg/".$file_name;
						        // move_uploaded_file($file_tmp,"http://leaselance.com/skinnerapp/assets/profilepic/".$file_name);


										if (move_uploaded_file($_FILES["file"]["tmp_name"], $uploadfile)) {
										       // echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
										      // echo $uploadfile; die();
										       $name="assets/productImg/".$file_name ;

										     $data['data']=$this->user_model->add_barcode($barcode,$productName,$brandName,$name);
										       $this->load->view('user/admin/addinstruction',$data);
										    } else {
										        echo "Sorry, there was an error uploading your file.";
										    }

						         
						      }
                       }
					
				}
				 
				
				//print_r($_FILES['file']);
				//$file = $this->input->post('file');echo"2ed2d2";
				


				

				

    	      }


		//print_r($data);
	     }	


	  public function dummyImage(){

	     	$data['data']=$this->user_model->dummy_Image();
	     	 $this->load->view('user/admin/dummyImage',$data);
	     }
	 public function addDummyImage(){

	     	$this->load->view('user/admin/addDummyImage');
	     }
	 public function uploadImages(){
	 		//echo phpinfo();
	 	
						 	   if (isset($_POST['submit'])) {
					$j = 0;     // Variable for indexing uploaded image.
					$target_path = '/home/skinner/public_html/assets/dummyimg/';     // Declaring Path for uploaded images.
					for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
					// Loop to get individual element from the array
					$validextensions = array("jpeg", "jpg", "png");      // Extensions which are allowed.
					$ext = explode('.', basename($_FILES['file']['name'][$i]));   // Explode file name from dot(.)
					$file_extension = end($ext); // Store extensions in the variable.
					$target_path = $target_path . md5(uniqid()) . "." . $ext[count($ext) - 1];     // Set the target path with a new name of image.
					$j = $j + 1;      // Increment the number of uploaded images according to the files in array.
					if (($_FILES["file"]["size"][$i] < 1000000)     // Approx. 100kb files can be uploaded.
					&& in_array($file_extension, $validextensions)) {
					if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $target_path)) {
					// If file moved to uploads folder.
						//print_r($target_path);
						$link=explode("/home/skinner/public_html/",$target_path);
						/*echo "<pre>";
						print_r($link[1]);
						echo "</pre>";*/

						
						echo "<pre>";
						print_r ($link);
						echo "</pre>";
						die;
					$this->user_model->Img_upload($link[1]);

					} else {     //  If File Was Not Moved.
					echo $j. ').<span id="error">please try again!.</span><br/><br/>';
					}
					} else {     //   If File Size And File Type Was Incorrect.
					echo $j. ').<span id="error">***Invalid file Size or Type***</span><br/><br/>';
					}
					}
					redirect(base_url('/index.php/user/dummyImage'), 'refresh');
					
					}




	 	}
	 	public function searchbyspecialist(){
	 		//print_r($_POST);die();


     	    $search = $this->input->post('search');
     	

     		$data['sepcialuserlist']=$this->user_model->search_specialist($search);
     		//print_r($data);
     		if(sizeof($data['sepcialuserlist'])>0){
     			//print_r($data);die();
     			//print_r($data);
     			$this->load->view('user/admin/searchbyspecialist',$data);
     			
     			//$this->load->view('pagename',$datapassed, TRUE);
     		}else
     		{
     			$data['sepcialuserlist']=1;
     			//print_r($data);
     			$this->load->view('user/admin/searchbyspecialist',$data);
     		}

		    // $this->load->view('/user/admin/specialist',$data);
     	


     }
     public function searchbypatient(){

     		$search = $this->input->post('search');
     	

     		$data['patient']=$this->user_model->search_patient($search);
     		//print_r($data);die();
     		if(sizeof($data['patient'])>0){
     			//print_r($data);die();
     			//print_r($data);
     			$this->load->view('user/admin/searchbypatient',$data);
     			
     			//$this->load->view('pagename',$datapassed, TRUE);
     		}else
     		{
     			$data['patient']=1;
     			//print_r($data);
     			$this->load->view('user/admin/searchbypatient',$data);
     		}
     }	
     public function searchbymember(){

     	$search = $this->input->post('search');
     	

     		$data['subadminlist']=$this->user_model->search_member($search);
     		//print_r($data);die();
     		if(sizeof($data['subadminlist'])>0){
     			//print_r($data);die();
     			//print_r($data);
     			$this->load->view('user/admin/searchbymember',$data);
     			
     			//$this->load->view('pagename',$datapassed, TRUE);
     		}else
     		{
     			$data['subadminlist']=1;
     			//print_r($data);
     			$this->load->view('user/admin/searchbymember',$data);
     		}

     }	    
     public function searchbyallproduct(){

     	$search = $this->input->post('search');
     	$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/allproduct'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->search_product_rows($search)
		];

			// Close tag for CURRENT link.
			$config['cur_tag_close'] = '</a>';

			// By clicking on performing NEXT pagination.
			$config['next_link'] = 'Next';

			// By clicking on performing PREVIOUS pagination.
			$config['prev_link'] = 'Previous';
	
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data['allproduct'] = $this->user_model->search_all_product($config['per_page'],$page,$search);
	  //	print_r($data);
		if(sizeof($data['allproduct'])>0){
     			//print_r($data);die();
     			//print_r($data);
     			$this->load->view('/user/admin/searchbyallproduct',$data);
     			
     			
     		}else
     		{
     			$data['allproduct']=1;
     			//print_r($data);
     			$this->load->view('/user/admin/searchbyallproduct',$data);
     		}
		

     } 
     public function searchbyAdminProduct(){
     	$search = $this->input->post('search');
     	$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/adminProduct'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->search_admin_product_rows($search)
		];

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
			
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data['allproduct'] = $this->user_model->search_admin_product($config['per_page'],$page,$search);
		if(sizeof($data['allproduct'])>0){
     			//print_r($data);die();
     			//print_r($data);
     			$this->load->view('/user/admin/searchbyAdminProduct',$data);
     			
     			
     		}else
     		{
     			$data['allproduct']=1;
     			//print_r($data);
     			$this->load->view('/user/admin/searchbyAdminProduct',$data);
     		}

     }

     	public function searchproductForApprovel(){
		$search=$this->input->post('search');

		

	    $this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/searchproductForApprovel'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->search_product_For_Approvel_row($search)
		];

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
	
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data = $this->user_model->search_product_For_Approvel($search,$config['per_page'],$page);
	  //	print_r($data);
		$pass=array();
		$checked="checked";
		$unchecked="unchecked";
		if(sizeof($data)>0){


			for($i=0;$i<sizeof($data);$i++){
			$pass['pass'][]='<tr class="lovepreet" id="preet_'.$data[$i]->barcodeId.'">
                                        <td class="w-10">
                                            <img src="'.base_url($data[$i]->img).'" alt="">
                                        </td>
                                        <td>
                                            
                                            <small class="text-muted">'.$data[$i]->productName.'</small><h6>'.$data[$i]->brandName.'</h6>
                                        </td>
                                        <td>'.$data[$i]->barcodeSrNo.'</td>
                                        
                                        
                                        <td>
                                          
                                          	<select class="selctaction"> <option value="select">Select Option</option> <option value="1,'.$data[$i]->barcodeId.'">Approved</option> <option value="0,'.$data[$i]->barcodeId.'">Dis-Approved</option>  </select>

                                          </td>

                                 
                                           
                                        
                                    </tr>';
                                }
                                   //print_r($pass);die();


				}else{

					$pass['pass']='<tr><td class="w-10">No Data</td></tr>';

				}

				echo json_encode($pass);
		



}		

	public function searchapproved(){
		$search=$this->input->post('search');

		

	    $this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/searchapproved'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->searchapproved_row($search)
		];

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
	
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data = $this->user_model->searchapproved($search,$config['per_page'],$page);
	  //	print_r($data);
		$pass=array();
		$checked="checked";
		$unchecked="unchecked";
		if(sizeof($data)>0){


			for($i=0;$i<sizeof($data);$i++){
			$pass['pass'][]='<tr id="preet_'.$data[$i]->barcodeId.'">
                                        <td class="w-10">
                                            <img src="'.base_url($data[$i]->img).'" alt="">
                                        </td>
                                        <td>
                                            
                                            <small class="text-muted">'.$data[$i]->productName.'</small><h6>'.$data[$i]->brandName.'</h6>
                                        </td>
                                        <td>'.$data[$i]->barcodeSrNo.'</td>
                                        
                                        
                                        <td><label class="switch">
                                          <span class="slider round checkedit"></span>
                                          <input type="checkbox" '.($data[$i]->status==1 ? $checked : $unchecked).' class="checkedit " id="check1" data-value="'.$data[$i]->status .'" data-barcode="'.$data[$i]->barcodeId .'">
                                          <span class="slider round "></span>
                                        </label></td>
                                       '.($data[$i]->productId==null ? '<td ><a class="btn-fab btn-fab-sm btn-primary shadow text-white" title="add to explor list" href="javascript:addToAdmin('.$data[$i]->barcodeId.')" id="kbc_'.$data[$i]->barcodeId.'"><i class="icon-pencil"></i></a>
                                            </td>' : '<td>product Allready Add</td> ').'

                                        <td>
                                                <a  title="Edit" href="javascript:editbarcode('.$data[$i]->barcodeId.')"><i class="icon-pencil"></i></a>
                                            </td>
                                           
                                        
                                    </tr>';
                                }
                                   //print_r($pass);die();


				}else{

					$pass['pass']='<tr><td class="w-10">No Data</td></tr>';

				}

				echo json_encode($pass);
		



}

	public function searchdisapproved(){
		$search=$this->input->post('search');

		

	    $this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/searchapproved'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->searchdisapproved_row($search)
		];

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
	
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data = $this->user_model->searchdisapproved($search,$config['per_page'],$page);
	  //	print_r($data);
		$pass=array();
		$checked="checked";
		$unchecked="unchecked";
		if(sizeof($data)>0){


			for($i=0;$i<sizeof($data);$i++){
			$pass['pass'][]='<tr id="preet_'.$data[$i]->barcodeId.'">
                                        <td class="w-10">
                                            <img src="'.base_url($data[$i]->img).'" alt="">
                                        </td>
                                        <td>
                                            
                                            <small class="text-muted">'.$data[$i]->productName.'</small><h6>'.$data[$i]->brandName.'</h6>
                                        </td>
                                        <td>'.$data[$i]->barcodeSrNo.'</td>
                                        
                                        
                                        <td><label class="switch">
                                          <span class="slider round checkedit"></span>
                                          <input type="checkbox" '.($data[$i]->status==1 ? $checked : $unchecked).' class="checkedit " id="check1" data-value="'.$data[$i]->status .'" data-barcode="'.$data[$i]->barcodeId .'">
                                          <span class="slider round "></span>
                                        </label></td>

                                        
                                           
                                        
                                    </tr>';
                                }
                                   //print_r($pass);die();


				}else{

					$pass['pass']='<tr><td class="w-10">No Data</td></tr>';

				}

				echo json_encode($pass);
		



}

     public function productForApprovel(){
		
		$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/productForApprovel'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->product_For_Approvel_row()
		];

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
	
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data = $this->user_model->product_For_Approvel($config['per_page'],$page);
	  //	print_r($data);
		$pass=array();
		$checked="checked";
		$unchecked="unchecked";
		if(sizeof($data)>0){


			for($i=0;$i<sizeof($data);$i++){
			$pass['pass'][]='<tr class="lovepreet" id="preet_'.$data[$i]->barcodeId.'">
                                        <td class="w-10">
                                            <img src="'.base_url($data[$i]->img).'" alt="">
                                        </td>
                                        <td>
                                            
                                            <small class="text-muted">'.$data[$i]->productName.'</small><h6>'.$data[$i]->brandName.'</h6>
                                        </td>
                                        <td>'.$data[$i]->barcodeSrNo.'</td>
                                        
                                        
                                        <td>
                                          
                                          	<select class="selctaction"> <option value="select">Select Option</option> <option value="1,'.$data[$i]->barcodeId.'">Approved</option> <option value="0,'.$data[$i]->barcodeId.'">Dis-Approved</option>  </select>

                                          </td>

                                        
                                           
                                        
                                    </tr>';
                                }
                                   //print_r($pass);die();


		}else{

			$pass['pass'][]='<tr><td class="w-10">No Data</td></tr>';

		}
		$this->load->view('/user/admin/productForApprovel',$pass);
		
		
}
	
	public function approved(){
		
		$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/approved'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->approved_row()
		];

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
	
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data = $this->user_model->approved($config['per_page'],$page);
	  //	print_r($data);die();
		$pass=array();
		$checked="checked";
		$new='new';
		$unchecked="unchecked";
		if(sizeof($data)>0){


			for($i=0;$i<sizeof($data);$i++){
			$pass['pass'][]='<tr class="lovepreet" id="preet_'.$data[$i]->barcodeId.'">
                                        <td class="w-10">
                                            <img src="'.base_url($data[$i]->img).'" alt="">
                                        </td>
                                        <td>
                                            
                                            <small class="text-muted">'.$data[$i]->productName.'</small><h6>'.$data[$i]->brandName.'</h6>
                                        </td>
                                        <td>'.$data[$i]->barcodeSrNo.'</td>
                                        
                                        
                                        <td><label class="switch">
                                          <span class="slider round checkedit"></span>
                                          <input type="checkbox" '.($data[$i]->status==1 ? $checked : $unchecked).' class="checkedit " id="check1" data-value="'.$data[$i]->status .'" data-barcode="'.$data[$i]->barcodeId .'">
                                          <span class="slider round "></span>
                                        </label></td>


                                        '.($data[$i]->productId==null ? '<td  id="kbc_'.$data[$i]->barcodeId.'"><a class="btn-fab btn-fab-sm btn-primary shadow text-white" title="add to explor list" href="javascript:addToAdmin('.$data[$i]->barcodeId.')"><i class="icon-pencil"></i></a>
                                            </td>' : '<td >product Allready Add</td> ').'<td id="kvc_'.$data[$i]->barcodeId.'" style="display:none;">product Allready Add</td>
                                         

                                        <td>


                                                <a  title="edit" href="javascript:editbarcode('.$data[$i]->barcodeId.')"><i class="icon-pencil"></i></a>
                                            </td>
                                           
                                        
                                    </tr>';
                                }
                                   //print_r($pass);die();


		}else{

			$pass['pass'][]='<tr><td class="w-10">No Data</td></tr>';

		}
		$this->load->view('/user/admin/approved',$pass);
		
		
}

	public function disapproved(){
		
		$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/disapproved'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->disapproved_row()
		];

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
	
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data = $this->user_model->disapproved($config['per_page'],$page);
	  //	print_r($data);
		$pass=array();
		$checked="checked";
		$unchecked="unchecked";
		if(sizeof($data)>0){


			for($i=0;$i<sizeof($data);$i++){
			$pass['pass'][]='<tr class="lovepreet" id="preet_'.$data[$i]->barcodeId.'">
                                        <td class="w-10">
                                            <img src="'.base_url($data[$i]->img).'" alt="">
                                        </td>
                                        <td>
                                            
                                            <small class="text-muted">'.$data[$i]->productName.'</small><h6>'.$data[$i]->brandName.'</h6>
                                        </td>
                                        <td>'.$data[$i]->barcodeSrNo.'</td>
                                        
                                        
                                        <td><label class="switch">
                                          <span class="slider round checkedit"></span>
                                          <input type="checkbox" '.($data[$i]->status==1 ? $checked : $unchecked).' class="checkedit " id="check1" data-value="'.$data[$i]->status .'" data-barcode="'.$data[$i]->barcodeId .'">
                                          <span class="slider round "></span>
                                        </label></td>

                                       
                                           
                                        
                                    </tr>';
                                }
                                   //print_r($pass);die();


		}else{

			$pass['pass'][]='<tr><td class="w-10">No Data</td></tr>';

		}
		$this->load->view('/user/admin/disapproved',$pass);
		
		
}
	public function status(){


		$val=$_POST['fieldvalue'];
		$checkid=$_POST['checkid'];
				if($val==1){
					$val=0;
				}
				else{
					$val=1;
				}

				$data['new_data_id']=$this->user_model->status($val,$checkid);
				echo json_encode($checkid);
				
												 
		

	}
	public function searchbybarcode(){

		$search = $this->input->post('search');
     	$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/searchbybarcode'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->search_bybarcode_row($search)
		];
				
		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
	
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data['allproduct'] = $this->user_model->search_by_barcode($config['per_page'],$page,$search);
		if(sizeof($data['allproduct'])>0){
     			//print_r($data);die();
     			//print_r($data);
     			$this->load->view('/user/admin/searchbybarcode',$data);
     			
     			
     		}else
     		{
     			$data['allproduct']=1;
     			//print_r($data);
     			$this->load->view('/user/admin/searchbybarcode',$data);
     		}
	}
	public function changePassword(){

		$this->load->view('/user/admin/changePassword');
	}

	public function setnewpass(){
    	
    	$data=array();
    	$id=$_SESSION['user_id'];
    	$currentpassword=$this->input->post('oldpassword');
    	$newpassword= $this->input->post('password');

    	$this->load->helper('form');
		$this->load->library('form_validation');
		
		// set validation rules
		
		$this->form_validation->set_rules('password', 'password', 'required');
		$this->form_validation->set_rules('password_confirm', 'Confirm Password', 'trim|required|min_length[6]|matches[password]');
		if ($this->form_validation->run() == false) {
			$data['status']=0;
			$data['response']=validation_errors();
			echo json_encode($data);
			
		} else {


    	$checkpass=$this->user_model->checkpass($currentpassword,$id);
    	if($checkpass==1){
    		$this->user_model->change_Pass($newpassword,$id);
    		$data['status']=1;
    		$data['response']="Password change successfully !";
    		
    		echo json_encode($data);
    	}else{

    		$data['status']=0;
    		$data['response']="Your current password invalid !";
    		echo json_encode($data);
    	}
    	}

    }
    public function deldumimg(){

    	$id=$this->input->get('id');
    	$this->user_model->del_dum_img($id);
    	$this->dummyImage();
    }
    public function gallery(){
    	if (isset($_SESSION['username'])){
    	$id=$this->input->get('id');
    	$data['gallery']=$this->user_model->gallery($id);
    	//print_r($data); die();
    	$this->load->view('user/admin/gallery',$data);
    	 }else{redirect(base_url('/login'), 'refresh');}
    }


    public function export(){
    	if (isset($_SESSION['username'])){

    	 $this->user_model->export();
    	}else{

    		redirect(base_url('/login'), 'refresh');
    	}
    }
     public function adminImport(){

     		///print_r($_FILES['csv_file']);die();
     					if($_FILES['csv_file']['size']==0){
     						$data3['status']=2;	
						     $data3['data']="please select file to import !";

     					}else{

     					 $mimes = explode(".",$_FILES['csv_file']['name']);
     					
     					if($mimes[1]=='csv'){

					    $ok = true;
					    $file = $_FILES['csv_file']['tmp_name'];
					    $handle = fopen($file, "r");

					    if ($file == NULL) {
					         $data3['status']=3;	
						     $data3['result']="please select VALID FORMENT to import !";
					    }
					    else {
					    	
					      while(($filesop = fgetcsv($handle, 10000000, ",")) !== false)
					        {
					        	if($filesop[0]!=null){

					        		if($filesop[12]==null){

					        			$path=null;
					        		}else{

					        			$path='/assets/videos/'.$filesop[12];
					        		}

					        	$data[]=array($filesop[0],$filesop[1],$filesop[2],$filesop[3],$filesop[4],$filesop[5],$filesop[6],$filesop[7],$filesop[8],$filesop[9],$filesop[10],$filesop[11],$path,$filesop[13],$filesop[14],$filesop[15]);
							}
					      }
					      
					   // print_r($data);die();
					     $data1=$this->user_model->admin_Import($data);
					     //print_r($data1);die();
					     if(sizeof($data1)>0){
					     $data3['status']=1;	
					     $data3['number']=sizeof($data1['data']);
					   
					   // remove special character from array 
						foreach ($data1['data'] as $key => $value) {
							 array_walk($value, function (&$item) {
						                    $item = strval($item);
						                    $item = htmlspecialchars($item);
						                    $item = mb_convert_encoding( $item, 'UTF-8', 'UTF-8');
						                    $item = html_entity_decode($item);
						                });  
							 $newData[$key]=$value;
						}


					     $data3['result']=$newData;
					     
					     
					     $data3['insert']=$data1['insert'];

					      }else{

					      	 $data3['status']=0;	
						     $data3['number']=0;
						     $data3['result']=array();
					      }
					    

				
             }
         }else{

					         $data3['status']=3;	
						     $data3['result']="please select VALID FORMENT to import !";        

         }
     }
     // /print_r($data3) ;
  
            echo json_encode($data3);
               
  }

  public function meargeproduct(){

              	//print_r($_POST);die();
  				$data=$this->input->post('data');
  				//print_r($data);die();
  				$result=$this->user_model->mearge_product($data);

  				if($result==1){
  					echo json_encode(1);
  				}
  				else{
  					echo json_encode(0);
  				}


  }
  public function exportview() {
			
			$this->load->view('user/admin/exportview');
		
	}

	public function uploadzip(){
		
		if($_FILES["zip_file"]["name"]) {
				$filename = $_FILES["zip_file"]["name"];
				$source = $_FILES["zip_file"]["tmp_name"];
				$type = $_FILES["zip_file"]["type"];
				
				$name = explode(".", $filename);
				$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
				foreach($accepted_types as $mime_type) {
					if($mime_type == $type) {
						$okay = true;
						break;
					} 
				}
				
				$continue = strtolower($name[1]) == 'zip' ? true : false;
				if(!$continue) {
					$message['message'] = "The file you are trying to upload is not a .zip file. Please try again.";
				}elseif($_FILES["zip_file"]["size"] > 300000000){

					$message['message'] = "Please selct zip file size less than 290mb";

				}else{

				$target_path = "/home/skinner/public_html/assets/videos".$filename;  // change this to the correct site path
				if(move_uploaded_file($source, $target_path)) {
					$zip = new ZipArchive();
					$x = $zip->open($target_path);
					if ($x === true) {
						$zip->extractTo("/home/skinner/public_html/assets/videos"); // change this to the correct site path
						$zip->close();
				
						unlink($target_path);
					}
					$message['message'] = "Your .zip file was uploaded and unpacked.";
				} else {	
					$message['message'] = "There was a problem with the upload. Please try again.";
				}
		}}

			echo json_encode($message);

	}
	public function imageuploadzip(){

		if($_FILES["zip_image"]["name"]) {
				$filename = $_FILES["zip_image"]["name"];
				$source = $_FILES["zip_image"]["tmp_name"];
				$type = $_FILES["zip_image"]["type"];
				
				$name = explode(".", $filename);
				$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
				foreach($accepted_types as $mime_type) {
					if($mime_type == $type) {
						$okay = true;
						break;
					} 
				}
				
				$continue = strtolower($name[1]) == 'zip' ? true : false;
				if(!$continue) {
					$message['message'] = "The file you are trying to upload is not a .zip file. Please try again.";
				}else{

				$target_path = "/home/skinner/public_html/assets/productImg/".$filename;  // change this to the correct site path
				if(move_uploaded_file($source, $target_path)) {
					$zip = new ZipArchive();
					$x = $zip->open($target_path);
					if ($x === true) {
						$zip->extractTo("/home/skinner/public_html/assets/productImg/"); // change this to the correct site path
						$zip->close();
				
						unlink($target_path);
					}
					$message['message'] = "Your .zip file was uploaded and unpacked.";
				} else {	
					$message['message'] = "There was a problem with the upload. Please try again.";
				}
		}}

			echo json_encode($message);

	}
	public function alertmessage(){

		$data['data']=$this->user_model->alert_message();
		$this->load->view('/user/admin/alertmessage',$data);
	}
	public function updatemessage(){
		$message = $this->input->post('message');
		$id = $this->input->post('id');
		$this->user_model->update_message($message,$id);
		$this->alertmessage();
	}
	public function editbarcode(){

		$id = $this->input->post('id');
		$data=$this->user_model->edit_barcode($id);
		echo json_encode($data);
	}
	public function editbarcodesubmit(){

		//print_r($_POST);
		//print_r($_FILES);
		$productName = $this->input->post('productName');
		$img = $this->input->post('img');
		$brandName = $this->input->post('brandName');
		$barcodeSrNo = $this->input->post('barcodeSrNo');

			 if($_FILES['file']['size']>0){
		 
						      $errors= array();
						      $file_name =uniqid().".png";
						      $file_size =$_FILES['file']['size'];
						      $file_tmp =$_FILES['file']['tmp_name'];
						      $file_type=$_FILES['file']['type'];
						      $tmp = explode('.', $_FILES['file']['name']);

						      $file_ext=end($tmp);;
						      
						      $expensions= array("jpeg","jpg","png");
						      
						      if(in_array($file_ext,$expensions)=== false){
						         $errors[]="extension not allowed, please choose a JPEG or PNG file.";
						      }
						      
						      if($file_size > 2097152){
						         $errors[]='File size must be excately 2 MB';
						      }
						      
						      if(empty($errors)==true){
						      	 $uploadfile = "/home/skinner/public_html//assets/productImg/".$file_name;
						        // move_uploaded_file($file_tmp,"http://leaselance.com/skinnerapp/assets/profilepic/".$file_name);


										if (move_uploaded_file($_FILES["file"]["tmp_name"], $uploadfile)) {
											$img="/assets/productImg/".$file_name;

											$this->user_model->edit_barcode_submit($productName,$img,$brandName,$barcodeSrNo);
										       // echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
										        //echo "Success";
										    } else {
										        echo "Sorry, there was an error uploading your file.";
										    }

						         
						      }
						  }else{
						  		$this->user_model->edit_barcode_submit($productName,$img,$brandName,$barcodeSrNo);
						  }

						  	echo json_encode(1);

	}
	public function changestatus(){

		$data = explode(",",$this->input->post('status'));
		$status=$data[0];
		$barcodeId=$data[1];
		$this->user_model->change_status($status,$barcodeId);
		echo json_encode($barcodeId);
	}
	public function addToAdmin(){

		$id = $this->input->post('id');
		$data=$this->user_model->edit_barcode($id);
		echo json_encode($data);
	}
	public function showgallery(){

		$data['response']='true';
		$userId = $this->input->post('userId');
		
			$datatype = $this->input->post('datatype');

			
			if($this->input->post('datatype')=="image"){
				$type=1;
			}else{

				$type=0;

			}
			

			if($this->input->get('pagenumber')!=null)
				{
					$pagenumber = $this->input->get('pagenumber');
				}
				else
				{
					 $pagenumber=1;
				}
				$data['page']=$this->page(ceil($this->user_model->show_gallery_count($this->input->post('search'),$userId,$type)/2),base_url('index.php/user/showgallery'),$pagenumber,$datatype,$this->input->post('search'));
				$limit=2;
				$offset=2*$pagenumber-2;

					
				  if(ceil($this->user_model->show_gallery_count($this->input->post('search'),$userId,$type)/2) < $pagenumber){
					$data['data']=array();
					$data['response']='false';
				}else{
				 $data['data']=$this->user_model->show_gallery($this->input->post('search'),$userId,$limit,$offset,$type);
				
				}

					 echo json_encode($data);



	}
	public function page($total,$siteurl,$currentpage,$type,$search){
			
			$page=array();
			for($i=1;$i<=$total;$i++){
				if($currentpage==$i){

					$page[]='<strong>'.$i.'</strong>';
				}else{
					$page[]='<a  href="javascript:openPageajax(`'.$siteurl.'?pagenumber='.$i.'`,`'.$type.'`,`'.$search.'`)"  >'.$i.'</a>';
					
				}
				
			}
			return $page ;


	}

		public function allbrand(){

		$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/allbrand'),
		'per_page'=>15,
		'total_rows'=>$this->user_model->allbrand_total_rows()
		];
		$config['cur_tag_open'] = '&nbsp;<a class="current">';

		// Close tag for CURRENT link.
		$config['cur_tag_close'] = '</a>';

		// By clicking on performing NEXT pagination.
		$config['next_link'] = 'Next';

		// By clicking on performing PREVIOUS pagination.
		$config['prev_link'] = 'Previous';
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$data['allbrand'] = $this->user_model->allbrand_list($config['per_page'],$page);
	  	//print_r($data);

		$this->load->view('/user/admin/allbrand',$data);

	}
	public function editbrandnamesubmit(){

		//print_r($_FILES);

		$oldbrand = $this->input->post('oldbrand');
		$img = $this->input->post('img');
		$brandName = $this->input->post('brandName');
		

			 if($_FILES['file']['size']>0){
		 
						      $errors= array();
						      $file_name =uniqid().".png";
						      $file_size =$_FILES['file']['size'];
						      $file_tmp =$_FILES['file']['tmp_name'];
						      $file_type=$_FILES['file']['type'];
						      $tmp = explode('.', $_FILES['file']['name']);

						      $file_ext=end($tmp);;
						      
						      $expensions= array("jpeg","jpg","png");
						      
						      if(in_array($file_ext,$expensions)=== false){
						         $errors[]="extension not allowed, please choose a JPEG or PNG file.";
						      }
						      
						      if($file_size > 2097152){
						         $errors[]='File size must be excately 2 MB';
						      }
						      
						      if(empty($errors)==true){
						      	 $uploadfile = "/home/skinner/public_html//assets/productImg/".$file_name;
						        // move_uploaded_file($file_tmp,"http://leaselance.com/skinnerapp/assets/profilepic/".$file_name);


										if (move_uploaded_file($_FILES["file"]["tmp_name"], $uploadfile)) {
											$img="/assets/productImg/".$file_name;

											$this->user_model->edit_brand_submit($oldbrand,$img,$brandName);
										       // echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
										        //echo "Success";
										    } else {
										        echo "Sorry, there was an error uploading your file.";
										    }

						         
						      }
						  }else{
						  		$this->user_model->edit_brand_submit($oldbrand,$img,$brandName);
						  }

						  	echo json_encode(1);
	}
}
			