<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User_model class.
 * 
 * @extends CI_Model
 */
class Patient_model extends CI_Model {

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
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
	public function message(){
		$data=array();
		$query=$this->db->query("select * from alert_message");
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}
			return $data;
		}

			public  function  member_Id($userId,$memberId){

			$this->db->query("update user_patient set memberId=".$memberId." where userId=".$userId."");
		  $query=$this->db->query("select * from user_patient where userId=".$userId."");
		
		   return  $query->result() ;

	}
	public function create_user($userName,$age,$city,$link, $countary,$password,$gender,$phone,$email,$token,$type,$deviceId) {
		
		$fcmUser="p".$phone;
		$data = array(
			'userName'   => $userName,
			'age'   => $age,
			'city'   => $city,
			'proPicName'   => $link,
			'countary'   => $countary,
			'email'      => $email,
			'password'   => $this->hash_password($password),
			'gender'   => $gender,
			'phone'   => $phone,
			'type'   => $type,
			'token'   =>$token,
			'fcmUser'   =>$fcmUser

		);
		
		//print_r($data);
		//die();
		
		$this->db->insert('user_patient', $data);
		$insert_id = $this->db->insert_id();

		$insert=$this->db->query("select * from user_token where userId='".$insert_id."'and token='".$token."' and userType='patient'")->result();


		if(sizeof($insert)>0){
			

		}else{


		$this->db->query("INSERT INTO user_token (userId, token, deviceId,type,userType)
			VALUES ('".$insert_id."','".$token."','".$deviceId."','".$type."','patient')");
	}




		$query=	$this->db->query("select * from user_patient where userId=".$insert_id."");

      return  $query->result();

     
		
	}
	public function get_user($user_id,$token,$type,$deviceId) {
		
		$this->db->from('user_patient');
		$this->db->where('userId', $user_id);

		$insert=$this->db->query("select * from user_token where userId='".$user_id."'and token='".$token."' and userType='$deviceId'")->result();


		if(sizeof($insert)>0){
			$this->db->query("update user_patient set token='".$token."', type='".$type."' where userId=".$user_id."");

		}else{


		$this->db->query("INSERT INTO user_token (userId, token, deviceId,type,userType)
			VALUES ('".$user_id."','".$token."','".$deviceId."','".$type."','patient')");


		$this->db->query("update user_patient set token='".$token."', type='".$type."' where userId=".$user_id."");


		
	}
		return $this->db->get()->row();
	}
	public function get_specialist($userId){
		 $id=$this->db->query("select * from patient_treatement where patientId='".$userId."' ")->result()[0]->specialId;
		return $data=$this->db->query("select * from sepcial_users where userId='".$id."' ")->result();
		 	
	
	}
	public function get_Concern($userId){
		 $data=array();
		 $query=$this->db->query("select concernType from patient_treatement where patientId='".$userId."' ");
		    foreach ($query->result() as $row)
			{//print_r($row);
			      $data[]=$row ;
				
			}
               return $data;
	
	 return $this->db->query("select * from sepcial_users where userId='".$specialId."'")->result();

	}
	public function get_user_id_from_username($username) {
		
		$this->db->select('userId');
		$this->db->from('user_patient');
		$this->db->where('email', $username);

		return $this->db->get()->row('userId');
		
	}
	public function search_specialist($searchspecialist,$limit,$offset){
			
		    $query=	$this->db->query("select * from sepcial_users where userName LIKE '%".$searchspecialist."%' or phone LIKE '%".$searchspecialist."%' or email LIKE '%".$searchspecialist."%' limit ".$offset.", ".$limit."");
		    foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			return $data;

	}
	public function total_search($searchspecialist){
		$query=	$this->db->query("select * from sepcial_users where userName LIKE '%".$searchspecialist."%' or phone LIKE '%".$searchspecialist."%' or email LIKE '%".$searchspecialist."%'");
		return $query->num_rows();
	}
	public function resolve_user_login($username, $password) {
		
		$this->db->select('password');
		$this->db->from('user_patient');
		$this->db->where('email', $username);
		$hash = $this->db->get()->row('password');
		
		return $this->verify_password_hash($password, $hash);
		
	}
	
	public function create_barcode($productName,$brandname,$barcodesrno,$file_name){


					$this->db->query("INSERT INTO bar_code_detail (barcodeSrNo, productName, brandName,img,status)
			VALUES ('".$barcodesrno."','".$productName."', '".$brandname."','".$file_name."',2)");

				return	$insert_id = $this->db->insert_id();
	}


	
	public function exist_bar($barcodesrno){

		//return $this->db->query("select * from bar_code_detail where barcodesrno=".$barcodesrno."");

		$this->db->from('bar_code_detail');
		$this->db->where('barcodesrno', $barcodesrno);
		return $this->db->get()->row();
		

	}
	public function add_product($barcodesrno,$userid){

		$this->db->query("INSERT INTO patient_product_rel (barcodesrno, userId)
			VALUES ('".$barcodesrno."','".$userid."')");
				return 1;

	}
	public function shows_specialist($limit,$offset){

		    $query=	$this->db->limit($limit,$offset)->get('sepcial_users');
		    //echo $this->db->last_query();
		    foreach ($query->result() as $row)
			{

                // to fetch the no of product specialist 
                $this->db->where('userId', $row->userId);
                $query = $this->db->get('specialist_product_rel');
                $row->noOfProduct= (String)$query->num_rows();
                // to fetch the no of patient  
                $this->db->where('specialId', $row->userId);
                $query = $this->db->get('patient_treatement');
                $row->noOfPatient= (String)$query->num_rows();
                $row->rating= number_format((rand(0, 50)/10), 1, '.', '');
                $row->noOfReviews=(String)rand(20,1500);
                
			        $data[]=$row ;
				
			}
			return $data;

	}
	public function check_product($deviceId,$userId,$barcodesrno){

		if($userId!=null)
		{
			$query=$this->db->query("select * from patient_product_rel where userId='".$userId."' and barcodesrno='".$barcodesrno."' ");
			//echo $this->db->last_query();
			if($query->num_rows()>0){
				return 1;
			}else{
				return 0;
			}

		}
		else
		{

		
			$query=$this->db->query("select * from patient_product_device where deviceId='".$deviceId."' and barcodesrno='".$barcodesrno."' ");
			//echo $this->db->last_query();
			if($query->num_rows()>0){
				return 1;
			}else{
				return 0;
			}
		
	}

  }
	public function temp_Id(){


			$query=$this->db->query("select max(id) as id from temp_id");
			 $id=$query->result();
			$id=$id[0]->id+1;
			$query=$this->db->query("insert into temp_id (id) values (".$id.")");
		 	$query=$this->db->query("select max(id) as id from temp_id");
			 $id=$query->result();
		    $id=$id[0]->id;
			 $_SESSION["id"]= $id;


	}
	public function add_product_device($barcodesrno,$deviceId){
		$this->db->query("INSERT INTO patient_product_device (barcodesrno, deviceId)
			VALUES ('".$barcodesrno."','".$deviceId."')");
			return 1;
	}
	public function view_product($id,$deviceId){
			$data=array();
			if($id!=null){
		$query=$this->db->query("select patient_product_device.*,bar_code_detail.* from patient_product_device left join bar_code_detail on patient_product_device.barcodesrno=bar_code_detail.barcodeSrNo where patient_product_device.userId=".$id."");
	}else{
		// $query=$this->db->query("select patient_product_device.*,bar_code_detail.* from patient_product_device left join bar_code_detail on patient_product_device.barcodesrno=bar_code_detail.barcodeSrNo where patient_product_device.deviceId='".$deviceId."'");
		$query=$this->db->query("select admin_product.*,patient_product_device.*,bar_code_detail.* from patient_product_device left join bar_code_detail on patient_product_device.barcodesrno=bar_code_detail.barcodeSrNo left join admin_product on admin_product.barcodeSrNo=patient_product_device.barcodesrno where patient_product_device.deviceId='".$deviceId."'");
		
	}
		//echo $this->db->last_query();
		foreach ($query->result() as $row){
            array_walk($row, function (&$item) {
                    $item = strval($item);
                    $item = htmlentities($item);
                    $item = html_entity_decode($item);
                });

                $row->rating= number_format((rand(0, 50)/10), 1, '.', '');
            
					 $data[]=$row ;
			}
			return $data;

	}
	public function reject_barcode($barcodesrno){

		//return $this->db->query("select * from bar_code_detail where barcodesrno=".$barcodesrno."");
		
		$query=$this->db->query("select * from bar_code_detail where barcodeSrNo='".$barcodesrno."' and status=0");
		 return $query->num_rows();
		

	}
	
	
	public function choose_concern(){

		    $query=$this->db->query("select * from concerns");
		    foreach ($query->result() as $row)
			{
				$row->picname= base_url($row->picname);
			    $data[]=$row ;
				
			}
			return $data;

	}
	public function total_num(){

		$query=  $this->db->query('SELECT * FROM sepcial_users');
		return $query->num_rows();
	}
	public function shows_pecialist($limit,$offset){

		    $query=	$this->db->limit($limit,$offset)->get('sepcial_users');
		    foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			return $data;

	}
	

	public function patient_Treatment($concernType,$specialId,$deviceId){
			$concernType=explode(',', $concernType);
			$checker=$this->db->query("select * from temp_choose_special where deviceId= '".$deviceId."' ")->num_rows();

			if($checker>0){

				$this->db->query("delete  from temp_choose_special where deviceId= '".$deviceId."' ");
			}

			foreach ($concernType as $key => $value) {
				
						$this->db->query("INSERT INTO temp_choose_special (deviceId, specialId, concernType)
			VALUES ('".$deviceId."','".$specialId."', '".$value."')");
				}	
	      
		
	  
	}
	
	/**
	 * hash_password function.
	 * 
	 * @access private
	 * @param mixed $password
	 * @return string|bool could be a string on success, or bool false on failure
	 */
	private function hash_password($password) {
		
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
	private function verify_password_hash($password, $hash) {
		
		return password_verify($password, $hash);
		
	}
	public function specialist_name($id){
		$query=$this->db->query("SELECT sepcial_users.userName FROM `sepcial_users` inner join patient_treatement on sepcial_users.userId=patient_treatement.specialId where patient_treatement.patientId=".$id." limit 0,1");
	     	return $query->result() ;
			//return $data;
		}
		public function concern_types($id){
		$query=$this->db->query("select concernType from patient_treatement  where patientId=".$id."");
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}

			return $data;
		}
	public function show_profile($id){
		$query=$this->db->query("select * from user_patient where userId=".$id."");
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}
			return $data;
		}
		public function insert_patient_treatment($id,$deviceId){

			$query=$this->db->query("select * from temp_choose_special where deviceId='".$deviceId."'");
			foreach ($query->result() as $row){
			
				$this->db->query("INSERT INTO patient_treatement (concernType, patientId, specialId)
			VALUES ('".$row->concernType."','".$id."', '".$row->specialId."')");
			}

		}
		public function dummy_img(){

		$query=$this->db->query("select * from dummy_image");
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}
			return $data;
	}
	public function request_specialist($userId){

			$query=$this->db->query("select * from patient_treatement where patientId='".$userId."' ");
			//echo $this->db->last_query();

			$data=$query->result();
		
			$this->db->query("INSERT INTO notification (patientId, specialistId)
			VALUES (".$userId.",".$data[0]->specialId.")");
			
			
			//print_r($data);

	}
	public function insert_patient_rel($userId,$deviceId){

		$query=$this->db->query("select * from patient_product_device where deviceId='".$deviceId."'");
		$data=array();
		    foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			foreach ($data as $key => $value) {
			$this->db->query("INSERT INTO patient_product_rel (barcodesrno, userId, manufactureDate,expiryDate)
			VALUES ('".$value->barcodesrno."','".$userId."', '".$value->manufactureDate."','".$value->expiryDate."')");
			}
		
	}
	public function insert_patient_request($userId,$deviceId){

			$query1=$this->db->query("select * from patient_treatement where patientId='".$userId."' ");
			//echo $this->db->last_query();

			$data=$query1->result();

		$query=$this->db->query("select * from patient_product_rel where userId='".$userId."'");
		 foreach ($query->result() as $row){
			
					 $this->db->query("INSERT INTO product_request (productId,patientId,specialId,barcodesrno)
			VALUES (".$row->id.",".$userId.",".$data[0]->specialId.",'".$row->barcodesrno."')");
					// $this->db->last_query();
			}
			//print_r($data);
	}
	public function delete_temp_choose_special($deviceId){
		  $this->db->query("DELETE FROM temp_choose_special WHERE deviceId='".$deviceId."'");

	}

   public function delete_device_product($deviceId){
		  $this->db->query("DELETE FROM patient_product_device WHERE deviceId='".$deviceId."'");

	}


	public function update_patient_treatment($id,$deviceId){

		$query=	$this->db->query("UPDATE patient_treatement SET patientId = ".$id." WHERE deviceId='".$deviceId."'");
	}
	
		public function change_password($newpassword,$email){
			$newpassword = $this->hash_password($newpassword);
			$query=$this->db->query("UPDATE user_patient set password='".$newpassword."' WHERE email='".$email."' ");
		}

		
	
	public function update_userid($userid){

		$query=$this->db->query("update patient_product_rel set userId=".$userid." where userId=".$_SESSION["id"]."");
		$query=$this->db->query("update patient_treatement set patientId=".$userid." where patientId=".$_SESSION["id"]."");


	}
	
	public function delete_product($userId,$deviceId,$productId,$type){


		if($type=='new'){
			$this->db->query("delete from patient_product_rel where userId=".$userId." and id=".$productId."");
			return 1;
		}elseif($type=='processing') {
			$this->db->query("delete from patient_product_rel where userId=".$userId." and id=".$productId."");
			$this->db->query("delete from product_request where patientId='".$userId."' and productId=".$productId."");
			return 1;

		}
		elseif($deviceId!=null) {
			$this->db->query("delete from patient_product_device where deviceId='".$deviceId."' and id=".$productId."");
			return 1;

		}
	}


	
	public function my_sheduale($id){

		$query=  $this->db->query("SELECT * from product_response where patientId=".$id."");
		$data=array();
		foreach ($query->result() as $row){
                $row->rating= number_format((rand(0, 50)/10), 1, '.', '');
			
					 $data[]=$row ;
			}
			if(sizeof($data)>0){
			return $data;
		}else{
			return $data=array();
		}
	}
	public function taketreatment_button($id, $date){

		$checker=  $this->db->query("select * from patient_sheduale_detail  where patientId=".$id." and days='".date("Y-m-d")."'")->num_rows();
		$taketreatment=  $this->db->query("select * from patient_sheduale_detail  where patientId=".$id." and days='".$date."' and status=1")->num_rows();
		
		if($checker==0 && date("Y-m-d")==$date){

			return 'yes';
		}elseif($date>date("Y-m-d")){
			return 'pending';

		}elseif($taketreatment>=1){

			return 'complete';
		}else{

			return 'skip';

		}


	}
	public function checkpass($currentpassword,$id){
		
		$query=  $this->db->query("select password from user_patient where userId=".$id."");
		$hash=$query->result()[0]->password;
		 
					if (password_verify($currentpassword, $hash)) {
					    return 1;
					} else {
					      return 0;
					}
	}

	public function change_Pass($newpassword,$id){
			$password= $this->hash_password($newpassword);

		$query=$this->db->query("update user_patient set password='".$password."' where userId=".$id."");

	} 
	public function specialist_detail($id){

		$query=$this->db->query("select sepcial_users.userName,sepcial_users.age,sepcial_users.city,sepcial_users.proPicName,sepcial_users.countary,sepcial_users.email,sepcial_users.yearOfExperience,sepcial_users.gender,sepcial_users.phone,sepcial_users.aboutMe,( select count(specialist_product_rel.barcodesrno) from specialist_product_rel where specialist_product_rel.userId=".$id." ) as productCount,( select count(DISTINCT patient_treatement.patientId) from patient_treatement where patient_treatement.specialId=".$id.") as PatientCount from sepcial_users left join specialist_product_rel on sepcial_users.userId=specialist_product_rel.userId left join patient_treatement on sepcial_users.userId=patient_treatement.specialId where sepcial_users.userId=".$id." limit 0,1");
		//echo $this->db->last_query();die();
		return $query->result();

	}   
	public function request_Again($id){

		    // $this->db->query("update notification set seen=1 where patientId=".$_SESSION['user_id']."");


			$query=$this->db->query("SELECT * FROM patient_product_rel WHERE patient_product_rel.barcodesrno NOT IN (SELECT barcodesrno FROM product_request where patientId=".$id.") and patient_product_rel.userId=".$id."");
			   //echo $this->db->last_query(); die;

			 $query1=$this->db->query("select * from patient_treatement where patientId=".$id."");
			 $specialId=$query1->result();
			 // print_r( $specialId);die();
			 $sepecialId=$specialId[0]->specialId;
			
			foreach ($query->result() as $row){
		
			$query=$this->db->query("INSERT INTO product_request (productId, patientId, specialId,barcodesrno)
			VALUES (".$row->id.",".$id.",".$sepecialId.",'".$row->barcodesrno."')");
			}
			$checker=$this->db->query("select * from notification where patientId=".$id."")->num_rows();
			if($checker==0){

				 $this->db->query("INSERT INTO notification (patientId, specialistId)
						VALUES (".$id.",".$sepecialId.")");
			}

			return $this->db->query("SELECT * from user_patient where userId='".$id."'")->result();

	}
	
	public function my_product_list_req($id)
	{
		$query=$this->db->query("select product_request.type,product_request.productStatus,patient_product_rel.id as productId,bar_code_detail.productName,bar_code_detail.brandName,bar_code_detail.img,bar_code_detail.barcodeSrNo from product_request inner join bar_code_detail on product_request.barcodesrno=bar_code_detail.barcodeSrNo inner join patient_product_rel on product_request.productId=patient_product_rel.id where patientId=".$id." and product_request.barcodesrno not in(select barcodeSrNo from product_response where patientId=".$id.")");
		/**/
		//echo $this->db->last_query(); 
		$data=array();
		foreach ($query->result() as $key=>$value){
			
					 $data[]=$value;

			}
			//print_r($data);
			return $data;
	}
	public function  my_product_list_res($id)
	 {
		$query=$this->db->query("select *,id as productId from product_response where patientId=".$id." and status=1");

		$data=array();
		foreach ($query->result() as $row){
			
					 $data[]=$row;

			}
			//print_r($data);
			return $data;
	}
	public function get_email($id){

		$query=$this->db->query("select email from user_patient where userId=".$id."");
		$data=" ";
		return $data=$query->result();
	}
	public function insert_email_data($id,$subject,$message){


		$query=$this->db->query("INSERT INTO mail_records (userType, subject, message,userId)
			VALUES ('patient','".$subject."','".$message."',".$id.")");
		$insert_id = $this->db->insert_id();
		$query=	$this->db->query("select * from mail_records where id=".$insert_id."");
		return $query->result();
	}
	public function total_product($id){
		$data1=array();
		$data2=array();
		$query1=$this->db->query("SELECT productId,productName,brandName,img,productStatus from admin_request where patientId=".$id."");
		    foreach ($query1->result() as $row)
			{
				
			        $data2[]=$row ;
				
			}
		 $query=$this->db->query("select productId,productName,brandName,img,productStatus from admin_product");
		    foreach ($query->result() as $row)
			{


				
			        	$data1[]=$row ;
			        
				
			}
			
					$finaldata=array_merge($data2,$data1);
					

						$tempArray = array();
						$finalArray = array();
						foreach ($finaldata as $key => $val) {
								if (!in_array($val->productId,$tempArray)) {
									$finalArray[] = $val;
									$tempArray[] = $val->productId;			
								} 
							}
							unset($tempArray);
							
							return sizeof($finalArray);


	
}

	
	public function explore_new_product($limit,$offset,$id){
		$data1=array();
		$data2=array();
		$query1=$this->db->query("SELECT productStatus,barcodeId,barcodeSrno,productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media from admin_request where patientId=".$id."  limit ".$offset.", ".$limit."");
		    foreach ($query1->result() as $row)
			{
                $row->rating= number_format((rand(0, 50)/10), 1, '.', '');
				
			        $data2[]=$row ;
				
			}
		 $query=$this->db->query("select productStatus,barcodeId,barcodeSrno,  productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media from admin_product  limit ".$offset.", ".$limit."");
		    foreach ($query->result() as $row)
			{

                $row->rating= number_format((rand(0, 50)/10), 1, '.', '');

				
			        	$data1[]=$row ;
			        
				
			}
			
					$finaldata=array_merge($data2,$data1);
					

						$tempArray = array();
						$finalArray = array();
						foreach ($finaldata as $key => $val) {
								if (!in_array($val->productId,$tempArray)) {
									$finalArray[] = $val;
									$tempArray[] = $val->productId;			
								} 
							}
							unset($tempArray);
							//print_r($finalArray);
							return $finalArray ;


	
}




public function my_array_unique($array, $keep_key_assoc=false){
    $duplicate_keys = array();
    $tmp = array();       

    foreach ($array as $key => $val){
        // convert objects to arrays, in_array() does not support objects
        if (is_object($val))
            $val = (array)$val;

        if (!in_array($val, $tmp))
            $tmp[] = $val;
        else
            $duplicate_keys[] = $key;
    }

    foreach ($duplicate_keys as $key)
        unset($array[$key]);

    return $keep_key_assoc ? $array : array_values($array);


}





	public function my_product_list_to_req($id){
		 $data=array();
		 $query=	$this->db->query("select patient_product_rel.type,patient_product_rel.productStatus,patient_product_rel.id as productId,bar_code_detail.productName,bar_code_detail.brandName,bar_code_detail.img,bar_code_detail.barcodeSrNo from patient_product_rel INNER JOIN bar_code_detail on patient_product_rel.barcodesrno=bar_code_detail.barcodeSrNo where patient_product_rel.barcodesrno not in(select barcodeSrNo from product_response where patientId=".$id.") and patient_product_rel.barcodesrno not in(select barcodeSrNo from product_request where patientId=".$id.") and userId=".$id."");
		// echo $this->db->last_query();
		    foreach ($query->result() as $row)
			{
                $row->rating= number_format((rand(0, 50)/10), 1, '.', '');
				
			        $data[]=$row ;
				
			}
			return $data;

	}
	public function request_To_Admin($id,$productId){

		$query=	$this->db->query("select barcodeId,barcodeSrNo, productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media from admin_product where productId=".$productId."");
		$product=$query->result();
		//print_r($product);
		 $this->db->query("INSERT INTO admin_request (barcodeId,barcodeSrno,productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media,patientId)
			VALUES ('".$product[0]->barcodeId."','".$product[0]->barcodeSrNo."','".$product[0]->productId."','".$product[0]->productName."', '".$product[0]->brandName."','".$product[0]->img."','".$product[0]->eyes."','".$product[0]->forhead."','".$product[0]->neck."','".$product[0]->allFace."','".$product[0]->everyday."','".$product[0]->onceAweek."','".$product[0]->twiceAweek."','".$product[0]->onceAmonth."','".$product[0]->instruction."','".$product[0]->am."','".$product[0]->pm."','".$product[0]->minute."','".$product[0]->step."','".$product[0]->numberOfstep."','".$product[0]->media."',".$id.")");

		 $query=$this->db->query("select barcodeId,barcodeSrno,productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media from admin_request where productId=".$productId." ");
		 return $query->result();
	}

	public function total_product_search($searchproduct,$id,$eye,$forhead,$neck,$allface){
		$data1=array();
		$data2=array();
		$where="";
		$ww = array();
				if($eye==1){

					$wheres="eyes=1";
					$where="eyes=1 or";
					$ww['eyes']=1;
				}else{
					$where="";
				}
				if($forhead==1){

					$where=$where." or forhead=1 ";
					$ww['forhead']=1;

				}else{

					$where=$where."  ";

				}
				if($neck==1){

					$where=$where." or neck=1";
					$ww['neck']=1;
				}else{
					$where=$where."";

				}
				if($allface==1){
					$ww['allface']=1;
					$where=$where." or allface=1";

				}else{

					$where=$where." ";

				}
				$where="";
				if(!empty($ww)){
					foreach ($ww as $kk => $vv) {
					$where .="$kk = $vv or ";	
					}

				$where ="and (".rtrim($where,' or ').")";
				}

		$query1=$this->db->query("SELECT productId,productName,brandName,img,productStatus from admin_request where (productName LIKE '%".$searchproduct."%' or brandName LIKE '%".$searchproduct."%') $where and patientId=".$id."");
		 $this->db->last_query();
		    foreach ($query1->result() as $row)
			{
				
			        $data2[]=$row ;
				
			}
		 $query=$this->db->query("select  productId,productName,brandName,img,productStatus from admin_product where (productName LIKE '%".$searchproduct."%' or brandName LIKE '%".$searchproduct."%') $where ");
		 $this->db->last_query();
		    foreach ($query->result() as $row)
			{


				
			        	$data1[]=$row ;
			        
				
			}
			
					$finaldata=array_merge($data2,$data1);
					

						$tempArray = array();
						$finalArray = array();
						foreach ($finaldata as $key => $val) {
								if (!in_array($val->productId,$tempArray)) {
									$finalArray[] = $val;
									$tempArray[] = $val->productId;			
								} 
							}
							unset($tempArray);
							
							return sizeof($finalArray);


	
}
	
	public function search_product($searchproduct,$limit,$offset,$id,$eye,$forhead,$neck,$allface){

		  $data1=array();
		$data2=array();
		$where="";
		$ww = array();
				if($eye==1){

					$wheres="eyes=1";
					$where="eyes=1 or";
					$ww['eyes']=1;
				}else{
					$where="";
				}
				if($forhead==1){

					$where=$where." or forhead=1 ";
					$ww['forhead']=1;

				}else{

					$where=$where."  ";

				}
				if($neck==1){

					$where=$where." or neck=1";
					$ww['neck']=1;
				}else{
					$where=$where."";

				}
				if($allface==1){
					$ww['allface']=1;
					$where=$where." or allface=1";

				}else{

					$where=$where." ";

				}
				$where="";
				if(!empty($ww)){
					foreach ($ww as $kk => $vv) {
					$where .="$kk = $vv or ";	
					}

				$where ="and (".rtrim($where,' or ').")";
				}

	//	echo(rtrim($where,' or '));die;
		$query1=$this->db->query("SELECT productStatus,barcodeId,barcodeSrno,productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media
 from admin_request  where (productName LIKE '%".$searchproduct."%' or brandName LIKE '%".$searchproduct."%') $where and patientId=".$id."  limit ".$offset.", ".$limit."");
		//echo $this->db->last_query();
		    foreach ($query1->result() as $row)
			{
				
			        $data2[]=$row ;
				
			}
		 $query=$this->db->query("select productStatus,barcodeId,barcodeSrno,productId,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfstep,media from admin_product  where (productName LIKE '%".$searchproduct."%' or brandName LIKE '%".$searchproduct."%') $where  limit ".$offset.", ".$limit."");
		 //echo $this->db->last_query();
		    foreach ($query->result() as $row)
			{


				
			        	$data1[]=$row ;
			        
				
			}
			
					$finaldata=array_merge($data2,$data1);
					

						$tempArray = array();
						$finalArray = array();
						foreach ($finaldata as $key => $val) {
								if (!in_array($val->productId,$tempArray)) {
									$finalArray[] = $val;
									$tempArray[] = $val->productId;			
								} 
							}
							unset($tempArray);
							//print_r($finalArray);
							return $finalArray ;


	}
	
	
  public function gallery($id,$type){

  		$data['images']=array();
  		$query=$this->db->query("select media,type from gallery where specialistId='".$id."' and ".$type."=1 and ".$type."Status=0 and type=1");
		    foreach ($query->result() as $row)
			{
				
			        $data['images'][]=$row ;
				
			}
			$data['videos']=array();
  		$query1=$this->db->query("select media,type from gallery where specialistId='".$id."' and ".$type."=1 and ".$type."Status=0 and type=0");
		    foreach ($query1->result() as $row)
			{
				
			        $data['videos'][]=$row ;
				
			}
			return $data ; 
  }
  public function check_items($deviceId,$userId){

  	if($userId!=null)
		{
			$query=$this->db->query("select * from patient_product_rel where userId='".$userId."'");
			//echo $this->db->last_query();
			if($query->num_rows()>0){
				return 1;
			}else{
				return 0;
			}

		}
		else
		{

		
			$query=$this->db->query("select * from patient_product_device where deviceId='".$deviceId."'");
			//echo $this->db->last_query();
			if($query->num_rows()>0){
				return 1;
			}else{
				return 0;
			}
		
	}

  }
  public function day_Request($id,$date){


			$this->db->query("INSERT INTO patient_sheduale_detail ( patientId, days,status)
			VALUES ('".$id."','".$date."',1)");
			return 1; 
			


  }
  public function edit_profile($id,$age,$city,$link,$countary,$phone,$gender,$name){
		$this->db->query("update user_patient set age=".$age.",userName='".$name."',city='".$city."',proPicName='".$link."',countary='".$countary."',phone='".$phone."',gender='".$gender."' where userId=".$id."");
		  $query=$this->db->query("select * from user_patient where userId=".$id."");
		   return  $query->result() ;
			
				
	}
	public function check_phone($phone){

		$query=$this->db->query("SELECT * from user_patient where phone='".$phone."'");
		if($query->num_rows()>0){
			return 1;
		}else{
			return 0;
		}
	}
	public function checkemail($email){

					$query=$this->db->query("select * from user_patient where email='".$email."'");
				  if($query->num_rows()>0){

				  	return 1 ;
				  } else
				  {
				  	return 0 ;
				  }
			
			
		}
		public function createdate($id){
  		$data=array();
  		$query=$this->db->query("select createdate from product_response  where patientId='".$id."' and status=1");
  		$data=$query->result();

  		return $data;
  }






   public function inserted_Date($id){

  	$data=array();
  		$query=$this->db->query("select days from patient_sheduale_detail where patientId='".$id."'");
		    foreach ($query->result() as $row)
			{
				
			        $data[]=$row ;
				
			}
			return $data ;
  }
  public function insert_remain_date($result,$id){

  		foreach ($result as $key => $value) {
  			$this->db->query("INSERT INTO patient_sheduale_detail ( patientId, days,status)
			VALUES ('".$id."','".$value."',0)");
  		}



  }
  public function red($id){
  	$data=array();
  		$query=$this->db->query("select days from patient_sheduale_detail where patientId='".$id."' and status=0");
		    foreach ($query->result() as $row)
			{
				
			        $data[]=$row ;
				
			}
			return $data ;
  }
  public function green($id){
  	$data=array();
  		$query=$this->db->query("select days from patient_sheduale_detail where patientId='".$id."' and status=1");
		    foreach ($query->result() as $row)
			{
				
			        $data[]=$row ;
				
			}
			return $data ;
  }
  public function lastinsertday($id){

  	$query=$this->db->query("SELECT max(days) as day FROM `patient_sheduale_detail` WHERE patientId='".$id."'");
  	return $query->result(); 

  }
  public function product_Exists($deviceId){

  	$query=$this->db->query("select * from patient_product_device where deviceId='".$deviceId."'")->num_rows();
  	if($query>0){

  		return 1;
  	}else{

  		return 0;
  	}
  }
  public function get_Specialist_Token($id){
  		$specialist=$this->db->query("select * from patient_treatement where patientId='".$id."'")->result()[0]->specialId;
  	$query=$this->db->query("select * from user_token where userId='".$specialist."' and userType='specialist'");

  	//print_r($query->result());
  		return $query->result() ;
  }
  public function distroy_token($userId,$token,$deviceId){

     	$this->db->query("delete from user_token where userId='".$userId."' and token='".$token."' and userType='patient'");

  }
  public function add_To_MyList($barcodeSrNo,$deviceId,$userId){

		if($userId!=null){

			$barcodeSrNo=explode(",",$barcodeSrNo);
				$notexist=array();
				$exist_bar=array();
				foreach ($barcodeSrNo as $value) {

					$query=$this->db->query("select barcodesrno from patient_product_rel where barcodesrno='".$value."' and userId='".$userId."'");
					if(sizeof($query->result())>0){
					$exist_bar[]=$query->result()[0]->barcodesrno;	
					//print_r($exist_bar);
				    }
				}

						foreach ($barcodeSrNo as  $innerVal) {
								if (in_array($innerVal,$exist_bar)) {	
									
	
								  }else {
												
												$notexist[] = $innerVal;
										}	
						}	

						

			foreach ($notexist as $value) {
				$this->db->query("INSERT INTO patient_product_rel (barcodesrno, userId)
			VALUES ('".$value."','".$userId."')");
			}

		}else{


			$barcodeSrNo=explode(",",$barcodeSrNo);


					$notexist=array();
				    $exist_bar=array();
				foreach ($barcodeSrNo as $value) {

					$query=$this->db->query("select barcodesrno from patient_product_device where barcodesrno='".$value."' and deviceId='".$deviceId."'");
					if(sizeof($query->result())>0){
					$exist_bar[]=$query->result()[0]->barcodesrno;	
					//print_r($exist_bar);
				    }
				}

						foreach ($barcodeSrNo as  $innerVal) {
								if (in_array($innerVal,$exist_bar)) {	
									
	
								  }else {
												
												$notexist[] = $innerVal;
										}	
						}	




			foreach ($notexist as $value) {
				$this->db->query("INSERT INTO patient_product_device (barcodesrno, deviceId)
			VALUES ('".$value."','".$deviceId."')");
			}

		}
	}

	public function get_token($userId){

		//$query1=  $this->db->query("SELECT specialId FROM patient_treatement where patientId='".$userId."'");
		//$id=$query1->result()[0]->specialId;
		$query=  $this->db->query("SELECT * FROM sepcial_users where userId='".$userId."'");
		//print_r($query->result());die();
		return $query->result();

	}
	public function chat_img($link){

		$this->db->query("INSERT INTO chat_image (img)
			VALUES ('".$link."')");
		$insert_id = $this->db->insert_id();
		$query=  $this->db->query("SELECT * FROM chat_image where id='".$insert_id."'");
		return $query->result();
	}
 

}
