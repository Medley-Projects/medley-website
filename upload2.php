<?php
$target = "";

//FORM 1
if(isset($_POST['form1']) && $_POST['form1']=='Upload Resume'){

$name = basename($_FILES['Uploaded']['name']);
 
 $d = "Upload/ConstructionEngineers/" .  date('Y/m/d') ."/";
  if(!is_dir($d)) {
    mkdir($d, 0755, true);
    }
 $target = $d.$name; 
if (move_uploaded_file($_FILES['Uploaded']['tmp_name'], $target))
        {
          echo "<p style='color:red;'>The file " . $name . " has been uploaded</p>";
        }
      else
        {
          echo "Sorry, there was a problem uploading your file.";
        }    
 }
//FORM 2
if(isset($_POST['form2']) && $_POST['form2']=='Upload Resume'){
$name = basename($_FILES['Uploaded']['name']);
 
 $d = "Upload/RFEngineers/" .  date('Y/m/d') ."/";
  if(!is_dir($d)) {
    mkdir($d, 0755, true);
    }
 $target = $d.$name; 
if (move_uploaded_file($_FILES['Uploaded']['tmp_name'], $target))
        {
          echo "<p style='color:red;'>The file " . $name . " has been uploaded</p>";
        }
      else
        {
          echo "Sorry, there was a problem uploading your file.";
        }
}
//FORM 3
if(isset($_POST['form3']) && $_POST['form3']=='Upload Resume'){
$name = basename($_FILES['Uploaded']['name']);
 
 $d = "Upload/ProjectManagers/" .  date('Y/m/d') ."/";
  if(!is_dir($d)) {
    mkdir($d, 0755, true);
    }
 $target = $d.$name; 
if (move_uploaded_file($_FILES['Uploaded']['tmp_name'], $target))
        {
          echo "<p style='color:red;'>The file " . $name . " has been uploaded</p>";
        }
      else
        {
          echo "Sorry, there was a problem uploading your file.";
        }
}

//FORM 4
if(isset($_POST['form4']) && $_POST['form4']=='Upload Resume'){
$name = basename($_FILES['Uploaded']['name']);
 
 $d = "Upload/ QualityAssuranceEng/" .  date('Y/m/d') ."/";
  if(!is_dir($d)) {
    mkdir($d, 0755, true);
    }
 $target = $d.$name; 
if (move_uploaded_file($_FILES['Uploaded']['tmp_name'], $target))
        {          
          echo "<p style='color:red;'>The file " . $name . " has been uploaded</p>";
        }
      else
        {
          echo "Sorry, there was a problem uploading your file.";
        }
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
            <h4 style="text-align:center;">Resume Submission in progress...</h4>
            <div class="loader displayed"></div>
          </div>
        </div>
      </div>
    </div>
    

  </body>
</html>