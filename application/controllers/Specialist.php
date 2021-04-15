<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User class.
 * 
 * @extends CI_Controller
 */
class Specialist extends CI_Controller {

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
        $this->load->model('Special_model');
        $this->load->model('Patient_model');
        $messageFile = file_get_contents(base_url()."/assets/message/message.json");
        
        

        $this->messages = json_decode($messageFile);
            
        // Use try-catch
        // JWT library throws exception if the token is not valid
       
       
        if (($this->router->fetch_method() != "setResponse")) {  //token disabled for set response and gallery api
             if (($this->router->fetch_method() != "gallery")) {
                $this->checkthetoken();
            }
      
        }
     
    
        // authorisation code block end
    }
    // check the token 
    public function checkthetoken() {
        
        // authorisation code block start

        $this->load->helper(['jwt', 'authorization']);

        $headers = $this->input->request_headers();
        // Extract the token
        if (!isset($headers['Authtoken'])) {
            $status = "false";
            $response = ['status' => $status, 'message' => 'Please provide Authtoken in headers.', 'data' => []];
            $this->response($response, $status);
            exit();
        }
        $token = $headers['Authtoken'];
        try {
                // Validate the token
                // Successfull validation will return the decoded user data else returns false
                $data = AUTHORIZATION::validateToken($token);
                if ($data === false) {
                    $status = "false";
                    $response = ['status' => $status, 'message' => 'Unauthorized Access! In valid token', 'data' => []];
                    $this->response($response, $status);
                    exit();
                } else {
                    if ($data->after == true) {
                        if (!$this->Special_model->getencpassword($data->userid, $data->password)) {
                            $status = "false";
                            $response = ['status' => $status, 'message' => 'Session Expired ! Login Again', 'data' => []];
                            $this->response($response, $status);
                            exit();
                        }
                    }
                }
            } catch (Exception $e) {
                // Token is invalid
                // Send the unathorized access message
                $status = "false";
                $response = ['response' => $status, 'message' => 'Unauthorized Access! Exception occurs	 ', 'data' => []];
                $this->response($response, $status);
                exit();
            }

    }

    // send response via this function
    public function response($data) {
       
        header('Content-Type: application/json');
        echo json_encode($data);
    }
	
	/**
	 * register function.
	 * 
	 * @access public
	 * @return void
	 */

	public function message(){

		$message=$this->Special_model->message();
		return $message;
	}
	
    public function register() {
        $loginStrategy = $this->input->post('loginStrategy');

        $userId = $this->input->post('userId');
        $memberId = $this->input->post('memberId');
        if ($this->input->post('memberId') != null) {
            $data3[] = $this->Special_model->member_Id($userId, $memberId);
            $data['response'] = "true";
            $data['message'] = $this->message()[2]->message;
            $data['data'] = $data3;
            $this->response($data);
        } else {
            // load form helper and validation library
            $this->load->helper('form');
            $this->load->library('form_validation');
            $email = $this->input->post('email');
            // set validation rules
            if ($this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[sepcial_users.email]')->run() === false && ($loginStrategy == "local-oauth")) {
                $data['response'] = "false";
                $data['message'] = $this->message()[0]->message;
                $data['data'] = array();
                $this->response($data);
            } elseif ($this->Special_model->check_phone($this->input->post('phone')) == 1 && ($loginStrategy == "local-oauth")) {
                $data['response'] = "false";
                $data['message'] = $this->message()[1]->message;
                $data['data'] = array();
                $this->response($data);
            } else {
                $userName = $this->input->post('userName');
                $age = $this->input->post('age');
                $city = $this->input->post('city');
                $proPicName = $this->input->post('proPicName');
                $countary = $this->input->post('countary');
                $deviceId = $this->input->post('deviceId');
                $password = $this->input->post('password');
                $yearOfExperience = $this->input->post('yearOfExperience');
                $gender = $this->input->post('gender');
                $phone = $this->input->post('phone');
                $email = $this->input->post('email');
                $aboutme = $this->input->post('aboutme');
                $token = $this->input->post('token');
                $type = $this->input->post('type');
                $jobProfile = $this->input->post('jobProfile');
                $emailPrivacy = $this->input->post('emailPrivacy');
                $phonePrivacy = $this->input->post('phonePrivacy');
                $loginStrategy = $this->input->post('loginStrategy');
                $profileId = $this->input->post('profileId');
                  

            
                //$barcodeId = $this->input->post('barcodeId');
            
                //echo $existbar->barcodeSrNo;
                //print_r($existbar);die();
                if (base64_decode($proPicName, true)) {
                    $current = base64_decode($proPicName);
                    $file = uniqid() . '.png';
                    $current .= "/";
                    file_put_contents(FCPATH . '/assets/profilepic/' . $file, $current);
                    $link = 'assets/profilepic/' . $file;
                } else {
                    $link = $proPicName;
                }

                if ($result = $this->Special_model->create_user(
                    $userName,
                    $age,
                    $city,
                    $link,
                    $countary,
                    $yearOfExperience,
                    $password,
                    $gender,
                    $phone,
                    $email,
                    $aboutme,
                    $token,
                    $type,
                    $deviceId,
                    $emailPrivacy,
                    $phonePrivacy,
                    $jobProfile ,
                    $loginStrategy,
                    $profileId

                )) {
                        // generate the qr code
                    $this->generateToken($result[0]->userId);

                    // create token

                    $tokenData = [
                        'userid' => $result[0]->userId,
                        'password' => $result[0]->password,
                        'type' => 'specialist',
                        'time' => time(),
                        'after' => true
                    ];
                                
                                
                                
                    // Create a token
                    $token = AUTHORIZATION::generateToken($tokenData);
                    $result[0]->authtoken = $token;

                    $data['response'] = "true";
                    $data['message'] = $this->message()[2]->message;
                    $data['data'] = $result;

                    $this->response($data);
                    $productExists = $this->Special_model->product_Exists($deviceId);

                    if ($productExists == 1) {
                        $this->Special_model->insert_specialist_rel($result[0]->userId, $deviceId);
                        $this->Special_model->delete_product_device($deviceId);
                    }
                } else {
                    $this->response("There was a problem creating your new account. Please try again");
                }
            }
        }
    }	

	 public function contactspace(){

     		$id=$this->input->post('userId');
     		$subject=$this->input->post('subject');
     		$message=$this->input->post('message');
     		$email=$this->Special_model->get_email($id);
     		//print_r($email[0]->email);
     		
       			$to      = "sumitpatial51@gmail.com";
				$subject = $subject;
				$message = $message ;
				$headers = $email[0]->email . phpversion();

           $result= mail($to, $subject, $message, $headers);

           if(!$result){
           			$data['response']="false";
           			$data['message']="mail failed";
           			$data['data']=array();
           			$this->response($data);
           }else{

           		   $data1= $this->Special_model->insert_email_data($id,$subject,$message);
           		   $data['response']="true";
           			$data['message']="mail send";
           			$data['data']=$data1;
           			$this->response($data);
           }
     }
	public function gallery(){

		$userId = $this->input->post('userId');
		$media = $this->input->post('img');
		$eyes = $this->input->post('eyes');
		$forhead = $this->input->post('forhead');
		$neck = $this->input->post('neck');
		$allFace = $this->input->post('allFace');
		$status = $this->input->post('status');
		$result=0;
		if(isset($_FILES['file']) && $_FILES['file']!=null){
							 $type=0;
							  $errors= array();
							  $file_name =uniqid().".mp4";
							  $file_size =$_FILES['file']['size'];
							  $file_tmp =$_FILES['file']['tmp_name'];
							  $file_type=$_FILES['file']['type'];
							  $tmp = explode('.', $_FILES['file']['name']);

							  $file_ext=end($tmp);;
							  
							  $expensions= array("mp4","avi","mov","3gp");
							  
							  if(in_array($file_ext,$expensions)=== false){
								 $errors[]="extension not allowed, please choose a  valid formt.";
							  }
							  
							  if($file_size > 2097152555){
								 $errors[]='File size large';
							  }
							  
							  if(empty($errors)==true){
								 $uploadfile = "/home/skinner/public_html/assets/gallery/".$file_name;
								// move_uploaded_file($file_tmp,"http://leaselance.com/skinnerapp/assets/profilepic/".$file_name);


										if (move_uploaded_file($_FILES["file"]["tmp_name"], $uploadfile)) {

											 $name="assets/gallery/".$file_name;
											  

											  $result=$this->Special_model->upload_gallery($userId,$name,$eyes,$forhead,$neck,$allFace,$status,$type);
											} 

								 
							  }
		   }

		else
		{

					$type=1;
					/*$current=base64_decode($media);
					$file = uniqid().'.png';
					$current .= "/";
					file_put_contents('/home/skinner/public_html/assets/gallery/'.$file,$current);
					$link='assets/gallery/'.$file;*/






					 $j = 0;    
					$target_path = '/home/skinner/public_html/assets/gallery/';     // Declaring Path for uploaded images.
					$count = $this->input->post('count');
					for ($i = 1; $i <=$count; $i++) {

					       $errors= array();
							  $file_name =uniqid().".png";
							  $file_size =$_FILES['images'.$i]['size'];
							  $file_tmp =$_FILES['images'.$i]['tmp_name'];
							  $file_type=$_FILES['images'.$i]['type'];
							  $tmp = explode('.', $_FILES['images'.$i]['name']);

							  $file_ext=end($tmp);;
							  
							  $expensions= array("jpeg", "jpg", "png"); 
							  
							  if(in_array($file_ext,$expensions)=== false){
								 $errors[]="extension not allowed, please choose a  valid formt.";
							  }
							  
							  if($file_size > 1000000){
								 $errors[]='File size large';
							  }
							  
							  if(empty($errors)==true){

								 $uploadfile = FCPATH."/assets/gallery/".$file_name;
						
										if (move_uploaded_file($_FILES['images'.$i]["tmp_name"], $uploadfile)) {
											
											 $name="assets/gallery/".$file_name;

											
											$result=$this->Special_model->upload_gallery($userId,$name,$eyes,$forhead,$neck,$allFace,$status,$type);

		
											} 
											
											
											
											
 
							  }
			}
			


		}
		if($result==1){
			$data['response']="true";
			$data['message']="upload successfully!";
			
		}else{

			$data['response']="false";
			$data['message']="error !";
			
		}

		$this->response($data);

	}
	public function showgallery(){
		$data['response']='true';
		$userId = $this->input->post('userId');
		
			$type = $this->input->post('type');
			if($this->input->post('type')=="image"){
				$type=1;
			}else{

				$type=0;

			}

			if($this->input->post('pagenumber')!=null)
				{
					$pagenumber = $this->input->post('pagenumber');
				}
				else
				{
					 $pagenumber=1;
				}
				$limit=10;
				$offset=10*$pagenumber-10;
			
				if(ceil($this->Special_model->show_gallery_count($this->input->post('search'),$userId,$type)/10) < $pagenumber){
					$data['data']=array();
					$data['response']='false';
				}else{
				 $data['data']=$this->Special_model->show_gallery($this->input->post('search'),$userId,$limit,$offset,$type);
				
				}

					 $this->response($data);



	}


	
	public function public_private_button(){

		$userId = $this->input->post('userId');
		$type=$this->input->post('type');
		$id = $this->input->post('id');
		$status = $this->input->post('status');
		$result=$this->Special_model->public_private_button($userId,$type,$id,$status);
		if($result==1){
			$data['response']="true";
			$data['message']="update !";

		}else{
			$data['response']="false";
			$data['message']="error !";
		}
		$this->response($data);

	}
	public function deletegallery(){

		$userId = $this->input->post('userId');
		$search=$this->input->post('search');
		$id = $this->input->post('id');
		$result=$this->Special_model->delete_gallery($userId,$search,$id);
		if($result==1){
			$data['response']="true";
			$data['message']="delete !";

		}else{
			$data['response']="false";
			$data['message']="error !";
		}
		$this->response($data);
	}
    public function login() {
        //print_r($this->message()[0]->message); die;

        $this->load->helper('form');
        $this->load->library('form_validation');

        // set variables from the form
        $username = $this->input->post('userName');
        $password = $this->input->post('password');
        $token = $this->input->post('token');
        $type = $this->input->post('type');
        $deviceId = $this->input->post('deviceId');
    
        if ($this->form_validation->set_rules('userName', 'Email', 'trim|required|valid_email|is_unique[sepcial_users.email]')->run() === false) {
            //	$this->Patient_model->resolve_user_login($username, $password); die;
            if ($this->Special_model->resolve_user_login($username, $password)) {
                $user_id = $this->Special_model->get_user_id_from_username($username);

                $user  = $this->Special_model->get_user($user_id, $token, $type, $deviceId);
                $arr = (array)$user;
                if (!empty($arr)) {
                    $tokenData = [
                        'userid' => $user->userId,
                        'password' => $user->password,
                        'type' => 'specialist',
                        'time' => time(),
                        'after' => true
                    ];
                    // Create a token
                    $token = AUTHORIZATION::generateToken($tokenData);
                    $user->authtoken = $token;
                }

                // set session user datas
                $_SESSION['user_id']      = (int)$user->userId;
                $_SESSION['username']     = (string)$user->userName;
                $_SESSION['proPicName']     = (string)$user->proPicName;
                //$_SESSION['name']     = (string)$user->fullName;
                $_SESSION['logged_in'] = (bool)true;
                //$_SESSION['is_confirmed'] = (bool)$user->is_confirmed;
                //$_SESSION['is_admin']     = (bool)$user->userRole;
                $data['response'] = "true";
                $data['message'] = $this->message()[7]->message;
                $data['data'] = $user;
                $this->response($data);
            } else {
                $data['response'] = "false";
                $data['message'] = $this->message()[9]->message;
                $data['data'] = array();
                $this->response($data);
            }
        } else {
            $data['response'] = "false";
            $data['message'] = $this->message()[8]->message;
            $data['data'] = array();
            $this->response($data);
        }
    }
	public function showprofile(){

		
			$id= $this->input->get('id');

		   
		   

			$data['profile']=$this->Special_model->show_profile($id);

			$this->response($data);
		
		
	}

	public function barcode(){
		
			
			$barcodesrno = $this->input->post('barcodesrno');
			//$productimg = $this->input->post('productimg');	
			$existbar=$this->Special_model->exist_bar($barcodesrno);
			$reject=$this->Special_model->reject_barcode($barcodesrno);
				
				if($reject>0){
					$data['response']="product rejected";
					$this->response($data);
				  }elseif(!$existbar){
					$data['response']="false";
					$this->response($data);
				  }else{
							$data['response']="true";
							$data['data']=$existbar;
							$this->response($data);
						  }



	}
	public function checkitems(){

				$deviceId = $this->input->post('deviceId');
				$userId=$this->input->post('userId');
				$existitem=$this->Special_model->check_items($deviceId,$userId);
				if($existitem==1){
					$data['response']='true';
					$data['message']=$this->message()[3]->message;
					
					$this->response($data);
				}else{
					$data['response']='false';
					$data['message']='Please insert product';
					
					$this->response($data);

				}


	}
    public function editprofile() {
        $id = $this->input->post('userId');
        $age = $this->input->post('age');
        $name = $this->input->post('name');
        $city = $this->input->post('city');
        $proPicName = $this->input->post('proPicName');
        $countary = $this->input->post('countary');
        $phone = $this->input->post('phone');
        $gender = $this->input->post('gender');
        $aboutme = $this->input->post('aboutme');
        $yearOfExperience = $this->input->post('yearOfExperience');
        $emailPrivacy = $this->input->post('emailPrivacy');
        $phonePrivacy = $this->input->post('phonePrivacy');
        $jobProfile = $this->input->post('jobProfile');

        //$email = $this->input->post('email');
        if (base64_decode($proPicName, true)) {
            $current = base64_decode($proPicName);
            $file = uniqid() . '.png';
            $current .= "/";
            file_put_contents(FCPATH . '/assets/profilepic/' . $file, $current);
            $link = 'assets/profilepic/' . $file;
        } else {
            $link = $proPicName;
        }
        $result = $this->Special_model->edit_profile($id, $age, $city, $link, $countary, $phone, $gender, $name, $aboutme, $yearOfExperience,$emailPrivacy,$phonePrivacy,$jobProfile);
        $data['response'] = "true";
        $data['message'] = "update successfully";
        $data['data'] = $result;
        $this->response($data);
    }

	public function addproduct(){
		ini_set("upload_max_filesize","300MB");
					
					$barcodesrno = $this->input->post('barcodesrno');
					$productName = $this->input->post('productName');
					$brandname = $this->input->post('brandname');
					$file_name = $this->input->post('file');
				 
					$existbar=$this->Special_model->exist_bar($barcodesrno);
										
				if(!$existbar){
					$current=base64_decode($file_name);
						$file = uniqid().'.png';
						$current .= "/";
						file_put_contents('/home/skinner/public_html/assets/productImg/'.$file,$current);
						$link='assets/productImg/'.$file;	
						//print_r( $link);
						 $this->Special_model->create_barcode($productName,$brandname,$barcodesrno, $link);
				  }	
					
								
				

				
				$deviceId = $this->input->post('deviceId');
				$userId=$this->input->post('userId');
				$checkproduct=$this->Special_model->check_product($deviceId,$userId,$barcodesrno);
				//echo $checkproduct;
				if($checkproduct==1){
					$data['response']='false';
					$data['message']=$this->message()[3]->message;
					
					$this->response($data);
				}else{
				if($userId!=null){
					$data['data']=$this->Special_model->add_product($barcodesrno,$userId);
				   // $data['data']=$this->Special_model->add_product_device($barcodesrno,$deviceId);
				}else{
					$data['data']=$this->Special_model->add_product_device($barcodesrno,$deviceId);
				}
				if($data['data']==1){
					$response['response']='true';
					$response['message']=$this->message()[5]->message;
					$this->response($response);
				}else{
					$response['response']='false';
					$this->response($response);
				}
		}	
	}
	public function viewproduct(){
			//print_r($_POST);
			$id = $this->input->post('userId');
			$deviceId = $this->input->post('deviceId');
			$data = $this->Special_model->view_product($id,$deviceId);
			
			if(sizeof($data)>0){
				$data1['response']="true";
				$data1['message']=$this->message()[12]->message;
				$data1['data']=$data;
				$this->response($data1);
			}else{
				$data1['response']="false";
				$data1['message']="no product ";
				$data1['data']=array();
				$this->response($data1);
			}
			
		}
	
	public function viewproductbrandwise() {
        //print_r($_POST);
        $data = array();
        $query = $this->db->query(
            "select img, brandName   from brand_name"
        );
        
        
        $i=0;
        foreach ($query->result() as $row) {
           
            
            $data[$i]['key'] = ucwords($row->brandName);
            $data[$i]['keyImage'] = $row->img;
            
            $query1 = $this->db->query(
            "select *  from admin_product where brandName LIKE '%" . $row->brandName . "%'" );

            $data[$i]['value'] = $query1->result_array();
          //  break;
            $i++;

        }
    //     echo "<pre>"; print_r($data); 
    $data1 = array();
        if(!empty($data)){
            $data1['response']="true";
            $data1['message']="All products";
            $data1['data']=$data;
         
        }else{
            $data1['response']="false";
            $data1['message']="no product ";
            $data1['data']=array();
            
        }
        $this->response($data1);

    }
public function addProductInstruction() {
        //print_r( $this->input->post('am'));
        //print_r($_POST);
        $specialId = $this->input->post('specialId');
        $barcodeId = $this->input->post('barcodeId');
        $barcodeSrNo = $this->input->post('barcodeSrNo');
        $productName = $this->input->post('productName');
        $brandName = $this->input->post('brandName');
        $eyes = $this->input->post('eyes');
        $forhead = $this->input->post('forhead');
        $allFace = $this->input->post('allFace');
        $neck = $this->input->post('neck');
        $everyday = $this->input->post('everyday');
        $onceAweek = $this->input->post('onceAweek');
        $twiceAweek = $this->input->post('twiceAweek');
        $onceAmonth = $this->input->post('onceAmonth');
        $instruction = $this->input->post('instruction');
        $am = $this->input->post('am');
        $pm = $this->input->post('pm');
        $minute = $this->input->post('minute');
        $step = $this->input->post('step');
        $numberOfStep = $this->input->post('numberOfStep');
        $img = $this->input->post('img');
        $media = $this->input->post('media');
        ;   
        if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload'] != null) {
            $errors = array();
            $file_name = uniqid() . ".mp4";
            $file_size = $_FILES['fileToUpload']['size'];
            $file_tmp = $_FILES['fileToUpload']['tmp_name'];
            $file_type = $_FILES['fileToUpload']['type'];
            $tmp = explode('.', $_FILES['fileToUpload']['name']);

            $file_ext = end($tmp);
            ;

            $expensions = array("mp4", "avi", "mov", "3gp");

            if (in_array($file_ext, $expensions) === false) {
                $errors[] = "extension not allowed, please choose a  valid formt.";
            }

            if ($file_size > 209715255) {
                $errors[] = 'File size large';
            }

            if (empty($errors) == true) {
                $uploadfile = FCPATH . "/assets/videos/" . $file_name;
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $uploadfile)) {
                    $name = "assets/videos/" . $file_name;
                    $filterdata = $this->Special_model->create_shedual(
                        $specialId,
                        $barcodeId,
                        $barcodeSrNo,
                        $productName,
                        $brandName,
                        $eyes,
                        $allFace,
                        $forhead,
                        $neck,
                        $everyday,
                        $onceAweek,
                        $twiceAweek,
                        $onceAmonth,
                        $instruction,
                        $am,
                        $pm,
                        $minute,
                        $step,
                        $numberOfStep,
                        $name,
                        $img
                    );
                    if ($filterdata) {
                        $data['response'] = "true";
                        $data['message'] = $this->message()[12]->message;
                        $data['data'] = array();
                        $this->response($data);
                    } else {
                        $data['response'] = "false";
                        $data['message'] = "error";
                        $data['data'] = array();
                        $this->response($data);
                    }
                }
            } else {
                $filterdata = $this->Special_model->create_shedual(
                    $specialId,
                    $barcodeId,
                    $barcodeSrNo,
                    $productName,
                    $brandName,
                    $eyes,
                    $allFace,
                    $forhead,
                    $neck,
                    $everyday,
                    $onceAweek,
                    $twiceAweek,
                    $onceAmonth,
                    $instruction,
                    $am,
                    $pm,
                    $minute,
                    $step,
                    $numberOfStep,
                    $media,
                    $img
                );
                if ($filterdata == 1) {
                    $data['response'] = "true";
                    $data['message'] = "add product";
                    $data['data'] = array();
                    $this->response($data);
                } else {
                    $data['response'] = "false";
                    $data['message'] = "error";
                    $data['data'] = array();
                    $this->response($data);
                }
            }
        } else {
            $filterdata = $this->Special_model->create_shedual(
                $specialId,
                $barcodeId,
                $barcodeSrNo,
                $productName,
                $brandName,
                $eyes,
                $allFace,
                $forhead,
                $neck,
                $everyday,
                $onceAweek,
                $twiceAweek,
                $onceAmonth,
                $instruction,
                $am,
                $pm,
                $minute,
                $step,
                $numberOfStep,
                $media,
                $img
            );
            if ($filterdata == 1) {
                $data['response'] = "true";
                $data['message'] = $this->message()[12]->message;
                $data['data'] = array();
                $this->response($data);
            } else {
                $data['response'] = "false";
                $data['message'] = "error";
                $data['data'] = array();
                $this->response($data);
            }
        }
    }
	public function viewinstruction(){

			$specialistId = $this->input->post('specialistId');
			$barcodeSoNo = $this->input->post('barcodeSoNo');
			$data1=$this->Special_model->view_instruction($specialistId,$barcodeSoNo);
			if(sizeof($data1)>0){
				$data['response']="true";
				$data['message']='product instruction';
				$data['data']=$data1;
				$this->response($data);

			}else{

				$data['response']="false";
				$data['message']="no instruction";
				$data['data']=array();
				$this->response($data);
			}
	}

	public function uploadVedio(){

			$specialId = $this->input->post('specialId');
			$barcodeSrNo = $this->input->post('barcodeSrNo');

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
								 $uploadfile = "/home/skinner/public_html/assets/videos/".$file_name;
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
public function showrequest() {
        /*$data=$this->Special_model->show_request();
        $this->response($data);*/
        $id = $this->input->post('userId');
        $pagenumber = $this->input->post('pagenumber');
        $this->load->library('pagination');
        $config = [
            'base_url' => base_url('index.php/user/showrequest'),
            'per_page' => 10,
            'total_rows' => $this->Special_model->total_row($id)
        ];
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data = array();
        
       $pagenumber=($pagenumber-1)*10;
        
        // $data1 = $this->Special_model->show_request($config['per_page'], $pagenumber, $id);
        $data1 = $this->Special_model->show_request( $id);

        if (sizeof($data1) > 0) {
            $data['response'] = "true";
            $data['message'] = "patient list";
            $data['data'] = $data1;
            $this->response($data);
        } else {
            $data['response'] = "false";
            $data['message'] = "patient list empty";
            $data['data'] = array();
            $this->response($data);
        }
    }

	public function patientProfile(){

		$userId=$this->input->post('userId');
		   $patientId = $this->input->post('patientId');
		   $profile=$this->Special_model->patient_Profile($patientId);

		   	    $data['response']="true";
				$data['message']="patient profile";
				$data['profile']=$profile;
				$data['corncern']=$this->patientConcern($patientId,$userId);
				 $this->response($data);

	}

	public function patientproduct(){
		   $userId=$this->input->post('userId');
		   $patientId = $this->input->post('patientId');



		   if($this->input->post('pagenumber')!=null){
				$pagenumber = $this->input->post('pagenumber');
				}
				else
				{ 
					$pagenumber=1;
				}
				$limit=10;
				$offset=10*$pagenumber-10;
				$this->load->library('pagination');
				$config=['base_url'=>base_url('index.php/Patient/patientproduct'),
				'per_page'=>5,
				'total_rows'=>$this->Special_model->total_patient_product($userId,$patientId)
				];
				$config['use_page_numbers']=TRUE;
				$this->pagination->initialize($config);

				$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
				$data =array();
				$data['response']='true';
				if(ceil($this->Special_model->total_patient_product($userId,$patientId)/10) < $pagenumber){
					$data['data']=array();
					$data['response']='false';
				}else{
				$data['data'] = $this->Special_model->patient_product($userId,$patientId,$limit,$offset);
				}
				 $this->response( $data);





	}
	public function patientConcern($patientId,$userId){
			
			return $res=$this->Special_model->patient_Concern($patientId,$userId);
		      	

	}
	public function createShedualForUser(){
		$userId=$this->input->post('userId');
		$patientId = $this->input->post('patientId');
		$data1 = $this->Special_model->create_Shedual_For_User($patientId,$userId);

		if(sizeof($data1)>0){
			 $data['response']="true";
				$data['message']="patient list";
				$data['data']=$data1;
				 $this->response($data);
		
		}else{

				$data['response']="false";
				$data['message']="no data";
				$data['data']=array();
				 $this->response($data);
		}

	}
		public function addToResponse(){
				
				$barcodesrno=$this->input->post('barcodesrno');
				$patientId=$this->input->post('patientId');
				$specialistId=$this->input->post('specialistId');
				$result=$this->Special_model->add_To_Response($patientId,$barcodesrno,$specialistId);
				//print_r($result);die();
				if(sizeof($result)>0){
							$data['response']="true";
							$data['message']="patient list";
							$data['data']=$result;
							

							$this->response($data);

				}else{
						$data['response']="false";
						$data['message']="no product detail";
						$data['data']=array();
						$this->response($data);


				}
		}
		public function setResponse(){
				$patientId = $this->input->post('patientId');
				$createdate = date("Y-m-d");
				//$userId = date("userId");				
				$barcodeSrNo = $this->input->post('barcodeSrNo');
				$productName = $this->input->post('productName');
				$brandName = $this->input->post('brandName');
				$img = $this->input->post('img');
				$eyes = $this->input->post('eyes');
				$forhead = $this->input->post('forhead');
				$neck = $this->input->post('neck');
				$everyday = $this->input->post('everyday');
				$onceAweek = $this->input->post('onceAweek');
				$twiceAweek = $this->input->post('twiceAweek');
				$onceAmonth = $this->input->post('onceAmonth');
				$instruction = $this->input->post('instruction');
				$am = $this->input->post('am');
				$pm = $this->input->post('pm');
				$minute = $this->input->post('minute');
				$step = $this->input->post('step');
				$numberOfStep = $this->input->post('numberOfStep');
				$media = $this->input->post('media');
				$specialistId = $this->input->post('specialistId');


				$result=$this->Special_model->set_Response($patientId,$createdate,$barcodeSrNo,$productName,$brandName,$img,$eyes,$forhead,$neck,$everyday,$onceAweek,$twiceAweek,$onceAmonth,$instruction,$am,$pm,$minute,$step,$numberOfStep,$media,$specialistId);
				if($result==1){

						 $data['response']="true";
						$data['message']="add";
						$data['data']=array();
						 $this->response($data);
		
				}else{

						$data['response']="false";
						$data['message']="error";
						$data['data']=array();
						$this->response($data);
				}	



		}

		public function deleteproduct(){
					$userId=$this->input->post('userId');
					$deviceId=$this->input->post('deviceId');
					$productId=$this->input->post('productId');
					
					if($this->Special_model->delete_product($userId,$deviceId,$productId)==1){

						$data['response']='true';
						$data['message']=$this->message()[6]->message;
						
					}else{

						$data['response']='false';
						$data['message']='product not delete';

					}
					$this->response($data);
		}
		public function deleteSchduelProduct(){
			$barcodeSrNo = $this->input->post('barcodeSrNo');
			$patientId = $this->input->post('patientId');
			$status = $this->input->post('status');

		   $result = $this->Special_model->delete_Schduel_Product($barcodeSrNo,$patientId,$status);
		   if($result==1){

						 $data['response']="true";
						$data['message']="delete";
						$data['data']=array();
						 $this->response($data);
		
				}else{

					   $data['response']="false";
						$data['message']="error";
						$data['data']=array();
						 $this->response($data);
				}
		}
		public function addProductResponse(){
			ini_set("upload_max_filesize","300MB");
					
					$barcodesrno = $this->input->post('barcodesrno');
					$productName = $this->input->post('productName');
					$brandname = $this->input->post('brandname');
					$file_name = $this->input->post('file');
				 
					$existbar=$this->Special_model->exist_bar($barcodesrno);
										
				if(!$existbar){
					$current=base64_decode($file_name);
						$file = uniqid().'.png';
						$current .= "/";
						file_put_contents('/home/skinner/public_html/assets/productImg/'.$file,$current);
						$link='assets/productImg/'.$file;	
						//print_r( $link);
						 $this->Special_model->create_barcode($productName,$brandname,$barcodesrno, $link);
				  }	
					
								
				

				
				$patientId=$this->input->post('patientId');
				$userId=$this->input->post('userId');
				$checkproduct=$this->Special_model->check_product_response($patientId,$barcodesrno);
				//echo $checkproduct;
				if($checkproduct==1){
					$data['response']='false';
					$data['message']='product Allready exist';
					
					
				}else{
				
					$this->Special_model->add_product_response($barcodesrno,$userId,$patientId);
				   // $data['data']=$this->Special_model->add_product_device($barcodesrno,$deviceId);
					$data['response']='true';
					$data['message']='product add';
				
				}
				 $this->response($data);
			
		}

		
    public function sendresponse() {
        $userId = $this->input->post('userId');
        $patientId = $this->input->post('patientId');
        $barcodeSrNo = $this->input->post('barcode');


        $result = $this->Special_model->send_response($userId, $patientId,$barcodeSrNo);
      
        if ($result == 1) {
            
          
            
            $tokens = $this->Special_model->get_Patient_Token($patientId);
            $Specialistname = $this->Special_model->get_specialist_name($userId);
            /*code for notifification*/
            $title = "Your schedule is ready !";
            // $notification_message = 'You get response from ' . $Specialistname[0]->userName . ' for your request. ';
            $notification_message = 'Click to see what '. $Specialistname[0]->userName.' made for you.';
            $headerdata = $this->getheaderdata();
            
            $uid = $headerdata->userid;
            $fcmdata = array(
                    'specialistId' =>$uid,
                    );


            foreach ($tokens as $value) {
                $userId = $value->userId;

                // send notification if only enable otherwise break the loop
                if (!$this->Patient_model->send_me_notification($userId)) break;



                 
                if ($value->type == "IOS") {
                    ob_start();
                    $url = 'https://fcm.googleapis.com/fcm/send';
                    $fields = array(
                        'to' => $value->token,
                        'notification' => array(
                            "head" => $title,
                            'title' => $notification_message,
                            'sound' => 'default',
                            'badge' => 1,
                        ),
                         'data' => $fcmdata

                    );
                    $fields = json_encode($fields);
                    $headers = array(
                        'Authorization: key=' . "AAAALlGM_1A:APA91bGGucMP2XZz4lcKSLsOD9RB-zdcqz-sd9buH1pEScK2giRS4G0SutZH6mL6GJmTapyT0WzTzLj63N1oT9Lu4xyI_B6syOaODm9MznvTbPpgSookgcsbRZDWYBkbzcGS9UZVNZ70",
                        'Content-Type: application/json'
                    );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

                    $result = curl_exec($ch);
                    curl_close($ch);
                    $result;
                    ob_flush();
                } else {
                     ob_start();
                    $apiKey = "AAAALlGM_1A:APA91bGGucMP2XZz4lcKSLsOD9RB-zdcqz-sd9buH1pEScK2giRS4G0SutZH6mL6GJmTapyT0WzTzLj63N1oT9Lu4xyI_B6syOaODm9MznvTbPpgSookgcsbRZDWYBkbzcGS9UZVNZ70";

                    $msg = [
                        'message' => $notification_message,
                        'title' => $title,
                        'URL' => "",
                        'notification_type' => "treatement_response",
                         'data' => $fcmdata
                    ];


                    $fields = array(
                        'registration_ids' => array($value->token),
                        'data' => $msg
                    );

                    $headers = [
                        'Authorization: key=' . $apiKey,
                        'Content-Type: application/json'
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    $result = curl_exec($ch);

                    //    print_r($result);
                    curl_close($ch);
                    // return $result;
                    ob_flush();
                }
            }


                    

            // save data array if the result of curl is successfull

          
                // data to save into the fcm notification table
                $notificationData = array(
                    'userId' => $patientId,
                    'title' => $title,
                    'message'   => $notification_message,
                    'entity '   => 'patient',
                    'type' => 'treatement_response',
                    'data' => serialize($fcmdata),
                    'utctime ' => date("Y-m-d H:i:s"),
                    'readStatus ' => false,
                );
                  $result = $this->Patient_model->savefcmnotidication($notificationData);
             
            $data['response'] = "true";
            $data['message'] = "Response send successfully";
            $data['data'] = array();
            $this->response($data);
            /*end*/
        } else {
            $data['response'] = "false";
            $data['message'] = "error";
            $data['data'] = array();
            $this->response($data);
        }
    }
		 public function changepassword(){
				$id=$this->input->post('userId');
				$currentpassword=$this->input->post('currentpassword');
				$newpassword= $this->input->post('newpassword');
				$checkpass=$this->Special_model->checkpass($currentpassword,$id);
				if($checkpass==1){
					$this->Special_model->change_Pass($newpassword,$id);
					$data['response']="true";
					$data['message']="Password change successfully !";
					$data['data']=array();
					$this->response($data);
				}else{
					$data['response']="false";
					$data['message']=$this->message()[15]->message;
					$data['data']=array();
					$this->response($data);
				}
	}
    public function logout() {
		
		$userId=$this->input->post('userId');
		$token=$this->input->post('token');
		$deviceId=$this->input->post('deviceId');
		$this->Special_model->distroy_token( $userId,$token,$deviceId);
		$data['response']='true';
		$data['message']='user logout';
		$data['data']=array();
		$this->response($data);
		
	}
	public function forgetpassword(){

	   $email = $this->input->post('email');
	   $check=$this->Special_model->checkemail( $email);
	   if($check==1){
				$newpassword=$this->generateRandomString();
				$to      = $email;
				$subject = 'NEW PASSWORD';
				$message = "this is your Password <h3>".$newpassword."</h3>" ;
				$headers = 'From: sumit@smartdesizns.co.in' . "\r\n" .
					'Reply-To: webmaster@example.com' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();

			mail($to, $subject, $message, $headers);
			$this->Special_model->change_password('12345',$email);
		  
				$data['response']='true';
				$data['message']='new password has been sent to Your emailid';
				$data['data']= $email;

				$this->response($data);
	   }else{
				$data['response']='false';
				$data['message']='This email address not exist';
				$data['data']= array();

				$this->response($data);
			
	   }
					

	}
	public function exploreProductListasbrand(){
		
		$data['response']='true';
	
	 if($this->input->post('searchbrand')!=null)
	 {
				if($this->input->post('pagenumber')!=null)
				{
					$pagenumber = $this->input->post('pagenumber');
				}
				else
				{
					 $pagenumber=1;
				}
				$limit=10;
				$offset=10*$pagenumber-10;
			
				  if(ceil($this->Special_model->total_search($this->input->post('searchbrand'))/10) < $pagenumber){
					$data['data']=array();
					$data['response']='false';
				}else{
				 $data['data']=$this->Special_model->search_explore_Product_List($this->input->post('searchbrand'),$limit,$offset);
				
				}

					 $this->response($data);


   }
   else
   {
			if($this->input->post('pagenumber')!=null){
				$pagenumber = $this->input->post('pagenumber');
				}
				else
				{ 
					$pagenumber=1;
				}
				$limit=10;
				$offset=10*$pagenumber-10;
				$this->load->library('pagination');
				$config=['base_url'=>base_url('index.php/Patient/exploreProductList'),
				'per_page'=>5,
				'total_rows'=>$this->Special_model->total_num()
				];
				$config['use_page_numbers']=TRUE;
				$this->pagination->initialize($config);

				$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
				$data =array();
				$data['response']='true';
				if(ceil($this->Special_model->total_num()/10) < $pagenumber){
					$data['data']=array();
					$data['response']='false';
				}else{
				$data['data'] = $this->Special_model->explore_Product_List($limit,$offset);
				}
				 $this->response( $data);
		}
	}


	 public function exploreProductListasproduct() {
        $filter = $this->input->post('filter');
        $filterarray = explode(",", $filter);
        // $searchspecialist = $this->input->post('searchspecialist');
        $data['response'] = 'true';

        if ($this->input->post('searchproduct') != null) {
            if ($this->input->post('pagenumber') != null) {
                $pagenumber = $this->input->post('pagenumber');
            } else {
                $pagenumber = 1;
            }
            $limit = 10;
            $offset = 10 * $pagenumber - 10;

            if (ceil($this->Special_model->exploreProductListasproduct_total_search($this->input->post('searchproduct')) / 10) < $pagenumber) {
                $data['data'] = array();
                $data['response'] = 'false';
            } else {
                $data['data'] = $this->Special_model->exploreProductListasproduct_search_explore_Product_List($this->input->post('searchproduct'), $limit, $offset);
            }

            $this->response($data);
        } else {
            if ($this->input->post('pagenumber') != null) {
                $pagenumber = $this->input->post('pagenumber');
            } else {
                $pagenumber = 1;
            }
            $limit = 10;
            $offset = 10 * $pagenumber - 10;
            $this->load->library('pagination');
            $config = [
                'base_url' => base_url('index.php/Patient/exploreProductList'),
                'per_page' => 10,
                'total_rows' => $this->Special_model->exploreProductListasproduct_total_num()
            ];
            $config['use_page_numbers'] = true;
            $this->pagination->initialize($config);

            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
            $data = array();
            $data['response'] = 'true';
            if (ceil($this->Special_model->exploreProductListasproduct_total_num() / 10) < $pagenumber) {
                $data['data'] = array();
                $data['response'] = 'false';
            } else {
                $data['data'] = $this->Special_model->exploreProductListasproduct_explore_Product_List($limit, $offset, $filterarray);
            }
            $this->response($data);
        }
    }
	public function addToMyList(){

		$barcodeSrNo = $this->input->post('barcodeSrNo');
		$deviceId = $this->input->post('deviceId');
		$userId = $this->input->post('userId');
		
		   $res= $this->Special_model->add_To_MyList($barcodeSrNo,$deviceId,$userId);
		   	if($res==1){
			   $data['response']='true';
				$data['message']='add to list';
				$data['data']=array();
			}else{
				$data['response']='false';
				$data['message']='Error';
				$data['data']=array();
			}
				$this->response($data);
	}
	public function showproductbybrand(){

				$data['response']='true';
				$brandName= $this->input->post('brandName');
			 if($this->input->post('searchproduct')!=null)
				 {
					if($this->input->post('pagenumber')!=null)
					{
						$pagenumber = $this->input->post('pagenumber');
					}
					else{
						 $pagenumber=1;
					}
					$limit=10;
					$offset=10*$pagenumber-10;
				
					  if(ceil($this->Special_model->showproductbybrand_total_search($this->input->post('searchproduct'),$brandName)/10) < $pagenumber){
						$data['data']=array();
						$data['response']='false';
					}else{
					 $data['data']=$this->Special_model->showproductbybrand_search_explore_Product_List($this->input->post('searchproduct'),$brandName,$limit,$offset);
					
					}

						 $this->response($data);


			   }
		   else
			   {
				if($this->input->post('pagenumber')!=null){
					$pagenumber = $this->input->post('pagenumber');
					
					}
					else
					{ 
						$pagenumber=1;
					}
					$limit=10;
					$offset=10*$pagenumber-10;
					$this->load->library('pagination');
					$config=['base_url'=>base_url('index.php/Patient/exploreProductList'),
					'per_page'=>10,
					'total_rows'=>$this->Special_model->showproductbybrand_total_num($brandName)
					];
					$config['use_page_numbers']=TRUE;
					$this->pagination->initialize($config);

					$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
					$data =array();
					$data['response']='true';
					if(ceil($this->Special_model->showproductbybrand_total_num($brandName)/10) < $pagenumber){
						$data['data']=array();
						$data['response']='false';
					}else{
					$data['data'] = $this->Special_model->showproductbybrand_explore_Product_List($brandName,$limit,$offset);
					}
					 $this->response( $data);
			}
				
	}
	public function myclients(){

		$userId = $this->input->post('userId');
		if($this->input->post('pagenumber')!=null){
		  $pagenumber = $this->input->post('pagenumber');
		  }
		  else
		  { 
			$pagenumber=1;
		  }
		   $limit=10;
		  $offset=10*$pagenumber-10;
		
		$this->load->library('pagination');
		$config=['base_url'=>base_url('index.php/user/myclients'),
		'per_page'=>10,
		'total_rows'=>$this->Special_model->my_clients_count($userId)
		];
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data =array();
		$result = $this->Special_model->my_clients($userId,$limit,$offset);

		 
			if(sizeof($result)>0){

				$data['response']='true';
				$data['message']='requested list';
				$data['data']=$result;				
			}
			else{
				$data['response']='false';
				$data['message']='no requested';
				$data['data']=array();	
			}

				$this->response($data);
				
	}
	function is_base64($s){
	// Check if there are valid base64 characters
	if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;

	// Decode the string in strict mode and check the results
	$decoded = base64_decode($s, true);
	if(false === $decoded) return false;

	// Encode the string again
	if(base64_encode($decoded) != $s) return false;

	return true;
}
	public function chat(){

		$name = $this->input->post('name');
		$userId = $this->input->post('userId');
		$message = $this->input->post('message');
		
		$tokens = $this->Special_model->get_token($userId);
		
		//print_r($token);
		/*code for notifification*/
				$link='';
				if ( base64_encode(base64_decode($message, true)) === $message){
					
					$current=base64_decode($message);
						$file = uniqid().'.png';
						$current .= "/";
						file_put_contents('/home/skinner/public_html/assets/chatimg/'.$file,$current);
						$link='assets/chatimg/'.$file;
						   $link=$this->Special_model->chat_img($link)[0]->img;
						   $message="image";
							
							
						}else{
							
							$message=$message;
						}


								$Specialistname=$this->Special_model->get_specialist_name($userId);
						 /*code for notifification*/

						 foreach ($tokens as  $value) {
						 if($value->type=="IOS"){
								ob_start();
								$url = 'https://fcm.googleapis.com/fcm/send';
									$fields = array (
											'to' =>$value->token ,
											'notification' => array ("head"=>'message','title' => $name,
								            'text' => $message,
								            'sound' => 'default',
								            "data"=>$link,
								            'badge' => 1,
								            
									        )





									
									);
									$fields = json_encode ( $fields );
									$headers = array (
											'Authorization: key=' . "AAAALlGM_1A:APA91bGGucMP2XZz4lcKSLsOD9RB-zdcqz-sd9buH1pEScK2giRS4G0SutZH6mL6GJmTapyT0WzTzLj63N1oT9Lu4xyI_B6syOaODm9MznvTbPpgSookgcsbRZDWYBkbzcGS9UZVNZ70",
											'Content-Type: application/json'
									);

									$ch = curl_init ();
									curl_setopt ( $ch, CURLOPT_URL, $url );
									curl_setopt ( $ch, CURLOPT_POST, true );
									curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
									curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
									curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

									$result = curl_exec ( $ch );
									curl_close ( $ch );
									 $result;  
									 ob_flush();
									}else{

										$apiKey = "AAAALlGM_1A:APA91bGGucMP2XZz4lcKSLsOD9RB-zdcqz-sd9buH1pEScK2giRS4G0SutZH6mL6GJmTapyT0WzTzLj63N1oT9Lu4xyI_B6syOaODm9MznvTbPpgSookgcsbRZDWYBkbzcGS9UZVNZ70";
									
									
										$notification_message =  $name;
										$msg =
											[
											'message' => $message,
											'title' =>$name ,
											'URL' => "",
										
											'notification_type' => "message",
										   
										];
									   
								   
									$fields = array(
										'registration_ids' => array($value->token),
										'data' => $msg
									);

									$headers =
										[
										'Authorization: key=' . $apiKey,
										'Content-Type: application/json'
									];
									$ch = curl_init();
									curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
									curl_setopt($ch, CURLOPT_POST, true);
									curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
									curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
									curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
									$result = curl_exec($ch);

								 //  print_r($result);
									curl_close($ch);
									return $result;  
									ob_flush();
								}
							}

									  $data['response']='true';
									  $data['message']='message send';
									  $this->response($data);
	}
	public function fcmNotification(){

		$name = $this->input->post('name');
		$userId = $this->input->post('userId');
		$message = $this->input->post('message');
		$imageURL = $this->input->post('imageURL');
		$videoURL = $this->input->post('videoURL');


		$token = $this->Special_model->get_token($userId)[0]->token;
		$type = $this->Special_model->get_token($userId)[0]->type;
		//print_r($token);
		/*code for notifification*/
				


								 ob_start();
								  if($type=="IOS"){
									 ob_start();
								  
									$apiKey = "AAAAH8rGUy8:APA91bFPSQ5ZRFKDKs7sLk95wctduJODnDqEm4tI98F8Ru4KJa9ALXAGPe9VxoMorHvWlMYohFC0RpqVOB3JFrSj03PXJSAkqksOsucjZ7zxnWxhylmAycQOgCDmx3AJBa2qI0Q07nXv";




									
							
								$url = 'https://fcm.googleapis.com/fcm/send';
									$fields = array (
											'to' =>$token ,
											'notification' => array (
												"head"=>'myschedule page',
													"body" => $message,
													"title" => $name,
													"icon" => "myicon",
													"data"=>$link
											)
									);
									  
									$fields = json_encode ( $fields );
									$headers = array (
											'Authorization: key=' . "AAAAH8rGUy8:APA91bFPSQ5ZRFKDKs7sLk95wctduJODnDqEm4tI98F8Ru4KJa9ALXAGPe9VxoMorHvWlMYohFC0RpqVOB3JFrSj03PXJSAkqksOsucjZ7zxnWxhylmAycQOgCDmx3AJBa2qI0Q07nXv",
											'Content-Type: application/json'
									);

									$ch = curl_init ();
									curl_setopt ( $ch, CURLOPT_URL, $url );
									curl_setopt ( $ch, CURLOPT_POST, true );
									curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
									curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
									curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

									$result = curl_exec ( $ch );
									//print_r($fields);
									curl_close ( $ch );
									 ob_flush();
								 
									}else{
										$apiKey = "AAAALlGM_1A:APA91bGGucMP2XZz4lcKSLsOD9RB-zdcqz-sd9buH1pEScK2giRS4G0SutZH6mL6GJmTapyT0WzTzLj63N1oT9Lu4xyI_B6syOaODm9MznvTbPpgSookgcsbRZDWYBkbzcGS9UZVNZ70";
									
									
										$notification_message =  $name;
										$msg =
											[
											'message' => $message,
											'title' =>$name ,
											'imageURL' => $imageURL,
											'videoURL' =>$videoURL ,
											'notification_type' => "chat"
										   
										];
									   
								   
									$fields = array(
										'registration_ids' => array($token),
										'data' => $msg
									);

									$headers =
										[
										'Authorization: key=' . $apiKey,
										'Content-Type: application/json'
									];
									$ch = curl_init();
									curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
									curl_setopt($ch, CURLOPT_POST, true);
									curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
									curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
									curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
									$result = curl_exec($ch);
									$data['response']='true';
            	                      $data['message']='message send';
            	                      $this->response($data);
								   //print_r($result);
									curl_close($ch);
									return $result;  
									ob_flush();

								}
							
	}
	

	function generateRandomString($length = 6) {
			$characters = '0123456789';
			$charactersLength = strlen($characters);
			$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
			 return $randomString;
	
	}
    // speciilist details  
    public function specialistdetail() {
       
        $headerdata = $this->getheaderdata();

        $specialistId = $headerdata->userid;
        $type = $headerdata->type;

        $data = array();
        $id = $this->input->post('id');
        $result = $this->Patient_model->specialist_detail($specialistId, 0);

        $data['response'] = 'true';
        $data['data'] = $result;
        $this->response($data);
	}
	
    // to get the single procuct
    public function singleproduct() {
        $barcodeSrNo = (Integer)$this->input->post('barcodesrno');
        $headerdata = $this->getheaderdata();
        $uid = $headerdata->userid;
        $type = $headerdata->type;

        $barcodeSrNo = (Integer)$this->input->post('barcodesrno');
        $data['data'] = $this->Patient_model->fetchsingleprodut($barcodeSrNo, $uid, $type);
        if (sizeof($data['data']) > 0) {
            $data['response'] = "true";
            $data['message'] = "Product Description";
        } else {
            $data['response'] = "false";
            $data['message'] = "No such product exist";
        }

        $this->response($data);
    }

    // to get the specilist notifications
    public function mynotifications() {
        $headerdata = $this->getheaderdata();
        $uid = $headerdata->userid;
        $data['data'] = $this->Patient_model->getfcmnotifications('specialist', $uid);
        if (sizeof($data['data']) > 0) {
            $data['response'] = "true";
            $data['message'] = "List of notifications";
        } else {
            $data['response'] = "false";
            $data['message'] = "No Notifications";
        }

        $this->response($data);
	}
    
    // change notification setting 
    public function changenotificationsetting() {
        $status= $this->input->post('status');
        $headerdata = $this->getheaderdata();
        $uid = $headerdata->userid;
        $temp=$this->Patient_model->change_notification_setting($uid, 'specialist', $status);
        if (sizeof($temp)>0) {
            $data['response'] = "true";
            $data['message'] = "Setting Changed successfully";
            $data['data'] = (String)$temp[0]->status;
        } else {
            $data['response'] = "false";
            $data['message'] = "OOPS some error occurs";
            $data['data'] = '';
        }

        $this->response($data);
    }
    // mark notification as read
    public function markread() {
        $id = $this->input->post('id');

        $result = $this->Patient_model->setnotificationread($id);
        if ($result == true) {
            $data['response'] = "true";
            $data['message'] = "Successfully marked as read";
        } else {
            $data['response'] = "false";
            $data['message'] = "Some error occurs";
        }
        $data['data'] = [];
        $this->response($data);
	}
	
    // country listing
    public function countries() {
        $data['data'] = $this->Special_model->countrieslist();
        if (sizeof($data['data']) > 0) {
            $data['response'] = "true";
            $data['message'] = "countries avaliable";
        } else {
            $data['response'] = "false";
            $data['message'] = "No more countries  avaliable";
        }

        $this->response($data);
	}
	
	// state listing 
    public function states() {
        $cid = (Integer)$this->input->post('countryId');

        $data['data'] = $this->Special_model->stateslist($cid);
        if (sizeof($data['data']) > 0) {
            $data['response'] = "true";
            $data['message'] = "states avaliable";
        } else {
            $data['response'] = "false";
            $data['message'] = "No more states avaliable";
        }

        $this->response($data);
    }

    // post reviews
    public function postreview() {
        $barcodeSrNo = (Integer)$this->input->post('barcodesrno');
        $comment = $this->input->post('comment');
        $rating = (Integer)$this->input->post('rating');
        $headerdata = $this->getheaderdata();
        $uid = $headerdata->userid;

        $datatosave = array(
            'userId' => $uid,
            'type' => 'specialist',
            'barcodeSrNo' => $barcodeSrNo,
            'comment' => $comment,
            'utctime ' => date("Y-m-d H:i:s"),
           'modifiedtime ' => date("Y-m-d H:i:s"),
            'rating' =>  $rating,
            'status ' => 'approved',
        );

        $data['data'] = $this->Special_model->savereviews($datatosave);
        if (!empty($data['data'])) {
            $data['response'] = "true";
            $data['message'] = "Review Posted Successfully";
        } else {
            $data['response'] = "false";
            $data['message'] = "OOPS some error occurs";
        }

        $this->response($data);
	}
	
	// edit the review 
    public function editreviews() {
        $comment = $this->input->post('comment');
        $rating = (Integer)$this->input->post('rating');
        $reviewId = (Integer)$this->input->post('reviewId');
        $data['data'] = [];

        if ($this->Special_model->edit_reviews($comment, $rating, $reviewId) == true) {
            $data['response'] = "true";
            $data['message'] = "Review Modified Successfully";
        } else {
            $data['response'] = "false";
            $data['message'] = "OOPS some error occurs";
        }

        $this->response($data);
    }

    // dashboard of specailist 
    public function dashboard() {

        //
        $data = array('data' => [], 'message' => "No data found", 'response' => "false");
        $headerdata = $this->getheaderdata();

        $uid = $headerdata->userid;
        $data['data'] = $this->Special_model->getallcounts($uid);
        if (sizeof($data['data']) > 0) {
            $data['response'] = "true";
            $data['message'] = "User Dashboard";
            $this->response($data);
        }
        // $this->response($data);
    }

    // list of filter attributes
    public function filterlisting() {
        $data = array('data' => [], 'message' => "No data found", 'response' => "false");
        $data['data'] = $this->Patient_model->getlistingoffilters();
        if (sizeof($data['data']) > 0) {
            $data['response'] = "true";
            $data['message'] = "listing of filters";
            $this->response($data);
        }
    }

    // get header data
    public function getheaderdata() {
        $headers = $this->input->request_headers();
        $token = $headers['Authtoken'];
        $headerdata = AUTHORIZATION::validateToken($token);
        return $headerdata;
    }

    // specialist qr code
    public function myqrcode() {
        $data['response'] = "true";
        $data['message'] = "Specialisr qr code ";
        $headerdata = $this->getheaderdata();
		$uid = $headerdata->userid;
		$filename = base_url('/assets/specialistQR/'.$uid.'.png');

		if (file_exists($filename)) {
			$data['data'] = base_url('/assets/specialistQR/' . $uid . '.png');
        	$this->response($data);

		} else {
			if ($this->generateToken($uid)) {
				$data['data'] = base_url('/assets/specialistQR/' . $uid . '.png');
            	$this->response($data);

			}else{
   				$data['response'] = "false";
        		$data['message'] = "QR code unavaliable";
				$data['data'] = "";
				$this->response($data);

			}
			
		}

        
    }

    // generate qr code  if token is not avaliable 
    public function generateToken($uid = "") {
        $this->load->library('ciqrcode');
        $qr_image = $uid . '.png';
        $params['data'] = $uid;
        $params['level'] = 'H';
        $params['size'] = 8;
        $params['savename'] = FCPATH . "assets/specialistQR/" . $qr_image;
    	return $this->ciqrcode->generate($params);
         
    }
   
  

    // complete the profile after social login
    public function completeprofile() {
       
        $data['age'] = $this->input->post('dob');
        $data['city'] = $this->input->post('city');
        $data['countary'] = $this->input->post('countary');
        $data['yearOfExperience'] = $this->input->post('yearOfExperience');
        $data['phone'] = $this->input->post('phone');
        $data['phonePrivacy'] = $this->input->post('phonePrivacy');
        $data['proPicName'] = $this->input->post('proPicName');
        $data['emailPrivacy'] = $this->input->post('emailPrivacy');
        $data['aboutme'] = $this->input->post('aboutme');
        $data['fcmUser'] = 's'.$this->input->post('phone');
         $data['jobProfile'] = $this->input->post('jobProfile');
        //  $data['email'] = $this->input->post('email');
        $data['iscompleted'] = true;
        $headerdata = $this->getheaderdata();
        $uid = $headerdata->userid;
        $this->load->helper('form');
        $this->load->library('form_validation');
        $email = $this->input->post('email');
         //  in case phone number exist
        if ($this->Special_model->check_phone($this->input->post('phone'))==1) {
            $senddata['response'] = "false";
            $senddata['message'] = "This Phone Number is already register with us.";

        }else{
        $result=$this->Special_model->complete_profile($uid, $data);
             if (sizeof($result)>0) {
                $senddata['response'] = "true";
                $senddata['message'] = "Successfully updated";
                $senddata['data'] = $result;
                $senddata['specialist'] =[];
                $senddata['concern'] = [];
            } else {
                $senddata['response'] = "false";
                $senddata['message'] = "OOPS some error occurs";
                $senddata['data'] = [];
                $senddata['specialist'] =[];
                $senddata['concern'] = [];
            }
         }
         $this->response($senddata);
    }


    // to store the temporary patient data  
    public function savemypatient()  {
        // Create a token
        $result = (array)$this->input->post();
        if(isset($result['image'])){
        $proPicName=$result['image'];
            if (base64_decode($proPicName, true)) {
                $current = base64_decode($proPicName);
                $file = uniqid() . '.png';
                $current .= "/";
                file_put_contents(FCPATH . '/assets/profilepic/' . $file, $current);
                $result['image'] = 'assets/profilepic/' . $file;
            }
        }
        $result['patientKey'] = AUTHORIZATION::generateToken($result);
        $output['response'] = "true";
        $output['message'] = "Patient Details ";
        $output['data'][0] = $result;
        $this->response($output);

    }


    // check if phone or email is unique
    public function isPatientUnique() {
         $output['response']="true";
         $output['message']="Every thing is ok. Please proceed .";
        if ($this->Patient_model->check_phone($this->input->post('phone'))) {
             
            $output['response']="false";
            $output['message']="This phone no is already register with us . Please check number .";
            
        }elseif ($this->Patient_model->check_email_exist($this->input->post('email'))) {
            $output['response']="false";
            $output['message']="This Email is already register with us . Please check email .";
            
        }
        $this->response($output);

    
    }


    // register patient temporary at user patient table with active status 0 
    public function confirmPatientRegistration() {
        $key=$this->input->post('patientKey');
        $deviceId = $this->input->post('deviceId');
        $data = AUTHORIZATION::validateToken($key);
        $patientId=$this->Patient_model->savepatient($data);
   
        $this->Patient_model->insert_patient_treatment($patientId, $deviceId);
        $this->Patient_model->request_specialist($patientId);
        $this->Patient_model->insert_patient_rel($patientId, $deviceId);
        $this->Patient_model->insert_patient_request($patientId, $deviceId);
        $this->Patient_model->delete_temp_choose_special($deviceId);
        $this->Patient_model->delete_device_product($deviceId);
        $specialist = $this->Patient_model->get_specialist($patientId);
        $output['response'] = "true";
        $output['message'] = $this->message()[2]->message;
        // $output['data'] = $result;
        $output["specialist"] = $specialist;
        $output["concern"] = $this->Patient_model->get_Concern($patientId);
        $this->response($output);
    }

    // send request to add the collegue
    public function addRequest() {
       
        $headerdata = $this->getheaderdata();
        $sp1 = $headerdata->userid;
        $sp2 = $this->input->post('to');
        $Specialistname = $this->Special_model->get_specialist_name($sp1);
        $data=$this->Special_model->send_request($sp1,$sp2);
        switch ($data['code']) {
            case '0': //code 0 when no error is thrown by database
                $this->sendnotification($sp1,$sp2, $this->messages->addRequest->notificationMessage->title," You have a new request from ". $Specialistname[0]->userName,$type="new_specialist_add_request",'specialist');
                $output['response']="true";
                $output['message']=$this->messages->addRequest->displayMessage->message;
            break;

            case '1062':  //error code thrown by database in case of duplicate pair 
                $output['response']="false";
                $output['message']=$this->messages->addRequest->displayMessage->duplicatemessage;
            break;
            
            default:
                $output['response']="false";
                $output['message']="Some Error Occurs";
            break;
        }
        
         $this->response($output);
    }


    // send request to cancel or unblock the collegue
    public function cancelRequest() {
        $headerdata = $this->getheaderdata();
        $sp1 = $headerdata->userid;
        $sp2 = $this->input->post('to');
        if ($this->Special_model->cancel_request($sp1,$sp2)) {
            $output['response']="true";
            $output['message']=$this->messages->cancelRequest->displayMessage->message;
        }else{
            $output['response']="false";
            $output['message']="Some Error Occurs";
        }
        $this->response($output);
    }

    // recieved requests 
    public function myRequests() {
        $data=[];
        $headerdata = $this->getheaderdata();
        $sp1 = $headerdata->userid;
        $data=$this->Special_model->get_requests($sp1);
        if ($data) {
            $output['response']="true";
            $output['message']="Pending Requests";
        }else{
            $output['response']="false";
            $output['message']="No Requests Pending";
        }
        $output['data']=$data;
        $this->response($output);
    }

    // set the response 
    public function sendMyResponse() {
        $data=[];
        $headerdata = $this->getheaderdata();
        $sp1 = $headerdata->userid;
        $sp2 = $this->input->post('requestId');
        $actionId = $this->input->post('actionId');
        $data=$this->Special_model->respond_to_request($sp1,$sp2,$actionId);
         if ($data) {
            //  only send notification if request accepted
            if ($actionId=="1") {
                 $Specialistname = $this->Special_model->get_specialist_name($sp1);
                $this->sendnotification($sp1,$sp2,$this->messages->sendMyResponse->notificationMessage->title, $Specialistname[0]->userName ." accepted your request.",$type="specialist_request_accepted",'specialist');
            }
            $output['response']="true";
            $output['message']=$this->messages->sendMyResponse->displayMessage->message;
            
         }else{
            $output['response']="false";
            $output['message']="some Error occurs";
         }
        //  $output['data']=$this->Special_model->get_requests($sp1);
         $this->response($output);
    }
   
    // get all specilist list (self and connected friends are excluded)
    public function allspecialists() {
        $data=[];
        $headerdata = $this->getheaderdata();
        $searchname = $this->input->post('searchspecialist');
        if ($this->input->post('pagenumber') != null) {
        $pagenumber = $this->input->post('pagenumber');
        } else {
        $pagenumber = 1;
        }
        $sp1 = $headerdata->userid;
      
        $data=$this->Special_model->get_all_specialists($sp1,$searchname);
         if ($data) {
            $output['response']="true";
            $output['message']="Specialist Lists";
            
         }else{
            $output['response']="false";
            $output['message']="No Specilist found";
         }
        $output['data']=array_slice($data , ($pagenumber-1)*10,10);
        $output['pagenumber']=$pagenumber;
        // $output['data']=array_slice($data , ($pagenumber-1)*10);
         $this->response($output);
    }

    // list of all the accepted add requests    
    public function myColleagues() {
        $data=[];
        $headerdata = $this->getheaderdata();
        $sp1 = $headerdata->userid;
        $data=$this->Special_model->get_my_colleagues($sp1,1);
        
      
        if ($data) {
            $output['response']="true";
            $output['message']="Colleagues Lists";
        }elseif(sizeof($data)==0){
             $output['response']="false";
             $output['message']="No Colleagues found";
        }else{
            $output['response']="false";
            $output['message']="some Error occurs";
        }
        $output['data']=$data;
        $this->response($output);
    }
    
    // get specilist details
    public function specialistprofile() {
        $headerdata = $this->getheaderdata();
        $selfId = $headerdata->userid;
        $type = $headerdata->type;
        $data = array();
        $id = $this->input->post('id');
        $result = $this->Special_model->specialist_detail($selfId, $id);
        if ($result) {
         $data['response'] = 'true';
        }else{
        $data['response'] = 'false';
        }
       
        $data['data'] = $result;
        $this->response($data);
    }
    

    // get the blocked users 
    public function blockedusers() {
        $data=[];
        $headerdata=$this->getheaderdata();
        $sid=$headerdata->userid;
        $data=$this->Special_model->get_my_colleagues($sid,3);
        if ($data) {
            $output['response']='true';
            $output['message']=$this->messages->blockedusers->displayMessage->success;
        }else{
            $output['response']='false';
            $output['message']=$this->messages->blockedusers->displayMessage->error;
        }
        $output['data']=$data;
        $this->response($output);
    }

     public function mychatlist() {
        $headerdata=$this->getheaderdata();
        $userId=$headerdata->userid;
        if ($this->input->post('pagenumber') != null) {
            $pagenumber = $this->input->post('pagenumber');
        } else {
            $pagenumber = 1;
        }
        $limit = 10;
        $offset = 10 * $pagenumber - 10;

        $this->load->library('pagination');
        $config = [
            'base_url' => base_url('index.php/user/myclients'),
            'per_page' => 10,
            'total_rows' => $this->Special_model->my_clients_count($userId)
        ];
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data = array();
        $result = $this->Special_model->my_chatlist($userId, $limit, $offset);

        if (sizeof($result) > 0) {
            $data['response'] = 'true';
            $data['message'] = 'requested list';
            $data['data'] = $result;
        } else {
            $data['response'] = 'false';
            $data['message'] = 'no requested';
            $data['data'] = array();
        }

        $this->response($data);
    }
    // send the notification
    public function sendnotification($from,$recieverId,$title,$notification_message,$type="",$entity)
    {
            /*code for notification*/

            if ($type=='specialist_request_accepted'||$type=='new_specialist_add_request'||$type=='specialist_facial_book') {
                // get device token of  specialist 
                $tokens = $this->Special_model->get_Specialist_Token($recieverId);
            }else {
                //  get device token of specialist via patient id 
                $tokens = $this->Special_model->get_Patient_Token($recieverId);
            }

            $fcmdata = array(
                'specialistId' =>$from,
                'patientId' =>'',
            );

            foreach ($tokens as $value) {
                $userId = $value->userId;
                // send notification if only enable otherwise break the loop
                if (!$this->Special_model->send_me_notification($userId)) break;
                //echo $name[0]->userName;
                if ($value->type == "IOS") {
                    ob_start();

                    $url = 'https://fcm.googleapis.com/fcm/send';

                    $fields = array(
                        'to' => $value->token,
                        'notification' => array(
                            "head" => $title,
                            // 'title' => $name[0]->userName.' requested you again',
                            'title' => $notification_message,
                            'notification_type' => $type,
                            'sound' => 'default',
                            'badge' => 1,
                            ),
                        'data' => $fcmdata
                    );

                    $fields = json_encode($fields);
                    $headers = array(
                    'Authorization: key=' .
                    "AAAALlGM_1A:APA91bGGucMP2XZz4lcKSLsOD9RB-zdcqz-sd9buH1pEScK2giRS4G0SutZH6mL6GJmTapyT0WzTzLj63N1oT9Lu4xyI_B6syOaODm9MznvTbPpgSookgcsbRZDWYBkbzcGS9UZVNZ70",
                    'Content-Type: application/json'
                    );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

                    $result = curl_exec($ch);

                    curl_close($ch);

                    ob_flush();
                } else {
                    
                    ob_start();
                    $apiKey =
                    "AAAALlGM_1A:APA91bGGucMP2XZz4lcKSLsOD9RB-zdcqz-sd9buH1pEScK2giRS4G0SutZH6mL6GJmTapyT0WzTzLj63N1oT9Lu4xyI_B6syOaODm9MznvTbPpgSookgcsbRZDWYBkbzcGS9UZVNZ70";

                    $msg = [
                        'message' => $notification_message,
                        'title' => $title,
                        'URL' => "",
                        'data' => $fcmdata,
                        'notification_type' => $type,
                    ];

                    $fields = array(
                        'registration_ids' => array($value->token),
                        'data' => $msg
                    );

                    $headers = [
                        'Authorization: key=' . $apiKey,
                        'Content-Type: application/json'
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    ob_flush();
                }
            }
            // save data array if the result of curl is successfull
            $notificationData = array(
                'userId' => $recieverId,
                'title' => $title,
                'message' => $notification_message,
                'type' => $type,
                'data' => serialize($fcmdata),
                'entity' => $entity,
                'utctime ' => date("Y-m-d H:i:s"),
                'readStatus ' => false,
            );
            $result = $this->Patient_model->savefcmnotidication($notificationData);
        
    }
	
	
	//Check and get specialist free time
	public function checkAvailTimeSlot(){
		$headerdata	  = $this->getheaderdata();
        $specailistId = $headerdata->userid;//current specailist Id
		
		$todayDate  		= gmdate('Y-m-d');
		$selectedDate   	= ($this->input->post('selectedDate'))?$this->input->post('selectedDate'):$todayDate;
		$selectedSpecialist = ($this->input->post('specialistId'))?$this->input->post('specialistId'):$specailistId;
		
		$checkTiming = $this->Special_model->check_available_time($selectedSpecialist,$selectedDate);
			if (sizeof($checkTiming) > 0) {
				$output['response']= "true";
				//$output['message'] = "Booked time slot of selected specialist";
				$output['message'] = "Available time slot for selected specialist";
			}
			elseif(sizeof($checkTiming)==0){
				$output['response'] = "false";
				$output['message']  = "No free time slot available";
			}	
			else{
				$output['response'] = "false";
				$output['message']  = "Some Error occurs";				
			}
		$output['data']     = $checkTiming;
		$this->response($output);//convert array to JSON
	}
	
	//Book facial
	public function bookFacial(){
		$headerdata	  = $this->getheaderdata();
        $specailistId = $headerdata->userid;
		$mkTime		  = '';
		$bookedMonth  = '';
		$bookedDay	  = '';
		$bookedYear   = '';
		
		$getDate = $this->input->post('bookedDate');
		if($getDate!=''){
			$expDate 	= explode('-',$this->input->post('bookedDate'));
			$bookedYear = $expDate[0];
			$bookedMonth= $expDate[1];
			$bookedDay  = $expDate[2];
		}
		$getTime = $this->input->post('bookedTime');
		if($getTime != ''){
			$expTime = explode(':',$getTime);
			$getTime = $expTime[0];
		}
		$mkTime  = (int)mktime($getTime,0,0,$bookedMonth,$bookedDay,$bookedYear);
	
		$data['bookedBy']   	= $specailistId; //current specailist Id
		$data['bookedFor']  	= ($this->input->post('specialistId'))?$this->input->post('specialistId'):$specailistId; //booked for specialist ID
		$data['bookedDate']  	= ($this->input->post('bookedDate'))?$this->input->post('bookedDate'):'0';//booking date
		$data['bookedTime']		= ($mkTime)?$mkTime:'0';//booking time
		$data['patientId'] 		= ($this->input->post('patientId'))?$this->input->post('patientId'):'0';//booked for patient Id
		$data['patientNotes']	= ($this->input->post('patientNotes'))?$this->input->post('patientNotes'):'';//booked for patient Id
		$data['specailistNotes']= ($this->input->post('privateNotes'))?$this->input->post('privateNotes'):'';

		$insertData = $this->Special_model->book_facial($data);
			if (!empty($insertData)) {
				$Specialistname = $this->Special_model->get_specialist_name($data['bookedFor']);
				
				$from 		= $data['bookedFor'];
				$recieverId = $data['patientId'];
				$title		= 'New booking';
				$type		= 'facial_book';
				$entity 	= 'patient';//receiver
				$notification_message = 'You have a new facial booking from '.$Specialistname[0]->userName;
				$this->sendnotification($from,$recieverId,$title,$notification_message,$type,$entity); //send notification to patient for booking
				
				/* send notification to other specialist */
				if($data['bookedFor'] != $specailistId)
				{
					$patientData = $this->Special_model->patient_Profile($data['patientId']);
					$from 		= $specailistId;
					$recieverId = $data['bookedFor'];
					$title		= 'New booking';
					$type		= 'specialist_facial_book';
					$entity 	= 'specialist';//receiver
					$notification_message = 'You have a new facial booking for '.$patientData[0]->userName .' by '.$Specialistname[0]->userName;
					$this->sendnotification($from,$recieverId,$title,$notification_message,$type,$entity); 
				}
				/* //send notification to other specialist */
				$output['response']= "true";
				$output['message'] = "Facial booked";
				$output['data']    = $data;
			}else{
				$output['response'] = "false";
				$output['message']  = "Try again, some error occurs";
				$output['data']     = $data;
			}
		$this->response($output);//convert array to JSON
	}
	
	//get list of facials booked by me for me
	public function myBookedFacials(){
		$headerdata	= $this->getheaderdata();
        $sId 		= $headerdata->userid;
		$rowCount 	= '0';
		$sDate		= '0';
		$sDay		= '0';
		
		$selectedDate  = ($this->input->post('selectedDate'))?$this->input->post('selectedDate'):'0';
		$bookingData   = $this->Special_model->get_my_booked_facials($sId,$selectedDate);
        if ($bookingData) {
            $output['response']= "true";
            $output['message'] = "Booking List";
			$rowCount = count($bookingData);
        }elseif(sizeof($bookingData)==0){
             $output['response'] = "false";
             $output['message']  = "No booking found";
        }else{
            $output['response'] = "false";
            $output['message']  = "Some Error occurs";
        }
		
		if($selectedDate != '0')
		{
			$sDate = date("d M", strtotime($selectedDate));
			$sDay  = date("D", strtotime($selectedDate));
		}
        $output['data'] 	= $bookingData;
		$output['rowCount'] = $rowCount;
		$output['displayDate'] 	= $sDate;
		$output['displayDay'] 	= $sDay;
		
        $this->response($output);
	}//myBookedFacials()

	//facial booking calendar
	public function facialBookingCalendar(){
		$headerdata	= $this->getheaderdata();
        $sId 		= $headerdata->userid;
		
		$timeZone     = ($this->input->post('timezone'))?$this->input->post('timezone'):'Asia/kolkata';
		$specailistId = ($this->input->post('specialistId'))?$this->input->post('specialistId'):$sId;
		$bookingData  = $this->Special_model->facial_booking_calendar($specailistId,$timeZone);
		
		$output = array();
		if(empty($bookingData)){
            $output['response']= "false";
            $output['message'] = "No Facial booked";
            $output['data']    = array();
		}else{
            $output['response']= "true";
            $output['message'] = "Facial list";
            $output['data']    = $bookingData;
		}
        
		$this->response($output);//convert array to JSON
	}
	
	public function completefacial(){
	    $data = array();
      //  $data['bookedBy']   	= $specailistId; //current specailist Id
        $data['bookingId']  	= ($this->input->post('bookingId'))?$this->input->post('bookingId'):0; 
        $data['isShowedUp']  	= ($this->input->post('isShowedUp'))?$this->input->post('isShowedUp'):'0';

        $data['isBuyedMoreProducts'] 		= ($this->input->post('isBuyedMoreProducts'))?$this->input->post('isBuyedMoreProducts'):'0';
        $data['amount']	= ($this->input->post('amount'))?$this->input->post('amount'):0;
        $data['barcodes']= ($this->input->post('barcodes'))?$this->input->post('barcodes'):'';
        
        $insertData = $this->Special_model->complete_facial($data);
        if (!empty($insertData)) {
            
            $this->db->where('id', $this->input->post('bookingId'));
            $this->db->update('specialist_book_facial',array('bookingStatus'=>1));
            
        }
        $output['response']= "true";
        $output['message'] = "Done";

        $this->response($output);
	}
	
	public function facialdetail(){
	    $data = array();
	    $bookingId 	= ($this->input->post('bookingId'))?$this->input->post('bookingId'):5; 
	    $getData = $this->Special_model->get_facial_detail($bookingId);
        
        //   print
        $output['response']= "true";
        $output['message'] = "Done";
        $output['data'] = $getData;
        $this->response($output);
	}
	
	public function facialaddproduct(){
	    $data = array();
	    $headerdata	= $this->getheaderdata();
        $sId 		= $headerdata->userid;
	    $barcodesrno = ($this->input->post('barcodesrno'))?$this->input->post('barcodesrno'):0; 
	    $patientId 	= ($this->input->post('patientId'))?$this->input->post('patientId'):0; 
	    
	    $getData = $this->Special_model->facialaddproduct($sId,$patientId,$barcodesrno);
	    
    	 //   print
        $output['response']= "true";
        $output['message'] = "Done";
        $output['data'] = $getData;
        $this->response($output);
	}
	
	public function addNewPatient(){
	    $data = array();
	    $headerdata	= $this->getheaderdata();
        $sId 		= $headerdata->userid;
        $output = array();
        $getData = $this->Special_model->addNewPatient($sId);
        if(!empty($getData['patient'])){
   
            $from_email = "no-reply@skinner-app.com"; 
            $to_email = $this->input->post('patientEmail'); 
               $output['response']= "true";  
                $output['data'] = array($getData['patient']); 
             //Load email library 
             $this->load->library('email'); 
       
             $this->email->from($from_email, 'Skinner'); 
             $this->email->to($to_email);
             $this->email->subject('Welcome User'); 
             $this->email->message('Dear Patient , Welcome to skinner . Your username is '.$to_email.' and password is 123456'); 
       
             //Send mail 
            if($this->email->send()) {
                $output['message'] = "Patient create and email sent";
            }else{ 
                $output['message'] = "Patient create but email not sent";
            } 
            
            $this->response($output);
        }else{
            $this->response(array('response'=>'false','message'=>'Patient already exist!!'));
        }
      
	}
}