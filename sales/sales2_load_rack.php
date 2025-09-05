<?
include_once('./_common.php');

# 파라메터 받기
$seq = $_REQUEST['seq'];
$wr_warehouse = $_REQUEST['wr_warehouse'];

# 입고 가능 랙 불러오기
$sql = "SELECT GR.* FROM g5_rack GR \n";
$sql .= "WHERE GR.gc_warehouse = '".$wr_warehouse."' AND GR.gc_use = 1 AND GR.gc_name != '임시창고' ORDER BY gc_name ASC;";
$rs = sql_query($sql);

# 랙이 선택 되었을 경우를 위해 입고 정보에서 랙정보 불러오기
$sql = "SELECT wr_rack FROM g5_sales2_list WHERE seq='".$sql."'";
$data = sql_fetch($sql);
?>
<option value="">==랙==</option>
<?while($row = sql_fetch_array($rs)){?>
	<option value="<?=$row['seq']?>" <?=get_selected($row['seq'],$data['wr_rack'])?>><?=$row['gc_name']?></option>
<?}?>