<?php
include_once('./_common.php');

if ($is_guest)
  alert('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

if (!$wr_19_s) $wr_19_s = G5_TIME_YMD;
if (!$wr_19_e) $wr_19_e = G5_TIME_YMD;

$count_chk_seq = (isset($_POST['chk_seq']) && is_array($_POST['chk_seq'])) ? count($_POST['chk_seq']) : 0;

if (!$count_chk_seq) alert('최소 1개이상 선택하세요.');

// if (!$_POST['wr_warehouse']) alert('창고가 선택되지 않았습니다.');

// $warehouse = $_POST['wr_warehouse'];
?>

<style>
  .not_item td {
    background: red;
    color: #fff
  }

  th,
  td {
    position: relative;
  }

  .resize-handle {
    position: absolute;
    width: 5px;
    height: 100%;
    top: 0;
    right: -2px;
    cursor: col-resize;
  }

  .tooltip {
    position: relative;
    display: inline-block;
    border-bottom: 1px dotted black;
    /* If you want dots under the hoverable text */
  }

  /* Tooltip text */
  .tooltip .tooltiptext {
    visibility: hidden;
    width: 470px;
    background-color: black;
    color: #fff;
    text-align: center;
    padding: 5px 0;
    border-radius: 6px;

    /* Position the tooltip text - see examples below! */
    position: absolute;
    left: 0;
    top: -40px;
    z-index: 1;
  }

  /* Show the tooltip text when you mouse over the tooltip container */
  .tooltip:hover .tooltiptext {
    visibility: visible;
  }

  .no_ea1,
  .no_ea2,
  .no_ea3,
  .no_ea4,
  .no_ea5,
  .no_ea6,
  .no_ea7 {
    font-weight: 600;
    color: red !important
  }

  .tbl_head03 table {
    clear: both;
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0
  }

  .tbl_head03 thead th {
    background: #6f809a;
    color: #fff;
    border: 1px solid #60718b;
    font-weight: normal;
    text-align: center;
    padding: 8px 5px;
    font-size: 0.92em
  }

  .tbl_head03 thead th a {
    color: #fff
  }

  .tbl_head03 thead input {
    vertical-align: top
  }

  /* middle 로 하면 게시판 읽기에서 목록 사용시 체크박스 라인 깨짐 */
  .tbl_head03 thead a {
    color: #383838;
    text-decoration: underline
  }

  .tbl_head03 tbody th {
    border: 1px solid #d6dce7;
    padding: 5px;
    text-align: center
  }

  .tbl_head03 tbody td {
    border: 1px solid #d6dce7;
    padding: 5px;
    text-align: center
  }

  .tbl_head03 tbody td .frm_input {
    width: 100%;
  }

  .tbl_head03 tbody td select {
    width: 100%
  }

  .tbl_head03 table .tbl_input {
    height: 27px;
    line-height: 25px;
    border: 1px solid #d5d5d5;
    width: 100%
  }

  .tbl_head03 table select {
    height: 27px;
    line-height: 25px;
    width: 100%;
  }

  tr.blank {
    height: 2px;
    background-color: #575757ff;
    padding: 0;
  }

  tr.blank td {
    padding: 0;
  }

  .td_input {
    width: 100%;
    height: 40px;
    text-align: right;
    padding: 0;
    margin: 0;
    font-size: 15px;
  }

  .result_list {
    margin: 10px;
    padding: 20px;
    border: 1px solid #e9e9e9;
    background: #fff;
  }

  .truncate-single-line {
    text-align: left;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  tr.clickable-row {
    cursor: pointer;
    height: 50px;
  }

  tr.clickable-row td {
    background-color: transparent;
  }

  tr.clickable-row:hover {
    background-color: #b1b5f9ff;
  }

  tr.selected-row {
    background-color: #b1b5f9ff;
  }

  .tbl_head03 .selected-item-list {
    padding: 0 0 10px 60px;
    background: darkgrey;
  }
</style>

<div class="new_win">
  <h1><?= $warehouseConfig[$warehouse]['ware_name'] ?> 출고</h1>

  <div id="excelfile_upload" class="result_list" style="overflow-x:auto;max-height:79vh;padding:0;height:50vh">
    <div class="tbl_head03 tbl_wrap" style="min-width:1700px;margin-bottom:10px;background: transparent;">
      <table id="releaseTable" style="table-layout: fixed;">
        <thead style="position:sticky;top:0;z-index:99">
          <tr style="height: 50px;">
            <th style="width:60px">도메인</th>
            <th style="width:150px">주문번호</th>
            <th style="width:100px">SKU</th>
            <th style="width:300px">상품명</th>
            <th style="width:50px">수량</th>
            <th style="width:130px">구매자 이름</th>
            <th style="width:150px">주소</th>
            <th style="width:100px">도시명</th>
            <th style="width:50px">주명</th>
            <th style="width:50px">나라명</th>
            <th style="width:100px">우편번호</th>
            <th style="width:100px">전화번호</th>
            <th style="width:150px">이메일</th>
            <th style="width:50px">박스수</th>
            <th style="width:60px">단가</th>
            <th style="width:60px">신고가격</th>
            <th style="width:50px">통화</th>
            <th style="width:60px">배송코드</th>
            <th style="width:100px">비고</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $seq_arr = array();
          foreach ($_POST['chk_seq'] as $val) {
            $seq_val = isset($val) ? preg_replace('/[^0-9]/', '', $val) : 0;
            if ($seq_val) $seq_arr[] = "'" . $seq_val . "'";
          }

          $seq_arr_str = implode(',', $seq_arr);

          $excel_sql = "SELECT a.*,
                          b.wr_10 AS wr_weight1,
                          b.wr_11 AS wr_weight_dan,
                          b.wr_18 AS wr_weight2,
                          b.wr_19 AS wr_weight3,
                          b.wr_13 AS wr_make_country                                     
                        FROM
                          g5_sales2_list a
                        LEFT JOIN
                          g5_write_product b ON a.wr_product_id = b.wr_id AND b.wr_delYn = 'N'
                        WHERE
                          a.wr_id IN ({$seq_arr_str})";

          $excel_arr_result = sql_fetch_all($excel_sql);
          $json_excel_arr_result = json_encode($excel_arr_result);

          foreach ($excel_arr_result as $i => $excel) { ?>
            <tr class="clickable-row" data-id=<?= $i ?>>
              <td><?= $excel['wr_18'] ?></td>
              <td class="truncate-single-line" style="text-align: left;"><?= $excel['wr_subject'] ?></td>
              <td class="truncate-single-line" style="text-align: left;"><?= $excel['wr_16'] ?></td>
              <td class="truncate-single-line" style="text-align: left;"><?= $excel['wr_17'] ?></td>
              <td id="order_qty_<?= $i ?>" style="color: red; font-size: 15px;"><?= $excel['wr_11'] ?></td>
              <td class="truncate-single-line" style="text-align: left;"><?= $excel['wr_2'] ?></td>
              <td class="truncate-single-line" style="text-align: left;"><?= $excel['wr_3'] ?></td>
              <td class="truncate-single-line" style="text-align: left;"><?= $excel['wr_5'] ?></td>
              <td><?= $excel['wr_6'] ?></td>
              <td><?= $excel['wr_7'] ?></td>
              <td class="truncate-single-line" style="text-align: left;"><?= $excel['wr_8'] ?></td>
              <td class="truncate-single-line" style="text-align: left;"><?= $excel['wr_9'] ?></td>
              <td class="truncate-single-line" style="text-align: left;"><?= $excel['wr_10'] ?></td>
              <td><?= $excel['wr_12'] ?></td>
              <td style="text-align: right;"><?= $excel['wr_13'] ?></td>
              <td style="text-align: right;"><?= $excel['wr_14'] ?></td>
              <td><?= $excel['wr_15'] ?></td>
              <td><?= $excel['wr_20'] ?></td>
              <td style="text-align: left;"><?= $excel['wr_21'] ?></td>
            <tr class="detail-row" id="detail_row_<?= $i ?>">
              <td colspan="19" class='selected-item-list'>
                <table style="width:100%; border-collapse: collapse;">
                  <tbody id="detail_content_<?= $i ?>">
                  </tbody>
                </table>
              </td>
            </tr>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <div id="return_list" class="result_list" style="overflow-x:auto;max-height:39vh;padding:0;height:36vh;">
    <div class="tbl_head03 tbl_wrap" style="min-width:1700px;margin-bottom:10px;background: transparent;">
      <table id="releaseTable" style="table-layout: fixed;">
        <thead style="position:sticky;top:0;z-index:99;background: #437458">
          <tr style="height: 50px;">
            <th style="width:30px"><input type="checkbox" onclick="selectAll(this)"></th>
            <th style="width:40px">도메인</th>
            <th style="width:80px">주문번호</th>
            <th style="width:80px">등록일시</th>
            <th style="width:20px">보유</th>
            <th style="width:40px">수량</th>
            <th style="width:70px">보관랙</th>
            <th style="width:40px">금액</th>
            <th style="width:40px">반품사진</th>
            <th style="width:600px">비고</th>
          </tr>
        </thead>
        <tbody id="returnListBody">
        </tbody>
    </div>
  </div>

  <div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;background:#fff;border-top:1px solid #ddd;margin-bottom:0">
    <span>매출적용 일자</span><input type="date" name="wr_date" value="<?= G5_TIME_YMD ?>" required>
    <span>발주적용 일자</span><input type="date" name="wr_date2" value="<?= G5_TIME_YMD ?>" required>

    <button type="submit" class="btn_submit btn tooltip" id='release_product'><span id="button_label">반품 출고!</span>
      <span class="tooltiptext" id='release_tooltip'>수량이 녹색인 주문건만 처리됩니다</span></button>
    <button type="button" onclick="window.close();" class="btn_close btn" id="closeMainBtn">닫기</button>
  </div>
</div>

<script>
  const json_excel_arr_result = <?= $json_excel_arr_result ?>;
  // console.log('json_excel_arr_result:', json_excel_arr_result);

  const warehouse = <?= $warehouse ?>;
  const salesDataCache = {};
  const quantityObject = {};
  const previousQtyObject = {};
  let currentOrderNum;
  let matchList = [];
  let product_id;
  let cacheKey;

  function pop_img_view(seq) {
    var _width = '1150';
    var _height = '850';

    var _left = Math.ceil((window.screen.width - _width) / 2);
    var _top = Math.ceil((window.screen.height - _height) / 2);

    window.open("./pop_return_img.php?seq=" + seq, "pop_img_view", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

    return false;
  }

  $(document).ready(function() {
    initializeCheckboxesOnNewRows();

    $('#return_list').on('change', 'input.td_input[name="quantity"]', function() {
      const quantityInput = $(this);
      const currentRow = quantityInput.closest('tr');
      const rowDataId = currentRow.data('id');
      let previousValue;

      function handleQuantityUpdate(orderNum, seq, rackName, wr_order, newQty) {
        if (orderNum < 0 || orderNum >= json_excel_arr_result.length) {
          console.error(`오류: 유효하지 않은 orderNum (${orderNum}) 입니다. 배열 범위를 벗어났습니다.`);
          return;
        }

        const targetOrder = json_excel_arr_result[orderNum];

        if (typeof targetOrder !== 'object' || targetOrder === null) {
          console.error(`오류: orderNum ${orderNum} 위치에 유효한 제품 객체가 없습니다.`);
          return;
        }

        targetOrder.orderData ??= {};

        targetOrder.orderData[seq] ??= {};

        if (newQty === 0) {
          if (targetOrder.orderData[seq][rackName] !== undefined) {
            delete targetOrder.orderData[seq][rackName];
            delete targetOrder.orderData[seq]['order_num'];
            console.log(`제품 인덱스 ${orderNum}, seq '${seq}'에서 랙 '${rackName}' 수량 0으로 인해 삭제되었습니다.`);
          } else {
            console.log(`제품 인덱스 ${orderNum}, seq '${seq}'에 랙 '${rackName}'이(가) 존재하지 않습니다. (삭제할 필요 없음)`);
          }

          if (Object.keys(targetOrder.orderData[seq]).length === 0) {
            delete targetOrder.orderData[seq];
            console.log(`제품 인덱스 ${orderNum}에서 seq '${seq}' 객체가 비어있어 삭제되었습니다.`);
          }

        } else {
          targetOrder.orderData[seq][rackName] = newQty;
          targetOrder.orderData[seq]['order_num'] = wr_order;
          console.log(`제품 인덱스 ${orderNum}, seq '${seq}' 랙 '${rackName}' 수량 ${newQty}로 업데이트되었습니다.`);
        }

        if (Object.keys(targetOrder.orderData).length === 0) {
          delete targetOrder.orderData;
          console.log(`제품 인덱스 ${orderNum}의 orderData 객체가 비어있어 삭제되었습니다.`);
        }

        console.log('현재 json_excel_arr_result 상태:', JSON.parse(JSON.stringify(json_excel_arr_result)));
      }

      if (!previousQtyObject[currentOrderNum]) {
        previousQtyObject[currentOrderNum] = {};
      }

      if (previousQtyObject[currentOrderNum][rowDataId] === undefined || previousQtyObject[currentOrderNum][rowDataId] === null) {
        previousValue = 0;
        previousQtyObject[currentOrderNum][rowDataId] = 0;
      } else {
        previousValue = previousQtyObject[currentOrderNum][rowDataId];
      }

      const checkbox = currentRow.find('#chk_' + rowDataId);
      let currentValue = parseInt(quantityInput.val(), 10);

      const differenceQuantity = currentValue - previousValue;
      let availQauntity = parseInt(salesDataCache[cacheKey][rowDataId]['wr_stock'], 10);

      if (availQauntity < differenceQuantity) {
        currentValue = previousValue;
        $(this).val(currentValue);
      } else {
        availQauntity -= differenceQuantity;
      }

      salesDataCache[cacheKey][rowDataId]['wr_stock'] = String(availQauntity);

      const availQtyTarget = currentRow.find('#avail_qty_' + rowDataId);
      availQtyTarget.text(salesDataCache[cacheKey][rowDataId]['wr_stock']);

      quantityObject[currentOrderNum][rowDataId] = currentValue;

      const targetObject = quantityObject[currentOrderNum];
      let totalQuantity = 0;

      if (targetObject) {
        const quantities = Object.values(targetObject);
        totalQuantity = quantities.reduce((prev, current) => prev + current, 0);
      }

      orderQuantity = parseInt(json_excel_arr_result[currentOrderNum]['wr_11'], 10);

      if (totalQuantity === orderQuantity) {
        $(`#order_qty_${currentOrderNum}`).css('color', 'mediumspringgreen');
        if (!matchList.includes(currentOrderNum)) {
          matchList.push(currentOrderNum);
        }
      } else {
        $(`#order_qty_${currentOrderNum}`).css('color', 'red');
        matchList = matchList.filter(item => item !== currentOrderNum);
      }

      handleQuantityUpdate(currentOrderNum, salesDataCache[cacheKey][rowDataId]['seq'],
        salesDataCache[cacheKey][rowDataId]['wr_rack'], salesDataCache[cacheKey][rowDataId]['wr_order_num'], currentValue);

      if (isNaN(currentValue)) {
        checkbox.prop('checked', false);
        return;
      }

      if (currentValue > 0) {
        checkbox.prop('checked', true);
      } else {
        checkbox.prop('checked', false);
      }
      createSelectionHtml(currentOrderNum);

      if (previousQtyObject[currentOrderNum][rowDataId] !== null || previousQtyObject[currentOrderNum][rowDataId] !== undefined) {
        previousQtyObject[currentOrderNum][rowDataId] = currentValue;
      }
    });

    $('#releaseTable').on('click', 'tr.clickable-row', function() {
      $('#releaseTable tr.clickable-row').removeClass('selected-row');
      $(this).addClass('selected-row');

      const id = $(this).data('id');
      currentOrderNum = id;

      if (!quantityObject[id]) {
        quantityObject[id] = {};
      }

      if (!previousQtyObject[id]) {
        previousQtyObject[id] = {};
      }

      product_id = json_excel_arr_result[id].wr_product_id;
      cacheKey = `${product_id}_${warehouse}`;

      if (salesDataCache[cacheKey]) {
        createTableHtml(salesDataCache[cacheKey]);
        createSelectionHtml(currentOrderNum);
        return;
      }

      const dataUrl = './sales0_return_list.php?product_id=' + product_id + '&warehouse=' + warehouse;

      $.getJSON(dataUrl)
        .done(function(data) {
          let tableHtml = '';

          if (data.length > 0) {
            salesDataCache[cacheKey] = data;
          } else {
            salesDataCache[cacheKey] = [];
          }
          createTableHtml(data);
          createSelectionHtml(currentOrderNum);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
          console.error("AJAX 요청 실패:", textStatus, errorThrown);
        });

      const nextDataId = id + 1;
      const $nextRow = $(`tr.clickable-row[data-id="${nextDataId}"]`);

      if ($nextRow.length > 0) {
        $nextRow[0].scrollIntoView({
          behavior: 'smooth',
          block: 'nearest'
        });
      } else {}
    });

    function createTableHtml(data) {
      let tableHtml = '';

      if (data.length > 0) {
        data.forEach(function(row, index) {
          var img_btn = "";
          if (row['wr_img1'] || row['wr_img2'] || row['wr_img3'] || row['wr_img4'] || row['wr_img5']) {
            img_btn = '<a href="javascript:pop_img_view(' + row['img_seq'] + ')">보기</a>';
          }

          const currnetQuantity = quantityObject[currentOrderNum][index] ?? 0;
          const previousQuantity = previousQtyObject[currentOrderNum][index] ?? 0;

          const stock_log = row['state_memo'] ?? '';
          tableHtml += '<tr data-id=' + index + ' style="height: 50px;">'; // 데이터 행 클래스
          tableHtml += '<td><input type="checkbox" name="chk_wr_id[' + index + ']" id="chk_' + index + '" value="' + row['seq'] + '" disabled></td>';
          tableHtml += '<td class="truncate-single-line" style="text-align: center;">' + row['wr_domain'] + '</td>';
          tableHtml += '<td class="truncate-single-line" style="text-align: left;">' + row['wr_order_num'] + '</td>';
          tableHtml += '<td class="truncate-single-line" style="text-align: left;">' + row['img_datetime'] + '</td>';
          tableHtml += '<td class="truncate-single-line" style="text-align: center;" id="avail_qty_' + index + '">' + row['wr_stock'] + '</td>';
          tableHtml += '<td style="text-align: right;"><input type="number" name="quantity" min="0" value="' + currnetQuantity +
            '" data-prev-value="' + previousQuantity + '" class="td_input"></td>';
          tableHtml += '<td class="truncate-single-line" style="text-align: left;">' + row['wr_warehouse'] + ' ' + row['wr_rack'] + '</td>';
          tableHtml += '<td class="truncate-single-line" style="text-align: right;">' + row['price'] + '</td>';
          tableHtml += '<td>' + img_btn + '</td>';
          tableHtml += '<td class="truncate-single-line" style="text-align: left;">' + stock_log + '</td>';
          tableHtml += '</tr>';
        });
      } else {
        tableHtml += '<tr style="height: 50px;"><td colspan="10" style="text-align:center;">선택된 상품에 대한 반품 내역이 없습니다.</td></tr>';
      }

      $('#returnListBody').html(tableHtml);
      initializeCheckboxesOnNewRows();
    }

    function createSelectionHtml(currentRow) {
      let tableHtml = '';

      const product_id = json_excel_arr_result[currentRow].wr_product_id;
      const cacheKey = `${product_id}_${warehouse}`;
      data = salesDataCache[cacheKey];

      const $detailRow = $(`#detail_row_${currentRow}`);
      const $detailContentBody = $(`#detail_content_${currentRow}`);

      data.forEach(function(row, index) {
        const $checkbox = $(`#chk_${index}`);

        if (!$checkbox.is(':checked')) return;

        const currnetQuantity = quantityObject[currentOrderNum][index] ?? 0;
        const stock_log = row['state_memo'] ?? '';

        tableHtml += '<tr data-id=' + index + ' style="height: 30px; background-color: white;top-margin:0;">';
        tableHtml += '<td style="width: 10px">📦</td>';
        tableHtml += '<td style="width: 200px;text-align: left;">' + row['wr_warehouse'] + ' ' + row['wr_rack'] + '</td>';
        tableHtml += '<td style="width: 40px;text-align: center;">' + currnetQuantity + '</td>';
        tableHtml += '<td style="text-align: left;">' + stock_log + '</td>';
        tableHtml += '</tr>';
      });

      $detailContentBody.html(tableHtml);
      $detailRow.show();
    }

    function initializeCheckboxesOnNewRows() {
      $('input.td_input[name="quantity"]').each(function() {
        const quantityInput = $(this);
        const checkbox = quantityInput.closest('tr').find('input[type="checkbox"][name^="chk_wr_id["]');
        const currentValue = parseInt(quantityInput.val(), 10);

        if (!isNaN(currentValue) && currentValue > 0) {
          checkbox.prop('checked', true);
        } else {
          checkbox.prop('checked', false);
        }
      });
    }

    $('#release_product').on('click', function(e) {
      e.preventDefault();
      matchList.sort((a, b) => a - b);
      const orderList = [];

      matchList.forEach(matchedOrderNum => {
        const orderInfo = json_excel_arr_result[matchedOrderNum];

        if (orderInfo) {
          orderList.push(orderInfo);
        }
      });

      const dataToInsert = {
        warehouse,
        orderList
      }

      if (dataToInsert.length === 0) {
        alert("처리할 데이터가 없습니다. 수량을 입력하고 매칭되는 주문건을 확인해주세요.");
        return;
      }
      // console.log("DB에 INSERT할 최종 데이터:", dataToInsert);

      sendDataToPhp(dataToInsert);
    });

    function sendDataToPhp(data) {
      const phpProcessFile = './ajax.return_release.php';

      $.ajax({
        url: phpProcessFile,
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(response) {
          // response는 PHP에서 전송한 JSON이 파싱된 JavaScript 객체입니다.

          // 결과를 조합하여 alert 메시지 구성
          let alertMessage = "작업 결과:\n\n";
          alertMessage += "전체 시도 횟수: " + response.total_loops + "회\n";
          alertMessage += "성공 횟수: " + response.success_count + "회\n";
          alertMessage += "실패 횟수: " + response.failed_count + "회\n\n";
          alertMessage += response.message;

          // alert 창으로 결과 출력
          alert(alertMessage);
        },
        // HTTP 2xx 외의 응답 (예: 404, 500) 또는 네트워크 오류 시
        error: function(jqXHR, textStatus, errorThrown) {
          let errorMessage = "오류 발생! 서버와 통신할 수 없습니다.";
          if (jqXHR.responseText) {
            try {
              const errorResponse = JSON.parse(jqXHR.responseText);
              errorMessage = "서버 오류: " + (errorResponse.message || "상세 메시지 없음");
            } catch (e) {
              // JSON 파싱 실패 시 원본 응답 텍스트 표시
              errorMessage = "서버 응답 파싱 실패: " + jqXHR.responseText;
            }
          }
          alert(errorMessage);
          console.error('AJAX Error:', jqXHR, textStatus, errorThrown);
        },
        complete: function() {
          if (window.opener && !window.opener.closed && window.location.origin === window.opener.location.origin) {
            try {
              $(window.opener.document).find('#closeMainBtn').trigger('click');
            } catch (e) {
              console.error('부모 창의 버튼 클릭 중 오류 발생:', e);
              alert('부모 창 버튼 클릭 시도 중 오류가 발생했습니다. 브라우저 보안 정책을 확인하세요.');
            }
          } else {
            alert('팝업에서 부모 창을 제어할 수 없습니다. (직접 열린 창이거나 도메인이 다릅니다)');
          }
          window.close();
        }
      });
    }
  });
</script>

<?php
include_once(G5_PATH . '/tail.sub.php');
