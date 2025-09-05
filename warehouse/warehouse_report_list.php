<?php
include_once('../common.php');
include_once(G5_THEME_PATH . '/head.php');

if (!$date) {
  $date = date('Y-m-d');
}

$query = "
SELECT
    CASE
        WHEN rs.wr_warehouse = '1000' THEN '한국창고'
        WHEN rs.wr_warehouse = '3000' THEN '미국창고'
        WHEN rs.wr_warehouse = '4000' THEN 'FBA창고'
        WHEN rs.wr_warehouse = '5000' THEN 'W-FBA창고'
        WHEN rs.wr_warehouse = '6000' THEN 'U-FBA창고'
        WHEN rs.wr_warehouse = '7000' THEN '한국반품창고'
        WHEN rs.wr_warehouse = '8000' THEN '미국반품창고'
        WHEN rs.wr_warehouse = '11000' THEN '한국폐기창고'
        WHEN rs.wr_warehouse = '12000' THEN '미국폐기창고'
        END AS wr_warehouse_ko,
    SUM(wr_stock) AS total_stock,
    SUM(wr_stock * wr_22) AS total_stock_price,
    rs.wr_warehouse
FROM g5_rack_stock AS rs
         INNER JOIN g5_write_product AS p ON p.wr_id = rs.wr_product_id
         LEFT JOIN g5_rack AS r ON rs.wr_rack = r.seq
WHERE rs.wr_datetime <= '{$date}'
GROUP BY wr_warehouse_ko
";

$result = sql_fetch_all($query);

$list = [];
foreach ($result as $item) {
  if (!$item['wr_warehouse_ko']) {
    continue;
  }

  $list[] = $item;
}

$total_stock = 0;
$total_stock_amount = 0;

?>

<link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css">
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
        word-break: text-overflow: ellipsis;
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

    a.btn_ov02, a.ov_listall {
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

    a.btn_ov02:hover, a.ov_listall:hover {
        background: #3f51b5
    }

    .tbl_head01 thead th, .tbl_head01 tbody td {
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
        height: 600px;
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

    .tbl_wrap tbody {
        overflow-y: scroll;
        max-height: 580px;
    }

</style>
<div id="bo_list">
  <div class="bo_list_innr">
    <h2 class="board_tit">창고보고서</h2>
    <p>기준일자까지의 창고의 재고 내역입니다.</p>
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
          <button type="button" class="btn_b01 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i>검색</button>
        </li>
      </ul>
    </div>
    <h2 style="padding-bottom:10px; font-size:20px; text-align:center">창고보고서</h2>
    <div class="tbl_head01 tbl_wrap">
      <table>
        <thead>
        <tr>
          <th style="width: 200px;">창고</th>
          <th>기준일자</th>
          <th>재고</th>
          <th>재고금액</th>
          <th style="width: 350px;"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($list as $k => $item) {
          $total_stock += $item['total_stock'];
          $total_stock_amount += $item['total_stock_price'];

          ?>
          <tr>
            <td><?= $item['wr_warehouse_ko'] ?></td>
            <td style="text-align: center;"><?= $date ?></td>
            <td style="text-align: right;"><?= number_format($item['total_stock']) ?></td>
            <td style="text-align: right;"><?= number_format($item['total_stock_price']) ?></td>
            <td class="tw-space-x-3" style="text-align: center;">
              <?php

              echo '<a href="#" onclick="open_popup(\''. $item['wr_warehouse'] .'\', \''. $date .'\');" class="btn btn_b02">기준일자 재고</a>';

              switch ($item['wr_warehouse_ko']) {
                case '한국창고':
                  echo '<a href="/report/report_list.php?search_warehouse=wr_32" target="_blank" class="btn btn_b02">현재재고</a>';
                  break;
                case '미국창고':
                  echo '<a href="/report/report_list.php?search_warehouse=wr_36" target="_blank" class="btn btn_b02">현재재고</a>';
                  break;
                case 'FBA창고':
                  echo '<a href="/report/report_list.php?search_warehouse=wr_42" target="_blank" class="btn btn_b02">현재재고</a>';
                  break;
                case 'W-FBA창고':
                  echo '<a href="/report/report_list.php?search_warehouse=wr_43" target="_blank" class="btn btn_b02">현재재고</a>';
                  break;
                case 'U-FBA창고':
                  echo '<a href="/report/report_list.php?search_warehouse=wr_44" target="_blank" class="btn btn_b02">현재재고</a>';
                  break;
                case '한국반품창고':
                  echo '<a href="/report/report_list.php?search_warehouse=wr_40" target="_blank" class="btn btn_b02">현재재고</a>';
                  break;
                case '미국반품창고':
                  echo '<a href="/report/report_list.php?search_warehouse=wr_41" target="_blank" class="btn btn_b02">현재재고</a>';
                  break;
                case '한국폐기창고':
                  echo '<a href="/report/report_list.php?search_warehouse=wr_45" target="_blank" class="btn btn_b02">현재재고</a>';
                  break;
                case '미국폐기창고':
                  echo '<a href="/report/report_list.php?search_warehouse=wr_46" target="_blank" class="btn btn_b02">현재재고</a>';
                  break;
              }
              ?>


            </td>
          </tr>
        <?php } ?>
        <tr>
          <td style="text-align: right;" colspan="2">합계</td>
          <td style="text-align: right;"><?= number_format($total_stock) ?></td>
          <td style="text-align: right;"><?= number_format($total_stock_amount) ?></td>
          <td></td>
        </tr>
        </tbody>
      </table>
    </div>
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

      <div style="margin-bottom: 15px; width: 100%;">
      </div>

      <div style="margin-bottom: 15px; width: 100%;">
        <label for="stx" style="font-weight:bold">기준일자 조회<strong class="sound_only"> 필수</strong></label>
        <div class="sch_bar" style="margin-top:3px; display: flex; justify-content: center;">
          <input type="date" name="date" value="<?php echo stripslashes($date) ?>" required class="sch_input"
                 size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
        </div>
      </div>

      <button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search"
                                                                                            aria-hidden="true"></i> 검색하기
      </button>
      <button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;"
              onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat"
                                                                              aria-hidden="true"></i> 검색초기화
      </button>
      <button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span
          class="sound_only">닫기</span></button>
    </form>
  </fieldset>
  <div class="bo_sch_bg"></div>
</div>
<script>
    $(document).ready(function () {
        $(".btn_bo_sch").on("click", function () {
            $(".bo_sch_wrap").toggle();
        })
        $('.bo_sch_bg, .bo_sch_cls').click(function () {
            $('.bo_sch_wrap').hide();
        });

    });

    function open_popup(wr_warehouse, date) {
        window.open('./warehouse_product_list_pop.php?wr_warehouse=' + wr_warehouse + '&date=' + date, '창고 재고 목록', 'width=1000,height=650,scrollbars=yes,resizable=yes');
    }

</script>

<?php
include_once(G5_THEME_PATH . '/tail.php');