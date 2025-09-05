<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);
?>
<style>
.not_item td { background:red; color:#fff }
.pg a { margin:0 5px }
</style>
<div class="new_win">
    <h1>제품검색 및 선택</h1>


    <form name="searchFrm" method="get" autocomplete="off">

    <div id="excelfile_upload">
      
        <input type="text" name="stx" value="<?php echo urldecode($stx)?>" class="frm_input"> 
       
		<button class="btn btn_admin">검색</button>
    </div>
	</form>
	
	<form name="frm" action="./sales1_search_update.php" method="post" onsubmit="return chkfrm(this);">
	<div id="excelfile_upload" class="result_list" style="overflow-y:scroll;max-height:400px;padding:0">
		
		<div class="tbl_head01 tbl_wrap" >
		<table>
			<thead style="position:sticky;top:0;">
			<tr>
				
				<th style="width:100px">대표코드</th>
				<th style="width:100px">SKU</th>
				<th style="width:400px">상품명</th>
				<th style="width:70px">담당자</th>
			
			
			</tr>
			</thead>
			<tbody>
				<?php 
				$sql_common = " from g5_write_product ";
				$sql_search = " where (1) ";


				if($stx) {
					$sql_search .= " and (
				wr_1 LIKE '%{$stx}%' or
				wr_5 LIKE '%{$stx}%' or
				wr_6 LIKE '%{$stx}%' or
				wr_4 LIKE '%{$stx}%' or
				wr_27 LIKE '%{$stx}%' or 
				wr_28 LIKE '%{$stx}%' or 
				wr_29 LIKE '%{$stx}%' or 
				wr_30 LIKE '%{$stx}%' or 
				wr_31 LIKE '%{$stx}%' or 
				wr_subject LIKE '%$stx%'
				)";
				}

				if (!$sst) {
					$sst  = "wr_id";
					$sod = "desc";
				}
				$sql_order = " order by $sst $sod ";

				$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
				$row = sql_fetch($sql);
				$total_count = $row['cnt'];

				$rows = $config['cf_page_rows'];
				$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
				if ($page < 1) {
					$page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
				}
				$from_record = ($page - 1) * $rows; // 시작 열을 구함

				$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
				$result = sql_query($sql);
				for($i=0; $row=sql_fetch_array($result); $i++) {
					
				
					$bg = "";
					$ea_chk = "";
					$ea_chk2 = "";
					
					if($item['wr_32'] < $row['wr_ea']) {
						$ea_chk = ";color:red;font-weight:600";
					}
					if($item['wr_36'] < $row['wr_ea']) {
						$ea_chk2 = ";color:red;font-weight:600";
					}
					
					$set = "";
					if($row['wr_set_sku']) {
						
						$set = '<br><span style="color:blue">('.$row['wr_set_sku'].')</span>';
					}
					
				?>
				<tr >
					<td ><?php echo $row['wr_5']?></td>
					<td ><?php echo $row['wr_1']?></td>
					<td style="cursor:pointer" onmouseover="this.style.background='#ddd'" onmouseout="this.style.background='#fff'" onclick="add_item('<?php echo addslashes($row['wr_subject'])?>', '<?php echo $row['wr_10']?>', '<?php echo $row['wr_18']?>', '<?php echo $row['wr_19']?>', '<?php echo $row['wr_14']?>', '<?php echo $row['wr_15']?>', '<?php echo $row['wr_16']?>')"><?php echo $row['wr_subject']?></td>
					<td><?php echo $row['wr_name']?></td>
				</tr>
				<?php }
				
				?>
			</tbody>
			
			
		</table>
		</div>
		
	</div>
	
 
</form>
    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?' . $qstr . '&amp;page='); ?>

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
})
function add_pop(sku,pname,wr_id) {
	
	window.open("/bbs/write.php?bo_table=product&sku="+sku+"&pname="+pname+"&swr_id="+wr_id, "add_item", "left=50, top=50, width=550, height=650, scrollbars=1");
	
}

function add_item(product, wr_10, wr_18, wr_19, wr_14, wr_15, wr_16){
	
	opener.window.document.searchFrm.product.value = product;
	opener.window.document.searchFrm.weight.value = wr_10;
	opener.window.document.searchFrm.weight1.value = wr_18;
	opener.window.document.searchFrm.weight2.value = wr_19;
	opener.window.document.searchFrm.width.value = wr_14;
	opener.window.document.searchFrm.length.value = wr_15;
	opener.window.document.searchFrm.height.value = wr_16;
	
	opener.window.document.searchFrm.submit();
	
	window.close();
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
}

function chkfrm(f){
	f.act.value = document.pressed;
	
	if (document.pressed == "발주생성") {
		
		if (!confirm("선택한 매출자료를 발주생성 하시겠습니까?")) {
			return false;
		}
	}
	
	if (document.pressed == "한국창고 출고") {
		
		if (!confirm("선택한 매출자료를 한국창고에서 출고등록 하시겠습니까?")) {
			return false;
		}
	}
	
	if (document.pressed == "미국창고 출고") {
		
		if (!confirm("선택한 매출자료를 미국창고에서 출고등록 하시겠습니까?")) {
			return false;
		}
	}
}
</script>
<?php
include_once(G5_PATH.'/tail.sub.php');