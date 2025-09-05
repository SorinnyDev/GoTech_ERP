<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
#bo_w { max-width:100%; width:700px}
.select2-container--default .select2-selection--single { height:40px; border:1px solid #d9dee9; background:#f1f3f6 }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:38px }
</style>
<section id="bo_w" >
	<h2 class="board_tit"><?php echo $board['bo_subject']?></h2>
    <form name="fwrite" id="fwrite" action="<?=$board_skin_url?>/write_update.skin.php" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <?php
    $option = '';
    $option_hidden = '';
    if ($is_notice || $is_html || $is_secret || $is_mail) {
        $option = '';
        if ($is_notice) {
            $option .= PHP_EOL.'<input type="checkbox" id="notice" name="notice" value="1" '.$notice_checked.'>'.PHP_EOL.'<label for="notice" class="notice_ck">공지</label>';
        }

        if ($is_html) {
            if ($is_dhtml_editor) {
                $option_hidden .= '<input type="hidden" value="html1" name="html">';
            } else {
                $option .= PHP_EOL.'<input type="checkbox" id="html" name="html" onclick="html_auto_br(this);" value="'.$html_value.'" '.$html_checked.'>'.PHP_EOL.'<label for="html" class="html_ck">html</label>';
            }
        }

        if ($is_secret) {
            if ($is_admin || $is_secret==1) {
                $option .= PHP_EOL.'<input type="checkbox" id="secret" name="secret" value="secret" '.$secret_checked.'>'.PHP_EOL.'<label for="secret" class="secret_ck">비밀글</label>';
            } else {
                $option_hidden .= '<input type="hidden" name="secret" value="secret">';
            }
        }

        if ($is_mail) {
            $option .= PHP_EOL.'<input type="checkbox" id="mail" name="mail" value="mail" '.$recv_email_checked.'>'.PHP_EOL.'<label for="mail">답변메일받기</label>';
        }
    }

    echo $option_hidden;
    ?>
	
    <div class="form_inpt">
        <h2 class="sound_only"><?php echo $g5['title'] ?></h2>
		
		<ul class="bo_w_info">
	        
	        <?php if (!$is_member) {  ?>
	        <li class="wli_left">
	        	<div class="wli_tit"></div>
		    	<?php if ($is_name) { ?>
		    	<div class="wli_cnt">
	            	<label for="wr_name" class="sound_only">이름<strong>필수</strong></label>
	            	<input type="text" name="wr_name" value="<?php echo $name ?>" id="wr_name" required class="frm_input full_input required" maxlength="20" placeholder="이름">
	        	</div>
	        	<?php } ?>
	        </li>
			<li class="wli_left">
				<div class="wli_tit">비밀번호</div>
		        <?php if ($is_password) { ?>
		        <div class="wli_cnt">	
		            <label for="wr_password" class="sound_only">비밀번호<strong>필수</strong></label>
		            <input type="password" name="wr_password" id="wr_password" <?php echo $password_required ?> class="frm_input full_input <?php echo $password_required ?>" maxlength="20" placeholder="비밀번호">
		        </div>
		        <?php } ?>
			</li>
			<li class="wli_left">
				<div class="wli_tit">이메일</div>
		        <?php if ($is_email) { ?>
		        <div class="wli_cnt">
		            <label for="wr_email" class="sound_only">이메일</label>
		            <input type="email" name="wr_email" value="<?php echo $email ?>" id="wr_email" class="frm_input full_input" email" maxlength="100" placeholder="이메일">
		        </div>
		        <?php } ?>
			</li>
			<?php } ?>
			
			<li class="bo_w_tit half_box">
				<div class="wli_tit">주문번호</div>
		    	<div class="wli_cnt">
		    		<label for="wr_subject" class="sound_only">주문번호<strong>필수</strong></label>
	    			<input type="text" name="wr_subject" value="<?php echo $wr_subject ?>" id="wr_subject" required class="frm_input required" placeholder="제품명을 반드시 입력하세요.">
		    	</div>
		    </li>
			
			<li class="bo_w_tit half_box">
				<div class="wli_tit">매출일자</div>
		    	<div class="wli_cnt">
		    		<label for="wr_date1" class="sound_only">매출일자<strong>필수</strong></label>
	    			<input type="text" name="wr_date1" value="<?php echo $wr_date1 ?>" id="wr_date1"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			
			<li class="bo_w_tit half_box">
				<div class="wli_tit">반품일자</div>
		    	<div class="wli_cnt">
		    		<label for="wr_date2" class="sound_only">약칭명<strong>필수</strong></label>
	    			<input type="text" name="wr_date2" value="<?php echo $wr_date2 ?>" id="wr_date2"  class="frm_input " placeholder="">
		    	</div>
		    </li>
            
			<li class="bo_w_tit half_box">
				<div class="wli_tit">SKU</div>
		    	<div class="wli_cnt">
		    		<label for="wr_1" class="sound_only">SKU<strong>필수</strong></label>
	    			<input type="text" name="wr_1" value="<?php echo $wr_1 ?>" id="wr_1"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			
			<li class="bo_w_tit half_box">
				<div class="wli_tit">상품명</div>
		    	<div class="wli_cnt">
		    		<label for="wr_2" class="sound_only">상품명<strong>필수</strong></label>
	    			<input type="text" name="wr_2" value="<?php echo $wr_2 ?>" id="wr_2"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">대표코드</div>
		    	<div class="wli_cnt">
		    		<label for="wr_3" class="sound_only">대표코드<strong>필수</strong></label>
	    			<input type="text" name="wr_3" value="<?php echo $wr_3 ?>" id="wr_3"  class="frm_input " placeholder="">
		    	</div>
		    </li>
    	</ul>

	    <?php if ($is_use_captcha) { //자동등록방지 ?>
	    <div class="wli_cnt wli_captcha">
	        <span class="sound_only">자동등록방지</span>
	        <?php echo $captcha_html ?>
	    </div>
	    <?php } ?>
	</div>

    <div class="bo_w_btn">
        <a href="<?php echo get_pretty_url($bo_table); ?>" class="btn_cancel">취소</a>
        <button type="submit" id="btn_submit" class="btn_submit" accesskey="s">저장하기</button>
    </div>
    </form>
</section>

<script>
$(document).ready(function() {
    $('.search_sel').select2();
});
<?php if($write_min || $write_max) { ?>
// 글자수 제한
var char_min = parseInt(<?php echo $write_min; ?>); // 최소
var char_max = parseInt(<?php echo $write_max; ?>); // 최대
check_byte("wr_content", "char_count");

$(function() {
    $("#wr_content").on("keyup", function() {
        check_byte("wr_content", "char_count");
    });
});

<?php } ?>
function html_auto_br(obj)
{
    if (obj.checked) {
        result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
        if (result)
            obj.value = "html2";
        else
            obj.value = "html1";
    }
    else
        obj.value = "";
}

function fwrite_submit(f)
{
    <?php echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>

    var subject = "";
    var content = "";
    $.ajax({
        url: g5_bbs_url+"/ajax.filter.php",
        type: "POST",
        data: {
            "subject": f.wr_subject.value,
            "content": f.wr_content.value
        },
        dataType: "json",
        async: false,
        cache: false,
        success: function(data, textStatus) {
            subject = data.subject;
            content = data.content;
        }
    });

    if (subject) {
        alert("제목에 금지단어('"+subject+"')가 포함되어있습니다");
        f.wr_subject.focus();
        return false;
    }

    if (content) {
        alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
        if (typeof(ed_wr_content) != "undefined")
            ed_wr_content.returnFalse();
        else
            f.wr_content.focus();
        return false;
    }

    if (document.getElementById("char_count")) {
        if (char_min > 0 || char_max > 0) {
            var cnt = parseInt(check_byte("wr_content", "char_count"));
            if (char_min > 0 && char_min > cnt) {
                alert("내용은 "+char_min+"글자 이상 쓰셔야 합니다.");
                return false;
            }
            else if (char_max > 0 && char_max < cnt) {
                alert("내용은 "+char_max+"글자 이하로 쓰셔야 합니다.");
                return false;
            }
        }
    }

    <?php echo $captcha_js; // 캡챠 사용시 자바스크립트에서 입력된 캡챠를 검사함  ?>

    document.getElementById("btn_submit").disabled = "disabled";

    return true;
}
</script>
