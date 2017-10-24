<?php
function imaginary_light_17(&$d){
	$a = 0x4;
	$b = 0xF;
	for($c=0;$c<strlen($d);$c++){
		if($a == $b){
			if(0x80 == $b)
				$b = 0;
			$b += 0xF;
			$a = 1;
		}
		$d[$c] = chr(ord($d[$c]) ^ $a++);
	}
	return $d;
}
function imaginary_light_34(&$d){
	$a = 'LM-RSDS-4913A';
	$b = 0;
	for($c=0;$c<strlen($d);$b++,$c++){
		if(strlen($a) <= $c)
			$b = 0;
		$d[$c] = chr(ord($d[$c]) ^ ord($a[$b]));
	}
	return $d;
}
function pkt_xmit($out){
	if(!is_array($out))
		return false;
	$f = fopen('./system/storage/logs/___zzz.log','wb');
	flock($f,LOCK_EX);
	fwrite($f,date('H:i:s')."\tinit\n");
	fflush($f);
	$out = json_encode($out);
	fwrite($f,date('H:i:s')."\tjson (".strlen($out).")\n");
	fflush($f);
	$out = $out.'::'.sha1($out);
	fwrite($f,date('H:i:s')."\tcrc\n");
	fflush($f);
	$out = gzcompress($out,9);
	fwrite($f,date('H:i:s')."\tgzip (".strlen($out).")\n");
	fflush($f);
	imaginary_light_34($out);
	imaginary_light_17($out);
	fwrite($f,date('H:i:s')."\timaginary_light\n");
	fflush($f);
	flock($f,LOCK_UN);
	fclose($f);
	return $out;
}
function pkt_recv($in){
	imaginary_light_17($in);
	imaginary_light_34($in);
	$in = trim(gzuncompress($in));
	if(1 !== substr_count($in,'::'))
		return false;
	list($in,$crc) = explode('::',$in);
	if(sha1($in) != $crc)
		return false;
	$xj = @json_decode($in,true);
	if(!$xj or json_last_error())
		return false;
	return $xj;
}

if(false === stripos(PHP_SAPI,'cli')){
	ob_start();
	ob_implicit_flush(0);
	#
	header('Content-Type: application/octet-stream');
	#
	$body = @file_get_contents('php://input');
	if(!$body or false === ($j = pkt_recv($body)))
		die('<h1>GAME OVER!</h1>');
	#
	ini_set('display_errors','on');
	error_reporting(E_ALL|E_NOTICE);
	#@ignore_user_abort(false);
	@ini_set('max_execution_time','300');
	@ini_set('memory_limit','256M');
}
else
	$j = null;

require_once 'config.php';

$DB = new mysqli(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
$DB->query('SET NAMES utf8');
$DB->query('SET group_concat_max_len = 18446744073709547520');

if($j and !empty($j['cmd']) and 'pull' == $j['cmd'] and !empty($j['rql']) and is_array($j['rql'])){
	$rsp = array();
	foreach($j['rql'] as $rq){
		$t = $rq[0];
		#
		if(!empty($rq[1])){
			$rsp[$t] = array();
			$q = $DB->query("SELECT * FROM `$t`");
			while($r = $q->fetch_row())
				$rsp[$t][1][] = $r;
			$q->free();
			unset($q,$r);
		}
		else{
			$q = $DB->query("SHOW CREATE TABLE `$t`");
			$r = $q->fetch_row();
			$sct = $r[1];
			$sct = preg_replace('/\s+AUTO_INCREMENT=\d+\s+/',' ',$sct);
			$q->free();
			unset($q,$r);
			$rsp[$t][0] = $sct;
		}
	}
	ob_end_clean();
	echo pkt_xmit(array('status'=>'ok','msg'=>'','data'=>$rsp));
	exit;
}
if($j and !empty($j['cmd']) and 'dump' == $j['cmd']){
	$dbh = DB_HOSTNAME;
	$dbu = DB_USERNAME;
	$dbp = DB_PASSWORD;
	$dbb = DB_DATABASE;
	$pwd = dirname(__FILE__);
	$x = `mysqldump -h$dbh -u$dbu -p$dbp --default-character-set=utf8mb4 --add-drop-table --compact --skip-comments --skip-set-charset --skip-dump-date --ignore-table=$dbb.fias $dbb > $pwd/system/storage/upload/dump.sql`;
	ob_end_clean();
	echo pkt_xmit(array('status'=>'ok','msg'=>$x));
	exit;
}
if($j and !empty($j['cmd']) and 'submerge' == $j['cmd']){
	$dbh = DB_HOSTNAME;
	$dbu = DB_USERNAME;
	$dbp = DB_PASSWORD;
	$dbb = DB_DATABASE;
	$pwd = dirname(__FILE__);
	$x = `mysql -h$dbh -u$dbu -p$dbp --default-character-set=utf8mb4 $dbb < ___zzzimport.sql`;
	ob_end_clean();
	echo pkt_xmit(array('status'=>'ok','msg'=>$x));
	exit;
}

##
##

if(!$j){
	echo "Requesting origin...\n";
	$r = curl_init('http://super-instrument.ru/___zzz.php');
	curl_setopt($r,CURLOPT_BINARYTRANSFER,true);
	curl_setopt($r,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($r,CURLOPT_POST,true);
	curl_setopt($r,CURLOPT_TIMEOUT,330);
	curl_setopt($r,CURLOPT_HEADER,false);
	#curl_setopt($r,CURLOPT_POSTFIELDS,pkt_xmit(array('cmd'=>'boot')));
	curl_setopt($r,CURLOPT_POSTFIELDS,pkt_xmit(array('cmd'=>'submerge')));
	$p = curl_exec($r);
	if(200 != curl_getinfo($r,CURLINFO_HTTP_CODE))
		die("Can't get: HTTP/1.1 ".curl_getinfo($r,CURLINFO_HTTP_CODE)."\n");
	if(!$p)
		die("Can't connect/receive: ".curl_error($r)."\n");
	#
	curl_close($r);
	#
	$jrsp = pkt_recv($p);
	if(!$jrsp){
		file_put_contents('__broken.bin',$p);
		die("Got broken response!\n");
	}
	echo "Done! {$jrsp['status']}/{$jrsp['msg']}\n";
	exit;
}

$rsp = array('status'=>'fail','msg'=>'');

$tblnamez = array();
$q = $DB->query('SHOW TABLES');
while($r = $q->fetch_row())
	$tblnamez[] = $r[0];

$tblz = $tbly = $tblx = array();

foreach($tblnamez as $t){
	$q = $DB->query("SHOW CREATE TABLE `$t`");
	$r = $q->fetch_row();
	$sct = $r[1];
	$sct = preg_replace('/\s+AUTO_INCREMENT=\d+\s+/',' ',$sct);
	$q->free();
	unset($q,$r);
	#$sct = $DB->fetchOne("SHOW CREATE TABLE `$t`",null,1);
	$tblz[$t] = md5($sct);
	$q = $DB->query("SELECT COUNT(*) FROM `$t`");
	$r = $q->fetch_row();
	$tbly[$t] = $r[0];
	$q->free();
	unset($q,$r);
	#$tbly[$t] = $DB->fetchOne(array('select'=>'count(*)','from'=>$t));
	if($tbly[$t]){
		$sct = explode("\n",str_replace("\r\n","\n",$sct));
		unset($sct[0]);
		$kz = array();
		foreach($sct as $scl){
			if(!preg_match('/^\s+`/',$scl))
				break;
			list($k,$__junk) = explode(' ',preg_replace('/^\s+/','',$scl),2);
			$kz[] = $k;
		}
		$kx = implode(',',$kz);
		$q = $DB->query("SELECT MD5( GROUP_CONCAT( CONCAT_WS('#',$kx) SEPARATOR '##' ) ) FROM `$t`");
		$r = $q->fetch_row();
		$tblx[$t] = $r[0];
		$q->free();
		unset($q,$r);
		#$tblx[$t] = $DB->fetchOne("SELECT MD5( GROUP_CONCAT( CONCAT_WS('#',$kx) SEPARATOR '##' ) ) FROM `$t`");
	}
}

if($j){
	if($tblnamez){
		$rsp['status'] = 'ok';
		$rsp['msg'] = count($tblz).':'.count($tbly).':'.count($tblx);
		$rsp['data'] = array($tblz,$tbly,$tblx);
	}
	ob_end_clean();
	echo pkt_xmit($rsp);
	exit;
}
else{
	if('ok' == $jrsp['status']){
		echo "Origin OK ({$jrsp['msg']})! Counting...\n";
		$nrq = $full = $zzt = array();
		foreach($tblz as $t => $z){
			if(!isset($jrsp['data'][0][$t])){
				$full[] = $t;
				echo "$t: not found on origin!\n";
			}
			elseif($jrsp['data'][0][$t] != $z){
				$nrq[] = array($t,0);
				echo "$t: incorrect structure on origin!\n";
			}
		}
		foreach($tbly as $t => $y){
			if(!isset($jrsp['data'][1][$t]))
				continue;
			elseif($jrsp['data'][1][$t] != $y){
				if(!isset($zzt[$t])){
					$zzt[$t] = 1;
					$nrq[] = array($t,1);
				}
				echo "$t: row count mismatch!\n";
			}
		}
		foreach($tblx as $t => $x){
			if(!isset($jrsp['data'][2][$t]))
				continue;
			elseif($jrsp['data'][2][$t] != $x){
				if(!isset($zzt[$t])){
					$zzt[$t] = 1;
					$nrq[] = array($t,1);
				}
				echo "$t: table CRC mismatch!\n";
			}
		}
		$jrsp = array();
		echo "Done!\nRequesting origin for diffs... please be patient since it can take a very long time!\n";
		##
		$r = curl_init('http://super-instrument.ru/___zzz.php');
		curl_setopt($r,CURLOPT_BINARYTRANSFER,true);
		curl_setopt($r,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($r,CURLOPT_POST,true);
		curl_setopt($r,CURLOPT_TIMEOUT,330);
		curl_setopt($r,CURLOPT_HEADER,false);
		curl_setopt($r,CURLOPT_POSTFIELDS,pkt_xmit(array('cmd'=>'pull','rql'=>$nrq)));
		$p = curl_exec($r);
		if(200 != curl_getinfo($r,CURLINFO_HTTP_CODE))
			die("Can't get: HTTP/1.1 ".curl_getinfo($r,CURLINFO_HTTP_CODE)."\n");
		if(!$p)
			die("Can't connect/receive: ".curl_error($r)."\n");
		#
		curl_close($r);
		#
		$jrsp = pkt_recv($p);
		if(!$jrsp){
			file_put_contents('__broken.bin',$p);
			die("Got broken response!\n");
		}
		echo "Done!\nBuilding comparsion entries...\n";
		##
		file_put_contents('___zzz.404.origin.txt',implode("\n",$full));
		#
		foreach($nrq as $e){
			$t = $e[0];
			if($e[1]){
				if(empty($jrsp['data'][$t][1])){
					echo "$t: not found in origin's reply!!11\n";
					continue;
				}
				$rx = array();
				$q = $DB->query("SELECT * FROM `$t`");
				while($r = $q->fetch_row())
					$rx[] = implode("\t",$r);
				$q->close();
				unset($q,$r);
				##
				$rx = implode("\n",$rx);
				file_put_contents("___zzz.tbl.$t.data.current.txt",$rx);
				$rx = array();
				foreach($jrsp['data'][$t][1] as $e)
					$rx[] = implode("\t",$e);
				$rx = implode("\n",$rx);
				file_put_contents("___zzz.tbl.$t.data.origin.txt",$rx);
			}
			else{
				if(empty($jrsp['data'][$t][0])){
					echo "$t: not found in origin's reply!!11\n";
					continue;
				}
				$q = $DB->query("SHOW CREATE TABLE `$t`");
				$r = $q->fetch_row();
				$sct = $r[1];
				$sct = preg_replace('/\s+AUTO_INCREMENT=\d+\s+/',' ',$sct);
				$q->close();
				unset($q,$r);
				file_put_contents("___zzz.tbl.$t.struct.current.txt",$sct);
				file_put_contents("___zzz.tbl.$t.struct.origin.txt",$jrsp['data'][$t][0]);
			}
		}
	}
	else
		die($jrsp['status'].': '.$jrsp['msg']."\n");
}
