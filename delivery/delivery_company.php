<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);

$it = sql_fetch("select * from g5_write_product where wr_id = '{$wr_id}'");
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
</style>
<div class="new_win">
    <h1>배송사 관리</h1>


  
	
    <div id="excelfile_upload">
		<form name="addFrm" action="./delivery_company_update.php" method="post" autocomplete="off" style="padding-bottom:10px;">
		<input type="hidden" name="mode" value="add">
		  <select name="wr_use">
			<option value="1">사용함</option>
			<option value="0">사용안함</option>
		  </select>
		<input type="text" name="wr_name" value="" class="frm_input required" required placeholder="배송사 이름">
		<input type="text" name="wr_percent" value="" class="frm_input " placeholder="유류할증율">
		<button class="btn_b01">배송사 추가</button>
		</form>
		
	  <form name="searchFrm" method="get" autocomplete="off" style="padding-top:10px; border-top:2px solid #f2f2f2">
		 <input type="text" name="stx" value="<?php echo urldecode($stx)?>" class="frm_input "  placeholder="검색어를 입력하세요.">
		<button class="btn_b01">검색</button>
	   </form>
    </div>
	</form>
	
	<form name="frm" action="./sales1_search_update.php" method="post" onsubmit="return chkfrm(this);">
	<div id="excelfile_upload" class="result_list" style="padding:12px;">
		
		<div style="clear:both"></div>
		<div class="tbl_head01 tbl_wrap">
		<table>
			<thead style="position:sticky;top:0;">
		
			<tr >
				<th>코드</th>
				<th>배송사명</th>
				<th>유류할증율</th>
				<th>사용여부</th>
				<th>관리</th>
			</tr>
			</thead>
			<tbody>
				<?php
				$kor_stock = 0;
				$sql_common = " from g5_delivery_company ";
				
				if($stx) {
					$stx = trim($_GET['stx']);
					$sql_search .= " where (wr_name LIKE '%$stx%' or wr_code = '$stx%')";
				}
				$sql_search .= " order by wr_code asc";
				$sql = " select * {$sql_common} {$sql_search}  ";
				
				$result = sql_query($sql);
				for($i=0; $row=sql_fetch_array($result); $i++) {
					
				
					$bg = 'bg' . ($i % 2);
					
				?>
				<tr class="<?php echo $bg; ?>">
					<td><?php echo $row['wr_code']?></td>
					<td><input type="text" class="wr_name frm_input" value="<?php echo $row['wr_name']?>"></td>
					<td><input type="text" class="wr_percent frm_input" value="<?php echo $row['wr_percent']?>" style="width:50px;text-align:right"></td>
					<td><input type="checkbox" class="wr_use" value="1" <?php echo get_checked($row['wr_use'], 1)?>></td>
					<td>
					<button type="button" class="btn02 modify" data="<?php echo $row['wr_code']?>">수정</button>
					
					<button type="button" class="btn01 delete" data="<?php echo $row['wr_code']?>">삭제</button>
				
					</td>
				</tr>
				<?php 
					
				}?>
			</tbody>
			
			
		</table>
		</div>
	</div>
	
	
	
	</div>


</div>
<script>

$(function(){
	
	
	$('.modify').bind('click', function(){
		
		let wr_name = $(this).closest('tr').find('.wr_name').val();
		let wr_percent = $(this).closest('tr').find('.wr_percent').val();
		let wr_use = $(this).closest('tr').find('.wr_use').is(':checked');
		let wr_use_val = 0;
		
		if(wr_use) {
			wr_use_val = 1;
		}
		
		if(!wr_name) {
			alert('배송사명을 입력하세요');
			$(this).closest('tr').find('.wr_name').focus();
			return false;
		}
		
		$.post('./delivery_company_update.php', {  wr_name : wr_name, wr_use : wr_use_val, wr_percent: wr_percent, wr_code : $(this).attr('data'), mode : 'mod' }, function(data){
			
			if(data == "y") {
				alert('배송사정보가 수정되었습니다.');
			} else {
				alert('처리 중 오류가 발생했습니다.');
			}
			
		})
	})
	
	$('.delete').bind('click', function(){
		
		if(confirm('정말 배송사를 삭제하시겠습니까?\n삭제 후 데이터 복구는 불가능합니다.')) {
			
			$(this).closest('tr').css('display', 'none');
			
			$.post('./delivery_company_update.php', { wr_code : $(this).attr('data'), mode : 'del' }, function(data){
				
				if(data == "y") {
					alert('배송사가 삭제되었습니다.');
				} else {
					alert('처리 중 오류가 발생했습니다.');
				}
				
			})
		} else {
			return false;
		}
	})
	
	
	
})

</script>
<?php
include_once(G5_PATH.'/tail.sub.php');