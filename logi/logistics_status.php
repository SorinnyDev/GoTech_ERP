<?php
include_once('./_common.php');

include_once(G5_THEME_PATH . '/head.php');

if (!$date1) {
  $date1 = date('Y-m-01');
}

if (!$date2) {
  $date2 = date('Y-m-d');
}

$sql_where = " A.wr_release_use = '1'";

if ($date1 && $date2) {
  $sql_where .= " AND A.wr_date4 BETWEEN '$date1' AND '$date2'";
}

if ($wr_domain) {
  $sql_where .= " AND wr_domain = '$wr_domain'";
}

# 환율 정보
$sql = "select rate from g5_excharge where ex_eng = 'JPY'";
$result = sql_fetch($sql);
$ex_jpy = $result['rate'];

$sql = "
select dc.wr_name as delivery_company_name,
       A.*
from g5_sales3_list A
         left join g5_write_product as wp on wp.wr_id = A.wr_product_id
         left join g5_delivery_company as dc on dc.wr_code = A.wr_delivery
where {$sql_where}
group by A.wr_ori_order_num
order by A.wr_domain, A.wr_date4 desc
";

$result = sql_query($sql);

$list = [];

while ($row = sql_fetch_array($result)) {
  $domain = $row['wr_domain'];
  $wr_date4 = $row['wr_date4'];

  $wr_delivery_currency = $row['wr_delivery'] == "1021" ? "JPY" : "KRW";

  if (!$row['wr_delivery_oil']) {
    $country_dcode = sql_fetch("SELECT wr_code AS code FROM g5_country WHERE code_2 = '{$row['wr_deli_country']}'");
    $country = $country_dcode['code'];

    $sql = "SELECT {$country} AS price, cust_code, weight_code,B.code_percent FROM g5_shipping_price A
                          LEFT OUTER JOIN g5_code_list B ON B.code_type='3' AND B.code_value=A.cust_code
                          WHERE weight_code >= {$row['wr_weight2']} and {$country} != 0 and cust_code = '{$row['wr_delivery']}'  group by cust_code order by price asc";

    $result2 = sql_fetch($sql);

    $wr_delivery_oil_percent = $result2['code_percent'];
    $wr_delivery_oil = $result2['price'] * $wr_delivery_oil_percent / 100;

    $row['wr_delivery_oil'] = $wr_delivery_oil;
  }

  if ($wr_delivery_currency == 'JPY') {
    $row['wr_delivery_fee'] = $row['wr_delivery_fee'] * $ex_jpy;
    $row['wr_delivery_fee2'] = $row['wr_delivery_fee2'] * $ex_jpy;
    $row['wr_delivery_oil'] = $row['wr_delivery_oil'] * $ex_jpy;
  }

  $total_fee = $row['wr_delivery_oil'] + $row['wr_delivery_fee'] + $row['wr_delivery_fee2'];

  $list[$domain][$wr_date4]['wr_date4'] = $row['wr_date4'];
  if ($list[$domain][$wr_date4]['sum'] > 0) {
    $list[$domain][$wr_date4]['sum'] += $total_fee;
  } else {
    $list[$domain][$wr_date4]['sum'] = $total_fee;
  }
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
      <h2 class="board_tit">물류비 현황</h2>
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
        <h2 style="padding-bottom:10px; font-size:20px; text-align:center">물류비 현황</h2>
        <div class="tbl_head01 tbl_wrap">
          <table>
            <thead>
            <tr>
              <th style="width: 120px;">도메인</th>
              <th>출고일자</th>
              <th>배송비 총액(₩)</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($list) > 0) { ?>
              <?php
              $sum_delivery_fee = 0;

              $tr_index = 0;

              foreach ($list as $k => $v) {
                $i = 0;
                $tr = $tr_index++ % 2 === 0 ? "even_tr" : "odd_tr";
                $cnt = count($v);

                ?>
                <?php foreach ($v as $k2 => $item) {
                  $sum_delivery_fee += $item['sum'];

                  ?>
                  <tr class="<?= $tr ?>">
                    <?php if ($i == 0) {
                      ?>
                      <td align="center" rowspan="<?= $cnt ?>"><?= $k ?></td>
                      <?php
                    } ?>
                    <td align="center"><?= $item['wr_date4'] ?></td>
                    <td align="center"><?= number_format($item['sum']) ?></td>
                  </tr>
                  <?
                  $i++;
                }
                ?>
              <?php } ?>
              <tr>
                <td colspan="2" align="right"><strong>합계</strong></td>
                <td align="center"><?= number_format($sum_delivery_fee) ?></td>
              </tr>
            <?php } else { ?>

            <?php } ?>
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
          <select name="wr_domain">
            <option value="">도메인 선택</option>
            <?php echo get_domain_option($wr_domain) ?>
          </select>
        </div>

        <div style="margin-bottom: 15px; width: 100%;">
          <label for="stx" style="font-weight:bold">매출일자 조회<strong class="sound_only"> 필수</strong></label>
          <div class="sch_bar" style="margin-top:3px">
            <input type="date" name="date1" value="<?php echo $date1 ?>" required class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
            <span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
            <input type="date" name="date2" value="<?php echo $date2 ?>" required class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
          </div>
        </div>

        <button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
        <button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat"
                                                                                                                                                             aria-hidden="true"></i> 검색초기화
        </button>
        <button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
      </form>
    </fieldset>
    <div class="bo_sch_bg"></div>
  </div>
  <script>
    $(document).ready(function () {
      $('.search_sel').select2();
    });
    jQuery(function ($) {
      // 게시판 검색
      $(".btn_bo_sch").on("click", function () {
        $(".bo_sch_wrap").toggle();
      })
      $('.bo_sch_bg, .bo_sch_cls').click(function () {
        $('.bo_sch_wrap').hide();
      });
    });
  </script>
  <script>


    $(function () {
      $(".ALL_calc_OK").bind("click", function () {
        var chk = $(this).is(":CHECKED");
        $("input[name='wr_cal_ok_arr[]']").prop("checked", chk);
        $(".ALL_calc_OK").prop("checked", chk);
      });

      $(".ALL_calc_CANCEL").bind("click", function () {
        var chk = $(this).is(":CHECKED");
        $("input[name='wr_cal_can_arr[]']").prop("checked", chk);
        $(".ALL_calc_CANCEL").prop("checked", chk);
      });

      $("input[name='wr_cal_ok_arr[]']").bind("click", function () {
        var chk = true;
        $("input[name='wr_cal_ok_arr[]']").each(function () {
          if ($(this).is(":checked") == false) {
            chk = false;
          }
        });
        $(".ALL_calc_OK").prop("checked", chk);
      });

      $("input[name='wr_cal_can_arr[]']").bind("click", function () {
        var chk = true;
        $("input[name='wr_cal_can_arr[]']").each(function () {
          if ($(this).is(":checked") == false) {
            chk = false;
          }
        });
        $(".ALL_calc_CANCEL").prop("checked", chk);
      });

      $('#sorting_box').bind('change', function () {
        let sort = $(this).val();

        if (sort == "default") {
          location.href = '?sst=wr_id&sod=desc&stx2=<?php echo $stx2?>';
        } else if (sort == "up") {
          location.href = '?sst=stock&sod=desc&stx2=<?php echo $stx2?>';
        } else if (sort == "down") {
          location.href = '?sst=stock&sod=asc&stx2=<?php echo $stx2?>';
        }
      });
    });

    function acc3_frm_submit(form) {
      return true;
    }

    // 모달 닫기
    function close_modal() {
      $(".bo_sch_bg").hide();
      $(".modal_view").hide();
      $("#modal_view_calc").empty();
    }
  </script>


<?php
include_once(G5_THEME_PATH . '/tail.php');