<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();

$currentdate = date('Y-m-d');
$condition = "status IN (1, 2, 3)";
$sql_query = "SELECT id, mobile, name, email, total_referrals, earn, balance, device_id, referred_by, refer_code, withdrawal_status, status, min_withdrawal, CONCAT(',',account_num, ',') AS account_num, holder_name, bank, branch, ifsc,blocked,convert_type,support_id FROM `users` WHERE $condition"; // Fetch all users without any condition
$db->sql($sql_query);
$developer_records = $db->getResult();

$filename = "AllUsers-data" . date('Ymd') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
$show_column = false;

if (!empty($developer_records)) {
    // Display column names in the first row
    foreach ($developer_records as $record) {
        if (!$show_column) {
            echo implode("\t", array_keys($record)) . "\n";
            $show_column = true;
        }

        // Fetch support name based on support_id
        $support_id = $record['support_id'];
        $sql = "SELECT name FROM `staffs` WHERE id = $support_id";
        $db->sql($sql);
        $res = $db->getResult();
        $support_name = isset($res[0]['name']) ? $res[0]['name'] : "";

        // Append support name to the user record
        $record['support_id'] = $support_name;

        echo implode("\t", array_values($record)) . "\n";
    }
}

exit;
?>
