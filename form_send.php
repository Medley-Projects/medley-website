<?php
if(isset($_POST['email'])) {
     
    // CHANGE THE TWO LINES BELOW
    $email_to = "prasanna.nirmale@shrigroup.net";
     
    $email_subject = "Medley Networks form submissions";
     
     
    function died($error) {
        // your error code can go here
        echo "We are very sorry, but there were error(s) found with the form you submitted. ";
        echo "These errors appear below.<br /><br />";
        echo $error."<br /><br />";
        echo "Please go back and fix these errors.<br /><br />";
        die();
    }
     
    // validation expected data exists
    if(!isset($_POST['Name']) ||
        !isset($_POST['CompanyName']) ||
        !isset($_POST['Address']) ||
        !isset($_POST['City']) ||
        !isset($_POST['State/Prov'])||
        !isset($_POST['country'])||
        !isset($_POST['Zip'])||
        !isset($_POST['PhoneNo'])||
        !isset($_POST['Fax'])||
        !isset($_POST['Email'])||
        !isset($_POST['Message'])		
		) {
        died('We are sorry, but there appears to be a problem with the form you submitted.');       
    }
     
    $Name = $_POST['Name']; // required
    $CompanyName = $_POST['CompanyName']; // required
    $Address = $_POST['Address']; // required
    $City = $_POST['City']; // not required
    $StateProv = $_POST['State/Prov']; // required
    $country = $_POST['country'];
	$Zip = $_POST['Zip'];
	$PhoneNo = $_POST['PhoneNo'];
	$Fax =  $_POST['Fax'];
	$Email = $_POST['Email'];
	$Message = $_POST['Message'];
    $error_message = "";
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';

  if(!preg_match($email_exp,$Email)) {
    $error_message .= 'The Email Address you entered does not appear to be valid.<br />';
  }
    $string_exp = "/^[A-Za-z .'-]+$/";
  if(!preg_match($string_exp,$Name)) {
    $error_message .= 'The Name you entered does not appear to be valid.<br />';
  }
  if(!preg_match($string_exp,$CompanyName)) {
    $error_message .= 'The Company Name you entered does not appear to be valid.<br />';
  }
  if(!preg_match($string_exp,$Address)) {
    $error_message .= 'The Address you entered does not appear to be valid.<br />';
  }
  if(!preg_match($string_exp,$City)) {
    $error_message .= 'The City you entered does not appear to be valid.<br />';
  }

  if(!preg_match($string_exp,$StateProv)) {
    $error_message .= 'The State/Prov you entered does not appear to be valid.<br />';
  }
  if(!preg_match($string_exp,$country)) {
    $error_message .= 'The Country you entered does not appear to be valid.<br />';
  }
  if(!preg_match($string_exp,$Zip)) {
    $error_message .= 'The Zip you entered does not appear to be valid.<br />';
  } 
  
  
  if(strlen($Message) < 2) {
    $error_message .= 'The Message you entered do not appear to be valid.<br />';
  }

  if(strlen($error_message) > 0) {
    died($error_message);
  }
    $email_message = "Form details below.\n\n";
     
    function clean_string($string) {
      $bad = array("content-type","bcc:","to:","cc:","href");
      return str_replace($bad,"",$string);
    }
     
    $email_message .= "Name: ".clean_string($Name)."\n";
    $email_message .= "Company Name: ".clean_string($CompanyName)."\n";
    $email_message .= "Address: ".clean_string($Address)."\n";
    $email_message .= "City: ".clean_string($City)."\n";
    $email_message .= "State/Prov: ".clean_string($StateProv)."\n";
	$email_message .= "country: ".clean_string($country)."\n";
	$email_message .= "Zip: ".clean_string($Zip)."\n";
	$email_message .= "PhoneNo: ".clean_string($PhoneNo)."\n";
	$email_message .= "Fax: ".clean_string($Fax)."\n";
	$email_message .= "Email: ".clean_string($Email)."\n";
    $email_message .= "Message: ".clean_string($Message)."\n";
	 
    
     
// create email headers
$headers = 'From: '.$Email."\r\n".'Reply-To: '.$Email."\r\n".'X-Mailer: PHP/'.phpversion();
@mail($email_to, $email_subject, $email_message, $headers);  
echo "Your message has been sent successfully!";
}
//header("refresh:3; url= http://localhost/medleynetworks/"); /* Redirect browser */
header("refresh:3; url= http://www.medleynetworks.com/"); /* Redirect browser */
?>

<!DOCTYPE html>
<html>
  <head>
    <style>
      html, body {
      position: relative;
      width: 100%;
      height: 100%;
      padding: 0;
      margin: 0;
      }
      .loader {
      border: 5px solid #f3f3f3;
      border-radius: 50%;
      border-top: 5px solid #3498db;

      border-bottom: 5px solid #3498db;
      width: 120px;
      height: 120px;
      -webkit-animation: spin 2s linear infinite;
      animation: spin 2s linear infinite;
      }

      @-webkit-keyframes spin {
      0% { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
      }

      @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
      }
      .displayed {
      display: block;
      margin-left: auto;
      margin-right: auto }
    </style>
  </head>
  <body >
    


    <div style="display: table; height: 100%; width:100%; overflow: hidden;">
      <div style="display: table-cell; vertical-align: middle;">
        <div>
          <div>
            <h4 style="text-align:center;">Enquiry Submission in progress...</h4>
                        <h4 style="text-align:center; color:#C30">Thank you for contacting us. We will be in touch with you very soon.</h4>
            <div class="loader displayed"></div>
          </div>
        </div>
      </div>
    </div>
    

  </body>
</html>