<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<script>
  document.querySelectorAll('.resize-handle').forEach(function(resizeHandle) {
    resizeHandle.addEventListener('mousedown', function(event) {
      let startX = event.clientX;
      let td = this.parentElement;
      let initialWidth = td.offsetWidth;

      function resize(event) {
        let deltaX = event.clientX - startX;
        td.style.width = initialWidth + deltaX + 'px';
      }

      function stopResize() {
        document.removeEventListener('mousemove', resize);
        document.removeEventListener('mouseup', stopResize);
      }

      document.addEventListener('mousemove', resize);
      document.addEventListener('mouseup', stopResize);
    });
  });
  
const mask = document.querySelector('.mask');
const html = document.querySelector('html');

html.style.overflow = 'hidden'; //로딩 중 스크롤 방지
window.addEventListener('load', function () {
  //아래 setTimeout은 로딩되는 과정을 임의로 생성하기 위해 사용. 실제 적용 시에는 삭제 후 적용해야함.

    mask.style.opacity = '0'; //서서히 사라지는 효과
    html.style.overflow = 'auto'; //스크롤 방지 해제
    mask.style.display = 'none';

})
</script>
<?php if ($is_admin == 'super') {  ?><!-- <div style='float:left; text-align:center;'>RUN TIME : <?php echo get_microtime()-$begin_time; ?><br></div> --><?php }  ?>

<!-- ie6,7에서 사이드뷰가 게시판 목록에서 아래 사이드뷰에 가려지는 현상 수정 -->
<!--[if lte IE 7]>
<script>
$(function() {
    var $sv_use = $(".sv_use");
    var count = $sv_use.length;

    $sv_use.each(function() {
        $(this).css("z-index", count);
        $(this).css("position", "relative");
        count = count - 1;
    });
});
</script>
<![endif]-->

<?php run_event('tail_sub'); ?>

</body>
</html>
<?php echo html_end(); // HTML 마지막 처리 함수 : 반드시 넣어주시기 바랍니다. ?>