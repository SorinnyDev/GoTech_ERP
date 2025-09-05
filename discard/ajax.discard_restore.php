<?php
include_once './_common.php';

if (!$id) {
  alert('잘못된 접근입니다.');
}

$sql = "
select *, dl.id as discard_id from g5_discard_list as dl
left join g5_discard_stock as ds on ds.discard_id = dl.id
where dl.id = '$id';
";

$discard = sql_fetch($sql);

if (!$discard) {
  alert('존재하지 않는 폐기재고 입니다.');
}

if ($discard['wr_warehouse'] === '한국 폐기창고') {
  $sql = "select * from g5_rack where gc_name = '{$target}' and gc_warehouse = '1000'";
  $target_rack = sql_fetch($sql);

  if (!$target_rack) {
    alert('존재하지 않는 렉입니다.');
  }

  $sql = "select * from g5_rack where gc_name = '{$discard['wr_rack']}' and gc_warehouse = '11000'";
  $rack = sql_fetch($sql);

  if (!$rack) {
    alert('잘못된 접근입니다.');
  }

  # 복원 - 본래 창고 증가
  $sql = "insert into g5_rack_stock set wr_warehouse = '1000', wr_rack = '{$target_rack['seq']}', wr_stock = '{$discard['wr_stock']}', wr_product_id = '{$discard['product_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '한국폐기창고 복원 ID:{$discard['discard_id']}' ";
  sql_query($sql);

  # 복원 - 폐기 창고 감소
  $sql = "insert into g5_rack_stock set wr_warehouse = '11000', wr_rack = '{$rack['seq']}', wr_stock = '-{$discard['wr_stock']}', wr_product_id = '{$discard['product_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '한국폐기창고 복원 ID:{$discard['discard_id']}' ";
  sql_query($sql);

  # 전체 재고 증가
  $sql = "update g5_write_product set wr_32 = wr_32 + {$discard['wr_stock']}, wr_32_real = wr_32_real + {$discard['wr_stock']} where wr_id = '{$discard['product_id']}'";
  sql_query($sql);

  #전체 재고 감소
  $sql = "update g5_write_product set wr_45 = wr_45 - {$discard['wr_stock']}, wr_45_real = wr_45_real - {$discard['wr_stock']} where wr_id = '{$discard['product_id']}'";
  sql_query($sql);


} else if ($discard['wr_warehouse'] === '미국 폐기창고') {
  $sql = "select * from g5_rack where gc_name = '{$target}' and gc_warehouse = '3000'";
  $target_rack = sql_fetch($sql);

  if (!$target_rack) {
    alert('존재하지 않는 렉입니다.');
  }

  $sql = "select * from g5_rack where gc_name = '{$discard['wr_rack']}' and gc_warehouse = '12000'";
  $rack = sql_fetch($sql);

  if (!$rack) {
    alert('잘못된 접근입니다.');
  }

  # 복원 - 본래 창고 증가
  $sql = "insert into g5_rack_stock set wr_warehouse = '3000', wr_rack = '{$target_rack['seq']}', wr_stock = '{$discard['wr_stock']}', wr_product_id = '{$discard['product_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '미국폐기창고 복원 ID:{$discard['discard_id']}' ";
  sql_query($sql);

  # 복원 - 폐기 창고 감소
  $sql = "insert into g5_rack_stock set wr_warehouse = '12000', wr_rack = '{$rack['seq']}', wr_stock = '-{$discard['wr_stock']}', wr_product_id = '{$discard['product_id']}', wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '미국폐기창고 복원 ID:{$discard['discard_id']}' ";
  sql_query($sql);

  # 전체 재고 증가
  $sql = "update g5_write_product set wr_36 = wr_36 + {$discard['wr_stock']}, wr_36_real = wr_36_real + {$discard['wr_stock']} where wr_id = '{$discard['product_id']}'";
  sql_query($sql);

  #전체 재고 감소
  $sql = "update g5_write_product set wr_46 = wr_46 - {$discard['wr_stock']}, wr_46_real = wr_46_real - {$discard['wr_stock']} where wr_id = '{$discard['product_id']}'";
  sql_query($sql);


}

$sql = "delete from g5_discard_list where id = '{$discard['discard_id']}'";
sql_query($sql);

$sql = "delete from g5_discard_stock where discard_id = '{$discard['discard_id']}'";
sql_query($sql);

alert('복원되었습니다.');