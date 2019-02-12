<HEAD>
<meta charset="UTF-8">
<link rel="stylesheet" href="include/style.css" type="text/css" />
<script	src="include/jquery.min.js"></script>

</HEAD>

<body>

<?php
include_once ('include/SmartpingDAO.php');

$dao = new SmartpingDAO ();

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

$listesaison = $dao->getListeSaison();
$annee = date("Y");
//$phase = $dao->getPhase($annee);

//echo "phase : ".$phase;

?>

		<form method="post" action="equipes.php">
		<p><center>
		<fieldset>
		<legend>Saison : <?php echo $saison ?> - Phase <?php echo $phase ?> </legend> <!-- Titre du fieldset --> 

		<select name="phase">
			<option value="<?php echo $phase?>"> phase <?php echo $phase?> </option>
			<option value="1"> phase 1 </option>
			<option value="2"> phase 2 </option>
		</select>

		<select name="saison">
			<option value=""> ----- Saison ----- </option>
			<?php  
			$i=0;
			foreach ( $listesaison as $value ) {
			$i++;	
			echo "<pre>";
			print_r($value);
			echo "</pre>";
			?>
			<?php echo $value[saison] ?>
			<option value="<?php echo $value['saison'] ?>"> <?php echo $value['saison'] ?> </option>
			<?php  
			}
			?>
		</select>

		<!-- un bouton pour valider -->
<!--		<input type="hidden" name="id" value="<?php echo $id?>" />
-->		<input type="submit" value="valider" name="bouton">
		</form>
		</center>
		</p>
		</form>
<?php

// GetequipesClub

?>
<div class="thumbnail" style="background-color: #FAFAFA">
	<div class="caption"><h3>Equipes senior</h3>	</div>
</div>
<p align="center">
<table class="sortable oddeven">
	<tr><th></th><th></th></tr>
	<?php
	$equipe = $dao->getEquipe ($saison,"M",$phase);
	foreach ( $equipe as $eqdetail ) {
		$lien = str_replace (  "&", ";" , $eqdetail['liendivision'] );
		$equipe_nom = preg_split("/( - )+/",$eqdetail['libequipe'] );
/*		echo "equipe_nom <pre>";
		print_r($equipe_nom);
		echo "</pre><br/>";
*/

	?>
	<tr data-href="equipe.php?idpoule=<?php echo $eqdetail['idpoule']?>&saison=<?php echo $saison?>&equipe=<?php echo $equipe_nom[0] ?>&division=<?php echo $eqdetail['libdivision'] ?>&lien=<?php echo $lien ?>#haut">
			<td><?php echo $equipe_nom[0]?></td> 
			<td><?php echo $eqdetail['libdivision']?></td> 
		</tr>
	<?php
	}
	?>
</table>


	<div class="thumbnail" style="background-color: #FAFAFA">
		<div class="caption"><h3>Equipes junior</h3></div>
	</div>
	<p align="center">
	<table class="sortable oddeven">
		<tr><th></th><th></th></tr>
<?php
$equipe = $dao->getEquipe ($saison,"J",$phase);
foreach ( $equipe as $eqdetail ) {
	?>
  <tr data-href="equipe.php?idpoule=<?php echo $eqdetail['idpoule']?>&saison=<?php echo $saison?>&equipe=<?php echo $eqdetail['libequipe'] ?>&division=<?php echo $eqdetail['libdivision'] ?>#haut">
			<td><?php echo $eqdetail['libequipe']?></td> 
			<td><?php echo $eqdetail['libdivision']?></td> 
		</tr>
<?php
}
?>
</table>

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
