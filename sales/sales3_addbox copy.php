<?php 
include_once('./_common.php');

$row = sql_fetch("select * from g5_sales3_list where seq = '{$seq}'");
//상품마스터 SKU값으로만 매칭 되도록 수정요청.
$item = sql_fetch("select *,IF(wr_18 > wr_19,wr_18,wr_19) AS wr_weight3 from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");

//231228 대표님 요청으로 수정 
$item_link = "location.href='".G5_BBS_URL."/write.php?bo_table=product&w=u&wr_id=".$row['wr_product_id']."'";


if($first == 1 && preg_match('/[a-zA-Z]/', $row['wr_order_num'])) {
	
	//합배송 관련 수정.
	//기획에 없었으나 수정요청으로 합배송은 리스트상에서 최상위 주문건만 컨트롤되도록 하기 위함.
	
	$sOrdernum = preg_replace("/[^0-9]/", "", $row['wr_order_num']); //합배송 문자열 제거
	$hap_ea = 0;
	$hap_danga = 0;
	$hap_singo = 0;
	$hap_weight2 = 0; // 총 무게
    $hap_weight3 = 0; // 부피 총합
	
	$hapSql = "select * from g5_sales3_list where wr_order_num LIKE '%$sOrdernum%'";
	$hapRst = sql_query($hapSql);

	while($hap=sql_fetch_array($hapRst)) {
        $wr_weight3 = sql_fetch("SELECT *,IF(wr_18 > wr_19,wr_18,wr_19) AS wr_weight3 FROM g5_write_product WHERE wr_id = '{$hap['wr_product_id']}' ")['wr_weight3'];
		$hap_ea += $hap['wr_ea'];
		$hap_danga += $hap['wr_danga'];
		$hap_singo += $hap['wr_singo'];
		$hap_weight2 += $hap['wr_weight2'];            // 총 무게
		$hap_weight3 += ($wr_weight3 * $hap['wr_ea']); // 부피 * 수량
		$hap_product[] = $hap['wr_code']."|@@|".$hap['wr_order_num'];
	}


?>
<div class="tbl_frm01 tbl_wrap" style="margin:0">
    <input type="hidden" name="seq" value="<?php echo $seq?>">
    <table>
        <tr>
            <th>도메인명</th>
            <td><input type="text" name="wr_domain" value="<?php echo $row['wr_domain']?>" class="readonly" readonly></td>
            <th>매출일자</th>
            <td><input type="date" name="wr_date" value="<?php echo $row['wr_date']?>" class="readonly" readonly></td>
            <th>주문번호</th>
            <td><input type="text" name="wr_order_num" value="<?php echo $sOrdernum?> [합배송]" class="readonly" readonly></td>
            <th>주문자ID</th>
            <td><input type="text" name="wr_mb_id" value="<?php echo $row['wr_mb_id']?>" class="readonly" readonly></td>
            <th>주문자명</th>
            <td><input type="text" name="wr_mb_name" value="<?php echo $row['wr_mb_name']?>" class="readonly" readonly></td>
        </tr>
        <tr>
            <th>우편번호</th>
            <td><input type="text" name="wr_zip" value="<?php echo $row['wr_zip']?>" class="readonly" readonly></td>
            <th>주소</th>
            <td colspan="7" style="text-align:left">
            <input type="text" name="wr_addr1" value="<?php echo $row['wr_addr1']?>" style="width:48%" class="readonly" readonly>
            <input type="text" name="wr_addr2" value="<?php echo $row['wr_addr2']?>" style="width:48%" class="readonly" readonly>
            </td>
        </tr>

        <tr>
            <th>도시명</th>
            <td><input type="text" name="wr_city" value="<?php echo $row['wr_city']?>" class="readonly" readonly></td>
            <th>주명</th>
            <td><input type="text" name="wr_ju" value="<?php echo $row['wr_ju']?>" class="readonly" readonly></td>
            <th>나라명</th>
            <td><input type="text" name="wr_country" value="<?php echo $row['wr_country']?>" class="readonly" readonly></td>
            <th>전화번호</th>
            <td><input type="text" name="wr_tel" value="<?php echo $row['wr_tel']?>" class="readonly" readonly></td>
            <th>이메일</th>
            <td><input type="text" name="wr_email" value="<?php echo $row['wr_email']?>" class="readonly" readonly></td>
        </tr>
    </table>

    <table style="margin-top:20px">
        <?php 
        $no=0;

        foreach($hap_product as $product) {
            
            $info = explode('|@@|', $product);
            
            //sku값으로 만 조회. 추후 wr_id로 매칭해야될 것
            $item2 = sql_fetch("select *,IF(wr_18 > wr_19 , wr_18 , wr_19) AS wr_weight3 from g5_write_product where (wr_1 = '".addslashes($info[0])."' or wr_27 = '".addslashes($info[0])."' or wr_28 = '".addslashes($info[0])."' or wr_29 = '".addslashes($info[0])."' or wr_30 = '".addslashes($info[0])."' or wr_31 = '".addslashes($info[0])."') ");
            
            $orderRow = sql_fetch("select * from g5_sales3_list where wr_order_num = '{$info[1]}'");
        ?>
        <tr>
            <th>제품정보<?php echo ($no+1)?></th>
            <td colspan="10">
            <input type="text" name="wr_code" value="<?php echo $info[0]?>" style="width:20%" class="readonly" readonly>
            
            
            <input type="text" name="wr_product_name2" value="<?php echo $item2['wr_subject']?>" style="width:58%" class="readonly" readonly>
            <?php if($orderRow['wr_direct_use'] == 1) {?>
            
            
            <?php if($orderRow['wr_release_use'] == 1) {?>
            <input type="text" value="<?php echo get_rack_name($orderRow['wr_rack'])?>" class="readonly" readonly style="width:20%">
            <?php } else {?>
            <select name="wr_rack[<?php echo $orderRow['seq']?>]" id="wr_rack" style="width:20%" >
                
                <?php 
                $sql_common = " from g5_rack ";
                $sql_search = " where gc_warehouse = '{$orderRow['wr_warehouse']}' and gc_use = 1 order by gc_name asc";
                $sql = " select * {$sql_common} {$sql_search}  ";
                $result = sql_query($sql);
                for($i=0; $rack=sql_fetch_array($result); $i++) {
                    
                    $item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($orderRow['wr_code'])."' or wr_27 = '".addslashes($orderRow['wr_code'])."' or wr_28 = '".addslashes($orderRow['wr_code'])."' or wr_29 = '".addslashes($orderRow['wr_code'])."' or wr_30 = '".addslashes($orderRow['wr_code'])."' or wr_31 = '".addslashes($orderRow['wr_code'])."') ");
                    
                    $stock = sql_fetch("select *, SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '{$rack['gc_warehouse']}' and wr_rack = '{$rack['seq']}' and wr_product_id = '{$item['wr_id']}' ");
                    
                    if($stock['total'] <= 0) continue;
                ?>
                <option value="<?php echo $rack['seq']?>"><?php echo $rack['gc_name']?> (재고:<?php echo $stock['total']?>)</option>
                <?php }?>
            </select>
            
            <?php }
            
            }?>
            
            </td>
            
        </tr>
        <?php $no++;
        }?>
        <tr>
            <th>총 수량</th>
            <td><input type="text" name="wr_ea" value="<?php echo $hap_ea?>" class="readonly" readonly></td>
            <th>박스수</th>
            <td><input type="text" name="wr_box" value="1" class="readonly" readonly></td>
            <th>총 단가</th>
            <td><input type="text" name="wr_danga" value="<?php echo $hap_danga?>" class="readonly" readonly></td>
            <th>총 신고가격</th>
            <td><input type="text" name="wr_singo" value="<?php echo $hap_singo?>" class="readonly" readonly></td>
            <th>통화</th>
            <td><input type="text" name="wr_currency" value="<?php echo $row['wr_currency']?>" class="readonly" readonly></td>
        </tr>
        <tr>
            <th>개당무게</th>
            <td><input type="text" name="wr_weight1" value="<?php echo $row['wr_weight1']?>" class="readonly" readonly></td>
            <th>총 부피</th>
            <td><input type="text" name="" value="<?php echo $hap_weight3?>" class="readonly" readonly></td>
            <th>총 무게</th>
            <td><input type="text" name="wr_weight2" id="wr_weight2" value="<?php echo $hap_weight2?>" class="readonly" readonly></td>
            <th>무게단위</th>
            <td><input type="text" name="" value="<?php echo $item['wr_11']?>" class="readonly" readonly></td>
            <th>HS코드</th>
            <td><input type="text" name="wr_hscode" value="<?php echo $row['wr_hscode']?>" class="readonly" readonly></td>
        </tr>
        <tr id="delivery_frm">

            <!-- // 부피계산이 완료 된 후에 추가됨 24-01-10
            <th>배송사</th>
            <td>
            <?php if($row['wr_servicetype'] != "0003") {?>
            <select name="wr_delivery" id="wr_delivery">
                <?php 
                
                $delivery_sql = "select * from g5_delivery_company where wr_use = 1";
                $delivery_rst = sql_query($delivery_sql);
                while($delivery_com=sql_fetch_array($delivery_rst)) {
                    $delivery[$delivery_com['wr_code']] = $delivery_com['wr_name'];
                }
                
                $country_code2 = get_country_krname($row['wr_country']); //나라명으로 erp배송사 코드조회하기위하여 한글명으로 변경
                
                $country_dcode = sql_fetch("select wr_code as code from g5_country where wr_country_ko = '{$country_code2}'"); 
                $country = $country_dcode['code'];
                
                $sql = "select {$country} as price, cust_code, weight_code from g5_shipping_price where weight_code >= '{$row['wr_weight2']}' and {$country} != 0  group by cust_code order by price asc";
                $rst = sql_query($sql);
                for ($i=0; $delivery2=sql_fetch_array($rst); $i++) {
                ?>
                <option value="<?php echo $delivery2['cust_code']?>" data="<?php echo $delivery2['price']?>" <?php echo get_selected($row['wr_deilivery'], $delivery2['cust_code'])?>><?php echo $delivery[$delivery2['cust_code']]?></option>
                    
                <?php 
                if($i == 0 && $row['wr_delivery_fee'] == 0) $low_delivery_fee = $delivery2['price']; //첫번째 배송비 저장
                }?>
            </select>
            <?php }?>
            </td>
            <th>배송요금</th>
            <td><input type="text" name="wr_delivery_fee" id="wr_delivery_fee" value="<?php echo $row['wr_servicetype']=="0003" ? "" : $low_delivery_fee?>" class="" ></td>
            -->

            <th>Service Type</th>
            <td><input type="text" name="wr_servicetype" value="<?php echo $row['wr_servicetype']?>" class="readonly" readonly></td>
            <th>packaging</th>
            <td><input type="text" name="wr_packaging" value="<?php echo $row['wr_packaging']?>" class="readonly" readonly></td>
            <th>제조국가</th>
            <td><input type="text" name="wr_make_country" value="<?php echo $row['wr_make_country']?>" class="readonly" readonly></td>
        </tr>
        <tr>
            <th>수출신고품명</th>
            <td><input type="text" name="wr_name2" value="<?php echo $row['wr_name2']?>" class="readonly" readonly></td>
            <th>수출국가</th>
            <td><input type="text" name="wr_country_code" value="<?php echo $row['wr_country_code']?>" class="readonly" readonly></td>
            <th>발주일자</th> 
            <td><input type="date" name="wr_date2" value="<?php echo $row['wr_date2']?>"  class="readonly" readonly></td>
            <th>입고일자</th> 
            <td><input type="date" name="wr_date3" value="<?php echo $row['wr_date3']?>" class="readonly" readonly ></td>
            <th>출고일자</th> 
            <td><input type="date" name="wr_date4" value="<?php echo $row['wr_date4']?>" class="readonly" readonly ></td>
            
        </tr>
        
        <tr>
            <th>수출트래킹NO</th>
            <td>
                <input type="text" name="wr_release_traking" value="<?php echo $row['wr_release_traking']?>" style="width:69%">
                <button type="button" onclick="release_traking_update()" class="btn_b01" >수정</button>
            </td>
            <th>비고</th>
            <td colspan="8"><input type="text" name="wr_release_etc" value="<?php echo $row['wr_release_etc']?>"></td>
        </tr>
    </table>

    <div class="habInfoFrm" style="padding:10px;">
        <ul style="display:inline-block; list-style:none;">
            <li style="padding-right:10px;">
                <label>가로</label>
                <input type="text" name="wr_hab_x" id="hab_x" class="frm_input number_fmt_list" />mm
            </li>
            <li style="padding-right:10px;">
                <label>세로</label>
                <input type="text" name="wr_hab_y" id="hab_y" class="frm_input number_fmt_list" />mm
            </li>
            <li style="padding-right:10px;">
                <label>높이</label>
                <input type="text" name="wr_hab_z" id="hab_z" class="frm_input number_fmt_list" />mm
            </li>
        </ul>

        <ul style="display:inline-block; list-style:none;height:120px;">
            <li >
                <label>부피무게1</label>
                <input type="text" name="wr_weight_sum1" id="hab_weight1" class="frm_input number_fmt_list" />kg
            </li>
            <li>
                <label>부피무게2</label>
                <input type="text" name="wr_weight_sum2" id="hab_weight2" class="frm_input number_fmt_list" />kg
            </li>
            <li style="text-align:left;padding:5px;">
                <button type="button" class="btn_b01 " id="hab_cal_btn" >계산</button>
                <button type="button" class="btn_b01 " style="background:#0397C7;" id="now_hab_cal_btn" >직접부피입력</button>
                <button type="button" class="btn_b02 " id="hab_reset_btn" >초기화</button>
            </li>
        </ul>
    </div>
    <script>
        $(function(){
            $(".number_fmt_list").bind('input',function(){
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            });

            // 중량무게1,중량무게2
            $("#hab_cal_btn").on('click',function(){
                let hab_x = $("#hab_x").val(),
                    hab_y = $("#hab_y").val(),
                    hab_z = $("#hab_z").val(),
                    weigth_sum1 = (parseInt(hab_x) * parseInt(hab_y) * parseInt(hab_z))/5000000,
                    weigth_sum2 = (parseInt(hab_x) * parseInt(hab_y) * parseInt(hab_z))/6000000;

                    console.log(parseInt(hab_x),parseInt(hab_y),parseInt(hab_z));
                if(hab_x==""){
                    alert("가로를 입력해주세요.");
                    $("#hab_x").focus();
                    return false;
                }
                if(hab_y==""){
                    alert("세로를 입력해주세요.");
                    $("#hab_y").focus();
                    return false;
                }
                if(hab_z==""){
                    alert("높이를 입력해주세요.");
                    $("#hab_z").focus();
                    return false;
                }
                //console.log(hab_x,hab_y,hab_z,weigth_sum1,weigth_sum2);

                $("#hab_weight1").val(weigth_sum1.toFixed(2));
                $("#hab_weight2").val(weigth_sum2.toFixed(2));
                
                delivery_list();
            });

            // 직접부피입력
            $("#now_hab_cal_btn").on('click',function(){
                let hab_weight1 = $("#hab_weight1").val(),
                    hab_weight2 = $("#hab_weight2").val();

                if(hab_weight1==""){
                    alert("부피무게1을 입력해주세요.");
                    $("#hab_weight1").focus();
                    return false;
                }
                
                if(hab_weight2==""){
                    alert("부피무게2을 입력해주세요.");
                    $("#hab_weight2").focus();
                    return false;
                }

                delivery_list();
            });


            // 중량무게1,중량무게2
            $("#hab_reset_btn").on('click',function(){
                $("#hab_x,#hab_y,#hab_z,#hab_weight1,#hab_weight2").val("");
            });
        });


        // 중량무게 입력완료시 배송사,배송요금 조회 추출
        function delivery_list(){
            // 초기화
            $(".del_chk").remove();

            // if($("input[name=wr_servicetype]").val()=="0003"){
            //     let stx = `
            //     <th class="del_chk">배송사</th>
            //     <td class="del_chk"><select name="wr_delivery" id="wr_delivery"></select></td>
                
            //     <th class="del_chk">배송요금</th>
            //     <td class="del_chk"><input type="text" name="wr_delivery_fee" /></td>
            //     `;
            //     alert("해당 배송코드(0003)는 배송사와 배송요금을 조회할 수 없습니다.");

            //     $("#delivery_frm").prepend(stx);
            //     return false;
            // }

            let seq = $("input[name=seq]").val(), //나라명
                wr_country = $("input[name=wr_country]").val(), //나라명
                weight1 = parseFloat($("#wr_weight2").val()),  //총 무게
                weight2 = parseFloat($("#hab_weight1").val()), //부피무게1
                weight3 = parseFloat($("#hab_weight2").val()), //부피무게2
                max = Math.max(weight1,weight2,weight3); // 가장 큰 수 구하기

            const obj = {
                seq        : seq,
                wr_country : wr_country,
                max_weight : max,
            }    

            $.post(g5_url+"/sales/ajax.sales3_update.php", obj, function(data){           
                // console.log(data);     
                $("#delivery_frm").prepend(data);
            });    

        }

    </script>

    <div class="win_btn btn_confirm" style="margin-top:20px;text-align:center">
        <?php if($row['wr_release_use'] == 0) {?>
        <input type="button" value="합배송 출고완료 처리" class="btn_submit btn_b01 hap_release_btn" id="frm_submit" style="background:#2a56ba" data="<?php echo $sOrdernum?>">
        <?php }?>
        <input type="button" value="상품 바로가기" class="btn_submit btn_b02" style="cursor:pointer;" onclick="<?=$item_link?>" >
    </div>
</div>


<?php
} else {
?>

<div class="tbl_frm01 tbl_wrap" style="margin:0">
    <input type="hidden" name="seq" value="<?php echo $seq?>">
    <table>
        <tr>
            <th>도메인명</th>
            <td><input type="text" name="wr_domain" value="<?php echo $row['wr_domain']?>" class="readonly" readonly></td>
            <th>매출일자</th>
            <td><input type="date" name="wr_date" value="<?php echo $row['wr_date']?>" class="readonly" readonly></td>
            <th>주문번호</th>
            <td><input type="text" name="wr_order_num" value="<?php echo $row['wr_order_num']?>" class="readonly" readonly></td>
            <th>주문자ID</th>
            <td><input type="text" name="wr_mb_id" value="<?php echo $row['wr_mb_id']?>" class="readonly" readonly></td>
            <th>주문자명</th>
            <td><input type="text" name="wr_mb_name" value="<?php echo $row['wr_mb_name']?>" class="readonly" readonly></td>
        </tr>
        <tr>
            <th>우편번호</th>
            <td><input type="text" name="wr_zip" value="<?php echo $row['wr_zip']?>" class="readonly" readonly></td>
            <th>주소</th>
            <td colspan="7" style="text-align:left">
            <input type="text" name="wr_addr1" value="<?php echo $row['wr_addr1']?>" style="width:48%" class="readonly" readonly>
            <input type="text" name="wr_addr2" value="<?php echo $row['wr_addr2']?>" style="width:48%" class="readonly" readonly>
            </td>
        </tr>
        
        <tr>
            <th>도시명</th>
            <td><input type="text" name="wr_city" value="<?php echo $row['wr_city']?>" class="readonly" readonly></td>
            <th>주명</th>
            <td><input type="text" name="wr_ju" value="<?php echo $row['wr_ju']?>" class="readonly" readonly></td>
            <th>나라명</th>
            <td><input type="text" name="wr_country" value="<?php echo $row['wr_country']?>" class="readonly" readonly></td>
            <th>전화번호</th>
            <td><input type="text" name="wr_tel" value="<?php echo $row['wr_tel']?>" class="readonly" readonly></td>
            <th>이메일</th>
            <td><input type="text" name="wr_email" value="<?php echo $row['wr_email']?>" class="readonly" readonly></td>
        </tr>
        
        <tr>
            <th>제품코드</th>
            <td colspan="10">
            <input type="text" name="wr_code" value="<?php echo $row['wr_code']?>" style="width:20%" class="readonly" readonly>
            <input type="text" name="wr_product_name1" value="<?php echo $item['wr_2']?>" style="width:20%" class="readonly" readonly>
            <input type="text" name="wr_product_name2" value="<?php echo $item['wr_subject']?>" style="width:58%" class="readonly" readonly>
            
            </td>
            
        </tr>
        <tr>
            <th>수량</th>
            <td><input type="text" name="wr_ea" value="<?php echo $row['wr_ea']?>" class="readonly" readonly></td>
            <th>박스수</th>
            <td><input type="text" name="wr_box" value="<?php echo $row['wr_box']?>" class="readonly" readonly></td>
            <th>단가</th>
            <td><input type="text" name="wr_danga" value="<?php echo $row['wr_danga']?>" class="readonly" readonly></td>
            <th>신고가격</th>
            <td><input type="text" name="wr_singo" value="<?php echo $row['wr_singo']?>" class="readonly" readonly></td>
            <th>통화</th>
            <td><input type="text" name="wr_currency" value="<?php echo $row['wr_currency']?>" class="readonly" readonly></td>
        </tr>
        <tr>
            <th>개당무게</th>
            <td><input type="text" name="wr_weight1" value="<?php echo $row['wr_weight1']?>" class="readonly" readonly></td>
            <th>부피</th>
            <td><input type="text" name="wr_weight3" value="<?php echo ($item['wr_weight3'] * $row['wr_ea'])?>" class="readonly" readonly></td>
            <th>총 무게</th>
            <td><input type="text" name="wr_weight2" value="<?php echo $row['wr_weight2']?>" class="readonly" readonly></td>
            <th>무게단위</th>
            <td><input type="text" name="" value="<?php echo $item['wr_11']?>" class="readonly" readonly></td>
            <th>HS코드</th>
            <td><input type="text" name="wr_hscode" value="<?php echo $row['wr_hscode']?>" class="readonly" readonly></td>
        </tr>
        <tr>
            
            <th>배송사</th>
            <td>
            <?php if($row['wr_servicetype'] != "0003") {?>
            <select name="wr_delivery" id="wr_delivery">
                <?php 
                $delivery_sql = "select * from g5_delivery_company where wr_use = 1";
                $delivery_rst = sql_query($delivery_sql);
                while($delivery_com=sql_fetch_array($delivery_rst)) {
                    $delivery[$delivery_com['wr_code']] = $delivery_com['wr_name'];
                }
                
                $country_code2 = get_country_krname($row['wr_country']); //나라명으로 erp배송사 코드조회하기위하여 한글명으로 변경
                
                $country_dcode = sql_fetch("select wr_code as code from g5_country where wr_country_ko = '{$country_code2}'"); 
                $country = $country_dcode['code'];
                
                $sql = "select {$country} as price, cust_code, weight_code from g5_shipping_price where weight_code >= '{$row['wr_weight2']}' and {$country} != 0  group by cust_code order by price asc";
                $rst = sql_query($sql);
                for ($i=0; $delivery2=sql_fetch_array($rst); $i++) {
                ?>
                <option value="<?php echo $delivery2['cust_code']?>" data="<?php echo $delivery2['price']?>" <?php echo get_selected($row['wr_deilivery'], $delivery2['cust_code'])?>><?php echo $delivery[$delivery2['cust_code']]?></option>
                    
                <?php 
                if($i == 0 && $row['wr_delivery_fee'] == 0) $low_delivery_fee = $delivery2['price']; //첫번째 배송비 저장
                }?>
            </select>
            <?php }?>
            </td>
            <th>배송요금</th>
            <td><input type="text" name="wr_delivery_fee" id="wr_delivery_fee" value="<?php echo $row['wr_servicetype']=="0003" ? "" : $low_delivery_fee?>" class="" ></td>
            
            
            
            <th>Service Type</th>
            <td><input type="text" name="wr_servicetype" value="<?php echo $row['wr_servicetype']?>" class="readonly" readonly></td>
            <th>packaging</th>
            <td><input type="text" name="wr_packaging" value="<?php echo $row['wr_packaging']?>" class="readonly" readonly></td>
            <th>제조국가</th>
            <td><input type="text" name="wr_make_country" value="<?php echo $row['wr_make_country']?>" class="readonly" readonly></td>
        </tr>
        <tr>
            <th>수출신고품명</th>
            <td><input type="text" name="wr_name2" value="<?php echo $row['wr_name2']?>" class="readonly" readonly></td>
            <th>수출국가</th>
            <td><input type="text" name="wr_country_code" value="<?php echo $row['wr_country_code']?>" class="readonly" readonly></td>
            <th>발주일자</th> 
            <td><input type="date" name="wr_date2" value="<?php echo $row['wr_date2']?>"  class="readonly" readonly></td>
            <th>입고일자</th> 
            <td><input type="date" name="wr_date3" value="<?php echo $row['wr_date3']?>" class="readonly" readonly ></td>
            <th>출고일자</th> 
            <td><input type="date" name="wr_date4" value="<?php echo $row['wr_date4']?>" class="readonly" readonly ></td>
            
        </tr>
        <tr>
            <th>발주주문번호</th>
            <td><input type="text" name="wr_order_num2" value="<?php echo $row['wr_order_num2']?>" class="readonly" readonly></td>
            <th>발주처</th>
            <td><input type="text" name="wr_orderer" value="<?php echo $row['wr_orderer']?>" class="readonly" readonly></td>
            <th>발주수량</th>
            <td><input type="text" name="wr_order_ea" value="<?php echo $row['wr_order_ea']?>" class="readonly" readonly></td>
        
            <th>입고창고</th>
            <td><input type="text" name="wr_warehouse" value="<?php echo $row['wr_warehouse']?>" class="readonly" readonly></td>
            <th>트래킹NO</th>
            <td ><input type="text" name="wr_order_traking" value="<?php echo $row['wr_order_traking']?>" class="readonly" readonly></td>
        </tr>
        <tr>
            <?php if($row['wr_direct_use'] == 1) {?>
            <th>출고 랙</th>
            <td>
            <?php if($row['wr_release_use'] == 1) {?>
            <input type="text" value="<?php echo get_rack_name($row['wr_rack'])?>" class="readonly" readonly>
            <?php } else {?>
            <select name="wr_rack" id="wr_rack" >
                <?php 
                $sql_common = " from g5_rack ";
                $sql_search = " where gc_warehouse = '{$row['wr_warehouse']}' and gc_use = 1 order by gc_name asc";
                $sql = " select * {$sql_common} {$sql_search}  ";
                $result = sql_query($sql);
                for($i=0; $rack=sql_fetch_array($result); $i++) {
                    
                    $item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");
                    
                    $stock = sql_fetch("select *, SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '{$rack['gc_warehouse']}' and wr_rack = '{$rack['seq']}' and wr_product_id = '{$item['wr_id']}' ");
                    
                    if($stock['total'] <= 0) continue;
                ?>
                <option value="<?php echo $rack['seq']?>"><?php echo $rack['gc_name']?> (재고:<?php echo $stock['total']?>)</option>
                <?php }?>
            </select>
            </td>
            <?php }?>
            
            
            <th>수출트래킹NO</th>
            <td>
                <input type="text" name="wr_release_traking" value="<?php echo $row['wr_release_traking']?>" style="width:69%">
                <button type="button" onclick="release_traking_update()" class="btn_b01" >수정</button>
            </td>
            <th>비고</th>
            <td colspan="6"><input type="text" name="wr_release_etc" value="<?php echo $row['wr_release_etc']?>"></td>
            <?php } else {?>
            <th>수출트래킹NO</th>
            <td>
                <input type="text" name="wr_release_traking" value="<?php echo $row['wr_release_traking']?>" style="width:69%">
                <button type="button" onclick="release_traking_update()" class="btn_b01" >수정</button>
            </td>
            <th>비고</th>
            <td colspan="8"><input type="text" name="wr_release_etc" value="<?php echo $row['wr_release_etc']?>"></td>
            <?php }?>
        </tr>
    </table>
    <div class="win_btn btn_confirm" style="margin-top:20px;text-align:center">

        <?php if($first == 1){ ?>
        <input type="button" value="저장" class="btn_submit btn_b01 addbtn1" id="frm_submit">
        <?php    if($row['wr_release_use'] == 0) {?>
        <input type="button" value="출고완료 처리" class="btn_submit btn_b01 addbtn2" id="frm_submit" style="background:#2a56ba" data="<?php echo $row['seq']?>">
        <?php  
                }
            } 
        ?>
        <input type="button" value="상품 바로가기" class="btn_submit btn_b02" style="cursor:pointer;" onclick="<?=$item_link?>" >
    </div>
</div>
<?php }?>

<script>
    // 수출트래킹NO만 수정 (따로 수정버튼 없어서 추가) 01-11
    function release_traking_update(){
        let seq = $("input[name=seq]").val(),
            wr_release_traking = $("input[name=wr_release_traking]").val();

            //console.log(seq,wr_release_traking);
        $.post(g5_url+"/sales/ajax.traking_update.php",{seq:seq,wr_release_traking:wr_release_traking},function(data){
            if(data=='y'){
                alert("수출트래킹NO가 수정이 완료되었습니다.");
            }else{
                alert("오류가 발생했습니다.");
            }
        });

    }
</script>