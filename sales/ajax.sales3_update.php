<?
include_once("./_common.php");

$row = sql_fetch("SELECT * FROM g5_sales3_list WHERE seq = '{$_POST['seq']}' ");
$delivery_sql = "SELECT * FROM g5_delivery_company WHERE wr_use = 1";
$delivery_rst = sql_query($delivery_sql);

while($delivery_com=sql_fetch_array($delivery_rst)) {
    $delivery[$delivery_com['wr_code']] = $delivery_com['wr_name'];
}

$country_dcode = sql_fetch(" SELECT wr_code as code FROM g5_country WHERE code_2 = '{$_POST['wr_country']}' "); 
$country = $country_dcode['code']; 

$sql = "SELECT {$country} as price, cust_code, weight_code, C.wr_percent as 'code_percent' FROM g5_shipping_price A
		LEFT OUTER JOIN g5_delivery_company C ON C.wr_code=A.cust_code
        WHERE weight_code >= {$_POST['max_weight']} AND {$country} != 0 and C.wr_use='1' GROUP BY cust_code ORDER BY price ASC";
$result1 = sql_fetch_all($sql);

$result1_copy = $result1;
foreach ($result1 as $item) {
  $result1_copy[$item['cust_code']] = $item;
}

$sql = "SELECT {$country} as price, cust_code, weight_code, C.wr_percent as 'code_percent' FROM g5_shipping_price A
		LEFT OUTER JOIN g5_delivery_company C ON C.wr_code=A.cust_code
        WHERE 1 AND {$country} != 0 and C.wr_use='1' GROUP BY cust_code ORDER BY price ASC";
$result2 = sql_fetch_all($sql);

$merged = $result1_copy;
foreach ($result2 as $k => $v) {
  if (!isset($result1_copy[$v['cust_code']])) {
    $merged[] = $v;
  }
}

$unique_list = [];
$merged = array_filter($merged, function ($item) use (&$unique_list) {
  if (in_array($item['cust_code'], $unique_list)) {
    return false;
  }
  $unique_list[] = $item['cust_code'];

  return true;
});

$wr_delivery_fee = (float)$row['wr_delivery_fee'];
$wr_delivery_oil = (float)$row['wr_delivery_oil'];
$wr_delivery_oil_percent = 1;
?>
<?if($mode != "order_view"){?>
	<th>배송사</th>
	<td>
		<?if(count($merged) > 0){?>
			<select name="wr_delivery" id="wr_delivery">
				<option value="" data="0" currency="KRW">==배송사 선택==</option>
				<?
				foreach ($merged as $i => $delivery2){
					if($delivery2['cust_code'] == $row['wr_delivery']){
						$wr_delivery_oil_percent = $delivery2['code_percent'];
						if(!$wr_delivery_fee){
							$wr_delivery_fee = $delivery2['price'];
						}

					}
				?>
					<option value="<?=$delivery2['cust_code']?>" data="<?=$delivery2['price']?>" currency="<?=($delivery2['cust_code'] == "1021")?"JPY":"KRW"?>" oil_percent="<?=$delivery2['code_percent']?>" <?=get_selected($delivery2['cust_code'],$row['wr_delivery'])?>><?=$delivery[$delivery2['cust_code']]?></option>
				<?
				}
					//$wr_delivery_fee = $row['wr_servicetype']=="0003" ? "0" :$wr_delivery_fee;
				?>
			</select>
		<?}else{?>
			<div><button type="button"  class="btn_b01" onclick="delivery_action('','')" style="background:#4565E4;" >배송비등록</button></div> 
		<?}?>
    <button type="button" class="btn_calc" id="btn_calc" onclick="open_cal_popup('<?=$row['seq']?>');">배송비 계산</button>
  </td>
	<th>배송요금</th>
	<td>
    <input type="text" name="wr_delivery_fee" id="wr_delivery_fee" value="<?=$wr_delivery_fee?>">
    <input type="hidden" name="wr_delivery_fee_original" id="wr_delivery_fee_original" value="<?=$wr_delivery_fee?>">
  </td>
	<th>추가배송비</th>
	<td>
    <input type="text" name="wr_delivery_fee2" id="wr_delivery_fee2" value="<?=$row['wr_delivery_fee2']?>">
    <input type="hidden" name="wr_delivery_fee2_original" id="wr_delivery_fee2_original" value="<?=$row['wr_delivery_fee2']?>">
  </td>
	<th>유류할증료</th>
	<td>
		<input type="hidden" id="wr_delivery_oil_percent" value="<?=$wr_delivery_oil_percent?>"/>
		<input type="text" name="wr_delivery_oil" id="wr_delivery_oil" value="<?=$wr_delivery_oil?>" class="readonly" readonly/>
	</td>
	<th>배송 총금액</th>
	<td>
		<input type="text" name="wr_delivery_total" id="wr_delivery_total" class="readonly" value="" readonly/>
	</td>
<?}else{?>
	<option value="" data="0" currency="KRW">==배송사 선택==</option>
	<?
	for ($i=0; $delivery2=sql_fetch_array($rst); $i++){
		$selected = "";
		if($delivery2['cust_code'] == $row['wr_delivery']){
			$selected == "SELECTED";
			if(!$wr_delivery_fee){
				$wr_delivery_fee = $delivery2['price'];
			}
		}else{
			if($i == 0){
				$selected == "SELECTED";
				if(!$wr_delivery_fee){
					$wr_delivery_fee = $delivery2['price'];
				}
			}
		}
	?>
		<option value="<?=$delivery2['cust_code']?>" data="<?=$delivery2['price']?>" oil_percent="<?=$delivery['code_percent']?>" currency="<?=($delivery2['cust_code'] == "1021")?"JPY":"KRW"?>" <?=$selected?>><?=$delivery[$delivery2['cust_code']]?></option>
	<?
	}
		//$wr_delivery_fee = $row['wr_servicetype']=="0003" ? "0" :$wr_delivery_fee;
	?>
<?}?>