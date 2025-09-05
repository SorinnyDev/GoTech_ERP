<?php 
include_once('../common.php');

include_once(G5_THEME_PATH.'/head.php');


$sql_common = " from g5_write_product ";
$sql_search = " where (1) ";
$sql_add = "";

if($stx2) {
    $sql_search .= " AND (wr_subject LIKE '%$stx2%' or wr_1 LIKE '%{$stx2}%' or wr_27 LIKE '%{$stx2}%' or wr_28 LIKE '%{$stx2}%' or wr_29 LIKE '%{$stx2}%' or wr_30 LIKE '%{$stx2}%' or wr_31 LIKE '%{$stx2}%' or wr_5 LIKE '%{$stx2}%' or wr_6 LIKE '%{$stx2}%' or wr_4 LIKE '%{$stx2}%' )  ";
    $sql_add .= " AND (wr_subject LIKE '%$stx2%' or wr_1 LIKE '%{$stx2}%' or wr_27 LIKE '%{$stx2}%' or wr_28 LIKE '%{$stx2}%' or wr_29 LIKE '%{$stx2}%' or wr_30 LIKE '%{$stx2}%' or wr_31 LIKE '%{$stx2}%' or wr_5 LIKE '%{$stx2}%' or wr_6 LIKE '%{$stx2}%' or wr_4 LIKE '%{$stx2}%' )  ";

}

if($report_type){
    $sql_search .= " AND {$report_type} != 0 ";
}

if($report_mb_id){
    $sql_search .= "  AND mb_id = '{$report_mb_id}' ";
    $sql_add .= " AND mb_id = '{$report_mb_id}'  ";
}

if($report_category){
    $sql_search .= "  AND wr_26 = '{$report_category}' ";
    $sql_add .= " AND wr_26 = '{$report_category}'  ";
}

if (!$sst) {
    $sst  = "wr_id";
    $sod = "desc";
}

if($sst == "stock")
    $sst = "(wr_32+wr_36+wr_37+wr_42+wr_43+wr_44)";

$sql_order = " order by $sst $sod ";
$sql = " SELECT count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 50;
$total_page  = ceil($total_count / $rows);  
if ($page < 1) {
    $page = 1; 
}
$from_record = ($page - 1) * $rows; 

$cur_no = $total_count - $from_record;


$sql = " SELECT * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$rst = sql_query($sql);

$cnt = sql_fetch("SELECT SUM(wr_32) as kor, SUM(wr_36) as usa, SUM(wr_37) as tmp, SUM(wr_42) as fba , SUM(wr_43) as wfba , SUM(wr_44) as ufba  from g5_write_product WHERE (1) {$sql_add} ");
?>
<link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
<style>
.cnt_left {padding:5px 10px; border-right:1px solid #ddd; word-break: text-overflow: ellipsis; overflow: hidden; white-space: nowrap;}
.list_03 li { padding:0 }
.list_03 li .cnt_left { line-height:43px }
.modify { cursor:pointer}
.tbl_frm01 th {background:#6f809a;color:#fff;border:1px solid #60718b;font-weight:normal;text-align:center;padding:8px 5px;font-size:0.92em}
.tbl_frm01 td  { border-bottom:1px solid #ddd; }
.tbl_frm01 td input { border:1px solid #ddd; padding:3px; width:100%}
.tbl_frm01 input.readonly { background:#f2f2f2}

.local_ov01 {position:relative;margin: 10px 0;}
.local_ov01 .ov_a{display:inline-block;line-height:30px;height:30px;font-size:0.92em;background:#ff4081;color:#fff;vertical-align:top;border-radius:5px;padding:0 7px}
.local_ov01 .ov_a:hover{background:#ff1464}
.btn_ov01{display:inline-block;line-height:30px;height:30px;font-size:0.92em;vertical-align:top}
.btn_ov01:after{display:block;visibility:hidden;clear :both;content:""}
.btn_ov01 .ov_txt{float:left;background:#9eacc6;color:#fff;border-radius:5px 0 0 5px;padding:0 5px}
.btn_ov01 .ov_num{float:left;background:#ededed;color:#666;border-radius:0 5px 5px 0;padding:0 5px}
.ov_txt.select_btn{background:blue;}
a.btn_ov02,a.ov_listall{display:inline-block;line-height:30px;height:30px;font-size:0.92em;background:#565e8c;color:#fff;vertical-align:top;border-radius:5px;padding:0 7px }
a.btn_ov02:hover,a.ov_listall:hover{background:#3f51b5}
@media (max-width:767px){
	.btn_top2 { margin-bottom:5px }
	.stock_list_mtb { margin: 15px 0; font-size:13px }
	.stock_list_mtb th { width:60px}
	.stock_list_mtb td{ padding:10px }
	.stock_list_mtb .stock_table td { width:33.333%;text-align:center; border-right:1px solid #ddd }
}
#container{margin:0}
#content-wrapper{margin-left:0}
</style>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit"><?=get_member($mb_id)['mb_name']?> 실사재고조사</h2>
		<form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
		<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
		<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
		<input type="hidden" name="stx" value="<?php echo $stx2 ?>">
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
				<div class="local_ov01 local_ov">
                    <span class="btn_ov01" onclick="location.href='<?=G5_URL?>/report/report_mb.php?sst=<?=$_GET['sst']?>&sod=<?=$_GET['sod']?>&report_type=&report_category=<?=$report_category?>&report_mb_id=<?=$report_mb_id?>&stx2=<?=$stx2?>'">
                        <span class="ov_txt <?=$report_type=="" ? "select_btn" : "" ?>">전체</span>
                        <span class="ov_num"><?php echo number_format($cnt['kor']+$cnt['usa']+$cnt['fba']+$cnt['wfba']+$cnt['ufba']+$cnt['tmp']) ?>개</span>
                    </span>
                    <span class="btn_ov01" onclick="location.href='<?=G5_URL?>/report/report_mb.php?sst=<?=$_GET['sst']?>&sod=<?=$_GET['sod']?>&report_type=wr_32&report_category=<?=$report_category?>&report_mb_id=<?=$report_mb_id?>&stx2=<?=$stx2?>'">
                        <span class="ov_txt <?=$report_type=="wr_32" ? "select_btn" : "" ?>">한국창고</span>
                        <span class="ov_num"><?php echo number_format($cnt['kor']) ?>개</span>
                    </span>
                    <span class="btn_ov01" onclick="location.href='<?=G5_URL?>/report/report_mb.php?sst=<?=$_GET['sst']?>&sod=<?=$_GET['sod']?>&report_type=wr_36&report_category=<?=$report_category?>&report_mb_id=<?=$report_mb_id?>&stx2=<?=$stx2?>'">
                        <span class="ov_txt <?=$report_type=="wr_36" ? "select_btn" : "" ?>">미국창고</span>
                        <span class="ov_num"><?php echo number_format($cnt['usa']) ?>개</span>
                    </span>
                    <span class="btn_ov01" onclick="location.href='<?=G5_URL?>/report/report_mb.php?sst=<?=$_GET['sst']?>&sod=<?=$_GET['sod']?>&report_type=wr_42&report_category=<?=$report_category?>&report_mb_id=<?=$report_mb_id?>&stx2=<?=$stx2?>'">
                        <span class="ov_txt <?=$report_type=="wr_42" ? "select_btn" : "" ?>">FBA창고</span>
                        <span class="ov_num"><?php echo number_format($cnt['fba']) ?>개</span>
                    </span>
                    <span class="btn_ov01" onclick="location.href='<?=G5_URL?>/report/report_mb.php?sst=<?=$_GET['sst']?>&sod=<?=$_GET['sod']?>&report_type=wr_43&report_category=<?=$report_category?>&report_mb_id=<?=$report_mb_id?>&stx2=<?=$stx2?>'">
                        <span class="ov_txt <?=$report_type=="wr_43" ? "select_btn" : "" ?>">W-FBA창고</span>
                        <span class="ov_num"><?php echo number_format($cnt['wfba']) ?>개</span>
                    </span>
                    <span class="btn_ov01" onclick="location.href='<?=G5_URL?>/report/report_mb.php?sst=<?=$_GET['sst']?>&sod=<?=$_GET['sod']?>&report_type=wr_44&report_category=<?=$report_category?>&report_mb_id=<?=$report_mb_id?>&stx2=<?=$stx2?>'">
                        <span class="ov_txt <?=$report_type=="wr_44" ? "select_btn" : "" ?>">U-FBA창고</span>
                        <span class="ov_num"><?php echo number_format($cnt['ufba']) ?>개</span>
                    </span>
                    <span class="btn_ov01" onclick="location.href='<?=G5_URL?>/report/report_mb.php?sst=<?=$_GET['sst']?>&sod=<?=$_GET['sod']?>&report_type=wr_37&report_category=<?=$report_category?>&report_mb_id=<?=$report_mb_id?>&stx2=<?=$stx2?>'">
                        <span class="ov_txt <?=$report_type=="wr_37" ? "select_btn" : "" ?>">임시창고</span>
                        <span class="ov_num"><?php echo number_format($cnt['tmp']) ?>개</span>
                    </span>
			    </div>
            </div> 

			<ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2';?>">
				<?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
			   
			    <li>
					<select id="sorting_box" class="frm_input" style="height:37px">
						<option value="default" <?php if($_GET['sst']=="wr_id") echo 'selected'; ?>>기본정렬</option>
						<option value="up" <?php if($_GET['sst'] == "stock" && $sod == "desc" ) echo 'selected'; ?>>재고많은순</option>
						<option value="down" <?php if($_GET['sst'] == "stock" && $sod == "asc") echo 'selected'; ?>>재고적은순</option>
					</select>
				</li>
                
			    <li>
                    <select id="category_box" class="frm_input" style="height:37px">
                        <option value="">전체 카테고리</option>
                    <?
                       $arr = get_code_list('2');
                       foreach($arr as $key => $value){
                        $selected = ($value['idx']==$report_category) ? "selected" : "";
                        echo "<option value='{$value['idx']}' {$selected} >{$value['code_name']}</option>"; 
                        }?>
                    </select>
				</li>

			    <li><button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button></li>
			    <li><button type="button" class="btn_b01 " id="excel_btn" style="background:#325422;">엑셀출력</button></li>
			</ul>
		</div>
	    <div id="bo_li_01" style="clear:both;overflow-x:scroll;overflow-y:hidden">
			<?php if(!is_mobile()){ //PC일때만 표시?>
	    	<ul class="list_head" style="width:100%;min-width:1576px;position:sticky;top:0;background:#fff;z-index:2;" >
	            <li style="width:100px">순번</li>
	            <li style="width:150px"><?php echo subject_sort_link('wr_5', $qstr2, 1) ?>대표코드</a></li>
	            <li style="width:150px"><?php echo subject_sort_link('wr_1', $qstr2, 1) ?>SKU</a></li>
            	<li style="width:100px"><?php echo subject_sort_link('wr_subject', $qstr2, 1) ?>상품명</a></li>
                <li style="width:200px"><?php echo subject_sort_link('wr_rack', $qstr2, 1) ?>랙번호</a></li>
            	<li style="width:100px"><?php echo subject_sort_link('wr_32', $qstr2, 1) ?>한국창고</a></li>
            	<li style="width:100px"><?php echo subject_sort_link('wr_36', $qstr2, 1) ?>미국창고</a></li>

            	<li style="width:100px"><?php echo subject_sort_link('wr_42', $qstr2, 1) ?>FBA창고</a></li>
            	<li style="width:100px"><?php echo subject_sort_link('wr_43', $qstr2, 1) ?>W-FBA창고</a></li>
            	<li style="width:100px"><?php echo subject_sort_link('wr_44', $qstr2, 1) ?>U-FBA창고</a></li>
            	<li style="width:100px"><?php echo subject_sort_link('wr_37', $qstr2, 1) ?>임시창고</a></li>
            	<li style="width:120px">총 재고량</li>
                <li style="width:120px">관리</li>
	        </ul>
			<?php }?>
	        <div id="bo_li_01" class="list_03" >
		        <ul style="width:100%;">
		            <?php 
					
					for ($i=0; $row=sql_fetch_array($rst); $i++) {
                        
                        $sql = "SELECT * FROM g5_stock_research WHERE mb_id = '{$mb_id}' AND wr_id = '{$row['wr_id']}' ";
						$obj = sql_fetch($sql);
						
                        $hab_cnt = $row['wr_32'] + $row['wr_36'] + $row['wr_42'] + $row['wr_43'] + $row['wr_44'] + $row['wr_37'];
						$hab_report_cnt = $obj['wr_32'] + $obj['wr_36'] + $obj['wr_42'] + $obj['wr_43'] + $obj['wr_44'] + $obj['wr_37']

					?>
					
		            <li class="modify" data="<?php echo $row['seq']?>">
		                <div class="num cnt_left" style="width:100px"><?php echo $cur_no  ?></div>
		                <div class="cnt_left" style="width:150px"><?php echo $row['wr_5'] ?> <a href="/bbs/write.php?bo_table=product&w=u&wr_id=<?php echo $row['wr_id']?>" target="_blank" title="제품관리 바로가기"><i class="fa fa-link" aria-hidden="true"></i></a></div>
		                <div class="cnt_left" style="width:150px" title="<?php echo $row['wr_1']?>"><?php echo $row['wr_1'] ?></div>
		                <div class="cnt_left" style="width:100px;" title="<?php echo $row['wr_subject'] ?>"><?php echo $row['wr_subject'] ?></div>

		                <div class="cnt_left" style="width:200px;text-align:right">
							<?=rack_search($row['wr_id'])?>
						</div>

		                <div class="cnt_left" style="width:100px;text-align:right">
							<input type="text" name="wr_32" class="wr_32 frm_input" style="width:100%;text-align:right" value="<?php echo $obj['wr_32']?>"><br>
							(재고 : <?=number_format($row['wr_32'])?>개)
						</div>

						<div class="cnt_left" style="width:100px;text-align:right">
						 	<input type="text" name="wr_36" class="wr_36 frm_input" style="width:100%;text-align:right" value="<?php echo $obj['wr_36']?>"><br>
							(재고 : <?=number_format($row['wr_36'])?>개)
						</div>

						<div class="cnt_left" style="width:100px;text-align:right">
						 	<input type="text" name="wr_42" class="wr_42 frm_input" style="width:100%;text-align:right" value="<?php echo $obj['wr_42']?>"><br>
							(재고 : <?=number_format($row['wr_42'])?>개)
						</div>

						<div class="cnt_left" style="width:100px;text-align:right">
						 <input type="text" name="wr_43" class="wr_43 frm_input" style="width:100%;text-align:right" value="<?php echo $obj['wr_43']?>"><br>
							(재고 : <?=number_format($row['wr_43'])?>개)
						</div>

						<div class="cnt_left" style="width:100px;text-align:right">
						 	<input type="text" name="wr_44" class="wr_44 frm_input" style="width:100%;text-align:right" value="<?php echo $obj['wr_44']?>"><br>
							(재고 : <?=number_format($row['wr_44'])?>개)
						</div>

                        <div class="cnt_left" style="width:100px;text-align:right">
						 	<input type="text" name="wr_37" class="wr_37 frm_input" style="width:100%;text-align:right" value="<?php echo $obj['wr_37']?>"><br>
							(재고 : <?=number_format($row['wr_37'])?>개)
						</div>

                        <div class="cnt_left" style="width:120px;text-align:right">
                            <input type="text" class="frm_input" id="hab_report_cnt<?=$row['wr_id']?>" style="width:100%;text-align:right" value="<?=$hab_report_cnt?>" readonly /><br>
                            (재고 총 합 : <?=number_format($hab_cnt)?>개)
                        </div>
						
						 <div class="cnt_left" style="width:120px;text-align:center">
							<button type="button" class="btn btn_b01 save_btn" data="<?php echo $obj['idx']?>" data2="<?php echo $row['wr_id']?>" >변경하기</button>
						</div>
				    </li>
				    <?php 
					
					$cur_no = $cur_no - 1;
					} ?>
		            <?php if (sql_num_rows($rst) == 0) { echo '<li class="empty_table">내역이 없습니다.</li>'; } ?>
		        </ul>
		    </div>
	    </div>
		
		</form>
	
	</div>
	
<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?' . $qstr . '&amp;page='); ?>
</div>

<div class="bo_sch_wrap">
	<fieldset class="bo_sch">
		<h3>검색</h3>
		<form name="fsearch" method="get">
			<input type="hidden" name="sst" value="<?=$_GET['sst']?>" />
			<input type="hidden" name="sod" value="<?=$_GET['sod']?>" />
			<input type="hidden" name="report_type" value="<?=$report_type?>" />
			<input type="hidden" name="report_category" value="<?=$report_category?>" />
			<input type="hidden" name="report_mb_id" value="<?=$report_mb_id?>" />
			<div class="sch_bar" style="margin-top:3px">	
				<input type="text" name="stx2" value="<?php echo stripslashes($stx2) ?>"  id="stx" class="sch_input" size="25" maxlength="255" placeholder="대표코드/SKU/상품명으로 검색">
				<button type="submit" value="검색" class="sch_btn"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only">검색</span></button>
			</div>
			<button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
		</form>
	</fieldset>
	<div class="bo_sch_bg"></div>
</div>
<script>

$("#header,#sidedrawer").css("display","none");

$(function() {
    // 게시판 검색
	$(".btn_bo_sch").on("click", function() {
		$(".bo_sch_wrap").toggle();
	});
	$('.bo_sch_bg, .bo_sch_cls').click(function(){
		$('.bo_sch_wrap').hide();
	});

	$('#sorting_box').bind('change', function(){
		
		let sort = $(this).val();
		
		if(sort == "default") {
			location.href = '?sst=wr_id&sod=desc&stx2=<?php echo $stx2?>&report_type=<?=$report_type?>&report_category=<?=$report_category?>&report_mb_id=<?=$report_mb_id?>';
		} else if(sort == "up") {
			location.href = '?sst=stock&sod=desc&stx2=<?php echo $stx2?>&report_type=<?=$report_type?>&report_category=<?=$report_category?>&report_mb_id=<?=$report_mb_id?>';
		} else if(sort == "down") {
			location.href = '?sst=stock&sod=asc&stx2=<?php echo $stx2?>&report_type=<?=$report_type?>&report_category=<?=$report_category?>&report_mb_id=<?=$report_mb_id?>';
		}
	});

    $('#category_box').bind('change', function(){
		
		let val = $(this).val();

        location.href = `?sst=<?=$sst?>&sod=<?=$sod?>&stx2=<?=$stx2?>&report_type=<?=$report_type?>&report_category=${val}`;
		
	});
	
	$('.save_btn').bind('click', function() {
		
		let idx = $(this).attr('data');
		let wr_id = $(this).attr('data2');
		let wr_32 = $(this).closest('li').find('.wr_32').val();
		let wr_36 = $(this).closest('li').find('.wr_36').val();
		let wr_37 = $(this).closest('li').find('.wr_37').val();
		let wr_42 = $(this).closest('li').find('.wr_42').val();
		let wr_43 = $(this).closest('li').find('.wr_43').val();
		let wr_44 = $(this).closest('li').find('.wr_44').val();
		
        const data = { 
            idx : idx, 
			wr_id : wr_id,
            wr_32 : wr_32, 
            wr_36 : wr_36, 
            wr_37 : wr_37, 
            wr_42 : wr_42, 
            wr_43 : wr_43, 
            wr_44 : wr_44
         };

        const obj = HttpJson('./report_mb_update.php',"post", data);
		console.log(obj);

        if(obj['result']){
            alert("실사재고수량이 변경되었습니다.");
            $("#hab_report_cnt"+wr_id).val(obj['total']);
        }else{
            alert("실행에 실패하였습니다.");
        }
		
	});
	
    $("#excel_btn").bind("click",function(){
        
        
        if(!confirm("엑셀 출력을 하시겠습니까?")){
            return false;
        }

        location.href = g5_url+`/report/report_excel2.php?sst=<?=$_GET['sst']?>&sod=<?=$_GET['sod']?>&stx2=<?=$stx2?>&report_type=<?=$report_type?>&report_category=<?=$report_category?>&report_mb_id=<?=$report_mb_id?>`;
    });


});

</script>


<?php 
include_once(G5_THEME_PATH.'/tail.php');