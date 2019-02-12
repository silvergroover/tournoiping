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
	$saison = $_POST['saison'];
//	$id = $_POST['id'];
} else {
	$saison = $dao->getSaison();
//	$id = $_GET ['id'];
}

$listesaison = $dao->getListeSaison();

?>

		<form method="post" action="joueurs.php">
		<p><center>
		<fieldset>
		<legend>Saison : <?php echo $saison ?></legend> <!-- Titre du fieldset --> 

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

// GetjoueursClub

?>
	<p align="center">
	
	
	<table class="sortable oddeven">
		<tr>
			<th>licence</th>
			<th> Nom / prenom</th>
			<th>pts mensuels</th>
			<th>Cat</th>
			<th>Pts Licence</th>
			<th>Prog mensuelle</th>
			<th>Prog annuelle</th>
			<th></th>
		</tr>

<?php
$joueur = $dao->getJoueurs ($saison);
foreach ( $joueur as $joueurdetail ) {
	?>
  <tr data-href="joueurSpid.php?id=<?php echo $joueurdetail['licence']?>#haut">
		<!--	<td><?php //echo $joueurdetail['licence']?></td> -->
			<td> <img alt="" src="include/photos/<?php echo $joueurdetail['licence']?>.jpg" style="float:right;height:70px;"</td>
			<td><?php echo $joueurdetail['nom']. " ".$joueurdetail['prenom'] ?></td>
			<td><?php echo $joueurdetail['point']?></td>
			<td><?php echo $joueurdetail['categ']?></td>
			<td><?php echo $joueurdetail['valinit']?></td>
			<td><?php 
				if ($joueurdetail['progmois']>0) {
					echo $joueurdetail['progmois']."  " ?><img alt="" src="include/arrow_up2.png" style="float:right;width:24px;height:24px;">
				<?php }
				elseif ($joueurdetail['progmois']<0) {
					echo $joueurdetail['progmois']."  "?><img alt="" src="include/arrow_down2.png" style="float:right;width:24px;height:24px;"> 
				<?php }
				else {
					echo $joueurdetail['progmois']."  "?><img alt="" src="include/arrow_equal.png" style="float:right;width:24px;height:24px;"> 
				<?php }?> 
			</td>
			<td><?php 
				if ($joueurdetail['progann']>0) {
					echo $joueurdetail['progann']."  " ?><img alt="" src="include/arrow_up2.png" style="float:right;width:24px;height:24px;">
				<?php }
				elseif ($joueurdetail['progann']<0) {
					echo $joueurdetail['progann']."  "?><img alt="" src="include/arrow_down2.png" style="float:right;width:24px;height:24px;"> 
				<?php }
				else {
					echo $joueurdetail['progann']."  "?> <img alt="" src="include/arrow_equal.png" style="float:right;width:24px;height:24px;"> 
				<?php }?> 
			</td>
			<td><a href="joueurSpid.php?id=<?php echo $joueurdetail['licence']?>#haut"><img src="include/stats.jpg" style="float:right;height:24px;" border="0" alt="" title="voir les stats"></a> 
		</tr>
<?php
}
?>
</table>
</body>
</html>
<script>
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
