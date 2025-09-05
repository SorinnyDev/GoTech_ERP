<?php 
include_once('./_common.php');

include_once(G5_THEME_PATH.'/head.php');



if($st_date && $ed_date){
    // $add_sql = "";

    // if($wr_18)
    //     $add_sql .= " AND wr_domain = '{$wr_18}' ";

    // if($wr_cal_chk)
    //     $add_sql .= " AND wr_cal_chk = '{$wr_cal_chk}' ";

    // $cnt_list = sql_fetch(" SELECT SUM(if(wr_cal_chk='Y')) AS succ_cnt, SUM(if(wr_cal_chk='N')) AS fail_cnt FROM g5_sales3_list WHERE wr_release_use = '1' {$add_sql} AND wr_date4 BETWEEN '{$st_date}' AND '{$ed_date}' ORDER BY wr_date4 ASC ");
    // echo " cnt : ".$cnt_list['succ_cnt']." ".$cnt_list['fail_cnt'];
}
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
</style>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">매출처 원장</h2>
		<form name="fboardlist" id="fboardlist" action="<?php echo G5_URL; ?>/acc/acc3_list_update.php" onsubmit="return acc3_frm_submit(this);" method="post">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
            <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
            <input type="hidden" name="stx" value="<?php echo $stx2 ?>">
            <input type="hidden" name="spt" value="<?php echo $spt ?>">
            <input type="hidden" name="sst" value="<?php echo $sst ?>">
            <input type="hidden" name="sod" value="<?php echo $sod ?>">
            <input type="hidden" name="page" value="<?php echo $page ?>">
            <input type="hidden" name="qstr" value="<?=$qstr?>">
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
                    <li><button type="submit" name="btn_submit" value="선택정산완료" class="btn_b02 " style="background:#8e74ef;color:white;"><i class="fa fa-file-pdf-o" ></i> 선택정산완료</button></li>
                    <li><button type="submit" name="btn_submit" value="선택정산취소" class="btn_b02 " ><i class="fa fa-file-pdf-o" ></i> 선택정산취소</button></li>
                    
                </ul>
            </div>
            <h2 style="padding-bottom:10px; font-size:20px; text-align:center">매출처 원장</h2>
            <div class="tbl_head01 tbl_wrap" >
                <table>
                    <thead style="position:sticky;top:0;">
                        <tr>
                            <th><input type="checkbox" name="allChk" id="allChk" /></th>
                            <th>일자</th>
                            <th>매출처</th>
                            <th style="width:200px">대표코드</th>
                            <th style="width:200px">SKU</th>
                            <th style="width:200px">상품명</th>
                            <th>수량</th>
                            <th>단가($)</th>
                            <th>신고가격($)</th>
                            <th>정산유무</th>
                            <th>비고</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    if($st_date && $ed_date){
                        $search_sql = "";

                        if($wr_18)
                            $search_sql .= " AND wr_domain = '{$wr_18}' ";

                        if($wr_cal_chk)
                            $search_sql .= " AND wr_cal_chk = '{$wr_cal_chk}' ";

                        $sql = "SELECT * FROM g5_sales3_list WHERE wr_release_use = '1' {$search_sql} AND wr_date4 BETWEEN '{$st_date}' AND '{$ed_date}' ORDER BY wr_date4 ASC";
                        $rs = sql_query($sql);

                        $danga_sum = 0;
                        $singo_sum = 0;
                        $cal_succ_cnt = 0;
                        $cal_fail_cnt = 0;
                        
                        for($i=0; $row=sql_fetch_array($rs); $i++) {
                            $item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");
                            
                            $danga_sum += $row['wr_danga'];
                            $singo_sum += $row['wr_singo'];

                            if($row['wr_cal_chk']=="Y")
                                $cal_succ_cnt++;
                            else
                                $cal_fail_cnt++;
                    ?>
                        <tr>
                            <td style="text-align:center;">
                                <input type="checkbox" name="chk[]" value="<?=$i?>" />
                                <input type="hidden" name="seq[<?=$i?>]" value="<?=$row['seq']?>" />
                            </td>
                            <td class="date"><?php echo $row['wr_date4']?></td>
                            <td><?=$row['wr_domain']?></td>
                            <td><?php echo $item['wr_5']?></td>
                            <td><?php echo $row['wr_code']?></td>
                            <td><?php echo $item['wr_subject']?></td>
                            <td class="num"><?php echo $row['wr_ea']?>개</td>
                            <td class="num"><?php echo $row['wr_danga']?>$</td>
                            <td class="num"><?php echo $row['wr_singo']?>$</td>
                            <td style="text-align:center;"><?=$row['wr_cal_chk']=='Y' ? "<span style='color:blue;'>정산완료</span>" : "<span style='color:red;'>정산미완료</span>"?></td>
                            <td style="text-align:center;">
                                <? if($row['wr_cal_chk']=="N"){ ?>
                                <button type="button" class="btn_b02" onclick="calculate_action('cal_chk','<?=$row['seq']?>','Y')" >정산</button>
                                <? }else{ ?>
                                <button type="button" class="btn_b02" onclick="calculate_action('cal_chk','<?=$row['seq']?>','N')" style="border:1px solid #F32C18;background:#F32C18;color:white;" >정산취소</button>
                                <? } ?>    
                            </td>
                        </tr>

                        
                        <? if($i==(sql_num_rows($rs)-1)){ ?>
                            <tr>
                                <td colspan='7' style="text-align:center;">합계</td>
                                <td colspan='1' style="text-align:right;"><?=number_format($danga_sum,2)?>$</td>
                                <td colspan='1' style="text-align:right;"><?=number_format($singo_sum,2)?>$</td>
                                <td colspan='1' style="text-align:center;">
                                    <div class='local_ov01 local_ov'>
                                        <span class='btn_ov01'>
                                            <span class='ov_txt' style="background:#1882F3;">완료</span>
                                            <span class='ov_num' style="background:white;"><?=$cal_succ_cnt?>건</span>
                                        </span>
                                        <span class='btn_ov01'>
                                            <span class='ov_txt' style="background:#FA2402;color:white;">미완료</span>
                                            <span class='ov_num' style="background:white;"><?=$cal_fail_cnt?>건</span>
                                        </span>
                                    </div>
                                <td colspan='1' style="text-align:right;"></td>
                            </tr>

                        <? } ?> 

                    <?php 
                            
                        } 
                    }

                    if(sql_num_rows($rs)==0){
                        echo "<tr><td colspan='10' style='text-align:center;'>검색된 목록이 없습니다.</td></tr>";
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
                $code_list = get_code_list('4');
                foreach($code_list as $key => $value){
                    echo "<option value=\"{$value['code_value']}\">{$value['code_name']}</option>";
                }
            ?>

		</select>
		
		<select name="wr_cal_chk" style="margin-bottom:15px">
			<option value="">전체 정산내역</option>
			<option value="Y" <?php echo get_selected($_GET['wr_cal_chk'], 'Y')?>>정산완료</option>
			<option value="N" <?php echo get_selected($_GET['wr_cal_chk'], 'N')?>>정산미완료</option>
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
<script>



$(function() {
    $("#allChk").bind("click",function(){
        if(this.checked){
            $("input[name^=chk]").prop("checked",true);
        }else{
            $("input[name^=chk]").prop("checked",false);
        }
    });

	$('#sorting_box').bind('change', function(){
		let sort = $(this).val();
		
		if(sort == "default") {
			location.href = '?sst=wr_id&sod=desc&stx2=<?php echo $stx2?>';
		} else if(sort == "up") {
			location.href = '?sst=stock&sod=desc&stx2=<?php echo $stx2?>';
		} else if(sort == "down") {
			location.href = '?sst=stock&sod=asc&stx2=<?php echo $stx2?>';
		}
	})
	
	$('.save_btn').bind('click', function() {
		
		let id = $(this).attr('data');
		let stock1 = $(this).closest('li').find('.wr_32').val();
		let stock2 = $(this).closest('li').find('.wr_36').val();
		let stock3 = $(this).closest('li').find('.wr_37').val();
		
		$.post('./stock_update.php', { wr_id : id, stock1 : stock1, stock2 : stock2, stock3 : stock3 }, function(data) {
			if(data == "y") {
				alert('재고수량이 저장되었습니다.');
			} else {
				alert('처리 중 오류가 발생했습니다.');
			}
		})
		
	});
	
	$('.excel_form').bind('click', function() {
		
		let id = $(this).attr('data');
		var _width = '600';
	    var _height = '600';
	 
	    var _left = Math.ceil(( window.screen.width - _width )/2);
	    var _top = Math.ceil(( window.screen.height - _height )/2); 
	
		window.open("./excel_pop.php", "excel_pop", "left="+_left+", top="+_top+", width="+_width+", height="+_height+", scrollbars=1");
		
		return false;
	});
	
	$('.delivery_com').bind('click', function() {
		
		var _width = '625';
	    var _height = '600';
	 
	    var _left = Math.ceil(( window.screen.width - _width )/2);
	    var _top = Math.ceil(( window.screen.height - _height )/2); 
	
		window.open("./delivery_company.php", "pop_delivery_company", "left="+_left+", top="+_top+", width="+_width+", height="+_height+", scrollbars=1");
		
		return false;
	});
	

	
});

function calculate_action(type,seq,wr_cal_chk){
    
    $.post(g5_url+"/acc/ajax.acc3_update.php",{type:type,seq:seq,wr_cal_chk:wr_cal_chk},function(data){
        if(data=='y'){
            let txt = wr_cal_chk=="Y" ? "정산처리" : "정산취소";
            alert(txt+"가 완료되었습니다.");
            location.reload();
        }else{
            alert("요류가 발생했습니다.");
        }
    });

}

function acc3_frm_submit(form){
    
    if($("input[name^=chk]:checked").length==0){
        alert("선택된 목록이 없습니다.");
        return false;
    }    

    return true;
}

</script>


<?php 
include_once(G5_THEME_PATH.'/tail.php');