<?
include_once('../common.php');

$sql = "SELECT * FROM g5_rack WHERE gc_warehouse='".$wr_warehouse."' AND gc_use='1' AND seq NOT IN(1)";
$rackRs = sql_query($sql);
?>
<select name="wr_rack" id="wr_rack" class="search_sel" onchange="fnLoadList();">
	<option value="">랙 선택</option>
	<?while($rackRow = sql_fetch_array($rackRs)){?>
		<option value="<?=$rackRow['seq']?>"><?=$rackRow['gc_name']?></option>
	<?}?>
</select>