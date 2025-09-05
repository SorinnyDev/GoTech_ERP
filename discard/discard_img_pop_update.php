<?php
include_once('./_common.php');

if ($is_guest) alert_close('로그인 후 이용하세요.');

@mkdir(G5_DATA_PATH."/discard", G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH."/discard", G5_DIR_PERMISSION);

$dir = G5_DATA_PATH."/discard";
$img_sql = "";

if($_POST['discard_id'] == "") alert('잘못 된 접근입니다.');

$discard_img = sql_fetch("select * from g5_discard_img where id = '{$discard_img_id}'");

if ($discard_img) {
  for($i=1; $i<=5; $i++) {

    if($_POST['wr_img_del'.$i] == 1){
      @unlink($dir."/".$discard_img['wr_img'.$i]);
      $img_sql .= " , wr_img{$i} = '' ";
    }

    if($_FILES['wr_img'.$i]['tmp_name']) {

      $fileinfo = pathinfo($_FILES['wr_img'.$i]['name']);
      $ext = $fileinfo['extension'];

      $file_ext = explode('.', $_FILES['wr_img'.$i]['name']);
      $rename = "discard_".time().$i.".".$ext;

      @move_uploaded_file($_FILES['wr_img'.$i]['tmp_name'], $dir."/".$rename);

      $img_sql .= " , wr_img{$i} = '{$rename}' ";
    }
  }


  $sql = "update g5_discard_img set 
	discard_id = '{$discard_id}'
	{$img_sql}
	where id = '{$discard_img_id}'
	";

  if(sql_query($sql, true)){
    alert_close('폐기 사진 수정이 완료되었습니다.\\n리스트에서 새로고침해주세요.');
  } else {
    alert('처리 중 오류가 발생했습니다.');
  }

} else {
  for($i=1; $i<=5; $i++) {
    if($_FILES['wr_img'.$i]['tmp_name']) {

      $fileinfo = pathinfo($_FILES['wr_img'.$i]['name']);
      $ext = $fileinfo['extension'];

      $file_ext = explode('.', $_FILES['wr_img'.$i]['name']);
      $rename = "discard_".time().$i.".".$ext;

      @move_uploaded_file($_FILES['wr_img'.$i]['tmp_name'], $dir."/".$rename);

      $img_sql .= " , wr_img{$i} = '{$rename}' ";
    }
  }

  $sql = "insert into g5_discard_img set 
	mb_id = '{$member['mb_id']}',
	discard_id = '{$discard_id}',
	wr_datetime = '".G5_TIME_YMDHIS."'
	{$img_sql}
	";

  if(sql_query($sql, true)){
    alert_close('폐기 사진 등록이 완료되었습니다.\\n리스트에서 새로고침해주세요.');
  } else {
    alert('처리 중 오류가 발생했습니다.');
  }


}


