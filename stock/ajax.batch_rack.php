<?
include_once('./_common.php');

# 창고별 랙 불러오기
$sql = "SELECT * FROM g5_rack WHERE gc_warehouse = '".$wr_warehouse."' AND gc_use = 1 ORDER BY gc_nm1 ASC,gc_nm2 ASC, gc_nm3 ASC";
$rs = sql_query($sql);
?>
<option value="">==지정랙 선택==</option>
<?while($row = @sql_fetch_array($rs)){?>
	<option value="<?=$row['seq']?>"><?=$row['gc_name']?></option>
<?}?>