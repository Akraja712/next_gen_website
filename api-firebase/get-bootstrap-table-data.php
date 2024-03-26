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
        
        
        if ($_SESSION['role'] == 'Admin') {
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
        
                if (isset($_GET['referred_by']) && $_GET['referred_by'] != '') {
                    $referred_by = $db->escapeString($fn->xss_clean($_GET['referred_by']));
                    if (!empty($where)) {
                        $where .= "AND ";
                    }
                    $where .= "referred_by = '$referred_by' ";
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
        
                // Include condition for Admin to see only data starting with 'BLR'
                $where = $where ? "$where AND refer_code LIKE 'BLR%'" : "refer_code LIKE 'BLR%'";
        
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
        
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['orders_earnings'] = $row['orders_earnings'];
        $tempRow['hiring_earings'] = $row['hiring_earings'];
        $tempRow['average_orders'] = $row['average_orders'];
        $tempRow['account_num'] = $row['account_num'];
        $tempRow['holder_name'] = $row['holder_name'];
        $tempRow['bank'] = $row['bank'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['ifsc'] = $row['ifsc'];
        $sql = "SELECT name FROM `staffs` WHERE id = $support_id";
        $db->sql($sql);
        $res = $db->getResult();
        $support_name = isset($res[0]['name']) ? $res[0]['name'] :"";
        $tempRow['support_name'] = $support_name;
        $tempRow['device_id'] = $row['device_id'];
        if($row['status']==0)
            $tempRow['status'] ="<label class='label label-default'>Not Verify</label>";
        elseif($row['status']==1)
            $tempRow['status']="<label class='label label-success'>Verified</label>";        
        else
            $tempRow['status']="<label class='label label-danger'>Blocked</label>";

        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
} elseif ($_SESSION['role'] == 'Super Admin') {
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
 
    if (isset($_GET['referred_by']) && $_GET['referred_by'] != '') {
        $referred_by = $db->escapeString($fn->xss_clean($_GET['referred_by']));
        if (!empty($where)) {
            $where .= "AND ";
        }
        $where .= "referred_by = '$referred_by' ";
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
        
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['refer_code'] = $row['refer_code'];
        $tempRow['referred_by'] = $row['referred_by'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['orders_earnings'] = $row['orders_earnings'];
        $tempRow['hiring_earings'] = $row['hiring_earings'];
        $tempRow['average_orders'] = $row['average_orders'];
        $tempRow['account_num'] = $row['account_num'];
        $tempRow['holder_name'] = $row['holder_name'];
        $tempRow['bank'] = $row['bank'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['ifsc'] = $row['ifsc'];
        $sql = "SELECT name FROM `staffs` WHERE id = $support_id";
        $db->sql($sql);
        $res = $db->getResult();
        $support_name = isset($res[0]['name']) ? $res[0]['name'] :"";
        $tempRow['support_name'] = $support_name;
        $tempRow['device_id'] = $row['device_id'];
        if($row['status']==0)
            $tempRow['status'] ="<label class='label label-default'>Not Verify</label>";
        elseif($row['status']==1)
            $tempRow['status']="<label class='label label-success'>Verified</label>";        
        else
            $tempRow['status']="<label class='label label-danger'>Blocked</label>";

        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
}   

//faq
if (isset($_GET['table']) && $_GET['table'] == 'faq') {

    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($_GET['offset']);
    if (isset($_GET['limit']))
        $limit = $db->escapeString($_GET['limit']);
    if (isset($_GET['sort']))
        $sort = $db->escapeString($_GET['sort']);
    if (isset($_GET['order']))
        $order = $db->escapeString($_GET['order']);

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $db->escapeString($_GET['search']);
            $where .= "WHERE id like '%" . $search . "%' OR mobile like '%" . $search . "%' OR otp like '%" . $search . "%'";
        }
    if (isset($_GET['sort'])){
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])){
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `faq` ";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
    $sql = "SELECT * FROM faq " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . ", " . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    
    $rows = array();
    $tempRow = array();

    foreach ($res as $row) {

        
        $operate = ' <a href="edit-faq.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-faq.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['question'] = $row['question'];
        $tempRow['answer'] = $row['answer'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//youtube_link
if (isset($_GET['table']) && $_GET['table'] == 'youtube_link') {

    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($_GET['offset']);
    if (isset($_GET['limit']))
        $limit = $db->escapeString($_GET['limit']);
    if (isset($_GET['sort']))
        $sort = $db->escapeString($_GET['sort']);
    if (isset($_GET['order']))
        $order = $db->escapeString($_GET['order']);

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $db->escapeString($_GET['search']);
            $where .= "WHERE id like '%" . $search . "%' OR mobile like '%" . $search . "%' OR otp like '%" . $search . "%'";
        }
    if (isset($_GET['sort'])){
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])){
        $order = $db->escapeString($_GET['order']);
    }
    $sql = "SELECT COUNT(`id`) as total FROM `youtube_link` ";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
    $sql = "SELECT * FROM youtube_link " . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . $offset . ", " . $limit;
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    
    $rows = array();
    $tempRow = array();

    foreach ($res as $row) {

        
        $operate = ' <a href="edit-youtube_link.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-youtube_link.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['link'] = $row['link'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//withdrawals table goes here
if (isset($_GET['table']) && $_GET['table'] == 'withdrawals') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    
    if ((isset($_GET['status'])  && $_GET['status'] != '')) {
        $status = $db->escapeString($fn->xss_clean($_GET['status']));
        $where .= "AND w.status=$status ";
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
            $where .= "AND (u.mobile LIKE '%" . $search . "%' OR u.name LIKE '%" . $search . "%') ";
        }
    if (isset($_GET['sort'])){
        $sort = $db->escapeString($_GET['sort']);

    }
    if (isset($_GET['order'])){
        $order = $db->escapeString($_GET['order']);

    }        
    $join = "WHERE w.user_id = u.id ";

    $sql = "SELECT COUNT(u.id) as `total` FROM `withdrawals` w,`users` u $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT w.id AS id,w.*,u.mobile,u.upi,u.account_num,u.holder_name,u.bank,u.branch,u.ifsc,u.earn,u.total_referrals,w.status AS status FROM `withdrawals` w,`users` u $join 
          $where ORDER BY $sort $order LIMIT $offset, $limit";
    $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;

    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $checkbox = '<input type="checkbox" name="enable[]" value="'.$row['id'].'">';
        $amount = $row['amount'];
        $tempRow['column'] = $checkbox;
        $tempRow['id'] = $row['id'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['total_referrals'] = $row['total_referrals'];
        $tempRow['earn'] = $row['earn'];
        $tempRow['upi'] = $row['upi'];
        $tempRow['account_num'] = ','.$row['account_num'].',';
        $tempRow['holder_name'] = $row['holder_name'];
        $tempRow['bank'] = $row['bank'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['ifsc'] = $row['ifsc'];
        $amount = $row['amount'];

        if ($amount < 250) {
            $taxRate = 0.05; // 5% tax rate
        } elseif ($amount <= 500) {
            $taxRate = 0.1; // 10% tax rate
        } elseif ($amount <= 1000) {
            $taxRate = 0.15; // 15% tax rate
        } else {
            $taxRate = 0.2; // 20% tax rate
        }
        
        $taxAmount = $amount * $taxRate;
        $pay_amount = $amount - $taxAmount;
        $tempRow['pay_amount'] = $pay_amount;
        $tempRow['amount'] = $row['amount'];
        $tempRow['datetime'] = $row['datetime'];
        if($row['status']==1)
                $tempRow['status']="<p class='text text-success'>Paid</p>";        
        elseif($row['status']==0)
                 $tempRow['status']="<p class='text text-primary'>Unpaid</p>"; 
        else
               $tempRow['status']="<p class='text text-danger'>Cancelled</p>";
        $rows[] = $tempRow;
        }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//transaction
if (isset($_GET['table']) && $_GET['table'] == 'transactions') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'date';
    $order = 'DESC';
    
    if ((isset($_GET['type']) && $_GET['type'] != '')) {
        $type = $db->escapeString($fn->xss_clean($_GET['type']));
        $where .= " AND l.type = '$type'";
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
            $where .= "AND (u.mobile LIKE '%" . $search . "%' OR u.name LIKE '%" . $search . "%') ";
        }
        
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
   
    $join = "LEFT JOIN `users` u ON l.user_id = u.id WHERE l.id IS NOT NULL " . $where;

    $sql = "SELECT COUNT(l.id) AS total FROM `transactions` l " . $join;
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
   
     $sql = "SELECT l.id AS id,l.*,u.name,u.mobile  FROM `transactions` l " . $join . " ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
     $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
           $operate = ' <a class="text text-danger" href="delete-transaction.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow = array();
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['type'] = $row['type'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['orders'] = $row['orders'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['total_qty_sold'] = $row['total_qty_sold'];
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}

//staffs table goes here
if (isset($_GET['table']) && $_GET['table'] == 'staffs') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if ((isset($_GET['status'])  && $_GET['status'] != '')) {
        $status = $db->escapeString($fn->xss_clean($_GET['status']));
        $where .= "AND s.status='$status' ";
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
            $where .= "AND (s.mobile LIKE '%" . $search . "%' OR s.name LIKE '%" . $search . "%') ";
        }
        
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
  
    $join = "LEFT JOIN `branches` b ON s.branch_id = b.id WHERE s.id IS NOT NULL ";

    $sql = "SELECT COUNT(s.id) as total FROM `staffs` s $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT s.id AS id,s.*,b.short_code AS branch FROM `staffs` s $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        $staff_id = $row['id'];
        $operate = '<a href="edit-staff.php?id=' . $row['id'] . '" class="text text-primary"><i class="fa fa-edit"></i>Edit</a>';
        $operate .= ' <a class="text text-danger" href="delete-staff.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
    
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['email'] = $row['email'];
        $tempRow['branch'] = $row['branch'];
        $sql = "SELECT id FROM `incentives` WHERE DATE(datetime) = '$currentdate' AND amount = 50 AND staff_id = $staff_id GROUP BY user_id";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        $direct_join = $num;
        $sql = "SELECT id FROM `incentives` WHERE DATE(datetime) = '$currentdate' AND amount = 7.50 AND staff_id = $staff_id GROUP BY user_id";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        $today_refer_joins = $num;

        $sql = "SELECT COUNT(id) AS total FROM `users` WHERE support_id = $staff_id AND status = 1 AND today_orders != 0";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $res[0]['total'];
        $today_active_users = $num;
        $tempRow['today_direct_joins'] = $direct_join;
        $tempRow['today_refer_joins'] = $today_refer_joins;
        $tempRow['today_active_users'] = $today_active_users;
        $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
if (isset($_GET['table']) && $_GET['table'] == 'staff_withdrawals') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'w.id';
    $order = 'DESC';
    if ((isset($_GET['user_id']) && $_GET['user_id'] != '')) {
        $user_id = $db->escapeString($fn->xss_clean($_GET['user_id']));
        $where .= "AND w.staff_id = '$user_id'";
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
            $where .= "AND s.name like '%" . $search . "%' OR t.amount like '%" . $search . "%' OR t.id like '%" . $search . "%'  OR t.type like '%" . $search . "%' OR s.mobile like '%" . $search . "%' ";
        }
        
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "WHERE w.staff_id = s.id ";

    $sql = "SELECT COUNT(w.id) as total FROM `staff_withdrawals` w,`staffs` s $join ". $where ."";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $sql = "SELECT w.id AS id,w.*,s.name,s.mobile,s.balance,u.mobile,s.branch,s.bank_name,s.bank_account_number,s.ifsc_code FROM `staff_withdrawals` w,`staffs` s $join
                        $where ORDER BY $sort $order LIMIT $offset, $limit";
             $db->sql($sql);
        }
        else{
            $sql = "SELECT w.id AS id,w.*,s.name,s.balance,s.mobile,s.branch,s.bank_name,s.bank_account_number,s.ifsc_code FROM `staff_withdrawals` w,`staffs` s $join
                    $where ORDER BY $sort $order LIMIT $offset, $limit";
             $db->sql($sql);
        }
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        // $operate = ' <a class="text text-danger" href="delete-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-trash"></i>Delete</a>';
        // $operate .= ' <a href="edit-withdrawal.php?id=' . $row['id'] . '"><i class="fa fa-edit"></i>Edit</a>';
        $checkbox = '<input type="checkbox" name="enable[]" value="'.$row['id'].'">';
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['bank_account_number'] = ','.$row['bank_account_number'].',';
        $tempRow['bank_name'] = $row['bank_name'];
        $tempRow['branch'] = $row['branch'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['balance'] = $row['balance'];
        $tempRow['ifsc_code'] = $row['ifsc_code'];
        $tempRow['column'] = $checkbox;
        if($row['status']==1)
            $tempRow['status'] ="<p class='text text-success'>Paid</p>";
        elseif($row['status']==0)
            $tempRow['status']="<p class='text text-primary'>Unpaid</p>";
        else
            $tempRow['status']="<p class='text text-danger'>Cancelled</p>";
        // $tempRow['operate'] = $operate;
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}


//staff transactions table goes here
if (isset($_GET['table']) && $_GET['table'] == 'staff_transactions') {
    $offset = 0;
    $limit = 10;
    $where = '';
    $sort = 'id';
    $order = 'DESC';
    if (isset($_GET['offset']))
        $offset = $db->escapeString($fn->xss_clean($_GET['offset']));
    if (isset($_GET['limit']))
        $limit = $db->escapeString($fn->xss_clean($_GET['limit']));

    if (isset($_GET['sort']))
        $sort = $db->escapeString($fn->xss_clean($_GET['sort']));
    if (isset($_GET['order']))
        $order = $db->escapeString($fn->xss_clean($_GET['order']));
    if (isset($_GET['type']) && !empty($_GET['type'])){
        $type = $db->escapeString($fn->xss_clean($_GET['type']));
        $where .= "AND t.type = '$type' ";
      
    }
    if (isset($_GET['staff']) && !empty($_GET['staff'])) {
        $staff = $db->escapeString($fn->xss_clean($_GET['staff']));
        $where .= "AND s.id = '$staff' ";
    }
    
    

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $db->escapeString($fn->xss_clean($_GET['search']));
        $where .= "AND s.name like '%" . $search . "%' OR t.amount like '%" . $search . "%' OR t.id like '%" . $search . "%'  OR t.type like '%" . $search . "%' OR s.mobile like '%" . $search . "%' ";
    }
    if (isset($_GET['sort'])) {
        $sort = $db->escapeString($_GET['sort']);
    }
    if (isset($_GET['order'])) {
        $order = $db->escapeString($_GET['order']);
    }
    $join = "LEFT JOIN `staffs` s ON t.staff_id = s.id WHERE t.id IS NOT NULL ";

    
    $sql = "SELECT COUNT(t.id) as total FROM `staff_transactions` t $join " . $where . "";
    $db->sql($sql);
    $res = $db->getResult();
    foreach ($res as $row)
        $total = $row['total'];

    $sql = "SELECT t.id AS id,t.*,s.name,s.mobile FROM `staff_transactions` t $join 
    $where ORDER BY $sort $order LIMIT $offset, $limit";
     $db->sql($sql);
    $res = $db->getResult();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $tempRow = array();
    foreach ($res as $row) {
        
        $tempRow['id'] = $row['id'];
        $tempRow['name'] = $row['name'];
        $tempRow['mobile'] = $row['mobile'];
        $tempRow['amount'] = $row['amount'];
        $tempRow['datetime'] = $row['datetime'];
        $tempRow['type'] = $row['type'];
        $rows[] = $tempRow;
    }
    $bulkData['rows'] = $rows;
    print_r(json_encode($bulkData));
}
$db->disconnect();
