<HEAD>
<meta charset="UTF-8">
<link href="include/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="include/style.css" type="text/css" />

</HEAD>

<body>
<?php

include_once ('include/tournoiDAO.php');
$dao = new SmartpingDAO ();
  
$annee = date("Y");
$tableaux = $dao->getTableauxTournoi($annee);
$places = $dao->getNbPlaces($annee);
?>
<div class="container">

	<!-- AFFICHE DU JOUEUR -->
<center>
	<div class="thumbnail" style="background-color: #FAFAFA">
		<div class="caption">
			<p align="center">
				<table>
					<tr>
					<th> tableau</th>
				
					<?php
					foreach ( $places as $key => $tab ) {
					?>
						<th><?php echo $key ?> </th>
					<?php
					}?>
					</tr>
					<tr>
						<td> places disponibles</td>
					<?php
					foreach ( $places as $key => $tab ) {
					?>
						<th><?php echo $tab['nb'] ?> </th>
					<?php
					}
					?>
					</tr>
				</table>
			</p>
<!--				<p style="text-align: center;"><strong><span style="color: #ff0000;"> Notez que le tableau K est annulé </span></strong></p> -->
		</div>
	</div>
</center>

<form method="post" action="tournoi.php">
	<p><center>
		<fieldset>
		<legend>Consulter les tableaux</legend> <!-- Titre du fieldset --> 

		<select name="tableau">
			<option value=""> ----- Choisir ----- </option>
			<?php  
			$i=0;
			foreach ( $tableaux as $key => $tab ) {
			$i++;	
			?>
			<option value="<?php echo $key ?>"> <?php echo $key ?> </option>
			<?php  
			}
			?>
		</select>

		<!-- un bouton pour valider -->
		<input type="submit" value="valider" name="OK">
	</center></p>
</form>
<?php  
	if(!empty($_POST))
	{
		$joueur = $dao->getJoueursTableau($_POST['tableau'],date("Y"));
/*		echo "<pre>";
			print_r($joueur);
		echo "</pre>";
*/	?>
	<h3> Tableau <?php echo $_POST['tableau']?></h3>
	<p align="center">
	<table class="sortable oddeven">
		<tr>
			<th> Nom / prenom</th>
			<th>Club</th>
			<th>Categorie</th>
			<th>Classement Nat</th>
			<th>Pts Licence</th>
			<th>pts mensuels</th>
			<th>Prog mensuelle</th>
			<th>Prog annuelle</th>
			<th></th>
		</tr>

<?php
foreach ( $joueur as $joueurdetail ) {
	?>
			<td><?php echo $joueurdetail['nom']. " ".$joueurdetail['prenom'] ?></td>
			<td><?php echo $joueurdetail['club']?></td>
			<td><?php echo $joueurdetail['categ']?></td>
			<td><?php echo $joueurdetail['clnat']?></td>
			<td><?php echo $joueurdetail['valinit']?></td>
			<td><?php echo $joueurdetail['point']?></td>
			<td><?php 
				if ($joueurdetail['progmois']>0) {
					echo $joueurdetail['progmois']."  " ?><img alt="" src="include/arrow_up2.png" style="float:right;width:24px;height:24px;">
				<?php }
				elseif ($joueurdetail['progmois']<0) {
					echo $joueurdetail['progmois']."  "?><img alt="" src="include/arrow_down2.png" style="float:right;width:24px;height:24px;"> 
				<?php }?> 
			</td>
			<td><?php 
				if ($joueurdetail['progann']>0) {
					echo $joueurdetail['progann']."  " ?><img alt="" src="include/arrow_up2.png" style="float:right;width:24px;height:24px;">
				<?php }
				elseif ($joueurdetail['progann']<0) {
					echo $joueurdetail['progann']."  "?><img alt="" src="include/arrow_down2.png" style="float:right;width:24px;height:24px;"> 
				<?php }?> 
			</td>
		</tr>
<?php
	}
}
?>
</table>
</body>
</html>
