<?
include_once("./_common.php");

$sql_common = " mb_id = '{$member['mb_id']}', 
                code_type = '{$_POST['code_type']}',  
                code_name = '{$_POST['code_name']}', 
                code_value = '{$_POST['code_value']}',
                code_use = '{$_POST['code_use']}',  ";

if($_POST['code_type']=="3")
    $sql_common .= " code_percent = '{$_POST['code_percent']}', ";

if($_POST['type']=="create" || $_POST['type']=="update"){
    if($_POST['code_orig_value']!=$_POST['code_value']){
        $chk_cnt = sql_fetch(" SELECT COUNT(*) cnt FROM g5_code_list WHERE code_type = '{$_POST['code_type']}' AND code_value = '{$_POST['code_value']}' AND del_yn = 'N' ")['cnt'];
        
        if($chk_cnt > 0){
            die('dupil_err');
        }
    }
}

switch($_POST['type']){
    case "create" :
        $sql = " INSERT INTO g5_code_list SET {$sql_common} wr_datetime = now()  ";
        break;
    case "update" :
        $sql = " UPDATE g5_code_list SET {$sql_common} up_datetime = now() WHERE idx = '{$_POST['idx']}'  ";
        break;
    case "delete" :
        $sql = " UPDATE g5_code_list SET del_yn = 'Y' WHERE idx = '{$_POST['idx']}' ";
        break;

    default :     
}

if(sql_query($sql)){
    die('y');
}else{
    die('n');
}

?>