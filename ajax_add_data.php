<?php 
session_start();
$conn = new mysqli("localhost","root","","test2");

// Check connection
if ($conn -> connect_errno) {
  echo "Failed to MySQL: " . $conn -> connect_error;
  exit();
}


$action =  $_REQUEST['action'];

if($action=='insert_auth') // insert record and authentication  and post data to url 
{
//fetch the data from post
$fname = $_REQUEST['fname']; 
$lname = $_REQUEST['lname']; 
$phone = $_REQUEST['phone']; 
$email = $_REQUEST['email']; 

//insert query 
$insert_data = "insert into user(fname,lname,phone,email,db_add_date) values('".$fname."','".$lname."','".$phone."','".$email."',now())" ;

if ($conn->query($insert_data) === TRUE) {
  //echo "1";
  	$user_id = $conn -> insert_id;
  	$curl = curl_init();
	$auth_data = array(
		'client_id' 		=> 'testclient',
		'client_secret' 	=> '7u3XQnMDYPjFEQPywCOm4i10gisyMJ26sl',
		'grant_type' 		=> 'client_credentials'
	);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, false);	
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $auth_data);
	curl_setopt($curl, CURLOPT_URL, 'https://oauth2.cognitaschoolsstaging.co.uk/token.php');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	$result = curl_exec($curl);
	if(!$result)
    {
   	  	//die("Connection Failure". curl_error($curl));
   	  	echo "100";  //Connection Failure.
   	  	curl_close($curl);	
   	}
   	else {


   		 //read the access token 
   		$jsonArrayResponse = json_decode($result);   		

   		if(array_key_exists("access_token", $jsonArrayResponse))
   		{
   			$_SESSION['access_token'] = $jsonArrayResponse->access_token;

   			//call the post request    		

   			// API URL
			$url = 'https://oauth2.cognitaschoolsstaging.co.uk/post.php';

			// Create a new cURL resource
			$ch = curl_init($url);

			// Setup request to send json via POST
			$post_data =  array('id'=>$user_id,'fname'=>$fname,'lname'=>$lname,'phone'=>$phone,'email'=>$email);
			$data = json_encode(array("user" => $post_data));

			// Attach encoded JSON string to the POST fields
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

			// Set the content type to application/json
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			// Execute the POST request
			$result_post = curl_exec($ch);

			if($result_post)
    		{
    			echo "101";  //post data successs
    		}

			// Close cURL resource
			curl_close($ch);
   		}
   		else if(array_key_exists("error", $jsonArrayResponse)){
   			echo "102";   			
   		} 			

   		///


   	}
	

	//echo  "<pre>";
	//print_r($jsonArrayResponse);
	//echo "</pre>";
	//echo $jsonArrayResponse->error;


} else {  
  		echo "-100";// error while inserting record into system
	}
}


else if($action=='get_data') // get using oauth 
{

	function getdata($url) 
	{ 	    
	    $ch = curl_init(); 
	    curl_setopt($ch, CURLOPT_URL,$url); 
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));	     
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	    
	    $result = curl_exec ($ch); 
	    curl_close ($ch); 
	    return $result; 
	} 

	if(isset($_SESSION['access_token'])) 
	{
	$result=getdata("https://oauth2.cognitaschoolsstaging.co.uk/get.php");	

	///decode Json
	 $data = json_decode($result,true);
	 $total=count($data);
	 if($total>=1)
	 {
	 	?>
	 	<table class="table table-bordered">
	    <thead>
	      <tr>
	        <th>Sr.No</th>
	        <th>First Name</th>
	        <th>Last Name</th>
	        <th>Phone</th>
	        <th>Email</th>
	      </tr>
	    </thead>
	    <tbody>
	    <?php 
	    $i=0;
	    foreach ($data as $value)
        { 
        	?>
        	<tr>
	        	<td><?php echo $i;?></td>
	        	<td><?php echo $value['fname'];?></td>
	        	<td><?php echo $value['lname'];?></td>
	        	<td><?php echo $value['phone'];?></td>
	        	<td><?php echo $value['email'];?></td>
      		</tr>
      	<?php 
      	$i++;
        }
        ?>
         </tbody>
  		 </table>
  	<?php 
	 }  
	}
	else {

		echo "No record found.";
	}
}

else if($action=='check_exists_email')
{
  $email = $_REQUEST['email'];

  $result = $conn->query("SELECT user_id FROM user where email='".$email."'");
  $row_cnt = $result->num_rows;
  if($row_cnt>=1)
  {
  	echo "1";  //we can convert into error object and on main page using stringify we can read 
  }
  else {
  	echo "0";
  }
}

?>
