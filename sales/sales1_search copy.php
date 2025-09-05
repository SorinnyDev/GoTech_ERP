<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);

if(!$wr_19_s)
	$wr_19_s = G5_TIME_YMD;

if(!$wr_19_e)
	$wr_19_e = G5_TIME_YMD;

?>
<style>
.not_item td { background:red; color:#fff }
.no_ea1, .no_ea2, .no_ea3 { font-weight:600; color:red !important}
.bg1 td{ background:#eff3f9 }
</style>
<div class="new_win">
    <h1>매출자료 가져오기</h1>


    <form name="searchFrm" method="get" autocomplete="off">

    <div id="excelfile_upload">
        <label for="excelfile">도메인</label>
        <select name="wr_18">
			<option value="">선택하세요</option>
			<option value="dodoskin" <?php echo get_selected($_GET['wr_18'], 'dodoskin')?>>dodoskin</option>
			<option value="eBay" <?php echo get_selected($_GET['wr_18'], 'eBay')?>>eBay</option>
			
		</select>
		
		<label for="excelfile" style="margin-left:50px">매출기간</label>
        <input type="date" name="wr_19_s" value="<?php echo urldecode($wr_19_s)?>" class="frm_input"> ~
        <input type="date" name="wr_19_e" value="<?php echo urldecode($wr_19_e)?>" class="frm_input">
		
		<button class="btn btn_admin">검색</button>
    </div>
	</form>
	
	<form name="frm" action="./sales1_search_update.php" method="post" onsubmit="return chkfrm(this);">
	<div id="excelfile_upload" class="result_list" style="overflow-x:scroll;max-height:400px;padding:0">
		
		<div class="tbl_head01 tbl_wrap" style="width:1700px">
		<table>
			<thead style="position:sticky;top:0;">
			<tr>
				<th style="width:20px"><input type="checkbox" onclick="selectAll(this)"></th>
				<th style="width:50px">순번</th>
				<th style="width:100px">매출일자</th>
				<th style="width:100px">도메인명</th>
				<th style="width:200px">주문번호</th>
				<th style="width:150px">SKU</th>
				<th style="width:400px">상품명</th>
				<th style="width:70px">한국재고</th>
				<th style="width:70px">미국재고</th>
				<th style="width:70px">FBA재고</th>
				<th style="width:70px">주문수량</th>
				
				<th style="width:150px">주문자명</th>
				<th style="width:70px">단가</th>
				<th style="width:70px">신고가격</th>
				<th style="width:70px">개당무게</th>
				<th style="width:70px">총 무게</th>
				<th style="width:150px">HS CODE</th>
				<th style="width:150px">나라명</th>
				<th style="width:150px">관리</th>
			
			</tr>
			</thead>
			<tbody>
				<?php 
				if($wr_18)
					$sql_search .= " and wr_domain = '{$wr_18}'";
				
				if($wr_19_s && $wr_19_e) {
					$sql_search .= " and wr_date BETWEEN '{$wr_19_s}' AND '{$wr_19_e}' ";
				}
				
				
				if($sql_search) {
				$sql = "select * from g5_sales0_list where (1) {$sql_search} and wr_date != '' and wr_chk = 0 order by wr_order_num asc";
				

				$rst = sql_query($sql);
				for($i=0; $row=sql_fetch_array($rst); $i++) {
					$item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");
					
				
					$bg = "";
					$ea_chk = "";
					$ea_chk2 = "";
                    $new_icon = "";
					
					if($item['wr_32'] < $row['wr_ea']) {
						$ea_chk = 'class="no_ea1"';
					}
					if($item['wr_36'] < $row['wr_ea']) {
						$ea_chk2 = 'class="no_ea2"';
					}
                    if($item['wr_42'] < $row['wr_ea']) {
						$ea_chk3 = 'class="no_ea3"';
					}
					
                    if($item['wr_38']=="Y"){
                        $new_icon = "<img src='".G5_IMG_URL."/new.png' alt='없음' width='25' height='25'/>";
                    }

					$set = "";
					if($row['wr_set_sku']) {
						
						$set = '<br><span style="color:blue">('.$row['wr_set_sku'].')</span>';
					}
					$bg = 'bg' . ($i % 2);
				?>
				<tr class="<?php echo $bg?>">
					<td><input type="checkbox" name="chk_seq[]" value="<?php echo $row['seq']?>" <?php echo $disabled?> class="chkbox"></td>
					<td><?php echo ($i+1)?></td>
					<td><?php echo substr($row['wr_date'],0,10)?></td>
					<td><?php echo $row['wr_domain']?></td>
					<td><?php echo $row['wr_order_num']?></td>
					<td><?php echo $row['wr_code']?><?php echo $set?></td>
					<td style="text-align:left"><?php echo $item['wr_subject']?></td>
					<td style="text-align:right;" <?php echo $ea_chk ?>><?php echo $item['wr_32']?></td>
					<td style="text-align:right;" <?php echo $ea_chk2 ?>><?php echo $new_icon.$item['wr_36']?></td>
					<td style="text-align:right;" <?php echo $ea_chk3 ?>><?php echo $item['wr_42']?></td>
					<td style="text-align:right"><?php echo $row['wr_ea']?></td>
					
					<td><?php echo $row['wr_mb_name']?></td>
					<td style="text-align:right"><?php echo $row['wr_danga']?></td>
					<td style="text-align:right"><?php echo $row['wr_singo']?></td>
					<td><?php echo $row['wr_weight1']?></td>
					<td><?php echo $row['wr_weight2']?></td>
					<td><?php echo $row['wr_hscode']?></td>
					<td><?php echo $row['wr_country']?></td>
					<td><button type="button" data="<?php echo $row['seq']?>" class="del_btn btn_b01" style="background:#9b2525;padding:0 8px; height:30px; line-height:30px">매출삭제</button></td>
					
				</tr>
				<?php }
				
				}?>
			</tbody>
			
			
		</table>
		</div>
	
	</div>
	<input type="hidden" name="act" value="">
    <div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;">
		발주적용 일자 <input type="date" name="wr_date2" value="<?php echo G5_TIME_YMD?>" placeholder="발주 적용일자" required>
        <input type="submit" value="발주생성" class="btn_submit btn" onclick="document.pressed=this.value">
        <input type="submit" value="한국창고 출고" class="btn_submit btn" style="background:#2a7eba" onclick="document.pressed=this.value">
        <input type="submit" value="미국창고 출고" class="btn_submit btn" style="background:#ba2a8e" onclick="document.pressed=this.value">
        <input type="submit" value="FBA창고 출고" class="btn_submit btn" style="background:#DC7100" onclick="document.pressed=this.value">
        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
    </div>
</form>
    

</div>
<script>
$(function(){
	$('.del_btn').bind('click', function(){
		if(confirm('정말 해당 매출자료를 삭제하시겠습니까?\n삭제 후 데이터 복구는 불가능합니다.')) {
			let el = $(this).closest('tr');
			
			$.post('./sales1_delete.php', { seq : $(this).attr('data') }, function(data) {
				
				if(data == "y") {
					el.remove();
					alert('매출정보가 삭제되었습니다.');
				} else {
					alert('처리 중 오류가 발생했습니다.');
				}
				$('#result_form').html(data);
			})
			
		} else {
			return false;
		}
	})
	
	$('.chkbox').bind('click', function(){
		let stat = $(this).is(':checked');
		
		if(stat == true) {
			$(this).closest('tr').find('td').css({'background':'#f2f2f2'});
		} else {
			$(this).closest('tr').find('td').css({'background':'#fff'});
		}
	})
})
function add_pop(sku,pname,wr_id) {
	
	window.open("/bbs/write.php?bo_table=product&sku="+sku+"&pname="+pname+"&swr_id="+wr_id, "add_item", "left=50, top=50, width=550, height=650, scrollbars=1");
	
}
function selectAll(selectAll)  {
  const checkboxes 
       = document.getElementsByName('chk_seq[]');
  
  checkboxes.forEach((checkbox) => {
	  if(checkbox.disabled == true) {
		  
	  } else {
    checkbox.checked = selectAll.checked;
	  }
  })
  
  $('.chkbox').each(function(){
		let stat = $(this).is(':checked');
	
		if(stat) {
			$(this).closest('tr').find('td').css({'background':'#f2f2f2'});
		} else {
			$(this).closest('tr').find('td').css({'background':'#fff'});
		}
	})
}
	
function chkfrm(f){
	f.act.value = document.pressed;
	
	if (document.pressed == "발주생성") {
		
		if (!confirm("선택한 매출자료를 발주생성 하시겠습니까?")) {
			return false;
		}
	}
	
	if (document.pressed == "한국창고 출고") {
		let stat = false;
		$('input:checkbox[name="chk_seq[]"]').each(function(){
			
			if($(this).is(':checked')) {
				let chk = $(this).closest('tr').find('.no_ea1');
				
				if(chk.length) {
					stat = true;
				}
			}
			
		})
		
		if(stat == true) {
			alert('선택하신 매출자료 중 [한국재고]가 부족한 제품이 있어 [한국창고 출고]가 불가합니다.');
			return false;
		}
	
		
		if (!confirm("선택한 매출자료를 [한국창고]에서 출고등록 하시겠습니까?")) {
			return false;
		}
		
	}
	
	if (document.pressed == "미국창고 출고") {
		let stat = false;
		$('input:checkbox[name="chk_seq[]"]').each(function(){
			
			if($(this).is(':checked')) {
				let chk = $(this).closest('tr').find('.no_ea2');
				
				if(chk.length) {
					stat = true;
				}
			}
			
		})
		
		if(stat == true) {
			alert('선택하신 매출자료 중 [미국재고]가 부족한 제품이 있어 [미국창고 출고]가 불가합니다.');
			return false;
		}
		
		if (!confirm("선택한 매출자료를 미국창고에서 출고등록 하시겠습니까?")) {
			return false;
		}
	}

    if (document.pressed == "FBA창고 출고") {
        alert("사용X 테스트 진행중.");
        return false;

		let stat = false;
		$('input:checkbox[name="chk_seq[]"]').each(function(){
			
			if($(this).is(':checked')) {
				let chk = $(this).closest('tr').find('.no_ea3');
				
				if(chk.length) {
					stat = true;
				}
			}
			
		})
		
		if(stat == true) {
			alert('선택하신 매출자료 중 [FBA재고]가 부족한 제품이 있어 [FBA창고 출고]가 불가합니다.');
			return false;
		}
		
		if (!confirm("선택한 매출자료를 FBA창고에서 출고등록 하시겠습니까?")) {
			return false;
		}
	}
	
}
</script>
<?php
include_once(G5_PATH.'/tail.sub.php');