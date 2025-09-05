<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_THEME_PATH.'/js/head_js.php');

if ((stripos($_SERVER['REQUEST_URI'], 'register') !== false) || !(defined("_DONT_WRAP_IN_CONTAINER_") && _DONT_WRAP_IN_CONTAINER_ === true)) {
?>

<header id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>

    <div class="to_content"><a href="#container">본문 바로가기</a></div>
	<div id="mobile-indicator"></div>
    <?php
    if(defined('_INDEX_')) { // index에서만 실행
        include G5_MOBILE_PATH.'/newwin.inc.php'; // 팝업레이어
    } ?>

    <div id="hd_wrapper">
		
		<div class="m_side_gnb">
			<button class="gnb_side"><i class="fa fa-bars"></i><span class="sound_only">전체메뉴</span></button>
	    	<div id="m_sch">
	        	<button class="sch_more">
		        	<i class="fa fa-search"></i>
		        </button>
		        <fieldset id="m_hd_sch"></fieldset>
	        </div>
		</div>
		
        <div id="logo">
        	<div class="logo_inner">
            	<a href="<?php echo G5_URL ?>">
            		<span class="sound_only"><?php echo $config['cf_title']; ?></span>
            		<img src="<?php echo G5_IMG_URL ?>/logo.png" alt="<?php echo $config['cf_title']; ?>">
            	</a>
        	</div>
        </div>
		
		<div class="header_ct">
			<div class="header_inner">
		        <div class="hd_sch_wr">
		            <fieldset id="hd_sch"></fieldset>
		        </div>
		        <div id="tnb">
		        	<?php echo outlogin("theme/basic"); ?>
			    </div>
        	</div>
        </div>
        
	</div>
</header>
	<!-- } 상단 끝 -->       


<div id="wrapper">
	
	<aside id="con_left">
		<div class="con_left_inner">
            <div id="gnb">
            	<div class="gnb_side">
        			<h2>메인메뉴</h2>
					<ul class="gnb_1dul">
                    <?php
					$menu_datas = get_menu_db(1, true);
					$i = 0;
					foreach( $menu_datas as $row ){
						if( empty($row) ) continue;
                    ?>
                        <li class="gnb_1dli">
                            <a href="<?php echo $row['me_link']; ?>" target="_<?php echo $row['me_target']; ?>" class="gnb_1da"><?php echo $row['me_name'] ?></a>
                            <?php
							$k = 0;
							foreach( (array) $row['sub'] as $row2 ){
								if( empty($row2) ) continue;

                                if($k == 0)
                                    echo '<button class="btn_gnb_op">하위분류</button><ul class="gnb_2dul">'.PHP_EOL;
                            ?>
                                <li class="gnb_2dli"><a href="<?php echo $row2['me_link']; ?>" target="_<?php echo $row2['me_target']; ?>" class="gnb_2da"><?php echo $row2['me_name'] ?></a></li>
                            <?php
							$k++;
                            }	//end foreach $row2

                            if($k > 0)
                                echo '</ul>'.PHP_EOL;
                            ?>
                        </li>
                    <?php
					$i++;
                    }	//end foreach $row

                    if ($i == 0) {  ?>
                        <li id="gnb_empty">메뉴 준비 중입니다.<?php if ($is_admin) { ?> <br><a href="<?php echo G5_ADMIN_URL; ?>/menu_list.php">관리자모드 &gt; 환경설정 &gt; 메뉴설정</a>에서 설정하세요.<?php } ?></li>
                    <?php } ?>
                    </ul>
	            </div>
			</div>
			<ul class="shortcut">
                <li><a href="<?php echo G5_BBS_URL ?>/faq.php"><i class="fa fa-question-circle"></i> 자주묻는 질문</a></li>
                <li><a href="<?php echo G5_BBS_URL ?>/qalist.php"><i class="fa fa-comments"></i> 1:1문의</a></li>
                <li class="hd_visit">
                    <a href="<?php echo G5_BBS_URL ?>/current_connect.php"><i class="fa fa-users"></i> 접속자 <span class="visit_num"><?php echo connect("theme/basic"); // 현재 접속자수 ?></span></a>
                </li>
                <li><a href="<?php echo G5_BBS_URL ?>/new.php"><i class="fa fa-history"></i> 새글</a></li>
            </ul>
		</div>
        <div id="bg"></div>
	</aside>

	<?php //echo $side_menu_html; ?>
		
	<!-- con_right 시작 { -->
	<div id="con_right">
		<div id="container">
		<?php } // if (!defined("_DONT_WRAP_IN_CONTAINER_")) 의 끝 ?>
		<?php if (!defined("_INDEX_") && !(defined("_H2_TITLE_") && _H2_TITLE_ === true)) {?>
        <h2 id="container_title" class="top">
        	<?php echo get_head_title($g5['title']) ?>
        </h2>
        <?php } ?>
