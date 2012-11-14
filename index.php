<?php
require('config.php');
//ip!
$ip=$_SERVER['REMOTE_ADDR'];
$client="[client ".$ip."]";
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
//pre($_POST);
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
			<span class="pull-left brand">PHP Log</span>  
			<form class="pull-left navbar-search" action="" method="POST" target="_self">
				<?php echo $ssl; ?>
				<button class="btn"><i class="icon-refresh"></i> actualitza</button>
			</form>
			<form class="pull-left navbar-search" action="" method="POST" target="_self">
				<input type="hidden" value="Ok" name="CLEAR" />
				<?php echo $ssl; ?>
				<?php $disabled = (count($error_log)>=1) ? '' : ' disabled' ; ?>
				<button class="btn" <?php echo $disabled;?>><i class="icon-trash"></i> esborra</button>
			</form>
			<form class="pull-right navbar-search form-inline" action="./index.php" method="POST" target="_self">
				<input type="hidden" value="ssl_off" name="ssl" />
				<?php 
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
					<?php echo $ssl; ?>
					<button class="btn btn-mini"><i class="icon-refresh"></i> actualitza</button>&nbsp;
				</form>
				<form class="pull-left" action="" method="POST" target="_self">
					<input type="hidden" value="Ok" name="CLEAR" />
					<?php echo $ssl; ?>
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
							$stri = '<tr>';
							$error_log[$i]=str_replace($client,"",$error_log[$i]);
							$client_eror_log=explode("]",$error_log[$i]);
							$hora=explode(" ",$client_eror_log[0]);
							//$date="30/07/2010 13:24:13"; //date example
							$mes = ($hora[1]=='Jan') ? '01' : $hora[1] ;
							$mes = ($hora[1]=='Feb') ? '02' : $hora[1] ;
							$mes = ($hora[1]=='Mar') ? '03' : $hora[1] ;
							$mes = ($hora[1]=='Apr') ? '04' : $hora[1] ;
							$mes = ($hora[1]=='May') ? '05' : $hora[1] ;
							$mes = ($hora[1]=='Jun') ? '06' : $hora[1] ;
							$mes = ($hora[1]=='Jul') ? '07' : $hora[1] ;
							$mes = ($hora[1]=='Aug') ? '08' : $hora[1] ;
							$mes = ($hora[1]=='Sep') ? '09' : $hora[1] ;
							$mes = ($hora[1]=='Oct') ? '10' : $hora[1] ;
							$mes = ($hora[1]=='Nov') ? '11' : $hora[1] ;
							$mes = ($hora[1]=='Dec') ? '12' : $hora[1] ;
							$date = $hora[2]."/".$mes."/".$hora[4]." ".$hora[3];
							list($day, $month, $year, $hour, $minute, $second) = split('[/ :]', $date); 
							$timestamp=mktime($hour, $minute,$second , $month, $day, $year);
							$time_ago = time_ago($timestamp);

							$findme='[error';
							if(strpos($client_eror_log[1], $findme)!=''){
								$error_type = '<span class="badge">error</span>';
							} else {
								$error_type = $client_eror_log[1];
							}

							$stri .='<td>'.$time_ago.'</td><td>'.$error_type.'</td><td><span>'.$client_eror_log[2];

							if(strpos($client_eror_log[2], $findme)!=''){

							} else {
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
