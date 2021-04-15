<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User_model class.
 * 
 * @extends CI_Model
 */
class User_model extends CI_Model {

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
	public function create_user($username,$age,$city,$countary, $email, $password,$name,$phone,$member) {
		
		
		$data = array(
			'userName'   => $username,
			'age'   => $age,
			'city'   => $city,
			'countary'   => $countary,
			'email'      => $email,
			'password'   => $this->hash_password($password),
			'fullname'   => $name,
			'phone'   => $phone,
			'userRole'   => $member
		);
		
		//print_r($data);
		//exit;
		
		return $this->db->insert('user_admin', $data);
		
	}
	
	/**
	 * resolve_user_login function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @param mixed $password
	 * @return bool true on success, false on failure
	 */
	public function resolve_user_login($username, $password) {
		
		$this->db->select('password');
		$this->db->from('user_admin');
		$this->db->where('userName', $username);
		$hash = $this->db->get()->row('password');
		
		return $this->verify_password_hash($password, $hash);
		
	}
	
	/**
	 * get_user_id_from_username function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @return int the user id
	 */
	public function get_user_id_from_username($username) {
		
		$this->db->select('userId');
		$this->db->from('user_admin');
		$this->db->where('userName', $username);

		return $this->db->get()->row('userId');
		
	}
	
	/**
	 * get_user function.
	 * 
	 * @access public
	 * @param mixed $user_id
	 * @return object the user object
	 */
	public function get_user($user_id) {
		
		$this->db->from('user_admin');
		$this->db->where('userId', $user_id);
		return $this->db->get()->row();
		
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

	public function sub_admin_list($limit,$offset){

		$query=	$this->db->limit($limit,$offset)->get('user_admin');

			//	echo $this->db->last_query();
			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			return $data;
			
	}
	public function Special_product($id){
		$data=array();
		$query=$this->db->query("select * from product_detail where specialId='".$id."'");
		foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			return $data;

  		
	}
	public function total_num(){
		$query=  $this->db->query('SELECT * FROM user_admin');
		return $query->num_rows();
	}
	public function sepcial_user_list($limit,$offset){
		$data=array();
		$query=	$this->db->query("SELECT sepcial_users.*,count(product_detail.specialId) as total from sepcial_users left join product_detail on sepcial_users.userId = product_detail.specialId group by userId limit ".$offset.",".$limit."");
		//echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row;
				
			}
			//print_r($data);
			return $data;


	}
	public function sepcial_user_total_rows(){
		$query=  $this->db->query('SELECT * FROM sepcial_users');
		return $query->num_rows();
	}
	public function patient_list($limit,$offset){
		$data=array();
		$query=	$this->db->query("select * from user_patient limit ".$offset.",".$limit."");
		//echo $this->db->last_query();
		
			foreach ($query->result() as $row)
			{
			        //print_r($row->userId);

					$specialist=$this->db->query("select sepcial_users.userName from patient_treatement inner join sepcial_users on patient_treatement.specialId=sepcial_users.userId where patient_treatement.patientId='".$row->userId."'")->result();
					
					   $concerns=$this->db->query("select concernType from patient_treatement where patientId=".$row->userId."")->result();
						
						$concern='';
						foreach ($concerns as $value) {
							$concern.=$value->concernType.',';
							
						}
// print_r($specialist[0]->userName);
			        $data[]=array('data'=>$row,'specialist'=>$specialist[0]->userName,'concern'=>$concern);
			        



				
			}
			//print_r($data);
			return $data;


	}
	public function patient_user_total_rows(){
		$query=  $this->db->query('select * from user_patient');
		return $query->num_rows();
	}
	public function all_product($limit,$offset){
		$data=array();
		$query= $this->db->query("select * from product_detail where status=0 order by productId desc limit ".$offset.",".$limit." ");
   //echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//print_r($data);
			return $data;

	}
	public function product_rows(){
		$query=  $this->db->query("select * from product_detail where status=0");
		return $query->num_rows();
	}
	public function product_by_patient($limit,$offset){
		$query=	$this->db->limit($limit,$offset)->query("SELECT patient_product.*,user_patient.userName,product_instructions.*,patient_product_media.imgName FROM patient_product INNER JOIN product_instructions ON product_instructions.productId = patient_product.productId INNER JOIN user_patient ON patient_product.userId=user_patient.userId INNER JOIN patient_product_media ON patient_product.productID=patient_product_media.productId");


			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			return $data;

	}
	public function insert_mail($userId,$usertype,$email,$message,$subject){

				$query=$this->db->query("INSERT INTO mail_records (userType,subject,message,userId,forAdmin,seen) VALUES ('".$usertype."','".$subject."','".$message."','".$userId."','admin',0)");
				 $insert_id = $this->db->insert_id();
				return $this->db->query("select * from mail_records where id=".$insert_id."")->result();



	}
	public function product_by_patient_rows(){
		$query=  $this->db->query('SELECT patient_product.*,user_patient.userName,product_instructions.*,patient_product_media.imgName FROM patient_product INNER JOIN product_instructions ON product_instructions.productId = patient_product.productId INNER JOIN user_patient ON patient_product.userId=user_patient.userId INNER JOIN patient_product_media ON patient_product.productID=patient_product_media.productId');
		return $query->num_rows();
	}
	public function show_profile($id,$table){
		$query=$this->db->query("select * from ".$table." where userId=".$id."");
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}
			return $data;
		}
		public function show_profile1($id,$table){
		$query=$this->db->query("select *,(SELECT count(specialId)  from product_request where specialId='".$id."') as totalpatient from  sepcial_users where userId=".$id."");
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}
			return $data;
		}
		public function show_profile2($id,$table){
		$query=$this->db->query("select patient_treatement.*,user_patient.*,sepcial_users.userName as specialist from patient_treatement inner join user_patient on patient_treatement.patientId=user_patient.userId inner join sepcial_users on patient_treatement.specialId=sepcial_users.userId where patient_treatement.patientId=".$id."");
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}
			return $data;
		}

	public function edit_user($id,$age,$gender,$city,$countary,$table,$imgname,$phone){

		$query=$this->db->query("update ".$table." set age=".$age.",gender=".$gender.",city='".$city."',countary='".$countary."',proPicName='".$imgname."' ,phone='".$phone."' where userId=".$id."");
		/*$query=$this->db->query("update sepcial_users set age=60,gender=1,city='jasdfk',countary='bhbnjh' where userId=1");*/

	}
	public function edit_user_witoutpic($id,$age,$gender,$city,$countary,$table,$phone){

		$query=$this->db->query("update ".$table." set age=".$age.",gender=".$gender.",city='".$city."',countary='".$countary."',phone='".$phone."' where userId=".$id."");
		/*$query=$this->db->query("update sepcial_users set age=60,gender=1,city='jasdfk',countary='bhbnjh' where userId=1");*/

	}
	public function active($id){

		$query=$this->db->query("update user_admin set status=1 where userId=".$id."");
	}
	public function deactive($id){
		$logtime=date('Y-m-d H:i:s');
		$query=$this->db->query("update user_admin set status=0,lastLogin='".$logtime."' where userId=".$id."");
	}
	public function add_Concern($concern,$username,$filename=""){


		$filename="/assets/img/concernpics/".$filename;
		$query=$this->db->query("INSERT INTO concerns (concernType, userName,picname)
VALUES ('".$concern."','".$username."','".$filename."')");
	}
	public function concern_rows(){
		$query=  $this->db->query("select * from concerns");
		return $query->num_rows();
	}

	public function concern_List($limit=3,$offset){
			$data=array();
		$query=	$this->db->limit($limit,$offset)->get('concerns');

			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//echo $this->db->last_query();
			return $data ;

	}
	public function concern_delete($id){

		$query=	$this->db->query("delete from concerns where concernId=".$id."");

		}
	public function specialist_delete($id){

		$query=	$this->db->query("delete from sepcial_users where userId=".$id."");

		}
		public function patient_delete($id){

		$query=	$this->db->query("delete from user_patient where userId=".$id."");

		}
		public function subadmin_delete($id){

		$query=	$this->db->query("delete from user_admin where userId=".$id."");

		}
		public function delete_Product($id){

		$query=	$this->db->query("delete from bar_code_detail where barcodeId=".$id."");

		}
		public function special_user_product($id){

			$query=	$this->db->query("select * from product_detail where specialId=".$id."");
					$data=array();
					foreach ($query->result() as $row)
					{
					        $data[]=$row ;
						
					}
					if(sizeof($data)>0){
					return $data ;
				}else{

					return $data[]="NO Product Add";
				}

		}
		public function product_Detail($id,$usertype){
			if($usertype=='specialist'){
			$query=	$this->db->query("select * from product_detail where productId=".$id."");
					$data=array();
					foreach ($query->result() as $row)
					{
					        $data[]=$row ;
						
					}
					
					echo json_encode($data) ;
					die();
				}
				if($usertype=='patient'){

					$query=	$this->db->query("select * from patient_product_rel where id=".$id."");
					$data=array();
					foreach ($query->result() as $row)
					{
					        $data[]=$row ;
						
					}
					
					echo json_encode($data) ;
					die();
				}
		}
		public function   patient_user_product($id){

			$query=	$this->db->query("SELECT patient_product_rel.*,bar_code_detail.*  from patient_product_rel left join bar_code_detail on patient_product_rel.barcodesrno=bar_code_detail.barcodeSrNo where patient_product_rel.userId=".$id."");
					$data=array();
					foreach ($query->result() as $row)
					{
					        $data[]=$row ;
						
					}
					if(sizeof($data)>0){
					return $data ;
				}else{

					return $data[]="NO Product Add";
				}

		}
		public function search_product($barcode){
			$data=array();
			$query=	$this->db->query("select * from bar_code_detail where barcodeSrNo='".$barcode."'");
			return $data=$query->result();
		}
		public function show_media($id){

			$query=	$this->db->query("select productId,media,status from product_detail where productId='".$id."'");
			return $query->result();
			$query=	$this->db->query("select productId,media,status from product_detail where productId='".$id."'");

		}
		public function insert_media($id,$media){

			$this->db->query("INSERT INTO product_video (productId, media)
			VALUES ('".$id."','".$media."')");
			$this->db->query("update product_detail set status=1 where productId=".$id."");
		}
		public function add_To_Explore($img,$barcodeId,$barcodeSrNo,$productName,$brandName,$media,$eyes,$forhead,$neck,$allFace,$everyday,$twiceAweek,$onceAweek,$onceAmonth,$am,$pm,$id,$instruction,$type,$numberOfStep,$step,$minutes){

			if($type=='special'){
			$this->db->query("update product_detail set addToExplore=1 where productId=".$id."");
			$this->db->query("INSERT INTO admin_product (barcodeId,barcodeSrNo,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfStep,media)
			VALUES ('".$barcodeId."','".$barcodeSrNo."','".$productName."','".$brandName."','".$img."','".$eyes."','".$forhead."','".$neck."','".$allFace."','".$everyday."','".$onceAweek."','".$twiceAweek."','".$onceAmonth."','".$instruction."','".$am."','".$pm."','".$minutes."','".$numberOfStep."','".$step."','".$media."')");
		}elseif($type=='new'){


			$this->db->query("INSERT INTO admin_product (barcodeId,barcodeSrNo,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,step,numberOfStep,media)
			VALUES ('".$barcodeId."','".$barcodeSrNo."','".$productName."','".$brandName."','".$img."','".$eyes."','".$forhead."','".$neck."','".$allFace."','".$everyday."','".$onceAweek."','".$twiceAweek."','".$onceAmonth."','".$instruction."','".$am."','".$pm."','".$minutes."','".$numberOfStep."','".$step."','".$media."')");
		}
		else
		{

		 $this->db->query("update admin_product set eyes='".$eyes."',forhead='".$forhead."',neck='".$neck."',allFace='".$allFace."',everyday='".$everyday."',onceAweek='".$onceAweek."',twiceAweek='".$twiceAweek."',onceAmonth='".$onceAmonth."',instruction='".$instruction."',am='".$am."',pm='".$pm."' where productId=".$id."");

		  }
		  $query=$this->db->query("select * from brand_name where brandName='".$brandName."'");

					if ($query->num_rows()==0) {

						$this->db->query("INSERT INTO brand_name (brandName, img)
			VALUES ('".$brandName."','assets/productImg/5c78de7aa8cb3.png')");

						

					}
			

		}
		public function total_count(){
			$notification=array();
			$query1=$this->db->query("select * from product_detail where status=0 and media !='0'");
			$query2=$this->db->query("select * from product_detail where addToExplore=0");
			$query3=$this->db->query("select * from mail_records where seen=1");
			$notification['video']=$query1->num_rows();
			$notification['addToExplore']=$query2->num_rows();
			$notification['total']= $query1->num_rows()+$query2->num_rows();
			$notification['inbox']= $query3->num_rows();
			return $notification;
		}
		public function get_mail($id,$type){
			$data=array();
			$this->db->query("update mail_records set seen=0 where  userId='".$id."' and userType='".$type."'");
			$query=$this->db->query("select * from mail_records where userId='".$id."' and userType='".$type."' order by id ASC");
			foreach ($query->result() as $row)
					{
					        $data[]=$row ;
						
					}
					return $data;

		}


		public function inbox_users_list(){
			$id1=array();
			$id2=array();
			$data['specialist']=array();
			$data['patient']=array();
			$patientId=$this->db->query("select * from mail_records where userType='patient' ORDER BY seen DESC ");
			
			foreach ($patientId->result() as $row)
					{

							$id1[]=$row->userId;
					        
							
					}


					foreach (array_unique($id1) as  $value) {
						$data['patient'][]=array($this->db->query("select * from user_patient where userId='".$value."'")->result(),$this->db->query("select * from mail_records where userId='".$value."' and seen=1  and userType='patient'")->num_rows()) ;
					}
					//print_r(array_unique($id));die();

					$specialId=$this->db->query("select * from mail_records where userType='specialist' ORDER BY seen DESC");


                     foreach ($specialId->result() as $row)
					{

							$id2[]=$row->userId;
					        
							
					}

					foreach (array_unique($id2) as  $value)
					{
					        $data['specialist'][]=array($this->db->query("select * from sepcial_users where userId='".$value."'")->result(),$this->db->query("select * from mail_records where userId='".$value."' and seen=1 and userType='specialist'")->num_rows()) ;
						
					}
					return $data;
		}
		public function video_approvel(){
			$data=array();
			$query=$this->db->query("select * from product_detail where status=0 and media !='0'");
			//echo $this->db->last_query();
			foreach ($query->result() as $row)
					{
					        $data[]=$row ;
						
					}
					return $data;
		}
		public function explore_product(){

			$data=array();
			$query=$this->db->query("select * from product_detail where addToExplore=0");

			foreach ($query->result() as $row)
					{
					        $data[]=$row ;
						
					}
					return $data;

		}
		public function product_Edit_Form($id,$type){

			if($type=='special'){
			$query=$this->db->query("select * from product_detail where productId='".$id."'");
		}
		else{
			$query=$this->db->query("select * from admin_product where productId='".$id."'");

		}
			return $query->result();
		
		}
		public function admin_product($limit,$offset){
			$data=array();
		$query= $this->db->query("select * from admin_product order by productId desc limit ".$offset.",".$limit." ");
   //echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//print_r($data);
			return $data;

	}
	public function admin_product_rows(){
		$query=  $this->db->query("select * from admin_product ");
		return $query->num_rows();
	}
	public function show_Admin_Media($id){


			$query=	$this->db->query("select media from admin_product where productId='".$id."'");
			return $query->result();
			
     }
     public function admin_product_detail($id){

     	$query=	$this->db->query("select * from admin_product where productId=".$id."");
			

			return $query->result();


     }
     public function upload_video($id, $name){

     	$this->db->query("INSERT INTO product_video(productId,media)
			VALUES ('".$id."','".$name."')");

     	$this->db->query("update admin_product set media='".$name."' where productId=".$id."");

     }
     public function update_video($id){

     $query=$this->db->query("select media from admin_product where productId=".$id."");
     	return $query->result();
     }
     public function add_barcode($barcode,$productName,$brandName,$name){


     		$this->db->query("INSERT INTO bar_code_detail (barcodeSrNo,productName,brandName,img)
			VALUES ('".$barcode."','".$productName."','".$brandName."','".$name."')");
			 $insert_id = $this->db->insert_id();

			 $query=$this->db->query("select * from bar_code_detail where barcodeId=".$insert_id."");
          	return $query->result();

     }
     public function dummy_Image(){

     	$query=$this->db->query("select * from dummy_image ");
          	return $query->result();
     }
     public function Img_upload( $link){

     	$this->db->query("INSERT INTO dummy_image(imgName) VALUES ('".$link."')");
     }



     public function search_specialist($search){
     		$data=array();
     		$query=	$this->db->query("SELECT sepcial_users.*,count(product_detail.specialId) as total from sepcial_users left join product_detail on sepcial_users.userId = product_detail.specialId where sepcial_users.userName like '%".$search."%' or sepcial_users.phone like '%".$search."%' group by userId ");
		//echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row;
				
			}
			//print_r($data);
			return $data;



     }
     public function search_patient($search){
     	$data=array();
     	$query=	$this->db->query("select patient_treatement.*,user_patient.*,sepcial_users.userName as specialist from patient_treatement inner join user_patient on patient_treatement.patientId=user_patient.userId inner join sepcial_users on patient_treatement.specialId=sepcial_users.userId where user_patient.userName like '%".$search."%' or user_patient.phone like '%".$search."%'");
		//echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row;
				
			}
			//print_r($data);
			return $data;

     }
    public function search_member($search){
    		$data=array();
    	$query=	$this->db->query("select * from user_admin where userName like '%".$search."%' or phone like '%".$search."%'");

			//	echo $this->db->last_query();
			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			return $data;
    }
    public function search_product_rows($search){

    	$query=  $this->db->query("select * from product_detail where barcodeSrNo like '%".$search."%' or productName like '%".$search."%' or brandName like '%".$search."%'");
		return $query->num_rows();
    }
    public function search_all_product($limit,$offset,$search){
    		$data=array();
    		$query= $this->db->query("select * from product_detail where barcodeSrNo like '%".$search."%' or productName like '%".$search."%' or brandName like '%".$search."%' order by productId desc limit ".$offset.",".$limit." ");
   //echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//print_r($data);
			return $data;
    }

    public function search_admin_product_rows($search){

    	$query=  $this->db->query("select * from admin_product where barcodeSrNo like '%".$search."%' or productName like '%".$search."%' or brandName like '%".$search."%'");
		return $query->num_rows();
    }
    public function search_admin_product($limit,$offset,$search){
    		$data=array();
    		$query= $this->db->query("select * from admin_product where barcodeSrNo like '%".$search."%' or productName like '%".$search."%' or brandName like '%".$search."%' order by productId desc limit ".$offset.",".$limit." ");
   //echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//print_r($data);
			return $data;
    }
    public function add_instruction($img,$barcodeId,$barcodeSrNo,$productName,$brandName,$eyes,$forhead,$neck,$allFace,$everyday,$twiceAweek,$onceAweek,$onceAmonth,$am,$pm,$instruction,$minute,$name){
    		if($name=='empty'){
    	   $this->db->query("INSERT INTO admin_product (barcodeId,barcodeSrNo,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute)
			VALUES ('".$barcodeId."','".$barcodeSrNo."','".$productName."','".$brandName."','".$img."','".$eyes."','".$forhead."','".$neck."','".$allFace."','".$everyday."','".$onceAweek."','".$twiceAweek."','".$onceAmonth."','".$instruction."','".$am."','".$pm."','".$minute."')");
			}else{

				 $this->db->query("INSERT INTO admin_product (barcodeId,barcodeSrNo,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,media)
			VALUES ('".$barcodeId."','".$barcodeSrNo."','".$productName."','".$brandName."','".$img."','".$eyes."','".$forhead."','".$neck."','".$allFace."','".$everyday."','".$onceAweek."','".$twiceAweek."','".$onceAmonth."','".$instruction."','".$am."','".$pm."','".$minute."','".$name."')");
			 $insert_id = $this->db->insert_id();
			 return $insert_id;
			} 

			$query=$this->db->query("select * from brand_name where brandName='".$brandName."'");

					if ($query->num_rows()==0) {

						$this->db->query("INSERT INTO brand_name (brandName, img)
			VALUES ('".$brandName."','assets/productImg/5c78de7aa8cb3.png')");

						

					}

    }


    public function product_For_Approvel($limit,$offset){
		$data=array();
		$query= $this->db->query("select * from bar_code_detail where status=2  limit ".$offset.",".$limit." ");
   //echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//print_r($data);
			return $data;

	}
	public function approved($limit,$offset){
		$data=array();
		$query= $this->db->query("select bar_code_detail.*,admin_product.productId from bar_code_detail left join admin_product on bar_code_detail.barcodeSrNo=admin_product.barcodeSrNo where bar_code_detail.status=1 order by admin_product.productId 
  limit ".$offset.",".$limit." ");
   //echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//print_r($data);
			return $data;

	}
	public function disapproved($limit,$offset){
		$data=array();
		$query= $this->db->query("select * from bar_code_detail where status=0  limit ".$offset.",".$limit." ");
   //echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//print_r($data);
			return $data;

	}
	public function search_product_For_Approvel($search,$limit,$offset){
		$data=array();
		$query= $this->db->query("select * from bar_code_detail  where status=2 and ( productName LIKE '%".$search."%' or brandName LIKE '%".$search."%')  limit ".$offset.",".$limit." ");
   //echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//print_r($data);
			return $data;

	}
	public function searchapproved($search,$limit,$offset){
		$data=array();
		$query= $this->db->query("select bar_code_detail.*,admin_product.productId from bar_code_detail left join admin_product on bar_code_detail.barcodeSrNo=admin_product.barcodeSrNo where bar_code_detail.status=1
 and ( bar_code_detail.productName LIKE '%".$search."%' or bar_code_detail.brandName LIKE '%".$search."%')  limit ".$offset.",".$limit." ");
   //echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//print_r($data);
			return $data;

	}
	public function searchdisapproved($search,$limit,$offset){
		$data=array();
		$query= $this->db->query("select * from bar_code_detail  where status=0 and ( productName LIKE '%".$search."%' or brandName LIKE '%".$search."%')  limit ".$offset.",".$limit." ");
   //echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//print_r($data);
			return $data;

	}
	public function product_For_Approvel_row(){
		$query=  $this->db->query("select * from bar_code_detail where status=2");
		return $query->num_rows();
	}
	public function approved_row(){
		$query=  $this->db->query("select bar_code_detail.*,admin_product.productId from bar_code_detail left join admin_product on bar_code_detail.barcodeSrNo=admin_product.barcodeSrNo where bar_code_detail.status=1
");
		return $query->num_rows();
	}
	public function disapproved_row(){
		$query=  $this->db->query("select * from bar_code_detail where status=0");
		return $query->num_rows();
	}
	public function search_product_For_Approvel_row($search){
		$query=  $this->db->query("select * from bar_code_detail where status=2 and ( productName LIKE '%".$search."%' or brandName LIKE '%".$search."%') ");
		return $query->num_rows();
	}
	public function searchapproved_row($search){
		$query=  $this->db->query("select bar_code_detail.*,admin_product.productId from bar_code_detail left join admin_product on bar_code_detail.barcodeSrNo=admin_product.barcodeSrNo where bar_code_detail.status=1
 and ( bar_code_detail.productName LIKE '%".$search."%' or bar_code_detail.brandName LIKE '%".$search."%') ");
		return $query->num_rows();
	}
	public function searchdisapproved_row($search){
		$query=  $this->db->query("select * from bar_code_detail where status=0 and ( productName LIKE '%".$search."%' or brandName LIKE '%".$search."%') ");
		return $query->num_rows();
	}
	public function status($val,$checkid){

		$this->db->query("update bar_code_detail set status=".$val." where barcodeId=".$checkid."");
		$query=	$this->db->query("select status from bar_code_detail where barcodeId=".$checkid."");
		//echo $this->db->last_query();

			return $query->result();

	}
	public function search_bybarcode_row($search){

    	$query=  $this->db->query("select * from bar_code_detail where barcodeSrNo like '%".$search."%' or productName like '%".$search."%' or brandName like '%".$search."%'");
		return $query->num_rows();
    }
     public function search_by_barcode($limit,$offset,$search){
    		$data=array();
    		$query= $this->db->query("select * from bar_code_detail where barcodeSrNo like '%".$search."%' or productName like '%".$search."%' or brandName like '%".$search."%'  limit ".$offset.",".$limit." ");
   //echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//print_r($data);
			return $data;
    }
    public function checkpass($currentpassword,$id){
		
		$query=  $this->db->query("select password from user_admin where userId=".$id."");
		$hash=$query->result()[0]->password;
		 
					if (password_verify($currentpassword, $hash)) {
					    return 1;
					} else {
					      return 0;
					}
	}
	public function change_Pass($newpassword,$id){
			$password= $this->hash_password($newpassword);

		$query=$this->db->query("update user_admin set password='".$password."' where userId=".$id."");

	}
	public function del_dum_img($id){

		$query=	$this->db->query("delete from dummy_image where imgId=".$id."");
	}
	public function gallery($id){


	$data['vedio']=array();
	$data['image']=array();
		$query1= $this->db->query("select gallery.*,sepcial_users.userName from gallery left join sepcial_users on  gallery.specialistId=sepcial_users.userId where specialistId= ".$id." and gallery.type=1");
        //echo $this->db->last_query();
		 $query2= $this->db->query("select gallery.*,sepcial_users.userName from gallery left join sepcial_users on  gallery.specialistId=sepcial_users.userId where specialistId= ".$id." and gallery.type=0");

		 	foreach ($query2->result() as $row)
			{
			        $data['vedio'][]=$row ;
				
			}

			foreach ($query1->result() as $row)
			{
			        $data['image'][]=$row ;
				
			}
			//print_r($data);
			return $data;
	}
	public function export(){


					$SQL =  $this->db->query("SELECT  * from bar_code_detail");
					$header = '';
					$result ='';
					$exportData = $SQL->result() or die ( "Sql error : " . mysql_error( ) );
					 
					foreach ($SQL->list_fields() as $field)
					{
						$header .= $field ;
					   
					}
					// print_r($header);die;

					
					   
					    foreach( $SQL->result() as $value )
					    {     
					   
					    $result  .= $value->barcodeId	 . "\t";
					     $result  .= $value->barcodeSrNo. "\t";
					      $result  .= $value->productName . "\t";
					       $result  .= $value->brandName . "\t";
					        $result  .=$value->img . "\t";
					         $result  .= $value->status	 ;
					          $result  .="\n";
					}
					
					 
					if ( $result == "" )
					{
					    $result = "\nNo Record(s) Found!\n";                        
					}
					 
					header("Content-type: text/x-csv");
                   header("Content-Disposition: attachment; filename=export.csv");
					echo "$header\n$result";
	
       }
       public function admin_Import($data){





       //	print_r($data);die();
       	$exist=array();
        $notexist=array();
        $temp=array();

        $query=$this->db->query("select barcodeSrNo from bar_code_detail");
        $checker=$query->result();
                /*  print_r(sizeof($checker));
                print_r($data);die();*/
                //$count=0;
        foreach ($checker as $value) {
            //print_r($value);
            //$counter=0;
            foreach ($data as $val) {
                    //print_r($val);
                    if($value->barcodeSrNo==$val[0]){
                        $exist[]=$val;
                    }
                        
            }
                        
        }
      	
      				$temp=array();             
					foreach ($exist as  $val) {

						$temp[]=$val[0];
					}
						foreach ($data as  $innerVal) {
								if (in_array($innerVal[0],$temp)) {	
									
	
								  }else {
												
												$notexist[] = $innerVal;
										}	
						}		
							
		/*
       	print_r($notexist);

       	echo"exit";
          print_r(sizeof($exist));
						die();*/
			if(sizeof($notexist)>0){
            for ($i=1; $i<sizeof($notexist) ; $i++) { 
/*
            			if (base64_decode($notexist[$i][3], true)) {

							$current=base64_decode($notexist[$i][3]);
							$file = uniqid().'.png';
							$current .= "/";
						    file_put_contents('/home/skinner/public_html/assets/productImg/'.$file,$current);
						    $link='assets/productImg/'.$file;
						}else{

							$link='';
						}*/

						 $link='assets/productImg/'.$notexist[$i][3];
						


            	//print_r($notexist[$i][0]);
            	$query=$this->db->query("INSERT INTO bar_code_detail (barcodeSrNo, productName,brandName,img,status)
                 VALUES ('".$notexist[$i][0]."','".$notexist[$i][1]."','".$notexist[$i][2]."','".$link."',1)");

            	$everyday=0;
            	$onceAweek=0;
            	$twiceAweek=0;
            	$onceAmonth=0;
            	$am=0;
            	$pm=0;

            	switch ($notexist[$i][10]) {
							    case 1:
							        $am=1;
							        break;
							    case 2:
							       $pm=1;
							        break;

	    
				}

            	switch ($notexist[$i][8]) {
							    case 1:
							       $everyday=1;
							        break;
							    case 2:
							       $onceAweek=1;
							        break;
							    case 3:
							       $twiceAweek=1;
							        break;
							    case 4:
							        $onceAmonth=1;
							        break;
				}
            	$query=$this->db->query("INSERT INTO admin_product (barcodeId, barcodeSrNo,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,media,price,step,numberOfStep)
                 VALUES (1,'".$notexist[$i][0]."','".$notexist[$i][1]."','".$notexist[$i][2]."','".$link."','".$notexist[$i][4]."','".$notexist[$i][5]."','".$notexist[$i][6]."','".$notexist[$i][7]."','".$everyday."','".$onceAweek."','".$twiceAweek."','".$onceAmonth."','".$notexist[$i][9]."','".$am."','".$pm."','".$notexist[$i][11]."','".$notexist[$i][12]."','".$notexist[$i][13]."','".$notexist[$i][14]."','".$notexist[$i][15]."')");


            	/*$query=$this->db->query("INSERT INTO product_detail(specialId,barcodeId, barcodeSrNo,productName,brandName,img,eyes,forhead,neck,allFace,everyday,onceAweek,twiceAweek,onceAmonth,instruction,am,pm,minute,media,status,addToExplore)
                 VALUES (0,1,'".$notexist[$i][0]."','".$notexist[$i][1]."','".$notexist[$i][2]."','".$link."','".$notexist[$i][4]."','".$notexist[$i][5]."','".$notexist[$i][6]."','".$notexist[$i][7]."','".$everyday."','".$onceAweek."','".$twiceAweek."','".$onceAmonth."','".$notexist[$i][9]."','".$am."','".$pm."','".$notexist[$i][11]."','".$notexist[$i][12]."',1,1)");*/

            }
        }
        //print_r($exist);
        //echo(sizeof($notexist));die;
        $restult=array('data'=>$exist,'insert'=>sizeof($notexist)-1);


        	$exist=array();
        $notxist=array();
        $temp=array();
        
        $query=$this->db->query("select brandName from brand_name");
        $checker=$query->result();
               //   print_r($checker);
                //print_r($data);die();
                //$count=0;
        foreach ($checker as $value) {
            //print_r($value);
            //$counter=0;
            foreach ($data as $val) {
                    //print_r($val);
                    if($value->brandName==$val[2]){
                        $exist[]=$val;
                    }
                        
            }
                        
        }
      	
      				$temp=array();             
					foreach ($exist as  $val) {

						$temp[]=$val[2];
					}
						foreach ($data as  $innerVal) {
								if (in_array($innerVal[2],$temp)) {	
									
	
								  }else {
												
												$notxist[] = $innerVal;
										}	
						}
						$bradname=array();
						for ($i=1; $i<sizeof($notxist) ; $i++) {

							$bradname[]=$notexist[$i][2];	

						}
							
            	foreach (array_unique($bradname) as $value) {
            		
            	 $this->db->query("INSERT INTO brand_name (brandName, img)
			VALUES ('".$value."','/icon-brand-normal.png')");
            	}
            return $restult;
            
          
	}
	public function mearge_product($data){
		foreach ($data as $value) {
			
		        $everyday=0;
            	$onceAweek=0;
            	$twiceAweek=0;
            	$onceAmonth=0;
            	$am=0;
            	$pm=0;

            	switch ($value[10]) {
							    case 1:
							        $am=1;
							        break;
							    case 2:
							       $pm=1;
							        break;
							    
				}

            	switch ($value[8]) {
							    case 1:
							       $everyday=1;
							        break;
							    case 2:
							       $onceAweek=1;
							        break;
							    case 3:
							       $twiceAweek=1;
							        break;
							    case 4:
							        $onceAmonth=1;
							        break;
				}
				if (base64_decode($value[3], true)) {

							$current=base64_decode($value[3]);
							$file = uniqid().'.png';
							$current .= "/";
						    file_put_contents('/home/skinner/public_html/assets/productImg/'.$file,$current);
						    $link='assets/productImg/'.$file;
						}else{

							$link=$proPicName;
						}
			
				//print_r($value);
				$query=$this->db->query("update bar_code_detail set productName='".$value[1]."',brandName='".$value[2]."',img='".$link."',status=1 where barcodeSrNo='".$value[0]."'");


				$query=$this->db->query("update admin_product set productName='".$value[1]."',brandName='".$value[2]."',img='".$link."',eyes='".$value[4]."',forhead='".$value[5]."',neck='".$value[6]."',allFace='".$value[7]."',everyday='".$everyday."',onceAweek='".$onceAweek."',twiceAweek='". $twiceAweek."',onceAmonth='".$onceAmonth."',instruction='".$value[9]."',am='".$am."',pm='".$pm."',minute='".$value[11]."',media='".$value[12]."',price='".$value[13]."' where barcodeSrNo='".$value[0]."'");

				$query=$this->db->query("update product_detail set productName='".$value[1]."',brandName='".$value[2]."',img='".$link."',eyes='".$value[4]."',forhead='".$value[5]."',neck='".$value[6]."',allFace='".$value[7]."',everyday='".$everyday."',onceAweek='".$onceAweek."',twiceAweek='". $twiceAweek."',onceAmonth='".$onceAmonth."',instruction='".$value[9]."',am='".$am."',pm='".$pm."',minute='".$value[11]."',media='".$value[12]."',status=1,addToExplore=1 where barcodeSrNo='".$value[0]."'");


			}

			return 1; 
	}


	public function alert_message(){
		$data=array();
		$query=  $this->db->query("select * from alert_message ");

		foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//print_r($data);
			return $data;
	}
	public function update_message($message,$id){

		$this->db->query("update alert_message set message='".$message."' where id=".$id."");
	}
	public function edit_barcode($id){

		$query=  $this->db->query("select * from bar_code_detail where barcodeId=".$id." ");

		return $query->result();

	}
	public function edit_barcode_submit($productName,$img,$brandName,$barcodeSrNo){
			$this->db->query("update bar_code_detail set productName='".$productName."',brandName='".$brandName."',img='".$img."' where barcodeSrNo='".$barcodeSrNo."'");

				$this->db->query("update product_detail set productName='".$productName."',brandName='".$brandName."',img='".$img."' where barcodeSrNo='".$barcodeSrNo."'");
				$this->db->query("update admin_product set productName='".$productName."',brandName='".$brandName."',img='".$img."' where barcodeSrNo='".$barcodeSrNo."'");

	}
	public function change_status($status,$barcodeId){

		$this->db->query("update bar_code_detail set status='".$status."' where barcodeId=".$barcodeId."");
	}
	public function show_gallery_count($search,$userId,$type){

		$query=$this->db->query("select * from gallery where specialistId=".$userId." and ".$search."=1 and type=".$type."");
		return $query->num_rows();

   }
   public function show_gallery($search,$userId,$limit,$offset,$type){
				$data=array();
			$query=$this->db->query("select * from gallery where specialistId=".$userId." and ".$search."=1 and type=".$type." limit ".$offset.", ".$limit." ");
		foreach ($query->result() as $row){
			
					 $data[]=$row ;
			}
			return $data;
		

	  
	}
	public function allbrand_total_rows(){

		$query=$this->db->query("select * from brand_name ");
		return $query->num_rows();

	}
	public function allbrand_list($limit,$offset){


        $data=array();
		$query= $this->db->query("select * from brand_name  limit ".$offset.",".$limit." ");
   //echo $this->db->last_query();

			foreach ($query->result() as $row)
			{
			        $data[]=$row ;
				
			}
			//print_r($data);
			return $data;


	}
	public function edit_brand_submit($oldbrand,$img,$brandName){

			$this->db->query("update brand_name set brandName='".$brandName."',img='".$img."' where brandName='".$oldbrand."'");
			$this->db->query("update admin_product set brandName='".$brandName."' where brandName='".$oldbrand."'");
			$this->db->query("update admin_request set brandName='".$brandName."' where brandName='".$oldbrand."'");

	}
}

      
    



 