<?php

//ip!
$ip=$_SERVER['REMOTE_ADDR'];
$client="[client ".$ip."]";
$log_path = '/var/log/apache2/error.log';
$ssl_log_path = '/var/log/apache2/ssl_error.log';
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
							$new_h= $hora[2]." ".$hora[1]." ".$hora[3]." ".$hora[4];			
							$findme='[error';
							if(strpos($client_eror_log[1], $findme)!=''){
								$error_type = '<span class="badge">error</span>';
							} else {
								$error_type = $client_eror_log[1];
							}

							$stri .='<td>'.$new_h.'</td><td>'.$error_type.'</td><td><span>'.$client_eror_log[2];

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
