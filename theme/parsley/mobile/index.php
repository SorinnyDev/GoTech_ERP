<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (!defined('_INDEX_')) define('_INDEX_', true);

include_once(G5_THEME_MOBILE_PATH.'/head.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

add_javascript('<script src="'.G5_JS_URL.'/jquery.bxslider.js"></script>', 10);

add_stylesheet('<link rel="stylesheet" href="/theme/parsley/mobile/skin/latest/basic/style.css">', 0);
?>
<style>
.conle_lt { min-height:330px }
.lt_basic h2 { font-size:15px }
.lt_basic ul { border-top:1px solid #ddd; margin-top:10px }
</style>
<!-- 메인 최신글 시작 -->
<div class="conle_idx_top">
	
	<?php if($member['mb_1'] == "USA") {?>
	<div class="conle_lt">
		<div class="lt_basic">
			<h2><strong><a href="/stock/stock_move_history.php">이관중 재고확인</a></strong></h2>
			<ul>
		<?php 
		$sql = "select * from g5_stock_move where wr_state = 0 order by seq desc limit 5";
		$rst = sql_query($sql);
		
		for ($i=0; $row=sql_fetch_array($rst); $i++) {  
		
		if($row['wr_type'] == 1)
			$type = "한국창고→미국창고";
		else 
			$type = "미국창고→한국창고";
		
		$item = sql_fetch("select wr_1, wr_subject from g5_write_product where wr_id = '{$row['product_id']}'");
		?>
			<li>
				<span class="lt_writer"><strong>[<?php echo $type?>]</strong></span>
				<a href="/stock/stock_move_history.php">
				 <?php echo cut_str($item['wr_subject'], 50)?>
				
				</a>
				<span class="lt_date">
					<span class="lt_date"><?php echo $row['wr_datetime'] ?></span>              
				</span>
			</li>
		<?php }  ?>
		<?php if ($i == 0) { //게시물이 없을 때  ?>
		<li class="empty_li">등록 된 데이터가 없습니다.</li>
		<?php }  ?>
		</ul>
		<a href="/stock/stock_move_history.php" class="lt_more"><span class="sound_only"><?php echo $bo_subject ?></span>더보기</a>

		</div>
    </div>
    
    <div class="conle_lt conle_lt_even">
	   <div class="lt_basic">
				<h2><strong><a href="/stock/stock_move_complete.php">재고이관 처리</a></strong></h2>
				<ul>
			<?php 
			$sql = "select * from g5_stock_move a LEFT JOIN g5_write_product b ON(a.product_id = b.wr_id) order by a.seq desc limit 5";
			$rst = sql_query($sql);
			
			for ($i=0; $row=sql_fetch_array($rst); $i++) {  
			
			if($row['wr_type'] == 1)
				$type = "한국창고→미국창고";
			else 
				$type = "미국창고→한국창고";
			
			$item = sql_fetch("select wr_1, wr_subject from g5_write_product where wr_id = '{$row['product_id']}'");
			?>
				<li>
					<span class="lt_writer"><strong>[<?php echo $type?>]</strong></span>
					<a href="/stock/stock_move_complete.php">
					 <?php echo cut_str($item['wr_subject'], 50)?>
					
					</a>
					<span class="lt_date">
						<span class="lt_date"><?php echo $row['wr_datetime'] ?></span>              
					</span>
				</li>
			<?php }  ?>
			<?php if ($i == 0) { //게시물이 없을 때  ?>
			<li class="empty_li">등록 된 데이터가 없습니다.</li>
			<?php }  ?>
			</ul>
			<a href="/stock/stock_move_complete.php" class="lt_more"><span class="sound_only"><?php echo $bo_subject ?></span>더보기</a>

			</div>
	</div>
	
	<div class="conle_lt conle_bt_lt">
		<div class="lt_basic">
			<h2><strong><a href="/sales/sales3.php?warehouse=3000">출고등록(미국)</a></strong></h2>
			<ul>
		<?php 
		$sql = "select * from g5_sales3_list order by seq desc limit 5";
		$rst = sql_query($sql);
		
		for ($i=0; $row=sql_fetch_array($rst); $i++) {  
		
		$item = sql_fetch("select wr_1, wr_subject from g5_write_product where wr_id = '{$row['wr_product_id']}'");
		?>
			<li>
				<span class="lt_writer"><strong>[<?php echo $row['wr_order_num']?>]</strong></span>
				<a href="/sales/sales3.php?warehouse=3000">
				 <?php echo cut_str($item['wr_subject'], 50)?>
				
				</a>
				<span class="lt_date">
					<span class="lt_date"><?php echo $row['wr_datetime'] ?></span>              
				</span>
			</li>
		<?php }  ?>
		<?php if ($i == 0) { //게시물이 없을 때  ?>
		<li class="empty_li">등록 된 데이터가 없습니다.</li>
		<?php }  ?>
		</ul>
		<a href="/sales/sales3.php?warehouse=3000" class="lt_more"><span class="sound_only"><?php echo $bo_subject ?></span>더보기</a>

		</div>
    </div>
	
	<?php } else {?>
	
	
	<div class="conle_lt conle_bt_lt">
		<div class="lt_basic">
			<h2><strong><a href="/sales/sales3.php?warehouse=1000">출고등록(한국)</a></strong></h2>
			<ul>
		<?php 
		$sql = "select * from g5_sales3_list where wr_warehouse = '1000' order by seq desc limit 5";
		$rst = sql_query($sql);
		
		for ($i=0; $row=sql_fetch_array($rst); $i++) {  
		
		$item = sql_fetch("select wr_1, wr_subject from g5_write_product where wr_id = '{$row['wr_product_id']}'");
		?>
			<li>
				<span class="lt_writer"><strong>[<?php echo $row['wr_order_num']?>]</strong></span>
				<a href="/sales/sales3.php?warehouse=1000">
				 <?php echo cut_str($item['wr_subject'], 50)?>
				
				</a>
				<span class="lt_date">
					<span class="lt_date"><?php echo $row['wr_datetime'] ?></span>              
				</span>
			</li>
		<?php }  ?>
		<?php if ($i == 0) { //게시물이 없을 때  ?>
		<li class="empty_li">등록 된 데이터가 없습니다.</li>
		<?php }  ?>
		</ul>
		<a href="/sales/sales3.php?warehouse=1000" class="lt_more"><span class="sound_only"><?php echo $bo_subject ?></span>더보기</a>

		</div>
    </div>
	<div class="conle_lt conle_bt_lt">
		<div class="lt_basic">
			<h2><strong><a href="/sales/sales3.php?warehouse=1000">출고등록(미국)</a></strong></h2>
			<ul>
		<?php 
		$sql = "select * from g5_sales3_list where wr_warehouse = '3000' order by seq desc limit 5";
		$rst = sql_query($sql);
		
		for ($i=0; $row=sql_fetch_array($rst); $i++) {  
		
		$item = sql_fetch("select wr_1, wr_subject from g5_write_product where wr_id = '{$row['wr_product_id']}'");
		?>
			<li>
				<span class="lt_writer"><strong>[<?php echo $row['wr_order_num']?>]</strong></span>
				<a href="/sales/sales3.php?warehouse=3000">
				 <?php echo cut_str($item['wr_subject'], 50)?>
				
				</a>
				<span class="lt_date">
					<span class="lt_date"><?php echo $row['wr_datetime'] ?></span>              
				</span>
			</li>
		<?php }  ?>
		<?php if ($i == 0) { //게시물이 없을 때  ?>
		<li class="empty_li">등록 된 데이터가 없습니다.</li>
		<?php }  ?>
		</ul>
		<a href="/sales/sales3.php?warehouse=3000" class="lt_more"><span class="sound_only"><?php echo $bo_subject ?></span>더보기</a>

		</div>
    </div>
	
	<div class="conle_lt">
		<div class="lt_basic">
			<h2><strong><a href="/sales/sales1.php">발주등록</a></strong></h2>
			<ul>
		<?php 
		/*
		$sql = "select a.*, b.wr_subject, b.wr_2, b.wr_32, b.wr_36, b.mb_id from g5_sales1_list a 
					LEFT JOIN g5_write_product b ON(a.wr_code = b.wr_1 OR a.wr_code = b.wr_27 OR a.wr_code = b.wr_28 OR a.wr_code = b.wr_29 OR a.wr_code = b.wr_30 OR a.wr_code = b.wr_31) order by a.seq desc limit 5";
		*/
		$sql = "SELECT * FROM g5_sales1_list ORDER BY seq DESC LIMIT 5";
		$rst = sql_query($sql);
		
		for ($i=0; $row=sql_fetch_array($rst); $i++) {  
			$subRow = sql_fetch("SELECT * FROM g5_write_product WHERE wr_1='".$row['wr_code']."' OR wr_27='".$row['wr_code']."' OR wr_28='".$row['wr_code']."' OR wr_29='".$row['wr_code']."' OR wr_30='".$row['wr_code']."' OR wr_31='".$row['wr_code']."'");
			$row['wr_subject']	= $subRow['wr_subject'];
			$row['wr_2']		= $subRow['wr_2'];
			$row['wr_32']		= $subRow['wr_32'];
			$row['wr_36']		= $subRow['wr_36'];
			$row['mb_id']		= $subRow['mb_id'];
		?>
			<li>
				<span class="lt_writer"><strong><?php echo $row['wr_date2']?></strong></span>
				<a href="/sales/sales1.php">
				 [<?php echo $row['wr_order_num']?>] 
				 <?php echo cut_str($row['wr_subject'], 50)?>
				
				</a>
				<span class="lt_date">
					<span class="lt_date"><?php echo $row['wr_domain']?></span>              
				</span>
			</li>
		<?php }  ?>
		<?php if ($i == 0) { //게시물이 없을 때  ?>
		<li class="empty_li">등록 된 데이터가 없습니다.</li>
		<?php }  ?>
		</ul>
		<a href="/sales/sales1.php" class="lt_more"><span class="sound_only"><?php echo $bo_subject ?></span>더보기</a>

		</div>
    </div>
    
    <div class="conle_lt conle_lt_even">
	   <div class="lt_basic">
				<h2><strong><a href="/sales/sales2.php">입고등록</a></strong></h2>
				<ul>
			<?php 
			$sql = "select * from g5_sales2_list where wr_direct_use = 0 order by seq desc limit 5";
			$rst = sql_query($sql);
			
			for ($i=0; $row=sql_fetch_array($rst); $i++) {  
			
			
			$item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");
			?>
				<li>
				<span class="lt_writer"><strong><?php echo $row['wr_date2']?></strong></span>
				<a href="/sales/sales1.php">
				 [<?php echo $row['wr_order_num']?>] 
				 <?php echo cut_str($item['wr_subject'], 50)?>
				
				</a>
				<span class="lt_date">
					<span class="lt_date"><?php echo $row['wr_domain']?></span>              
				</span>
			</li>
			<?php }  ?>
			<?php if ($i == 0) { //게시물이 없을 때  ?>
			<li class="empty_li">등록 된 데이터가 없습니다.</li>
			<?php }  ?>
			</ul>
			<a href="/sales/sales2.php" class="lt_more"><span class="sound_only"><?php echo $bo_subject ?></span>더보기</a>

			</div>
	</div>
		<?php }?>
</div>
<!-- 메인 최신글 끝 -->

<?php
include_once(G5_THEME_MOBILE_PATH.'/tail.php');
?>