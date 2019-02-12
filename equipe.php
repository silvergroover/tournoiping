<HEAD>
<meta charset="UTF-8">
<link rel="stylesheet" href="include/style.css" type="text/css" />
<script	src="include/jquery.min.js"></script>

</HEAD>

<body>

<?php
include_once ('include/SmartpingDAO.php');

$dao = new SmartpingDAO ();

if(!empty($_POST)) {
/*	echo "<pre>";
	print_r($_POST);
	echo "</pre><br/>";
*/	
	//phase
	if ( !empty ($_POST['phase'])) {
		$phase = $_POST['phase'];
	} 
	else {	
		$phase = $dao->getPhase(date("Y"));
	}
	//saison
	if ( !empty ($_POST['saison'])) {
		$saison = $_POST['saison'];
	} 
	else {	
		$saison = $dao->getSaison();
	}
	$equipe_nom = $_POST['equipe'];
	
	$poule = $dao->getIdPoule($saison,$equipe_nom,$phase,"M");


	$idpoule = $poule[0]['idpoule']; 
	$division = $poule[0]['libdivision']; 
	$lien_poule = $poule[0]['liendivision'];

} else {
	$saison = $dao->getSaison();
	$annee = date("Y");
	$phase = $dao->getPhase($annee);
}

if(!empty($_GET)) {
	$saison = $_GET['saison'];
	$idpoule = $_GET['idpoule'];
	$lien_poule = str_replace (  ";", "&", $_GET['lien'] );
	$lien_poule = str_replace (  " ", "+", $lien_poule );
	$equipe_nom = $_GET['equipe'];
	$division = $_GET['division'];
}

$listesaison = $dao->getListeSaison();
?>

		<form method="post" action="equipe.php">
		<p><center>
		<fieldset>
		<legend>Saison : <?php echo $saison ?> - Phase : <?php echo $phase?></legend> <!-- Titre du fieldset --> 
		<input type="hidden"  name="equipe"  value="<?php echo $equipe_nom ?>">
		<input type="hidden"  name="division"  value="<?php echo $division ?>">
		<input type="hidden"  name="lien_poule"  value="<?php echo $lien_poule ?>">
		<select name="phase">
			<option value="<?php echo $phase?>"> phase <?php echo $phase?> </option>
				<option value="1"> phase 1 </option>
				<option value="2"> phase 2 </option>
		</select>
		
		<select name="saison">
			<option value="<?php echo $saison ?>"> <?php echo $saison ?> </option>
			<?php  
			$i=0;
			foreach ( $listesaison as $value ) {
			$i++;	
/*			echo "<pre>";
			print_r($value);
			echo "</pre>";
*/			?>
			<?php echo $value[saison] ?>
			<option value="<?php echo $value['saison'] ?>"> <?php echo $value['saison'] ?> </option>
			<?php  
			}
			?>
		</select>

		<!-- un bouton pour valider -->
		<input type="submit" value="valider" name="bouton">
		</form>
		</center>
		</p>
		</form>
<?php

// Getequipe

?>

	<p align="center">
<?php
$tour_liste = $dao->getToursPoule($saison,$idpoule);
?>
	
<div class="thumbnail" style="background-color: #FAFAFA">
	<div class="caption"><h3><?php echo $equipe_nom ?> <?php echo $division ?>  </h3>	</div>
</div>
	<p align="center">
	
	<table class="sortable oddeven">
		<tr>
			<th>clt</th>
			<th>Equipe</th>
			<th>pts</th>
			<th>joués</th>
			<th>vic</th>
			<th>nul</th>
			<th>def</th>
			<th>Ff/P</th>
			<th>PG</th>
			<th>PP</th>
		</tr>

<?php
$equipe = $dao->getPoule($saison,$idpoule);
foreach ( $equipe as $eqdetail ) {
	?>
  <tr>
			<td><?php echo $eqdetail['clt']?></td> 
			<td><?php echo $eqdetail['equipe']?></td> 
			<td><?php echo $eqdetail['pts']?></td> 
			<td><?php echo $eqdetail['joue']?></td> 
			<td><?php echo $eqdetail['vic']?></td> 
			<td><?php echo $eqdetail['nul']?></td> 
			<td><?php echo $eqdetail['def']?></td> 
			<td><?php echo $eqdetail['pf']?></td> 
			<td><?php echo $eqdetail['pg']?></td> 
			<td><?php echo $eqdetail['pp']?></td> 
		</tr>
<?php
}
?>
</table>

<?php
foreach ( $tour_liste as $tour ) {
?>

	<p align="center">
		
	<table class="sortable oddeven">
			<tr>
			<th><?php echo $tour['libelle'] ?></th>
			<th></th>
			<th></th>
			<th></th>
			</tr>

	<?php
	$equipe = $dao->getEquRenc($idpoule,$saison,$tour['libelle']);
	foreach ( $equipe as $eqdetail ) {
	//	echo "lien : <br>".$eqdetail['lien']."<br>";
		$lien = str_replace (  "&", "|" , $eqdetail['lien'] );
	//	echo $lien."<br>";
		?>
	  <tr data-href="rencontre.php?lien=<?php echo $lien."&eqa=".$eqdetail['equa']."&eqb=".$eqdetail['equb']?>#haut">
				<td><?php echo $eqdetail['equa']?></td> 
				<td><?php echo $eqdetail['equb']?></td> 
				<td><?php echo $eqdetail['scorea']?></td> 
				<td><?php echo $eqdetail['scoreb']?></td> 
			</tr>
	<?php
	}
	?>
	</table>

<?php
}
?>

</body>
</html>
<script>
$(document).ready(function(){
    $('table tr').click(function(){
    	<!--        window.open($(this).data('href'),'blank');
    	 -->
        window.location = $(this).data('href');
        return false;
    });
});

<!--$(document).ready(function(){
<!--    $('table tr').click(function(){
    	<!--        window.open($(this).data('href'),'blank');
    	 -->
<!--        window.location = $(this).data('href');
<!--        return false;
<!--    });
<!--});
</script>
<script src="sorttable.js"></script>
