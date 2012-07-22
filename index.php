<?php

//ip!
$ip=$_SERVER['REMOTE_ADDR'];
$client="[client ".$ip."]";
if (isset($_POST['CLEAR']))	exec("cat /dev/null > ./error.log");
$log_path = './error.log';
exec("cat $log_path",$eror_log);

function pre($arr){
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

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
			<form class="pull-left navbar-search pull-left" action="./index.php" method="POST" target="_self">
				<button class="btn btn-inverse"><i class="icon-refresh icon-white"></i> actualitza</button>
			</form>
			<form class="pull-left navbar-search pull-left" action="./index.php" method="POST" target="_self">
				<input type="hidden" value="Ok" name="CLEAR" />
				<?php $disabled = (count($eror_log)>=1) ? '' : ' disabled' ; ?>
				<button class="btn btn-inverse" <?php echo $disabled;?>><i class="icon-trash icon-white"></i> esborra</button>
			</form>
		</div>
	</div>
</header>
<div class="container-fluid">
	<section>
		<div class="row-fluid">
			<p>client: <code><?php echo $ip;?></code>, log: <code><?php echo $log_path;?></code></p>
		<?php
		if (count($eror_log)>=1) {
			echo '<table class="table table-striped table-bordered table-condensed">';
			for ($i=0;$i<count($eror_log);$i++) {
				if (strpos($eror_log[$i],$client)!==false) {
					$stri = '<tr>';
					$eror_log[$i]=str_replace($client,"",$eror_log[$i]);
					$client_eror_log=explode("]",$eror_log[$i]);
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
						if (strpos($eror_log[$i],$findme)!==false){
							$stri = str_replace( $findme, '<span class="label label-warning">'.$findme.'</span>', $stri).'';
						}

						$findme = 'PHP Warning:';
						if (strpos($eror_log[$i],$findme)!==false){
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
	</section>
</div>
<script type="text/javascript">
window.onload=toBottom;
function toBottom() {	window.scrollTo(0, document.body.scrollHeight); }
</script>
</body>
</html>
