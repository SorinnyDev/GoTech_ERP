<?
include_once("./_common.php");
?>

<? include $_SERVER[DOCUMENT_ROOT]."/lib/vars.php" ?>
<? include $EV_LIB_PATH."common.php" ?>

<?

if($is_guest)
	alert('로그인 후 이용바랍니다.');

$filename = "report_list_test_".G5_TIME_YMD.".xls";

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Description: PHP4 Generated Data");


// 만든 테이블을 출력해줘야 만들어진 엑셀파일에 데이터가 나타납니다.
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";

# 파라메터 받기
$wr_domain	= $wr_18;
$wmb_id		= $wmb_id;
$st_date	= $st_date;
$ed_date	= $ed_date;

# 검색 기간의 환율 정보 가져오기
$exData = array();
$today = date("Y-m-d");
$date_arr = date_to_date($st_date,$ed_date);
foreach($date_arr as $k => $v){
	$ex_date = $v['date'];
	$rowData = array();
	if($today == $ex_date){
		$sql = "SELECT * FROM g5_excharge";
		$exRs = sql_query($sql);
		while($exRow = sql_fetch_array($exRs)){
			$currency = $exRow['ex_eng'];
			if($currency == "JPY"){
				$exRow['rate'] = str_replace(",","",$exRow['rate']) * 0.01;
			}
			$rowData[$currency] = $exRow;
		}
	}else{
		$month = date("Ym",strtotime($ex_date));
		$table = "g5_excharge_".$month;
		$sql = "SELECT * FROM ".$table." WHERE ex_date='".$ex_date."'";
		$exRs = sql_query($sql);
		$cnt_chk = sql_num_rows($exRs);
		if($cnt_chk > 0){
			while($exRow = sql_fetch_array($exRs)){
				$currency = $exRow['ex_eng'];
				if($currency == "JPY"){
					$exRow['rate'] = str_replace(",","",$exRow['rate']) * 0.01;
				}
				$rowData[$currency] = $exRow;
			}
		}else{
			$after_month = date("Ym",strtotime($ex_date."+1 month"));
			$after_table = "g5_excharge_".$after_month;
			$sql = "SELECT * FROM ".$table." WHERE ex_date='".$ex_date."'";
			$exRs = sql_query($sql);
			$cnt_chk = sql_num_rows($exRs);
			if($cnt_chk > 0){
				while($exRow = sql_fetch_array($exRs)){
					$currency = $exRow['ex_eng'];
					if($currency == "JPY"){
						$exRow['rate'] = str_replace(",","",$exRow['rate']) * 0.01;
					}
					$rowData[$currency] = $exRow;
				}
			}else{
				$sql = "SELECT * FROM g5_excharge";
				$exRs = sql_query($sql);
				while($exRow = sql_fetch_array($exRs)){
					$currency = $exRow['ex_eng'];
					if($currency == "JPY"){
						$exRow['rate'] = str_replace(",","",$exRow['rate']) * 0.01;
					}
					$rowData[$currency] = $exRow;
				}
			}
		}
	}

	$exData[$ex_date] = $rowData;
}

$sql_where = "";
if($wr_domain != ""){
	$sql_where .= " AND A.wr_domain = '".$wr_domain."' ";

	//$sql_where .= " AND (A.wr_ori_order_num = '#36575')  ";

}

# 매출 데이터(출고기준) 조회
$sql = "SELECT  A.wr_date4,A.wr_danga,A.wr_paymethod,A.wr_domain,A.wr_currency,C.wr_1 AS 'sku',C.wr_5 AS 'p_code',IF(A.wr_set_sku != '',A.wr_set_sku,A.wr_order_num) AS 'set'
	,A.wr_ori_order_num,A.wr_singo, REPLACE(IF(A.wr_exchange_rate = '' OR A.wr_exchange_rate = 0,E.rate,A.wr_exchange_rate),',','') AS wr_exchange_rate
	,A.wr_order_num,IFNULL(B.ibgo_danga,C.wr_22) AS danga,A.wr_tax,A.wr_shipping_price,A.wr_currency
	,A.wr_delivery_fee,A.wr_delivery_fee2,A.wr_delivery_oil,C.wr_subject
	,IFNULL(B.chul_ea,A.wr_ea) AS chul_ea 
FROM g5_sales3_list A
LEFT OUTER JOIN g5_sales3_det B ON B.sales3_id=A.seq AND B.del_yn='N'
LEFT OUTER JOIN g5_write_product C ON C.wr_id=A.wr_product_id
LEFT OUTER JOIN g5_write_product_fee D ON D.wr_id=C.wr_id AND D.warehouse=A.wr_warehouse
LEFT OUTER JOIN g5_excharge E ON E.ex_eng = A.wr_currency
WHERE A.wr_release_use='1' ".$sql_where." AND A.wr_date4 BETWEEN '".$st_date."' AND '".$ed_date."' 
ORDER BY A.wr_domain,A.wr_date4 ASC,A.wr_order_num ASC,A.wr_ori_order_num ASC";

$rs = sql_query($sql);


$list = array();




while($row = sql_fetch_array($rs)){
	$wr_ori_order_num = $row['wr_ori_order_num'];
	$wr_order_num = $row['wr_order_num'];
	$set = $row['set'];
	$list[$wr_ori_order_num]['data'][$set]['data'][$wr_order_num]['data'][] = $row;

	$wr_ori_order_num_list  = $wr_ori_order_num_list .",". $wr_ori_order_num;

	$wr_order_num_list  = $wr_order_num_list .",". $wr_order_num;

}

foreach($list as $k => $v){
	foreach($v['data'] as $k2=> $v2){
		foreach($v2['data'] as $k3 => $v3){
			$cnt = count($list[$k]['data'][$k2]['data'][$k3]['data']);
			$list[$k]['data'][$k2]['data'][$k3]['cnt'] = (int)$list[$k]['data'][$k2]['data'][$k3]['cnt'] + $cnt;
			$list[$k]['data'][$k2]['cnt'] = (int)$list[$k]['data'][$k2]['cnt'] + $cnt;
			$list[$k]['cnt'] = (int)$list[$k]['cnt'] + $cnt;
		}
	}
}
?>
<table border="1">
	<thead>
		<tr>
			<th class="sticky-th" style="width:100px;">매출처</th>
			<th class="sticky-th" style="width:80px;">날짜</th>
			<th class="sticky-th" style="width:100px;">주문번호</th>
			<th class="sticky-th" style="width:300px;">상품명</th>
			<th class="sticky-th" style="width:200px;">SKU</th>
			<th class="sticky-th" style="width:200px;">대표코드</th>
			<th class="sticky-th" style="width:80px;">통화</th>
			<th class="sticky-th" style="width:100px;">수수료1</th>
			<th class="sticky-th" style="width:100px;">수수료2</th>
			<th class="sticky-th" style="width:100px;">기본 배송비</th>
			<th class="sticky-th" style="width:100px;">추가 배송비</th>
			<th class="sticky-th" style="width:100px;">매입원가</th>
			<th class="sticky-th" style="width:50px;">수량</th>
			<th class="sticky-th" style="width:100px;">합계</th>
			<th class="sticky-th" style="width:80px;">환율</th>
			<th class="sticky-th" style="width:100px;">매출단가</th>
			<th class="sticky-th" style="width:100px;">신고가격</th>
			<th class="sticky-th" style="width:100px;">매출 * 환율(원)</th>
			<th class="sticky-th" style="width:100px;">TAX</th>
			<th class="sticky-th" style="width:100px;">손익(원)</th>
			<th class="sticky-th" style="width:100px;">이익률(%)</th>

			<th class="sticky-th" style="width:100px;">부가세환급(원)</th>

		</tr>
	</thead>
	<tbody>
		<?if(count($list) > 0){?>
			<?
				$total_basic_fee = 0;
	$total_sales_fee = 0;
	$total_delivery_fee = 0;
	$total_delivery_fee2 = 0;
	$total_sales_price = 0;
	$total_tax = 0;
	$total_pm_price = 0;

	$total_rate = 0;

	$total_ea = 0;
	$total_ibgo_price = 0;

	//부가세환급
	$total_buga_price = 0;


	$set_sales_price = 0;

	$sales3_det_num_count = 1;


	$result = get_rows($sql);


	$chknum = 1;

	for($i=0;$i<count($result);$i++){

		$row = $result[$i];


		$ibgo_price = 0;


		# 기본수수료 계산
		if($row['wr_paymethod'] == "Shopify Payments"){
			$basic_fee = $row['wr_singo']*2.4/100 + 0.3;

		}else if($row['wr_paymethod'] == "PayPal Express Checkout"){
			$basic_fee = $row['wr_singo'] * 3.49/100 + 0.49;

		}else if($row['wr_paymethod'] == "Shop Cash"){
			$basic_fee = $row['wr_singo'] * 2.4/100 + 0.3;

		}else if($row['wr_paymethod'] == "Shopify Payments + PayPal Express Checkout"){
			$basic_fee = $row['wr_singo'] * 3.49/100 + 0.49;

		}else if($row['wr_paymethod'] == "Shop Cash + Shopify Payments"){
			$basic_fee = $row['wr_singo']*2.4/100 + 0.3;

		}else{
			$basic_fee = 0;
		}


		//환율
		$exchange_rate = str_replace(",","",$exData[$row['wr_date4']][$row['wr_currency']]['rate']);


		//$wr_ori_order_num_val = preg_replace("/[^0-9]*/s", "", $row['wr_ori_order_num']);
		$wr_ori_order_num_val = $row['wr_ori_order_num'];



		//합배송 or 셋트 갯수체크(1개 이상이면 합배송 및 셋트) //세트구분은 wr_set_sku 값에 값이 있으면 세트임 
		//$acc5_set_count = get_acc5_set_count ($wr_ori_order_num_val);
		$acc5_set_count = substr_count($wr_ori_order_num_list, $wr_ori_order_num_val);

		//wr_order_num 중복값 체크 (g5_sales3_det 테이블에 같은 주문번호 wr_order_num가 있는지 숫자체크 중복이면 2이상)
		$acc5_wr_order_num_count = substr_count($wr_order_num_list, $row['wr_order_num']);


		//echo $acc5_set_count;
		//exit;






		
		//if 문 >>>> g5_sales3_det 테이블에 중복 데이터가 있을경우 계산 0
		
		if($sales3_det_num_count < $acc5_wr_order_num_count){ //매출(신고가격) * 환율
			$wr_singo_rate = 0;
		}else{
			$wr_singo_rate= floor(((float)$row['wr_singo']) * $exchange_rate);
		}


		if($sales3_det_num_count < $acc5_wr_order_num_count){ //수수료1
			$basic_fee_rate = 0;
		}else{
			$basic_fee_rate= $basic_fee * $exchange_rate;
		}

		# 수수료2의 경우 도도스킨(자사몰)의 경우 수수료2가 0원 아닐 경우 업체 계산식에 맞게 수정(업체 계산식을 늦게 받아서 작업진행이 안되었슴)
		if($sales3_det_num_count < $acc5_wr_order_num_count){ //수수료1

			if($row['wr_domain'] == "dodoskin"){
				$sales_fee_rate = 0;
			}else{
				$sales_fee_rate = 0;
			}

		}else{
			$sales_fee_rate= 0;
		}
			

		//기본배송비 / 추가배송비
		if($sales3_det_num_count < $acc5_wr_order_num_count){ //수수료1
			$wr_delivery_fee = 0;
			$wr_delivery_fee2 = 0;
		}else{
			$wr_delivery_fee= $row['wr_delivery_fee'];
			$wr_delivery_fee2= $row['wr_delivery_fee2'];
		}





		//if 문 >>>> g5_sales3_det 테이블에 중복 데이터가 있을경우 계산 0 끝

		


		//합계
		$total_sales_price = $total_sales_price + ($wr_singo_rate); //매출* 환율 

		$total_basic_fee = $total_basic_fee + ($basic_fee_rate); //수수료1
		$total_sales_fee = $total_sales_fee + ($sales_fee_rate * $exchange_rate); //수수료2

		$total_delivery_fee = $total_delivery_fee + $wr_delivery_fee; //기본배송비	
		$total_delivery_fee2 = $total_delivery_fee2 + $wr_delivery_fee2; //추가배송비
	



		if($acc5_set_count == $chknum){
			$total_tax = $total_tax + floor(((float)$row['wr_tax']) * $exchange_rate);
		}

		$ibgo_price = $ibgo_price + ($row['danga']*$row['chul_ea']);

		$total_ibgo_price = $total_ibgo_price + ($row['danga']*$row['chul_ea']);



		//부가세환급
		$buga_price = ($row['danga'] * $row['chul_ea']) * 0.1;

		//부가세환급 합계
		$total_buga_price = $total_buga_price + ($row['danga'] * $row['chul_ea']) * 0.1;


		

		// 손익(원) = ( 매출 * 환율 세트합산 +부가세환급 세트합산 - TAX ) - (수수료1 세트합산 + 수수료2 세트합산 + 기본배송비 + 추가배송비 + 합계 세트합산 ) - 기준으로 왼쪽 오른쪽 계산 후 -

		//매출 * 환율(세트합산)
		$set_sales_price = $set_sales_price + $wr_singo_rate;

		//부가세환급(세트합산)
		$set_buga_price = $set_buga_price + $buga_price;



		//TAX(세트별로 1개씩만 계산)
		if($acc5_set_count == $chknum){
			$tax_price = floor(((float)$row['wr_tax']) * $exchange_rate);
			$set_tax_price = $set_tax_price + $tax_price;
		}

		//수수료1(세트합산)
		$basic_fee_price = floor($basic_fee_rate);
		$set_basic_fee_price = $set_basic_fee_price + $basic_fee_price;


		//기본배송비/추가배송비
		$set_delivery1_price = $set_delivery1_price + $wr_delivery_fee;
		$set_delivery2_price = $set_delivery2_price + $wr_delivery_fee2;

		//합계(세트합산)
		$set_total_price = $set_total_price + ($row['danga'] * $row['chul_ea']);



		$pm_price = ($set_sales_price + $set_buga_price - $set_tax_price ) - ($set_basic_fee_price + $set_delivery1_price + $set_delivery2_price + $set_total_price);
		$pm_price = floor($pm_price);


		//$pm_price = get_pm_price ($wr_ori_order_num_val, $exchange_rate, $wr_domain, $stwr_domaindate, $ed_date);



		//echo $set__sales_price;
		//exit;


		//이익률(%) = 이익률 손익 / 매출 * 환율 * 100 
		if($acc5_set_count == $chknum){
			$pm_rate = floor($pm_price) / $set_sales_price * 100;
		}




		$total_ea = $total_ea + $row['chul_ea'];


		$wr_ori_order_num = $row['wr_ori_order_num'];



		if ($i%2==0){
			//$b_color="#e6e6e6";
		}else{
			//$b_color="#ffffff";
		}

	


?>
		<tr>

			<td style="background-color: <?=$b_color?>;"><?=$row['wr_domain']?></td>
			<td ><?=$row['wr_date4']?></td>

			<td >
				<?=$row['wr_order_num']?>
				<br><?=$row['wr_ori_order_num']?>
			</td>

			<td ><?=$row['wr_subject']?></td>
			<td ><?=$row['sku']?></td>
			<td ><?=$row['p_code']?></td>
			<td ><?=$row['wr_currency']?></td>


			<!-- 수수료1/수수료2 -->
			<td >
				<?=number_format($basic_fee_rate)?>
				
			</td>
			<td ><?=$sales_fee_rate?></td>


			<!-- 기본 배송비 / 추가 배송비 -->
			<td ><?=$wr_delivery_fee?></td>
			<td ><?=$wr_delivery_fee2?></td>
			

			<!-- 매입원가/	수량/	합계/	환율 -->
			<td ><?=$row['danga']?></td>
			<td ><?=$row['chul_ea']?></td>
			<td ><?=$row['danga']*$row['chul_ea']?></td>
			<td ><?=$exchange_rate?></td>


			<!-- 매출단가	 / 신고가격	 -->
			<td ><?=(float)$row['wr_danga']?></td>
			<td ><?=(float)$row['wr_singo']?></td>


			<!-- 매출 * 환율(원) -->
			<!-- // (set_sales_price + set_buga_price - set_tax_price ) - (set_basic_fee_price + set_delivery1_price + set_delivery2_price + set_total_price) -->
			<td>

				<!-- g5_sales3_det 테이블에 중복 데이터가 있을경우 계산x -->
				<?if($sales3_det_num_count < $acc5_wr_order_num_count){?>
					
				<?}else{?>
					<?=number_format($wr_singo_rate)?>
				<?}?>

				
				<!--<br> <?=$chknum?> -->

				<?//if($acc5_set_count == $chknum){?> 
					<!--<br> <?=$acc5_wr_order_num_count?>-->
					
				<?//}?>

				<?if($acc5_wr_order_num_count > 1){?>
					<!--<br> 주문번호 중복순서: <?=$sales3_det_num_count?>-->
				<?}?>

				<!--<br> 번호: <?=$i?>-->


			</td>
			

			<!-- TAX / 손익(원)	/ 이익률(%)	 TAX = wr_tax + wr_shipping_price * 환율 -->

			<?if($acc5_set_count == $chknum){?> 
			
				<td><?=floor(((float)$row['wr_tax']) * $exchange_rate)?></td>

			<?}else{?>
				<td style="border-bottom: 0px solid #d9dee9;"></td>			
			<?}?>




			<!-- 손익(원) -->
			<!--  셋트 마지막 일 경우 -->
			<!-- (set_sales_price + set_buga_price - set_tax_price ) - (set_basic_fee_price + set_delivery1_price + set_delivery2_price + set_total_price); -->
			<?if($acc5_set_count == $chknum){?> 

				<td>
					<?=number_format($pm_price)?> <!--<br> 번호: <?=$i?>-->
					<!--<br> <?php echo $set_tax_price;?>-->
				</td>

			<?}else{?>
				<td style="border-bottom: 0px solid #d9dee9;"></td>			
			<?}?>




			<!-- 이익률 손익 / 매출 * 환율 * 100 -->
			<?if($acc5_set_count == $chknum){?> 

				<td><?=sprintf('%0.2f',$pm_rate)?></td>

			<?}else{?>
				<td style="border-bottom: 0px solid #d9dee9;"></td>			
			<?}?>



			

			<!-- 부가세환급 -->
			<td><?=number_format($buga_price)?></td>


		</tr>

	<?

		if($acc5_set_count == $chknum){



			$total_pm_price = $total_pm_price + $pm_price;


			$total_rate = floor($total_pm_price) / $total_sales_price * 100;
			$total_rate =  floor($total_rate);



			$set_sales_price = 0;
			$set_buga_price = 0;
			$set_tax_price = 0;
			$set_basic_fee_price = 0;

			$set_delivery1_price = 0;
			$set_delivery2_price = 0;

			$set_total_price = 0;

		}



		// 셋트 갯수랑 중가값이 같다면 1개세트끝 다시 chknum 초기화
		if($acc5_set_count == $chknum){
			$chknum = 1;
		}else{
			$chknum++;
		}


		// g5_sales3_det 테이블에 중복 데이터가 있을경우 마지막 중복데이터빼고 나머지++
		if($acc5_wr_order_num_count > $sales3_det_num_count){
			$sales3_det_num_count ++;
		}else{
			$sales3_det_num_count = 1; //초기화
		}


	}?>
	
		
		
	<!-- 합계 -->
	<tr>
		<td colspan="7">합계 </td>
		<td><?=number_format($total_basic_fee)?></td>
		<td><?=number_format($total_sales_fee)?></td>
		<td><?=number_format($total_delivery_fee)?></td>
		<td><?=number_format($total_delivery_fee2)?></td>
		<td></td>
		<td><?=number_format($total_ea)?></td>
		<td><?=number_format($total_ibgo_price)?></td>
		<td></td>
		<td></td>
		<td></td>

		<!-- 매출 * 환율 -->
		<td><?=number_format($total_sales_price)?></td>

		<!-- tax -->
		<td><?=number_format($total_tax)?></td>

		<td><?=number_format($total_pm_price)?></td>

		<td><?=$total_rate?></td>


		<td><?=number_format($total_buga_price)?></td>


	</tr>
	<!-- //합계 -->




	
		<?}else{?>
			<tr>
				<td colspan="18">데이터가 존재하지 않습니다.</td>
			</tr>
		<?}?>
	</tbody>
</table>
