<?
include_once("./_common.php");

$row = sql_fetch("SELECT * FROM g5_sales0_list WHERE seq = '{$_POST['seq']}' ");
$delivery_sql = "SELECT * FROM g5_delivery_company WHERE wr_use = 1";
$delivery_rst = sql_query($delivery_sql);

while($delivery_com=sql_fetch_array($delivery_rst)) {
    $delivery[$delivery_com['wr_code']] = $delivery_com['wr_name'];
}

$country_dcode = sql_fetch(" SELECT wr_code as code FROM g5_country WHERE code_2 = '{$_POST['wr_country']}' "); 
$country = $country_dcode['code']; 

$sql = "SELECT {$country} as price, cust_code, weight_code, B.code_percent FROM g5_shipping_price A
		LEFT OUTER JOIN g5_code_list B ON B.code_type='3' AND B.code_value=A.cust_code 
        WHERE weight_code >= {$_POST['max_weight']} AND {$country} != 0  GROUP BY cust_code ORDER BY price ASC";
$rst = sql_query($sql);
$wr_delivery_fee = (float)$row['wr_delivery_fee'];
?>

<option value="" data="0" oil_percent="0" currency="KRW">==배송사 선택==</option>
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
	<option value="<?=$delivery2['cust_code']?>" data="<?=$delivery2['price']?>" oil_percent="<?=$delivery2['code_percent']?>" currency="<?=($delivery2['cust_code'] == "1021")?"JPY":"KRW"?>" <?=$selected?>><?=$delivery[$delivery2['cust_code']]?></option>
<?
}
?>