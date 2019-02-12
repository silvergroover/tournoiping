<?php
echo "include dao";
include_once ('include/SmartpingDAO.php');

// create object session
echo "connect to smartping";

$api = new Service('SW021', 'Hd125pYK04');
$dao = new SmartpingDAO();

if (empty($_SESSION['serial'])) {
	$_SESSION['serial'] = Service::generateSerial();
}
$api->setSerial($_SESSION['serial']);

//initialize connection
$api->initialization();
echo "initialization complete";

// Get list of licences in db
$licenceList = $dao->getListLicence();

// put licence in simple array
foreach ($licenceList as $licence) {
	// 	echo $licence['licence']."<br>";
 	$licenceArray[] = $licence['licence'];
 }

 // prepare donnees pour progression mensuelle
 $mois = date('n', strtotime('-3 month'));
 $annee = date("Y");
 $cleProgMens = $dao->getCleProgMens($mois,$annee);
/*	echo "<pre>";
	print_r($cleProgMens);
	echo "</pre>";
*/	
 $licenceProgMens[] = '';
 if (!empty($cleProgMens)) {
 foreach ($cleProgMens as $licence) {
 	echo $licence['licence']."<br>";
 	$licenceProgMens[] = $licence['licence'];
 }
 }
 // Get list of players on fftt db
$joueur = $api->getJoueursByClub('07350021');
	echo "<pre>";
 print_r($joueur);
 echo "</pre>";


// pour chaque joueur, on regarde s'il est d�j� en base, sinon on l'insère
foreach ($joueur as $val) {
	set_time_limit(10);
	$joueurdetail = $api->getJoueur($val['licence']);
	if ( in_array($joueurdetail['licence'], $licenceArray,true)) {
		echo $licence['licence']." is in db<br>";
	}
	else {
		$dao->insererJoueur($joueurdetail['prenom'],$joueurdetail['nom'],$joueurdetail['point'],$joueurdetail['categ'],$joueurdetail['valinit'],$joueurdetail['progmois'],$joueurdetail['progann'],$joueurdetail['licence']);
		echo $licence['licence']." is inserted<br>";
	}
	
	//$mois = date("m");
	if ( in_array($joueurdetail['licence'], $licenceProgMens,true)) {
	//	$dao->updatePointsMens($joueurdetail['licence'],$annee,$mois,$joueurdetail['point'],$joueurdetail['progmois'],$joueurdetail['progann']);
		$dao->updatePointsMens($joueurdetail['licence'],$annee,$mois,$joueurdetail['valinit'],$joueurdetail['progmois'],$joueurdetail['progann']);
	}
	else {
		$dao->insererPointsMens($joueurdetail['licence'],$annee,(int)$mois,$joueurdetail['valinit'],$joueurdetail['categ'],$joueurdetail['valinit'],$joueurdetail['progmois'],$joueurdetail['progann']);
	}

	
	// on récupere la date de la derniere partie insérée dans la table fftt_parties_spid
	$DateDernierePartie = $dao->getDateDernierePartie('fftt_parties_spid',$val['licence']);
//			echo "date db : ".$DateDernierePartie."<br>";
	$DateDernierePartie = strtotime( $DateDernierePartie );
	
		// on récupère les parties sur l'api fftt Spid
	$JoueurParties = $api->getJoueurPartiesSpid($val['licence']);
/*	echo "<pre>";
	print_r($JoueurParties);
	echo "</pre>";
*/	
	
	//pour chaque partie, on insere en base si la date de la partie est postérieure à la derniere en base
	foreach ($JoueurParties as $partie){
		$date = date_create_from_format('d/m/y', $partie['date']);
		$DatePartie = strtotime(date_format($date, 'Y-m-d'));
			echo "date api : ".$DatePartie.", date db : ".$DateDernierePartie."<br>";
		if ($DatePartie > $DateDernierePartie ) {
			$result =  $dao->insererPartieSpid($val['licence'],$partie['nom'],$partie['date'],$partie['classement'],$partie['epreuve'],$partie['victoire'],$partie['forfait']);
			echo $result."<br>";
		}
		else {
			echo "date api : ".$partie['date']."<br>";
		}
	}

	// on récupere la date de la derniere partie insérée dans la table fftt_parties
	$DateDernierePartie = $dao->getDateDernierePartie('fftt_parties',$val['licence']);
//			echo "date db : ".$DateDernierePartie."<br>";
	$DateDernierePartie = strtotime( $DateDernierePartie );
	
		// on récupère les parties sur l'api fftt
	$JoueurParties = $api->getJoueurParties($val['licence']);
/*	echo "<pre>";
	print_r($JoueurParties);
	echo "</pre>";
*/	
	
	//pour chaque partie, on insere en base si la date de la partie est post�rieure � la derniere en base
	foreach ($JoueurParties as $partie){
		if (is_array($partie['numjourn'])){
			$numjourn = 0;
		} 
		else { $numjourn = $partie['numjourn'];
		}
		$date = date_create_from_format('d/m/y', $partie['date']);
		$DatePartie = strtotime(date_format($date, 'Y-m-d'));
			echo "date api : ".$DatePartie.", date db : ".$DateDernierePartie."<br>";
		if ($DatePartie > $DateDernierePartie ) {
			$result =  $dao->insererPartie($val['licence'],$partie['advlic'],$partie['vd'],$numjourn,
											$partie['codechamp'],$partie['date'],$partie['advnompre'],
											$partie['pointres'],$partie['coefchamp'],$partie['advclaof']);
			echo $result."<br>";
		}
		else {
			echo "date api : ".$partie['date']."<br>";
		}
	}

	// on récupère l'historique des saisons sur l'api fftt
	$historique = $api->getJoueurHistorique($val['licence']);
	foreach ($historique as $histo){
			$result =  $dao->insererhisto($val['licence'],$histo['saison'],$histo['phase'],$histo['point']);
			echo $result."<br>";
	}
	
}	
//	echo "detail:".$joueurdetail['prenom'].",".$joueurdetail['nom'].",".$joueurdetail['point'].",".$joueurdetail['categ'].",".$joueurdetail['valinit'].",".$joueurdetail['progmois'].",".$joueurdetail['progann'].",".$joueurdetail['licence']."\n";
//	$dao->insererJoueur($joueurdetail['prenom'],$joueurdetail['nom'],$joueurdetail['point'],$joueurdetail['categ'],$joueurdetail['valinit'],$joueurdetail['progmois'],$joueurdetail['progann'],$joueurdetail['licence']);

	 
	 
?>