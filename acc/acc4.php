<?php 
include_once('./_common.php');

include_once(G5_THEME_PATH.'/head.php');


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
.tbl_head01 tbody tr:nth-child(even) td{background:#eff3f9}
.text-center{text-align:center;}
.text-right{text-align:right;}
.text-left{text-align:left;}
</style>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">출고대장</h2>
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

                    <li><button type="button" class="btn_b01 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i>검색</button></li>
                    
                </ul>
            </div>
            <h2 style="padding-bottom:10px; font-size:20px; text-align:center">출고대장</h2>
            <div class="tbl_head01 tbl_wrap" >
                <table>
                    <thead style="position:sticky;top:0;">
                        <tr>
                            <th style="width:100px;">일자</th>
                            <th>매출처</th>
                            <th style="width:100px;">주문번호</th>
                            <th style="width:200px">대표코드</th>
                            <th style="width:200px">SKU</th>
                            <th>상품명</th>
                            <th style="width:100px">수량</th>
                            <th style="width:100px">단가($)</th>
                            <th style="width:100px">신고가격($)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    if($st_date && $ed_date){
                        $search_sql = "";

                                                
                        if($wr_18)
                            $search_sql .= " AND wr_domain = '{$wr_18}' ";

                        $sql = "SELECT * FROM g5_sales3_list WHERE wr_release_use = '1' {$search_sql} AND wr_date4 BETWEEN '{$st_date}' AND '{$ed_date}' ORDER BY wr_date4 ASC";
                        $rs = sql_query($sql);
                        $danga_sum = 0;
                        $singo_sum = 0;
                        for($i=0; $row=sql_fetch_array($rs); $i++) {
                            $item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");
                            $danga_sum += $row['wr_danga'];
                            $singo_sum += $row['wr_singo'];
                    
                    ?>
                        <tr>
                            <td class="date"><?php echo $row['wr_date4']?></td>
                            <td class="text-center"><?=$row['wr_domain']?></td>
                            <td class="text-center"><?=$row['wr_order_num']?></td>
                            <td class="text-center"><?php echo $item['wr_5']?></td>
                            <td class="text-center"><?php echo $row['wr_code']?></td>
                            <td><?php echo $item['wr_subject']?></td>
                            <td class="num"><?php echo $row['wr_ea']?>개</td>
                            <td class="num"><?php echo $row['wr_danga']?>$</td>
                            <td class="num"><?php echo $row['wr_singo']?>$</td>
                        </tr>

                        <? if($i==(sql_num_rows($rs)-1)){ ?>
                            <tr>
                                <td colspan="7" class="text-center">합계</td>
                                <td style="text-align:right;"><?=number_format($danga_sum,2)?>$</td>
                                <td class="num"><?=number_format($singo_sum,2)?>$</td>
                            </tr>
                               

                        <? } ?>    
                        
                    <?php 
                            
                        
                        }

                          
                    }

                    if(sql_num_rows($rs)==0){
                        echo "<tr><td colspan='12' style='text-align:center;'>검색된 목록이 없습니다.</td></tr>";
                    }  
                    
                    
                    ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
	


<div class="bo_sch_wrap">
	<fieldset class="bo_sch" style="padding:10px">
		<h3>검색</h3>
		<form name="fsearch" method="get" >
		<select name="wr_18" style="margin-bottom:15px">
			<option value="">전체 매출처</option>
            <?
               $arr = get_code_list('4');
               foreach($arr as $key => $value){
            ?>
                <option value="<?=$value['code_value']?>" <?=($wr_18==$value['code_value'] ? "selected" : "")?> ><?=$value['code_name']?></option>
            <? } ?>
		</select>
		
		
		<label for="stx" style="font-weight:bold">일자 조회<strong class="sound_only"> 필수</strong></label>
		<div class="sch_bar" style="margin-top:3px">
			
			<input type="date" name="st_date" value="<?php echo $st_date ?>" required  class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
			<span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
			<input type="date" name="ed_date" value="<?php echo stripslashes($ed_date) ?>" required  class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
			
		</div>
		
		<button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
		<button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?php echo $_SERVER['PHP_SELF']?>'"><i class="fa fa-repeat" aria-hidden="true"></i> 검색초기화</button>
		<button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
		</form>
	</fieldset>
	<div class="bo_sch_bg"></div>
</div>

<script>

$(function() {
    $(".btn_bo_sch").on("click", function() {
		$(".bo_sch_wrap").toggle();
	})
	$('.bo_sch_bg, .bo_sch_cls').click(function(){
		$('.bo_sch_wrap').hide();
	});
});

</script>


<?php 
include_once(G5_THEME_PATH.'/tail.php');