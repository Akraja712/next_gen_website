<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

date_default_timezone_set('Asia/Kolkata');

include_once('../includes/crud.php');
$db = new Database();
$db->connect();

$sql = "SELECT * FROM `abcd_users` WHERE project_type = 'amail'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    foreach ($res as $row) {
        $name = $row['name'];
        $email = $row['email'];    
        $mobile = $row['mobile'];
        $password = $row['password'];
        $dob = $row['dob'];
        $balance = $row['balance'];
        $refer_code = $row['refer_code'];
        $device_id = $row['device_id'];
        $referred_by = 'amail';
        $hr_id = $refer_code;
        $datetime = date('Y-m-d H:i:s');
    
        $sql = "INSERT INTO users (`name`, `mobile`, `email`, `password`, `location`, `dob`, `hr_id`, `aadhaar_num`, `referred_by`, `device_id`, `last_updated`, `registered_date`, `order_available`,`balance`) 
        VALUES ('$name', '$mobile', '$email', '$password', '0', '$dob', '$hr_id', '0', '$referred_by', '$device_id', '$datetime', '$datetime', '1','$balance')";

        $db->sql($sql);  
    }

    $response['success'] = true;
    $response['message'] = "Users Inserted Successfully";
    echo json_encode($response);
} else {
    $response['success'] = false;
    $response['message'] = "User with ID 11 Not Found";
    echo json_encode($response);
}
?>
