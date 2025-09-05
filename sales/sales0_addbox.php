<?php 
include_once('./_common.php');

$row = sql_fetch("select * from g5_sales0_list where seq = '{$seq}'");
//상품마스터 SKU값으로만 매칭 되도록 수정요청.
$item = sql_fetch("select *,IF(wr_18 > wr_19, wr_18, wr_19) AS wr_weight3 from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");

# 배송업체 불러오기
$sql = "SELECT * FROM `g5_delivery_company` WHERE wr_use = '1'";
$deliRs = sql_query($sql);

# 출고등록 되었을 경우 출고일 기준 환율 가져오기
$sql = "SELECT * FROM g5_sales3_list WHERE sales0_id='".$seq."'";
$sales3 = sql_fetch($sql);
if(isDefined($sales3['wr_date4'])){
	$wr_date = date("Y-m-d",strtotime($sales3['wr_date4']));
}else{
	$wr_date = date("Y-m-d",strtotime($row['wr_date']));
}

# 업체요청으로 등록시점에 저장된 정보가 아닌 환율로그에서 데이터 가져오는 형식으로 변경
$today = date("Y-m-d");
if($today == $wr_date){
	$exData = fnGetExcharge($row['wr_currency']);
	$row['wr_exchange_rate'] = str_replace(",","",$exData['rate']);
}else{
	$exData = fnGetExcharge($row['wr_currency'],$wr_date);
	$row['wr_exchange_rate'] = str_replace(",","",$exData['rate']);
	if(!$row['wr_exchange_rate']){
		$after_month = date("Y-m-d",strtotime($wr_date."+1 month"));
		//$after_table = "g5_excharge_".date("Ym",strtotime($after_month));
		$after_table = "g5_excharge_log";
		$sql = "SELECT * FROM ".$after_table." WHERE ex_eng='".$row['wr_currency']."' AND ex_date='".$wr_date."'";
		$exData = sql_fetch($sql);
		$row['wr_exchange_rate'] = str_replace(",","",$exData['rate']);
		if(!$row['wr_exchange_rate']){
			$exData = fnGetExcharge($row['wr_currency']);
			$row['wr_exchange_rate'] = str_replace(",","",$exData['rate']);
		}
	}
}

if($row['wr_currency'] == "JPY"){
	$row['wr_exchange_rate'] = $row['wr_exchange_rate'] * 0.01;
}

if($row['wr_currency'] == "KRW"){
	$row['wr_exchange_rate'] = 1;
}
?>

<div class="tbl_frm01 tbl_wrap" style="margin:0">
	<input type="hidden" name="seq" value="<?php echo $seq?>">
	<input type="hidden" name="wr_product_id" value="<?=$row['wr_product_id']?>"/>
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
				<input type="date" name="wr_date" value="<?=$row['wr_date']?>" class="readonly" readonly/>
			</td>
			<th>주문번호</th>
			<td>
				<input type="text" name="wr_order_num" value="<?=$row['wr_order_num']?>" class="readonly" readonly/>
			</td>
			<th>주문자ID</th>
			<td>
				<input type="text" name="wr_mb_id" value="<?=$row['wr_mb_id']?>" class="readonly" readonly/>
			</td>
		</tr>

		<tr>
			<th>주문자명</th>
			<td>
				<input type="text" name="wr_mb_name" value="<?=$row['wr_mb_name']?>"/>
			</td>
			<th>전화번호</th>
			<td>
				<input type="text" name="wr_tel" value="<?=$row['wr_tel']?>"/>
			</td>
			<th>이메일</th>
			<td>
				<input type="text" name="wr_email" value="<?=$row['wr_email']?>"/>
			</td>
			<th>통화</th>
			<td>
				<input type="text" name="wr_currency" value="<?=$row['wr_currency']?>" class="readonly" readonly/>
			</td>
		</tr>
		<tr>
			<th>수량</th>
			<td>
				<input type="text" name="wr_ea" value="<?=$row['wr_ea']?>"/>
			</td>
			<th>박스수</th>
			<td>
				<input type="text" name="wr_box" value="<?=$row['wr_box']?>" class="readonly" readonly/>
			</td>
			<th>단가</th>
			<td>
				<input type="text" name="wr_danga" class="cal_price" value="<?=$row['wr_danga']?>" autocomplete="off"/>
			</td>
			<th>신고가격</th>
			<td>
				<input type="text" name="wr_singo" class="cal_price" value="<?=$row['wr_singo']?>" autocomplete="off"/>
			</td>
		</tr>
		<tr>
			<th>환율</th>
			<td>
				<input type="text" name="wr_exchange_rate" value="<?=$row['wr_exchange_rate']?>" class="readonly" readonly/>
			</td>
			<th>단가(환율적용)</th>
			<td>
				<input type="text" id="wr_exchange_danga" value="<?=floor($row['wr_danga']*$row['wr_exchange_rate'])?>" class="readonly" readonly/>
			</td>
			<th>신고가격(환율적용)</th>
			<td>
				<input type="text" id="wr_exchange_singo" value="<?=floor($row['wr_singo']*$row['wr_exchange_rate'])?>" class="readonly" readonly/>
			</td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th>수수료1(<?=$row['wr_currency']?>)</th>
			<td>
				<input type="text" name="wr_fee1" id="wr_fee1" class="cal_price" value="<?=$row['wr_fee1']?>" autocomplete="off"/>
			</td>
			<th>수수료1(KRW)</th>
			<td>
				<input type="text" id="wr_fee1_exchange" value="<?=($row['wr_fee1']*$row['wr_exchange_rate'])?>" class="readonly" readonly/>
			</td>
			<th>수수료2(<?=$row['wr_currency']?>)</th>
			<td>
				<input type="text" name="wr_fee2" id="wr_fee2" class="cal_price" value="<?=$row['wr_fee2']?>" autocomplete="off"/>
			</td>
			<th>수수료2(KRW)</th>
			<td>
				<input type="text" id="wr_fee2_exchange" value="<?=($row['wr_fee2']*$row['wr_exchange_rate'])?>" class="readonly" readonly/>
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
				<input type="text" name="wr_zip" value="<?php echo $row['wr_deli_zip']?>">
			</td>
			<th>주소</th>
			<td colspan="6">
				<input type="text" name="wr_addr1" value="<?=$row['wr_addr1']?>" style="width:48%">
				<input type="text" name="wr_addr2" value="<?=$row['wr_addr2']?>" style="width:48%">
			</td>
		</tr>
		<!--// 주문정보 -->

		<!-- 상품정보 -->
		<tr>
			<th class="title" rowspan="5">제품정보</th>
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
				<input type="text" name="wr_hscode" value="<?=$row['wr_hscode']?>"/>
			</td>
			<th>제품수량</th>
		</tr>
		<tr>
			<th>한국창고</th>
			<td>
				<input type="text" id="wr_32" value="<?=$item['wr_32']?>" class="readonly" readonly/>
			</td>
			<th>미국창고</th>
			<td>
				<input type="text" id="wr_36" value="<?=$item['wr_36']?>" class="readonly" readonly/>
			</td>
			<th>FBA</th>
			<td>
				<input type="text" id="wr_42" value="<?=$item['wr_42']?>" class="readonly" readonly/>
			</td>
			<th>W-FBA</th>
			<td>
				<input type="text" id="wr_43" value="<?=$item['wr_43']?>" class="readonly" readonly/>
			</td>
		</tr>
		<tr>
			<th>U-FBA</th>
			<td>
				<input type="text" id="wr_44" value="<?=$item['wr_44']?>" class="readonly" readonly/>
			</td>
			<th>임시창고</th>
			<td>
				<input type="text" id="wr_37" value="<?=$item['wr_37']?>" class="readonly" readonly/>
			</td>
			<td colspan="4"></td>
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
		</tr>
		
		<tr>
			<th>부피</th>
			<td>
				<input type="text" name="wr_weight3" value="<?=(($row['wr_weight3'])?$row['wr_weight3']:((int)$item['wr_14']*(int)$item['wr_15']*(int)$item['wr_16']))*$row['wr_ea']?>"/>
			</td>
			<th>중량(개당무게)</th>
			<td>
				<input type="text" name="wr_weight1" value="<?=($row['wr_weight1'])?(float)$row['wr_weight1']:(float)$item['wr_10']?>"/>
			</td>
			<th>총 무게</th>
			<td>
				<input type="text" name="wr_weight2" value="<?=($row['wr_weight2'])?(float)$row['wr_weight2']:((float)$item['wr_10']*(int)$row['wr_ea'])?>" class="readonly" readonly/>
			</td>
			<th>무게 단위</th>
			<td>
				<input type="text" value="<?=$item['wr_12']?>" class="readonly" readonly/>
			</td>
		</tr>
		
		<!--// 상품정보 -->

		<!-- 배송정보 -->
		<tr>
			<th class="title" rowspan="5">배송정보</th>
			<th>수령자명</th>
			<td>
				<input type="text" name="wr_deli_nm" value="<?=$row['wr_deli_nm']?>"/>
			</td>
			<th>수령자 연락처</th>
			<td>
				<input type="text" name="wr_deli_tel" value="<?=$row['wr_deli_tel']?>"/>
			</td>
			<th>Service Type</th>
			<td>
				<select name="wr_servicetype" class="frm_input" style="background:#FFFFFF;">
					<option value="">==Service Type==</option>
					<?foreach($service_arr as $key => $val){?>
						<option value="<?=$key?>" <?=get_selected($key,$row['wr_servicetype'])?>><?=$val?>(<?=$key?>)</option>
					<?}?>
				</select>
			</td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th>도시명</th>
			<td>
				<input type="text" name="wr_deli_city" value="<?=$row['wr_deli_city']?>"/>
			</td>
			<th>주명</th>
			<td>
				<input type="text" name="wr_deli_ju" value="<?=$row['wr_deli_ju']?>"/>
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
				<input type="text" name="wr_deli_zip" value="<?=$row['wr_deli_zip']?>"/>
			</td>
			<th>주소</th>
			<td colspan="6">
				<input type="text" name="wr_deli_addr1" value="<?=$row['wr_deli_addr1']?>" style="width:48%">
				<input type="text" name="wr_deli_addr2" value="<?=$row['wr_deli_addr2']?>" style="width:48%">
			</td>
		</tr>
		<tr>
			<th>배송사</th>
			<td>
				<select name="wr_delivery" id="wr_delivery" class="frm_input" style="background:#FFFFFF">
					<option value="" data="0" currency="KRW" oil_percent="0">==배송사 선택==</option>
					<?
						$delivery_sql = "select * from g5_delivery_company where wr_use = 1";
						$delivery_rst = sql_query($delivery_sql);

						$wr_delivery_fee = $row['wr_delivery_fee'];
						
						while($delivery_com=sql_fetch_array($delivery_rst)) {
							$delivery[$delivery_com['wr_code']] = $delivery_com['wr_name'];
						}

						$country_dcode = sql_fetch("SELECT wr_code AS code FROM g5_country WHERE code_2 = '{$row['wr_deli_country']}'"); 
						$country = $country_dcode['code'];

          $sql = "SELECT {$country} AS price, cust_code, weight_code, B.wr_percent as 'code_percent' FROM g5_shipping_price A
								LEFT OUTER JOIN g5_delivery_company B ON B.wr_code=A.cust_code
								WHERE weight_code >= {$row['wr_weight2']} and {$country} != 0 and B.wr_use = '1' group by cust_code order by price asc";
						$rst = sql_query($sql);
						$wr_delivery_fee = $row['wr_delivery_fee'];
						$oil_percent = 0;
						$wr_delivery_oil = $row['wr_delivery_oil'];
						for ($i=0; $delivery2=sql_fetch_array($rst); $i++) {
							if($delivery2['cust_code'] == $row['wr_delivery']){
								$oil_percent = $delivery2['code_percent'];
								$wr_delivery_fee = ($wr_delivery_fee != "")? $wr_delivery_fee:$delivery2['price'];
							}
						?>
							<option value="<?=$delivery2['cust_code']?>" data="<?=$delivery2['price']?>" currency="<?=($delivery2['cust_code'] == "1021")?"JPY":"KRW"?>" oil_percent="<?=$delivery2['code_percent']?>" <?=get_selected($row['wr_delivery'],$delivery2['cust_code'])?>><?=$delivery[$delivery2['cust_code']]?></option>
						<?
						}?>
				</select>
			</td>
			<th>배송요금</th>
			<td>
				<input type="text" name="wr_delivery_fee" value="<?=$row['wr_delivery_fee']?>"/>
			</td>
			<th>배송 유류할증료</th>
			<td>
				<input type="hidden" id="wr_delivery_oil_percent" value="<?=$oil_percent?>"/>
				<input type="text" name="wr_delivery_oil" id="wr_delivery_oil" value="<?=$wr_delivery_oil?>" class="readonly" readonly/>
			</td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th>배송통화</th>
			<td>
				<input type="text" id="wr_delivery_currency" class="frm_input readonly" value="<?=($row['wr_delivery'] == "1021")?"JPY":"KRW"?>" readonly/>
			</td>
			<th>추가 배송요금</th>
			<td>
				<input type="text" name="wr_delivery_fee2" value="<?=$row['wr_delivery_fee2']?>"/>
			</td>
			<th>총 배송요금</th>
			<td>
				<input type="text" name="wr_delivery_total" value="" class="readonly" readonly/>
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
					<?
					$mbSql = " select mb_id, mb_name from g5_member order by mb_name asc";
					$mbRst = sql_query($mbSql);
					for($i=0; $mb=sql_fetch_array($mbRst); $i++) {
					?>
						<option value="<?php echo $mb['mb_id']?>" <?php echo get_selected($mb['mb_id'], $row['mb_id'])?>><?php echo $mb['mb_name']?>(<?php echo $mb['mb_id']?>)</option>
					<?}?>
				</select>
			</td>
			<th>비고</th>
			<td>
				<input type="text" name="wr_etc" value="<?=$row['wr_etc']?>"/>
			</td>
		</tr>
		<!--// 담당자 정보-->

		

		
	</table>
	<div class="win_btn btn_confirm" style="margin-top:20px;text-align:center">
		<input type="button" value="저장" class="btn_submit btn_b01" id="frm_submit">
		<?
		$chk_cnt = sql_fetch(" SELECT COUNT(*) AS cnt FROM g5_write_product WHERE wr_id = '{$item['wr_id']}' ")['cnt'];
		if($chk_cnt > 0) $item_link = "location.href='".G5_BBS_URL."/write.php?bo_table=product&w=u&wr_id=".$item['wr_id']."'";
		else $item_link ="item_link()";
		?>
		<input type="button" value="상품 바로가기" class="btn_submit btn_b02" style="cursor:pointer;" onclick="<?=$item_link?>" >
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	fnCalcDeliveryTotal();
	// 수량 변경시 가능 여부 체크
	$("input[name='wr_ea']").bind("blur",function(){
		fnClacStock();
	});

	$("input[name='wr_weight1']").on("keyup",function(){
		var wr_weight1 = $(this).val();
		var wr_ea = $("input[name='wr_ea']").val();
		var wr_weight2 = wr_weight1 * wr_ea;
		$("input[name='wr_weight2']").val(wr_weight2);
		fnLoadDelivery();
	});
	$("#wr_delivery").bind("change",function(){
		fnSelDelivery();
	});

	$("input[name='wr_delivery_fee']").bind("keyup",function(){
		fnCalcDelivery();
	});

	$("input[name='wr_delivery_fee2']").bind("keyup",function(){
		fnCalcDeliveryTotal();
	});

	$(".cal_price").bind("blur",function(){
		var wr_danga = $("input[name='wr_danga']").val(); // 단가
		var wr_singo = $("input[name='wr_singo']").val(); // 신고가격
		var wr_fee1 = $("#wr_fee1").val(); // 수수료1
		var wr_fee2 = $("#wr_fee2").val(); // 수수료2
		var wr_ea = $("input[name='wr_ea']").val(); // 수량
		var wr_exchange = $("input[name='wr_exchange_rate']").val(); // 환율

		var wr_exchange_danga = Math.floor(wr_danga * wr_exchange); // 환율 적용 단가
		var wr_exchange_singo = Math.floor(wr_singo * wr_exchange); // 환율 적용 신고가
		var wr_fee1_exchange = Math.floor(wr_fee1 * wr_exchange); // 환율 적용 수수료1
		var wr_fee2_exchange = Math.floor(wr_fee2 * wr_exchange); // 환율 적용 수수료2
		
		$("#wr_exchange_danga").val(wr_exchange_danga);
		$("#wr_exchange_singo").val(wr_exchange_singo);
		$("#wr_fee1_exchange").val(wr_fee1_exchange);
		$("#wr_fee2_exchange").val(wr_fee2_exchange);
	});
})

// 수량 변경시 변경 가능 여부 체크
function fnClacStock(){
	var ori_wr_ea = <?=$row['wr_ea']?>;
	var params = "mode=sales0&seq=<?=$seq?>&wr_ea="+$("input[name='wr_ea']").val();
	$.ajax({
		url:"./ajax.sales_stock.php?"+params,
		dataType:"json",
		success:function(data){
			if(isDefined(data.message)){
				alert(data.message);
			}
			if(data.ret_code == false){
				$("input[name='wr_ea']").val(ori_wr_ea);
			}else{
				fnCalcSingo();
			}
		}
	});
}

// 신고가격 계산
function fnCalcSingo(){
	var wr_ea = $("input[name='wr_ea']").val();
	var wr_danga = $("input[name='wr_danga']").val();
	var wr_singo = parseFloat(wr_ea) * parseFloat(wr_danga);
	$("input[name='wr_singo']").val(wr_singo);
	fnCalcExchangeSingo();
}

// 신고 가격 변경시 환율 다시 계산
function fnCalcExchangeSingo(){
	var wr_exchange_rate = $("input[name='wr_exchange_rate']").val();
	var wr_singo = $("input[name='wr_singo']").val();
	var wr_exchage_singo = parseFloat(wr_exchange_rate) * parseFloat(wr_singo);
	$("#wr_exchange_singo").val(Math.floor(wr_exchage_singo));
}

// 중량 변경시 배송업체 다시 로드
function fnLoadDelivery(){
	var wr_country = $("input[name='wr_deli_country']").val();
	var max_weight = $("input[name='wr_weight2']").val();
	var params = "seq=<?=$seq?>&max_weight="+max_weight+"&wr_country="+wr_country;
	$.post("./ajax.sales0_upload.php",params,function(data){
		$("select[name='wr_delivery']").html(data);
	},'html');
	fnSelDelivery();
}

// 배송업체 변경시 이벤트
function fnSelDelivery(){
	var wr_delivery_fee = $("#wr_delivery option:selected").attr("data");
	var wr_delivery_currencty = $("#wr_delivery option:selected").attr("currency");
	var oil_percent = $("#wr_delivery option:selected").attr("oil_percent");
	if(!isDefined(oil_percent)){
		oil_percent = 0;
	}
	$("input[name='wr_delivery_fee']").val(wr_delivery_fee);
	$("#wr_delivery_currency").val(wr_delivery_currencty);
	$("#wr_delivery_oil_percent").val(oil_percent);
	fnCalcDelivery();
}

// 배송비(유류할증료) 계산
function fnCalcDelivery(){
	var wr_delivery_fee = $("input[name='wr_delivery_fee']").val();
	var oil_percent = $("#wr_delivery_oil_percent").val();
	if(!isDefined(wr_delivery_fee)){
		wr_delivery_fee = 0;
	}
	if(!isDefined(oil_percent)){
		oil_percent = 0;
	}
	var wr_delivery_oil = Math.round(wr_delivery_fee * oil_percent);
	$("input[name='wr_delivery_oil']").val(wr_delivery_oil);
	fnCalcDeliveryTotal();
}

// 총 배송비 계산
function fnCalcDeliveryTotal(){
	var wr_delivery_fee = $("input[name='wr_delivery_fee']").val();
	var wr_delivery_fee2 = $("input[name='wr_delivery_fee2']").val();
	var wr_delivery_oil = $("input[name='wr_delivery_oil']").val();
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
	$("input[name='wr_delivery_total']").val(wr_delivery_total);
}
</script>