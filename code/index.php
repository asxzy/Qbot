<?php
// 发送报头
header('Content-Type: text/html; charset=UTF-8');

// 判断日常微博
$time = date('H:i');
$date = date('Y-m-d');
// 睡觉时间晚上禁言
if( $time <= '07:55' && '23:25' <= $time )
	exit();

// 查看是否有已存在的认证
$mysql = new SaeMysql();
$sql = "SELECT * FROM `oauth` LIMIT 1";
$keys = $mysql->getLine( $sql );

$sql = "SELECT * FROM `status` LIMIT 1";
$status = $mysql->getLine( $sql );

include_once( 'config.php' );
include_once( 'saet.ex.class.php' );

$c = new SaeTClient( WB_AKEY , WB_SKEY ,$keys['oauth_token'] , $keys['oauth_token_secret']  );

// 早上
if('07:55' < $time && $time < '08:05' && $date != $status['morning']){
	$random = array_rand($words['morning']);
	$c->update($words['morning'][$random]);
	$sql = "UPDATE `status` SET `morning` = '$date' WHERE `id` =1;";
	$mysql->runSql($sql);
	exit();
}
// 中午
if('11:55' < $time && $time < '12:05' && $date != $status['noon']){
	$random = array_rand($words['noon']);
	$c->update($words['noon'][$random]);
	$sql = "UPDATE `status` SET `noon` = '$date' WHERE `id` =1;";
	$mysql->runSql($sql);
	exit();
}
// 晚上
if('23:15' < $time && $time < '23:25' && $date != $status['evening']){
	$random = array_rand($words['evening']);
	$c->update($words['evening'][$random]);
	$sql = "UPDATE `status` SET `evening` = '$date' WHERE `id` =1;";
	$mysql->runSql($sql);
	exit();
}


// 判断关注人微博
$ms = array_reverse($c->home_timeline(1,200,$status['lastview']));
$user = $c->verify_credentials();
//$ms = array_reverse($c->home_timeline(1,200)); // for debug


// 如有新微博，则开始匹配
if( is_array( $ms ) && $ms != NULL ){
	// 匹配关键字
	foreach( $ms as $item ){
		// 判断是否为自己转发或已经转发过
		if($item['user']['id'] != $user['id'] && $item['retweeted_status']['user']['id'] != $user['id'] ){
			// 用户匹配
			foreach ( $key_users as $x){
				if(stristr($item['screen_name'],$x) != false || stristr($item['retweeted_status']['screen_name'],$x) != false){
					//判断是否是二次转发
					if(isset($item['retweeted_status'])){
						$comment = $item['text'];
					}else{
						$comment = 'RT';
					}
					$flag = $item['id'];
					$c->repost($item['id'],$comment);
					echo '转发微博:ID:'.$item['id'].' text:'.$item['text'];
					// 更新最后状态
					$sql = "UPDATE `status` SET `lastview` = '$flag' WHERE `id` =1;";
					$mysql->runSql($sql);
					exit();
				}
			}
			// 关键词匹配
			foreach ( $key_words as $x){
				if(stristr($item['text'],$x) != false || stristr($item['retweeted_status']['text'],$x) != false){
					//判断是否是二次转发
					if(isset($item['retweeted_status'])){
						$comment = $item['text'];
					}else{
						$comment = 'RT';
					}
					$flag = $item['id'];
					$c->repost($item['id'],$comment);
					echo '转发微博:ID:'.$item['id'].' text:'.$item['text'];
					// 更新最后状态
					$sql = "UPDATE `status` SET `lastview` = '$flag' WHERE `id` =1;";
					$mysql->runSql($sql);
					exit();
				}
			}
		}
	}
	// 更新最后状态
	$flag = $ms[0]['id'];
	$sql = "UPDATE `status` SET `lastview` = '$flag' WHERE `id` =1;";
	$mysql->runSql($sql);
}


// 整点无任务卖萌~~
$minute = date('i');
if('55' < $minute || $minute < '05'){
	$random = array_rand($words['cute']);
	$c->update($words['cute'][$random]);
	echo '卖萌:'.$words['cute'][$random];
	exit();
}


?>