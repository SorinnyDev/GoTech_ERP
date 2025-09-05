<?
include_once('./_common.php');
$seq = str_replace(",","','",$seq_arr);
$sql = "SELECT * FROM V_sales0_list WHERE seq IN('".$seq."')";
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
.tbl_frm01 tbody tr td{
	text-align: center;
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
						<th><input type="checkbox" class="ALL_unit_check"></th>
						<th>원주문번호</th>
						<th>주문번호</th>
						<th>대표코드</th>
						<th>SKU</th>
						<th>상품명</th>
						<th>갯수</th>
						<th>단가</th>
						<th>신고가격</th>
						<th>정산유무</th>
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
								<input type="checkbox" name="seq_arr[]" value="<?=$row['seq']?>"/>
							</td>
							<td><?=$row['wr_ori_order_num']?></td>
							<td><?=$row['wr_order_num']?></td>
							<td><?=$code?></td>
							<td><?=$SKU?></td>
							<td><?=$row['product_nm']?></td>
							<td><?=$row['wr_ea']?></td>
							<td><?=$row['wr_danga']?></td>
							<td><?=$row['wr_singo']?></td>
							<td>
								<?if($row['wr_cal_chk'] == "Y"){?>
									정산 완료
								<?}else{?>
									미정산
								<?}?>
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
	$(".ALL_unit_check").bind("click",function(){
		var chk = $(this).is(":checked");
		$("input[name='seq_arr[]']").prop("checked",chk);
	});

	$("input[name='seq_arr[]']").bind("click",function(){
		var chk = true;
		$("input[name='seq_arr[]']").each(function(){
			if($(this).is(":checked") == false){
				chk = false;
			}
		});
		$(".ALL_unit_check").prop("checked",chk);
	});
});
function fnCalcUnitOK(){
	$("#mode").val("OK");
	$("#calc_frm").ajaxSubmit({
		url:"ajax.acc3_proc.php",
		type:"POST",
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

function fnCalcUnitCANCEL(){
	$("#mode").val("CANCEL");
	$("#calc_frm").ajaxSubmit({
		url:"ajax.acc3_proc.php",
		type:"POST",
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