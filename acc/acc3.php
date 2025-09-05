<?php
include_once('./_common.php');

include_once(G5_THEME_PATH . '/head.php');

if (!$st_date) {
	$st_date = date("Y-m-d");
}
if (!$ed_date) {
	$ed_date = date("Y-m-d");
}

$search_accumulate = 'N';

$carryover = 0;
$accumulate_amount = 0;

# 환율 정보
$sql = "select rate, ex_eng from g5_excharge";
$result = sql_fetch_all($sql);

$ex_list = array_column($result, 'rate', 'ex_eng');

/*
# 이월 합산 금액 검색
if ($sc_domain) {
    # 이월 합산 금액 보정
    $sql = "SELECT sum(amount) as amount FROM g5_acc3_carryover_amount WHERE sc_domain = '{$sc_domain}' and base_date <= '{$st_date}'";
    $result = sql_fetch($sql);

    $carryover = $result['amount'];

    $search_accumulate = 'Y';

    if ($sc_domain === 'ACSUM') {
        $query = "
        select seq, wr_date4, wr_currency, wr_paymethod, wr_singo, wr_domain, wr_ori_order_num, 
        wr_tax, wr_shipping_price, fee1_status, fee2_status, tax_status
        from g5_sales3_list as sl
        left join g5_write_product wp on wp.wr_id = sl.wr_product_id
        left join g5_sales3_cal sc on sc.sales3_seq = sl.seq
        where wr_release_use = '1' and wr_date4 < '$st_date' and wr_date4 >= '2025-01-01' and wr_domain IN ('AC', 'ACF')
        order by wr_date4 desc, wr_domain, wr_order_num desc
        ";
    } elseif ($sc_domain === 'ACDSUM') {
        $query = "
        select seq, wr_date4, wr_currency, wr_paymethod, wr_singo, wr_domain,
        wr_ori_order_num, wr_tax, wr_shipping_price, fee1_status, fee2_status, tax_status
        from g5_sales3_list as sl
        left join g5_write_product wp on wp.wr_id = sl.wr_product_id
        left join g5_sales3_cal sc on sc.sales3_seq = sl.seq
        where wr_release_use = '1' and wr_date4 < '$st_date' and wr_date4 >= '2025-01-01' 
        and wr_domain IN ('AC-CAD', 'ACF-CAD_I', 'ACF-CAD_F', 'ACF-CAD')
        order by wr_date4 desc, wr_domain, wr_order_num desc
        ";
    } elseif ($sc_domain === 'Q10JPSUM') {
        $query = "
        select seq, wr_date4, wr_currency, wr_paymethod, wr_singo, wr_domain, wr_ori_order_num,
        wr_tax, wr_shipping_price, fee1_status, fee2_status, tax_status
        from g5_sales3_list as sl
        left join g5_write_product wp on wp.wr_id = sl.wr_product_id
        left join g5_sales3_cal sc on sc.sales3_seq = sl.seq
        where wr_release_use = '1' and wr_date4 < '$st_date' and wr_date4 >= '2025-01-01' and wr_domain IN ('qoo10-jp', 'qoo10jp-1')
        order by wr_date4 desc, wr_domain, wr_order_num desc
        ";
    } else {
        $query = "
        select seq, wr_date4, wr_currency, wr_paymethod, wr_singo, wr_domain, wr_ori_order_num,
        wr_tax, wr_shipping_price, fee1_status, fee2_status, tax_status
        from g5_sales3_list as sl
        left join g5_write_product wp on wp.wr_id = sl.wr_product_id
        left join g5_sales3_cal sc on sc.sales3_seq = sl.seq
        where wr_release_use = '1' and wr_date4 < '$st_date' and wr_date4 >= '2025-01-01' and wr_domain = '$sc_domain'
        order by wr_date4 desc, wr_domain, wr_order_num desc
        ";
    }

    $result = sql_query($query);

    while ($row = sql_fetch_array($result)) {
        $list0[$row['wr_domain']][$row['wr_date4']][$row['seq']] = $row;
    }

    foreach ($list0 as $k => $v) {
        foreach ($v as $k2 => $v2) {
            foreach ($v2 as $k3 => $item) {
                // 환율
                if ($item['wr_currency'] === 'JPY') {
                    $item['wr_exchange_rate'] = $item['wr_exchange_rate'] / 100;
                }

                $exchange_rate = $item['wr_exchange_rate'];
                // 수수료1 계산
                $basic_fee_rate = calculateBasicFee(
                    $item['wr_paymethod'],
                    $item['wr_singo'],
                    $exchange_rate,
                    false
                );

                // 수수료2 계산
                $sales_fee_rate = calculateSalesFee($$item['wr_domain'], false);

                // 세트의 마지막 여부 확인
                $isFinal = isFinalSet($k3, $item['wr_ori_order_num'], $list0);

                // TAX 계산
                $tax_price = calculateTax(
                    $item['wr_tax'],
                    $item['wr_shipping_price'],
                    $exchange_rate,
                    $isFinal
                );

                $remainingAmount = $item['wr_singo'] * $exchange_rate;

                if ($item['fee1_status']) {
                    $remainingAmount -= $basic_fee_rate;
                }

                if ($item['fee2_status']) {
                    $remainingAmount -= $sales_fee_rate;
                }

                if ($item['tax_status']) {
                    $remainingAmount -= $tax_price;
                }

                $remainingAmount = $remainingAmount < 0 ? 0 : $remainingAmount;

                $accumulate_amount += $remainingAmount;
            }
        }
    }
}
*/

if (!$search_by_date) {
	$search_by_date = 'N';
}

$sql_where = "";

if ($sc_domain === 'ACSUM') {
	$sql_where .= " and wr_domain IN ('AC', 'ACF') ";
} elseif ($sc_domain == 'ACDSUM') {
	$sql_where .= " and wr_domain IN ('AC-CAD', 'ACF-CAD_I', 'ACF-CAD_F', 'ACF-CAD') ";
} elseif ($sc_domain == 'Q10JPSUM') {
	$sql_where .= " and wr_domain IN ('qoo10-jp', 'qoo10jp-1') ";
} elseif ($sc_domain) {
	$sql_where .= " AND wr_domain = '" . $sc_domain . "' ";
}

if ($sc_cal_chk) {
	$sql_where .= " AND wr_cal_chk = '" . $sc_cal_chk . "' ";
}

if ($search_value) {
	$search_value = trim($search_value);
	$sql_where .= " AND (wr_order_num = '$search_value' or IF(wr_product_nm = '' OR wr_product_nm IS NULL, 
                    wr_subject, wr_product_nm) LIKE '%$search_value%')";
}

$list = [];

if ($ledger === 'all' || $ledger === 'account' || !isset($ledger)) {
	$query = "
    select wr_date4, seq, if(wr_exchange_rate > 0, wr_exchange_rate, e.rate) as wr_exchange_rate, 
    wr_domain, wr_order_num, wr_ori_order_num, IFNULL(wr_5, IFNULL(wr_4, IFNULL(wr_6, ''))) as code, 
    wr_1, IF(wr_product_nm = '' OR wr_product_nm IS NULL, wr_subject, wr_product_nm) as product_nm, wr_ea, 
    wr_danga, wr_singo, wr_cal_chk, wr_paymethod, wr_singo, wr_currency, wr_tax, wr_shipping_price, sc.*
    from g5_sales3_list as sl
    left join g5_write_product wp on wp.wr_id = sl.wr_product_id
	left join g5_sales3_cal sc on sc.sales3_seq = sl.seq
    left join g5_excharge e on e.ex_eng = sl.wr_currency
    where wr_release_use = '1' {$sql_where} and wr_date4 between '" . $st_date . "' and '" . $ed_date . "'
    order by wr_date4 desc, wr_domain, wr_order_num desc
    ";

	$result = sql_query($query);

	if (sql_num_rows($result) > 0) {
		while ($row = sql_fetch_array($result)) {
			$row['receipt'] = 'N';

			if ($search_by_date === 'Y') {
				$list[$row['receipt']][$row['wr_date4']][$row['wr_domain']][$row['seq']] = $row;
			} else {
				$list[$row['receipt']][$row['wr_domain']][$row['wr_date4']][$row['seq']] = $row;
			}
		}
	}
}

if ($ledger === 'all' || $ledger === 'receipt' || !isset($ledger)) {
	$sql_search = " ";

	if ($st_date && $ed_date) {
		$sql_search .= " and l.wr_datetime BETWEEN '{$st_date} 00:00:00' AND '{$ed_date} 23:59:59' ";
	}

	$sql_search .= " and l.wr_state = 2 ";

	if ($stx) {
		$sql_search .= " and l.wr_order_num LIKE '%$stx%' ";
	}

	if (isset($sc_domain) && isNotEmpty($sc_domain)) {
		// $sql_search .= " and s3.wr_domain = '$sc_domain' ";
		$sql_search .= $sql_where;
	}

	if (!$sst && !$sod) {
		$sst = "l.seq";
		$sod = "desc";
	}

	$sql_order = "order by $sst $sod";

	$query = "
    SELECT
        sl.wr_date4, sl.seq, sl.wr_domain, sl.wr_order_num, sl.wr_ori_order_num,
        wp.wr_1, sl.wr_ea, sl.wr_danga, sl.wr_singo, sl.wr_cal_chk,
        sl.wr_paymethod, sl.wr_currency, sl.wr_tax, sl.wr_shipping_price,
        IF(sl.wr_exchange_rate > 0, sl.wr_exchange_rate, e.rate) AS wr_exchange_rate,
        IFNULL(wp.wr_5, IFNULL(wp.wr_4, IFNULL(wp.wr_6, ''))) AS code,
        IF(sl.wr_product_nm = '' OR sl.wr_product_nm IS NULL, wp.wr_subject, sl.wr_product_nm) AS product_nm,
        sc.*
    FROM
        g5_sales3_list AS sl
    LEFT JOIN
        g5_write_product wp ON wp.wr_id = sl.wr_product_id
    LEFT JOIN
        g5_sales3_cal sc ON sc.sales3_seq = sl.seq
    LEFT JOIN
        g5_excharge e ON e.ex_eng = sl.wr_currency
    WHERE
        sl.wr_release_use = '1'
        AND sl.wr_order_num IN (
        SELECT 
            l.wr_order_num
            FROM g5_return_list as l
            LEFT JOIN g5_sales3_list as s3 ON s3.seq = l.sales3_id
            WHERE (1) {$sql_search} {$sql_order} )           
    ORDER BY
        sl.wr_date4 DESC, sl.wr_domain, sl.wr_order_num DESC;
    ";

	$result = sql_query($query);

	if (sql_num_rows($result) > 0) {
		while ($row = sql_fetch_array($result)) {
			$row['receipt'] = 'Y';
			$row['wr_ea'] = -$row['wr_ea'];
			$row['wr_singo'] = -$row['wr_singo'];

			if ($search_by_date === 'Y') {
				$list[$row['receipt']][$row['wr_date4']][$row['wr_domain']][$row['seq']] = $row;
			} else {
				$list[$row['receipt']][$row['wr_domain']][$row['wr_date4']][$row['seq']] = $row;
			}
		}
	}
}

// 수수료 계산 함수
function calculateBasicFee($paymentMethod, $wrSingo, $exchangeRate, $isDuplicate)
{
	// 중복 데이터라면 수수료 0 반환
	if ($isDuplicate) {
		return 0;
	}

	// 결제 수단에 따른 기본 수수료 계산
	switch ($paymentMethod) {
		case "Shopify Payments":
		case "Shop Cash":
		case "Shop Cash + Shopify Payments":
			$basicFee = $wrSingo * 2.4 / 100 + 0.3;
			break;

		case "PayPal Express Checkout":
		case "Shopify Payments + PayPal Express Checkout":
			$basicFee = $wrSingo * 3.49 / 100 + 0.49;
			break;

		default:
			$basicFee = 0; // 기타 결제 수단의 경우
			break;
	}

	// 환율 적용
	return $basicFee * $exchangeRate;
}

// 중복 여부 확인 함수
function isDuplicateOrder($currentCount, $totalCount)
{
	return $currentCount < $totalCount;
}

// 수수료2 계산 함수 (예: 도도스킨 등 추가 계산 방식 포함 가능)
function calculateSalesFee($domain, $isDuplicate)
{
	if ($isDuplicate) {
		return 0;
	}

	if ($domain === "dodoskin") {
		return 0; // 도도스킨의 경우 현재 수수료2는 0으로 처리
	}

	// 기타 계산 방식 (필요 시 추가)
	return 0;
}

// 환율 조회 함수
function getExchangeRate($date, $currency, $defaultRateTable = "g5_excharge", $logRateTable = "g5_excharge_log")
{
	$today = date("Y-m-d");
	$exchangeRate = 1; // 기본 환율 (없을 경우 1로 설정)

	// 오늘 날짜의 환율 조회
	if ($date === $today) {
		$sql = "SELECT * FROM $defaultRateTable";
	} else {
		$month = date("Ym", strtotime($date));
		$sql = "SELECT * FROM $logRateTable WHERE ex_date='$date'";
	}

	$result = sql_query($sql);
	if (sql_num_rows($result) > 0) {
		while ($row = sql_fetch_array($result)) {
			if ($row['ex_eng'] === $currency) {
				$rate = str_replace(",", "", $row['rate']);
				if ($currency === "JPY") {
					$rate *= 0.01;
				}
				$exchangeRate = $rate;
				break;
			}
		}
	} else {
		// 기본 테이블에서 조회
		$sql = "SELECT * FROM $defaultRateTable";
		$result = sql_query($sql);
		while ($row = sql_fetch_array($result)) {
			if ($row['ex_eng'] === $currency) {
				$rate = str_replace(",", "", $row['rate']);
				if ($currency === "JPY") {
					$rate *= 0.01;
				}
				$exchangeRate = $rate;
				break;
			}
		}
	}

	return $exchangeRate;
}

// TAX 계산 함수
function calculateTax($taxAmount, $shippingPrice, $exchangeRate, $isFinalSet = true)
{
	// 세트의 마지막 데이터만 TAX 계산
	if (!$isFinalSet) {
		return 0;
	}

	// TAX 계산: (세금 + 배송비) × 환율
	$tax = ((float) $taxAmount + (float) $shippingPrice) * (float) $exchangeRate;

	return floor($tax); // 소수점 절사
}

// 세트의 마지막 여부 확인 함수
function isFinalSet($currentSeq, $orderNum, $list)
{
	$itemSequences = []; // 해당 주문번호의 모든 seq를 저장할 배열

	// 세트 내 해당 주문번호의 모든 seq 수집
	foreach ($list as $domain => $dateGroup) {
		foreach ($dateGroup as $date => $items) {
			foreach ($items as $seq => $item) {
				if ($item['wr_ori_order_num'] === $orderNum) {
					$itemSequences[] = $seq;
				}
			}
		}
	}

	// seq 정렬 (숫자 순서 보장)
	sort($itemSequences);

	// 현재 seq가 해당 주문번호의 마지막 seq인지 확인
	return $currentSeq === end($itemSequences);
}

?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229" />

<style>
	.select2-container--default .select2-selection--single {
		height: 40px;
		border: 1px solid #d9dee9;
		background: #f1f3f6
	}

	.select2-container--default .select2-selection--single .select2-selection__rendered {
		line-height: 38px
	}

	.select2-container--default .select2-selection--single .select2-selection__arrow b {
		margin-top: 4px
	}

	.cnt_left {
		padding: 5px 10px;
		border-right: 1px solid #ddd;
		word-break: normal;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
	}

	.list_03 li {
		padding: 0
	}

	.list_03 li .cnt_left {
		line-height: 1.5em
	}

	.modify {
		cursor: pointer
	}

	.tbl_frm01 th {
		background: #6f809a;
		color: #fff;
		border: 1px solid #60718b;
		font-weight: normal;
		text-align: center;
		padding: 8px 5px;
		font-size: 0.92em
	}

	.tbl_frm01 td {
		border-bottom: 1px solid #ddd;
	}

	.tbl_frm01 td input {
		border: 1px solid #ddd;
		padding: 3px;
		width: 100%
	}

	.tbl_frm01 input.readonly {
		background: #f2f2f2
	}

	.local_ov01 {
		position: relative;
		margin: 10px 0;
	}

	.local_ov01 .ov_a {
		display: inline-block;
		line-height: 30px;
		height: 30px;
		font-size: 0.92em;
		background: #ff4081;
		color: #fff;
		vertical-align: top;
		border-radius: 5px;
		padding: 0 7px
	}

	.local_ov01 .ov_a:hover {
		background: #ff1464
	}

	.btn_ov01 {
		display: inline-block;
		line-height: 30px;
		height: 30px;
		font-size: 0.92em;
		vertical-align: top
	}

	.btn_ov01:after {
		display: block;
		visibility: hidden;
		clear: both;
		content: ""
	}

	.btn_ov01 .ov_txt {
		float: left;
		background: #9eacc6;
		color: #fff;
		border-radius: 5px 0 0 5px;
		padding: 0 5px
	}

	.btn_ov01 .ov_num {
		float: left;
		background: #ededed;
		color: #666;
		border-radius: 0 5px 5px 0;
		padding: 0 5px
	}

	a.btn_ov02,
	a.ov_listall {
		display: inline-block;
		line-height: 30px;
		height: 30px;
		font-size: 0.92em;
		background: #565e8c;
		color: #fff;
		vertical-align: top;
		border-radius: 5px;
		padding: 0 7px
	}

	a.btn_ov02:hover,
	a.ov_listall:hover {
		background: #3f51b5
	}

	.tbl_head01 thead th,
	.tbl_head01 tbody td {
		border-right: 1px solid #e9e9e9 !important
	}

	.tbl_head01 thead th {
		background: #f2f2f2;
		font-weight: bold
	}

	.tbl_head01 tbody td {
		padding: 10px 5px;
		color: #222
	}

	.tbl_head01 tbody td.num {
		text-align: right
	}

	.tbl_head01 tbody td.date {
		text-align: center
	}

	.odd_tr td {
		background: #eff3f9;
	}

	.even_tr td {
		background: #ffffff;
	}

	.tbl_head01 tbody .text_left {
		text-align: left;
	}

	.tbl_head01 tbody .text_center {
		text-align: center;
	}

	.tbl_head01 tbody .text_right {
		text-align: right;
	}

	.modal_view {
		display: none;
		width: 100%;
		height: 100%;
		position: fixed;
		top: 0;
		left: 0;
		z-index: 999;
	}

	.modal_detail {
		position: absolute;
		top: 30%;
		left: 23%;
		background: #fff;
		text-align: left;
		width: 1400px;
		height: 700px;
		margin-left: -165px;
		margin-top: -180px;
		overflow-y: auto;
		border-radius: 5px;
		-webkit-box-shadow: 1px 1px 18px rgba(0, 0, 0, 0.2);
		-moz-box-shadow: 1px 1px 18px rgba(0, 0, 0, 0.2);
		box-shadow: 1px 1px 18px rgba(0, 0, 0, 0.2);
		border: 1px solid #dde7e9;
		background: #fff;
		border-radius: 3px;
	}

	.modal_detail .modal_cls {
		position: absolute;
		right: 0;
		top: 0;
		color: #b5b8bb;
		border: 0;
		padding: 12px 15px;
		font-size: 16px;
		background: #fff;
	}


	.tbl_wrap {
		overflow-y: auto;
		max-height: 600px;
		margin-top: -2px;
	}

	.tbl_wrap table {
		border-collapse: collapse;
		width: 100%;
	}

	.tbl_wrap thead th {
		position: sticky;
		top: 0;
		z-index: 2;
	}

	.tbl_wrap tr {
		height: 40px;
	}

	.tbl_wrap tbody {
		overflow-y: scroll;
		max-height: 580px;

	}
</style>

<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">매출처 원장</h2>
		<form name="fboardlist" id="fboardlist" action="<?php echo G5_URL; ?>/acc/acc3_list_update.php"
			onsubmit="return acc3_frm_submit(this);" method="post">
			<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
			<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
			<input type="hidden" name="stx" value="<?php echo $stx2 ?>">
			<input type="hidden" name="spt" value="<?php echo $spt ?>">
			<input type="hidden" name="sst" value="<?php echo $sst ?>">
			<input type="hidden" name="sod" value="<?php echo $sod ?>">
			<input type="hidden" name="page" value="<?php echo $page ?>">
			<input type="hidden" name="qstr" value="<?= $qstr ?>">
			<input type="hidden" name="sw" value="">

			<?php if ($is_category) { ?>
				<nav id="bo_cate">
					<h2><?php echo ($board['bo_mobile_subject'] ? $board['bo_mobile_subject'] : $board['bo_subject']) ?> 카테고리</h2>
					<ul id="bo_cate_ul">
						<?php echo $category_option ?>
					</ul>
				</nav>
			<?php } ?>

			<div id="bo_li_top_op">
				<div class="bo_list_total">
					<div class="local_ov01 local_ov">
						<span class="btn_ov01">
							&nbsp;
						</span>
					</div>
				</div>
				<ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?>">
					<?php if ($rss_href) { ?>
						<li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
					<li><button type="button" class="btn_b01 btn_bo_sch">
							<i class="fa fa-search" aria-hidden="true"></i>검색</button>
					</li>
					<li>
						<button type="submit" name="btn_submit" value="선택정산완료" class="btn_b02 " style="background:#8e74ef;color:white;">
							<i class="fa fa-file-pdf-o"></i> 선택정산완료
						</button>
					</li>
					<li>
						<button type="submit" name="btn_submit" value="선택정산취소" class="btn_b02 ">
							<i class="fa fa-file-pdf-o"></i>
							선택정산취소
						</button>
					</li>
					<?php if ($search_by_date !== 'Y') { ?>
						<li>
							<button type="button" class="btn_b01 " onclick="excel_download();" style="background:#325422;"><i
									class="fa fa-file-pdf-o tw-mr-1"></i>엑셀출력
							</button>
						</li>
					<?php } ?>
					<?php if ($sc_domain && !in_array($sc_domain, ['ACSUM', 'ACDSUM', 'Q10JPSUM'])) { ?>
						<li>
							<button type="button" class="btn_b02 btn_bo_sch2"><i class="fa-solid fa-pen-to-square"></i> 매입처
								이월 금액 보정
							</button>
						</li>
					<?php } ?>
				</ul>
			</div>
			<h2 style="padding-bottom:10px; font-size:20px; text-align:center">매출처 원장</h2>
			<div class="tbl_head01 tbl_wrap" style="overflow-x: auto;">
				<?php if ($search_by_date === 'N') { ?>
					<table style="width: 100%; min-width: 1400px;">
						<thead style="position:sticky;top:0;">
							<tr>
								<th><input type="checkbox" id="ALLCHK" /></th>
								<th>매출처</th>
								<th style="width: 90px;">일자</th>
								<th>주문번호</th>
								<th style="width:150px">대표코드</th>
								<th style="width:150px">SKU</th>
								<th style="width:200px">상품명</th>
								<th style="width:50px">수량</th>
								<th style="width:80px">단가($)</th>
								<th style="width:80px">신고가격($)</th>
								<th style="width:70px">수수료1</th>
								<th style="width:70px">수수료2</th>
								<th style="width:80px">TAX</th>
								<th style="width:100px">잔액</th>
								<th>정산 유무</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if (count($list) > 0) {
								$total_ea = 0;
								$total_danga = 0;
								$total_singo = 0;
								$total_fee1 = 0;
								$total_fee2 = 0;
								$total_tax = 0;
								$total_remainingAmount = 0;
								$tr_index = 0;

								/*
								foreach ($list as $key => $val) {
									foreach ($val as $k => $v) {
										foreach ($v as $k2 => $v2) {
											foreach ($v2 as $k3 => $item) {
												$total_ea += $item['wr_ea'];
												$total_danga += (float)$item['wr_danga'];
												$total_singo += (float)$item['wr_singo'];

												// 환율
												if ($item['wr_currency'] === 'JPY') {
													$item['wr_exchange_rate'] = $item['wr_exchange_rate'] / 100;
												}
												$exchange_rate = $item['wr_exchange_rate'];
												// 수수료1 계산
												$basic_fee_rate = calculateBasicFee(
													$item['wr_paymethod'],
													$item['wr_singo'],
													$exchange_rate,
													false
												);

												// 수수료2 계산
												$sales_fee_rate = calculateSalesFee($$item['wr_domain'], false);

												// 세트의 마지막 여부 확인
												$isFinal = isFinalSet($k3, $item['wr_ori_order_num'], $list);

												// TAX 계산
												$tax_price = calculateTax(
													$item['wr_tax'],
													$item['wr_shipping_price'],
													$exchange_rate,
													$isFinal
												);

												$remainingAmount = $item['wr_singo'] * $exchange_rate;

												if ($item['fee1_status']) {
													$remainingAmount -= $basic_fee_rate;
												}

												if ($item['fee2_status']) {
													$remainingAmount -= $sales_fee_rate;
												}

												if ($item['tax_status']) {
													$remainingAmount -= $tax_price;
												}

												//$remainingAmount = max(0, round($remainingAmount, 2));

												// 합계 계산
												// if ($remainingAmount < 0) {
												//     echo 'style="color: red;"';
												// }

												// 합계 누적
												$total_fee1 += round($basic_fee_rate, 2);
												$total_fee2 += $sales_fee_rate;
												$total_tax += $tax_price;
												$total_remainingAmount += $remainingAmount;
											}
										}
									}
								}
								*/

								foreach ($list as $receipt_key => $val_by_receipt) { // $receipt_key는 'N', 'Y'
									$receipt = $receipt_key;
									$receipt_row_start = true; // 영수증별 첫 번째 행 플래그

									foreach ($val_by_receipt as $domain_key => $val_by_domain) { // $domain_key는 'AC', 'BD' 등
										$domain = $domain_key;
										$domain_row_start = true; // 도메인별 첫 번째 행 플래그

										// 1단계: 현재 도메인($val_by_domain) 내의 모든 날짜로부터 최종 아이템들을 하나의 배열로 수집
										$all_items_for_this_domain = [];
										foreach ($val_by_domain as $date_key => $items_for_date) {
											foreach ($items_for_date as $item) {
												// 아이템에 원래 날짜 정보가 없다면 여기서 추가할 수 있습니다.
												// $item['original_date'] = $date_key; // 이렇게 추가하면 $item에 날짜 정보가 포함됩니다.
												$item['original_date'] = $date_key;
												$all_items_for_this_domain[] = $item;
											}
										}

										// 2단계: 수집된 전체 아이템에 대해 상위 10개, 하위 10개 로직 적용
										$current_total_items_count = count($all_items_for_this_domain);
										$display_limit = 10; // 표시할 상위/하위 아이템 개수

										$items_to_display = [];
										$show_ellipsis = false;

										if ($current_total_items_count > ($display_limit * 2)) {
											// 20개 초과 시: 상위 10개와 하위 10개만 추출
											for ($i = 0; $i < $display_limit; $i++) {
												$items_to_display[] = $all_items_for_this_domain[$i];
											}
											$show_ellipsis = true; // "..." 표시가 필요함을 나타냄
											for ($i = $current_total_items_count - $display_limit; $i < $current_total_items_count; $i++) {
												$items_to_display[] = $all_items_for_this_domain[$i];
											}
										} else {
											// 20개 이하일 경우 모든 데이터 출력
											$items_to_display = $all_items_for_this_domain;
										}

										// 3단계: 화면 출력을 위한 rowspan 계산 (필터링된 아이템 수 + 생략 줄)
										$rowspan_for_domain = count($items_to_display);
										if ($show_ellipsis) {
											$rowspan_for_domain += 1; // 생략 줄 때문에 1줄 추가
										}

										// 4단계: 필터링된 아이템들을 순회하며 출력
										$tr_class = $tr_index++ % 2 === 0 ? "even_tr" : "odd_tr";
										foreach ($items_to_display as $item_index => $item) {
											$date = $item['original_date'];
											// "..." 표시를 위한 가상의 아이템 처리
											if ($show_ellipsis && $item_index === $display_limit) { // 상위 10개 출력 후 생략 줄 삽입
												echo "<tr class='{$tr_class}'>";
												// 영수증 셀 (필요하다면)
												if ($receipt_row_start) {
													// $receipt_rowspan은 이중 루프 밖에서 계산되어야 함.
													// 복잡해지므로 여기서는 각 행에 출력한다고 가정하거나,
													// 전체 $list를 미리 순회하여 각 receipt_key, domain_key 별 총 rowspan을 계산해야 함.
													// 여기서는 간단히 처리하고, 실제 전체 rowspan은 별도 계산이 필요합니다.
													// 예시를 위해 임시로 1로 설정.
													// 실제 구현에서는 $list 전체를 미리 순회하여 각 $receipt_key와 $domain_key에 해당하는
													// 최종 출력될 행의 총 개수를 계산하여 $receipt_total_rowspan, $domain_total_rowspan 변수에 저장해야 합니다.
													// echo "<td rowspan='{$rowspan_for_domain}'>{$receipt}</td>";
													$receipt_row_start = false;
												}
												// 도메인 셀
												if ($domain_row_start) {
													// echo "<td rowspan='{$rowspan_for_domain}'>{$domain}</td>";
													$domain_row_start = false;
												}
												echo "<td colspan='15' style='text-align:center;'>... (생략된 항목) ...</td>"; // 나머지 컬럼 합치기
												echo "</tr>\n";
												// 생략 줄이 출력된 후에는 tr_class를 다시 계산하거나, 다음 행에 적용될 수 있도록 처리
												$tr_class = $tr_index++ % 2 === 0 ? "even_tr" : "odd_tr";
											}

											// 실제 아이템 출력
											// echo "<tr class='{$tr_class}'>";
											// if ($receipt_row_start) {
											// 	echo "<td rowspan='{$rowspan_for_domain}'>{$receipt}</td>";
											// 	$receipt_row_start = false;
											// }
											// if ($domain_row_start) {
											// 	echo "<td rowspan='{$rowspan_for_domain}'>{$domain}</td>";
											// 	$domain_row_start = false;
											// }

											// $total_ea += $item['wr_ea'];
											// $total_danga += (float)$item['wr_danga'];
											// $total_singo += (float)$item['wr_singo'];

											// 환율
											if ($item['wr_currency'] === 'JPY') {
												$item['wr_exchange_rate'] = $item['wr_exchange_rate'] / 100;
											}
											$exchange_rate = $item['wr_exchange_rate'];
											// 수수료1 계산
											$basic_fee_rate = calculateBasicFee(
												$item['wr_paymethod'],
												$item['wr_singo'],
												$exchange_rate,
												false
											);

											// 수수료2 계산
											$sales_fee_rate = calculateSalesFee($$item['wr_domain'], false);

											// 세트의 마지막 여부 확인
											$isFinal = isFinalSet($k3, $item['wr_ori_order_num'], $list);

											// TAX 계산
											$tax_price = calculateTax(
												$item['wr_tax'],
												$item['wr_shipping_price'],
												$exchange_rate,
												$isFinal
											);

											$remainingAmount = $item['wr_singo'] * $exchange_rate;

											if ($item['fee1_status']) {
												$remainingAmount -= $basic_fee_rate;
											}

											if ($item['fee2_status']) {
												$remainingAmount -= $sales_fee_rate;
											}

											if ($item['tax_status']) {
												$remainingAmount -= $tax_price;
											}

											//$remainingAmount = max(0, round($remainingAmount, 2));

											// 합계 계산
											// if ($remainingAmount < 0) {
											//     echo 'style="color: red;"';
											// }

											// // 합계 누적
											// $total_fee1 += round($basic_fee_rate, 2);
											// $total_fee2 += $sales_fee_rate;
											// $total_tax += $tax_price;
											// $total_remainingAmount += $remainingAmount;
							?>
											<tr class="<?= $tr ?>">
												<td align="center">
													<input type="checkbox" name="seq_arr[]" value="<?= $item['seq'] ?>" />
												</td>
												<td title="<?= $domain ?>"><?= $domain ?></td>
												<td class="text_center" title="<?= $date ?>"><?= $date ?></td>
												<td><?= $item['wr_order_num'] ?></td>
												<td><?= $item['code'] ?></td>
												<td><?= $item['wr_1'] ?></td>
												<td><?= $item['product_nm'] ?></td>
												<td <?php if ($item['wr_ea'] < 0) {
															echo 'style="color: red;"';
														} ?>>
													<?= number_format((int)$item['wr_ea']) ?>
												</td>
												<td><?= number_format((float)$item['wr_danga'], 2) ?></td>
												<td <?php if ($item['wr_singo'] < 0) {
															echo 'style="color: red;"';
														} ?>>
													<?= number_format((float)$item['wr_singo'], 2) ?>
												</td>
												<td>
													<label>
														<input type="checkbox" name="sales3_cal_fee1" class="sales3_cal" value="<?= $basic_fee_rate ?>"
															data-seq="<?= $item['seq'] ?>" <?= $item['fee1_status'] ? "checked" : "" ?> />
													</label>
													<?= number_format($basic_fee_rate) ?>
												</td>
												<td>
													<label>
														<input type="checkbox" name="sales3_cal_fee2" class="sales3_cal" value="<?= $sales_fee_rate ?>"
															data-seq="<?= $item['seq'] ?>" <?= $item['fee2_status'] ? "checked" : "" ?> />
													</label>
													<?= number_format($sales_fee_rate) ?>
												</td>
												<td>
													<label>
														<input type="checkbox" name="sales3_cal_tax" class="sales3_cal" value="<?= $tax_price ?>"
															data-seq="<?= $item['seq'] ?>" <?= $item['tax_status'] ? "checked" : "" ?> />
													</label>
													<?= number_format($tax_price) ?>
												</td>
												<td>
													₩<span class="remaining-amount"
														data-remaining="<?= $remainingAmount ?>"
														<?php if ($remainingAmount < 0) {
															echo 'style="color: red;"';
														} ?>>
														<?= number_format((float)$remainingAmount) ?>
													</span>
												</td>
												<td align="center">
													<label><input type="checkbox" name="wr_cal_chk" value="Y" data="<?= $item['seq'] ?>"
															<?= ($item['wr_cal_chk'] == "Y") ? "checked" : "" ?> />
														<?= $item['wr_cal_chk'] === "Y" ? "정산완료" : "미정산" ?>
													</label>
												</td>
											</tr>
								<?php
										}
									}
								}
								?>
								<tr>
									<th colspan="7">합계</th>
									<td style="text-align: left !important;"><?= number_format($total_ea) ?></td>
									<td style="text-align: left !important;"><?= number_format($total_danga, 2) ?></td>
									<td style="text-align: left !important;"><?= number_format($total_singo, 2) ?></td>
									<td style="text-align: left !important;"><?= number_format($total_fee1) ?></td>
									<td style="text-align: left !important;"><?= number_format($total_fee2) ?></td>
									<td style="text-align: left !important;"><?= number_format($total_tax) ?></td>
									<td class="total-remaining-amount" style="text-align: left !important;">
										<?= number_format($total_remainingAmount) ?></td>
									<td></td>
								</tr>
							<?php } ?>
							<?php if ($search_accumulate !== 'Y') { ?>
								<?php if (!$sc_domain) { ?>
									<tr>
										<td colspan='11' style='text-align:center;'>도메인을 선택해주세요.</td>
									</tr>
								<?php } else { ?>
									<tr>
										<td colspan='11' style='text-align:center;'>검색된 목록이 없습니다.</td>
									</tr>
								<?php } ?>
							<?php } ?>
							<?php if ($search_accumulate === 'Y') { ?>
								<tr>
									<td style="text-align: center; background:rgb(232, 239, 243);" colspan="12"><strong>이월 합산 금액</strong></td>
									<td colspan="3" style="background:rgb(232, 239, 243);">₩<?= number_format($accumulate_amount + $carryover) ?></td>
								</tr>
								<?php if (!in_array($sc_domain, ['ACSUM', 'ACDSUM', 'Q10JPSUM'])) { ?>
									<tr>
										<td style="text-align: center; background:rgb(232, 239, 243);" colspan="12"><strong>이월 보정 금액</strong></td>
										<td colspan="3" style="background:rgb(232, 239, 243);">₩<?= number_format($carryover) ?></td>
									</tr>
								<?php } ?>
							<?php } ?>
						</tbody>
					</table>
					<!-- 일자별 합계 -->
				<?php } else { ?>
					<table>
						<thead style="position:sticky;top:0;">
							<tr>
								<th style="width: 90px;">일자</th>
								<th style="width: 450px;">매출처</th>
								<th>수량</th>
								<th>총 단가($)</th>
								<th>총 신고가격($)</th>
								<th>총 수수료1</th>
								<th>총 수수료2</th>
								<th>총 TAX</th>
								<th>총 잔액</th>
							</tr>
						</thead>
						<tbody>
							<?php if (count($list) > 0) {
								$total_ea = 0;
								$total_danga = 0;
								$total_singo = 0;
								$total_fee1 = 0;
								$total_fee2 = 0;
								$total_tax = 0;
								$total_remainingAmount = 0;
								$tr_index = 0;

								$quantityInfo = [];
							?>
								<?php foreach ($list as $key => $val) {
									$receipt = $key;

									foreach ($val as $k => $v) {
										$date = $k;
										$tr = $tr_index++ % 2 === 0 ? "even_tr" : "odd_tr";

										foreach ($v as $k2 => $v2) {
											$domain = $k2;
											$rowspan = count($v2);
											$row1 = true;
											$rowspan2 = count($v2);
											$row2 = true;

											foreach ($v2 as $k3 => $item) {
												$quantityInfo[$domain][$date]['wr_ea'] += $item['wr_ea'];
												$quantityInfo[$domain][$date]['wr_danga'] += $item['wr_danga'];
												$quantityInfo[$domain][$date]['wr_singo'] += $item['wr_singo'];

												$total_ea += $item['wr_ea'];
												$total_danga += $item['wr_danga'];;
												$total_singo += $item['wr_singo'];

												// 환율
												if ($item['wr_currency'] === 'JPY') {
													$item['wr_exchange_rate'] = $item['wr_exchange_rate'] / 100;
												}
												$exchange_rate = $item['wr_exchange_rate'];

												// 수수료1 계산
												$basic_fee_rate = calculateBasicFee(
													$item['wr_paymethod'],
													$item['wr_singo'],
													$exchange_rate,
													false
												);

												// 수수료2 계산
												$sales_fee_rate = calculateSalesFee($$item['wr_domain'], false);

												// 세트의 마지막 여부 확인
												$isFinal = isFinalSet($k3, $item['wr_ori_order_num'], $list);

												// TAX 계산
												$tax_price = calculateTax(
													$item['wr_tax'],
													$item['wr_shipping_price'],
													$exchange_rate,
													$isFinal
												);

												$remainingAmount = $item['wr_singo'] * $exchange_rate;

												if ($item['fee1_status']) {
													$remainingAmount -= $basic_fee_rate;
												}

												if ($item['fee2_status']) {
													$remainingAmount -= $sales_fee_rate;
												}

												if ($item['tax_status'] && $remainingAmount > 0) {
													$remainingAmount -= $tax_price;
												}

												$remainingAmount = $remainingAmount < 0 ? 0 : $remainingAmount;

												$quantityInfo[$domain][$date]['basic_fee_rate'] += $basic_fee_rate;
												$quantityInfo[$domain][$date]['sales_fee_rate'] += $sales_fee_rate;
												$quantityInfo[$domain][$date]['tax_price'] += $tax_price;
												$quantityInfo[$domain][$date]['remaining_amount'] += $remainingAmount;

												$total_fee1 += $basic_fee_rate;
												$total_fee2 += $sales_fee_rate;
												$total_tax += $tax_price;
												$total_remainingAmount += $remainingAmount;
											} ?>
											<tr class="<?= $tr ?>">
												<td class="text_center" title="<?= $date ?>"><?= $date ?></td>
												<td title="<?= $domain ?>"><?= $domain ?></td>
												<td><?= number_format($quantityInfo[$domain][$date]['wr_ea']) ?></td>
												<td><?= number_format($quantityInfo[$domain][$date]['wr_danga'], 2) ?></td>
												<td><?= number_format($quantityInfo[$domain][$date]['wr_singo'], 2) ?></td>
												<td><?= number_format($quantityInfo[$domain][$date]['basic_fee_rate'], 2) ?></td>
												<td><?= number_format($quantityInfo[$domain][$date]['sales_fee_rate'], 2) ?></td>
												<td><?= number_format($quantityInfo[$domain][$date]['tax_price'], 2) ?></td>
												<td><?= number_format($quantityInfo[$domain][$date]['remaining_amount'], 2) ?></td>
											</tr>
								<?php }
									}
								} ?>
								<tr>
									<th colspan=" 2">합계</th>
									<td style="text-align: left !important;"><?= number_format($total_ea) ?></td>
									<td style="text-align: left !important;"><?= number_format($total_danga, 2) ?></td>
									<td style="text-align: left !important;"><?= number_format($total_singo, 2) ?></td>
									<td style="text-align: left !important;"><?= number_format($total_fee1, 2) ?></td>
									<td style="text-align: left !important;"><?= number_format($total_fee2, 2) ?></td>
									<td style="text-align: left !important;"><?= number_format($total_tax) ?></td>
									<td style="text-align: left !important;"><?= number_format($total_remainingAmount) ?></td>
								</tr>

							<?php } else { ?>
								<?php if (!$sc_domain) { ?>
									<tr>
										<td colspan='11' style='text-align:center;'>도메인을 선택해주세요.</td>
									</tr>
								<?php } else { ?>
									<tr>
										<td colspan='11' style='text-align:center;'>검색된 목록이 없습니다.</td>
									</tr>
							<?php }
							} ?>
						</tbody>
					</table>

				<?php } ?>
			</div>
		</form>
	</div>
</div>

<!-- 개별 정산 팝업-->
<div class="modal_view">
	<div class="modal_detail" id="modal_view_calc"></div>
	<div class="bo_sch_bg"></div>
</div>
<!--// 주문건 확인 팝업-->

<div class="bo_sch_wrap">
	<fieldset class="bo_sch" style="padding:10px">
		<h3>검색</h3>
		<form name="fsearch" method="get">
			<select name="sc_domain" class="search_sel" style="width:100%;margin-bottom:15px">
				<option value="">전체 매출처</option>
				<option value="ACSUM" <?= get_selected("ACSUM", $sc_domain) ?>>AC + ACF</option>
				<option value="ACDSUM" <?= get_selected("ACDSUM", $sc_domain) ?>>AC-CAD + ACF-CAD</option>
				<option value="Q10JPSUM" <?= get_selected("Q10JPSUM", $sc_domain) ?>>QOO10jp + QOO10JP-1</option>
				<?php
				$code_list = get_code_list('4');
				foreach ($code_list as $key => $value) {
					echo "<option value=\"{$value['code_value']}\" " . get_selected($value['code_value'], $sc_domain) . ">{$value['code_name']}</option>";
				}
				?>

			</select>

			<select name="sc_cal_chk" style="margin-bottom:15px">
				<option value="">전체 정산내역</option>
				<option value="Y" <?php echo get_selected($_GET['sc_cal_chk'], 'Y') ?>>정산완료</option>
				<option value="N" <?php echo get_selected($_GET['sc_cal_chk'], 'N') ?>>정산미완료</option>
			</select>

			<div style="margin-bottom:15px;">
				<input type="text" name="search_value" value="<?php echo $search_value ?>" class="frm_input" style="width:100%;"
					placeholder="주문번호 및 상품명 검색">
			</div>

			<label for="stx" style="font-weight:bold">일자 조회<strong class="sound_only"> 필수</strong></label>
			<div class="sch_bar" style="margin-top:3px;">
				<input type="date" name="st_date" value="<?php echo $st_date ?>" required class="sch_input" size="25"
					maxlength="20" placeholder="" style="width:45%;text-align:center">
				<span style="float:left;height:38px;line-height:38px; margin:0 5px">~</span>
				<input type="date" name="ed_date" value="<?php echo stripslashes($ed_date) ?>" required class="sch_input"
					size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
			</div>

			<div style="margin-top: 10px">
				<label for="search_by_date"><strong>일자별 합계</strong></label>
				<input type="checkbox" name="search_by_date" id="search_by_date" value="Y" <?= $search_by_date === 'Y' ? "checked" : "" ?> />
			</div>

			<div style="margin-top: 10px">
				<input type="radio" name="ledger" id="all" value="all"
					<?= (!isset($_POST['ledger']) || $_POST['ledger'] === 'all') ? "checked" : "" ?>>
				<label for="all" style="margin-right: 10px;">전체</label>

				<input type="radio" name="ledger" id="account" value="account"
					<?= (isset($_POST['ledger']) && $_POST['ledger'] === 'account') ? "checked" : "" ?>>
				<label for="account" style="margin-right: 10px;">매출만</label>

				<input type="radio" name="ledger" id="receipt" value="receipt"
					<?= (isset($_POST['ledger']) && $_POST['ledger'] === 'receipt') ? "checked" : "" ?>>
				<label for="receipt">반품만</label>
			</div>

			<button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px">
				<i class="fa fa-search" aria-hidden="true"></i> 검색하기
			</button>

			<button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;"
				onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'">
				<i class="fa fa-repeat" aria-hidden="true"></i> 검색초기화
			</button>

			<button type="button" class="bo_sch_cls" title="닫기">
				<i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span>
			</button>
		</form>
	</fieldset>
	<div class="bo_sch_bg"></div>
</div>
<!-- 매입처 이월 금액 보정 -->
<div class="bo_sch_wrap edit-modal">
	<fieldset class="bo_sch" style="padding:10px; width: 400px;">
		<h3>매입처 이월 금액 보정</h3>
		<form name="famount" method="get">
			<input type="hidden" name="sc_domain" value="<?= $sc_domain ?>">

			<div class="tw-mb-[15px]">
				<?php
				$sql = "select * from g5_acc3_carryover_amount where sc_domain = '$sc_domain' and abs(amount) > 0 order by base_date";
				$result = sql_fetch_all($sql);
				?>
				<div class="tbl_head01 tbl_wrap" style="margin: unset;">
					<table>
						<thead>
							<tr>
								<th>보정일자</th>
								<th>보정금액</th>
							</tr>
						</thead>
						<tbody>
							<?php if (count($result) > 0) { ?>
								<?php foreach ($result as $item) { ?>
									<tr>
										<td><?= $item['base_date'] ?></td>
										<td><?= $item['amount'] ?></td>
									</tr>
								<?php } ?>
							<?php } else { ?>
								<tr>
									<td align="center" colspan="2">보정 기록이 없습니다.</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>

				</div>
			</div>
			<div style="margin-bottom: 15px;">
				<select name="wr_orderer" class="frm_input search_sel" style="width:100%;" disabled>
					<option value="">매출처</option>
					<option value="ACSUM" <?= get_selected("ACSUM", $sc_domain) ?>>AC + ACF</option>
					<option value="ACDSUM" <?= get_selected("ACDSUM", $sc_domain) ?>>AC-CAD + ACF-CAD</option>
					<option value="Q10JPSUM" <?= get_selected("Q10JPSUM", $sc_domain) ?>>QOO10jp + QOO10JP-1</option>
					<?php
					$code_list = get_code_list('4');
					foreach ($code_list as $key => $value) {
						echo "<option value=\"{$value['code_value']}\" " . get_selected($value['code_value'], $sc_domain) . ">{$value['code_name']}</option>";
					}
					?>
					?>
				</select>
			</div>
			<div style="margin-bottom: 15px;">
				<div class="sch_bar" style="margin-top:3px; display: flex; justify-content: center;">
					<input type="date" name="base_date" value="<?php echo $st_date ?>" required class="sch_input" size="25"
						maxlength="20" placeholder="" style="width:45%;text-align:center">
				</div>
			</div>
			<div style="margin-bottom:15px;">
				<input type="number" name="amount" value="" class="frm_input" style="width:100%;"
					placeholder="보정 금액" autocomplete="off">
			</div>

			<button type="button" value="저장" class="btn_b01" style="width:100%;margin-top:15px" onclick="fnUpdateCarryoverAmount();">적용하기
			</button>
			<button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span
					class="sound_only">닫기</span></button>
		</form>
	</fieldset>
	<div class="bo_sch_bg"></div>
</div>
<!-- 매입처 이월 금액 보정 -->
<script>
	$(document).ready(function() {
		$('.search_sel').select2();
	});
	jQuery(function($) {
		// 게시판 검색
		$(".btn_bo_sch").on("click", function() {
			$(".bo_sch_wrap").not(".edit-modal").toggle();
		})
		$(".btn_bo_sch2").on("click", function() {
			$(".edit-modal").toggle();
		})
		$('.bo_sch_bg, .bo_sch_cls').click(function() {
			$('.bo_sch_wrap').hide();
		});
	});
</script>
<script>
	$(function() {

		// 전체 체크
		$("#ALLCHK").click(function() {
			var flag = $(this).is(":checked");
			$("input[name='seq_arr[]']").prop("checked", flag);
		});

		// 개별 체크
		$("input[name='seq_arr[]']").click(function() {
			var flag = true;
			$("input[name='seq_arr[]']").each(function() {
				if ($(this).is(":checked") == false) {
					flag = false;
				}
			});
			$("#ALLCHK").prop("checked", flag);
		});

		$("input[name='wr_cal_chk']").click(function() {
			var flag = $(this).is(":checked");
			var seq = $(this).attr("data");
			if (flag == true) {
				var wr_cal_chk = "Y";
			} else {
				var wr_cal_chk = "N";
			}

			const fee1 = $(this).closest("tr").find("[name='sales3_cal_fee1']");
			const fee2 = $(this).closest("tr").find("[name='sales3_cal_fee2']");
			const tax = $(this).closest("tr").find("[name='sales3_cal_tax']");

			const fee1Checked = fee1.prop("checked");
			const fee2Checked = fee2.prop("checked");
			const taxChecked = tax.prop("checked");

			const fee1Value = fee1.val();
			const fee2Value = fee2.val();
			const taxValue = tax.val();

			$.ajax({
				url: "./ajax.acc3_update.php?seq=" + seq +
					"&wr_cal_chk=" + wr_cal_chk +
					"&fee1_checked=" + fee1Checked +
					"&fee2_checked=" + fee2Checked +
					"&tax_checked=" + taxChecked +
					"&fee1_value=" + fee1Value +
					"&fee2_value=" + fee2Value +
					"&tax_value=" + taxValue,
				dataType: "json",
				success: function(data) {
					if (isDefined(data.message)) {
						if (!flag) {
							fee1.prop("checked", false);
							fee2.prop("checked", false);
							tax.prop("checked", false);
						}
						alert(data.message);
					}
				}
			});
		});
	});

	$(document).on("click", ".sales3_cal", function() {
		const value = Math.round(this.value);
		const seq = this.dataset.seq;
		const isChecked = this.checked;
		const remainingElement = this.closest('tr').querySelector('.remaining-amount');
		const totalRemainingElement = document.querySelector('.total-remaining-amount');

		let remaining = parseInt(remainingElement.innerText.replace(/,/g, ''), 10);
		let totalRemaining = parseInt(totalRemainingElement.innerText.replace(/,/g, ''), 10);

		let type;

		if (this.name === "sales3_cal_fee1") {
			type = 'fee1';
		} else if (this.name === "sales3_cal_fee2") {
			type = 'fee2';
		} else {
			type = 'tax';
		}

		$.post("./ajax.acc3_cal.php", {
			seq: seq,
			value: value,
			type: type,
			is_checked: isChecked
		}, (response) => {
			console.log(response);
			remainingElement.innerText = (isChecked ? remaining - value : remaining + value).toLocaleString();
			totalRemainingElement.innerText = (isChecked ? totalRemaining - value : totalRemaining + value).toLocaleString();
		}, 'json');
	});

	function calculate_action(type, seq, wr_cal_chk) {

		$.post(g5_url + "/acc/ajax.acc3_update.php", {
			type: type,
			seq: seq,
			wr_cal_chk: wr_cal_chk
		}, function(data) {
			if (data == 'y') {
				let txt = wr_cal_chk == "Y" ? "정산처리" : "정산취소";
				alert(txt + "가 완료되었습니다.");
				location.reload();
			} else {
				alert("요류가 발생했습니다.");
			}
		});
	}

	function fnCalcUnit(seq_arr) {
		var params = "seq_arr=" + seq_arr;
		$.post("./ajax.acc3_modal.php", params, function(data) {
			$("#modal_view_calc").html(data);
			$(".modal_view").toggle();
		});
	}

	function acc3_frm_submit(form) {
		return true;
	}

	// 모달 닫기
	function close_modal() {
		$(".bo_sch_bg").hide();
		$(".modal_view").hide();
		$("#modal_view_calc").empty();
	}


	$(document).on("click", "[name=search_accumulate]", function() {
		const sch_bar = document.querySelector('.sch_bar');
		const sdate = document.querySelector('[name=st_date]');
		const edate = document.querySelector('[name=ed_date]');

		if (!this.checked) {
			sdate.disabled = false;
			edate.disabled = false;
			sch_bar.style.backgroundColor = '#FFFFFF';
		} else {
			sdate.disabled = true;
			edate.disabled = true;
			sch_bar.style.backgroundColor = '#f1f3f6';
		}

	});

	function fnUpdateCarryoverAmount() {
		const params = $("form[name='famount']").serialize();

		$.post("./ajax.acc3_amount.php", params, function(data) {
			if (data.status === 'Y') {
				location.reload();
			} else {
				alert("서버 오류 입니다.");
			}
		}, 'json');

	}

	function excel_download() {
		if (!confirm("엑셀 출력을 하시겠습니까?")) {
			return false;
		}

		const currentUrl = new URL(window.location.href); // 현재 URL을 가져옴
		const params = currentUrl.search; // 쿼리스트링만 가져옴
		const newUrl = `${g5_url}/acc/acc3_excel.php${params}`; // 기존 쿼리스트링을 새로운 URL에 붙임

		location.href = newUrl;
	}
</script>

<?php
include_once(G5_THEME_PATH . '/tail.php');
