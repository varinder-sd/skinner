<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * User class.
 *
 * @extends CI_Controller
 */
class Patient extends CI_Controller
{
    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public $key = "jwt_key ";
    public $messages;

    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url'));
        $this->load->model('Patient_model');
        $this->load->model('Special_model');
        // $messageFile = file_get_contents(base_url()."/assets/message/message.json");
         
      // $this->messages = json_decode($messageFile);
  
        // Load these helper to create JWT tokens
        $this->load->helper(['jwt', 'authorization']);
        if ($this->router->fetch_method() == "initialise") {
            $status = "true";
            $tokenData = [
                'after' => false

            ];
        
            // Create a token
            $token = AUTHORIZATION::generateToken($tokenData, $this->key);
            $response = [
                'status' => $status, 'message' => 'Initial api token ', 'data' => [
                    'Authtoken' => $token
                ]
            ];
            $this->response($response, $status);
            exit();
        }

        $headers = $this->input->request_headers();
        // Extract the token
        if (!isset($headers['Authtoken'])) {
            $status = "false";
            $response = ['status' => $status, 'message' => 'Please provide Authtoken in headers.', 'data' => []];
            $this->response($response, $status);
            exit();
        }
        $token = $headers['Authtoken'];
        // Use try-catch
        // JWT library throws exception if the token is not valid
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
                // eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhZnRlciI6ZmFsc2V9.sn9tstWY4hZ7nORaLod2AWH2lU20gojRO_gqdh9P95U


                // check the type of token before or after
                if ($data->after == true) {
                    if (!$this->Patient_model->getencpassword($data->userid, $data->password)) {
                        $status = "false";
                        $response = ['status' => $status, 'message' => 'Session Expired ! Login Again', 'data' => []];
                        $this->response($response, $status);
                        exit();
                    }
                }
                // else if ($data->type==false) {
                // 	# code...
                // }
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

    public function message()
    {
        $message = $this->Patient_model->message();
        return $message;
    }
    public function response($data)
    {
         header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function getSpecialistToken($id)
    {
        return $data = $this->Patient_model->get_Specialist_Token($id);
    }
    public function register()
    {
        $loginStrategy = $this->input->post('loginStrategy');
        $userId = $this->input->post('userId');
        $memberId = $this->input->post('memberId');
        if ($this->input->post('memberId') != null) {
            $data3[] = $this->Patient_model->member_Id($userId, $memberId);
            $data['response'] = "true";
            $data['message'] = $this->message()[2]->message;
            $data['data'] = $data3;
            $this->response($data);
        } else {
             $email = $this->input->post('email');

            //$data = new stdClass();
            ini_set("upload_max_filesize", "300MB");
            // load form helper and validation library
            $this->load->helper('form');
            $this->load->library('form_validation');
            // set validation rules
            if ($this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[user_patient.email]')->run() === false && ($loginStrategy == "local-oauth")) {
                $data['response'] = "false";
                $data['message'] = $this->message()[0]->message;
                $data['data'] = array();
                $this->response($data);
            } elseif ($this->Patient_model->check_phone($this->input->post('phone')) == 1 && ($loginStrategy == "local-oauth")) {
                $data['response'] = "false";
                $data['message'] = $this->message()[1]->message;
                $data['data'] = array();
                $this->response($data);
            } else {
                $userName = $this->input->post('userName');
                $age = $this->input->post('age');
                $city = $this->input->post('city');
                $proPicName = $this->input->post('proPicName');
                $countary =  $this->input->post('countary');
                $deviceId =  $this->input->post('deviceId');
                $password = $this->input->post('password');
                //$yearOfExperience = $this->input->post('yearOfExperience');
                $gender = $this->input->post('gender');
                $phone = $this->input->post('phone');
                $email = $this->input->post('email');
                $token = $this->input->post('token');
                $type = $this->input->post('type');
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
                if ($result = $this->Patient_model->create_user(
                    $userName,
                    $age,
                    $city,
                    $link,
                    $countary,
                    $password,
                    $gender,
                    $phone,
                    $email,
                    $token,
                    $type,
					$deviceId,
                    $loginStrategy,
                    $profileId
                )) {
                    //print_r($result[0]->userId);
                    $tokenData = [
                        'userid' => $result[0]->userId,
                        'username' => $result[0]->userName,
                        'password' => $result[0]->password,
                        'type' => 'patient',
                        'time' => time(),
                        'after' => true
                    ];

                    // generate the token
                
                    // Create a token
                    $token = AUTHORIZATION::generateToken($tokenData);
                    $result[0]->authtoken = $token;

                    $productExists = $this->Patient_model->product_Exists($deviceId);

                    if ($productExists == 1) {
                        $this->Patient_model->insert_patient_treatment($result[0]->userId, $deviceId);
                        $this->Patient_model->request_specialist($result[0]->userId);
                        $this->Patient_model->insert_patient_rel($result[0]->userId, $deviceId);//one 
                        $this->Patient_model->insert_patient_request($result[0]->userId, $deviceId);
                        $this->Patient_model->delete_temp_choose_special($deviceId);
                        $this->Patient_model->delete_device_product($deviceId);//two 
                        $specialist = $this->Patient_model->get_specialist($result[0]->userId);
                        $data['response'] = "true";
                        $data['message'] = $this->message()[2]->message;
                        $data['data'] = $result;
                        $data["specialist"] = $specialist;
                        $data["concern"] = $this->Patient_model->get_Concern($result[0]->userId);
                        $this->response($data);
                        /*code for notifification*/
                        $tokens = $this->getSpecialistToken($result[0]->userId);

                        $notification_message = 'You have new request for treatment from ' . $result[0]->userName;
                        $title = "New treatment request";
                        $fcmdata = array(
                            'patientId' => $result[0]->userId,
                            'patientfcm' => $result[0]->fcmUser
                        );
                        //print_r($tokens);
                        foreach ($tokens as $value) {
                            $userId = $value->userId;

                             // send notification if only enable otherwise break the loop
                            // if (!$this->Special_model->send_me_notification($userId)) break;
                            if ($value->type == "IOS") {
                                ob_start();
                                $url = 'https://fcm.googleapis.com/fcm/send';
                                $fields = array(
                                    'to' => $value->token,
                                    'notification' => array(
                                        "head" => $title,
                                        'title' => $notification_message,
                                        'notification_type' => "treatement_request",
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
                                    'notification_type' => "treatement_request",
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

                                curl_close($ch);
                                $result;
                                ob_flush();
                            }
                        }
                

                        // save data array if the result of curl is successfull

                        
                            // data to save into the fcm notification table
                            $notificationData = array(
                                'userId' => $userId,
                                'title' => $title,
                                'message'   => $notification_message,
                                'entity '   => 'specialist',
                                'type' => 'treatement_request',
                                'data' => serialize($fcmdata),
                                'utctime ' => date("Y-m-d H:i:s"),
                                'readStatus ' => false,
                            );

                            $result = $this->Patient_model->savefcmnotidication($notificationData);
                        
                    } else {
                        //$specialist = $this->Patient_model->get_specialist($result[0]->userId);

                        $this->Patient_model->insert_patient_treatment($result[0]->userId, $deviceId);
                        $this->Patient_model->request_specialist($result[0]->userId);
                        $this->Patient_model->delete_temp_choose_special($deviceId);
                        $specialist = $this->Patient_model->get_specialist($result[0]->userId);
                        $data['response'] = "true";
                        $data['message'] = $this->message()[2]->message;
                        $data['data'] = $result;
                        $data["specialist"] = $specialist;
                        $data["concern"] = $this->Patient_model->get_Concern($result[0]->userId);
                        $this->response($data);
                    }
                } else {
                    $this->response("There was a problem creating your new account. Please try again");
                }
            }
        }
    }

    public function barcode()
    {
            //echo $this->getSpecialistToken(138);die();
        //print_r($_POST);
        $barcodesrno = $this->input->post('barcodesrno');
        //$productimg = $this->input->post('productimg');
        $existbar = $this->Patient_model->exist_bar($barcodesrno);
        $reject = $this->Patient_model->reject_barcode($barcodesrno);
        if ($reject > 0) {
            $data['response'] = "product rejected";
            $this->response($data);
        } elseif (!$existbar) {
            $data['response'] = "false";
            $this->response($data);
        } else {
            $data['response'] = "true";
            $data['data'] = $existbar;
            $this->response($data);
        }
    }

    /*this api is doutfull*/
    public function checkitems()
    {
        $deviceId = $this->input->post('deviceId');
        $userId = $this->input->post('userId');
        $existitem = $this->Patient_model->check_items($deviceId, $userId);
        if ($existitem == 1) {
            $data['response'] = 'true';
            $data['message'] = $this->message()[3]->message;

            $this->response($data);
        } else {
            $data['response'] = 'false';
            $data['message'] = $this->message()[4]->message;

            $this->response($data);
        }
    }

    /*end*/
    public function addproduct()
    {
        ini_set("upload_max_filesize", "300MB");

        $barcodesrno = $this->input->post('barcodesrno');
        $productName = $this->input->post('productName');
        $brandname = $this->input->post('brandname');
        $file_name = $this->input->post('file');

        $existbar = $this->Patient_model->exist_bar($barcodesrno);

        if (!$existbar) {
            //echo dirname(__FILE__); die;
            $current = base64_decode($file_name);
            $file = uniqid() . '.png';
            $current .= "/";

            file_put_contents(FCPATH . '/assets/productImg/' . $file, $current);
            $link = 'assets/productImg/' . $file;
            //print_r( $link);
            $this->Patient_model->create_barcode($productName, $brandname, $barcodesrno, $link);
        }

        $deviceId = $this->input->post('deviceId');
        $userId = $this->input->post('userId');
        $checkproduct = $this->Patient_model->check_product($deviceId, $userId, $barcodesrno);
        //echo $checkproduct;
        if ($checkproduct == 1) {
            $data['response'] = 'false';
            $data['message'] = $this->message()[3]->message;

            $this->response($data);
        } else {
            if ($userId != null) {
                $data['data'] = $this->Patient_model->add_product($barcodesrno, $userId);
            // $data['data']=$this->Special_model->add_product_device($barcodesrno,$deviceId);
            } else {
                $data['data'] = $this->Patient_model->add_product_device($barcodesrno, $deviceId);
            }
            if ($data['data'] == 1) {
                $response['response'] = 'true';
                $response['message'] = $this->message()[5]->message;
                $this->response($response);
            } else {
                $response['response'] = 'false';
                $this->response($response);
            }
        }
    }
    public function addproductmanualpatient()
    {
        ini_set("upload_max_filesize", "300MB");
        $barcodesrno = $this->input->post('barcodesrno');
        $productName = $this->input->post('productName');
        $brandname = $this->input->post('brandname');
        $patientKey = $this->input->post('patientEmail');
        $file_name = $this->input->post('file');
        $deviceId = $this->input->post('deviceId');
        $existbar = $this->Patient_model->exist_bar($barcodesrno);

        if (!$existbar) {
            //echo dirname(__FILE__); die;
            $current = base64_decode($file_name);
            $file = uniqid() . '.png';
            $current .= "/";

            file_put_contents(FCPATH . '/assets/productImg/' . $file, $current);
            $link = 'assets/productImg/' . $file;
            //print_r( $link);
            $this->Patient_model->create_barcode($productName, $brandname, $barcodesrno, $link);
        }


        $checkproduct = $this->Patient_model->check_product_to_manual_patient($deviceId, $barcodesrno,$patientKey);
        //echo $checkproduct;
        if ($checkproduct == 1) {
            $data['response'] = 'false';
            $data['message'] = $this->message()[3]->message;

            $this->response($data);
        } else {
            $data['data'] = $this->Patient_model->add_product_device_patient_manual($barcodesrno, $deviceId,$patientKey);
        }
        if ($data['data']) {
            $response['response'] = 'true';
            $response['message'] = $this->message()[5]->message;
            $this->response($response);
        } else {
            $response['response'] = 'false';
            $response['message'] = "some error occurs";
            $this->response($response);
        }
    }
    
    public function deleteproduct()
    {
        $userId = $this->input->post('userId');
        $deviceId = $this->input->post('deviceId');
        $productId = $this->input->post('productId');
        $patientKey = $this->input->post('patientEmail');
        $type = $this->input->post('type');
        if ($patientKey) {
           $response=$this->Patient_model->delete_manual_product($deviceId, $productId, $patientKey);
        } else {
           $response=$this->Patient_model->delete_product($userId, $deviceId, $productId, $type);
        }
        
        
        if ($response) {
            $data['response'] = 'true';
            $data['message'] = $this->message()[6]->message;
        } else {
            $data['response'] = 'false';
            $data['message'] = 'product not delete';
        }
        $this->response($data);
    }

    public function chooseconcern()
    {
        $data['response'] = 'true';
        $data['data'] = $this->Patient_model->choose_concern();

        $this->response($data);
    }
    public function showspecialist()
    {
        // $searchspecialist = $this->input->post('searchspecialist');
        $data['response'] = 'true';
        if ($this->input->post('searchspecialist') != null) {
            if ($this->input->post('pagenumber') != null) {
                $pagenumber = $this->input->post('pagenumber');
            } else {
                $pagenumber = 1;
            }
            $limit = 10;
            $offset = 10 * $pagenumber - 10;

            if (ceil($this->Patient_model->total_search($this->input->post('searchspecialist')) / 10) < $pagenumber) {
                $data['data'] = array();
                $data['response'] = 'false';
            } else {
                $data['data'] = $this->Patient_model->search_specialist($this->input->post('searchspecialist'), $limit, $offset);
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
                'base_url' => base_url('index.php/Patient/showspecialist'),
                'per_page' => 5,
                'total_rows' => $this->Patient_model->total_num()
            ];
            $config['use_page_numbers'] = true;
            $this->pagination->initialize($config);

            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
            $data = array();
            $data['response'] = 'true';
            if (ceil($this->Patient_model->total_num() / 10) < $pagenumber) {
                $data['data'] = array();
            } else {
                $data['data'] = $this->Patient_model->shows_specialist($limit, $offset);
            }
            $this->response($data);
        }
    }

    public function patientTreatment()
    {
         $data['shareCode'] ="";
         $data['shareMessage'] ="";
         $concernType = $this->input->post('concernType');
        $specialId = $this->input->post('specialId');
        $deviceId = $this->input->post('deviceId');
        $result = $this->Patient_model->patient_Treatment($concernType, $specialId, $deviceId);
         // special code for version 3 feature
         $key=$this->input->post('patientKey');
         if ($key!=""&&$key!=null) {
             
             $newpassword = $this->generateRandomString();
             $datatosave = AUTHORIZATION::validateToken($key);
             $patientId=$this->Patient_model->savepatient($datatosave);
            $data['shareMessage'] = 'Dear Patient , Welcome to skinner . Your username is '.$datatosave->email.' and password is ' .$newpassword ;
             $encryptData = [
                        'username' => $datatosave->email,
                        'password' => $newpassword,
                        'time' => time(),
                        'type' => 'qrtype',
                    ];
                    
                   
                    // Create a token
                    $encryptToken = AUTHORIZATION::generateToken($encryptData);
                    
             if ($this->generateQrcodeforpatient($patientId,$encryptToken)) {
                
                $data['shareCode'] = base_url('/assets/patientShareQrCode/' . $patientId . '.png');
             }

             $to = $datatosave->email;
             $subject = 'Welcome User';
             $mailData = array(
                'password'=> $newpassword,
             );


           //  $message = $this->load->view('email/new_patient.php',$mailData,TRUE);
              $message = "this is your Password <h3>" . $newpassword . "</h3>";
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
             $headers .= 'From: no-reply@skinner-app.com' . "\r\n"
             . 'Reply-To: no-reply@skinner-app.com' . "\r\n"
             . 'X-Mailer: PHP/' . phpversion();

             mail($to, $subject, $message, $headers);
             $this->Patient_model->change_password($newpassword, $to);
         }else{
             $patientId = $this->getPatientData()->userid;
         }
       
        // in case of patient tretment after login via google or facebook auth
        if ($patientId != "0") {
            $this->Patient_model->insert_patient_treatment($patientId, $deviceId);
            $this->Patient_model->request_specialist($patientId);
            if ($key) {
                $this->Patient_model->insert_manual_patient_rel($datatosave->email,$deviceId,$patientId);//one
                $this->Patient_model->delete_device_manual_patient_product($deviceId,$datatosave->email);//two
            } else {
                $this->Patient_model->insert_patient_rel($patientId, $deviceId);
                $this->Patient_model->delete_device_product($deviceId);
            }
            $this->Patient_model->insert_patient_request($patientId, $deviceId);
            $this->Patient_model->delete_temp_choose_special($deviceId);
            $tokens = $this->getSpecialistToken($patientId);
            $notification_message = 'You have new request for treatment';
            $title = "New treatment request";
            $fcmdata = array(
                'patientId' => $patientId,
                );
                $userId=$specialId;
            //print_r($tokens);
            foreach ($tokens as $value) {
                $userId = $value->userId;
                // send notification if only enable otherwise break the loop
                if (!$this->Special_model->send_me_notification($userId)) break;
                 
              
                if ($value->type == "IOS") {
                    ob_start();
                    $url = 'https://fcm.googleapis.com/fcm/send';
                    $fields = array(
                        'to' => $value->token,
                        'notification' => array(
                            "head" => $title,
                            'title' => $notification_message,
                            'notification_type' => "treatement_request",
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
                        'notification_type' => "treatement_request",
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

                    curl_close($ch);
                    $result;
                    ob_flush();
                }
            }
    

            // save data array if    the result of curl is successfull

            
                // data to save into the fcm notification table
                $notificationData = array(
                    'userId' => $userId,
                    'title' => $title,
                    'message'   => $notification_message,
                    'entity '   => 'specialist',
                    'type' => 'treatement_request',
                    'data' => serialize($fcmdata),
                    'utctime ' => date("Y-m-d H:i:s"),
                    'readStatus ' => false,
                );

                $result = $this->Patient_model->savefcmnotidication($notificationData);
            
            // send the notification

        }
        $data['specialist']=[];
        $data['concern']=[];
        $data['specialist'] =  $this->Patient_model->get_specialist($patientId);
        $data["concern"] = $this->Patient_model->get_Concern($patientId);
        if (sizeof($data['specialist']) == 0) {
           $data['specialist']=[];
        }
        if (sizeof($data['concern']) == 0) {
           $data['concern']=[];
        }
        $data['response'] = "true";
        $data['data'] =[];
        $this->response($data);
    }

    public function login()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
    
        // set variables from the form
        $token = $this->input->post('token');
        $username = $this->input->post('userName');
        $password = $this->input->post('password');
        $shareCode = $this->input->post('shareCode');

        // if login via share code 
        if($shareCode){
               $userData = AUTHORIZATION::validateToken($shareCode);
               
             
               
               $type = $userData->type;
               if ($type=="qrtype") {
                  $username = $userData->username;
                  $password = $userData->password;
               }else{
                    $data['response'] = "false";
                    $data['message'] = 'Invalid Token';
                    $data['data'] = array();
                    $this->response($data);
                    exit();

               }
               
               
        }
        $deviceId = $this->input->post('deviceId');
        $type = $this->input->post('type');
        if ($this->form_validation->set_rules('userName', 'Email', 'trim|required|valid_email|is_unique[user_patient.email]')->run() === false) {
            if ($this->Patient_model->resolve_user_login($username, $password)) {
                $user_id = $this->Patient_model->get_user_id_from_username($username);
                $user    = $this->Patient_model->get_user($user_id, $token, $type, $deviceId);


                $arr = (array)$user;
                if ($arr) {
                    // generate the json web token
                    // $tokenData = [
                    // 	'after'=>false
                    // ];

                    $tokenData = [
                        'userid' => $user->userId,
                        'username' => $user->userName,
                        'password' => $user->password,
                        'type' => 'patient',
                        'time' => time(),
                        'after' => true
                    ];
                                
                                
                                
                    // Create a token
                    $token = AUTHORIZATION::generateToken($tokenData);
                    $user->authtoken = $token;
                }

                $specialist = $this->Patient_model->get_specialist($user_id);
                //print_r($user);die();
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
                $data["specialist"] = $specialist;
                $data["concern"] = $this->Patient_model->get_Concern($user_id);
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

    public function showprofile()
    {
        $id = $this->input->post('id');

        $data['response'] = 'true';
        $data['profile'] = $this->Patient_model->show_profile($id);
        $data['specialist name'] = $this->Patient_model->specialist_name($id);
        $data['specialist'] = $this->Patient_model->get_specialist($id);

        $data['concern types'] = $this->Patient_model->concern_types($id);

        $this->response($data);
    }

    public function forgetpassword()
    {
        $email = $this->input->post('email');
        $check = $this->Patient_model->checkemail($email);
        if ($check == 1) {
            $newpassword = $this->generateRandomString();
            $to      = $email;
          
            $subject = 'NEW PASSWORD';
            $message = "this is your Password <h3>" . $newpassword . "</h3>";
            $headers = 'From: no-reply@skinner-app.com' . "\r\n"
                . 'Reply-To: no-reply@skinner-app.com' . "\r\n"
                . 'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);
            $this->Patient_model->change_password($newpassword, $email);

            $data['response'] = 'true';
            $data['message'] = $this->message()[10]->message;
            $data['data'] = $email;

            $this->response($data);
        } else {
            $data['response'] = 'false';
            $data['message'] = $this->message()[11]->message;
            $data['data'] = array();

            $this->response($data);
        }
    }

    public function viewproduct()
    {
        //print_r($_POST);
        $id = $this->input->post('userId');
        $deviceId = $this->input->post('deviceId');
        $data = $this->Patient_model->view_product($id, $deviceId);
        if (sizeof($data) > 0) {
            $data1['response'] = "true";
            $data1['message'] = $this->message()[12]->message;
            $data1['data'] = $data;
            $this->response($data1);
        } else {
            $data1['response'] = "false";
            $data1['message'] = $this->message()[13]->message;
            $data1['data'] = array();
            $this->response($data1);
        }
    }
    public function viewmanualpatientproduct()
    { 
        //print_r($_POST);
        $patientKey = $this->input->post('patientEmail');
        $deviceId = $this->input->post('deviceId');
        $data = $this->Patient_model->getmanualpatientproduct($patientKey, $deviceId);
        if (sizeof($data) > 0) {
            $data1['response'] = "true";
            $data1['message'] = $this->message()[12]->message;
            $data1['data'] = $data;
            $this->response($data1);
        } else {
            $data1['response'] = "false";
            $data1['message'] = $this->message()[13]->message;
            $data1['data'] = array();
            $this->response($data1);
        }
    }

    public function generateRandomString($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function editprofile()
    {
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
            $current = base64_decode($proPicName);
            $file = uniqid() . '.png';
            $current .= "/";
            file_put_contents(FCPATH . '/assets/profilepic/' . $file, $current);
            $link = 'assets/profilepic/' . $file;
        } else {
            $link = $proPicName;
        }

        $result = $this->Patient_model->edit_profile($id, $age, $city, $link, $countary, $phone, $gender, $name);
        $specialist = $this->Patient_model->get_specialist($id);
        $data['response'] = "true";
        $data['message'] = "update successfully";
        $data['data'] = $result;
        $data["specialist"] = $specialist;
        $data["concern"] = $this->Patient_model->get_Concern($id);

        $this->response($data);
    }
    public function mysheduale()
    {
        $id = $this->input->post('userId');
        $datas=[];
        $this->checker($id);
        $createdate = $this->Patient_model->createdate($id);

        if (!empty($createdate[0]->createdate)) {
            $red = $this->Patient_model->red($id);
            $green = $this->Patient_model->green($id);

            
            $lastinsertday = $this->Patient_model->lastinsertday($id);
            $looplimit = 30 - (sizeof($green) + sizeof($red));
            $days = array();

            for ($i = 0; $i < sizeof($green); $i++) {
                $days[] = array('date' => $green[$i]->days, 'color' => 'green');
            }

            for ($i = 0; $i < sizeof($red); $i++) {
                $days[] = array('date' => $red[$i]->days, 'color' => 'red');
            }
            $dates = array();
            foreach ($days as $value) {
                $dates[] = $value['date'];
            }

            if (sizeof($dates) > 0) {
                $last = date("Y-m-d", strtotime(max($dates) . " + 1 days"));
            } else {
                $last = $lastinsertday[0]->day;
            }
            
            // echo "<pre>";
            // print_r ($variable);
            // echo "</pre>";
            
            for ($k = 0; $k < $looplimit; $k++) {
                $days[] = array('date' => date("Y-m-d", strtotime($last . " + " . $k . " days")), 'color' => "yellow");
            }



            for ($i = 0; $i < sizeof($days); $i++) {
                $color = $this->datemaker($days[$i]["date"], $id);
                if ($color == "yes") {
                    $datas[] = array("date" => $days[$i]["date"], "color" => $days[$i]["color"]);
                }
            }

    
            

            $data['response'] = "true";
            $data['message'] = "date list";
            $data['data'] = $datas;
            $this->response($data);
        } else {
            $data['response'] = "false";
            $data['message'] = $this->message()[16]->message;
            $data['data'] = array();
            $this->response($data);
        }
    }

    public function datemaker($date, $id)
    {
  
        
  
        $res = array();
        $time = array();
        $data = $this->Patient_model->my_sheduale($id);
        
       
        
        for ($i = 0; $i < sizeof($data); $i++) {

        //print_r($data);

            if ($data[$i]->everyday == 1) {
                for ($k = 0; $k < 30; $k++) {
                    $everyday[] = date("Y-m-d", strtotime($data[$i]->createdate . " + " . $k . " days"));
                    //print_r($days);
                    if ($date == $everyday[$k]) {
                        //echo"abc";
                        //print_r( $data[$i]);
                        $time[] = +$data[$i]->minute;
                        $res[] = $data[$i];
                    }
                }
                

                
                //print_r($days);
            }
            if ($data[$i]->onceAweek == 1) {
                for ($k = 0; $k < 4; $k++) {
                    $b = $k * 7;
                    $onceAweek[] = date("Y-m-d", strtotime($data[$i]->createdate . " + " . $b . " days"));

                    if ($date == $onceAweek[$k]) {
                        //echo"abc";
                        //print_r( $data[$i]);
                        $time[] = +$data[$i]->minute;
                        $res[] = $data[$i];
                    }
                }
            }
            if ($data[$i]->twiceAweek == 1) {
                for ($k = 0; $k < 8; $k++) {
                    $b = $k * 3;
                    $twiceAweek[] = date("Y-m-d", strtotime($data[$i]->createdate . " + " . $b . " days"));
                    if ($date == $twiceAweek[$k]) {
                        //echo"abc";
                        //print_r( $data[$i]);
                        $time[] = +$data[$i]->minute;
                        $res[] = $data[$i];
                    }
                }
            }
            if ($data[$i]->onceAmonth == 1) {
                if ($date == $data[$i]->createdate) {
                    //echo"abc";
                    //print_r( $data[$i]);
                    $time[] = +$data[$i]->minute;
                    $res[] = $data[$i];
                }
            }
        }  //print_r($time);
        $finaltime = 0;
        for ($i = 0; $i < sizeof($time); $i++) {
            $finaltime = $finaltime + $time[$i];
        }

        
  
        

        if (sizeof($res) > 0) {
            return "yes";
        } else {
            return "no";
        }
        // $this->response($res);
    }
    public function treatmentdirction()
    {
        $date = $this->input->post('date');
        $id = $this->input->post('userId');
        //$date = '2018-09-15';
        $res = array();
        $time = array();
        $data = $this->Patient_model->my_sheduale($id);
        for ($i = 0; $i < sizeof($data); $i++) {

        //print_r($data);

            if ($data[$i]->everyday == 1) {
                for ($k = 0; $k < 30; $k++) {
                    $everyday[] = date("Y-m-d", strtotime($data[$i]->createdate . " + " . $k . " days"));
                    //print_r($days);
                    if ($date == $everyday[$k]) {
                        //echo"abc";
                        //print_r( $data[$i]);
                        $time[] = +$data[$i]->minute;
                        $res[] = $data[$i];
                    }
                }
                

                
                //print_r($days);
            }
            if ($data[$i]->onceAweek == 1) {
                for ($k = 0; $k < 4; $k++) {
                    $b = $k * 7;
                    $onceAweek[] = date("Y-m-d", strtotime($data[$i]->createdate . " + " . $b . " days"));

                    if ($date == $onceAweek[$k]) {
                        //echo"abc";
                        //print_r( $data[$i]);
                        $time[] = +$data[$i]->minute;
                        $res[] = $data[$i];
                    }
                }
            }
            if ($data[$i]->twiceAweek == 1) {
                for ($k = 0; $k < 8; $k++) {
                    $b = $k * 3;
                    $twiceAweek[] = date("Y-m-d", strtotime($data[$i]->createdate . " + " . $b . " days"));
                    if ($date == $twiceAweek[$k]) {
                        //echo"abc";
                        //print_r( $data[$i]);
                        $time[] = +$data[$i]->minute;
                        $res[] = $data[$i];
                    }
                }
            }
            if ($data[$i]->onceAmonth == 1) {
                if ($date == $data[$i]->createdate) {
                    //echo"abc";
                    //print_r( $data[$i]);
                    $time[] = +$data[$i]->minute;
                    $res[] = $data[$i];
                }
            }
        }  //print_r($time);
        $finaltime = 0;
        for ($i = 0; $i < sizeof($time); $i++) {
            $finaltime = $finaltime + $time[$i];
        }

        if (sizeof($res) > 0) {
            $status = $this->Patient_model->taketreatment_button($id, $date);

            $data12['response'] = "true";
            $data12['buttonStatus'] = $status;
            $data12['message'] = $this->message()[12]->message;
            $data12['time'] = strval($finaltime);
            $data12['data'] = $res;

            $this->response($data12);
        } else {
            $data12['response'] = "false";
            $data12['buttonStatus'] = 'no';
            $data12['message'] = $this->message()[13]->message;
            $data12['data'] = array();
            $this->response($data12);
        }
        // $this->response($res);
    }
    public function changepassword()
    {
        $id = $this->input->post('userId');
        $currentpassword = $this->input->post('currentpassword');

        $shareCode = $this->input->post('shareCode');

        if ($shareCode) {
        	 $encryptData = AUTHORIZATION::validateToken($shareCode);
        	 $currentpassword = $encryptData->password;
        }
        $newpassword = $this->input->post('newpassword');
        $isnew = $this->input->post('isnew');
        $checkpass = $this->Patient_model->checkpass($currentpassword, $id);
        // Create a token
        if ($checkpass == 1) {
            $newencpassword=$this->Patient_model->change_Pass($newpassword, $id);
            $headers = $this->input->request_headers();
             $token = $headers['Authtoken'];
             $headerdata = AUTHORIZATION::validateToken($token);
            // check for first time login patient added via manually by patient 
            if ($isnew) {
                 $headerdata->password=$newencpassword;
            }
            $token = AUTHORIZATION::generateToken($headerdata);
            $data['response'] = "true";
            $data['message'] = "Password change successfully !";
            $data['data'][0]['authtoken'] = $token;
            $data['data'][0]['pass'] = $headerdata;
            $this->response($data);
        } else {
            $data['response'] = "false";
            $data['message'] = $this->message()[15]->message;
            $data['data'] = array();
            $this->response($data);
        }
    }
    public function logout()
    {
        $userId = $this->input->post('userId');
        $token = $this->input->post('token');
        $deviceId = $this->input->post('deviceId');
        $this->Patient_model->distroy_token($userId, $token, $deviceId);
        $data['response'] = 'true';
        $data['message'] = 'user logout';
        $data['data'] = array();

        $this->response($data);
    }
    public function specialistdetail()
    {
        
        // echo "<pre>";
        // print_r ($this->messages);
        // echo "</pre>";
        
        $headers = $this->input->request_headers();
        $token = $headers['Authtoken'];
		$headerdata = AUTHORIZATION::validateToken($token);
				// in case of previous login
		if ($headerdata->after) {
			$patientId = $headerdata->userid;
				$type = $headerdata->type;

		}else{
			$patientId =0;

		}
       
        $data = array();
        $id = $this->input->post('id');
        $result = $this->Patient_model->specialist_detail($id, $patientId);

        $data['response'] = 'true';
        $data['data'] = $result;

        $this->response($data);
    }
    public function specialistGallery()
    {
        $data = array();
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $data['response'] = 'true';
        $data['gallery'] = $this->Patient_model->gallery($id, $type);

        $this->response($data);
    }

    public function requestAgain()
    {
        $data = array();
        $id = $this->getPatientData()->userid;
        $userId = '';
        $patientfcm = $this->input->post('patientfcm');

        $name = $this->Patient_model->request_Again($id);

        $data['response'] = "true";
        $data['message'] = $this->message()[12]->message;
        $data['data'] = array();
        $this->response($data);
        /*code for notifification*/
        $result = $this->Patient_model->get_specialist($id);
         $userId = $result[0]->userId;

        $tokens = $this->getSpecialistToken($id);
        //print_r($tokens);
        $title = "New treatment request";
        $notification_message = 'You have new request for treatment from ' . $name[0]->userName;
        $fcmdata = array(
            'patientId' => $id,
            'patientfcm' => $patientfcm
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
                                                // 'title' => $name[0]->userName.'  requested you again',
                        'title' => $notification_message,
                        'notification_type' => "treatement_request",
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

                ob_flush();
            } else {
                  ob_start();
                $apiKey = "AAAALlGM_1A:APA91bGGucMP2XZz4lcKSLsOD9RB-zdcqz-sd9buH1pEScK2giRS4G0SutZH6mL6GJmTapyT0WzTzLj63N1oT9Lu4xyI_B6syOaODm9MznvTbPpgSookgcsbRZDWYBkbzcGS9UZVNZ70";

                $msg = [
                    'message' => $notification_message,
                    'title' => $title,
                    'URL' => "",
                    'data' => $fcmdata,
                    'notification_type' => "treatement_request",
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
                'userId' => $userId,
                'title' => $title,
                'message' => $notification_message,
                'type' => 'treatement_request',
                'data' => serialize($fcmdata),
                'entity' => 'specialist',
                'utctime ' => date("Y-m-d H:i:s"),
                'readStatus ' => false,
            );

            $result = $this->Patient_model->savefcmnotidication($notificationData);
    
    }

    public function dummyimg()
    {
        //die('hello kapu');
        $data5 = $this->Patient_model->dummy_img();
        $result = array();
        foreach ($data5 as $key => $value) {
            // $result1[]=array("images"=>base_url($value->imgName);
            $result1[] = array("images" => $value->imgName);
            //$result[]=$result;
        }
        $result['data'] = $result1;
        $result['response'] = "true";
        //print_r($result);
        $this->response($result);
    }
    public function myproductlist()
    {
        $specialId = $this->input->post('specialistId');
        $id = $this->getPatientData()->userid;
        $data1 = $this->Patient_model->my_product_list_req($id);
        $data2 = $this->Patient_model->my_product_list_to_req($id);
        $data3 = $this->Patient_model->my_product_list_res($id);
        //print_r($data3);
        $final = array_merge($data1, $data2);
        $finaldata = array_merge($final, $data3);

        // $serialized = array();
        // for ($i=0; $i < sizeof($finaldata); $i++) { 
        //   $test = in_array($finaldata['barcodeSrNo'], $serialized);
        //     if ($test == false) {
        //       $serialized[] = $finaldata[];
        //     }
        //  }
        //  foreach ($finaldata as $key => $value) {
        //  $finaldata[$key]=$this->Special_model->fetchdataifpreviousApproved($value->barcodeSrNo,$specialId);
        // $finaldata[$key]->rating = $this->Special_model->getrating($value->barcodeSrNo);
        //  $finaldata[$key]->productId="";
        //  }
        // print_r($finaldata);die();

        
        if (sizeof($finaldata) > 0) {
            $data['message'] = $this->message()[12]->message;
            $data['response'] = "true";
            $data['data'] = $finaldata;
            $this->response($data);
        } else {
            $data['response'] = "false";
            $data['message'] = $this->message()[13]->message;
            $data['data'] = array();
            $this->response($data);
        }
    }

    function super_unique($array,$key)
    {
       $temp_array = [];
       foreach ($array as &$v) {
           if (!isset($temp_array[$v[$key]]))
           $temp_array[$v[$key]] =& $v;
       }
       $array = array_values($temp_array);
       return $array;

    }

    public function contactspace()
    {
        $id = $this->input->post('userId');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $email = $this->Patient_model->get_email($id);
        //print_r($email[0]->email);

        $to      = "sumitpatial51@gmail.com";
        $subject = $subject;
        $message = $message;
        $headers = $email[0]->email . phpversion();

        $result = mail($to, $subject, $message, $headers);

        if (!$result) {
            $data['response'] = "false";
            $data['message'] = "mail failed";
            $data['data'] = array();
            $this->response($data);
        } else {
            $data1 = $this->Patient_model->insert_email_data($id, $subject, $message);
            $data['response'] = "true";
            $data['message'] = "mail send";
            $data['data'] = $data1;
            $this->response($data);
        }
    }
    public function explorenewproduct()
    {
        //print_r($_POST);die;
        $data['response'] = "true";
        $id = $this->input->post('userId');
        $eye = $this->input->post('eye');
        $forhead = $this->input->post('forhead');
        $neck = $this->input->post('neck');
        $allface = $this->input->post('allface');
        if ($this->input->post('searchproduct') != null or $eye == 1 or $forhead == 1 or $neck == 1 or $allface == 1) {
            if ($this->input->post('pagenumber') != null) {
                $pagenumber = $this->input->post('pagenumber');
            } else {
                $pagenumber = 1;
            }
            $limit = 10;
            $offset = 10 * $pagenumber - 10;

            if (ceil(
                $this->Patient_model->total_product_search($this->input->post('searchproduct'), $id, $eye, $forhead, $neck, $allface) / 10
            ) < $pagenumber) {
                $data['data'] = array();
                $data['response'] = "false";
            } else {
                $data['data'] = $this->Patient_model->search_product($this->input->post('searchproduct'), $limit, $offset, $id, $eye, $forhead, $neck, $allface);
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
                'base_url' => base_url('index.php/Patient/showspecialist'),
                'per_page' => 5,
                'total_rows' => $this->Patient_model->total_product($id)
            ];
            $config['use_page_numbers'] = true;
            $this->pagination->initialize($config);

            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
            $data = array();
            $data['response'] = 'true';
            if (ceil($this->Patient_model->total_product($id, $eye, $forhead, $neck, $allface) / 10) < $pagenumber) {
                $data['data'] = array();
            } else {
                $data['data'] = $this->Patient_model->explore_new_product($id, $limit, $offset);
            }
            $this->response($data);
        }
    }
    public function requestToAdmin()
    {
        $id = $this->input->post('userId');
        $productId = $this->input->post('productId');
        $result = $this->Patient_model->request_To_Admin($id, $productId);

        $data['response'] = "true";
        $data['message'] = "request send";
        $data['data'] = $result;
        $this->response($data);
    }
    public function deleterRequest()
    {
        $id = $this->input->post('userId');
        $productId = $this->input->post('productId');
        $result = $this->Patient_model->deleter_Request($id, $productId);
    }
    public function taketreatment()
    {

       
        $id = $this->input->post('userId');
        $date = date("Y-m-d");
        $result = $this->Patient_model->day_Request($id, $date);
        $headers = $this->input->request_headers();
        $token = $headers['Authtoken'];
        $headerdata = AUTHORIZATION::validateToken($token);
        $uname = $headerdata->username;
        if (!empty($result)) {
            $data['response'] = "true";
            $data['message'] = "Treatement complete";
            $data['data'] = $result ;
            $specilaist=$this->Patient_model->specialist_databypatientid($id);
            $this->sendnotification($id,$specilaist->userId,"Treatment Complete","Treatment Completed for the Scheduled Day by $uname","daywisetreatment_complete");
        } else {
            $data['response'] = "false";
            $data['message'] = "error";
            $data['data'] = array();
        }
        $this->response($data);
    }

    public function checker($id)
    {
        $insertedDate = $this->Patient_model->inserted_Date($id);
        

        
        //print_r(sizeof($insertedDate));die();

        if (sizeof($insertedDate) > 0) {
            $startdate = $this->Patient_model->createdate($id);
            //print_r( $startdate);
            $startdate = $startdate[0]->createdate;
            $enddate = date('Y-m-d', strtotime("-1 days"));

            for ($i = 0; $i < sizeof($insertedDate); $i++) {
                $days[] = $insertedDate[$i]->days;
            }
            $alldates = $this->dateRange($startdate, $enddate);

            $result = array_diff($alldates, $days);
            $this->Patient_model->insert_remain_date($result, $id);
        }
    }

    public function dateRange($first, $last, $step = '+1 day', $format = 'Y-m-d')
    {
        $dates = [];
        $current = strtotime($first);
        $last = strtotime($last);



        while ($current <= $last) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }


        return $dates;
    }

    public function exploreProductListasproduct()
    {
        $filterparam['eyes'] = $this->input->post('eye');
        $filterparam['forhead'] = $this->input->post('forhead');
        $filterparam['neck'] = $this->input->post('neck');
        $filterparam['allFace'] = $this->input->post('allFace');
        $type = $this->input->post('type');

        $filter = $this->input->post('filter');

        $filterarray = explode(",", $filter);

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
                'per_page' => 5,
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
                $data['data'] = $this->Special_model->exploreProductListasproduct_explore_Product_List($limit, $offset, $filterarray, $filterparam, $type);
            }
            $this->response($data);
        }
    }

    public function addToMyList()
    {
        $barcodeSrNo = $this->input->post('barcodeSrNo');
        $deviceId = $this->input->post('deviceId');
        $userId = $this->input->post('userId');
        $this->Patient_model->add_To_MyList($barcodeSrNo, $deviceId, $userId);

        $data['response'] = 'true';
        $data['message'] = 'add to list';
        $data['data'] = array();
        $this->response($data);
    }

    // manual patient product data add 
    public function addToMyListManualPatient()
    {
        $barcodeSrNo = $this->input->post('barcodeSrNo');
        $deviceId = $this->input->post('deviceId');
        $patientKey = $this->input->post('patientEmail');
        $this->Patient_model->add_To_MyList_manual_patient($barcodeSrNo, $deviceId, $patientKey);
        $data['response'] = 'true';
        $data['message'] = 'Product added Successfully';
        $data['data'] = [];
        $this->response($data);
    }

    // single product details
    public function singleproduct() {
        $headers = $this->input->request_headers();
        $token = $headers['Authtoken'];
        $headerdata = AUTHORIZATION::validateToken($token);

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
    public function chat()
    {
        $name = $this->input->post('name');
        $userId = $this->input->post('userId');
        $message = $this->input->post('message');
        $patientfcm = $this->input->post('patientfcm');

        //$token = $this->Patient_model->get_token($userId)[0]->token;
        //$type = $this->Patient_model->get_token($userId)[0]->type;

        $link = "";
        if (base64_encode(base64_decode($message, true)) === $message) {
            $current = base64_decode($message);
            $file = uniqid() . '.png';
            $current .= "/";
            file_put_contents(FCPATH . '/assets/chatimg/' . $file, $current);
            $link = 'assets/chatimg/' . $file;
            $link = $this->Patient_model->chat_img($link)[0]->img;
            $message = "";
        } else {
            $message = $message;
        }

        /*code for notifification*/


        $tokens = $this->getSpecialistToken($userId);
        //print_r($tokens);

        $fcmdata = array(
            'patientId' => $userId,
            'patientfcm' => $patientfcm
        );
        foreach ($tokens as $value) {
            $userId = $value->userId;
             // send notification if only enable otherwise break the loop
            if (!$this->Special_model->send_me_notification($userId)) break;
           
            if ($value->type == "IOS") {
                ob_start();
                $url = 'https://fcm.googleapis.com/fcm/send';
                $fields = array(
                    'to' => $value->token,
                    'notification' => array(
                        "head" => 'messagep', 'title' => $name,
                        'notification_type' => "message",
                        'text' => $message,
                        'sound' => 'default',
                        "data" => $link,
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
                    'message' => $message,
                    'title' => $name,
                    'URL' => "",
                    'data' => $fcmdata,
                    'notification_type' => "message",
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

    
            // save data array if the result of curl is successfull
            
                // $notificationData = array(
                //     'userId' => $userId,
                //     'title' => $name,
                //     'message'   => $message,
                //     'entity '   => 'specialist',
                //     'type' => 'message',
                //     'data' => serialize($fcmdata),
                //     'utctime ' => date("Y-m-d H:i:s"),
                //     'readStatus ' => false,
                // );
					// update the count 
                $this->Special_model->updateinboxmessage($userId);

            }
    
        $data['response'] = 'true';
        $data['message'] = 'message send';
        $this->response($data);
    }
    public function fcmNotification()
    {
        $name = $this->input->post('name');
        $userId = $this->input->post('userId');
        $message = $this->input->post('message');
        $imageURL = $this->input->post('imageURL');
        $videoURL = $this->input->post('videoURL');
        $dataofspec=$this->Patient_model->get_token($userId)[0];
        $token = $dataofspec->token;
        $type = $dataofspec->type;
        
        /*code for notifification*/



        if ($type == "IOS") {

            ob_start();
                $url = 'https://fcm.googleapis.com/fcm/send';
                $fields = array(
                    'to' => $token,
                    'notification' => array(
                        "head" => 'messagep', 
                        'title' => $name,
                        'notification_type' => "message",

                        'text' => $message,
                        'sound' => 'default',
                       'imageURL' => $imageURL,
                       'videoURL' => $videoURL,
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

            $notification_message = $name;
            $msg = [
                'message' => $message,
                'title' => $name,
                'imageURL' => $imageURL,
                'videoURL' => $videoURL,
                'notification_type' => "message"
            ];

            $fields = array(
                'registration_ids' => array($token),
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
            $data['response'] = 'true';
            $data['message'] = 'message send';
            $this->response($data);
            //print_r($result);
            curl_close($ch);
            return $result;
            ob_flush();
        }
    }

    // initialise the app
    public function initialise()
    {

    }

    // to get the patient notifications
    public function mynotifications()
    {
        $headers = $this->input->request_headers();

        $token = $headers['Authtoken'];
        $headerdata = AUTHORIZATION::validateToken($token);
        
        // eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhZnRlciI6ZmFsc2V9.sn9tstWY4hZ7nORaLod2AWH2lU20gojRO_gqdh9P95U
        $uid = $headerdata->userid;

        $data['data'] = $this->Patient_model->getfcmnotifications('patient', $uid);
        if (sizeof($data['data']) > 0) {
            $data['response'] = "true";
            $data['message'] = "List of notifications";
        } else {
            $data['response'] = "false";
            $data['message'] = "No Notifications";
        }

        $this->response($data);
    }

    // mark notification as read
    public function markread()
    {
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
  
    public function postreview()
    {
        $barcodeSrNo = (Integer)$this->input->post('barcodesrno');
        $comment = $this->input->post('comment');
        $rating = $this->input->post('rating');

        $headers = $this->input->request_headers();
        $token = $headers['Authtoken'];
        $headerdata = AUTHORIZATION::validateToken($token);

        $uid = $headerdata->userid;

        $datatosave = array(
            'userId' => $uid,
            'type' => 'patient',
            'barcodeSrNo' => $barcodeSrNo,
            'comment' => $comment,
            'utctime ' => date("Y-m-d H:i:s"),
            'modifiedtime ' => date("Y-m-d H:i:s"),
            'rating'   => $rating,
            'status'   => 'approved',
        );

        $data['data'] = $this->Patient_model->savereviews($datatosave);
        if (!empty($data['data'])) {
            $data['response'] = "true";
            $data['message'] = "Review Posted Successfully";
        } else {
            $data['response'] = "false";
            $data['message'] = "OOPS some error occurs";
        }

        $this->response($data);
    }


    // edit the reviews
    public function editreviews()
    {
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


    // give rating to the specialist
    public function postspecialistrating()
    {
        $feedback = $this->input->post('feedback');
        $rating = $this->input->post('rating');
        $sid = $this->input->post('specialistId');
        $patientData=$this->getPatientData();
        
     
        $patientId = $patientData->userid;
     
       
        $datatosave = array(
            'userId' => $sid,
            'givenby' => $patientId,
            'feedback' => $feedback,
            'timestamp '   => date("Y-m-d H:i:s"),
            'modifiedtime' => date("Y-m-d H:i:s"),
            'rating'   =>  $rating,
            'status'   => 'approved',
        );
        $data['data'] = [];

        if ($this->Patient_model->specialist_rating($datatosave) == true) {
            // send notification to specialist about the feedback 
        
                        $tokens = $this->getSpecialistToken($patientId);

                        $notification_message = "You have a new feedback from ".$patientData->username;
                        $title = "New Feedback";
                        $fcmdata = array(
                            'patientId' => $patientId,
                        );
                        //print_r($tokens);
                        foreach ($tokens as $value) {
                            $userId = $value->userId;

                             // send notification if only enable otherwise break the loop
                             if (!$this->Special_model->send_me_notification($userId)) break;

                          
                            if ($value->type == "IOS") {
                                ob_start();
                                $url = 'https://fcm.googleapis.com/fcm/send';
                                $fields = array(
                                    'to' => $value->token,
                                    'notification' => array(
                                        "head" => $title,
                                        'title' => $notification_message,
                                        'notification_type' => "treatement_request",
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
                                    'notification_type' => "treatement_request",
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

                                curl_close($ch);
                                $result;
                                ob_flush();
                            }
                        }
                

                        // save data array if the result of curl is successfull

                        
                            // data to save into the fcm notification table
                            $notificationData = array(
                                'userId' => $sid,
                                'title' => $title,
                                'message'   => $notification_message,
                                'entity '   => 'specialist',
                                'type' => 'feedback',
                                'data' => serialize($fcmdata),
                                'utctime ' => date("Y-m-d H:i:s"),
                                'readStatus ' => false,
                            );

                            $result = $this->Patient_model->savefcmnotidication($notificationData);




            // end of send notification to specialist about the feedback
            $data['response'] = "true";
            $data['message'] = "Review Posted Successfully";
        } else {
            $data['response'] = "false";
            $data['message'] = "OOPS some error occurs";
        }

        $this->response($data);
    }


    // edit the patient feed back and rating
    public function editfeedback()
    {
        $feedback = $this->input->post('feedback');
        $rating = $this->input->post('rating');
        $reviewId = (Integer)$this->input->post('reviewId');
        $patientData=$this->getPatientData();
        $patientId = $patientData->userid;
        $data['data'] = [];

        if ($this->Special_model->edit_feedback($feedback, $rating, $reviewId) == true) {
            $data['response'] = "true";
            $data['message'] = "Review Modified Successfully";
            $tokens = $this->getSpecialistToken($patientId);

                        $notification_message = "A Review has been modified by ".$patientData->username ;
                        $title = "Feedback Changed";
                        $fcmdata = array(
                            'patientId' => $patientId,
                        );
                        //print_r($tokens);
                        foreach ($tokens as $value) {
                            $userId = $value->userId;
                             // send notification if only enable otherwise break the loop
                             if (!$this->Special_model->send_me_notification($userId)) break;

                          
                            if ($value->type == "IOS") {
                                ob_start();
                                $url = 'https://fcm.googleapis.com/fcm/send';
                                $fields = array(
                                    'to' => $value->token,
                                    'notification' => array(
                                        "head" => $title,
                                        'title' => $notification_message,
                                        'notification_type' => "treatement_request",
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
                                    'notification_type' => "treatement_request",
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

                                curl_close($ch);
                                $result;
                                ob_flush();
                            }
                        }
                

                        // save data array if the result of curl is successfull

                        
                            // data to save into the fcm notification table
                            $notificationData = array(
                                'userId' => $userId,
                                'title' => $title,
                                'message'   => $notification_message,
                                'entity '   => 'specialist',
                                'type' => 'feedback',
                                'data' => serialize($fcmdata),
                                'utctime ' => date("Y-m-d H:i:s"),
                                'readStatus ' => false,
                            );

                            $result = $this->Patient_model->savefcmnotidication($notificationData);



        } else {
            $data['response'] = "false";
            $data['message'] = "OOPS some error occurs";
        }

        $this->response($data);
    }

    // dashboard of patient
    public function dashboard()
    {

        //
        $data = array('data' => [], 'message' => "No data found", 'response' => "false");
        $headers = $this->input->request_headers();
        $token = $headers['Authtoken'];
        $headerdata = AUTHORIZATION::validateToken($token);

        $uid = $headerdata->userid;
        
    
        
        $data['data'] = $this->Patient_model->getallcounts($uid);
        if (sizeof($data['data']) > 0) {
            $data['response'] = "true";
            $data['message'] = "Patient Dashboard";
            $this->response($data);
        }
        // $this->response($data);
    }


    // get the userid 
    public function getPatientData()
    {
        $headers = $this->input->request_headers();
        $token = $headers['Authtoken'];
        $headerdata = AUTHORIZATION::validateToken($token);

        // in case of token is generated after the login
        if ($headerdata->after == true) {
            return $headerdata;
        } else {
            $headerdata->userid=0;
            return $headerdata;
        }
    }

    // list of filter attributes 
    public function filterlisting()
    {
        $data = array('data' => [], 'message' => "No data found", 'response' => "false");

        $data['data'] = $this->Patient_model->getlistingoffilters();
        if (sizeof($data['data']) > 0) {
            $data['response'] = "true";
            $data['message'] = "listing of filters";
            $this->response($data);
        }
    }


    // terms and condition webview
    public function termsandconditions()
    {
        $data['response'] = "true";
        $data['message'] = "terms and conditions page";
        $data['data'] = base_url('index.php/user/termsandconditions');
        $this->response($data);
    }

    // complete the profile after social login
    public function completeprofile() {
        $id = $this->getPatientData()->userid;
        $data['age'] = $this->input->post('dob');
        // $data['email'] = $this->input->post('email');
        $data['phone'] = $this->input->post('phone');
        $data['proPicName'] = $this->input->post('proPicName');
        $data['fcmUser'] = 'p'.$this->input->post('phone');
        $data['iscompleted'] = true;
         
   
         
        //  in case phone number exist 
         if ($this->Patient_model->check_phone($this->input->post('phone'))==1) {
            $senddata['response'] = "false";
            $senddata['message'] = "This Phone Number is already register with us.";

         }
        //  else if ($this->Patient_model->check_email($data['email'],$id)>0) {
        //        $senddata['response'] = "false";
        //     $senddata['message'] = "This email is already registered with us.";

        //  }
         
         else {
        $result=$this->Patient_model->complete_profile($id, $data);
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

    // my treatment patitent
    public function mytreatment()
    {
        // $date = $this->input->post('date');
        $id = $this->getPatientData()->userid;
        //$date = '2018-09-15';
        $res = array();
        $time = array();
        $data = $this->Patient_model->my_sheduale($id);
        for ($i = 0; $i < sizeof($data); $i++) {

        //print_r($data);

            if ($data[$i]->everyday == 1) {
                for ($k = 0; $k < 30; $k++) {
                    $everyday[] = date("Y-m-d", strtotime($data[$i]->createdate . " + " . $k . " days"));
                    //print_r($days);
                    //echo"abc";
                    //print_r( $data[$i]);
                    $time[] = +$data[$i]->minute;
                    $res[] = $data[$i];
                }
                

                
                //print_r($days);
            }
            if ($data[$i]->onceAweek == 1) {
                for ($k = 0; $k < 4; $k++) {
                    $b = $k * 7;
                    $onceAweek[] = date("Y-m-d", strtotime($data[$i]->createdate . " + " . $b . " days"));
                    //echo"abc";
                    //print_r( $data[$i]);
                    $time[] = +$data[$i]->minute;
                    $res[] = $data[$i];
                }
            }
            if ($data[$i]->twiceAweek == 1) {
                for ($k = 0; $k < 8; $k++) {
                    $b = $k * 3;
                    $twiceAweek[] = date("Y-m-d", strtotime($data[$i]->createdate . " + " . $b . " days"));
                  
                    //echo"abc";
                    //print_r( $data[$i]);
                    $time[] = +$data[$i]->minute;
                    $res[] = $data[$i];
                }
            }
            if ($data[$i]->onceAmonth == 1) {
                
                  
                        //echo"abc";
                //print_r( $data[$i]);
                $time[] = +$data[$i]->minute;
                $res[] = $data[$i];
            }
        }  //print_r($time);
        $finaltime = 0;
        for ($i = 0; $i < sizeof($time); $i++) {
            $finaltime = $finaltime + $time[$i];
        }

        if (sizeof($res) > 0) {
            // $status=$this->Patient_model->taketreatment_button($id, $date);

            $data12['response'] = "true";
            // $data12['buttonStatus']=$status;
            $data12['message'] = $this->message()[12]->message;
            $data12['time'] = strval($finaltime);
            $data12['data'] = $res;

            $this->response($data12);
        } else {
            $data12['response'] = "false";
            $data12['buttonStatus'] = 'no';
            $data12['message'] = $this->message()[13]->message;
            $data12['data'] = array();
            $this->response($data12);
        }
        // $this->response($res);
    }

    // save the image after treatment complete 
    // 
    public function imagereport() {
        $datatosave['treatmentId'] = $this->input->post('treatmentId');
        $picname = $this->input->post('picname');
        $datatosave['timestamp'] =  date("Y-m-d H:i:s");
        $datatosave['patientId'] =$this->getPatientData()->userid;
         $current = base64_decode($picname);
        $file = uniqid() . '.png';
        $current .= "/";
        file_put_contents(FCPATH . '/assets/patientreportimages/' . $file, $current);
        $datatosave['imageUrl']= 'assets/patientreportimages/' . $file;

        if ($this->Patient_model->save_image($datatosave)) {
            $data['response'] = "true";
            $data['message'] = "Looking Great!";
        } else {
            $data['response'] = "false";
            $data['message'] = "OOPS some error occurs";
        }
        $this->response($data);
    }

    // treat ment listing   
    public function treatmentlisting() {
        $data = array('data' => [], 'message' => "No treatment found", 'response' => "false");
        // $headerdata = $this->getheaderdata();

        $uid = $this->getPatientData()->userid;
        $data['data'] = $this->Patient_model->get_mytreatment($uid);
        if (sizeof($data['data']) > 0) {
            $data['response'] = "true";
            $data['message'] = "treatment listing";
          
        }
          $this->response($data);
        // $this->response($data);
    }

    // change notification setting 
    public function changenotificationsetting() {
        $status= $this->input->post('status');
        $patientId =$this->getPatientData()->userid;
        $temp=$this->Patient_model->change_notification_setting($patientId,'patient',$status);
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

     // generate qr code  if token is not avaliable 
    public function generateQrcodeforpatient($uid = "",$url) {
        $this->load->library('ciqrcode');
        $qr_image = $uid . '.png';
        $params['data'] = $url;
        $params['level'] = 'H';
        $params['size'] = 8;
        $params['savename'] = FCPATH . "assets/patientShareQrCode/" . $qr_image;
    	return $this->ciqrcode->generate($params);
         
    }
      // generate qr code  if token is not avaliable 
    public function createvideothumbnail($uid = "") {


        $videos_dir = FCPATH.'/assets/videos';
        $videos_dir = opendir($videos_dir);
        $output_dir =  FCPATH.'/assets/videothumbnail/';

        $file='HydroTherm Intense Masque.mp4';
        while (false !== ($file = readdir($videos_dir))) {
            if ($file != '.' && $file != '..'){
                $in = $videos_dir.'/'.$file;
                $out = $output_dir.$file.'.jpg';
                // exec("/usr/local/bin/ffmpeg -itsoffset -105 -i ".$in." -vcodec mjpeg -vframes 1 -an -f rawvideo -s 100x100 ".$out);
            }
        }
    	//  return $this->response();
         
    }

    // send the notification
    public function sendnotification($id,$recieverId,$title,$notification_message,$type) {
         /*code for notifification*/

         $tokens = $this->getSpecialistToken($id);

         
        
         $fcmdata = array(
         'patientId' => $id,
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
         'notification_type' => "treatement_request",
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
         'entity' => 'specialist',
         'utctime ' => date("Y-m-d H:i:s"),
         'readStatus ' => false,
         );

         $result = $this->Patient_model->savefcmnotidication($notificationData);

    
    }


    public function exploreProductListasbrand() {
        $data['response'] = 'true';
        if ($this->input->post('searchbrand') != null) {
            if ($this->input->post('pagenumber') != null) {
                $pagenumber = $this->input->post('pagenumber');
            } else {
                $pagenumber = 1;
            }
            $limit = 10;
            $offset = 10 * $pagenumber - 10;

            if (ceil($this->Special_model->total_search($this->input->post('searchbrand')) / 10) < $pagenumber) {
                $data['data'] = array();
                $data['response'] = 'false';
            } else {
                $data['data'] = $this->Special_model->search_explore_Product_List($this->input->post('searchbrand'), $limit, $offset);
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
                'per_page' => 5,
                'total_rows' => $this->Special_model->total_num()
            ];
            $config['use_page_numbers'] = true;
            $this->pagination->initialize($config);

            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
            $data = array();
            $data['response'] = 'true';
            if (ceil($this->Special_model->total_num() / 10) < $pagenumber) {
                $data['data'] = array();
                $data['response'] = 'false';
            } else {
                $data['data'] = $this->Special_model->explore_Product_List($limit, $offset);
            }
            $this->response($data);
        }
    }


     public function showproductbybrand() {
        $data['response'] = 'true';
        $brandName = $this->input->post('brandName');
        if ($this->input->post('searchproduct') != null) {
            if ($this->input->post('pagenumber') != null) {
                $pagenumber = $this->input->post('pagenumber');
            } else {
                $pagenumber = 1;
            }
            $limit = 10;
            $offset = 10 * $pagenumber - 10;

            if (ceil($this->Special_model->showproductbybrand_total_search($this->input->post('searchproduct'), $brandName) / 10) < $pagenumber) {
                $data['data'] = array();
                $data['response'] = 'false';
            } else {
                $data['data'] = $this->Special_model->showproductbybrand_search_explore_Product_List($this->input->post('searchproduct'), $brandName, $limit, $offset);
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
                'total_rows' => $this->Special_model->showproductbybrand_total_num($brandName)
            ];
            $config['use_page_numbers'] = true;
            $this->pagination->initialize($config);

            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
            $data = array();
            $data['response'] = 'true';
            if (ceil($this->Special_model->showproductbybrand_total_num($brandName) / 10) < $pagenumber) {
                $data['data'] = array();
                $data['response'] = 'false';
            } else {
                $data['data'] = $this->Special_model->showproductbybrand_explore_Product_List($brandName, $limit, $offset);
            }
            $this->response($data); 
        }
    }

   

    // work after 14 jan 

    // patient  qr code 
    public function myqrcode() {
        $data['response'] = "true";
        $data['message'] = "Patient qr code ";
        $headerdata = $this->getPatientData();
		$uid = $headerdata->userid;
		$filename = base_url('/assets/patientQR/'.$uid.'.png');

		if (file_exists($filename)) {
			$data['data'] = base_url('/assets/patientQR/' . $uid . '.png');
        	$this->response($data);

		} else {
			if ($this->generateToken($uid)) {
				$data['data'] = base_url('/assets/patientQR/' . $uid . '.png');
            	$this->response($data);

			}else{
   				$data['response'] = "false";
        		$data['message'] = "QR code unavaliable";
				$data['data'] = "";
				$this->response($data);

			}
			
		}

        
    }

    // generate the patient qr code in patientQR folder 
     public function generateToken($uid = "") {
        $this->load->library('ciqrcode');
        $qr_image = $uid . '.png';
        $params['data'] = "P_".$uid;
        $params['level'] = 'H';
        $params['size'] = 8;
        $params['savename'] = FCPATH . "assets/patientQR/" . $qr_image;
    	return $this->ciqrcode->generate($params);
         
    }
   


    // for developers
    public function fordevelopers()  {
        $headers = $this->input->request_headers();
        $token = $headers['Authtoken'];
        $headerdata = AUTHORIZATION::validateToken($token);
         $this->response($headerdata);
    }
    
    public function getheaderdata()  {
        $headers = $this->input->request_headers();
        $token = $headers['Authtoken'];
        $headerdata = AUTHORIZATION::validateToken($token);
        return $headerdata;
    }
    
    public function facialBookingCalendar(){
		$headerdata	= $this->getheaderdata();
        $sId 		= $headerdata->userid;
		
		$timeZone     = ($this->input->post('timezone'))?$this->input->post('timezone'):'Asia/kolkata';
		$patientId = ($this->input->post('patientId'))?$this->input->post('specialistId'):$sId;
		$bookingData  = $this->Patient_model->facial_booking_calendar($patientId,$timeZone);
		$data = array();
		if(!empty($bookingData)){
			$data['response']="true";
			$data['message']="Treatment schedules!";
			$data['data']=$bookingData;
			
		}else{

			$data['response']="false";
			$data['message']="error !";
			$data['data']=array();
		}
		$this->response($data);//convert array to JSON
	}
	
	//get list of facials booked by me for me
	public function myBookedFacials(){
		$headerdata	= $this->getheaderdata();
        $sId 		= $headerdata->userid;
		$rowCount 	= '0';
		$sDate		= '0';
		$sDay		= '0';
		
		$selectedDate  = ($this->input->post('selectedDate'))?$this->input->post('selectedDate'):'0';
		$bookingData   = $this->Patient_model->get_my_booked_facials($sId,$selectedDate);
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

    
}
