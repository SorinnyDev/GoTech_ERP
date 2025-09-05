<?php
include_once('./_common.php');


if (!$warehouse)
  $warehouse = 1000;

if ($warehouse == 3000) {
  $menu_num = 5;
  $title = "출고등록(미국)";
} else if ($warehouse == 4000) {
  $menu_num = 8;
  $title = "출고등록(FBA)";
} else if ($warehouse == 5000) {
  $menu_num = 8;
  $title = "출고등록(W-FBA)";
} else if ($warehouse == 6000) {
  $menu_num = 8;
  $title = "출고등록(U-FBA)";
} else if ($warehouse == 7000) {
  $menu_num = 8;
  $title = "출고등록(한국반품)";
} else if ($warehouse == 8000) {
  $menu_num = 8;
  $title = "출고등록(미국반품)";
} else if ($warehouse == "FBA_ALL") {
  $menu_num = 8;
  $title = "출고등록(모든 FBA)";
} else {
  $menu_num = 2;
  $title = "출고등록(한국)";
}

if (!$date1)
  $date1 = G5_TIME_YMD;
if (!$date2)
  $date2 = G5_TIME_YMD;

include_once(G5_THEME_PATH . '/head.php');

$qstr .= "&amp;date1={$date1}&amp;date2={$date2}";
$qstr .= "&amp;country={$country}&amp;release={$release}";
// $qstr .= "&amp;warehouse={$warehouse}";
$qstr .= "&amp;stx={$stx}";
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

      .tbl_frm01 .title {
          background: #444444;
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

      .tbl_frm01 td input,
      .tbl_frm01 td select {
          border: 1px solid #ddd;
          padding: 3px;
          width: 100%
      }

      .tbl_frm01 input.readonly,
      .tbl_frm01 select.readonly {
          background: #f2f2f2
      }

      .btn_calc {
          display: inline-block;
          padding: 0 20px;
          font-size: 1em;
          height: 25px;
          border: none;
          text-decoration: none;
          vertical-align: middle;
          background: #2aba8a;
          color: #FFFFFF;
      }

      .bo_sch .calc_popup_cls {
          position: absolute;
          right: 0;
          top: 0;
          color: #b5b8bb;
          border: 0;
          padding: 12px 15px;
          font-size: 16px;
          background: #fff;
      }

      .bg1 {
          background: #eff3f9
      }

      .hap::after {
          content: '합';
          display: inline-block;
          border: 1px solid #2aba8a;
          background: #2aba8a;
          color: #fff;
          font-size: 11px;
          padding: 0px 3px;
          line-height: 15px;
          border-radius: 5px;
          margin-left: 10px
      }

      .baro {
          display: block;
          border: 1px solid #28479f;
          background: #28479f;
          color: #fff;
          font-size: 11px;
          padding: 0px 3px;
          line-height: 15px;
          border-radius: 5px;
          width: 52px
      }

      .modal_wrap {
          display: none;
      }

      .modal_wrap_bg {
          background: #000;
          background: rgba(0, 0, 0, 0.1);
          width: 100%;
          height: 100%;
      }

      .modal_frm {
          position: absolute;
          top: 50%;
          left: 50%;
          background: #fff;
          text-align: left;
          width: 330px;
          margin-left: -165px;
          margin-top: -180px;
          padding: 10px;
          overflow-y: auto;
          border-radius: 5px;
          -webkit-box-shadow: 1px 1px 18px rgba(0, 0, 0, 0.2);
          -moz-box-shadow: 1px 1px 18px rgba(0, 0, 0, 0.2);
          box-shadow: 1px 1px 18px rgba(0, 0, 0, 0.2);
          border: 1px solid #dde7e9;
          background: #fff;
          border-radius: 3px;

      }

      .modal_wrap_cls {
          position: absolute;
          right: 0;
          top: 0;
          color: #b5b8bb;
          border: 0;
          padding: 12px 15px;
          font-size: 16px;
          background: #fff;
      }

      .bo_sch .calc_div {
          padding: 15px;
          display: block;
      }

      .notready {
          background: #FFFFA1;
      }
  </style>
  <div id="bo_list">
    <div class="bo_list_innr">
      <h2 class="board_tit"><?php echo $title ?></h2>
      <form name="fboardlist" id="fboardlist" action="./sales3_list_update.php" onsubmit="return fboardlist_submit(this);"
            method="post">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx ?>">
        <input type="hidden" name="spt" value="<?php echo $spt ?>">
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">
        <input type="hidden" name="warehouse" id="warehouse" value="<?= $warehouse ?>" />
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
            <? if ($warehouse == "4000") { ?>
              <span>
							<button type="submit" name="btn_submit" value="출고일괄처리" class="btn02" onclick="document.pressed=this.value"
                      style="background:#2aba8a;border:1px solid #2aba8a;">출고 일괄 처리</button>
						</span>
            <? } ?>
            <span> <button type="submit" name="btn_submit" value="선택삭제" class="btn02"
                           onclick="document.pressed=this.value">선택삭제</button></span>
            <!--<span> <button type="submit" name="btn_submit" value="완전삭제" class="btn02" onclick="document.pressed=this.value" style="background:#ff4081;border:1px solid #ff4081;">완전삭제</button></span>-->
          </div>
          <ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?>">
            <li class="wli_cnt">
              <label for="wmb_id" class="sound_only">중량무게1<strong>필수</strong></label>

              <select name="wmb_id" id="wmb_id" class="frm_input search_sel"
                      onchange="location.href='?mb_id='+this.value+'<?php echo $qstr ?>&delivery_com=<?php echo $delivery_com ?>'"
                      style="height:37px">
                <option value="">담당자 전체</option>
                <?php
                $mbSql = " select mb_id, mb_name from g5_member where del_yn = 'N' order by mb_name asc";
                $mbRst = sql_query($mbSql);
                for ($i = 0; $mb = sql_fetch_array($mbRst); $i++) {
                  ?>
                  <option value="<?php echo $mb['mb_id'] ?>" <?php echo get_selected($mb['mb_id'], $mb_id) ?>>
                    <?php echo $mb['mb_name'] ?>(<?php echo $mb['mb_id'] ?>)</option>
                <?php } ?>
              </select>
            </li>
            <li>
              <? if ($warehouse == 4000 || $warehouse == 5000 || $warehouse == 6000 || $warehouse == "FBA_ALL") { ?>
                <!--<select name="warehouse" id="warehouse" class="frm_input" style="height:37px" onchange="location.href='?warehouse='+this.value+'<?php echo $qstr ?>'">
																<option value="FBA_ALL"  <?php echo get_selected($warehouse, 'FBA_ALL') ?>>FBA 전체</option>
																<option value="4000" <?php echo get_selected($warehouse, '4000') ?>>FBA</option>
																<option value="5000" <?php echo get_selected($warehouse, '5000') ?>>W-FBA</option>
																<option value="6000" <?php echo get_selected($warehouse, '6000') ?>>U-FBA</option>
														</select>-->
              <? } else { ?>
                <select name="delivery_com" id="delivery_company" class="frm_input" style="height:37px"
                        onchange="location.href='?mb_id=<?php echo $mb_id ?>&delivery_com='+this.value+'<?php echo $qstr ?>'">
                  <!--<option value="postoffice">우체국</option>
                              <option value="SHIPTER">SHIPTER</option>
                              <option value="KSE">KSE</option>
                              <option value="fedex">fedex</option>
                              <option value="DHL">DHL</option>
                              <option value="normal">현재 리스트</option>-->
                  <option value="">배송사별 정렬</option>
                  <option value="1002" <?php echo get_selected($delivery_com, '1002') ?>>K-PACKET</option>
                  <option value="1017" <?php echo get_selected($delivery_com, '1017') ?>>S-PACKET</option>
                  <option value="1009" <?php echo get_selected($delivery_com, '1009') ?>>SHIPTER</option>
                  <option value="1021" <?php echo get_selected($delivery_com, '1021') ?>>KSE</option>
                  <option value="1006" <?php echo get_selected($delivery_com, '1006') ?>>FEDEX-ZIO(IP)</option>
                  <option value="1007" <?php echo get_selected($delivery_com, '1007') ?>>DHL</option>
                  <?php if ($warehouse == 1000) { ?>
                  <option value="normal" <?php echo get_selected($delivery_com, 'normal') ?>>물류</option><?php } ?>

                </select>

              <? } ?>
            </li>
            <li><button type="button" class="btn02" onclick="export_excel();" style="height:37px"><i
                    class="fa fa-file-excel-o" aria-hidden="true"></i> 엑셀출력</button></li>
            <?php
            //k-packet만 수출신고양식 출력
            if ($delivery_com == "1002") { ?>
              <li><button type="button" class="btn01" onclick="export_excel2();" style="height:37px"><i
                      class="fa fa-file-excel-o" aria-hidden="true"></i> 수출신고</button></li>
            <?php } ?>
            <li><button type="button" class="btn01" onclick="import_excel();" style="height:37px"><i
                    class="fa fa-file-excel-o" aria-hidden="true"></i> 엑셀출고등록</button></li>
            <li><button type="button" class="btn_b01" onclick="pop_excel();"> <i class="fa fa-database"
                                                                                 aria-hidden="true"></i> 입고자료 가져오기</button></li>

            <? if ($member['mb_id'] == "test") { ?>
              <li><button type="button" class="btn_b01 btn_modal_wrap"> <i class="fa fa-database" aria-hidden="true"></i>테스트
                  배송요금 생성</button></li>
            <? } ?>
            <li><button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i>
                검색</button></li>
            <!--<li><button type="button" class="btn_b02 all_delete" style="color:#fff;background:red"> 전체초기화(임시)</button></li>-->

          </ul>
        </div>

        <div id="bo_li_01" style="overflow-x:scroll;height:400px">
          <ul class="list_head" style="width:3500px;position:sticky;top:0;background:#fff;z-index:99">
            <li style="width:50px"><input type="checkbox" id="chkall"
                                          onclick="if (this.checked) all_checked(true); else all_checked(false);"></li>
            <li style="width:70px">순번</li>
            <li style="width:30px">무게</li>
            <li style="width:30px"><?php echo subject_sort_link('wr_release_use', $qstr2, 1) ?>출고</a></li>
            <li style="width:100px">도메인명</li>
            <li style="width:200px"><?php echo subject_sort_link('wr_order_num', $qstr2, 1) ?>주문번호</a></li>
            <li style="width:100px">랙 번호</li>
            <li style="width:100px">매출일자</li>
            <li style="width:100px">발주일자</li>
            <li style="width:100px">입고일자</li>
            <li style="width:100px">출고일자</li>
            <li style="width:150px">상품코드</li>

            <li style="width:150px">약칭명</li>
            <li style="width:250px">상품명칭</li>
            <li style="width:200px">대표코드</li>

            <li style="width:70px">수량</li>
            <li style="width:70px">박스수</li>
            <li style="width:100px">단가</li>
            <li style="width:100px">신고가격</li>
            <!--<li style="width:100px">수수료</li>-->
            <li style="width:70px">통화</li>
            <li style="width:70px">개당무게</li>
            <li style="width:70px">총무게</li>
            <li style="width:100px">배송사</li>
            <li style="width:100px">배송요금</li>
            <li style="width:100px">HS코드</li>
            <li style="width:100px">주문자ID</li>
            <li style="width:100px">주문자명</li>
          </ul>

          <div id="bo_li_01" class="list_03">
            <ul style="width:3500px">
              <?php
              if ($warehouse == 1000)
                $sql_search = " (A.wr_warehouse = 1000 or A.wr_warehouse = 9000) ";
              else if ($warehouse == 3000)
                $sql_search = " A.wr_warehouse = 3000 ";
              else if ($warehouse == 4000)
                $sql_search = " A.wr_warehouse = 4000 ";
              else if ($warehouse == 5000)
                $sql_search = " A.wr_warehouse = 5000 ";
              else if ($warehouse == 6000)
                $sql_search = " A.wr_warehouse = 6000 ";
              else if ($warehouse == 7000)
                $sql_search = " A.wr_warehouse = 7000 ";
              else if ($warehouse == 8000)
                $sql_search = " A.wr_warehouse = 8000 ";
              else if ($warehouse == "FBA_ALL")
                $sql_search = " (A.wr_warehouse = '4000' or A.wr_warehouse = '5000' or A.wr_warehouse = '6000') ";

              if ($dType == "1") {
                $sql_search .= " AND wr_domain NOT IN('" . implode("','", $circulation) . "')";
              } else if ($dType == "2") {
                $sql_search .= " AND wr_domain IN('" . implode("','", $circulation) . "')";
              }

              if ($date1 && $date2)
                $sql_search .= " and wr_date4 BETWEEN '{$date1}' AND '{$date2}'";

              if ($mb_id) {
                $sql_search .= " and A.mb_id = '$mb_id' ";
              }

              if ($wr_18)
                $sql_search .= " and wr_domain = '{$wr_18}' ";

              if ($country == 0) {

              } else if ($country == 1) {
                $sql_search .= " and wr_deli_country = 'US' ";
              } else if ($country == 2) {
                $sql_search .= " and wr_deli_country != 'US' ";
              }

              if ($release == 0) {

              } else if ($release == 1) {
                $sql_search .= " and wr_release_use = '1' ";
              } else if ($release == 2) {
                $sql_search .= " and wr_release_use = '0' ";
              }

              if ($delivery_com && $delivery_com != 'normal') {
                $sql_search .= " and wr_delivery = '$delivery_com' ";
              }

              if ($stx) {
                $sql_search .= " and wr_order_num LIKE '%$stx%' ";
              }

              if ($search_pnm) {
                $search_pnm = trim($search_pnm);
                $sql_search .= " and wr_product_nm LIKE '%$search_pnm%' ";
              }

              if ($search_sku) {
                $search_sku = trim($search_sku);
                $sql_search .= " and wp.wr_5 = '$search_sku' ";
              }

              if ($serviceType === "0001") {
                $sql_search .= " and A.wr_servicetype = '0001'";
              } else if ($serviceType == '1000') {
                $sql_search .= " and A.wr_servicetype != '0001'";
              }

              if (!$sst && !$sod) {
                $sst = "wr_ori_order_num";
                $sod = "asc";
              }
              $sql_order = "order by $sst $sod";

              $sql = "select A.*,B.hap_cnt,C.sales3_cnt,(CASE WHEN IFNULL(wr_set_sku,'') > '' THEN CONCAT(A.wr_ori_order_num,'_',A.wr_set_sku) ELSE '' END) AS set_order from g5_sales3_list A 
								LEFT OUTER JOIN(
									SELECT wr_ori_order_num,COUNT(*) AS hap_cnt FROM g5_sales0_list GROUP BY wr_ori_order_num
								)B ON B.wr_ori_order_num=A.wr_ori_order_num
								LEFT OUTER JOIN(
									SELECT wr_ori_order_num,COUNT(*) AS sales3_cnt FROM g5_sales3_list GROUP BY wr_ori_order_num
								)C ON C.wr_ori_order_num=A.wr_ori_order_num
								left join g5_write_product as wp on wp.wr_id = A.wr_product_id
								where {$sql_search} {$sql_order} ";

              $rst = sql_query($sql);
              for ($i = 0; $row = sql_fetch_array($rst); $i++) {

                $sql_sub_search = " wr_id = '{$row['wr_product_id']}'";

                $item = sql_fetch("select * from g5_write_product where {$sql_sub_search} ");

                $weight_state = "&nbsp";


                $release_state = "&nbsp;";
                if ($row['wr_release_use'] == 1) {
                  $mb = get_member($row['wr_release_mbid'], 'mb_name, mb_id');
                  $release_state = '<i class="fa fa-check" aria-hidden="true" style="color:green" title="' . $row['wr_release_date'] . '/' . $mb['mb_name'] . '(' . $mb['mb_id'] . ')"></i>';
                }

                $imsi_item = "";
                if ($item['ca_name'] == "임시") {
                  $imsi_item = "color:blue";
                } else if ($item['ca_name'] == "최종확정") {
                  $imsi_item = "color:red";
                }


                switch ($row['wr_domain']) {
                  case "dodoskin":
                    $only_number_odnum = preg_replace('/[^0-9]*/s', '', $row['wr_order_num']);
                    break;
                  case "Shopee BR":
                    $only_number_odnum = substr($row['wr_order_num'], 0, 13);
                    break;
                  case "Ebay-dodoskin":
                  case "Ebay":
                    $only_number_odnum = substr($row['wr_order_num'], 0, 6);
                    break;
                  default:
                    $only_number_odnum = preg_replace('/[^0-9]*/s', '', $row['wr_order_num']);
                    break;
                }


                $order_num[] = "'tr_" . $only_number_odnum . "'";

                if (($row['wr_domain'] == "dodoskin" || $row['wr_domain'] == "Ebay-dodoskin") && preg_match('/[a-zA-Z]/', $row['wr_order_num'])) {
                  $hap_class = "hap";
                  if ($row['wr_weight_sum1'] && $row['wr_weight_sum2']) {
                    $weight_state = '<i class="fa fa-check" aria-hidden="true" style="color:green"></i>';
                  }
                } else {
                  $hap_class = "";
                  if ($row['wr_weight1']) {
                    $weight_state = '<i class="fa fa-check" aria-hidden="true" style="color:green"></i>';
                  }
                }

                if ($row['hap_cnt'] != $row['sales3_cnt']) {
                  $li_bg = "notready";
                } else {
                  $li_bg = "";
                }

                //$bg = 'bg' . ($i % 2);

                if ($row['wr_rack']) {
                  $rack_id = $row['wr_rack'];
                } else {
                  $rack = sql_fetch("select wr_rack from g5_rack_stock where wr_warehouse = '{$warehouse}' and wr_product_id = '{$row['wr_product_id']}' GROUP BY wr_rack HAVING SUM(wr_stock) > 0;");
                  $rack_id = $rack['wr_rack'];
                }

                $wr_release_etc = "";
                if ($row['wr_release_etc']) {
                  $wr_release_etc = '<br><strong style="color:red">비고</strong>';
                }

                # 수수료 계산
                if ($row['wr_sales_fee']) {
                  $wr_fee = round(($row['wr_singo'] * $row['wr_sales_fee'] / 100), 2);
                } else {
                  $item_fee = sql_fetch("SELECT * FROM g5_write_product_fee WHERE wr_id='" . $row['wr_product_id'] . "' AND warehouse='" . $row['wr_warehouse'] . "' AND domain='" . $row['wr_domain'] . "'");
                  if (!$item_fee['fidx']) {
                    $wr_fee = "0";
                  } else {
                    $wr_fee = round(($row['wr_singo'] * $item_fee['product_fee'] / 100), 2);
                  }
                }

                $rack_name = get_rack_name($rack_id);
                $wr_direct_use = "";
                if ($row['wr_direct_use'] == 1)
                  $wr_direct_use = "<span class=\"baro\">바로출고</span>";
                ?>
                <li class="modify tr_<?= $only_number_odnum ?> <?= $li_bg ?>" data="<?php echo $row['seq'] ?>">
                  <div class="num cnt_left" style="width:50px">
                    <input type="checkbox" name="seq[]" data-set-order="<?= $row['set_order'] ?>"
                           value="<?php echo $row['seq'] ?>">
                  </div>
                  <div class="num cnt_left" style="width:70px"><?php echo ($i + 1) ?><?= $wr_release_etc ?></div>
                  <div class="cnt_left" style="width:30px;text-align:center"><?php echo $weight_state ?></div>
                  <div class="cnt_left" style="width:30px;text-align:center"><?php echo $release_state ?></div>
                  <div class="cnt_left" style="width:100px"><?php echo $row['wr_domain'] ?><?php echo $wr_direct_use ?></div>
                  <div class="cnt_left <?php echo $hap_class ?>" style="width:200px;"><?php echo $row['wr_order_num'] ?>
                  </div>
                  <div class="cnt_left" style="width:100px;"><?php echo $rack_name ?></div>
                  <div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_date'] ?></div>
                  <div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_date2'] ?></div>
                  <div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_date3'] ?></div>
                  <div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_date4'] ?></div>
                  <div class="cnt_left" style="width:150px;text-align:center;<?php echo $imsi_item ?>">
                    <?php echo $item['wr_1'] ?></div>

                  <div class="cnt_left" style="width:150px;"><?php echo $item['wr_2'] ?></div>
                  <div class="cnt_left" style="width:250px;<?php echo $imsi_item ?>"><?php echo $row['wr_product_nm'] ?>
                  </div>
                  <div class="cnt_left" style="width:200px;"><?php echo $item['wr_5'] ?></div>
                  <div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_ea'] ?></div>
                  <div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_box'] ?></div>
                  <div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_danga'] ?></div>
                  <div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_singo'] ?></div>
                  <!--<div class="cnt_left" style="width:100px;text-align:right"><?php echo $wr_fee ?></div>-->
                  <div class="cnt_left" style="width:70px;text-align:center"><?php echo $row['wr_currency'] ?></div>
                  <div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_weight1'] ?></div>
                  <div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_weight2'] ?></div>
                  <div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_delivery'] ?></div>
                  <div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_delivery_fee'] ?></div>
                  <div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_hscode'] ?></div>
                  <div class="cnt_left" style="width:100px;"><?php echo $row['wr_mb_id'] ?></div>
                  <div class="cnt_left" style="width:100px;"><?php echo $row['wr_mb_name'] ?></div>

                </li>
              <?php }

              if ($i == 0) {
                echo '<li class="empty_table">내역이 없습니다.</li>';
              }

              // 중복체크 후 ',' 문자 삽입
              if ($order_num != "") {
                $ordernum = array_unique($order_num);
                $ordernum = @implode(",", $order_num);
              }

              ?>
            </ul>
          </div>
        </div>

      </form>

      <div>
        <h2 style="margin-top:20px; margin-bottom:10px;font-size:14px">출고정보</h2>
        <form id="result_addform">
          <div style="border:1px solid #ddd; width:100%; min-height:450px;" id="result_form">
            <p style="text-align:center; font-size:15px; color:red;padding-top:150px">상단 리스트에서 선택하세요.</p>
          </div>
        </form>
      </div>
    </div>

  </div>


  <div class="bo_sch_wrap">
    <fieldset class="bo_sch" style="top: 35%;">
      <h3>검색</h3>
      <form name="fsearch" method="get">
        <input type="hidden" name="mb_id" value="<?php echo $mb_id ?>">
        <input type="hidden" name="warehouse" value="<?php echo $warehouse ?>">
        <input type="hidden" name="delivery_com" value="<?php echo $delivery_com ?>">
        <select name="wr_18" style="margin-bottom:15px">
          <option value="">도메인 선택</option>
          <?php echo get_domain_option($_GET['wr_18']) ?>
        </select>

        <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
          <label><input type="radio" name="dType" value="0" <?php echo get_checked($dType, 0) ?>> 전체</label>
          <label><input type="radio" name="dType" value="1" <?php echo get_checked($dType, 1) ?>> 화장품</label>
          <label><input type="radio" name="dType" value="2" <?php echo get_checked($dType, 2) ?>> 유통</label>
        </div>
        <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
          <label><input type="radio" name="country" value="0" <?php echo get_checked($country, 0) ?>> 전체</label>
          <label><input type="radio" name="country" value="1" <?php echo get_checked($country, 1) ?>> US만 출력</label>
          <label><input type="radio" name="country" value="2" <?php echo get_checked($country, 2) ?>> US제외 출력</label>
        </div>
        <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
          <label><input type="radio" name="release" value="0" <?php echo get_checked($release, 0) ?>> 전체</label>
          <label><input type="radio" name="release" value="1" <?php echo get_checked($release, 1) ?>> 출고건</label>
          <label><input type="radio" name="release" value="2" <?php echo get_checked($release, 2) ?>> 미출고건</label>
        </div>
        <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
          <label><input type="radio" name="serviceType" value="0" <?php echo get_checked($serviceType, 0) ?>> 전체</label>
          <label><input type="radio" name="serviceType" value="0001" <?php echo get_checked($serviceType, '0001') ?>> 특송만</label>
          <label><input type="radio" name="serviceType" value="1000" <?php echo get_checked($serviceType, '1000') ?>> 특송 외 출력</label>
        </div>
        <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
          <input type="text" name="stx" value="<?php echo urldecode($_GET['stx']) ?>" class="frm_input" style="width:100%;"
                 placeholder="주문번호 조회">
        </div>

        <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
          <input type="text" name="search_pnm" value="<?php echo urldecode($_GET['search_pnm']) ?>" class="frm_input"
                 style="width:100%;" placeholder="상품명 조회">
        </div>

        <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
          <input type="text" name="search_sku" value="<?php echo urldecode($_GET['search_sku']) ?>" class="frm_input"
                 style="width:100%;" placeholder="SKU 조회">
        </div>

        <label for="stx" style="font-weight:bold">매출일자 조회<strong class="sound_only"> 필수</strong></label>
        <div class="sch_bar" style="margin-top:3px">

          <input type="date" name="date1" value="<?php echo $date1 ?>" required id="stx" class="sch_input" size="25"
                 maxlength="20" placeholder="" style="width:45%;text-align:center">
          <span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
          <input type="date" name="date2" value="<?php echo stripslashes($date2) ?>" required id="stx" class="sch_input"
                 size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">

        </div>
        <button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search"
                                                                                              aria-hidden="true"></i> 검색하기</button>
        <button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;"
                onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat" aria-hidden="true"></i>
          검색초기화</button>
        <button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span
              class="sound_only">닫기</span></button>
      </form>
    </fieldset>
    <div class="bo_sch_bg"></div>
  </div>

<? if ($member['mb_id'] == "test") { ?>
  <div class="modal_wrap">
    <fieldset class="modal_frm">
      <h3>검색</h3>

      <div class="sch_bar" style="margin-top:3px">
        <label for=""></label>
        <input type="text" name="" class="frm_input" value="" />
      </div>

      <button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search"
                                                                                            aria-hidden="true"></i> 검색하기</button>
      <button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;"
              onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat" aria-hidden="true"></i>
        검색초기화</button>
      <button type="button" class="modal_wrap_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span
            class="sound_only">닫기</span></button>

    </fieldset>
    <div class="modal_wrap_bg"></div>
  </div>
<? } ?>


  <script>
    jQuery(function ($) {
      // 배송요금 추가
      $(".btn_modal_wrap").on("click", function () {
        $(".modal_wrap").toggle();
      })
      $('.modal_wrap_bg, .modal_wrap_cls').click(function () {
        $('.modal_wrap').hide();
      });

      // 게시판 검색
      $(".btn_bo_sch").on("click", function () {
        $(".bo_sch_wrap").toggle();
      })
      $('.bo_sch_bg, .bo_sch_cls').click(function () {
        $('.bo_sch_wrap').hide();
      });

      var classes = [];
      $('#bo_li_01 li').each(function () {
        var className = $(this).attr('class');
        if (className && classes.indexOf(className) === -1) {
          classes.push(className);
          $('.' + className).addClass('bordered');
        }
      });

      var classArray = [<?php echo $ordernum ?>];


      // 클래스명 배열을 순회하며 랜덤 색상 부여
      $.each(classArray, function (index, className) {
        var randomColor = getRandomColor();
        //console.log(className);
        $('.' + className + ':last').css('border-bottom', '2px solid #000');
        $('.' + className + ':first').attr('data-first', 1);

      });

      function getRandomColor() {
        var letters = '123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
          color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
      }

    });

    function item_link() {
      if (confirm("검색된 제품이 없습니다\n\n직접 검색하시겠습니까?")) {
        location.href = g5_bbs_url + "/board.php?bo_table=product";
      }
    }


    function export_excel() {
      //01.11 다시 작업해야됨
      //alert('점검 중입니다.');
      //return false;
      let file_name = $('#delivery_company').val();
      let warehouse = $('#warehouse').val();

      if ($('input:checkbox[name="seq[]"]:checked').length == 0) {
        alert('먼저 출력할 출고 데이터를 선택하세요.');
        return false;
      }

      if (warehouse == "FBA_ALL" || warehouse == "4000" || warehouse == "5000" || warehouse == "6000")
        file_name = "fba";
      alert('sales3_excel_' + file_name);
      var excelForm = $("<form></form>");
      excelForm.attr("method", "POST");
      excelForm.attr("action", './sales3_excel_' + file_name + '.php');

      var chk = document.getElementsByName("seq[]");
      var no_arr = [];
      for (i = 0; i < chk.length; i++) {
        if (chk[i].checked) {
          excelForm.append($("<input/>", { type: "hidden", name: "seq[]", value: chk[i].value }));

        }
      }
      excelForm.append($("<input/>", { type: "hidden", name: "warehouse", value: "<?php echo $warehouse ?>" }));

      excelForm.appendTo("body");
      excelForm.submit();

      /*
      location.href = './sales3_excel_'+file_name+'.php?warehouse=<?php echo $warehouse ?>& date1=<?php echo $date1 ?> & date2=<?php echo $date2 ?>';
		return false;*/
    }

    function export_excel2() {
      //수출신고 추가 24.05.10

      let file_name = $('#delivery_company').val();
      let warehouse = $('#warehouse').val();

      if ($('input:checkbox[name="seq[]"]:checked').length == 0) {
        alert('먼저 출력할 출고 데이터를 선택하세요.');
        return false;
      }


      var excelForm = $("<form></form>");
      excelForm.attr("method", "POST");
      excelForm.attr("action", './sales3_excel_declaration.php');

      var chk = document.getElementsByName("seq[]");
      var no_arr = [];
      for (i = 0; i < chk.length; i++) {
        if (chk[i].checked) {
          excelForm.append($("<input/>", { type: "hidden", name: "seq[]", value: chk[i].value }));

        }
      }
      excelForm.append($("<input/>", { type: "hidden", name: "warehouse", value: "<?php echo $warehouse ?>" }));

      excelForm.appendTo("body");
      excelForm.submit();

      /*
      location.href = './sales3_excel_'+file_name+'.php?warehouse=<?php echo $warehouse ?>& date1=<?php echo $date1 ?> & date2=<?php echo $date2 ?>';
		return false;*/
    }

    function pop_excel() {
      let id = $(this).attr('data');
      window.open("./sales3_search.php?warehouse=<?php echo $warehouse ?>", "sales3_search", " width=" + screen.width + ", height=" + screen.height + ", scrollbars=1, fullscreen=yes");

      return false;


    }

    function import_excel() {
      window.open("./sales3_import_excel.php", "import_excel", "left=50, top=50, width=500, height=550, scrollbars=1");
    }

    function pop_excel_test() {
      let id = $(this).attr('data');
      var _width = '1500';
      var _height = '850';

      var _left = Math.ceil((window.screen.width - _width) / 2);
      var _top = Math.ceil((window.screen.height - _height) / 2);

      window.open("./sales3_search_test.php?warehouse=<?php echo $warehouse ?>", "sales3_search", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

      return false;
    }


    $(function () {
      $(document).on('change', '#wr_delivery', function () {
        let fee = $('#wr_delivery option:selected').attr('data');
        var currency = $('#wr_delivery option:selected').attr('currency');
        var oil_percent = $('#wr_delivery option:selected').attr('oil_percent');
        if (!isDefined(oil_percent)) {
          oil_percent = 0;
        }
        $('#wr_delivery_fee').val(fee);
        $("#wr_delivery_currency").val(currency);
        $("#wr_delivery_oil_percent").val(oil_percent);
        fnCalcDeliveryOil();
      });



      $(document).on("keyup", "#wr_delivery_fee", function () {
        fnCalcDeliveryOil();

      });

      $(document).on("keyup", "#wr_delivery_fee2", function () {
        fnCalcDeliveryTotal();
      });

      $('.all_delete').bind('click', function () {
        if (confirm('정말 데이터를 초기화 하시겠습니까?\n출고등록 - 한국/미국 모두 초기화 됩니다.')) {
          location.href = './all_delete.php?table_name=3';
        }
        return false;
      });

      $('.modify').bind('click', function () {
        $('#result_form').html('');
        let id = $(this).attr('data');
        let first = $(this).attr('data-first');
        $('#result_form').html('<center><img style="padding-top:150px" src="/mobile/shop/img/loading.gif"></center>');
        $.post('./sales3_addbox_test.php', { seq: id, first: first }, function (data) {
          $('#result_form').html(data);
        });

      });

      $(document).on('click', '.addbtn1', function () {
        $(this).attr('disabled', true);
        var formData = $("#result_addform").serialize();

        $.ajax({
          cache: false,
          url: "./sales3_addbox_update.php",
          type: 'POST',
          data: formData,
          success: function (data) {
            if (data == "y") {
              alert('데이터가 정상적으로 저장되었습니다.');
            } else {
              alert('데이터 저장중 오류가 발생했습니다.');
              return false;
            }

            $('.addbtn1').attr('disabled', false);
          }, // success

          error: function (xhr, status) {
            alert(xhr + " : " + status);
          }
        });
      });

      $(document).on('click', '.addbtn2', function () {
        $(this).attr('disabled', true);
        let seq = $(this).attr('data');
        let no = $('input[name=wr_release_traking]').val();
        let wr_delivery = $('#wr_delivery').val();
        let wr_delivery_fee = $('#wr_delivery_fee').val();
        let wr_delivery_fee2 = $('#wr_delivery_fee2').val();
        let rack = "";

        if (!("<?= $warehouse ?>" == "5000" || "<?= $warehouse ?>" == "6000" || "<?= $warehouse ?>" == "FBA_ALL")) {
          if (!no) {
            alert('출고완료 처리는 [수출트래킹NO]을 필수로 입력하셔야 합니다.');
            $(this).attr('disabled', false);
            return false;
          }
        }

        if ($('select[name=wr_rack]').length) {
          rack = $('select[name=wr_rack]').val();

          if (!rack) {
            //alert('재고를 출고할 랙을 선택하세요.');
            //$(this).attr('disabled', false);
            //return false;
          }
        }

        $.ajax({
          cache: false,
          url: "./sales3_addbox_release.php",
          type: 'POST',
          data: { seq: seq, rack: rack, wr_delivery: wr_delivery, wr_delivery_fee: wr_delivery_fee, wr_delivery_fee2: wr_delivery_fee2 },
          success: function (data) {
            if (data == "y") {
              alert('출고처리가 정상적으로 저장되었습니다.\n페이지가 새로고침 됩니다.');
              location.reload();
            } else if (data == "nn") {
              alert('재고가 있는 랙을 찾을 수 없습니다. 랙재고를 확인하세요.');
              $(this).attr('disabled', false);
              return false;
            } else {
              alert('데이터 저장중 오류가 발생했습니다.');
              return false;
            }

            $('.addbtn2').hide();
          }, // success

          error: function (xhr, status) {
            alert(xhr + " : " + status);
          }
        });
      });

      function extractArrayIndex(name) {
        // 정규 표현식을 사용하여 name 속성에서 배열 안의 숫자 추출
        var match = name.match(/\[(\d+)\]/);

        return match ? parseInt(match[1], 10) : null;
      }

      //합배송 처리
      $(document).on('click', '.hap_release_btn', function () {
        $(this).attr('disabled', true);
        let ordernum = $(this).attr('data');
        let no = $('input[name=wr_release_traking]').val();
        let etc = $('input[name=wr_release_etc]').val();
        let wr_delivery = $('#wr_delivery').val();
        let wr_delivery_fee = $('#wr_delivery_fee').val();
        let wr_delivery_fee2 = $("#wr_delivery_fee2").val();
        let wr_domain = $('input[name=wr_domain]').val();

        if (!("<?= $warehouse ?>" == "5000" || "<?= $warehouse ?>" == "6000" || "<?= $warehouse ?>" == "FBA_ALL")) {
          if (!no) {
            alert('합배송 출고완료 처리는 [수출트래킹NO]을 필수로 입력하셔야 합니다.');
            $(this).attr('disabled', false);
            return false;
          }
        }
        var formData = new FormData();

        if ($('select[name^=wr_rack]').length) {

          let rack_arr = [];

          $('select[name^=wr_rack]').each(function () {

            let rack_seq = extractArrayIndex($(this).attr('name'));
            let rack_data = $(this).val();

            if (rack_seq && rack_data) {
              rack_arr.push(rack_seq + "|@|" + rack_data);
            }
          })

          for (var i = 0; i < rack_arr.length; i++) {
            formData.append('rack[]', rack_arr[i]);
          }

          if (rack_arr.length == 0) {
            //alert('재고를 출고할 랙을 선택하세요.');
            //$(this).attr('disabled', false);
            //return false;
          }
        }

        formData.append('ordernum', ordernum);
        formData.append('no', no);
        formData.append('etc', etc);
        formData.append('wr_delivery', wr_delivery);
        formData.append('wr_delivery_fee', wr_delivery_fee);
        formData.append('wr_delivery_fee2', wr_delivery_fee2);
        formData.append('wr_domain', wr_domain);

        $.ajax({
          cache: false,
          url: "./sales3_addbox_release_hap.php",
          type: 'POST',
          processData: false, // FormData 사용 시 이 옵션을 false로 설정
          contentType: false,
          data: formData,
          success: function (data) {
            if (data == "y") {
              alert('출고처리가 정상적으로 저장되었습니다.\n페이지가 새로고침 됩니다.');
              location.reload();
            } else {
              alert('데이터 저장중 오류가 발생했습니다.');
              return false;
            }

            $('.hap_release_btn').hide();
          }, // success

          error: function (xhr, status) {
            alert(xhr + " : " + status);
          }
        });
      });

    });

    // 배송 유류할증료 계산
    function fnCalcDeliveryOil() {
      var wr_delivery_fee = $("#wr_delivery_fee").val();
      var oil_percent = $("#wr_delivery_oil_percent").val();
      if (!isDefined(wr_delivery_fee)) {
        wr_delivery_fee = 0;
      }

      if (!isDefined(oil_percent)) {
        oil_percent = 0;
      }
      var wr_delivery_oil = wr_delivery_fee * oil_percent;
      $("#wr_delivery_oil").val(Math.floor(wr_delivery_oil));
      fnCalcDeliveryTotal();
    }

    // 배송비 총금액 계산
    function fnCalcDeliveryTotal() {
      var wr_delivery_fee = $("#wr_delivery_fee").val();
      var wr_delivery_fee2 = $("#wr_delivery_fee2").val();
      var wr_delivery_oil = $("#wr_delivery_oil").val();
      if (!isDefined(wr_delivery_fee)) {
        wr_delivery_fee = 0;
      }
      if (!isDefined(wr_delivery_fee2)) {
        wr_delivery_fee2 = 0;
      }
      if (!isDefined(wr_delivery_oil)) {
        wr_delivery_oil = 0;
      }

      var wr_delivery_total = parseFloat(wr_delivery_fee) + parseFloat(wr_delivery_fee2) + parseFloat(wr_delivery_oil);
      console.log(wr_delivery_total);
      $("#wr_delivery_total").val(wr_delivery_total);
    }

    $('input[name="seq[]"]').bind('click', function () {
      let stat = $(this).is(':checked');
      var set_order = $(this).attr("data-set-order");
      if (isDefined(set_order)) {
        alert("세트 상품입니다.");
        $("input[data-set-order='" + set_order + "']").prop("checked", $(this).is(":checked"));
      }
      if (stat) {
        $(this).closest('li').css({ 'background': '#f2f2f2' });
      } else {
        $(this).closest('li').css({ 'background': '#fff' });
      }
    });

    function all_checked(sw) {
      var f = document.fboardlist;

      for (var i = 0; i < f.length; i++) {
        if (f.elements[i].name == "seq[]")
          f.elements[i].checked = sw;
      }

      $('input[name="seq[]"]').each(function () {
        let stat = $(this).is(':checked');

        if (stat) {
          $(this).closest('li').css({ 'background': '#f2f2f2' });
        } else {
          $(this).closest('li').css({ 'background': '#fff' });
        }
      });
    }

    function fboardlist_submit(f) {
      var chk_count = 0;

      for (var i = 0; i < f.length; i++) {
        if (f.elements[i].name == "seq[]" && f.elements[i].checked)
          chk_count++;
      }

      if (!chk_count) {
        alert(document.pressed + "할 데이터를 하나 이상 선택하세요.");
        return false;
      }


      if (document.pressed == "선택삭제") {
        if (!confirm("선택한 데이터를 정말 삭제하시겠습니까?\n\n[경고] 한번 삭제한 자료는 복구할 수 없습니다."))
          return false;

        f.removeAttribute("target");
        f.action = "./sales3_list_update.php";
      } else if (document.pressed == "완전삭제") {
        if (!confirm("선택한 데이터를 정말 삭제하시겠습니까?\n\n[경고] 주문번호에 관련된 자료 전부 삭제하는 기능입니다.\n\n한번 삭제한 자료는 복구할 수 없습니다.\n\n이전 과정 전부 삭제됩니다."))
          return false;

        f.removeAttribute("target");
        f.action = "./sales3_list_update.php";
      } else if (document.pressed == "출고일괄처리") {
        if (!confirm("선택한 데이터를 일괄 출고 완료하시겠습니까?")) {
          return false;
        }
        f.removeAttribute("target");
        f.action = "./sales3_list_update.php";
      }

      return true;
    }

    window.addEventListener('message', function (event) {
      if (event.data?.event === 'popup_submit') {
        $.post('./sales3_addbox_test.php', { seq: event.data?.seq, first: 1 }, function (data) {
          $('#result_form').html(data);
        });
      }
    });

  </script>


<?php
include_once(G5_THEME_PATH . '/tail.php');