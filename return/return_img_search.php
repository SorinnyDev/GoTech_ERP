<?php
include_once('./_common.php');
include_once(G5_PATH . '/head.sub.php');

if ($is_guest)
  alert_close('로그인 후 이용하세요.');

add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

if (!isset($date1) || isEmpty($date1)) {
  $date1 = date('Y-m-01');
}

if (!isset($date2) || isEmpty($date2)) {
  $date2 = date('Y-m-d');
}

$sql_common = " from g5_return_list ";
$sql_search = " where wr_state != 0 ";


if ($stx) {
  $sql_search .= " and ( wr_order_num LIKE '%$stx%')";
}

if (!$sst) {
  $sst = "seq";
  $sod = "desc";
}

if (isNotEmpty($date1) && isNotEmpty($date2)) {
  $sql_search .= " and wr_datetime between '$date1 00:00:00'  and '$date2 23:59:59' ";
}

$sql_order = " order by $sst $sod ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) {
  $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
}
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";

$result = sql_fetch_all($sql);

$current_params = $_GET;
unset($current_params['page']);

$qstr = http_build_query($current_params);

?>
  <style>
      .not_item td {
          background: red;
          color: #fff
      }

      .pg a {
          margin: 0 5px
      }
      .date {
          width: 250px;
          height: 35px;
          padding: 0;
          display: flex;
          border: 1px solid #d5d5d5;
      }
  </style>
  <div class="new_win">
    <h1>반품정보 선택</h1>
    <form name="searchFrm" method="get" autocomplete="off">
      <div id="excelfile_upload" style="display: flex; gap: 5px;">
        <input type="text" name="stx" value="<?php echo urldecode($stx) ?>" class="frm_input" placeholder="주문번호 검색">
        <div class="date">
          <input type="date" name="date1" value="<?php echo stripslashes($date1) ?>" id="stx" class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
          <span style="display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
          <input type="date" name="date2" value="<?php echo stripslashes($date2) ?>" id="stx" class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
        </div>
        <button class="btn btn_admin" style="height: 35px;">검색</button>
      </div>
    </form>

    <form name="frm" action="" method="post" onsubmit="return chkfrm(this);">
      <div id="excelfile_upload" class="result_list" style="overflow-y:scroll;max-height:400px;padding:0">
        <div class="tbl_head01 tbl_wrap">
          <table>
            <thead style="position:sticky;top:0;">
            <tr>
              <th style="width:100px">주문번호</th>
              <th style="width:400px">상품명</th>
              <th style="width:70px">등록일시</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($result as $row) {


              $bg = "";
              $ea_chk = "";
              $ea_chk2 = "";

              $set = "";
              if ($row['wr_set_sku']) {

                $set = '<br><span style="color:blue">(' . $row['wr_set_sku'] . ')</span>';
              }

              $item = sql_fetch("select wr_id, wr_subject, wr_1 from g5_write_product where wr_id = '{$row['product_id']}'");
              ?>
              <tr>
                <td><?php echo $row['wr_order_num'] ?></td>
                <td style="cursor:pointer;text-align:left" onmouseover="this.style.background='#ddd'" onmouseout="this.style.background='#fff'"
                    onclick="add_item('<?php echo $row['seq'] ?>', '<?php echo $row['wr_order_num'] ?>', '<?php echo addslashes($item['wr_subject']) ?>', '<?php echo $item['wr_id'] ?>')"><?php echo $item['wr_subject'] ?></td>
                <td><?php echo substr($row['wr_datetime'], 2, 9) ?></td>
              </tr>
            <?php }

            ?>
            </tbody>


          </table>
        </div>

      </div>


    </form>
    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?' . $qstr . '&amp;page='); ?>

  </div>
  <script>


    function add_item(seq, order_num, wr_subject, wr_id) {

      opener.window.document.frm.return_id.value = seq;
      opener.window.document.frm.order_num.value = order_num;
      opener.window.document.frm.pdt_name.value = wr_subject;
      opener.window.document.frm.product_id.value = wr_id;


      window.close();
    }

  </script>
<?php
include_once(G5_PATH . '/tail.sub.php');