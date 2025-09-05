<?php
include_once('./_common.php');

if ($is_guest)
	alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

if (!$wr_19_s)
	$wr_19_s = G5_TIME_YMD;

if (!$wr_19_e)
	$wr_19_e = G5_TIME_YMD;

$mode = isset($_GET['mode']) ? trim($_GET['mode']) : '';
if (!$mode)
	$mode = 'crawling';

$stx = isset($_GET['stx']) ? trim($_GET['stx']) : '';
if (empty($stx)) {
	$stx = '';
	$escaped_stx = '';
} else {
	$escaped_stx = sql_real_escape_string($stx);
}

$total_products_base_sql = "SELECT COUNT(*) FROM crawling_product";
$total_products_where_clause = "";

if ($stx) {
	$total_products_where_clause = " WHERE product_id = '{$escaped_stx}' OR product_title LIKE '%{$escaped_stx}%'";
}

$items_per_page = 1;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$total_products_sql = $total_products_base_sql . $total_products_where_clause;
$total_result = sql_query($total_products_sql);
$total_products = $total_result->fetch_row()[0];

$total_pages = ceil($total_products / $items_per_page);

$offset = ($current_page - 1) * $items_per_page;
if ($offset < 0) $offset = 0;

$site_sql = "SELECT site_name FROM cms_crawling_site WHERE is_active = 1";
$target_site = sql_fetch_all($site_sql);
$target_site_json = json_encode($target_site);

?>
<link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
<link href="https://cdn.jsdelivr.net/npm/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />
<style>
	.not_item td {
		background: red;
		color: #fff
	}

	th,
	td {
		position: relative;
	}

	.resize-handle {
		position: absolute;
		width: 5px;
		height: 100%;
		top: 0;
		right: -2px;
		cursor: col-resize;
	}

	.tooltip {
		position: relative;
		display: inline-block;
		border-bottom: 1px dotted black;
		/* If you want dots under the hoverable text */
	}

	/* Tooltip text */
	.tooltip .tooltiptext {
		visibility: hidden;
		width: 470px;
		background-color: black;
		color: #fff;
		text-align: center;
		padding: 5px 0;
		border-radius: 6px;

		/* Position the tooltip text - see examples below! */
		position: absolute;
		left: 0;
		top: -40px;
		z-index: 1;
	}

	/* Show the tooltip text when you mouse over the tooltip container */
	.tooltip:hover .tooltiptext {
		visibility: visible;
	}

	.no_ea1,
	.no_ea2,
	.no_ea3,
	.no_ea4,
	.no_ea5,
	.no_ea6,
	.no_ea7 {
		font-weight: 600;
		color: red !important
	}

	.tbl_head01 tbody td.truncate-single-line {
		text-align: left;
		max-width: 350px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.pagination {
		text-align: center;
		margin-top: 20px;
	}

	.pagination a,
	.pagination span {
		display: inline-block;
		padding: 8px 16px;
		margin: 0 4px;
		border: 1px solid #ddd;
		text-decoration: none;
		color: #333;
		border-radius: 4px;
	}

	.pagination a:hover {
		background-color: #f0f0f0;
	}

	.pagination span.current-page {
		background-color: #007bff;
		color: white;
		border-color: #007bff;
		font-weight: bold;
	}

	.clickable-row {
		cursor: pointer;
	}

	/* 선택된 행에 적용될 스타일 */
	.clickable-row.selected-row {
		border: 3px solid mediumspringgreen;
		box-sizing: border-box;
		/* 테두리가 요소의 너비/높이에 포함되도록 (선택 사항) */
	}

	.rowspan-linked-row.selected-row {
		border: 3px solid mediumspringgreen;
		box-sizing: border-box;
	}
</style>
<div class="new_win">
	<h1>가격 비교</h1>
	<form name="searchFrm" method="get" autocomplete="off">
		<div id="excelfile_upload">
			<button type="button" class="btn_b02 btn_bo_sch">대상추가</button>
			<!-- <button class="btn url_add">사이트추가</button> -->
			<input type="hidden" name="mode" id="mode_input" value="search">
			<input type="text" name="stx" value="<?= htmlspecialchars($stx) ?>" class="frm_input" style="width:400px" placeholder="상품코드, 상품명 검색">
			<button type="button" class="btn btn_admin" data-mode="search">
				<i class="fa fa-search" aria-hidden="true"></i>검색
			</button>
			<button type="button" class="btn_b02 crawling" data-mode="crawling">크롤링</button>
			<button type="button" class="btn_b02 all_product" data-mode="all_product">전체상품</button>
		</div>
	</form>

	<div id="excelfile_upload" class="result_list" style="overflow-x:scroll;max-height:79vh;padding:0">
		<div class="tbl_head01 tbl_wrap" style="min-width:1700px;margin-bottom:70px">
			<table>
				<thead style="position:sticky;top:0;z-index:99">
					<tr style="height:50px">
						<th style="width:40px">
							<div class="resize-handle"></div>순번
						</th>
						<th style="width:80px;display:none;">
							<div class="resize-handle"></div>상품코드
						</th>
						<th style="width:300px">
							<div class="resize-handle"></div>상품명
						</th>
						<th style="width:80px;display:none;">
							<div class="resize-handle"></div>옵션코드
						</th>
						<th style="width:50px">
							<div class="resize-handle"></div>재고
						</th>
						<th style="width:50px">
							<div class="resize-handle"></div>옵션
						</th>
						<th style="width:50px">
							<div class="resize-handle"></div>DODO가
						</th>
						<th style="width:50px">
							<div class="resize-handle"></div>최저가
						</th>
						<th style="width:30px">
							<div class="resize-handle"></div>할인률
						</th>
						<th style="width:50px">
							<div class="resize-handle"></div>적용가
						</th>
						<th style="width:50px">
							<div class="resize-handle"></div>몰
						</th>
						<th style="width:350px">
							<div class="resize-handle"></div>url
						</th>
						<th style="width:50px">
							<div class="resize-handle"></div>현재가
						</th>
						<th style="width:20px">
							<div class="resize-handle"></div>상태
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					switch ($mode) {
						case "crawling":
							$sql = "SELECT
											cp.*,
											ct.id AS target_id,
											ct.target_price,
											ct.site,
											ct.url,
											ct.is_active,
											ct.reg_datetime
									FROM
											crawling_product AS cp
									LEFT JOIN
											crawling_target AS ct ON cp.variant_id = ct.variant_id
									WHERE
											cp.variant_title = 'Default Title'
											AND cp.status = 'active'
											AND ct.variant_id IS NULL
									ORDER BY    
											cp.variant_inventory_quantity DESC
									LIMIT
											{$offset}, {$items_per_page}";
							break;

						case "search":
							$sql_subquery_base = "SELECT * FROM crawling_product";
							$sql_subquery_where = "";

							if ($stx) {
								$sql_subquery_where = " WHERE product_id = '{$escaped_stx}' OR product_title LIKE '%{$escaped_stx}%' OR variant_sku ='{$escaped_stx}'";
							}
							$sql = "SELECT
													cp.*, 
													ct.id AS target_id,
													ct.target_price,
													ct.site,
													ct.url,
													ct.is_active,
													ct.reg_datetime
											FROM
													({$sql_subquery_base}{$sql_subquery_where} ORDER BY id DESC LIMIT {$offset}, {$items_per_page}) AS cp											
											LEFT JOIN
													crawling_target AS ct ON cp.variant_id = ct.variant_id
											ORDER BY
													cp.variant_inventory_quantity DESC";
							break;

						case "all_product":
							$sql = "SELECT
													cp.*,
													ct.id AS target_id,
													ct.target_price,
													ct.site,
													ct.url,
													ct.is_active,
													ct.reg_datetime
											FROM
													( 
															SELECT
																	cp_sub.variant_id,
																	cp_sub.variant_inventory_quantity
															FROM
																	crawling_product AS cp_sub
															WHERE
																	EXISTS (SELECT 1 FROM crawling_target AS ct_sub WHERE ct_sub.variant_id = cp_sub.variant_id)
																	AND cp_sub.variant_title like 'Default Title'
																	AND cp_sub.status like 'active'
															ORDER BY
																	cp_sub.variant_inventory_quantity DESC
															LIMIT {$offset}, {$items_per_page}
													) AS top_10_cp_variants											
											INNER JOIN
													crawling_product AS cp ON cp.variant_id = top_10_cp_variants.variant_id
											INNER JOIN
													crawling_target AS ct ON cp.variant_id = ct.variant_id
											ORDER BY
													cp.variant_inventory_quantity DESC";
							break;
					}
					$result = sql_query($sql);

					$products = [];

					if ($result && $result->num_rows > 0) { // $result가 false가 아니고, 결과 행이 있을 경우에만 루프 실행
						while ($row = $result->fetch_assoc()) {
							$id = $row['id'];

							if (!isset($products[$id])) {
								$products[$id] = [
									'product_data' => [
										'id' => $row['id'],
										'product_id' => $row['product_id'],
										'product_title' => $row['product_title'],
										'online_store_url' => $row['online_store_url'],
										'variant_id' => $row['variant_id'],
										'variant_sku' => $row['variant_sku'],
										'variant_price' => $row['variant_price'],
										'variant_inventory_quantity' => $row['variant_inventory_quantity'],
										'variant_title' => $row['variant_title'],
										'cutoff' => $row['cutoff'],
									],
									'targets' => []
								];
							}

							if (!is_null($row['target_id'])) {
								$products[$id]['targets'][] = [
									'id' => $row['target_id'],
									'target_price' => $row['target_price'],
									'site' => $row['site'],
									'url' => $row['url'],
									'is_active' => $row['is_active'],
									'reg_datetime' => $row['reg_datetime'],
								];
							} else {
								$products[$id]['targets'][] = [
									'id' => null,
									'target_price' => null,
									'site' => null,
									'url' => null,
									'is_active' => null,
									'reg_datetime' => null,
								];
							}
						}
					}

					foreach ($products as $id => $product) {
						$data = $product['product_data'];
						$targets = $product['targets'];
						$target_count = count($targets);

						$target_prices = array_map(function ($target) {
							return (float)$target['target_price'];
						}, $targets);

						if ($target_prices) {
							$min_target_price = min($target_prices);
						} else {
							$min_target_price = 0.0;
						}

					?>
						<tr style="height: 40px" data-id="<?= $data['id'] ?>" class="clickable-row">
							<td rowspan="<?= $target_count ?>"><?= $data['id'] ?></td>
							<td rowspan="<?= $target_count ?>" style="display: none;"><?= $data['product_id'] ?></td>
							<td rowspan="<?= $target_count ?>" class="truncate-single-line" style="max-width: 300px">
								<a href="<?= $data['online_store_url'] ?>" target="_blank" style="text-decoration: none; color: inherit;">
									<?= $data['product_title'] ?>
								</a>
							</td>
							<td rowspan="<?= $target_count ?>" style="display: none;"><?= $data['variant_id'] ?></td>
							<td rowspan="<?= $target_count ?>"><?= $data['variant_inventory_quantity'] ?></td>
							<td rowspan="<?= $target_count ?>"><?= $data['variant_title'] ?></td>
							<td rowspan="<?= $target_count ?>" class="variant_price"><?= $data['variant_price'] ?></td>
							<td rowspan="<?= $target_count ?>" class="min_target_price"><?= $min_target_price ?></td>
							<td rowspan="<?= $target_count ?>" class="cutoff"><?= $data['cutoff'] ?></td>
							<?php $applied_price = round((float)$min_target_price * (1 - (float)$data['cutoff'] / 100), 2); ?>
							<td rowspan="<?= $target_count ?>" class="applied_price"><?= $applied_price ?></td>
							<td class="site-cell"><?= $targets[0]['site'] ?></td>
							<td class="truncate-single-line url-cell"><a href="<?= $targets[0]['url'] ?>" target="_blank"><?= $targets[0]['url'] ?></a></td>
							<td class="price-cell"><?= $targets[0]['target_price'] ?></td>
							<td class="is-active-cell"><?= ($targets[0]['is_active'] == '0') ? '🟢' : '❌' . $targets[0]['is_active']; ?></td>
						</tr>
						<?php
						for ($i = 1; $i < $target_count; $i++) {
						?>
							<tr style="height: 40px">
								<td><?= $targets[$i]['site'] ?></td>
								<td class="truncate-single-line"><a href="<?= $targets[$i]['url'] ?>" target="_blank"><?= $targets[$i]['url'] ?></a></td>
								<td><?= $targets[$i]['target_price'] ?></td>
								<td><?= ($targets[$i]['is_active'] == '0') ? '🟢' : '❌' . $targets[$i]['is_active']; ?></td>
							</tr>
						<?php }	?>

					<?php	} ?>
				</tbody>
			</table>

			<?php if ($total_pages > 1):
			?>
				<div class="pagination">
					<?php
					$query_params = $_GET;
					unset($query_params['page']);

					$base_url = 'price_compare.php?';
					if (!empty($query_params)) {
						$base_url .= http_build_query($query_params) . '&';
					}

					if ($current_page > 1) {
						echo '<a href="' . $base_url . 'page=' . ($current_page - 1) . '">&laquo; 이전</a>';
					}

					$start_page = max(1, $current_page - 2);
					$end_page = min($total_pages, $current_page + 2);

					if ($end_page - $start_page < 4) {
						$start_page = max(1, $end_page - 4);
					}

					if ($end_page - $start_page < 4) {
						$end_page = min($total_pages, $start_page + 4);
					}

					for ($i = $start_page; $i <= $end_page; $i++) {
						if ($i == $current_page) {
							echo '<span class="current-page">' . $i . '</span>';
						} else {
							echo '<a href="' . $base_url . 'page=' . $i . '">' . $i . '</a>';
						}
					}

					if ($current_page < $total_pages) {
						echo '<a href="' . $base_url . 'page=' . ($current_page + 1) . '">다음 &raquo;</a>';
					}
					?>
				</div>
			<?php endif;
			?>
		</div>
	</div>

	<div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;background:#fff;border-top:1px solid #ddd;margin-bottom:0">
	</div>
</div>

<div class="bo_sch_wrap">
	<fieldset class="bo_sch">
		<h3>대상추가</h3>
		<form name="fsearch" method="get">
			<!-- <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px;">
				<span>몰</span>
				<select id="site-select-php" name="site_php">
					<option value="">사이트 선택</option>
					<?php /* 
					foreach ($target_site as $site) {
						$site_name = htmlspecialchars($site['site_name']);
						echo "<option value=\"{$site_name}\">{$site_name}</option>\n";
					}
					*/ ?>
				</select>
			</div> -->
			<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
				<span>url</span><input type="text" id="product-url-input" name="product_url" value="" class="frm_input" style="width:100%;">
			</div>
			<!-- <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
				<span>현재가</span><input type="text" id="product-price-input" name="product_price" value="" class="frm_input" style="width:100%;">
			</div> -->
			<button type="button" id="add-target-btn" value="대상추가" class="btn_b01" style="width:49%;margin-top:15px">대상추가</button>
			<button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat" aria-hidden="true"></i> 검색초기화</button>
			<button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
		</form>

	</fieldset>
	<div class="bo_sch_bg"></div>
</div>
<script>
	jQuery(function($) {
		$(".btn_bo_sch").on("click", function() {
			$(".bo_sch_wrap").toggle();
		})
		$('.bo_sch_bg, .bo_sch_cls').click(function() {
			$('.bo_sch_wrap').hide();
		});
	});

	$(document).ready(function() {
		$('.clickable-row').on('click', function() {
			const clickedRow = $(this);

			$('.clickable-row.selected-row').removeClass('selected-row');
			$('.rowspan-linked-row.selected-row').removeClass('selected-row rowspan-linked-row');

			clickedRow.addClass('selected-row');

			let currentRow = clickedRow;
			const firstTd = clickedRow.find('td:first');
			const rowspanValue = parseInt(firstTd.attr('rowspan'));

			if (!isNaN(rowspanValue) && rowspanValue > 1) {
				for (let i = 0; i < rowspanValue - 1; i++) {
					currentRow = currentRow.next('tr');
					if (currentRow.length) {
						currentRow.addClass('selected-row rowspan-linked-row');
					} else {
						break;
					}
				}
			}
		});
	});

	// PHP에서 정의된 $products_json 배열을 자바스크립트 변수로 전달
	const productsData = <?php echo json_encode($products); ?>;

	$(document).on('click', '#add-target-btn', function() {
		const $selectedRow = $('.clickable-row.selected-row');

		if ($selectedRow.length === 0) {
			alert('먼저 대상을 추가할 행을 선택해 주세요.');
			return;
		}

		const selectedDataId = $selectedRow.data('id').toString();

		// const selectedSite = $('#site-select-php').val(); // select 박스의 현재 선택 값
		const productUrl = $('#product-url-input').val(); // input 박스의 현재 입력 값
		// const productPrice = $('#product-price-input').val(); // select 박스의 현재 선택 값
		// const isActive = '🟢';

		if (!productUrl) {
			alert('몰과 URL을 모두 입력해 주세요.');
			return;
		}

		if (productUrl.startsWith("https://jolse.com")) {
			selectedSite = "jolse";
		} else if (productUrl.startsWith("https://www.amazon.com")) {
			selectedSite = "amazon";
		} else if (productUrl.startsWith("https://www.stylekorean.com")) {
			selectedSite = "stylekorea";
		} else if (productUrl.startsWith("https://www.stylevana.com")) {
			selectedSite = "stylevana";
		} else if (productUrl.startsWith("https://www.yesstyle.com")) {
			selectedSite = "yesstyle";
		} else if (productUrl.startsWith("https://global.oliveyoung.com")) {
			selectedSite = "oliveyoung";
		} else {
			// 어떤 조건에도 해당하지 않을 경우의 처리
			selectedSite = "unknown";
			console.log("알 수 없는 사이트입니다:", productUrl);
		}

		let product_id = null;
		let variant_id = null;
		// let variant_price = null;

		const productInfo = productsData[selectedDataId];

		if (productInfo && productInfo.product_data) {
			product_id = productInfo.product_data.product_id;
			variant_id = productInfo.product_data.variant_id;
		}

		if (!product_id || !variant_id) {
			alert('선택된 행의 상품 정보를 찾을 수 없습니다.');
			return;
		}

		const postData = {
			product_id: product_id,
			variant_id: variant_id,
			site: selectedSite,
			url: productUrl,
			// target_price: productPrice,
			// is_active: 0 // 요청에 따라 is_active는 무조건 0으로 설정
		};

		$.ajax({
			url: 'insert_crawling_target.php', // TODO: 실제 PHP 파일 경로로 변경해주세요.
			type: 'POST',
			data: postData,
			dataType: 'json', // 서버로부터 JSON 응답을 기대합니다.
			success: function(response) {
				if (response.success) {
					let rowspan;
					const $siteCell = $selectedRow.find('.site-cell');

					if ($siteCell.length > 0 && $.trim($siteCell.text()).length > 0) {
						$selectedRow.children('td').each(function() {
							const $td = $(this);

							if ($td.hasClass('site-cell')) {
								return false; // jQuery의 each() 루프를 중단하는 방법
							}

							rowspan = $td.attr('rowspan'); // 현재 rowspan 속성 값 (문자열)

							if (rowspan) {
								rowspan = parseInt(rowspan, 10); // 숫자로 변환
								rowspan++; // 1 증가
								$td.attr('rowspan', rowspan); // 새로운 rowspan 값 설정
							}
						});
					}

					if (rowspan > 1) {
						let newRowHtml = '<tr class="selected-row rowspan-linked-row" style="height: 40px;">';

						newRowHtml += `<td>${selectedSite}</td>`; // 몰(사이트) 값
						newRowHtml += `<td class="truncate-single-line"><a href="${productUrl}" target="_blank">${productUrl}</a></td>`; // URL 값
						// newRowHtml += `<td>${productPrice}</td>`; // 몰(사이트) 값
						// newRowHtml += `<td>${isActive}</td>`; // is_active (임시)
						newRowHtml += '</tr>';

						$selectedRow.after(newRowHtml);
					} else {
						$selectedRow.find('.site-cell').text(selectedSite); // 텍스트로 사이트 이름 채우기

						const $urlCellLink = $selectedRow.find('.url-cell a');
						$urlCellLink.attr('href', productUrl); // a 태그의 href 속성 변경
						$urlCellLink.text(productUrl); // a 태그의 텍스트 변경 (사용자가 볼 내용)

						// $selectedRow.find('.price-cell').text(productPrice);
						// $selectedRow.find('.is-active-cell').text(isActive);
					}
				} else {
					alert('크롤링 대상 정보 추가에 실패했습니다: ' + response.message);
					console.error('Server Error:', response.error); // 개발자 도구에서 상세 에러 확인
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert('서버와 통신 중 오류가 발생했습니다.');
				console.error('AJAX Error:', textStatus, errorThrown, jqXHR.responseText); // 상세 에러 로깅
			}
		});

		// const $minTargetPriceTd = $selectedRow.find('.min_target_price');

		// if ($minTargetPriceTd.length === 0) {
		// 	return true; // jQuery .each()에서 다음 요소로 넘어갑니다.
		// }

		// let minTargetPriceText = $minTargetPriceTd.text().trim();
		// let currentMinTargetPrice = parseFloat(minTargetPriceText);

		// if (minTargetPriceText === '0' || isNaN(currentMinTargetPrice)) {
		// 	$minTargetPriceTd.text(productPrice);
		// 	currentMinTargetPrice = productPrice; // 계산에 사용할 값도 업데이트
		// } else {
		// 	if (currentMinTargetPrice > productPrice) {
		// 		$minTargetPriceTd.text(productPrice);
		// 		currentMinTargetPrice = productPrice; // 계산에 사용할 값도 업데이트
		// 	}
		// }

		// const appliedPriceElement = $selectedRow.find('.applied_price'); // 해당 행 내의 .applied_price td를 찾습니다.
		// const cutOffElement = $selectedRow.find('.cutoff');
		// const cutoff = parseFloat(cutOffElement.text().trim());

		// if (appliedPriceElement.length > 0) {
		// 	const calculatedAppliedPrice = (currentMinTargetPrice * (1 - (cutoff / 100)));
		// 	const finalAppliedPrice = parseFloat(calculatedAppliedPrice.toFixed(2));

		// 	appliedPriceElement.text(finalAppliedPrice);

		// 	if (product_id && variant_id !== undefined && finalAppliedPrice !== null && !isNaN(finalAppliedPrice)) {
		// 		$.ajax({
		// 			url: 'update_crawling_product.php', // PHP 파일 경로
		// 			type: 'POST', // 데이터 변경이므로 POST 사용
		// 			dataType: 'json', // 서버로부터 JSON 응답을 기대
		// 			data: {
		// 				product_id,
		// 				variant_id,
		// 				variant_price: finalAppliedPrice // 업데이트할 최종 가격
		// 			},
		// 			success: function(response) {
		// 				if (response.success) {
		// 					console.log(`[${product_id}, ${variant_id}] 가격 업데이트 성공: ${response.message}`);
		// 				} else {
		// 					console.error(`[${product_id}, ${variantId}] 가격 업데이트 실패: ${response.message}`);
		// 					alert(`가격 업데이트 실패 [${product_id}, ${variant_id}]: ${response.message}`);
		// 				}
		// 			},
		// 			error: function(jqXHR, textStatus, errorThrown) {
		// 				console.error(`[${product_id}, ${variant_id}] AJAX 오류:`, textStatus, errorThrown, jqXHR.responseText);
		// 				alert(`서버 통신 중 오류 발생 [${product_id}, ${variant_id}].`);
		// 			}
		// 		});
		// 	} else {
		// 		console.warn('필수 데이터(product_id, variant_id, finalAppliedPrice)가 누락되어 AJAX 요청을 건너뜁니다.', {
		// 			product_id,
		// 			variant_id,
		// 			finalAppliedPrice,
		// 			row: $row
		// 		});
		// 	}
		// } else {
		// 	console.warn("해당 행에 .applied_price 클래스를 가진 요소가 없습니다:", $selectedRow);
		// }

		// $('#site-select-php').val(null);
		$('#product-url-input').val(null);
		// $('#product-price-input').val(null);

		$('.bo_sch_wrap').hide();
	});

	$(document).on('click', '.bo_sch_cls', function() {
		$('.bo_sch_wrap').hide();
	});

	document.addEventListener('DOMContentLoaded', function() {
		const searchForm = document.forms['searchFrm'];
		const modeInput = document.getElementById('mode_input');
		const modeChangeButtons = document.querySelectorAll('button[data-mode]');

		modeChangeButtons.forEach(button => {
			button.addEventListener('click', function() {
				const selectedMode = this.dataset.mode; // data-mode="xxx" -> this.dataset.mode (xxx)
				modeInput.value = selectedMode;

				searchForm.submit();
			});
		});
	});
</script>
<?php
include_once(G5_PATH . '/tail.sub.php');
