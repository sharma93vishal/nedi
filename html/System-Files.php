<?php
# Program: System-Files.php
# Programmer: Remo Rickli

$printable = 1;
$exportxls = 0;
$logpath = '/tmp';

include_once ("inc/header.php");

# Edit to fit your system...
$sysfiles = array(	"log/msg.txt",
			"log/iftools.php",
			"log/devtools.php",
			"log/montools.php",
			"log/cmd_$_SESSION[user].php",
			"$nedipath/inc/crontab",
			"$nedipath/nedi.conf",
			"$nedipath/seedlist",
			"$nedipath/agentlist",
			"/etc/raddb/eap.conf",
			"/etc/raddb/radiusd.conf",
			"/etc/raddb/clients.conf",
			"/etc/raddb/users",
			"/etc/snmp/snmptrapd.conf",
			"/etc/dhcpd.conf",
			"/var/log/messages",
			"/var/log/smsd.log",
			"/var/log/radius/radius.log",
			"/var/www/logs/error_log"
		);

$ocol[1] = "DarkBlue";
$ocol[2] = "DarkRed";
$ocol[4] = "DarkMagenta";
$ocol[5] = "Brown";
$ocol[6] = "DarkGreen";
$ocol[7] = "GoldenRod";

$tftpboot = "/var/tftpboot";

$_GET = sanitize($_GET);
$del  = isset($_GET['del']) ? $_GET['del'] : '';
$mde  = isset($_GET['mde']) ? $_GET['mde'] : '';
$sub  = isset($_GET['sub']) ? $_GET['sub'] : 'topo';
$tft  = isset($_GET['tft']) ? preg_replace('/[<>\/\\\]/','',$_GET['tft']) : "";

$_POST= sanitize($_POST);
$mde  = isset($_POST['mde']) ? $_POST['mde'] : $mde;
$txt  = isset($_POST['txt']) ? $_POST['txt'] : '';
$log  = isset($_POST['log']) ? $_POST['log'] : '';
$cfg  = isset($_POST['cfg']) ? $_POST['cfg'] : '';
$cli  = isset($_POST['cli']) ? $_POST['cli'] : '';
$sub  = isset($_POST['sub']) ? $_POST['sub'] : $sub;
$tft  = isset($_POST['tft']) ? preg_replace('/[<>\/\\\]/','',$_POST['tft']) : $tft;
$file = isset($_POST['file']) ? $_POST['file'] : "";
$wrt  = isset($_POST['wrt']) ? 1 : "";
$all  = isset($_POST['all']) ? "checked" : "";

if( $mde == 'o' and !preg_match('/^topo/',$sub) ){							# Only allow topo/
	$mde = '';
	$sub = 'topo';
}

$editable = 0;
$delable  = 0;
$tftpable = 0;

if( $isadmin and $file){
	$file  = ( in_array($file, $sysfiles) ) ? $file : "";
	$editable = 1;
}elseif( preg_match("/net/",$_SESSION['group']) ){
	if($log){
		$file = ( file_exists("$logpath/$log") )? "$logpath/$log":"";
		$delable = 1;
	}elseif($cfg){
		$file = ( file_exists("$nedipath/conf/$cfg") )? "$nedipath/conf/$cfg":"";
		$delable  = 1;
		$tftpable = 1;
	}elseif($cli){
		$file = ( file_exists("$nedipath/cli/$cli") )? "$nedipath/cli/$cli":"";
		$delable  = 1;
		$tftpable = 0;
	}elseif($tft){
		$file  = "$tftpboot/$tft";
		$tftpable = 1;
		$delable  = 1;
	}else{
		$file = "";
	}
}
?>

<script language="JavaScript"><!--
// apparently from PHPMyAdmin
function insertAtCursor(myField, myValue) {
	var curPos = myField.scrollTop;
	//IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)
		+ myValue
		+ myField.value.substring(endPos, myField.value.length);
	} else {
		myField.value += myValue;
	}
	myField.focus();
	myField.scrollTop = curPos;
}
//--></script>

<h1>System Files</h1>

<table class="content" >
<tr class="bgmain">
<td class="ctr s">
	<a href="<?= $self ?>.php"><img src="img/32/<?= $selfi ?>.png" title="<?= $self ?>"></a>
</td>
<td>
<?php
if($isadmin){
?>
	<form method="post" action="<?= $self ?>.php">
		<img src="img/16/tool.png" title="System">
		<select name="file" onchange="this.form.submit();">
			<option value=""><?= $edilbl ?> ->
<?php
foreach ($sysfiles as $sf){
	$op = intval(strrpos($sf, "/")/2);
	echo "\t\t\t<option value=\"$sf\"".( ($file == $sf)?" selected":"")." style=\"color: $ocol[$op]\">$sf\n";
	}
?>
		</select>
	</form>
<?php
}

if( preg_match("/net/",$_SESSION['group']) ){
?>
	<form method="post" action="<?= $self ?>.php">
		<img src="img/16/log.png" title="<?= $dsclbl ?> <?= $loglbl ?>">
		<select name="log" onchange="this.form.submit();">
			<option value=""><?= $sholbl ?> ->
<?php
foreach (glob("$logpath/nedi*") as $f) {
	$l = substr($f, strlen($logpath)+1 );
	echo "\t\t\t<option value=\"$l\"".( ($file == $f)?" selected":"").">$l\n";
}
?>
		</select>
	</form>
<?php } ?>
</td>
<td>
	<form method="post" action="<?= $self ?>.php">
		<img src="img/16/conf.png" title="Device <?= $cfglbl ?>">
		<select name="cfg" onchange="this.form.submit();">
			<option value=""><?= $sholbl ?> ->
<?php
foreach (glob("$nedipath/conf/*") as $d){
	if (is_dir($d)){
		$cfgd = substr( $d, strlen($nedipath)+6 );
		echo "\t\t\t<option value=\"\" class=\"drd\">-- ".urldecode($cfgd)."\n";
		foreach (glob("$d/*.cfg") as $f) {
 			$l = substr($f,strlen($d)+1);
			echo "\t\t\t<option value=\"$cfgd/$l\" ".( ($file == $f)?" selected":"").">$l\n";
		}
	}
}
?>
		</select>
	</form>
	<form method="post" action="<?= $self ?>.php">
		<img src="img/16/kons.png" title="<?= $cmdlbl ?>/<?= $loglbl ?>">
		<select name="cli" onchange="this.form.submit();">
			<option value=""><?= $sholbl ?> ->
<?php
$ldir = array();
foreach (glob("$nedipath/cli/*") as $d){
	if (is_dir($d)){
		$ldir[] = $d;
	}else{
		$l = substr($d,strlen("$nedipath/cli/") );
		echo "\t\t\t<option value=\"$l\" ".( ($file == $f)?" selected":"").">$l\n";
	}
}
foreach ($ldir as $d){
	$clid = substr( $d, strlen($nedipath)+5 );
	echo "\t\t\t<option value=\"\" class=\"drd\">-- ".urldecode($clid)."\n";
	foreach (glob("$d/*") as $f) {
		$l = substr($f,strlen($d)+1);
		echo "\t\t\t<option value=\"$clid/$l\" ".( ($file == $f)?" selected":"").">$l\n";
	}
}

?>
		</select>
	</form>
</td>
<td>
	<form method="post" action="<?= $self ?>.php" name="file" enctype="multipart/form-data">
		<img src="img/16/bup.png" title="<?= $cmdlbl ?>">
		<select name="mde" onchange="if(document.file.mde.selectedIndex == 1){alert('<?= (($verb1)?"$rpllbl System $cfglbl":"System $cfglbl $rpllbl") ?>');}">
			<option value="b" <?= ($mde == "b")?" selected":""?>><?= (($verb1)?"$updlbl NeDi":"NeDi $updlbl") ?> (<?= (($verb1)?"$buplbl $cfglbl":"$cfglbl $buplbl") ?>)
			<option value="u" <?= ($mde == "u")?" selected":""?>><?= (($verb1)?"$updlbl NeDi":"NeDi $updlbl") ?> (<?= (($verb1)?"$rpllbl $cfglbl":"$cfglbl $rpllbl") ?>)
			<option value="g" <?= ($mde == "g")?" selected":""?>><?= (($verb1)?"$updlbl $imglbl":"$imglbl $updlbl") ?>
			<option value="i" <?= ($mde == "i")?" selected":""?>><?= (($verb1)?"$implbl DB":"DB $implbl") ?>
			<option value="r" <?= ($mde == "r")?" selected":""?>><?= $dellbl ?> <?= $stco['160'] ?> RRDs
			<option value="l" <?= ($mde == "l")?" selected":""?>><?= $upllbl ?>-log
			<option value="t" <?= ($mde == "t")?" selected":""?>><?= $upllbl ?>-tftp
			<option value="o" <?= ($mde == "o")?" selected":""?>><?= $upllbl ?>-<?= $sub ?>
		</select>
		<br>
		<img src="img/16/clip.png" title="<?= $fillbl ?>">
		<input name="tgz" class="s" type="file" accept="archive/tar">
		<input type="hidden" name="sub" value="<?= $sub ?>">
</td>
<td class="s">
	<input type="submit" class="button" name="up" value="<?= $cmdlbl ?>">
	</form>
</td>
</tr>
</table>

<?php

if($isadmin){
	if( $del and preg_match("/^(".str_replace('/', '\\/', $tftpboot)."|log|map|topo)/",$del) ){	# Only allow $tftpboot, log/, map/ and topo/
		if(is_dir($del)){
			array_map('unlink', glob("$del/*"));
			if( rmdir($del) ){
				echo "<h5>$dellbl $del OK</h5>";
			}else{
				echo "<h4>$errlbl $dellbl $del</h4>";
			}
		}elseif( unlink ($del) ){
			echo "<h5>$dellbl $del OK</h5>";
		}else{
			echo "<h4>$errlbl $dellbl $del</h4>";
		}
	}elseif($wrt and $file){
		$wbytes = file_put_contents($file, preg_replace("/\r/", "", $txt ) );
		if( $wbytes === FALSE ){
			echo "<h4>$errlbl: $wrtlbl $file!</h4>\n";
		}else{
			echo "<h5>$wrtlbl $file ($wbytes bytes) OK</h5>\n";
		}
		if($file == "$nedipath/inc/crontab"){
			system("crontab $file", $fail);
			if($fail){
				echo "<h4>Crontab $updlbl $errlbl</h4>\n";
			}else{
				echo "<h5>Crontab $updlbl OK</h5>\n";
			}
		}elseif($tftpable and $all){
			if(chmod($file,0666) ){
				echo "<h5>$cmdlbl $alllbl $wrtlbl $acslbl OK</h5>";
			}else{
				echo "<h4>$errlbl $wrtlbl $alllbl $acslbl</h4>\n";
			}
		}
	}
}

if($file){
?>

<h2><?= basename($file) ?></h2>

<table class="content">
	<tr class="bgsub">
		<td class="ctr">
<?php
	$contents = "";
	if (file_exists($file)) {
		$contents = file_get_contents ("$file");
	};

	if($isadmin){
		if($editable or $tftpable){
?>
		<form method="post" action="<?= $self ?>.php" name="edit">
			<?= $addlbl ?> &nbsp;
			<input type="button" value="Tab" OnClick="insertAtCursor(document.edit.txt, '	');";>
			<input type="button" value="#" OnClick="insertAtCursor(document.edit.txt, '#');";>
			<input type="button" value=";" OnClick="insertAtCursor(document.edit.txt, ';');";>
			<input type="button" value="|" OnClick="insertAtCursor(document.edit.txt, '|');";>
			<input type="button" value="/" OnClick="insertAtCursor(document.edit.txt, '/');";>
			<input type="button" value="$" OnClick="insertAtCursor(document.edit.txt, '$');";> &nbsp; &nbsp;
<?php
			if($tftpable){
?>
			<input type="text" class="m" name="tft" value="<?= basename($file) ?>" onfocus="select();" >
			<input type="checkbox" name="all" <?= $all ?> title="<?= $alllbl ?> <?= $wrtlbl ?> <?= $acslbl ?>">
			<input type="submit" class="button" name="wrt" value="<?= $wrtlbl ?> TFTP">
<?php
			}else{
?>
			<input type="hidden" name="file" value="<?= $file ?>">
			<input type="submit" class="button" name="wrt" value="<?= $wrtlbl ?>">
<?php
			}
		}
		if($delable and $contents){
?>
			<div style="float:right"><a href="?del=<?= urlencode($file) ?>"><img src="img/16/bcnl.png" onclick="return confirm('<?= $dellbl ?>, <?= $cfmmsg ?>')" title="<?= $dellbl ?>!"></a></div>
<?php
		}
	}

?>
			<br>
<textarea rows="30" name="txt" cols="120" class="code">
<?= $contents ?>
</textarea>
		</form>
		</td>
	</tr>
</table>

<?php
}elseif($isadmin and ($mde == "u" or $mde == "b") and $_SESSION['ver']  != "%VERSION%"){
?>

<h1>NeDi <?= $updlbl ?></h1>

<div class="textpad code pre txta tqrt">
<?php
	if(array_key_exists('tgz',$_FILES)){
		if( file_exists($_FILES['tgz']['tmp_name']) ){
			if($mde == "b"){
				echo "$realbl ".$_FILES['tgz']['name']."\n\n";
				if( file_exists("log/msg.txt") ){
					if (!copy("log/msg.txt", "/tmp/msg.txt")) {
						echo "<h4>$errlbl $wrtlbl /tmp/msg.txt</h4>\n";
						die;
					}else{
						echo "$buplbl msg.txt\n";
					}
				}
				if( file_exists("log/devtools.php") ){
					if (!copy("log/devtools.php", "/tmp/devtools.php")) {
						echo "<h4>$errlbl $wrtlbl /tmp/devtools.php</h4>\n";
						die;
					}else{
						echo "$buplbl devtools.php\n";
					}
				}
				if (!copy("$nedipath/inc/crontab", "/tmp/crontab")) {
					echo "<h4>$errlbl $wrtlbl /tmp/crontab</h4>\n";
					die;
				}else{
					echo "$buplbl crontab\n";
				}
				if (!copy("$nedipath/nedi.conf", "/tmp/nedi.conf")) {
					echo "<h4>$errlbl $wrtlbl /tmp/nedi.conf</h4>\n";
					die;
				}else{
					echo "$buplbl nedi.conf\n";
				}
				if (!copy("$nedipath/seedlist", "/tmp/seedlist")) {
					echo "<h4>$errlbl $wrtlbl /tmp/seedlist</h4>\n";
					die;
				}else{
					echo "$buplbl seedlist\n";
				}
				if (!copy("$nedipath/agentlist", "/tmp/agentlist")) {
					echo "<h4>$errlbl $wrtlbl /tmp/agentlist</h4>\n";
					die;
				}else{
					echo "$buplbl agentlist\n\n";
				}
			}

			system("tar zxf ".$_FILES['tgz']['tmp_name']." -C $nedipath", $stat);
			if($stat){
				echo "<h4>$errlbl $wrtlbl ".$_FILES['tgz']['name']."</h4>\n";
				die;
			}else{
				echo "$wrtlbl ".$_FILES['tgz']['name']."\n\n";
			}

			if($mde == "b"){
				if( file_exists("/tmp/msg.txt") ){
					if (!copy("/tmp/msg.txt", "log/msg.txt")) {
						echo "<h4>$errlbl $wrtlbl log/msg.txt</h4>\n";
					}else{
						echo "$wrtlbl log/msg.txt\n";
					}
				}
				if( file_exists("/tmp/devtools.php") ){
					if (!copy("/tmp/devtools.php", "log/devtools.php")) {
						echo "<h4>$errlbl $wrtlbl log/devtools.php</h4>\n";
					}else{
						echo "$wrtlbl log/devtools.php\n";
					}
				}
				if (!copy("/tmp/crontab", "$nedipath/inc/crontab")) {
					echo "<h4>$errlbl $wrtlbl $nedipath/inc/crontab</h4>\n";
				}else{
					echo "$wrtlbl $nedipath/inc/crontab\n";
				}
				if (!copy("/tmp/nedi.conf", "$nedipath/nedi.conf")) {
					echo "<h4>$errlbl $wrtlbl $nedipath/nedi.conf</h4>\n";
				}else{
					echo "$wrtlbl $nedipath/nedi.conf\n";
				}
				if (!copy("/tmp/seedlist", "$nedipath/seedlist")) {
					echo "<h4>$errlbl $wrtlbl $nedipath/seedlist</h4>\n";
				}else{
					echo "$wrtlbl $nedipath/seedlist\n";
				}
				if (!copy("/tmp/agentlist", "$nedipath/agentlist")) {
					echo "<h4>$errlbl $wrtlbl $nedipath/agentlist</h4>\n";
				}else{
					echo "$wrtlbl $nedipath/agentlist\n\n";
				}
			}
			echo "<h5>$updlbl OK</h5>";
			include_once("log/Readme.txt");
		}else{
			echo "<h4>$errlbl $realbl ".$_FILES['tgz']['name']."</h4>";
		}
	}
?>
</div><br>

<?php
}elseif($isadmin and $mde == "i"){
?>

<h2>NeDi <?= $implbl ?></h2>

<div class="textpad code pre txta tqrt">
<?php
	if(array_key_exists('tgz',$_FILES)){
		if(file_exists($_FILES['tgz']['tmp_name'])) {
			echo "<h5>$realbl ".$_FILES['tgz']['name']."</h5>\n";
			if( $backend == 'mysql'){
				system("zcat ".$_FILES['tgz']['tmp_name']." | mysql $dbname --user=$dbuser ".(($dbpass)?"--password=$dbpass":""), $stat);
			}elseif( $backend == 'Pg'){
				system("export PGPASSWORD=$dbpass;zcat ".$_FILES['tgz']['tmp_name']." | psql -h $dbhost -U $dbuser $dbname", $stat);
			}
			if($stat){
				echo "<h4>$errlbl $wrtlbl ".$_FILES['tgz']['name']."</h4>\n";
			}else{
				echo "<h5>$implbl OK</h5>\n";
			}
		}else{
			echo "<h4>$errlbl $realbl ".$_FILES['tgz']['name']."</h4>";
		}
	}
?>
</div><br>
<?php
}elseif($isadmin and $mde == "g"){
?>

<h2><?= $imglbl ?> <?= $updlbl ?></h2>

<div class="textpad code pre txta tqrt">
<?php
	if(array_key_exists('tgz',$_FILES)){
		if(file_exists($_FILES['tgz']['tmp_name'])) {
			echo "<h5>$realbl ".$_FILES['tgz']['name']."</h5>\n";
			system("tar zxvf ".$_FILES['tgz']['tmp_name']." -C img", $stat);
			if($stat){
				echo "<h4>$errlbl $wrtlbl ".$_FILES['tgz']['name']."</h4>\n";
			}else{
				echo "<h5>$chglbl OK</h5>";
			}
		}else{
			echo "<h4>$errlbl $realbl ".$_FILES['tgz']['name']."</h4>";
		}
	}
?>
</div><br>
<?php
}elseif($mde == "l" or $mde == "t" or $mde == "m" or $mde == "o"){
?>
<h2><?= $fillbl ?> <?= $upllbl ?></h2>

<div class="textpad code pre txta tqrt">
<?php

	$dir = $tftpboot;
	if($mde == "l"){
		$dir = "log";
	}elseif($mde == "o"){
		$dir = $sub;
	}
	if(array_key_exists('tgz',$_FILES)){
		if(file_exists($_FILES['tgz']['tmp_name'])) {
			echo "$realbl ".$_FILES['tgz']['name']."\n\n";
			if( rename($_FILES['tgz']['tmp_name'],"$dir/".$_FILES['tgz']['name']) ){
				echo "$wrtlbl \"$dir/".$_FILES['tgz']['name']."\"\n";
				if($mde == "t"){
					if(chmod("/$dir/".$_FILES['tgz']['name'],0644) ){
						echo "<h5>$cmdlbl $alllbl $realbl OK</h5>";
					}else{
						echo "<h4>$errlbl $realbl $alllbl</h4>\n";
					}
				}
				echo "<h5>$upllbl OK</h5>";
			}else{
				echo "<h4>$errlbl $wrtlbl \"$dir/".$_FILES['tgz']['name']."\"</h4>\n";
			}
		}else{
			echo "<h4>$errlbl $realbl \"".$_FILES['tgz']['tmp_name']."\"</h4>";
		}
	}else{
		echo "<h3>$sellbl $fillbl</h3>";
	}
?>
</div><br>

<?php
}elseif($isadmin and $mde == "r"){
?>
<h2>RRDs <?= $updlbl ?> > <?= $retire ?> <?= $tim['d'] ?></h2>

<div class="textpad code pre txta tqrt">
<?php
	$nrrd = 0;
	foreach (glob("$nedipath/rrd/*") as $dv){
		if (is_dir($dv) && $dv != "." && $dv != "..") {
			foreach (glob("$dv/*.rrd") as $rrd){
				$mtime = filemtime($rrd);
				if( $mtime < (time() - $retire * 86400) ){
					$dstat = (unlink($rrd))?"OK":"$errlbl";
					echo date($_SESSION['timf'],$mtime)." $rrd: $dellbl $dstat\n";
				}
			}
		}
	}

?>
</div><br>
<?php
}
?>

<h2><?= $fillbl ?> <?= $lstlbl ?></h2>

<table class="full fixed">
<tr>
<td class="helper">

<?php FileList('log',"web"); ?>
<br>
<p>
<?php FileList('map',"web"); ?>
<br>
<p>
<?php FileList($tftpboot,"tftp"); ?>

</td>
<td class="helper">

<?php FileList('topo',"web"); ?>

</td>
</tr>
</table>
<?php

function FileList($dir,$opt=""){

	global $row,$modgroup,$self,$stco,$namlbl,$fillbl,$totlbl,$sizlbl,$updlbl,$cmdlbl;
?>
<h3>
	<?= ($opt == "tftp")?"<a href=\"?tft=my.txt\"><img src=\"img/16/add.png\" title=\"$stco[10]\"></a>":''; ?>
	<?= $dir ?>
</h3>

<table class="content">
	<tr class="bgmain">
		<th colspan="2">
			<?= $namlbl ?>

		</th>
		<th>
			<?= $sizlbl ?>

		</th>
		<th>
			<?= $updlbl ?>

		</th>
		<th>
			<?= $cmdlbl ?>

		</th>
	</tr>
<?php
	$row  = 0;
	$tsiz = DirList($dir,$opt,0);
?>
	<tr class="bgmain">
		<td colspan="5">
			<?= $row ?> <?= $fillbl ?>, <?= $totlbl ?> <?= DecFix($tsiz) ?>
		</td>
	</tr>
</table>

<?php
}

function DirList($dir,$opt,$lvl){

	global $row,$sub,$upllbl,$levlbl,$dellbl,$edilbl,$cfmmsg,$isadmin;

	$tsiz = 0;
	$ed   = '';
	foreach (glob("$dir/*") as $f){
		if ($row % 2){$bg = "txta"; $bi = "imga";}else{$bg = "txtb"; $bi = "imgb";}
		$row++;
		$plen = strlen($dir);
		$t = substr($f,$plen+1);
		TblRow($bg);
		if(is_dir($f) or $lvl){
			echo "\t\t<td class=\"$bi s nw\">\n";
		}else{
			echo "\t\t<td class=\"$bi xs ctr\">\n";
		}

		$i=0;
		while ($i < $lvl) {
			echo "\t\t\t<img src=\"img/sub.png\">\n";
			$i++;
		}
		if(is_dir($f)){
			if($opt == "web"){
				echo "\t\t\t<a href=\"?sub=$f&mde=o\"><img src=\"img/16/".(($f == $sub)?'foye':'fogy').".png\" title=\"$upllbl $levlbl $lvl\"></a>\n";
			}else{
				echo "\t\t\t<img src=\"img/16/fogy.png\" title=\"Folder $levlbl $lvl\">\n";
			}
			echo "\t\t</td>\n\t\t<td colspan=\"4\">\n\t\t\t$t\n";
			if($isadmin){
				echo "\t\t\t<span style=\"float:right\"><a href=\"?del=".urlencode($f)."\"> <img src=\"img/16/bcnl.png\" onclick=\"return confirm('$dellbl, $cfmmsg')\" title=\"$dellbl!\"></a></span>\n";
			}
			echo "\t\t</td>\t</tr>\n";
			$tsiz += DirList("$dir/$t",$opt,$lvl+1);
		}else{
			list($ico,$ed) = FileImg($f);
			echo "\t\t\t$ico\n\t\t</td>\n\t\t<td>\n";
			if($opt == "web"){
				echo "\t\t\t<a href=\"$f\" target=\"window\">$t</a>\n\t\t</td>\n";
			}else{
				echo "\t\t\t$t\n\t\t</td>\n";
			}
			$siz = filesize($f);
			$tsiz += $siz;
			echo "\t\t<td align=\"right\">\t\t\t".DecFix($siz)."\n\t\t</td>\n\t\t<td align=\"right\">\n\t\t\t".date ($_SESSION['timf'],filemtime($f))."\n\t\t<td align=\"right\">\n";
			if($isadmin){
				if($opt == "tftp" and $ed){
					echo "\t\t\t<a href=\"?tft=".urlencode($t)."\"><img src=\"img/16/note.png\" title=\"$edilbl\"></a>\n";
				}
				echo "\t\t\t<a href=\"?del=".urlencode($f)."\"><img src=\"img/16/bcnl.png\" onclick=\"return confirm('$dellbl, $cfmmsg')\" title=\"$dellbl!\"></a>\n";
			}
			echo "\t\t</td>\n\t</tr>\n";
		}
	}
	return $tsiz;
}

include_once ("inc/footer.php");
?>
