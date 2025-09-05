<?php
include_once("./_common.php");

$sql = " UPDATE g5_sales3_list SET wr_hab_x = '{$_POST['wr_hab_x']}',
                                   wr_hab_y = '{$_POST['wr_hab_y']}',
                                   wr_hab_z = '{$_POST['wr_hab_z']}',
                                   wr_weight_sum1 = '{$_POST['wr_weight_sum1']}',
                                   wr_weight_sum2 = '{$_POST['wr_weight_sum2']}',
                                   wr_weight_sum3 = '{$_POST['wr_weight_sum3']}'
                                WHERE seq = '{$_POST['seq']}'    ";

if(sql_query($sql)){
    die('y');
}else{
    die('n');
}


?>