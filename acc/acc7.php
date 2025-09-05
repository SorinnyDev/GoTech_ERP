<?php
include_once('./_common.php');

include_once(G5_THEME_PATH . '/head.php');

if (!$st_date) {
  $st_date = date("2025-01-01");
}

if (!$ed_date) {
  $ed_date = date("Y-m-d");
}

$sql_where = " ";
$sql_wd_where = " ";
$sql_order_by = " ";
if ($sc_orderer) {
  $orderer_arr = explode("|", $sc_orderer);
  $type = $orderer_arr[0];
  $code = $orderer_arr[1];

  $sql_where .= " AND sl.wr_orderer = '" . $code . "' ";
  $sql_wd_where .= " AND wd.wd_orderer = '$code'";
}

if (!isset($search_query_date)) {
  $search_query_date = 'wr_date3';
} else if (!in_array($search_query_date, ['wr_date3', 'wr_date2'])) {
  alert('잘못된 접근입니다. (query date error)', './acc7.php');
}

if ($search_query_date === 'wr_date3') {
  $sql_where .= " and sl.wr_date3 BETWEEN '$st_date' AND '$ed_date' ";
  $sql_order_by .= " order by cl.code_name, wr_date3 desc ";
} else {
  $sql_where .= " and sl.wr_date2 BETWEEN '$st_date' AND '$ed_date' ";
  $sql_order_by .= " order by cl.code_name, wr_date2 desc ";
}

$list = array();
if ($sc_orderer == "" || $type == "orderer") {
  $sql = "
    select cl.code_name as orderer_name,
           sl.wr_date2,
           sl.wr_date3,
           sum(sl.wr_order_total) as order_total,
           sum(IFNULL(wr_warehouse_price, 0)) as pay_price,
           sum(wr_misu) as misu_total,
           (select sum(amount) as amount from g5_acc2_carryover_amount as ca where ca.wr_orderer = sl.wr_orderer and base_date >= '$st_date') as carryover
    from g5_sales2_list as sl
    inner join g5_code_list as cl on cl.idx = sl.wr_orderer
    left join g5_sales1_list as sl1 on sl1.wr_order_num = sl.wr_order_num
    left join g5_sales_metadata as sm on sm.entity_type = 'g5_sales1_list' and entity_id = sl1.seq and `key` = 'code_card'
    WHERE sl.wr_direct_use = '0' {$sql_where}
    group by sl.wr_orderer
    {$sql_order_by}
	";

  $rs = sql_query($sql);
  while ($row = sql_fetch_array($rs)) {
    $orderer_name = $row['orderer_name'];
    $base_date = $row[$search_query_date];
    $list[$orderer_name]['data'][$base_date] = $row;
  }
}

$query = "
  select *, cl.code_name as orderer_name from g5_acc2_wd as wd
  left join g5_code_list as cl on cl.idx = wd.wd_orderer
  where wd_date between '{$st_date}' and '{$ed_date}' {$sql_wd_where}
  order by wd_date
  ";

$wd_list = sql_fetch_all($query);


# 매입처 원장 입출금 내역 리스트 합치기
foreach ($wd_list as $k => $v) {
  $orderer = $v['orderer_name'];

  if (isset($list[$orderer]['data']) && count($list[$orderer]['data']) > 0) {
    reset($list[$orderer]['data']);
    $first_key = key($list[$orderer]['data']);

    $list[$orderer]['data'][$first_key]['pay_price'] += $v['wd_price'];
    $list[$orderer]['data'][$first_key]['misu_total'] -= $v['wd_price'];
  } else {
    $list[$orderer]['data']['0000-00-00'] = [
      'orderer_name' => $orderer,
      'wr_date3' => '0000-00-00',
      'order_total' => 0,
      'pay_price' => $v['wd_price'],
      'misu_total' => -$v['wd_price'],
      'carryover' => 0,
    ];
  }
}

foreach ($list as $k => $v) {
  $orderer_cnt = count($v['data']);
  $list[$k]['cnt'] = $orderer_cnt;
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
      <h2 class="board_tit">외상매입금 현황</h2>
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
        <h2 style="padding-bottom:10px; font-size:20px; text-align:center">외상매입금 현황</h2>
        <div class="tbl_head01 tbl_wrap">
          <table>
            <thead>
            <tr>
              <th>매입처</th>
              <th style="width: 180px;"><?= $search_query_date === 'wr_date3' ? '매입일자' : '발주일자' ?></th>
              <th>매입금액</th>
              <th>지급액</th>
              <th>미지급액</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($list) > 0) { ?>
              <?php
              $sum_order_total = 0;
              $sum_pay_price = 0;
              $sum_misu_total = 0;

              $tr_index = 0;

              foreach ($list as $k => $v) {
                $i = 0;
                $tr = $tr_index++ % 2 === 0 ? "even_tr" : "odd_tr";

                ?>
                <?php foreach ($v['data'] as $k2 => $v2) {
                  $v2['misu_total'] = $v2['misu_total'] + $v2['carryover'];
                  $sum_order_total += $v2['order_total'];
                  $sum_pay_price += $v2['pay_price'];
                  $sum_misu_total += $v2['misu_total'];


                  ?>
                  <tr class="<?= $tr ?>">
                    <?php if ($i == 0) {
                      ?>
                      <td align="center" rowspan="<?= $v['cnt'] ?>"><?= $k ?></td>
                      <?php
                    } ?>
                    <td align="center"><?= $st_date ?> ~ <?= $ed_date ?></td>
                    <td align="center"><?= number_format($v2['order_total']) ?></td>
                    <td align="center"><?= number_format($v2['pay_price']) ?></td>
                    <td align="center" title="보정금액: <?=number_format($v2['carryover'])?>원"><?= number_format($v2['misu_total']) ?></td>
                  </tr>
                  <?
                  $i++;
                }
                ?>
              <?php } ?>
              <tr>
                <td colspan="2" align="right"><strong>합계</strong></td>
                <td align="center"><?= number_format($sum_order_total) ?></td>
                <td align="center"><?= number_format($sum_pay_price) ?></td>
                <td align="center"><?= number_format($sum_misu_total) ?></td>
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

        <div style="margin-bottom: 15px; width: 100%;">
          <select name="sc_orderer" class="search_sel" style="width:100%;margin-bottom:15px">
            <option value="">매입처</option>
            <?php
            $code_list = get_code_list('5');
            foreach ($code_list as $key => $value) {
              echo "<option value=\"orderer|{$value['idx']}\" " . get_selected("orderer|" . $value['idx'], $sc_orderer) . ">{$value['code_name']}</option>";
            }
            ?>
          </select>
        </div>

        <div style="margin-bottom: 15px; width: 100%;">
          <label for="search_query_date" style="font-weight:bold; margin-top: 15px;">일자 기준</label>
          <select name="search_query_date" id="search_query_date" class="frm_input" style="background:#FFFFFF; width: 100%;">
            <option value="wr_date3" <?= get_selected('wr_date3', $search_query_date) ?>>매입 일자</option>
            <option value="wr_date2" <?= get_selected('wr_date2', $search_query_date) ?>>발주 일자</option>
          </select>
        </div>


        <div style="margin-bottom: 15px; width: 100%;">
          <label for="stx" style="font-weight:bold">일자 조회<strong class="sound_only"> 필수</strong></label>
          <div class="sch_bar" style="margin-top:3px; display: flex;">
            <input type="date" name="st_date" value="<?php echo $st_date ?>" required class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
            <span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
            <input type="date" name="ed_date" value="<?php echo stripslashes($ed_date) ?>" required class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
          </div>
        </div>

        <button type="button" onclick="search_submit();" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
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

    function search_submit() {
      const form = document.querySelector('[name=fsearch]');
      const st_date_input = form.querySelector('[name=st_date]');
      const st_date = new Date(st_date_input.value);
      const min_date = new Date('2025-01-01');

      if (st_date < min_date) {
        alert('조회 시작일은 2025-01-01 이후로 설정 해야합니다.');
        return;
      }

      form.submit();
    }
  </script>


<?php
include_once(G5_THEME_PATH . '/tail.php');