<?php
include_once('./_common.php');
include_once(G5_THEME_PATH . '/head.php');

if (!$st_date) {
  $st_date = date("Y-m-d");
}

if (!$ed_date) {
  $ed_date = date("Y-m-d");
}

if (!$search_query_date) {
  $search_query_date = 'wr_date3';
}

$sql_where = " ";
$sql_orderby = " ";
$wd_sql_where = " ";
$wd_sql_valid = true;

$qstr = $_SERVER['QUERY_STRING'];
$list = [];
$rows_cnt = 0;
$code = '';
$column = 'wr_orderer';
$sum_misu = 0;
$carryover = 0;

if ($search_query_date === 'wr_date3') {
  $sql_where .= " and sl.wr_date3 between '{$st_date}' and '{$ed_date}' ";
  $sql_orderby .= " order by sl.wr_date3 ";
} else {
  $sql_where .= " and sl.wr_date2 between '{$st_date}' and '{$ed_date}' ";
  $sql_orderby .= " order by sl.wr_date2 ";
}

if ($sc_orderer) {
  $sc_arr = explode("|", $sc_orderer);
  $code = $sc_arr[1];
}

if ($code_card) {
  $sql_where .= " AND code.idx = '$code_card'";
  $wd_sql_valid = $code_card == '1461';
}

if ($code) {
  $code = trim($code);
  $sql_where .= " AND sl.wr_orderer = '{$code}'";
  $wd_sql_where .= " AND wd_orderer = '$code'";

  # 이월 합산 금액
  $sql_sum = "
  select sum(wr_misu) as sum_misu
      from g5_sales2_list as sl
           left join g5_write_product as wp on wp.wr_id = sl.wr_product_id
           left join g5_member as m on m.mb_id = sl.mb_id
      where wr_direct_use = '0'
        and wr_date3 >= '2025-01-01' and wr_date3 < '$st_date' and  wr_orderer = '$code'  
";

  $result = sql_fetch($sql_sum);

  $sum_misu = $result['sum_misu'];

  # 이월 합산 금액 보정
  $sql = "SELECT sum(amount) as amount FROM g5_acc2_carryover_amount WHERE wr_orderer = '{$code}' and base_date <= '{$st_date}'";
  $result = sql_fetch($sql);

  $carryover = $result['amount'];
}

if ($search_value) {
  $wd_sql_valid = false;
  $search_value = trim($search_value);
  $sql_where .= " AND (sl.wr_order_num = '$search_value' OR IF(sl.wr_product_nm = '' OR sl.wr_product_nm IS NULL, wr_subject, sl.wr_product_nm) LIKE '%$search_value%' OR IFNULL(wr_5, IFNULL(wr_4, IFNULL(wr_6, ''))) = '$search_value')";
}

if ($search_mb_id) {
  $wd_sql_valid = false;
  $search_mb_id = trim($search_mb_id);
  $sql_where .= " AND sl.mb_id = '$search_mb_id'";
}

if ($search_misu_min) {
  $wd_sql_valid = false;
  $sql_where .= " and sl.wr_misu + 0 >= '$search_misu_min'";
}

if ($search_misu_max) {
  $wd_sql_valid = false;
  $sql_where .= " and sl.wr_misu + 0 <= '$search_misu_max'";
}

if ($misu_only === 'Y') {
  $wd_sql_valid = false;
  $sql_where .= " and sl.wr_misu + 0 > 0";
}


$query = "
    select sl.seq,
           sl.wr_date3,
           sl.wr_date2,
           sl.wr_orderer,
           (SELECT code_name FROM g5_code_list WHERE idx = sl.wr_orderer) as wr_orderer_nm,
           sl.wr_order_num,
           sl.wr_code,
           IFNULL(wr_5, IFNULL(wr_4, IFNULL(wr_6, ''))) as code,
           IF(sl.wr_product_nm = '' OR sl.wr_product_nm IS NULL, wr_subject, sl.wr_product_nm) as product_nm,
           sl.wr_order_total,
           IFNULL(wr_warehouse_price, 0) as pay_price,
           sl.wr_misu,
           mb_name,
           code.code_name
    from g5_sales2_list as sl
             left join g5_write_product as wp on wp.wr_id = sl.wr_product_id
						 left join g5_member as m on m.mb_id = sl.mb_id
             left join g5_sales1_list as sl1 on sl1.wr_order_num = sl.wr_order_num
             left join g5_sales_metadata as sm on sm.entity_type = 'g5_sales1_list' and entity_id = sl1.seq and `key` = 'code_card'
             left join g5_code_list as code on code.code_type = '7' and code.idx = sm.value
             left join g5_sales2_after_pay as ap on ap.sales2_id = sl.seq
    where wr_direct_use = '0'
       {$sql_where} {$sql_orderby}
";

$result = sql_query($query);

while ($row = sql_fetch_array($result)) {
  $based_date = $search_query_date;
  $list[$row['wr_orderer']][$row[$based_date]][$row['seq']] = $row;
}

if ($search_misu_min) {
  $sql_where .= " and ap.wr_misu + 0 >= '$search_misu_min'";
}

if ($search_misu_max) {
  $sql_where .= " and ap.wr_misu + 0 <= '$search_misu_max'";
}

if ($misu_only === 'Y') {
  $sql_where .= " and ap.wr_misu + 0 > 0";
}


$wd_list = [];
if ($wd_sql_valid) {
  $query = "
  select * from g5_acc2_wd as wd
  left join g5_code_list as cl on cl.idx = wd.wd_orderer
  where wd_date between '{$st_date}' and '{$ed_date}' {$wd_sql_where}
  order by wd_date
  ";

  $wd_list = sql_fetch_all($query);
}

# 기본 list + 입출금 내역 list 합치기
$wd_temp_list = array_column($wd_list, null, 'wd_date');

$wd_temp_list = [];
foreach ($wd_list as $wd_item) {
  $domain_date = $wd_item['wd_orderer'] . '_' . $wd_item['wd_date'];
  $wd_temp_list[$domain_date][] = $wd_item;
}

foreach ($wd_temp_list as $domain_date => $wd_items) {
  list($orderer, $date) = explode('_', $domain_date, 2);

  foreach ($wd_items as $wd_item) {
    $fake_seq = 'wd_' . $wd_item['id'];

    // 발주자가 $list에 없으면 추가
    if (!isset($list[$orderer])) {
      $list[$orderer] = [];
    }

    if (!isset($list[$orderer][$date])) {
      $list[$orderer][$date] = [];
    }

    // 최종 삽입
    $list[$orderer][$date][$fake_seq] = $wd_item;
  }

  ksort($list[$orderer]);
}


?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/num-to-korean@0.5.3/dist/num-to-korean.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
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

  td input {
    width: 100px !important;
  }

  td input[type="checkbox"] {
    width: 100% !important;
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

  form[name=famount] .tbl_wrap {
    overflow-y: auto;
    max-height: 180px;
    margin-top: -2px;
  }

  form[name=famount] .tbl_wrap tr {
    height: 40px;
  }

  form[name=famount] .tbl_wrap tbody {
    overflow-y: scroll;
    max-height: 180px;

  }

  .wd_tr td {
    background: #e0ebf8;
  }
</style>
<div id="bo_list">
  <div class="bo_list_innr">
    <h2 class="board_tit">매입처 원장</h2>
    <form name="acc2_frm" id="acc2_frm" action="<?php echo G5_URL; ?>/acc/acc2_list_update.php" method="post">
      <input type="hidden" name="qstr" value="<?= $qstr ?>" />
      <input type="hidden" name="sw" value="">
      <input type="hidden" name="mode" value="<?= $column ?>" />
      <input type="hidden" name="is_all" value="N" />

      <?php if ($is_category) { ?>
        <nav id="bo_cate">
          <h2><?php echo ($board['bo_mobile_subject'] ? $board['bo_mobile_subject'] : $board['bo_subject']) ?>
            카테고리</h2>
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
          <li>
            <button type="button" class="btn_b01 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 매입처
              검색
            </button>
          </li>
          <li>
            <button type="button" class="btn_b02 btn_bo_sch3" style="background: #4472C4; color: white;">
              입출금
            </button>
          </li>
          <li>
            <button type="button" value="선택입금" onclick="fnPayOrder(false);" class="btn_b02 "
              style="background:#8e74ef;color:white;">선택지급
            </button>
          </li>
          <li>
            <button type="button" value="선택입금" onclick="fnPayOrder(true);" class="btn_b02 tw-bg-[#ff9138]"
              style="color:white;">선택 일괄지급
            </button>
          </li>
          <li>
            <button type="button" class="btn_b01 " id="excel_btn" style="background:#325422;"><i
                class="fa fa-file-pdf-o tw-mr-1"></i>엑셀출력
            </button>
            <?php if ($code) { ?>
          <li>
            <button type="button" class="btn_b02 btn_bo_sch2"><i class="fa-solid fa-pen-to-square"></i> 매입처
              이월 금액 보정
            </button>
          </li>
        <?php } ?>
        <!--<li><button type="submit" name="btn_submit" value="선택출금" class="btn_b02 " ><i class="fa fa-file-pdf-o" ></i> 선택출금</button></li>-->
        </ul>
      </div>
      <h2 style="padding-bottom:10px; font-size:20px; text-align:center">매입처 원장</h2>
      <div class="tbl_head01 tbl_wrap" style="overflow-x: auto;">
        <table style="width: 100%; min-width: 1200px;">
          <thead style="position:sticky;top:0;">
            <tr>
              <th><input type="checkbox" id="ALLCHK" /></th>
              <th style="width:90px"><?= $search_query_date === 'wr_date3' ? '매입일자' : '발주일자' ?></th>
              <th style="min-width:60px">매입처</th>
              <th>주문번호</th>
              <th style="width: 100px">대표코드</th>
              <th style="width: 60px">담당자</th>
              <th style="width: 80px;">SKU</th>
              <th style="width:150px">상품명</th>
              <th style="width:80px">매입액</th>
              <th style="width:80px">
                지급금(<label for="payAll"><input type="checkbox" id="payAll" />일괄지급</label>)
              </th>
              <th style="width:80px">미지급금</th>
              <th style="width:80px">지급수단</th>
              <th style="width:80px"></th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($list) > 0) {
              $total_order_total = 0;
              $total_pay_price = 0;
              $total_misu = 0;
              $tr_index = 0;
            ?>

              <?php foreach ($list as $k => $v) {
                foreach ($v as $k2 => $v2) {
                  $date = $k2;
                  $rowspan = count($v2);
                  $row1 = true;
                  $rowspan2 = count($v2);
                  $row2 = true;

                  $tr = $tr_index++ % 2 === 0 ? "even_tr" : "odd_tr";
                  foreach ($v2 as $k3 => $item) {
                    if (!isset($item['wd_date'])) {
                      $total_pay_price += $item['pay_price'];
                      $total_order_total += $item['wr_order_total'];
                      $total_misu += $item['wr_misu'];
              ?>
                      <tr class="<?= $tr ?>">
                        <td align="center">
                          <input type="checkbox" name="seq_arr[]" value="<?= $item['seq'] ?>" />
                        </td>
                        <td><?= $item[$search_query_date] ?></td>
                        <td><?= $item['wr_orderer_nm'] ?></td>
                        <td><?= $item['wr_order_num'] ?></td>
                        <td><?= $item['wr_code'] ?></td>
                        <td><?= $item['mb_name'] ?></td>
                        <td><?= $item['code'] ?></td>
                        <td><?= $item['product_nm'] ?></td>
                        <td align="center">
                          <input type="text" name="total_price[<?= $item['seq'] ?>]"
                            value="<?= number_format($item['wr_order_total']) ?>"
                            class="frm_input readonly" readonly>
                        </td>
                        <td align="center">
                          <input type="hidden" name="ori_pay_price[<?= $item['seq'] ?>]" class="ori_pay_price"
                            value="<?= (int)$item['pay_price'] ?>" />
                          <input type="text" name="pay_price[<?= $item['seq'] ?>]" data="<?= $item['seq'] ?>"
                            class="frm_input pay_price" value="<?= number_format((int)$item['pay_price']) ?>" />
                        </td>
                        <td align="center">
                          <input type="text" name="misu[<?= $item['seq'] ?>]" data="<?= $item['seq'] ?>"
                            class="frm_input misu readonly" value="<?= number_format($item['wr_misu']) ?>" readonly />
                        </td>
                        <td align="center">
                          <input type="text" name="card_name"
                            class="frm_input readonly" value="<?= $item['code_name'] ?>" readonly />
                        </td>
                        <td align="center">
                          <a href="javascript:void(0);" class="btn_b01"
                            onclick="fnPayUnit('<?= $item['seq'] ?>');">지급</a>
                        </td>
                      </tr>
                    <?php
                    } else {
                      $total_pay_price += $item['wd_price'];
                      $total_misu -= $item['wd_price'];
                    ?>
                      <tr class="wd_tr">
                        <td></td>
                        <td><?= $item['wd_date'] ?></td>
                        <td><?= $item['code_name'] ?></td>
                        <td colspan="6"></td>
                        <td>
                          <input type="text" class="frm_input" value="<?= $item['wd_price'] ?>" disabled />
                        </td>
                        <td colspan="2">
                          <textarea class="tw-w-full frm_input" disabled><?= $item['wd_text'] ?></textarea>
                        </td>
                        <td>
                          <button class="btn_b02" type="button" onclick="process_wd_delete('<?= $item['id'] ?>');">삭제</button>
                        </td>
                      </tr>
                    <?php } ?>


                  <?php } ?>
                <?php } ?>
              <?php } ?>

              <tr>
                <th colspan="8">합계</th>
                <td style="text-align: left !important;"><?= number_format($total_order_total) ?></td>
                <td style="text-align: left !important;"><?= number_format($total_pay_price) ?></td>
                <td style="text-align: left !important;"><?= number_format($total_misu) ?></td>
                <td></td>
              </tr>

              <?php if ($code) { ?>
                <tr>
                  <td style="text-align: center; background:rgb(232, 239, 243);" colspan="10"><strong>이월 미지급 합산
                      금액</strong></td>
                  <td colspan="1" style="background:rgb(232, 239, 243);">
                    ₩<?= number_format($sum_misu + $carryover) ?></td>
                  <td colspan="2" style="text-align: center; background:rgb(232, 239, 243);"></td>
                </tr>
                <tr>
                  <td style="text-align: center; background:rgb(232, 239, 243);" colspan="10"><strong>이월 보정 금액</strong>
                  </td>
                  <td colspan="1" style="background:rgb(232, 239, 243);">₩<?= number_format($carryover) ?></td>
                  <td colspan="2" style="text-align: center; background:rgb(232, 239, 243);"></td>
                </tr>
              <?php } ?>

            <?php } else { ?>
              <tr>
                <td colspan="12" style="text-align: center;">조회되는 데이터가 없습니다.</td>
              </tr>

              <?php if (count($wd_list) > 0) {
                foreach ($wd_list as $k => $wd) {
                  $total_pay_price += $wd['wd_price'];
                  $total_misu -= $wd['wd_price'];

              ?>
                  <tr class="wd_tr">
                    <td></td>
                    <td><?= $wd['wd_date'] ?></td>
                    <td><?= $wd['code_name'] ?></td>
                    <td colspan="6"></td>
                    <td>
                      <input type="text" class="frm_input" value="<?= $wd['wd_price'] ?>" disabled />
                    </td>
                    <td colspan="2">
                      <textarea class="tw-w-full frm_input" disabled><?= $wd['wd_text'] ?></textarea>
                    </td>
                    <td>
                      <button class="btn_b02" type="button" onclick="process_wd_delete('<?= $wd['id'] ?>');">삭제</button>
                    </td>
                  </tr>
                <?php
                }
                ?>
                <tr>
                  <th colspan="8">합계</th>
                  <td style="text-align: left !important;"><?= number_format(0) ?></td>
                  <td style="text-align: left !important;"><?= number_format($total_pay_price) ?></td>
                  <td style="text-align: left !important;"><?= number_format(0) ?></td>
                  <td></td>
                </tr>

              <?php
              }
              ?>


              <?php if ($code) { ?>
                <tr>
                  <td style="text-align: center; background:rgb(232, 239, 243);" colspan="10"><strong>이월 미지급 합산
                      금액</strong></td>
                  <td colspan="1" style="background:rgb(232, 239, 243);">
                    ₩<?= number_format($sum_misu + $carryover) ?></td>
                  <td colspan="2" style="text-align: center; background:rgb(232, 239, 243);"></td>
                </tr>
                <tr>
                  <td style="text-align: center; background:rgb(232, 239, 243);" colspan="10"><strong>이월 보정 금액</strong>
                  </td>
                  <td colspan="1" style="background:rgb(232, 239, 243);">₩<?= number_format($carryover) ?></td>
                  <td colspan="2" style="text-align: center; background:rgb(232, 239, 243);"></td>
                </tr>
              <?php } ?>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </form>
  </div>
</div>

<!-- 개별 정산 팝업-->
<div class="modal_view">
  <div class="modal_detail" id="modal_view_calc"></div>
  <div class="bo_sch_bg"></div>
</div>
<!--// 주문건 확인 -->

<div class="bo_sch_wrap">
  <fieldset class="bo_sch" style="padding:10px; top: 40%;">
    <h3>검색</h3>
    <form name="fsearch" method="get">
      <div style="margin-bottom: 15px;">
        <select name="sc_orderer" class="frm_input search_sel" style="width:100%;">
          <option value="">매입처</option>
          <?php
          $arr = get_code_list('5');
          foreach ($arr as $key => $value) {
            echo '<option value="wr_orderer|' . $value['idx'] . '" ' . get_selected($sc_orderer, "wr_orderer|" . $value['idx']) . '>' . $value['code_name'] . '</option>';
          }
          ?>
        </select>
      </div>
      <div style="margin-bottom:15px;">
        <input type="text" name="search_value" value="<?php echo $search_value ?>" class="frm_input"
          style="width:100%;"
          placeholder="주문번호/상품명/SKU 검색">
      </div>
      <div style="margin-bottom: 15px; width: 100%;">
        <select name="search_mb_id" id="search_mb_id" class="frm_input search_sel" style="width: 100%;">
          <option value="">담당자 전체</option>
          <?php
          $mbSql = " select mb_id, mb_name from g5_member where del_yn = 'N' order by mb_name asc";
          $mbRst = sql_query($mbSql);
          for ($i = 0; $mb = sql_fetch_array($mbRst); $i++) {
          ?>
            <option value="<?php echo $mb['mb_id'] ?>" <?php echo get_selected($mb['mb_id'], $search_mb_id) ?>>
              <?php echo $mb['mb_name'] ?>(<?php echo $mb['mb_id'] ?>)
            </option>
          <?php } ?>
        </select>
      </div>
      <div style="margin-bottom: 15px; width: 100%;">
        <select name="code_card" class="frm_input search_sel" style="background:#FFFFFF; width: 100%;">
          <option value="0">결제카드 전체</option>
          <?php
          $arr = get_code_list('7');
          foreach ($arr as $key => $v) {
            $selected = $v['idx'] == $code_card ? "selected" : "";
            echo "<option value='{$v['idx']}' {$selected}>{$v['code_name']}</option>";
          }
          ?>
        </select>
      </div>
      <div style="margin-bottom: 15px; width: 100%;">
        <select name="misu_only" class="frm_input search_sel" style="background:#FFFFFF; width: 100%;">
          <option value="">전체</option>
          <option value="Y" <?= get_selected('Y', $misu_only) ?>>미지급금만</option>
        </select>
      </div>
      <div style="margin-bottom: 15px; width: 100%; display: flex; align-items: center;">
        <input type="number" name="search_misu_min" min="0" value="<?= $search_misu_min ?>" class="frm_input"
          placeholder="미지급액 최소값" />
        <span>~</span>
        <input type="number" name="search_misu_max" value="<?= $search_misu_max ?>" class="frm_input"
          placeholder="미지급액 최대값" />
      </div>
      <div style="margin-bottom: 15px; width: 100%;">
        <label for="search_query_date" style="font-weight:bold; margin-top: 15px;">일자 기준</label>
        <select name="search_query_date" id="search_query_date" class="frm_input" style="background:#FFFFFF; width: 100%;">
          <option value="wr_date3" <?= get_selected('wr_date3', $search_query_date) ?>>매입 일자</option>
          <option value="wr_date2" <?= get_selected('wr_date2', $search_query_date) ?>>발주 일자</option>
        </select>
      </div>
      <div>
        <label for="stx" style="font-weight:bold; margin-top: 15px;">일자 조회<strong class="sound_only">
            필수</strong></label>
        <div class="sch_bar" style="margin-top:3px">
          <input type="date" name="st_date" value="<?php echo $st_date ?>" required class="sch_input" size="25"
            maxlength="20" placeholder="" style="width:45%;text-align:center">
          <span style="float:left;height:38px;line-height:38px; margin:0 5px">~</span>
          <input type="date" name="ed_date" value="<?php echo stripslashes($ed_date) ?>" required class="sch_input"
            size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
        </div>
      </div>

      <button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search"
          aria-hidden="true"></i>
        검색하기
      </button>
      <button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;"
        onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat"
          aria-hidden="true"></i>
        검색초기화
      </button>
      <button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span
          class="sound_only">닫기</span></button>
    </form>
  </fieldset>
  <div class="bo_sch_bg"></div>
</div>


<!-- 매입처 이월 금액 보정 -->
<div class="bo_sch_wrap edit-modal">
  <fieldset class="bo_sch" style="padding:10px; width: 400px;">
    <h3>매입처 이월 금액 보정</h3>
    <form name="famount" method="get">
      <input type="hidden" name="wr_orderer" value="<?= $sc_orderer ?>">

      <div class="tw-mb-[15px]">
        <?php
        $sql = "select * from g5_acc2_carryover_amount where wr_orderer = '$code' and abs(amount) > 0 order by base_date";
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
          <option value="">매입처</option>
          <?php
          $arr = get_code_list('5');
          foreach ($arr as $key => $value) {
            echo '<option value="wr_orderer|' . $value['idx'] . '" ' . get_selected($sc_orderer, "wr_orderer|" . $value['idx']) . '>' . $value['code_name'] . '</option>';
          }
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
        <input type="number" name="amount" value="" class="frm_input" style="width:100%;" id="amount_number"
          placeholder="보정 금액" autocomplete="off">
      </div>

      <div style="margin-bottom:15px;">
        <input type="text" value="" class="frm_input" id="amount_text" style="width:100%;"
          autocomplete="off" readonly>
      </div>

      <button type="button" value="저장" class="btn_b01" style="width:100%;margin-top:15px"
        onclick="fnUpdateCarryoverAmount();">적용하기
      </button>
      <button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span
          class="sound_only">닫기</span></button>
    </form>
  </fieldset>
  <div class="bo_sch_bg"></div>
</div>
<!-- 매입처 이월 금액 보정 -->

<!-- 입출금 모달 -->
<div class="bo_sch_wrap wd-modal">
  <fieldset class="bo_sch" style="padding:10px; width: 300px;">
    <h3>입출금</h3>
    <form name="formWdModal" method="get">
      <div style="margin-bottom: 15px;">
        <div class="sch_bar" style="margin-top:3px; display: flex; justify-content: center;">
          <input type="date" name="wd_date" value="<?php echo $wd_date ?>" required class="sch_input" size="25"
            maxlength="20" placeholder="" style="width:45%;text-align:center">
        </div>
      </div>
      <div style="margin-bottom: 15px;">
        <select name="wd_orderer" class="frm_input search_sel" style="width:100%;">
          <option value="">업체명</option>
          <?php
          $arr = get_code_list('5');
          foreach ($arr as $key => $value) {
            echo '<option value="' . $value['idx'] . '" ' . get_selected($wd_orderer, $value['idx']) . '>' . $value['code_name'] . '</option>';
          }
          ?>
        </select>
      </div>
      <div style="margin-bottom: 15px;">
        <input type="text" class="frm_input tw-w-full" name="wd_price" placeholder="금액 입력" value="<?= $wd_price ?>">
      </div>
      <div style="margin-bottom: 15px;">
        <label for="wd_text" class="tw-w-full tw-font-bold">적요 입력</label>
        <textarea name="wd_text" id="wd_text" class="tw-w-full tw-h-[50px]"><?= $wd_text ?></textarea>
      </div>
      <button type="button" value="저장" class="btn_b01" style="width:100%;margin-top:15px"
        onclick="process_wd();">저장하기
      </button>
      <button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span
          class="sound_only">닫기</span></button>
    </form>
  </fieldset>
  <div class="bo_sch_bg"></div>
</div>
<!-- 입출금 모달 -->



<script>
  $(document).ready(function() {
    const amountNumberInput = $('#amount_number');
    const amountTextInput = $('#amount_text');

    if (amountNumberInput.length && amountTextInput.length) {
      amountNumberInput.on('input', function() {
        let inputValue = $(this).val();
        let koreanText = '';

        if (inputValue < 0) {
          koreanText += '마이너스 ';
          inputValue = - inputValue;
        }
        koreanText += numToKorean.numToKorean(Number(inputValue));
        amountTextInput.val(koreanText);
      });
    }
    $('.search_sel').select2();
  });

  jQuery(function($) {
    // 게시판 검색
    $(".btn_bo_sch").on("click", function() {
      $(".bo_sch_wrap").not(".edit-modal").not(".date-modal").not(".wd-modal").toggle();
    });
    $(".btn_bo_sch2").on("click", function() {
      $(".edit-modal").toggle();
    });
    $(".btn_bo_sch3").on("click", function() {
      $(".wd-modal").toggle();
    })
    $('.bo_sch_bg, .bo_sch_cls').click(function() {
      $('.bo_sch_wrap').hide();
    });
  });
</script>
<script>
  $(function() {

    $("#excel_btn").bind("click", function() {

      if (!confirm("엑셀 출력을 하시겠습니까?")) {
        return false;
      }

      const currentUrl = new URL(window.location.href); // 현재 URL을 가져옴
      const params = currentUrl.search; // 쿼리스트링만 가져옴
      const newUrl = `${g5_url}/acc/acc2_excel.php${params}`; // 기존 쿼리스트링을 새로운 URL에 붙임


      location.href = newUrl;
    });


    $("#ALLCHK").click(function() {
      var flag = $(this).is(":checked");
      $("input[name='seq_arr[]']").prop("checked", flag);
    });

    $("input[name='seq_arr[]']").click(function() {
      var flag = true;
      $("input[name='seq_arr[]']").each(function() {
        if ($(this).is(":checked") == false) {
          flag = false;
        }
      });

      $("#ALLCHK").prop("checked", flag);
    });

    $("#payAll").click(function() {
      var flag = $(this).is(":checked");
      $(".pay_price").each(function() {
        var seq = $(this).attr("data");
        if (flag == true) { // 일괄지급 체크시
          var total_price = $("input[name='total_price[" + seq + "]']").val().replace(/[^0-9]/g, "");;
          $("input[name='pay_price[" + seq + "]']").val(total_price);
          $("input[name='misu[" + seq + "]']").val(0);

        } else { // 일괄지급 미체크시
          var total_price = $("input[name='total_price[" + seq + "]']").val().replace(/[^0-9]/g, "");;
          var ori_pay_price = $("input[name='ori_pay_price[" + seq + "]']").val().replace(/[^0-9]/g, "");;
          $("input[name='pay_price[" + seq + "]']").val(ori_pay_price);
          $("input[name='misu[" + seq + "]']").val(total_price - ori_pay_price);
        }
      });
    });

    $(".pay_price").bind("blur", function() {
      var seq = $(this).attr("data");
      var total_price = $("input[name='total_price[" + seq + "]']").val().replace(/[^0-9]/g, "");
      var pay_price = $(this).val().replace(/[^0-9]/g, "");
      if (isDefined(pay_price) == false) {
        pay_price = 0;
      }
      var misu = total_price - pay_price;
      $("input[name='misu[" + seq + "]']").val(misu.toLocaleString());
    });

    $('#sorting_box').bind('change', function() {

      let sort = $(this).val();

      if (sort == "default") {
        location.href = '?sst=wr_id&sod=desc&stx2=<?php echo $stx2 ?>';
      } else if (sort == "up") {
        location.href = '?sst=stock&sod=desc&stx2=<?php echo $stx2 ?>';
      } else if (sort == "down") {
        location.href = '?sst=stock&sod=asc&stx2=<?php echo $stx2 ?>';
      }
    });

    $(".wr_warehouse_price").bind("keyup", function() {
      var price = $(this).val().replace(/[^0-9]/g, "");
      var conv_price = get_number(price)
      $(this).val(number_format(conv_price));
      var seq_arr = $(this).attr("seq");
      var total_order_price = $("input[name='wr_total_price[" + seq_arr + "]']").val().replace(/[^0-9]/g, "");;
      var total_misu = total_order_price - conv_price;
      $("input[name='total_misu_arr[" + seq_arr + "]']").val(number_format(total_misu));
      fnCalcWarehousePrice();
    });

    $('.excel_form').bind('click', function() {

      let id = $(this).attr('data');
      var _width = '600';
      var _height = '600';

      var _left = Math.ceil((window.screen.width - _width) / 2);
      var _top = Math.ceil((window.screen.height - _height) / 2);

      window.open("./excel_pop.php", "excel_pop", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

      return false;
    });

    $('.delivery_com').bind('click', function() {

      var _width = '625';
      var _height = '600';

      var _left = Math.ceil((window.screen.width - _width) / 2);
      var _top = Math.ceil((window.screen.height - _height) / 2);

      window.open("./delivery_company.php", "pop_delivery_company", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

      return false;
    });

    $(".number_fmt_list").bind('input', function() {
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    });


  });

  function fnPayUnit(seq) {
    var pay_price = $("input[name='pay_price[" + seq + "]']").val();
    var misu = $("input[name='misu[" + seq + "]']").val();
    var param = {
      "mode": "<?= $column ?>",
      "seq": seq,
      "pay_price": pay_price,
      "misu": misu
    };
    $.ajax({
      url: "./ajax.acc2_proc.php",
      data: param,
      dataType: "json",
      success: function(data) {
        if (isDefined(data.message)) {
          alert(data.message);
          location.reload();
        }
      }
    });
    return false;
  }

  function fnCalcWarehousePrice() {
    var total_warehouse_price = 0;
    var total_misu = 0;
    $(".wr_warehouse_price").each(function() {
      var seq_arr = $(this).attr("seq");
      var price = $(this).val();
      var conv_price = get_number(price);
      var misu = get_number($("input[name='total_misu_arr[" + seq_arr + "]']").val());
      if (!isDefined(conv_price)) {
        conv_price = 0;
      }
      total_warehouse_price = parseFloat(total_warehouse_price) + parseFloat(conv_price);
      total_misu = parseFloat(total_misu) + parseFloat(misu);
    });

    $("#total_warehouse_price").html(number_format(total_warehouse_price));
    $("#total_misu").html(number_format(total_misu));
  }

  function select_pay(seq, type) {
    let wr_misu = $("#wr_misu" + seq).val();
    $.post(g5_bbs_url + "/ajax.misu_update.php", {
      wr_misu: wr_misu,
      type: type,
      seq: seq
    }, function(data) {
      if (data == 'y') {
        alert("완료되었습니다.");
        location.reload();
      } else if (data == 'n') {
        alert("로그인이나 관리자가 아닙니다.");
      } else {
        alert("에러가 발생했습니다.");
      }
    });

  }

  function fnPayOrder(is_all = false) {
    if (is_all) {
      document.querySelector('[name=is_all]').value = 'Y';
    } else {
      document.querySelector('[name=is_all]').value = 'N';
    }

    $("#acc2_frm").ajaxSubmit({
      url: "../acc/acc2_list_update.php",
      data: "post",
      dataType: "json",
      success: function(data) {
        if (isDefined(data.message)) {
          alert(data.message);
        }
        if (data.ret_code == true) {
          document.location.reload();
        }
      }
    });
  }

  function fnPayOrderUnit(seq_arr) {
    var params = "seq_arr=" + seq_arr;
    $.post("./ajax.acc2_modal.php", params, function(data) {
      $("#modal_view_calc").html(data);
      $(".modal_view").toggle();
    });
  }

  // 모달 닫기
  function close_modal() {
    $(".bo_sch_bg").hide();
    $(".modal_view").hide();
    $("#modal_view_calc").empty();
  }

  function fnUpdateCarryoverAmount() {
    const params = $("form[name='famount']").serialize();

    $.post("./ajax.acc2_amount.php", params, function(data) {
      if (data.status === 'Y') {
        location.reload();
      } else {
        alert("서버 오류 입니다.");
      }
    }, 'json');

  }

  function open_pay_modal(seq) {
    $(".date-modal").toggle();
    $(".date-modal [name=seq]").val(seq);
  }

  function process_wd() {
    const form = document.querySelector('form[name=formWdModal]');
    const wd_date = form.querySelector('[name=wd_date]');
    const wd_orderer = form.querySelector('[name=wd_orderer]');
    const wd_text = form.querySelector('[name=wd_text]');
    const wd_price = form.querySelector('[name=wd_price]');

    if (!wd_date.value) {
      alert('날짜를 기입해주세요.');
      return;
    }

    if (!wd_orderer.value) {
      alert('업체를 선택해주세요.');
      return;
    }

    $.post("./ajax.acc2_wd.php", {
      wd_date: wd_date.value,
      wd_orderer: wd_orderer.value,
      wd_text: wd_text.value,
      wd_price: wd_price.value
    }, function(data) {
      if (data.status === 'Y') {
        alert(data.message);
        location.reload();
      } else {
        alert(data.message);
      }
    }, 'json');

  }

  function process_wd_delete(id) {
    if (!confirm('삭제하시겠습니까?')) {
      return;
    }
    $.post("./ajax.acc2_wd_delete.php", {
      id
    }, function(data) {
      if (data.status === 'Y') {
        alert(data.message);
        location.reload();
      } else {
        alert(data.message);
      }
    }, 'json');

  }
</script>

<?php
include_once(G5_THEME_PATH . '/tail.php');
