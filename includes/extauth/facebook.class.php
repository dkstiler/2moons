<?php

require(ROOT_PATH.'/includes/libs/facebook/facebook.php');
class FacebookAuth extends Facebook {
	function __construct()
	{
		global $gameConfig;
		
		if($gameConfig['facebookEnable'] == 0)
		{
			HTTP::redirectTo("index.php");
		}
		
		parent::__construct(array(
			'appId'  => $gameConfig['facebookAPIKey'],
			'secret' => $gameConfig['facebookSecureKey'],
		));	
	}
	
	function isVaild() {
		return !!$this->getAccount();
	}
	
	function getAccount()
	{
		return $this->getUser();
	}
	
	function register() 
	{
		$uid	= $this->getAccount();
		
		try {
			$me = $this->api('/me');
		} catch (FacebookApiException $e) {
			HTTP::redirectTo('index.php?code=4');
		}
		
		$ValidReg	= $GLOBALS['DATABASE']->countquery("SELECT cle FROM ".USERS_VALID." WHERE universe = ".$UNI." AND email = '".$GLOBALS['DATABASE']->escape($me['email'])."';");
		if(!empty($ValidReg))
			HTTP::redirectTo("index.php?uni=".$UNI."&page=reg&action=valid&clef=".$ValidReg);
							
		$GLOBALS['DATABASE']->query("INSERT INTO ".USERS_AUTH." SET
		id = (SELECT id FROM ".USERS." WHERE email = '".$GLOBALS['DATABASE']->escape($me['email'])."' OR email_2 = '".$GLOBALS['DATABASE']->escape($me['email'])."'),
		account = ".$uid.",
		mode = 'facebook';");
	}
	
	function getLoginData()
	{
		global $UNI;
	
		$uid	= $this->getAccount();
		
		return $GLOBALS['DATABASE']->getFirstRow("SELECT 
		user.id, user.username, user.dpath, user.authlevel, user.id_planet 
		FROM ".USERS_AUTH." auth 
		INNER JOIN ".USERS." user ON auth.id = user.id AND user.universe = ".$UNI."
		WHERE auth.account = ".$uid." AND mode = 'facebook';");
	}
}