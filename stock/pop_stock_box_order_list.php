<?php
include_once('./_common.php');

if ($is_guest) alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

if (!$box_code) {
  alert_close('잘못된 접근입니다.');
}

$query = "
select b.id, b.box_code, bo.reg_datetime, s3.*
from g5_stock_box_order as bo
         left join g5_stock_box as b on b.box_code = bo.box_code
         left join g5_sales3_list as s3 on s3.wr_order_num = bo.wr_order_num
where bo.is_deleted = false
    and b.box_code = '$box_code'
order by b.reg_datetime desc
";

$result = sql_fetch_all($query);

$list = $result;


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
  <h1><strong><?= $box_code ?></strong> 박스 주문목록</h1>

  <form name="frm" method="post">
  <div id="excelfile_upload" class="result_list" style="padding:12px;">

    <div style="clear:both"></div>
    <div class="tbl_head01 tbl_wrap">
      <table>
        <thead style="position:sticky;top:0;">
        <tr>
          <th>주문번호</th>
          <th>SKU</th>
          <th>상품명</th>
          <th>수량</th>
          <th style="width: 170px;">등록일자</th>
          <th style="width: 70px;"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($list as $k => $row) {
          $bg = 'bg' . ($k % 2);
          ?>

          <tr class="<?php echo $bg; ?>">
            <td><?php echo $row['wr_order_num'] ?></td>
            <td><?php echo $row['wr_code'] ?></td>
            <td><?php echo $row['wr_product_nm']; ?></td>
            <td><?php echo number_format($row['wr_ea']) ?></td>
            <td><?= $row['reg_datetime'] ?></td>
            <td>
              <a href="#" onclick="deleteBox('<?= $row['seq'] ?>')" class="btn btn_b02" style="background: #FF3746; color: white;">삭제</a>
            </td>
          </tr>
        <?php } ?>
        </tbody>


      </table>
    </div>

  </div>

  <script>
    function deleteBox(seq) {
      if (!confirm("해당 주문을 삭제하겠습니까?")) {
        return;
      }

      $.post(
        "./ajax.delete_box_order.php",
        { seq: seq },
        (response) => {
          if (response.result) {
            location.reload();
          }
        }, 'json'
      );

    }
  </script>
<?php
include_once(G5_PATH . '/tail.sub.php');
