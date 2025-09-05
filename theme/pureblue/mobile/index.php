<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (!defined('_INDEX_')) define('_INDEX_', true);


include_once(G5_THEME_MOBILE_PATH.'/head.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

add_javascript('<script src="'.G5_JS_URL.'/jquery.bxslider.js"></script>', 10);
?>

<!-- 메인 최신글 시작 -->
<div id="index_content">
	<!--  배너 최신글 { -->
    <?php
    // 이 함수가 바로 최신글을 추출하는 역할을 합니다.
    // 사용방법 : latest(스킨, 게시판아이디, 출력라인, 글자수);
    // 테마의 스킨을 사용하려면 theme/basic 과 같이 지정
    echo latest('theme/gallery', 'gallery', 4, 23);
    ?>
    <!-- } 배너 최신글 끝 -->

	<!--  공지사항 최신글 { -->
    <?php
    // 이 함수가 바로 최신글을 추출하는 역할을 합니다.
    // 사용방법 : latest(스킨, 게시판아이디, 출력라인, 글자수);
    // 테마의 스킨을 사용하려면 theme/basic 과 같이 지정
    echo latest('theme/notice', 'notice', 4, 23);
    ?>
    <!-- } 공지사항 최신글 끝 -->
    
	<div>
	<?php
	//  최신글
	$sql = " select bo_table
	            from `{$g5['board_table']}` a left join `{$g5['group_table']}` b on (a.gr_id=b.gr_id)
	            where a.bo_device <> 'mobile' ";
	if(!$is_admin) 
	$sql .= " and a.bo_use_cert = '' ";
	$sql .= " and a.bo_table not in ('notice', 'gallery', 'qa') ";     //공지사항과 갤러리 게시판은 제외
	$sql .= " order by b.gr_order, a.bo_order ";
	$result = sql_query($sql);
	for ($i=0; $row=sql_fetch_array($result); $i++) {
	    // 이 함수가 바로 최신글을 추출하는 역할을 합니다.
	    // 스킨은 입력하지 않을 경우 관리자 > 환경설정의 최신글 스킨경로를 기본 스킨으로 합니다.
	    // 사용방법
	    // latest(스킨, 게시판아이디, 출력라인, 글자수);
	    echo latest('theme/basic', $row['bo_table'], 5, 25);
	}
	?>
	</div>
    <div class="gal_tab tabs" style="float:left;width:65%;">
		<div class="pic_tab_heading" role="tablist" aria-label="Entertainment">
			<button role="tab" aria-selected="true" aria-controls="nils-tab" id="nils">게시판1</button>
			<button role="tab" aria-selected="false" aria-controls="agnes-tab" id="agnes" tabindex="-1">게시판2</button>
			<a href="" class="lt_more">더보기</a>
		</div>

		<div tabindex="0" role="tabpanel" id="nils-tab" aria-labelledby="nils">
			<!-- 탭 갤러리 최신글 1 { -->
		    <?php
		    // 이 함수가 바로 최신글을 추출하는 역할을 합니다.
		    // 사용방법 : latest(스킨, 게시판아이디, 출력라인, 글자수);
		    // 테마의 스킨을 사용하려면 theme/basic 과 같이 지정
		    echo latest('theme/pic_basic', 'gallery', 6, 23);
		    ?>
		    <!-- } 탭 갤러리 최신글1 끝 -->
		</div>

		<div tabindex="0" role="tabpanel" id="agnes-tab" aria-labelledby="agnes" hidden="">
			<!-- 탭 갤러리 최신글2 { -->
		    <?php
		    // 이 함수가 바로 최신글을 추출하는 역할을 합니다.
		    // 사용방법 : latest(스킨, 게시판아이디, 출력라인, 글자수);
		    // 테마의 스킨을 사용하려면 theme/basic 과 같이 지정
		    echo latest('theme/pic_basic', 'free', 6, 23);
		    ?>
		    <!-- } 탭 갤러리 최신글2 끝 -->
	    </div>
    </div>

    <div class="lt" style="float:left;width:35%;">
    	<div class="lt_slider">
    		<!-- 베이직 슬라이더1 { -->
			<?php
		    // 이 함수가 바로 최신글을 추출하는 역할을 합니다.
		    // 사용방법 : latest(스킨, 게시판아이디, 출력라인, 글자수);
		    // 테마의 스킨을 사용하려면 theme/basic 과 같이 지정
		    echo latest('theme/basic_slider', 'free', 6, 23);
		    ?>
		    <!-- } 베이직 슬라이더1 끝 -->
			<!-- 베이직 슬라이더2 { -->
			<?php
		    // 이 함수가 바로 최신글을 추출하는 역할을 합니다.
		    // 사용방법 : latest(스킨, 게시판아이디, 출력라인, 글자수);
		    // 테마의 스킨을 사용하려면 theme/basic 과 같이 지정
		    echo latest('theme/basic_slider', 'gallery', 6, 23);
		    ?>
			<?php
		    // 이 함수가 바로 최신글을 추출하는 역할을 합니다.
		    // 사용방법 : latest(스킨, 게시판아이디, 출력라인, 글자수);
		    // 테마의 스킨을 사용하려면 theme/basic 과 같이 지정
		    echo latest('theme/basic_slider', 'qa', 6, 23);
		    ?>
		    <!-- } 베이직 슬라이더2 끝 -->
		</div>
	</div>
</div>
<!-- 메인 최신글 끝 -->

<div class="index_menu">
	<?php echo poll("theme/basic"); // 설문조사 ?>
	<?php echo popular("theme/basic"); // 인기검색어, 테마의 스킨을 사용하려면 스킨을 theme/basic 과 같이 지정  ?>
	<?php echo visit("theme/basic"); // 방문자수 ?>
</div>

<!-- 최신글 탭 스타일 -->
<script src="<?php echo G5_THEME_JS_URL ?>/latest_tab.js"></script>
<!-- 게시판 슬라이더 -->
<script>
$('.lt_slider').each(function(){
	$(this).bxSlider({
		pager:true,
		hideControlOnEnd: true,
		nextText: '<i class="fa fa-angle-right" aria-hidden="true"></i>',
		prevText: '<i class="fa fa-angle-left" aria-hidden="true"></i>'
	});
});
</script>

<?php
include_once(G5_THEME_MOBILE_PATH.'/tail.php');
?>