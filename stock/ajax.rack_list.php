<?php
include_once('./_common.php');

if ($is_guest) {
  die('로그인 후 이용하세요.');
}

$warehouse = isset($_GET['warehouse']) ? $_GET['warehouse'] : '';

if (empty($warehouse)) {
  die('창고 정보가 없습니다.');
}

// 창고 이름 가져오기
$warehouseName = PLATFORM_TYPE[$warehouse] ?? '알 수 없음';
?>
<table>
  <thead style="position:sticky;top:0;">
    <tr>
      <th style="width:100%" colspan="3"><strong><?= $warehouseName ?></strong></th>
    </tr>
    <!--  <tr>
    <th style="width:100%" colspan="3">
      <input type="text" id="warehouse_<?php /*=$warehouse*/ ?>_ser" data-warehouse="<?php /*=$warehouse*/ ?>" class="frm_input rack_search" placeholder="랙 이름 검색" style="width:100%">
    </th>
  </tr>
-->
    <tr>
      <th style="width:100px;">랙 이름</th>
      <th>총 재고</th>
      <th>관리</th>
    </tr>
  </thead>
  <tbody id="warehouse_<?= $warehouse ?>_list">
    <?php
    // 1. 랙 목록 가져오기
    $sql = "SELECT * FROM g5_rack 
            WHERE gc_warehouse = '{$warehouse}' 
            AND gc_use = 1 
            ORDER BY SUBSTRING(gc_name, 1, 1) ASC, gc_name ASC";
    $rack_result = sql_query($sql);
    $rack_list = array();
    $rack_seq_list = array();

    for ($i = 0; $row = sql_fetch_array($rack_result); $i++) {
      $rack_list[$row['seq']] = $row;
      $rack_seq_list[] = $row['seq'];
    }

    if (empty($rack_seq_list)) {
      echo '<tr><td colspan="3" class="empty_table">등록된 랙이 없습니다.</td></tr>';
    } else {
      // 2. 랙별 재고 총합을 한 번에 조회
      $rack_seq_str = implode(',', $rack_seq_list);
      $sql = "SELECT wr_rack, SUM(wr_stock) as total 
                FROM g5_rack_stock 
                WHERE wr_rack IN ({$rack_seq_str}) 
                AND wr_warehouse = '{$warehouse}' 
                GROUP BY wr_rack";
      $stock_result = sql_query($sql);
      $stock_map = array();

      while ($stock_row = sql_fetch_array($stock_result)) {
        $stock_map[$stock_row['wr_rack']] = $stock_row['total'];
      }

      // 3. 화면에 출력
      $total_stock = 0;
      $i = 0;
      foreach ($rack_list as $rack_seq => $rack) {
        $stock_amount = isset($stock_map[$rack_seq]) ? $stock_map[$rack_seq] : 0;
        $bg = 'bg' . ($i % 2);
        $i++;
        $total_stock += $stock_amount;
    ?>
        <tr class="<?php echo $bg; ?>" data-rack-name="<?php echo strtolower($rack['gc_name']); ?>">
          <td><input type="text" class="rack_name frm_input" value="<?php echo $rack['gc_name'] ?>"></td>
          <td><?php echo number_format($stock_amount) ?></td>
          <td>
            <?php if ($rack['gc_name'] != "임시창고") { ?>
              <button type="button" class="btn02 modify" data="<?php echo $rack_seq ?>" data-warehouse="<?php echo $warehouse ?>">수정</button>

              <?php if ($stock_amount > 0) { ?>
                <button type="button" class="diabled_btn btn01" disabled title="재고가 있어 삭제가 불가능합니다." onclick="error_msg()">삭제</button>
              <?php } else { ?>
                <button type="button" class="btn01 delete" data="<?php echo $rack_seq ?>" data-warehouse="<?php echo $warehouse ?>">삭제</button>
              <?php } ?>
            <?php } ?>
          </td>
        </tr>
    <?php
      }

      // 창고 총 재고 정보를 위한 히든 필드 추가
      echo '<input type="hidden" id="total_stock_' . $warehouse . '" value="' . $total_stock . '">';
    }
    ?>
  </tbody>
</table>