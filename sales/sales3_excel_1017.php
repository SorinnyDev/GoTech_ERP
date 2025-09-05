<?php 
include_once('./_common.php');

if($is_guest)
	alert('로그인 후 이용바랍니다.');

if(count($_POST['seq']) == 0) alert('잘못 된 접근입니다.');

$filename = "postoffice_".G5_TIME_YMD.".xls";

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Description: PHP4 Generated Data");




//엑셀 파일로 만들고자 하는 데이터의 테이블을 만듭니다.
$EXCEL_FILE = "
<table border='1'>
	
    <tr style=\"background:#ddd\">
		<th style=\"width:70px\">상품 구분</th>
		<th style=\"width:150px\">수취인명</th>
		<th style=\"width:140px\">수취인 이메일</th>
		<th style=\"width:140px\">수취인 국가번호</th>
		<th style=\"width:140px\">수취인 지역번호</th>
		<th style=\"width:140px\">수취인 전화국번</th>
		<th style=\"width:140px\">수취인 전화전화번호</th>
		<th style=\"width:150px\">전화번호</th>
		<th style=\"width:100px\">국가코드</th>
		<th style=\"width:100px\">국가명</th>
		<th style=\"width:100px\">우편번호</th>
		<th style=\"width:200px\">상세주소</th>
		<th style=\"width:100px\">도시명</th>
		<th style=\"width:100px\">주 명</th>
		<th style=\"width:70px\">건물명(미국만 입력가능)</th>
		<th style=\"width:100px\">포장중량</th>
		<th style=\"width:200px\">품목명</th>
		<th style=\"width:100px\">순중량</th>
		<th style=\"width:70px\">가격</th>
		<th style=\"width:70px\">통화단위</th>
		<th style=\"width:100px\">HS CODE</th>
		<th style=\"width:100px\">배송비</th>
		<th style=\"width:100px\">추가 배송비</th>
		<th style=\"width:100px\">생산지</th>
		<th style=\"width:100px\">규격</th>
		<th style=\"width:100px\">보험가입여부</th>
		<th style=\"width:100px\">보험가입금액(물품가액을기입 원\)</th>
		<th style=\"width:100px\">EMS : EEMS 프리미엄 : P K-Packet : K등기소형 :R</th>
		<th style=\"width:100px\">EMS 비서류 : em,    EMS 서류 : ee,K-Packet : rl, 소형포장물 : re</th>
		<th style=\"width:100px\">고객주문번호( 숫자,영문 30자이내)</th>
		<th style=\"width:100px\">주문인우편번호(숫자6자리)</th>
		<th style=\"width:100px\">주문인주소( 영문 140자이내 공백포함)</th>
		<th style=\"width:100px\">주문인명( 영문 35자이내 공백포함)</th>
		<th style=\"width:100px\">주문인전화 국가번호(숫자4자리)</th>
		<th style=\"width:100px\">주문인전화 지역번호(숫자4자리)</th>
		<th style=\"width:100px\">주문인전화국번호(숫자4자리)</th>
		<th style=\"width:100px\">주문인전화뒷번호(숫자4자리)</th>
		<th style=\"width:100px\">주문인 전화 전체번호 국가번호-지역번호-국번-전화번호( 숫자, - 허용)ex. 86-062-678-1234</th>
		<th style=\"width:100px\">주문인휴대전화 지역번호(숫자3자리)</th>
		<th style=\"width:100px\">주문인휴대전화국번호(숫자4자리)</th>
		<th style=\"width:100px\">주문인휴대전화뒷번호(숫자4자리)</th>
		<th style=\"width:100px\">주문인 휴대전화 전체지역번호-국번-뒷번호(숫자, - 허용)ex. 010-1234-5678</th>
		<th style=\"width:100px\">주문인EMAIL( 영문 40자이내)</th>
		<th style=\"width:100px\">수출우편물 정보 관세청 제공 여부(Y/N)</th>
		<th style=\"width:100px\">사업자번호(숫자10자리)</th>
		<th style=\"width:100px\">수출화주이름 또는 상호(수출우편물 정보 관세청 제공 동의시 필수)</th>
		<th style=\"width:100px\">수출이행등록여부(Y/N)</th>
		<th style=\"width:100px\">수출신고번호1(14~15자리)</th>
		<th style=\"width:100px\">전량분할발송여부(Y:전량,N:분할)</th>
		<th style=\"width:100px\">선기적포장개수</th>
		<th style=\"width:100px\">수출신고번호2(14~15자리)</th>
		<th style=\"width:100px\">전량분할발송여부(Y:전량,N:분할)</th>
		<th style=\"width:100px\">선기적포장개수</th>
		<th style=\"width:100px\">수출신고번호3(14~15자리)</th>
		<th style=\"width:100px\">전량분할발송여부(Y:전량,N:분할)</th>
		<th style=\"width:100px\">선기적포장개수</th>
		<th style=\"width:100px\">수출신고번호4(14~15자리)</th>
		<th style=\"width:100px\">전량분할발송여부(Y:전량,N:분할)</th>
		<th style=\"width:100px\">선기적 포장개수</th>
		<th style=\"width:100px\">추천우체국코드(POSA만 사용)5자리숫자</th>
		<th style=\"width:100px\">수출면장여부(Y/N)</th>
		<th style=\"width:100px\">브라질/인도네시아세금식별번호(* 브라질행 EMS, K-Packet의 경우 필수 입력 * 인도네시아행 선택)</th>
		<th style=\"width:100px\">가로(cm)</th>
		<th style=\"width:100px\">세로(cm)</th>
		<th style=\"width:100px\">높이(cm)</th>
		<th style=\"width:100px\">부피중량 적용 제외 여부(Y/N)</th>
		<th style=\"width:100px\">IOSS/EORI/TAX NUMBER식별 번호</th>
    </tr>
";

if($warehouse == 1000)
	$sql_search = " (wr_warehouse = 1000 or wr_warehouse = 9000) ";
else if($warehouse == 3000)
	$sql_search = " wr_warehouse = 3000 ";


$sql_order = "order by seq desc";

$sql_search .= " and seq IN (".implode(',', $_POST['seq']).") ";

$sql = "select * from g5_sales3_list where {$sql_search} {$sql_order} ";
$rst = sql_query($sql);
for ($i=0; $row=sql_fetch_array($rst); $i++) {

$item = sql_fetch("select * from g5_write_product where wr_id = '{$row['wr_product_id']}'");

$release_state = "X";
if($row['wr_release_use'] == 1) {
	$release_state = 'O';
}

$tel = preg_replace('/[^0-9]*/s', '', $row['wr_deli_tel']);
$weight2 = 0;
$singo = 0;

//주문번호에서 A가 포함되어있다면 합배송으로 처리.
if (strpos($row['wr_order_num'], 'A') !== false) {
	
	$sOrdernum = preg_replace("/[^0-9]/", "", $row['wr_order_num']); //합배송 문자열 제거
	$hapSql = "select * from g5_sales3_list where wr_order_num LIKE '%$sOrdernum%'";
	$hapRst = sql_query($hapSql);
	while($hap=sql_fetch_array($hapRst)) {
		
		$singo += $hap['wr_singo'];
		$weight2 += $hap['wr_weight2'];
		
		
		
	}
} else {
	$weight2 = $row['wr_weight2'];
	$singo = $row['wr_singo'];
	
	
}

if((int)$item['wr_16'] > 89) {
	$height_chk = "N";
} else {
	$height_chk = "Y";
}

$EXCEL_FILE .= "
    <tr>
		<td>GIFT</td>
		<td>".$row['wr_mb_name']." ".$row['wr_order_num']."</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td>".$tel."</td>
		<td>".$row['wr_deli_country']."</td>
		<td></td>
		<td>".$row['wr_deli_zip']."</td>
		<td>".$row['wr_deli_addr1']." ".$row['wr_deli_addr2']."</td>
		<td>".$row['wr_deli_city']."</td>
		<td>".$row['wr_deli_ju']."</td>
		<td></td>
		<td>".$weight2."</td>
		<td>".$row['wr_name2']."</td>
		<td>".$weight2."</td>
		<td>".$singo."</td>
		<td>USD</td>
		<td>".$row['wr_hscode']."</td>
		<td>".$row['wr_delivery_fee']."</td>
		<td>".$row['wr_delivery_fee2']."</td>
		<td>KR</td>
		<td></td>
		<td>N</td>
		<td></td>
		<td>R</td>
		<td>re</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td>".$item['wr_14']."</td>
		<td>".$item['wr_15']."</td>
		<td>".$item['wr_16']."</td>
		<td>".$height_chk."</td>
		<td></td>
    </tr>
";
}

$EXCEL_FILE .= "</table>";

// 만든 테이블을 출력해줘야 만들어진 엑셀파일에 데이터가 나타납니다.
echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
echo $EXCEL_FILE;