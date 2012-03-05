<?php
// 查看是否有已存在的认证
$mysql = new SaeMysql();

include_once( 'config.php' );
include_once( 'saet.ex.class.php' );

$o = new SaeTOAuth( WB_AKEY , WB_SKEY  );

$keys = $o->getRequestToken();

if( strpos( $_SERVER['SCRIPT_URI'] , 'install.php' ) === false )
	$callback =  $_SERVER['SCRIPT_URI'].'/callback.php';
else	
	$callback =  str_replace( 'install.php' , 'callback.php' , $_SERVER['SCRIPT_URI'] );

$aurl = $o->getAuthorizeURL( $keys['oauth_token'] ,false , $callback );
$mysql->runSql("TRUNCATE TABLE oauth");
$mysql->runSql("INSERT INTO `oauth` (`oauth_token`, `oauth_token_secret`) VALUES ('$keys[oauth_token]' , '$keys[oauth_token_secret]');");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>XDTIC_BOT</title>
</head>
<body>
    <p><a href="<?=$aurl?>"><img src="weibo_login.png" title="点击进入授权页面" alt="点击进入授权页面" border="0" /></a></p>

</body>
</html>
