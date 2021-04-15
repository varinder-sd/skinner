<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User_model class.
 * 
 * @extends CI_Model
 */
class Special_model extends CI_Model {

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
	public function message(){
		$data=array();
		$query=$this->db->query("select * from alert_message");
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}
			return $data;
	}

	
	private function verify_password_hash($password, $hash) {
		
		return password_verify($password, $hash);
		
	}

	public function show_gallery($search,$userId,$limit,$offset,$type){
				$data=array();
			$query=$this->db->query("select id,media,".$search."Status as status from gallery where specialistId=".$userId."  and type=".$type." limit ".$offset.", ".$limit." ");
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}
			return $data;
		

	  
	}
	public function show_gallery_count($search,$userId,$type){


		$query=$this->db->query("select * from gallery where specialistId=".$userId." and ".$search."=1 and type=".$type."");
		return $query->num_rows();

}
public function insert_email_data($id,$subject,$message){


		$query=$this->db->query("INSERT INTO mail_records (userType, subject, message,userId)
			VALUES ('specialist','".$subject."','".$message."',".$id.")");
		$insert_id = $this->db->insert_id();
		$query=	$this->db->query("select * from mail_records where id=".$insert_id."");
		return $query->result();
	}
	public function get_email($id){

		$query=$this->db->query("select email from sepcial_users where userId=".$id."");
		$data=" ";
		return $data=$query->result();
	}
public function delete_gallery($userId,$search,$id){

	$this->db->query("update gallery set ".$search."=0 where specialistId=".$userId." and id=".$id."");
	return 1;
}
	public  function  member_Id($userId,$memberId){

			$this->db->query("update sepcial_users set memberId=".$memberId." where userId=".$userId."");
		  $query=$this->db->query("select * from sepcial_users where userId=".$userId."");
		   return  $query->result() ;

	}
	public function show_profile($id){
		$query=$this->db->query("select * from sepcial_users where userId=".$id."");
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
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
	public function create_user($userName,$age,$city,$proPicName, $countary, $yearOfExperience,$password,$gender,$phone,$email,$aboutme,$token,$type,$deviceId) {
		
		$fcmUser="s".$phone;
		$data = array(
			'userName'   => $userName,
			'age'   => $age,
			'city'   => $city,
			'proPicName'   => $proPicName,
			'countary'   => $countary,
			'email'      => $email,
			'yearOfExperience' => $yearOfExperience,
			'password'   => $this->hash_password($password),
			'gender'   => $gender,
			'aboutMe'   => $aboutme,
			'phone'   => $phone,
			'type'   => $type,
			'token'   =>$token,
			'fcmUser'   =>$fcmUser
		);
		
		//print_r($data);
		//exit;
		
		$this->db->insert('sepcial_users', $data);
		$insert_id = $this->db->insert_id();

		$insert=$this->db->query("select * from user_token where userId='".$insert_id."'and token='".$token."' and userType='specialist'")->result();


		if(sizeof($insert)>0){
			

		}else{


		$this->db->query("INSERT INTO user_token (userId, token, deviceId,type,userType)
			VALUES ('".$insert_id."','".$token."','".$deviceId."','".$type."','specialist')");
	}


        $query=	$this->db->query("select * from sepcial_users where userId=".$insert_id."");

      return  $query->result();
		
	}
	public function edit_profile($id,$age,$city,$link,$countary,$phone,$gender,$name,$aboutme,$yearOfExperience){
		$this->db->query("update sepcial_users set age=".$age.",userName='".$name."',city='".$city."',proPicName='".$link."',countary='".$countary."',phone='".$phone."',gender='".$gender."',aboutMe='".$aboutme."',yearOfExperience='".$yearOfExperience."' where userId=".$id."");
		  $query=$this->db->query("select * from sepcial_users where userId=".$id."");
		   return  $query->result() ;
			
				
	}
	public function get_user($user_id,$token,$type,$deviceId) {
		
		$this->db->from('sepcial_users');
		$this->db->where('userId', $user_id);
		$insert=$this->db->query("select * from user_token where userId='".$user_id."'and token='".$token."' and userType='specialist'")->result();


		if(sizeof($insert)>0){
			$this->db->query("update sepcial_users set token='".$token."', type='".$type."' where userId=".$user_id."");

		}else{


		$this->db->query("INSERT INTO user_token (userId, token, deviceId,type,userType)
			VALUES ('".$user_id."','".$token."','".$deviceId."','".$type."','specialist')");


		$this->db->query("update sepcial_users set token='".$token."', type='".$type."' where userId=".$user_id."");


		
	}
		return $this->db->get()->row();
		
	}
	public function get_user_id_from_username($username) {
		
		$this->db->select('userId');
		$this->db->from('sepcial_users');
		$this->db->where('email', $username);

		return $this->db->get()->row('userId');
		
	}
	public function resolve_user_login($username, $password) {
		
		$this->db->select('password'); 
		$this->db->from('sepcial_users');
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
	public function reject_barcode($barcodesrno){

		//return $this->db->query("select * from bar_code_detail where barcodesrno=".$barcodesrno."");
		
		$query=$this->db->query("select * from bar_code_detail where barcodeSrNo='".$barcodesrno."' and status=0");
		 return $query->num_rows();
		

	}
	public function check_product($deviceId,$userId,$barcodesrno){

		if($userId!=null)
		{
			$query=$this->db->query("select * from specialist_product_rel where userId='".$userId."' and barcodesrno='".$barcodesrno."' ");
			//echo $this->db->last_query();
			if($query->num_rows()>0){
				return 1;
			}else{
				return 0;
			}

		}
		else
		{

		
			$query=$this->db->query("select * from special_product_device where deviceId='".$deviceId."' and barcodesrno='".$barcodesrno."' ");
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
			//print_r($id);
			$query=$this->db->query("insert into temp_id (id) values (".$id.")");
			$insert_id = $this->db->insert_id();
		 	$query=$this->db->query("select max(id) as id from temp_id");
			 $id=$query->result();
		    $id=$id[0]->id;
			$_SESSION["id"]= $id;


	}
	public function add_product($barcodesrno,$userId){

		$this->db->query("INSERT INTO specialist_product_rel (barcodesrno, userId)
			VALUES ('".$barcodesrno."','".$userId."')");
			return 1;


	}
	public function add_product_device($barcodesrno,$deviceId){
		$this->db->query("INSERT INTO special_product_device (barcodesrno, deviceId)
			VALUES ('".$barcodesrno."','".$deviceId."')");
			return 1;
	}
	public function view_product($id,$deviceId){
			$data=array();
			if($id!=null){

		// $query1=$this->db->query("select specialist_product_rel.*,bar_code_detail.* from specialist_product_rel inner join bar_code_detail on specialist_product_rel.barcodesrno=bar_code_detail.barcodeSrNo  where specialist_product_rel.userId='".$id."' and specialist_product_rel.barcodesrno not in (SELECT barcodeSrNo from product_detail where specialId='".$id."')");
		$query1=$this->db->query("select product.eyes,product.forhead,product.neck,product.allFace, specialist_product_rel.*,bar_code_detail.* from specialist_product_rel inner join bar_code_detail on specialist_product_rel.barcodesrno=bar_code_detail.barcodeSrNo left join admin_product as product on product.barcodeSrNo=specialist_product_rel.barcodesrno where specialist_product_rel.userId='".$id."' and specialist_product_rel.barcodesrno not in (SELECT barcodeSrNo from product_detail where specialId='".$id."')");
		 //echo $this->db->last_query();
		// $query2=$this->db->query("select * from product_detail where specialId='".$id."'");
		$query2=$this->db->query("select product.eyes,product.forhead,product.neck,product.allFace,product_detail.* from product_detail left join admin_product as product on product.barcodeSrNo = product_detail.barcodeSrNo where specialId='".$id."'");

		foreach ($query1->result() as $row){ 
               $row->rating= number_format((rand(0, 50)/10), 1, '.', '');
			array_walk($row, function (&$item) {
				$item = strval($item);
				$item = htmlentities($item);
				$item = html_entity_decode($item);
			});

					 $data[]=$row ;
			}
		foreach ($query2->result() as $row){
               $row->rating= number_format((rand(0, 50)/10), 1, '.', '');
			array_walk($row, function (&$item) {
				$item = strval($item);
				$item = htmlentities($item);
				$item = html_entity_decode($item);
			});

			$data[]=$row ;
		}	
			
		return $data;


	}else{
		// $query=$this->db->query("select special_product_device.*,bar_code_detail.* from special_product_device left join bar_code_detail on special_product_device.barcodesrno=bar_code_detail.barcodeSrNo where special_product_device.deviceId='".$deviceId."'");
		$query=$this->db->query("select product.eyes,product.forhead,product.neck,product.allFace, special_product_device.*,bar_code_detail.* from special_product_device left join bar_code_detail on special_product_device.barcodesrno=bar_code_detail.barcodeSrNo left join admin_product as product on product.barcodeSrNo=special_product_device.barcodesrno  where special_product_device.deviceId='".$deviceId."'");
		foreach ($query->result() as $row){
					   $row->rating= number_format((rand(0, 50)/10), 1, '.', '');
						array_walk($row, function (&$item) {
							$item = strval($item);
							$item = htmlentities($item);
							$item = html_entity_decode($item);
						});
					$data[]=$row ;
			}
			return $data;
		
	}
		//echo $this->db->last_query();
		

	}
	public function update_userid($userid){

		$query=$this->db->query("update specialist_product_rel set userId=".$userid." where userId=".$_SESSION["id"]."");

	}
	public function check_avilable($data){
		//print_r(sizeof($data));

		for($i=0;$i<sizeof($data);$i++){

			//print_r($data[$i]->barcodeId);
			$query=$this->db->query("select * from product_detail where barcodeId=".$data[$i]->barcodeId."");
			if($query->num_rows()>0){
				//echo"hii";
				$data[$i]->check = 1;
			 // array_push($data,'check'=>1);
		   }


		}
		return $data;
	}
	public function view_instruction($specialistId,$barcodeSoNo){

		$query=$this->db->query("select * from product_detail where barcodeSrNo='".$barcodeSoNo."' and specialId='".$specialistId."'");
		if($query->num_rows()>0){

			return $query->result();

		}else{

			$query1=$this->db->query("select * from admin_product where barcodeSrNo='".$barcodeSoNo."'");
			return $query1->result();
		}
		
	}
	public function create_shedual($specialId,$barcodeId,$barcodeSrNo,$productName,$brandName,$eyes,$forhead,$neck,$everyday,$onceAweek,$twiceAweek,$onceAmonth,$instruction,$am,$pm,$minute,$step,$numberOfStep,$media,$img){

			$query1=$this->db->query("select * from product_detail where specialId='".$specialId."' and barcodeSrNo='".$barcodeSrNo."'");
  		if($query1->num_rows()>0){

  			$this->db->query("update product_detail set productName='".$productName."',brandName='".$brandName."',img='".$img."',eyes='".$eyes."',forhead='".$forhead."',neck='".$neck."',everyday='".$everyday."',onceAweek='".$onceAweek."',twiceAweek='".$twiceAweek."',onceAmonth='".$onceAmonth."',instruction='".$instruction."',am='".$am."',pm='".$pm."',minute='".$minute."',step='".$step."',numberOfStep='".$numberOfStep."',media='".$media."' where specialId='".$specialId."' and barcodeSrNo='".$barcodeSrNo."'");

  		}else{

  			$this->db->query("INSERT INTO product_detail (specialId, barcodeId, barcodeSrNo,productName,brandName,img,eyes,forhead,neck,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfStep,media)
			VALUES (".$specialId.",".$barcodeId.", '".$barcodeSrNo."','".$productName."','".$brandName."','".$img."',".$eyes.",".$forhead.",".$neck.",".$everyday.",".$onceAweek.",".$twiceAweek.",".$onceAmonth.",'".$instruction."',".$am.",".$pm.",".$minute.",".$step.",".$numberOfStep.",'".$media."')");

			$this->db->query("delete from specialist_product_rel where userId='".$specialId."' and barcodesrno='".$barcodeSrNo."'");

  		}

			return 1;

	}
	public function check_phone($phone){

		$query=$this->db->query("SELECT * from sepcial_users where phone='".$phone."'");
		if($query->num_rows()>0){
			return 1;
		}else{
			return 0;
		}

	}
	public function total_patient_product($userId,$patientId){

			$query=  $this->db->query("select * from product_response where patientId=".$patientId." and specialistId=".$userId." and status=1");
		return $query->num_rows();

	}

	public function patient_product($userId,$patientId,$limit,$offset){

		$data=array();
		$query=$this->db->query("select * from product_response where patientId=".$patientId." and specialistId=".$userId." and status=1 limit ".$offset.",".$limit."");
		//echo $this->db->last_query();
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}
			return $data;

	}
	public function patient_Concern($patientId,$userId){
		$data=array();
		$query=$this->db->query("SELECT concernType from patient_treatement where patientId='".$patientId."' and specialId='".$userId."'");
		//echo $this->db->last_query();
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}
			return $data;

	}
	public function show_request($limit,$offset,$id){
			$data=array();
		$query=$this->db->query("SELECT notification.*,user_patient.* FROM notification INNER JOIN user_patient ON notification.patientId = user_patient.userId where notification.specialistId=".$id." limit ".$offset.",".$limit." ");
		//echo $this->db->last_query();
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}
			return $data;

	}
	public function total_row($id){
	
		$query=  $this->db->query("SELECT notification.*,user_patient.* FROM notification INNER JOIN user_patient ON notification.patientId = user_patient.userId where notification.specialistId=".$id."");
		return $query->num_rows();
	}
	public function delete_Schduel_Product($barcodeSrNo,$patientId,$status){

		if($status=='approve'){

			$this->db->query("delete from product_response where patientId='".$patientId."' and barcodeSrNo='".$barcodeSrNo."'");
		}else{
			$this->db->query("delete from product_request where patientId='".$patientId."' and barcodesrno='".$barcodeSrNo."'");
		}
		return 1;
	}
	public function check_product_response($patientId,$barcodesrno){

		
		
			$query=$this->db->query("select * from product_request where patientId='".$patientId."' and barcodesrno='".$barcodesrno."' ")->num_rows();
			$query1=$this->db->query("select * from product_response where patientId='".$patientId."' and barcodeSrNo='".$barcodesrno."' ")->num_rows();
			$checker=$query+$query1;
			//echo $this->db->last_query();
			if($checker>0){
				return 1;
			}else{
				return 0;
			}

		
	}
	public function add_product_response($barcodesrno,$userId,$patientId){

		$this->db->query("INSERT INTO product_request ( patientId, specialId,barcodesrno)
			VALUES (".$patientId.",".$userId.",'".$barcodesrno."')");
	}
	public function create_Shedual_For_User($patientId,$specialId){
		$data=array();
		$query=  $this->db->query("select bar_code_detail.*,product_request.productStatus from product_request inner join bar_code_detail on product_request.barcodesrno=bar_code_detail.barcodeSrNo  where product_request.patientId=".$patientId." and product_request.specialId=".$specialId." and product_request.barcodesrno NOT IN(SELECT barcodeSrNo from product_response where patientId=".$patientId.")");
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}

			//print_r($data);die;
			$data1=array();
		$query1=  $this->db->query("SELECT * from product_response where patientId='".$patientId."' ");
		foreach ($query1->result() as $row){
			
					 $data1[]=$row ;
			}
			
			return array_merge($data, $data1);
	}
	/*public function create_Shedual_For_User($patientId,$specialId){
      
			$query=  $this->db->query("select * from product_request where patientId=".$patientId." and specialId=".$specialId."");
			foreach($query->result() as $row){
				 //print_r( $row->barcodeId);
					$query=$this->db->query("select * from product_detail where barcodeSrNo='".$row->barcodesrno."'");
					// print_r( $row->barcodeId);
					 if($query->num_rows()>0){
					 	foreach ($query->result() as $row){
			
									 $data[]=$row ;
							}
							//return $data;
					 }else{

					 	$query=$this->db->query("select * from bar_code_detail where barcodeSrNo='".$row->barcodesrno."'");

						 	foreach ($query->result() as $row){
				
										 $data[]=$row ;
								}
								//return $data;

					 }
			}
			return $data;

	}*/
	
	public function delete_product($userId,$deviceId,$productId){

		
		if($userId!=null){
			$checker=$this->db->query("select * from specialist_product_rel where productId='".$productId."' and userId='".$userId."'");

					  if($checker->num_rows()>0){
					$this->db->query("delete from specialist_product_rel where userId=".$userId." and productId=".$productId."");
					return 1;
				      }else{

				      	$this->db->query("delete from product_detail where specialId=".$userId." and productId=".$productId."");
					return 1;
				      }
		}else {
			$this->db->query("delete from special_product_device where deviceId='".$deviceId."' and productId=".$productId."");
			return 1;

		}
	}
	public function send_response($userId,$patientId){
		$this->db->query("update product_response set status=1 where patientId=".$patientId."");
		$this->db->query("delete from product_request where specialId=".$userId." and patientId=".$patientId."");
		$this->db->query("delete from notification where specialistId=".$userId." and patientId=".$patientId."");
		 return 1;
	}
	public function checkpass($currentpassword,$id){
		
		$query=  $this->db->query("select password from sepcial_users where userId=".$id."");
		$hash=$query->result()[0]->password;
		 
					if (password_verify($currentpassword, $hash)) {
					    return 1;
					} else {
					      return 0;
					}
	}

	public function change_Pass($newpassword,$id){
			$password= $this->hash_password($newpassword);

		$query=$this->db->query("update sepcial_users set password='".$password."' where userId=".$id."");

	}
	public function insert_specialist_rel($userId,$deviceId){

		$query=$this->db->query("select * from special_product_device where deviceId='".$deviceId."'");
		$data=array();
		    foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			foreach ($data as $key => $value) {
			$this->db->query("INSERT INTO specialist_product_rel (barcodesrno, userId)
			VALUES ('".$value->barcodesrno."','".$userId."')");
			}
		    //echo $this->db->last_query();
	}
	public function delete_product_device($deviceId){
		  $this->db->query("DELETE FROM special_product_device WHERE deviceId='".$deviceId."'");

	}
	public function upload_gallery($userId,$link,$eyes,$forhead,$neck,$allFace,$status,$type){

		$this->db->query("INSERT INTO gallery (specialistId,media,type,forhead,eyes,neck,allFace,forheadStatus,eyesStatus,neckStatus,allFaceStatus)
			VALUES ('".$userId."','".$link."','".$type."','".$forhead."','".$eyes."','".$neck."','".$allFace."','".$status."','".$status."','".$status."','".$status."')");
		return 1;

	}
	public function public_private_button($userId,$type,$id,$status){

		$query=$this->db->query("UPDATE gallery set ".$type."Status='".$status."' WHERE specialistId='".$userId."' and id='".$id."' ");
		return 1;

	}
	public function checkemail($email){

					$query=$this->db->query("select * from sepcial_users where email='".$email."'");
				  if($query->num_rows()>0){

				  	return 1 ;
				  } else
				  {
				  	return 0 ;
				  }
			
			
		}
		public function change_password($newpassword,$email){
			echo $newpassword = $this->hash_password($newpassword);die();
			$query=$this->db->query("UPDATE sepcial_users set password='".$newpassword."' WHERE email='".$email."' ");
			//echo $this->db->last_query();
		}
	public function check_items($deviceId,$userId){

  	if($userId!=null)
		{
			$query=$this->db->query("select * from specialist_product_rel where userId='".$userId."'");
			//echo $this->db->last_query();
			if($query->num_rows()>0){
				return 1;
			}else{
				return 0;
			}

		}
		else
		{

		
			$query=$this->db->query("select * from special_product_device where deviceId='".$deviceId."'");
			//echo $this->db->last_query();
			if($query->num_rows()>0){
				return 1;
			}else{
				return 0;
			}
		
	}

  }
  public function add_To_Response($patientId,$barcodesrno,$specialistId){
  	$data=array();
  

  	$query2=$this->db->query("select * from product_response where barcodeSrNo='".$barcodesrno."' and patientId	='".$patientId."'");

  	if(sizeof($query2->result())>0){

  		return $data['res']=$query2->result();
  	}

  	$query1=$this->db->query("select * from product_detail where barcodeSrNo='".$barcodesrno."' and specialId='".$specialistId."'");

  		 if(sizeof($query1->result())>0){

  		return $data['res']=$query1->result();
  	  }  
    $query3=$this->db->query("select * from admin_product where barcodeSrNo='".$barcodesrno."' ");

    if(sizeof($query3->result())>0){

  		return $data['res']=$query3->result();
  	  }  
     $query4=$this->db->query("select * from bar_code_detail where barcodeSrNo='".$barcodesrno."'");
  	
     if(sizeof($query4->result())>0){

  		return $data['res']=$query4->result();
  	  }
  		    
  		 
  		    	

  		    
			
  }
  public function patient_Profile($patientId){

      return $this->db->query("select * from user_patient where userId='".$patientId."'")->result();



  }
  public function set_Response($patientId,$createdate,$barcodeSrNo,$productName,$brandName,$img,$eyes,$forhead,$neck,$everyday,$onceAweek,$twiceAweek,$onceAmonth,$instruction,$am,$pm,$minute,$step,$numberOfStep,$media,$specialistId){
  		$query=$this->db->query("select * from product_response where patientId='".$patientId."' and barcodeSrNo='".$barcodeSrNo."'");

  		if($query->num_rows()>0){

  			$query=$this->db->query("update product_response set productName='".$productName."',brandName='".$brandName."',img='".$img."',eyes='".$eyes."',forhead='".$forhead."',neck='".$neck."',everyday='".$everyday."',onceAweek='".$onceAweek."',twiceAweek='".$twiceAweek."',onceAmonth='".$onceAmonth."',instruction='".$instruction."',am='".$am."',pm='".$pm."',minute='".$minute."',step='".$step."',numberOfStep='".$numberOfStep."',media='".$media."' where patientId='".$patientId."' and barcodeSrNo='".$barcodeSrNo."'");

  		}else{

  		$this->db->query("INSERT INTO product_response (patientId,specialistId,createdate,barcodeSrNo,productName,brandName,img,eyes,forhead,neck,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfStep,media)
			VALUES ('".$patientId."','".$specialistId."','".$createdate."','".$barcodeSrNo."','".$productName."','".$brandName."','".$img."','".$eyes."','".$forhead."','".$neck."','".$everyday."','".$onceAweek."','".$twiceAweek."','".$onceAmonth."','".$instruction."','".$am."','".$pm."','".$minute."','".$step."','".$numberOfStep."','".$media."')");
  		//echo $this->db->last_query();
  	}

  		$query1=$this->db->query("select * from product_detail where specialId='".$specialistId."' and barcodeSrNo='".$barcodeSrNo."'");
  		if($query1->num_rows()==0){

  			$this->db->query("INSERT INTO product_detail(specialId,barcodeSrNo,productName,brandName,img,eyes,forhead,neck,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfStep,media)
			VALUES ('".$specialistId."','".$barcodeSrNo."','".$productName."','".$brandName."','".$img."','".$eyes."','".$forhead."','".$neck."','".$everyday."','".$onceAweek."','".$twiceAweek."','".$onceAmonth."','".$instruction."','".$am."','".$pm."','".$minute."','".$step."','".$numberOfStep."','".$media."')");

  		}


		 return 1;

  }
   public function product_Exists($deviceId){

  	$query=$this->db->query("select * from special_product_device where deviceId='".$deviceId."'")->num_rows();
  	if($query>0){

  		return 1;
  	}else{

  		return 0;
  	}
	
	
  }
  public function total_search($searchbrand){
		$query=	$this->db->query("select brandName from brand_name where brandname LIKE '%".$searchbrand."%'  ");
		return $query->num_rows();
	}
	public function search_explore_Product_List($searchbrand,$limit,$offset){
			
		    $query=	$this->db->query("select img, brandName   from brand_name where brandName LIKE '%".$searchbrand."%'  limit ".$offset.", ".$limit."");
		    foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			return $data;

	}
	public function total_num(){

		$query=  $this->db->query('SELECT brandName FROM brand_name ');
		return $query->num_rows();
	}
	public function explore_Product_List($limit,$offset){

		     $query=	$this->db->query("select  img, brandName from brand_name  limit ".$offset.", ".$limit."");
		    foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			return $data;

	}

	public function add_To_MyList($barcodeSrNo,$deviceId,$userId){

		if($userId!=null){

			$barcodeSrNo=explode(",",$barcodeSrNo);
				$notexist=array();
				$exist_bar=array();
				foreach ($barcodeSrNo as $value) {

					$query=$this->db->query("select barcodesrno from specialist_product_rel where barcodesrno='".$value."' and userId='".$userId."'");
					if(sizeof($query->result())>0){
					$exist_bar[]=$query->result()[0]->barcodesrno;	
					//print_r($exist_bar);
				    }
				}

				foreach ($barcodeSrNo as $value) {

					$query=$this->db->query("select barcodeSrNo from product_detail where barcodeSrNo='".$value."' and specialId='".$userId."'");
					if(sizeof($query->result())>0){
					 $exist_bar[]=$query->result()[0]->barcodeSrNo;	
					}
				}


						foreach ($barcodeSrNo as  $innerVal) {
								if (in_array($innerVal,$exist_bar)) {	
									
	
								  }else {
												
												$notexist[] = $innerVal;
										}	
						}	

						

			foreach ($notexist as $value) {
				$this->db->query("INSERT INTO specialist_product_rel (barcodesrno, userId)
			VALUES ('".$value."','".$userId."')");
			}

		}else{


			$barcodeSrNo=explode(",",$barcodeSrNo);


					$notexist=array();
				    $exist_bar=array();
				foreach ($barcodeSrNo as $value) {

					$query=$this->db->query("select barcodesrno from special_product_device where barcodesrno='".$value."' and deviceId='".$deviceId."'");
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
				$this->db->query("INSERT INTO special_product_device (barcodesrno, deviceId)
			VALUES ('".$value."','".$deviceId."')");
			}

		}
		return 1;
	}


	public function get_Patient_Token($id){

  	$query=$this->db->query("select * from user_token where userId='".$id."' and userType='patient'");
  		return $query->result() ;
  }
  public function get_specialist_name($userId){

  	$query=$this->db->query("select userName from sepcial_users where userId='".$userId."'");
  		return $query->result() ;
  }
   public function distroy_token($userId,$token,$deviceId){

     	$this->db->query("delete from user_token where userId='".$userId."' and token='".$token."' and userType='patient'");

  }
  public function my_clients($userId,$limit,$offset){
  		$data=array();
  		$query=$this->db->query("SELECT user_patient.* from user_patient inner join product_response on user_patient.userId=product_response.patientId where specialistId='".$userId."' GROUP by user_patient.userId limit ".$offset.", ".$limit."");
  		foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			return $data;
  }
   public function my_clients_count($userId){

  	   $query=$this->db->query("SELECT user_patient.* from user_patient inner join product_response on user_patient.userId=product_response.patientId where specialistId='".$userId."'");
  	     return $query->num_rows();
  }
  public function exploreProductListasproduct_total_search($searchbrand){
		$query=	$this->db->query("select * from admin_product where productName LIKE '%".$searchbrand."%'");
		return $query->num_rows();
	}
	public function exploreProductListasproduct_search_explore_Product_List($searchbrand,$limit,$offset){
			
		    $query=	$this->db->query("select *  from admin_product where productName LIKE '%".$searchbrand."%'  limit ".$offset.", ".$limit."");
		    foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			return $data;

	}
	public function exploreProductListasproduct_total_num(){

		$query=  $this->db->query('SELECT brandName FROM admin_product');
		return $query->num_rows();
	}
	public function exploreProductListasproduct_explore_Product_List($limit,$offset){

		     $query=	$this->db->query("select * from admin_product  limit ".$offset.", ".$limit."");
		    foreach ($query->result() as $key=>$row)
			{   
                // tempory rating  
               $row->rating= number_format((rand(0, 50)/10), 1, '.', '');


			        $data[]=$row;
				
			}
			return $data;

	}
	public function showproductbybrand_total_search($searchbrand,$brandName){
		$query=	$this->db->query("select * from admin_product where productName LIKE '%".$searchbrand."%' and brandName='".$brandName."'");
		return $query->num_rows();
	}

	public function showproductbybrand_search_explore_Product_List($searchbrand,$brandName,$limit,$offset){
			
		    $query=	$this->db->query("select *  from admin_product where productName LIKE '%".$searchbrand."%' and brandName='".$brandName."'  limit ".$offset.", ".$limit."");
		    foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			return $data;

	}
	public function showproductbybrand_total_num($brandName){

		$query=  $this->db->query("SELECT brandName FROM admin_product where brandName='".$brandName."'");
		return $query->num_rows();
	}
	public function showproductbybrand_explore_Product_List($brandName,$limit,$offset){

		     $query=	$this->db->query("select * from admin_product where brandName='".$brandName."'  limit ".$offset.", ".$limit."");
		    foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			return $data;

	}
	public function get_token($userId){

		$query=  $this->db->query("SELECT * FROM user_patient where userId='".$userId."'");
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