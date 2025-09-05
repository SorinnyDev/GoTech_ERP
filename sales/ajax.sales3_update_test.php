<?
include_once("./_common.php");

$_POST['seq'] = '171897';
$_POST['wr_country'] = 'US';
$_POST['max_weight'] = 10;

$row = sql_fetch("SELECT * FROM g5_sales3_list WHERE seq = '{$_POST['seq']}' ");
$delivery_sql = "SELECT * FROM g5_delivery_company WHERE wr_use = 1";
$delivery_rst = sql_query($delivery_sql);

while($delivery_com=sql_fetch_array($delivery_rst)) {
    $delivery[$delivery_com['wr_code']] = $delivery_com['wr_name'];
}

$country_dcode = sql_fetch(" SELECT wr_code as code FROM g5_country WHERE code_2 = '{$_POST['wr_country']}' "); 
$country = $country_dcode['code']; 

$sql = "SELECT {$country} as price, cust_code, weight_code, B.code_percent FROM g5_shipping_price A
		LEFT OUTER JOIN g5_code_list B ON B.code_type='3' AND B.code_value=A.cust_code 
		LEFT OUTER JOIN g5_delivery_company C ON C.wr_code=A.cust_code
        WHERE weight_code >= {$_POST['max_weight']} AND {$country} != 0 and C.wr_use='1' and B.code_use='Y' GROUP BY cust_code ORDER BY price ASC";
$rst = sql_query($sql);

$wr_delivery_fee = (float)$row['wr_delivery_fee'];
$wr_delivery_oil = (float)$row['wr_delivery_oil'];
$wr_delivery_oil_percent = 1;

?>
<?if(!$is_overwrite){?>
	<th>배송사</th>
	<td>
		<?if(sql_num_rows($rst) > 0){?>
			<select name="wr_delivery" id="wr_delivery">
				<option value="" data="0" currency="KRW">==배송사 선택==</option>
				<?
				for ($i=0; $delivery2=sql_fetch_array($rst); $i++){
					if($delivery2['cust_code'] == $row['wr_delivery']){
						$wr_delivery_oil_percent = $delivery2['code_percent'];
						if(!$wr_delivery_fee){
							$wr_delivery_fee = $delivery2['price'];
						}
						if(!$wr_delivery_oil){
							$wr_delivery_oil = $wr_delivery_fee*($wr_delivery_oil_percent-1);
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
	</td>
	<th>배송요금</th>
	<td><input type="text" name="wr_delivery_fee" id="wr_delivery_fee" value="<?=$wr_delivery_fee?>"></td>
	<th>추가배송비</th>
	<td><input type="text" name="wr_delivery_fee2" id="wr_delivery_fee2" value="<?=$row['wr_delivery_fee2']?>"></td>
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
			$selected = "SELECTED";
			if(!$wr_delivery_fee){
				$wr_delivery_fee = $delivery2['price'];
			}
		}else{
			if($i == 0){
				$selected = "SELECTED";
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