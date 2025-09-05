<?php
include_once('./_common.php');

$row = sql_fetch("select * from g5_sales1_list where seq = '{$seq}'");
$sql = "select *,IF(wr_18>wr_19,wr_18,wr_19) AS wr_weight3 from g5_write_product where wr_id = '{$row['wr_product_id']}'";
$item = sql_fetch($sql);

if (isEmpty($item)) {
    $sql = "select *,IF(wr_18>wr_19,wr_18,wr_19) AS wr_weight3 from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."')";
    $item = sql_fetch($sql);
}

$item_32_stock = $item['wr_32'];
$item_36_stock = $item['wr_36'];

# 결제카드 조회
$sql = "SELECT * FROM g5_sales_metadata WHERE entity_type = 'g5_sales1_list' AND entity_id = '{$seq}' AND `key` = 'code_card'";
$card = sql_fetch($sql);

# 배송업체 불러오기
$sql = "SELECT * FROM `g5_delivery_company` WHERE wr_use = '1'";
$deliRs = sql_query($sql);

# 발주 단가가 없을 경우 상품에서 가져오기
if (!$row['wr_order_price']) {
    $row['wr_order_price'] = $item['wr_22'];
}

# 출고등록 되었을 경우 출고일 기준 환율 가져오기
$sql = "SELECT * FROM g5_sales3_list WHERE sales1_id='".$seq."'";
$sales3 = sql_fetch($sql);
if (isDefined($sales3['wr_date4'])) {
    $wr_date = date("Y-m-d", strtotime($sales3['wr_date4']));
} else {
    $wr_date = date("Y-m-d", strtotime($row['wr_date']));
}
# 업체요청으로 등록시점에 저장된 정보가 아닌 환율로그에서 데이터 가져오는 형식으로 변경
$today = date("Y-m-d");
if ($today == $wr_date) {
    $exData = fnGetExcharge($row['wr_currency']);
    $row['wr_exchange_rate'] = str_replace(",", "", $exData['rate']);
} else {
    $exData = fnGetExcharge($row['wr_currency'], $wr_date);
    $row['wr_exchange_rate'] = str_replace(",", "", $exData['rate']);
    if (!$row['wr_exchange_rate']) {
        $after_month = date("Y-m-d", strtotime($wr_date."+1 month"));
        //$after_table = "g5_excharge_".date("Ym",strtotime($after_month));
        $after_table = "g5_excharge_log";
        $sql = "SELECT * FROM ".$after_table." WHERE ex_eng='".$row['wr_currency']."' AND ex_date='".$wr_date."'";
        $exData = sql_fetch($sql);
        $row['wr_exchange_rate'] = str_replace(",", "", $exData['rate']);
        if (!$row['wr_exchange_rate']) {
            $exData = fnGetExcharge($row['wr_currency']);
            $row['wr_exchange_rate'] = str_replace(",", "", $exData['rate']);
        }
    }
}

if ($row['wr_currency'] == "JPY") {
    $row['wr_exchange_rate'] = $row['wr_exchange_rate'] * 0.01;
}

if ($row['wr_currency'] == "KRW") {
    $row['wr_exchange_rate'] = 1;
}

# 안전 재고 없을 경우
$date = new DateTime($row['wr_date2']);
$date->modify('-1 days');
$ed_date = $date->format('Y-m-d');

$date = new DateTime($ed_date);
$date->modify('-3 months');
$st_date = $date->format('Y-m-d');

$product_id = $item['wr_id'];

# 안전재고 수량
$sql = "
    SELECT
    SUM(G0.wr_ea) AS total_sales_ea
    FROM g5_sales3_list G0
    LEFT JOIN g5_write_product WP ON WP.wr_id = G0.wr_product_id
    WHERE wr_release_use = '1'
    AND G0.wr_date4 BETWEEN '$st_date' AND '$ed_date' 
    AND G0.wr_product_id = '{$product_id}'
    GROUP BY
    G0.wr_product_id    
  ";

$safe_result = sql_fetch($sql);
$safe_ea = round(($safe_result['total_sales_ea'] / 3) * 2);
$row['wr_safe_ea'] = $safe_ea;


?>

<div class="tbl_frm01 tbl_wrap" style="margin:0">
	<input type="hidden" name="seq" value="<?php echo $seq?>">
	<input type="hidden" name="wr_etc_chk" value="<?=$row['wr_etc_chk']?>"/>
	<table>
		<!-- 주문정보 -->
		<tr>
			<th class="title" rowspan="7">주문정보</th>
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
			<td colspan="2"></td>
		</tr>

		<tr>
			<th>주문자ID</th>
			<td>
				<input type="text" name="wr_mb_id" value="<?=$row['wr_mb_id']?>" class="readonly" readonly/>
			</td>
			<th>주문자명</th>
			<td>
				<input type="text" name="wr_mb_name" value="<?=$row['wr_mb_name']?>" class="readonly" readonly/>
			</td>
			<th>전화번호</th>
			<td>
				<input type="text" name="wr_tel" value="<?=$row['wr_tel']?>" class="readonly" readonly/>
			</td>
			<th>이메일</th>
			<td>
				<input type="text" name="wr_email" value="<?=$row['wr_email']?>" class="readonly" readonly/>
			</td>
		</tr>
		<tr>
			<th>수량</th>
			<td>
				<input type="text" name="wr_ea" value="<?=$row['wr_ea']?>" class="readonly" readonly/>
			</td>
			<th>박스수</th>
			<td>
				<input type="text" name="wr_box" value="<?=$row['wr_box']?>" class="readonly" readonly/>
			</td>
			<th>단가(<?=$row['wr_currency']?>)</th>
			<td>
				<input type="text" name="wr_danga" class="cal_price" value="<?=$row['wr_danga']?>"/>
			</td>
			<th>신고가격(<?=$row['wr_currency']?>)</th>
			<td>
				<input type="text" name="wr_singo" class="cal_price" value="<?=$row['wr_singo']?>"/>
			</td>
		</tr>
		<tr>
			<th>통화</th>
			<td>
				<input type="text" name="wr_currency" id="wr_currency" value="<?=$row['wr_currency']?>" class="readonly" readonly/>
			</td>
			<th>환율</th>
			<td>
				<input type="text" name="wr_exchange_rate" id="wr_exchange_rate" value="<?=$row['wr_exchange_rate']?>" class="readonly" readonly/>
			</td>
			<th>단가(KRW)</th>
			<td>
				<input type="text" id="wr_exchange_danga" value="<?=floor((float)$row['wr_danga'] * (float)$row['wr_exchange_rate'])?>" class="readonly" readonly/>
			</td>
			<th>신고가격(KRW)</th>
			<td>
				<input type="text" id="wr_exchange_singo" value="<?=floor((float)$row['wr_singo'] * (float)$row['wr_exchange_rate'])?>" class="readonly" readonly/>
			</td>
		</tr>
		<tr>
			<th>수수료1(<?=$row['wr_currency']?>)</th>
			<td>
				<input type="text" name="wr_fee1" id="wr_fee1" class="cal_price" value="<?=$row['wr_fee1']?>"/>
			</td>
			<th>수수료1(KRW)</th>
			<td>
				<input type="text" id="wr_fee1_exchange" value="<?=((int)$row['wr_fee1'] * (int)$row['wr_exchange_rate'])?>" class="readonly" readonly/>
			</td>
			<th>수수료2(<?=$row['wr_currency']?>)</th>
			<td>
				<input type="text" name="wr_fee2" id="wr_fee2" class="cal_price" value="<?=$row['wr_fee2']?>"/>
			</td>
			<th>수수료2(KRW)</th>
			<td>
				<input type="text" id="wr_fee2_exchange" value="<?=((float)$row['wr_fee2'] * (float)$row['wr_exchange_rate'])?>" class="readonly"readonly/>
			</td>
		</tr>
		<tr>
			<th>도시명</th>
			<td>
				<input type="text" name="wr_city" value="<?=$row['wr_city']?>" class="readonly" readonly/>
			</td>
			<th>주명</th>
			<td>
				<input type="text" name="wr_ju" value="<?=$row['wr_ju']?>" class="readonly" readonly/>
			</td>
			<th>나라명</th>
			<td>
				<input type="text" name="wr_country" value="<?=$row['wr_country']?>" class="readonly" readonly/>
			</td>
			<td colspan="2"></td>
		</tr>

		<tr>
			<th>우편번호</th>
			<td>
				<input type="text" name="wr_zip" value="<?php echo $row['wr_deli_zip']?>" class="readonly" readonly>
			</td>
			<th>주소</th>
			<td colspan="6">
				<input type="text" name="wr_addr1" value="<?=$row['wr_addr1']?>" style="width:48%" class="readonly" readonly>
				<input type="text" name="wr_addr2" value="<?=$row['wr_addr2']?>" style="width:48%" class="readonly" readonly>
			</td>
		</tr>
		<!--// 주문정보 -->

		<!-- 상품정보 -->
		<tr>
			<th class="title" rowspan="3">제품정보</th>
			<th>제품명</th>
			<td>
				<input type="text" name="wr_subject" value="<?=$item['wr_subject']?>" class="readonly" readonly/>
			</td>
			<th>SKU</th>
			<td>
				<input type="text" name="wr_code" value="<?=$row['wr_code']?>" class="readonly" class="readonly" readonly/>
			</td>
			<th>HS코드</th>
			<td>
				<input type="text" name="wr_make_country" value="<?=$row['wr_hscode']?>" class="readonly" readonly/>
			</td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th>수출신고품명</th>
			<td>
				<input type="text" name="wr_name2" value="<?=$row['wr_name2']?>" class="readonly" readonly/>
			</td>
			<th>수출국가</th>
			<td>
				<input type="text" name="wr_country_code" value="<?=$row['wr_country_code']?>" class="readonly" readonly/>
			</td>
			<th>통화</th>
			<td>
				<input type="text" name="wr_currency" value="<?=$row['wr_currency']?>" class="readonly" readonly/>
			</td>
			<td colspan="2"></td>
		</tr>
		
		<tr>
			<th>부피</th>
			<td>
				<input type="text" name="wr_weight3" value="<?=(($row['wr_weight3']) ? $row['wr_weight3'] : ((int)$item['wr_14'] * (int)$item['wr_15'] * (int)$item['wr_16'])) * $row['wr_ea']?>" class="readonly" readonly/>
			</td>
			<th>중량(개당무게)</th>
			<td>
				<input type="text" name="wr_weight1" value="<?=($row['wr_weight1']) ? (float)$row['wr_weight1'] : (float)$item['wr_10']?>" class="readonly" readonly/>
			</td>
			<th>총 무게</th>
			<td>
				<input type="text" name="wr_weight2" value="<?=($row['wr_weight2']) ? (float)$row['wr_weight2'] : ((float)$item['wr_10'] * (int)$row['wr_ea'])?>" class="readonly" readonly/>
			</td>
			<th>무게 단위</th>
			<td>
				<input type="text" value="<?=$item['wr_12']?>" class="readonly" readonly/>
			</td>
		</tr>
		<!--// 상품정보 -->

		<!-- 발주 정보 -->
		<tr>
			<th class="title" rowspan="2">발주정보</th>
			<th>발주주문번호</th>
			<td>
				<input type="text" name="wr_order_num2" value="<?=$row['wr_order_num2']?>"/>
			</td>
			<th>발주일자</th>
			<td>
				<input type="date" name="wr_date2" value="<?=$row['wr_date2']?>"/>
			</td>
			<th>발주처</th>
			<td>
				<div style="display:flex;">
				<select name="wr_orderer" class="frm_input search_sel" style="background:#FFFFFF;">
				<?php
                $arr = get_code_list('5');
foreach ($arr as $k => $v) {
    $selected = $v['idx'] == $row['wr_orderer'] ? "selected" : "";
    echo "<option value='{$v['idx']}' {$selected} >{$v['code_name']}</option>";
}
?>
				</select>

				<select name="metadata_code_card" class="frm_input search_sel" style="background:#FFFFFF;">
					<option value="0">결제카드</option>
					<?php
        $arr = get_code_list('7');
foreach ($arr as $key => $v) {
    $selceted = $v['idx'] == $card['value'] ? "selected" : "";
    echo "<option value='{$v['idx']}' {$selceted}>{$v['code_name']}</option>";
}
?>
				</select>

				</div>
			</td>
			<th>트래킹NO</th>
			<td>
				<input type="text" name="wr_order_traking" value="<?=$row['wr_order_traking']?>"/>
			</td>
		</tr>
		<tr>
			<th>안전재고(한/미)<br>발주수량</th>
			<td>
				<input type="text" name="wr_safe_ea" class="readonly" onkeyup="this.value=get_number(this.value);" value="<?=$row['wr_safe_ea']?>" style="width: 80%;" readonly/><span style="padding-left: 3px;">(<?= number_format($item_32_stock) ?>/<?= number_format($item_36_stock) ?>)</span><br>
				<input type="text" name="wr_order_ea" class="auto_order" onkeyup="this.value=get_number(this.value);" value="<?=$row['wr_order_ea']?>"/>
			</td>
			<th>발주단가</th>
			<td>
				<input type="text" name="wr_order_price" class="auto_order" onkeyup="this.value=get_number(this.value);" style="width:80%" value="<?=$row['wr_order_price']?>"/>
				<select name="wr_taxType" id="wr_taxType" class="frm_input" style="width:18%;background:#FFFFFF;">
					<option value="1" <?=get_selected("1", $row['wr_taxType'])?>>과세</option>
					<option value="2" <?=get_selected("2", $row['wr_taxType'])?>>면세</option>
				</select>
			</td>
			<th>배송비</th>
			<td>
				<input type="text" name="wr_order_fee" class="auto_order" onkeyup="this.value=get_number(this.value);" value="<?=$row['wr_order_fee']?>"/>
			</td>
			<th>발주금액</th>
			<td>
				<input type="text" name="wr_order_total" onkeyup="this.value=get_number(this.value);" value="<?=$row['wr_order_total']?>"/>
			</td>
		</tr>
		<!--// 발주 정보 -->

		<!-- 배송정보 -->
		<tr>
			<th class="title" rowspan="5">배송정보</th>
			<th>수령자명</th>
			<td>
				<input type="text" name="wr_deli_nm" value="<?=$row['wr_deli_nm']?>" class="readonly" readonly/>
			</td>
			<th>수령자 연락처</th>
			<td>
				<input type="text" name="wr_deli_tel" value="<?=$row['wr_deli_tel']?>" class="readonly" readonly/>
			</td>
			<th>Service Type</th>
			<td>
				<select name="wr_servicetype" class="frm_input" disabled>
					<option value="">==Service Type==</option>
					<?foreach ($service_arr as $key => $val) {?>
						<option value="<?=$key?>" <?=get_selected($key, $row['wr_servicetype'])?>><?=$val?>(<?=$key?>)</option>
					<?}?>
				</select>
			</td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th>도시명</th>
			<td>
				<input type="text" name="wr_deli_city" value="<?=$row['wr_deli_city']?>" class="readonly" readonly/>
			</td>
			<th>주명</th>
			<td>
				<input type="text" name="wr_deli_ju" value="<?=$row['wr_deli_ju']?>" class="readonly" readonly/>
			</td>
			<th>나라명</th>
			<td>
				<input type="text" name="wr_deli_country" value="<?=$row['wr_deli_country']?>" class="readonly" readonly/>
			</td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th>우편번호</th>
			<td>
				<input type="text" name="wr_deli_zip" value="<?=$row['wr_deli_zip']?>" class="readonly" readonly/>
			</td>
			<th>주소</th>
			<td colspan="6">
				<input type="text" name="wr_deli_addr1" value="<?=$row['wr_deli_addr1']?>" style="width:48%" class="readonly" readonly>
				<input type="text" name="wr_deli_addr2" value="<?=$row['wr_deli_addr2']?>" style="width:48%" class="readonly" readonly>
			</td>
		</tr>
		<tr>
			<th>배송사</th>
			<td>
				<select name="wr_delivery" id="wr_delivery" class="frm_input">
					<option value="" data="0" oil_percent="0" currency="">==배송사 선택==</option>
					<?php
$delivery_sql = "select * from g5_delivery_company where wr_use = 1";
$delivery_rst = sql_query($delivery_sql);

$wr_delivery_fee = $row['wr_delivery_fee'];

while ($delivery_com = sql_fetch_array($delivery_rst)) {
    $delivery[$delivery_com['wr_code']] = $delivery_com['wr_name'];
}

$country_dcode = sql_fetch("SELECT wr_code AS code FROM g5_country WHERE code_2 = '{$row['wr_deli_country']}'");
$country = $country_dcode['code'];

$sql = "SELECT {$country} AS price, cust_code, weight_code, B.wr_percent as 'code_percent' FROM g5_shipping_price A
								LEFT OUTER JOIN g5_delivery_company B ON B.wr_code=A.cust_code
								WHERE weight_code >= {$row['wr_weight2']} and {$country} != 0 and B.wr_use = '1' group by cust_code order by price asc";
$rst = sql_query($sql);

for ($i = 0; $delivery2 = sql_fetch_array($rst); $i++) {
    ?>
						<option value="<?=$delivery2['cust_code']?>" data="<?=$delivery2['price']?>" oil_percent="<?=$delivery2['code_percent']?>" currency="<?=($delivery2['cust_code'] == "1021") ? "JPY" : "KRW"?>" <?=get_selected($row['wr_delivery'], $delivery2['cust_code'])?>><?=$delivery[$delivery2['cust_code']]?></option>
					<?php
        if ($i == 0 && !$wr_delivery_fee) {
            $wr_delivery_fee = $delivery2['price'];
        }
}?>
				</select>
			</td>
			<th>배송요금</th>
			<td>
				<input type="text" name="wr_delivery_fee" value="<?=$row['wr_delivery_fee']?>"/>
			</td>
			<th>배송 유류할증료</th>
			<td>
				<input type="hidden" id="wr_delivery_oil_percent" value=""/>
				<input type="text" name="wr_delivery_oil" id="wr_delivery_oil" value="<?=$row['wr_delivery_oil']?>" class="readonly" readonly/>
			</td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th>배송통화</th>
			<td>
				<input type="text" id="wr_delivery_currency" value="<?=($row['wr_delivery'] == "1021") ? "JPY" : "KRW"?>" class="readonly" readonly/>
			</td>
			<th>추가 배송요금</th>
			<td>
				<input type="text" name="wr_delivery_fee2" value="<?=$row['wr_delivery_fee2']?>"/>
			</td>
			<th>총 배송비</th>
			<td>
				<input type="text" id="wr_delivery_total" value="" class="readonly" readonly/>
			</td>
			<td colspan="2"></td>
		</tr>
		<!--// 배송정보 -->
		
		<!-- 담당자 정보-->
		<tr>
			<th class="title">담당자정보</th>
			<th>담당자</th>
			<td>
				<select name="wmb_id" id="wmb_id" class="frm_input" style="background:#FFFFFF">
					<?php
$mbSql = " select mb_id, mb_name from g5_member order by mb_name asc";
$mbRst = sql_query($mbSql);
for ($i = 0; $mb = sql_fetch_array($mbRst); $i++) {
    ?>
						<option value="<?php echo $mb['mb_id']?>" <?php echo get_selected($mb['mb_id'], $row['mb_id'])?>><?php echo $mb['mb_name']?>(<?php echo $mb['mb_id']?>)</option>
					<?}?>
				</select>
			</td>
			<th>비고</th>
			<td colspan="5">
				<input type="text" name="wr_order_etc" value="<?=$row['wr_order_etc']?>"/>
			</td>
		</tr>
		<!--// 담당자 정보-->	
	</table>
	<div class="win_btn btn_confirm" style="margin-top:20px;text-align:center">
		<input type="button" value="저장" class="btn_submit btn_b01" id="frm_submit">
	</div>
</div>
<script>
$(function(){
	$(document).ready(function() {
		$('.search_sel').select2();

		// 총 배송요금 계산
		fnSelDelivery();

		$(".auto_order").bind("keyup",function(){
			var wr_order_ea = $("input[name='wr_order_ea']").val();
			var wr_order_price = $("input[name='wr_order_price']").val();
			var wr_order_fee = $("input[name='wr_order_fee']").val();
			if(!isDefined(wr_order_ea)){
				wr_order_ea = 0;
			}

			if(!isDefined(wr_order_price)){
				wr_order_price = 0;
			}

			if(!isDefined(wr_order_fee)){
				wr_order_fee = 0;
			}
			
			// 총 발주 금액
			var wr_order_total = (wr_order_ea * wr_order_price) + parseInt(wr_order_fee);
			$("input[name='wr_order_total']").val(wr_order_total);
		});

		// 배송사 선택시 배송요금 자동 계산
		$("#wr_delivery").bind("change",function(){
			var wr_delivery_fee = $("#wr_delivery option:selected").attr("data");
			var wr_delivery_currency =  $("#wr_delivery option:selected").attr("currency");
			var wr_delivery_oil_percent = $("#wr_delivery option:selected").attr("oil_percent");

			$("input[name='wr_delivery_fee']").val(wr_delivery_fee);
			$("#wr_delivery_currency").val(wr_delivery_currency);
			$("#wr_delivery_oil_percent").val(wr_delivery_oil_percent);
			
			fnCalcDeliveryOil();
		});

		$("input[name='wr_delivery_fee2']").bind("keyup",function(){
			fnSelDelivery();
		});

		// 금액 변경시 환율 계산
		$(".cal_price").bind("blur",function(){
			var wr_danga = $("input[name='wr_danga']").val();
			var wr_singo = $("input[name='wr_singo']").val();
			var wr_fee1 = $("input[name='wr_fee1']").val();
			var wr_fee2 = $("input[name='wr_fee2']").val();
			var wr_exchange = $("#wr_exchange_rate").val();

			var wr_danga_exchange = Math.floor(wr_danga * wr_exchange);
			var wr_singo_exchange = Math.floor(wr_singo * wr_exchange);
			var wr_fee1_exchange = Math.floor(wr_fee1 * wr_exchange);
			var wr_fee2_exchange = Math.floor(wr_fee2 * wr_exchange);

			$("#wr_exchange_danga").val(wr_danga_exchange);
			$("#wr_exchange_singo").val(wr_singo_exchange);
			$("#wr_fee1_exchange").val(wr_fee1_exchange);
			$("#wr_fee2_exchange").val(wr_fee2_exchange);
		});
	});
});

// 유류할증료 계산
function fnCalcDeliveryOil(){
	var wr_delivery_oil = 0;
	var wr_delivery_fee = $("input[name='wr_delivery_fee']").val();
	var wr_delivery_oil_percent = $("#wr_delivery_oil_percent").val();
	wr_delivery_oil = wr_delivery_fee * wr_delivery_oil_percent;
	$("input[name='wr_delivery_oil']").val(wr_delivery_oil.toFixed(0));
	fnSelDelivery()
}

// 배송사 선택시 배송요금 자동 계산
function fnSelDelivery(){
	var wr_delivery_fee = $("input[name='wr_delivery_fee']").val();
	var wr_delivery_fee2 = $("input[name='wr_delivery_fee2']").val();
	var wr_delivery_oil = $("input[name='wr_delivery_oil']").val();

	if(!isDefined(wr_delivery_fee)){
		wr_delivery_fee = 0;
	}
	if(!isDefined(wr_delivery_fee2)){
		wr_delivery_fee2 = 0;
	}
	if(!isDefined(wr_delivery_oil)){
		wr_delivery_oil = 0;
	}
	
	var wr_delivery_total = parseFloat(wr_delivery_fee) + parseFloat(wr_delivery_fee2) + parseFloat(wr_delivery_oil);
	$("#wr_delivery_total").val(wr_delivery_total);
}
</script>