<?php
include_once('./_common.php');

$row = sql_fetch("select * from g5_sales3_list where seq = '{$seq}'");
//상품마스터 SKU값으로만 매칭 되도록 수정요청.
$item = sql_fetch("select *,IF(wr_18 > wr_19,wr_18,wr_19) AS wr_weight3 from g5_write_product where wr_id = '{$row['wr_product_id']}' ");

if ($row['wr_direct_use'] == "1") {
	$row['wr_order_price'] = $item['wr_22'];
	$row['wr_order_total'] = (float)$item['wr_22'] * (int)$row['wr_ea'];
}
if ($row['wr_orderer']) {
	$orderer_data = sql_fetch("SELECT * FROM g5_code_list WHERE idx = '{$row['wr_orderer']}'");
	$row['wr_orderer_nm'] = $orderer_data['code_name'];
} else {
	$row['wr_orderer_nm'] = "";
}

//231228 대표님 요청으로 수정 
$item_link = "window.open('" . G5_BBS_URL . "/write.php?bo_table=product&w=u&wr_id=" . $row['wr_product_id'] . "')";

function get_delivery_name($wr_delivery)
{
	return sql_fetch("SELECT wr_name FROM g5_delivery_company WHERE wr_code = '{$wr_delivery}' ")['wr_name'];
}

# 업체요청으로 등록시점에 저장된 정보가 아닌 환율로그에서 데이터 가져오는 형식으로 변경
$today = date("Y-m-d");
$wr_date = date("Y-m-d", strtotime($row['wr_date4']));
if ($today == $wr_date) {
	$exData = fnGetExcharge($row['wr_currency']);
	$row['wr_exchange_rate'] = str_replace(",", "", $exData['rate']);
} else {
	$exData = fnGetExcharge($row['wr_currency'], $wr_date);
	$row['wr_exchange_rate'] = str_replace(",", "", $exData['rate']);
	if (!$row['wr_exchange_rate']) {
		$after_month = date("Y-m-d", strtotime($wr_date . "+1 month"));
		//$after_table = "g5_excharge_".date("Ym",strtotime($after_month));
		$after_table = "g5_excharge_log";
		$sql = "SELECT * FROM " . $after_table . " WHERE ex_eng='" . $row['wr_currency'] . "' AND ex_date='" . $wr_date . "'";
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

$hap_chk = false;
switch ($row['wr_domain']) {
	case "dodoskin":
		if (preg_match('/[a-zA-Z]/', $row['wr_order_num']))
			$hap_chk = true;
		$sOrdernum = '#' . preg_replace("/[^0-9]/", "", $row['wr_order_num']); //합배송 문자열 제거
		break;
	case "Shopee BR":
		if (strlen($row['wr_order_num']) > 14)
			$hap_chk = true;
		$sOrdernum = substr($row['wr_order_num'], 0, 13);
		break;
	case "Ebay-dodoskin":
	case "Ebay":
		if (strlen($row['wr_order_num']) > 6)
			$hap_chk = true;
		$sOrdernum = substr($row['wr_order_num'], 0, 6);
		break;
	case "AC":
	case "AC-CAD":
		if (strlen($row['wr_order_num']) > 17)
			$hap_chk = true;
		$sOrdernum = substr($row['wr_order_num'], 0, 17);
		break;

	case "AJP":
	case "ACF-CAD":
		if (strlen($row['wr_order_num']) > 19)
			$hap_chk = true;
		$sOrdernum = substr($row['wr_order_num'], 0, 19);
		break;
	default:
		if (preg_match('/[a-zA-Z]/', $row['wr_order_num']))
			$hap_chk = true;
		$sOrdernum = preg_replace("/[^0-9]/", "", $row['wr_order_num']); //합배송 문자열 제거
		break;
}

# 임시로 오리지널 주문번호로 넣음
if (isEmpty($sOrdernum) || $sOrdernum === '#') {
	$sOrdernum = $row['wr_ori_order_num'];
}

# 합배송 체크 다시 체크
if ($hap_chk) {
	$sql = "select COUNT(*) as cnt from g5_sales3_list where wr_ori_order_num = '{$row['wr_ori_order_num']}'";
	$hapResult = sql_fetch($sql);

	$hap_chk = ($hapResult['cnt'] > 1);
}

# 배송업체 불러오기
$sql = "SELECT * FROM `g5_delivery_company` WHERE wr_use = '1'";
$delivery_rst = sql_query($sql);
while ($delivery_com = sql_fetch_array($delivery_rst)) {
	$delivery[$delivery_com['wr_code']] = $delivery_com['wr_name'];
}

# 국가 코드 불러오기
$country_dcode = sql_fetch(" SELECT wr_code as code FROM g5_country WHERE code_2 = '{$row['wr_deli_country']}' ");
$country = $country_dcode['code'];

$sql = "SELECT {$country} AS price, cust_code, weight_code, ifnull(B.wr_percent, 0) as code_percent FROM g5_shipping_price A
		LEFT OUTER JOIN g5_delivery_company B ON B.wr_code=A.cust_code
		WHERE weight_code >= {$row['wr_weight2']} and {$country} != 0  group by cust_code order by price asc";
$rst = sql_query($sql);

if ($first == 1 && $hap_chk) {
	//합배송 관련 수정.
	//기획에 없었으나 수정요청으로 합배송은 리스트상에서 최상위 주문건만 컨트롤되도록 하기 위함.


	$hap_ea = 0;
	$hap_danga = 0;
	$hap_singo = 0;
	$hap_weight2 = 0; // 총 무게
	$hap_weight3 = 0; // 부피 총합

	$hap_order_price = 0; // 발주 단가(없을 경우 제품의 단가 불러오기)
	$hap_order_fee = 0; // 발주 배송비 없을 경우 0원
	$hap_order_total = 0;
	$hap_product = [];

	$hapSql = "select * from g5_sales3_list where wr_order_num LIKE '%$sOrdernum%' AND wr_warehouse='" . $row['wr_warehouse'] . "' order by wr_order_num asc";
	$hapRst = sql_query($hapSql);
	while ($hap = sql_fetch_array($hapRst)) {
		$wr_weight3 = sql_fetch("SELECT *,IF(wr_18 > wr_19,wr_18,wr_19) AS wr_weight3 FROM g5_write_product WHERE wr_id = '{$hap['wr_product_id']}' ");
		$hap_ea += $hap['wr_ea'];
		$hap_danga += $hap['wr_danga'];
		$hap_singo += $hap['wr_singo'];
		// $hap_weight2 += $hap['wr_weight2'];                       // 총 무게
		$hap_weight2 += (float)$wr_weight3['wr_10'] * (int)$hap['wr_ea'];        // 총 무게
		$hap_weight3 += ((float)$wr_weight3['wr_weight3'] * (int)$hap['wr_ea']); // 부피 * 수량
		$hap_product[] = $hap['wr_code'] . "|@@|" . $hap['wr_order_num'];

		# 발주 단가
		if (!$row['wr_order_price']) {
			$row['wr_order_price'] = (int)$wr_weight3['wr_22'];
		}

		if (strpos($row['wr_release_etc'], '반품출고 ') === 0) {
			$row['wr_order_price'] = 0;
		}

		$hap_order_price = $hap_order_price + (int)$row['wr_order_price'];

		# 발주 배송비
		$row['wr_order_fee'] = (!$row['wr_order_fee']) ? 0 : $row['wr_order_fee'];
		$hap_order_fee += $row['wr_order_fee'];

		$hap_order_total = $hap_order_total + (int)$row['wr_order_fee'] + (int)$row['wr_order_price'];

		//echo $wr_weight3['wr_10']." ".$hap['wr_ea']." ";
	}
?>
	<div class="tbl_frm01 tbl_wrap" style="margin:0">
		<input type="hidden" name="seq" value="<?php echo $seq ?>">
		<table>
			<!-- 주문정보 -->
			<tr>
				<th class="title" rowspan="3">주문정보</th>
				<th>도메인명</th>
				<td><input type="text" name="wr_domain" value="<?php echo $row['wr_domain'] ?>" class="readonly" readonly></td>
				<th>매출일자</th>
				<td><input type="date" name="wr_date" value="<?php echo $row['wr_date'] ?>" class="readonly" readonly></td>
				<th>주문번호</th>
				<td><input type="text" name="wr_order_num" value="<?php echo $sOrdernum ?> [합배송]" class="readonly" readonly></td>
				<th>주문자ID</th>
				<td><input type="text" name="wr_mb_id" value="<?php echo $row['wr_mb_id'] ?>" class="readonly" readonly></td>
				<th>주문자명</th>
				<td><input type="text" name="wr_mb_name" value="<?php echo $row['wr_mb_name'] ?>" class="readonly" readonly></td>
			</tr>
			<tr>
				<th>우편번호</th>
				<td><input type="text" name="wr_zip" value="<?php echo $row['wr_zip'] ?>" class="readonly" readonly></td>
				<th>주소</th>
				<td colspan="7" style="text-align:left">
					<input type="text" name="wr_addr1" value="<?php echo $row['wr_addr1'] ?>" style="width:48%" class="readonly" readonly>
					<input type="text" name="wr_addr2" value="<?php echo $row['wr_addr2'] ?>" style="width:48%" class="readonly" readonly>
				</td>
			</tr>

			<tr>
				<th>도시명</th>
				<td><input type="text" name="wr_city" value="<?php echo $row['wr_city'] ?>" class="readonly" readonly></td>
				<th>주명</th>
				<td><input type="text" name="wr_ju" value="<?php echo $row['wr_ju'] ?>" class="readonly" readonly></td>
				<th>나라명</th>
				<td><input type="text" name="wr_country" value="<?php echo $row['wr_country'] ?>" class="readonly" readonly></td>
				<th>전화번호</th>
				<td><input type="text" name="wr_tel" value="<?php echo $row['wr_tel'] ?>" class="readonly" readonly></td>
				<th>이메일</th>
				<td><input type="text" name="wr_email" value="<?php echo $row['wr_email'] ?>" class="readonly" readonly></td>
			</tr>
			<!--// 주문정보 -->



		</table>

		<table style="margin-top:20px">
			<?php
			$no = 0;
			$hap_cnt = count($hap_product);
			foreach ($hap_product as $product) {

				$info = explode('|@@|', $product);

				//sku값으로 만 조회. 추후 wr_id로 매칭해야될 것
				$item2 = sql_fetch("select *,IF(wr_18 > wr_19 , wr_18 , wr_19) AS wr_weight3 from g5_write_product where (wr_1 = '" . addslashes($info[0]) . "' or wr_27 = '" . addslashes($info[0]) . "' or wr_28 = '" . addslashes($info[0]) . "' or wr_29 = '" . addslashes($info[0]) . "' or wr_30 = '" . addslashes($info[0]) . "' or wr_31 = '" . addslashes($info[0]) . "') ");

				$orderRow = sql_fetch("select * from g5_sales3_list where wr_order_num = '{$info[1]}'");
			?>
				<tr>
					<? if ($no == 0) { ?>
						<th class="title" rowspan="<?= $hap_cnt ?>">제품정보</th>
					<? } ?>
					<th>제품정보<?php echo ($no + 1) ?></th>
					<td colspan="10">
						<input type="text" name="wr_code" value="<?php echo $item2['wr_1'] ?>" style="width:20%" class="readonly" readonly>


						<input type="text" name="wr_product_name2" value="<?php echo $item2['wr_subject'] ?>" style="width:58%" class="readonly" readonly>
						<?php if ($orderRow['wr_direct_use'] == 1) { ?>


							<?php if ($orderRow['wr_release_use'] == 1) { ?>
								<input type="text" value="<?php echo get_rack_name($orderRow['wr_rack']) ?>" class="readonly" readonly style="width:20%">
							<?php } else { ?>
								<select name="wr_rack[<?php echo $orderRow['seq'] ?>]" id="wr_rack" style="width:20%">

									<?php
									$sql_common = " from g5_rack ";
									$sql_search = " where gc_warehouse = '{$orderRow['wr_warehouse']}' and gc_use = 1 order by gc_name asc";
									$sql = " select * {$sql_common} {$sql_search}  ";
									$result = sql_query($sql);
									for ($i = 0; $rack = sql_fetch_array($result); $i++) {

										$item = sql_fetch("select * from g5_write_product where (wr_1 = '" . addslashes($orderRow['wr_code']) . "' or wr_27 = '" . addslashes($orderRow['wr_code']) . "' or wr_28 = '" . addslashes($orderRow['wr_code']) . "' or wr_29 = '" . addslashes($orderRow['wr_code']) . "' or wr_30 = '" . addslashes($orderRow['wr_code']) . "' or wr_31 = '" . addslashes($orderRow['wr_code']) . "') ");

										$stock = sql_fetch("select *, SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '{$rack['gc_warehouse']}' and wr_rack = '{$rack['seq']}' and wr_product_id = '{$item['wr_id']}' ");

										if ($stock['total'] <= 0) continue;
									?>
										<option value="<?php echo $rack['seq'] ?>"><?php echo $rack['gc_name'] ?> (재고:<?php echo $stock['total'] ?>)</option>
									<?php } ?>
								</select>

						<?php }
						} ?>

					</td>

				</tr>
			<?php $no++;
			} ?>

			<!-- 발주정보 -->
			<tr>
				<th class="title" rowspan="2">발주정보</th>
				<th>발주주문번호</th>
				<td>
					<input type="text" name="wr_order_num2" value="<?= $row['wr_order_num2'] ?>" class="input_frm readonly" readonly />
				</td>
				<th>발주일자</th>
				<td>
					<input type="text" name="wr_date2" value="<?= $row['wr_date2'] ?>" class="input_frm readonly" readonly />
				</td>
				<th>발주처</th>
				<td>
					<input type="text" name="wr_orderer_nm" value="<?= $row['wr_orderer_nm'] ?>" class="input_frm readonly" readonly />
				</td>
				<th>트래킹NO</th>
				<td>
					<input type="text" name="wr_order_traking" value="<?= $row['wr_order_traking'] ?>" class="input_frm readonly" readonly />
				</td>
				<td colspan="2"></td>
			</tr>
			<tr>
				<th>발주수량</th>
				<td>
					<input type="text" name="wr_order_ea" value="<?= $hap_ea ?>" class="input_frm readonly" readonly />
				</td>
				<th>발주단가</th>
				<td>
					<input type="text" name="wr_order_price" value="<?= $hap_order_price ?>" class="input_frm readonly" readonly />
				</td>
				<th>배송비</th>
				<td>
					<input type="text" name="wr_order_fee" value="<?= $hap_order_fee ?>" class="input_frm readonly" readonly />
				</td>
				<th>발주금액</th>
				<td>
					<input type="text" name="wr_order_total" value="<?= $hap_order_total ?>" class="input_frm readonly" readonly />
				</td>
				<td colspan="2"></td>
			</tr>
			<!--// 발주정보 -->

			<!-- 배송정보 -->
			<tr>
				<th class="title" rowspan="12">배송정보</th>
				<th>수령자명</th>
				<td>
					<input type="text" name="wr_deli_nm" value="<?= $row['wr_deli_nm'] ?>" class="readonly" readonly />
				</td>
				<th>수령자 연락처</th>
				<td>
					<input type="text" name="wr_deli_tel" value="<?= $row['wr_deli_tel'] ?>" class="readonly" readonly />
				</td>
				<td colspan="6"></td>
			</tr>
			<tr>
				<th>도시명</th>
				<td>
					<input type="text" name="wr_deli_city" value="<?= $row['wr_deli_city'] ?>" class="readonly" readonly />
				</td>
				<th>주명</th>
				<td>
					<input type="text" name="wr_deli_ju" value="<?= $row['wr_deli_ju'] ?>" class="readonly" readonly />
				</td>
				<th>나라명</th>
				<td>
					<input type="text" name="wr_deli_country" value="<?= $row['wr_deli_country'] ?>" class="readonly" readonly />
				</td>
				<td colspan="4"></td>
			</tr>
			<tr>
				<th>우편번호</th>
				<td>
					<input type="text" name="wr_deli_zip" value="<?= $row['wr_deli_zip'] ?>" class="readonly" readonly />
				</td>
				<th>주소</th>
				<td colspan="7">
					<input type="text" name="wr_deli_addr1" value="<?= $row['wr_deli_addr1'] ?>" style="width:48%" class="readonly" readonly>
					<input type="text" name="wr_deli_addr2" value="<?= $row['wr_deli_addr2'] ?>" style="width:48%" class="readonly" readonly>
				</td>
			</tr>
			<tr>
				<th>총 수량</th>
				<td><input type="text" name="wr_ea" value="<?php echo $hap_ea ?>" class="readonly" readonly></td>
				<th>박스수</th>
				<td><input type="text" name="wr_box" value="1" class="readonly" readonly></td>
				<th>총 단가(<?= $row['wr_currency'] ?>)</th>
				<td><input type="text" name="wr_danga" value="<?php echo $hap_danga ?>" class="readonly" readonly></td>
				<th>총 신고가격(<?= $row['wr_currency'] ?>)</th>
				<td><input type="text" name="wr_singo" value="<?php echo $hap_singo ?>" class="readonly" readonly></td>
				<td colspan="2"></td>
			</tr>
			<tr>
				<th>통화</th>
				<td><input type="text" name="wr_currency" value="<?php echo $row['wr_currency'] ?>" class="readonly" readonly></td>
				<th>환율</th>
				<td>
					<input type="text" name="wr_exchange_rate" value="<?= $row['wr_exchange_rate'] ?>" class="readonly" readonly />
				</td>
				<th>단가(KRW)</th>
				<td>
					<input type="text" id="wr_exchange_danga" value="<?= floor($hap_danga * $row['wr_exchange_rate']) ?>" class="readonly" readonly />
				</td>
				<th>단가(KRW)</th>
				<td>
					<input type="text" id="wr_exchange_singo" value="<?= floor($hap_singo * $row['wr_exchange_rate']) ?>" class="readonly" readonly />
				</td>
				<th>TAX</th>
				<td><input type="text" value="<?= $row['wr_tax'] ?>" readonly /></td>
			</tr>
			<tr>
				<th>수수료1(<?= $row['wr_currency'] ?>)</th>
				<td>
					<input type="text" name="wr_fee1" id="wr_fee1" value="<?= $row['wr_fee1'] ?>" class="readonly" readonly />
				</td>
				<th>수수료1(KRW)</th>
				<td>
					<input type="text" id="wr_fee1_exchange" value="<?= floor((float)$row['wr_fee1'] * (float)$row['wr_exchange_rate']) ?>" class="readonly" readonly />
				</td>
				<th>수수료2(<?= $row['wr_currency'] ?>)</th>
				<td>
					<input type="text" name="wr_fee2" id="wr_fee2" value="<?= $row['wr_fee2'] ?>" class="readonly" readonly />
				</td>
				<th>수수료2(KRW)</th>
				<td>
					<input type="text" id="wr_fee2_exchange" value="<?= floor($row['wr_fee2'] * $row['wr_exchange_rate']) ?>" class="readonly" readonly />
				</td>

				<th>Shipping price</th>
				<td><input type="text" value="<?= $row['wr_shipping_price'] ?>" readonly /></td>
			</tr>
			<tr>
				<th>개당무게</th>
				<td><input type="text" name="wr_weight1" value="<?php echo $row['wr_weight1'] ?>" class="readonly" readonly></td>
				<th>총 부피</th>
				<td><input type="text" name="" value="<?php echo $hap_weight3 ?>" class="readonly" readonly></td>
				<th>총 무게</th>
				<td><input type="text" name="wr_weight2" id="wr_weight2" value="<?php echo $hap_weight2 ?>" class="readonly" readonly></td>
				<th>무게단위</th>
				<td><input type="text" name="" value="<?php echo $item['wr_11'] ?>" class="readonly" readonly></td>
				<td colspan="2">
				</td>
			</tr>
			<tr id="delivery_frm">
				<th>Service Type</th>
				<td><input type="text" name="wr_servicetype" value="<?php echo $row['wr_servicetype'] ?>" class="readonly" readonly></td>
				<th>packaging</th>
				<td><input type="text" name="wr_packaging" value="<?php echo $row['wr_packaging'] ?>" class="readonly" readonly></td>
				<th>제조국가</th>
				<td><input type="text" name="wr_make_country" value="<?php echo $row['wr_make_country'] ?>" class="readonly" readonly></td>
				<th>HS코드</th>
				<td><input type="text" name="wr_hscode" value="<?php echo $row['wr_hscode'] ?>" class="readonly" readonly></td>
				<th>배송통화</th>
				<td>
					<input type="text" id="wr_delivery_currency" value="<?= ($row['wr_delivery'] == "1021") ? "JPY" : "KRW" ?>" class="readonly" readonly />
				</td>
			</tr>

			<tr id="delivery_area">
				<th>배송사</th>
				<?php
				$delivery_name = get_delivery_name($row['wr_delivery']);

				$sql = "
                SELECT {$country} AS price, cust_code, weight_code, B.wr_percent as 'code_percent'
                FROM g5_shipping_price A
                         LEFT OUTER JOIN g5_delivery_company B ON B.wr_code = A.cust_code
                WHERE 1
                  and B.wr_use = '1'
                group by cust_code
                order by price asc
              ";

				$result = sql_fetch_all($sql);
				$result_copy = $result;
				foreach ($result as $item) {
					$result_copy[$item['cust_code']] = $item;
				}

				$result2 = [];
				while ($delivery2 = sql_fetch_array($rst)) {
					$result2[] = $delivery2;
				}

				$merged = $result_copy;
				foreach ($result2 as $k => $v) {
					if (!isset($merged[$v['cust_code']])) {
						$merged[] = $v;
					}
				}

				?>
				<td>
					<select name="wr_delivery" class="frm_input" style="width:80%;height:30px;margin:5px;background:#FFFFFF;" onchange="">
						<option value="" data="0" currency="KRW">==배송사 선택==</option>
						<?php foreach ($merged as $i => $delivery2) { ?>
							<option value="<?= $delivery2['cust_code'] ?>" data="<?= $delivery2['price'] ?>" currency="<?= ($delivery['cust_code'] == "1021") ? "JPY" : "KRW" ?>" oil_percent="<?= $delivery2['code_percent'] ?>" <?= get_selected($delivery2['cust_code'], $row['wr_delivery']) ?>><?= $delivery[$delivery2['cust_code']] ?></option>
						<?php } ?>
					</select>
					<button type="button" class="btn_calc" id="btn_calc" onclick="open_cal_popup('<?= $row['seq'] ?>');">배송비 계산</button>
				</td>
				<th>배송요금</th>
				<td>
					<input type="text" name="wr_delivery_fee" id="wr_delivery_fee" value="<?= $row['wr_delivery_fee'] ?>">
					<input type="hidden" name="wr_delivery_fee_original" id="wr_delivery_fee_original" value="<?= $row['wr_delivery_fee'] ?>">
				</td>
				<th>추가배송비</th>
				<td>
					<input type="text" name="wr_delivery_fee2" id="wr_delivery_fee2" value="<?= $row['wr_delivery_fee2'] ?>">
					<input type="hidden" name="wr_delivery_fee2_original" id="wr_delivery_fee2_original" value="<?= $row['wr_delivery_fee2'] ?>">
				</td>
				<th>유류할증료</th>
				<td>
					<input type="hidden" id="oil_percent" value="" />
					<input type="text" name="wr_delivery_oil" id="wr_delivery_oil" value="<?= $row['wr_delivery_oil'] ?>" />
				</td>
				<th>배송 총금액</th>
				<td>
					<input type="text" name="wr_delivery_total" id="wr_delivery_total" value="" />
				</td>
			</tr>
			<tr>
				<th>수출신고품명</th>
				<td><input type="text" name="wr_name2" value="<?php echo $row['wr_name2'] ?>" class="readonly" readonly></td>
				<th>수출국가</th>
				<td><input type="text" name="wr_country_code" value="<?php echo $row['wr_country_code'] ?>" class="readonly" readonly></td>
				<th>발주일자</th>
				<td><input type="date" name="wr_date2" value="<?php echo $row['wr_date2'] ?>" class="readonly" readonly></td>
				<th>입고일자</th>
				<td><input type="date" name="wr_date3" value="<?php echo $row['wr_date3'] ?>" class="readonly" readonly></td>
				<th>출고일자</th>
				<td><input type="date" name="wr_date4" value="<?php echo $row['wr_date4'] ?>" class="readonly" readonly></td>
			</tr>

			<tr>
				<th>수출트래킹NO</th>
				<td>
					<input type="text" name="wr_release_traking" value="<?php echo $row['wr_release_traking'] ?>" style="width:69%">
					<button type="button" onclick="release_traking_update()" class="btn_b01">수정</button>
				</td>
				<th>비고</th>
				<td colspan="5">
					<input type="text" name="wr_release_etc" value="<?php echo $row['wr_release_etc'] ?>" style="width:92%;">
					<button type="button" onclick="etc_update()" class="btn_b01">수정</button>
				</td>
				<th>TBN</th>
				<td>
					<select name="tbn_code">
						<option value="">선택</option>
						<?php
						$query = "select * from g5_stock_box_order where wr_order_num = '{$row['wr_order_num']}'";
						$box = sql_fetch($query);

						$dateObj = new DateTime($row['wr_date4']);
						$date = $dateObj->format('Ymd');

						if ($row['wr_delivery'] == '1030') {
							$prefix = 'C';
						} else if ($row['wr_delivery'] == '1029') {
							$prefix = 'S';
						} else {
							$prefix = 'ETC';
						}
						for ($i = 1; $i < 21; $i++) {
							$value = "{$prefix}{$date}-{$i}";
							$selected = get_selected($box['box_code'], $value);
							echo "<option {$selected} value='{$prefix}{$date}-{$i}'>{$prefix}{$date}-{$i}</option>";
						}
						?>
					</select>
				</td>
			</tr>
		</table>

		<script>
			$(function() {

				// 초기 로드시 실행
				init();

				$(".number_fmt_list").bind('input', function() {
					this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
				});

				$("#hab_cal_save").on('click', function() {
					let hab_x = $("#hab_x").val(),
						hab_y = $("#hab_y").val(),
						hab_z = $("#hab_z").val(),
						hab_sum1 = $("#hab_weight1").val(),
						hab_sum2 = $("#hab_weight2").val(),
						hab_sum3 = $("#hab_weight3").val(),
						seq = $("input[name=seq]").val();

					const obj = {
						wr_hab_x: hab_x,
						wr_hab_y: hab_y,
						wr_hab_z: hab_z,
						wr_weight_sum1: hab_sum1,
						wr_weight_sum2: hab_sum2,
						wr_weight_sum3: hab_sum3,
						seq: seq,
					};

					if (!confirm("정말 저장하시겠습니까?")) {
						return false;
					}

					$.post(g5_url + "/sales/ajax.hab_cal_action.php", obj, function(data) {
						console.log(data);

						if (data == 'y') {
							alert("저장이 완료되었습니다.");
						} else {
							alert("오류가 발생했습니다.");
						}
					});
				});

				// 중량무게1,중량무게2
				$("#hab_cal_btn").on('click', function() {
					let hab_x = $("#hab_x").val(),
						hab_y = $("#hab_y").val(),
						hab_z = $("#hab_z").val(),
						weigth_sum1 = (parseInt(hab_x) * parseInt(hab_y) * parseInt(hab_z)) / 5000000,
						weigth_sum2 = (parseInt(hab_x) * parseInt(hab_y) * parseInt(hab_z)) / 6000000;

					if (hab_x == "") {
						alert("가로를 입력해주세요.");
						$("#hab_x").focus();
						return false;
					}
					if (hab_y == "") {
						alert("세로를 입력해주세요.");
						$("#hab_y").focus();
						return false;
					}
					if (hab_z == "") {
						alert("높이를 입력해주세요.");
						$("#hab_z").focus();
						return false;
					}
					//console.log(hab_x,hab_y,hab_z,weigth_sum1,weigth_sum2);

					$("#hab_weight1").val(weigth_sum1.toFixed(2));
					$("#hab_weight2").val(weigth_sum2.toFixed(2));

					delivery_list();
				});

				// 직접부피입력
				$("#now_hab_cal_btn").on('click', function() {
					let hab_weight1 = $("#hab_weight1").val(),
						hab_weight2 = $("#hab_weight2").val(),
						hab_weight3 = $("#hab_weight3").val();

					if (hab_weight1 == "") {
						alert("부피무게1을 입력해주세요.");
						$("#hab_weight1").focus();
						return false;
					}

					if (hab_weight2 == "") {
						alert("부피무게2을 입력해주세요.");
						$("#hab_weight2").focus();
						return false;
					}

					if (hab_weight3 == "") {
						alert("총 무게를 입력해주세요.");
						$("#hab_weight3").focus();
						return false;
					}

					delivery_list();
				});


				// 리셋
				$("#hab_reset_btn").on('click', function() {
					$("#hab_x,#hab_y,#hab_z,#hab_weight1,#hab_weight2,#hab_weight3").val("");
				});


				// 배송사저장
				$("#now_delivery_btn").on('click', function() {
					let wr_delivery = $("select[name=wr_delivery] option:selected").val(),
						wr_delivery_fee = $("#wr_delivery_fee").val(),
						wr_delivery_fee2 = $("#wr_delivery_fee2").val(),
						seq = $("input[name=seq]").val(),
						data = {
							wr_delivery: wr_delivery,
							wr_delivery_fee: wr_delivery_fee,
							wr_delivery_fee2: wr_delivery_fee2,
							seq: seq,
						};

					if (!wr_delivery) {
						alert("배송사가 존재하지 않습니다.");
						return false;
					}

					if (!wr_delivery_fee) {
						alert("배송비가 존재하지 않습니다.");
						return false;
					}

					if (!confirm("정말 배송사를 저장하시겠습니까?")) {
						return false;
					}

					const obj = HttpJson(g5_url + "/sales/ajax.delivery_save.php", "post", data);
					if (obj['result']) {
						alert("배송사 저장이 완료되었습니다.");
						$("#update_delivery_name").val(obj['delivery_name']);
						$("#update_delivery_fee").val(wr_delivery_fee);
						$("#update_delivery_fee2").val(wr_delivery_fee2);
					} else {
						alert("저장에 실패하였습니다.");
					}

				});

			});

			$("#delivery_cal_btn").bind("click", function() {
				delivery_list();
			});


			// 중량무게 입력완료시 배송사,배송요금 조회 추출
			function delivery_list() {
				// 초기화
				$(".del_chk").remove();

				let seq = $("input[name=seq]").val(), //나라명
					wr_country = $("input[name=wr_country]").val(), //나라명
					weight1 = parseFloat($("#hab_weight1").val()), //부피무게1
					weight2 = parseFloat($("#hab_weight2").val()), //부피무게2
					weight3 = parseFloat($("#hab_weight3").val()), //총 무게
					// weight4 = parseFloat($("#wr_weight2").val()),  //총 무게 (필드값)
					max = Math.max(weight1, weight2, weight3); // 가장 큰 수 구하기
				// weight1 = parseFloat($("#wr_weight2").val()),  //총 무게

				console.log(weight1 + "/" + weight2 + "/" + weight3 + "/" + max);

				const obj = {
					seq: seq,
					wr_country: wr_country,
					max_weight: max,
				}

				$.post(g5_url + "/sales/ajax.sales3_update.php", obj, function(data) {
					$("#delivery_area").html(data);
					fnCalcDeliveryTotal();
					$("#delivery_area").show();

				}, 'html');

			}

			// 초기화
			function init() {
				$("#delivery_area").hide();
				if (isDefined("<?= $row['wr_weight_sum1'] ?>")) {
					delivery_list();
				}
			}
		</script>

		<div class="win_btn btn_confirm" style="margin-top:20px;text-align:center">
			<input type="button" value="저장" class="btn_submit btn_b01 addbtn1" id="frm_submit">
			<?php if ($row['wr_release_use'] == 0) { ?>

				<input type="button" value="합배송 출고완료 처리" class="btn_submit btn_b01 hap_release_btn" id="frm_submit" style="background:#2a56ba" data="<?php echo $sOrdernum ?>">
			<?php } ?>
			<input type="button" value="상품 바로가기" class="btn_submit btn_b02" style="cursor:pointer;" onclick="<?= $item_link ?>">
		</div>
	</div>


<?php
} else {
?>

	<div class="tbl_frm01 tbl_wrap" style="margin:0">
		<input type="hidden" name="seq" value="<?php echo $seq ?>">
		<table>
			<!-- 주문정보 -->
			<tr>
				<th class="title" rowspan="3">주문정보</th>
				<th>도메인명</th>
				<td><input type="text" name="wr_domain" value="<?php echo $row['wr_domain'] ?>" class="readonly" readonly></td>
				<th>매출일자</th>
				<td><input type="date" name="wr_date" value="<?php echo $row['wr_date'] ?>" class="readonly" readonly></td>
				<th>주문번호</th>
				<td><input type="text" name="wr_order_num" value="<?php echo $sOrdernum ?> [합배송]" class="readonly" readonly></td>
				<th>주문자ID</th>
				<td><input type="text" name="wr_mb_id" value="<?php echo $row['wr_mb_id'] ?>" class="readonly" readonly></td>
				<th>주문자명</th>
				<td><input type="text" name="wr_mb_name" value="<?php echo $row['wr_mb_name'] ?>" class="readonly" readonly></td>
			</tr>
			<tr>
				<th>우편번호</th>
				<td><input type="text" name="wr_zip" value="<?php echo $row['wr_zip'] ?>" class="readonly" readonly></td>
				<th>주소</th>
				<td colspan="7" style="text-align:left">
					<input type="text" name="wr_addr1" value="<?php echo $row['wr_addr1'] ?>" style="width:48%" class="readonly" readonly>
					<input type="text" name="wr_addr2" value="<?php echo $row['wr_addr2'] ?>" style="width:48%" class="readonly" readonly>
				</td>
			</tr>

			<tr>
				<th>도시명</th>
				<td><input type="text" name="wr_city" value="<?php echo $row['wr_city'] ?>" class="readonly" readonly></td>
				<th>주명</th>
				<td><input type="text" name="wr_ju" value="<?php echo $row['wr_ju'] ?>" class="readonly" readonly></td>
				<th>나라명</th>
				<td><input type="text" name="wr_country" value="<?php echo $row['wr_country'] ?>" class="readonly" readonly></td>
				<th>전화번호</th>
				<td><input type="text" name="wr_tel" value="<?php echo $row['wr_tel'] ?>" class="readonly" readonly></td>
				<th>이메일</th>
				<td><input type="text" name="wr_email" value="<?php echo $row['wr_email'] ?>" class="readonly" readonly></td>
			</tr>
			<!--// 주문정보 -->

			<!-- 제품정보 -->
			<tr>
				<th class="title">제품정보</th>
				<th>SKU</th>
				<td>
					<input type="text" name="wr_code" value="<?php echo $item['wr_1'] ?>" class="readonly" readonly>
				</td>
				<th>제품명</th>
				<td colspan="3">
					<input type="text" name="wr_product_name2" value="<?php echo $item['wr_subject'] ?>" class="readonly" readonly>
				</td>
				<th>제품약칭</th>
				<td colspan="3">
					<input type="text" name="wr_product_name1" value="<?php echo $item['wr_2'] ?>" class="readonly" readonly>
				</td>
			</tr>
			<!--// 제품정보 -->

			<!-- 발주정보 -->
			<tr>
				<th class="title" rowspan="2">발주정보</th>
				<th>발주주문번호</th>
				<td>
					<input type="text" name="wr_order_num2" value="<?= $row['wr_order_num2'] ?>" class="input_frm readonly" readonly />
				</td>
				<th>발주일자</th>
				<td>
					<input type="text" name="wr_date2" value="<?= $row['wr_date2'] ?>" class="input_frm readonly" readonly />
				</td>
				<th>발주처</th>
				<td>
					<input type="text" name="wr_orderer_nm" value="<?= $row['wr_orderer_nm'] ?>" class="input_frm readonly" readonly />
				</td>
				<th>트래킹NO</th>
				<td>
					<input type="text" name="wr_order_traking" value="<?= $row['wr_order_traking'] ?>" class="input_frm readonly" readonly />
				</td>
				<td colspan="2"></td>
			</tr>
			<tr>
				<th>발주수량</th>
				<td>
					<input type="text" name="wr_order_ea" value="<?= $row['wr_order_ea'] ?>" class="input_frm readonly" readonly />
				</td>
				<th>발주단가</th>
				<td>
					<?php
					$display_price = 0;
					if (strpos($row['wr_release_etc'], '반품출고 ') === 0) {
						$display_price = 0;
					} else {
						if (!$row['wr_order_price']) {
							$display_price = $item['wr_22'];
						} else {
							$display_price = $row['wr_order_price'];
						}
					}
					?>
					<input type="text" name="wr_order_price" value="<?= $display_price ?>" class="input_frm readonly" readonly />
				</td>
				<th>배송비</th>
				<td>
					<input type="text" name="wr_order_fee" value="<?= $row['wr_order_fee'] ?>" class="input_frm readonly" readonly />
				</td>
				<th>발주금액</th>
				<td>
					<?php
					$display_total = 0;
					if (strpos($row['wr_release_etc'], '반품출고 ') === 0) {
						$display_total = 0;
					} else {
						if (!$row['wr_order_total']) {
							$display_total = $item['wr_22'] * $row['wr_ea'];
						} else {
							$display_total = $row['wr_order_total'];
						}
					}
					?>
					<input type="text" name="wr_order_total" value="<?= $display_total ?>" class="input_frm readonly" readonly />
				</td>
				<td colspan="2"></td>
			</tr>
			<!--// 발주정보 -->

			<!-- 배송정보 -->
			<tr>
				<th class="title" rowspan="12">배송정보</th>
				<th>수령자명</th>
				<td>
					<input type="text" name="wr_deli_nm" value="<?= $row['wr_deli_nm'] ?>" class="readonly" readonly />
				</td>
				<th>수령자 연락처</th>
				<td>
					<input type="text" name="wr_deli_tel" value="<?= $row['wr_deli_tel'] ?>" class="readonly" readonly />
				</td>
				<td colspan="7"></td>
			</tr>
			<tr>
				<th>도시명</th>
				<td colspan="3">
					<input type="text" name="wr_deli_city" value="<?= $row['wr_deli_city'] ?>" class="readonly" readonly />
				</td>
				<th>주명</th>
				<td>
					<input type="text" name="wr_deli_ju" value="<?= $row['wr_deli_ju'] ?>" class="readonly" readonly />
				</td>
				<th>나라명</th>
				<td>
					<input type="text" name="wr_deli_country" value="<?= $row['wr_deli_country'] ?>" class="readonly" readonly />
				</td>
				<td colspan="2"></td>
			</tr>
			<tr>
				<th>우편번호</th>
				<td>
					<input type="text" name="wr_deli_zip" value="<?= $row['wr_deli_zip'] ?>" class="readonly" readonly />
				</td>
				<th>주소</th>
				<td colspan="7">
					<input type="text" name="wr_deli_addr1" value="<?= $row['wr_deli_addr1'] ?>" style="width:48%" class="readonly" readonly>
					<input type="text" name="wr_deli_addr2" value="<?= $row['wr_deli_addr2'] ?>" style="width:48%" class="readonly" readonly>
				</td>
			</tr>
			<tr>
				<th>총 수량</th>
				<td><input type="text" name="wr_ea" value="<?= $row['wr_ea'] ?>" class="readonly" readonly></td>
				<th>박스수</th>
				<td><input type="text" name="wr_box" value="1" class="readonly" readonly></td>
				<th>단가(<?= $row['wr_currency'] ?>)</th>
				<td><input type="text" name="wr_danga" value="<?= $row['wr_danga'] ?>" class="readonly" readonly></td>
				<th>신고가격(<?= $row['wr_currency'] ?>)</th>
				<td><input type="text" name="wr_singo" value="<?= $row['wr_singo'] ?>" class="readonly" readonly></td>
				<td colspan="2"></td>
			</tr>
			<tr>
				<th>통화</th>
				<td><input type="text" name="wr_currency" value="<?php echo $row['wr_currency'] ?>" class="readonly" readonly></td>
				<th>환율</th>
				<td>
					<input type="text" name="wr_exchange_rate" value="<?= $row['wr_exchange_rate'] ?>" class="readonly" readonly />
				</td>
				<th>단가(KRW)</th>
				<td>
					<input type="text" id="wr_exchange_danga" value="<?= floor($row['wr_danga'] * $row['wr_exchange_rate']) ?>" class="readonly" readonly />
				</td>
				<th>신고가격(KRW)</th>
				<td>
					<input type="text" id="wr_exchange_singo" value="<?= floor($row['wr_singo'] * $row['wr_exchange_rate']) ?>" class="readonly" readonly />
				</td>
				<th>TAX</th>
				<td><input type="text" value="<?= $row['wr_tax'] ?>" readonly /></td>
			</tr>

			<tr>
				<th>수수료1(<?= $row['wr_currency'] ?>)</th>
				<td>
					<input type="text" name="wr_fee1" id="wr_fee1" value="<?= $row['wr_fee1'] ?>" class="readonly" readonly />
				</td>
				<th>수수료1(KRW)</th>
				<td>
					<input type="text" id="wr_fee1_exchange" value="<?= floor((float)$row['wr_fee1'] * (float)$row['wr_exchange_rate']) ?>" class="readonly" readonly />
				</td>
				<th>수수료2(<?= $row['wr_currency'] ?>)</th>
				<td>
					<input type="text" name="wr_fee2" id="wr_fee2" value="<?= $row['wr_fee2'] ?>" class="readonly" readonly />
				</td>
				<th>수수료2(KRW)</th>
				<td>
					<input type="text" id="wr_fee2_exchange" value="<?= floor($row['wr_fee2'] * $row['wr_exchange_rate']) ?>" class="readonly" readonly />
				</td>
				<th>Shipping price</th>
				<td><input type="text" value="<?= $row['wr_shipping_price'] ?>" readonly /></td>
			</tr>

			<tr>
				<th>개당무게</th>
				<td><input type="text" name="wr_weight1" value="<?php echo $row['wr_weight1'] ?>" class="readonly" readonly></td>
				<th>총 부피</th>
				<td><input type="text" name="" value="<?php echo $hap_weight3 ?>" class="readonly" readonly></td>
				<th>총 무게</th>
				<td><input type="text" name="wr_weight2" id="wr_weight2" value="<?= $row['wr_weight2'] ?>" class="readonly" readonly></td>
				<th>무게단위</th>
				<td><input type="text" name="" value="<?php echo $item['wr_11'] ?>" class="readonly" readonly></td>
				<td colspan="2">
				</td>
			</tr>
			<tr id="delivery_frm">
				<th>Service Type</th>
				<td><input type="text" name="wr_servicetype" value="<?php echo $row['wr_servicetype'] ?>" class="readonly" readonly></td>
				<th>packaging</th>
				<td><input type="text" name="wr_packaging" value="<?php echo $row['wr_packaging'] ?>" class="readonly" readonly></td>
				<th>제조국가</th>
				<td><input type="text" name="wr_make_country" value="<?php echo $row['wr_make_country'] ?>" class="readonly" readonly></td>
				<th>HS코드</th>
				<td><input type="text" name="wr_hscode" value="<?php echo $row['wr_hscode'] ?>" class="readonly" readonly></td>
				<th>배송통화</th>
				<td>
					<input type="text" id="wr_delivery_currency" value="<?= ($row['wr_delivery'] == "1021") ? "JPY" : "KRW" ?>" class="readonly" readonly />
				</td>
			</tr>
			<? if ($first) { ?>
				<tr>
					<th>배송사</th>
					<?
					$delivery_name = get_delivery_name($row['wr_delivery']);
					?>
					<td>
						<select name="wr_delivery" id="wr_delivery">`
							<option value="" data="0" currency="KRW">==배송사 선택==</option>
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

							$result1 = sql_fetch_all($sql);

							$result1_copy = [];
							foreach ($result1 as $item) {
								$result1_copy[$item['cust_code']] = $item;
							}

							$sql = "
                SELECT {$country} AS price, cust_code, weight_code, B.wr_percent as 'code_percent'
                FROM g5_shipping_price A
                         LEFT OUTER JOIN g5_delivery_company B ON B.wr_code = A.cust_code
                WHERE 1
                  and B.wr_use = '1'
                group by cust_code
                order by price asc
              ";
							$result2 = sql_fetch_all($sql);

							$merged = $result1_copy;
							foreach ($result2 as $k => $v) {
								if (!isset($merged[$v['cust_code']])) {
									$merged[] = $v;
								}
							}

							$wr_delivery_oil_percent = 0;
							$wr_delivery_oil = $row['wr_delivery_oil'];
							foreach ($merged as $i => $delivery2) {
								if ($delivery2['cust_code'] == $row['wr_delivery']) {
									$wr_delivery_oil_percent = $delivery2['code_percent'];
								}
							?>
								<option value="<?= $delivery2['cust_code'] ?>" data="<?= $delivery2['price'] ?>" currency="<?= ($delivery2['cust_code'] == "1021") ? "JPY" : "KRW" ?>" oil_percent="<?= $delivery2['code_percent'] ?>" <?= get_selected($row['wr_delivery'], $delivery2['cust_code']) ?>><?= $delivery[$delivery2['cust_code']] ?></option>
							<?
							} ?>
						</select>
						<button type="button" class="btn_calc" id="btn_calc" onclick="open_cal_popup('<?= $row['seq'] ?>');">배송비 계산</button>
					</td>
					<th>배송요금</th>
					<td>
						<input type="text" name="wr_delivery_fee" id="wr_delivery_fee" value="<?= $wr_delivery_fee ?>">
						<input type="hidden" name="wr_delivery_fee_original" id="wr_delivery_fee_original" value="<?= $wr_delivery_fee ?>">
					</td>
					<th>추가배송비</th>
					<td>
						<input type="text" name="wr_delivery_fee2" id="wr_delivery_fee2" value="<?= $row['wr_delivery_fee2'] ?>">
						<input type="hidden" name="wr_delivery_fee2_original" id="wr_delivery_fee2_original" value="<?= $row['wr_delivery_fee2'] ?>">
					</td>
					<th>유류할증료</th>
					<td>
						<input type="hidden" id="wr_delivery_oil_percent" value="<?= $wr_delivery_oil_percent ?>" />
						<input type="text" name="wr_delivery_oil" id="wr_delivery_oil" value="<?= $wr_delivery_oil ?>" class="readonly" readonly />
					</td>
					<th>배송 총금액</th>
					<td>
						<input type="text" name="wr_delivery_total" id="wr_delivery_total" value="" class="readonly" readonly />
					</td>
				</tr>
			<? } ?>
			<tr>
				<th>수출신고품명</th>
				<td><input type="text" name="wr_name2" value="<?php echo $row['wr_name2'] ?>" class="readonly" readonly></td>
				<th>수출국가</th>
				<td><input type="text" name="wr_country_code" value="<?php echo $row['wr_country_code'] ?>" class="readonly" readonly></td>
				<th>발주일자</th>
				<td><input type="date" name="wr_date2" value="<?php echo $row['wr_date2'] ?>" class="readonly" readonly></td>
				<th>입고일자</th>
				<td><input type="date" name="wr_date3" value="<?php echo $row['wr_date3'] ?>" class="readonly" readonly></td>
				<th>출고일자</th>
				<td><input type="date" name="wr_date4" value="<?php echo $row['wr_date4'] ?>" class="readonly" readonly></td>
			</tr>

			<tr>
				<th>수출트래킹NO</th>
				<td>
					<input type="text" name="wr_release_traking" value="<?php echo $row['wr_release_traking'] ?>" style="width:69%">
					<button type="button" onclick="release_traking_update()" class="btn_b01">수정</button>
				</td>
				<th>비고</th>
				<td colspan="5">
					<input type="text" name="wr_release_etc" value="<?php echo $row['wr_release_etc'] ?>" style="width:60%">
					<button type="button" onclick="etc_update()" class="btn_b01">수정</button>
				</td>
				<th>TBN</th>
				<td>
					<select name="tbn_code">
						<option value="">선택</option>
						<?php
						$query = "select * from g5_stock_box_order where wr_order_num = '{$row['wr_order_num']}'";
						$box = sql_fetch($query);

						$dateObj = new DateTime($row['wr_date4']);
						$date = $dateObj->format('Ymd');

						if ($row['wr_delivery'] == '1030') {
							$prefix = 'C';
						} else if ($row['wr_delivery'] == '1029') {
							$prefix = 'S';
						} else {
							$prefix = 'ETC';
						}
						for ($i = 1; $i < 21; $i++) {
							$value = "{$prefix}{$date}-{$i}";
							$selected = get_selected($box['box_code'], $value);
							echo "<option {$selected} value='{$prefix}{$date}-{$i}'>{$prefix}{$date}-{$i}</option>";
						}
						?>
					</select>
				</td>
			</tr>
		</table>
		<div class="win_btn btn_confirm" style="margin-top:20px;text-align:center">
			<? if ($first == 1) { ?>

			<? } ?>
			<input type="button" value="저장" class="btn_submit btn_b01 addbtn1" id="frm_submit">
			<? if ($row['wr_release_use'] == 0) { ?>
				<input type="button" value="출고완료 처리" class="btn_submit btn_b01 addbtn2" id="frm_submit" style="background:#2a56ba" data="<?php echo $row['seq'] ?>">
			<? } ?>
			<input type="button" value="상품 바로가기" class="btn_submit btn_b02" style="cursor:pointer;" onclick="<?= $item_link ?>">
		</div>
	</div>
<?php } ?>
<!-- 부피 및 무게 계산 팝업 -->
<div class="calc_popup" style="display:none;">
	<fieldset class="bo_sch">
		<h3>부피 및 무게 계산</h3>
		<div class="calc_div">
			<label for="stx" style="font-weight:bold">가로<strong class="sound_only"> 필수</strong></label>
			<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
				<input type="text" name="wr_hab_x" id="hab_x" value="<?= (int)$row['wr_hab_x'] ?>" placeholder="가로" class="frm_input number_fmt_list" style="background:#FFFFFF;" />mm
			</div>

			<label for="stx" style="font-weight:bold">세로<strong class="sound_only"> 필수</strong></label>
			<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
				<input type="text" name="wr_hab_y" id="hab_y" value="<?= (int)$row['wr_hab_y'] ?>" placeholder="세로" class="frm_input number_fmt_list" style="background:#FFFFFF;" />mm
			</div>

			<label for="stx" style="font-weight:bold">높이<strong class="sound_only"> 필수</strong></label>
			<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
				<input type="text" name="wr_hab_z" id="hab_z" value="<?= (int)$row['wr_hab_z'] ?>" placeholder="높이" class="frm_input number_fmt_list" style="background:#FFFFFF;" />mm
			</div>

			<label for="stx" style="font-weight:bold">부피무게1<strong class="sound_only"> 필수</strong></label>
			<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
				<input type="text" name="wr_weight_sum1" id="hab_weight1" value="<?= (float)$row['wr_weight_sum1'] ?>" class="frm_input number_fmt_list" style="background:#FFFFFF;" />kg
			</div>

			<label for="stx" style="font-weight:bold">부피무게2<strong class="sound_only"> 필수</strong></label>
			<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
				<input type="text" name="wr_weight_sum2" id="hab_weight2" value="<?= (float)$row['wr_weight_sum2'] ?>" class="frm_input number_fmt_list" style="background:#FFFFFF;" />kg
			</div>

			<label for="stx" style="font-weight:bold">총 무게<strong class="sound_only"> 필수</strong></label>
			<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
				<input type="text" name="wr_weight_sum3" id="hab_weight3" value="<?= (float)$row['wr_weight_sum3'] ?>" class="frm_input number_fmt_list" style="background:#FFFFFF;" />kg
			</div>

			<button type="button" value="계산하기" id="hab_cal_btn" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i>무게 자동 계산하기</button>
			<button type="button" value="계산하기" id="delivery_cal_btn" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i>배송비 계산하기</button>
			<button type="button" class="calc_popup_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
		</div>
	</fieldset>
	<div class="bo_sch_bg"></div>
</div>
<!--// 부피 및 무게 계산 팝업 -->

<script>
	$(document).ready(function() {
		fnCalcDeliveryTotal();
		$(".calc_popup_cls").on("click", function() {
			$(".calc_popup").hide();
		});
	});

	// 수출트래킹NO만 수정 (따로 수정버튼 없어서 추가) 01-11
	function release_traking_update() {
		let seq = $("input[name=seq]").val(),
			wr_release_traking = $("input[name=wr_release_traking]").val();

		//console.log(seq,wr_release_traking);
		$.post(g5_url + "/sales/ajax.traking_update.php", {
			seq: seq,
			wr_release_traking: wr_release_traking
		}, function(data) {
			if (data == 'y') {
				alert("수출트래킹NO가 수정이 완료되었습니다.");
			} else {
				alert("오류가 발생했습니다.");
			}
		});
	}

	// 비고 수정  01-17
	function etc_update() {
		let seq = $("input[name=seq]").val(),
			wr_release_etc = $("input[name=wr_release_etc]").val();

		$.post(g5_url + "/sales/ajax.etc_update.php", {
			seq: seq,
			wr_release_etc: wr_release_etc
		}, function(data) {
			if (data == 'y') {
				alert("비고가 수정이 완료되었습니다.");
			} else {
				alert("오류가 발생했습니다.");
			}
		});
	}

	// 배송비 등록
	function delivery_action(mode, val) {

		if (!mode) {
			alert("타입이 없습니다.");
			return false;
		}

		let code = code,
			code_val = code_val;

		const obj = {
			mode: mode,
			code: code,
			code_val: code_val,
			val: val,
		}

		$.post(g5_url + "/sales/ajax.delivery_action.php", obj, function(data) {
			console.log(data);
			if (data == 'y') {

			} else {

			}
		});
	}

	function open_cal_popup(seq) {
		window.open('./sales3_calc_popup.php?seq=' + seq, '배송비 최적 계산', 'width=700,height=850,scrollbars=yes,resizable=yes');
	}

	function open_box_popup(seq) {
		window.open('./sales3_box_popup.php?seq=' + seq, '기타이관', 'width=500,height=650,scrollbars=yes,resizable=yes');
	}
</script>