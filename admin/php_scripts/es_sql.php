<?php session_start();
    $isstatus='';
    $is_prepare='';
    if(!empty($_POST['SubmitLogin'])){
        if($_POST['SubmitLogin']=="QRY" && $_SESSION['sess_esuser']=='esadmin' && !empty($session_id)){
            $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        }else{
            if(!empty($_POST['txtusername']) && !empty($_POST['txtpw'])){
                if($_POST['txtusername']=="esadmin" && $_POST['txtpw']=="esadmin@dpis"){
                    $_SESSION['sess_esuser']='esadmin';
                    //echo '<script>window.location=\'../admin/es_sql.html\';</script>';
                }else{
                    $isstatus='<font color=red> [ชื่อหรือรหัสผ่านไม่ถูกต้อง]</font>';
                }
            }else{
                $isstatus='<font color=red> [กรุณาระบุชื่อ/รหัสผ่าน</font>';
            }
        }
        
    }else{
        //echo '<script>window.location=\'../admin/es_sql.html\';</script>';
    }
    if(!empty($isstatus)){
        echo '<script>window.location=\'../admin/es_sql.html\';</script>';
    }
?>
