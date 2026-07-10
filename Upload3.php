<?php

if (isset($_POST['submit']))
{
	$target = "/Upload";
	
	$ok = 1;
  if (isset($_POST['ConstructionUploaded'])) {    
  echo 1;
    $target = $target . basename($_FILES['ConstructionUploaded']['name']);
      if (move_uploaded_file($_FILES['ConstructionUploaded']['tmp_name'], $target))
        {
          echo "The file ConstructionUploaded " . basename($_FILES['ConstructionUploaded']['name']) . " has been uploaded";
        }
      else
        {
          echo "Sorry, there was a problem uploading your file.";
        }
    }
  
  if (isset($_POST['RFUploaded'])) {
   echo 2;
   $target = $target . basename($_FILES['RFUploaded']['name']);
    if (move_uploaded_file($_FILES['RFUploaded']['tmp_name'], $target))
      {
        echo "The file RFUploaded " . basename($_FILES['RFUploaded']['name']) . " has been uploaded";
      }
    else
      {
        echo "Sorry, there was a problem uploading your file.";
      }    
  }
  if (isset($_POST['ProjectManagersUploaded'])) {
   echo 3;
   $target = $target . basename($_FILES['ProjectManagersUploaded']['name']);
    if (move_uploaded_file($_FILES['ProjectManagersUploaded']['tmp_name'], $target))
      {
        echo "The file ProjectManagersUploaded " . basename($_FILES['ProjectManagersUploaded']['name']) . " has been uploaded";
      }
    else
      {
        echo "Sorry, there was a problem uploading your file.";
      }    
  }
  if (isset($_POST['QualityAssuranceUploaded'])) {
   echo 4;
   $target = $target . basename($_FILES['QualityAssuranceUploaded']['name']);
    if (move_uploaded_file($_FILES['QualityAssuranceUploaded']['tmp_name'], $target))
      {
        echo "The file QualityAssuranceUploaded " . basename($_FILES['QualityAssuranceUploaded']['name']) . " has been uploaded";
      }
    else
      {
        echo "Sorry, there was a problem uploading your file.";
      }    
  }
  
	
}

?>
