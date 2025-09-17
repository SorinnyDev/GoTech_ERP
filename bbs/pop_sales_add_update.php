<?php
include_once('./_common.php');

// 제품이 많을 경우 대비 설정변경
set_time_limit(0);
ini_set('memory_limit', '50M');

function only_number($n)
{
  return preg_replace('/[^0-9]/', '', (string)$n);
}

// 알파벳 시퀀스 생성 함수 (A, B, ..., Z, AA, AB, AC, ...)
function getAlphabetSequence($num) {
  $result = '';
  while ($num > 0) {
    $num--;
    $result = chr(65 + ($num % 26)) . $result;
    $num = intval($num / 26);
  }
  return $result;
}


$is_upload_file = (isset($_FILES['excelfile']['tmp_name']) && $_FILES['excelfile']['tmp_name']) ? 1 : 0;

if (!$is_upload_file) {
  alert("엑셀 파일을 업로드해 주세요.");
}

if (!$domain)
  alert('업로드하실 플랫폼을 선택하셔야 합니다.');

if ($is_upload_file) {


  $file = $_FILES['excelfile']['tmp_name'];


  include_once(G5_LIB_PATH . '/PHPExcel/IOFactory.php');

  $objPHPExcel = PHPExcel_IOFactory::load($file);
  $sheet = $objPHPExcel->getSheet(0);

  $num_rows = $sheet->getHighestRow();
  $highestColumn = $sheet->getHighestColumn();

  $savedir = "/home/gotec/www/upload/" . date("Ymd") . "/";
  FileUploadName("", $savedir, $file, $_FILES['excelfile']['name'], "");

  $dup_it_id = array();
  $fail_it_id = array();
  $fail_over = array();
  $dup_count = 0;
  $total_count = 0;
  $fail_count = 0;
  $succ_count = 0;

  //시작열 설정
  /*if(in_array($domain, array("dodoskin", "AC-CAD", "AJP", "WM"))) {
    $start_row = 2;
  } else if($domain == "Ebay" || $domain == "Ebay-dodoskin") {
    $start_row = 4;
  }*/
  if ($domain == "Ebay") {
    $start_row = 1;
    $ebay_arr = array();
  } else if ($domain == "Ebay-dodoskin") {
    $start_row = 1;
    $ebay_arr = array();
  } else if ($domain == "ACF-CAD" || $domain == "ACF-CAD_F" || $domain == "ACF-CAD_I") {
    $start_row = 9;
  } else if ($domain == "AC" || $domain == "ACF") {
    $start_row = 1;
  } else {
    $start_row = 2;
  }

  for ($i = $start_row; $i <= $num_rows; $i++) {
    $total_count++;

    $j = 0;

    $rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,
      NULL,
      FALSE,
      FALSE);

    if ($domain == "dodoskin") {

      $wr_subject = addslashes($rowData[0][0]); //주문번호
      $wr_10 = addslashes($rowData[0][1]); //이메일
      $wr_15 = addslashes($rowData[0][7]); //통화
      $wr_13 = addslashes($rowData[0][18]); //단가

      $shippingmethod = trim(addslashes($rowData[0][14])); //배송코드
      $wr_11 = addslashes($rowData[0][16]); //수량
      $wr_17 = addslashes($rowData[0][17]); //상품명
      $wr_16 = addslashes($rowData[0][20]); //상품코드(sku)
      $wr_2 = addslashes($rowData[0][24]); //구매자이름
      $wr_3 = addslashes($rowData[0][26]); //주소1
      $wr_4 = addslashes($rowData[0][27]); //주소2
      $wr_5 = addslashes($rowData[0][29]); //도시명
      $wr_8 = addslashes($rowData[0][30]); //우편번호
      $wr_6 = addslashes($rowData[0][31]); //주명
      $wr_7 = addslashes($rowData[0][32]); //나라명
      $wr_9 = addslashes($rowData[0][33]); //전화번호
      $wr_18 = $domain; //도메인명
      $wr_12 = addslashes($rowData[0][16]); //박스수

      $wr_21 = addslashes($rowData[0][81]); //비고
      $shipping = addslashes($rowData[0][9]); //shipping J열
      $taxes = addslashes($rowData[0][10]); //taxes K열
      $discount = addslashes($rowData[0][13]); //discount amount

      # 배송 관련 정보 추가
      $wr_27 = addslashes($rowData[0][34]); // 배송 수령자명
      $wr_28 = addslashes($rowData[0][36]); // 배송지 주소1
      $wr_29 = addslashes($rowData[0][37]); // 배송지 주소2
      $wr_30 = addslashes($rowData[0][39]); // 배송지 도시명
      $wr_31 = addslashes($rowData[0][41]); // 배송지 주명
      $wr_32 = addslashes($rowData[0][42]); // 배송지 나라명
      $wr_33 = addslashes($rowData[0][40]); // 배송지 우편번호
      $wr_34 = addslashes($rowData[0][43]); // 배송 수령자 연락처

      $paymethod = $rowData[0][47]; // 결제 방법 AV열 ( 결제 방법에 따른 수수료1 계산이 다름)

      if (!$shipping) $shipping = 0;
      if (!$taxes) $taxes = 0;
      if (!$discount) $discount = 0;

      $wr_13 = (float)$rowData[0][18]; // (L열-K열)÷Q열
      $wr_14 = ((float)$rowData[0][18] * (float)$rowData[0][16])  + ($shipping + $taxes) - $discount; //신고가격 (단가 * 수량) + (shipping + taxes) - discount
      #$wr_14             		 = ($wr_13 * $wr_11) + ($shipping + $taxes) - $discount; //신고가격 (단가 * 수량) + (shipping + taxes) - discount
      $wr_22 = $taxes; // tax K열
      $wr_23 = (float)$shipping; // shipping price J열

      $wr_24 = $paymethod;

      // 수수료1 계산
      if ($wr_24 == "Shopify Payments") {
        $wr_35 = $wr_14 * 2.4 / 100 + 0.3;

      } else if ($wr_24 == "PayPal Express Checkout") {
        $wr_35 = $wr_14 * 3.49 / 100 + 0.49;

      } else if ($wr_24 == "Shop Cash") {
        $wr_35 = $wr_14 * 2.4 / 100 + 0.3;

      } else if ($wr_24 == "Shopify Payments + PayPal Express Checkout") {
        $wr_35 = $wr_14 * 3.49 / 100 + 0.49;

      } else if ($wr_24 == "Shop Cash + Shopify Payments") {
        $wr_35 = $wr_14 * 2.4 / 100 + 0.3;

      } else {
        $wr_35 = 0;
      }

      $wr_36 = 0; // 수수료2

       /*
        * 2025-09-16 기존 사용하던 엑셀 문구 비교 -> 문구 변경으로 인한 코드 수정
       switch ($shippingmethod) {

            case "Expedited Shipping ( 5 ~ 7 Business Days)" :
                $wr_20 = "0001";
                break;
            case "Standard (15 ~ 25 Business Days)" :
                $wr_20 = "0003";
                break;
            case "Standard Shipping ( 15 ~ 30 Business Days)" :
                $wr_20 = "0002";
                break;
            case "Express from USA shipping (3 to 7 Business Days)" :
                $wr_20 = "0003";
                break;
            case "Shipping" :
                $wr_20 = "0003";
                break;
            case "USA 3 Days shipping (3 to 7 Business Days)" :
                $wr_20 = "0003";
                break;
            case "Economy Shipping ( 15 ~ 30 Business Days ) - No tracking number" :
                $wr_20 = "0006";
                break;

            default :
                $wr_20 = "";
                break;
        }

        if ($wr_14 > 80 && $wr_32 == 'US') {
        $wr_20 = "0001";
      }
       */

       /* if (strpos($shippingmethod, 'Expedited') !== false) {
            // shippingmethod에 'Expedited'가 포함된 경우
            $wr_20 = '0001';
        } elseif (strpos($shippingmethod, 'Standard') !== false) {

            // shippingmethod에 'Standard'가 포함된 경우
            if ($wr_32 == 'US') {
                // AQ열의 값이 'US'인 경우
                $wr_20 = '0003';
            } else {
                // AQ열의 값이 'US'가 아닌 경우
                $wr_20 = '0002';
            }
        } else {
            // 위 조건에 모두 해당하지 않는 경우
            $wr_20 = '';
        }*/

        $expl_shippingmethod = explode(" ",trim($shippingmethod))[0];

        echo "<script>console.log('=== 행 " . $i . " 처리 시작 ===');</script>";
        echo "<script>console.log('주문번호: " . addslashes($wr_subject) . "');</script>";
        echo "<script>console.log('shippingmethod: " . addslashes($shippingmethod) . "');</script>";
        echo "<script>console.log('첫단어: " . addslashes($expl_shippingmethod) . "');</script>";
        echo "<script>console.log('국가: " . addslashes($wr_32) . "');</script>";


        if( strcasecmp($expl_shippingmethod, 'Expedited') == 0 ) {

            // shippingmethod에 'Expedited'가 포함된 경우
            $wr_20 = '0001';
            echo "<script>console.log('Expedited 조건 -> wr_20 = 0001');</script>";

        }else if( strcasecmp($expl_shippingmethod, 'Standard') == 0 ){

            echo "<script>console.log('Standard 조건 진입');</script>";

            if ($wr_32 == 'US') {
                // AQ열의 값이 'US'인 경우
                $wr_20 = '0003';
                echo "<script>console.log('US 조건 -> wr_20 = 0003');</script>";
            } else {
                // AQ열의 값이 'US'가 아닌 경우
                $wr_20 = '0002';
                echo "<script>console.log('비US 조건 -> wr_20 = 0002');</script>";
            }

        }else {
            // 위 조건에 모두 해당하지 않는 경우
            $wr_20 = '';
            echo "<script>console.log('조건 불일치 -> wr_20 = 빈값');</script>";
        }

        echo "<script>console.log('wr_20 설정 후: " . $wr_20 . "');</script>";





    } else if ($domain == "Ebay") {

      if ($i == 1) {
        $ebay_excel = array('Sales Record Number' => "wr_subject", 'Buyer Email' => "wr_10", 'Sold For' => "wr_13", 'Shipping Service' => "shippingmethod", 'Quantity' => "wr_11", 'Item Title' => "wr_17", 'Custom Label' => "wr_16", 'Buyer Username' => "wr_1", 'Buyer Name' => "wr_2", 'Buyer Address 1' => "wr_3", 'Buyer Address 2' => "wr_4", 'Buyer City' => "wr_5", 'Buyer State' => "wr_6", 'Buyer Country' => "wr_7", 'Buyer Zip' => "wr_8", 'Ship To Phone' => "wr_9", 'Shipping And Handling' => "shipping", 'Ship To Name' => "wr_27", 'Ship To Address 1' => "wr_28", 'Ship To Address 2' => "wr_29", 'Ship To City' => "wr_30", 'Ship To State' => "wr_31", 'Ship To Country' => "wr_32", 'Ship To Zip' => "wr_33", 'Ship To Phone' => "wr_34", 'Total Price' => "total_price", 'eBay Collected Tax' => "tax_price", '수수료1' => 'wr_35');

        for ($j = 0; $j < count($rowData[0]); $j++) {
          if ($ebay_excel[$rowData[0][$j]] != "") {
            $ebay_arr[$j]['column'] = $ebay_excel[$rowData[0][$j]];
          }
        }
        continue;
      } else {

        $wr_15 = 'USD'; //통화
        $wr_18 = $domain; //도메인명
        foreach ($ebay_arr as $key => $val) {

          if ($val['column'] == "wr_13" || $val['column'] == "shipping" || $val['column'] == "tax_price" || $val['column'] == "wr_35") {
            if ($val['column'] == "wr_13") {
              $str_pattern = '/([\xEA-\xED][\x80-\xBF]{2}|[a-zA-Z])+/';
              preg_match_all($str_pattern, $rowData[0][$key], $match);
              $sub_currency = $match[0][0];
              if (isDefined($sub_currency) == false) {
                $wr_15 = "USD";
              } else {
                $sql = "SELECT * FROM g5_excharge WHERE SUBSTR(ex_eng,1,2) = '" . $sub_currency . "'";
                $row = sql_fetch($sql);
                $wr_15 = $row['ex_eng'];
                if (isDefined($wr_15) == false) {
                  $wr_15 = "USD";
                }
              }
            }
            $int = preg_replace('/[^.0-9]*/s', '', $rowData[0][$key]);
            ${"" . $val['column']} = round($int, 2);
          } else if ($val['column'] == "wr_7" || $val['column'] == "wr_32") {
            ${"" . $val['column']} = addslashes(get_country_code($rowData[0][$key]));
          } else if ($val['column'] == "wr_11") {
            $wr_11 = addslashes($rowData[0][$key]);
            $wr_12 = addslashes($rowData[0][$key]);
          } else if ($val['column'] == "wr_34") {
            $wr_9 = addslashes((string)$rowData[0][$key]);
            $wr_34 = addslashes((string)$rowData[0][$key]);
          } else {
            ${"" . $val['column']} = addslashes($rowData[0][$key]);
          }
        }

        $wr_22 = $tax_price;
        $wr_23 = 0; // shipping price 없음으로 엑셀에 되어있어서 0원으로 처리
        $wr_14 = ($wr_11 * $wr_13) + $shipping;

        $wr_36 = 0; // 수수료2
      }

      // 이베이의 경우 엑셀 파일 계속 변함으로 엑셀의 컬럼으로 필드에 값 대입
//			$wr_subject              = addslashes($rowData[0][0]); //주문번호
//			$wr_10             		 = addslashes($rowData[0][4]); //이메일
//			$wr_15             		 = 'USD'; //통화
//			$wr_13             		 = addslashes(str_replace('$', '', $rowData[0][27])); //단가
//
//			$shippingmethod          = trim(addslashes($rowData[0][57])); //배송코드
//			$wr_11             		 = addslashes($rowData[0][26]); //수량
//			$wr_17             		 = addslashes($rowData[0][23]); //상품명
//			$wr_16             		 = addslashes($rowData[0][24]); //상품코드(sku)
//			$wr_1 					 = addslashes($rowData[0][2]); //구매자ID
//			$wr_2             		 = addslashes($rowData[0][3]); //구매자이름
//			$wr_3             		 = addslashes($rowData[0][6]); //주소1
//			$wr_4             		 = addslashes($rowData[0][7]); //주소2
//			$wr_5             		 = addslashes($rowData[0][8]); //도시명
//			$wr_8             		 = addslashes($rowData[0][10]); //우편번호
//			$wr_6             		 = addslashes($rowData[0][9]); //주명
//			$wr_7             		 = addslashes(get_country_code($rowData[0][11])); //나라명
//			$wr_9             		 = addslashes($rowData[0][15]); //전화번호
//			$wr_18             		 = $domain; //도메인명
//			$wr_12             		 = addslashes($rowData[0][26]); //박스수
//			$shipping				 = addslashes(str_replace('$', '', $rowData[0][28])); //배송비
//
//			# 배송 관련 정보 추가
//			$wr_27					= addslashes($rowData[0][14]); // 배송 수령자명
//			$wr_28					= addslashes($rowData[0][16]); // 배송지 주소1
//			$wr_29					= addslashes($rowData[0][17]); // 배송지 주소2
//			$wr_30					= addslashes($rowData[0][18]); // 배송지 도시명
//			$wr_31					= addslashes($rowData[0][19]); // 배송지 주명
//			$wr_32					= addslashes(get_country_code($rowData[0][21])); // 배송지 나라명
//			$wr_33					= addslashes($rowData[0][20]); // 배송지 우편번호
//			$wr_34					= addslashes($rowData[0][15]); // 배송 수령자 연락처
//
//
//			//$wr_14             		 = ($wr_13 * $wr_11) + $shipping; //신고가격 (단가 * 수량) + 배송비
//
//			$total_price			 = addslashes(str_replace('$', '', number_format($rowData[0][46], 2)));
//			$taxrate      			 = addslashes(str_replace('$', '', number_format($rowData[0][38], 2)));
//
//			$wr_14             		 = $total_price - $taxrate; //신고가격 (totalprice - 이베이수수료)

      $wr_20 = $rowData[0][63];

      //구매자 아이디가 없는경우 제일하단 토탈 레코드, sellerID:dodoskin 인경우임.
      if (!$wr_1) {
        $fail_count++;
        $fail_it_id[] = $wr_subject;
        continue;
      }


    } else if ($domain == "Ebay-dodoskin") {
      if ($i == 1) {
        $ebay_excel = array('Sales Record Number' => "wr_subject", 'Buyer Email' => "wr_10", 'Sold For' => "wr_13", 'Shipping Service' => "shippingmethod", 'Quantity' => "wr_11", 'Item Title' => "wr_17", 'Custom Label' => "wr_16", 'Buyer Username' => "wr_1", 'Buyer Name' => "wr_2", 'Buyer Address 1' => "wr_3", 'Buyer Address 2' => "wr_4", 'Buyer City' => "wr_5", 'Buyer State' => "wr_6", 'Buyer Country' => "wr_7", 'Buyer Zip' => "wr_8", 'Ship To Phone' => "wr_9", 'Shipping And Handling' => "shipping", 'Ship To Name' => "wr_27", 'Ship To Address 1' => "wr_28", 'Ship To Address 2' => "wr_29", 'Ship To City' => "wr_30", 'Ship To State' => "wr_31", 'Ship To Country' => "wr_32", 'Ship To Zip' => "wr_33", 'Ship To Phone' => "wr_34", 'Total Price' => "total_price", 'eBay Collected Tax' => "tax_price", '수수료1' => 'wr_35');

        for ($j = 0; $j < count($rowData[0]); $j++) {
          if ($ebay_excel[$rowData[0][$j]] != "") {
            $ebay_arr[$j]['column'] = $ebay_excel[$rowData[0][$j]];
          }
        }
        continue;
      } else {

        $wr_15 = 'USD'; //통화
        $wr_18 = $domain; //도메인명
        foreach ($ebay_arr as $key => $val) {

          if ($val['column'] == "wr_13" || $val['column'] == "shipping" || $val['column'] == "tax_price" || $val['column'] == "wr_35") {
            if ($val['column'] == "wr_13") {
              $str_pattern = '/([\xEA-\xED][\x80-\xBF]{2}|[a-zA-Z])+/';
              preg_match_all($str_pattern, $rowData[0][$key], $match);
              $sub_currency = $match[0][0];
              if (isDefined($sub_currency) == false) {
                $wr_15 = "USD";
              } else {
                $sql = "SELECT * FROM g5_excharge WHERE SUBSTR(ex_eng,1,2) = '" . $sub_currency . "'";
                $row = sql_fetch($sql);
                $wr_15 = $row['ex_eng'];
                if (isDefined($wr_15) == false) {
                  $wr_15 = "USD";
                }
              }
            }
            $int = preg_replace('/[^.0-9]*/s', '', $rowData[0][$key]);
            ${"" . $val['column']} = round($int, 2);
          } else if ($val['column'] == "wr_7" || $val['column'] == "wr_32") {
            ${"" . $val['column']} = addslashes(get_country_code($rowData[0][$key]));
          } else if ($val['column'] == "wr_11") {
            $wr_11 = addslashes($rowData[0][$key]);
            $wr_12 = addslashes($rowData[0][$key]);
          } else if ($val['column'] == "wr_34") {
            $wr_9 = addslashes((string)$rowData[0][$key]);
            $wr_34 = addslashes((string)$rowData[0][$key]);
          } else {
            ${"" . $val['column']} = addslashes((string)$rowData[0][$key]);
          }
        }

        $wr_22 = $tax_price;
        $wr_23 = 0; // shipping price 없음으로 엑셀에 되어있어서 0원으로 처리
        $wr_14 = ($wr_11 * $wr_13) + $shipping;

        $wr_36 = 0; // 수수료2
        $wr_37 = 0; // shipping tax
      }
      /*
      // 이베이의 경우 엑셀 파일 계속 변함으로 엑셀의 컬럼으로 필드에 값 대입
      $wr_subject              = addslashes($rowData[0][0]); //주문번호
      $wr_10             		 = addslashes($rowData[0][4]); //이메일
      $wr_15             		 = 'USD'; //통화
      $wr_13             		 = addslashes(str_replace('$', '', $rowData[0][27])); //단가

      $shippingmethod          = trim(addslashes($rowData[0][57])); //배송코드
      $wr_11             		 = addslashes($rowData[0][26]); //수량
      $wr_17             		 = addslashes($rowData[0][23]); //상품명
      $wr_16             		 = addslashes($rowData[0][24]); //상품코드(sku)
      $wr_1 					 = addslashes($rowData[0][2]); //구매자ID
      $wr_2             		 = addslashes($rowData[0][3]); //구매자이름
      $wr_3             		 = addslashes($rowData[0][6]); //주소1
      $wr_4             		 = addslashes($rowData[0][7]); //주소2
      $wr_5             		 = addslashes($rowData[0][8]); //도시명
      $wr_8             		 = addslashes($rowData[0][10]); //우편번호
      $wr_6             		 = addslashes($rowData[0][9]); //주명
      $wr_7             		 = addslashes(get_country_code($rowData[0][11])); //나라명
      $wr_9             		 = addslashes($rowData[0][15]); //전화번호
      $wr_18             		 = $domain; //도메인명
      $wr_12             		 = addslashes($rowData[0][26]); //박스수
      $shipping				 = addslashes(str_replace('$', '', $rowData[0][28])); //배송비

      # 배송 관련 정보 추가
      $wr_27					= addslashes($rowData[0][14]); // 배송 수령자명
      $wr_28					= addslashes($rowData[0][16]); // 배송지 주소1
      $wr_29					= addslashes($rowData[0][17]); // 배송지 주소2
      $wr_30					= addslashes($rowData[0][18]); // 배송지 도시명
      $wr_31					= addslashes($rowData[0][19]); // 배송지 주명
      $wr_32					= addslashes(get_country_code($rowData[0][21])); // 배송지 나라명
      $wr_33					= addslashes($rowData[0][20]); // 배송지 우편번호
      $wr_34					= addslashes($rowData[0][15]); // 배송 수령자 연락처


      //$wr_14             		 = ($wr_13 * $wr_11) + $shipping; //신고가격 (단가 * 수량) + 배송비

      $total_price			 = addslashes(str_replace('$', '', number_format($rowData[0][46], 2)));
      $taxrate      			 = addslashes(str_replace('$', '', number_format($rowData[0][38], 2)));

      $wr_14             		 = $total_price - $taxrate; //신고가격 (totalprice - 이베이수수료)
      $wr_22					 = $taxrate;
      $wr_23					 = ($rowData[0][27] == "")?"0.00": addslashes(str_replace('$', '', number_format($rowData[0][28], 2)));
      */
      if (strpos($shippingmethod, "Expedited International Shipping") !== false) {
        $wr_20 = "0001";
      } else if (strpos($shippingmethod, "Economy International Shipping") !== false) {
        $wr_20 = "0002";
      } else if (strpos($shippingmethod, "USPS First CLass") !== false || strpos($shippingmethod, "Standard Shipping from outside US") !== false) {
        $wr_20 = "0003";
      }


      //구매자 아이디가 없는경우 제일하단 토탈 레코드, sellerID:dodoskin 인경우임.
      if (!$wr_1) {
        $fail_count++;
        $fail_it_id[] = $wr_subject;
        continue;
      }

    } else if ($domain == "AC-CAD") {

      $wr_subject = addslashes($rowData[0][0]); //주문번호
      $wr_10 = addslashes($rowData[0][4]); //이메일
      $wr_15 = addslashes($rowData[0][10]); //통화
      $wr_13 = addslashes($rowData[0][11]); //단가

      //$shippingmethod          = trim(addslashes($rowData[0][57])); //배송코드
      $wr_11 = addslashes($rowData[0][9]); //수량
      $wr_17 = addslashes($rowData[0][8]); //상품명
      $wr_16 = addslashes($rowData[0][7]); //상품코드(sku)
      $wr_1 = addslashes($rowData[0][1]); //구매자ID
      $wr_2 = addslashes($rowData[0][5]); //구매자이름
      $wr_3 = addslashes($rowData[0][17]); //주소1
      $wr_4 = addslashes($rowData[0][18]); //주소2
      $wr_5 = addslashes($rowData[0][20]); //도시명
      $wr_8 = addslashes($rowData[0][22]); //우편번호
      $wr_6 = addslashes($rowData[0][21]); //주명
      $wr_7 = addslashes($rowData[0][23]); //나라명
      $wr_9 = addslashes($rowData[0][6]); //전화번호
      $wr_18 = $domain; //도메인명
      $wr_12 = addslashes($rowData[0][9]); //박스수
      $shipping = addslashes(str_replace('$', '', $rowData[0][28])); //배송비

      # 배송 관련 정보 추가
      $wr_27 = $wr_2; // 배송 수령자명
      $wr_28 = $wr_3; // 배송지 주소1
      $wr_29 = $wr_4; // 배송지 주소2
      $wr_30 = $wr_5; // 배송지 도시명
      $wr_31 = $wr_6; // 배송지 주명
      $wr_32 = $wr_7; // 배송지 나라명
      $wr_33 = $wr_8; // 배송지 우편번호
      $wr_34 = addslashes($rowData[0][24]); // 배송 수령자 연락처


      $wr_13 = ($rowData[0][11] + $rowData[0][13] + $rowData[0][25]) / $wr_11; // (L열+N열+Z열)÷J열
      $wr_14 = $rowData[0][11] + $rowData[0][13] + $rowData[0][25]; //  (L열+N열+Z열)

      $wr_22 = $rowData[0][12] + $rowData[0][14]; // tax M열+O열
      $wr_23 = $rowData[0][13]; // N열

      $wr_35 = (isDefined($rowData[0][29]) == true) ? $rowData[0][29] : 0; // 수수료1 AD열
      $wr_36 = 0; // 수수료2

      /*
      24.01.11 상품db 매칭하여 무게중 가장 무거운걸로 체크
      1. 2키로 이상일때 0001
      2. 2키로 이하일때 0002
      3. 매칭안될때는 공란으로.

      wr_10 : 1개당무게
      wr_18 : 중량무게1
      wr_19 : 중량무게2
      */
      $item = sql_fetch("select * from g5_write_product where (wr_1 = '" . addslashes($wr_16) . "' or wr_27 = '" . addslashes($wr_16) . "' or wr_28 = '" . addslashes($wr_16) . "' or wr_29 = '" . addslashes($wr_16) . "' or wr_30 = '" . addslashes($wr_16) . "' or wr_31 = '" . addslashes($wr_16) . "') AND wr_delYn = 'N' ");

      $max_weight = max($item['wr_10'], $item['wr_18'], $item['wr_19']);

      $wr_20 = $rowData[0][30];

    } else if ($domain == "AC") {

      $wr_subject = addslashes($rowData[0][0]); //주문번호
      $wr_10 = addslashes($rowData[0][4]); //이메일
      $wr_15 = addslashes($rowData[0][10]); //통화


      //$shippingmethod          = trim(addslashes($rowData[0][57])); //배송코드
      $wr_11 = addslashes($rowData[0][9]); //수량
      $wr_17 = addslashes($rowData[0][8]); //상품명
      $wr_16 = addslashes($rowData[0][7]); //상품코드(sku)
      $wr_1 = ""; //구매자ID
      $wr_2 = addslashes($rowData[0][5]); //구매자이름
      $wr_3 = addslashes($rowData[0][17]); //주소1
      $wr_4 = addslashes($rowData[0][18]); //주소2
      $wr_5 = addslashes($rowData[0][20]); //도시명
      $wr_8 = addslashes($rowData[0][22]); //우편번호
      $wr_6 = addslashes($rowData[0][21]); //주명
      $wr_7 = addslashes($rowData[0][23]); //나라명
      $wr_9 = addslashes($rowData[0][6]); //전화번호
      $wr_18 = $domain; //도메인명
      $wr_12 = addslashes($rowData[0][9]); //박스수
      $shipping = addslashes(str_replace('$', '', $rowData[0][28])); //배송비

      # 배송 관련 정보 추가
      $wr_27 = addslashes($rowData[0][16]); // 배송 수령자명
      $wr_28 = $wr_3; // 배송지 주소1
      $wr_29 = $wr_4; // 배송지 주소2
      $wr_30 = $wr_5; // 배송지 도시명
      $wr_31 = $wr_6; // 배송지 주명
      $wr_32 = $wr_7; // 배송지 나라명
      $wr_33 = $wr_8; // 배송지 우편번호
      $wr_34 = addslashes($rowData[0][24]); // 배송 수령자 연락처

      $wr_13 = ($rowData[0][11] + $rowData[0][13] + $rowData[0][25]) / $wr_11; // (L열+N열+Z열)÷J열
      $wr_14 = $rowData[0][11] + $rowData[0][13] + $rowData[0][25]; //  (L열+N열+Z열)

      $wr_22 = $rowData[0][12] + $rowData[0][14]; // tax M열+O열
      $wr_23 = $rowData[0][13]; // N열

      $wr_35 = (isDefined($rowData[0][29]) == true) ? $rowData[0][29] : 0; // 수수료1 AD열
      $wr_36 = 0; // 수수료2

      if ($domain == "AC") {
        $wr_21 = addslashes($rowData[0][32]); //비고
      }
      /*
      24.01.11 상품db 매칭하여 무게중 가장 무거운걸로 체크
      1. 2키로 이상일때 0001
      2. 2키로 이하일때 0002
      3. 매칭안될때는 공란으로.

      wr_10 : 1개당무게
      wr_18 : 중량무게1
      wr_19 : 중량무게2
      */
      $item = sql_fetch("select * from g5_write_product where (wr_1 = '" . addslashes($wr_16) . "' or wr_27 = '" . addslashes($wr_16) . "' or wr_28 = '" . addslashes($wr_16) . "' or wr_29 = '" . addslashes($wr_16) . "' or wr_30 = '" . addslashes($wr_16) . "' or wr_31 = '" . addslashes($wr_16) . "') AND wr_delYn = 'N'  ");

      $max_weight = max($item['wr_10'], $item['wr_18'], $item['wr_19']);

      # 서비스타입 지정 AC
      $wr_20 = $rowData[0][30];


    } else if ($domain == "ACF") {

      //type이 order인것만 입력
      $state = addslashes($rowData[0][2]);
      if ($state != "Order") continue;


      $wr_subject = addslashes($rowData[0][3]); //주문번호
      //$wr_10             		 = addslashes($rowData[0][4]); //이메일
      $shippingmethod = trim(addslashes($rowData[0][8])); //배송코드

      //acf-cad는 통화를 담당자가 직접선택함.
      if ($domain == "ACF-CAD") {
        $wr_15 = $_POST['currency']; //통화
        $wr_7 = "UK"; //나라명
        if ($shippingmethod == "Standard Orders") {
          $wr_20 = "0002";
        } else {
          $wr_20 = "0001";
        }
      } else if ($domain == "ACF") {
        $wr_15 = "USD";
        $wr_7 = "US"; //나라명
        if ($shippingmethod == "Standard Orders") {
          $wr_20 = "0003";
        } else {
          $wr_20 = "0001";
        }

      }
      //$wr_13             		 = addslashes($rowData[0][11]); //단가


      $wr_11 = addslashes($rowData[0][6]); //수량
      $wr_17 = addslashes($rowData[0][5]); //상품명
      $wr_16 = addslashes($rowData[0][4]); //상품코드(sku)
      //$wr_1 					 = addslashes($rowData[0][2]); //구매자ID
      //$wr_2             		 = addslashes($rowData[0][16]); //구매자이름
      //$wr_3             		 = addslashes($rowData[0][17]); //주소1
      //$wr_4             		 = addslashes($rowData[0][18]); //주소2


      $wr_5 = addslashes($rowData[0][10]); //도시명

      $wr_8 = addslashes($rowData[0][12]); //우편번호
      $wr_6 = addslashes($rowData[0][11]); //주명

      //$wr_9             		 = addslashes($rowData[0][24]); //전화번호
      $wr_18 = $domain; //도메인명
      $wr_12 = addslashes($rowData[0][6]); //박스수
      //$shipping				 = addslashes(str_replace('$', '', $rowData[0][28])); //배송비

      # 배송 관련 정보 추가
      $wr_27 = ""; // 배송 수령자명
      $wr_28 = ""; // 배송지 주소1
      $wr_29 = ""; // 배송지 주소2
      $wr_30 = $wr_5; // 배송지 도시명
      $wr_31 = $wr_6; // 배송지 주명
      $wr_32 = $wr_7; // 배송지 나라명
      $wr_33 = $wr_8; // 배송지 우편번호
      $wr_34 = ""; // 배송 수령자 연락처


      $wr_13 = ($rowData[0][14] + (-1 * $rowData[0][22])) / $rowData[0][6]; // (O열+W열)÷G열
      $wr_14 = $rowData[0][14] + (-1 * $rowData[0][22]) + $rowData[0][15]; // O열+W열+P열
      $wr_22 = $rowData[0][15]; // TAX P열
      $wr_23 = 0; // shipping 개별단가, 신고가격에서 계산이 되었음으로 0원 처리

      $wr_35 = -1 * $rowData[0][25]; // 수수료1 Z열
      $wr_36 = -1 * $rowData[0][26]; // 수수료2 AA열

    } else if ($domain == "ACF-CAD") {

      //type이 order인것만 입력
      $state = addslashes($rowData[0][2]);
      if ($state != "Order") continue;


      $wr_subject = addslashes($rowData[0][3]); //주문번호
      //$wr_10             		 = addslashes($rowData[0][4]); //이메일
      $shippingmethod = trim(addslashes($rowData[0][8])); //배송코드

      $wr_15 = $_POST['currency']; //통화
      $wr_7 = "GB"; //나라명
      if ($shippingmethod == "Standard Orders") {
        $wr_20 = "0002";
      } else {
        $wr_20 = "0001";
      }


      $wr_11 = addslashes($rowData[0][6]); //수량
      $wr_17 = addslashes($rowData[0][5]); //상품명
      $wr_16 = addslashes($rowData[0][4]); //상품코드(sku)
      //$wr_1 					 = addslashes($rowData[0][2]); //구매자ID
      //$wr_2             		 = addslashes($rowData[0][16]); //구매자이름
      //$wr_3             		 = addslashes($rowData[0][17]); //주소1
      //$wr_4             		 = addslashes($rowData[0][18]); //주소2


      $wr_5 = addslashes($rowData[0][9]); //도시명

      $wr_8 = addslashes($rowData[0][11]); //우편번호
      $wr_6 = addslashes($rowData[0][10]); //주명

      //$wr_9             		 = addslashes($rowData[0][24]); //전화번호
      $wr_18 = $domain; //도메인명
      $wr_12 = addslashes($rowData[0][6]); //박스수
      //$shipping				 = addslashes(str_replace('$', '', $rowData[0][28])); //배송비

      # 배송 관련 정보 추가
      $wr_27 = ""; // 배송 수령자명
      $wr_28 = ""; // 배송지 주소1
      $wr_29 = ""; // 배송지 주소2
      $wr_30 = $wr_5; // 배송지 도시명
      $wr_31 = $wr_6; // 배송지 주명
      $wr_32 = $wr_7; // 배송지 나라명
      $wr_33 = $wr_8; // 배송지 우편번호
      $wr_34 = ""; // 배송 수령자 연락처


      $wr_13 = ($rowData[0][13] + (-1 * $rowData[0][19])) / $wr_11; //단가 (N열+T열)÷G열
      $wr_14 = $rowData[0][13] + (-1 * $rowData[0][19]) + $rowData[0][14]; // N열+T열+O열

      $wr_22 = $rowData[0][14]; // 상품 TAX O열
      $wr_23 = 0; // shipping 개별단가, 신고가격에 합산이 되었음으로 0원 처리

      $wr_35 = -1 * $rowData[0][22]; // 수수료1 W열
      $wr_36 = -1 * $rowData[0][23]; // 수수료2 X열

    } else if ($domain == "ACF-CAD_F" || $domain == "ACF-CAD_I") {

      //type Commande, Ordine 인것만 입력
      $state = addslashes($rowData[0][2]);
      if ($domain == "ACF-CAD_F") {
        $wr_7 = "FR";
        if ($state != "Commande") {
          continue;
        }
      }


      if ($domain == "ACF-CAD_I") {
        $wr_7 = "IT";
        if ($state != "Ordine") {
          continue;
        }
      }

      $wr_subject = addslashes($rowData[0][3]); //주문번호
      //$wr_10             		 = addslashes($rowData[0][4]); //이메일

      //acf-cad는 통화를 담당자가 직접선택함.

      $wr_15 = $_POST['currency']; //통화

      $wr_13 = addslashes(str_replace(',', '.', $rowData[0][13])); //단가

      $shippingmethod = trim(addslashes($rowData[0][15])); //배송코드
      $wr_11 = addslashes($rowData[0][6]); //수량
      $wr_17 = addslashes($rowData[0][5]); //상품명
      $wr_16 = addslashes($rowData[0][4]); //상품코드(sku)
      //$wr_1 					 = addslashes($rowData[0][2]); //구매자ID
      //$wr_2             		 = addslashes($rowData[0][16]); //구매자이름
      //$wr_3             		 = addslashes($rowData[0][9]); //주소1
      //$wr_4             		 = addslashes($rowData[0][10]); //주소2
      $wr_5 = addslashes($rowData[0][9]); //도시명
      $wr_8 = addslashes($rowData[0][11]); //우편번호
      $wr_6 = addslashes($rowData[0][10]); //주명
      //$wr_7             		 = addslashes($rowData[0][10]); //나라명
      //$wr_9             		 = addslashes($rowData[0][24]); //전화번호
      $wr_18 = "ACF-CAD"; //도메인명
      $wr_12 = addslashes($rowData[0][6]); //박스수
      //$shipping				 = addslashes(str_replace('$', '', $rowData[0][28])); //배송비

      # 배송 관련 정보 추가
      $wr_27 = ""; // 배송 수령자명
      $wr_28 = ""; // 배송지 주소1
      $wr_29 = ""; // 배송지 주소2
      $wr_30 = $wr_5; // 배송지 도시명
      $wr_31 = $wr_6; // 배송지 주명
      $wr_32 = $wr_7; // 배송지 나라명
      $wr_33 = $wr_8; // 배송지 우편번호
      $wr_34 = ""; // 배송 수령자 연락처

      $wr_13 = ($rowData[0][13] + $rowData[0][19]) / $wr_11; //단가 (N열+T열)÷G열
      $wr_14 = $rowData[0][13] + $rowData[0][19] + $rowData[0][14]; // N열+T열+O열

      $wr_22 = $rowData[0][14]; // 상품 TAX O열
      $wr_23 = 0; // shipping 개별단가, 신고가격에 합산이 되었음으로 0원 처리

      $wr_35 = -1 * $rowData[0][22]; // 수수료1 W열
      $wr_36 = -1 * $rowData[0][23]; // 수수료2 X열

      if ($shippingmethod == "Standard") {
        $wr_20 = "0002";
      } else {
        $wr_20 = "0001";
      }


    } else if ($domain == "AJP") {

      $wr_subject = addslashes($rowData[0][6]); //주문번호
      $wr_10 = addslashes($rowData[0][10]); //이메일
      $wr_15 = addslashes($rowData[0][16]); //통화
      $wr_14 = addslashes($rowData[0][17]); //신고가격


      //$shippingmethod          = trim(addslashes($rowData[0][57])); //배송코드
      $wr_11 = addslashes($rowData[0][15]); //수량
      $wr_13 = (int)$wr_14 / (int)$wr_11; //단가

      $wr_17 = addslashes($rowData[0][14]); //상품명
      $wr_16 = addslashes($rowData[0][13]); //상품코드(sku)
      //$wr_1 					 = addslashes($rowData[0][11]); //구매자ID
      $wr_2 = addslashes($rowData[0][11]); //구매자이름
      $wr_3 = addslashes($rowData[0][23]); //주소1
      $wr_4 = addslashes($rowData[0][24]) . ' ' . addslashes($rowData[0][25]); //주소2
      //$wr_5             		 = addslashes($rowData[0][20]); //도시명
      $wr_8 = addslashes($rowData[0][28]); //우편번호
      $wr_6 = addslashes($rowData[0][27]); //주명
      $wr_7 = 'JP'; //나라명
      $wr_9 = addslashes($rowData[0][12]); //전화번호
      $wr_18 = $domain; //도메인명
      $wr_12 = addslashes($rowData[0][15]); //박스수
      //$shipping				 = addslashes(str_replace('$', '', $rowData[0][28])); //배송비
      $wr_20 = "0007"; //고정

      # 배송 관련 정보 추가
      $wr_27 = $wr_2; // 배송 수령자명
      $wr_28 = $wr_3; // 배송지 주소1
      $wr_29 = $wr_4; // 배송지 주소2
      $wr_30 = ""; // 배송지 도시명
      $wr_31 = $wr_6; // 배송지 주명
      $wr_32 = $wr_7; // 배송지 나라명
      $wr_33 = $wr_8; // 배송지 우편번호
      $wr_34 = $wr_9; // 배송 수령자 연락처

      $wr_13 = ($rowData[0][17] - $rowData[0][18]) / $rowData[0][15]; // 단가 (R열-S열)÷P열
      $wr_14 = $rowData[0][17]; // 신고가격 R열

      $wr_22 = $rowData[0][18]; // tax S열
      $wr_23 = 0; // shipping price 없음

      $wr_35 = $rowData[0][32]; // 수수료1 AG열
      $wr_36 = 0; // 수수료2 없음
      $wr_37 = 0; // shipping tax

    } else if ($domain == "WM") {

      $wr_subject = addslashes($rowData[0][1]); //주문번호
      //$wr_10             		 = addslashes($rowData[0][4]); //이메일
      $wr_15 = "USD"; //통화
      $wr_13 = addslashes($rowData[0][27]); //단가

      //$shippingmethod          = trim(addslashes($rowData[0][57])); //배송코드
      $wr_11 = addslashes($rowData[0][24]); //수량
      $wr_17 = addslashes($rowData[0][19]); //상품명
      $wr_16 = addslashes($rowData[0][25]); //상품코드(sku)

      $wr_2 = addslashes($rowData[0][5]); //구매자이름
      $wr_3 = addslashes($rowData[0][8]); //주소1
      $wr_4 = addslashes($rowData[0][9]); //주소2
      $wr_5 = addslashes($rowData[0][11]); //도시명
      $wr_8 = addslashes($rowData[0][13]); //우편번호
      $wr_6 = addslashes($rowData[0][12]); //주명
      $wr_7 = "US"; //나라명
      $wr_9 = addslashes($rowData[0][7]); //전화번호
      $wr_18 = $domain; //도메인명
      $wr_12 = addslashes($rowData[0][24]); //박스수
      //$shipping				 = addslashes(str_replace('$', '', $rowData[0][28])); //배송비

      # 배송 관련 정보 추가
      $wr_27 = $wr_2; // 배송 수령자명
      $wr_28 = $wr_3; // 배송지 주소1
      $wr_29 = $wr_4; // 배송지 주소2
      $wr_30 = $wr_5; // 배송지 도시명
      $wr_31 = $wr_6; // 배송지 주명
      $wr_32 = $wr_7; // 배송지 나라명
      $wr_33 = $wr_8; // 배송지 우편번호
      $wr_34 = $wr_9; // 배송 수령자 연락처

      $wr_13 = $rowData[0][27]; // 단가 AB열
      $wr_14 = $rowData[0][27] * $rowData[0][24] + $rowData[0][29]; // 신고가격 AB열*Y열+AD열

      $wr_22 = $rowData[0][30]; // tax AE열
      $wr_23 = $rowData[0][29]; // shipping price AD열

      $wr_35 = $rowData[0][46]; // 수수료1 제품관리] 수수료 입력하는 부분에서 끌어오기
      $wr_36 = 0; // 수수료2 없음
      $wr_37 = 0; // shipping tax

      $wr_20 = $rowData[0][49]; # WM 서비스타입


    } else if ($domain == "WM CA") {

      $wr_subject = addslashes($rowData[0][1]); //주문번호
      //$wr_10             		 = addslashes($rowData[0][4]); //이메일
      $wr_15 = "CAD"; //통화
      $wr_13 = $rowData[0][24]; //단가


      //$shippingmethod          = trim(addslashes($rowData[0][57])); //배송코드
      $wr_11 = addslashes($rowData[0][22]); //수량
      $wr_17 = addslashes($rowData[0][20]); //상품명
      $wr_16 = addslashes($rowData[0][23]); //상품코드(sku)

      $wr_2 = addslashes($rowData[0][5]); //구매자이름
      $wr_3 = addslashes($rowData[0][8]); //주소1
      $wr_4 = addslashes($rowData[0][9]); //주소2
      $wr_5 = addslashes($rowData[0][10]); //도시명
      $wr_8 = addslashes($rowData[0][12]); //우편번호
      $wr_6 = addslashes($rowData[0][11]); //주명
      $wr_7 = "CA"; //나라명
      $wr_9 = addslashes($rowData[0][7]); //전화번호
      $wr_18 = $domain; //도메인명
      $wr_12 = addslashes($rowData[0][22]); //박스수
      //$shipping				 = addslashes(str_replace('$', '', $rowData[0][28])); //배송비

      # 배송 관련 정보 추가
      $wr_27 = $wr_2; // 배송 수령자명
      $wr_28 = $wr_3; // 배송지 주소1
      $wr_29 = $wr_4; // 배송지 주소2
      $wr_30 = $wr_5; // 배송지 도시명
      $wr_31 = $wr_6; // 배송지 주명
      $wr_32 = $wr_7; // 배송지 나라명
      $wr_33 = $wr_8; // 배송지 우편번호
      $wr_34 = $wr_9; // 배송 수령자 연락처

      $wr_13 = $rowData[0][24] / $rowData[0][22]; // 단가 Y/W
      $wr_14 = $rowData[0][24]; // 신고가격 Y열

      $wr_22 = $rowData[0][26]; // tax AA열
      $wr_23 = 0; // shipping price Z열 사용안함 표시가 되어있어서 0원처리

      $wr_35 = $rowData[0][39]; // 수수료1 제품관리] 수수료 입력하는 부분에서 끌어오기
      $wr_36 = 0; // 수수료2 없음

      $wr_20 = "0001"; //고정


    } else if (in_array($domain, array("Shopee BR", "Shopee MX", "Shopee VN", "Shopee SG", "Shopee MY", "Shopee PH", "Shopee MX"))) {

      $wr_subject = addslashes($rowData[0][0]); //주문번호
      //$wr_10             		 = addslashes($rowData[0][4]); //이메일
      if ($domain == "Shopee BR") {
        $wr_15 = "BRL"; //통화
      } else if ($domain == "Shopee MX") {
        $wr_15 = "MXN"; //통화
      } else if ($domain == "Shopee VN") {
        $wr_15 = "VND"; //통화
      } else if ($domain == "Shopee SG") {
        $wr_15 = "SGD"; //통화
      } else if ($domain == "Shopee MY") {
        $wr_15 = "MYR"; //통화
      } else if ($domain == "Shopee PH") {
        $wr_15 = "PHP"; //통화
      } else {
        $wr_15 = "USD"; //통화
      }

      $wr_13 = addslashes($rowData[0][15]); //단가

      //$shippingmethod          = trim(addslashes($rowData[0][57])); //배송코드
      $wr_11 = addslashes($rowData[0][16]); //수량
      $wr_17 = addslashes($rowData[0][11]); //상품명

      if ($domain == "Shopee VN")
        $wr_16 = addslashes(only_number($rowData[0][18]));
      else
        $wr_16 = addslashes(only_number($rowData[0][12]));//상품코드(sku)

      $wr_2 = addslashes($rowData[0][44]); //구매자이름
      $wr_3 = addslashes($rowData[0][47]); //주소1
      //$wr_4             		 = addslashes($rowData[0][9]); //주소2
      $wr_5 = addslashes($rowData[0][50]); //도시명
      $wr_8 = addslashes($rowData[0][53]); //우편번호
      $wr_6 = addslashes($rowData[0][51]); //주명
      $wr_7 = addslashes($rowData[0][52]);; //나라명
      $wr_9 = addslashes($rowData[0][45]); //전화번호
      $wr_18 = $domain; //도메인명
      $wr_12 = addslashes($rowData[0][16]); //박스수
      //$shipping				 = addslashes(str_replace('$', '', $rowData[0][28])); //배송비

      # 배송 관련 정보 추가
      $wr_27 = $wr_2; // 배송 수령자명
      $wr_28 = $wr_3; // 배송지 주소1
      $wr_29 = ""; // 배송지 주소2
      $wr_30 = $wr_5; // 배송지 도시명
      $wr_31 = $wr_6; // 배송지 주명
      $wr_32 = $wr_7; // 배송지 나라명
      $wr_33 = $wr_8; // 배송지 우편번호
      $wr_34 = $wr_9; // 배송 수령자 연락처

      $wr_14 = ($wr_13 * $wr_11); //신고가격 (단가 * 수량)
      $wr_20 = ""; //배송코드 필요없음.
      $shippingmethod = trim(addslashes($rowData[0][4])); //배송코드
      if (strpos($shippingmethod, "Expresso Padrão") > 0) {
        $wr_20 = "0002";
      } else {
        $wr_20 = "0001";
      }

      $wr_13 = 0; // 단가
      $wr_14 = 0; // 신고가격

      $wr_22 = 0; // tax
      $wr_23 = 0; // shipping price

      $wr_35 = 0; // 수수료1
      $wr_36 = 0; // 수수료2
      $wr_37 = 0; // shipping tax

    }


    /* $wr_subject              = addslashes($rowData[0][$j++]); //주문번호
     $wr_1             		 = addslashes($rowData[0][$j++]); //구매자아이디
     $wr_2             		 = addslashes($rowData[0][$j++]); //구매자이름
     $wr_3             		 = addslashes($rowData[0][$j++]); //주소1
     $wr_4             		 = addslashes($rowData[0][$j++]); //주소2
     $wr_5             		 = addslashes($rowData[0][$j++]); //도시명
     $wr_6             		 = addslashes($rowData[0][$j++]); //주명
     $wr_7             		 = addslashes($rowData[0][$j++]); //나라명
     $wr_8             		 = addslashes($rowData[0][$j++]); //우편번호
     $wr_9             		 = addslashes($rowData[0][$j++]); //전화번호
     $wr_10             		 = addslashes($rowData[0][$j++]); //이메일
     $wr_11             		 = addslashes($rowData[0][$j++]); //수량
     $wr_12             		 = addslashes($rowData[0][$j++]); //박스수
     $wr_13             		 = addslashes($rowData[0][$j++]); //단가
     $wr_14             		 = addslashes($rowData[0][$j++]); //신고가격
     $wr_15             		 = addslashes($rowData[0][$j++]); //통화
     $wr_16             		 = addslashes($rowData[0][$j++]); //상품코드
     $wr_17             		 = addslashes($rowData[0][$j++]); //상품명
     $wr_18             		 = addslashes($rowData[0][$j++]); //도메인명
     $wr_20             		 = addslashes($rowData[0][$j++]); //특송
     $wr_21             		 = addslashes($rowData[0][$j++]); //비고*/


    if (!$wr_subject) {
      $fail_count++;
      $fail_it_id[] = $i;
      continue;
    }


    $write_table = "g5_write_sales";
    $bo_table = "sales";
    $wr_num = get_next_num($write_table);

    $chk = sql_fetch("select * from  {$write_table} where wr_subject LIKE BINARY '%$wr_subject%' and wr_16 = '{$wr_16}' and wr_17 = '{$wr_17}' and wr_10 = '{$wr_10}' ");
    #$chk = sql_fetch("select * from  {$write_table} where wr_subject LIKE '%$wr_subject%' and wr_16 = '{$wr_16}' and wr_10 = '{$wr_10}' ");

    if ($domain != "dodoskin" && $chk && $chk['1'] != '1') {
      $fail_count++;
      $fail_over[] = $i;
      $fail_over_arr[] = $chk['wr_2'];

      continue;
    }
    $mb_id = $member['mb_id'];
    $wr_name = $member['mb_name'];
    $wr_password = '';
    $wr_content = '-';
    if (isDefined($wr_16) == true) {
      $item = sql_fetch("select wr_id,taxType from g5_write_product where (wr_1 = '" . addslashes($wr_16) . "' or wr_27 = '" . addslashes($wr_16) . "' or wr_28 = '" . addslashes($wr_16) . "' or wr_29 = '" . addslashes($wr_16) . "' or wr_30 = '" . addslashes($wr_16) . "' or wr_31 = '" . addslashes($wr_16) . "') AND wr_delYn = 'N'  ");
    } else {
      $item['wr_id'] = "0";
    }

    // WM , WM CA의 경우 수수료1을 상품에서 가져오기 때문에 상품수수료 테이블을 조회 하여 계산
    if ($domain == "WM" || $domain == "WM CA") {
      if ($item['wr_id'] > 0) {
        $wr_37 = $item['taxType']; // 상품과 매칭이 되었을 경우 해당 정보로 입력( 매출등록시 세트 상품의 경우 각 상품별 과세/면세 적용 )
        $sql = "SELECT * FROM g5_write_product_fee WHERE wr_id='" . $item['wr_id'] . "' AND domain = '" . $domain . "' LIMIT 1";
        $feeRow = sql_fetch($sql);
//				if(isDefined($feeRow['product_fee']) == true){
//					$wr_35 = $feeRow['product_fee'];
//				}else{
//					$wr_35 = 0;
//				}
      } else { // 상품이 조회되지 않을 경우 기본값으로 과세 적용
        $wr_37 = "1";
      }
    }

    $sql = " insert into $write_table
                set wr_num = '$wr_num',
                     wr_reply = '$wr_reply',
                     wr_comment = 0,
					 ori_order_num = '" . $wr_subject . "',
                     wr_subject = '$wr_subject',
                     wr_content = '$wr_content',
                     wr_link1_hit = 0,
                     wr_link2_hit = 0,
                     wr_hit = 0,
                     wr_good = 0,
                     wr_nogood = 0,
                     mb_id = '{$mb_id}',
                     wr_password = '$wr_password',
                     wr_name = '$wr_name',
                     wr_datetime = '" . G5_TIME_YMDHIS . "',
                     wr_last = '" . G5_TIME_YMDHIS . "',
                     wr_ip = '{$_SERVER['REMOTE_ADDR']}',
                     wr_1 = '$wr_1',
                     wr_2 = '$wr_2',
                     wr_3 = '$wr_3',
                     wr_4 = '$wr_4',
                     wr_5 = '$wr_5',
                     wr_6 = '$wr_6',
                     wr_7 = '$wr_7',
                     wr_8 = '$wr_8',
                     wr_9 = '$wr_9',
                     wr_10 = '$wr_10',
					 wr_11 = '{$wr_11}',
					 wr_12 = '{$wr_12}',
					wr_13 = '{$wr_13}',
					wr_14 = '{$wr_14}',
					wr_15 = '{$wr_15}',
					wr_16 = '{$wr_16}',
					wr_17 = '{$wr_17}',
					wr_18 = '{$wr_18}',
					wr_19 = '{$wr_19}',
					wr_20 = '{$wr_20}',
					wr_21 = '{$wr_21}',
					wr_22 = '{$wr_22}',
					wr_23 = '{$wr_23}',
					wr_24 = '{$wr_24}',
					wr_25 = '{$wr_25}',
					wr_27 = '{$wr_27}',
					wr_28 = '{$wr_28}',
					wr_29 = '{$wr_29}',
					wr_30 = '{$wr_30}',
					wr_31 = '{$wr_31}',
					wr_32 = '{$wr_32}',
					wr_33 = '{$wr_33}',
					wr_34 = '{$wr_34}',
					wr_35 = '{$wr_35}',
					wr_36 = '{$wr_36}',
					wr_37 = '{$wr_37}',
					wr_product_id = '{$item['wr_id']}'
					 ";

    $rs = sql_query($sql);


    $wr_id = sql_insert_id();

    // 부모 아이디에 UPDATE
    sql_query(" update $write_table set wr_parent = '$wr_id' where wr_id = '$wr_id' ");

    // 새글 INSERT
    sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '{$bo_table}', '{$wr_id}', '{$wr_id}', '" . G5_TIME_YMDHIS . "', '{$member['mb_id']}' ) ");

    // 게시글 1 증가
    sql_query("update {$g5['board_table']} set bo_count_write = bo_count_write + 1 where bo_table = '{$bo_table}'");


    $succ_count++;
  }

  if ($domain == "Ebay" || $domain == "Ebay-dodoskin") {

    //24.1.12 EBAY 합배송은 첫번째 열에 인적사항만 들어가있어 해당 정보만 추출하고 삭제처리해야됨

    $query = "SELECT *, COUNT(*) AS cnt FROM g5_write_sales GROUP BY wr_subject HAVING cnt > 1";
    $result = sql_query($query);

    while ($row = sql_fetch_array($result)) {

      $ordernum = $row['wr_subject'];
      $cnt = $row['cnt'];
      $wr_2 = $row['wr_2'];

      for ($i = 1; $i <= $cnt; $i++) {
        // 중복 주문번호 뒤에 알파벳 시퀀스 추가 (A, B, C, ..., Z, AA, AB, AC, ...)
        $newOrderNumber = $row['wr_subject'] . getAlphabetSequence($i);


        $updateQuery = "UPDATE g5_write_sales SET wr_subject = '$newOrderNumber',
				wr_1 = '" . addslashes($row['wr_1']) . "',
				wr_2 = '" . addslashes($row['wr_2']) . "',
				wr_3 = '" . addslashes($row['wr_3']) . "',
				wr_4 = '" . addslashes($row['wr_4']) . "',
				wr_5 = '" . addslashes($row['wr_5']) . "',
				wr_6 = '" . addslashes($row['wr_6']) . "',
				wr_7 = '" . addslashes($row['wr_7']) . "',
				wr_8 = '" . addslashes($row['wr_8']) . "',
				wr_9 = '" . addslashes($row['wr_9']) . "',
				wr_10 = '" . addslashes($row['wr_10']) . "',
				wr_15 = '" . addslashes($row['wr_15']) . "',
				wr_18 = '" . addslashes($row['wr_18']) . "',
				wr_19 = '" . addslashes($row['wr_19']) . "',
				wr_20 = '" . addslashes($row['wr_20']) . "',
				wr_21 = '" . addslashes($row['wr_21']) . "',
				wr_22 = '" . addslashes($row['wr_22']) . "',
				wr_23 = '" . addslashes($row['wr_23']) . "',
				wr_24 = '" . addslashes($row['wr_24']) . "',
				wr_25 = '1',
				wr_27 = '" . addslashes($row['wr_27']) . "',
				wr_28 = '" . addslashes($row['wr_28']) . "',
				wr_29 = '" . addslashes($row['wr_29']) . "',
				wr_30 = '" . addslashes($row['wr_30']) . "',
				wr_31 = '" . addslashes($row['wr_31']) . "',
				wr_32 = '" . addslashes($row['wr_32']) . "',
				wr_33 = '" . addslashes($row['wr_33']) . "',
				wr_34 = '" . addslashes($row['wr_34']) . "'
				WHERE wr_subject = '$ordernum' and wr_17 != '' LIMIT 1";


        sql_query($updateQuery);
      }

      //합배송 업데이트 완료 후 첫번째 열 삭제(문자열 붙지 않은것)
      @sql_query("delete from g5_write_sales WHERE wr_subject = '$ordernum' LIMIT 1");
    }

  } else {

    $query = "SELECT *, COUNT(*) AS cnt FROM g5_write_sales GROUP BY wr_subject HAVING cnt > 1";
    $result = sql_query($query);

    while ($row = sql_fetch_array($result)) {


      $ordernum = $row['wr_subject'];
      $cnt = $row['cnt'];
      $wr_2 = $row['wr_2'];

      $total_wr_14 = 0;
      $express_ordernum = '';
      for ($i = 1; $i <= $cnt; $i++) {
        // 중복 주문번호 뒤에 알파벳 시퀀스 추가 (A, B, C, ..., Z, AA, AB, AC, ...)
        $newOrderNumber = $row['wr_subject'] . getAlphabetSequence($i);

        $updateQuery = "UPDATE g5_write_sales SET wr_subject = '$newOrderNumber',
				wr_1 = '" . addslashes($row['wr_1']) . "',
				wr_2 = '" . addslashes($row['wr_2']) . "',
				wr_3 = '" . addslashes($row['wr_3']) . "',
				wr_4 = '" . addslashes($row['wr_4']) . "',
				wr_5 = '" . addslashes($row['wr_5']) . "',
				wr_6 = '" . addslashes($row['wr_6']) . "',
				wr_7 = '" . addslashes($row['wr_7']) . "',
				wr_8 = '" . addslashes($row['wr_8']) . "',
				wr_9 = '" . addslashes($row['wr_9']) . "',
				wr_10 = '" . addslashes($row['wr_10']) . "',
				wr_15 = '" . addslashes($row['wr_15']) . "',
				wr_18 = '" . addslashes($row['wr_18']) . "',
				wr_19 = '" . addslashes($row['wr_19']) . "',
				wr_20 = '" . addslashes($row['wr_20']) . "',
				wr_21 = '" . addslashes($row['wr_21']) . "',
				wr_22 = '" . addslashes($row['wr_22']) . "',
				wr_23 = '" . addslashes($row['wr_23']) . "',
				wr_24 = '" . addslashes($row['wr_24']) . "',
				wr_25 = '1',
				wr_27 = '" . addslashes($row['wr_27']) . "',
				wr_28 = '" . addslashes($row['wr_28']) . "',
				wr_29 = '" . addslashes($row['wr_29']) . "',
				wr_30 = '" . addslashes($row['wr_30']) . "',
				wr_31 = '" . addslashes($row['wr_31']) . "',
				wr_32 = '" . addslashes($row['wr_32']) . "',
				wr_33 = '" . addslashes($row['wr_33']) . "',
				wr_34 = '" . addslashes($row['wr_34']) . "'
				WHERE wr_subject = '$ordernum' LIMIT 1";

        sql_query($updateQuery);

        if ($domain == 'dodoskin' && $row['wr_32'] == 'US') {
          $total_wr_14 += $row['wr_14'];
          $express_ordernum = $row['wr_subject'];
        }
      }

      #dodoskin 특송 80$ 이상, US 건 일 경우 특송처리
      //if ($express_ordernum && $total_wr_14 > 80) {
        if ($express_ordernum ) {
        $query = "UPDATE g5_write_sales set wr_20 = '0001' where wr_subject like '%$express_ordernum%' limit $cnt ";
        sql_query($query);
      }
    }

  }

}

$g5['title'] = '매출자료 엑셀일괄등록 결과';
include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);
?>

  <div class="new_win">
    <h1><?php echo $g5['title']; ?></h1>

    <div class="local_desc01 local_desc">
      <p>매출자료 등록을 완료했습니다.</p>
    </div>

    <dl id="excelfile_result">
      <dt>총 매출건수</dt>
      <dd><?php echo number_format($total_count); ?></dd>
      <dt>완료건수</dt>
      <dd><?php echo number_format($succ_count); ?></dd>
      <dt>실패건수(중복/오류)</dt>
      <dd><?php echo number_format($fail_count); ?>

        <?php
        if ($fail_count) {
          echo "<br>주문번호 없음 : " . implode(',', $fail_it_id);
        }

        if ($fail_count) {
          echo "<br>주문번호 중복 : " . implode(',', $fail_over);
        }

        ?>
      </dd>


    </dl>

    <div class="btn_win01 btn_win">
      <button type="button" onclick="window.close();">창닫기</button>
    </div>

  </div>

<?php
include_once(G5_PATH . '/tail.sub.php');