<?php
include_once('./_common.php');
include_once(G5_THEME_PATH . '/head.php');

if (!$st_date) {
  $st_date = date('Y-m-01');
}

if (!$ed_date) {
  $ed_date = date('Y-m-d');
}

$where = ' and b.is_deleted = false ';

if ($ordernum) {
  $where .= " and bo.wr_order_num = '$ordernum' ";
}

if ($box_code) {
  $where .= " and b.box_code like '%$box_code%' ";
}


$query = "
select b.id, b.box_code, b.reg_datetime, b.is_receipt, sum(wr_ea) as 'wr_ea',
       case
           when b.is_receipt = true and b.receipt_datetime is null then '수령완료 (데이터 생성 전)'
           when b.is_receipt = true and b.receipt_datetime is not null then b.receipt_datetime
           else ''
           end as 'receipt_datetime' from g5_stock_box as b
left join g5_stock_box_order as bo on b.box_code = bo.box_code and bo.is_deleted = false
left join g5_sales3_list as s3 on s3.wr_order_num = bo.wr_order_num        
where (1) and b.is_transfer = true and b.reg_datetime between '$st_date 00:00:00' and '$ed_date 23:59:59' {$where}
group by b.id
order by b.reg_datetime desc
";

$result = sql_fetch_all($query);

$list = $result;

?>
  <link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
  <link rel="stylesheet" href="/css/mangolabs.css?ver=<?= date('Y-m-d H:i:s') ?>">
  <style>
      td {
          text-align: center;
      }
  </style>
  <div id="bo_list">
    <div class="bo_list_innr">
      <h2 class="board_tit">미국 기타이관</h2>
      <div id="bo_li_top_op">
        <div class="bo_list_total">
          <div class="local_ov01 local_ov">
            <span class="btn_ov01">
              &nbsp;
            </span>
          </div>
        </div>
        <ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?>">
          <li>
            <button type="button" class="btn_b01 btn_bo_sch">
              <i class="fa fa-search" aria-hidden="true"></i>검색
            </button>
          </li>
        </ul>
      </div>
      <div>
        <div class="tbl_head01 tbl_wrap">
          <table>
            <thead>
            <tr>
              <th>박스번호</th>
              <th>상품수량</th>
              <th style="width: 220px;">생성일자</th>
              <th style="width: 220px;">수령일자</th>
              <th style="width: 270px;"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($list as $k => $item) { ?>
            <tr>
              <td><?= $item['box_code'] ?></td>
              <td><?= $item['wr_ea'] ? number_format($item['wr_ea']) : '없음' ?></td>
              <td><?= $item['reg_datetime'] ?></td>
              <td><?= $item['receipt_datetime'] ?></td>
              <td>

                <?php if ($item['is_receipt']) { ?>
                  <a href="#" class="btn btn_b02" style="background: #f2f2f2;">수령완료</a>
                <?php } else { ?>
                  <a href="#" onclick="processBox('<?= $item['id'] ?>')" class="btn btn_b01">수령진행</a>
                <?php } ?>
                <a href="#" onclick="openPopup('<?= $item['box_code'] ?>')" class="btn btn_b02">상세보기</a>
                <a href="#" onclick="deleteBox('<?= $item['box_code'] ?>')" class="btn btn_b02" style="background: #FF3746; color: white;">삭제</a>
              </td>
            </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="bo_sch_wrap">
    <fieldset class="bo_sch">
      <h3>검색</h3>
      <form name="fsearch" method="get">
        <div style="margin-bottom:15px;">
          <input type="text" name="ordernum" value="<?= $ordernum ?>" class="frm_input" style="width:100%;" placeholder="주문번호 검색">
        </div>
        <div style="margin-bottom:15px;">
          <input type="text" name="box_code" value="<?= $box_code ?>" class="frm_input" style="width:100%;" placeholder="박스번호 검색">
        </div>
        <strong>박스 등록일자 조회</strong>
        <div class="sch_bar" style="margin-top:3px">
          <input type="date" name="st_date" value="<?php echo $st_date ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
          <span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
          <input type="date" name="ed_date" value="<?php echo $ed_date ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
        </div>
        <button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
        <button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'">
          <i class="fa fa-repeat" aria-hidden="true"></i> 검색초기화
        </button>
        <button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
      </form>
    </fieldset>
    <div class="bo_sch_bg"></div>
  </div>

  <script>
    $(function () {
      $(".btn_bo_sch").on("click", function () {
        $(".bo_sch_wrap").toggle();
      })
      $('.bo_sch_bg, .bo_sch_cls').click(function () {
        $('.bo_sch_wrap').hide();
      });
    });

    function createBox() {
      if (!confirm("신규 박스를 생성하겠습니까?")) {
        return;
      }

      $.post(
        "./ajax.create_box.php",
        {  },
        (response) => {
          if (response.result) {
            location.reload();
          }
        }, 'json'
      );
    }

    function processBox(id) {
      if (!confirm("이관진행하시겠습니까?")) {
        return;
      }

      $.post(
        "./ajax.usa_process_box.php",
        { id },
        (response) => {
          if (response.result) {
            location.reload();
          }
        }, 'json'
      );
    }

    function deleteBox(box_code) {
      if (!confirm("해당 박스를 삭제하겠습니까?")) {
        return;
      }

      $.post(
        "./ajax.delete_box.php",
        { box_code: box_code },
        (response) => {
          if (response.result) {
            location.reload();
          }
        }, 'json'
      );

    }

    function openPopup(box_code) {
      window.open("./pop_stock_box_order_list.php?box_code="+box_code, "box_code", "width=800,height=500,scrollbars=yes,resizable=yes");
    }

  </script>


<?php
include_once(G5_THEME_PATH . '/tail.php');
