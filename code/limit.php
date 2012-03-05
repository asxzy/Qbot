<?php
// 查看是否有已存在的认证
$mysql = new SaeMysql();
$sql = "SELECT * FROM `oauth` LIMIT 1";
$keys = $mysql->getLine( $sql );

include_once( 'config.php' );
include_once( 'saet.ex.class.php' );

$c = new SaeTClient( WB_AKEY , WB_SKEY ,$keys['oauth_token'] , $keys['oauth_token_secret']  );
$limit = $c->rate_limit_status ();

var_dump($limit);
?>