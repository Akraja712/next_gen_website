<?php
session_start();

// set time for session timeout
$currentTime = time() + 25200;
$expired = 3600;

// if session not set go to login page
if (!isset($_SESSION['username'])) {
    header("location:index.php");
}

// if current time is more than session timeout back to login page
if ($currentTime > $_SESSION['timeout']) {
    session_destroy();
    header("location:index.php");
}

// destroy previous session timeout and create new one
unset($_SESSION['timeout']);
$_SESSION['timeout'] = $currentTime + $expired;

header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
date_default_timezone_set('Asia/Kolkata');

include_once('../includes/custom-functions.php');
$fn = new custom_functions;
include_once('../includes/crud.php');
include_once('../includes/variables.php');
$db = new Database();
$db->connect();

        // Get the current date and time
        $date = new DateTime('now');

        // Round off to the nearest hour
        $date->modify('+' . (60 - $date->format('i')) . ' minutes');
        $date->setTime($date->format('H'), 0, 0);
    
        // Format the date and time as a string
        $date_string = $date->format('Y-m-d H:i:s');
        $currentdate = date('Y-m-d');
if (isset($_GET['table']) && $_GET['table'] == 'users') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';

    if (isset($_GET['status']) && $_GET['status'] != '') {
        $status = $db->escapeString($fn->xss_clean($_GET['status']));
        $where .= "status = '$status' ";
    }    
    if (isset($_GET['date']) && $_GET['date'] != '') {
        $date = $db->escapeString($fn->xss_clean($_GET['date']));
        if (!empty($where)) {
            $where .= "AND ";
        }
        $where .= "joined_date = '$date' ";
    }
    if (isset($_GET['enroll_date']) && $_GET['enroll_date'] != '') {
        $enroll_date = $db->escapeString($fn->xss_clean($_GET['enroll_date']));
        if (!empty($where)) {
            $where .= "AND ";
        }
        $where .= "enroll_date = '$enroll_date' ";
    }
    if (isset($_GET['referred_by']) && $_GET['referred_by'] != '') {
        $referred_by = $db->escapeString($fn->xss_clean($_GET['referred_by']));
        if (!empty($where)) {
            $where .= "AND ";
        }
        $where .= "referred_by = '$referred_by' ";
    }
    if (isset($_GET['student_plan']) && $_GET['student_plan'] != '') {
        $student_plan = $db->escapeString($fn->xss_clean($_GET['student_plan']));
        if (!empty($where)) {
            $where .= "AND ";
        }
        $where .= "student_plan = '$student_plan' ";
    }
  
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));

     if (isset($_GET['search']) && !empty($_GET['search'])) {
         $search = $db->escapeString($fn->xss_clean($_GET['search']));
         $searchCondition = "name LIKE '%$search%' OR mobile LIKE '%$search%' OR status LIKE '%$search%' OR refer_code LIKE '%$search%'";
         $where = $where ? "$where AND $searchCondition" : $searchCondition;
     }
    
     $sqlCount = "SELECT COUNT(id) as total FROM users " . ($where ? "WHERE $where" : "");
     $db->sql($sqlCount);
     $resCount = $db->getResult();
     $total = $resCount[0]['total'];
    
     $sql = "SELECT * FROM users " . ($where ? "WHERE $where" : "") . " ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
     $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;

    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $support_id = $row['support_id'];

        $operate = ' <a href="edit-users.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-users.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['fcm_id'] = $row['fcm_id'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['student_plan'] = $row['student_plan'];
        $tempRow['today_orders'] = $row['today_orders'];
        $tempRow['total_orders'] = $row['total_orders'];
        $tempRow['description'] = $row['description'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['orders_earnings'] = $row['orders_earnings'];
        $tempRow['hiring_earings'] = $row['hiring_earings'];
        $tempRow['registered_date'] = $row['registered_date'];
        $tempRow['average_orders'] = $row['average_orders'];
        $sql = "SELECT name FROM `staffs` WHERE id = $support_id";
        $db->sql($sql);
        $res = $db->getResult();
        $support_name = isset($res[0]['name']) ? $res[0]['name'] :"";
        $tempRow['support_name'] = $support_name;
        $tempRow['account_num'] = $row['account_num'];
        $tempRow['holder_name'] = $row['holder_name'];
        $tempRow['bank'] = $row['bank'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['ifsc'] = $row['ifsc'];
        $tempRow['device_id'] = $row['device_id'];
        if($row['status']==0)
            $tempRow['status'] ="<label class='label label-default'>Not Verify</label>";
        elseif($row['status']==1)
            $tempRow['status']="<label class='label label-success'>Verified</label>";        
        else
            $tempRow['status']="<label class='label label-danger'>Blocked</label>";

         $tempRow['joined_date'] = $row['joined_date'];
         $tempRow['enroll_date'] = $row['enroll_date'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
        
$db->disconnect();
