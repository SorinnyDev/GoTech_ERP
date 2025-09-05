<?
include_once("./_common.php");

$sql = "SELECT * FROM g5_write_sales WHERE wr_id='".$wr_id."'";
$row = sql_fetch($sql);

$sql = "SELECT * FROM g5_sales0_list WHERE wr_id='".$wr_id."'";
$chk = sql_fetch($sql);
$readonly = "";
if($chk['seq']){
	$readonly = "readonly";
}
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
	<h3><?=$row['wr_subject']?></h3>
	<form name="sales_frm" id="sales_frm" method="POST">
		<input type="hidden" name="wr_id" value="<?=$wr_id?>"/>
		<div class="tbl_frm01 tbl_wrap" style="margin-top:40px;">
			<table id="tbl_frm01">
				<tr>
					<th>원주문번호</th>
					<td><input type="text" name="ori_order_num" value="<?=$row['ori_order_num']?>" class="readonly" readonly/></td>
					<td colspan="6"></td>
				</tr>
				<tr>
					<th>구매자명</th>
					<td>
						<input type="text" name="wr_2" id="wr_2" value="<?=$row['wr_2']?>" class="readonly" readonly/>
					</td>
					<th>구매자연락처</th>
					<td>
						<input type="text" name="wr_9" id="wr_9" value="<?=$row['wr_9']?>" class="readonly" readonly/>
					</td>
					<th>구매자 이메일</th>
					<td>
						<input type="text" name="wr_10" id="wr_10" value="<?=$row['wr_10']?>" class="readonly" readonly/>
					</td>
					<td colspan="2"></td>
				</tr>
				<tr>
					
					<th>구매자 국가</th>
					<td>
						<input type="text" name="wr_7" id="wr_7" value="<?=$row['wr_7']?>" class="readonly" readonly/>
					</td>
					<th>구매자 주</th>
					<td>
						<input type="text" name="wr_6" id="wr_6" value="<?=$row['wr_6']?>" class="readonly" readonly/>
					</td>
					<th>구매자 도시</th>
					<td>
						<input type="text" name="wr_5" id="wr_5" value="<?=$row['wr_5']?>" class="readonly" readonly/>
					</td>
					<td colspan="2"></td>
				</tr>
				<tr>
					<th>구매자 우편번호</th>
					<td>
						<input type="text" name="wr_8" id="wr_8" value="<?=$row['wr_8']?>" class="readonly" readonly/>
					</td>
					<th>구매자 주소</th>
					<td colspan="5">
						<input type="text" name="wr_3" id="wr_3" value="<?=$row['wr_3']?>" class="readonly" style="width:49%;" readonly/>
						<input type="text" name="wr_4" id="wr_4" value="<?=$row['wr_4']?>" class="readonly" style="width:49%;" readonly/>
					</td>
				</tr>
				<tr>
					
					<th>배송지 국가</th>
					<td>
						<input type="text" name="wr_32" id="wr_32" value="<?=$row['wr_32']?>" class="readonly" readonly/>
					</td>
					<th>배송지 주</th>
					<td>
						<input type="text" name="wr_31" id="wr_31" value="<?=$row['wr_31']?>" class="readonly" readonly/>
					</td>
					<th>배송지 도시</th>
					<td>
						<input type="text" name="wr_30" id="wr_30" value="<?=$row['wr_30']?>" class="readonly" readonly/>
					</td>
					<td colspan="2"></td>
				</tr>
				<tr>
					<th>배송지 우편번호</th>
					<td>
						<input type="text" name="wr_33" id="wr_33" value="<?=$row['wr_33']?>" class="readonly" readonly/>
					</td>
					<th>배송지 주소</th>
					<td colspan="5">
						<input type="text" name="wr_28" id="wr_28" value="<?=$row['wr_28']?>" class="readonly" style="width:49%;" readonly/>
						<input type="text" name="wr_29" id="wr_29" value="<?=$row['wr_29']?>" class="readonly" style="width:49%;" readonly/>
					</td>
				</tr>
				<tr>
					<th>상품명</th>
					<td>
						<input type="text" name="wr_17" id="wr_17" value="<?=$row['wr_17']?>" class="<?=$readonly?>" <?=$readonly?>/>
					</td>
					<th>SKU</th>
					<td>
						<input type="text" name="wr_16" id="wr_16" value="<?=$row['wr_16']?>" class="<?=$readonly?>" <?=$readonly?>/>
					</td>
					<th>수량</th>
					<td>
						<input type="text" name="wr_11" id="wr_11" value="<?=$row['wr_11']?>" class="<?=$readonly?>" <?=$readonly?>/>
					</td>
					<th>박스수</th>
					<td>
						<input type="text" name="wr_12" id="wr_12" value="<?=$row['wr_11']?>" class="<?=$readonly?>" <?=$readonly?>/>
					</td>
				</tr>
				<tr>
					<th>단가</th>
					<td>
						<input type="text" name="wr_13" id="wr_13" value="<?=$row['wr_13']?>" class="<?=$readonly?>" <?=$readonly?>/>
					</td>
					<th>신고가격</th>
					<td>
						<input type="text" name="wr_14" id="wr_14" value="<?=$row['wr_14']?>" class="<?=$readonly?>" <?=$readonly?>/>
					</td>
					<th>TAX</th>
					<td>
						<input type="text" name="wr_22" id="wr_22" value="<?=$row['wr_22']?>" class="<?=$readonly?>" <?=$readonly?>/>
					</td>
					<th>SHIPPING TAX</th>
					<td>
						<input type="text" name="wr_23" id="wr_23" value="<?=$row['wr_23']?>" class="<?=$readonly?>" <?=$readonly?>/>
					</td>
				</tr>
				<tr>
					<th>수수료1</th>
					<td>
						<input type="text" name="wr_35" id="wr_35" value="<?=$row['wr_35']?>" class="<?=$readonly?>" <?=$readonly?>/>
					</td>
					<th>수수료2</th>
					<td>
						<input type="text" name="wr_36" id="wr_36" value="<?=$row['wr_36']?>" class="<?=$readonly?>" <?=$readonly?>/>
					</td>
					<td colspan="4"></td>
				</tr>
			</table>
			<div class="win_btn btn_confirm" style="margin-top:20px;text-align:center">
				<?if($readonly != "readonly"){?>
					<button type="button" title="저장" class="btn_b01" onclick="fnSaveData();" style="width:100px;margin-top:15px">저장</button>
					<button type="button" title="저장" class="btn_b02" onclick="fnResetData('<?=$wr_id?>');" style="width:100px;margin-top:15px">초기화</button>
				<?}?>
				<button type="button" class="modal_cls" title="닫기" onclick="close_modal();"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
			</div>
			
		</div>
	</form>
</fieldset>
<script type="text/javascript">
$(document).ready(function(){
	$("#wr_11, #wr_13").bind("blur",function(){
		fnCalcPrice();
	});
});
// 수량 / 단가 변경시 자동계산
function fnCalcPrice(){
	var wr_11 = $("#wr_11").val();
	var wr_13 = $("#wr_13").val();
	if(isDefined(wr_11) == false){
		wr_11 = 0;
	}
	if(isDefined(wr_13) == false){
		wr_13 = 0;
	}

	var wr_14 = wr_11*wr_13;
	$("#wr_14").val(wr_14.toFixed(2));
}

// 저장
function fnSaveData(){
	$("#sales_frm").ajaxSubmit({
		url:"ajax.sales_update.php",
		dataType:"json",
		success:function(data){
			if(isDefined(data.message)){
				alert(data.message);
			}
			if(data.ret_code == true){
				document.location.reload();
			}else{
				fnResetData("<?=$wr_id?>");
			}
		}
	});
}
</script>