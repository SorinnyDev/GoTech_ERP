<?php
include_once('./_common.php');

include_once(G5_THEME_PATH . '/head.php');

if ($is_force_update == 'Y' && $member['mb_type'] != '1') {
  alert('관리자 접근만 허용됩니다.', './stock_list.php');
}


$cnt = sql_fetch("select SUM(wr_32) as kor, SUM(wr_36) as usa, SUM(wr_37) as tmp, SUM(wr_42) as fba , SUM(wr_43) as wfba , SUM(wr_44) as ufba  from g5_write_product");

$qstr .= "&amp;stx2={$stx2}";
$qstr .= "&amp;brand={$brand}";

if (!isset($view_warehouse) && $_COOKIE['cookie_warehouse']) {
  $view_warehouse = json_decode(stripslashes($_COOKIE['cookie_warehouse']), true);
}

function _get_checked($field_array, $value)
{
  if (is_array($field_array)) {
    return in_array($value, $field_array) ? ' checked="checked"' : '';
  }

  return ($field_array === $value) ? ' checked="checked"' : '';
}

?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
<style>
  .cnt_left {
    padding: 5px 10px;
    border-right: 1px solid #ddd;
    word-break: normal;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
  }

  .list_03 li {
    padding: 0;
    height: auto !important
  }

  .list_03 li .cnt_left {
    line-height: 43px
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

  @media (max-width: 767px) {
    .btn_top2 {
      margin-bottom: 10px
    }

    .stock_list_mtb {
      margin: 15px 0;
      font-size: 13px
    }

    .stock_list_mtb th {
      width: 60px
    }

    .stock_list_mtb td {
      padding: 10px
    }

    .stock_list_mtb .stock_table td {
      width: 16.66%;
      text-align: center;
      border-right: 1px solid #ddd;
    }

    .stock_list_mtb .stock_table th {
      border-right: 1px solid #ddd
    }

    .stock_list_mtb .stock_table {
      border: 0
    }
  }

  .select2-container--default .select2-selection--single {
    width: 100%;
    height: 37px;
    border: 1px solid #d9dee9;
    background: #f1f3f6;
    border-radius: 0
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow b {
    margin-top: 4px
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
    top: 45%;
    left: 50%;
    background: #fff;
    text-align: left;
    width: 250px;
    height: 300px;
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

  .modal_view2 {
    display: none;
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 999;
  }

  .modal_detail2 {
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

  .modal_detail2 .modal_cls {
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
    <h2 class="board_tit">기초재고 관리</h2>
    <form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php"
      onsubmit="return fboardlist_submit(this);" method="post">
      <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
      <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
      <input type="hidden" name="stx" value="<?php echo $stx2 ?>">
      <input type="hidden" name="spt" value="<?php echo $spt ?>">
      <input type="hidden" name="sst" value="<?php echo $sst ?>">
      <input type="hidden" name="sod" value="<?php echo $sod ?>">
      <input type="hidden" name="page" value="<?php echo $page ?>">
      <input type="hidden" name="sw" value="">

      <?php if ($is_category) { ?>
        <nav id="bo_cate">
          <h2><?php echo ($board['bo_mobile_subject'] ? $board['bo_mobile_subject'] : $board['bo_subject']) ?>
            카테고리</h2>
          <ul id="bo_cate_ul">
            <?php echo $category_option ?>
          </ul>
        </nav>
      <?php } ?>

      <div id="bo_li_top_op" style="padding-bottom: 0;">
        <div class="bo_list_total">
          <div class="local_ov01 local_ov">
            <span class="btn_ov01">
              <span class="ov_txt">전체</span>
              <span
                class="ov_num"><?php echo number_format($cnt['kor'] + $cnt['usa'] + $cnt['fba'] + $cnt['wfba'] + $cnt['ufba'] + $cnt['tmp']) ?>개</span>
            </span>
            <span class="btn_ov01">
              <span class="ov_txt">한국창고</span>
              <span class="ov_num"><?php echo number_format($cnt['kor']) ?>개</span>
            </span>
            <span class="btn_ov01">
              <span class="ov_txt">미국창고</span>
              <span class="ov_num"><?php echo number_format($cnt['usa']) ?>개</span>
            </span>
            <span class="btn_ov01">
              <span class="ov_txt">FBA창고</span>
              <span class="ov_num"><?php echo number_format($cnt['fba']) ?>개</span>
            </span>
            <span class="btn_ov01">
              <span class="ov_txt">W-FBA창고</span>
              <span class="ov_num"><?php echo number_format($cnt['wfba']) ?>개</span>
            </span>
            <span class="btn_ov01">
              <span class="ov_txt">U-FBA창고</span>
              <span class="ov_num"><?php echo number_format($cnt['ufba']) ?>개</span>
            </span>
            <span class="btn_ov01">
              <span class="ov_txt">임시창고</span>
              <span class="ov_num"><?php echo number_format($cnt['tmp']) ?>개</span>
            </span>
          </div>
        </div>
        <div style="display: flex; justify-content: start; align-items: center;">
          <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
            <div class="sch_bar" style="margin-top:3px; display: flex;">
              <div class="tw-mr-5">
                <label for="view_all">전체</label>
                <input id="view_all" type="checkbox" name="view_warehouse[]" value="all"
                  class="sch_view" <?= _get_checked($view_warehouse, 'all') ?>>
              </div>

              <?php foreach ($warehouseConfig as $key => $warehouse) {
              ?>
                <div class="tw-mr-5">
                  <label for="view_<?php echo $warehouse['filed']; ?>"><?php echo $warehouse['ware_name']; ?></label>
                  <input id="view_<?php echo $warehouse['filed']; ?>" type="checkbox" name="view_warehouse[]"
                    value="<?php echo $warehouse['filed']; ?>"
                    class="sch_view" <?= _get_checked($view_warehouse, $warehouse['filed']) ?>>
                </div>
              <?php } ?>

              <div class="tw-mr-5">
                <label for="view_wr_37">임시창고</label>
                <input id="view_wr_37" type="checkbox" name="view_warehouse[]" value="wr_37"
                  class="sch_view" <?= _get_checked($view_warehouse, 'wr_37') ?>>
              </div>
            </div>
          </div>
        </div>
        <div style="display: flex; justify-content: end; align-items: center;">
          <div>
            <ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?> tw-mb-[10px]">
              <?php if ($rss_href) { ?>
                <li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>

              <li>
                <select id="sorting_box" class="frm_input" style="height:37px">
                  <option value="default" <?php if ($sst == "wr_id" || !$sst) {
                                            echo 'selected';
                                          } ?>>기본정렬
                  </option>
                  <option value="up" <?php if ($sst == "stock" && $sod == "desc") {
                                        echo 'selected';
                                      } ?>>재고많은순
                  </option>
                  <option value="down" <?php if ($sst == "stock" && $sod == "asc") {
                                          echo 'selected';
                                        } ?>>재고적은순
                  </option>
                </select>
              </li>
              <li>
                <select id="brand" class="frm_input" style="height:37px">
                  <option value="">브랜드 선택</option>
                  <?php
                  $arr = get_code_list('1'); // 카테고리 코드 조회
                  foreach ($arr as $key => $value) {
                    $selected = $_GET['brand'] == $value['idx'] ? "selected" : "";
                    echo "<option value=\"{$value['idx']}\" {$selected} >{$value['code_name']}</option>";
                  }
                  ?>
                </select>
              </li>
              <li>
                <button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i>
                  검색
                </button>
              </li>
              <li>
                <button type="button" class="btn_b01 rack_form3">지정랙 일괄 변경</button>
              </li>
              <li>
                <button type="button" class="btn_b01 rack_form">창고별 랙 관리</button>
              </li>
              <li>
                <button type="button" class="btn_b01 rack_form2">랙별 상품현황</button>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div id="bo_li_01" style="clear:both;overflow-x:scroll;overflow-y:hidden">
        <?php if (!is_mobile()) { ?>
          <ul class="list_head"
            style="width:100%;min-width:3000px;position:sticky;top:0;background:#fff;z-index:2;">
            <li style="width:100px">
              <input type="checkbox" id="ALL_CHK">
            </li>
            <li style="width:150px"><?php echo subject_sort_link('wr_5', $qstr2, 1) ?>대표코드</a></li>
            <li style="width:150px"><?php echo subject_sort_link('wr_1', $qstr2, 1) ?>SKU</a></li>
            <li style="width:400px"><?php echo subject_sort_link('wr_subject', $qstr2, 1) ?>상품명</a></li>
            <?php if (!is_array($view_warehouse)) {
              $view_warehouse = [];
            }
            ?>
            <?php
            foreach ($warehouseConfig as $key => $warehouse) {
              $current_filed = $warehouse['filed'];

              if (in_array($current_filed, $view_warehouse) || in_array('all', $view_warehouse)) {
            ?>
                <li style="width:100px">
                  <?php echo subject_sort_link($current_filed, $qstr2, 1); ?>
                  <?php echo $warehouse['ware_name']; ?>
                  </a>
                </li>
            <?php
              }
            }
            ?>
            <?php if (in_array('wr_37', $view_warehouse) || in_array('all', $view_warehouse)) { ?>
              <li style="width:100px"><?php echo subject_sort_link('wr_37', $qstr2, 1) ?>임시창고</a></li>
            <?php } ?>
            <li style="width:120px">관리</li>
            <?php if (in_array($member['mb_id'], ['test', 'admin'])) { ?>
              <li style="width:120px">수정</li>
            <?php } ?>
            <?php if ($member['mb_type'] == 1) { ?>
              <li style="width:160px">관리자 기능</li>
            <?php } ?>
          </ul>
        <?php } ?>
        <div id="bo_li_01" class="list_03" style="min-width: 3000px;">
          <ul style="width:100%;height: 580px; overflow: auto;">
            <?php
            $sql_common = " from g5_write_product ";
            $sql_search = " where wr_delYn = 'N' ";

            if ($stx2) {
              $sql_search .= " and (wr_subject LIKE '%$stx2%' or (wr_1 LIKE '%{$stx2}%' or wr_27 LIKE '%{$stx2}%' or wr_28 LIKE '%{$stx2}%' or wr_29 LIKE '%{$stx2}%' or wr_30 LIKE '%{$stx2}%' or wr_31 LIKE '%{$stx2}%' or wr_5 LIKE '%{$stx2}%' or wr_6 LIKE '%{$stx2}%' or wr_4 LIKE '%{$stx2}%' ))  ";
            }

            if ($brand) {
              $sql_search .= " and wr_23 = '{$brand}' ";
            }

            if ($search_warehouse) {
              $sql_search .= " and $search_warehouse > 0 ";
            }

            if (!$sst) {
              $sst = "wr_id";
              $sod = "desc";
            }

            if ($sst == "stock") {
              $sst = "(wr_32+wr_36+wr_37+wr_42+wr_43+wr_44)";
            }

            $sql_order = " order by $sst $sod ";
            $sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
            $row = sql_fetch($sql);
            $total_count = $row['cnt'];

            $rows = 50;
            $total_page = ceil($total_count / $rows);
            if ($page < 1) {
              $page = 1;
            }
            $from_record = ($page - 1) * $rows;

            $cur_no = $total_count - $from_record;

            $sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";

            $rst = sql_query($sql);

            for ($i = 0; $row = sql_fetch_array($rst); $i++) { ?>
              <li class="modify" id="container-<?= $row['wr_id'] ?>" data="<?php echo $row['seq'] ?>"
                style="<?= ($row['wr_delYn'] == "Y") ? "background:yellow;" : "" ?>">
                <div class="num cnt_left" style="width:100px">
                  <input type="checkbox" name="wr_id_arr[]" value="<?= $row['wr_id'] ?>" /><br>
                </div>
                <div class="cnt_left" style="width:150px"><?php echo $row['wr_5'] ?> <a
                    href="/bbs/write.php?bo_table=product&w=u&wr_id=<?php echo $row['wr_id'] ?>" target="_blank"
                    title="제품관리 바로가기"><i class="fa fa-link" aria-hidden="true"></i></a></div>
                <div class="cnt_left" style="width:150px"
                  title="<?php echo $row['wr_1'] ?>"><?php echo $row['wr_1'] ?>
                </div>
                <div class="cnt_left" style="width:400px;" title="<?php echo $row['wr_subject'] ?>">
                  <a href="javascript:;"
                    onclick="fnSalesMissModal('<?= $row['wr_id'] ?>');"><?php echo $row['wr_subject'] ?></a>
                </div>

                <?php if ($is_force_update == 'Y') {
                  foreach ($warehouseConfig as $key => $warehouse) {
                    $current_filed = $warehouse['filed'];
                    $current_filed_real = $warehouse['filed_real'];

                    if (in_array($current_filed, $view_warehouse) || in_array('all', $view_warehouse)) {
                ?>
                      <div class="cnt_left" style="width:100px;text-align:center;margin:0;padding:0;">
                        <input type="text" name="<?php echo $current_filed; ?>" class="<?php echo $current_filed; ?> frm_input"
                          style="width:40px;text-align:right;background:#ffe8ea;margin:0;padding:0;"
                          value="<?php echo (int)$row[$current_filed]; ?>"> / <?php echo (int)$row[$current_filed_real]; ?>
                      </div>
                    <?php
                    }
                  }
                  if (in_array('wr_37', $view_warehouse) || in_array('all', $view_warehouse)) { ?>
                    <div class="cnt_left" style="width:100px;text-align:right">
                      <input type="text" name="wr_37" class="wr_37 frm_input"
                        style="width:100%;text-align:right"
                        value="<?php echo $row['wr_37'] ?>">
                    </div>
                  <?php } ?>

                  <div class="cnt_left" style="width:120px;text-align:center">
                    <!--<button type="button" class="btn btn_b01 save_btn" data="<?php echo $row['wr_id'] ?>" >변경하기</button>-->
                    <button type="button" class="btn btn_b02 view_btn" data="<?php echo $row['wr_id'] ?>">재고관리
                    </button>
                  </div>

                  <?php } else {
                  foreach ($warehouseConfig as $warehouse_key => $warehouse) {
                    // 현재 창고의 'filed' 값을 가져옵니다. (예: 'wr_32', 'wr_33', ...)
                    $current_filed = $warehouse['filed'];
                    // 현재 창고의 'filed_real' 값을 가져옵니다. (예: 'wr_32_real', 'wr_33_real', ...)
                    $current_filed_real = $warehouse['filed_real'];

                    // 기존의 if 조건문을 동적으로 적용합니다.
                    if (in_array($current_filed, $view_warehouse) || in_array('all', $view_warehouse)) {
                  ?>
                      <div class="cnt_left" style="width:100px;text-align:right;margin:0;padding:0;">
                        <?= $row[$current_filed] ?> /
                        <input type="text" name="<?= $current_filed; ?>" class="<?= $current_filed; ?> frm_input"
                          style="width:40px;text-align:right;margin:0;padding:0;" value="<?= $row[$current_filed_real]; ?>">
                        <a href="#none" onclick="rack_info(<?php echo $row['wr_id'] ?>, '<?php echo $warehouse_key; ?>')">▼</a>
                        <div id="rack_info_<?php echo $row['wr_id'] . $warehouse_key; ?>"></div>
                      </div>
                    <?php
                    }
                  }

                  if (in_array('wr_37', $view_warehouse) || in_array('all', $view_warehouse)) { ?>
                    <div class="cnt_left" style="width:100px;text-align:right">
                      <input type="text" name="wr_37" class="wr_37 frm_input"
                        style="width:100%;text-align:right"
                        value="<?php echo $row['wr_37'] ?>">
                    </div>
                  <?php } ?>

                  <div class="cnt_left" style="width:120px;text-align:center">
                    <!--<button type="button" class="btn btn_b01 save_btn" data="<?php echo $row['wr_id'] ?>" >변경하기</button>-->
                    <button type="button" class="btn btn_b02 view_btn" data="<?php echo $row['wr_id'] ?>">재고관리
                    </button>
                  </div>

                <?php } ?>
                <?php if (in_array($member['mb_id'], ['test', 'admin'])) { ?>
                  <div class="cnt_left" style="width:120px;text-align:center">
                    <button type="button" class="btn btn_b02" onclick="wr_stock_update('<?= $row['wr_id'] ?>');"
                      data="<?php echo $row['wr_id'] ?>" style="background: #fd7e14; color: white;">재고수정
                    </button>
                  </div>
                <?php } ?>
                <?php if (in_array($member['mb_id'], ['test', 'admin'])) { ?>
                  <div class="cnt_left" style="width:180px;text-align:center">
                    <?php if ($is_force_update == 'Y') { ?>
                      <button type="button" class="btn btn_b02" onclick="wr_stock_force_update_activate(false);"
                        data="<?php echo $row['wr_id'] ?>" style="background: #dc3545; color: white;">강제
                        재고수정
                        비활성화
                      </button>

                    <?php } else { ?>
                      <button type="button" class="btn btn_b02" onclick="wr_stock_force_update_activate(true);"
                        data="<?php echo $row['wr_id'] ?>" style="background: #dc3545; color: white;">강제
                        재고수정
                        활성화
                      </button>
                    <?php } ?>
                  </div>
                <?php } ?>
              </li>
            <?php
            }
            $cur_no = $cur_no - 1;
            ?>
            <?php if ($i == 0) {
              echo '<li class="empty_table">내역이 없습니다.</li>';
            } ?>
          </ul>
        </div>
      </div>
    </form>
  </div>
  <?php
  $query_params = $_GET;
  unset($query_params['page']); // 기존 page 제거

  // http_build_query() 사용 후, 배열 키에서 숫자 제거
  $query_string = urldecode(http_build_query($query_params));
  $query_string = preg_replace('/view_warehouse\[\d+\]=/', 'view_warehouse[]=', $query_string);

  $paging_url = $_SERVER['SCRIPT_NAME'] . '?' . $query_string . '&amp;page=';

  echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $paging_url);
  ?>
</div>

<!-- 주문건 확인 팝업-->
<div class="modal_view2">
  <div class="modal_detail2" id="modal_view_order"></div>
  <div class="bo_sch_bg"></div>
</div>
<!--// 주문건 확인 팝업-->


<!-- 담당자 지정랙 일괄 변경 -->
<div class="modal_view">
  <div class="modal_detail" id="modal_view_calc"></div>
  <div class="bo_sch_bg"></div>
</div>
<!--// 담당자 지정랙 일괄 변경 -->

<div class="bo_sch_wrap">
  <fieldset class="bo_sch">
    <h3>검색</h3>
    <form name="fsearch" method="get">

      <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
        <div class="sch_bar" style="margin-top:3px">
          <input type="text" name="stx2" value="<?php echo stripslashes($stx2) ?>" id="stx" class="sch_input"
            size="25" maxlength="255" placeholder="대표코드/SKU/상품명으로 검색">
        </div>
      </div>
      <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
        <select name="search_warehouse" id="search_warehouse" class="frm_input search_sel" style="width: 100%;">
          <option value="">전체 창고</option>
          <option value="wr_32" <?= get_selected('wr_32', $search_warehouse) ?>>한국창고</option>
          <option value="wr_36" <?= get_selected('wr_36', $search_warehouse) ?>>미국창고</option>
          <option value="wr_42" <?= get_selected('wr_42', $search_warehouse) ?>>FBA창고</option>
          <option value="wr_43" <?= get_selected('wr_43', $search_warehouse) ?>>W-FBA창고</option>
          <option value="wr_44" <?= get_selected('wr_44', $search_warehouse) ?>>U-FBA창고</option>
          <option value="wr_37" <?= get_selected('wr_37', $search_warehouse) ?>>임시창고</option>
          <option value="wr_40" <?= get_selected('wr_40', $search_warehouse) ?>>한국반품창고</option>
          <option value="wr_41" <?= get_selected('wr_41', $search_warehouse) ?>>미국반품창고</option>
          <option value="wr_45" <?= get_selected('wr_45', $search_warehouse) ?>>한국폐기창고</option>
          <option value="wr_46" <?= get_selected('wr_46', $search_warehouse) ?>>미국폐기창고</option>
        </select>
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
<script>
  jQuery(function($) {
    // 게시판 검색
    $(".btn_bo_sch").on("click", function() {
      $(".bo_sch_wrap").toggle();
    })
    $('.bo_sch_bg, .bo_sch_cls').click(function() {
      $('.bo_sch_wrap').hide();
    });
  });
</script>
<script>
  $(document).ready(function() {
    $("#ALL_CHK").click(function() {
      var chk = $(this).is(":checked");
      $("input[name='wr_id_arr[]']").prop("checked", chk);
    });

    $("input[name='wr_id_arr[]']").click(function() {
      var chk = true;
      $("input[name='wr_id_arr[]']").each(function() {
        if ($(this).is(":checked") == false) {
          chk = false;
        }
      });
      $("#ALL_CHK").prop("checked", chk);
    });
  });

  function wr_stock_update(wr_id) {
    const is_force_update = '<?= $is_force_update ?>';
    const mb_id = '<?= $member['mb_id'] ?>';
    const container = document.querySelector('#container-' + wr_id);
    const wr_32 = container.querySelector('[name=wr_32]').value;
    const wr_36 = container.querySelector('[name=wr_36]').value;
    const wr_42 = container.querySelector('[name=wr_42]').value;
    const wr_43 = container.querySelector('[name=wr_43]').value;
    const wr_44 = container.querySelector('[name=wr_44]').value;
    const wr_37 = container.querySelector('[name=wr_37]').value;
    const wr_40 = container.querySelector('[name=wr_40]').value;
    const wr_41 = container.querySelector('[name=wr_41]').value;
    const wr_45 = container.querySelector('[name=wr_45]').value;
    const wr_46 = container.querySelector('[name=wr_46]').value;

    $.post('./ajax.wr_stock_check.php', {
      wr_id,
      wr_32,
      wr_36,
      wr_42,
      wr_43,
      wr_44,
      wr_37,
      is_force_update,
      mb_id,
      wr_40,
      wr_41,
      wr_45,
      wr_46
    }, (response) => {
      let message = response.message;
      let success = response.success;

      if (!success) {
        alert(message);
        return;
      }

      if (!confirm(message)) {
        return;
      }

      $.post('./ajax.wr_stock_update.php', {
        wr_id,
        wr_32,
        wr_36,
        wr_42,
        wr_43,
        wr_44,
        wr_37,
        wr_40,
        wr_41,
        wr_45,
        wr_46,
        is_force_update,
        mb_id
      }, (response) => {
        let message = response.message;
        let success = response.success;

        alert(message);

        if (success) {
          location.reload();
        }


      }, 'json');


    }, 'json');

  }

  function wr_stock_force_update_activate(isActivate = true) {
    const currentUrl = new URL(window.location.href);

    if (isActivate) {
      currentUrl.searchParams.set('is_force_update', 'Y');
    } else {
      currentUrl.searchParams.delete('is_force_update');
    }


    console.log(currentUrl.toString());

    window.history.pushState({}, '', currentUrl);

    location.href = currentUrl.toString();
  }


  function rack_info(wr_id, warehouse) {

    $.post('./ajax.rack_info.php', {
      wr_id: wr_id,
      warehouse: warehouse
    }, function(data) {
      $('#rack_info_' + wr_id + warehouse).html(data);
      $('#rack_info_' + wr_id + warehouse).show();

    })

  }

  // 해당제품의 미처리건 엑셀다운로드
  function fnSalesMissModal(wr_id) {
    var params = "wr_id=" + wr_id;
    $.post("./ajax.stock_miss_modal.php", params, function(data) {
      $("#modal_view_order").html(data);
      $(".modal_view2").toggle();

    });
    //document.location.href="./ajax.stock_miss_excel.php?wr_id="+wr_id;
  }

  $(function() {
    $(document).on('click', '.close_rackinfo', function() {
      $(this).parent().hide();
    })

    $('#brand').select2();

    $('#sorting_box').bind('change', function() {

      let sort = $(this).val();

      if (sort == "default") {
        location.href = '?sst=wr_id&sod=desc&stx2=<?php echo $stx2 ?>';
      } else if (sort == "up") {
        location.href = '?sst=stock&sod=desc&stx2=<?php echo $stx2 ?>';
      } else if (sort == "down") {
        location.href = '?sst=stock&sod=asc&stx2=<?php echo $stx2 ?>';
      }
    })
    $('#brand').bind('change', function() {

      let brand = $(this).val();

      location.href = '?brand=' + brand;

    })

    $('.save_btn').bind('click', function() {

      let id = $(this).attr('data');
      let stock1 = $(this).closest('li').find('.wr_32').val();
      let stock2 = $(this).closest('li').find('.wr_36').val();
      let stock3 = $(this).closest('li').find('.wr_37').val();
      let stock4 = $(this).closest('li').find('.wr_42').val();
      let stock5 = $(this).closest('li').find('.wr_43').val();
      let stock6 = $(this).closest('li').find('.wr_44').val();

      const obj = {
        wr_id: id,
        stock1: stock1,
        stock2: stock2,
        stock3: stock3,
        stock4: stock4,
        stock5: stock5,
        stock6: stock6
      };

      $.post('./stock_update.php', obj, function(data) {
        if (data == "y") {
          alert('재고수량이 저장되었습니다.');
        } else {
          alert('처리 중 오류가 발생했습니다.');
        }
      })

    });

    $('.view_btn').bind('click', function() {
      if ("<?= $member['mb_id'] ?>" != "test") {
        // alert("테스트 진행 중 입니다 잠시 후 이용해주세요.");
        // return false;
      }

      let id = $(this).attr('data');
      var _width = '1150';
      var _height = '800';

      var _left = Math.ceil((window.screen.width - _width) / 2);
      var _top = Math.ceil((window.screen.height - _height) / 2);

      window.open("./pop_rack.php?wr_id=" + id, "pop_rack" + id, "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

      return false;
    });

    $('.rack_form').bind('click', function() {
      if ("<?= $member['mb_id'] ?>" != "test") {
        // alert("테스트 진행 중 입니다 잠시 후 이용해주세요.");
        // return false;
      }

      var _width = '1500';
      var _height = '800';

      var _left = Math.ceil((window.screen.width - _width) / 2);
      var _top = Math.ceil((window.screen.height - _height) / 2);

      window.open("./pop_rack_form.php", "pop_rack_form", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

      return false;
    });

    $('.rack_form2').bind('click', function() {
      if ("<?= $member['mb_id'] ?>" != "test") {
        // alert("테스트 진행 중 입니다 잠시 후 이용해주세요.");
        // return false;
      }

      var _width = '1500';
      var _height = '800';

      var _left = Math.ceil((window.screen.width - _width) / 2);
      var _top = Math.ceil((window.screen.height - _height) / 2);

      window.open("./stock_chk.php", "pop_rack_form2", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

      return false;
    });

    // 지정랙 일괄 변경 모달 출력
    $('.rack_form3').bind('click', function() {
      var chk = 0;
      var chk_arr = new Array();
      $("input[name='wr_id_arr[]']").each(function() {
        if ($(this).is(":checked")) {
          chk_arr[chk] = $(this).val();
          chk++;
        }
      });

      var chk_arr_str = chk_arr.join("|");

      if (chk > 0) {
        var params = "chk_arr_str=" + chk_arr_str;
        $.post("./stock_update_modal.php", params, function(data) {
          $("#modal_view_calc").html(data);
          $(".modal_view").toggle();
        });
      } else {
        alert("일괄 변경할 상품을 선택해주세요.");
      }
      return false;
    });

    $(".sch_view").on("change", function() {
      let currentParams = new URLSearchParams(window.location.search);
      let selectedValues = [];

      if (this.id === "view_all") {
        if ($(this).is(":checked")) {
          $(".sch_view").not("#view_all").prop("checked", false);
          selectedValues = ["all"];
        }
      } else {
        $("#view_all").prop("checked", false);
        $(".sch_view:checked").each(function() {
          if (this.id !== "view_all") {
            selectedValues.push($(this).val());
          }
        });
      }

      // 기존 URL 파라미터 유지
      let form = $("<form>", {
        method: "GET",
        action: window.location.pathname
      });

      // 기존 URL의 모든 파라미터 추가
      currentParams.forEach((value, key) => {
        if (key !== "view_warehouse[]") {
          form.append($("<input>", {
            type: "hidden",
            name: key,
            value: value
          }));
        }
      });

      // 선택된 체크박스 값을 추가
      selectedValues.forEach(value => {
        form.append($("<input>", {
          type: "hidden",
          name: "view_warehouse[]",
          value: value
        }));
      });

      set_cookie('cookie_warehouse', JSON.stringify(selectedValues), 60 * 60 * 24 * 365);

      $("body").append(form);
      form.submit();
    });


  }); // init func end

  // 모달 닫기
  function close_modal() {
    $(".bo_sch_bg").hide();
    $(".modal_view").hide();
    $(".modal_view2").hide();
    $("#modal_view_calc").empty();
  }
</script>


<?php
include_once(G5_THEME_PATH . '/tail.php');
