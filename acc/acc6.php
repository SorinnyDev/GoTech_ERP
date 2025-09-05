<?php
include_once('./_common.php');

include_once(G5_THEME_PATH . '/head.php');

if (!$st_date) {
  $st_date = date("Y-m-01");
}
if (!$ed_date) {
  $ed_date = date("Y-m-t");
}

$sql_where = "";
if ($sc_domain === 'ACSUM') {
  $sql_where .= " and A.wr_domain IN ('AC', 'ACF') ";
} else if ($sc_domain == 'ACDSUM') {
  $sql_where .= " and A.wr_domain IN ('AC-CAD', 'ACF-CAD_I', 'ACF-CAD_F', 'ACF-CAD') ";
} else if ($sc_domain == 'Q10JPSUM') {
  $sql_where .= " and A.wr_domain IN ('qoo10-jp', 'qoo10jp-1') ";
} else if ($sc_domain) {
  $sql_where .= " AND A.wr_domain = '" . $sc_domain . "' ";
}

$list = array();

$sql = "
SELECT A.wr_domain,
       A.wr_date4,
       wr_currency,
       SUM((A.wr_singo))                                                                            AS sum_singo,
       REPLACE(IF(A.wr_exchange_rate = 0, C.rate, A.wr_exchange_rate), ',', '')                         AS wr_exchange_rate,
       SUM((A.wr_singo *
                 (CASE
                      WHEN A.wr_currency = 'JPY' THEN
                          REPLACE(IF(A.wr_exchange_rate = 0, C.rate, A.wr_exchange_rate), ',', '') / 100
                      ELSE
                          REPLACE(IF(A.wr_exchange_rate = 0, C.rate, A.wr_exchange_rate), ',', '')
                  END)))                                                                               AS sum_exchange_singo,
       SUM((IF(A.wr_cal_chk = 'Y', A.wr_singo, 0) *
                 (CASE
                      WHEN A.wr_currency = 'JPY' THEN
                          REPLACE(IF(A.wr_exchange_rate = 0, C.rate, A.wr_exchange_rate), ',', '') / 100
                      ELSE
                          REPLACE(IF(A.wr_exchange_rate = 0, C.rate, A.wr_exchange_rate), ',', '')
                  END)))                                                                               AS sum_Y_price,
       SUM((IF(A.wr_cal_chk = 'N', A.wr_singo, 0) *
                 (CASE
                      WHEN A.wr_currency = 'JPY' THEN
                          REPLACE(IF(A.wr_exchange_rate = 0, C.rate, A.wr_exchange_rate), ',', '') / 100
                      ELSE
                          REPLACE(IF(A.wr_exchange_rate = 0, C.rate, A.wr_exchange_rate), ',', '')
                  END)))                                                                               AS sum_N_price,
       (SELECT SUM(amount)
        FROM g5_acc3_carryover_amount AS ca
        WHERE ca.sc_domain = A.wr_domain
          AND ca.base_date = A.wr_date4)                                                                AS carryover
FROM g5_sales3_list A
         LEFT OUTER JOIN g5_write_product_fee B
                         ON B.wr_id = A.wr_product_id
                         AND B.domain = A.wr_domain
                         AND B.warehouse = A.wr_warehouse
         LEFT OUTER JOIN g5_excharge C ON C.ex_eng = A.wr_currency
WHERE A.wr_release_use = '1'
  AND A.wr_date4 BETWEEN '$st_date' AND '$ed_date'
  {$sql_where}
GROUP BY A.wr_domain, A.wr_date4
ORDER BY wr_domain ASC, wr_date4 ASC;

";
$rs = sql_query($sql);
while ($row = sql_fetch_array($rs)) {
  $wr_domain = $row['wr_domain'];
  $wr_date = $row['wr_date4'];
  $list[$wr_domain]['data'][$wr_date] = $row;
}

foreach ($list as $domain => $item) {
  if ($domain === 'AC' || $domain === 'ACF') {
    foreach ($item['data'] as $date => $v) {
      if (!isset($list['AC+ACF']['data'][$date])) {
        $list['AC+ACF']['data'][$date] = [
          'wr_domain' => 'AC + ACF',
          'sum_exchange_singo' => 0,
          'sum_Y_price' => 0,
          'sum_N_price' => 0,
        ];
      }

      $list['AC+ACF']['data'][$date]['sum_exchange_singo'] += $v['sum_exchange_singo'];
      $list['AC+ACF']['data'][$date]['sum_Y_price'] += $v['sum_Y_price'];
      $list['AC+ACF']['data'][$date]['sum_N_price'] += $v['sum_N_price'];
    }
  } else if (in_array($domain, ['AC-CAD', 'ACF-CAD_I', 'ACF-CAD_F', 'ACF-CAD'])) {
    foreach ($item['data'] as $date => $v) {
      if (!isset($list['AC-CAD+ACF-CAD']['data'][$date])) {
        $list['AC-CAD+ACF-CAD']['data'][$date] = [
          'wr_domain' => 'AC-CAD + ACF-CAD',
          'sum_exchange_singo' => 0,
          'sum_Y_price' => 0,
          'sum_N_price' => 0,
        ];
      }

      $list['AC-CAD+ACF-CAD']['data'][$date]['sum_exchange_singo'] += $v['sum_exchange_singo'];
      $list['AC-CAD+ACF-CAD']['data'][$date]['sum_Y_price'] += $v['sum_Y_price'];
      $list['AC-CAD+ACF-CAD']['data'][$date]['sum_N_price'] += $v['sum_N_price'];
    }

  } else if (in_array($domain, ['qoo10-jp', 'qoo10jp-1'])) {
    foreach ($item['data'] as $date => $v) {
      if (!isset($list['QOO10jp+QOO10JP-1']['data'][$date])) {
        $list['QOO10jp+QOO10JP-1']['data'][$date] = [
          'wr_domain' => 'QOO10jp + QOO10JP-1',
          'sum_exchange_singo' => 0,
          'sum_Y_price' => 0,
          'sum_N_price' => 0,
        ];
      }
      $list['QOO10jp+QOO10JP-1']['data'][$date]['sum_exchange_singo'] += $v['sum_exchange_singo'];
      $list['QOO10jp+QOO10JP-1']['data'][$date]['sum_Y_price'] += $v['sum_Y_price'];
      $list['QOO10jp+QOO10JP-1']['data'][$date]['sum_N_price'] += $v['sum_N_price'];
    }

  }

  if (!$sc_domain) {
    if (in_array($domain, ['AC', 'AC-CAD', 'ACF', 'ACF-CAD', 'qoo10jp-1', 'qoo10-jp'])) {
      unset($list[$domain]);
    }
  }
}


foreach ($list as $k => $v) {
  $domain_cnt = count($v['data']);
  $list[$k]['cnt'] = $domain_cnt;
}

if ($sc_domain === 'ACSUM') {
  unset($list['AC']);
  unset($list['ACF']);
} else if ($sc_domain == 'ACDSUM') {
  unset($list['AC-CAD']);
  unset($list['ACF-CAD']);
  unset($list['ACF-CAD_I']);
  unset($list['ACF-CAD_F']);
} else if ($sc_domain == 'Q10JPSUM') {
  unset($list['qoo10-jp']);
  unset($list['qoo10jp-1']);
} else if ($sc_domain === 'AC') {
  unset($list['AC+ACF']);
} else if ($sc_domain === 'ACF') {
  unset($list['AC+ACF']);
} else if ($sc_domain === 'AC-CAD') {
  unset($list['AC-CAD+ACF-CAD']);
} else if ($sc_domain === 'ACF-CAD') {
  unset($list['AC-CAD+ACF-CAD']);
} else if ($sc_domain === 'ACF-CAD') {
  unset($list['AC-CAD+ACF-CAD']);
} else if ($sc_domain === 'ACF-CAD_F') {
  unset($list['AC-CAD+ACF-CAD']);
} else if ($sc_domain === 'qoo10-jp') {
  unset($list['QOO10jp+QOO10JP-1']);
} else if ($sc_domain === 'qoo10jp-1') {
  unset($list['QOO10jp+QOO10JP-1']);
}

ksort($list);

# 플랫폼별 합계

foreach ($list as $k => &$v) {
  $total_sum_singo = 0;
  $total_sum_Y = 0;
  $total_sum_N = 0;
  foreach ($v['data'] as $j => $item) {
    $total_sum_singo += $item['sum_exchange_singo'];
    $total_sum_Y += $item['sum_Y_price'];
    $total_sum_N += $item['sum_N_price'];
  }

  $v['data']['platform_total'] = [
    'total_singo' => $total_sum_singo,
    'total_sum_Y' => $total_sum_Y,
    'total_sum_N' => $total_sum_N
  ];
}

function getExchangeRate($currency)
{
  $exchangeRate = 1; // 기본 환율 (없을 경우 1로 설정)

  $sql = "select * from g5_excharge";

  $result = sql_fetch_all($sql);
  foreach ($result as $row) {
    if ($row['ex_eng'] === $currency) {
      $rate = str_replace(",", "", $row['rate']);
      if ($currency === "JPY") {
        $rate *= 0.01;
      }
      $exchangeRate = $rate;
      break;
    }

  }

  return $exchangeRate;
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
  </style>
  <div id="bo_list">
    <div class="bo_list_innr">
      <h2 class="board_tit">외상매출금 현황</h2>
      <form name="fboardlist" id="fboardlist" action="<?php echo G5_URL; ?>/acc/acc3_list_update.php" onsubmit="return acc3_frm_submit(this);" method="post">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx2 ?>">
        <input type="hidden" name="spt" value="<?php echo $spt ?>">
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">
        <input type="hidden" name="qstr" value="<?= $qstr ?>">
        <input type="hidden" name="sw" value="">


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
            <li>
              <button type="button" class="btn_b01 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i>검색</button>
            </li>
          </ul>
        </div>
        <h2 style="padding-bottom:10px; font-size:20px; text-align:center">외상매출금 현황</h2>
        <div class="tbl_head01 tbl_wrap">
          <table>
            <thead style="position:sticky;top:0;">
            <tr>
              <th>매출처</th>
              <th>일자</th>
              <th>매출금액(KRW)</th>
              <th>입금액(KRW)</th>
              <th>외상매출금(KRW)</th>
            </tr>
            </thead>
            <tbody>
            <?php if (sql_num_rows($rs)) {

              $total_singo = 0;
              $total_Y_price = 0;
              $total_N_price = 0;
              unset($v);
              foreach ($list as $k => $v) {
                $i = 0;
                ?>
                <?php foreach ($v['data'] as $k2 => $v2) {
                  $v2['sum_N_price'] = $v2['sum_N_price'] + $v2['carryover'];
                  $total_singo = $total_singo + $v2['sum_exchange_singo'];
                  $total_Y_price = $total_Y_price + $v2['sum_Y_price'];
                  $total_N_price = $total_N_price + $v2['sum_N_price'];

                  ?>
                  <?php if ($i == $v['cnt']) { ?>
                    <tr class="odd_tr">
                      <td align="center"></td>
                      <td align="center">합계</td>
                      <td align="center"><?= number_format($v2['total_singo']) ?></td>
                      <td align="center"><?= number_format($v2['total_sum_Y']) ?></td>
                      <td align="center"><?= number_format($v2['total_sum_N']) ?></td>
                    </tr>
                    <?php
                  } else { ?>
                    <tr>
                      <?php if ($i == 0) {
                        ?>
                        <td rowspan="<?= $v['cnt'] ?>" align="center"><?= $v2['wr_domain'] ?></td>
                        <?php
                      } ?>
                      <td align="center"><?= $k2 ?></td>
                      <td align="center"><?= number_format($v2['sum_exchange_singo']) ?></td>
                      <td align="center"><?= number_format($v2['sum_Y_price']) ?></td>
                      <td align="center" title="보정금액: <?= number_format($v2['carryover']) ?>원"><?= number_format($v2['sum_N_price']) ?></td>
                    </tr>

                    <?php
                  }
                  $i++;
                }
              }
              ?>
              <tr>
                <td colspan="2" align="right">합계</td>
                <td align="center"><?= number_format($total_singo) ?></td>
                <td align="center"><?= number_format($total_Y_price) ?></td>
                <td align="center"><?= number_format($total_N_price) ?></td>
              </tr>
            <?php } else { ?>

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
  <!--// 주문건 확인 팝업-->

  <div class="bo_sch_wrap">
    <fieldset class="bo_sch" style="padding:10px">
      <h3>검색</h3>
      <form name="fsearch" method="get">

        <select name="sc_domain" class="search_sel" style="width:100%;margin-bottom:15px">
          <option value="">==전체 매출처==</option>
          <option value="ACSUM" <?= get_selected("ACSUM", $sc_domain) ?>>AC + ACF</option>
          <option value="ACDSUM" <?= get_selected("ACDSUM", $sc_domain) ?>>AC-CAD + ACF-CAD</option>
          <option value="Q10JPSUM" <?= get_selected("Q10JPSUM", $sc_domain) ?>>QOO10jp + QOO10JP-1</option>
          <?
          $code_list = get_code_list('4');
          foreach ($code_list as $key => $value) {
            echo "<option value=\"{$value['code_value']}\" " . get_selected($value['code_value'], $sc_domain) . ">{$value['code_name']}</option>";
          }
          ?>

        </select>

        <label for="stx" style="font-weight:bold">일자 조회<strong class="sound_only"> 필수</strong></label>
        <div class="sch_bar" style="margin-top:3px">

          <input type="date" name="st_date" value="<?php echo $st_date ?>" required class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
          <span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
          <input type="date" name="ed_date" value="<?php echo stripslashes($ed_date) ?>" required class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">

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