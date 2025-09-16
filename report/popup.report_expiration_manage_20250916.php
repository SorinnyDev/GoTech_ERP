<?php
include_once('../common.php');
include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

if (!isset($seqs) || isEmpty($seqs)) {
  alert_close('잘못된 접근입니다.');
}

$seq_list = explode(',', $seqs);

$product_rack_list = [];
foreach ($seq_list as $seq) {
  /*$sql = "
    select rs.wr_rack, rs.wr_warehouse, wp.wr_1, wp.wr_subject, r.gc_name, rep.id as 'expired_id', wp.wr_id, rep.expired_date from g5_rack_stock as rs
    left join g5_write_product as wp on rs.wr_product_id = wp.wr_id
    left join g5_rack as r on r.seq = rs.wr_rack
    left join g5_rack_expired as rep on rep.rack_id = r.seq and rep.product_id = rs.wr_product_id
    where rs.wr_product_id = '{$seq}'";*/

$sql = "
    SELECT 
        s2.wr_rack, 
        s2.wr_warehouse, 
        wp.wr_1, 
        wp.wr_subject, 
        r.gc_name, 
        rep.id as 'expired_id', 
        wp.wr_id, 
        rep.expired_date,
        s2.seq as sales2_seq
    FROM g5_sales2_list as s2
    LEFT JOIN g5_write_product as wp ON s2.wr_product_id = wp.wr_id
    LEFT JOIN g5_rack as r ON r.seq = s2.wr_rack
    LEFT JOIN g5_rack_expired as rep ON rep.sales2_seq = s2.seq
    WHERE s2.wr_product_id = '{$seq}'
    AND s2.wr_chul_ea > 0";


  if($search_expired == 'Y' && $expired_st_date && $expired_ed_date)
      $sql .= " AND rep.expired_date BETWEEN '{$expired_st_date}' AND '{$expired_ed_date}' ";

    $sql .= " GROUP BY s2.wr_rack, s2.wr_warehouse, wp.wr_1, wp.wr_subject, r.gc_name, rep.id, wp.wr_id, rep.expired_date, s2.seq";

  $rack_list = sql_fetch_all($sql);


  $expired_array = [];
  foreach ($rack_list as $rack) {
    $wr_rack = $rack['wr_rack'];
    $sku = $rack['wr_1'];
    $subject = $rack['wr_subject'];
    $product_id = $rack['wr_id'];
    $warehouse = $rack['wr_warehouse'];
    $gc_name = $rack['gc_name'];
    $expired_date = $rack['expired_date'];
    $gc_is_expired = !!$rack['expired_id'];
      $sales2_seq = $rack['sales2_seq'];

    //$sql = "SELECT SUM(wr_stock) AS total FROM g5_rack_stock WHERE wr_product_id = '{$seq}' AND wr_rack = '{$wr_rack}' ORDER BY seq ASC, rep.expired_date desc";
      $sql = "SELECT SUM(wr_chul_ea) AS total FROM g5_sales2_list WHERE wr_product_id = '{$seq}' AND wr_rack = '{$wr_rack}' AND wr_chul_ea > 0";
    $total = sql_fetch($sql)['total'];

      if($total >= 0 ) {
          // $temp_data 대신, $product_rack_list[$wr_rack]에 직접 값을 할당합니다.
          $product_rack_list[$wr_rack]['warehouse'] = PLATFORM_TYPE[$warehouse];
          $product_rack_list[$wr_rack]['rack_name'] = $gc_name;
          $product_rack_list[$wr_rack]['seq'] = $wr_rack;
          $product_rack_list[$wr_rack]['total'] = number_format($total);
          $product_rack_list[$wr_rack]['product_nm'] = $subject;
          $product_rack_list[$wr_rack]['product_id'] = $product_id;
          $product_rack_list[$wr_rack]['sku'] = $sku;
          $product_rack_list[$wr_rack]['is_expired'] = $gc_is_expired;
          $product_rack_list[$wr_rack]['expired_date'] = $expired_date;
          $product_rack_list[$wr_rack]['sales2_seq'] = $sales2_seq;

          // 만료일 정보가 있는 경우에만 expired_array에 해당 데이터를 추가합니다.
          if ($gc_is_expired) {
              $expired_array[] = $product_rack_list[$wr_rack];
          }
      }
  }
}


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
<div id="popup_container" class="new_win">
  <h1>유통기한 임박 재고 관리</h1>
  <form name="frm" method="post">
    <div id="excelfile_upload" class="result_list" style="padding:12px;">
      <div class="tbl_head01 tbl_wrap">
        <table>
          <thead style="position:sticky;top:0;">
          <tr>
            <th>SKU</th>
            <th>상품명</th>
            <th style="width: 100px;">랙</th>
            <th>재고</th>
            <th>유통기한</th>
            <th style="width: 70px;"></th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($product_rack_list as $k => $row) {
            $bg = 'bg' . ($row['is_expired'] ? '1' : '0');
            ?>

            <tr class="<?php echo $bg; ?>">
              <td><?php echo $row['sku'] ?></td>
              <td><?php echo $row['product_nm'] ?></td>
              <td>[<?= $row['warehouse']; ?>] <?= $row['rack_name']; ?></td>
              <td><?php echo number_format($row['total']) ?></td>
              <td>
                <input type="date" name="expired_date" value="<?= $row['expired_date'] ?>" class="sch_input" size="25"
                       maxlength="20" placeholder="" style="text-align:center">
              </td>
              <td>
  <?php if ($row['is_expired']) { ?>
    <a href="#" onclick="update_rack_expired_status(this, '<?= $row['rack_name']; ?>', '<?= $row['seq']; ?>', '<?= $row['product_id']; ?>', '<?= $row['sales2_seq']; ?>', true)" class="btn btn_b02" style="background: #FF3746; color: white;">해제</a>
  <?php } else { ?>
    <a href="#" onclick="update_rack_expired_status(this, '<?= $row['rack_name']; ?>', '<?= $row['seq']; ?>', '<?= $row['product_id']; ?>', '<?= $row['sales2_seq']; ?>')" class="btn btn_b01" style="color: white;">등록</a>
  <?php } ?>
</td>
            </tr>
          <?php } ?>
          </tbody>

        </table>
      </div>
    </div>

</div>
<script>
  function update_rack_expired_status($this, rack_name, seq, product_id, sales2_seq, is_delete = false) {
    if (is_delete) {
      if (!confirm('[' + rack_name + ']을 유통기한 임박 랙에서 해제하시겠습니까?')) {
        return;
      }
    } else {
      if (!confirm('[' + rack_name + ']을 유통기한 임박 랙으로 등록하시겠습니까?')) {
        return;
      }
    }

    const expired_date = $this.closest('tr').querySelector('[name=expired_date]').value;

    if (!expired_date) {
      alert('유통기한 날짜를 입력해주세요.');
      return;
    }

    $.post('./ajax.rack_update_expired_status.php', {seq, product_id, sales2_seq, expired_date}, function (result) {
      if (result === 'Y') {
        alert('저장되었습니다. 재고보고서에서 새로고침해주세요.');
        location.reload();
      } else {
        alert(result ?? '저장에 실패했습니다.');
      }
    });

  }
</script>
<?php
include_once(G5_PATH . '/tail.sub.php');
