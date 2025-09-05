<script>
function fsearchbox_submit(f)
{
    if (f.stx.value.length < 2) {
        alert("검색어는 두글자 이상 입력하십시오.");
        f.stx.select();
        f.stx.focus();
        return false;
    }

    // 검색에 많은 부하가 걸리는 경우 이 주석을 제거하세요.
    var cnt = 0;
    for (var i=0; i<f.stx.value.length; i++) {
        if (f.stx.value.charAt(i) == ' ')
            cnt++;
    }

    if (cnt > 1) {
        alert("빠른 검색을 위하여 검색어에 공백은 한개만 입력할 수 있습니다.");
        f.stx.select();
        f.stx.focus();
        return false;
    }

    return true;
}

// 반응형일 떄 모바일 모드인지 알아 본다.
function is_mobile_width() {
    return $('#mobile-indicator').is(':visible');
}

// 윈도우 리사이즈 전의 상태를 기록한 변수
var before_status = '';

// 반응형 메뉴를 추가한다.
function response_menu()
{
    var current_status = is_mobile_width() ? 'mobile' : 'pc';

    if(before_status != current_status) {
        if(current_status == 'mobile') {
            // 모바일 모드
            $("#con_left").css('display', 'none');
            ajax_get_search_bar('m_hd_sch', 'head');
            $("#hd_sch").html('');

            // 사이드 메뉴 닫기 버튼을 추가
            $("#gnb").prepend('<button id="gnb_close" class="hd_closer"><span class="sound_only">메뉴닫기</span><i class="fa fa-times" aria-hidden="true"></i></button>');
        } else {
            // PC 모드
            $("#con_left").css('display', 'block');
            ajax_get_search_bar('hd_sch');
            $("#m_hd_sch").html('');
            // 사이드 메뉴 닫기 버튼을 삭제
            $("#gnb_close").remove();
        }

        // show_popular_search_word();
        before_status = current_status;
    }
}

// 검색 바를 ajax로 가져온다.
function ajax_get_search_bar(id, place)
{
    var $el = $("#" + id);

        $.ajax({
        url: g5_theme_mobile_url + "/ajax.searchbar.php",
        type: 'post',
        data: {
            'place': place,
            'theme': '<?php echo $theme ?>'
        },
        datatype: 'json',
        async: false,
        cache: false,
        success: function(data) {
            $el.html(data);
        }
    });
}

// 인기검색어의 유무에 따라 인기검색어 틀을 보여준다.
function show_popular_search_word()
{
    if($("#popular div a").length > 0) {
        $("#popular").show();
    } else {
        $("#popular").hide();
    }
}

// 브라우저 크기 resize()
$(window).resize(function (){
    response_menu();
});

$(function ($) {

    $(document).on("click", ".sch_more", function() {
        // 검색바 토글
        $("#m_hd_sch").toggle();
    });

    $(document).on("click", ".sch_more_close", function() {
        $("#m_hd_sch").hide();
    });

    response_menu();

    $("#container").on("click", function(e) {
        if($("#gnb_close").length == 1) {
            $("#con_left").css('display', 'none');
        }
    }).on("click_font_resize", function(e) {

        var $this = $(this),
            $text_size_button = $("#text_size button");

        $text_size_button.removeClass("select");

        if( $this.hasClass("ts_up") ){
            $text_size_button.eq(1).addClass("select");
        } else if ( $this.hasClass("ts_up2") ) {
            $text_size_button.eq(2).addClass("select");
        } else {
            $text_size_button.eq(0).addClass("select");
        }
    });

    $(".btn_gnb_op").click(function(e){
        $(this).toggleClass("btn_gnb_cl").next(".gnb_2dul").slideToggle(300);

    });

    // 사이드 메뉴 닫기 버튼 클릭
    $(document).on("click", ".hd_closer", function() {
         $("#con_left").css('display', 'none');
    });

    // 사이드 메뉴 외의 영역 클릭시 사이드 메뉴 감춤
    $(document).on("click", "#bg", function(e) {
         $("#con_left").css('display', 'none');
    });

    // 접속자
    $(document).on("click", ".hd_visit button", function() {
        $('.hd_visit button').toggle(function(){
            $(this).addClass('visit_close');
            $(this).next('div.oa_open').removeClass('oa_close');
        },function(){
            $(this).removeClass('visit_close');
            $(this).next('div.oa_open').addClass('oa_close');
        });
    });

    // 사이드 메뉴 버튼 클릭 (모바일 모드)
    $(document).on("click", "button[class='gnb_side']", function() {
        if($("#con_left").css('display') == 'block') {
            $("#con_left").css('display', 'none');
        } else {
            $("#con_left").css('display', 'block');
        }
    });

});
</script>
