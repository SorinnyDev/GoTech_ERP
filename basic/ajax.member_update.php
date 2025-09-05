<?
include_once("./_common.php");


if($_POST['mb_password']){
    $mb_password = get_encrypt_string($_POST['mb_password']);
    $password_sql = " mb_password = '{$mb_password}', ";
}

$sql_common = " mb_id = '{$_POST['mb_id']}', 
                mb_type = '{$_POST['mb_type']}',  
                mb_name = '{$_POST['mb_name']}',  
                mb_nick = '{$_POST['mb_name']}', 
                {$password_sql}    ";

switch($_POST['type']){
    case "create" :
        $sql = " INSERT INTO g5_member SET {$sql_common} mb_level = '10', mb_datetime = now()  ";
        break;

    case "update" :
        $sql = " UPDATE g5_member SET {$sql_common} up_datetime = now()  WHERE mb_no = '{$_POST['mb_no']}'  ";
        break;

    case "delete" :
        $sql = " UPDATE g5_member SET del_yn = 'Y' WHERE mb_no = '{$_POST['mb_no']}' ";
        break;

    default :     
}

if($_POST['type']=="create" || $_POST['type']=="update"){
    if($_POST['mb_id'] != $_POST['mb_orig_id']){
        $cnt = sql_fetch(" SELECT COUNT(*) AS cnt FROM g5_member WHERE mb_id = '{$_POST['mb_id']}' AND del_yn = 'N' ")['cnt'];
        
        if($cnt > 0){
            die('dupli_err');
        }
    }
}

if(sql_query($sql)){
    die('y');
}else{
    die('n');
}

?>