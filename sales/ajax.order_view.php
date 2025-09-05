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
$countryData = get_country();

# 배송업체 목록 불러오기
$delivery_sql = "SELECT * FROM g5_delivery_company WHERE wr_use = 1";
$delivery_rst = sql_query($delivery_sql);

while($delivery_com=sql_fetch_array($delivery_rst)) {
    $delivery[$delivery_com['wr_code']] = $delivery_com['wr_name'];
}

$country_dcode = sql_fetch(" SELECT wr_code as code FROM g5_country WHERE code_2 = '{$row['wr_deli_country']}' "); 
$country = $country_dcode['code']; 

$sql = "SELECT {$country} as price, cust_code, weight_code,ifnull(wr_percent, 0) as code_percent FROM g5_shipping_price A
		LEFT OUTER JOIN g5_delivery_company B ON B.wr_code=A.cust_code
        WHERE weight_code >= {$row['wr_weight2']} AND {$country} != 0  GROUP BY cust_code ORDER BY price ASC";
$rst = sql_query($sql);

$wr_delivery_fee = (float)$row['wr_delivery_fee'];

# 발주처 정보
$sql = "SELECT * FROM g5_code_list WHERE code_type='5' AND del_yn = 'N' AND code_use = 'Y' ORDER BY code_value ASC";
$ordererRs = sql_query($sql);

# 관리자 목록
if(!$row['wr_exchange_rate']){
	$exchangeData = fnGetExcharge($row['wr_currency'],$row['wr_date']);
	if(!$exchangeData['rate']){
		$exchangeData = fnGetExcharge($row['wr_currency']);
	}
	$row['wr_exchange_rate'] = str_replace(",","",$exchangeData['rate']);
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
	<h3><?=$row['wr_order_num']?></h3>
	<form name="order_frm" id="order_frm" method="POST">
		<input type="hidden" name="sales0_id" value="<?=$sales0['seq']?>"/>
		<input type="hidden" name="sales1_id" value="<?=$sales1['seq']?>"/>
		<input type="hidden" name="sales2_id" value="<?=$sales2['seq']?>"/>
		<input type="hidden" name="sales3_id" value="<?=$sales3['seq']?>"/>
		<input type="hidden" name="wr_table" value="<?=$wr_table?>"/>
		<input type="hidden" name="wr_direct_use" value="<?=$row['wr_direct_use']?>"/>
		<input type="hidden" name="wr_product_id" value="<?=$row['wr_product_id']?>"/>
		<div class="tbl_frm01 tbl_wrap" style="margin-top:40px;">
			<table id="tbl_frm01">
				<?if($sales0['seq']){?>
				<!-- 매출정보 -->
					<tr>
						<th class="title" colspan="8">매출정보</th>
					</tr>
					<tr>
						<th>도메인</th>
						<td>
							<input type="text" name="wr_domain" value="<?=$row['wr_domain']?>" class="readonly" readonly/>
						</td>
						<th>매출일자</th>
						<td>
							<input type="text" name="wr_date" value="<?=$row['wr_date']?>" class="readonly" readonly/>
						</td>
						<th>원 주문번호</th>
						<td>
							<input type="text" name="wr_ori_order_num" value="<?=$row['wr_ori_order_num']?>" class="readonly" readonly/>
						</td>
						<th>주문번호</th>
						<td>
							<input type="text" name="wr_order_num" value="<?=$row['wr_order_num']?>" class="readonly" readonly/>
						</td>
					</tr>
					<tr>
						<th>주문자 ID</th>
						<td>
							<input type="text" name="wr_mb_id" value="<?=$row['wr_mb_id']?>"/>
						</td>
						<th>주문자명</th>
						<td>
							<input type="text" name="wr_mb_name" value="<?=$row['wr_mb_name']?>"/>
						</td>
						<th>전화번호</th>
						<td>
							<input type="text" name="wr_tel" value="<?=$row['wr_tel']?>"/>
						</td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<th>주문 국가명</th>
						<td>
							<select name="wr_country" id="wr_country" >
								<option value="">국가코드</option>
								<?foreach($countryData as $key => $val){?>
									<option value="<?=$val['code_2']?>" <?=get_selected($val['code_2'],$row['wr_country'])?> data="<?=$val['code_kr']?>"><?=$val['code_kr']?>(<?=$val['code_2']?>)</option>
								<?}?>
							</select>
						</td>
						<th>주문 주명</th>
						<td>
							<input type="text" name="wr_ju" value="<?=$row['wr_ju']?>"/>
						</td>
						<th> 주문 도시명</th>
						<td>
							<input type="text" name="wr_city" value="<?=$row['wr_city']?>"/>
						</td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<th>주문자 우편번호</th>
						<td>
							<input type="text" name="wr_zip" value="<?=$row['wr_zip']?>"/>
						</td>
						<th>주문자 주소</th>
						<td colspan="5">
							<input type="text" name="wr_addr1" value="<?=$row['wr_addr1']?>" style="width:48%;"/>
							<input type="text" name="wr_addr2" value="<?=$row['wr_addr2']?>" style="width:48%;"/>
						</td>
					</tr>
					<tr>
						<th>수량</th>
						<td>
							<input type="text" name="wr_ea" value="<?=$row['wr_ea']?>" class="readonly" readonly/>
						</td>
						<th>단가(<?=$row['wr_currency']?>)</th>
						<td>
							<input type="text" name="wr_danga" value="<?=$row['wr_danga']?>"/>
						</td>
						<th>신고가격(<?=$row['wr_currency']?>)</th>
						<td>
							<input type="text" name="wr_singo" value="<?=$row['wr_singo']?>"/>
						</td>
						<th>통화</th>
						<td>
							<input type="text" name="wr_currency" value="<?=$row['wr_currency']?>" class="readonly" readonly/>
						</td>
					</tr>
					<tr>
						<th>환율</th>
						<td>
							<input type="text" name="wr_exchange_rate" id="wr_exchange_rate" value="<?=$row['wr_exchange_rate']?>" class="readonly" readonly/>
						</td>
						<th>단가(KRW)</th>
						<td>
							<input type="text" id="wr_exchange_danga" value="<?=floor($row['wr_danga']*$row['wr_exchange_rate'])?>" class="readonly" readonly/>
						</td>
						<th>신고가격(KRW)</th>
						<td>
							<input type="text" id="wr_exchange_singo" value="<?=floor($row['wr_singo']*$row['wr_exchange_rate'])?>" class="readonly" readonly/>
						</td>
					</tr>
					<tr>
						<th>매출비고</th>
						<td colspan="7">
							<input type="text" name="wr_etc" value="<?=$row['wr_etc']?>"/>
						</td>
					</tr>
				<?}?>
				<!--// 매출정보 -->

				<!-- 제품정보 -->
				<tr>
					<th class="title" colspan="8">제품정보</th>
				</tr>
				<tr>
					<th>제품코드</th>
					<td>
						<input type="text" name="wr_code" value="<?=$row['wr_code']?>" class="readonly" readonly/>
					</td>
					<th>제품약칭</th>
					<td>
						<input type="text" name="wr_2" value="<?=$item['wr_2']?>" class="readonly" readonly/>
					</td>
					<th>제품명</th>
					<td colspan="3">
						<input type="text" name="wr_subject" value="<?=$item['wr_subject']?>" class="readonly" readonly/>
					</td>
				</tr>
				<tr>
					<th>브랜드</th>
					<td>
						<input type="text" name="wr_23" value="<?=get_code_name('1',$item['wr_23'])?>" class="readonly" readonly/>
					</td>
					<th>수출국가코드</th>
					<td>
						<input type="text" name="wr_country_code" value="<?=$row['wr_country_code']?>" class="readonly" readonly/>
					</td>
					<th>수출신고품명</th>
					<td>
						<input type="text" name="wr_name2" value="<?=$row['wr_name2']?>" class="readonly" readonly/>
					</td>
          <th>제품 발주단가</th>
          <td>
            <input type="text" name="wr_name2" value="<?=$item['wr_22']?>" class="readonly" readonly/>
          </td>
        </tr>
				<tr>
					<th>개당무게</th>
					<td>
						<input type="text" name="wr_weight1" value="<?=$row['wr_weight1']?>" class="readonly" readonly/>
					</td>
					<th>무게단위</th>
					<td>
						<input type="text" name="wr_weight_dan" value="<?=$row['wr_weight_dan']?>" class="readonly" readonly/>
					</td>
					<th>HS 코드</th>
					<td>
						<input type="text" name="wr_hscode" value="<?=$row['wr_hscode']?>" class="readonly" readonly/>
					</td>
					<th>제조국가</th>
					<td>
						<input type="text" name="wr_make_country" value="<?=$row['wr_make_country']?>" class="readonly" readonly/>
					</td>
				</tr>
				<!--// 제품정보-->

				<!-- 발주정보 -->
				<?if($sales1['seq'] && $sales2['wr_direct_use'] != "1"){
					# 해당 데이터가 다음 단계로 넘어갔을 경우 발주 비고를 제외하고는 수정을 못하게 처리
					if($sales1['wr_chk'] == "1"){
						$readonly = "readonly";
					}
				?>
					<tr>
						<th class="title" colspan="8">발주정보</th>
					</tr>
					<tr>
						<th>발주처</th>
						<td>
							<select name="wr_orderer">
								<option value="">발주처</option>
								<?while($ordererRow = sql_fetch_array($ordererRs)){?>
									<option value="<?=$ordererRow['idx']?>" <?=get_selected($ordererRow['idx'],$row['wr_orderer'])?>><?=$ordererRow['code_name']?></option>
								<?}?>
							</select>
						</td>
						<th>발주일</th>
						<td>
							<input type="text" name="wr_date2" value="<?=$row['wr_date2']?>" class="readonly" readonly/>
						</td>
						<th>발주 주문번호</th>
						<td>
							<input type="text" name="wr_order_num2" value="<?=$row['wr_order_num2']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
						<th>발주 트래킹번호</th>
						<td>
							<input type="text" name="wr_order_traking" value="<?=$row['wr_order_traking']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
					</tr>
					<tr>
						<th>발주 수량</th>
						<td>
							<input type="text" name="wr_order_ea" id="wr_order_ea" class="orderer_calc <?=$readonly?>" value="<?=$row['wr_order_ea']?>" <?=$readonly?>/>
						</td>
						<th>발주단가</th>
						<td>
							<input type="text" name="wr_order_price" id="wr_order_price" class="orderer_calc <?=$readonly?>" value="<?=$row['wr_order_price']?>" <?=$readonly?>/>
						</td>
						<th>발주 배송비</th>
						<td>
							<input type="text" name="wr_order_fee" id="wr_order_fee" class="orderer_calc <?=$readonly?>" value="<?=$row['wr_order_fee']?>" <?=$readonly?>/>
						</td>
						<th>발주금액</th>
						<td>
							<input type="text" name="wr_order_total" id="wr_order_total" class="orderer_calc <?=$readonly?>" value="<?=$row['wr_order_total']?>" <?=$readonly?>/>
						</td>
					</tr>
					<tr>
						<th>발주비고</th>
						<td colspan="7">
							<input type="text" name="wr_order_etc" value="<?=$row['wr_order_etc']?>"/>
						</td>
					</tr>
				<?}?>
				<!--// 발주정보-->

				<!-- 입고정보 -->
				<?if($sales2['seq'] && $sales2['wr_direct_use'] != "1"){?>
					<tr>
						<th class="title" colspan="8">입고정보</th>
					</tr>
					<tr>
						<th>입고일</th>
						<td>
							<input type="text" name="wr_date3" value="<?=$row['wr_date3']?>" class="readonly" readonly/>
						</td>
						<th>결제구분</th>
						<td>
							<select name="wr_pay_type" id="wr_pay_type">
								<option value="">결제구분</option>
								<option value="1" <?=get_selected("1",$row['wr_pay_type'])?>>카드결제</option>
								<option value="2" <?=get_selected("2",$row['wr_pay_type'])?>>직발주</option>
							</select>
						</td>
						<th>지급금</th>
						<td>
							<input type="text" name="wr_warehouse_price" id="wr_warehouse_price" value="<?=(float)$row['wr_warehouse_price']?>"/>
						</td>
						<th>미지급금</th>
						<td>
							<input type="text" name="wr_misu" id="wr_misu" value="<?=(float)$row['wr_misu']?>" class="readonly" readonly/>
						</td>
					</tr>
					<tr>
						<th>입고비고</th>
						<td colspan="7">
							<input type="text" name="wr_warehouse_etc" value="<?=$row['wr_warehouse_etc']?>"/>
						</td>
					</tr>
				<?}?>
				<!--// 입고정보-->

				<!-- 출고정보 및 배송정보-->
					<?if($sales3){
						$readonly = "";
						if($sales3['wr_release_use'] == "1"){
							$readonly = "readonly";
							$disabled = "DISABLED";
						}
					?>
					<tr>
						<th class="title" colspan="8">출고 및 배송 정보</th>
					</tr>
					<tr>
						<th>트래킹NO</th>
						<td>
							<input type="text" name="wr_release_traking" value="<?=$row['wr_release_traking']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
						<th>출고일</th>
						<td>
							<input type="text" name="wr_date4" value="<?=$row['wr_date4']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
						<th>출고유무</th>
						<td>
							<?if($row['wr_release_use'] == "1"){?>
								출고완료
							<?}else{?>
								출고대기
							<?}?>
						</td>
						<th>출고 처리자</th>
						<td>
							<?=get_member($row['wr_release_mbid'], 'mb_name, mb_id')['mb_name']?>(<?=get_member($row['wr_release_mbid'], 'mb_name, mb_id')['mb_id']?>)
						</td>
					</tr>
					<tr>
						<th>배송 국가</th>
						<td>
							<select name="wr_deli_country" id="wr_deli_country" <?=$disabled?>>
								<option value="">배송 국가</option>
								<?foreach($countryData as $key => $val){?>
									<option value="<?=$val['code_2']?>" <?=get_selected($val['code_2'],$row['wr_deli_country'])?> data="<?=$val['code_kr']?>"><?=$val['code_kr']?>(<?=$val['code_2']?>)</option>
								<?}?>
							</select>
						</td>
						<th>배송 주</th>
						<td>
							<input type="text" name="wr_deli_ju" value="<?=$row['wr_deli_ju']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
						<th>배송 도시</th>
						<td>
							<input type="text" name="wr_deli_city" value="<?=$row['wr_deli_city']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
						<th>추가금</th>
						<td>
							<input type="text" name="wr_add_price" value="<?=(float)$row['wr_add_price']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
					</tr>
					<tr>
						<th>수령자</th>
						<td>
							<input type="text" name="wr_deli_nm" value="<?=$row['wr_deli_nm']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
						<th>수령자 연락처</th>
						<td>
							<input type="text" name="wr_deli_tel" value="<?=$row['wr_deli_tel']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
						<td colspan="4"></td>
					</tr>
					<tr>
						<th>배송지 우편번호</th>
						<td>
							<input type="text" name="wr_deli_zip" value="<?=$row['wr_deli_zip']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
						<th>배송지 주소</th>
						<td colspan="5">
							<input type="text" name="wr_deli_addr1" value="<?=$row['wr_deli_addr1']?>" style="width:48%;" class="<?=$readonly?>" <?=$readonly?>/>
							<input type="text" name="wr_deli_addr2" value="<?=$row['wr_deli_addr2']?>" style="width:48%;" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
					</tr>
					<tr>
						<th>Service Type</th>
						<td>
							<select name="wr_servicetype" <?=$disabled?>>
								<option value="">Service Type</option>
								<?foreach($service_arr as $key => $val){?>
									<option value="<?=$key?>" <?=get_selected($key,$row['wr_servicetype'])?>><?=$val?>( <?=$key?> )</option>
								<?}?>
							</select>
						</td>
						<th>Packaging</th>
						<td>
							<input type="text" name="wr_packaging" value="<?=$row['wr_packaging']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
						<th>박스수</th>
						<td>
							<input type="text" name="wr_box" value="<?=$row['wr_box']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
						<th>총 무게</th>
						<td>
							<input type="text" name="wr_weight2" id="wr_weight2" value="<?=$row['wr_weight2']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
					</tr>
					<tr>
						<th>배송사</th>
						<td>
							<select name="wr_delivery" id="wr_delivery" style="width:48%;" onchange="fnGetDelivery();" <?=$disabled?>>
								<option value="">배송업체</option>
								<?
								for ($i=0; $delivery2=sql_fetch_array($rst); $i++){
									$selected = "";
									if($delivery2['cust_code'] == $row['wr_delivery']){
										$selected == "SELECTED";
										if(!$wr_delivery_fee){
											$wr_delivery_fee = $delivery2['price'];
										}
										$wr_delivery_oil_percent = $delivery2['code_percent'];
									}
								?>
									<option value="<?=$delivery2['cust_code']?>" data="<?=$delivery2['price']?>" oil_percent="<?=$delivery2['code_percent']?>" delivery_currency="<?=($delivery2['cust_code'] == "1021")?"JPY":"KRW"?>" <?=get_selected($delivery2['cust_code'],$row['wr_delivery'])?>><?=$delivery[$delivery2['cust_code']]?></option>
								<?
								}
								?>
							</select>
						</td>
						<th>배송요금</th>
						<td>
							<input type="text" name="wr_delivery_fee" id="wr_delivery_fee" value="<?=$row['wr_delivery_fee']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
						<th>추가배송비</th>
						<td>
							<input type="text" name="wr_delivery_fee2" id="wr_delivery_fee2" value="<?=$row['wr_delivery_fee2']?>" class="<?=$readonly?>" <?=$readonly?>/>
						</td>
						<th>배송 유류할증료</th>
						<td>
							<input type="hidden" id="oil_percent" value="<?=$wr_delivery_oil_percent?>"/>
							<input type="text" name="wr_delivery_oil" id="wr_delivery_oil" value="<?=$row['wr_delivery_oil']?>" class="readonly" readonly/>
						</td>
					</tr>
					
					<tr>
						<th>배송 총금액</th>
						<td>
							<input type="text" id="wr_delivery_total" value="<?=((float)$row['wr_delivery_fee'] + (float)$row['wr_delivery_fee2'])?>" class="readonly" readonly/>
						</td>
						<th>배송비 통화</th>
						<td>
							<input type="text" id="delivery_currency" value="<?=($row['wr_delivery'] == "1021")?"JPY":"KRW"?>" class="readonly" readonly/>
						</td>
						<th>출고비고</th>
						<td colspan="5">
							<input type="text" name="wr_release_etc" value="<?=$row['wr_release_etc']?>"/>
						</td>
					</tr>
				<?}?>
				<!--// 출고정보-->
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
var re_warehouse_price = <?=(float)$row['wr_warehouse_price']?>;
var fLoadChk = false;
$(document).ready(function(){
	// 페이지 로드시 호출
	init();

	// 발주 금애계산
	$(".orderer_calc").bind("keyup",function(){
		fnOrdererCalc();
	});

	// 입고 결제 구분 변경시 금액 계산
	$("#wr_pay_type").bind("change",function(){
		fnWarehouseCalc();
	});

	// 직불이면 지급액 수정시 미지급금 자동 계산
	$("#wr_warehouse_price").bind("keyup",function(){
		var price = $(this).val();
		$(this).val(get_number(price));
		fnWarehouseCalc();
	});

	// 배송 국가 변경시 배송사에 연계된 금액 불러오기
	$("#wr_deli_country").bind("change",function(){
		fnGetDeliveryList();
	});

	// 배송비, 추가배송비 수정시 배송 총금액 자동 계산
	$("#wr_delivery_fee, #wr_delivery_fee2").bind("keyup",function(){
		var price = $(this).val();
		$(this).val(get_number(price));
		fnCalcDeliveryOil();
	});
});

// 페이지 로드시 호출
function init(){
	fnWarehouseCalc(); // 입고정보 카드
	fnOrderer(); // 발주처
}

// 배송 국가 변경시 배송비 금애계산
function fnGetDeliveryList(){
	var wr_deli_country = $("#wr_deli_country").val();
	var wr_delivery = $("#wr_delivery").val();
	var params = "mode=order_view&seq="+$("input[name='sales3_id']").val()+"&wr_country="+wr_deli_country+"&max_weight="+$("#wr_weight2").val()+"&wr_delivery="+wr_delivery;
	$.post("../sales/ajax.sales3_update.php",params,function(data){
		$("#wr_delivery").html(data);
	});
}

// 배송업체 선택tl 기본 배송요금,유류할증율,배송비 통화 필드에 값넣기
function fnGetDelivery(){
	var delivery_nm = $("#wr_delivery option:selected").text();
	var fee = $("#wr_delivery option:selected").attr("data");
	var oil_percent = $("#wr_delivery option:selected").attr("oil_percent");
	var delivery_currency = $("#wr_delivery option:selected").attr("delivery_currency");
	var wr_delivery = $("#wr_delivery").val();
	if(!isDefined(wr_delivery)){
		fee = 0;
	}
	$("input[name='wr_delivery_fee']").val(fee);
	$("#oil_percent").val(oil_percent);
	$("#delivery_currency").val(delivery_currency);
	fnCalcDeliveryOil();
}

// 유류할증료 계산
function fnCalcDeliveryOil(){
	var wr_delivery_fee = $("#wr_delivery_fee").val();
	var wr_delivery_oil_percent = $("#oil_percent").val();
	var wr_delivery_oil = 0;
	if(!isDefined(wr_delivery_fee)){
		wr_delivery_fee = 0;
	}
	if(!isDefined(wr_delivery_oil_percent)){
		wr_delivery_oil_percent = 0;
	}
	wr_delivery_oil = parseFloat(wr_delivery_fee) * parseFloat(wr_delivery_oil_percent);
	$("#wr_delivery_oil").val(wr_delivery_oil.toFixed(0));
	fnDeliveryCalc();
}

// 배송비 자동 계산
function fnDeliveryCalc(){
	var wr_delivery_fee = $("#wr_delivery_fee").val();
	var wr_delivery_fee2 = $("#wr_delivery_fee2").val();
	var wr_delivery_oil = $("#wr_delivery_oil").val();
	var wr_delivery_total = 0;
	if(!isDefined(wr_delivery_fee)){
		wr_delivery_fee = 0;
	}
	if(!isDefined(wr_delivery_fee2)){
		wr_delivery_fee2 = 0;
	}
	if(!isDefined(wr_delivery_oil)){
		wr_delivery_oil = 0;
	}
	wr_delivery_total = parseFloat(wr_delivery_fee) + parseFloat(wr_delivery_fee2) + parseFloat(wr_delivery_oil);
	$("#wr_delivery_total").val(wr_delivery_total);
}

// 발주 금액 또는 수량 변경시 계산
function fnOrdererCalc(){
	var wr_order_ea = parseInt($("#wr_order_ea").val());
	var wr_order_price = parseInt($("#wr_order_price").val());
	var wr_order_fee = parseInt($("#wr_order_fee").val());
	
	var wr_order_total = (wr_order_ea * wr_order_price) + wr_order_fee;
	$("#wr_order_total").val(wr_order_total);

}

// 발주처 선택
function fnOrderer(){
	var wr_orderer_nm = $("#wr_orderer option:selected").attr("data");
	$("#wr_orderer_nm").val(wr_orderer_nm);
}

// 입고 정보 금액 계산
function fnWarehouseCalc(){
	var wr_pay_type = $("#wr_pay_type").val();
	var wr_order_total = $("#wr_order_total").val();
	var wr_warehouse_price = parseFloat($("#wr_warehouse_price").val());
	var wr_misu = parseFloat($("#wr_misu").val());
	if(!isDefined(wr_warehouse_price)){
		wr_warehouse_price = 0;
	}

	if(!isDefined(wr_misu)){
		wr_misu = 0;
	}

	// 결제 구분에 따라 지급액 및 미지급금 계산
	if(wr_pay_type == "1"){// 카드 결제의 경우 발주금액을 지급액으로
		$("#wr_warehouse_price").prop("readonly",true);
		$("#wr_warehouse_price").addClass("readonly");
		$("#wr_warehouse_price").val(wr_order_total);
		$("#wr_misu").val(0);
		re_warehouse_price = wr_order_total;
	}else if(wr_pay_type == "2"){// 직발주의 경우 관리자가 직접 입력
		$("#wr_warehouse_price").prop("readonly",false);
		$("#wr_warehouse_price").removeClass("readonly");
		wr_misu = wr_order_total - wr_warehouse_price;
		if(wr_misu < 0){
			alert("지급액이 발주금액을 초과하였습니다.");
			$("#wr_warehouse_price").val(re_warehouse_price);
			return false;
		}else{
			$("#wr_misu").val(wr_misu);
			re_warehouse_price = wr_warehouse_price;
		}
	}else{
		$("#wr_warehouse_price").prop("readonly",true);
		$("#wr_warehouse_price").addClass("readonly");
		$("#wr_warehouse_price").val(0);
		$("#wr_misu").val(wr_order_total);
	}
}

// 초기화면으로 돌아가기
function fnResetData(){
	var seq = $("input[name='sales0_id']").val();
	fnModalReset(seq);
}

// 데이터 저장
function fnSaveData(){
	if(!confirm("저장한 내용은 복원이 불가능합니다.\n수정을 하기셌습니까?")){
		return false;
	}
	$("#order_frm").ajaxSubmit({
		url:"../sales/ajax.order_proc.php",
		dataType:"json",
		success:function(data){
			if(data.ret_code == true){
				fnResetData();
			}else{
				alert(data.message);
			}
		}
	})
}
</script>