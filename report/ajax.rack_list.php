<?
include_once('../common.php');

switch($wr_warehouse){
	case "1000":
		$warehouse = "한국";
	break;
	case "3000":
		$warehouse = "미국";
	break;
	case "4000":
		$warehouse = "FBA";
	break;
	case "5000":
		$warehouse = "W-FBA";
	break;
	case "6000":
		$warehouse = "U-FBA";
	break;
	case "7000":
		$warehouse = "한국반품창고";
	break;
	case "8000":
		$warehouse = "미국반품창고";
	break;
	case "9000":
		$warehouse = "임시창고";
	break;
}

# 임시창고 및 전체 재고 합계 불러오기
$sql = "SELECT SUM(wr_37) AS wr_37,SUM(wr_37_real) AS wr_37_real \n";
$sql .= "	,SUM(wr_32 + wr_36 + wr_40 + wr_41 + wr_42 + wr_43 + wr_44) AS total_stock \n";
$sql .= "	,SUM(wr_32_real + wr_36_real + wr_40_real + wr_41_real + wr_42_real + wr_43_real + wr_44_real) AS total_stock_real \n";
$sql .= "FROM g5_write_product";
$tempRow = sql_fetch($sql);
?>
<!-- 재고 업데이트 -->
<form name="report_frm" id="report_frm" method="post">
	<input type="hidden" name="rack_warehouse" name="rack_warehouse" value="<?=$wr_warehouse?>"/>
	<div id="excelfile_upload" class="result_list" style="padding:12px;width:100%;float:left;">
		<?if($wr_warehouse != "9000"){?>
			<div class="tbl_head01 tbl_wrap" style=" float:left;width:100%;height:550px;">
				<table>
					<thead style="position:sticky;top:0;">
						<tr>
							<th style="width:100%" colspan="4"><strong><?=$warehouse?>창고</strong> </th>
							<th style="width:100%" colspan="4"><strong>실사재고 수량</strong><br><span id="report_title<?=$wr_warehouse?>"></span></th>
						</tr>
						<tr >
							<th style="width:10%">랙 번호</th>
							<th style="width:15%">SKU</th>
							<th style="width:15%">대표코드</th>
							<th style="width:20%">상품명</th>
							<th style="width:10%">총재고</th>
							<th style="width:10%">출고예정재고</th>
							<th style="width:10%">잔여재고</th>
							<th style="width:10%">실사재고</th>
						</tr>
					</thead>
					<tbody id="<?=$wr_warehouse?>_list">
						<?if($wr_rack){?>
							<?php
							$sql_where = "";
							$join_where = "";
							if($wr_rack){
								$sql_where .= " AND GR.seq='".$wr_rack."' ";
								$join_where .= " AND G2.wr_rack='".$wr_rack."' ";
							}

							$field = $storage_arr[$wr_warehouse]['field'];
							$field_real = $storage_arr[$wr_warehouse]['field_real'];

							# 각 랙별 랙이름, 상품 정보 , 재고
							$sql = "SELECT GR.seq AS rack_sea \n";
							$sql .= "	,GR.gc_name";
							$sql .= "	,GRS.wr_product_id \n";
							$sql .= "	,GWP.wr_subject \n";
							$sql .= "	,GWP.wr_1 AS sku1,GWP.wr_27 AS sku2,GWP.wr_28 AS sku3,GWP.wr_29 AS sku4,GWP.wr_30 AS sku5,GWP.wr_31 AS sku6 \n";
							$sql .= "	,GWP.wr_5 AS code1,GWP.wr_6 AS code2,GWP.wr_4 AS code3 \n";
							$sql .= "	,GWP.{$field} AS product_stock,GWP.{$field_real} AS product_real_stock \n";
							$sql .= "	,GWP.wr_37,GWP.wr_37_real \n";
							$sql .= "	,IFNULL(GRS.total_stock,0) AS total_stock \n";
							$sql .= "	,IFNULL(GS3.temp_stock,0) AS temp_stock \n";
							$sql .= "	,IFNULL(GS3_1.rack_stock,0) AS rack_stock \n";
							$sql .= "FROM g5_rack GR \n";
							$sql .= "LEFT OUTER JOIN( \n";
							$sql .= "	SELECT wr_rack,wr_product_id,SUM(wr_stock) AS total_stock FROM g5_rack_stock GROUP BY wr_rack,wr_product_id \n";
							$sql .= ")GRS ON GRS.wr_rack = GR.seq \n";
							$sql .= "LEFT OUTER JOIN g5_write_product GWP ON GWP.wr_id=GRS.wr_product_id \n";
							$sql .= "LEFT OUTER JOIN( \n";
							$sql .= "	SELECT IFNULL(G2.wr_rack,1) AS wr_rack,G2.wr_product_id,SUM(G2.wr_ea) AS  temp_stock FROM g5_sales2_list G2 \n";
							$sql .= "	LEFT OUTER JOIN g5_sales3_list G3 ON G3.wr_order_num = G2.wr_order_num AND G3.wr_warehouse='{$wr_warehouse}' \n";
							$sql .= "	WHERE IFNULL(G3.wr_release_use,0) = '0' AND G2.wr_direct_use = '0' {$join_where} \n";
							$sql .= "	GROUP BY IFNULL(G2.wr_rack,1),G2.wr_product_id \n";
							$sql .= ")GS3 ON GS3.wr_product_id = GRS.wr_product_id AND GRS.wr_rack = 1  \n";
							$sql .= "LEFT OUTER JOIN(  \n";
							$sql .= "	SELECT IFNULL(G2.wr_rack,1) AS wr_rack,G2.wr_product_id,SUM(G2.wr_ea) AS  rack_stock FROM g5_sales2_list G2 \n";
							$sql .= "	LEFT OUTER JOIN g5_sales3_list G3 ON G3.wr_order_num = G2.wr_order_num AND G3.wr_warehouse='{$wr_warehouse}' \n";
							$sql .= "	WHERE IFNULL(G3.wr_release_use,0) = '0' AND G2.wr_direct_use = '1' {$join_where} \n";
							$sql .= "	GROUP BY IFNULL(G2.wr_rack,1),G2.wr_product_id \n";
							$sql .= ")GS3_1 ON GS3_1.wr_product_id = GRS.wr_product_id AND GS3_1.wr_rack = GRS.wr_rack  \n";
							$sql .= "WHERE GR.gc_warehouse = '{$wr_warehouse}' AND GR.gc_use='1'  \n";
							$sql .= "		AND GR.seq NOT IN(1) AND GRS.total_stock > 0 {$sql_where} \n";
							$sql .= "ORDER BY CAST(GR.gc_nm1 AS INTEGER) ASC,GR.gc_nm2 ASC, CAST(GR.gc_nm3 AS INTEGER) ASC ,GRS.wr_product_id ASC";
							$rs = sql_query($sql);
							$list = array();
							while($row = sql_fetch_array($rs)){
								$list[$row['gc_name']][] = $row;
							}
							# 랙명칭으로 SORTING
							ksort($list,SORT_STRING );

							#$list = sort($list,SORT_STRING );
							$kor_stock = 0;
							$sql_common = " from g5_rack ";
							$sql_search = " where gc_warehouse = '{$wr_warehouse}' and gc_use = 1 ORDER BY CAST(gc_nm1 AS INTEGER) ASC,gc_nm2 ASC, CAST(gc_nm3 AS INTEGER) ASC";
							$sql = " select * {$sql_common} {$sql_search}  ";
							$result = sql_query($sql);
							$rack_arr = array();
							while($row = sql_fetch_array($result)){
								$rack_arr[] = $row;
							}

							foreach($rack_arr as $k => $v){
								$rowspan = count((array)$list[$v['gc_name']]);
								$rack_data = $list[$v['gc_name']];
							?>
								<?for($i = 0; $i < $rowspan ; $i++){
									$row = $rack_data[$i];
									if($row['rack_sea'] == 1){
										$stock_real = $row['wr_37_real'];
										$sales_stock = $row['temp_stock'];
										$calc_stock = $row['wr_37'];
									}else{
										$stock_real = $row['total_stock'] + $row['rack_stock'];
										$sales_stock = $row['rack_stock'];
										$calc_stock = $row['total_stock'];
									}
									$SKU = $row['sku1'];
									if(!$SKU){
										$SKU = $row['sku2'];
										if(!$SKU){
											$SKU = $row['sku3'];
											if(!$SKU){
												$SKU = $row['sku4'];
												if(!$SKU){
													$SKU = $row['sku5'];
													if(!$SKU){
														$SKU = $row['sku6'];
													}
												}
											}
										}
									}

									$CODE = $row['code1'];
									if(!$CODE){
										$CODE = $row['code2'];
										if(!$CODE){
											$CODE = $row['code3'];
										}
									}
								?>
									<tr class="<?=(($k%2) == 1)?"bg_gray":"bg_white"?> <?=$v['gc_name']?>">
										<?if($i == 0){?>
											<td rowspan="<?=$rowspan?>"><?=$v['gc_name']?></td>
										<?}else{?>
											<td style="display:none;"><?=$v['gc_name']?></td>
										<?}?>
										<td><?=$SKU?></td>
										<td><?=$CODE?></td>
										<td><?=$row['wr_subject']?></td>
										<td>
											<input type="hidden" name="real_stock_arr[<?=$row['rack_sea']?>|<?=$row['wr_product_id']?>]" value="<?=$stock_real?>"/>
											<?=number_format((int)$stock_real)?>
										</td>
										<td><?=number_format((int)$sales_stock)?></td>
										<td>
											<input type="hidden" name="stock_arr[<?=$row['rack_sea']?>|<?=$row['wr_product_id']?>]" value="<?=$calc_stock?>"/>
											<?=number_format((int)$calc_stock)?>
										</td>
										<td>
											<input type="text" name="report_stock_arr[<?=$row['rack_sea']?>|<?=$row['wr_product_id']?>]" class="frm_input" value="">
										</td>
									</tr>
								<?}?>


							<?php 
							   
								
							}?>
						<?}else{?>
							<tr>
								<td colspan="8">랙을 선택해주세요.</td>
							</tr>
						<?}?>
					</tbody>
				</table>
			</div>
		<?}else{?>
			<div class="tbl_head01 tbl_wrap" style=" float:left;width:100%;height:550px;">
				<table>
					<thead style="position:sticky;top:0;">
						<tr>
							<th style="width:100%" colspan="3"><strong><?=$warehouse?>창고</strong> </th>
							<th style="width:100%" colspan="4"><strong>실사재고 수량</strong><br><span id="report_title<?=$wr_warehouse?>"></span></th>
						</tr>
						<tr >
							<th style="width:15%">SKU</th>
							<th style="width:15%">대표코드</th>
							<th style="width:20%">상품명</th>
							<th style="width:10%">총재고</th>
							<th style="width:10%">출고예정재고</th>
							<th style="width:10%">잔여재고</th>
							<th style="width:10%">실사재고</th>
						</tr>
					</thead>
					<tbody id="<?=$wr_warehouse?>_list">
						<?
						$sql_where = "";
						if($sc_subject != ""){
							$sql_where .= " AND REPLACE(TRIM(GWP.wr_subject),' ','') LIKE '%".str_replace(" ","",trim($sc_subject))."%' ";
						}
						
						$sql = "SELECT GWP.wr_subject,GWP.wr_id AS wr_product_id \n";
						$sql .= "	,GWP.wr_1 AS sku1,GWP.wr_27 AS sku2,GWP.wr_28 AS sku3,GWP.wr_29 AS sku4,GWP.wr_30 AS sku5,GWP.wr_31 AS sku6 \n";
						$sql .= "	,GWP.wr_5 AS code1,GWP.wr_6 AS code2,GWP.wr_4 AS code3  \n";
						$sql .= "	,GWP.wr_37,GWP.wr_37_real  \n";
						$sql .= "	,IFNULL(TBL.temp_ea,0) AS temp_ea,IFNULL(TBL.sales_ea,0) AS sales_ea  \n";
						$sql .= "FROM g5_write_product GWP \n";
						$sql .= "LEFT OUTER JOIN( \n";
						$sql .= "	SELECT SUM(wr_stock) AS temp_ea,SUM(IF(GS3.wr_release_use=0,wr_stock2,0)) AS sales_ea,GTW.wr_product_id FROM g5_temp_warehouse GTW \n";
						$sql .= "	LEFT OUTER JOIN g5_sales2_list GS2 ON GS2.seq=GTW.sales2_id \n";
						$sql .= "	LEFT OUTER JOIN g5_sales3_list GS3 ON GS3.wr_order_num = GS2.wr_order_num \n";
						$sql .= "	WHERE GTW.wr_product_id <> 0 AND IFNULL(GS3.wr_release_use,0) = 0 \n";
						$sql .= "	GROUP BY GTW.wr_product_id \n";
						$sql .= ")TBL ON TBL.wr_product_id = GWP.wr_id \n";
						$sql .= "WHERE wr_subject IS NOT NULL ".$sql_where." ORDER BY wr_37 DESC";
						$rs = sql_query($sql);
						while($row = @sql_fetch_array($rs)){
							$SKU = $row['sku1'];
							if(!$SKU){
								$SKU = $row['sku2'];
								if(!$SKU){
									$SKU = $row['sku3'];
									if(!$SKU){
										$SKU = $row['sku4'];
										if(!$SKU){
											$SKU = $row['sku5'];
											if(!$SKU){
												$SKU = $row['sku6'];
											}
										}
									}
								}
							}

							$CODE = $row['code1'];
							if(!$CODE){
								$CODE = $row['code2'];
								if(!$CODE){
									$CODE = $row['code3'];
								}
							}
							$calc_stock = $row['wr_37_real'] - $row['sales_ea'];
						?>
							<tr>
								<td><?=$SKU?></td>
								<td><?=$CODE?></td>
								<td><?=$row['wr_subject']?></td>
								<td>
									<input type="hidden" name="real_stock_arr[<?=$row['wr_product_id']?>]" value="<?=$row['wr_37_real']?>"/>
									<?=number_format($row['wr_37_real'])?>
								</td>
								<td>
									<input type="hidden" name="sales_stock_arr[<?=$row['wr_product_id']?>]" value="<?=$row['sales_ea']?>"/>
									<?=number_format($row['sales_ea'])?>
								</td>
								<td>
									<input type="hidden" name="stock_arr[<?=$row['wr_product_id']?>]" value="<?=$calc_stock?>"/>
									<?=number_format($calc_stock)?>
								</td>
								<td>
									<input type="text" name="report_stock_arr[<?=$row['wr_product_id']?>]" class="frm_input" value="">
								</td>
							</tr>
						<?}?>
					</tbody>
				</table>
			</div>
		<?}?>
		<div style="clear:both"></div>
	</div>
</form>