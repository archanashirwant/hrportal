<?php

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Microsoft\Graph\Core\GraphConstants;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class UserController extends Controller
{

    public function add(){	
		
		 $tokenCache = new TokenCache();
		 $accessToken = $tokenCache->getAccessToken();

		if(empty($accessToken)){
			$_SESSION['errors']= 'Invald Token or token expired signin again';
			$tokenCache->clearTokens();
			$this->render("index");
			return false;
		}

		if(!empty($_POST)) {	 			
			
			$mail = trim($_POST['displayName']).trim($_POST['domain']);

			$extraInfo= '{
				"userPrincipalName": "'.$mail.'", 
				"mail":"'.$mail.'", 
				"mailNickname":"'.$_POST['displayName'].'",
				"companyName":"Multibank Group",
				"passwordProfile": {                
							"password": "Mexgroup1"            
					},
				"accountEnabled": true
				}';
			$extraArray = json_decode($extraInfo,true);
			$manager = $_POST['manager'];
			unset($_POST['manager']);
			unset($_POST['domain']);
			$postArray = $_POST;
		
			$result = array_merge($postArray,$extraArray);
			$userData = json_encode($result);		

			$tokenCache = new TokenCache();
			$accessToken = $tokenCache->getAccessToken();			

			try {
					// Create a Graph client
					$graph = new Graph();
					$graph->setAccessToken($accessToken);
					//Create New User
					$newuser = $graph->createRequest("POST", "/users")
									 ->attachBody($userData)
									 ->execute();
					//Assign manager to newly created user
					$setManager = $graph->createRequest("PUT", "/users/".$mail."/manager/\$ref")
									  ->attachBody('{"@odata.id": "https://graph.microsoft.com/v1.0/users/'.$manager.'"}')
									  ->execute();
					$_SESSION['success'] = 'User Created successfully';
					$this->sendApprovalMail($mail);
				
			} catch (GuzzleHttp\Exception\ClientException $e) {
					$errorMessage = json_decode((string) $e->getResponse()->getBody());
					print_r($_POST);
					$_SESSION['errors']= $errorMessage->error->message.$mail;				
						
			}
   	  }
	  else {
	  		
			$this->render("add");
			return false;
	  }
	   $this->render("add");	

	}

	public function block() {
		if(isset($_POST['mail'])){
			
				$tokenCache = new TokenCache();
				$accessToken = $tokenCache->getAccessToken();

				if(empty($accessToken)){
					$_SESSION['errors']= 'Invald Token or token expired signin again';
					$this->render("index");
					return false;
				}
			try {
				// Create a Graph client
				$graph = new Graph();
				$graph->setAccessToken($accessToken);

				$userToBlock = $graph->createRequest("PATCH", "/users/".$_POST['mail'])
									->attachBody('{"accountEnabled": false}')
									->execute();

				$_SESSION['success'] = 'User '.$_POST['mail'].' blocked';					

			} catch (GuzzleHttp\Exception\ClientException $e) {
					$errorMessage = json_decode((string) $e->getResponse()->getBody());
					$_SESSION['errors']= $errorMessage->error->message;									
			}
							
		}else{
			
			$this->render("block");
			return false;
		}
		$this->render("block");							 

	}
	public function sendApprovalMail($usermail){

		// Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer(true);

		try {
			//Server settings
			$mail->SMTPDebug = 0;                      // Enable verbose debug output
			$mail->isSMTP();                                            // Send using SMTP
			$mail->Host       = 'smtp.office365.com';                    // Set the SMTP server to send through
			$mail->SMTPAuth   =  true;                                   // Enable SMTP authentication
			$mail->Username   = 'azure_ad-notification@multibankfx.com';            // SMTP username
			$mail->Password   = 'Changes!23';                               // SMTP password
			$mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

			//Recipients
			$mail->setFrom('azure_ad-notification@multibankfx.com', 'Mailer');
			$mail->addAddress('archana.shirwant@multibankfx.com', 'CTO');     // Add a recipient
			
			$mail->Subject = 'User Approval';
			$mail->isHTML(true);
			$mail->Body    = 'User with following details has need created <br>';
			$mail->Body    .= 'Name:'.$_POST['givenName'].' '.$_POST['surname'].'   Email'.$usermail;
			$mail->send();
			
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}

	}

    public function display() {
		$this->render("index");			
    }

	
}