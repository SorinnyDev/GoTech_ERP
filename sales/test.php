<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);

$wr_order_num = sql_fetch("SELECT wr_order_num FROM g5_sales0_list  WHERE seq = '{$_POST['seq'][$i]}' ")['wr_order_num'];
?>
<style>
.not_item td { background:red; color:#fff }
.tbl_wrap td{padding:30px 0 !important}
</style>
<div class="new_win">
    <h1>재고관리</h1>


    <form name="searchFrm" method="get" autocomplete="off">

    <div id="excelfile_upload">
        <label for="excelfile">창고선택</label>
        <select name="wr_18">
			<option value="">지오택 본사창고</option>
			<?php 
			$sql="select * from g5_code where gc_use = 1 order by gc_name";
			$rst = sql_query($sql);
			while($row=sql_fetch_array($rst)) {
			?>
			<option value="<?php echo $row['gc_name']?>" <?php echo get_selected($_GET['wr_18'], $row['gc_name'])?>><?php echo $row['gc_name']?></option>
			<?php }?>
		</select>
		<label for="excelfile" style="margin-left:40px">검색</label>
		<input type="text" name="stx" value="SKU, 바코드, 상품명등으로 검색" class="frm_input" style="width:250px">
		<button class="btn btn_admin">검색</button>
    </div>
	</form>
	
	<form name="frm" action="./sales1_search_update.php" method="post">
	<div id="excelfile_upload" class="result_list" style="overflow-x:scroll;padding:0">
		
		<div class="tbl_head01 tbl_wrap">
		<table>
		
			<tbody>
				
				<tr >
					<td style="background:#ddd">1-1</td>
					<td>2-1</td>
					<td>3-1</td>
					<td>4-1</td>
					<td>5-1</td>
				</tr>
				<tr><td colspan="5"></td></tr>
				<tr >
					<td>1-2</td>
					<td>2-2</td>
					<td>3-2</td>
					<td>4-2</td>
					<td>5-2</td>
				</tr>
				<tr><td colspan="5"></td></tr>
				<tr >
					<td>1-3</td>
					<td>2-3</td>
					<td>3-3</td>
					<td>4-3</td>
					<td>5-3</td>
				</tr>
				<tr><td colspan="5"></td></tr>
				<tr >
					<td>1-4</td>
					<td>2-4</td>
					<td>3-4</td>
					<td>4-4</td>
					<td>5-4</td>
				</tr>
				
			</tbody>
			
			
		</table>
		</div>
	
	</div>
	
    <div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;">
        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
    </div>
</form>
    

</div>
<script>
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
}
</script>
<?php
include_once(G5_PATH.'/tail.sub.php');