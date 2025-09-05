<?php
include_once('./_common.php');

include_once(G5_THEME_PATH . '/head.php');

if (!$st_date) {
  $st_date = date("Y-m-d");
}

if (!$ed_date) {
  $ed_date = date("Y-m-d");
}
$sql_where = "";
if ($sc_domain) {
  $sql_where .= " AND G2.wr_domain = '" . $sc_domain . "'";
}

if ($code_stx) {
  $code_stx = trim($code_stx);
  $sql = "select wr_id from g5_write_product where ( wr_subject LIKE '%{$code_stx}%' or wr_1 = '" . addslashes($code_stx) . "' or wr_27 = '" . addslashes($code_stx) . "' or wr_28 = '" . addslashes($code_stx) . "' or wr_29 = '" . addslashes($code_stx) . "' or wr_30 = '" . addslashes($code_stx) . "' or wr_31 = '" . addslashes($code_stx) . "') ";
  $wr_product_id = $item['wr_id'];
  $sql_where .= " AND G2.wr_product_id IN({$sql})";
}

$wr_18_sql = "";

if ($wr_18) {
  $wr_18_sql = " AND wr_domain = '{$wr_18}' ";
}

$list = array();

# 입고 조회
$sql = "SELECT G2.wr_date3,G2.wr_domain,wr_orderer,(SELECT code_name FROM g5_code_list WHERE idx=G2.wr_orderer) as wr_orderer_nm,G2.wr_product_id,WP.wr_subject,SUM(G2.wr_order_ea) AS in_ea, wr_order_price \n";
$sql .= "	,SUM(GTW.wr_stock) AS temp_stock";
$sql .= "	,IFNULL(WP.wr_5,'') AS code1 \n";
$sql .= "	,IFNULL(WP.wr_4,'') AS code2 \n";
$sql .= "	,IFNULL(WP.wr_6,'') AS code3 \n";
$sql .= "	,IFNULL(WP.wr_1,'') AS sku1 \n";
$sql .= "	,IFNULL(WP.wr_27,'') AS sku2 \n";
$sql .= "	,IFNULL(WP.wr_28,'') AS sku3 \n";
$sql .= "	,IFNULL(WP.wr_29,'') AS sku4 \n";
$sql .= "	,IFNULL(WP.wr_30,'') AS sku5 \n";
$sql .= "	,IFNULL(WP.wr_31,'') AS sku6 \n";
$sql .= "	,IFNULL(WP.wr_32_real,0) AS kor_ea \n";
$sql .= "	,IFNULL(WP.wr_36_real,0) AS usa_ea \n";
$sql .= "	,IFNULL(WP.wr_37_real,0) AS temp_ea \n";
$sql .= "	,IFNULL(WP.wr_40_real,0) AS re_kor_ea \n";
$sql .= "	,IFNULL(WP.wr_41_real,0) AS re_usa_eea \n";
$sql .= "	,IFNULL(WP.wr_42_real,0) AS fba_ea \n";
$sql .= "	,IFNULL(WP.wr_43_real,0) AS w_fba_ea \n";
$sql .= "	,IFNULL(WP.wr_44_real,0) AS u_fba_ea \n";
$sql .= "FROM g5_sales2_list G2 \n";
$sql .= "LEFT OUTER JOIN( \n";
$sql .= "	SELECT * FROM g5_write_product  \n";
$sql .= ")WP ON WP.wr_id=G2.wr_product_id \n";
$sql .= "LEFT OUTER JOIN g5_temp_warehouse GTW ON GTW.sales2_id=G2.seq \n";
$sql .= "WHERE wr_direct_use = '0' AND wr_date3 <> '' AND wr_date3 BETWEEN '" . $st_date . "' AND '" . $ed_date . "' {$sql_where} \n";
$sql .= "GROUP BY wr_date3,wr_domain,wr_orderer,wr_product_id \n";
$sql .= "ORDER BY wr_date3 ASC,wr_domain ASC,wr_orderer ASC, wr_product_id ASC";

$rs = sql_query($sql);
while ($row = @sql_fetch_array($rs)) {
  $wr_date3 = $row['wr_date3'];
  $wr_product_id = $row['wr_product_id'];
  $wr_code = $row['code1'];
  if (!$wr_code) {
    $wr_code = $row['code2'];
    if (!$wr_code) {
      $wr_code = $row['code3'];
    }
  }
  $wr_domain = $row['wr_domain'];
  if (!$wr_domain) {
    $wr_domain == "ETC";
  }

  $wr_orderer = $row['wr_orderer_nm'];

  # 대표코드
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['code1'] = $row['code1'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['code2'] = $row['code2'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['code3'] = $row['code3'];

  # 상품정보
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['wr_subject'] = $row['wr_subject'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['code'] = $wr_code;
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['sku1'] = $row['sku1'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['sku2'] = $row['sku2'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['sku3'] = $row['sku3'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['sku4'] = $row['sku4'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['sku5'] = $row['sku5'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['sku6'] = $row['sku6'];

  # 상품 실재고
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['kor_ea'] = $row['kor_ea'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['usa_ea'] = $row['usa_ea'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['temp_ea'] = $row['temp_ea'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['re_kor_ea'] = $row['re_kor_ea'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['re_usa_ea'] = $row['re_usa_ea'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['fba_ea'] = $row['fba_ea'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['w_fba_ea'] = $row['w_fba_ea'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['u_fba_ea'] = $row['u_fba_ea'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['total_ea'] = $row['kor_ea'] + $row['usa_ea'] + $row['temp_ea'] + $row['fba_ea'] + $row['w_fba_ea'] + $row['u_fba_ea'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['total_re_ea'] = $row['re_kor_ea'] + $row['re_usa_ea'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['in_ea'] = $row['in_ea'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['temp_stock'] = $row['temp_stock'];
  $list[$wr_date3]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['wr_order_price'] = $row['wr_order_price'];

}

# 출고 조회
$sql = "SELECT wr_date4,wr_domain,wr_orderer,(SELECT code_name FROM g5_code_list WHERE idx=G2.wr_orderer) as wr_orderer_nm,wr_product_id,WP.wr_subject,SUM(wr_ea) AS out_ea \n";
$sql .= "	,IFNULL(WP.wr_5,'') AS code1 \n";
$sql .= "	,IFNULL(WP.wr_4,'') AS code2 \n";
$sql .= "	,IFNULL(WP.wr_6,'') AS code3 \n";
$sql .= "	,IFNULL(WP.wr_1,'') AS sku1 \n";
$sql .= "	,IFNULL(WP.wr_27,'') AS sku2 \n";
$sql .= "	,IFNULL(WP.wr_28,'') AS sku3 \n";
$sql .= "	,IFNULL(WP.wr_29,'') AS sku4 \n";
$sql .= "	,IFNULL(WP.wr_30,'') AS sku5 \n";
$sql .= "	,IFNULL(WP.wr_31,'') AS sku6 \n";
$sql .= "	,IFNULL(WP.wr_32_real,0) AS kor_ea \n";
$sql .= "	,IFNULL(WP.wr_36_real,0) AS usa_ea \n";
$sql .= "	,IFNULL(WP.wr_37_real,0) AS temp_ea \n";
$sql .= "	,IFNULL(WP.wr_40_real,0) AS re_kor_ea \n";
$sql .= "	,IFNULL(WP.wr_41_real,0) AS re_usa_eea \n";
$sql .= "	,IFNULL(WP.wr_42_real,0) AS fba_ea \n";
$sql .= "	,IFNULL(WP.wr_43_real,0) AS w_fba_ea \n";
$sql .= "	,IFNULL(WP.wr_44_real,0) AS u_fba_ea \n";
$sql .= "FROM g5_sales3_list G2 \n";
$sql .= "LEFT OUTER JOIN( \n";
$sql .= "	SELECT * FROM g5_write_product  \n";
$sql .= ")WP ON WP.wr_id=G2.wr_product_id \n";
$sql .= "WHERE wr_release_use = '1' AND wr_date4 BETWEEN '" . $st_date . "' AND '" . $ed_date . "' {$sql_where} \n";
$sql .= "GROUP BY wr_date4,wr_domain,wr_orderer,wr_product_id \n";
$sql .= "ORDER BY wr_date4 ASC,wr_domain ASC,wr_orderer ASC, wr_product_id ASC";

$rs = sql_query($sql);
while ($row = @sql_fetch_array($rs)) {
  $wr_date4 = $row['wr_date4'];
  $wr_product_id = $row['wr_product_id'];
  $wr_code = $row['code1'];
  if (!$wr_code) {
    $wr_code = $row['code2'];
    if (!$wr_code) {
      $wr_code = $row['code3'];
    }
  }
  $wr_domain = $row['wr_domain'];
  if (!$wr_domain) {
    $wr_domain == "ETC";
  }
  $wr_orderer = $row['wr_orderer_nm'];
  if (!$wr_orderer) {
    $wr_orderer = "";
  }

  # 대표코드
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['code1'] = $row['code1'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['code2'] = $row['code2'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['code3'] = $row['code3'];

  # SKU
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['wr_subject'] = $row['wr_subject'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['code'] = $wr_code;
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['sku1'] = $row['sku1'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['sku2'] = $row['sku2'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['sku3'] = $row['sku3'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['sku4'] = $row['sku4'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['sku5'] = $row['sku5'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['sku6'] = $row['sku6'];

  # 상품 실재고
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['kor_ea'] = $row['kor_ea'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['usa_ea'] = $row['usa_ea'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['temp_ea'] = $row['temp_ea'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['re_kor_ea'] = $row['re_kor_ea'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['re_usa_ea'] = $row['re_usa_ea'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['fba_ea'] = $row['fba_ea'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['w_fba_ea'] = $row['w_fba_ea'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['u_fba_ea'] = $row['u_fba_ea'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['total_ea'] = $row['kor_ea'] + $row['usa_ea'] + $row['temp_ea'] + $row['fba_ea'] + $row['w_fba_ea'] + $row['u_fba_ea'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['total_re_ea'] = $row['re_kor_ea'] + $row['re_usa_ea'];
  $list[$wr_date4]['data'][$wr_product_id]['data'][$wr_domain]['data'][$wr_orderer]['out_ea'] = $row['out_ea'];

}
# 병팝계산
foreach ($list as $key => $val) {
  $rowCnt = 0;
  foreach ($val['data'] as $key2 => $val2) {
    $row2Cnt = 0;
    foreach ($val2['data'] as $key3 => $val3) {
      $row3Cnt = 0;
      foreach ($val3['data'] as $key4 => $val4) {
        $rowCnt++;
        $list[$key]['row_date'] = $rowCnt;
        $row2Cnt++;
        $list[$key]['data'][$key2]['row_product'] = $row2Cnt;
        $row3Cnt++;
        $list[$key]['data'][$key2]['data'][$key3]['row_domain'] = $row3Cnt;
      }
    }
  }
}

$keys = array_keys($list);
usort($keys, function ($a, $b) {
  return strtotime($b) - strtotime($a);
});

$sorted_list = [];
foreach ($keys as $key) {
  $sorted_list[$key] = $list[$key];
}

$list = $sorted_list;

?>

  <link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
  <style>
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

      .tbl_head01 tbody td.text_left {
          text-align: left;
      }

      .tbl_head01 tbody td.text_center {
          text-align: center;
      }

      .tbl_head01 tbody td.text_right {
          text-align: right;
      }
  </style>
  <div id="bo_list">
    <div class="bo_list_innr">
      <h2 class="board_tit">상품 수불 원장</h2>
      <form name="fboardlist" id="fboardlist" action="<?= G5_URL ?>/acc/acc1_list_update.php"
            onsubmit="return fboardlist_submit(this);" method="post">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx ?>">
        <input type="hidden" name="spt" value="<?php echo $spt ?>">
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">
        <input type="hidden" name="sw" value="">

        <?php if ($is_category) { ?>
          <nav id="bo_cate">
            <h2><?php echo($board['bo_mobile_subject'] ? $board['bo_mobile_subject'] : $board['bo_subject']) ?> 카테고리</h2>
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
            <li>
              <button type="button" class="btn_b01 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i>
                상품검색
              </button>
            </li>
          </ul>
        </div>
        <h2 style="padding-bottom:10px; font-size:20px; text-align:center">상품 수불 원장</h2>
        <div class="tbl_head01 tbl_wrap">
          <table>
            <thead style="position:sticky;top:0;">
            <tr>
              <th>일자</th>
              <th>대표코드</th>
              <th>SKU</th>
              <th width="500px;">상품명</th>
              <th>매출처</th>
              <th>매입처</th>
              <th>입고</th>
              <th>출고</th>
              <th>재고</th>
              <th>입고단가</th>
            </tr>
            </thead>
            <tbody>
            <?
            $tr_class = 0;

            $total_sum_in_ea = 0;
            $total_sum_out_ea = 0;
            foreach ($list as $key => $val) {
              $num1 = 0;
              $row_cnt = $val['row_date'];
              if ($tr_class % 2 == 0) {
                $tr = "even_tr";
              } else {
                $tr = "odd_tr";
              }
              ?>
              <? foreach ($val['data'] as $key2 => $val2) {
                $num2 = 0;
                $row2_cnt = $val2['row_product'];
                //$row2_cnt = 1;
                ?>
                <? foreach ($val2['data'] as $key3 => $val3) {
                  $num3 = 0;
                  $row3_cnt = $val3['row_domain'];
                  ?>
                  <? foreach ($val3['data'] as $key4 => $val4) {
                    $total_sum_in_ea += (int)$val4['in_ea'];
                    $total_sum_out_ea += (int)$val4['out_ea'];
                    ?>
                    <tr class="<?= $tr ?>">
                      <? if ($num1 == 0) { ?>
                        <td rowspan="<?= $row_cnt ?>" class="text_center"><?= $key ?></td>
                      <? } ?>
                      <? if ($num2 == 0) { ?>
                        <td rowspan="<?= $row2_cnt ?>" class="text_center"><?= $val4['code'] ?></td>
                        <td rowspan="<?= $row2_cnt ?>" class="text_center"><?= $val4['sku1'] ?></td>
                        <td rowspan="<?= $row2_cnt ?>" class="text_center"><?= $val4['wr_subject'] ?></td>
                      <? } ?>
                      <? if ($num3 == 0) { ?>
                        <td rowspan="<?= $row3_cnt ?>" class="text_center"><?= $key3 ?></td>
                      <? } ?>
                      <td class="text_center"><?= $key4 ?></td>
                      <td class="text_center"><?= (int)$val4['in_ea'] ?></td>
                      <td class="text_center"><?= (int)$val4['out_ea'] ?></td>
                      <? if ($num2 == 0) { ?>
                        <td rowspan="<?= $row2_cnt ?>" class="text_center">
                          <?php
                          $sql = "
                                                        SELECT SUM(G2.wr_order_ea) AS in_ea
                                                        FROM g5_sales2_list G2
                                                        WHERE wr_direct_use = '0'
                                                          AND wr_date3 <= '" . $key . "'
                                                          AND G2.wr_product_id = '" . $key2 . "'
                                                    ";

                          $total_in_ea = sql_fetch($sql);

                          $sql = "
                                                        SELECT SUM(wr_ea) AS out_ea
                                                        FROM g5_sales3_list G2
                                                        WHERE wr_release_use = '1'
                                                          AND wr_date4 <= '" . $key . "'
                                                          AND wr_product_id = '" . $key2 . "'
                                                        GROUP BY wr_product_id;
                                                    ";
                          $total_out_ea = sql_fetch($sql);

                          $total_stock = $total_in_ea['in_ea'] - $total_out_ea['out_ea']
                          ?>

                          <?= $total_stock ?>
                        </td>
                        <td rowspan="<?= $row2_cnt ?>" class="text_center">
                          <?= number_format($val4['wr_order_price']) ?>
                        </td>
                      <? } ?>
                    </tr>

                    <?
                    $num1++;
                    $num2++;
                    $num3++;
                  }
                }
                ?>
              <?php } ?>

              <?php
              $tr_class++;
            }
            ?>
            <?php if ($code_stx) { ?>
              <tr>
                <td class="text_center" colspan="6"><strong>합계</strong></td>
                <td class="text_center"><?=number_format($total_sum_in_ea)?></td>
                <td class="text_center"><?=number_format($total_sum_out_ea)?></td>
                <td colspan="2"></td>
              </tr>
            <?php }?>

            </tbody>
          </table>
        </div>
      </form>
    </div>
  </div>


  <div class="bo_sch_wrap">
    <fieldset class="bo_sch">
      <h3>검색</h3>
      <form name="fsearch" method="get">
        <select name="sc_domain" style="margin-bottom:15px">
          <option value="">전체 도메인</option>
          <?
          $arr = get_code_list('4');
          foreach ($arr as $key => $value) {
            $selected = ($value['code_value'] == $sc_domain) ? "selected" : "";
            ?>
            <option value="<?= $value['code_value'] ?>" <?= $selected ?>><?= $value['code_name'] ?></option>
            <?
          }
          ?>
        </select>


        <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
          <input type="text" name="code_stx" value="<?php echo $code_stx ?>" class="frm_input" style="width:100%;"
                 placeholder="상품명 및 코드 조회">
        </div>

        <label for="stx" style="font-weight:bold">일자 조회<strong class="sound_only"> 필수</strong></label>
        <div class="sch_bar" style="margin-top:3px">

          <input type="date" name="st_date" value="<?php echo $st_date ?>" required id="stx" class="sch_input" size="25"
                 maxlength="20" placeholder="" style="width:45%;text-align:center">
          <span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
          <input type="date" name="ed_date" value="<?php echo stripslashes($ed_date) ?>" required id="stx"
                 class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">

        </div>
        <button type="submit" value="검색" class="btn_b01" id="search_btn" style="width:49%;margin-top:15px"><i
              class="fa fa-search" aria-hidden="true"></i> 검색하기
        </button>
        <button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;"
                onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat" aria-hidden="true"></i>
          검색초기화
        </button>
        <button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span
              class="sound_only">닫기</span></button>
      </form>
    </fieldset>
    <div class="bo_sch_bg"></div>
  </div>
  <script>

    $(function () {

      $('#sorting_box').bind('change', function () {

        let sort = $(this).val();

        if (sort == "default") {
          location.href = '?sst=wr_id&sod=desc&stx2=<?php echo $stx2 ?>';
        } else if (sort == "up") {
          location.href = '?sst=stock&sod=desc&stx2=<?php echo $stx2 ?>';
        } else if (sort == "down") {
          location.href = '?sst=stock&sod=asc&stx2=<?php echo $stx2 ?>';
        }
      });


      $('.excel_form').bind('click', function () {

        let id = $(this).attr('data');
        var _width = '600';
        var _height = '600';

        var _left = Math.ceil((window.screen.width - _width) / 2);
        var _top = Math.ceil((window.screen.height - _height) / 2);

        window.open("./excel_pop.php", "excel_pop", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

        return false;
      });

      $('.delivery_com').bind('click', function () {

        var _width = '625';
        var _height = '600';

        var _left = Math.ceil((window.screen.width - _width) / 2);
        var _top = Math.ceil((window.screen.height - _height) / 2);

        window.open("./delivery_company.php", "pop_delivery_company", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

        return false;
      });

      $(".btn_bo_sch").on("click", function () {
        $(".bo_sch_wrap").toggle();
      });

      $('.bo_sch_bg, .bo_sch_cls').click(function () {
        $('.bo_sch_wrap').hide();
      });

    });

  </script>


<?php
include_once(G5_THEME_PATH . '/tail.php');