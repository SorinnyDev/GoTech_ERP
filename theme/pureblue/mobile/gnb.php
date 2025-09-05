<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<div class="gnb1 gnb_menu swiper-container" id="swipe_gnb_menu">
    <ul class="gnb_1dul swiper-wrapper">
        <?php
        $gnb_zindex = 999; // gnb_1dli z-index 값 설정용
		$menu_datas = get_menu_db(1, true);
		$i = 0;
		foreach( $menu_datas as $row ){
			if( empty($row) ) continue;
        ?>
        <li class="gnb_1dli" style="z-index:<?php echo $gnb_zindex--; ?>">
            <a href="<?php echo $row['me_link']; ?>" target="_<?php echo $row['me_target']; ?>" class="gnb_1da"><?php echo $row['me_name'] ?></a>
        </li>
        <?php
        $i++;
        }   //end foreach $row

        if ($i == 0) {  ?>
            <li class="gnb_empty">메뉴 준비 중입니다.<?php if ($is_admin) { ?> <a href="<?php echo G5_ADMIN_URL; ?>/menu_list.php">관리자모드 &gt; 환경설정 &gt; 메뉴설정</a>에서 설정하실 수 있습니다.<?php } ?></li>
        <?php } ?>
    </ul>
</div>
