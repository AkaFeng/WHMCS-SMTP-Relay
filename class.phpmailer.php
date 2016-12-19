<?php

class PHPMailer {
	
	public $Subject = '';
	public $Body = '';
	private $sender = '';
	private $recipient = '';
	
    public function __construct($exceptions = false){}
	public function IsSMTP() {}
	public function clearReplyTos() {}
	protected function serverHostname() { return "127.0.0.1";}
    public function clearAllRecipients() {}
    public function addReplyTo($address, $name = '')
    {
		$this->sender = $address;
        return $address;
    }
	public function addAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment'){ return true; }
	
    function curl_get_contents($url,$timeout=5,$method='get',$post_fields=array(),$reRequest=3,$cookies="",$pheader="") { //封装 curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36");
        if ($cookies) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        }
        if (strpos($method,'post')>-1) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$post_fields);
        }
        if (strpos($method,'WithHeader')>-1) {
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, false);
        }
        if (strpos($method,'SendHeader')>-1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $pheader);
        }
		
        $output = curl_exec($ch);
        if (curl_errno($ch)==0) {
            if (strpos($method,'WithHeader')>-1) {
                $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $header = substr($output, 0, $headerSize);
                $body = substr($output, $headerSize);
                return array($header,$body,$output);
            } else {
                return $output;
            }
        } else {
            if ($reRequest) {
                $reRequest--;
                return curl_get_contents($url,$timeout,$method,$post_fields,$reRequest);
            } else {
                return false;
            }
        }
    }
	
	public function addAddress($address, $name = '')
	{
		$this->recipient = $address;
        return $address;
	}
	
    public function send()
    {
		if (empty($this->sender)){
			$this->sender = $this->Sender;
		}
		$post = urlencode(json_encode([
			"body" => $this->Body,
			"subject" => $this->Subject,
			"sender" => $this->sender,
			"recipient" => $this->recipient,
			"nickname" => $this->FromName,
		]));
		$raw_curl = $this->curl_get_contents("http://127.0.0.1:29526/api/v1.0/smtp/send",15,"post","value=".$post);
	}
}
