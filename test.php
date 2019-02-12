<?php

include_once ('include/Smartping.inc');

// create object session
$api = new Service('SW021', 'Hd125pYK04');

if (empty($_SESSION['serial'])) {
	$_SESSION['serial'] = Service::generateSerial();
}
$api->setSerial($_SESSION['serial']);

//initialize connection
$api->initialization();

//Getjoueur
?>
<h3>infos joueur</h3>
<?php 
$joueur = $api->getJoueur('3532025');
foreach ($joueur as $cle=>$val) {
		echo $cle." = ".$val." <br /> ";
}

//GetjoueursClub
/*?>
<h3>infos joueurs Club</h3>
<table>
  <tr>
    <th>prénom / Nom</th>
    <th>pts mensuels</th>
    <th>Cat</th>
    <th>Pts Licence</th>
    <th>Prog mensuelle</th>
    <th>Prog annuelle</th>
  </tr>

<?php 
$joueur = $api->getJoueursByClub('07350021');
foreach ($joueur as $cle=>$val) {
	$joueurdetail = $api->getJoueur($val['licence']);
?>
  <tr>
    <td><?php echo $joueurdetail['prenom']. " ". $joueurdetail['nom']?></td>
    <td><?php echo $joueurdetail['point']?></td>
    <td><?php echo $joueurdetail['categ']?></td>
    <td><?php echo $joueurdetail['valinit']?></td>
    <td><?php echo $joueurdetail['progmois']?></td>
    <td><?php echo $joueurdetail['progann']?></td>
  </tr>
<?php 	
}
*/?>
</table>
<?php 
// Info parties du joueur

?>
<h3>infos Parties du joueur</h3>
<?php 
$JoueurParties = $api->getJoueurParties('3528756');
?>
<table>
  <tr>
    <th>date</th>
    <th>adversaire</th>
    <th>Classement</th>
    <th>Résultat</th>
    <th>points</th>
    <th>coeff</th>
  </tr>

<?php 
foreach ($JoueurParties as $cletab=>$valtab) {
?>
  <tr>
    <td><?php echo $valtab['date']?></td>
    <td><?php echo $valtab['advnompre']?></td>
    <td><?php echo $valtab['advclaof']?></td>
    <td><?php echo $valtab['vd']?></td>
    <td><?php echo $valtab['pointres']?></td>
    <td><?php echo $valtab['coefchamp']?></td>
  </tr>
<?php 	
echo "<pre>";
print_r($valtab);
echo "</pre>";
?>

<?php 	
}
?>
</table>
<?php 	

echo "<pre>";
print_r($JoueurParties);
echo "</pre>";
?>

<h3>infos equipes</h3>
<?php 
$equ = $api->getEquipesByClub('07350021','M');

foreach ($equ as $cequ=>$vequ) {
//	$idpoule = $val['idpoule'];

echo "<pre>";
print_r(array_keys($vequ));
echo "</pre>";

$champ = "";
foreach (array_keys($vequ) as $keyval){
	if ($champ == "") {
		$champ = $keyval;
	}else {
		;
	$champ = $keyval.",".$champ;
	echo $champ."<br />";
}
}

	foreach ($vequ as $cle=>$val) {
		echo $cle." = ".$val." <br /> ";
	}
	echo "<br />";
?>

<h3>info poule</h3>
<?php 

$poule = $api->getPouleClassement($vequ['iddiv'],$vequ['idpoule']);
echo "<pre>";
print_r($poule);
echo "</pre>";
}

//Club by departement
?>
<h3>infos historique joueur</h3>
<?php 
$Joueurhisto = $api->getJoueurHistorique('3532025');
echo "<pre>";
print_r($Joueurhisto);
echo "</pre>";

//Club by departement
?>
<h3>infos clubs du département</h3>
<?php 
/*$clubdep = $api->getClubsByDepartement('35');
	foreach ($clubdep as $cledep=>$valdep) {
		foreach ($valdep as $cle=>$val) {	
			echo $cle." = ".$val." <br /> ";
		}
	echo "<br />"; 
	}
*/

//Info club Aurore de vitré
?>
<h3>infos club</h3>
<?php 
/*$infoclub = $api->getClub('07350021');
echo "<pre>";
print_r($infoclub);
echo "</pre>";
*/?>
