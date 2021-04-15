<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**

 * User_model class.

 *

 * @extends CI_Model

 */
class Patient_model extends CI_Model
{
    /**

     * __construct function.

     *

     * @access public

     * @return void

     */

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    /**

     * create_user function.

     *

     * @access public

     * @param mixed $username

     * @param mixed $email

     * @param mixed $password

     * @return bool true on success, false on failure

     */

    public function message()
    {
        $data = array();

        $query = $this->db->query("select * from alert_message");

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function member_Id($userId, $memberId)
    {
        $this->db->query("update user_patient set memberId=" . $memberId . " where userId=" . $userId . "");

        $query = $this->db->query("select * from user_patient where userId=" . $userId . "");

        return $query->result();
    }
     public function get_token($userId)
     {
     $query = $this->db->query("SELECT * FROM sepcial_users where userId='" . $userId . "'");

     return $query->result();
     }

    public function create_user(
        $userName = "",
        $age = "",
        $city = "",
        $link = "",
        $countary = "",
        $password = "",
        $gender = "",
        $phone = "",
        $email = "",
        $token = "",
        $type = "",
        $deviceId = "",
        $loginStrategy = "local-oauth",
        $profileId = ""
    ) {
        $fcmUser = "";
        $userId = "";
        $fcmUser = 'p' . $profileId;
        $isCompleted=true;
        switch ($loginStrategy) {
                // in case of google authentication
            case 'google-oauth':

            $isCompleted=false;

                $query = $this->db->get_where('user_patient', array('profileId' => $profileId, 'loginStrategy' => 'google-oauth'));
                if ($query->num_rows() > 0) {
                    $userId = $query->result()[0]->userId;
                }

            break;

            case 'facebook-oauth':
                $isCompleted=false;

                $query = $this->db->get_where('user_patient', array('profileId' => $profileId, 'loginStrategy' => 'facebook-oauth'));
                if ($query->num_rows() > 0) {
                    $userId = $query->result()[0]->userId;
                }

            break;

            default:
                $fcmUser = "p" . $phone;
            break;
        }

 
        // user id in case user is already signup 

        $query = $this->db->get_where('user_patient', array('email' => $email));
        if ($query->num_rows() > 0) {
            $userId = $query->result()[0]->userId;
        }


        $data = array(
            'userName'   => $userName,
            'age'   => $age,
            'city' => $city,
            'proPicName' => $link,
            'countary'   => $countary,
            'email'      => $email,
            'password'   => $this->hash_password($password),
            'gender' => $gender,
            'phone' => $phone,
            'type' => $type,
            'token' => $token,
            'status' => 1,
            'fcmUser' => $fcmUser,
            'loginStrategy' => $loginStrategy,
            'profileId' => $profileId,
            'iscompleted ' => $isCompleted
        );

        if ($userId == "") {
            $this->db->insert('user_patient', $data);
            $insert_id = $this->db->insert_id();
        } else {
            $insert_id = $userId;
        }

        $insert = $this->db->query("select * from user_token where userId='" . $insert_id . "'and token='" . $token . "' and userType='patient'")->result();

        if (sizeof($insert) > 0) {
        } else {
            $this->db->query(
                "INSERT INTO user_token (userId, token, deviceId,type,userType)

			VALUES ('" . $insert_id . "','" . $token . "','" . $deviceId . "','" . $type . "','patient')"
            );
        }

        $query = $this->db->query("select * from user_patient where userId=" . $insert_id . "");

        return  $query->result();
    }

    public function get_user($user_id, $token, $type, $deviceId)
    {
        $this->db->from('user_patient');

        $this->db->where('userId', $user_id);

        $insert = $this->db->query("select * from user_token where userId='" . $user_id . "'and token='" . $token . "' and userType='$deviceId'")->result();

        if (sizeof($insert) > 0) {
            $this->db->query("update user_patient set token='" . $token . "', type='" . $type . "' where userId=" . $user_id . "");
        } else {
            $this->db->query(
                "INSERT INTO user_token (userId, token, deviceId,type,userType)

			VALUES ('" . $user_id . "','" . $token . "','" . $deviceId . "','" . $type . "','patient')"
            );

            $this->db->query("update user_patient set token='" . $token . "', type='" . $type . "' where userId=" . $user_id . "");
        }
        $data=$this->db->get()->row();
        array_walk($data, function (&$item) {
            $item = strval($item);
            $item = htmlentities($item);
            $item = html_entity_decode($item);
        });
        return $data;

    }

    public function get_specialist($userId)
    {
        $query = $this->db->query("select * from patient_treatement where patientId='" . $userId . "' ");
        if ($query->num_rows() > 0) {
            $id = $query->result()[0]->specialId;
            return $data = $this->db->query("select * from sepcial_users where userId='" . $id . "' ")->result();
        } else {
            return [];
        }
    }

    public function get_Concern($userId)
    {
        $data = array();
        $query = $this->db->query("select concernType from patient_treatement where patientId='" . $userId . "' ");
        foreach ($query->result() as $row) {//print_r($row);
            $data[] = $row;
        }
        return $data;
        return $this->db->query("select * from sepcial_users where userId='" . $specialId . "'")->result();
    }

    public function get_user_id_from_username($username)
    {
        $this->db->select('userId');

        $this->db->from('user_patient');

        $this->db->where('email', $username);

        return $this->db->get()->row('userId');
    }

    public function search_specialist($searchspecialist, $limit, $offset)
    {
        $query = $this->db->query(
            "select * from sepcial_users where userName LIKE '%" . $searchspecialist . "%' or phone LIKE '%" . $searchspecialist . "%' or email LIKE '%" . $searchspecialist . "%' limit " . $offset . ", " . $limit . ""
        );

        foreach ($query->result() as $row) {
            $row->noOfProduct = (String)$query->num_rows();

            // to fetch the no of patient

            $this->db->where('specialId', $row->userId);

            $query = $this->db->get('patient_treatement');

            $row->noOfPatient = (String)$query->num_rows();

            $row->rating = $this->getratingforspecialist($row->userId);

            $row->noOfReviews = (String)$this->getreviewsCountforspecialist($row->userId);

            $data[] = $row;
        }

        return $data;
    }

    public function total_search($searchspecialist)
    {
        $query = $this->db->query(
            "select * from sepcial_users where userName LIKE '%" . $searchspecialist . "%' or phone LIKE '%" . $searchspecialist . "%' or email LIKE '%" . $searchspecialist . "%'"
        );

        return $query->num_rows();
    }

    public function check_email_exist($email){
        $data = false ;
        $query = $this->db->get_where('user_patient', array('email' => $email,'loginStrategy'=>'local-oauth'));
        if ($query->num_rows() > 0) {
          return true;
        }else {
              return false;
        }

    }  
    public function resolve_user_login($username, $password)
    {
        $this->db->select('password,loginStrategy');
        $this->db->from('user_patient');
        $this->db->where('email', $username);
        $this->db->where('loginStrategy', 'local-oauth');

        $query = $this->db->get();
        $user_data = $query->row_array();
        // echo $this->db->last_query();
        // print_r($user_data); die;
        $hash = $user_data['password'];
   
        
        if ($user_data['loginStrategy'] != "local-oauth") {
            return false;
        }
        return $this->verify_password_hash($password, $hash);
    }

    public function create_barcode($productName, $brandname, $barcodesrno, $file_name)
    {
        $this->db->query(
            "INSERT INTO bar_code_detail (barcodeSrNo, productName, brandName,img,status)
			VALUES ('" . $barcodesrno . "','" . $productName . "', '" . $brandname . "','" . $file_name . "',2)"
        );
        return $insert_id = $this->db->insert_id();
    }

    public function exist_bar($barcodesrno)
    {
        //return $this->db->query("select * from bar_code_detail where barcodesrno=".$barcodesrno."");

        $this->db->from('bar_code_detail');
        $this->db->where('barcodesrno', $barcodesrno);
        return $this->db->get()->row();
    }
    public function add_product($barcodesrno, $userid)
    {
        $this->db->query("INSERT INTO patient_product_rel (barcodesrno, userId)

			VALUES ('" . $barcodesrno . "','" . $userid . "')");

        return 1;
    }

    public function shows_specialist($limit, $offset)
    {
        $query = $this->db->limit($limit, $offset)->get('sepcial_users');

        //echo $this->db->last_query();

        foreach ($query->result() as $row) {



                // to fetch the no of product specialist

            $this->db->where('userId', $row->userId);

            $query = $this->db->get('specialist_product_rel');

            $row->noOfProduct = (String)$query->num_rows();

            // to fetch the no of patient

            $this->db->where('specialId', $row->userId);

            $query = $this->db->get('patient_treatement');

            $row->noOfPatient = (String)$query->num_rows();

            $row->rating = $this->getratingforspecialist($row->userId);

            $row->noOfReviews = (String)$this->getreviewsCountforspecialist($row->userId);

            $data[] = $row;
        }

        return $data;
    }

    public function check_product($deviceId, $userId, $barcodesrno)
    {
        if ($userId != null) {
            $query = $this->db->query("select * from patient_product_rel where userId='" . $userId . "' and barcodesrno='" . $barcodesrno . "' ");

            //echo $this->db->last_query();

            if ($query->num_rows() > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            $query = $this->db->query("select * from patient_product_device where deviceId='" . $deviceId . "' and barcodesrno='" . $barcodesrno . "' ");

            //echo $this->db->last_query();

            if ($query->num_rows() > 0) {
                return 1;
            } else {
                return 0;
            }
        }
    }
    public function check_product_to_manual_patient($deviceId, $barcodesrno,$patientKey)
    {
       
         $query = $this->db->query("select * from patient_product_device_manual_patient where deviceId='" . $deviceId . "' and barcodesrno='" . $barcodesrno . "' and patient_key= '".$patientKey."'");

            if ($query->num_rows() > 0) {
                return 1;
            } else {
                return 0;
            }
        
    }

    public function temp_Id()
    {
        $query = $this->db->query("select max(id) as id from temp_id");

        $id = $query->result();

        $id = $id[0]->id + 1;

        $query = $this->db->query("insert into temp_id (id) values (" . $id . ")");

        $query = $this->db->query("select max(id) as id from temp_id");

        $id = $query->result();

        $id = $id[0]->id;

        $_SESSION["id"] = $id;
    }

    public function add_product_device($barcodesrno, $deviceId)
    {
        $this->db->query("INSERT INTO patient_product_device (barcodesrno, deviceId)

			VALUES ('" . $barcodesrno . "','" . $deviceId . "')");

        return 1;
    }
    public function add_product_device_patient_manual($barcodesrno, $deviceId,$patientKey)
    {
        $data = array(
        'barcodesrno'=>$barcodesrno,
        'deviceId'=>$deviceId,
        'patient_key'=>$patientKey,
        );
        $this->db->insert('patient_product_device_manual_patient',$data);
        return $this->db->insert_id();
    }

    public function view_product($id, $deviceId)
    {
        $data = array();

        if ($id != null) {
            $query = $this->db->query(
                "select patient_product_device.*,bar_code_detail.* from patient_product_device left join bar_code_detail on patient_product_device.barcodesrno=bar_code_detail.barcodeSrNo where patient_product_device.userId=" . $id . ""
            );
        } else {
            $sql="select patient_product_device.*,bar_code_detail.* from patient_product_device left join bar_code_detail on patient_product_device.barcodesrno=bar_code_detail.barcodeSrNo where patient_product_device.deviceId='".$deviceId."'";
            
           
        //  $query=$this->db->query("select patient_product_device.*,bar_code_detail.* from patient_product_device left join bar_code_detail on patient_product_device.barcodesrno=bar_code_detail.barcodeSrNo where patient_product_device.deviceId='".$deviceId."'");

            $query = $this->db->query(
                "select admin_product.*,patient_product_device.*,bar_code_detail.* from patient_product_device left join bar_code_detail on patient_product_device.barcodesrno=bar_code_detail.barcodeSrNo inner join admin_product on admin_product.barcodeSrNo=patient_product_device.barcodesrno where patient_product_device.deviceId='" . $deviceId . "'"
            );
        }

        //echo $this->db->last_query();

        foreach ($query->result() as $row) {
            
             
            array_walk(
                $row,
                function (&$item) {
                    $item = strval($item);

                    $item = htmlentities($item);

                    $item = html_entity_decode($item);
                }
            );

            $row->rating = $this->getrating($row->barcodeSrNo);
            // get the parts icon
            $row->partIcon = "";

            if ($row->eyes=="1") {
                $row->partIcon = base_url('assets/bodyparts/eyes.png');
            } elseif ($row->forhead=="1") {
                $row->partIcon = base_url('assets/bodyparts/forhead.png');
            } elseif ($row->neck=="1") {
                $row->partIcon = base_url('assets/bodyparts/neck.png');
            } elseif ($row->allFace=="1") {
                $row->partIcon = base_url('assets/bodyparts/allFace.png');
            }

            $data[] = $row;
        }

        return $data;
    }
    public function getmanualpatientproduct($patientKey, $deviceId)
    {

        $data=[];
        $barCode=[];
        $this->db->select('*');
        $this->db->from('patient_product_device_manual_patient');
        $this->db->join('admin_product', 'patient_product_device_manual_patient.barcodesrno =admin_product.barcodeSrNo','inner');
        $this->db->where('patient_product_device_manual_patient.patient_key',$patientKey);
        $this->db->where('patient_product_device_manual_patient.deviceId',$deviceId);
        // foreach ($query->result() as $row) {
        foreach ($this->db->get()->result() as $row) {
           
            array_walk(
                $row,
                function (&$item) {
                    $item = strval($item);

                    $item = htmlentities($item);

                    $item = html_entity_decode($item);
                }
            );
            array_push($barCode,$row->barcodeSrNo); 
            $row->rating = $this->getrating($row->barcodeSrNo);
            
            // get the parts icon
            $row->partIcon = "";

            if ($row->eyes=="1") {
                $row->partIcon = base_url('assets/bodyparts/eyes.png');
            } elseif ($row->forhead=="1") {
                $row->partIcon = base_url('assets/bodyparts/forhead.png');
            } elseif ($row->neck=="1") {
                $row->partIcon = base_url('assets/bodyparts/neck.png');
            } elseif ($row->allFace=="1") {
                $row->partIcon = base_url('assets/bodyparts/allFace.png');
            }

            $data[] = $row;
        }
      
     
      
        
        // $data = array();
        // $query = $this->db->query("select admin_product.*,patient_product_device_manual_patient.*,bar_code_detail.* from patient_product_device_manual_patient left join bar_code_detail on patient_product_device_manual_patient.barcodesrno=bar_code_detail.barcodeSrNo inner join admin_product on admin_product.barcodeSrNo=patient_product_device_manual_patient.barcodesrno where patient_product_device_manual_patient.deviceId='" . $deviceId . "' and patient_product_device_manual_patient.patient_key='".$patientKey."'");
        $this->db->select('*');
        $this->db->from('patient_product_device_manual_patient');
        $this->db->join('bar_code_detail', 'patient_product_device_manual_patient.barcodesrno =bar_code_detail.barcodeSrNo','inner');
        $this->db->where('patient_product_device_manual_patient.patient_key',$patientKey);
        $this->db->where('patient_product_device_manual_patient.deviceId',$deviceId);
        // foreach ($query->result() as $row) {
        foreach ($this->db->get()->result() as $row) {
            



            
            if (in_array($row->barcodesrno, $barCode)) {
               continue;
            }
            $row->id="";
            $row->barcodesrno="";
            $row->deviceId="";
            $row->patient_key="";
            $row->productId="";
            $row->barcodeId="";
            $row->eyes="";
            $row->forhead="";
            $row->neck="";
            $row->allFace="";
            $row->everyday="";
            $row->onceAweek="";
            $row->twiceAweek="";
            $row->onceAmonth="";
            $row->instruction="";
            $row->am="";
            $row->pm="";
            $row->minute="";
            $row->step="";
            $row->numberOfStep="";
            $row->media="";
            $row->productStatus="";
            $row->type="";
            $row->price="";
            $row->productOrder="";
            $row->rating="";
            $row->partIcon="";
           
            array_walk(
                $row,
                function (&$item) {
                    $item = strval($item);

                    $item = htmlentities($item);

                    $item = html_entity_decode($item);
                }
            );
          
            $row->rating = $this->getrating($row->barcodeSrNo);
           

            $data[] = $row;
        }
        

        return $data;
    }

    public function reject_barcode($barcodesrno)
    {



        //return $this->db->query("select * from bar_code_detail where barcodesrno=".$barcodesrno."");

        $query = $this->db->query("select * from bar_code_detail where barcodeSrNo='" . $barcodesrno . "' and status=0");

        return $query->num_rows();
    }

    public function choose_concern()
    {
        $query = $this->db->query("select * from concerns");

        foreach ($query->result() as $row) {
            $row->picname = base_url($row->picname);

            $data[] = $row;
        }

        return $data;
    }

    public function total_num()
    {
        $query = $this->db->query('SELECT * FROM sepcial_users');

        return $query->num_rows();
    }

    public function shows_pecialist($limit, $offset)
    {
        $query = $this->db->limit($limit, $offset)->get('sepcial_users');

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function patient_Treatment($concernType, $specialId, $deviceId)
    {
        $concernType = explode(',', $concernType);

        $checker = $this->db->query("select * from temp_choose_special where deviceId= '" . $deviceId . "' ")->num_rows();

        if ($checker > 0) {
            $this->db->query("delete  from temp_choose_special where deviceId= '" . $deviceId . "' ");
        }
        
     

        foreach ($concernType as $key => $value) {
            $this->db->query(
                "INSERT INTO temp_choose_special (deviceId, specialId, concernType)

			VALUES ('" . $deviceId . "','" . $specialId . "', '" . $value . "')"
            );
        }
    }

    /**

     * hash_password function.

     *

     * @access private

     * @param mixed $password

     * @return string|bool could be a string on success, or bool false on failure

     */

    private function hash_password($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**

     * verify_password_hash function.

     *

     * @access private

     * @param mixed $password

     * @param mixed $hash

     * @return bool

     */

    private function verify_password_hash($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function specialist_name($id)
    {
        $query = $this->db->query(
            "SELECT sepcial_users.userName FROM `sepcial_users` inner join patient_treatement on sepcial_users.userId=patient_treatement.specialId where patient_treatement.patientId=" . $id . " limit 0,1"
        );

        return $query->result();

        //return $data;
    }
    public function specialist_databypatientid($id)
    {
        $sql= "SELECT sepcial_users.userName,sepcial_users.userId FROM `sepcial_users` inner join patient_treatement on sepcial_users.userId=patient_treatement.specialId where patient_treatement.patientId=" . $id . " limit 0,1";
        
 
        $query = $this->db->query($sql);

        return $query->result()[0];

        //return $data;
    }

    public function concern_types($id)
    {
        $data = [];
        $query = $this->db->query("select concernType from patient_treatement  where patientId=" . $id . "");

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function show_profile($id)
    {
        $query = $this->db->query("select * from user_patient where userId=" . $id . "");

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function insert_patient_treatment($id, $deviceId)
    {
        $query = $this->db->query("select * from temp_choose_special where deviceId='" . $deviceId . "'");

        foreach ($query->result() as $row) {
            $this->db->query(
                "INSERT INTO patient_treatement (concernType, patientId, specialId)

			VALUES ('" . $row->concernType . "','" . $id . "', '" . $row->specialId . "')"
            );
        }
    }

    public function dummy_img()
    {
        $query = $this->db->query("select * from dummy_image");

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function request_specialist($userId)
    {
        $query = $this->db->query("select * from patient_treatement where patientId='" . $userId . "' ");

        //echo $this->db->last_query();

        if ($query->num_rows() > 0) {
            $data = $query->result();
            $this->db->query(
                "INSERT INTO notification (patientId, specialistId)

			            VALUES (" . $userId . "," . $data[0]->specialId . ")"
            );
        }

        //print_r($data);
    }

    public function insert_patient_rel($userId, $deviceId)
    {
        $query = $this->db->query("select * from patient_product_device where deviceId='" . $deviceId . "'");

        $data = array();

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        foreach ($data as $key => $value) {
            $this->db->query(
                "INSERT INTO patient_product_rel (barcodesrno, userId, manufactureDate,expiryDate)

			VALUES ('" . $value->barcodesrno . "','" . $userId . "', '" . $value->manufactureDate . "','" . $value->expiryDate . "')"
            );
        }
    }
    public function insert_manual_patient_rel($patientEmail, $deviceId,$patientId)
    {
        $data = [];
         $this->db->select("*");
         $this->db->where('deviceId', $deviceId);
         $this->db->where('patient_key', $patientEmail);
         $query = $this->db->get('patient_product_device_manual_patient');
        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        foreach ($data as $key => $value) {
            $datatosave['barcodesrno']=$value->barcodesrno;
            $datatosave['userId']=$patientId;
            $this->db->insert('patient_product_rel', $datatosave);
           
        }
    }

    public function insert_patient_request($userId, $deviceId)
    {
        $query1 = $this->db->query("select * from patient_treatement where patientId='" . $userId . "' ");

        //echo $this->db->last_query();

        if ($query1->num_rows() > 0) {
            $data = $query1->result();
            $query = $this->db->query("select * from patient_product_rel where userId='" . $userId . "'");

            foreach ($query->result() as $row) {
                $this->db->query("INSERT INTO product_request (productId,patientId,specialId,barcodesrno)

			VALUES (" . $row->id . "," . $userId . "," . $data[0]->specialId . ",'" . $row->barcodesrno . "')"
                );

                // $this->db->last_query();
            }
        }


      



      

        //print_r($data);
    }
    public function insert_manual_patient_request($userId, $deviceId)
    {
        $query1 = $this->db->query("select * from patient_treatement where patientId='" . $userId . "' ");

        //echo $this->db->last_query();

        if ($query1->num_rows() > 0) {
            $data = $query1->result();
            $query = $this->db->query("select * from patient_product_rel where userId='" . $userId . "'");

            foreach ($query->result() as $row) {
                $this->db->query("INSERT INTO product_request (productId,patientId,specialId,barcodesrno)

			VALUES (" . $row->id . "," . $userId . "," . $data[0]->specialId . ",'" . $row->barcodesrno . "')"
                );

                // $this->db->last_query();
            }
        }


      



      

        //print_r($data);
    }

    public function delete_temp_choose_special($deviceId)
    {
        $this->db->query("DELETE FROM temp_choose_special WHERE deviceId='" . $deviceId . "'");
    }

    public function delete_device_product($deviceId)
    {
        $this->db->query("DELETE FROM patient_product_device WHERE deviceId='" . $deviceId . "'");
    }
    public function delete_device_manual_patient_product($deviceId,$patientEmail)
    {
        $this->db->where('deviceId', $deviceId);
        $this->db->where('patient_key', $patientEmail);
        $this->db->delete('patient_product_device_manual_patient');
        // $this->db->query("DELETE FROM patient_product_device_manual_patient WHERE deviceId='" . $deviceId . "' and patient_key='".$patientEmail."');
    }

    public function update_patient_treatment($id, $deviceId)
    {
        $query = $this->db->query("UPDATE patient_treatement SET patientId = " . $id . " WHERE deviceId='" . $deviceId . "'");
    }

    public function change_password($newpassword, $email)
    {
        $newpassword = $this->hash_password($newpassword);

        $query = $this->db->query("UPDATE user_patient set password='" . $newpassword . "' WHERE email='" . $email . "' ");
    }

    public function update_userid($userid)
    {
        $query = $this->db->query("update patient_product_rel set userId=" . $userid . " where userId=" . $_SESSION["id"] . "");

        $query = $this->db->query("update patient_treatement set patientId=" . $userid . " where patientId=" . $_SESSION["id"] . "");
    }

    public function delete_product($userId, $deviceId, $productId, $type)
    {
        if ($type == 'new') {
            $this->db->query("delete from patient_product_rel where userId=" . $userId . " and id=" . $productId . "");

            return 1;
        } elseif ($type == 'processing') {
            $this->db->query("delete from patient_product_rel where userId=" . $userId . " and id=" . $productId . "");

            $this->db->query("delete from product_request where patientId='" . $userId . "' and productId=" . $productId . "");

            return 1;
        } elseif ($deviceId != null) {
            
         
            
            $this->db->query("delete from patient_product_device where deviceId='" . $deviceId . "' and id=" . $productId . "");

            return 1;
        }
    } 
    public function delete_manual_product($deviceId, $productId, $patientKey)
    {
          $this->db->where('deviceId',$deviceId);
          $this->db->where('patient_key',$patientKey);
          $this->db->where('barcodesrno',$productId);
          $this->db->delete('patient_product_device_manual_patient');
          return $this->db->affected_rows();
    }

    public function my_sheduale($id)
    {
        $query = $this->db->query("SELECT * from product_response where patientId=" . $id . "");

        $data = array();

        foreach ($query->result() as $row) {
            $row->rating = $this->getrating($row->barcodeSrNo);
            // get the parts icon
            $row->partIcon = "";

            if ($row->eyes=="1") {
                $row->partIcon = base_url('assets/bodyparts/eyes.png');
            } elseif ($row->forhead=="1") {
                $row->partIcon = base_url('assets/bodyparts/forhead.png');
            } elseif ($row->neck=="1") {
                $row->partIcon = base_url('assets/bodyparts/neck.png');
            } elseif ($row->allFace=="1") {
                $row->partIcon = base_url('assets/bodyparts/allFace.png');
            }

            $data[] = $row;
        }

        if (sizeof($data) > 0) {
            return $data;
        } else {
            return $data = array();
        }
    }
    public function fetchsingleprodut($bsno = "", $uid = "", $type = "")
    {
        $query = $this->db->query("SELECT * FROM `admin_product` where barcodeSrNo=" . $bsno . "");

        $data = array();

        foreach ($query->result() as $row) {
            $row->rating = $this->getrating($bsno);
            $temp = $this->getreviews($bsno, $uid, $type);
            // get the parts icon
            $row->partIcon = "";

            if ($row->eyes=="1") {
                $row->partIcon = base_url('assets/bodyparts/eyes.png');
            } elseif ($row->forhead=="1") {
                $row->partIcon = base_url('assets/bodyparts/forhead.png');
            } elseif ($row->neck=="1") {
                $row->partIcon = base_url('assets/bodyparts/neck.png');
            } elseif ($row->allFace=="1") {
                $row->partIcon = base_url('assets/bodyparts/allFace.png');
            }

            $row->ratingDetails = $temp['ratingDetails'];
            $row->userReview = $temp['userReview'];
            $row->hasEdited = $temp['hasEdited'];
            $row->allReviews = $temp['allReviews'];

            $data[] = $row;
        }
        return $data;
    }

    public function taketreatment_button($id, $date)
    {
        $checker = $this->db->query("select * from patient_sheduale_detail  where patientId=" . $id . " and days='" . date("Y-m-d") . "'")->num_rows();

        $taketreatment = $this->db->query("select * from patient_sheduale_detail  where patientId=" . $id . " and days='" . $date . "' and status=1")->num_rows();

        if ($checker == 0 && date("Y-m-d") == $date) {
            return 'yes';
        } elseif ($date > date("Y-m-d")) {
            return 'pending';
        } elseif ($taketreatment >= 1) {
            return 'complete';
        } else {
            return 'skip';
        }
    }

    public function checkpass($currentpassword, $id)
    {
        $query = $this->db->query("select password from user_patient where userId=" . $id . "");

        $hash = $query->result()[0]->password;

        if (password_verify($currentpassword, $hash)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function change_Pass($newpassword, $id)
    {
        $password = $this->hash_password($newpassword);

        $data = array(
            'password' => $password,
            'status'=> 1,
        );
         $this->db->where('userId', $id);
         $this->db->update('user_patient',$data);
         return $password;
    }


    // get specialist details
    public function specialist_detail($specialistId, $patientId)
    {
        // data is empty
        $data = [];

        $query = $this->db->query(
            "select sepcial_users.jobProfile,sepcial_users.userName,sepcial_users.age,sepcial_users.city,sepcial_users.proPicName,sepcial_users.countary,sepcial_users.email,sepcial_users.yearOfExperience,sepcial_users.phonePrivacy,sepcial_users.emailPrivacy,sepcial_users.gender,sepcial_users.phone,sepcial_users.aboutMe,( select count(specialist_product_rel.barcodesrno) from specialist_product_rel where specialist_product_rel.userId=" . $specialistId . " ) as productCount,( select count(DISTINCT patient_treatement.patientId) from patient_treatement where patient_treatement.specialId=" . $specialistId . ") as PatientCount from sepcial_users left join specialist_product_rel on sepcial_users.userId=specialist_product_rel.userId left join patient_treatement on sepcial_users.userId=patient_treatement.specialId where sepcial_users.userId=" . $specialistId . " limit 0,1"
        );
        foreach ($query->result() as $row) {
            $row->rating = $this->getratingforspecialist($specialistId);
            $temp = $this->getreviewsforspecialist($specialistId, $patientId);
            array_walk(
                $row,
                function (&$item) {
                    $item = strval($item);
                    $item = htmlentities($item);
                    $item = html_entity_decode($item);
                }
                );
            $row->ratingDetails = $temp['ratingDetails'];
            $row->userReview = $temp['userReview'];
            $row->hasEdited = $temp['hasEdited'];
            $row->allReviews = $temp['allReviews'];
             $row->noOfReviews = (String)$this->getreviewsCountforspecialist($specialistId);

            $data[] = $row;
        }


        return $data;
    }

    public function request_Again($id)
    {



            // $this->db->query("update notification set seen=1 where patientId=".$_SESSION['user_id']."");

        $query = $this->db->query(
            "SELECT * FROM patient_product_rel WHERE patient_product_rel.barcodesrno NOT IN (SELECT barcodesrno FROM product_request where patientId=" . $id . ") and patient_product_rel.userId=" . $id . ""
        );

        //echo $this->db->last_query(); die;

        $query1 = $this->db->query("select * from patient_treatement where patientId=" . $id . "");

        $specialId = $query1->result();

        // print_r( $specialId);die();

        $sepecialId = $specialId[0]->specialId;

        foreach ($query->result() as $row) {
            $query = $this->db->query(
                "INSERT INTO product_request (productId, patientId, specialId,barcodesrno)

			VALUES (" . $row->id . "," . $id . "," . $sepecialId . ",'" . $row->barcodesrno . "')"
            );
        }

        $checker = $this->db->query("select * from notification where patientId=" . $id . "")->num_rows();

        if ($checker == 0) {
            $this->db->query("INSERT INTO notification (patientId, specialistId) VALUES (" . $id . "," . $sepecialId . ")");
        }

        return $this->db->query("SELECT * from user_patient where userId='" . $id . "'")->result();
    }

    public function my_product_list_req($id)
    {
        $query = $this->db->query("select product_request.type,product_request.productStatus,patient_product_rel.id as productId,bar_code_detail.productName,bar_code_detail.brandName,bar_code_detail.img,bar_code_detail.barcodeSrNo from product_request inner join bar_code_detail on product_request.barcodesrno=bar_code_detail.barcodeSrNo inner join patient_product_rel on product_request.productId=patient_product_rel.id where patientId=" . $id . " and product_request.barcodesrno not in(select barcodeSrNo from product_response where patientId=" . $id . ")");

        /**/

        //echo $this->db->last_query();

        $data = array();

        foreach ($query->result() as $key => $value) {
            $value->rating = $this->getrating($value->barcodeSrNo);
            $temp = $this->getreviews($value->barcodeSrNo);

            $value->allReviews = $temp['allReviews'];

            $data[] = $value;
        }

        //print_r($data);

        return $data;
    }

    public function my_product_list_res($id)
    {
        $query = $this->db->query("select *,id as productId from product_response where patientId=" . $id . " and status=1");

        $data = array();

        foreach ($query->result() as $row) {
            $row->rating = $this->getrating($row->barcodeSrNo);
            $temp = $this->getreviews($row->barcodeSrNo);
            // get the parts icon
            $row->partIcon = "";

            if ($row->eyes=="1") {
            $row->partIcon = base_url('assets/bodyparts/eyes.png');
            } elseif ($row->forhead=="1") {
                $row->partIcon = base_url('assets/bodyparts/forhead.png');
            } elseif ($row->neck=="1") {
                $row->partIcon = base_url('assets/bodyparts/neck.png');
            } elseif ($row->allFace=="1") {
                $row->partIcon = base_url('assets/bodyparts/allFace.png');
            }
            $row->allReviews = $temp['allReviews'];
            $data[] = $row;
        }

        //print_r($data);

        return $data;
    }

    public function get_email($id)
    {
        $query = $this->db->query("select email from user_patient where userId=" . $id . "");

        $data = " ";

        return $data = $query->result();
    }

    public function insert_email_data($id, $subject, $message)
    {
        $query = $this->db->query(
            "INSERT INTO mail_records (userType, subject, message,userId)

			VALUES ('patient','" . $subject . "','" . $message . "'," . $id . ")"
        );

        $insert_id = $this->db->insert_id();

        $query = $this->db->query("select * from mail_records where id=" . $insert_id . "");

        return $query->result();
    }

    public function total_product($id)
    {
        $data1 = array();

        $data2 = array();

        $query1 = $this->db->query("SELECT productId,productName,brandName,img,productStatus from admin_request where patientId=" . $id . "");

        foreach ($query1->result() as $row) {
            $data2[] = $row;
        }

        $query = $this->db->query("select productId,productName,brandName,img,productStatus from admin_product");

        foreach ($query->result() as $row) {
            $data1[] = $row;
        }

        $finaldata = array_merge($data2, $data1);

        $tempArray = array();

        $finalArray = array();

        foreach ($finaldata as $key => $val) {
            if (!in_array($val->productId, $tempArray)) {
                $finalArray[] = $val;

                $tempArray[] = $val->productId;
            }
        }

        unset($tempArray);

        return sizeof($finalArray);
    }

   
    public function explore_new_product($id, $limit = "", $offset = "")
    {
        $data1 = array();
        $data2 = array();
        $tempArray = array();
        $finalArray = array();

        // in case of get counts 
        if ($limit == "") {
            $query1 = $this->db->query(
                "SELECT productStatus,barcodeId,barcodeSrno,productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media from admin_request where patientId=" . $id . ""
            );
            $query = $this->db->query(
                "select productStatus,barcodeId,barcodeSrno,  productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media from admin_product "
            );
        } else {
            $query1 = $this->db->query(
                "SELECT productStatus,barcodeId,barcodeSrno,productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media from admin_request where patientId=" . $id . "  limit " . $offset . ", " . $limit . ""
            );
            $query = $this->db->query(
                "select productStatus,barcodeId,barcodeSrno,  productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media from admin_product  order by productOrder desc limit " . $offset . ", " . $limit . ""
            );
        }

        foreach ($query1->result() as $row) {
            $row->rating = $this->getrating($row->barcodeSrno);

            // get the parts icon 
            // if ($row->eyes=="1") {
            //     $row->partIcon = base_url('assets/bodyparts/eyes.png');
            // } elseif ($row->forhead=="1") {
            //     $row->partIcon = base_url('assets/bodyparts/forhead.png');
            // } elseif ($row->neck=="1") {
            //     $row->partIcon = base_url('assets/bodyparts/neck.png');
            // } elseif ($row->allFace=="1") {
            //     $row->partIcon = base_url('assets/bodyparts/allFace.png');
            // }

            $data2[] = $row;
        }

        foreach ($query->result() as $row) {
            $row->rating = $this->getrating($row->barcodeSrno);
            // get the parts icon
           

            $data1[] = $row;
        }

        $finaldata = array_merge($data2, $data1);

        foreach ($finaldata as $key => $val) {
            if (!in_array($val->productId, $tempArray)) {
                $finalArray[] = $val;

                $tempArray[] = $val->productId;
            }
        }
        
        unset($tempArray);


       if (isset($finalArray)) {
          foreach ($finalArray as $key=>$row) {
            $row->partIcon = "";

              if ($row->eyes=="1") {
                $row->partIcon = base_url('assets/bodyparts/eyes.png');
            } elseif ($row->forhead=="1") {
                $row->partIcon = base_url('assets/bodyparts/forhead.png');
            } elseif ($row->neck=="1") {
                $row->partIcon = base_url('assets/bodyparts/neck.png');
            } elseif ($row->allFace=="1") {
                $row->partIcon = base_url('assets/bodyparts/allFace.png');
            }
            $finalArray[$key]=$row;
          }
       }
        // in case of get counts 
        if ($limit == "") {
            return (String)sizeof($finalArray);
        } else {
            return $finalArray;
        }
    }


    public function my_array_unique($array, $keep_key_assoc = false)
    {
        $duplicate_keys = array();

        $tmp = array();

        foreach ($array as $key => $val) {

        // convert objects to arrays, in_array() does not support objects

            if (is_object($val)) {
                $val = (array)$val;
            }

            if (!in_array($val, $tmp)) {
                $tmp[] = $val;
            } else {
                $duplicate_keys[] = $key;
            }
        }

        foreach ($duplicate_keys as $key) {
            unset($array[$key]);
        }

        return $keep_key_assoc ? $array : array_values($array);
    }

    public function my_product_list_to_req($id)
    {
        $data = array();

        $query = $this->db->query(
            "select patient_product_rel.type,patient_product_rel.productStatus,patient_product_rel.id as productId,bar_code_detail.productName,bar_code_detail.brandName,bar_code_detail.img,bar_code_detail.barcodeSrNo from patient_product_rel INNER JOIN bar_code_detail on patient_product_rel.barcodesrno=bar_code_detail.barcodeSrNo where patient_product_rel.barcodesrno not in(select barcodeSrNo from product_response where patientId=" . $id . ") and patient_product_rel.barcodesrno not in(select barcodeSrNo from product_request where patientId=" . $id . ") and userId=" . $id . "");

        // echo $this->db->last_query();

        foreach ($query->result() as $row) {
            $row->rating = $this->getrating($row->barcodeSrNo);
            // get the parts icon
            // if ($row->eyes=="1") {
            //     $row->partIcon = base_url('assets/bodyparts/eyes.png');
            // } elseif ($row->forhead=="1") {
            //     $row->partIcon = base_url('assets/bodyparts/forhead.png');
            // } elseif ($row->neck=="1") {
            //     $row->partIcon = base_url('assets/bodyparts/neck.png');
            // } elseif ($row->allFace=="1") {
            //     $row->partIcon = base_url('assets/bodyparts/allFace.png');
            // }

            $temp = $this->getreviews($row->barcodeSrNo);
            $row->allReviews = $temp['allReviews'];
            $data[] = $row;
        }

        return $data;
    }

    public function request_To_Admin($id, $productId)
    {
        $query = $this->db->query(
            "select barcodeId,barcodeSrNo, productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media from admin_product where productId=" . $productId . ""
        );

        $product = $query->result();

        //print_r($product);

        $this->db->query(
            "INSERT INTO admin_request (barcodeId,barcodeSrno,productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media,patientId)

			VALUES ('" . $product[0]->barcodeId . "','" . $product[0]->barcodeSrNo . "','" . $product[0]->productId . "','" . $product[0]->productName . "', '" . $product[0]->brandName . "','" . $product[0]->img . "','" . $product[0]->eyes . "','" . $product[0]->forhead . "','" . $product[0]->neck . "','" . $product[0]->allFace . "','" . $product[0]->everyday . "','" . $product[0]->onceAweek . "','" . $product[0]->twiceAweek . "','" . $product[0]->onceAmonth . "','" . $product[0]->instruction . "','" . $product[0]->am . "','" . $product[0]->pm . "','" . $product[0]->minute . "','" . $product[0]->step . "','" . $product[0]->numberOfstep . "','" . $product[0]->media . "'," . $id . ")"
        );

        $query = $this->db->query(
            "select barcodeId,barcodeSrno,productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media from admin_request where productId=" . $productId . " "
        );

        return $query->result();
    }

    public function total_product_search($searchproduct, $id, $eye, $forhead, $neck, $allface)
    {
        $data1 = array();

        $data2 = array();

        $where = "";

        $ww = array();

        if ($eye == 1) {
            $wheres = "eyes=1";

            $where = "eyes=1 or";

            $ww['eyes'] = 1;
        } else {
            $where = "";
        }

        if ($forhead == 1) {
            $where = $where . " or forhead=1 ";

            $ww['forhead'] = 1;
        } else {
            $where = $where . "  ";
        }

        if ($neck == 1) {
            $where = $where . " or neck=1";

            $ww['neck'] = 1;
        } else {
            $where = $where . "";
        }

        if ($allface == 1) {
            $ww['allface'] = 1;

            $where = $where . " or allface=1";
        } else {
            $where = $where . " ";
        }

        $where = "";

        if (!empty($ww)) {
            foreach ($ww as $kk => $vv) {
                $where .= "$kk = $vv or ";
            }

            $where = "and (" . rtrim($where, ' or ') . ")";
        }

        $query1 = $this->db->query(
            "SELECT productId,productName,brandName,img,productStatus from admin_request where (productName LIKE '%" . $searchproduct . "%' or brandName LIKE '%" . $searchproduct . "%') $where and patientId=" . $id . ""
        );

        $this->db->last_query();

        foreach ($query1->result() as $row) {
            $data2[] = $row;
        }

        $query = $this->db->query(
            "select  productId,productName,brandName,img,productStatus from admin_product where (productName LIKE '%" . $searchproduct . "%' or brandName LIKE '%" . $searchproduct . "%') $where "
        );

        $this->db->last_query();

        foreach ($query->result() as $row) {
            $data1[] = $row;
        }

        $finaldata = array_merge($data2, $data1);

        $tempArray = array();

        $finalArray = array();

        foreach ($finaldata as $key => $val) {
            if (!in_array($val->productId, $tempArray)) {
                $finalArray[] = $val;

                $tempArray[] = $val->productId;
            }
        }

        unset($tempArray);

        return sizeof($finalArray);
    }

    public function search_product($searchproduct, $limit, $offset, $id, $eye, $forhead, $neck, $allface)
    {
        $data1 = array();

        $data2 = array();

        $where = "";

        $ww = array();

        if ($eye == 1) {
            $wheres = "eyes=1";

            $where = "eyes=1 or";

            $ww['eyes'] = 1;
        } else {
            $where = "";
        }

        if ($forhead == 1) {
            $where = $where . " or forhead=1 ";

            $ww['forhead'] = 1;
        } else {
            $where = $where . "  ";
        }

        if ($neck == 1) {
            $where = $where . " or neck=1";

            $ww['neck'] = 1;
        } else {
            $where = $where . "";
        }

        if ($allface == 1) {
            $ww['allface'] = 1;

            $where = $where . " or allface=1";
        } else {
            $where = $where . " ";
        }

        $where = "";

        if (!empty($ww)) {
            foreach ($ww as $kk => $vv) {
                $where .= "$kk = $vv or ";
            }

            $where = "and (" . rtrim($where, ' or ') . ")";
        }



        //	echo(rtrim($where,' or '));die;

        $query1 = $this->db->query(
            "SELECT productStatus,barcodeId,barcodeSrno,productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media

        from admin_request  where (productName LIKE '%" . $searchproduct . "%' or brandName LIKE '%" . $searchproduct . "%') $where and patientId=" . $id . "  limit " . $offset . ", " . $limit . ""
        );

        //echo $this->db->last_query();

        foreach ($query1->result() as $row) {
            $data2[] = $row;
        }

        $query = $this->db->query(
            "select productStatus,barcodeId,barcodeSrno,productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media from admin_product  where (productName LIKE '%" . $searchproduct . "%' or brandName LIKE '%" . $searchproduct . "%') $where  limit " . $offset . ", " . $limit . ""
        );

        //echo $this->db->last_query();

        foreach ($query->result() as $row) {
            $data1[] = $row;
        }

        $finaldata = array_merge($data2, $data1);

        $tempArray = array();

        $finalArray = array();

        foreach ($finaldata as $key => $val) {
            if (!in_array($val->productId, $tempArray)) {
                $finalArray[] = $val;

                $tempArray[] = $val->productId;
            }
        }

        unset($tempArray);

        //print_r($finalArray);

        return $finalArray;
    }

    public function gallery($id, $type)
    {
        $data['images'] = array();

        $query = $this->db->query(
            "select media,type from gallery where specialistId='" . $id . "' and " . $type . "=1 and " . $type . "Status=0 and type=1"
        );

        foreach ($query->result() as $row) {
            $data['images'][] = $row;
        }

        $data['videos'] = array();

        $query1 = $this->db->query(
            "select media,type from gallery where specialistId='" . $id . "' and " . $type . "=1 and " . $type . "Status=0 and type=0"
        );

        foreach ($query1->result() as $row) {
            $data['videos'][] = $row;
        }

        return $data;
    }

    public function check_items($deviceId, $userId)
    {
        if ($userId != null) {
            $query = $this->db->query("select * from patient_product_rel where userId='" . $userId . "'");

            //echo $this->db->last_query();

            if ($query->num_rows() > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            $query = $this->db->query("select * from patient_product_device where deviceId='" . $deviceId . "'");

            //echo $this->db->last_query();

            if ($query->num_rows() > 0) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    public function day_Request($id, $date)
    {
        
        
        
        $data=[];
        list($y, $m, $d) = explode('-', $date);
        $unixdate=mktime(0, 0, 0, $m, $d, $y);
          $data = array(
          'patientId'=>$id,
          'days'=>$date,
          'status'=>1,
          'unixdate'=>$unixdate
          );    

          $this->db->insert('patient_sheduale_detail',$data);
        // $query="INSERT INTO patient_sheduale_detail ( patientId, days,status,unixdate) VALUES ('" . $id . "','" . $date . "',1,$unixdate.')";
       
        $sql="SELECT * from patient_sheduale_detail where patientId='" . $id . "' and days ='" . $date. "'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $data=$query->result()[0];

        }
           

        

        return $data;
    }

    public function edit_profile($id, $age, $city, $link, $countary, $phone, $gender, $name)
    {
        
        
         $data = array(
            'age' => $age,
            'userName'=> $name,
            'city'=>$city,
            'proPicName'=> $link,
            'countary'=> $countary,
            'phone'=> $phone,
            'gender'=> $gender,
          
        );
         $this->db->where('userId', $id);
         $this->db->update('user_patient',$data);

       


        
        // $this->db->query(
        //     "update user_patient set age='" . $age . "',userName='" . $name . "',city='" . $city . "',proPicName='" . $link . "',countary='" . $countary . "',phone='" . $phone . "',gender='" . $gender . "' where userId=" . $id . ""
        // );

        $query = $this->db->query("select * from user_patient where userId=" . $id . "");

        return $query->result();
    }

    public function check_phone($phone)
    {
        if ($phone=="") {
           return 0;
        }
        $query = $this->db->query("SELECT * from user_patient where phone='" . $phone . "'");

        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function checkemail($email)
    {
        $query = $this->db->query("select * from user_patient where email='" . $email . "'");

        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function createdate($id)
    {
        $data = array();
        $sid=0;
        $query = $this->db->query("select createdate from product_response  where patientId='" . $id . "' and status=1");

        $data = $query->result();

        return $data;
    }

    public function inserted_Date($id)
    {
        $data = array();

        $query = $this->db->query("select days from patient_sheduale_detail where patientId='" . $id . "'");

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function insert_remain_date($result, $id)
    {
        foreach ($result as $key => $value) {
            list($y, $m, $d) = explode('-', $value);
              $unixdate=mktime(0, 0, 0, $m, $d, $y);
            $this->db->query("INSERT INTO patient_sheduale_detail ( patientId, days,status,unixdate) VALUES ('" . $id . "','" . $value . "',0,$unixdate)");
        }
    }

    public function red($id)
    {
        $data = array();
       
        

        $query = $this->db->query("select days,unixdate from patient_sheduale_detail where patientId='" . $id . "' and status=0");

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function green($id)
    {
        $data = array();

        $query = $this->db->query("select days,unixdate from patient_sheduale_detail where patientId='" . $id . "' and status=1");

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function lastinsertday($id)
    {
        $query = $this->db->query("SELECT max(unixdate) as day FROM `patient_sheduale_detail` WHERE patientId='" . $id . "'");

        return $query->result();
    }

    public function product_Exists($deviceId)
    {
        $query = $this->db->query("select * from patient_product_device where deviceId='" . $deviceId . "'")->num_rows();

        if ($query > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function get_Specialist_Token($id)
    {
        $specialist = $this->db->query("select * from patient_treatement where patientId='" . $id . "'")->result()[0]->specialId;

        $query = $this->db->query("select * from user_token where userId='" . $specialist . "' and userType='specialist'");



        //print_r($query->result());

        return $query->result();
    }

    public function distroy_token($userId, $token, $deviceId="")
    {
        $this->db->query("delete from user_token where userId='" . $userId . "' and token='" . $token . "' and userType='patient'");
    }

    public function add_To_MyList($barcodeSrNo, $deviceId, $userId)
    {
        if ($userId != null) {
            $barcodeSrNo = explode(",", $barcodeSrNo);

            $notexist = array();

            $exist_bar = array();

            foreach ($barcodeSrNo as $value) {
                $query = $this->db->query("select barcodesrno from patient_product_rel where barcodesrno='" . $value . "' and userId='" . $userId . "'");

                if (sizeof($query->result()) > 0) {
                    $exist_bar[] = $query->result()[0]->barcodesrno;

                    //print_r($exist_bar);
                }
            }



            foreach ($barcodeSrNo as $innerVal) {
                if (in_array($innerVal, $exist_bar)) {
                } else {
                    $notexist[] = $innerVal;
                }
            }

            foreach ($notexist as $value) {
                
              
                
                $this->db->query("INSERT INTO patient_product_rel (barcodesrno, userId)

			VALUES ('" . $value . "','" . $userId . "')");
            }
        } else {
            $barcodeSrNo = explode(",", $barcodeSrNo);

            $notexist = array();

            $exist_bar = array();

            foreach ($barcodeSrNo as $value) {
                $query = $this->db->query("select barcodesrno from patient_product_device where barcodesrno='" . $value . "' and deviceId='" . $deviceId . "'");

                if (sizeof($query->result()) > 0) {
                    $exist_bar[] = $query->result()[0]->barcodesrno;

                    //print_r($exist_bar);
                }
            }

            foreach ($barcodeSrNo as $innerVal) {
                if (in_array($innerVal, $exist_bar)) {
                } else {
                    $notexist[] = $innerVal;
                }
            }

            foreach ($notexist as $value) {
                $this->db->query("INSERT INTO patient_product_device (barcodesrno, deviceId)

			VALUES ('" . $value . "','" . $deviceId . "')");
            }
        }
    }

    // c
    public function add_To_MyList_manual_patient($barcodeSrNo, $deviceId,$patientKey)
    {
            $barcodeSrNo = explode(",", $barcodeSrNo);
             $this->db->where('deviceId',$deviceId);
             $this->db->where('patient_key',$patientKey);
             $this->db->delete('patient_product_device_manual_patient');
            foreach ($barcodeSrNo as $value) {
                  
                 
                  
                $data = array(
                    'barcodesrno'=>$value,
                    'deviceId'=>$deviceId,
                    'patient_key'=>$patientKey,
                ); 
               $this->db->insert('patient_product_device_manual_patient',$data);
                
            }
    }

    public function getencpassword($userId = "", $receivehash)
    {
        $hash=null;
        $query = $this->db->query("select password from user_patient where userId=" . $userId . "");

        if ($query->result()) {
             $hash = $query->result()[0]->password;
        }
       

        if ($receivehash == $hash) {
            return true;
        } else {
            return false;
        }
    }

    public function chat_img($link)
    {
        $this->db->query("INSERT INTO chat_image (img)

			VALUES ('" . $link . "')");

        $insert_id = $this->db->insert_id();

        $query = $this->db->query("SELECT * FROM chat_image where id='" . $insert_id . "'");

        return $query->result();
    }


    // function to save the fcm notification of both patient and specialists

    public function savefcmnotidication($data)
    {
        $this->db->insert('fcm_notifications', $data);

        return $this->db->insert_id();
    }



    // function to get all fcm notification on the basis of patient and specialist

    public function getfcmnotifications($entity = '', $uid, $action = "")
    {
          
        // $query=  $this->db->query("SELECT * FROM fcm_notifications where entity='".$entity."' and userId = ".$uid);

        $data = [];
        $this->db->order_by('id', 'desc');
        $query = $this->db->get_where('fcm_notifications', array('entity' => $entity, 'userId' => $uid));
        foreach ($query->result() as $key => $value) {
            $value->timelapsed = $this->time_elapsed_string($value->utctime);
            $temp = unserialize($value->data);
            if ($temp == false) {
                $value->data = [];
            } else {
                array_walk(
                    $temp,
                    function (&$item) {
                        $item = strval($item);
                        $item = htmlentities($item);
                        $item = html_entity_decode($item);
                    }
                );

                $value->data = $temp;
            }
            // $value->data=;
            $data[] = $value;
        }
        return $data;

        // return $query->result();
    }

    
    // get the average product rating according to bar code no
    public function getrating($bsno = "")
    {
        $query = $this->db->query("SELECT  ROUND(avg(rating),1) as average FROM `reviews` where barcodeSrNo='$bsno' and status='approved'");

        if ($query->result()[0]->average == null) {
            return "";
        } else {
            return $query->result()[0]->average;
        }
    }
    // get the average product rating according to bar code no
    public function getreviewsCountforspecialist($sid = "")
    {
        $query = $this->db->query("SELECT * FROM `specialist_rating` where userId = $sid and feedback !=''");

        if ($query->num_rows() !== 0) {
            return $query->num_rows();
        } else {
            return 0;
        }
    }
  


    // get average specialist rating
    public function getratingforspecialist($specialId = "")
    {
        $query = $this->db->query("SELECT  ROUND(avg(rating),1) as average FROM `specialist_rating` where userId='$specialId' and status='approved'");

        if ($query->result()[0]->average == null) {
            return "";
        } else {
            return $query->result()[0]->average;
        }
    }

    // get the reviews of the products

    public function getreviews($bsno = "", $uid = "", $type = "")
    {
        $data = [];
        $ratingarray = array();
        $userarray = array();
        $temparray = array("1" => "0", "2" => "0", "3" => "0", "4" => "0", "5" => "0","total"=>"0");


        $data['ratingDetails'] = $temparray;
        $data['userReview'] = [];
        $data['allReviews'] = [];
        $data['hasEdited'] = "0";
       
        $this->db->order_by('modifiedtime', 'desc');
        $query = $this->db->get_where('reviews', array('barcodeSrNo' => $bsno, 'status' => 'approved'));

        if ($query->result() == null) {
            return $data;
        } else {
            foreach ($query->result() as $key => $value) {
                array_push($ratingarray, $value->rating);
                
                $value->date = date("Y-m-d", strtotime($value->modifiedtime));
                $value->time = date("h:i:a", strtotime($value->modifiedtime));
                $value->timelapsed = $this->time_elapsed_string($value->modifiedtime);
                $temp = $this->getusername($value->userId, $value->type);
        
                
                $value->userName = $temp->userName;
                $value->proPicName = $temp->proPicName;
                $data['allReviews'][$key] = $value;

                if ($value->type == $type&&$value->userId==$uid) {
                    array_push($userarray, $value->userId);
                    $data['userReview'][0] = $value;
                }
            }
            $temp = array_count_values($ratingarray);

            //   $test=array_count_values($ratingarray);
            foreach ($temp as $key => $value) {
                $temparray[$key] = "$value";
            }

            $temparray['total'] = (String)array_sum($temparray);



            $data['ratingDetails'] = $temparray;
            $data['hasEdited'] = (String)in_array($uid, $userarray);

            return $data;
        }
    }

    // time elapsed 
    public function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) {
            $string = array_slice($string, 0, 1);
        }
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    // get reviews for the specialist
    public function getreviewsforspecialist($specialistId, $patientId)
    {
        $data = [];
        $ratingarray = array();
        $userarray = array();
        $data['ratingDetails'] = [];
        $data['userReview'] = [];
        $data['hasEdited'] = "0";
        $data['allReviews'] = [];
        $userreview = [];

        $temparray =  array("1" => "0", "2" => "0", "3" => "0", "4" => "0", "5" => "0","total"=>"0");
         $data['ratingDetails'] = $temparray;
        $this->db->order_by('modifiedtime', 'desc');
        $query = $this->db->get_where('specialist_rating', array('userId' => $specialistId, 'status' => 'approved'));

        //   $sql="SELECT * FROM `specialist_rating` where userId = '$specialistId' and status='approved'";
        // $query=  $this->db->query($sql);

        if ($query->result() == null) {
            return $data;
        } else {
            foreach ($query->result() as $key => $value) {
                array_push($ratingarray, $value->rating);
                
                $value->date = date("Y-m-d", strtotime($value->modifiedtime));
                $value->time = date("h:i:a", strtotime($value->modifiedtime));
                $value->userdetails = $this->getusername($value->givenby, 'patient');
                $tempData = $this->getusername($value->givenby, 'patient');
                $value->userName = $tempData->userName;
                $value->proPicName = $tempData->proPicName;
                $value->timelapsed = $this->time_elapsed_string($value->modifiedtime);
                if ($value->givenby == $patientId) {
                    array_push($userarray, $value->givenby);
                   $data['userReview'][0] = $value;
                }
                $data['allReviews'][$key] = $value;
            }
            $temp = array_count_values($ratingarray);

            //   $test=array_count_values($ratingarray);
            foreach ($temp as $key => $value) {
                $temparray[$key] = "$value";
            }

            $temparray['total'] = (String)array_sum($temparray);

            $data['ratingDetails'] = $temparray;
            
            // $data['userReview'] = $userreview;
            $data['hasEdited'] = (String)in_array($patientId, $userarray);

            return $data;
        }
    }


    // get username according to the the userid
    public function getusername($uid = "", $type = "")
    {
        $data=[];
     
        if ($type == 'patient') {
            $sql = "SELECT userName ,proPicName FROM `user_patient` where userId = '$uid' ";
        // code...
        } elseif ($type == 'specialist') {
            $sql = "SELECT userName ,proPicName FROM `sepcial_users` where userId = '$uid' ";

            // code...
        }
        $query = $this->db->query($sql);

        if ($query->result() == null) {
            return $data;
        } else {
            return $query->result()[0];
        }
    }



    
    // set notification as read as per its id
    public function setnotificationread($id) {
        return $this->db->query("UPDATE `fcm_notifications` SET `readStatus`=1 WHERE id=" . $id);
    }

    // save reviews in reviews table
    public function savereviews($data) {
        $this->db->insert('reviews', $data);

        return $this->db->insert_id();
    }

    // save speciliast reviews in specialist_rating table
    public function specialist_rating($data) {
        $this->db->insert('specialist_rating', $data);

        return $this->db->insert_id();
    }
    

    // get all the counts for dashboard
    public function getallcounts($patientId = "") {
        
        
        

        $data = array(
            'myproductList' => $this->myproductcount($patientId),
                // "allMessagesCount"=>array('unreadMessages'=>"0","unreadNotifications"=>"0","total"=>"0"),
            "allmessageCount"=> $this->allmessageCount($patientId),
             "sendmenotification" => (String)$this->send_me_notification($patientId),
         //   "notificationCount" => $this->getNotificationcount($patientId),
            "products" => $this->getPopularProducts($patientId),
            "userInfo" => $this->getusermetainfo($patientId),
            "treatmentProduct" => (String)sizeof($this->get_mytreatment($patientId)),
            "exploreNew" => $this->explore_new_product($patientId),
             "images"=>$this->get_images($patientId)
        );
        return $data;
    }


    // get the profile complete and usertype 
    public function getusermetainfo($patientId) {
        $this->db->select("loginStrategy,iscompleted");
        $this->db->where('userId', $patientId);
        $query = $this->db->get('user_patient');
        return $query->result()[0];
     

    }

      // check the permision of sending the notification
    public function send_me_notification($patientId) {
        
        $this->db->select("*");
        $this->db->where('entityId',$patientId);
        $this->db->where('entity', 'patient');
        $query = $this->db->get('notifyme_setting');
         if ($query->num_rows() > 0) {
             return $query->result()[0]->status;
         } else {
             return true;
         }
    }
    // get all counts of treatment 
    public function treatment_count($id)
    {
          $this->db->distinct();
        $this->db->where('patientId', $id);
        $query = $this->db->get('patient_sheduale_detail');
        return (String)$query->num_rows();
        // return 21;
    }

    // get notifications count 
	public function getNotificationcount($patientId) {
		$data=['read'=>"0","unread"=>"0","total"=>"0"];
		$sql="SELECT count(id) as num,readStatus FROM `fcm_notifications` where userId = ".$patientId." and entity='patient' group by readStatus order by readStatus desc";
 		$query = $this->db->query($sql);

        foreach ($query->result() as $row) {
			if ($row->readStatus=="1") {
				$key="read";
				# code...
			}else{
				$key="unread";
			}
            $data[$key] = $row->num;
		}
		$data["total"]=(String)($data["read"]+$data["unread"]);
        return $data;

	}
    // all message count
	public function allmessageCount($patientId="0") {
	 	$this->db->select("count");

        $this->db->where('entity_id', $patientId);
        $this->db->where('type', 'patient');
		$query = $this->db->get('inboxmessage_count');
	
		
		if ($query->num_rows()>0) {
			return $query->result()[0]->count;

		}else{
			return "0";
		}
        

	}
    // all message count
	public function change_notification_setting($entity_id,$entity_type,$status) {
         $data = array('entityId' => $entity_id,
        'entity'=>$entity_type,
        'status ' =>$status);
      
	 	$this->db->select("*");

        $this->db->where('entityId', $entity_id);
        $this->db->where('entity', $entity_type);
		$query = $this->db->get('notifyme_setting');
		 if ($query->num_rows() > 0) {
           
            $this->db->where('entityId', $entity_id);
            $this->db->where('entity', $entity_type);
             $this->db->update('notifyme_setting', $data);
              $this->db->where('entityId', $entity_id);
            $this->db->where('entity', $entity_type);
             $this->db->from('notifyme_setting');
            return $this->db->get()->result();
        } else {
            $this->db->insert('notifyme_setting', $data);
               $this->db->where('entityId', $entity_id);
            $this->db->where('entity', $entity_type);
             $this->db->from('notifyme_setting');
            return $this->db->get()->result();
          
        }
        

	}

    // get my product count
    public function myproductcount($patientId = "") {
        $id = $patientId;
        $data1 = $this->Patient_model->my_product_list_req($id);
        // print_r($data1);
        $data2 = $this->Patient_model->my_product_list_res($id);
        // print_r($data2);die;
        $data3 = $this->Patient_model->my_product_list_to_req($id);
        //print_r($data3);
        $final = array_merge($data1, $data2);

        $finaldata = array_merge($final, $data3);

        return (String)sizeof($finaldata);
    }


    // get  all the top selling products
  
    // get  all the top selling products
    public function get_images($patientId = "") {
        $data=[];
        $this->db->from('image_report');
        $this->db->where('patientId ', $patientId);
         $this->db->order_by('id', 'desc');
        $images = $this->db->get();
        foreach ($images->result() as $key => $value) {
           $data[]['imageUrl']=base_url($value->imageUrl);
        }
        return $data;
    }
    // get all filter listing (not using in app)
    public function getlistingoffilters() {
        $dataoffilter = array(
            '0' => [
                "key" => "Area of use",
                "keyId" => "0",
                "returnKey" => "areaOfUse",
                "value" => [
                    [

                        "returnValue" => "eyes",
                        "thisName" => "Eyes",
                        "selected" => "false",
                        "key" => "Area of use",
                        "keyId" => "0"
                    ], [

                        "returnValue" => "forhead",
                        "thisName" => "Forhead",
                        "selected" => "false",
                        "key" => "Area of use",
                        "keyId" => "0"
                    ], [

                        "returnValue" => "neck",
                        "thisName" => "Neck",
                        "selected" => "false",
                        "key" => "Area of use",
                        "keyId" => "0"
                    ], [

                        "returnValue" => "allFace",
                        "thisName" => "All Face",
                        "selected" => "false",
                        "key" => "Area of use",
                        "keyId" => "0"
                    ]
                ]
            ],
            '1' => [
                "key" => "Usage Time",
                "keyId" => "1",
                "returnKey" => "usageTime",
                "value" => [
                    [
                        "returnValue" => "everyday",
                        "thisName" => "Everyday",
                        "selected" => "false",
                        "key" => "Usage Time",
                        "keyId" => "1",
                    ], [
                        "returnValue" => "onceAweek",
                        "thisName" => "Once a Week",
                        "selected" => "false",
                        "key" => "Usage Time",
                        "keyId" => "1",
                    ], [
                        "returnValue" => "twiceAweek",
                        "thisName" => "twice a Week",
                        "selected" => "false",
                        "key" => "Usage Time",
                        "keyId" => "1",
                    ], [
                        "returnValue" => "onceAmonth",
                        "thisName" => "Once a Month",
                        "selected" => "false",
                        "key" => "Usage Time",
                        "keyId" => "1",
                    ]
                ]
            ]
        );
        return $dataoffilter;
    }

    // update count of all message 
    public function updateinboxmessage($patientId) {

        $data = array('type' => 'patient',
        'entity_id'=>$patientId,
        'count' =>1);
        $this->db->where('type','patient');
        $this->db->where('entity_id', $patientId);
        $q = $this->db->get('inboxmessage_count');

        if ($q->num_rows() > 0) {
            
            $previous=$q->result()[0]->count;
            $new = ++$previous;
            $this->db->where('type', 'patient');
            $this->db->where('entity_id', $patientId);
            $data['count']=$new;
            $this->db->update('inboxmessage_count', $data);
        } else {
            
            $this->db->insert('inboxmessage_count', $data);
        }
    }

    // update the patient remaining data after the first time social login

    public function complete_profile($patientId,$data=[]) {
        $this->db->where('userId', $patientId);
        $this->db->update('user_patient', $data);
         $this->db->where('userId', $patientId);
        $result=$this->db->get('user_patient')->result();
        array_walk( $result[0], function (&$item) {
            $item = strval($item);
            $item = htmlentities($item);
            $item = html_entity_decode($item);
        });
        return $result;


    }


    public function check_email($email,$id)
    {
        $query = $this->db->query(
            "select * from user_patient where email = '$email' and userId!=$id"
        );
        return $query->num_rows();
    }
    // save speciliast reviews in specialist_rating table
    public function save_image($data) {
        $this->db->insert('image_report', $data);
        return $this->db->insert_id();
    }

    // get  all the popular  products on the basis of top selling
    public function getPopularProducts($patientId = "")  {
        
        $products = []; 
        // in case of manaual recommended popular products
        if ( $this->getCalculationType('patient_popular_product')=='manually') {
            $this->db->select('*');
            $this->db->from('manual_show_products');
            $this->db->join('admin_product', 'admin_product.barcodeSrNo  = manual_show_products.barcodesrno ', 'inner');
            // $this->db->where('patient_product_rel.userId', $patientId);
            $this->db->limit(5);
        }else {
            $this->db->select("admin_product.barcodeId,   admin_product.barcodeSrNo,admin_product.productName,admin_product.brandName,admin_product.img,admin_product.eyes,admin_product.forhead,admin_product.neck,admin_product.productId, patient_product_rel.barcodesrno, patient_product_rel.barcodesrno, count(patient_product_rel.barcodesrno) as noofpatient");
            $this->db->from('patient_product_rel');
            $this->db->join('admin_product', 'admin_product.barcodeSrNo=patient_product_rel.barcodesrno', 'inner');
            // $this->db->where('patient_product_rel.userId', $patientId);
            $this->db->limit(6);
            $this->db->group_by("admin_product.barcodeId,admin_product.barcodeSrNo,admin_product.productName,admin_product.brandName,admin_product.img,admin_product.eyes,admin_product.forhead,admin_product.neck,admin_product.productId, patient_product_rel.barcodesrno");
            $this->db->order_by('noofpatient', 'desc');
        }
        $products = $this->db->get()->result();
        foreach ($products as $key => $value) {
             $value->rating = $this->getrating($value->barcodeSrNo);
            $products[$key]=$value;
        }
        return $products;
    }
    
     public function getCalculationType($key="") {
          $default='automatic';
        $this->db->where('varibale_name', $key);
        $result=$this->db->get('setting_variable')->result();
        if (sizeof($result)>0) {
        $default=$result[0]->variable_value;
           
        }
        return $default;

    }


    // mytreatment
    public function get_mytreatment($patientId="") {
        $data=[];
        $flag=false;
        // 
          if ( $this->getCalculationType('specilist_mytreatment')!='all') {
        $flag=true;
        } 
        $this->db->distinct();
        $this->db->select('image_report.*,patient_sheduale_detail.*');
        $this->db->from('patient_treatement');
        $this->db->join('patient_sheduale_detail', 'patient_sheduale_detail.patientId=patient_treatement.patientId', 'inner');
        $this->db->join('image_report', 'image_report.treatmentId=patient_sheduale_detail.id', 'left');
        $this->db->where('patient_sheduale_detail.patientId', $patientId);
        $this->db->order_by('days','desc');


        // fetch only completed treatment 
       if ($flag) {
        $this->db->where('status', 1);
       }

        $result= $this->db->get()->result();
    

        foreach ($result as $key => $value) {
            $value->timelapsed = "";

            if ($value->status=="0") {
                $value->imageUrl=base_url('/assets/patientreportimages/mised.png');
                $value->message=$this->message()[18]->message;
                $value->timelapsed =  $this->time_elapsed_string($value->days);
                $value->status='missed';
                $dateTimestamp1 =  strtotime($value->days); 
                $dateTimestamp2 =  strtotime(date("Y/m/d")); 
                // in case of upcoming 
                if ( $dateTimestamp1> $dateTimestamp2 ) {
                  
                    $value->timelapsed =   str_replace("ago","remaining",$this->time_elapsed_string($value->days));
                     $value->status='upcoming';
                    $value->message=$this->message()[19]->message;
                    $value->imageUrl=base_url('/assets/patientreportimages/upcoming.png');
                }elseif ($dateTimestamp1== $dateTimestamp2) {
                     $value->status='Today';
                    $value->message=$this->message()[19]->message;
                     $value->timelapsed = "today";
                    $value->imageUrl=base_url('/assets/patientreportimages/today.png');
                }
            }else {
             $value->timelapsed = $this->time_elapsed_string($value->timestamp);
                $value->imageUrl=($value->imageUrl=="")?base_url('/assets/patientreportimages/completed.png'):base_url($value->imageUrl);
                $value->message=$this->message()[17]->message;
                $value->status='Treatment Completed';

            }

            array_walk($value, function (&$item) {
                $item = strval($item);
                $item = htmlentities($item);
                $item = html_entity_decode($item);
            });

            $result[$key]=$value;
         
        
        }
        
         return $result;

    }

//    save patient data wirh active status 0
// 
public function savepatient($data) {
    
        $data = array(
            'userName' => $data->userName,
            'age' => $data->dob,
            'city' => '',
            'proPicName' => isset($data->image)?$data->image:"",
            'countary'   => '',
            'email' => $data->email,
            'password'   =>'',
            'gender' => '',
            'phone' => $data->phone,
            'type' => '',
            'token' => '',
            'fcmUser' => 'p'.$data->phone,
            'loginStrategy' => 'local-oauth',
            'profileId' => '',
            'iscompleted ' => '0'
        );

        $this->db->insert('user_patient', $data);
        return $this->db->insert_id();
}

	function _group_by($array, $key) {
		$return = array();
		foreach($array as $val) {
			$return[$val[$key]][] = $val;
		}
		return $return;
	}
	//facial booking calendar
	public function facial_booking_calendar($sId,$timeZone)
	{
		$getData = array();
		$getData1 = array();
		$tmpArr  = array();
		$mainArr = array();
		$fetchData = $this->db->select(array('id','specialistId','bookedDate','bookedTime','bookingStatus'))->get_where('specialist_book_facial',array('patientId' =>$sId));
		
		if ($fetchData->num_rows() > 0) {
			$k = 0; $statusReady = 0;
			foreach($fetchData->result() as $row)
			{
				$todayDate    = gmdate('Y-m-d');
				$fbookedDate  	= $row->bookedDate;
				$fbookingStatus  = $row->bookingStatus;
				//red: incomplete, green: all complete, yellow: today
				// date into dateTimestamp 
				$currentDate = strtotime($todayDate); 
				$bookedDate  = strtotime($fbookedDate); 
				  
				// Compare the timestamp date  
				if ($currentDate == $bookedDate || $bookedDate > $currentDate) {
					$tmpArr[$k] = array('id'=>$row->id,'bDate'=>$fbookedDate,'bStatus'=>$fbookingStatus); 	
				}
				elseif ($bookedDate < $currentDate) {
					$tmpArr[$k] = array('id'=>$row->id,'bDate'=>$fbookedDate,'bStatus'=>$fbookingStatus); 
					
				}
				else{
					$color = 'green'; 
				}
				$k++;
			}//foreach row
			/* $mainArr = $getData;
			print_r($mainArr); */
			
			$getPrevious = $this->_group_by($tmpArr, 'bDate');
			if($getPrevious){
				$k = 0;
				$wholeStatus 	= 0;
				
				foreach($getPrevious as $booking)
				{
					
					$completeCount  = 0;
					//echo '<br>';
					$getCount = count($booking);
					foreach($booking as $abc){
						if($abc['bStatus'] == 1){
							$completeCount++;
						}
						else{
							$completeCount--;
						}
					}
					//print_R($booking[0]['bDate'] .' < '. $todayDate);
					//print_R('>>'.$completeCount.' , '.$getCount); 
					if(($todayDate == $booking[0]['bDate']) || ($booking[0]['bDate'] > $todayDate))
					{
						if($completeCount == $getCount){
							$color = 'green';
						}
						else{
							$color = 'yellow';
						}
					}//active date
					
					else if($booking[0]['bDate'] < $todayDate){
						if($completeCount == $getCount){
							$color = 'green';
						}
						else{
							$color = 'red';
						}
					}//previous date
					else{
						$color = 'yellow';
					}//upcoming
					
						$getData1[$k]['date']  = $booking[0]['bDate']; 
						$getData1[$k]['color'] = $color;
					$k++;
				}//foreach main date			
			}
		}
		
		$data = $getData1;
		return $data;
	}
	
	
		//get list of facials booked by me for me
	public function get_my_booked_facials($sId,$selectedDate){
		$data 	= '';
		$getData = array();
		if($selectedDate == 0){
			$checkConditions = array('patientId' =>$sId);
		}
		else{
			$checkConditions = array('patientId' =>$sId, 'bookedDate'=>$selectedDate);
		}
		
		$fetchdata = $this->db->select(array('id','specialistId','bookedDate','bookedTime','patientNotes','privateNotes','bookingStatus','user_patient.userName as specialistName','specialist_book_facial.patientId'))->join('sepcial_users as user_patient', 'user_patient.userId = specialist_book_facial.specialistId','Left')->get_where('specialist_book_facial',$checkConditions);
		
		if ($fetchdata->num_rows() > 0) {
			$i = 0;
			foreach ($fetchdata->result() as $row)
			{
				$getData[$i] = $row;
				$getTime = $row->bookedTime;
				//$getTime = date("H:i", $getTime);//our time zone
				$getTime = gmdate("H:i", $getTime);
				$getData[$i]->exactTime = $getTime;
				$i++;
			}
		}
		$data = $getData;
		return $data;
	}
	
}

?>