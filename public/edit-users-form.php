<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
?>

<?php
if (isset($_GET['id'])) {
    $ID = $db->escapeString($_GET['id']);
} else {
    // $ID = "";
    return false;
    exit(0);
}



if (isset($_POST['btnEdit'])){

    $datetime = date('Y-m-d H:i:s');
    $date = date('Y-m-d');
    $mobile = $db->escapeString($_POST['mobile']);
    $earn = $db->escapeString($_POST['earn']);
    $balance = $db->escapeString($_POST['balance']);
    $referred_by = $db->escapeString($_POST['referred_by']);
    $refer_code= $db->escapeString($_POST['refer_code']);
    $withdrawal_status = $db->escapeString($_POST['withdrawal_status']);
    $blocked = $db->escapeString($_POST['blocked']);
    $min_withdrawal = $db->escapeString($_POST['min_withdrawal']);
   // $status = $db->escapeString($_POST['status']);
    $total_referrals = $db->escapeString(($_POST['total_referrals']));
    $convert_type = $db->escapeString(($_POST['convert_type']));
    $account_num = $db->escapeString($_POST['account_num']);
    $holder_name = $db->escapeString($_POST['holder_name']);
    $bank = $db->escapeString($_POST['bank']);
    $branch = $db->escapeString(($_POST['branch']));
    $ifsc = $db->escapeString(($_POST['ifsc']));
    $support_id = $db->escapeString(($_POST['support_id']));
    $ecom_status = $db->escapeString(($_POST['ecom_status']));
    $password = $db->escapeString(($_POST['password']));


    $error = array();

    if (empty($mobile)) {
        $error['mobile'] = " <span class='label label-danger'>Required!</span>";
    }
    if (empty($upi)) {
        $error['upi'] = " <span class='label label-danger'>Required!</span>";
    }
    if (empty($balance)) {
        $error['balance'] = " <span class='label label-danger'>Required!</span>";
    }
    if (empty($earn)) {
        $error['earn'] = " <span class='label label-danger'>Required!</span>";
    }
    if (empty($languages)) {
        $error['languages'] = " <span class='label label-danger'>Required!</span>";
    }
    if (empty($support_id)) {
        $error['update_users'] = " <span class='label label-danger'> Support Required!</span>";
    }

    if (!empty($mobile) && 
    !empty($support_id)) {

        $refer_bonus_sent = $fn->get_value('users','refer_bonus_sent',$ID);
        if (!empty($referred_by) && $refer_bonus_sent != 1) {
            $sql_query = "SELECT * FROM users WHERE refer_code = '$referred_by'";
            $db->sql($sql_query);
            $res = $db->getResult();
            $num = $db->numRows($res);
        
            if ($num == 1) {
                $user_status = $res[0]['status'];
                $ecom_status = $res[0]['ecom_status'];
                $user_id = $res[0]['id'];
                
                if ($user_status == 1) {
                    if ($ecom_status == 0) {
                        $referral_bonus = 250;
                    } else {
                        if ($convert_type == 1) {
                            $referral_bonus = 3000;
                        } elseif ($convert_type == 2) {
                            $referral_bonus = 4000;
                        } elseif ($convert_type == 3) {
                            $referral_bonus = 5000;
                        } elseif ($convert_type == 4) {
                            $referral_bonus = 250;
                        }
        
                    $sql_query = "UPDATE users SET `total_referrals` = total_referrals + 1, `hiring_earings` = hiring_earings + $referral_bonus  WHERE id = $user_id";
                    $db->sql($sql_query);
                    
                    $sql_query = "INSERT INTO transactions (user_id, amount, datetime, type) VALUES ($user_id, $referral_bonus, '$datetime', 'refer_bonus')";
                    $db->sql($sql_query);
                    
                    $sql_query = "UPDATE users SET refer_bonus_sent = 1 WHERE id = $ID";
                    $db->sql($sql_query);
                    }
                }
            }
        }
        
            
            $sql_query = "UPDATE users SET mobile='$mobile',earn='$earn',balance='$balance',referred_by='$referred_by',refer_code='$refer_code',withdrawal_status='$withdrawal_status',min_withdrawal='$min_withdrawal',ecom_status=$ecom_status,blocked = '$blocked',total_referrals = '$total_referrals',convert_type = '$convert_type',holder_name='$holder_name', bank='$bank', branch='$branch', ifsc='$ifsc', account_num='$account_num',support_id='$support_id',password='$password' WHERE id =  $ID";
            $db->sql($sql_query);
            $update_result = $db->getResult();
    
            if (!empty($update_result)) {
                $update_result = 0;
            } else {
                $update_result = 1;
            }
    
            // check update result
            if ($update_result == 1) {
                $error['update_users'] = " <section class='content-header'><span class='label label-success'>User Details updated Successfully</span></section>";
            } else {
                $error['update_users'] = " <span class='label label-danger'>Failed to update</span>";
            }
        }
}
 
$data = array();



$sql_query = "SELECT * FROM users WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();

$refer_code = $res[0]['refer_code'];
$referred_by = isset($_POST['referred_by']) ? $_POST['referred_by'] : $res[0]['referred_by'];



if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "users.php";
    </script>
<?php } ?>
<section class="content-header">
    <h1>
        Edit Users<small><a href='users.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to users</a></small></h1>
    <small><?php echo isset($error['update_users']) ? $error['update_users'] : ''; ?></small>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>
<section class="content">
    <!-- Main row -->

    <div class="row">
        <div class="col-md-11">

            <!-- general form elements -->
            <div class="box box-primary">
               <div class="box-header with-border">
                             <div class="form-group col-md-3">
                                <h4 class="box-title"> </h4>
                                <a class="btn btn-block btn-success" href="add-balance.php?id=<?php echo $ID ?>"><i class="fa fa-plus-square"></i>  Add Balance</a>
                            </div> 
                </div>
                <!-- /.box-header -->
                <form id="edit_project_form" method="post" enctype="multipart/form-data">
                <input type="hidden" class="form-control" name="total_referrals" value="<?php echo $res[0]['total_referrals']; ?>">
                <div class="box-body">
                        <div class="row">
                              <div class="form-group">
                              <div class="col-md-3">
                                    <label for="exampleInputEmail1">Name</label> <i class="text-danger asterik">*</i<?php echo isset($error['name']) ? $error['name'] : ''; ?>>
                                     <input type="text" class="form-control" name="name" value="<?php echo $res[0]['name']; ?>">
                                  </div>
                                 <div class="col-md-3">
                                    <label for="exampleInputEmail1"> Mobile Number</label> <i class="text-danger asterik">*</i<?php echo isset($error['mobile']) ? $error['mobile'] : ''; ?>>
                                     <input type="text" class="form-control" name="mobile" value="<?php echo $res[0]['mobile']; ?>">
                                  </div>
                               <div class="col-md-3">
                                    <label for="exampleInputEmail1"> Refered By</label> <i class="text-danger asterik">*</i<?php echo isset($error['referred_by']) ? $error['referred_by'] : ''; ?>>
                                    <input type="text" class="form-control" name="referred_by" value="<?php echo $res[0]['referred_by']; ?>">
                                 </div>  
                                 <div class="col-md-3">
                                    <label for="exampleInputEmail1">Password</label> <i class="text-danger asterik">*</i<?php echo isset($error['password']) ? $error['password'] : ''; ?>>
                                    <input type="text" class="form-control" name="password" value="<?php echo $res[0]['password']; ?>">
                                 </div> 
                               </div>
                             </div>
                          <br>
                          <div class="row">
                              <div class="form-group">
                                   <div class="col-md-3">
                                     <label for="exampleInputEmail1"> Refer Code</label> <i class="text-danger asterik">*</i><?php echo isset($error['refer_code']) ? $error['refer_code'] : ''; ?>
                                     <input type="text" class="form-control" name="refer_code" value="<?php echo $res[0]['refer_code']; ?>">
                                   </div>
                                   <div class="col-md-3">
                                <div class="form-group">
                                <label for="exampleInputEmail1">Convert Type</label> <i class="text-danger asterik">*</i>
                                    <select id='convert_type' name="convert_type" class='form-control'>
                                    <option value='0' <?php if ($res[0]['convert_type'] == '0') echo 'selected'; ?>>Select</option>
                                     <option value='1' <?php if ($res[0]['convert_type'] == '1') echo 'selected'; ?>>Company convert</option>
                                      <option value='2' <?php if ($res[0]['convert_type'] == '2') echo 'selected'; ?>>User convert</option>
                                      <option value='3' <?php if ($res[0]['convert_type'] == '3') echo 'selected'; ?>>e-commerce</option>
                                      <option value='4' <?php if ($res[0]['convert_type'] == '4') echo 'selected'; ?>>Joined</option>
                                    </select>
                                </div>
                            </div>
                                   <div class="form-group col-md-6">
                                    <label class="control-label">Status</label><i class="text-danger asterik">*</i><br>
                                    <div id="ecom_status" class="btn-group">
                                        <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="ecom_status" value="0" <?= ($res[0]['ecom_status'] == 0) ? 'checked' : ''; ?>> Not-verified
                                        </label>
                                        <label class="btn btn-success" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="ecom_status" value="1" <?= ($res[0]['ecom_status'] == 1) ? 'checked' : ''; ?>> Verified
                                        </label>
                                        <label class="btn btn-danger" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="ecom_status" value="2" <?= ($res[0]['ecom_status'] == 2) ? 'checked' : ''; ?>> Blocked
                                        </label>
                                    </div>
                                </div>
                               </div>
                             </div>
                             <div class="row">
                              <div class="form-group">
                              <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Select Support</label> <i class="text-danger asterik">*</i>
                                    <select id='support_id' name="support_id" class='form-control' style="background-color: #7EC8E3">
                                             <option value="">--Select--</option>
                                                <?php
                                                $sql = "SELECT * FROM `staffs`";
                                                $db->sql($sql);

                                                $result = $db->getResult();
                                                foreach ($result as $value) {
                                                ?>
                                                    <option value='<?= $value['id'] ?>' <?= $value['id']==$res[0]['support_id'] ? 'selected="selected"' : '';?>><?= $value['name'] ?></option>
                                                    
                                                <?php } ?>
                                    </select>
                            </div>
                               </div>
                             </div>
                        <br>
                        <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>
                    </div>
                    <hr>
                    <br>
                        <div class="row">
                            <div class="form-group">
                            <div class="col-md-3">
                                    <label for="exampleInputEmail1">Earn</label> <i class="text-danger asterik">*</i><?php echo isset($error['earn']) ? $error['earn'] : ''; ?>
                                    <input type="text" class="form-control" name="earn" value="<?php echo $res[0]['earn']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1"> Balance</label> <i class="text-danger asterik">*</i><?php echo isset($error['balance']) ? $error['balance'] : ''; ?>
                                    <input type="text" class="form-control" name="balance" value="<?php echo $res[0]['balance']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Min Withdrawal</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="min_withdrawal" value="<?php echo $res[0]['min_withdrawal']; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleInputEmail1">Total Referrals</label> <i class="text-danger asterik">*</i><?php echo isset($error['total_referrals']) ? $error['total_referrals'] : ''; ?>
                                    <input type="text" class="form-control" name="total_referrals" value="<?php echo $res[0]['total_referrals']; ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                            <div class="row">
                            <div class="col-md-3">
                              <div class="form-group">
                                <label for="">Withdrawal Status</label><br>
                                    <input type="checkbox" id="withdrawal_button" class="js-switch" <?= isset($res[0]['withdrawal_status']) && $res[0]['withdrawal_status'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="withdrawal_status" name="withdrawal_status" value="<?= isset($res[0]['withdrawal_status']) && $res[0]['withdrawal_status'] == 1 ? 1 : 0 ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Blocked</label><br>
                                    <input type="checkbox" id="blocked_button" class="js-switch" <?= isset($res[0]['blocked']) && $res[0]['blocked'] == 1 ? 'checked' : '' ?>>
                                    <input type="hidden" id="blocked" name="blocked" value="<?= isset($res[0]['blocked']) && $res[0]['blocked'] == 1 ? 1 : 0 ?>">
                                </div>
                            </div>   
                       </div>
                       <div class="row">
                            <div class="form-group">
                            <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Account Number</label> <i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="account_num" value="<?php echo $res[0]['account_num']; ?>">
                                </div>
                                <div class='col-md-6'>
                                    <label for="exampleInputEmail1">Holder Name</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="holder_name" value="<?php echo $res[0]['holder_name']; ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">IFSC</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="ifsc" value="<?php echo $res[0]['ifsc']; ?>">
                                </div>
                                <div class="col-md-4">
                                <label for="exampleInputEmail1">Bank</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="bank" value="<?php echo $res[0]['bank']; ?>">
                                </div>
                                <div class="col-md-4">
                                <label for="exampleInputEmail1">Branch</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="branch" value="<?php echo $res[0]['branch']; ?>">
                                </div>
                               
                                </div>
                            </div>
                            
                            <br>
                            </div>
                     
                         </div><!-- /.box-body -->
                         <br>
                </form>
            </div><!-- /.box -->
        </div>
    </div>
</section>
<div class="separator"> </div>
<?php $db->disconnect(); ?>
<script>
    var changeCheckbox = document.querySelector('#withdrawal_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#withdrawal_status').val(1);

        } else {
            $('#withdrawal_status').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#student_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#student_plan').val(1);

        } else {
            $('#student_plan').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#days_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#days_60_plan').val(1);

        } else {
            $('#days_60_plan').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#blocked_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#blocked').val(1);

        } else {
            $('#blocked').val(0);
            }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#reset_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#reset_available').val(1);

        } else {
            $('#reset_available').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#order_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#order_available').val(1);

        } else {
            $('#order_available').val(0);
        }
    };
</script>
<script>
    var changeCheckbox = document.querySelector('#product_status_button');
    var init = new Switchery(changeCheckbox);
    changeCheckbox.onchange = function() {
        if ($(this).is(':checked')) {
            $('#product_status').val(1);

        } else {
            $('#product_status').val(0);
        }
    };
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#link").click(function() {
            var refer_code = $("input[name='refer_code']").val();
            var link = "https://nextgencareer.abcdapp.in/index.php?"; 
            var full_link = link + "refer_code=" + refer_code;

            var tempInput = $("<input>");
            $("body").append(tempInput);
            tempInput.val(full_link).select();
            document.execCommand("copy");
            tempInput.remove();

            alert("Marketing link with refer_code copied to clipboard!");
        });
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>


