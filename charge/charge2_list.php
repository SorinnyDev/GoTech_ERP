<?php 
include_once('./_common.php');
include_once(G5_THEME_PATH.'/head.php');


$qstr = $_SERVER['QUERY_STRING'];
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

/* modal css */
#modalOpenButton, #modalCloseButton {cursor: pointer;}
#modalContainer {
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  background: rgba(0, 0, 0, 0.5);
}

#modalContent {
  position: absolute;
  background-color: #ffffff;
  width: 300px;
  height: auto;
  padding: 15px;
}

#modalContainer.hidden {
  display: none;
}

.modal_title{
    margin:10px 0;
}
.modal_body > .frm_input{
    margin-bottom:5px;
}
.modal_bottom{
    margin-top:5px;
}

</style>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">수수료 관리</h2>
		<form name="fboardlist" id="fboardlist" action="<?php echo G5_URL; ?>/charge/charge2_list_update.php" onsubmit="return charge_frm_chk(this);" method="post">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
            <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
            <input type="hidden" name="stx" value="<?php echo $stx2 ?>">
            <input type="hidden" name="spt" value="<?php echo $spt ?>">
            <input type="hidden" name="sst" value="<?php echo $sst ?>">
            <input type="hidden" name="sod" value="<?php echo $sod ?>">
            <input type="hidden" name="page" value="<?php echo $page ?>">
            <input type="hidden" name="qstr" value="<?php echo $qstr ?>">
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
                    <li><button type="submit" name="btn_submit" value="선택수정" class="btn_b02 " style="background:#8e74ef;color:white;"><i class="fa fa-file-pdf-o" ></i> 선택수정</button></li>
                    <li><button type="button" class="btn_b02" id="modalOpenButton" onclick="modal_action('create','')" style="background:#029CFA;color:white;"><i class="fa fa-file-pdf-o" ></i>수수료 추가</button></li>
                    
                </ul>
            </div>
            <h2 style="padding-bottom:10px; font-size:20px; text-align:center">수수료 관리</h2>
            <div class="tbl_head01 tbl_wrap" >
                <table>
                    <thead style="position:sticky;top:0;">
                        <tr>
                            <th style="width:50px;">
                                <input type="checkbox" name="allchk" id="allChk"  />
                            </th>
                            <th style="width:100px;">일자</th>
                            <?
                            $sql = " SELECT cl_idx,cl_name,cl_order FROM g5_charge2_sub ORDER BY cl_order ASC ";
                            $result = sql_query($sql);
                            
                            $cl_arr = [];

                            while($row=sql_fetch_array($result)){
                                array_push($cl_arr,$row);
                                ?>
                                <th style='width:100px;'><?=$row['cl_name']?>
                                    <button type='button' class='btn_b01' onclick="modal_action('update','<?=$row['cl_idx']?>','<?=$row['cl_name']?>','<?=$row['cl_order']?>')">수정</button>
                                    <button type='button' class='btn_b02' onclick="modal_action('delete','<?=$row['cl_idx']?>')">삭제</button>
                                </th>
                                <?      
                            }

                            ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    //if($st_date && $ed_date){
                        function date_lookup($type,$st_date,$ed_date){
                            $st_date = strtotime($st_date);
                            $ed_date = strtotime($ed_date);
                            $one_day = 86400;
                            $cnt = ($ed_date - $st_date)/$one_day;
                            $temp = $type=="ASC" ? $st_date : $ed_date;
                            

                            for($i=1;$i<$cnt+1;$i++){
                                if(empty($arr[0])){
                                    $arr[0] = $type=="ASC" ? date("Y-m-d",$st_date) : date("Y-m-d",$ed_date);
                                }
                                if($type=="ASC"){
                                    $temp += $one_day;
                                    $arr[$i] = date("Y-m-d",$temp);
                                }else if($type=="DESC"){
                                    $temp -= $one_day;
                                    $arr[$i] = date("Y-m-d",$temp);
                                }
                            }
                            return $arr;
                        }

                        $search_sql = "";
                        $arr = date_lookup("ASC",$st_date,$ed_date);

                        for($i=0; $i<count($arr); $i++) {
                    
                    ?>
                        <tr>
                            <td style="text-align:center;">
                                <input type="checkbox" name="chk[]" value="<?=$i?>" />
                                <input type="hidden" name="cl_date[<?=$i?>]" value="<?=$arr[$i]?>" />
                            </td>
                            <td class="date"><?php echo $arr[$i]?></td>
                            <? for($j=0;$j<count($cl_arr);$j++){ 
                                $cl_idx = $cl_arr[$j]['cl_idx']; 
                                $rate = sql_fetch(" SELECT rate FROM g5_charge2 WHERE cl_date = '{$arr[$i]}' and cl_idx = '{$cl_idx}' ")['rate'];
                                ?>
                                <td class="text-center">
                                    <input type="hidden" name="cl_idx[<?=$arr[$i]?>][<?=$j?>]" class="frm_input" value="<?=$cl_idx?>" />
                                    <input type="text" name="rate[<?=$arr[$i]?>][<?=$j?>]" class="frm_input number_fmt_list" value="<?=$rate?>" />
                                </td>
                            <? } ?>

                        </tr>
                        
                    <?php 
                            
                        }

                    //}

                    if(count($arr)==0){
                        echo "<tr><td colspan='12' style='text-align:center;'>검색된 목록이 없습니다.</td></tr>";
                    }  
                    
                    
                    ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
	
<div id="modalContainer" class="hidden">
  <div id="modalContent">
    <div class="modal_header">
        <h1 class="modal_title">수수료 추가</h1>
    </div>
    <div class="modal_body">
        <input type="hidden" name="tg_type" value="" />
        <input type="hidden" name="tg_idx" value="" />
        <input type="text" name="tg_name" value="" placeholder="수수료명" class="frm_input" />
        <input type="text" name="tg_order" value="" placeholder="순서" class="frm_input" />
    </div>
    <div class="modal_bottom">
        <button type="button" id="clAddButton" class="btn_b01" >추가</button>
        <button type="button" id="modalCloseButton" onclick="modal_action('','')" class="btn_b02">닫기</button>
    </div>
  </div>
</div>

<div class="bo_sch_wrap">
	<fieldset class="bo_sch" style="padding:10px">
		<h3>검색</h3>
		<form name="fsearch" method="get" >

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

$(function() {
    $("#allChk").bind("click",function(){
        if(this.checked){
            $("input[name^=chk]").prop("checked",true);
        }else{
            $("input[name^=chk]").prop("checked",false);
        }
    });
    
    $(".number_fmt_list").bind('input',function(){
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    });

    $("#clAddButton").bind('click',function(){
        let cl_type = $("input[name=tg_type]").val(),
            cl_name = $("input[name=tg_name]").val(),
            cl_order = $("input[name=tg_order]").val(),
            cl_idx = $("input[name=tg_idx]").val();

        const obj = {type:cl_type,cl_idx:cl_idx,cl_name:cl_name,cl_order:cl_order};  
        const txt = cl_type == 'create' ? "추가가" : "수정이"

        $.post(g5_url+"/charge/ajax.charge2_update.php", obj, function(data){
            if(data){
                alert(`수수료 ${txt} 완료되었습니다.`);
                location.reload();
            }else{
                alert("오류가 발생했습니다.");
            }
        });
    });

	
});

// 폼체크
function charge_frm_chk(form){
    
    if($("input[name^=chk]:checked").length==0){
        alert("선택된 목록이 없습니다.");
        return false;
    }    

    return true;
}

function modal_action(type,cl_idx,cl_name,cl_order){
    if(type!="delete"){
        if($("#modalContainer").hasClass("hidden")===true)
            $("#modalContainer").removeClass("hidden");
        else
            $("#modalContainer").addClass("hidden");
    }
    
    switch(type){
        case "create" :
            $(".modal_title").text("수수료 추가");
            $("input[name=tg_idx]").val("");
            $("input[name=tg_name]").val("");
            $("input[name=tg_order]").val("");
            $("input[name=tg_type]").val("create");
            $("#clAddButton").text("추가");
            break;

        case "update" :
            $(".modal_title").text("수수료 수정");
            $("input[name=tg_idx]").val(cl_idx);
            $("input[name=tg_name]").val(cl_name);
            $("input[name=tg_order]").val(cl_order);
            $("input[name=tg_type]").val("update");
            $("#clAddButton").text("수정");            
            break;

        case "delete" :
            if(confirm("정말 삭제하시겠습니까?\n[경고]\n추가된 수수료 전부 삭제됩니다.")){
                $.post(g5_url+"/charge/ajax.charge2_update.php",{type:"delete",cl_idx:cl_idx},function(data){
                    if(data){
                        alert("삭제가 완료되었습니다.");
                        location.reload();
                    }else{
                        alert("오류가 발생했습니다.");
                    }
                });
            }
            break;

        default :     
    }

}



</script>


<?php 
include_once(G5_THEME_PATH.'/tail.php');