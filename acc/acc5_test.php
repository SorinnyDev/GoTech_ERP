<?php 
include_once('./_common.php');
include_once(G5_THEME_PATH.'/head.php');


$rowspan1 = " rowspan='1' ";
$rowspan2 = " rowspan='2' ";

?>
<link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
<style>
.cnt_left {padding:5px 10px; border-right:1px solid #ddd; word-break: text-overflow: ellipsis; overflow: hidden; white-space: nowrap;}
.list_03 li { padding:0 }
.list_03 li .cnt_left { line-height:1.5em }
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
a.btn_ov02,a.ov_listall{display:inline-block;line-height:30px;height:30px;font-size:0.92em;background:#565e8c;color:#fff;vertical-align:top;border-radius:5px;padding:0 7px }
a.btn_ov02:hover,a.ov_listall:hover{background:#3f51b5}
.tbl_head01 thead th, .tbl_head01 tbody td { border-right:1px solid #e9e9e9 !important }
.tbl_head01 thead th { background:#f2f2f2; font-weight:bold }
.tbl_head01 tbody td { padding:10px 5px; color:#222 }
.tbl_head01 tbody td.num { text-align:right }
.tbl_head01 tbody td.date { text-align:center }
.text-center{text-align:center;}
.text-right{text-align:right;}
.text-left{text-align:left;}
#content-wrapper{overflow-x:unset;}
.sticky-th{position:sticky; top: 0px;}
.tbl_head01 tbody td{background:#FFF;text-align:center;}
.tbl_head01 tbody .odd{background:#eff3f9}
</style>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">손익자료</h2>
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
                    <span class="btn_ov01">
                    &nbsp;
                    </span>
                </div>
                
                </div>		
                <ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2';?>">
                    <?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
					<li><a href="javascript:;" onclick="fnExcelDown();" class="btn_b01"><i class="fa fa-search" aria-hidden="true"></i>EXCEL</a></li>
                    <li><button type="button" class="btn_b01 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i>검색</button></li>
                </ul>
            </div>
            <h2 style="padding-bottom:10px; font-size:20px; text-align:center">손익자료</h2>
			<div class="tbl_head01 tbl_wrap" style="overflow-x:scroll;height:600px;">
				<table style="width:3500px;position:sticky;top:0;background:#fff;">
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
							<th class="sticky-th" style="width:100px;">환율</th>
							<th class="sticky-th" style="width:100px;">매출단가</th>
							<th class="sticky-th" style="width:100px;">신고가격</th>
							<th class="sticky-th" style="width:100px;">매출 * 환율(원)</th>
							<th class="sticky-th" style="width:100px;">TAX</th>
							<th class="sticky-th" style="width:100px;">손익(원)</th>
							<th class="sticky-th" style="width:100px;">이익률(%)</th>
							<th class="sticky-th" style="width:100px;">부가세환급(원)</th>
						</tr>
					</thead>
					<tbody  id="ajax_list" style="overflow-y:scroll;">
					</tbody>
				</table>
			</div>
        </form>
    </div>
</div>
	


<div class="bo_sch_wrap">
	<fieldset class="bo_sch" style="padding:10px">
		<h3>검색</h3>
		<form name="fsearch" method="get" onsubmit="return false;">
		<select name="wr_18" style="margin-bottom:15px">
			<option value="">전체</option>
			<?php echo get_domain_option($_GET['wr_18'])?>
		</select>
		<!--
		<select name="wmb_id" id="wmb_id" class="frm_input search_sel">
			<option value="">담당자 전체</option>
			<?php 
			$mbSql = " select mb_id, mb_name from g5_member order by mb_name asc";
			$mbRst = sql_query($mbSql);
			for($i=0; $mb=sql_fetch_array($mbRst); $i++) {
			?>
			<option value="<?php echo $mb['mb_id']?>" <?php echo get_selected($mb['mb_id'], $wmb_id)?>><?php echo $mb['mb_name']?>(<?php echo $mb['mb_id']?>)</option>
			<?php }?>
		</select>
		-->
		
		<label for="stx" style="font-weight:bold">일자 조회<strong class="sound_only"> 필수</strong></label>
		<div class="sch_bar" style="margin-top:3px">
			
			<input type="date" name="st_date" value="<?php echo $st_date ?>" required  class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
			<span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
			<input type="date" name="ed_date" value="<?php echo stripslashes($ed_date) ?>" required  class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
			
		</div>
		
		<button type="button" value="검색" class="btn_b01" style="width:49%;margin-top:15px" onclick="fnLoadList();"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
		<button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?php echo $_SERVER['PHP_SELF']?>'"><i class="fa fa-repeat" aria-hidden="true"></i> 검색초기화</button>
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

// 손익보고서 리스트 불러오기
function fnLoadList(){
	var params = $("form[name='fsearch']").serialize();
	$.post("./ajax.acc5_list_test.php",params,function(data){
		$("#ajax_list").html(data);
		$('.bo_sch_wrap').hide();
	});
}

function fnExcelDown(){
	var params = $("form[name='fsearch']").serialize();
	document.location.href="./acc5_list_excel_test.php?"+params;
}

$(function() {

	
	
	
});

</script>



<?php 
include_once(G5_THEME_PATH.'/tail.php');