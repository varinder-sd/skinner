<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**

 * User_model class.

 *

 * @extends CI_Model

 */
class Special_model extends CI_Model
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
        $this->load->model('Patient_model');
        $this->load->database();
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

    public function message()
    {
        $data = array();

        $query = $this->db->query("select * from alert_message");

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    private function verify_password_hash($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function show_gallery($search, $userId, $limit, $offset, $type)
    {
        $data = array();

        $query = $this->db->query(
            "select id,media," . $search . "Status as status from gallery where specialistId=" . $userId . "  and type=" . $type . " limit " . $offset . ", " . $limit . " "
        );

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function show_gallery_count($search, $userId, $type)
    {
        $query = $this->db->query("select * from gallery where specialistId=" . $userId . " and " . $search . "=1 and type=" . $type . "");

        return $query->num_rows();
    }

    public function insert_email_data($id, $subject, $message)
    {
        $query = $this->db->query(
            "INSERT INTO mail_records (userType, subject, message,userId)

			VALUES ('specialist','" . $subject . "','" . $message . "'," . $id . ")"
        );

        $insert_id = $this->db->insert_id();

        $query = $this->db->query("select * from mail_records where id=" . $insert_id . "");

        return $query->result();
    }

    public function get_email($id)
    {
        $query = $this->db->query("select email from sepcial_users where userId=" . $id . "");

        $data = " ";

        return $data = $query->result();
    }

    public function delete_gallery($userId, $search, $id)
    {
        $this->db->query("update gallery set " . $search . "=0 where specialistId=" . $userId . " and id=" . $id . "");

        return 1;
    }

    public function member_Id($userId, $memberId)
    {
        $this->db->query("update sepcial_users set memberId=" . $memberId . " where userId=" . $userId . "");

        $query = $this->db->query("select * from sepcial_users where userId=" . $userId . "");

        return $query->result();
    }

    public function show_profile($id)
    {
        $query = $this->db->query("select * from sepcial_users where userId=" . $id . "");

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        return $data;
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

    public function create_user(
                            $userName = "",
                            $age = "",
                            $city = "",
                            $proPicName = "",
                            $countary = "",
                            $yearOfExperience = "",
                            $password = "",
                            $gender = "",
                            $phone = "",
                            $email = "",
                            $aboutme = "",
                            $token = "",
                            $type = "",
                            $deviceId = "",
                            $emailPrivacy=1,
                            $phonePrivacy=1,
                            $jobProfile = "",
                            $loginStrategy = "local-oauth",
                            $profileId = ""
    ) {
        $fcmUser = "";
        $userId = "";
        $fcmUser = 's' . $profileId;
        $isCompleted=true;
        switch ($loginStrategy) {
               
                // in case of google authentication
            case 'google-oauth':

                $isCompleted=false;

                $query = $this->db->get_where('sepcial_users', array('profileId' => $profileId,'loginStrategy'=>'google-oauth'));
                if ($query->num_rows() > 0) {
                    $userId = $query->result()[0]->userId;
                }


                break;

            case 'facebook-oauth':

                $isCompleted=false;

                //  $where = "(profileId=$profileId or email = $email	)";

                $query = $this->db->get_where('sepcial_users', array('profileId' => $profileId,'loginStrategy'=>'facebook-oauth'));
              

                if ($query->num_rows() > 0) {
                    $userId = $query->result()[0]->userId;
                }

                break;

            default:
                $fcmUser = "s" . $phone;
                break;
        }
            

        // user id in case user is already signup 

        $query = $this->db->get_where('sepcial_users', array('email' => $email));
        if ($query->num_rows() > 0) {
            $userId = $query->result()[0]->userId;
        }

        $data = array(
            'userName'   => $userName,
            'age'   => $age,
            'city' => $city,
            'proPicName' => $proPicName,
            'countary'   => $countary,
            'email'      => $email,
            'yearOfExperience' => $yearOfExperience,
            'password' => $this->hash_password($password),
            'gender' => $gender,
            'aboutMe' => $aboutme,
            'phone' => $phone,
            'type' => $type,
            'jobProfile' => $jobProfile,
            'token'   => $token,
            'fcmUser' => $fcmUser,
            'loginStrategy' => $loginStrategy,
            'profileId' => $profileId,
            'emailPrivacy' => $emailPrivacy,
            'phonePrivacy' => $phonePrivacy,
            'iscompleted' => $isCompleted
        );



        // only create user if user is not in database
        if ($userId == "") {
            $this->db->insert('sepcial_users', $data);
            $insert_id = $this->db->insert_id();
        } else {
            $insert_id = $userId;
        }

        $insert = $this->db->query("select * from user_token where userId='" . $insert_id . "'and token='" . $token . "' and userType='specialist'")->result();

        if (sizeof($insert) > 0) {
        } else {
            $this->db->query(
                "INSERT INTO user_token (userId, token, deviceId,type,userType)

			VALUES ('" . $insert_id . "','" . $token . "','" . $deviceId . "','" . $type . "','specialist')"
            );
        }

        $query = $this->db->query("select * from sepcial_users where userId=" . $insert_id . "");

        return  $query->result();
    }

    public function edit_profile($id=0, $age=0, $city=0, $link='', $countary='', $phone=0, $gender=0, $name='', $aboutme='', $yearOfExperience='',$emailPrivacy=0,$phonePrivacy=0,$jobProfile=0)
    {
        $data = array(
            'age' => $age,
            'userName'=> $name,
            'city'=>$city,
            'proPicName'=> $link,
            'countary'=> $countary,
            'phone'=> $phone,
            'gender'=> $gender,
            'aboutMe'=> $aboutme,
            'yearOfExperience'=> $yearOfExperience,
            'emailPrivacy'=> $emailPrivacy,
            'phonePrivacy'=> $phonePrivacy,
            'jobProfile'=>$jobProfile
        );
         $this->db->where('userId', $id);
         $this->db->update('sepcial_users',$data);

        // $this->db->query(
        //     "update sepcial_users set age=" . $age . ",userName='" . $name . "',city='" . $city . "',proPicName='" . $link . "',countary='" . $countary . "',phone='" . $phone . "',gender='" . $gender . "',aboutMe='" . $aboutme . "',yearOfExperience='" . $yearOfExperience . "',phonePrivacy=$phonePrivacy,emailPrivacy=$emailPrivacy where userId=" . $id . ""
        // );

        $query = $this->db->query("select * from sepcial_users where userId=" . $id . "");

        return $query->result();
    }

    public function get_user($user_id, $token, $type, $deviceId)
    {
        $data=[];
        $this->db->from('sepcial_users');
        $this->db->where('userId', $user_id);
        $insert = $this->db->query("select * from user_token where userId='" . $user_id . "'and token='" . $token . "' and userType='specialist'")->result();

        if (sizeof($insert) > 0) {
            $this->db->query("update sepcial_users set token='" . $token . "', type='" . $type . "' where userId=" . $user_id . "");
        } else {
            $this->db->query(
                "INSERT INTO user_token (userId, token, deviceId,type,userType)
			VALUES ('" . $user_id . "','" . $token . "','" . $deviceId . "','" . $type . "','specialist')"
            );
            $this->db->query("update sepcial_users set token='" . $token . "', type='" . $type . "' where userId=" . $user_id . "");
        }

        //      
        $data=$this->db->get()->row();
        array_walk( $data, function (&$item) {
            $item = strval($item);
            $item = htmlentities($item);
            $item = html_entity_decode($item);
        });
        return $data;
    }

    public function get_user_id_from_username($username)
    {
        $this->db->select('userId');

        $this->db->from('sepcial_users');

        $this->db->where('email', $username);

        return $this->db->get()->row('userId');
    }

    public function resolve_user_login($username, $password)
    {
        $this->db->select('password');
        $this->db->from('sepcial_users');
        $this->db->where('loginStrategy', 'local-oauth');
        $this->db->where('email', $username);
        $hash = $this->db->get()->row('password');
        return $this->verify_password_hash($password, $hash);
    }

    public function check_email_exist($email){
        $data = false ;
        $query = $this->db->get_where('sepcial_users', array('email' => $email,'loginStrategy'=>'local-oauth'));
        if ($query->num_rows() > 0) {
          return true;
        }else {
              return false;
        }

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

    public function reject_barcode($barcodesrno)
    {

        //return $this->db->query("select * from bar_code_detail where barcodesrno=".$barcodesrno."");
        $query = $this->db->query("select * from bar_code_detail where barcodeSrNo='" . $barcodesrno . "' and status=0");
        return $query->num_rows();
    }

    public function check_product($deviceId, $userId, $barcodesrno)
    {
        if ($userId != null) {
            $query = $this->db->query("select * from specialist_product_rel where userId='" . $userId . "' and barcodesrno='" . $barcodesrno . "' ");
            //echo $this->db->last_query();
            if ($query->num_rows() > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            $query = $this->db->query("select * from special_product_device where deviceId='" . $deviceId . "' and barcodesrno='" . $barcodesrno . "' ");
            //echo $this->db->last_query();
            if ($query->num_rows() > 0) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    public function temp_Id()
    {
        $query = $this->db->query("select max(id) as id from temp_id");
        $id = $query->result();
        $id = $id[0]->id + 1;
        //print_r($id);
        $query = $this->db->query("insert into temp_id (id) values (" . $id . ")");
        $insert_id = $this->db->insert_id();
        $query = $this->db->query("select max(id) as id from temp_id");
        $id = $query->result();
        $id = $id[0]->id;
        $_SESSION["id"] = $id;
    }

    public function add_product($barcodesrno, $userId)
    {
        $this->db->query("INSERT INTO specialist_product_rel (barcodesrno, userId)

			VALUES ('" . $barcodesrno . "','" . $userId . "')");

        return 1;
    }

    public function add_product_device($barcodesrno, $deviceId)
    {
        $this->db->query("INSERT INTO special_product_device (barcodesrno, deviceId)

			VALUES ('" . $barcodesrno . "','" . $deviceId . "')");

        return 1;
    }

    public function view_product($id, $deviceId = "",$filterparam=array())
    {
        $data = array();
    
        $key = array_search('1', $filterparam);

        if ($id != null) {

        $sql1= "select product.eyes,product.forhead,product.neck,product.allFace,product.barcodeSrNo, specialist_product_rel.*,bar_code_detail.* from specialist_product_rel inner join bar_code_detail on specialist_product_rel.barcodesrno=bar_code_detail.barcodeSrNo left join admin_product as product on product.barcodeSrNo=specialist_product_rel.barcodesrno where specialist_product_rel.userId='" . $id . "' and specialist_product_rel.barcodesrno not in (SELECT barcodeSrNo from product_detail where specialId='" . $id . "')";
         // in case of filter
            if ($key) {
                $sql1.=' and product.'.$key.' = 1';
            }
            $sql1.=' order by productOrder desc';


        

        // $query1=$this->db->query("select specialist_product_rel.*,bar_code_detail.* from specialist_product_rel inner join bar_code_detail on specialist_product_rel.barcodesrno=bar_code_detail.barcodeSrNo  where specialist_product_rel.userId='".$id."' and specialist_product_rel.barcodesrno not in (SELECT barcodeSrNo from product_detail where specialId='".$id."')");

            $query1 = $this->db->query($sql1);

            //echo $this->db->last_query();

            // $query2=$this->db->query("select * from product_detail where specialId='".$id."'");
            $sql2= "select product.eyes,product.forhead,product.neck,product.allFace,product_detail.* from product_detail left join admin_product as product on product.barcodeSrNo = product_detail.barcodeSrNo where specialId='" . $id . "'";
            
            // in case of filter
            if ($key) {
                $sql2.=' and product_detail.'.$key.' = 1';
            }
            $sql2.=' order by productOrder desc';


            $query2 = $this->db->query($sql2);
           

            foreach ($query1->result() as $row) {
                // get the parts icon
                $row->partIcon = "";
                array_walk(
                    $row,
                    function (&$item) {
                        $item = strval($item);
                        $item = htmlentities($item);
                        $item = html_entity_decode($item);
                    }
                );

                $row->rating = $this->getrating($row->barcodeSrNo);
                if ($row->eyes=="1") {
                $row->partIcon = base_url('assets/bodyparts/eyes.png');
                }elseif ($row->forhead=="1") {
                $row->partIcon = base_url('assets/bodyparts/forhead.png');
                }elseif ($row->neck=="1") {
                $row->partIcon = base_url('assets/bodyparts/neck.png');
                }elseif ($row->allFace=="1") {
                $row->partIcon = base_url('assets/bodyparts/allFace.png');
                }
               

                $data[] = $row;
            }

            foreach ($query2->result() as $row) {
                $row->rating = $this->getrating($row->barcodeSrNo);
                // get the parts icon
                $row->partIcon = "";

                if ($row->eyes=="1") {
                $row->partIcon = base_url('assets/bodyparts/eyes.png');
                }elseif ($row->forhead=="1") {
                $row->partIcon = base_url('assets/bodyparts/forhead.png');
                }elseif ($row->neck=="1") {
                $row->partIcon = base_url('assets/bodyparts/neck.png');
                }elseif ($row->allFace=="1") {
                $row->partIcon = base_url('assets/bodyparts/allFace.png');
                }
                array_walk(
                    $row,
                    function (&$item) {
                        $item = strval($item);

                        $item = htmlentities($item);

                        $item = html_entity_decode($item);
                    }
                );

                $data[] = $row;
            }

            return $data;
        } else {

            // $query=$this->db->query("select special_product_device.*,bar_code_detail.* from special_product_device left join bar_code_detail on special_product_device.barcodesrno=bar_code_detail.barcodeSrNo where special_product_device.deviceId='".$deviceId."'");
            $sql= "select product.eyes,product.barcodeSrNo,product.forhead,product.neck,product.allFace, special_product_device.*,bar_code_detail.* from special_product_device left join bar_code_detail on special_product_device.barcodesrno=bar_code_detail.barcodeSrNo left join admin_product as product on product.barcodeSrNo=special_product_device.barcodesrno  where special_product_device.deviceId='" . $deviceId . "'";            

            // in case of filter 
             if ($key) {
             $sql.=' and product.'.$key.' = 1';
            }
            $sql.=' order by productOrder desc';
            
                  
            $query = $this->db->query($sql);

            foreach ($query->result() as $row) {
                $row->rating = $this->getrating($row->barcodeSrNo);
                // get the parts icon
                $row->partIcon = "";

                if ($row->eyes=="1") {
                $row->partIcon = base_url('assets/bodyparts/eyes.png');
                }elseif ($row->forhead=="1") {
                $row->partIcon = base_url('assets/bodyparts/forhead.png');
                }elseif ($row->neck=="1") {
                $row->partIcon = base_url('assets/bodyparts/neck.png');
                }elseif ($row->allFace=="1") {
                $row->partIcon = base_url('assets/bodyparts/allFace.png');
                }
                array_walk(
                    $row,
                    function (&$item) {
                        $item = strval($item);

                        $item = htmlentities($item);

                        $item = html_entity_decode($item);
                    }
                );

                $data[] = $row;
            }

            return $data;
        }

        //echo $this->db->last_query();
    }

    public function update_userid($userid)
    {
        $query = $this->db->query("update specialist_product_rel set userId=" . $userid . " where userId=" . $_SESSION["id"] . "");
    }

    public function check_avilable($data)
    {

        //print_r(sizeof($data));

        for ($i = 0; $i < sizeof($data); $i++) {



            //print_r($data[$i]->barcodeId);

            $query = $this->db->query("select * from product_detail where barcodeId=" . $data[$i]->barcodeId . "");

            if ($query->num_rows() > 0) {

                //echo"hii";

                $data[$i]->check = 1;

                // array_push($data,'check'=>1);
            }
        }

        return $data;
    }

    public function view_instruction($specialistId, $barcodeSoNo)
    {
        $query = $this->db->query("select * from product_detail where barcodeSrNo='" . $barcodeSoNo . "' and specialId='" . $specialistId . "'");

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            $query1 = $this->db->query("select * from admin_product where barcodeSrNo='" . $barcodeSoNo . "'");

            return $query1->result();
        }
    }

    public function create_shedual(
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
    ) {
        $query1 = $this->db->query("select * from product_detail where specialId='" . $specialId . "' and barcodeSrNo='" . $barcodeSrNo . "'");

        if ($query1->num_rows() > 0) {
            $this->db->query(
                "update product_detail set productName='" . $productName . "',brandName='" . $brandName . "',img='" . $img . "',eyes='" . $eyes .  "',allFace='" . $allFace ."',forhead='" . $forhead . "',neck='" . $neck . "',everyday='" . $everyday . "',onceAweek='" . $onceAweek . "',twiceAweek='" . $twiceAweek . "',onceAmonth='" . $onceAmonth . "',instruction='" . $instruction . "',am='" . $am . "',pm='" . $pm . "',minute='" . $minute . "',step='" . $step . "',numberOfStep='" . $numberOfStep . "',media='" . $media . "' where specialId='" . $specialId . "' and barcodeSrNo='" . $barcodeSrNo . "'"
            );
        } else {
            $this->db->query(
                "INSERT INTO product_detail (specialId, barcodeId, barcodeSrNo,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfStep,media)

			VALUES (" . $specialId . "," . $barcodeId . ", '" . $barcodeSrNo . "','" . $productName . "','" . $brandName . "','" . $img . "'," . $eyes . "," . $forhead . "," . $neck . "," . $allFace . "," . $everyday . "," . $onceAweek . "," . $twiceAweek . "," . $onceAmonth . ",'" . $instruction . "'," . $am . "," . $pm . "," . $minute . "," . $step . "," . $numberOfStep . ",'" . $media . "')"
            );

            $this->db->query("delete from specialist_product_rel where userId='" . $specialId . "' and barcodesrno='" . $barcodeSrNo . "'");
        }

        return 1;
    }

    public function check_phone($phone)
    {
        $query = $this->db->query("SELECT * from sepcial_users where phone='" . $phone . "'");

        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function total_patient_product($userId, $patientId)
    {
        $query = $this->db->query("select * from product_response where patientId=" . $patientId . " and specialistId=" . $userId . " and status=1");

        return $query->num_rows();
    }

    public function patient_product($userId, $patientId, $limit, $offset)
    {
        $data = array();

        $query = $this->db->query(
            "select * from product_response where patientId=" . $patientId . " and specialistId=" . $userId . " and status=1 limit " . $offset . "," . $limit . ""
        );

        //echo $this->db->last_query();

        foreach ($query->result() as $row) {
            $row->partIcon = "";
                
         
                $row->rating = $this->getrating($row->barcodeSrNo);
                if ($row->eyes=="1") {
                    $row->partIcon = base_url('assets/bodyparts/eyes.png');
                } elseif ($row->forhead=="1") {
                    $row->partIcon = base_url('assets/bodyparts/forhead.png');
                } elseif ($row->neck=="1") {
                    $row->partIcon = base_url('assets/bodyparts/neck.png');
                } elseif ($row->allFace=="1") {
                    $row->partIcon = base_url('assets/bodyparts/allFace.png');
                }
                array_walk(
                    $row,
                    function (&$item) {
                        $item = strval($item);
                        $item = htmlentities($item);
                        $item = html_entity_decode($item);
                    }
                ); 
            $data[] = $row;
        }

        return $data;
    }

    public function patient_Concern($patientId, $userId)
    {
        $data = array();

        $query = $this->db->query("SELECT concernType from patient_treatement where patientId='" . $patientId . "' and specialId='" . $userId . "'");

        //echo $this->db->last_query();

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function show_request( $id)
    {
        $data = array();

        // $query = $this->db->query(
        //     "SELECT notification.*,user_patient.* FROM notification INNER JOIN user_patient ON notification.patientId = user_patient.userId where notification.specialistId=" . $id . " limit " . $offset . "," . $limit . " "
        // );
        $query = $this->db->query(
            "SELECT notification.*,user_patient.* FROM notification INNER JOIN user_patient ON notification.patientId = user_patient.userId where notification.specialistId=".$id
        );

        //echo $this->db->last_query();

        foreach ($query->result() as $row) {
            $data[] = $row;
        }



        return $data;
    }

    public function total_row($id)
    {
        $query = $this->db->query(
            "SELECT notification.*,user_patient.* FROM notification INNER JOIN user_patient ON notification.patientId = user_patient.userId where notification.specialistId=" . $id . ""
        );

        return $query->num_rows();
    }

    public function delete_Schduel_Product($barcodeSrNo, $patientId, $status)
    {
        if ($status == 'approve') {
            $this->db->query("delete from product_response where patientId='" . $patientId . "' and barcodeSrNo='" . $barcodeSrNo . "'");
        } else {
            $this->db->query("delete from product_request where patientId='" . $patientId . "' and barcodesrno='" . $barcodeSrNo . "'");
        }

        return 1;
    }

    public function check_product_response($patientId, $barcodesrno)
    {
        $query = $this->db->query("select * from product_request where patientId='" . $patientId . "' and barcodesrno='" . $barcodesrno . "' ")->num_rows();

        $query1 = $this->db->query("select * from product_response where patientId='" . $patientId . "' and barcodeSrNo='" . $barcodesrno . "' ")->num_rows();

        $checker = $query + $query1;

        //echo $this->db->last_query();

        if ($checker > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function add_product_response($barcodesrno, $userId, $patientId)
    {
        $this->db->query(
            "INSERT INTO product_request ( patientId, specialId,barcodesrno)

			VALUES (" . $patientId . "," . $userId . ",'" . $barcodesrno . "')"
        );
    }

    public function create_Shedual_For_User($patientId, $specialId)
    {
        $data = array();
        $finaldata = array();

        $query = $this->db->query(
            "select admin_product.*,bar_code_detail.*,product_request.productStatus from product_request inner join bar_code_detail on product_request.barcodesrno=bar_code_detail.barcodeSrNo  
            inner join admin_product on admin_product.barcodeSrNo=bar_code_detail.barcodeSrNo
            where product_request.patientId=" . $patientId . " and product_request.specialId=" . $specialId . " and product_request.barcodesrno NOT IN(SELECT barcodeSrNo from product_response where patientId=" . $patientId . ")"
        );

        foreach ($query->result() as $row) {
             $row->partIcon = "";
                
         
                $row->rating = $this->getrating($row->barcodeSrNo);
                if ($row->eyes=="1") {
                    $row->partIcon = base_url('assets/bodyparts/eyes.png');
                } elseif ($row->forhead=="1") {
                    $row->partIcon = base_url('assets/bodyparts/forhead.png');
                } elseif ($row->neck=="1") {
                    $row->partIcon = base_url('assets/bodyparts/neck.png');
                } elseif ($row->allFace=="1") {
                    $row->partIcon = base_url('assets/bodyparts/allFace.png');
                }
                array_walk(
                    $row,
                    function (&$item) {
                        $item = strval($item);
                        $item = htmlentities($item);
                        $item = html_entity_decode($item);
                    }
                ); 

            $data[] = $row;
        }



        //print_r($data);die;

        $data1 = array();

        $query1 = $this->db->query("SELECT * from product_response where patientId='" . $patientId . "' ");

        foreach ($query1->result() as $row) {
             $row->partIcon = "";
               
         
                $row->rating = $this->getrating($row->barcodeSrNo);
                if ($row->eyes=="1") {
                    $row->partIcon = base_url('assets/bodyparts/eyes.png');
                } elseif ($row->forhead=="1") {
                    $row->partIcon = base_url('assets/bodyparts/forhead.png');
                } elseif ($row->neck=="1") {
                    $row->partIcon = base_url('assets/bodyparts/neck.png');
                } elseif ($row->allFace=="1") {
                    $row->partIcon = base_url('assets/bodyparts/allFace.png');
                }
                array_walk(
                    $row,
                    function (&$item) {
                        $item = strval($item);
                        $item = htmlentities($item);
                        $item = html_entity_decode($item);
                    }
                );

            $data1[] = $row;
        }


        // return array_merge($data, $data1);
        $finaldata=array_merge($data, $data1);
        foreach ($finaldata as $key => $value) {
           $finaldata[$key]->productStatus=$this->checkifpreviousApproved($value->barcodeSrNo,$specialId,$patientId);
        }
        return $finaldata;
    }

    // check if previous approved 
    public function checkifpreviousApproved($barcodeSrNo,$sid,$patientId) {
          $this->db->distinct();
          $this->db->where('barcodeSrNo', $barcodeSrNo);
          $this->db->where('specialistId', $sid);
           $this->db->where('patientId', $patientId);
          $query = $this->db->get('product_response');
          if ($query->num_rows()>0) {
             return "approve";
          }else{
                return "processing";
          }
          
    }

      public function fetchdataifpreviousApproved($barcodeSrNo,$sid) {
          $this->db->distinct();
          $this->db->where('barcodeSrNo', $barcodeSrNo);
          $this->db->where('specialistId', $sid);
          $query = $this->db->get('product_response');
          return $query->result()[0];
          
    }


  

    

    public function delete_product($userId, $deviceId, $productId)
    {
        if ($userId != null) {
            $checker = $this->db->query("select * from specialist_product_rel where productId='" . $productId . "' and userId='" . $userId . "'");

            if ($checker->num_rows() > 0) {
                $this->db->query("delete from specialist_product_rel where userId=" . $userId . " and productId=" . $productId . "");

                return 1;
            } else {
                $this->db->query("delete from product_detail where specialId=" . $userId . " and productId=" . $productId . "");

                return 1;
            }
        } else {
            $this->db->query("delete from special_product_device where deviceId='" . $deviceId . "' and productId=" . $productId . "");

            return 1;
        }
    }

    public function send_response($userId, $patientId,$barcodeSrNo)
    {

        // magic 
        $products=explode(',',$barcodeSrNo);
        foreach (array_unique($products) as $key => $value) {
             $this->db->where('barcodeSrNo', $value);
             $this->db->where('specialistId', $userId);
             $query = $this->db->get('product_response');
             if (!$this->checkifexist($value,$userId, $patientId)) {

                $data=(Array)$query->result()[0];
                $data['patientId']=$patientId;
                $data['id']=null;
                $data['createdate']=date("Y-m-d");
                $this->db->insert('product_response', $data);
             }
          
        }



        // 
        $this->db->query("update product_response set status=1 where patientId=" . $patientId . "");

        $this->db->query("delete from product_request where specialId=" . $userId . " and patientId=" . $patientId . "");

        $this->db->query("delete from notification where specialistId=" . $userId . " and patientId=" . $patientId . "");

        return 1;
    }


       public function checkifexist($barcodeSrNo,$sid,$pid) {
          $this->db->distinct();
          $this->db->where('barcodeSrNo', $barcodeSrNo);
          $this->db->where('specialistId', $sid);
          $this->db->where('patientId', $pid);
          $query = $this->db->get('product_response');
          
       
       
          
          if ($query->num_rows()>0) {
             return true;
          }else{
                return false;
          }
          
    }
    public function checkpass($currentpassword, $id)
    {
        $query = $this->db->query("select password from sepcial_users where userId=" . $id . "");

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

        $query = $this->db->query("update sepcial_users set password='" . $password . "' where userId=" . $id . "");
    }

    public function insert_specialist_rel($userId, $deviceId)
    {
        $query = $this->db->query("select * from special_product_device where deviceId='" . $deviceId . "'");

        $data = array();

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        foreach ($data as $key => $value) {
            $this->db->query(
                "INSERT INTO specialist_product_rel (barcodesrno, userId)

			VALUES ('" . $value->barcodesrno . "','" . $userId . "')"
            );
        }

        //echo $this->db->last_query();
    }

    public function delete_product_device($deviceId)
    {
        $this->db->query("DELETE FROM special_product_device WHERE deviceId='" . $deviceId . "'");
    }

    public function upload_gallery($userId, $link, $eyes, $forhead, $neck, $allFace, $status, $type)
    {
        $this->db->query(
            "INSERT INTO gallery (specialistId,media,type,forhead,eyes,neck,allFace,forheadStatus,eyesStatus,neckStatus,allFaceStatus)

			VALUES ('" . $userId . "','" . $link . "','" . $type . "','" . $forhead . "','" . $eyes . "','" . $neck . "','" . $allFace . "','" . $status . "','" . $status . "','" . $status . "','" . $status . "')"
        );

        return 1;
    }

    public function public_private_button($userId, $type, $id, $status)
    {
        $query = $this->db->query("UPDATE gallery set " . $type . "Status='" . $status . "' WHERE specialistId='" . $userId . "' and id='" . $id . "' ");

        return 1;
    }

    public function checkemail($email)
    {
        $query = $this->db->query("select * from sepcial_users where email='" . $email . "'");

        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function change_password($newpassword, $email)
    {
        echo $newpassword = $this->hash_password($newpassword);
        die();

        $query = $this->db->query("UPDATE sepcial_users set password='" . $newpassword . "' WHERE email='" . $email . "' ");

        //echo $this->db->last_query();
    }

    public function check_items($deviceId, $userId)
    {
        if ($userId != null) {
            $query = $this->db->query("select * from specialist_product_rel where userId='" . $userId . "'");

            //echo $this->db->last_query();

            if ($query->num_rows() > 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            $query = $this->db->query("select * from special_product_device where deviceId='" . $deviceId . "'");

            //echo $this->db->last_query();

            if ($query->num_rows() > 0) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    public function add_To_Response($patientId, $barcodesrno, $specialistId)
    {
        $data = array();

        $query2 = $this->db->query("select * from product_response where barcodeSrNo='" . $barcodesrno . "' and patientId	='" . $patientId . "'");

        if (sizeof($query2->result()) > 0) {
            return $data['res'] = $query2->result();
        }

        $query1 = $this->db->query("select * from product_detail where barcodeSrNo='" . $barcodesrno . "' and specialId='" . $specialistId . "'");

        if (sizeof($query1->result()) > 0) {
            return $data['res'] = $query1->result();
        }

        $query3 = $this->db->query("select * from admin_product where barcodeSrNo='" . $barcodesrno . "' ");

        if (sizeof($query3->result()) > 0) {
            return $data['res'] = $query3->result();
        }

        $query4 = $this->db->query("select * from bar_code_detail where barcodeSrNo='" . $barcodesrno . "'");

        if (sizeof($query4->result()) > 0) {
            return $data['res'] = $query4->result();
        }
    }

    public function patient_Profile($patientId)
    {
        return $this->db->query("select * from user_patient where userId='" . $patientId . "'")->result();
    }

    public function set_Response(
        $patientId,
        $createdate,
        $barcodeSrNo,
        $productName,
        $brandName,
        $img,
        $eyes,
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
        $specialistId
    ) {
        $data = array("specialId"=>$specialistId,
            "barcodeSrNo"=>$barcodeSrNo,
            "productName"=>$productName, 
            "brandName"=> $brandName,
            "img"=>$img ,
            "eyes"=>$eyes,
            "forhead"=>$forhead,
            "neck"=>$neck ,
            "everyday"=>$everyday,
            "onceAweek"=>$onceAweek,
            "twiceAweek"=>$twiceAweek,
            "onceAmonth"=> $onceAmonth,
            "instruction"=> $instruction,
            "am"=>$am ,
            "pm"=>$pm ,
            "minute"=>$minute,
            "step"=>$step ,
            "numberOfStep"=>$numberOfStep,
            "media"=>$media 
        );
        $query = $this->db->query("select * from product_response where patientId='" . $patientId . "' and barcodeSrNo='" . $barcodeSrNo . "'");
        $dataforproduct = array(
            "productName" => $productName ,
            "brandName" => $brandName ,
            "img" => $img ,
            "eyes"=> $eyes ,
            "forhead"=>$forhead,
            "neck"=>$neck ,
            "everyday"=>$everyday ,
            "onceAweek"=>$onceAweek ,
            "twiceAweek"=>$twiceAweek ,
            "onceAmonth"=>$onceAmonth ,
            "instruction"=>$instruction ,
            "am"=>$am ,
            "pm"=>$pm ,
            "minute"=>$minute ,
            "step"=>$step ,
            "numberOfStep"=>$numberOfStep ,
            "media"=>$media,
            "patientId"=>$patientId,
            "specialistId"=>$specialistId,
            "createdate"=>$createdate,
            "barcodeSrNo"=>$barcodeSrNo
            );
            
        if ($query->num_rows() > 0) {
            $this->db->where("patientId",$patientId);
            $this->db->where("barcodeSrNo",$barcodeSrNo);
             $this->db->update('product_response', $dataforproduct);
            // $query = $this->db->query(
            //     "update product_response set productName='" . $productName . "',brandName='" . $brandName . "',img='" . $img . "',eyes='" . $eyes . "',forhead='" . $forhead . "',neck='" . $neck . "',everyday='" . $everyday . "',onceAweek='" . $onceAweek . "',twiceAweek='" . $twiceAweek . "',onceAmonth='" . $onceAmonth . "',instruction='" . $instruction . "',am='" . $am . "',pm='" . $pm . "',minute='" . $minute . "',step='" . $step . "',numberOfStep='" . $numberOfStep . "',media='" . $media . "' where patientId='" . $patientId . "' and barcodeSrNo='" . $barcodeSrNo . "'"
            // );
        } else {
            // $this->db->query(
            //     "INSERT INTO product_response (patientId,specialistId,createdate,barcodeSrNo,productName,brandName,img,eyes,forhead,neck,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfStep,media)

			// VALUES ('" . $patientId . "','" . $specialistId . "','" . $createdate . "','" . $barcodeSrNo . "','" . $productName . "','" . $brandName . "','" . $img . "','" . $eyes . "','" . $forhead . "','" . $neck . "','" . $everyday . "','" . $onceAweek . "','" . $twiceAweek . "','" . $onceAmonth . "','" . $instruction . "','" . $am . "','" . $pm . "','" . $minute . "','" . $step . "','" . $numberOfStep . "','" . $media . "')"
            // );

            //echo $this->db->last_query();
              $this->db->insert('product_response', $dataforproduct);
        }

        $query1 = $this->db->query("select * from product_detail where specialId='" . $specialistId . "' and barcodeSrNo='" . $barcodeSrNo . "'");

        if ($query1->num_rows() == 0) {
            $this->db->insert('product_detail', $data);
        }else{
            $this->db->where("specialId",$specialistId);
            $this->db->where("barcodeSrNo",$barcodeSrNo);
             $this->db->update('product_detail', $data);
        }
        return 1;
    }

    public function product_Exists($deviceId)
    {
        $query = $this->db->query("select * from special_product_device where deviceId='" . $deviceId . "'")->num_rows();

        if ($query > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function total_search($searchbrand)
    {
        $query = $this->db->query("select brandName from brand_name where brandname LIKE '%" . $searchbrand . "%'  ");

        return $query->num_rows();
    }

    public function search_explore_Product_List($searchbrand, $limit, $offset)
    {
        $query = $this->db->query(
            "select img, brandName   from brand_name where brandName LIKE '%" . $searchbrand . "%'  limit " . $offset . ", " . $limit . ""
        );

        foreach ($query->result() as $row) {
            $row->brandName=ucwords($row->brandName);

            $data[] = $row;
        }

        return $data;
    }

    public function total_num()
    {
        $query = $this->db->query('SELECT brandName FROM brand_name ');

        return $query->num_rows();
    }

    public function explore_Product_List($limit, $offset)
    {
        $query = $this->db->query("select  img, brandName from brand_name  limit " . $offset . ", " . $limit . "");

        foreach ($query->result() as $row) {
            $row->brandName=ucwords($row->brandName);
            $data[] = $row;
        }

        return $data;
    }

    public function add_To_MyList($barcodeSrNo, $deviceId, $userId)
    {
        if ($userId != null) {
            $barcodeSrNo = explode(",", $barcodeSrNo);

            $notexist = array();

            $exist_bar = array();

            foreach ($barcodeSrNo as $value) {
                $query = $this->db->query("select barcodesrno from specialist_product_rel where barcodesrno='" . $value . "' and userId='" . $userId . "'");

                if (sizeof($query->result()) > 0) {
                    $exist_bar[] = $query->result()[0]->barcodesrno;

                    //print_r($exist_bar);
                }
            }

            foreach ($barcodeSrNo as $value) {
                $query = $this->db->query("select barcodeSrNo from product_detail where barcodeSrNo='" . $value . "' and specialId='" . $userId . "'");

                if (sizeof($query->result()) > 0) {
                    $exist_bar[] = $query->result()[0]->barcodeSrNo;
                }
            }

            foreach ($barcodeSrNo as $innerVal) {
                if (in_array($innerVal, $exist_bar)) {
                } else {
                    $notexist[] = $innerVal;
                }
            }

            foreach ($notexist as $value) {
                $this->db->query("INSERT INTO specialist_product_rel (barcodesrno, userId)

			VALUES ('" . $value . "','" . $userId . "')");
            }
        } else {
            $barcodeSrNo = explode(",", $barcodeSrNo);

            $notexist = array();

            $exist_bar = array();

            foreach ($barcodeSrNo as $value) {
                $query = $this->db->query("select barcodesrno from special_product_device where barcodesrno='" . $value . "' and deviceId='" . $deviceId . "'");

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
                $this->db->query("INSERT INTO special_product_device (barcodesrno, deviceId)

			VALUES ('" . $value . "','" . $deviceId . "')");
            }
        }

        return 1;
    }

    public function get_Patient_Token($id)
    {
        $query = $this->db->query("select * from user_token where userId='" . $id . "' and userType='patient'");

        return $query->result();
    }
    

    public function get_specialist_name($userId)
    {
        $query = $this->db->query("select userName from sepcial_users where userId='" . $userId . "'");

        return $query->result();
    }

    public function distroy_token($userId, $token, $deviceId)
    {
        $this->db->query("delete from user_token where userId='" . $userId . "' and token='" . $token . "' and userType='patient'");
    }

    public function my_clients($userId, $limit, $offset)
    {
        $data = array();

        $query = $this->db->query(
            "SELECT user_patient.* from user_patient inner join product_response on user_patient.userId=product_response.patientId where specialistId='" . $userId . "' GROUP by user_patient.userId limit " . $offset . ", " . $limit . ""
        );

        foreach ($query->result() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function my_chatlist($userId, $limit, $offset)
    {
        $data = array();
        $data['specialists']=[];
        $data['patients']=[];
        $query = $this->db->query(
            "SELECT user_patient.* from user_patient inner join product_response on user_patient.userId=product_response.patientId where specialistId='" . $userId . "' GROUP by user_patient.userId limit " . $offset . ", " . $limit . ""
        );

        foreach ($query->result() as $row) {
            $data['patients'][] = $row;
        }
        $data['specialists']=$this->get_my_colleagues($userId,1);

        return $data;
    }

    public function my_clients_count($userId)
    {
        $query = $this->db->query(
            "SELECT user_patient.* from user_patient inner join product_response on user_patient.userId=product_response.patientId where specialistId='" . $userId . "'"
        );

        return (String)$query->num_rows();
    }

    public function exploreProductListasproduct_total_search($searchbrand)
    {
        $query = $this->db->query("select * from admin_product where productName LIKE '%" . $searchbrand . "%'");

        return $query->num_rows();
    }

    public function exploreProductListasproduct_search_explore_Product_List($searchbrand, $limit, $offset)
    {
        $query = $this->db->query("select *  from admin_product where productName LIKE '%" . $searchbrand . "%'  limit " . $offset . ", " . $limit . "");

        foreach ($query->result() as $row) {
             $row->partIcon = "";
                
         
                $row->rating = $this->getrating($row->barcodeSrNo);
                if ($row->eyes=="1") {
                    $row->partIcon = base_url('assets/bodyparts/eyes.png');
                } elseif ($row->forhead=="1") {
                    $row->partIcon = base_url('assets/bodyparts/forhead.png');
                } elseif ($row->neck=="1") {
                    $row->partIcon = base_url('assets/bodyparts/neck.png');
                } elseif ($row->allFace=="1") {
                    $row->partIcon = base_url('assets/bodyparts/allFace.png');
                }
                //  $row->rating = $this->getrating($row->barcodeSrNo);
                array_walk(
                    $row,
                    function (&$item) {
                        $item = strval($item);
                        $item = htmlentities($item);
                        $item = html_entity_decode($item);
                    }
                );
            
            $data[] = $row;
        }

        return $data;
    }

    public function exploreProductListasproduct_total_num()
    {
        $query = $this->db->query('SELECT brandName FROM admin_product');

        return $query->num_rows();
    }

    public function exploreProductListasproduct_explore_Product_List($limit, $offset, $filterarray = array(), $filterparam = array(), $type = "android")
    {
        $bodyparts = array("eyes","forhead","neck","allFace");
        $data = [];
        // mark is true only if filter parameter is provided 1 in the api
        $mark = false;
        if (isset($filterarray[0]) && $filterarray[0] != "") {
            $mark = true;
            $columnName = array_fill_keys($filterarray, "1");
        }
        if ($type == "android") {
            foreach ($filterparam as $key => $value) {
                if ($value == "1") {
                    $columnName[$key] = $value;
                    $mark = true;
                }
            }
        }
        // in case of filtering 
        if ($mark) {
            $this->db->where($columnName);
        }
        $this->db->order_by("productOrder", "desc");

        $query = $this->db->get('admin_product', $limit, $offset);
        foreach ($query->result() as $key => $row) {
                $row->partIcon = "";
            
            if ($row->eyes=="1") {
               $row->partIcon = base_url('assets/bodyparts/eyes.png');

            }elseif ($row->forhead=="1") {
               $row->partIcon = base_url('assets/bodyparts/forhead.png');
            
            }elseif ($row->neck=="1") {
               $row->partIcon = base_url('assets/bodyparts/neck.png');
            
            }elseif ($row->allFace=="1") {
               $row->partIcon = base_url('assets/bodyparts/allFace.png');
            }
             if ($mark) {
                reset($columnName);
                $row->partIcon = base_url('assets/bodyparts/'.key($columnName).'.png');
            }
            $row->rating = $this->getrating($row->barcodeSrNo);
            $data[] = $row;
        }
        return $data;
    }

    public function showproductbybrand_total_search($searchbrand, $brandName)
    {
        $query = $this->db->query("select * from admin_product where productName LIKE '%" . $searchbrand . "%' and brandName='" . $brandName . "'");

        return $query->num_rows();
    }

    public function showproductbybrand_search_explore_Product_List($searchbrand, $brandName, $limit, $offset)
    {
        $query = $this->db->query(
            "select *  from admin_product where productName LIKE '%" . $searchbrand . "%' and brandName='" . $brandName . "'  limit " . $offset . ", " . $limit . ""
        );

        foreach ($query->result() as $row) {
               $row->brandName=ucwords(strtolower($row->brandName));
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
                array_walk(
                    $row,
                    function (&$item) {
                        $item = strval($item);

                        $item = htmlentities($item);

                        $item = html_entity_decode($item);
                    }
                );

                $data[] = $row;
        }

        return $data;
    }

    public function showproductbybrand_total_num($brandName)
    {
        $query = $this->db->query("SELECT brandName FROM admin_product where brandName='" . $brandName . "'");

        return $query->num_rows();
    }

    public function showproductbybrand_explore_Product_List($brandName, $limit, $offset)
    {
        $data=[];
       
     
        $query = $this->db->query("select * from admin_product where brandName='" . $brandName . "'  limit " . $offset . ", " . $limit . "");

        foreach ($query->result() as $row) {
             $row->rating = $this->getrating($row->barcodeSrNo);
             $row->brandName=ucwords(strtolower($row->brandName));

        
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
                array_walk(
                    $row,
                    function (&$item) {
                        $item = strval($item);

                        $item = htmlentities($item);

                        $item = html_entity_decode($item);
                    }
                );

            $data[] = $row;
        }

        return $data;
    }

    public function get_token($userId)
    {
        $query = $this->db->query("SELECT * FROM user_patient where userId='" . $userId . "'");

        return $query->result();
    }

    public function getencpassword($userId = "", $receivehash)
    {
        $query = $this->db->query("select password from sepcial_users where userId=" . $userId . "");

        $hash = $query->result()[0]->password;

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

    // to get the country listings
    public function countrieslist()
    {
        $query = $this->db->get('tbl_country');
        return $query->result();
    }
    // to get the staye listings
    public function stateslist($cid = '')
    {
        $query = $this->db->get_where('tbl_state', array('country_id' => $cid));

        return $query->result();
    }

    // save reviews in reviews table
    public function savereviews($data)
    {
        $this->db->insert('reviews', $data);

        return $this->db->insert_id();
    }

    // save reviews in reviews table
    public function edit_reviews($comment, $rating, $reviewId = 0)
    {
        $this->db->where('id', $reviewId);
        return $this->db->update('reviews', array('comment' => $comment, 'rating' => $rating, 'modifiedtime ' => date("Y-m-d H:i:s")));
    }

    // edit feedback of specialist 
    public function edit_feedback($comment, $rating, $reviewId = 0)
    {
        $this->db->where('id', $reviewId);
        return $this->db->update('specialist_rating', array('feedback' => $comment, 'rating' => $rating,'modifiedtime' => date("Y-m-d H:i:s")));
    }


    // get all the counts for dashboard
    public function getallcounts($specialistId)
    {
		
		
        $data = array(
            'averageRating' => "0", 'totalCustomers' => "0",
			"notificationCount" => $this->getNotificationcount($specialistId),
            "allmessageCount"=> $this->allmessageCount($specialistId),
             "userInfo" => $this->getusermetainfo($specialistId),
            "reviewsCount" => $specialistId,
          //  "sendmenotification" => (String)$this->send_me_notification($specialistId),
            "productsCount" => array("total" => "0", "newProducts" => "0"),
            "mostSellingProducts" => [],
         ///   "images"=>$this->get_images($specialistId)
        );
        $data['averageRating'] 	= $this->getratingforspecialist($specialistId);
        $data['reviewsCount'] = $this->getReviewCountforspecialist($specialistId);
        $data['totalCustomers'] = $this->getTotalCustomers($specialistId);
        $data['productsCount']['total'] = (String)sizeof($this->view_product($specialistId,'',[]));
        $data['connectedColleagues'] = (String)sizeof($this->get_my_colleagues($specialistId,1));
        $data['mySentRequests'] = (String)sizeof($this->get_requests($specialistId));
        $data['mostSellingProducts'] = $this->getTopSellingProducts($specialistId);
		$data['facialThisMonth'] = '2';
        return $data;
    }


    // get  all the top selling products
    public function get_images($specialistId = "") {
        $data=[];
        $this->db->from('image_report');
        $this->db->where('patientId ', 0);
         $this->db->order_by('id', 'desc');
        $images = $this->db->get();
        foreach ($images->result() as $key => $value) {
           $data[]['imageUrl']=base_url($value->imageUrl);
        }
        return $data;
    }

    // get the profile complete and usertype 
    
    public function getusermetainfo($specialistId) {
        $this->db->select("loginStrategy,iscompleted");
        $this->db->where('userId', $specialistId);
        $query = $this->db->get('sepcial_users');
        return $query->result()[0];
     

    }

    // check the permision of sending the notification
    public function send_me_notification($specialistId) {
      $this->db->select("*");
        $this->db->where('entityId', $specialistId);
        $this->db->where('entity', 'specialist');
        $query = $this->db->get('notifyme_setting');
         if ($query->num_rows() > 0) {
             return $query->result()[0]->status;
         } else {
             return true;
         }

    }

	// all message count
	public function allmessageCount($specialId="0") {
	 	$this->db->select("count");
        $this->db->where('entity_id', $specialId);
        $this->db->where('type', 'specialist');
		$query = $this->db->get('inboxmessage_count');
		if ($query->num_rows()>0) {
			return $query->result()[0]->count;
		}else{
			return "0";
		}
        

	}
	// get notifications count 
	public function getNotificationcount($specialId) {
		$data=['read'=>"0","unread"=>"0","total"=>"0"];
		$sql="SELECT count(id) as num,readStatus FROM `fcm_notifications` where userId = ".$specialId." and entity='specialist' group by readStatus order by readStatus desc";
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
    // get  Review Count for specialist
    public function getReviewCountforspecialist($specialId = "")
    {
        $this->db->distinct();

        $this->db->select("givenby");

        $this->db->where('userId', $specialId);
        $query = $this->db->get('specialist_rating');
        return (String)$query->num_rows();
    }
    
    // get  Review Count for specialist
    public function getTotalCustomers($specialId = "")
    {
        $this->db->distinct();

        $this->db->select("userId");
        $this->db->from('user_patient');

        $this->db->join('product_response', 'product_response.patientId  = user_patient.userId ');

        $this->db->where('specialistId', $specialId);

        $query = $this->db->get();
        //  "SELECT user_patient.* from user_patient inner join product_response on user_patient.userId=product_response.patientId where specialistId='" . $userId . "'"
        return (String)$query->num_rows();
    }
    // get  Review Count for specialist
    public function getProductCount($specialId = "")
    {
        $this->db->distinct();
        $this->db->where('specialId', $specialId);
        $query = $this->db->get('product_detail');
        return (String)$query->num_rows();
    }
    // get  all the top selling products
    public function getTopSellingProducts($specialId = "")
    {
        $products = []; 

        $this->db->select(
            'product_detail.barcodeSrNo , count(patient_product_rel.barcodesrno) as noofpatient ,product_detail.productId,product_detail.specialId,product_detail.barcodeId,product_detail.barcodeSrNo,product_detail.productName,product_detail.brandName,product_detail.addButton,product_detail.img,product_detail.eyes,product_detail.forhead,product_detail.neck,product_detail.allFace,product_detail.everyday,product_detail.onceAweek,product_detail.twiceAweek,product_detail.onceAmonth,product_detail.instruction,product_detail.am,product_detail.pm,product_detail.minute,product_detail.step,product_detail.numberOfStep,product_detail.media,product_detail.status,product_detail.addToExplore'
        );
        $this->db->from('product_detail');
        $this->db->join('patient_product_rel', 'product_detail.barcodeSrNo  = patient_product_rel.barcodesrno ');
        $this->db->where('specialId', $specialId);
        $this->db->limit(5);

        $this->db->group_by(
            'product_detail.barcodeSrNo ,product_detail.productId,product_detail.specialId,product_detail.barcodeId,product_detail.barcodeSrNo,product_detail.productName,product_detail.brandName,product_detail.addButton,product_detail.img,product_detail.eyes,product_detail.forhead,product_detail.neck,product_detail.allFace,product_detail.everyday,product_detail.onceAweek,product_detail.twiceAweek,product_detail.onceAmonth,product_detail.instruction,product_detail.am,product_detail.pm,product_detail.minute,product_detail.step,product_detail.numberOfStep,product_detail.media,product_detail.status,product_detail.addToExplore'
        );
        $this->db->order_by('noofpatient', 'desc');

        $products = $this->db->get()->result();
      if (empty($products)) {
          $this->db->from('specialist_product_rel');
        $this->db->join('admin_product', 'admin_product.barcodeSrNo=specialist_product_rel.barcodesrno');
        $this->db->where('specialist_product_rel.userId', $specialId);
        $this->db->order_by("admin_product.productOrder", "desc");
        $this->db->limit(5);
        $products = $this->db->get()->result();
      }
        return $products;
	}
	 // update count 
    
     public function updateinboxmessage($specialistId) {
        $data = array('type' => 'specialist',
        'entity_id'=>$specialistId,
        'count' =>1);
        $this->db->where('type','specialist');
        $this->db->where('entity_id', $specialistId);
        $q = $this->db->get('inboxmessage_count');
        if ($q->num_rows() > 0) {
            
            $previous=$q->result()[0]->count;
            $new = ++$previous;
             $this->db->where('type', 'specialist');
                $this->db->where('entity_id', $specialistId);
                $data['count']=$new;
            $this->db->update('inboxmessage_count', $data);
        } else {
             $this->db->insert('inboxmessage_count', $data);
        }
    }

    // complete the profile after the social login :
    
    public function complete_profile($specialistId,$data=[]) {
        $this->db->where('userId', $specialistId);
        $this->db->update('sepcial_users', $data);
         $this->db->where('userId', $specialistId);
        $result=$this->db->get('sepcial_users')->result();
        array_walk( $result[0], function (&$item) {
            $item = strval($item);
            $item = htmlentities($item);
            $item = html_entity_decode($item);
        });
        return $result;
    

    }

    public function check_email($email,$id)
    {
        
        $query = $this->db->query("select * from sepcial_users where email = '$email' and userId!=$id");
        return $query->num_rows();
    }

    // send friend requests
    public function send_request($s1,$s2) {
      
        $data['specialist_one_id ']= min($s1,$s2);
        $data['specialist_two_id ']= max($s1,$s2);
       
         $data['action_specialist_id']=$s1;
         $data['status ']=0;
         $db_debug = $this->db->db_debug; //save setting

         $this->db->db_debug = FALSE; //disable debugging for queries

        $this->db->insert('specialist_relationship', $data); //run query

        $result =$this->db->error();

         $this->db->db_debug = $db_debug; //restore setting
        
        // $this->db->insert('specialist_relationship', $data);
        return $result;
    }

    // cancel requests
    public function cancel_request($s1,$s2) {
      
        $data['specialist_one_id ']= min($s1,$s2);
        $data['specialist_two_id ']= max($s1,$s2);
        //  $data['action_specialist_id']=$s1;
        //  $data['status ']=0;
        
        // $this->db->insert('specialist_relationship', $data);
        return $this->db->delete('specialist_relationship', $data);
    }

    // get friend requests of pending friend 
    public function get_requests($s1) {
        $sender=[];
        $data=[];
        $query=$this->db;
        $query->distinct();
        $query->select('sepcial_users.userId as requestId ,specialist_relationship.specialist_one_id,specialist_relationship.specialist_two_id,sepcial_users.*');
         $query->from('specialist_relationship');
         $query->join('sepcial_users', 'sepcial_users.userId=specialist_relationship.action_specialist_id');
        $query->where("(`specialist_one_id` = $s1 OR `specialist_two_id` = $s1)");
        $query->where('specialist_relationship.status', 0);
        $query->where('action_specialist_id !=', $s1);
        $data=$query->get()->result();
       
        return $data;
        
    }

    // respond to request  based on actionId if actionId status 1 for accept friend request 2 for denied and 3 for block 
    public function respond_to_request($sp1,$sp2,$actionId) {
        $data['action_specialist_id']=$sp1;
        $data['status']=$actionId;
        $this->db->where('specialist_one_id', min($sp1,$sp2));
        $this->db->where('specialist_two_id', max($sp1,$sp2));
        return $this->db->update('specialist_relationship', $data);
    }


    // get the list of all specialist (already added specialist are ignored in this list )
    public function get_all_specialists($s1,$searchname="")
    {
        $data=[];
        $excludespecialist=[];
        $pendingspecialist=[];
        // get all specialist 
        $this->db->distinct();
        $this->db->where('userId!=', $s1);
        $this->db->group_start();
        $this->db->or_like('userName', $searchname, 'both');
         $this->db->or_like('phone', $searchname, 'both');
         $this->db->or_like('email', $searchname, 'both');
        $this->db->group_end();
        // $this->db->like('phone', $searchname, 'both');
        // $this->db->like('email', $searchname, 'both');
        $query = $this->db->get('sepcial_users');
        $data=$query->result();
        // to get the pending and accepted friends of specialists
        $query=$this->db;
        $query->distinct();
        $query->select('*');
        $query->from('specialist_relationship');
        $data2=$query->where("(`specialist_one_id` = $s1 OR `specialist_two_id` = $s1)")->get()->result();
        foreach ($data2 as $key => $value) {
            if ($value->status==0) {
                $pendingspecialist[]=$value->specialist_one_id;
                $pendingspecialist[]=$value->specialist_two_id;
            }else if ($value->status==3||$value->status==1){ //exclude in case of block and already friend
                $excludespecialist[]=$value->specialist_one_id;
                $excludespecialist[]=$value->specialist_two_id;
            }
            
        }
        foreach ($data as $key => $value) {
            if (in_array($value->userId,$pendingspecialist)) {
               $value->addstatus="0";
            }else if (in_array($value->userId,$excludespecialist)) {
                unset($data[$key]);
               continue;
            }else{
                $value->addstatus="1";
            }
             $value->noOfProduct = "";
             $value->noOfPatient = "";
             $value->noOfReviews = "";
             $value->rating=$this->getratingforspecialist($value->userId);
            $data[$key]=$value;
        }
        return $data;
    }
   

    // get all the accepted requests
    public function get_my_colleagues($s1,$type)
    {
        // $type ==1 for friends and $type== 3 for blocked and $type==2 for declined list and $type==0 for  of collegues
        $sender=[];
        $data=[];
        $friendids=[];
        $query=$this->db;
        $query->distinct();
        $query->select('*');
        $query->from('specialist_relationship');
        $query->where("(`specialist_one_id` = $s1 OR `specialist_two_id` = $s1)");
        $this->db->where('status=',$type);

        // in case of blocked blocked by user condition 
        if ($type=="3") {
             $this->db->where('action_specialist_id=',$s1);
        }
        $data=$query->get()->result();
        foreach ($data as $key => $value) {
            if ($s1==$value->specialist_one_id) {
               $friendids[]=$value->specialist_two_id;
            }else{
                $friendids[]=$value->specialist_one_id;
            }
        }
      
        
        if ($friendids) {
            $this->db->select('*');
            $this->db->where_in('userId', $friendids);
            $data=$this->db->get('sepcial_users')->result();
        }
        foreach ($data as $key => $value) {
             $value->noOfProduct = "";
             $value->noOfPatient = "";
             $value->noOfReviews = "";
             $value->rating=$this->getratingforspecialist($value->userId);
            $data[$key]=$value;
        }
        return $data;
       
    }


    // get device token for sending notification
    public function get_Specialist_Token($id)
    {
        $data=[];
        $this->db->select('*');
        $this->db->where('userId', $id);
        $this->db->where('userType', 'specialist');
        $data=$this->db->get('user_token')->result();
        return $data;
    }


    // get specialist details
    public function specialist_detail($selfid, $id)
    {
        // data is empty
        $data = [];
        if ($this->checkfriendship($selfid, $id,3)===true) {
            return $data;
        }

        $query = $this->db->query(
            "select sepcial_users.jobProfile,sepcial_users.userName,sepcial_users.age,sepcial_users.city,sepcial_users.proPicName,sepcial_users.countary,sepcial_users.email,sepcial_users.yearOfExperience,sepcial_users.phonePrivacy,sepcial_users.emailPrivacy,sepcial_users.gender,sepcial_users.phone,sepcial_users.aboutMe,( select count(specialist_product_rel.barcodesrno) from specialist_product_rel where specialist_product_rel.userId=" . $id . " ) as productCount,( select count(DISTINCT patient_treatement.patientId) from patient_treatement where patient_treatement.specialId=" . $id . ") as PatientCount from sepcial_users left join specialist_product_rel on sepcial_users.userId=specialist_product_rel.userId left join patient_treatement on sepcial_users.userId=patient_treatement.specialId where sepcial_users.userId=" . $id . " limit 0,1"
        );
        foreach ($query->result() as $row) {
            $row->rating = $this->getratingforspecialist($id);
            $temp = $this->Patient_model->getreviewsforspecialist($id, 0);
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
            $row->addstatus = $this->checkfriendship($selfid, $id,1);
             $row->noOfReviews = (String)$this->Patient_model->getreviewsCountforspecialist($id);

            $data[] = $row;
        }
        return $data;
    }

    // check if is friends or not 
    public function checkfriendship($s1,$s2,$status) 
    {
        $data=[];
        $query=$this->db;
        $query->distinct();
        $query->select('*');
        $query->from('specialist_relationship');
        $data=$query->where("(`specialist_one_id` = ".min($s1,$s2)." and `specialist_two_id` = ".max($s1,$s2).")")->get()->result();
        if ($data) {
             // in case of block return true 
            if ($status=="3"&&$data[0]->status=="3") {
               return true;
            }
            if ($data[0]->status=="1") {
            return "1";
            }else {
            return "0";
            }
        }
        return "1";
    }
	
	
	//Check and get specialist available time
	public function check_available_time($sId,$selectedDate){
		$data 	 = '';
		$getData = '';
		$todayDate  = gmdate('Y-m-d');
		if($todayDate!=''){
			$expDate 	= explode('-',$todayDate);
			$currentYear = $expDate[0];
			$currentMonth= $expDate[1];
			$currentDay  = $expDate[2];
		}
		
		$convertSelDate = strtotime($selectedDate); 
		$currentDate 	= strtotime($todayDate); 
		if($convertSelDate == $currentDate){//today
			$currentHour	= gmdate('H');	
			$currentTime  	= mktime($currentHour,0,0,$currentMonth,$currentDay,$currentYear);
			$condition = array('bookedDate'=>$selectedDate,'specialistId' =>$sId,'bookedTime >'=>$currentTime);
		}
		else{//upcoming day
			$condition 	= array('bookedDate'=>$selectedDate,'specialistId' =>$sId); 
			$currentHour= '01';	
		}
		
		/* $fetchData = $this->db->select(array('bookedDate','bookedTime','patientId','bookingStatus'))->get_where('specialist_book_facial',$condition); */
		$fetchData = $this->db->select(array('bookedTime'))->get_where('specialist_book_facial',$condition);
		
		//get list of upcoming hours
		$timeArr  = array();
		$bookTime = array();
		for($i=$currentHour;$i<=24;$i++){
			$number  = str_pad($i, 2, '0', STR_PAD_LEFT);
			$timeArr[] = $number;
		}
		if ($fetchData->num_rows() > 0) {
			$i = 0;
			foreach ($fetchData->result() as $row)
			{
				//$getData[$i] = $row;
				$getTime = $row->bookedTime;
				//$getTime = date("H:i", $getTime);
				$bookTime[] = gmdate("H", $getTime);
				//$getData[$i]->availableTime = $getTime;
				$i++;
			}
		}
		//print_r($bookTime);
		$getData = array_diff($timeArr, $bookTime);
		$selectedDate  = array('selectedDate'=>$selectedDate);
		//$getData 	   = array_merge($getAvTimeList,$selectedDate);
		$data = array_values($getData);
		return $data;
	}
	
	//Book facial
	public function book_facial($data){
		$facialData  = 	array(
							'bookedBy' 		=> $data['bookedBy'],
							'specialistId' 	=> $data['bookedFor'],
							'bookedDate' 	=> $data['bookedDate'],
							'bookedTime' 	=> $data['bookedTime'],
							'patientId' 	=> $data['patientId'],
							'patientNotes' 	=> $data['patientNotes'],
							'privateNotes' 	=> $data['specailistNotes'],
							'bookingStatus' => '0'
						);
						
		$this->db->insert('specialist_book_facial', $facialData);
        return $this->db->insert_id();
	}

    	//Book facial
	public function complete_facial($data){
					
		$this->db->insert('completefacial', $data);
        return $this->db->insert_id();
	}


	//get list of facials booked by me for me
	public function get_my_booked_facials($sId,$selectedDate){
		$data 	= '';
		$getData = array();
		if($selectedDate == 0){
			$checkConditions = array('specialistId' =>$sId);
		}
		else{
			$checkConditions = array('specialistId' =>$sId, 'bookedDate'=>$selectedDate);
		}
		
		$fetchdata = $this->db->select(array('id','specialistId','bookedDate','bookedTime','patientNotes','privateNotes','bookingStatus','user_patient.userName as patientName','specialist_book_facial.patientId'))->join('user_patient', 'user_patient.userId = specialist_book_facial.patientId','Left')->get_where('specialist_book_facial',$checkConditions);
		
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
		$tmpArr  = array();
		$mainArr = array();
		$fetchData = $this->db->select(array('id','specialistId','bookedDate','bookedTime','bookingStatus'))->get_where('specialist_book_facial',array('specialistId' =>$sId));
		
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
	
	public function get_facial_detail($bId)
	{
	   $fetchData = $this->db->select('*')->get_where('completefacial',array('bookingId' =>$bId));
	   
		$res =  $fetchData->result_array();
	//	print_r($res);
		$dat = array();
		$i=0;
		if(!empty($res)){
    		foreach($res as $bar){
    		 $dat[$i] = $bar;
    	
            $fetchpro = $this->db->query("select * from admin_product where barcodeSrNo IN ($bar[barcodes])");
            
            $prod =  $fetchpro->result_array();
    		 
    		 $dat[$i]['products'] = $prod;
    		 $i++;   
    		}
    		
    	
		}
		
		return $dat;
	}
	
/*	
	public function facialaddproduct($sId,$patientId,$barcodesrno){
	    //die($patientId);
	    $fetchpro = $this->db->query("select `barcodeSrNo`, `productName`, `brandName`, `img`, `eyes`, `forhead`, `neck`, `allFace`, `everyday`, `onceAweek`, `twiceAweek`, `onceAmonth`, `instruction`, `am`, `pm`, `minute`, `step`, `numberOfStep`, `media`  from admin_product where barcodeSrNo IN ($barcodesrno)");
            
        $prod =  $fetchpro->result_array();
        $last_id = array();
        if(!empty($prod)){
    		foreach($prod as $product){
    
              $prod =  $product;   
              
              $prod['specialistId'] = $sId;
              $prod['patientId'] = $patientId;
              //$prod['status'] = 1;
             
                
                //$this->db->insert('product_response', $prod);
             $this->db->insert(' product_request', $prod);   
                
                
                $last_id[] = $this->db->insert_id();
            }
    		
    		
        }
	    return $last_id;
	}
	*/
	
	public function facialaddproduct($sId,$patientId,$barcodesrno){
	    //die($patientId);
	    $fetchpro = $this->db->query("select `productId`, `barcodeSrNo`  from admin_product where barcodeSrNo IN ($barcodesrno)");
            
        $prod1 =  $fetchpro->result_array();
        $last_id = array();
        if(!empty($prod1)){
    		foreach($prod1 as $product){
    
             // $prod =  $product;   
              
              $prod['specialId'] = $sId;
              $prod['patientId'] = $patientId;
              //$prod['status'] = 1;
              $prod['productId'] = $product['productId'];
               $prod['barcodesrno'] = $product['barcodeSrNo'];
                
                //$this->db->insert('product_response', $prod);
             $this->db->insert(' product_request', $prod);   
                
                
                $last_id[] = $this->db->insert_id();
            }
    		
    		
        }
	    return $last_id;
	}
	
	public function addNewPatient($sId=0){

        if(empty($sId)){
            $sId = 35;  //it can't be empty I just added this line for POSTMAN testing;
        }
        $last_id = array();
	    $data = $_POST;
	    

	    $query = $this->db->get_where('user_patient', array('email' => $data['patientEmail']));
        if ($query->num_rows() == 0) {
    
            $userdata = array(
                'userName'   => $data['patientName'],
                'email'      => $data['patientEmail'],
                'password'   => $this->hash_password(123456),
                'phone' => $data['patientPhone'],
                'status' => 1,
                'loginStrategy'=>'local-oauth'
            );
            $this->db->insert('user_patient', $userdata);
            $patient_id = $this->db->insert_id();
           
            $this->db->insert('patient_treatement', array('specialId'=>$sId,'patientId'=>$patient_id,'concernType'=>'facial'));
    
    	    $sql =  "select `barcodeSrNo`, `productName`, `brandName`, `img`, `eyes`, `forhead`, `neck`, `allFace`, `everyday`, `onceAweek`, `twiceAweek`, `onceAmonth`, `instruction`, `am`, `pm`, `minute`, `step`, `numberOfStep`, `media`  from admin_product where barcodeSrNo IN ($data[selectedProducts])"; 
    	    
    	    $fetchpro = $this->db->query($sql);
                
            $prod =  $fetchpro->result_array();
           
            if(!empty($prod)){
        		foreach($prod as $product){
        
                  $prod =  $product;   
                  
                  $prod['specialistId'] = $sId;
                  $prod['patientId'] = $patient_id;
                  //$prod['status'] = 1;
                 
                    
                    $this->db->insert('product_response', $prod);
                    
                    $last_id[] = $this->db->insert_id();
                }
            }
        }else{
             $prod =  $query->row_array();
            $patient_id = $prod['userId'];
            
        }
	    return array('patient'=>array('userId'=>$patient_id,'userName'=>$data['patientName']),'products'=>$last_id);
	}
	
}