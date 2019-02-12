<HEAD>
<meta charset="UTF-8">
<link rel="stylesheet" href="include/style.css" type="text/css" />
<script	src="include/jquery.min.js"></script>

</HEAD>

<body>

<?php
include_once ('include/SmartpingDAO.php');

$dao = new SmartpingDAO ();

if(!empty($_GET)) {
	$lien = str_replace (  "|", "&", $_GET['lien'] );
	$lien = str_replace (  " ", "+", $lien );
	$eqa = $_GET['eqa'];
	$eqb = $_GET['eqb'];
	
  //  echo $lien;
}

if(empty($saison)) { $saison = $dao->getSaison();}

?>

<?php

// Getequipe


?>
<div class="thumbnail" style="background-color: #FAFAFA">
	<div class="caption"><h3><?php echo $eqa ?> - <?php echo $eqb ?>  </h3>	</div>
</div>

	<div class="thumbnail" style="background-color: #FAFAFA">
		<div class="caption">
			<h3> Joueurs</h3>
	
		</div>
	</div>

	<p align="center">
	<table class="sortable oddeven">
		<tr>
			<th><?php echo $eqa?></th>
			<th></th>
			<th></th>
			<th><?php echo $eqb?></th>
		</tr>

<?php

$joueurs = $dao->getJoueursRenc($lien);
foreach ( $joueurs as $joueur ) {
	?>
  <tr >
		<td><?php echo $joueur['ja']?></td> 
		<td><?php echo $joueur['ca']?></td> 
		<td><?php echo $joueur['cb']?></td> 
		<td><?php echo $joueur['jb']?></td> 
	</tr>
<?php
}
?>
</table>

</p>
	<div class="thumbnail" style="background-color: #FAFAFA">
		<div class="caption">
			<h3>Détail des parties</h3>
	
		</div>
	</div>
	<p align="center">
	
	<table class="sortable oddeven">
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>

<?php
$rencontre = $dao->getRencontre($lien);
foreach ( $rencontre as $renc_detail ) {
	$scorea = str_replace (  "0", "-", $renc_detail['scorea'] );
	$scoreb = str_replace (  "0", "-", $renc_detail['scoreb'] );
	?>
  <tr >
		<?php if ($scorea == 1) {echo "<td class=\"b\">". $renc_detail['ja'];}
		else { echo "<td>". $renc_detail['ja'];	}?></td> 
		<td><?php echo $scorea?></td> 
		<td><?php echo $scoreb?></td> 
		<?php if ($scoreb == 1) {echo "<td class=\"b\">". $renc_detail['jb'];}
		else { echo "<td>". $renc_detail['jb'];	}?></td> 
	</tr>
<?php
}
?>
</table>
</body>
</html>
<script src="sorttable.js"></script>
