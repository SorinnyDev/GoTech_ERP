<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');


 

@mkdir(G5_DATA_PATH."/return", G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH."/return", G5_DIR_PERMISSION);

$dir = G5_DATA_PATH."/return";
$img_sql = "";

if($w == '') {
	
	if($_POST['return_id'] == "" || $_POST['product_id'] == "") alert('잘못 된 접근입니다.');
	
	for($i=1; $i<=5; $i++) {
		if($_FILES['wr_img'.$i]['tmp_name']) {
			
			$fileinfo = pathinfo($_FILES['wr_img'.$i]['name']);
			$ext = $fileinfo['extension'];
			
			$file_ext = explode('.', $_FILES['wr_img'.$i]['name']);
			$rename = "return_".time().$i.".".$ext; 
			
			@move_uploaded_file($_FILES['wr_img'.$i]['tmp_name'], $dir."/".$rename);
			
			$img_sql .= " , wr_img{$i} = '{$rename}' ";
		}
	}
	
	$sql = "insert into g5_return_img set 
	mb_id = '{$member['mb_id']}',
	return_id = '{$return_id}',
	product_id = '{$product_id}',
	wr_memo = '{$wr_memo}',
	wr_datetime = '".G5_TIME_YMDHIS."'
	{$img_sql}
	";
	
	
	if(sql_query($sql, true)){
		alert_close('반품 사진이 등록되었습니다.\\n리스트에서 새로고침해주세요.');
	} else {
		alert('처리 중 오류가 발생했습니다.');
	}
	
} else if($w == 'u') {
	
	if($_POST['return_id'] == "" || $_POST['product_id'] == "") alert('잘못 된 접근입니다.');
	
	$chk = sql_fetch("select * from g5_return_img where seq = '{$seq}'");
	
	if(!$chk) alert('잘못 된 접근입니다.');
	
	for($i=1; $i<=5; $i++) {
		
		if($_POST['wr_img_del'.$i] == 1){
			@unlink($dir."/".$chk['wr_img'.$i]);
			$img_sql .= " , wr_img{$i} = '' ";
		}
		
		if($_FILES['wr_img'.$i]['tmp_name']) {
			
			$fileinfo = pathinfo($_FILES['wr_img'.$i]['name']);
			$ext = $fileinfo['extension'];
			
			$file_ext = explode('.', $_FILES['wr_img'.$i]['name']);
			$rename = "return_".time().$i.".".$ext; 
			
			@move_uploaded_file($_FILES['wr_img'.$i]['tmp_name'], $dir."/".$rename);
			
			$img_sql .= " , wr_img{$i} = '{$rename}' ";
		}
		
		
	}
	
	$sql = "update g5_return_img set 

	return_id = '{$return_id}',
	product_id = '{$product_id}',
	wr_memo = '{$wr_memo}'
	{$img_sql}
	where seq = '{$seq}'
	";
	
	
	if(sql_query($sql, true)){
		alert_close('반품 사진 수정이 완료되었습니다.\\n리스트에서 새로고침해주세요.');
	} else {
		alert('처리 중 오류가 발생했습니다.');
	}
	

} else if($w == 'd') {
	if(!$seq) alert('잘못 된 접근입니다.');
	
	$sql = "delete from g5_return_img where seq = '{$seq}'";
	
	if(sql_query($sql, true)){
		alert('반품 사진 삭제가 완료되었습니다.');
	} else {
		alert('처리 중 오류가 발생했습니다.');
	}
	
	
}