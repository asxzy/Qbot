<?php
// 查看是否有已存在的认证
$mysql = new SaeMysql();
$sql = "SELECT * FROM `oauth` LIMIT 1";
$data = $mysql->getData( $sql );

include_once( 'config.php' );
include_once( 'saet.ex.class.php' );

$o = new SaeTOAuth( WB_AKEY , WB_SKEY , $data[0]['oauth_token'] , $data[0]['oauth_token_secret']  );

$keys = $o->getAccessToken(  $_REQUEST['oauth_verifier'] ) ;

$mysql->runSql("TRUNCATE TABLE oauth");
$mysql->runSql("INSERT INTO `oauth` (`oauth_token`, `oauth_token_secret`) VALUES ('$keys[oauth_token]' , '$keys[oauth_token_secret]');");
?>
授权完成,<a href="./index.php">进入你的微博列表页面</a><br />