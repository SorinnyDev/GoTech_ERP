<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 선택옵션으로 인해 셀합치기가 가변적으로 변함
$colspan = 2;

if ($is_checkbox) $colspan++;

foreach($list as $i => $v) {
    $list[$i]['wr_reply_style'] = "padding-left:". ($list[$i]['reply'] ? (strlen($list[$i]['wr_reply'])*10) : '0'). "px";
}

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);



?>
<style>
.cnt_left {padding:5px 10px; border-right:1px solid #ddd; word-break: text-overflow: ellipsis; overflow: hidden; white-space: nowrap;}
.list_03 li { padding:0 }
</style>
<!-- 게시판 목록 시작 -->
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit"><?php echo $board['bo_subject']?></h2>
		<form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
		<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
		<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
		<input type="hidden" name="stx" value="<?php echo $stx ?>">
		<input type="hidden" name="spt" value="<?php echo $spt ?>">
		<input type="hidden" name="sst" value="<?php echo $sst ?>">
		<input type="hidden" name="sod" value="<?php echo $sod ?>">
		<input type="hidden" name="page" value="<?php echo $page ?>">
		<input type="hidden" name="sw" value="">
		
		<?php if ($is_category) { ?>
	    <nav id="bo_cate">
	        <h2><?php echo ($board['bo_mobile_subject'] ? $board['bo_mobile_subject'] : $board['bo_subject']) ?> 카테고리</h2>
	        <ul id="bo_cate_ul">
	            <?php echo $category_option ?>
	        </ul>
	    </nav>
	    <?php } ?>
    
	    <div id="bo_li_top_op">
		    <div class="bo_list_total">
			   <span> <button type="submit" name="btn_submit" value="선택삭제" class="btn02" onclick="document.pressed=this.value">선택삭제</button></span>
               <? if($member['mb_id']=="admin"){ ?>
			   <span> <button type="submit" name="btn_submit" value="완전삭제" class="btn02" style="background:#ff4081;border:1px solid #ff4081;" onclick="document.pressed=this.value">완전삭제</button></span>
                <? } ?>
            </div>		
			<?php if ($rss_href || $write_href) { ?> 
			<ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2';?>">
				<?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
			    <!-- <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b01">매출등록</a></li><?php } ?>
				<li><button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button></li> -->
				
			    <?php if ($is_admin == 'super' || $is_auth) {  ?>
				<li>
					<!--<button type="button" class="btn_more btn_b04"><span class="sound_only">글쓰기 옵션 더보기</span><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>-->
					<?php if ($list_href || $is_checkbox || $write_href) { ?>
			        <ul class="btn_bo_adm">
			        	<?php if ($list_href) { ?>
				        <li><a href="<?php echo $list_href ?>" class="btn_b01 btn">목록</a></li>
				        <?php } ?>
			            <li><button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value">선택삭제</button></li>
			         
			        </ul>
			        <?php } ?>
				    <script>
						$(document).ready(function(){
							$(".btn_more").click(function(){
								$(".btn_bo_adm").toggle();
							});
						});
					</script>
				</li>
			    <?php } ?>
			</ul>
			<?php } ?>
		</div>
	    <div id="bo_li_01" style="overflow:scroll; max-height:700px;">
	    	<ul class="list_head" style="width:1500px;position:sticky;top:0; background:#fff;z-index:3" >
	    		<?php if ($is_checkbox) { ?>
	            <li class="sel">
	                <?php if ($is_checkbox) { ?>
				    <div class="list_chk all_chk">
				        <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
				        <label for="chkall"><span class="sound_only">현재 페이지 게시물 </span>전체선택</label>
				    </div>
				    <?php } ?>
	            </li>
	            <?php } ?>
	            <li style="width:100px"><?php echo subject_sort_link('wr_hit', $qstr2, 1) ?>순번</a></li>
	            <li style="width:200px">주문번호</li>
            	<li style="width:100px">매출일자</li>
            	<li style="width:100px">반품일자</li>
            	<li style="width:250px">SKU</li>
            	<li style="width:250px">상품명</li>
            	<li style="width:100px">대표코드</li>
	        </ul>
	        
	        <div id="bo_li_01" class="list_03">
		        <ul style="width:1500px">
		            <?php for ($i=0; $i<count($list); $i++) { ?>
		            <li class="<?php if ($list[$i]['is_notice']) echo "bo_notice"; ?>">
		            	
		                <?php if ($is_checkbox) { // 게시글별 체크박스?>
		                <span class="sel bo_chk li_chk">
		                	<label for="chk_wr_id_<?php echo $i ?>"><span class="sound_only"><?php echo $list[$i]['subject'] ?></span></label>
		                    <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>"> 	
		                </span>
		                <?php } ?>
		                
		                <div class="num cnt_left" style="width:100px"><?php echo $list[$i]['num'] ?></div>
		                <div class="cnt_left" style="width:200px"><?php echo $list[$i]['wr_subject'] ?></div>
		                <div class="cnt_left" style="width:100px"><?php echo $list[$i]['wr_date1'] ?></div>
		                <div class="cnt_left" style="width:100px"><?php echo $list[$i]['wr_date2'] ?></div>
		                <div class="cnt_left" style="width:250px"><?php echo $list[$i]['wr_1'] ?></div>
		                <div class="cnt_left" style="width:250px"><?php echo $list[$i]['wr_2'] ?></div>
		                <div class="cnt_left" style="width:100px"><?php echo $list[$i]['wr_3'] ?></div>
		                
		                
				    </li>
				    <?php } ?>
		            <?php if (count($list) == 0) { echo '<li class="empty_table">게시물이 없습니다.</li>'; } ?>
		        </ul>
		    </div>
	    </div>
		</form>
	</div>
	<div id="bo_li_op">
		<!-- 게시판 검색 시작 { -->
		<fieldset id="bo_sch">
		    <legend>게시물 검색</legend>
		    <form name="fsearch" method="get">
		    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
		    <input type="hidden" name="sca" value="<?php echo $sca ?>">
		    <input type="hidden" name="sop" value="and">
		    <label for="sfl" class="sound_only">검색대상</label>
		    <select name="sfl" id="sfl">
		        <option value="wr_subject" <?php echo get_selected($sfl, 'wr_subject', true)?>>제품명</option>
		        <option value="wr_1" <?php echo get_selected($sfl, 'wr_1')?>>SKU</option>
		        <option value="wr_3" <?php echo get_selected($sfl, 'wr_3')?>>수출신고 명칭</option>
		        <option value="wr_5||wr_6" <?php echo get_selected($sfl, 'wr_3||wr_6')?>>대표코드</option>
		        <option value="wr_4" <?php echo get_selected($sfl, 'wr_4')?>>바코드</option>
		    </select>
		    <input name="stx" value="<?php echo stripslashes($stx) ?>" placeholder="검색어를 입력하세요" required id="stx" class="sch_input" size="15" maxlength="20">
		    <button type="submit" value="검색" class="sch_btn"><i class="fa fa-search" aria-hidden="true"></i> <span class="sound_only">검색</span></button>
		    </form>
		</fieldset>
		
		<!-- } 게시판 검색 끝 -->
		<?php if ($rss_href || $write_href) { ?>		
		<ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2';?>">
			<?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
			<?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b01">반품등록</a></li><?php } ?>
			
		    <?php if ($is_admin == 'super' || $is_auth) {  ?>
			<li>
				
				<button type="button" class="btn_more2 btn_b04"><span class="sound_only">글쓰기 옵션 더보기</span><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
				<?php if ($list_href || $is_checkbox || $write_href) { ?>
		        <ul class="btn_bo_adm2">
		        	<?php if ($list_href) { ?>
			        <li><a href="<?php echo $list_href ?>" class="btn_b01 btn">목록</a></li>
			        <?php } ?>
		            <li><button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value">선택삭제</button></li>
		            <li><button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value">선택복사</button></li>
		            <li><button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value">선택이동</button></li>
		        </ul>
		        <?php } ?>
			    <script>
					$(document).ready(function(){
						$(".btn_more2").click(function(){
							$(".btn_bo_adm2").toggle();
						});
					});
				</script>
			</li>
		    <?php } ?>
		</ul>
		<?php } ?>		
	</div>
</div>

<div class="bo_sch_wrap">
	<fieldset class="bo_sch">
		<h3>검색</h3>
		<form name="fsearch" method="get">
		<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
		    <input type="hidden" name="sca" value="<?php echo $sca ?>">
		    <input type="hidden" name="sop" value="and">
		    <input type="hidden" name="sfl" value="wr_subject||wr_2||wr_17">
		<?php 
		if(!$date1) $date1 = G5_TIME_YMD;
		if(!$date2) $date2 = G5_TIME_YMD;
		?>
		
		<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
			<input type="text" name="stx" value="<?php echo urldecode($_GET['stx'])?>" class="frm_input" style="width:100%;" placeholder="검색어 입력">
		</div>
		
		
		<button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
		<button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?php echo $_SERVER['REQUEST_URI']?>'"><i class="fa fa-repeat" aria-hidden="true"></i> 검색초기화</button>
		<button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
		</form>
	</fieldset>
	<div class="bo_sch_bg"></div>
</div>
<script>
jQuery(function($){
	// 게시판 검색
	$(".btn_bo_sch").on("click", function() {
		$(".bo_sch_wrap").toggle();
	})
	$('.bo_sch_bg, .bo_sch_cls').click(function(){
		$('.bo_sch_wrap').hide();
	});
});
</script>
<?php if($is_checkbox) { ?>
<noscript>
<p>자바스크립트를 사용하지 않는 경우<br>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>
<?php } ?>

<!-- 페이지 -->
<?php echo $write_pages; ?>

<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function fboardlist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택복사") {
        select_copy("copy");
        return;
    }

    if(document.pressed == "선택이동") {
        select_copy("move");
        return;
    }

    if(document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
            return false;

        f.removeAttribute("target");
        f.action = g5_bbs_url+"/board_list_update.php";
    }

	if(document.pressed == "완전삭제") {
        if (!confirm("선택한 데이터를 정말 삭제하시겠습니까?\n\n[경고] 한번 삭제한 자료는 복구할 수 없습니다.\n\n이전 과정까지 삭제됩니다."))
            return false;

        f.removeAttribute("target");
        f.action = g5_bbs_url+"/board_list_update.php";
    }

    return true;
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
    var f = document.fboardlist;

    if (sw == 'copy')
        str = "복사";
    else
        str = "이동";

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.sw.value = sw;
    f.target = "move";
    f.action = g5_bbs_url+"/move.php";
    f.submit();
}

function pop_excel(){
	
	window.open("/bbs/pop_sales_add.php", "add", "left=50, top=50, width=500, height=550, scrollbars=1");
}
</script>
<?php } ?>
<!-- 게시판 목록 끝 -->
