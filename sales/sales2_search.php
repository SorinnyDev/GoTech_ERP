<?php
include_once('./_common.php');

if ($is_guest)
  alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

if (!$wr_19_s)
  $wr_19_s = G5_TIME_YMD;

if (!$wr_19_e)
  $wr_19_e = G5_TIME_YMD;


if (isset($nm_type)) {
  set_cookie('nm_type', $nm_type, 60 * 60 * 24 * 365);
}

if (!isset($nm_type) && get_cookie('nm_type')) {
  $nm_type = get_cookie('nm_type');
}

?>
  <style>
      .not_item td {
          background: red;
          color: #fff
      }

      .bg1 td {
          background: #eff3f9
      }
  </style>
  <div class="new_win">
    <h1>발주자료 가져오기</h1>


    <form name="searchFrm" method="get" autocomplete="off">

      <div id="excelfile_upload" style="">
        <label for="excelfile">도메인</label>
        <select name="wr_18">
          <option value="">선택하세요</option>
          <?php echo get_domain_option($_GET['wr_18']) ?>
        </select>
        <select name="mb_id" id="wmb_id" class="frm_input search_sel">
          <option value="">담당자 전체</option>
          <?php
          $mbSql = " select mb_id, mb_name from g5_member where del_yn = 'N' order by mb_name asc";
          $mbRst = sql_query($mbSql);
          for ($i = 0; $mb = sql_fetch_array($mbRst); $i++) {
            ?>
            <option value="<?php echo $mb['mb_id'] ?>" <?php echo get_selected($mb['mb_id'], $mb_id) ?>><?php echo $mb['mb_name'] ?>(<?php echo $mb['mb_id'] ?>)</option>
          <?php } ?>
        </select>
        <label for="excelfile" style="margin-left:50px">제품명</label>
        <select class="frm_input" name="nm_type" id="nm_type">
          <option value="type_1" <?= get_selected('type_1', $nm_type) ?>>제품명칭</option>
          <option value="type_2" <?= get_selected('type_2', $nm_type) ?>>몰타이틀</option>
        </select>
        <label for="excelfile" style="margin-left:50px">발주기간</label>
        <input type="date" name="wr_19_s" value="<?php echo urldecode($wr_19_s) ?>" class="frm_input"> ~
        <input type="date" name="wr_19_e" value="<?php echo urldecode($wr_19_e) ?>" class="frm_input">
        <input type="text" name="stx" value="<?php echo urldecode($stx) ?>" class="frm_input" placeholder="주문번호/대표코드 검색">
        <input type="text" name="tracking_no" value="<?php echo urldecode($tracking_no) ?>" class="frm_input" placeholder="트래킹번호 검색">
        <button class="btn btn_admin">검색</button>

      </div>

    </form>

    <form name="frm" action="./sales2_search_update.php" method="post">
      <div id="excelfile_upload" class="result_list" style="clear:both;overflow-x:scroll;max-height:79vh;padding:0">

        <div class="tbl_head01 tbl_wrap" style="min-width:1700px;width:100%;margin-bottom:70px">
          <table>
            <thead style="position:sticky;top:0;">
            <tr>
              <th style="width:20px"><input type="checkbox" onclick="selectAll(this)"></th>
              <th style="width:50px">순번</th>
              <th style="width:100px">매출일자</th>
              <th style="width:100px">발주일자</th>
              <th style="width:100px">도메인명</th>
              <th style="width:100px">담당자</th>
              <th style="width:200px">주문번호</th>
              <th style="width:60px">특송</th>
              <th style="width:150px">SKU</th>
              <th style="width:150px">대표코드</th>
              <th style="width:400px">상품명</th>
              <th style="width:70px">한국재고</th>
              <th style="width:70px">발주수량</th>
              <th style="width:70px">주문수량</th>

              <th style="width:150px">주문자명</th>
              <th style="width:70px">단가</th>
              <th style="width:70px">신고가격</th>
              <th style="width:70px">개당무게</th>
              <th style="width:70px">총 무게</th>
              <th style="width:150px">HS CODE</th>
              <th style="width:150px">나라명</th>

            </tr>
            </thead>
            <tbody>
            <?php
            if ($wr_18)
              $sql_search .= " and a.wr_domain = '{$wr_18}'";

            if ($mb_id)
              $sql_search .= " and b.mb_id = '{$mb_id}'";

            if ($wr_19_s && $wr_19_e) {
              $sql_search .= " and a.wr_date2 BETWEEN '{$wr_19_s}' AND '{$wr_19_e}' ";
            }

            if ($stx) {
              $sql_search .= " AND ( a.wr_order_num LIKE '%$stx%' or b.wr_1  LIKE '%$stx%' or b.wr_5  LIKE '%$stx%' ) ";
            }

            if ($tracking_no) {
              $sql_search .= " AND wr_order_traking LIKE '%" . $tracking_no . "%'";
            }

            $sql_search .= " and a.wr_warehouse != '3000'";
            if ($sql_search) {
              $sql = "select * from g5_sales1_list a
              LEFT JOIN g5_write_product b ON b.wr_id=a.wr_product_id
              
              where (1) {$sql_search} and a.wr_date != '' and wr_chk = 0 order by a.seq desc";
              //echo $sql;
              $rst = sql_query($sql);
              for ($i = 0; $row = sql_fetch_array($rst); $i++) {

                /*$item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");*/
                //24.06.28 세트상품제외 같은 주문번호가 있을 경우 패스
                $chk = sql_fetch("select * from g5_sales2_list where wr_order_num = '{$row['wr_order_num']}' and wr_set_sku = ''");

                if ($chk) {
                  continue;
                }

                $mb = get_member($row['mb_id'], 'mb_name');

                $bg = "";
                $ea_chk = "";

                $total_ea = (int)$row['wr_32'] + (int)$row['wr_order_ea'];


                if ($total_ea < $row['wr_ea']) {
                  $ea_chk = ";color:blue;font-weight:600";
                }
                $imsi_item = "";
                if ($row['ca_name'] == "임시") {
                  $imsi_item = "color:blue";
                } else if ($row['ca_name'] == "최종확정") {
                  $imsi_item = "color:red";
                }

                if ($row['wr_servicetype'] === '0001') {
                  $express_title = '<i class="fa fa-check" aria-hidden="true" style="color:green"></i>';
                } else {
                  $express_title = '';
                }

                $set = ($row['wr_set_sku']) ? '<br><span style="color:blue">(' . $row['wr_set_sku'] . ')</span>' : "";
                $bg = 'bg' . ($i % 2);
                ?>
                <tr class="<?php echo $bg ?>">
                  <td><input type="checkbox" name="chk_seq[]" value="<?php echo $row['seq'] ?>" class="chkbox"></td>
                  <td><?php echo($i + 1) ?></td>
                  <td><?php echo substr($row['wr_date'], 0, 10) ?></td>
                  <td><?php echo substr($row['wr_date2'], 0, 10) ?></td>
                  <td><?php echo $row['wr_domain'] ?></td>
                  <td><?php echo $mb['mb_name'] ?></td>
                  <td><?php echo $row['wr_order_num'] . $set ?></td>
                  <td><?php echo $express_title ?></td>
                  <td style="<?php echo $imsi_item ?>"><?php echo $row['wr_code'] ?></td>
                  <td style="<?php echo $imsi_item ?>"><?php echo $row['wr_5'] ?></td>
                  <td style="text-align:left;<?php echo $imsi_item ?>"><?= $nm_type === 'type_1' ? $row['wr_subject'] : $row['wr_product_nm'] ?></td>
                  <td style="text-align:right;"><?php echo $row['wr_32'] ?></td>
                  <td style="text-align:right;<?php echo $ea_chk ?>"><?php echo $row['wr_order_ea'] ?></td>
                  <td style="text-align:right"><?php echo $row['wr_ea'] ?></td>

                  <td><?php echo $row['wr_mb_name'] ?></td>
                  <td style="text-align:right"><?php echo $row['wr_danga'] ?></td>
                  <td style="text-align:right"><?php echo $row['wr_singo'] ?></td>
                  <td><?php echo $row['wr_weight1'] ?></td>
                  <td><?php echo $row['wr_weight2'] ?></td>
                  <td><?php echo $row['wr_hscode'] ?></td>
                  <td><?php echo $row['wr_country'] ?></td>

                </tr>
              <?php }

            } ?>
            </tbody>


          </table>
        </div>

      </div>

      <div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;background:#fff;border-top:1px solid #ddd;margin-bottom:0">
        입고 일자 <input type="date" name="wr_date3" class="frm_input" value="<?php echo G5_TIME_YMD ?>" required>
        <select name="warehouse" required>
          <option value="">입고창고 선택</option>
          <option value="1000" <?php echo get_selected($_GET['warehouse'], '1000') ?>>한국창고</option>
          <option value="3000" <?php echo get_selected($_GET['warehouse'], '3000') ?>>미국창고</option>
          <option value="4000" <?php echo get_selected($_GET['warehouse'], '4000') ?>>FBA창고</option>
          <option value="5000" <?php echo get_selected($_GET['warehouse'], '5000') ?>>W-FBA창고</option>
          <option value="6000" <?php echo get_selected($_GET['warehouse'], '6000') ?>>U-FBA창고</option>
        </select>
        <input type="submit" value="입고생성" class="btn_submit btn">
        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>


        <button type="button" class="btn02 btn" onclick="export_excel();" style="height:37px;float:right"><i class="fa fa-file-excel-o" aria-hidden="true"></i> 엑셀출력</button>
      </div>
    </form>


  </div>
  <script>
    function export_excel() {

      location.href = './sales2_excel.php?stx=<?php echo urldecode($stx)?>&wr_18=<?php echo $wr_18?>&wr_19_s=<?php echo $wr_19_s?>&wr_19_e=<?php echo $wr_19_e?>';
      return false;
    }

    $(function () {
      $('.chkbox').bind('click', function () {
        let stat = $(this).is(':checked');

        if (stat) {
          $(this).closest('tr').find('td').addClass('selected_line');
        } else {
          $(this).closest('tr').find('td').removeClass('selected_line');
        }
      })
    })

    function add_pop(sku, pname, wr_id) {

      window.open("/bbs/write.php?bo_table=product&sku=" + sku + "&pname=" + pname + "&swr_id=" + wr_id, "add_item", "left=50, top=50, width=550, height=650, scrollbars=1");

    }

    function selectAll(selectAll) {
      const checkboxes
        = document.getElementsByName('chk_seq[]');

      checkboxes.forEach((checkbox) => {
        if (checkbox.disabled == true) {

        } else {
          checkbox.checked = selectAll.checked;
        }
      })
      $('.chkbox').each(function () {
        let stat = $(this).is(':checked');

        if (stat) {
          $(this).closest('tr').find('td').addClass('selected_line');
        } else {
          $(this).closest('tr').find('td').removeClass('selected_line');
        }
      })
    }

    /* 제품명 몰타이틀/제품명칭 선택할 수 있게끔 */
    $(document).on('change', '#nm_type', function () {
      const currentUrl = new URL(window.location.href);
      currentUrl.searchParams.set('nm_type', this.value);

      window.location.href = currentUrl.toString();
    });

  </script>
<?php
include_once(G5_PATH . '/tail.sub.php');