<?
include_once('./_common.php');

# 매출 정보 불러오기
$sql = "SELECT * FROM g5_sales0_list WHERE seq='".$seq."'";
$sales0 = sql_fetch($sql);
$row = $sales0;
$wr_table = "g5_sales0_list";
$wr_bigo = $row['wr_etc'];

# 발주 정보 불러오기
$sql = "SELECT * FROM g5_sales1_list WHERE wr_order_num = '".$row['wr_order_num']."'";
$sales1 = sql_fetch($sql);
if($sales1['seq']){
	$row = $sales1;
	$wr_table = "g5_sales1_list";
	$wr_bigo = $row['wr_order_etc'];
}

# 입고 정보 불러오기
$sql = "SELECT * FROM g5_sales2_list WHERE wr_order_num = '".$row['wr_order_num']."'";
$sales2 = sql_fetch($sql);
if($sales2['seq']){
	$row = $sales2;
	$wr_table = "g5_sales2_list";
	$wr_bigo = $row['wr_warehouse_etc'];
}

# 출고 정보 불러오기
$sql = "SELECT * FROM g5_sales3_list WHERE wr_order_num = '".$row['wr_order_num']."'";
$sales3 = sql_fetch($sql);
if($sales3['seq']){
	$row = $sales3;
	$wr_table = "g5_sales3_list";
	$wr_bigo = $row['wr_release_etc'];
}

# 상품정보 불러오기
$sql = "SELECT * FROM g5_write_product WHERE wr_id='".$row['wr_product_id']."'";
$item = sql_fetch($sql);

# 도메인 불러오기
$sql = "SELECT * FROM g5_code_list WHERE code_type='4' AND code_use='Y' AND del_yn='N';";
$domainRs = sql_query($sql);

# 국가 정보 불러오기
$sql = "SELECT * FROM g5_country_code";
$countryRs = sql_query($sql);

# 배송업체 목록 불러오기
$delivery_sql = "SELECT * FROM g5_delivery_company WHERE wr_use = 1";
$delivery_rst = sql_query($delivery_sql);

while($delivery_com=sql_fetch_array($delivery_rst)) {
    $delivery[$delivery_com['wr_code']] = $delivery_com['wr_name'];
}

$country_dcode = sql_fetch(" SELECT wr_code as code FROM g5_country WHERE code_2 = '{$row['wr_deli_country']}' "); 
$country = $country_dcode['code']; 

$sql = "SELECT {$country} as price, cust_code, weight_code FROM g5_shipping_price 
        WHERE weight_code >= {$row['wr_weight2']} AND {$country} != 0  GROUP BY cust_code ORDER BY price ASC";
$rst = sql_query($sql);

$wr_delivery_fee = (float)$row['wr_delivery_fee'];
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
	<form name="order_frm" id="order_frm" method="POST">
		<input type="hidden" name="sales0_id" value="<?=$sales0['seq']?>"/>
		<input type="hidden" name="sales1_id" value="<?=$sales1['seq']?>"/>
		<input type="hidden" name="sales2_id" value="<?=$sales2['seq']?>"/>
		<input type="hidden" name="sales3_id" value="<?=$sales3['seq']?>"/>
		<input type="hidden" name="wr_table" value="<?=$wr_table?>"/>
		<div class="tbl_frm01 tbl_wrap" style="margin-top:20px;">
			<table>
				<tr>
					<th>도메인명</th>
					<td>
						<input type="text" name="wr_domain" value="<?=$row['wr_domain']?>" class="readonly" readonly/>
					</td>
					<th>매출일자</th>
					<td>
						<input type="text" name="wr_date" value="<?=$row['wr_date']?>" class="readonly" readonly/>
					</td>
					<th>주문번호</th>
					<td>
						<input type="text" name="wr_order_num" value="<?=$row['wr_order_num']?>" class="readonly" readonly/>
					</td>
					<th>Buyer ID</th>
					<td>
						<input type="text" name="wr_mb_id" value="<?=$row['wr_mb_id']?>"/>
					</td>
					<th>Buyer 명</th>
					<td>
						<input type="text" name="wr_mb_name" value="<?=$row['wr_mb_name']?>"/>
					</td>
				</tr>
				<tr>
					<th>우편번호</th>
					<td>
						<input type="text" name="wr_deli_zip" value="<?=$row['wr_deli_zip']?>"/>
					</td>
					<th>주소</th>
					<td colspan="7">
						<input type="text" name="wr_deli_addr1" value="<?=$row['wr_deli_addr1']?>" style="width:48%;"/>
						<input type="text" name="wr_deli_addr2" value="<?=$row['wr_deli_addr2']?>" style="width:48%;"/>
					</td>
				</tr>
				<tr>
					<th>도시명</th>
					<td>
						<input type="text" name="wr_deli_city"  value="<?=$row['wr_deli_city']?>"/>
					</td>
					<th>주명</th>
					<td>
						<input type="text" name="wr_deli_ju" value="<?=$row['wr_deli_ju']?>"/>
					</td>
					<th>나라명</th>
					<td colspan="3">
						<select name="wr_deli_country" id="wr_deli_country" style="width:48%;" onchange="fnGetCountry();">
							<option value="">국가코드</option>
							<?while($countryRow = sql_fetch_array($countryRs)){?>
								<option value="<?=$countryRow['code_2']?>" <?=get_selected($countryRow['code_2'],$row['wr_deli_country'])?> data="<?=$countryRow['code_kr']?>"><?=$countryRow['code_kr']?>(<?=$countryRow['code_2']?>)</option>
							<?}?>
						</select>
						<input type="text" id="wr_deli_country_nm" value="<?=get_country_code($row['wr_deli_country'])?>" style="width:48%;" class="readonly" readonly/>
					</td>
					<th>전화번호</th>
					<td>
						<input type="text" name="wr_deli_tel" value="<?=$row['wr_deli_tel']?>"/>
					</td>
				</tr>
				<tr>
					<th>제품코드</th>
					<td colspan="9">
						<input type="text" name="wr_code" value="<?=$item['wr_1']?>" style="width:20%;" class="readonly" readonly/>
						<input type="text" value="<?=$item['wr_2']?>" style="width:20%;" class="readonly" readonly/>
						<input type="text" value="<?=$item['wr_subject']?>" style="width:58%;" class="readonly" readonly/>
					</td>
				</tr>
				<tr>
					<th>수량</th>
					<td>
						<input type="text" name="wr_ea" value="<?=$row['wr_ea']?>" class="readonly" readonly/>
					</td>
					<th>박스수</th>
					<td>
						<input type="text" name="wr_box" value="<?=$row['wr_box']?>"/>
					</td>
					<th>단가</th>
					<td>
						<input type="text" name="wr_danga" id="wr_danga" value="<?=$row['wr_danga']?>"/>
					</td>
					<th>신고가격</th>
					<td>
						<input type="text" name="wr_singo" id="wr_singo" value="<?=$row['wr_singo']?>"/>
					</td>
					<th>통화</th>
					<td>
						<input type="text" name="wr_currency" id="wr_currency" value="<?=$row['wr_currency']?>"/>
					</td>
				</tr>
				<tr>
					<th>개당무게</th>
					<td>
						<input type="text" name="wr_weight1" id="wr_weight1" value="<?=$row['wr_weight1']?>"/>
					</td>
					<th>총무게</th>
					<td>
						<input type="text" name="wr_weight2" id="wr_weight2" value="<?=$row['wr_weight2']?>"/>
					</td>
					<th>무게단위</th>
					<td>
						<input type="text" name="wr_weight_dan" value="<?=$row['wr_weight_dan']?>"/>
					</td>
					<th>HS 코드</th>
					<td>
						<input type="text" name="wr_hscode" value="<?=$row['wr_hscode']?>"/>
					</td>
					<th>제조국가</th>
					<td>
						<input type="text" name="wr_make_country" value="<?=$row['wr_make_country']?>"/>
					</td>
				</tr>
				<tr>
					<th>배송사</th>
					<td colspan="3">
						<select name="wr_delivery" id="wr_delivery" style="width:48%;" onchange="fnGetDelivery();">
							<option value="">배송업체</option>
							<?
							for ($i=0; $delivery2=sql_fetch_array($rst); $i++){
								$selected = "";
								if($delivery2['cust_code'] == $row['wr_delivery']){
									$selected == "SELECTED";
									if(!$wr_delivery_fee){
										$wr_delivery_fee = $delivery2['price'];
									}
								}else{
									if($i == 0){
										$selected == "SELECTED";
										if(!$wr_delivery_fee){
											$wr_delivery_fee = $delivery2['price'];
										}
									}
								}
							?>
								<option value="<?=$delivery2['cust_code']?>" data="<?=$delivery2['price']?>" <?=$selected?>><?=$delivery[$delivery2['cust_code']]?></option>
							<?
							}
							?>
						</select>
						<input type="text" id="wr_delivery_nm" value="" style="width:48%;" class="readonly" readonly/>
					</td>
					<th>배송요금</th>
					<td>
						<input type="text" name="wr_delivery_fee" value="<?=$row['wr_delivery_fee']?>"/>
					</td>
					<th>추가배송비</th>
					<td>
						<input type="text" name="wr_delivery_fee2" value="<?=(float)$row['wr_delivery_fee2']?>"/>
					</td>
					<th>추가금</th>
					<td>
						<input type="text" name="wr_add_price" value="<?=(float)$row['wr_add_price']?>"/>
					</td>
				</tr>
				<tr>
					<th>Service Type</th>
					<td>
						<select name="wr_servicetype">
							<option value="">Service Type</option>
							<?foreach($service_arr as $key => $val){?>
								<option value="<?=$key?>" <?=get_selected($key,$row['wr_servicetype'])?>><?=$val?>( <?=$key?> )</option>
							<?}?>
						</select>
					</td>
					<th>packaging</th>
					<td>
						<input type="text" name="wr_packaging" value="<?=$row['wrpackaging']?>"/>
					</td>
					<th>수출국가코드</th>
					<td>
						<input type="text" name="wr_country_code" value="<?=$row['wr_country_code']?>"/>
					</td>
					<th>수출신고품명</th>
					<td>
						<input type="text" name="wr_name2" value="<?=$row['wr_name2']?>"/>
					</td>
					<th>주문번호</th>
					<td>
						<input type="text" name="wr_order_num2" value="<?=$row['wr_order_num2']?>" class="readonly" readonly/>
					</td>
				</tr>
				<tr>
					<th>발주일자</th>
					<td>
						<input type="text" name="wr_date2" value="<?=$row['wr_date2']?>"/>
					</td>
					<th>입고일자</th>
					<td>
						<input type="text" name="wr_date3" value="<?=$row['wr_date3']?>" class="readonly" readonly/>
					</td>
					<th>출고일자</th>
					<td>
						<input type="text" name="wr_date4" value="<?=$row['wr_date4']?>" class="readonly" readonly/>
					</td>
					<td colspan="4" style="text-align:left;">
						<ul>
							<li>
								<input type="checkbox" id="sales1" <?=($sales1['seq'])?"CHECKED":""?> class="readonly" disabled/>발주완료
							</li>
							<li>
								<input type="checkbox" id="sales2" <?=($sales2['seq'])?"CHECKED":""?> class="readonly" disabled/>입고완료
							</li>
							<li>
								<input type="checkbox" id="wr_direct_use" <?=($row['wr_direct_use'] == "1")?"CHECKED":""?> class="readonly" disabled/>다이렉트입고
							</li>
							<li>
								<input type="checkbox" id="wr_release_use" <?=($row['wr_release_use'] == "1")?"CHECKED":""?> class="readonly" disabled/>출고완료
							</li>
						</ul>
					</td>
				</tr>
				<tr>
					<th>발주처</th>
					<td colspan="3">
						<select name="wr_orderer" id="wr_orderer" style="width:48%;" onchange="fnOrderer();">
							<option value="">발주처</option>
							<?php 
							$arr = get_code_list('5');
							foreach($arr as $k=>$v){
							?>
								
								<option value='<?=$v['idx']?>' <?=get_selected($v['idx'],$row['wr_orderer'])?> data="<?=$v['code_name']?>"><?=$v['code_name']?>( <?=$v['code_value']?> )</option>
							<?}?>
						</select>
						<input type="text" id="wr_orderer_nm" value="" style="width:48%;" class="readonly" readonly/>
					</td>
					<th>발주수량</th>
					<td>
						<input type="text" name="wr_order_ea" value="<?=$row['wr_order_ea']?>"/>
					</td>
					<th>발주단가</th>
					<td>
						<input type="text" name="wr_order_price" value="<?=$row['wr_order_price']?>"/>
					</td>
					<th>발주금액</th>
					<td>
						<input type="text" name="wr_order_total" value="<?=$row['wr_order_total']?>"/>
					</td>
				</tr>
				<tr>
					<th>트래킹NO</th>
					<td>
						<input type="text" name="wr_order_traking" value="<?=$row['wr_order_traking']?>"/>
					</td>
					<th>수출트래킹NO</th>
					<td>
						<input type="text" name="wr_release_traking" value="<?=$row['wr_release_traking']?>"/>
					</td>
					<th>상품대표코드</th>
					<td>
						<input type="text" name="wr_code" value="<?=$row['wr_code']?>" class="readonly" readonly/>
					</td>
					<th>E-mail</th>
					<td colspan="3">
						<input type="text" name="wr_email" value="<?=$row['wr_email']?>"/>
					</td>
				</tr>
				<tr>
					<th>출고창고</th>
					<td>
						<input type="text" name="wr_warehouse" value="<?=$row['wr_warehouse']?>" class="readonly" readonly/>
					</td>
					<th>비고</th>
					<td colspan="7">
						<input type="text" name="wr_bigo" value="<?=$wr_bigo?>"/>
					</td>
				</tr>
			</table>
			<div class="win_btn btn_confirm" style="margin-top:20px;text-align:center">
				<button type="button" title="저장" class="btn_b01" onclick="fnSaveData();" style="width:100px;margin-top:15px">저장</button>
				<button type="button" title="저장" class="btn_b02" onclick="fnResetData();" style="width:100px;margin-top:15px">초기화</button>
				<button type="button" class="modal_cls" title="닫기" onclick="close_modal();"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
			</div>
			
		</div>
	</form>
</fieldset>
<script type="text/javascript">
$(document).ready(function(){
	// 페이지 로드시 호출
	init();
});

// 페이지 로드시 호출
function init(){
	fnGetCountry(); //나라명
	fnGetDelivery(); // 배송업체
	fnOrderer(); // 발주처
}

// 국가 코드 
function fnGetCountry(){
	var code_kr = $("#wr_deli_country option:selected").attr("data");
	$("#wr_deli_country_nm").val(code_kr);
}

// 배송업체 선택
function fnGetDelivery(){
	var delivery_nm = $("#wr_delivery option:selected").text();
	var fee = $("#wr_delivery option:selected").attr("data");
	var wr_delivery = $("#wr_delivery").val();
	if(!isDefined(wr_delivery)){
		$("#wr_delivery_nm").val("");
		fee = 0;
	}else{
		$("#wr_delivery_nm").val(delivery_nm);
	}
	$("input[name='wr_delivery_fee']").val(fee);
}

// 발주처 선택
function fnOrderer(){
	var wr_orderer_nm = $("#wr_orderer option:selected").attr("data");
	$("#wr_orderer_nm").val(wr_orderer_nm);
}

// 수정내용 저장
function fnSaveData(){
	if(!confirm("저장한 내용은 복원이 불가능합니다.\n수정을 하기셌습니까?")){
		return false;
	}
}

// 초기화면으로 돌아가기
function fnResetData(){
	var seq = $("input[name='seq']").val();
	fnModalReset(seq);
}

// 데이터 저장
function fnSaveData(){
	$("#order_frm").ajaxSubmit({
		url:"../sales/ajax.order_proc.php",
		dataType:"json",
		success:function(data){
			
		}
	})
}
</script>