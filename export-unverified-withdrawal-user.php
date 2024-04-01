<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();


$sql_query = "SELECT name,mobile FROM `users` u,`withdrawals` w WHERE u.id = w.user_id AND w.status = 1 AND u.status = 0";
$db->sql($sql_query);
$developer_records = $db->getResult();

$filename = "AllUsers-data" . date('Ymd') . ".csv";

// Set the appropriate headers for CSV
header('Content-Type: text/csv');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: no-cache');
header('Pragma: no-cache');

$fp = fopen('php://output', 'w');

$show_column = false;

if (!empty($developer_records)) {
    // Display column names in the first row
    foreach ($developer_records as $record) {
        if (!$show_column) {
            fputcsv($fp, array_keys($record));
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

        fputcsv($fp, array_values($record));
    }
}

fclose($fp);
exit;
?>
