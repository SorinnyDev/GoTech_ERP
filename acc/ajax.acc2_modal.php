<?
include_once('./_common.php');
$seq = str_replace(",","','",$seq_arr);
$sql = "SELECT * FROM V_sales2_list WHERE seq IN('".$seq."')";
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
ul{
	list-style:none;
	margin:0;
	padding:0;
}
li{
	margin:0;
	padding:0;
	border:0;
	float:left;
	display:inline;
}
</style>
<fieldset style="padding:10px">
	<h3><?=$row['wr_order_num']?></h3>
	<form name="calc_frm" id="calc_frm" method="POST">
		<input type="hidden" name="mode" id="mode" value=""/>
		<div class="tbl_frm01 tbl_wrap" style="margin-top:40px;">
			<table id="tbl_frm01">
				<thead>
					<tr>
						<th><input type="checkbox" id="ALL_unit_check"></th>
						<th>주문번호</th>
						<th>대표코드</th>
						<th>SKU</th>
						<th>상품명</th>
						<th>수량</th>
						<th>단가</th>
						<th>배송비</th>
						<th>총매입액</th>
						<th>지급금</th>
						<th>미지급금</th>
					</tr>
				</thead>
				<tbody>
					<?while($row = sql_fetch_array($rs)){
						# 출력할 대표코드
						$code = $row['code1'];
						if(!$code){
							$code = $row['code2'];
							if(!$code){
								$code = $row['code3'];
							}
						}

						# 출력할 SKU
						$SKU = $row['sku1'];
						if(!$SKU){
							$SKU = $row['sku2'];
							if(!$SKU){
								$SKU = $row['sku3'];
								if(!$SKU){
									$SKU = $row['sku4'];
									if(!$SKU){
										$SKU = $row['sku5'];
										if(!$SKU){
											$SKU = $row['sku6'];
										}
									}
								}
							}
						}
					?>
						<tr>
							<td>
								<input type="checkbox" name="seq_unit[]" value="<?=$row['seq']?>">
							</td>
							<td><?=$row['wr_order_num']?></td>
							<td><?=$code?></td>
							<td><?=$SKU?></td>
							<td><?=$row['product_nm']?></td>
							<td><?=$row['wr_order_ea']?></td>
							<td><?=number_format($row['wr_order_price'])?></td>
							<td><?=number_format($row['wr_order_fee'])?></td>
							<td>
								<input type="hidden" id="wr_order_total_<?=$row['seq']?>" value="<?=$row['wr_order_total']?>"/>
								<?=number_format($row['wr_order_total'])?>
							</td>
							<td>
								<input type="text" name="wr_warehouse_price_arr[<?=$row['seq']?>]" class="frm_input warehouse_price" value="<?=number_format($row['wr_warehouse_price'])?>" seq="<?=$row['seq']?>"/>
							</td>
							<td>
								<input type="text" name="wr_misu_arr[<?=$row['seq']?>]" class="frm_input readonly" value="<?=number_format($row['wr_misu'])?>" readonly/>
							</td>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
	</form>
	<div class="win_btn btn_confirm" style="margin-top:20px;text-align:center">
		<button type="button" title="저장" class="btn_b01" onclick="fnCalcUnitOK();" style="width:100px;margin-top:15px">정산처리</button>
		<button type="button" title="저장" class="btn_b02" onclick="fnCalcUnitCANCEL();" style="width:100px;margin-top:15px">정산취소</button>
		<button type="button" class="modal_cls" title="닫기" onclick="close_modal();"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
	</div>
</fieldset>
<script type="text/javascript">
$(document).ready(function(){
	$(".warehouse_price").bind("keyup",function(){
		var seq = $(this).attr("seq");
		var wr_order_total = $("#wr_order_total_"+seq).val();
		var price = get_number($(this).val());
		var wr_misu = parseFloat(wr_order_total) - parseFloat(price);
		$(this).val(number_format(price));
		$("input[name='wr_misu_arr["+seq+"]']").val(number_format(wr_misu));
	});
	
	//전체 선택 / 전체 취소
	$("#ALL_unit_check").bind("click",function(){
		var chk = $(this).is(":checked");
		$("input[name='seq_unit[]']").prop("checked",chk);
	});

	// 체크박스 클릭시 전체 선택되었을 경우 전체 선택 박스 체크 처리
	$("input[name='seq_unit[]']").bind("click",function(){
		var chk = true;
		$("input[name='seq_unit[]']").each(function(){
			if($(this).is(":checked") == false){
				chk = false;
			}
		});
		$("#ALL_unit_check").prop("checked",chk);
	});
});

function fnCalcUnitOK(){
	$("#calc_frm").ajaxSubmit({
		url:"./ajax.acc2_proc.php",
		dataType:"json",
		success:function(data){
			if(isDefined(data.message)){
				alert(data.message);
			}
			if(data.ret_code == true){
				document.location.reload();
			}
		}
	});
}
</script>