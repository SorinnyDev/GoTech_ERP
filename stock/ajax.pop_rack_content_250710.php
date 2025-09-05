<?php
include_once './_common.php';

if (!isset($wr_id) || !isset($warehouse_key)) alert('잘못된 접근입니다.');

$it = sql_fetch("select * from g5_write_product where wr_id = '{$wr_id}'");

$warehouse_fields = [];

foreach ($warehouseConfig as $warehousecode => $warehouseinfo) {
  $warehouse_fields[$warehousecode] = [$warehouseinfo['filed'], $warehouseinfo['filed_real']];
}

foreach (PLATFORM_TYPE as $key => $value) {
  if ($key != $warehouse_key) continue;
  if (!isset($warehouse_fields[$key])) continue;

  list($stock_field, $real_field) = $warehouse_fields[$key];
  $store_stock = $it[$stock_field];
  $store_stock_real = $it[$real_field];
?>
  <div class="tbl_head01 tbl_wrap" style="width:20%; float:left;height:550px;overflow-y:scroll;">
    <table>
      <thead style="position:sticky;top:0;">
        <tr>
          <th style="width:100%" colspan="2">
            <strong style="display:block;padding-bottom:10px"><?= $value ?></strong>
            <?= $store_stock ?> /
            <input type="text" name="" value="<?= number_format($store_stock_real) ?>" class="total_qty_input">개
            <button type="button" class="total_qty_btn" onclick="open_log('<?= $key ?>', '<?= $wr_id ?>')">이동로그</button>
          </th>
        </tr>
        <tr>
          <th style="width:100%" colspan="2">
            <input type="text" id="<?= $key ?>_ser" class="frm_input rack_search" placeholder="랙 이름 검색" style="width:100%">
          </th>
        </tr>
        <tr>
          <th style="width:30%">랙 번호</th>
          <th>재고수량</th>
        </tr>
      </thead>
      <tbody id="<?= $key ?>_list">
        <?php
        $sql = "SELECT
                  gr.seq,            -- g5_rack의 고유 식별자
                  gr.gc_name,        -- 랙 이름
                  gr.gc_warehouse,   -- 랙이 속한 창고
                  gr.gc_use,         -- 랙 사용 여부 (1이면 사용 중)
                  COALESCE(rs.total_stock, 0) AS total_stock, -- 해당 랙의 총 재고 수량 (없으면 0)
                  COALESCE(ss.sales_stock, 0) AS sales_stock  -- 해당 랙의 판매 예정 재고 수량 (없으면 0)
                FROM
                    g5_rack gr
                LEFT JOIN (
                    -- 랙별 총 재고 수량을 계산하는 서브쿼리
                    SELECT
                        wr_rack,
                        SUM(wr_stock) AS total_stock
                    FROM
                        g5_rack_stock
                    WHERE
                        wr_warehouse = '{$key}' AND wr_product_id = '{$wr_id}'
                    GROUP BY
                        wr_rack
                ) AS rs ON gr.seq = rs.wr_rack
                LEFT JOIN (
                    -- 랙별 판매 예정 재고 수량을 계산하는 서브쿼리
                    SELECT
                        A.wr_rack,
                        SUM(A.wr_ea) AS sales_stock
                    FROM
                        g5_sales2_list A
                    LEFT JOIN
                        g5_sales3_list B ON B.wr_order_num = A.wr_order_num
                    WHERE
                        A.wr_direct_use = '1'
                        AND A.wr_etc_use = '0'
                        AND IFNULL(B.wr_release_use, 0) = '0'
                        AND A.wr_warehouse = '{$key}'
                        AND A.wr_product_id = '{$wr_id}'
                    GROUP BY
                        A.wr_rack
                ) AS ss ON gr.seq = ss.wr_rack
                WHERE
                    gr.gc_warehouse = '{$key}'
                    AND gr.gc_use = 1
                ORDER BY
                    total_stock DESC,                -- total_stock을 기준으로 내림차순 정렬
                    sales_stock DESC,                -- total_stock이 같으면 sales_stock을 기준으로 내림차순 정렬
                    SUBSTRING(gr.gc_name, 1, 1) ASC; -- sales_stock도 같으면 gc_name의 첫 글자를 기준으로 오름차순 정렬";

        $result = sql_query($sql);
        for ($i = 0; $row = sql_fetch_array($result); $i++) {
          // $rack_seq = $row['seq'];
          // $stock_total = $stock_map[$rack_seq] ?? 0;
          // $sales_stock = $sales_map[$rack_seq] ?? 0;
          $bg = 'bg' . ($i % 2);
        ?>
          <tr class="<?= $bg ?>">
            <td><?= $row['gc_name'] ?></td>
            <td>
              <form name="frmIndi<?= $rack_seq ?>" action="./rack_move_update.php" method="post">
                <input type="hidden" name="mode" value="indi">
                <input type="hidden" name="wr_id" value="<?= $wr_id ?>">
                <input type="hidden" name="warehouse" value="<?= $key ?>">
                <input type="hidden" name="seq" value="<?= $row['seq'] ?>">
                <input type="hidden" name="ori_qty" value="<?= $row['total_stock'] ?>">
                <input type="hidden" name="sales_stock" value="<?= $row['sales_stock'] ?>">
                <?= $row['total_stock'] ?> /
                <input type="number" name="qty" class="frm_input qty" value="<?= $row['total_stock'] + $row['sales_stock'] ?>" style="text-align:right;width:40%"
                  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                <button class="btn01">수정</button>
              </form>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
<?php } ?>