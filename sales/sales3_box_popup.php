<?php
include_once('./_common.php');

if ($is_guest) alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

if (!$seq) {
  alert_close('잘못된 접근입니다.');
}

$query = "select * from g5_sales3_list where seq = '$seq'";

$sales3 = sql_fetch($query);

if (!$st_date) {
  $st_date = date('Y-m-01') . ' 00:00:00';
}

if (!$ed_date) {
  $ed_date = date('Y-m-d') . ' 23:59:59';
}

$query = "
select b.id, b.box_code, b.reg_datetime, s0.* from g5_stock_box as b
left join g5_stock_box_order as bo on b.box_code = bo.box_code
left join g5_sales0_list as s0 on s0.wr_order_num = bo.wr_order_num        
where (1) and b.reg_datetime between '$st_date' and '$ed_date'
group by b.id
order by b.reg_datetime desc
";


$list = sql_fetch_all($query);


?>

  <style>
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
          padding: 10px 10px
      }

      .tbl_frm01 td input {
          border: 1px solid #ddd;
          padding: 3px;
          width: 100%;
          min-width: 150px;
          height: 30px
      }

      .tbl_frm01 .btn_b02 {
          height: 30px;
          line-height: 30px
      }

      .tbl_frm01 input.readonly {
          background: #f2f2f2
      }

      .not_item td {
          background: red;
          color: #fff
      }

      .pg a {
          margin: 0 5px
      }

      .tbl_head01 td {
          background: none
      }

      .move_stock th {
          width: 100px;
          background: #ddd
      }

      .move_stock select {
          width: 30% !important
      }

      .move_stock button {
          width: 30% !important;
          height: 35px;
          line-height: 35px;
          border: 1px solid #2aba8a;
          background: #2aba8a;
          color: #fff
      }

      .move_stock2 td {
          padding: 15px
      }

      .down_arrow {
          position: relative
      }

      .down_arrow::after {
          content: "↓";
          position: absolute;
          top: -14px;
          font-size: 20px;
          font-weight: 600;
          left: 50%;
          background: #fff;
          margin-left: -20px;
          color: #2aba8a
      }

      .diabled_btn {
          background: #ddd;
          cursor: not-allowed
      }
  </style>
  <div class="new_win">
  <h1>박스 목록</h1>

  <div id="excelfile_upload" class="result_list" style="padding:12px;">

    <form name="frm" method="post" action="./ajax.sales_create_box_order.php">
      <input type="hidden" name="seq" value="<?= $sales3['seq'] ?>">
      <div id="excelfile_upload" class="result_list" style="padding:12px;">

        <div style="clear:both"></div>
        <div class="tbl_head01 tbl_wrap">
          <table>
            <thead style="position:sticky;top:0;">

            <tr>
              <th></th>
              <th>박스번호</th>
              <th>등록일</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($list as $k => $row) {
              $bg = 'bg' . ($k % 2);
              ?>

              <tr class="<?php echo $bg; ?>" onclick="checkedArea('<?= $row['box_code'] ?>');">
                <td><input type="radio" value="<?= $row['box_code'] ?>" id="<?= $row['box_code'] ?>" name="box_code" <?= $k == 0 ? 'checked' : '' ?>></td>
                <td><?= $row['box_code'] ?></td>
                <td><?= $row['reg_datetime'] ?></td>
              </tr>
            <?php } ?>
            </tbody>


          </table>
        </div>
        <div class="tw-flex tw-justify-center tw-space-x-5">
          <button type="submit" class="btn_b01">적용</button>
          <button type="button" class="btn_b02" onclick="createBox();">박스생성</button>
          <button type="button" class="btn_b02" onclick="window.close();">닫기</button>
        </div>
      </div>

    </form>
  </div>

  <script>
    function createBox() {
      if (!confirm("신규 박스를 생성하겠습니까?")) {
        return;
      }

      $.post(
        "../stock/ajax.create_box.php",
        {  },
        (response) => {
          if (response.result) {
            location.reload();
          }
        }, 'json'
      );
    }

    function checkedArea(area) {
      document.getElementById(area).checked = true;
    }
  </script>
<?php
include_once(G5_PATH . '/tail.sub.php');
