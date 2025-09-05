<?
include_once('./_common.php');

$item = sql_fetch("SELECT * FROM g5_write_product WHERE wr_id='".$wr_id."'");

$sql = "SELECT A.wr_domain,A.wr_warehouse,A.wr_order_num,A.wr_date3,IFNULL(B.wr_date4,'') AS wr_date4, IF(B.wr_release_use IS NULL,'출고등록 전','출고등록') AS chul_yn,A.wr_direct_use,C.gc_name,A.wr_ea \n";
$sql .= "FROM g5_sales2_list A \n";
$sql .= "LEFT OUTER JOIN g5_sales3_list B ON B.wr_order_num = A.wr_order_num \n";
$sql .= "LEFT OUTER JOIN g5_rack C ON C.seq=A.wr_rack \n";
$sql .= "WHERE A.wr_etc_use = '0' AND IFNULL(B.wr_release_use,'0') = '0' AND A.wr_product_id='".$wr_id."' ORDER BY A.wr_date3 ASC";
$rs = sql_query($sql);
?>
<style>
.tbl_frm01 .title {
    background: #444444;
    color: #fff;
    border: 1px solid #60718b;
    font-weight: normal;
    text-align: center;
    padding: 8px 5px;
    font-size: 0.92em;
}

#tbl_frm01 td{
	text-align:center;
}

</style>
<fieldset style="padding:10px">
	<h3><?=$item['wr_subject']?> 출고 미완료</h3>
	<form name="order_frm" id="order_frm" method="POST">
		<div class="tbl_frm01 tbl_wrap" style="margin-top:40px;">
			<table id="tbl_frm01">
				<thead>
					<tr>
						<th>도메인</th>
						<th>창고</th>
						<th>랙</th>
						<th>주문번호</th>
						<th>바로출고유무</th>
						<th>입고등록일</th>
						<th>출고등록일</th>
						<th>출고등록유무</th>
						<th>수량</th>
					</tr>
				</thead>
				<tbody>
					<?if(sql_num_rows($rs) > 0){?>
						<?while($row = sql_fetch_array($rs)){
							if($row['wr_warehouse'] == "1000"){
								$ware = "한국창고";
							}else if($row['wr_warehouse'] == "3000"){
								$ware = "미국창고";
							}else if($row['wr_warehouse'] == "4000"){
								$ware = "FBA창고";
							}else if($row['wr_warehouse'] == "5000"){
								$ware = "W-FBA창고";
							}else if($row['wr_warehouse'] == "6000"){
								$ware = "U-FBA창고";
							}
						?>
							<tr>
								<td><?=$row['wr_domain']?></td>
								<td><?=$ware?></td>
								<td><?=$row['gc_name']?></td>
								<td><?=$row['wr_order_num']?></td>
								<td><?=($row['wr_direct_use'] == "0")?"발주 후 출고":"바로 출고"?></td>
								<td><?=$row['wr_date3']?></td>
								<td><?=$row['wr_date4']?></td>
								<td><?=$row['chul_yn']?></td>
								<td><?=$row['wr_ea']?></td>
							</tr>
						<?}?>
					<?}else{?>
						<tr>
							<td colspan="9">출고 미완료 건이 없습니다.</td>
						</tr>
					<?}?>
				</tbody>
			</table>
			<div class="win_btn btn_confirm" style="margin-top:20px;text-align:center">
				<?if(sql_num_rows($rs) > 0){?>
					<button type="button" title="저장" class="btn_b01" onclick="fnExcelDownUnit('<?=$wr_id?>');" style="width:100px;margin-top:15px">Excel</button>
				<?}?>
				<button type="button" class="modal_cls" title="닫기" onclick="close_modal();"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
			</div>
		</div>
	</form>
</fieldset>
<script type="text/javascript">
function fnExcelDownUnit(wr_id){
	document.location.href="./ajax.stock_miss_excel.php?wr_id="+wr_id;
}
</script>