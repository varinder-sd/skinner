<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User class.
 * 
 * @extends CI_Controller
 */
class Patient extends CI_Controller {

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
		$this->load->model('Patient_model');
		$this->load->model('Special_model');
		
	}
	public function message(){

		$message=$this->Patient_model->message();
		return $message;
	}
	public function response($data){


		 echo json_encode($data);


	}
	
	
	public function getSpecialistToken($id){
		 
		return $data=$this->Patient_model->get_Specialist_Token($id) ;
		
	}
	public function register() {
		$userId = $this->input->post('userId');
			$memberId = $this->input->post('memberId');
		if($this->input->post('memberId')!=null){

				$data3[]=$this->Patient_model->member_Id($userId,$memberId);

								$data['response']="true";
								$data['message']=$this->message()[2]->message;
								$data['data']=$data3;
								$this->response($data);


		}else{



		//$data = new stdClass();
		ini_set("upload_max_filesize","300MB");
		// load form helper and validation library
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		// set validation rules
		if($this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[user_patient.email]')->run() === false){
			;
		   $data['response']="false";
			$data['message']=$this->message()[0]->message;
			$data['data']=array();
			$this->response($data);
			
		
		}elseif($this->Patient_model->check_phone($this->input->post('phone'))==1){
			
		   $data['response']="false";
			$data['message']=$this->message()[1]->message;
			$data['data']=array();
			$this->response($data);
			
		
		}


		else{


			$userName = $this->input->post('userName');
			$age=  $this->input->post('age');
			$city=  $this->input->post('city');
			$proPicName=  $this->input->post('proPicName');
			$countary =  $this->input->post('countary');
			$deviceId =  $this->input->post('deviceId');
			$password = $this->input->post('password');
			//$yearOfExperience = $this->input->post('yearOfExperience');
			$gender = $this->input->post('gender');
			$phone = $this->input->post('phone');
			$email = $this->input->post('email');
			$token = $this->input->post('token');
			$type = $this->input->post('type');

			//$barcodeId = $this->input->post('barcodeId');
			
			//echo $existbar->barcodeSrNo;
			//print_r($existbar);die();
						if (base64_decode($proPicName, true)) {

							 $current=base64_decode($proPicName);
						$file = uniqid().'.png';
						$current .= "/";
					    file_put_contents('/home/skinner/public_html/assets/profilepic/'.$file,$current);
					    $link='assets/profilepic/'.$file;
						}else{

							$link=$proPicName;
						}
			           

					if ($result=$this->Patient_model->create_user($userName,$age,$city,$link,$countary,$password,$gender,$phone,$email,$token,$type,$deviceId)) {
						//print_r($result[0]->userId);
								
								$productExists=$this->Patient_model->product_Exists($deviceId);

								if($productExists==1){  



								$this->Patient_model->insert_patient_treatment($result[0]->userId,$deviceId);
								$this->Patient_model->request_specialist($result[0]->userId);
								$this->Patient_model->insert_patient_rel($result[0]->userId,$deviceId);
								$this->Patient_model->insert_patient_request($result[0]->userId,$deviceId);
								$this->Patient_model->delete_temp_choose_special($deviceId);
								$this->Patient_model->delete_device_product($deviceId);
								$specialist = $this->Patient_model->get_specialist($result[0]->userId);
								$data['response']="true";
								$data['message']=$this->message()[2]->message;
								$data['data']=$result;
								$data["specialist"]=$specialist;
						     	$data["concern"]=$this->Patient_model->get_Concern($result[0]->userId);
								$this->response($data);
								/*code for notifification*/
								$tokens=$this->getSpecialistToken($result[0]->userId);
     							//print_r($tokens);
     							foreach ($tokens as  $value) {
								 ob_start();
							      if($value->type=="IOS"){
							        
							        	ob_start();
			                    $url = 'https://fcm.googleapis.com/fcm/send';
									$fields = array (
									        'to' => $value->token,
									        'notification' => array ("head"=>'request page',
									        	'title' => 'You have new request for treatment from '. $result[0]->userName,
								            
								            'sound' => 'default',
								            'badge' => 1,
								            
									        ),
									        'data'=>array(
									        	'patientId' =>  $result[0]->userId,
								                 'patientfcm' =>$result[0]->fcmUser
 
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
							        
							        
							            $notification_message = 'You have new request for treatment from '. $result[0]->userName;
							            $msg =
							                [
							                'message' => $notification_message,
							                'title' =>"New treatment request" ,
							                'URL' => "",
							            
							                'notification_type' => "treatement_request",
							               
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

							       
							        curl_close($ch);
							         $result;  
							        ob_flush();


							        }

						}

								
								}else{
									//$specialist = $this->Patient_model->get_specialist($result[0]->userId);

									$this->Patient_model->insert_patient_treatment($result[0]->userId,$deviceId);
									$this->Patient_model->request_specialist($result[0]->userId);
									$this->Patient_model->delete_temp_choose_special($deviceId);
								$specialist = $this->Patient_model->get_specialist($result[0]->userId);
								$data['response']="true";
								$data['message']=$this->message()[2]->message;
								$data['data']=$result;
								$data["specialist"]=$specialist;
						     	$data["concern"]=$this->Patient_model->get_Concern($result[0]->userId);
								$this->response($data);

									
						}
								
							
			      	}
			      	else{
			      			$this->response("There was a problem creating your new account. Please try again");
			      	}
						
				
			}
		}
	}


	public function barcode(){

			
			//echo $this->getSpecialistToken(138);die();
			//print_r($_POST);
			$barcodesrno = $this->input->post('barcodesrno');
			//$productimg = $this->input->post('productimg');	
			$existbar=$this->Patient_model->exist_bar($barcodesrno);
			$reject=$this->Patient_model->reject_barcode($barcodesrno);
			



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
	
	
/*this api is doutfull*/
	public function checkitems(){

		        $deviceId = $this->input->post('deviceId');
				$userId=$this->input->post('userId');
				$existitem=$this->Patient_model->check_items($deviceId,$userId);
				if($existitem==1){
					$data['response']='true';
					$data['message']=$this->message()[3]->message;
					
					$this->response($data);
				}else{
					$data['response']='false';
					$data['message']=$this->message()[4]->message;
					
					$this->response($data);

				}


	}

	/*end*/
	public function addproduct(){

		ini_set("upload_max_filesize","300MB");
					
					$barcodesrno = $this->input->post('barcodesrno');
					$productName = $this->input->post('productName');
				    $brandname = $this->input->post('brandname');
				    $file_name = $this->input->post('file');
				 
				    $existbar=$this->Patient_model->exist_bar($barcodesrno);
										
				if(!$existbar){
//echo dirname(__FILE__); die;
					$current=base64_decode($file_name);
						$file = uniqid().'.png';
						$current .= "/";
					    file_put_contents('/home/skinner/public_html/assets/productImg/'.$file,$current);
					    $link='assets/productImg/'.$file;	
					    //print_r( $link);
                         $this->Patient_model->create_barcode($productName,$brandname,$barcodesrno, $link);
				  }	
				  	
				  				
				

				
				$deviceId = $this->input->post('deviceId');
				$userId=$this->input->post('userId');
				$checkproduct=$this->Patient_model->check_product($deviceId,$userId,$barcodesrno);
				//echo $checkproduct;
				if($checkproduct==1){
					$data['response']='false';
					$data['message']=$this->message()[3]->message;
					
					$this->response($data);
				}else{
				if($userId!=null){
				    $data['data']=$this->Patient_model->add_product($barcodesrno,$userId);
				   // $data['data']=$this->Special_model->add_product_device($barcodesrno,$deviceId);
			    }else{
			    	$data['data']=$this->Patient_model->add_product_device($barcodesrno,$deviceId);
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
	public function deleteproduct(){

		$userId=$this->input->post('userId');
		$deviceId=$this->input->post('deviceId');
		$productId=$this->input->post('productId');
		$type=$this->input->post('type');
		if($this->Patient_model->delete_product($userId,$deviceId,$productId,$type)==1){

			$data['response']='true';
			$data['message']=$this->message()[6]->message;
			
		}else{

			$data['response']='false';
			$data['message']='product not delete';

		}
			$this->response($data);
	}


	public function chooseconcern(){
	 $data['response']='true';	
     $data['data']=$this->Patient_model->choose_concern();

     $this->response($data);

	}
	public function showspecialist(){
  // $searchspecialist = $this->input->post('searchspecialist');
		$data['response']='true';
     if($this->input->post('searchspecialist')!=null)
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
   	
   	      if(ceil($this->Patient_model->total_search($this->input->post('searchspecialist'))/10) < $pagenumber){
			$data['data']=array();
			$data['response']='false';
		}else{
		 $data['data']=$this->Patient_model->search_specialist($this->input->post('searchspecialist'),$limit,$offset);
		
	    }

		     $this->response( $data);


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
				$config=['base_url'=>base_url('index.php/Patient/showspecialist'),
				'per_page'=>5,
				'total_rows'=>$this->Patient_model->total_num()
				];
				$config['use_page_numbers']=TRUE;
				$this->pagination->initialize($config);

				$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
				$data =array();
				$data['response']='true';
				if(ceil($this->Patient_model->total_num()/10) < $pagenumber){
					$data['data']=array();
				}else{
				$data['data'] = $this->Patient_model->shows_specialist($limit,$offset);
			    }
				 $this->response( $data);
		}
	}


	public function patientTreatment(){

				$concernType = $this->input->post('concernType');
				
				$specialId = $this->input->post('specialId');
				$deviceId = $this->input->post('deviceId');
				
				$result=$this->Patient_model->patient_Treatment($concernType,$specialId,$deviceId);
				$data['response']="true";
				$data['data']=$result;
				$this->response($data);
	}

	
	public function login() { 
			$this->load->helper('form');
		$this->load->library('form_validation');
	
			// set variables from the form
		     $token = $this->input->post('token');
			$username = $this->input->post('userName');
			$password = $this->input->post('password');
			$deviceId = $this->input->post('deviceId');
			$type = $this->input->post('type');
			if($this->form_validation->set_rules('userName', 'Email', 'trim|required|valid_email|is_unique[user_patient.email]')->run() === false){
						if ($this->Patient_model->resolve_user_login($username, $password)) {
							
							$user_id = $this->Patient_model->get_user_id_from_username($username);
							$user    = $this->Patient_model->get_user($user_id,$token,$type,$deviceId);
							$specialist    = $this->Patient_model->get_specialist($user_id);
							//print_r($user);die();
							// set session user datas
							$_SESSION['user_id']      = (int)$user->userId;
							$_SESSION['username']     = (string)$user->userName;
							$_SESSION['proPicName']     = (string)$user->proPicName;
							//$_SESSION['name']     = (string)$user->fullName;
							$_SESSION['logged_in']    = (bool)true;
							//$_SESSION['is_confirmed'] = (bool)$user->is_confirmed;
							//$_SESSION['is_admin']     = (bool)$user->userRole;
							$data['response']="true";
							$data['message']=$this->message()[7]->message;
							$data['data']=$user;
							$data["specialist"]=$specialist;
							$data["concern"]=$this->Patient_model->get_Concern($user_id);
							$this->response($data);
							
						}
						else {
				$data['response']="false";
				$data['message']=$this->message()[9]->message;
				$data['data']=array();
				$this->response($data);
				
				
			} 
		}else {
				$data['response']="false";
				$data['message']=$this->message()[8]->message;
				$data['data']=array();
				$this->response($data);
				
				
			}
			
		}
		
	

	public function showprofile(){

		
			$id= $this->input->post('id');

           
            
		  $data['response']='true';
		  $data['profile']=$this->Patient_model->show_profile($id);
		  $data['specialist name']=$this->Patient_model->specialist_name($id);
		  $data['concern types']=$this->Patient_model->concern_types($id);

			$this->response($data);
		
		
	}


	public function forgetpassword(){

      $email = $this->input->post('email');
       $check=$this->Patient_model->checkemail( $email);
       if($check==1){
       			$newpassword=$this->generateRandomString();
       			$to      = $email;
				$subject = 'NEW PASSWORD';
				$message = "this is your Password <h3>".$newpassword."</h3>" ;
				$headers = 'From: sumit@smartdesizns.co.in' . "\r\n" .
				    'Reply-To: webmaster@example.com' . "\r\n" .
				    'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);
            $this->Patient_model->change_password($newpassword,$email);
          
            	$data['response']='true';
            	$data['message']=$this->message()[10]->message;
            	$data['data']= $email;

            	$this->response($data);
       }else{
       			$data['response']='false';
            	$data['message']=$this->message()[11]->message;
            	$data['data']= array();

            	$this->response($data);
         	
       }
				

	}

	public function viewproduct(){
			//print_r($_POST);
			$id = $this->input->post('userId');
			$deviceId = $this->input->post('deviceId');
			$data = $this->Patient_model->view_product($id,$deviceId);
			if(sizeof($data)>0){
				$data1['response']="true";
				$data1['message']=$this->message()[12]->message;
				$data1['data']=$data;
				$this->response($data1);
			}else{
				$data1['response']="false";
				$data1['message']=$this->message()[13]->message;
				$data1['data']=array();
				$this->response($data1);
			}
			
		}


	function generateRandomString($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
    
    }
    public function editprofile(){

    	

    	$id = $this->input->post('userId'); 
    	$age = $this->input->post('age');
    	$name = $this->input->post('name');
    	$city = $this->input->post('city');
    	$proPicName = $this->input->post('proPicName');
    	$countary = $this->input->post('countary');
    	$phone = $this->input->post('phone');
    	$gender = $this->input->post('gender');
    	//$email = $this->input->post('email');


    	            if (base64_decode($proPicName, true)) {

							 $current=base64_decode($proPicName);
						     $file = uniqid().'.png';
					       	$current .= "/";
					         file_put_contents('/home/skinner/public_html/assets/profilepic/'.$file,$current);
					       $link='assets/profilepic/'.$file;
					      	}else{

							$link=$proPicName;
						 }

    	$result=$this->Patient_model->edit_profile($id,$age,$city,$link,$countary,$phone,$gender,$name);
    		$specialist= $this->Patient_model->get_specialist($id);
    	        $data['response']="true";
				$data['message']="update successfully";
				$data['data']=$result;
				$data["specialist"]=$specialist;
			    $data["concern"]=$this->Patient_model->get_Concern($id);
				
				$this->response($data);
				
    

}
    public function mysheduale(){
    	$id = $this->input->post('userId');

    	$this->checker($id);
    	$createdate=$this->Patient_model->createdate($id);
    	
    	if(!empty($createdate[0]->createdate)){
    	$red=$this->Patient_model->red($id);
    	$green=$this->Patient_model->green($id);
    	$lastinsertday=$this->Patient_model->lastinsertday($id);
        $looplimit=30-(sizeof($green)+sizeof($red));
        $days=array();

         for ($i=0;$i<sizeof($green);$i++) { 
     	 	
     	 	$days[]=array('date'=>$green[$i]->days,'color'=>'green');
     	 }
    	
    			
    		
    	for ($i=0;$i<sizeof($red);$i++) { 
     	 	
     	 	$days[]=array('date'=>$red[$i]->days,'color'=>'red');
     	 }
     	 $dates=array();
     	 foreach ($days as  $value) {
     	 	$dates[]=$value['date'];
     	 }
     	 
     	 if(sizeof($dates)>0){

     	 	$last=date("Y-m-d", strtotime(max($dates). " + 1 days"));

     	 }else{

     	 		$last=$lastinsertday[0]->day;
     	 }
     	 for($k=0;$k<$looplimit;$k++){

  
    				$days[]=array('date'=>date("Y-m-d", strtotime($last. " + ".$k." days")),'color'=>"yellow");
     	 		
    				
    				
    			}
     	
    			for ($i=0; $i <sizeof($days) ; $i++) { 
    				
    				$color=$this->datemaker($days[$i]["date"],$id);
    				if($color=="yes"){
    			  $datas[]=array("date"=>$days[$i]["date"],"color"=>$days[$i]["color"]);
    			}
    			}

    			
     	          
           		    $data['response']="true";
           			$data['message']="date list";
           			$data['data']=$datas;
           			$this->response($data);
     	           
           }else{

           		    $data['response']="false";
           			$data['message']=$this->message()[16]->message;
           			$data['data']=array();
           			$this->response($data);
           }
		
   }

  public function datemaker($date,$id){
    
   	
   	$res=array();
   	$time=array();
   	$data=$this->Patient_model->my_sheduale($id);
   	for($i=0;$i<sizeof($data);$i++){ 

   		//print_r($data);

   			if($data[$i]->everyday==1){
    			for($k=0;$k<30;$k++){  
    				$everyday[]=date("Y-m-d", strtotime($data[$i]->createdate. " + ".$k." days"));
    				//print_r($days);
    				if( $date ==  $everyday[$k]){
	    				//echo"abc";
	    				//print_r( $data[$i]);
	    				$time[]=+$data[$i]->minute;
	    				$res[]=$data[$i];
	    			}
    			}
    			

    			
    			//print_r($days);
    		 }
    		 if($data[$i]->onceAweek==1){
				for($k=0;$k<4;$k++){
					$b=$k*7;
				    $onceAweek[]=date("Y-m-d", strtotime($data[$i]->createdate. " + ".$b." days"));
				  
				    if( $date == $onceAweek[$k]){
	    				//echo"abc";
	    				//print_r( $data[$i]);
	    				$time[]=+$data[$i]->minute;
	    				$res[]=$data[$i];
	    			}
				
			}

       }
       if($data[$i]->twiceAweek==1){
				for($k=0;$k<8;$k++){
					$b=$k*3;
				    $twiceAweek[]=date("Y-m-d", strtotime($data[$i]->createdate. " + ".$b." days"));
				    if( $date ==  $twiceAweek[$k]){
	    				//echo"abc";
	    				//print_r( $data[$i]);
	    				$time[]=+$data[$i]->minute;
	    				$res[]=$data[$i];
	    			}
				
			}

       }
       if($data[$i]->onceAmonth==1){
				
				    if( $date ==  $data[$i]->createdate){
	    				//echo"abc";
	    				//print_r( $data[$i]);
	    				$time[]=+$data[$i]->minute;
	    				$res[]=$data[$i];
	    			}
				
			

             }
       }  //print_r($time);
       $finaltime=0;
       for($i=0;$i<sizeof($time);$i++){
       		$finaltime=$finaltime+$time[$i];
       }
       
       if(sizeof($res)>0){
       			
       	return "yes";
       }
       else{

       		return "no";
       		   
       }
      // $this->response($res);

   }
   public function treatmentdirction(){
    $date = $this->input->post('date');
    $id = $this->input->post('userId');
   	//$date = '2018-09-15';
   	$res=array();
   	$time=array();
   	$data=$this->Patient_model->my_sheduale($id);
   	for($i=0;$i<sizeof($data);$i++){ 

   		//print_r($data);

   			if($data[$i]->everyday==1){
    			for($k=0;$k<30;$k++){  
    				$everyday[]=date("Y-m-d", strtotime($data[$i]->createdate. " + ".$k." days"));
    				//print_r($days);
    				if( $date ==  $everyday[$k]){
	    				//echo"abc";
	    				//print_r( $data[$i]);
	    				$time[]=+$data[$i]->minute;
	    				$res[]=$data[$i];
	    			}
    			}
    			

    			
    			//print_r($days);
    		 }
    		 if($data[$i]->onceAweek==1){
				for($k=0;$k<4;$k++){
					$b=$k*7;
				    $onceAweek[]=date("Y-m-d", strtotime($data[$i]->createdate. " + ".$b." days"));
				  
				    if( $date == $onceAweek[$k]){
	    				//echo"abc";
	    				//print_r( $data[$i]);
	    				$time[]=+$data[$i]->minute;
	    				$res[]=$data[$i];
	    			}
				
			}

       }
       if($data[$i]->twiceAweek==1){
				for($k=0;$k<8;$k++){
					$b=$k*3;
				    $twiceAweek[]=date("Y-m-d", strtotime($data[$i]->createdate. " + ".$b." days"));
				    if( $date ==  $twiceAweek[$k]){
	    				//echo"abc";
	    				//print_r( $data[$i]);
	    				$time[]=+$data[$i]->minute;
	    				$res[]=$data[$i];
	    			}
				
			}

       }
       if($data[$i]->onceAmonth==1){
				
				    if( $date ==  $data[$i]->createdate){
	    				//echo"abc";
	    				//print_r( $data[$i]);
	    				$time[]=+$data[$i]->minute;
	    				$res[]=$data[$i];
	    			}
				
			

             }
       }  //print_r($time);
       $finaltime=0;
       for($i=0;$i<sizeof($time);$i++){
       		$finaltime=$finaltime+$time[$i];
       }
       
       if(sizeof($res)>0){
       			$status=$this->Patient_model->taketreatment_button($id, $date);

       		    $data12['response']="true";
       		    $data12['buttonStatus']=$status;
	     		$data12['message']=$this->message()[12]->message;
	     		$data12['time']=strval($finaltime);
	     		$data12['data']=$res;
	     		
	     		 $this->response($data12);

       }
       else{


       		    $data12['response']="false";
       		    $data12['buttonStatus']='no';
	     		$data12['message']=$this->message()[13]->message;
	     		$data12['data']=array();
	     		 $this->response($data12);
       }
      // $this->response($res);

   }  
   public function changepassword(){
    	
    	
    	$id=$this->input->post('userId');
    	$currentpassword=$this->input->post('currentpassword');
    	$newpassword= $this->input->post('newpassword');
    	$checkpass=$this->Patient_model->checkpass($currentpassword,$id);
    	if($checkpass==1){
    		$this->Patient_model->change_Pass($newpassword,$id);
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
		$this->Patient_model->distroy_token( $userId,$token,$deviceId);
		$data['response']='true';
		$data['message']='user logout';
     	$data['data']=array();
     	

     	$this->response($data);
		
	}
     public function specialistdetail(){
     	$data=array();
     	$id=$this->input->post('id');
     	$result=$this->Patient_model->specialist_detail($id);
     	
     	$data['response']='true';
     	$data['data']=$result;
     	

     	$this->response($data);

     }
     public function specialistGallery(){
     	$data=array();
     	$id=$this->input->post('id');
     	$type=$this->input->post('type');
     	$data['response']='true';
     	$data['gallery']=$this->Patient_model->gallery($id,$type);

     	$this->response($data);

     }



     public function requestAgain(){
     	  $data=array();
     	 $id=$this->input->post('userId');
     	 $patientfcm = $this->input->post('patientfcm');

     	$name= $this->Patient_model->request_Again($id);
     	
     		     $data['response']="true";
	     		$data['message']=$this->message()[12]->message;
	     		$data['data']=array();
	     		 $this->response($data);
     			/*code for notifification*/
     								$result=$this->Patient_model->get_specialist($id);
     								$tokens=$this->getSpecialistToken($id);
     								//print_r($tokens);
     							foreach ($tokens as  $value) {
     									
     								//echo $name[0]->userName;
     							 ob_start();
							      if($value->type=="IOS"){
							        
							        ob_start();
			                    $url = 'https://fcm.googleapis.com/fcm/send';

									/*$fields = array (
									        'to' => $value->token,
									        'notification' => array (
									        	    "head"=>'request page',
									                "body" => $name[0]->userName.'  requested you again',
									                "title" => "Notification",
									                "icon" => "myicon"
									        )
									);*/


									$fields = array (
									        'to' => $value->token,
									        'notification' => array ("head"=>'request page',
									        	'title' => $name[0]->userName.'  requested you again',
								            
								            'sound' => 'default',
								            'badge' => 1,
								            
									        ),
									        'data'=>array(
									        	'patientId' =>  $id,
								                 'patientfcm' =>$patientfcm
 
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
							        
							        
							            $notification_message = 'You have new request for treatment from '. $name[0]->userName;
							            $msg =
							                [
							                'message' => $notification_message,
							                'title' =>"New treatment request" ,
							                'URL' => "",
							            
							                'notification_type' => "treatement_request",
							               
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

							       
							        curl_close($ch);
							       $result ;  
							        ob_flush();

							    }

							  }
								
     	
     	//$this->response("Request Send Successfully!");
     }




     public function dummyimg(){
     	$data5=$this->Patient_model->dummy_img();
     	$result=array();
     	foreach ($data5 as $key => $value) {
     		$result1[]=array("images"=>base_url($value->imgName) ;
     		//$result[]=$result;
     		
     		
     	}
     	$result['data']=$result1;
     	$result['response']="true";
     //print_r($result);
     $this->response($result);
     }
     public function myproductlist(){

     	$id=$this->input->post('userId');
     	$data1=$this->Patient_model->my_product_list_req($id);
     	// print_r($data1);
     	$data2=$this->Patient_model->my_product_list_res($id);
     	// print_r($data2);die;
     	$data3=$this->Patient_model->my_product_list_to_req($id);
     	//print_r($data3);
     	$final=array_merge($data1,$data2);
     	$finaldata=array_merge($final,$data3);
       // print_r($finaldata);die();

     	if(sizeof($finaldata)>0)
     	{

	     		$data['response']="true";
	     		$data['message']=$this->message()[12]->message;
	     		$data['data']=$finaldata;
	     		 $this->response($data);
     	}
     	else{   

	     		$data['response']="false";
	     		$data['message']=$this->message()[13]->message;
	     		$data['data']=array();
	     		 $this->response($data);
     	    }
     }
     public function contactspace(){

     		$id=$this->input->post('userId');
     		$subject=$this->input->post('subject');
     		$message=$this->input->post('message');
     		$email=$this->Patient_model->get_email($id);
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

           		   $data1= $this->Patient_model->insert_email_data($id,$subject,$message);
           		   $data['response']="true";
           			$data['message']="mail send";
           			$data['data']=$data1;
           			$this->response($data);
           }
     }
     public function explorenewproduct(){
     		//print_r($_POST);die;
     		  $data['response']="true";
     		  $id= $this->input->post('userId');
     		  $eye= $this->input->post('eye');
     		  $forhead= $this->input->post('forhead');
     		  $neck= $this->input->post('neck');
     		  $allface= $this->input->post('allface');
     	if($this->input->post('searchproduct')!=null or $eye==1 or $forhead==1 or $neck==1 or $allface==1)
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
   	
   	      if(ceil($this->Patient_model->total_product_search($this->input->post('searchproduct'),$id,$eye,$forhead,$neck,$allface)/10) < $pagenumber){
			$data['data']=array();
			$data['response']="false";
		}else{
		 $data['data']=$this->Patient_model->search_product($this->input->post('searchproduct'),$limit,$offset,$id,$eye,$forhead,$neck,$allface);

	    }

		     $this->response( $data);


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
        $config=['base_url'=>base_url('index.php/Patient/showspecialist'),
        'per_page'=>5,
        'total_rows'=>$this->Patient_model->total_product($id)
        ];
        $config['use_page_numbers']=TRUE;
        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data =array();
        $data['response']='true';
        if(ceil($this->Patient_model->total_product($id,$eye,$forhead,$neck,$allface)/10) < $pagenumber){
          $data['data']=array();
        }else{
        $data['data'] = $this->Patient_model->explore_new_product($limit,$offset,$id);
          }
         $this->response( $data);
     }

     }
     public function requestToAdmin(){
     	$id=$this->input->post('userId');
     	$productId=$this->input->post('productId');
     	$result=$this->Patient_model->request_To_Admin($id,$productId);

     	      $data['response']="true";
     		  $data['message']="request send";
     		  $data['data']=$result;
     		  $this->response($data);

     }
     public function deleterRequest(){
     	$id=$this->input->post('userId');
     	$productId=$this->input->post('productId');
     	$result=$this->Patient_model->deleter_Request($id,$productId);

     }
     public function taketreatment(){
     	$id=$this->input->post('userId');
     	$date=date("Y-m-d");
     	$result=$this->Patient_model->day_Request($id,$date);

     	if ($result==1) {
     		   $data['response']="true";
     		   $data['message']="Treatement complete";
     		  $data['data']=array();
     	}else{
     		$data['response']="false";
     		   $data['message']="error";
     		  $data['data']=array();
     	}
     	$this->response($data);
     }


     public function checker($id){

     	 
     	 $insertedDate=$this->Patient_model->inserted_Date($id);
     	 //print_r(sizeof($insertedDate));die();

     	 if(sizeof($insertedDate)>0){
     	 $startdate= $this->Patient_model->createdate($id);
     	 //print_r( $startdate);
     	 $startdate=$startdate[0]->createdate;
     	 $enddate= date('Y-m-d',strtotime("-1 days"));
     	 

     	 for($i=0; $i<sizeof($insertedDate) ; $i++) { 
     	 	
     	 	$days[]=$insertedDate[$i]->days;
     	 }
     	  $alldates=$this->dateRange( $startdate, $enddate) ;
     	
     	 $result=array_diff($alldates,$days);
     	  $this->Patient_model->insert_remain_date($result,$id);
     	 	   
     	 }
     }
    
     function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
    $dates = [];
    $current = strtotime( $first );
    $last = strtotime( $last );

    while( $current <=$last ) {

        $dates[] = date( $format, $current );
        $current = strtotime( $step, $current );
    }

    return $dates;
}

	public function exploreProductListasproduct(){
  // $searchspecialist = $this->input->post('searchspecialist');
		$data['response']='true';
	
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
   	
   	      if(ceil($this->Special_model->exploreProductListasproduct_total_search($this->input->post('searchproduct'))/10) < $pagenumber){
			$data['data']=array();
			$data['response']='false';
		}else{
		 $data['data']=$this->Special_model->exploreProductListasproduct_search_explore_Product_List($this->input->post('searchproduct'),$limit,$offset);
		
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
				'total_rows'=>$this->Special_model->exploreProductListasproduct_total_num()
				];
				$config['use_page_numbers']=TRUE;
				$this->pagination->initialize($config);

				$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
				$data =array();
				$data['response']='true';
				if(ceil($this->Special_model->exploreProductListasproduct_total_num()/10) < $pagenumber){
					$data['data']=array();
					$data['response']='false';
				}else{
				$data['data'] = $this->Special_model->exploreProductListasproduct_explore_Product_List($limit,$offset);
			    }
				 $this->response( $data);
		}
	}

	public function addToMyList(){

		$barcodeSrNo = $this->input->post('barcodeSrNo');
		$deviceId = $this->input->post('deviceId');
		$userId = $this->input->post('userId');
		
		 $this->Patient_model->add_To_MyList($barcodeSrNo,$deviceId,$userId);

		       $data['response']='true';
            	$data['message']='add to list';
            	$data['data']=array();

            	$this->response($data);
			    
	}
	public function chat(){

		$name = $this->input->post('name');
		
		$userId = $this->input->post('userId');
		$message = $this->input->post('message');
		$patientfcm = $this->input->post('patientfcm');

		//$token = $this->Patient_model->get_token($userId)[0]->token;
		//$type = $this->Patient_model->get_token($userId)[0]->type;

				$link="";
			if ( base64_encode(base64_decode($message, true)) === $message){

							 $current=base64_decode($message);
						$file = uniqid().'.png';
						$current .= "/";
					    file_put_contents('/home/skinner/public_html/assets/chatimg/'.$file,$current);
					    $link='assets/chatimg/'.$file;
					       $link=$this->Patient_model->chat_img($link)[0]->img;
					       $message="image";
						}else{

							$message=$message;
						}
		
		/*code for notifification*/


						$tokens=$this->getSpecialistToken($userId);
						//print_r($tokens);
                        foreach ($tokens as  $value) {
								 ob_start();
							      if($value->type=="IOS"){
							        
							        	ob_start();
			                    $url = 'https://fcm.googleapis.com/fcm/send';
									$fields = array (
									        'to' => $value->token,
									        'notification' =>array ("head"=>'messagep','title' => $name,
								            'text' => $message,
								            'sound' => 'default',
								            "data"=>$link,
								            'badge' => 1,
								            
									        ),
										    'data'=> array(
										      
										       'patientId'=> $userId,
										        'patientfcm'=> $patientfcm
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
									 $result ;
									 ob_flush();
							     
							        }else{
							        	$apiKey = "AAAALlGM_1A:APA91bGGucMP2XZz4lcKSLsOD9RB-zdcqz-sd9buH1pEScK2giRS4G0SutZH6mL6GJmTapyT0WzTzLj63N1oT9Lu4xyI_B6syOaODm9MznvTbPpgSookgcsbRZDWYBkbzcGS9UZVNZ70";
							        
							        
							            $notification_message = 'You have new request for treatment from '. $notification_message =  $name;
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

							       
							        curl_close($ch);
							         $result;  
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

		$token = $this->Patient_model->get_token($userId)[0]->token;
		$type = $this->Patient_model->get_token($userId)[0]->type;

			
		
		/*code for notifification*/


							 ob_start();
							      if($type=="IOS"){
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
	/*public function test(){
		//echo"sdfsdfg";die;

		ob_start();
							      
							        $apiKey = "AAAAH8rGUy8:APA91bFPSQ5ZRFKDKs7sLk95wctduJODnDqEm4tI98F8Ru4KJa9ALXAGPe9VxoMorHvWlMYohFC0RpqVOB3JFrSj03PXJSAkqksOsucjZ7zxnWxhylmAycQOgCDmx3AJBa2qI0Q07nXv";




							        
						
			                    $url = 'https://fcm.googleapis.com/fcm/send';
									$fields = array (
									        'to' =>'dvYC9hktZV0:APA91bEZO5mfynKNrFvCJJckAkOVNbC5HHOon2rio9qPEBEENrEUO9lVqACPw6dEPrb6pJu8CAOyIBP9qWinOaDwIsQMdsDohQ9Is3e3R-OB06KSCT2LP7WCBWuZ-oOBt7oNSsjcE15H' ,
									        'notification' => array (
									        	"head"=>'myschedule page',
									                "body" => 'erfgree',
									                "title" => 'wefff3f',
									                "icon" => "myicon",
									                "data"=>'wefwfwfwf'
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
									print_r($result);
									return $result;  
									 ob_flush();
							     
							        


	}*/
	


}                                                                                               