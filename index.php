<?php

//Get Keys: https://cloud.nabzclan.vip/account/edit

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("key1", "key here 1"); // add key 2
define("key2", "key here 2"); // add key 2

function apiToken() {
  
  $url = "https://cloud.nabzclan.vip/api/v2/authorize"."?key1=".key1."&key2=".key2;

  $curl = curl_init();

  curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
  ]);
  
  $response = curl_exec($curl);
  $err = curl_error($curl);
  
  curl_close($curl);
  
  if ($err) {

    return "cURL Error #:" . $err;

  } else {

    $data = json_decode($response, true);

    $access_token = $data["data"]["access_token"];
    $account_id = $data["data"]["account_id"];

    return ["access_token" => $access_token, "account_id" => $account_id];

  }

}
function uploadFile() {

  $response = apiToken();

  $access_token = $response["access_token"];
  $account_id = $response["account_id"];

  $filePath = $_FILES['file']['tmp_name'];
  $type=$_FILES['file']['type'];
  $fileName = $_FILES['file']['name'];


  $data = array(
    'access_token' => $response['access_token'],
    'account_id' => $response['account_id'],
    'upload_file' => curl_file_create($filePath, $type, $fileName),
    'folder_id' => '632'
  );
    
  $url = "https://cloud.nabzclan.vip/api/v2/file/upload";

  $curl = curl_init();

  curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_HTTPHEADER => array('Content-Type: multipart/form-data'),
    CURLOPT_POST => 1,
    CURLOPT_TIMEOUT => 3600,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $data
  ]);
  
  $response = curl_exec($curl);
  $err = curl_error($curl);
  
  curl_close($curl);

  $decoded = json_decode($response, true);
  if (isset($decoded['data']['url'])) {
    $decoded['data']['url'] = stripslashes($decoded['data']['url']);
  }
  echo "<pre>" . json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";

}

if (isset($_POST['upload'])) {

uploadFile();

}


?>
<form action="" method="POST" enctype="multipart/form-data">
  <input type="file" name="file">
  <button name="upload">Upload</button>
</form>
