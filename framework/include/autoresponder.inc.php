<?

class Inc_Autoresponder{
			 
	
	
	//
	//
	//
	//
	//
	// DO NOT EDIT THIS FILE WHILE REWRITE DEVELOPMENT PLEASE EDIT IN /tmp/include_src 
	//
	//
	//
	//
	//
	public static function sendCallerSendMessage($optinObj,$email,$subject,$content){
		if($optinObj->fhq_parent_id>0)return false;
	
		$mail = new PHPMailer(true);
		try {
	
			$body = $content;
			$body             = preg_replace('/\\\\/','', $body); //Strip backslashes
		
			$mail->IsSMTP();                           // tell the class to use SMTP
			$mail->SMTPAuth   = TRUE;                 // enable SMTP authenticationfhq_smtp_isauthen
			$mail->Port       = $optinObj->fhq_smtp_port;                    // set the SMTP server port
			$mail->Host       = $optinObj->fhq_smtp_server;		// SMTP server
			$mail->Username   = $optinObj->fhq_smtp_username;     // SMTP server username
			$mail->Password   = $optinObj->fhq_smtp_password;            // SMTP server password
	
		
			$mail->AddReplyTo($optinObj->fhq_send_replyemail);
		
			$mail->From       = $optinObj->fhq_send_fromemail;
			$mail->FromName   =  $optinObj->fhq_send_fromname;
			
			$mail->AddAddress($email);
		
			$mail->Subject  = $subject;
		
			$bodybr = $body;
			if(strpos($body,"<br/>")===false){
				$bodybr = Inc_Htmlutil::clickable($body);
			}
			$mail->MsgHTML($bodybr);
	
			$mail->IsHTML(true); // send as HTML
			$mail->AltBody    = $body; 
	
			$mail->Send();
			
			$mail->SmtpClose();
			
			return true;
		} catch (phpmailerException $e) {
			$logdir = prepath().'../log';
			$logfile = $logdir.'/'.'error_mail_'.date('Y-m-d').'.log';
			error_log("\n ".'['.date('Y-m-d H:i:s').']'."  $email $subject optin[ {$optinObj->id}] ".$e->errorMessage()."\n", 3, $logfile);
			$mail->SmtpClose();
	
			return false;		
		}
		
	
	}
	
	//
	//
	//
	//
	//
	// DO NOT EDIT THIS FILE WHILE REWRITE DEVELOPMENT PLEASE EDIT IN /tmp/include_src 
	//
	//
	//
	//
	//
	public static function sendDoubleOptin($optinObj,$optlistObj,$memberid,$name,$email,$custom=NULL){
		//=============== FOR UNIT TEST ======================//
		if(Conf()->UNITTEST){
			switch(Conf()->UNITCASE_sendDoubleOptin){
				case 'True':
					Conf()->UNITCASE_sendDoubleOptinReturn = 'SUCCESS';
					return true;
				case 'False':
					Conf()->UNITCASE_sendDoubleOptinReturn = 'ERROR';
					return false;
				//default:
				//	return false;
			}
		}
		//======================= END ========================//
		//exit("XXXX");
		//echo "Inc_Autoresponder::sendDoubleOptin($optinObj->id,$optlistObj->id,$memberid,$name,$email,$custom)";
		$delivery = MD_ARDelivery::BuildDoubleOptinDelivery($optinObj->id,$memberid);
		if($delivery==NULL){
			$delivery = new MD_ARDelivery();
			
			if($optlistObj->isconfirm){
				return true;
			}else{
				
				switch($optlist->status){
				
					case 'MANUAL':
					case 'IMPORT':
					case 'NONE':
					default:
						if($optinObj->fhq2_sendfrom_type =='SLAVEMAILER' && $optinObj->fhq_smtp_isauthen){
							$delivery->cache_fhq_sendfrom_type = $optinObj->fhq2_sendfrom_type;
							break;
						}
						if($optinObj->fhq2_sendfrom_type =='SLAVESERVER' && $optinObj->fhq2_smtp_tested){
							$delivery->cache_fhq_sendfrom_type = $optinObj->fhq2_sendfrom_type;
							break;
						}					
				
					case 'COMFIRM':
					case 'CONFIRM':
					case 'PRODUCT':
					case 'CAMPAIGN':
					case 'UNCOMFIRM':
					case 'UNCONFIRM':
						$delivery->cache_fhq_sendfrom_type = $optinObj->fhq_sendfrom_type;
						//var_dump($optinObj);
						if($optinObj->fhq_sendfrom_type ==CONST_MAILSYSTEM && !$optinObj->fhq_smtp_isauthen){
							return false;
						}
						if($optinObj->fhq_sendfrom_type =='LOCALSERVER' && !$optinObj->fhq_smtp_tested){
							return false;
						}					
						break;
		
	
	
				}
				//echo "xxx";			
			}	
			
			//$delivery->cache_fhq_sendfrom_type = $optinObj->fhq_sendfrom_type;
			
			$delivery->cache_optin_id = $optinObj->id;
			$delivery->member_id = $optinObj->member_id;
		
			$delivery->sendind_type = 'CONFIRMATION'; 
			$delivery->receiver_id = $memberid;
		}
		if(is_array($custom)){
			foreach($custom as $k => $v){
				$delivery->SetCustom($k, $v);
			}
		}else{
			$delivery->custom = $custom;
		}
		$delivery->date_added = Inc_Var::DatePHPToMysql(time());
		$delivery->date_sent = Inc_Var::DatePHPToMysql(time());
		$delivery->cache_firstname = $name;
		$delivery->cache_email = $email;
		//SaveDS()->Put('xxx',var_export($delivery,true));
		$delivery->status = 'PENDING';
		
		$result = $delivery->Update();
		//Inc_Var::vardump($delivery);
		return (!$result->ifError);
	}	
	
	
		
}