<?
include_once("./_common.php");


for($i = 159088; $i< 1000000; $i++){
	$sql = "insert into g5_member(mb_id,mb_password,mb_type,mb_name,mb_nick,mb_nick_date,mb_email,mb_level,mb_adult,mb_datetime,mb_ip,mb_email_certify,mb_mailling,mb_sms,mb_open,mb_signature,up_datetime,mb_memo,mb_lost_certify,mb_profile)
values('devAdmin".$i."','sha256:12000:GEdmAAoMx485t83yVU0nb1k3oY6mrV1u:/U6kP4CbVVQADJRc9XLG8moWYSB+hC0+',1,'개발관리자','개발관리자',now(),'',10,0,now(),'172.30.1.35',now(),0,0,1,'',now(),'','','');";
sql_query($sql);
}
?>