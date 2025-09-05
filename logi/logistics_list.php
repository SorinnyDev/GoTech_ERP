<?php
include_once('./_common.php');
include_once(G5_THEME_PATH . '/head.php');


if (!$date1) {
  $date1 = date('Y-m-d');
}

if (!$date2) {
  $date2 = date('Y-m-d');
}

if (!$country) {
  $country = 'KR';
}

$sql_where = "where A.wr_release_use = '1'";

if ($date1 && $date2) {
  $sql_where .= " AND A.wr_date4 BETWEEN '$date1' AND '$date2'";
}

if ($country == 'KR') {
  $sql_where .= " AND (A.wr_warehouse = 1000 or A.wr_warehouse = 9000)";
} else if ($country == 'US') {
  $sql_where .= " AND A.wr_warehouse = 3000";
}

if ($ordernum) {
  $sql_where .= " AND (A.wr_order_num = '$ordernum' or A.wr_ori_order_num = '$ordernum')";
}

if ($delivery) {
  $sql_where .= " AND wr_delivery = '$delivery'";
}

# 환율 정보
$sql = "select rate from g5_excharge where ex_eng = 'JPY'";
$result = sql_fetch($sql);
$ex_jpy = $result['rate'] / 100;

$sql = "
select A.*,
       C.sales3_cnt,
       (CASE
            WHEN IFNULL(wr_set_sku, '') > '' THEN CONCAT(A.wr_ori_order_num, '_', A.wr_set_sku)
            ELSE '' END) AS set_order,
       dc.wr_name as delivery_company_name
from g5_sales3_list A
         LEFT OUTER JOIN(SELECT wr_ori_order_num, COUNT(*) AS sales3_cnt
                         FROM g5_sales3_list
                         GROUP BY wr_ori_order_num) C ON C.wr_ori_order_num = A.wr_ori_order_num
         left join g5_write_product as wp on wp.wr_id = A.wr_product_id
         left join g5_delivery_company as dc on dc.wr_code = A.wr_delivery
        {$sql_where}
        group by A.wr_ori_order_num

";

$result = sql_query($sql);

$list = [];

while ($row = sql_fetch_array($result)) {
  $domain = $row['wr_domain'];
  $wr_date = $row['wr_date'];
  $list[$domain][] = $row;
}

?>

  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
          height: 580px;
      }

  </style>
  <div id="bo_list">
    <div class="bo_list_innr">
      <h2 class="board_tit">물류비 내역</h2>
      <div id="bo_li_top_op">
        <div style="margin-bottom: 15px;">
          <select name="country" class="frm_input country-type">
            <option value="KR" <?php echo get_selected($country, 'KR') ?>>한국출고등록</option>
            <option value="US" <?php echo get_selected($country, 'US') ?>>미국출고등록</option>
          </select>

          <ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?>">
            <li>
              <button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button>
            </li>
          </ul>
        </div>

        <div id="bo_li_01" style="clear:both; overflow-x: auto;">

          <ul class="list_head" style="min-width: 1500px; position:sticky;top:0;background:#fff;z-index:2;">
            <li style="width:120px">도메인</li>
            <li style="width:180px">주문번호</li>
            <li style="width:120px">주문수량</li>
            <li style="width:120px">매출일자</li>
            <li style="width:120px">출고일자</li>
            <li style="width:120px">국가</li>
            <li style="width:120px">배송사</li>
            <li style="width:120px">배송통화</li>
            <li style="width:120px">배송요금(₩)</li>
            <li style="width:120px">유류할증료(₩)</li>
            <li style="width:120px">추가배송요금(₩)</li>
            <li style="width:120px">배송 총 금액(₩)</li>
          </ul>

          <?php if (count($list) > 0) { ?>

            <div id="bo_li_02" class="list_03">
              <ul style="width:100%;min-width:max-content;height: 580px;overflow:auto;">
                <?php
                $total_fee = 0;
                $total_oil_fee = 0;
                $total_fee2 = 0;
                $total_sum_fee = 0;
                foreach ($list as $k => $value) {
                  $domain = $k;
                  ?>
                  <?php foreach ($value as $item) {
                    $wr_delivery_currency = $item['wr_delivery'] == "1021" ? "JPY" : "KRW";

                    if ($wr_delivery_currency == 'JPY') {
                      $item['wr_delivery_fee'] = $item['wr_delivery_fee'] * $ex_jpy;
                      $item['wr_delivery_fee2'] = $item['wr_delivery_fee2'] * $ex_jpy;
                      $item['wr_delivery_oil'] = $item['wr_delivery_oil'] * $ex_jpy;
                    }

                    $delivery_total_fee = $item['wr_delivery_fee'] + $item['wr_delivery_fee2'] + $item['wr_delivery_oil'];

                    $total_fee += $item['wr_delivery_fee'];
                    $total_oil_fee += $item['wr_delivery_oil'];
                    $total_fee2 += $item['wr_delivery_fee2'];
                    $total_sum_fee += $delivery_total_fee;
                    ?>
                    <li class="<?php echo $bg ?>">
                      <div class="cnt_left" style="width:120px;text-align:center">
                        <?= $item['wr_domain'] ?>
                      </div>
                      <div class="cnt_left" style="width:180px;text-align:center">
                        <?= $item['wr_ori_order_num'] ?>
                      </div>
                      <div class="cnt_left" style="width:120px;text-align:center">
                        <?= $item['sales3_cnt'] ?>
                      </div>
                      <div class="cnt_left" style="width:120px;text-align:center">
                        <?= $item['wr_date'] ?>
                      </div>
                      <div class="cnt_left" style="width:120px;text-align:center">
                        <?= $item['wr_date4'] ?>
                      </div>
                      <div class="cnt_left" style="width:120px;text-align:center">
                        <?= $item['wr_country'] ?>
                      </div>
                      <div class="cnt_left" style="width:120px;text-align:center">
                        <?= $item['delivery_company_name'] ?>
                      </div>
                      <div class="cnt_left" style="width:120px;text-align:center">
                        <?= $wr_delivery_currency ?>
                      </div>
                      <div class="cnt_left" style="width:120px;text-align:right">
                        <?= number_format($item['wr_delivery_fee']) ?>
                      </div>
                      <div class="cnt_left" style="width:120px;text-align:right">
                        <?= (floor($item['wr_delivery_oil']) == $item['wr_delivery_oil'])
                          ? number_format($item['wr_delivery_oil'], 0)
                          : number_format($item['wr_delivery_oil'], 1) ?>
                      </div>
                      <div class="cnt_left" style="width:120px;text-align:right">
                        <?= (floor($item['wr_delivery_fee2']) == $item['wr_delivery_fee2'])
                          ? number_format($item['wr_delivery_fee2'], 0)
                          : number_format($item['wr_delivery_fee2'], 1) ?>
                      </div>
                      <div class="cnt_left" style="width:120px;text-align:right">
                        <?php
                        ?>
                        <?= (floor($delivery_total_fee) == $delivery_total_fee)
                          ? number_format($delivery_total_fee, 0)
                          : number_format($delivery_total_fee, 1) ?>
                      </div>
                    </li>

                  <?php } ?>
                <?php } ?>
                <li>
                  <div class="cnt_left" style="width:1020px;text-align:center">
                    <strong>합계</strong>
                  </div>
                  <div class="cnt_left" style="width:120px;text-align:right">
                    <?= (floor($total_fee) == $total_fee)
                      ? number_format($total_fee, 0)
                      : number_format($total_fee, 1) ?>
                  </div>
                  <div class="cnt_left" style="width:120px;text-align:right">
                    <?= (floor($total_oil_fee) == $total_oil_fee)
                      ? number_format($total_oil_fee, 0)
                      : number_format($total_oil_fee, 1) ?>
                  </div>
                  <div class="cnt_left" style="width:120px;text-align:right">
                    <?= (floor($total_fee2) == $total_fee2)
                      ? number_format($total_fee2, 0)
                      : number_format($total_fee2, 1) ?>
                  </div>
                  <div class="cnt_left" style="width:120px;text-align:right">
                    <?= (floor($total_sum_fee) == $total_sum_fee)
                      ? number_format($total_sum_fee, 0)
                      : number_format($total_sum_fee, 1) ?>
                  </div>
                </li>
              </ul>
            </div>
          <?php } else { ?>
            <div id="bo_li_02" class="list_03">
              <ul style="width:100%;min-width:max-content;height: 60px;overflow:auto;">
                <li>
                  <div class="cnt_left" style="width:1500px;text-align:center">
                    조회되는 데이터가 없습니다.
                  </div>
                </li>

              </ul>
            </div>
          <?php } ?>

        </div>
      </div>
    </div>
  </div>

  <div class="bo_sch_wrap">
    <fieldset class="bo_sch">
      <h3>검색</h3>
      <form name="fsearch" method="get">
        <input type="hidden" name="warehouse" value="<?= $warehouse ?>">
        <input type="hidden" name="country" value="<?= $country ?>">
        <?php
        ?>
        <select name="delivery" style="margin-bottom:15px">
          <option value="">배송사 선택</option>
          <?php
          $sql = "select * from g5_delivery_company where wr_use = 1";
          $delivery_list = sql_fetch_all($sql);
          foreach ($delivery_list as $key => $value) {
            echo '<option value="' . $value['wr_code'] . '" ' . get_selected($value['wr_code'], $delivery) . '>' . $value['wr_name'] . '</option>';
          }
          ?>
        </select>

        <div style="margin-bottom:15px;">
          <input type="text" name="ordernum" value="<?= $ordernum ?>" class="frm_input" style="width:100%;" placeholder="주문번호 검색">
        </div>
        <strong>출고일자 조회</strong>
        <div class="sch_bar" style="margin-top:3px">
          <input type="date" name="date1" value="<?php echo $date1 ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
          <span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
          <input type="date" name="date2" value="<?php echo stripslashes($date2) ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
        </div>
        <button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
        <button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'">
          <i class="fa fa-repeat" aria-hidden="true"></i> 검색초기화
        </button>
        <button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
      </form>
    </fieldset>
    <div class="bo_sch_bg"></div>
  </div>
  <script>

    jQuery(function ($) {
      // 게시판 검색
      $(".btn_bo_sch").on("click", function () {
        $(".bo_sch_wrap").toggle();
      })
      $('.bo_sch_bg, .bo_sch_cls').click(function () {
        $('.bo_sch_wrap').hide();
      });
    });

    document.querySelector('.country-type').addEventListener('change', function() {
      const selectedValue = this.value;
      const url = new URL(window.location.href);

      url.searchParams.set('country', selectedValue);

      window.location.href = url.toString();
    });


  </script>
<?php
include_once(G5_THEME_PATH . '/tail.php');