<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$latest_skin_url.'/style.css">', 0);
$list_count = count($list);
$start_page_num = $list_count ? '1' : '0';
$is_show_next_prev = ($list_count > 4) ? 1 : 0;
?>

<div class="lt_basic">
    <div class="lt_basic_inner">
    <h2 class="lt_title"><strong><a href="<?php echo get_pretty_url($bo_table); ?>" class="lt_title"><?php echo $bo_subject ?></strong></a></h2>
    <div class="<?php echo $list_count ? 'latest-sel' : ''; ?>">
	    <ul>
	    <?php for ($i=0; $i<count($list); $i++) { ?>
	        <li>
	            <?php
	            if ($list[$i]['icon_secret']) echo "<i class=\"fa fa-lock\" aria-hidden=\"true\"></i> ";
	            //echo $list[$i]['icon_reply']." ";
	            echo "<a href=\"".$list[$i]['href']."\" class=\"lt_tit\">";
	            if ($list[$i]['is_notice'])
	                echo "<strong>".$list[$i]['subject']."</strong>";
	            else
	                echo $list[$i]['subject'];
	
	                // if ($list[$i]['link']['count']) { echo "[{$list[$i]['link']['count']}]"; }
	                // if ($list[$i]['file']['count']) { echo "<{$list[$i]['file']['count']}>"; }
	
	            if ($list[$i]['icon_new']) echo " <span class=\"new_icon\">N<span class=\"sound_only\">새글</span></span>";
	            if ($list[$i]['icon_file']) echo " <i class=\"fa fa-download\" aria-hidden=\"true\"></i>" ;
	            if ($list[$i]['icon_link']) echo " <i class=\"fa fa-link\" aria-hidden=\"true\"></i>" ;
	            if ($list[$i]['icon_hot']) echo " <i class=\"fa fa-heart\" aria-hidden=\"true\"></i>";
	
	            echo "</a>";
	
	            ?>
	            <div class="lt_info">
	                <?php echo $list[$i]['name'] ?>
	                <span class="lt_date"><?php echo $list[$i]['datetime'] ?></span>
	            	<span class="lt_comnt"><?php if ($list[$i]['comment_cnt']) { ?><span class="sound_only">댓글</span><i class="fa fa-commenting-o" aria-hidden="true"></i><?php echo $list[$i]['comment_cnt']; ?><span class="sound_only">개</span><?php } ?>
	            </div>
	        </li>
	    <?php } ?>
	    <?php if ($list_count == 0) { //게시물이 없을 때 ?>
	    <li class="empty_li">게시물이 없습니다.</li>
	    <?php } ?>
	    </ul>
    </div>
    <a href="<?php echo get_pretty_url($bo_table); ?>" class="lt_more"><span class="sound_only"><?php echo $bo_subject ?></span>더보기</a>
	</div>
</div>
