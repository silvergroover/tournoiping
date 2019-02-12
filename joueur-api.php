<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script>
<link rel="stylesheet" href="include/style.css" type="text/css" />
 
<?php

include_once ('include/Smartping.inc');
include_once ('include/SmartpingDAO.inc');

// create object session
$api = new Service('SW021', 'Hd125pYK04');

if (empty($_SESSION['serial'])) {
	$_SESSION['serial'] = Service::generateSerial();
}
$api->setSerial($_SESSION['serial']);

//initialize connection
$api->initialization();

$orga = $api->getEpreuves('D','E');
echo "<pre>";
print_r($orga);
echo "</pre>";

//GetParties
?>
<div class="container">

<h3>infos joueur</h3>
<p align="center">
<table class="sortable">
  <tr>
	<th class="text-center"><u>Victoire ou d�faite</u></th>
    <th class="text-center"><u>Date</u></th>
    <th class="text-center"><u>Nom Prenom </u></th>
    <th data-type="numeric" class="text-center"><u>Classement</u></th>
    <th data-type="numeric" class="text-center"><u>Epreuves</u></th>  </tr>

<?php 
//$JoueurParties = $api->getJoueurPartiesSpid('3528756');
foreach ($JoueurParties as $cletab=>$valtab) {
?>
  <tr>
    <td><?php echo $valtab['nom']?></td>
    <td><?php echo $valtab['classement']?></td>
    <td><?php echo $valtab['date']?></td>
    <td><?php echo $valtab['epreuve']?></td>
    <td><?php echo $valtab['victoire']?></td>
    <td><?php echo $valtab['forfait']?></td>
  </tr>
<?php 	
/*echo "<pre>";
print_r($valtab);
echo "</pre>";
*/?>

<?php } ?>
</table>

<table class="sortable">
  <tr>
    <th  class="text-center"><u>Saison</u></th>
    <th data-type="numeric" class="text-center"><u>Phase</u></th>  
	<th data-type="numeric" class="text-center"><u>Echelon</u></th>
    <th class="text-center"><u>Place</u></th>
    <th data-type="numeric" class="text-center"><u>Point </u></th>
    </tr>

?>
<?php 
//$JoueurParties = $api->getJoueurHistorique('3528756');
foreach ($JoueurParties as $cletab=>$valtab) {
	?>
  <tr>
    <td><?php echo $valtab['saison']?></td>
    <td><?php echo $valtab['phase']?></td>
    <td> - <?php //echo $valtab['echelon']?></td>
    <td> - <?php //echo $valtab['place']?></td>
    <td><?php echo $valtab['point']?></td>
  </tr>
<?php } ?>
</table>

<?php 

echo "<pre>";
print_r($JoueurParties);
echo "</pre>";

//$JoueurParties = $api->getJoueurParties('3528756');
echo "<pre>";
print_r($JoueurParties);
echo "</pre>";
?>


<script src="sorttable.js"></script>
        