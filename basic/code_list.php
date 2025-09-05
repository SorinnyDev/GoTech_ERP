<?php 
include_once('./_common.php');
include_once(G5_THEME_PATH.'/head.php');

$sql_common = " from {$g5['member_table']} ";

$sql_search = " where (1) ";

if(!$code_type)
   $code_type = "1";

switch($code_type){
    case "1" :
        $title_txt = "브랜드";
        break;

    case "2" :
        $title_txt = "카테고리";
        break;

    case "3" :
        $title_txt = "배송";
        break;

    case "4" :
        $title_txt = "매출처";
        break;

    case "5" :
        $title_txt = "매입처";
        break;

    case "6" :
        $title_txt = "환율";
        break;
    case "7" :
        $title_txt = "카드";
        break;
}

if($sch_stx){
    $search_sql = " AND {$sfl} LIKE '%{$sch_stx}%' ";
}

$sql = " SELECT COUNT(*) AS cnt FROM g5_code_list WHERE code_type = '{$code_type}' {$search_sql} AND del_yn = 'N' ORDER BY code_name ASC ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) {
    $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
}
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " SELECT * FROM g5_code_list WHERE code_type = '{$code_type}' {$search_sql} AND del_yn = 'N' ORDER BY code_name ASC LIMIT {$from_record}, {$rows}";
//codeView($sql);
$result = sql_query($sql);
$qstr = $_SERVER['QUERY_STRING'];

define("CODE_USE",["Y"=>"사용","N"=>"사용안함"]);

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
		<h2 class="board_tit">코드관리</h2>
		<form name="fboardlist" id="fboardlist" action="<?php echo G5_URL; ?>/basic/code_list_update.php" onsubmit="return code_frm_chk(this);" method="post">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
            <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
            <input type="hidden" name="stx" value="<?php echo $stx2 ?>">
            <input type="hidden" name="spt" value="<?php echo $spt ?>">
            <input type="hidden" name="sst" value="<?php echo $sst ?>">
            <input type="hidden" name="sod" value="<?php echo $sod ?>">
            <input type="hidden" name="page" value="<?php echo $page ?>">
            <input type="hidden" name="qstr" value="<?php echo $qstr ?>">
            <input type="hidden" name="sw" value="">
            
            <!-- <button type="button" onclick="test_btn()">테스트 버튼</button> -->
            
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
                    <li><button type="button" class="btn_b02" id="modalOpenButton" onclick="modal_action('create','')" style="background:#029CFA;color:white;"><i class="fa fa-file-pdf-o" ></i>코드 추가</button></li>
                    
                </ul>
            </div>
            <h2 style="padding-bottom:10px; font-size:20px; text-align:center"><?=$title_txt?> 관리</h2>

            

            <div class="local_ov01 local_ov" style="margin:10px;">
                <?
                // $tot_cnt = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_code_list WHERE del_yn = 'N' ")['cnt'];
                ?>
                <!--
                <a href="<?=G5_URL?>/basic/code_list.php?code_type=">
                    <span class="btn_ov01">
                        <span class="ov_txt" style="background:#1882F3;border:1px solid #1882F3;">전체</span>
                        <span class="ov_num" style="background:white;border:1px solid #1882F3;"><?=number_format($tot_cnt)?>건</span>
                    </span>
                </a>
                -->

                <? 
                foreach(CODE_TYPE as $key => $value){
                    $cnt = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_code_list WHERE code_type = '{$key}' AND del_yn = 'N' ")['cnt'];
                ?>
                <a href="<?=G5_URL?>/basic/code_list.php?code_type=<?=$key?>">
                    <span class="btn_ov01">
                        <span class="ov_txt" <?=$key==$code_type ? "style=\"background:#1882F3;border:1px solid #1882F3;\" " : ""?> ><?=$value?></span>
                        <span class="ov_num" <?=$key==$code_type ? " style=\"background:white;border:1px solid #1882F3;\" " : ""?> ><?=number_format($cnt)?>건</span>
                    </span>
                </a>
                <? 
                } 
                ?>
            </div>
            <div class="tbl_head01 tbl_wrap" >
                <table>
                    <thead style="position:sticky;top:0;">
                        <tr>
                            <th style="width:50px;">
                                <input type="checkbox" name="allchk" id="allChk"  />
                            </th>
                            <th style="width:100px;">분류</th>
                            <th style="width:100px;">이름</th>
                            <th style="width:100px;">코드</th>
                            <? if($code_type=="3"){?>
                            <th style="width:100px;">유류할증율(%)</th>    
                            <?}?>
                            <!-- <th style="width:100px;">순서</th> -->
                            <th style="width:100px;">사용여부</th>
                            <th style="width:150px;">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    for($i=0;$row=sql_fetch_array($result); $i++) {
                    ?>
                        <tr>
                            <td style="text-align:center;">
                                <input type="checkbox" name="chk[]" value="<?=$i?>" />
                                <input type="hidden" name="idx[<?=$i?>]" value="<?=$row['idx']?>" />
                                <input type="hidden" name="code_orig_value[<?=$row['idx']?>]" value="<?=$row['code_value']?>" />
                            </td>
                            <td >
                                <select name="code_type[<?=$row['idx']?>]" class="frm_input">
                                    <? foreach(CODE_TYPE as $key=>$value){ 
                                        $selected = $key==$row['code_type'] ? "selected" : "";
                                        ?>
                                    <option value="<?=$key?>" <?=$selected?> ><?=$value?></option>
                                    <? } ?>
                                </select>
                            </td>
                            
                            <td ><input type="text" name="code_name[<?=$row['idx']?>]" value="<?=$row['code_name']?>" class="frm_input" /></td>
                            <td ><input type="text" name="code_value[<?=$row['idx']?>]" value="<?=$row['code_value']?>" class="frm_input" /></td>
                            
                            <? if($code_type=="3"){?>
                            <td><input type="text" name="code_percent[<?=$row['idx']?>]" value="<?=$row['code_percent']?>" class="frm_input" /></td>    
                            <? } ?>

                            <!-- <td ><input type="text" name="code_order[<?=$row['idx']?>]" value="<?=$row['code_order']?>" class="frm_input" /></td> -->

                            <td>
                                <? foreach(CODE_USE as $key => $value){ ?>
                                <input type="radio" id="code_use<?=$row['idx'].$key?>" name="code_use[<?=$row['idx']?>]" value="<?=$key?>" <?=$key==$row['code_use'] ? "checked" : ""?> >
                                <label for="code_use<?=$row['idx'].$key?>"><?=$value?></label>
                                <? } ?>
                            </td>
                            <td >
                                <button type="button" class="btn_b01 " onclick="modal_action('update','<?=$row['idx']?>','<?=$row['code_type']?>','<?=$row['code_name']?>','<?=$row['code_value']?>','<?=$row['code_use']?>','<?=$row['code_percent']?>')">수정</button>
                                <button type="button" class="btn_b02" onclick="modal_action('delete','<?=$row['idx']?>');">삭제</button>
                            </td>
                        </tr>
                        
                    <?php 
                            
                    }

                    if(sql_num_rows($result)==0){
                        echo "<tr><td colspan='12' style='text-align:center;'>검색된 목록이 없습니다.</td></tr>";
                    }  
                    
                    ?>
                    </tbody>
                </table>
            </div>
        </form>

        <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?' . $qstr . '&amp;page='); ?>


    </div>
</div>
	
<div id="modalContainer" class="hidden">
  <div id="modalContent">
    <div class="modal_header">
        <h1 class="modal_title"><?=$title_txt?> 추가</h1>
    </div>
    <div class="modal_body">
        <input type="hidden" name="tg_type" value="" />
        <input type="hidden" name="tg_idx" value="" />
        <input type="hidden" name="tg_orig_value" value="" />
        
        <select name="tg_code_type" class="frm_input" onchange="delivery_check()">
            <? foreach(CODE_TYPE as $key => $value){ ?>
            <option value="<?=$key?>" <?=$key==$code_type ? "selected" : ""?> ><?=$value?></option>
            <? } ?>    
        </select><br>

        <input type="text" name="tg_name" value="" placeholder="이름" class="frm_input" />
        <input type="text" name="tg_value" value="" placeholder="<?=$title_txt?> 코드" class="frm_input" />
        <input type="text" name="tg_percent" value="" placeholder="유류할증율(%)" class="frm_input number_fmt_list" id="percent_field" />
        <!-- <input type="text" name="tg_order" value="" placeholder="순서" class="frm_input" /> -->

        <div style="margin:10px 0;">
            <input type="radio" id="tg_use1" name="tg_use" value="Y" checked ><label for="tg_use1">사용</label>
            <input type="radio" id="tg_use2" name="tg_use" value="N"><label for="tg_use2">사용안함</label>
        </div>
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
            <input type="hidden" name="code_type" value="<?=$code_type?>" />
            <label for="stx" style="font-weight:bold"><strong class="sound_only"> 필수</strong></label>
            <div class="sch_bar" style="margin-top:3px;padding:10px;">
                <select name="sfl" class="frm_input" style="width:29%">
                    <option value="code_name" <?=$sfl=="code_name" ? "selected" : ""?> >이름</option>
                    <option value="code_value" <?=$sfl=="code_value" ? "selected" : ""?> >코드명</option>
                </select>
                <input type="text" name="sch_stx" class="frm_input" value="<?=$sch_stx?>" style="width:69%" />
            </div>
            <button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
            <button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?php echo $_SERVER['PHP_SELF']?>'"><i class="fa fa-repeat" aria-hidden="true"></i> 검색초기화</button>
            <button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
		</form>
	</fieldset>
	<div class="bo_sch_bg"></div>
</div>

<script>
var title_txt = "<?=$title_txt?>";


jQuery(function($){
	// 게시판 검색
	$(".btn_bo_sch").on("click", function() {
		$(".bo_sch_wrap").toggle();
	})
	$('.bo_sch_bg, .bo_sch_cls').click(function(){
		$('.bo_sch_wrap').hide();
	});
});

// 코드 불러오기
function code_create_action(type){
    let url = g5_url+"/basic/ajax.code_num_create.php";
    let result = HttpJson(url,"POST",{type : type});

    console.log(result);
    return result;
}

// modal type 변경시
function delivery_check(){
    let selected = $("select[name=tg_code_type] option:selected").val(),
        str = "";
        
    switch(selected){
        case "1" :
            str = "브랜드";
            break;
        case "2" :
            str = "카테고리";
            break;
        case "3" :
            str = "배송";
            break;
        case "4" :
            str = "매출처";
            break;
        case "5" :
            str = "매입처";
            break;
        case "6" :
            str = "매입처";
            break;
    }

    if(selected=="3"){
        $("#percent_field").css("display","block");
    }else{
        $("#percent_field").css("display","none");
    }
    $("input[name=tg_value]").prop("placeholder",str + " 코드");
    
    if($("input[name=tg_type]").val()=="create"){
        $("input[name=tg_value]").val(code_create_action(selected));
    }
}

$(function() {
    delivery_check();

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
        let tg_type = $("input[name=tg_type]").val(),
            tg_idx = $("input[name=tg_idx]").val(),
            tg_code_type = $("select[name=tg_code_type] option:selected").val(),
            tg_name = $("input[name=tg_name]").val(),
            tg_value = $("input[name=tg_value]").val(),
            tg_orig_value = $("input[name=tg_orig_value]").val(),
            tg_percent = $("input[name=tg_percent]").val(),
            // tg_order = $("input[name=tg_order]").val(),
            tg_use = $("input[name=tg_use]:checked").val();

        const obj = {
            type:tg_type,
            idx:tg_idx,
            code_type:tg_code_type,
            code_name:tg_name,
            code_value:tg_value,
            code_orig_value:tg_orig_value,
            code_percent:tg_percent,
            // code_order:tg_order,
            code_use:tg_use,
        };  

        const txt = tg_type == 'create' ? "추가가" : "수정이";

        $.post(g5_url+"/basic/ajax.code_update.php", obj, function(data){

            if(data=="y"){
                alert(`${txt} 완료되었습니다.`);
                location.href=g5_url+"/basic/code_list.php?code_type=" + tg_code_type;
            }else if(data=="dupil_err"){
                alert("중복된 코드입니다. 확인 후 다시 이용해주세요.");
            }else{
                alert("오류가 발생했습니다.");
            }
        });
    });	

});

// 폼체크
function code_frm_chk(form){
    if($("input[name^=chk]:checked").length==0){
        alert("선택된 목록이 없습니다.");
        return false;
    }    

    return true;
}


function modal_action(type,tg_idx,tg_code_type,tg_name,tg_value,tg_use,code_percent){
    let create_code = "",
        select_val = $("select[name=tg_code_type] option:selected").val();
    
    if(type!="delete"){
        if($("#modalContainer").hasClass("hidden")===true)
            $("#modalContainer").removeClass("hidden");
        else
            $("#modalContainer").addClass("hidden");
    }
    
    if(type=="create"){
        create_code = code_create_action(select_val);
    }


    switch(type){
        case "create" :
            $(".modal_title").text(`${title_txt} 추가`);
            // $("select[name=tg_code_type] option").prop("selected",false);
            $("select[name=tg_code_type]").val(select_val).prop("selected",true);
            $("input[name=tg_idx]").val("");
            $("input[name=tg_name]").val("");
            $("input[name=tg_value]").val(create_code);
            $("input[name=tg_orig_value]").val("");
            $("input[name=tg_percent]").val("");
            $("input[name=tg_type]").val("create");
            $("#tg_use1").prop("checked",true);
            $("#tg_use2").prop("checked",false);
            $("#clAddButton").text("추가");
            break;

        case "update" :
            $(".modal_title").text(`${title_txt} 수정`);
            $("input[name=tg_idx]").val(tg_idx);
            $("select[name=tg_code_type]").val("<?=$code_type?>").prop("selected",true);
            $("input[name=tg_name]").val(tg_name);
            $("input[name=tg_value]").val(tg_value);
            $("input[name=tg_orig_value]").val(tg_value);
            $("input[name=tg_percent]").val(code_percent);
            $("input[name=tg_type]").val("update");
            $("#clAddButton").text("수정");        
            
            if(tg_use=="Y"){
                $("#tg_use1").prop("checked",true);
                $("#tg_use2").prop("checked",false);
            }else{
                $("#tg_use1").prop("checked",false);
                $("#tg_use2").prop("checked",true);
            }

            break;

        case "delete" :
            if(confirm(`정말 삭제하시겠습니까?\n[경고]\n추가된 ${title_txt} 전부 삭제됩니다.`)){
                $.post(g5_url+"/basic/ajax.code_update.php",{type:"delete",idx:tg_idx},function(data){
                    if(data=='y'){
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

    // 필드 체크
    delivery_check();
}

</script>


<?php 
include_once(G5_THEME_PATH.'/tail.php');