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
  th, td {
    position: relative;
  }
 .resize-handle {
    position: absolute;
    width: 5px;
    height: 100%;
	top:0;
    right: -2px;
    cursor: col-resize;
  }
</style>
<div class="new_win">
    <h1>엑셀자료 가져오기</h1>


    <form name="searchFrm" method="get" autocomplete="off">

    <div id="excelfile_upload">
        <label for="excelfile">도메인</label>
        <select name="wr_18">
			<option value="">선택하세요</option>
			<?php echo get_domain_option($_GET['wr_18'])?>
		</select>
		
		<label for="excelfile" style="margin-left:50px">기간</label>
        <input type="date" name="wr_19_s" value="<?php echo urldecode($wr_19_s)?>" class="frm_input"> ~
        <input type="date" name="wr_19_e" value="<?php echo urldecode($wr_19_e)?>" class="frm_input">
		
		<button class="btn btn_admin">검색</button>
    </div>
	</form>
	
	<form name="frm" action="./sales0_search_update.php" method="post">
	<div id="excelfile_upload" class="result_list" style="overflow-x:scroll;max-height:90vh;padding:0">
		
		<div class="tbl_head01 tbl_wrap" style="min-width:1700px;width:100%;margin-bottom:70px">
		<table>
			<thead style="position:sticky;top:0;z-index:99">
			<tr>
				<th style="width:20px"><input type="checkbox" onclick="selectAll(this)"></th>
				<th style="width:100px"><div class="resize-handle"></div>순번</th>
				<th style="width:100px"><div class="resize-handle"></div>업로드일자</th>
				<th style="width:100px"><div class="resize-handle"></div>도메인명</th>
				<th style="width:200px"><div class="resize-handle"></div>주문번호</th>
				<th style="width:150px"><div class="resize-handle"></div>SKU</th>
				
				<th style="width:150px"><div class="resize-handle"></div>구매자 이름</th>
				<th style="width:250px"><div class="resize-handle"></div>주소1</th>
				<th style="width:250px"><div class="resize-handle"></div>주소2</th>
				<th style="width:100px"><div class="resize-handle"></div>도시명</th>
				<th style="width:100px"><div class="resize-handle"></div>주명</th>
				<th style="width:100px"><div class="resize-handle"></div>나라명</th>
				<th style="width:70px"><div class="resize-handle"></div>우편번호</th>
				<th style="width:150px"><div class="resize-handle"></div>전화번호</th>
				<th style="width:150px"><div class="resize-handle"></div>이메일</th>
				<th style="width:70px"><div class="resize-handle"></div>수량</th>
				<th style="width:70px"><div class="resize-handle"></div>박스수</th>
				<th style="width:70px"><div class="resize-handle"></div>단가</th>
				<th style="width:70px"><div class="resize-handle"></div>신고가격</th>
				<th style="width:70px"><div class="resize-handle"></div>통화</th>
				<th style="width:400px"><div class="resize-handle"></div>상품명</th>
				<th style="width:100px"><div class="resize-handle"></div>배송코드</th>
				<th style="width:300px"><div class="resize-handle"></div>비고</th>
			</tr>
			</thead>
			<tbody>
				<?php 
				if($wr_18)
					$sql_search .= " and wr_18 = '{$wr_18}'";
				
				if($wr_19_s && $wr_19_e) {
					$sql_search .= " and wr_datetime BETWEEN '{$wr_19_s} 00:00:00' AND '{$wr_19_e} 23:59:59' ";
				}
				
				
				if($sql_search) {
				$sql = "select * from g5_write_sales where (1) {$sql_search} order by wr_id desc";
				// echo $sql;
				$rst = sql_query($sql);
				for($i=0; $row=sql_fetch_array($rst); $i++) {
					
					$chk = sql_fetch("select * from g5_sales0_list where wr_id = '{$row['wr_id']}'");
					
					if($chk) continue; //중복체크
					
					$item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_16'])."' or wr_27 = '".addslashes($row['wr_16'])."' or wr_28 = '".addslashes($row['wr_16'])."' or wr_29 = '".addslashes($row['wr_16'])."' or wr_30 = '".addslashes($row['wr_16'])."' or wr_31 = '".addslashes($row['wr_16'])."')");
					
					
					
					if($item['wr_33'] == "세트") {
					
						$item_34 = explode('|@|', $item['wr_34']);
						$item_35 = explode('|@|', $item['wr_35']);
						
						for($a=0; $a<count($item_34); $a++) {
							
							$item2 = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($item_34[$a])."' or wr_27 = '".addslashes($item_34[$a])."' or wr_28 = '".addslashes($item_34[$a])."' or wr_29 = '".addslashes($item_34[$a])."' or wr_30 = '".addslashes($item_34[$a])."' or wr_31 = '".addslashes($item_34[$a])."')");
							
							$disabled = "";
							// var_dump($item);
							if(!$item2) {
								$bg = 'class="not_item"';
								$disabled = "disabled";
							}
							
							if( $row['wr_16'] == '' ) {
								$disabled = "disabled";
								 $row['wr_16'] = '등록하기';
							}
					?>
					<tr <?php echo $empty_pdt?>>
						<td><input type="checkbox" name="chk_wr_id[]" value="<?php echo $row['wr_id']?>|<?php echo $item2['wr_id']?>" <?php echo $disabled?> class="chkbox set_<?php echo $row['wr_id']?>"></td>
						<td style="color:blue">SET<br>(<?php echo $row['wr_subject']?>)</td>
						<td><?php echo substr($row['wr_datetime'],0,10)?></td>
						<td><?php echo $row['wr_18']?></td>
						<td><?php echo $row['wr_subject']?></td>
						<td>
						<?php echo $item_34[$a]?><br><span style="color:blue">(
                        <?php if($disabled) { ?>
                            <i class="fa fa-times-circle" aria-hidden="true" style="color:red" title="제품연동 안됨"></i>
						    <a href="#none" onclick="add_pop('<?php echo urlencode($item_34[$a])?>', '', '<?php echo $row['wr_id']?>')">
						<?php echo $row['wr_16']; ?>
						</a>
						<?php } else {
							echo $row['wr_16'];
						}?>)</span></td>
						
						<td><?php echo $row['wr_2']?></td>
						<td><?php echo $row['wr_3']?></td>
						<td><?php echo $row['wr_4']?></td>
						<td><?php echo $row['wr_5']?></td>
						<td><?php echo $row['wr_6']?></td>
						<td><?php echo $row['wr_7']?></td>
						<td><?php echo $row['wr_8']?></td>
						<td><?php echo $row['wr_9']?></td>
						<td><?php echo $row['wr_10']?></td>
						<td><?php echo ($item_35[$a]*$row['wr_11'])?></td>
						<td><?php echo ($item_35[$a]*$row['wr_11'])?></td>
						<td><?php echo $row['wr_13']?></td>
						<td><?php echo $row['wr_14']?></td>
						<td><?php echo $row['wr_15']?></td>
						
						<td><?php echo $item2['wr_subject']?></td>
						<td><?php echo $row['wr_20']?></td>
						<td><?php echo $row['wr_21']?></td>
					
					</tr>
					
					
					<?php
						}
					
					} else {
				
					
					
					
					$bg = "";
					$disabled = "";
                    // var_dump($item);
					if(!$item) {
						$bg = 'class="not_item"';
						$disabled = "disabled";
					}
					
					if( $row['wr_16'] == '' ) {
						$disabled = "disabled";
						 $row['wr_16'] = '등록하기';
					}
				?>
				<tr >
					<td><input type="checkbox" name="chk_wr_id[]" value="<?php echo $row['wr_id']?>" <?php echo $disabled?>></td>
					<td><?php echo ($i+1)?></td>
					<td><?php echo substr($row['wr_datetime'],0,10)?></td>
					<td><?php echo $row['wr_18']?></td>
					<td><?php echo $row['wr_subject']?></td>
					<td>
					<?php if($disabled) {?>
					<i class="fa fa-times-circle" aria-hidden="true" style="color:red" title="제품연동 안됨"></i>
					<a href="#none" onclick="add_pop('<?php echo urlencode($row['wr_16'])?>', '<?php echo urlencode($row['wr_17'])?>', '<?php echo $row['wr_id']?>')">
					<?php echo $row['wr_16'];?>
					</a>
					<?php } else {
						echo $row['wr_16'];
					}?></td>
					
					<td><?php echo $row['wr_2']?></td>
					<td><?php echo $row['wr_3']?></td>
					<td><?php echo $row['wr_4']?></td>
					<td><?php echo $row['wr_5']?></td>
					<td><?php echo $row['wr_6']?></td>
					<td><?php echo $row['wr_7']?></td>
					<td><?php echo $row['wr_8']?></td>
					<td><?php echo $row['wr_9']?></td>
					<td><?php echo $row['wr_10']?></td>
					<td><?php echo $row['wr_11']?></td>
					<td><?php echo $row['wr_12']?></td>
					<td><?php echo $row['wr_13']?></td>
					<td><?php echo $row['wr_14']?></td>
					<td><?php echo $row['wr_15']?></td>
					
					<td><?php echo $row['wr_17']?></td>
					<td><?php echo $row['wr_20']?></td>
					<td><?php echo $row['wr_21']?></td>
					
				</tr>
				<?php }
				}
				
				}?>
			</tbody>
			
			
		</table>
		</div>
	
	</div>
	
    <div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;background:#fff;border-top:1px solid #ddd;margin-bottom:0">
		매출적용 일자 <input type="date" name="wr_date" value="<?php echo G5_TIME_YMD?>" required>
        <input type="submit" value="매출생성" class="btn_submit btn">
        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
    </div>
</form>
    

</div>
<script>
function add_pop(sku,pname,wr_id) {
	
	window.open("/bbs/write.php?bo_table=product&sku="+sku+"&pname="+pname+"&swr_id="+wr_id, "add_item", "left=50, top=50, width=550, height=650, scrollbars=1");
	
}
$(function(){
	$('input[name="chk_wr_id[]"]').bind('click', function(){
		let stat = $(this).attr('checked');
		let v = $(this).val();
		let vc = v.split('|');
		
		
		if(stat == "checked") {
			$('.set_'+vc[0]).prop('checked', true);
		} else {
			$('.set_'+vc[0]).prop('checked', false);
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
function selectAll(selectAll)  {
  const checkboxes 
       = document.getElementsByName('chk_wr_id[]');
  
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
</script>
<?php
include_once(G5_PATH.'/tail.sub.php');