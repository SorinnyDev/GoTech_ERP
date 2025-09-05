<?
	//session_start();


	//오늘날자
	$now_Ymd = date("Y-m-d");

	//현재시간
	$now_His = date("H:i:s");


	$db_link = null;

	function db_connect_go(){

		global $db_link;
		global $EV_DB_SERVER, $EV_DB_USER, $EV_DB_PASS, $EV_DB_NAME, $EV_DB_CHARSET;
		
		if( $db_link == null ){
			$db_link = mysqli_connect($EV_DB_SERVER, $EV_DB_USER, $EV_DB_PASS, $EV_DB_NAME);
			if (!$db_link) {
				
				die('사용할수 없는 데이터베이스 입니다. 관리자에 문의하시기 바랍니다: ' . mysqli_error());

			}

			if (!mysqli_select_db($db_link, $EV_DB_NAME)) {
				
				die('데이터베이스 세션이 끊어졌습니다. 관리자에 문의하시기 바랍니다: ' . mysqli_error());

			}

			mysqli_query(" set names $EV_DB_CHARSET ");
		}
		
		
	}


	function db_close(){

		global $db_link;
		
		if( $db_link != null ){
			mysqli_close($db_link);
			$db_link = null;
		}

	}
	
	function get_int($psql){
		$v_val = get_val($psql);
		
		if($v_val==null) return 0;
		if($v_val=="") return 0;
		
		return fnCInt($v_val);
	}
	
	function get_val($psql){

		global $db_link;

	
		db_connect_go();



		if( $db_link != null ){

			$result = mysqli_query($db_link, $psql);
			
			if (!$result) {
				
				die("사용할수 없는 데이터베이스 입니다. 관리자에 문의하시기 바랍니다.\n". mysqli_error());
				exit;

			}

			while ($row = mysqli_fetch_array($result)) {
				return $row[0];
				break;
			}
			
			mysqli_free_result($result);
		}
		
		return "";
	}

	function get_row($psql){
		$v_val = array();

		$result = get_rows($psql);
		for($i=0;$i<count($result);$i++){
			$v_val = $result[$i];
			break;
		}

		return $v_val;
	}


	function get_rows($psql){
		global $db_link;

		db_connect_go();

		if( $db_link != null ){
			$result = mysqli_query($db_link, $psql);

			if (!$result) {
				die("사용할수 없는 데이터베이스 입니다. 관리자에 문의하시기 바랍니다.\n". mysqli_error());
				exit;

			}

			$i = 0;
			while ($row = mysqli_fetch_assoc($result)) {
				$rows[$i] = $row;
				$i++;
			}

			mysqli_free_result($result);
		}

		return $rows;
	}

	function execute_query($sql){

		global $db_link;

		db_connect_go();
		
		if( $db_link != null ){
			return mysqli_query($db_link, $sql);
		}else{
			return null;
		}
	}








	//########## 로그인 관련 시작 ##########
	$fLogin = new clsLogin("user_id","user_nm","user_kind","user_info");
	$aLogin = new clsLogin("admin_id","admin_nm","admin_kind","admin_info");
	
	class clsLogin {
		protected $sessIdNm = "";
		protected $sessNameNm = "";
		protected $sessKindNm = "";
		protected $sessInfoNm = null;	//추가로 필요한 회원정보 배열이나 객체값을 저장합니다.
		
		function __construct($sessIdNm,$sessNameNm,$sessKindNm,$sessInfoNm) {
			$this->sessIdNm = $sessIdNm;
			$this->sessNameNm = $sessNameNm;
			$this->sessKindNm = $sessKindNm;
			$this->sessInfoNm = $sessInfoNm;
		}
		function __destruct() {
		}
		
		function isLogin(){
			if($_SESSION[$this->sessIdNm]==null || $_SESSION[$this->sessIdNm]==""){
				return false;
			}else{
				return true;
			}
		}
		
		public function logout(){
			$this->setId("");
			$this->setName("");
			$this->setKind("");
			$this->setInfo(null);
			session_unset();
		}
		public function login($id, $name, $kind, $info){
			$this->setId($id);
			$this->setName($name);
			$this->setKind($kind);
			$this->setInfo($info);
		}
		
		public function getId(){ return $_SESSION[$this->sessIdNm]; }
		public function setId($val){ $_SESSION[$this->sessIdNm] = $val; }
		
		public function getName(){ return $_SESSION[$this->sessNameNm]; }
		public function setName($val){ $_SESSION[$this->sessNameNm] = $val; }
		
		public function getKind(){ return $_SESSION[$this->sessKindNm]; }
		public function setKind($val){ $_SESSION[$this->sessKindNm] = $val; }
		
		public function getInfo(){ return $_SESSION[$this->sessInfoNm]; }
		public function setInfo($val){ $_SESSION[$this->sessInfoNm] = $val; }

		public function getVal($key){
			$info = $this->getInfo();
			return $info[$key]; 
		}
		public function setVal($key,$val){
			$info = &$this->getInfo();
			$info[$key] = $val;
		}
	}
	//########## //로그인 관련 시작 ##########

	function alertRedirect($msg,$url){
		global $EV_SITE_CHARSET;
		$msg = str_replace("\n","\\n",$msg);
		echo "
			<html>
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset={$EV_SITE_CHARSET}' />
			<script type='text/javascript'>
				alert(\"".$msg."\");
				location.replace('".$url."');
			</script>
			</head>
			</html>
		";
		pageExit();
	}
	function alertMove($msg,$url){
		global $EV_SITE_CHARSET;
		$msg = str_replace("\n","\\n",$msg);
		echo "
			<html>
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset={$EV_SITE_CHARSET}' />
			<script type='text/javascript'>
				if(\"".trim($msg)."\"!=\"\")alert(\"".$msg."\");
				document.location.href = '".$url."';
			</script>
			</head>
			</html>
		";
		pageExit();
	}
	function alertReload($msg){
		global $EV_SITE_CHARSET;
		$msg = str_replace("\n","\\n",$msg);
		echo "
			<html>
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset={$EV_SITE_CHARSET}' />
			<script type='text/javascript'>
				alert(\"".$msg."\");
				location.reload();
			</script>
			</head>
			</html>
		";
		pageExit();
	}
	function alertBack($msg){
		global $EV_SITE_CHARSET;
		$msg = str_replace("\n","\\n",$msg);
		echo "
			<html>
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset={$EV_SITE_CHARSET}' />
			<script type='text/javascript'>
				alert(\"".$msg."\");
				history.back();
			</script>
			</head>
			</html>
		";
		pageExit();
	}


	function alertBack_pop($msg){
		global $EV_SITE_CHARSET;
		$msg = str_replace("\n","\\n",$msg);
		echo "
			<html>
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset={$EV_SITE_CHARSET}' />
			<script type='text/javascript'>
				alert(\"".$msg."\");
				self.close();
			</script>
			</head>
			</html>
		";
		pageExit();
	}


	function callParentCallback($resultInfo){
		global $EV_SITE_CHARSET;
		echo "
		<html>
		<head>
	 	<meta http-equiv='Content-Type' content='text/html; charset={$EV_SITE_CHARSET}' />
		<script type='text/javascript'>
			var p = parent;
			if(p){
				if(p.procCompleteCallback){
					p.procCompleteCallback('".$resultInfo."');
				}
			}
		</script>
		</head>
		</html>
		";
		pageExit();
	}
	/**
	 * 로그인 페이지 이동, 현재 경로 가지고 이동
	 */
	function moveLoginPageReturn($msg){
		$url = "/member/memberLogin.php?url=".urlencode($_SERVER[REQUEST_URI]);
		alertMove($msg, $url);
	}
	function pageExit(){
		db_close();
		exit();
	}


	function alertOnly($msg){
		global $EV_SITE_CHARSET;
		$msg = str_replace("\n","\\n",$msg);
		echo "
			<html>
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset={$EV_SITE_CHARSET}' />
			<script type='text/javascript'>
				alert(\"".$msg."\");
			</script>
			</head>
			</html>
		";
		pageExit();
	}
	function alertClose($msg){
		global $EV_SITE_CHARSET;
		$msg = str_replace("\n","\\n",$msg);
		echo "
			<html>
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset={$EV_SITE_CHARSET}' />
			<script type='text/javascript'>
				if(\"".$msg."\"!=\"\")alert(\"".$msg."\");
				window.close();
			</script>
			</head>
			</html>
		";
		pageExit();
	}


	function printScript($scr){
		global $EV_SITE_CHARSET;
		echo "
			<html>
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset={$EV_SITE_CHARSET}' />
			<script type='text/javascript'>
				$scr
			</script>
			</head>
			</html>
		";
		pageExit();
	}

	function inject($val){
		if (!get_magic_quotes_gpc()) {
			$val = addslashes($val);
		}
		return $val;
	}

	function getRequest($key){
		return inject($_REQUEST[$key]);
	}
	
	function getRequestTrim($key){
		return ntrim(getRequest($key));
	}

	function getPost($key){
		return inject($_POST[$key]);
	}
	
	function getPostTrim($key){
		return ntrim(getPost($key));
	}

	function getGet($key){
		return inject($_GET[$key]);
	}
	
	function getGetTrim($key){
		return ntrim(getGet($key));
	}

	function getGetDef($key,$def){
		$v_val = inject($_GET[$key]);
		$v_val = ($v_val==null || trim($v_val)=="" ? $def : $v_val);
		return $v_val;
	}

	function file_upload($form_name, $sub_dir, $prefix){

		global $EV_UPLOAD_DIR, $EV_UPLOAD_EXT_ALL, $EV_UPLOAD_PATH;
		
		$fileName = $_FILES[$form_name][name];
		$fileSize = 0;
		$saveName = "";

		if($fileName!=null && $fileName!=""){

			$fileTemp = $_FILES[$form_name][tmp_name];

			$fileSize = $_FILES[$form_name][size];


		
			if(strpos($fileName, ".xlsx") !== false || strpos($fileName, ".docx") !== false || strpos($fileName, ".pptx") !== false || strpos($fileName, ".mpeg") !== false || strpos($fileName, ".jpeg") !== false){ 
				$fileExt = strtolower(substr($fileName,-5));
			}else{
				$fileExt = strtolower(substr($fileName,-4));
			}


			$saveName = $prefix.trim(time()).rand(100,999).$fileExt;
			
			$uploadFile = $EV_UPLOAD_DIR."${sub_dir}/".$saveName;


		//	echo $saveName;
		//	exit;

			
			//확장자 체크
			if( strpos($EV_UPLOAD_EXT_ALL.",", str_replace(".", "", $fileExt.","))===false ){
				alertBack("업로드 할 수 없는 파일입니다.");
			}



			
			if (!move_uploaded_file($fileTemp, $uploadFile)) {

				//print "파일 업로드 공격의 가능성이 있습니다! 디버깅 정보입니다:\n";
				//print_r($_FILES);
				//pageExit();

			}
		}
		
		return array(
			"saveName"=>$saveName
			,"fileSize"=>$fileSize
			,"fileExt"=>$fileExt
			,"fileName"=>$fileName
			,"formName"=>$form_name
			,"subDir"=>$sub_dir
			,"fullPath"=>$EV_UPLOAD_PATH.$sub_dir."/".$saveName
			,"fullDir"=>$EV_UPLOAD_DIR.$sub_dir."/".$saveName
		);
	}

	function deleteBoardFile($bdl_seq){
		if( strlen(trim($bdl_seq))!=0 ){
			$list = get_rows("
				select *
				from tb_board_file 
				where bdl_seq='$bdl_seq'
			");

			if( is_array($list) ){
				for($i=0;$i<count($list);$i++){
					$info = $list[$i];
					
					if( strlen(trim($info[bdf_snm]))!=0 and strlen(trim($info[bdf_dir]))!=0 ){
						deleteOldFile($info[bdf_snm], $info[bdf_dir]);
					}
				}
			}
		}
	}

	function insertBoardFile($bdl_seq, $file_info){
		if( strlen(trim($bdl_seq))!=0 and is_array($file_info) and strlen(trim($file_info[saveName]))!=0 ){
			
			//기존파일 삭제
			$info = get_row("
				select bdf_snm, bdf_dir
				from tb_board_file 
				where bdl_seq='$bdl_seq'
				and bdf_kind='".$file_info["formName"]."'
			");
			if( strlen(trim($info[bdf_snm]))!=0 and strlen(trim($info[bdf_dir]))!=0 ){
				deleteOldFile($info[bdf_snm], $info[bdf_dir]);
			}
			
			execute_query("
				delete from tb_board_file 
				where bdl_seq='$bdl_seq'
				and bdf_kind='".$file_info["formName"]."'
			");

			//파일정보 입력
			execute_query("
				insert into tb_board_file(
				     bdf_fnm
				    ,bdf_snm
				    ,bdl_seq
				    ,bdf_size
				    ,bdf_kind
				    ,bdf_ext
				    ,bdf_dir
				)values(
				     '" . $file_info["fileName"] . "'
				    ,'" . $file_info["saveName"] . "'
				    ,'" . $bdl_seq . "'
				    ,'" . $file_info["fileSize"] . "'
				    ,'" . $file_info["formName"] . "'
				    ,'" . $file_info["fileExt"] . "'
				    ,'" . $file_info["subDir"] . "'
				)
			");
		}
	}
	
	function deleteOldFile($old_filename,$sub_dir){
		global $EV_UPLOAD_DIR;

		$old_filename = ($old_filename==null ? "" : trim($old_filename));
		
		if( $old_filename!="" ){
			$old_filename2 = $EV_UPLOAD_DIR."${sub_dir}/".$old_filename;
			if( file_exists($old_filename2) ){
				unlink($old_filename2);
			}
		}
	}

	function clacPaging($pageno,$pagesize,$total_record,&$first,&$last,&$total_page_cnt){
		$pagesize = intval(nvl($pagesize,"0"));
		$pagesize = $pagesize==0 ? 10 : $pagesize;

		$first = $pagesize*($pageno-1);
		$last = $pagesize*$pageno;
		$IsNext = $total_record - $last;

		if($IsNext > 0) {
		} else {
			$last = $total_record;
		}

		$total_page_cnt = ceil($total_record/$pagesize);
	}


	function pageList($pageno,$pagesize,$sql_select,$sql_body,$sql_order){
		$pageno = intval($pageno)==0 ? 1 : $pageno;

		//total count
		$sql = "select count(*) {$sql_body} ";
		$total_record = get_int($sql);


		//calc paging
		clacPaging($pageno,$pagesize,$total_record,$first,$last,$total_page);

		//list data
		$sql = "
			{$sql_select}
			{$sql_body}
			{$sql_order}
			limit {$first},{$pagesize}
		";

		$list = get_rows($sql);
		$listCount = is_array($list) ? count($list) : 0;


		//return
		if($listCount > 0 && is_array($list)){
			$result['listCount']		= $listCount;
			$result['list']				= $list;
			$result['total_record']		= $total_record;
			$result['total_page']		= $total_page;
		}else{
			$result['listCount']		= 0;
			$result['list']				= null;
			$result['total_record']		= 0;
			$result['total_page']		= 0;
		}

		return $result;
	}



	
	//$pagecnt : 전체 항목개수
	//$page_size : 페이지수, 보통10개씩
	//$n_pageno : 현재 페이지 번호
	function paging_print($pagecnt,$page_size,$n_pageno){
		global $EV_PATH_IMG;

		if( $pagecnt > 0 ){
			$page_per_block = 10;

			$total_page = ceil($pagecnt/$page_size);


			//echo $pagecnt;

			$total_block = ceil($total_page/$page_per_block);
			$block = ceil($n_pageno/$page_per_block);
			$first_page = ($block-1) * $page_per_block;
			$last_page = $block*$page_per_block;

			if($block >= $total_block) {
				$last_page = $total_page;
			}

			echo "<table height=21 border=0 align=center cellpadding=0 cellspacing=0><tr>";

			//echo "<td><a href=javascript:go_page(1)><img src=/admin/img/bbs1_13.gif border=0 align=absmiddle /></a>&nbsp;</td>";

			if($block > 1) {
				$is_page = $first_page;
				echo "<td><a href='javascript:gopage($is_page);'><img src=/admin/img/bbs1_15.gif border=0 align=absmiddle /></a>&nbsp;</td>";
			}else{
				echo "<td><a href='#' onclick='return false;'><img src=/admin/img/bbs1_15.gif border=0 align=absmiddle /></a>&nbsp;</td>";
			}
			
			for($link_page=$first_page+1; $link_page<=$last_page; $link_page++) {
				if($link_page!=($first_page+1)){
					echo " ";
				}



				if($n_pageno == $link_page) {
					echo "<td align=center valign=middle bgcolor=#efefef><div style='padding:0 10px;cursor:pointer;border:1px solid #e3e3e3;' onclick='javascript:#'><b>${link_page}</b></div></td>";
				} else {
					echo "<td align=center valign=middle><div style='padding:0 10px;cursor:pointer;border:1px solid #e3e3e3;' onclick='javascript:gopage(${link_page});'><b>${link_page}</b></div></td>";
				}
			}
			


			if($block < $total_block) {
				$is_page = $last_page +1;
				echo "<td>&nbsp;<a href=javascript:gopage($is_page);><img src=/admin/img/bbs1_16.gif border=0 align=absmiddle /></a></td>";
			}else{
				echo "<td>&nbsp;<a href='#' onclick='return false;'><img src=/admin/img/bbs1_16.gif border=0 align=absmiddle /></a></li>";
			}

			//echo " <td>&nbsp;<a href='javascript:gopage($total_page);'><img src=/admin/img/bbs1_14.gif border=0 align=absmiddle /></a></td>";

			echo "</tr></table>";


		} else {
			echo "
			<table height=21 border=0 align=center cellpadding=0 cellspacing=0><tr>
			<td><a href='#' onclick='return false;'><img src=/admin/img/bbs1_15.gif border=0 align=absmiddle /></a>&nbsp;</td>  
			<td align=center valign=middle><div style='padding:0 10px;cursor:pointer;border:1px solid #e3e3e3;' onclick='javascript:#'><b>1</b></div></td>
			<td>&nbsp;<a href='#' onclick='return false;'><img src=/admin/img/bbs1_16.gif border=0 align=absmiddle /></a></td> 
			</tr></table>
			";

		}
	}

	/**
	 * $val 이 널값이면, $rep 값을 반환("" 도 널값으로 처리)
	 * @param string $val
	 * @param string $rep
	 * @return string
	 */
	function nvl($val,$rep=""){
		if( is_null($val) or trim($val)=="" ){
			return $rep;
		}else{
			return $val;
		}
	}

	/**
	 * $val 이 널값이면, $rep 값을 반환(null값만 체크, "" 는 널이 아님)
	 * @param string $val
	 * @param string $rep
	 * @return string
	 */
	function nvl2($val,$rep=""){
		if( is_null($val) ){
			return $rep;
		}else{
			return $val;
		}
	}

	/**
	 * post 파라미터를 체크해서, 빈값이면 이전페이지로 돌아감
	 * chkParamPost(array("key1","key2"));
	 */
	function chkParamPost($arr){
		if(is_array($arr)){
			foreach($arr as $key){
				$val = getPost($key);
				if($val==null || trim($val)==""){
					alertBack("정보가 부족합니다.");
				}
			}
		}
	}


	function hpOptions(){
		echo "
		<option value='010' >010</option>
		<option value='011' >011</option>
		<option value='016' >016</option>
		<option value='017' >017</option>
		<option value='018' >018</option>
		<option value='019' >019</option>
		";
	}

	function cutStr($str, $len, $suffix=".."){
		global $EV_SITE_CHARSET;
		return mb_strimwidth($str, 0, $len, $suffix, $EV_SITE_CHARSET);
	}

	
	/**
	 * fnDecode(array(판별변수, 판별상수1, 판별값1, 판별상수2, 판별값2, 기본값))
	 * echo fnDecode(array(v_val, "Y", "예", "아니오"));
	 * echo fnDecode(array(v_val, "Y", "예", "N", "아니오", "아니오"));
	 * 오라클의 decode 함수를 php 버전으로 만들었습니다.
	 * 최소 배열인자의 개수는 4개 이상 이어야 합니다.
	 * 인자를 잘 못넘기면 빈값("")을 반환합니다.
	 */
	function fnDecode($p_ary){
		$result = "";
		$ary_len = 0;
		
		if(is_array($p_ary)){
			$ary_len = count($p_ary);
			
			//최소 인자배열의 항목수가 4개이상이어야 하고, 짝수이어야 함..
			if( $ary_len >= 4 && $ary_len % 2 == 0){
				$v1 = $p_ary[0];				//판별변수
				$v2 = $p_ary[$ary_len-1];		//기본값
				
				$result = $v2;
				
				for($i = 1; $i<=($ary_len-2); $i+=2){
					if($p_ary[$i] == $v1){
						$result = $p_ary[$i+1];
						break;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * [파라미터]
	 * @param string $vname : 변수명(콤마구분)
	 * @param string $optPageExit : true(페이지 실행중단), false(페이지 계속실행, 디폴트)
	 */
	function debug($vname,$optPageExit=false){
		$list = explode(",", $vname);
		foreach($list as $val){
			$val = trim($val);
			global $$val;
			echo "<br />\n{$val} : ".$$val;
		}
		if($optPageExit) pageExit();
	}

	/**
	 * 전화번호같이 구분자로 구분되어있는 문자열 분리하여 특정 인덱스값 문자열 반환하기
	 * "010-1111-2222" ==> "010" || "1111" || "2222"
	 * $idx : 0부터 시작
	 * $div : 구분자
	 */
	function parseStr($str, $idx, $div="-"){
		if( is_null($str) ) $str = "";
		$arr = explode($div, $str);
		return $arr[$idx];
	}
	
	/**
	 * 생년월일을 받아서 나이를 계산해서 반환
	 * $birth : "1999-01-01" or "19990101"
	 * 잘못된 생년월일이 넘어오면 0을 반환
	 */
	function birth2age($birth){
		$birth = trim(!is_null($birth) ? $birth : "");
		$birth = str_replace(" ", "", $birth);
		$birth = str_replace("\t", "", $birth);
		$birth = str_replace("-", "", $birth);
		$birth = str_replace(".", "", $birth);
		
		if($birth=="") return 0;
		if(strlen($birth)<8) return 0;
		
		//현재시간 파싱
		$this_time_ary = getdate();
		
		$this_year = intval($this_time_ary[year]);
		$this_month = intval($this_time_ary[month]);
		$this_day = intval($this_time_ary[mday]);
		
		//생년월일 파싱
		$birth_year = intval(substr($birth, 0, 4));
		$birth_month = intval(substr($birth, 4, 2));
		$birth_day = intval(substr($birth, 6, 2));
		
		//나이반환
		return $this_year - $birth_year + 1;
	}
	
	/**
	 * 특정 문자열을 구분자로 잘라서 배열을 만들고, 
	 * 해당 배열에서 특정 항목들만을 조합한 문자열을 반환
	 * 
	 * $str					: 대상문자열
	 * $parse_delimiter		: 문자열을 자를때 구분 문자열
	 * $join_delimiter		: 문자열을 조합할때 구분 문자열
	 * $target_keys			: 배열에서 조합할 인덱스(array(1,3,"kk", ...))
	 */
	function fnArySomeItemsJoin($str,$parse_delimiter,$join_delimiter,$target_keys) {
		if(trim(nvl($str,""))=="") return "";
		if(nvl($parse_delimiter,"")=="") return $str;
		
		if(!is_array($target_keys)){
			if(trim(nvl($target_keys,""))=="") return $str;
			
			$arr = explode($parse_delimiter,$str);
			
			return $arr[$target_keys];
		}else{
			$arr = explode($parse_delimiter,$str);
			
			$cnt = 0;
			$result = "";
			for($i=0;$i<count($target_keys);$i++){
				$key = trim(nvl($target_keys[$i],""));
				
				if($key!=""){
					if($cnt>0) $result .= $join_delimiter;
					$result .= $arr[$key];
					$cnt++;
				}
			}
			return $result;
		}
	}
	
	/**
	 * 값이 널이거나, "" 빈값이면 - true, 임의값이 있으면 - false
	 * @param unknown_type $val
	 * @return boolean
	 */
	function fnIsNull($val) {
		if( trim(nvl($val,""))=="" ){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * <b>널체크해서, trim 적용함수</b>
	 * @param string $val
	 * @param string $rep
	 */
	function ntrim($val,$rep=""){
		return trim(nvl($val,$rep));
	}
	
	/**
	 * json 한글처리를 위해서, 배열값을 인코딩
	 */
	function arrayUrlEncode($arr){
		if(is_array($arr)){
			foreach($arr as $key=>$val){
				if(is_array($val)){
					$arr[$key] = arrayUrlEncode($val);
				}else{
					$arr[$key] = urlencode($val);
				}
			}
		}
		return $arr;
	}
	
	/**
	 * json 한글처리를 위해서, 배열값을 디코딩
	 */
	function arrayUrlDecode($arr){
		if(is_array($arr) or is_object($arr)){
			foreach($arr as $key=>$val){
				if(is_array($val) or is_object($val)){
					if(is_array($arr)){
						$arr[$key] = arrayUrlDecode($val);
					}else if(is_object($arr)){
						$arr->$key = arrayUrlDecode($val);
					}
				}else{
					if(is_array($arr)){
						$arr[$key] = urldecode($val);
					}else if(is_object($arr)){
						$arr->$key = urldecode($val);
					}
				}
			}
		}
		return $arr;
	}
	
	/**
	 * php object 를 배열로 변환
	 */
	function object2array($obj){
		if(!$obj) return array();
		
		$arr = array();
		
		if(is_array($obj) or is_object($obj)){
			foreach($obj as $key=>$val){
				if(is_array($val) or is_object($val)){
					$arr[$key] = object2array($val);
				}else{
					$arr[$key] = $val;
				}
			}
		}
		
		return $arr;
	}
	
	/**
	 * $fileMap 타입을, $fileInfo 타입으로 키값을 맞춤
	 * 
	 * - $fileMap 타입
	 * [fi_seq] => 137
     * [fi_fnm] => 겨울.jpg
     * [fi_snm] => fileDeli_1365585442405.jpg
     * [seq] => 20130410181657952270_auc_moveworth_rep_18
     * [fi_size] => 105542
     * [fi_kind] => fileDeli
     * [fi_ext] => .jpg
     * [fi_dir] => auction
     * 
     * - $fileInfo 타입
	 * [saveName] => ""
	 * [fileSize] => ""
	 * [fileExt] => ""
	 * [fileName] => ""
	 * [formName] => ""
	 * [subDir] => ""
	 * [fullPath] => ""
	 * [fullDir] => ""
	 *
	 * @param fileMapType $fileMap
	 */
	function fileMap2Info(&$fileMap,$exceptKeys="fullDir"){
		global $EV_UPLOAD_PATH, $EV_UPLOAD_DIR;
		
		$fileMap[saveName] = $fileMap[fi_snm];
		$fileMap[fileSize] = $fileMap[fi_size];
		$fileMap[fileExt] = $fileMap[fi_ext];
		$fileMap[fileName] = $fileMap[fi_fnm];
		$fileMap[formName] = $fileMap[fi_kind];
		$fileMap[subDir] = $fileMap[fi_dir];
		
		if(fnIsNull($fileMap[fullPath])){
			$fileMap[fullPath] = $EV_UPLOAD_PATH.$fileMap[subDir]."/".$fileMap[saveName];
		}
		if(fnIsNull($fileMap[fullDir])){
			$fileMap[fullDir] = $EV_UPLOAD_DIR.$fileMap[subDir]."/".$fileMap[saveName];
		}
		
		//배제항목 처리
		$exceptKeys = ntrim($exceptKeys);
		if($exceptKeys!=""){
			$exceptKeysArr = explode(",",$exceptKeys);
			if(is_array($exceptKeysArr)){
				foreach ($exceptKeysArr as $key=>$val){
					$fileMap[$val] = "";
				}
			}
		}
		
		return $fileMap;
	}
	
	/**
	 * 콤마등을 제거하고, 정수형태로 변환하여 반환
	 * @param string $num
	 * @return number
	 */
	function fnCInt($num){
		if(is_null($num)){
			return 0;
		}
		$num = str_replace(",", "", $num);
		//return intval($num);
		return floor($num);
	}
	
	/**
	 * $val 가 $min보다 작으면 $min,
	 * $val 가 $max보다 크면 $max,
	 * 범위안에 포함되어있으면, 원래 값 반환
	 * @param number $min : 범위의 최소값
	 * @param number $max : 범위의 최대값
	 * @param number $val : 범위체크할 값
	 */
	function fnRange($min, $max, $val){
		$min = intval($min);
		$max = intval($max);
		$val = intval($val);
		
		if($min > $max){
			fnSwap($min,$max);
		}
		
		if($val<$min)return $min;
		if($val>$max)return $max;
		
		return $val;
	}
	
	/**
	 * $val1 변수와 $val2 두 변수의 값을 바꿉니다.
	 * @param unknown_type $val1
	 * @param unknown_type $val2
	 */
	function fnSwap(&$val1,&$val2){
		$tmp = $val1;
		$val1 = $val2;
		$val2 = $tmp;
	}
	
	/**
	 * 구분자로 배열로 만들어서, 특정 인덱스 항목으로 replace 한 문자열을 반환
	 * ex) "1_2_3" => "1_대치문자열_3"
	 * @param string $index : 몇번째 문자열을 바꿀지 인덱스 숫자 혹은 지정문자열(0~N, last, last-1, last-N, ...)
	 * @param string $replace : 바꿀문자열
	 * @param string $subject : 원본문자열
	 * @param string $div : 구분문자열
	 */
	function div_replace($index, $replace, $subject, $div=","){
		$div = is_null($div) ? "" : $div;
		$replace = is_null($replace) ? "" : $replace;
		
		if($div=="") return "";
		
		$arr = explode($div, $subject);
		$lastIndex = count($arr)-1;
		
		if($lastIndex<0)return "";
		
		### 인덱스가 숫자이면
		if(is_numeric($index)){
			$index = fnRange(0, $lastIndex, $index);
			$arr[$index] = $replace;
		}else{
			$index = str_replace("last", $lastIndex, $index);
			
			eval("\$index = ".$index.";");
			
			$index = fnRange(0, $lastIndex, $index);
			$arr[$index] = $replace;
		}
		
		return implode($div, $arr);
	}
	
	/**
	 * 구분자로 배열로 만들어서, 특정 인덱스 항목의 문자열을 반환
	 * ex) "1_2_3" => 2번째 문자열 2(인덱스 1을넘김)
	 * @param string $index : 몇번째 문자열을 반환할지 인덱스 숫자 혹은 지정문자열(0~N, last, last-1, last-N, ...)
	 * @param string $subject : 원본문자열
	 * @param string $div : 구분문자열
	 */
	function div_getstr($index, $subject, $div=","){
		$div = is_null($div) ? "" : $div;
		
		if($div=="") return "";
		
		$arr = explode($div, $subject);
		$lastIndex = count($arr)-1;
		
		if($lastIndex<0)return "";
		
		### 인덱스가 숫자이면
		if(is_numeric($index)){
			$index = fnRange(0, $lastIndex, $index);
			return $arr[$index];
		}else{
			$index = str_replace("last", $lastIndex, $index);
			
			eval("\$index = ".$index.";");
			
			$index = fnRange(0, $lastIndex, $index);
			return $arr[$index];
		}
		
		return "";
	}
	
	/**
	 * mysql ifnull 함수 구현, '0000-00-00 00:00:00'도 널로 인식
	 * echo fnMysqlIfnull("regdate", "now()");
	 * @param string $dateColName
	 * @param string $replaceValue
	 * @return string
	 */
	function fnMysqlIfnull($dateColName,$replaceValue){
		return " case when isnull({$dateColName}) or {$dateColName}='0000-00-00 00:00:00' then {$replaceValue} else {$dateColName} end ";
	}
	
	/**
	 * 숫자에 콤마출력
	 * @param string $num
	 * @return string
	 */
	function fnNumberFormat($num){
		return number_format(fnCInt($num));
	}
	
	/**
	 * 파일 확장자 구하기
	 */
	function getFileExt($fpath){
		$fpath = ntrim($fpath);
		if($fpath=="")return;
		return substr(strrchr($fpath,"."),1);
	}
	
	/**
	 * 파일경로 보안처리
	 */
	function fileSecurity($fpath){
		$fpath = ntrim($fpath);
		$fpath = str_replace("..", "", $fpath);
		$fpath = str_replace("/", "", $fpath);
		$fpath = str_replace("\\", "", $fpath);
		return $fpath;
	}

	function zf($num){
		$num = trim($num);
		if(strlen($num)<2){
			return "0".$num;
		}else{
			return $num;
		}
	}

	
	function text_cut($val, $gubun){
		
		$text_cut_result = iconv_substr($val, 0, $gubun, "utf-8");
		
		return $text_cut_result;
	}




	function get_member_id($idx){

		return $member_idx = get_val(" select user_id from tb_member where idx = '$idx' and use_yn='Y' ");

	}


	function get_member_name_id($user_name){

		return $member_name_id = get_val(" select user_id from tb_member where user_name = '$user_name' and use_yn='Y' ");

	}


	function get_member_name($in_mem_idx){
		
		return $in_mem_name = get_val("select user_name from tb_member where idx='$in_mem_idx' and use_yn='Y'  ");

	}

	function get_member_name1($user_id){
		
		return $user_name = get_val("select user_name from tb_member where user_id = '$user_id' and use_yn='Y'  ");

	}


	function get_member_ban_name($idx){
		
		return $ban_name = get_val("select ban_name from tb_class where idx = '$idx' and use_yn='Y'  ");

	}

	function get_class_user_damim_name($idx){
		
		$user_damim = get_val("select user_damim from tb_class where idx = '$idx' and use_yn='Y'  ");

		return $user_damim_name = get_val("select user_name from tb_member where user_id = '$user_damim' and use_yn='Y'  ");

	}


	function tb_class_ca_2($idx){
		
		$ca_2 = get_val("select ca_2 from tb_class where idx = '$idx' and use_yn='Y'  ");

		return $ca_2_val = getCode_one($ca_2);

	}



	function get_book_name($idx){
		
		return $book_name = get_val("select book_name from tb_book where idx = '$idx'  ");

	}


	function get_book_sel(){
		

		$listCode = get_rows(" select * from tb_book where use_yn='y' ORDER BY book_name ASC ");

		foreach($listCode as $i=>$row){
			echo "<option value='$row[idx]' >$row[book_name]</option>";
		}

	}


	function get_tb_schedule_book1_sel($class_idx){


		$listCode = get_rows(" select * from tb_schedule where class_idx='$class_idx' and book1 <> '0'  ORDER BY regdate desc ");

		foreach($listCode as $i=>$row){


			//echo $row[book1]."<br>";

			$sqlSearch .= "  WHEN idx = '$row[book1]' THEN $i ";


		}

		echo $sqlSearch;


		$listCode1 = get_rows(" select * from tb_book where use_yn='y' ORDER BY CASE $sqlSearch END desc ");

		//echo " select * from tb_book where use_yn='y' ORDER BY CASE $sqlSearch END ";

		foreach($listCode1 as $x=>$row1){
			echo "<option value='$row1[idx]' >$row1[book_name]</option>";
		}


	}



	function get_tb_schedule_book2_sel($class_idx){


		$listCode = get_rows(" select * from tb_schedule where class_idx='$class_idx' and book2 <> '0'  ORDER BY regdate desc ");

		foreach($listCode as $i=>$row){


			//echo $row[book2]."<br>";

			$sqlSearch .= "  WHEN idx = '$row[book2]' THEN $i ";


		}

		echo $sqlSearch;


		$listCode1 = get_rows(" select * from tb_book where use_yn='y' ORDER BY CASE $sqlSearch END desc ");

		//echo " select * from tb_book where use_yn='y' ORDER BY CASE $sqlSearch END ";

		foreach($listCode1 as $x=>$row1){
			echo "<option value='$row1[idx]' >$row1[book_name]</option>";
		}


	}


	




	function get_level_avg($idx, $test_val, $member_s_idx, $jido_b_num, $grade_Sdate, $grade_Edate){




		$level_group_name = get_val("select level_group_name from tb_class where idx = '$idx' and use_yn='Y'  ");

		//echo $level_group_name;

		$listCode = get_rows(" select * from tb_class where level_group_name='$level_group_name' and use_yn='y' ORDER BY idx desc ");

		//echo " select * from tb_class where level_group_name='$level_group_name' and use_yn='y' ORDER BY idx desc "."<br>";

		foreach($listCode as $i=>$row){

			//echo $row[idx]."<br>";
			//exit;


			$class_idx = $row[idx];


			$listCode1 = get_rows(" SELECT * FROM tb_grade WHERE class_idx='$class_idx' and grade_Sdate = '$grade_Sdate' and grade_Edate = '$grade_Edate' AND test_name_list <>'' AND test2 <> '' GROUP BY member_s_idx ");


			//echo " SELECT * FROM tb_grade WHERE class_idx='$class_idx' and grade_Sdate = '$grade_Sdate' and grade_Edate = '$grade_Edate' AND test_name_list <>'' AND test2 <> '' "."<br>";

			
			//echo count($listCode1)."<br>";

			foreach($listCode1 as $k=>$info){
			


				$test_name_list = preg_replace("/\s+/","",$info[test_name_list]);
				$test2_list = preg_replace("/\s+/","",$info[test2]);


				//echo $test_name_list."<br>";


				$test_name_list_arr = explode(",", $test_name_list);

				$test2_list_arr = explode(",", $test2_list);



				//echo $test_name_list."<br>";
				
				//echo $test2_list."<br>";


				//echo count($test_name_list_arr)."<br>";


				//$level_avg_val_total_count=1;

				for ( $a=0; $a < count($test_name_list_arr) ; $a++) {

					//echo $test2_list_arr[$a]."<br>";

					if($test_name_list_arr[$a]==$test_val){

						//echo $test_name_list_arr[$a]."<br>";

						
						//echo $test2_list_arr[$a]."<br>";
						
						
						$level_avg_val_total += $test2_list_arr[$a];



						if($test2_list_arr[$a]){
							$level_avg_val_total_count += 1;
						}

						

	

					}


				}



			}




		}


		

		

		//echo $level_avg_val_total."<br>";

		$level_avg_val_result = $level_avg_val_total / $level_avg_val_total_count;


		//echo $level_avg_val_total." / ".$level_avg_val_total_count."<br>";

		
		//echo $test_val."=".round($level_avg_val_result,2)."<br>";




		return round($level_avg_val_result,2);


	}







	function get_tb_time($idx,$gubun){


		if($gubun=="1"){

			$week = get_val("select week from tb_time where idx = '$idx'  ");

			return $week_txt = get_week_txt($week);

		}elseif($gubun=="2"){

			return $s_time_1 = get_val("select s_time_1 from tb_time where idx = '$idx'  ");

		}elseif($gubun=="3"){

			return $s_time_2 = get_val("select s_time_2 from tb_time where idx = '$idx'  ");

		}elseif($gubun=="4"){

			return $e_time_1 = get_val("select e_time_1 from tb_time where idx = '$idx'  ");

		}elseif($gubun=="5"){

			return $e_time_2 = get_val("select e_time_2 from tb_time where idx = '$idx'  ");

		}else{

			$week = get_val("select week from tb_time where idx = '$idx'  ");

			return $week_txt = get_week_txt($week);
		}

		
		

	}


	function get_tb_time_sel($week_num){

		//$listCode = get_rows(" select * from tb_time where week = '".$week_num."' AND use_yn='y' ORDER BY week ASC ");
		$listCode = get_rows(" select * from tb_time where use_yn='y' ORDER BY week ASC ");


		foreach($listCode as $i=>$row){

			$time_list =  $row[s_time_1]."시".$row[s_time_2]."분 ~ ".$row[e_time_1]."시".$row[e_time_2]."분";

			echo "<option value='$row[idx]' >$time_list</option>";
		}

	}


	function get_tb_time_sel_2($ca_1, $ca_2, $in_date){


		if($in_date=="0"){
			$in_date = "7";
		}

		$listCode = get_rows(" select * from tb_time where ca_1='$ca_1' and ca_2 ='$ca_2' and week ='$in_date' and use_yn='y' ORDER BY week ASC ");


		foreach($listCode as $i=>$row){

			$time_list =  $row[s_time_1]."시".$row[s_time_2]."분 ~ ".$row[e_time_1]."시".$row[e_time_2]."분";

			echo "<option value='$row[idx]' >$time_list</option>";
		}

	}



	function get_jindo_ca_3_sel($ca_1, $ca_2){


		$listCode = get_rows(" select * from tb_code where pcode='$ca_2' and cd_open_yn='Y' ORDER BY cd_ord ASC ");

		//echo " select * from tb_code where pcode='$ca_2' and cd_open_yn='Y' ORDER BY cd_ord ASC ";


		foreach($listCode as $i=>$row){


			echo "<option value='$row[code]' >$row[cd_val]</option>";
		}

	}






	function get_s_sel($idx,$grade_Sdate,$grade_Edate){

		$listCode = get_rows(" select * from tb_sookang where class_idx='$idx' and use_yn='y' ORDER BY idx desc ");


		foreach($listCode as $i=>$row){

			//echo $row[member_s_idx]."<br>";
			//exit;

			$stu_name =  get_member_name($row[member_s_idx]);

			//echo $stu_name."<br>";


			//tb_grade에 성적입력이 있다면 [입력완료] 로 표시해줌
			//$tb_grade_count = get_int("select count(idx) from tb_grade where class_idx='$idx' and member_s_idx='$row[member_s_idx]' ");


			//$info = get_row(" SELECT COUNT(a.idx) as tb_grade_count ,b.in_date FROM tb_grade AS a JOIN tb_jindo AS b ON a.class_idx = b.class_idx WHERE a.jido_b_num = b.b_num AND a.class_idx='$idx' AND a.member_s_idx='$row[member_s_idx]' ");

			$info = get_row(" SELECT COUNT(idx) as tb_grade_count , grade_Sdate, grade_Edate FROM tb_grade WHERE class_idx='$idx' AND member_s_idx='$row[member_s_idx]' and grade_Sdate='$grade_Sdate' and grade_Edate='$grade_Edate'  ");


			$tb_grade_count = $info[tb_grade_count];


			if($tb_grade_count > 0){
				$tb_grade_count_txt ="[입력완료]";
				$grade_SEdate_list = $info[grade_Sdate]."~".$info[grade_Edate];
			}else{
				$tb_grade_count_txt ="";
				$grade_SEdate_list = "";
			}


			echo "<option value='$row[member_s_idx]' >$stu_name $tb_grade_count_txt $grade_SEdate_list</option>";
		}

	}




	function add_hyphen($tel)
	{
		$tel = preg_replace("/[^0-9]/", "", $tel);    // 숫자 이외 제거
		if (substr($tel,0,2)=='02')
			return preg_replace("/([0-9]{2})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
		else if (strlen($tel)=='8' && (substr($tel,0,2)=='15' || substr($tel,0,2)=='16' || substr($tel,0,2)=='18'))
			// 지능망 번호이면
			return preg_replace("/([0-9]{4})([0-9]{4})$/", "\\1-\\2", $tel);
		else
			return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
	}






	function get_project_txt($gubun){

		if($gubun == "1") $project_txt = "완료";
		if($gubun == "2") $project_txt = "미완료";
		if($gubun == "3") $project_txt = "미확인";
	
		return $project_txt;

	}





	function get_member_mb_money($idx){
		
		return $member_mb_money = get_val("select mb_money from tb_member where idx='$idx'  ");
	}



	function get_order_set(){

		$listCode = get_rows(" select * from order_set order by idx asc ");

		foreach($listCode as $i=>$row){

			echo "<option value='$row[idx]'>$row[name]</option>";
		}

	}

	


	
	function get_payment_name($od_payment){

		if($od_payment=="cs"){
			echo "현금";
		}elseif($od_payment=="on"){
			echo "온라인";
		}else{
			echo "카드";
		}

	}



	function get_acc5_set_count1111($wr_ori_order_num){

		
		return $acc5_set_count = get_int(" SELECT count(seq) FROM g5_sales3_list WHERE wr_ori_order_num='$wr_ori_order_num' ");

	}




	function get_ceo_name(){
		
		return $ceo_idx = get_val("select user_name from tb_member where user_level='50' ");
	}


	function get_payment_gubun($gubun){

		$payment = "외상"; // 기본은 외상
		if($gubun == "cd") $payment = "카드";
		if($gubun == "tc") $payment = "현금영수증";
		if($gubun == "cs") $payment = "현금";
		if($gubun == "on") $payment = "온라인";

		return $payment;

	}




	//오늘날짜 기존으로 이번주 시작일(월)~종료일(일) 날짜구하기
	function wz_get_addday($day, $add) {

		$day    = preg_replace('/[^0-9]/', '', $day);

		$y      = substr( $day, 0, 4 );

		$m      = substr( $day, 4, 2 );

		$d      = (int)substr( $day, 6, 2 );

		if ($add >= 0) { 
			return date("Y-m-d", mktime(0,0,0, $m, ($d+$add), $y));    
		}

		else {

			if ($d > $add) { 
				return date("Y-m-d", mktime(0,0,0, $m, ($d+$add), $y));
			} 

			else {
				return date("Y-m-d", mktime(0,0,0, $m, ($d-$add), $y));
			}

		}  

	}



	//include $EV_LIB_PATH."paging.php";
	//include $EV_LIB_PATH."code.php";
?>

