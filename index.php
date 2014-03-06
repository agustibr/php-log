<?php
require('config.php');
$has_client_error=false;
$warn_has_client_error = true;
//ip!
$ip=$_SERVER['REMOTE_ADDR'];
if ( isset($_POST['ip']) ) :
	$ip=$_POST['ip'];
	$ip_input = '<input type="hidden" value="'.$_POST['ip'].'" name="ip" />';
elseif ( isset($_GET['ip']) ) :
	$ip=$_GET['ip'];
	$ip_input = '<input type="hidden" value="'.$_GET['ip'].'" name="ip" />';
endif;
$client="[client ".$ip."";
$log_path = $setup['log_path'];
$ssl_log_path = $setup['ssl_log_path'];
if ( isset($_POST['ssl']) ){
	if ($_POST['ssl'] == 'ssl_on') $log_path = $ssl_log_path; 
} 
$ssl = (isset($_POST['ssl']) && $_POST['ssl']== 'ssl_on') ? '<input type="hidden" value="ssl_on" name="ssl" />' : '<input type="hidden" value="ssl_off" name="ssl" />' ;
if (isset($_POST['CLEAR']))	exec("cat /dev/null > $log_path");
exec("cat $log_path",$error_log);
function pre($arr){
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

function time_ago($time) {
	$periods = array("sec", "min", "hour", "day", "week", "month", "year", "decade");
	$lengths = array("60","60","24","7","4.35","12","10");
	$now = time();
	$difference = $now - $time;
	$tense = "ago";
	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
		$difference /= $lengths[$j];
	}
	$difference = round($difference);
	if($difference != 1) $periods[$j].= "s";
	return "$difference $periods[$j] ago";
}
// pre($error_log);
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="favicon.ico"/>
	<title>php-log [ip: <?php echo $ip;?>]</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="bootstrap/css/bootstrap.css" media="screen" rel="stylesheet" type="text/css" />
	<style type="text/css">
	body {margin-bottom: 60px;padding-top:1em;border-top:2px solid #222;}
	</style>

</head>
<body>
<header class="navbar navbar-fixed-bottom">
	<div class="navbar-inner">
		<div class="container-fluid">
			<?php //echo $varnotdefined; ?>
			<span class="pull-left brand">PHP Log</span>  
			<form class="pull-left navbar-search" action="" method="POST" target="_self">
				<?php 
				echo $ssl; 
				if( isset($ip_input) ) echo $ip_input; 
				?>

				<button class="btn"><i class="icon-refresh"></i> actualitza</button>
			</form>
			<form class="pull-left navbar-search" action="" method="POST" target="_self">
				<input type="hidden" value="Ok" name="CLEAR" />
				<?php 
				echo $ssl; 
				if( isset($ip_input) ) echo $ip_input; 
				?>
				<?php $disabled = (count($error_log)>=1) ? '' : ' disabled' ; ?>
				<button class="btn" <?php echo $disabled;?>><i class="icon-trash"></i> esborra</button>
			</form>
			<form class="pull-right navbar-search form-inline" action="./index.php" method="POST" target="_self">
				<input type="hidden" value="ssl_off" name="ssl" />
				<?php
				if( isset($ip_input) ) echo $ip_input; 
				$disabled = ($ssl_log_path!='') ? '' : ' disabled="disabled"' ;
				$checked = ( isset($_POST['ssl']) && $_POST['ssl']== 'ssl_on') ? 'checked="checked"' : '' ;
				?>
				<input type="checkbox" <?php echo $disabled .' ' . $checked;?> value="ssl_on" name="ssl" id="optionsCheckbox"/>
				<button class="btn btn-inverse" <?php echo $disabled;?>><i class="icon-lock icon-white"></i> ssl</button>
			</form>
		</div>
	</div>
</header>
<div class="container-fluid">
	<section>
		<div class="row-fluid">
			<div class="span12">
				
				<form class="pull-left" action="" method="POST" target="_self">
					<?php 
					echo $ssl; 
					if( isset($ip_input) ) echo $ip_input; 
					?>
					<button class="btn btn-mini"><i class="icon-refresh"></i> actualitza</button>&nbsp;
				</form>
				<form class="pull-left" action="" method="POST" target="_self">
					<input type="hidden" value="Ok" name="CLEAR" />
					<?php 
					echo $ssl; 
					if( isset($ip_input) ) echo $ip_input; 
					?>
					<?php $disabled = (count($error_log)>=1) ? '' : ' disabled' ; ?>
					<button class="btn btn-mini" <?php echo $disabled;?>><i class="icon-trash"></i></button>&nbsp;
				</form>
				<p class="pull-left">client: <code><?php echo $ip;?></code>, log: <code><?php echo $log_path;?></code></p>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<?php
				
				if (count($error_log)>=1) {
					echo '<table class="table table-striped table-bordered table-condensed">';
					for ($i=0;$i<count($error_log);$i++) {
						if (strpos($error_log[$i],$client)!==false) {
							echo 'kk';
							$has_client_error=true;
							$stri = '<tr>';
							$error_log[$i]=str_replace($client,"",$error_log[$i]);
							$client_eror_log=explode("]",$error_log[$i]);
							$hora=explode(" ",$client_eror_log[0]);
							//$date="30/07/2010 13:24:13"; //date example
							if($hora[1]=='Jan') $mes = '01';
							if($hora[1]=='Feb') $mes = '02';
							if($hora[1]=='Mar') $mes = '03';
							if($hora[1]=='Apr') $mes = '04';
							if($hora[1]=='May') $mes = '05';
							if($hora[1]=='Jun') $mes = '06';
							if($hora[1]=='Jul') $mes = '07';
							if($hora[1]=='Aug') $mes = '08';
							if($hora[1]=='Sep') $mes = '09';
							if($hora[1]=='Oct') $mes = '10';
							if($hora[1]=='Nov') $mes = '11';
							if($hora[1]=='Dec') $mes = '12';
							$auxhr=explode(".", $hora[3]);
							$date = $hora[2]."/".$mes."/".$hora[4]." ".$auxhr[0];
							$day=$hora[2];
							$month=$mes;
							$year=$hora[4];
							$dy=explode(":", $auxhr[0]);
							$hour=$dy[0];
							$minute=$dy[1];
							$second=$dy[2];
							$timestamp=mktime($hour, $minute, $second , $month, $day, $year);
							$time_ago = time_ago($timestamp);
							$findme='[:error';
							if(strpos($client_eror_log[1], $findme)!=''){
								$error_type = '<span class="badge">error</span>';
							} else {
								$error_type = $client_eror_log[1];
							}

							$stri .='<td>'.$time_ago.'</td><td>'.$error_type.'</td><td><span>'.$client_eror_log[4];

							if(strpos($client_eror_log[4], $findme)!=''){
								
							} else {
								//$stri.=$client_eror_log[4];
								
								$findme = 'PHP Notice:';
								if (strpos($error_log[$i],$findme)!==false){
									$stri = str_replace( $findme, '<span class="label label-warning">'.$findme.'</span>', $stri).'';
								}

								$findme = 'PHP Warning:';
								if (strpos($error_log[$i],$findme)!==false){
									$stri = str_replace( $findme, '<span class="label label-important">'.$findme.'</span> ', $stri).'';
								}

								$findme = 'File does not exist:';
								if (strpos($error_log[$i],$findme)!==false){
									$stri = str_replace( $findme, '<span class="label label-info">'.$findme.'</span> ', $stri).'';
								}

								$findme = 'Undefined variable:';
								if (strpos($error_log[$i],$findme)!==false){
									$stri = str_replace( $findme, '<strong>'.$findme.'</strong> ', $stri).'';
								}
								$findme = 'referer:';
								if (strpos($error_log[$i],$findme)!==false){
									$stri = str_replace( $findme, '<br/><span class="label">'.$findme.'</span> ', $stri).'';
								}
								$findme = ' in ';
								if (strpos($error_log[$i],$findme)!==false){
									$stri = str_replace( $findme, ''.$findme.'<code> ', $stri).'';
								}
								$findme = ' on line ';
								if (strpos($error_log[$i],$findme)!==false){
									$stri = str_replace( $findme, '</code>'.$findme.'', $stri).'';
								}
								
								
							}
							echo $stri.'</span></td></tr>';
						} else {
							if(!$has_client_error && $warn_has_client_error) :
								echo '<td>?</td><td><span class="label label-success">other</span></td><td>una altra ip te errors</td></tr>';
								$warn_has_client_error = false;
							endif;
						}
					}
					echo "</table>";
				} else {
					echo '<div class="alert alert-success"><strong>Ben Fet!</strong> No hi ha errors :)</div>';
				}
				?>	
			</div>
		</div>
	</section>
</div>
<script type="text/javascript">
window.onload=toBottom;
function toBottom() {	window.scrollTo(0, document.body.scrollHeight); }
</script>
</body>
</html>
