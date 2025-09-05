<?php 
include_once('./_common.php');
include_once(G5_THEME_PATH.'/head.php');


$sql_common = " FROM g5_member ";
$sql_search = " WHERE del_yn = 'N' AND (mb_id !='devAdmin'&& mb_id !='test') ";

$mb_type = (!$mb_type) ? "1" : $mb_type;

if($mb_type){
    $sql_search .= " AND mb_type = '{$mb_type}' ";
}

if($stx){
    switch($sfl){
        case "mb_name" :
            $sql_search .= " AND mb_name LIKE '%{$stx}%' ";
            break;

        case "mb_id" :
            $sql_search .= " AND mb_id = '{$stx}' ";
            break;
    }
}

$sql = " SELECT COUNT(*) AS cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  
if ($page < 1) {
    $page = 1; 
}
$from_record = ($page - 1) * $rows; 

$sql = " SELECT * {$sql_common} {$sql_search} ORDER BY mb_no DESC LIMIT {$from_record}, {$rows}";
$result = sql_query($sql);

$qstr = $_SERVER['QUERY_STRING'];

define("MB_TYPE",["1"=>"관리팀","2"=>"물류팀","3"=>"Ebay","4"=>"Amazon","5"=>"Qoo10","6"=>"Japan","7"=>"디자인","8"=>"자사몰","9"=>"shopee"]);
define("CODE_USE",["Y"=>"사용","N"=>"사용안함"]);

$title_txt = MB_TYPE[$mb_type];
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
		<h2 class="board_tit"><?=$title_txt?> 관리</h2>
		<form name="fboardlist" id="fboardlist" action="<?php echo G5_URL; ?>/basic/member_list_update.php" onsubmit="return code_frm_chk(this);" method="post">
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
                    <li><button type="submit" name="btn_submit" value="선택수정" class="btn_b01 " style="background:#8e74ef;color:white;"><i class="fa fa-file-pdf-o" ></i> 선택수정</button></li>
                    <li><button type="submit" name="btn_submit" value="선택삭제" class="btn_b01 " style="background:#D63B2C;color:white;"><i class="fa fa-file-pdf-o" ></i> 선택삭제</button></li>
                    <li><button type="button" class="btn_b02" id="modalOpenButton" onclick="modal_action('create','')" style="background:#029CFA;color:white;"><i class="fa fa-file-pdf-o" ></i>직원 추가</button></li>
                    
                </ul>
            </div>
            <h2 style="padding-bottom:10px; font-size:20px; text-align:center"><?=$title_txt?> 관리</h2>

            <div class="local_ov01 local_ov" style="margin:10px;">
                <? 
                foreach(MB_TYPE as $key => $value){
                    $cnt = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_member WHERE mb_type = '{$key}' AND del_yn = 'N' AND (mb_id !='admin'&& mb_id !='test') ")['cnt'];
                ?>
                <a href="<?=G5_URL?>/basic/member_list.php?mb_type=<?=$key?>">
                    <span class="btn_ov01">
                        <span class="ov_txt" style="background:#1882F3;border:1px solid #1882F3;"><?=$value?></span>
                        <span class="ov_num" style="background:white;border:1px solid #1882F3;"><?=number_format($cnt)?>명</span>
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
                                <input type="hidden" name="mb_no[<?=$i?>]" value="<?=$row['mb_no']?>" />
                                <input type="hidden" name="orig_mb_id[<?=$row['mb_no']?>]" value="<?=$row['mb_id']?>" />
                            </td>
                            <td >
                                <select name="mb_type[<?=$row['mb_no']?>]" class="frm_input">
                                <? foreach(MB_TYPE as $key=>$value){ 
                                    $selected = $key==$row['mb_type'] ? "selected" : "";
                                    ?>
                                    <option value="<?=$key?>" <?=$selected?> ><?=$value?></option>
                                <? } ?>
                                </select>
                            </td>
                            
                            <td ><input type="text" name="mb_name[<?=$row['mb_no']?>]" value="<?=$row['mb_name']?>" class="frm_input" /></td>
                            <td ><input type="text" name="mb_id[<?=$row['mb_no']?>]" value="<?=$row['mb_id']?>" class="frm_input" /></td>

                            <td >
                                <button type="button" class="btn_b01 " onclick="modal_action('update','<?=$row['mb_no']?>','<?=$row['mb_type']?>','<?=$row['mb_name']?>','<?=$row['mb_id']?>')">수정</button>
                                <button type="button" class="btn_b02" onclick="modal_action('delete','<?=$row['mb_no']?>');">삭제</button>
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
            <h1 class="modal_title">직원 추가</h1>
        </div>
        <div class="modal_body">
            <input type="hidden" name="tg_type" value="" />
            <input type="hidden" name="tg_no" value="" />
            <input type="hidden" name="tg_orig_id" value="" />
            <select name="tg_mb_type" class="frm_input">
                <? foreach(MB_TYPE as $key => $value){ 
                    $selected = ($key==$mb_type) ? "selected" : "";
                    ?>
                <option value="<?=$key?>" <?=$selected?> ><?=$value?></option>
                <? } ?>
            </select><br>
            <input type="text" name="tg_name" value="" placeholder="이름" class="frm_input" required/>
            <input type="text" name="tg_id" value="" placeholder="직원코드" class="frm_input" required/>
            <button type="button" class="btn_b01" onclick="dupli_check();" style="background:#029CFA;">중복 확인</button>
            <input type="password" name="tg_pw" value="" placeholder="비밀번호" class="frm_input" required/>
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
            <input type="hidden" name="mb_type" value="<?=$mb_type?>"/>
            <label for="stx" style="font-weight:bold">검색<strong class="sound_only"> 필수</strong></label>
            <div class="sch_bar" style="margin-top:3px;padding:10px;">
                <select name="sfl" class="frm_input" style="width:30%;">
                    <option value="mb_name" <?=$sfl=="mb_name" ? "selected" : ""?> >이름</option>   
                    <option value="mb_id" <?=$sfl=="mb_id" ? "selected" : ""?> >코드</option> 
                </select>
                <input type="text" name="stx" value="<?=$stx?>" class="frm_input" style="width:68%;" />
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
        let tg_type = $("input[name=tg_type]").val(),
            tg_mb_type = $("select[name=tg_mb_type] option:selected").val(),
            tg_name = $("input[name=tg_name]").val(),
            tg_id = $("input[name=tg_id]").val(),
            tg_orig_id = $("input[name=tg_orig_id]").val(),
            tg_pw = $("input[name=tg_pw]").val(),
            tg_no = $("input[name=tg_no]").val(),
            txt = (tg_type == "create") ? "추가" : "수정";

        const obj = {
            type:tg_type,
            mb_type:tg_mb_type,
            mb_name:tg_name,
            mb_id:tg_id,
            mb_orig_id:tg_orig_id,
            mb_password:tg_pw,
            mb_no:tg_no,
        };  

        if(!tg_name){
            alert("이름을 입력해주세요.");
            $("input[name=tg_name]").focus();
            return false;
        }
        
        if(!tg_id){
            alert("코드를 입력해주세요.");
            $("input[name=tg_id]").focus();
            return false;
        }

        if(!tg_pw && tg_type=="create"){
            alert("패스워드를 입력해주세요.");
            $("input[name=tg_pw]").focus();
            return false;
        }

        $.post(g5_url+"/basic/ajax.member_update.php", obj, function(data){
            // console.log(data);
            if(data=="y"){
                alert(`${title_txt} ${txt} 완료되었습니다.`);
                location.href=g5_url+"/basic/member_list.php?mb_type=" + tg_mb_type;
            }else if(data=="dupli_err"){
                alert("중복된 코드입니다. 다시 확인 후 이용해주세요.");
            }else{
                alert("오류가 발생했습니다.");
            }
        });

    });	
});

function code_frm_chk(form){
    if($("input[name^=chk]:checked").length==0){
        alert("선택된 목록이 없습니다.");
        return false;
    }    

    return true;
}

function dupli_check(){
    let mb_id = $("input[name=tg_id]").val();

    $.post(g5_url+"/basic/ajax.member_action.php",{mb_id:mb_id},function(data){
        if(data=='y'){
            alert("중복된 코드 입니다. 다시 입력 해주세요.");
            $("input[name=tg_id]").focus();
        }else{
            alert("사용 가능한 코드입니다.");
        }
    });
}


function modal_action(type,tg_no,tg_mb_type,tg_name,tg_id){
    if(type!="delete"){
        if($("#modalContainer").hasClass("hidden")===true)
            $("#modalContainer").removeClass("hidden");
        else
            $("#modalContainer").addClass("hidden");
    }
    
    switch(type){
        case "create" :
            $(".modal_title").text(`직원 추가`);
            $("input[name=tg_no]").val("");
            $("select[name=tg_code_type] option").prop("selected",false);
            $("input[name=tg_name]").val("");
            $("input[name=tg_id]").val("");
            $("input[name=tg_orig_id]").val("");
            $("input[name=tg_pw]").val("");
            $("input[name=tg_type]").val("create");
            $("#clAddButton").text("추가");
            break;

        case "update" :
            $(".modal_title").text(`직원 수정`);
            $("input[name=tg_no]").val(tg_no);
            $("select[name=tg_mb_type]").val(tg_mb_type).prop("selected",true);
            $("input[name=tg_name]").val(tg_name);
            $("input[name=tg_id]").val(tg_id);
            $("input[name=tg_orig_id]").val(tg_id);
            $("input[name=tg_type]").val("update");
            $("#clAddButton").text("수정");        

            break;

        case "delete" :
            if(confirm(`정말 삭제하시겠습니까?\n[경고]\n추가된 ${title_txt} 전부 삭제됩니다.`)){
                $.post(g5_url+"/basic/ajax.member_update.php",{type:"delete",mb_no:tg_no},function(data){
                    if(data=='y'){
                        alert("삭제가 완료되었습니다.");
                        location.reload();
                         
                    }else if(data=='dupli_err'){
                        alert("중복된 코드 입니다. 다시 입력 후 이용해주세요.");

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