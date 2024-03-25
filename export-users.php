<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();

$currentdate = date('Y-m-d');
$condition = "status IN (0, 1, 2)";
$sql_query = "SELECT * FROM `users` WHERE $condition";

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
