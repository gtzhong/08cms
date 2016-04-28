<?
!defined('M_COM') && exit('No Permisson');
function sys_mail(&$to,&$subject,&$msg,&$from){
	global $mail_smtp,$mail_mode,$mail_port,$mail_auth,$mail_from,$mail_user,$mail_pwd,$mail_delimiter,$mail_silent,
	$adminemail,$mcharset,$cmsname;
	if($mail_silent) error_reporting(0);
	$delimiter = $mail_delimiter == 1 ? "\r\n" : ($mail_delimiter == 2 ? "\r" : "\n");
	$user = isset($mail_user) ? $mail_user : 1;
	$subject = '=?'.$mcharset.'?B?'.base64_encode(str_replace(array("\r","\n"),'','['.$cmsname.'] '.$subject)).'?=';
	$msg = chunk_split(base64_encode(str_replace(array("\n\r","\r\n","\r","\n","\r\n.",),array("\r","\n","\n","\r\n"," \r\n..",),$msg)));
	$from = $from == '' ? '=?'.$mcharset.'?B?'.base64_encode($cmsname)."?= <$adminemail>" : (preg_match('/^(.+?)\<(.+?)\>$/',$from, $v) ? '=?'.$mcharset.'?B?'.base64_encode($v[1])."?= <$v[2]>" : $from);
	$toarr = array();
	foreach(explode(',',$to) as $k) $toarr[] = preg_match('/^(.+?)\<(.+?)\>$/',$k,$v) ? ($user ? '=?'.$mcharset.'?B?'.base64_encode($v[1])."?= <$v[2]>" : $v[2]) : $k;
	$mail_mode != 2 && $to = implode(',',$toarr);
	$headers = "From: $from{$delimiter}X-Priority: 3{$delimiter}X-Mailer: 08CMS{$delimiter}MIME-Version: 1.0{$delimiter}Content-type: text/html; charset=$mcharset{$delimiter}Content-Transfer-Encoding: base64{$delimiter}";
	$mail_port = $mail_port ? $mail_port : 25;
	if($mail_mode == 1 && function_exists('mail')){
		@mail($to,$subject,$msg,$headers);
	}elseif($mail_mode == 2){
       _08_Loader::import(_08_OUTSIDE_PATH . 'PHPMailer-master::PHPMailerAutoload');
       $phpmailer = new PHPMailer;

       # $phpmailer->SMTPDebug = 3;                                #Enable verbose debug output
        
        $phpmailer->isSMTP();                                       #Set mailer to use SMTP
        $phpmailer->Host = $mail_smtp;  # Specify main and backup SMTP servers
        $phpmailer->SMTPAuth = true;                               # Enable SMTP authentication
        $phpmailer->Username = $mail_user;                  #SMTP username
        $phpmailer->Password = $mail_pwd;                        #    SMTP password
        $phpmailer->SMTPSecure = 'tls';                           #  Enable TLS encryption, `ssl` also accepted
        $phpmailer->Port = $mail_port;                              #       TCP port to connect to
        
        $phpmailer->From = $mail_user;
        $phpmailer->FromName = $mail_user;
        
        $toarr = (array) $toarr;

        foreach ($toarr as $to_mail)
        {
            if (false !== strpos($to_mail, '<'))
            {
                @list($name, $mail) = explode(' ', $to_mail);
                $mail = str_replace(array('<', '>'), '', $mail);
                $name = trim($name);
            }
            else
            {
            	$mail = $to_mail;
                $name = '';
            }
            
            $phpmailer->addAddress($mail, $name);    #  Add a recipient
        }
        
        $phpmailer->addReplyTo($mail_user, $subject);
        
        $phpmailer->WordWrap = 50;    
        $phpmailer->isHTML(true);                                #   Set email format to HTML
        
        $phpmailer->Subject = $subject;
        $phpmailer->XMailer = '08CMS (http://www.08cms.com/)';
        $phpmailer->Body    = base64_decode($msg);
        $phpmailer->CharSet = $mcharset;
        $phpmailer->AltBody = $phpmailer->Body;

        if ( !$phpmailer->send() )
        {   
            return 'Error: ' . $phpmailer->ErrorInfo;
        }
//		if(!$fp = @fsockopen($mail_smtp, $mail_port,$errno,$errstr,30)) return "($mail_smtp:$mail_port) CONNECT - Unable to connect to the SMTP server";
//		stream_set_blocking($fp,true);
//	
//		$nmsg = fgets($fp,512);
//		if(substr($nmsg,0,3) != '220') return "$mail_smtp:$mail_port CONNECT - $nmsg";
//	
//		fputs($fp, ($mail_auth ? 'EHLO' : 'HELO')." 08CMS\r\n");
//		$nmsg = fgets($fp,512);
//		if(substr($nmsg,0,3) != 220 && substr($nmsg,0,3) != 250) return "($mail_smtp:$mail_port) HELO/EHLO - $nmsg";
//	
//		while(1){
//			if(substr($nmsg,3,1) != '-' || empty($nmsg)) break;
//			$nmsg = fgets($fp,512);
//		}
//	
//		if($mail_auth) {
//			fputs($fp,"AUTH LOGIN\r\n");
//			$nmsg = fgets($fp,512);
//			if(substr($nmsg, 0, 3) != 334) return "($mail_smtp:$mail_port) AUTH LOGIN - $nmsg";
//	
//			fputs($fp,base64_encode($mail_user)."\r\n");
//			$nmsg = fgets($fp,512);
//			if(substr($nmsg,0,3) != 334) return "($mail_smtp:$mail_port) USERNAME - $nmsg";
//	
//			fputs($fp,base64_encode($mail_pwd)."\r\n");
//			$nmsg = fgets($fp,512);
//			if(substr($nmsg,0,3) != 235) return "($mail_smtp:$mail_port) PASSWORD - $nmsg";
//			$from = $mail_from;
//		}
//	
//		fputs($fp,"MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/","\\1",$from).">\r\n");
//		$nmsg = fgets($fp,512);
//		if(substr($nmsg,0,3) != 250) {
//			fputs($fp,"MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/","\\1",$from).">\r\n");
//			$nmsg = fgets($fp,512);
//			if(substr($nmsg,0,3) != 250) return "($mail_smtp:$mail_port) MAIL FROM - $nmsg";
//		}
//	
//		foreach(explode(',',$to) as $v){
//			$v = trim($v);
//			if($v){
//				fputs($fp,"RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $v).">\r\n");
//				$nmsg = fgets($fp,512);
//				if(substr($nmsg,0,3) != 250){
//					fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/","\\1",$v).">\r\n");
//					$nmsg = fgets($fp,512);
//					return "($mail_smtp:$mail_port) RCPT TO - $nmsg";
//				}
//			}
//		}
//	
//		fputs($fp,"DATA\r\n");
//		$nmsg = fgets($fp,512);
//		if(substr($nmsg,0,3) != 354) return "($mail_smtp:$mail_port) DATA - $nmsg";
//		$headers .= 'Message-ID: <'.date('YmdHs').'.'.substr(md5($msg.microtime()),0,6).rand(100000, 999999).'@'.$_SERVER['HTTP_HOST'].">{$delimiter}";
//		fputs($fp,"Date: ".date('r')."\r\n");
//		fputs($fp,"To: ".$to."\r\n");
//		fputs($fp,"Subject: ".$subject."\r\n");
//		fputs($fp,$headers."\r\n");
//		fputs($fp,"\r\n\r\n");
//		fputs($fp,"$msg\r\n.\r\n");
//		$nmsg = fgets($fp, 512);
//        exit("Date: ".date('r')."\r\nTo: ".$to."\r\nSubject: ".$subject."\r\n{$headers}\r\n\r\n\r\n$msg\r\n.\r\n");
//		if(substr($nmsg, 0, 3) != 250) return "($mail_smtp:$mail_port) END - $nmsg";
//		fputs($fp, "QUIT\r\n");
	}elseif($mail_mode == 3){
		ini_set('SMTP',$mail_smtp);
		ini_set('smtp_port',$mail_port);
		ini_set('sendmail_from',$from);
		@mail($to,$subject,$msg,$headers);
	}
	return '';

}
?>