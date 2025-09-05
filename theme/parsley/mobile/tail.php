<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

		</div>
		<div class="conri" style="display:none">
			<!--  공지사항 최신글 { -->
		    <?php
		    // 이 함수가 바로 최신글을 추출하는 역할을 합니다.
		    // 사용방법 : latest(스킨, 게시판아이디, 출력라인, 글자수);
		    // 테마의 스킨을 사용하려면 theme/basic 과 같이 지정
		    echo latest('theme/notice', 'notice', 2, 23);
		    ?>
		    
		    <!-- } 공지사항 최신글 끝 -->
			<div class="notice">
			<h2>오늘 일정</h2>
			<ul>
		
		
			<li class="empty_li">등록 된 일정이 없습니다.</li>
		
			</ul>
		</div>
		</div>
	</div>
	<!-- } container 끝 -->

	<footer id="footer">
	    <div id="ft_copy">
	        <div id="ft_company">
	           
	        </div>
	        Copyright &copy; <b>GOTECH</b> All rights reserved.<br>
	    </div>
	    <button type="button" id="top_btn"><i class="fa fa-arrow-up" aria-hidden="true"></i><span class="sound_only">상단으로</span></button>

	    <script>
        $(function() {
            // 폰트 리사이즈 쿠키있으면 실행
            font_resize("html_wrap", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));

            // '상단으로' 버튼
            $("#top_btn").on("click", function() {
                $("html, body").animate({scrollTop:0}, '500');
                return false;
            });
        });
        </script>
            
	    <?php	
	    if ($config['cf_analytics']) {
	        echo $config['cf_analytics'];
	    }
	    ?>
	</footer>
	</div>
	<!-- } wrapper 끝 -->
</div>
<!-- } 컨텐츠 끝 -->
	
<?php
include_once(G5_THEME_PATH."/tail.sub.php");
?>