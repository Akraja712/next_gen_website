<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();

$currentdate = date('Y-m-d');
$condition = "status = 1"; // Condition for verified users (status not equal to 0)
$sql_query = "SELECT id, mobile, name, email, total_referrals, earn, balance, device_id, referred_by, refer_code, withdrawal_status, status, min_withdrawal, CONCAT(',',account_num, ',') AS account_num, holder_name, bank, branch, ifsc,blocked,convert_type,support_id FROM `users` WHERE $condition"; // Fetch all users without any condition
$db->sql($sql_query);
$developer_records = $db->getResult();

$filename = "unverifiedUsers-data" . date('Ymd') . ".xls";			
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");	
$show_column = false;

if (!empty($developer_records)) {
  foreach ($developer_records as $record) {
    if (!$show_column) {
      // display field/column names in the first row
      echo implode("\t", array_keys($record)) . "\n";
      $show_column = true;
    }
    echo implode("\t", array_values($record)) . "\n";
  }
}

exit;  
?>
