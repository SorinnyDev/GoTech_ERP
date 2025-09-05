<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);

$title = "반품상품";
if($w == 'u'){
	$row = sql_fetch("select * from g5_return_img where seq = '{$seq}'");
	if(!$row)
		alert_close('정보가 없습니다.');
	
	$return = sql_fetch("select * from g5_return_list where seq = '{$row['return_id']}'");
	$item = sql_fetch("select wr_subject from g5_write_product where wr_id = '{$row['product_id']}'");
	$title .= " 사진수정";
} else {
	$title .=" 사진등록";
}
?>
<style>
.not_item td { background:red; color:#fff }
.pg a { margin:0 5px }
.tbl_head01 td { background:none }
.move_stock th{ width:100px;background:#ddd }
.move_stock select{ width:30% !important }
.move_stock button { width:30% !important; height:35px; line-height:35px; border:1px solid #2aba8a; background:#2aba8a; color:#fff }
.move_stock2 td {padding:15px }
.down_arrow{position:relative}
.down_arrow::after{content:"↓"; position:absolute; top:-14px;font-size:20px;font-weight:600;left:50%;background:#fff; margin-left:-20px;color:#2aba8a }
.diabled_btn { background:#ddd; cursor:not-allowed }
#excelfile_upload strong { display:inline-block; width:70px; margin-bottom:5px }
</style>
<div class="new_win">
    <h1><?php echo $title?></h1>


    <form name="frm" action="./return_img_pop_update.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="w" value="<?php echo $w?>">
	<input type="hidden" name="seq" value="<?php echo $row['seq']?>">
	<input type="hidden" name="return_id" value="<?php echo $row['return_id']?>">
	<input type="hidden" name="product_id" value="<?php echo $row['product_id']?>">
	
    <div id="excelfile_upload">
      <h2 style="margin-left:0">반품정보 선택</h2>
	  <input type="text" name="order_num" id="order_num" value="<?php echo $return['wr_order_num']?>" placeholder="주문번호" class="frm_input" style="width:100px" readonly>
	  <input type="text" name="pdt_name" id="pdt_name" value="<?php echo $item['wr_subject']?>" placeholder="상품명" class="frm_input" style="width:250px" readonly>
	 
	  <button class="btn_b01" type="button" onclick="pop_search()">검색</button>
	 
    </div>
	
	
	<div id="excelfile_upload" class="result_list" style="padding:12px;">
		
		<div style="clear:both"></div>
		<div class="tbl_frm01 tbl_wrap" style="">
		<table>
			
			<tbody>
				<?php for($i=1; $i<=5; $i++) {?>
				<tr class="<?php echo $bg; ?>">
				<td>사진 <?php echo $i?></td>
				<td><input type="file" name="wr_img<?php echo $i?>">
				
				<?php if($w == 'u' && $row['wr_img'.$i]) {?>
				<label><img src="<?php echo G5_DATA_URL?>/return/<?php echo $row['wr_img'.$i]?>" width="50" height="50">
				<input type="checkbox" name="wr_img_del<?php echo $i?>" value="1"> 사진삭제</label>
				<?php }?>
				</td>
				</tr>
				<?php }?>
			</tbody>
			
			
		</table>
		</div>
		<textarea name="wr_memo" id="wr_memo" placeholder="메모"><?php echo $row['wr_memo']?></textarea>
	<div style="clear:both"></div>
	 <div class="win_btn btn_confirm tw-mt-2" style="">
		
		
        <input type="submit" value="확인" class="btn_submit btn">
		
        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
    </div>
	</div>
	
	
	
	</div>


</div>
<script>

function pop_search(){
	var _width = '650';
    var _height = '450';
 
    var _left = Math.ceil(( window.screen.width - _width )/2);
    var _top = Math.ceil(( window.screen.height - _height )/2); 

	window.open("./return_img_search.php", "return_img_search", "left="+_left+", top="+_top+", width="+_width+", height="+_height+", scrollbars=1");
	
	return false;
}

</script>
<?php
include_once(G5_PATH.'/tail.sub.php');