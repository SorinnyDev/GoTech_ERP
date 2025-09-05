<?php
include_once('./_common.php');

if ($is_guest) {
    die('n');
}

$chk = 0;

$sql_common = " from g5_rack ";
$sql_search = " where gc_warehouse = '{$warehouse}' and gc_use = 1 order by gc_name asc";
$sql = " select * {$sql_common} {$sql_search}  ";

$result = sql_query($sql);

for ($a = 0; $rack = sql_fetch_array($result); $a++) {
    ?>
  <option value="<?= $rack['seq'] ?>"><?= $rack['gc_name'] ?></option>
<?php
    $chk++;
}

if ($chk == 0) {
    echo '<option value="">재고없음</option>';
}
