<?php 
include_once('./_common.php');

			
if($date1 && $date2)
	$sql_search .= " and a.wr_date BETWEEN '{$date1}' AND '{$date2}'";


if(!$sql_search) {
	$sql_search .= "  ";
}

if($mb_id)
	$sql_search .= " and a.mb_id = '$mb_id' ";

if($wr_18)
	$sql_search .= " and a.wr_domain = '{$wr_18}' ";

if($stx)
	$sql_search .= " and a.wr_order_num LIKE '%$stx%' ";

if(!$sst && !$sod) {
	$sst = "a.seq";
	$sod = "desc";
}
$sql_order = "order by $sst $sod";

$sql = "select a.*, b.wr_subject, b.wr_2 from g5_sales0_list a 
LEFT JOIN g5_write_product b ON(a.wr_code = b.wr_1 OR a.wr_code = b.wr_27 OR a.wr_code = b.wr_28 OR a.wr_code = b.wr_29 OR a.wr_code = b.wr_30 OR a.wr_code = b.wr_31)

where (1) {$sql_search} {$sql_order} limit {$next_num}, 50";

$rst = sql_query($sql);
for ($i=0; $row=sql_fetch_array($rst); $i++) {

	//$item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");
	
	$set = $set_class = "";
	if($row['wr_set_sku']) {
		$set_class = "set";
		$set = '<br><span style="color:blue">('.$row['wr_set_sku'].')</span>';
	}
?>
<li class="modify <?php echo $set_class?>" data="<?php echo $row['seq']?>">
	
  
	<div class="num cnt_left" style="width:50px"><input type="checkbox" name="seq[]" value="<?php echo $row['seq']?>"></div>
	<div class="num cnt_left" style="width:70px"><?php echo ($i+1) ?></div>
	<div class="cnt_left" style="width:100px"><?php echo $row['wr_domain'] ?></div>
	<div class="cnt_left" style="width:200px;"><?php echo $row['wr_order_num'] ?></div>
	<div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_date'] ?></div>
	<div class="cnt_left" style="width:150px;text-align:center"><?php echo $row['wr_code'] ?><?php echo $set?></div>
	<div class="cnt_left" style="width:150px;"><?php echo $row['wr_2'] ?></div>
	<div class="cnt_left" style="width:250px;"><?php echo $row['wr_subject'] ?></div>
	<div class="cnt_left" style="width:200px;"><?php echo $row['wr_code'] ?></div>
	<div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_ea'] ?></div>
	<div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_box'] ?></div>
	<div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_danga'] ?></div>
	<div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_singo'] ?></div>
	<div class="cnt_left" style="width:70px;text-align:center"><?php echo $row['wr_currency'] ?></div>
	<div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_weight1'] ?></div>
	<div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_weight2'] ?></div>
	<div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_delivery'] ?></div>
	<div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_delivery_fee'] ?></div>
	<div class="cnt_left" style="width:100px;"><?php echo $row['wr_mb_id'] ?></div>
	<div class="cnt_left" style="width:100px;"><?php echo $row['wr_mb_name'] ?></div>
  
   

	<!-- 
	// 추천, 비추천 
	<?php if ($is_good) { ?><span class="sound_only">추천</span><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> <?php echo $list[$i]['wr_good'] ?><?php } ?>
	<?php if ($is_nogood) { ?><span class="sound_only">비추천</span><i class="fa fa-thumbs-o-down" aria-hidden="true"></i> <?php echo $list[$i]['wr_nogood'] ?><?php } ?>
	-->
	
</li>
<?php } ?>