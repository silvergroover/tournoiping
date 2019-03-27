<?php

include_once ('Smartping.php');
include_once ('JournalException.php');
include_once ('local.php');

class SmartpingDAO {

	//Méthode de gestion de connexion
	private function connexion () {
include_once ('local.php');
		$url = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
		$usr = USER;
		$pwd = PWD;
		$db = new PDO($url, $usr, $pwd);
		//par défaut PDO ne renvoi pas d'erreur. (ERRMODE_SILENT). la ligne suivante permet de pouvoir gérer les erreurs
		$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	
		return $db;
	}


	public function getTableauxTournoi($annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT * FROM fftt_tournoi_tableaux where annee = $annee";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function getJoueursTableau($tab,$annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT * FROM `fftt_tournoi_joueurs` inner join `fftt_tournoi_joueurs_tab` 
			on `fftt_tournoi_joueurs`.licence = `fftt_tournoi_joueurs_tab`.`licence` 
			WHERE `fftt_tournoi_joueurs_tab`.tableau = '$tab' AND `fftt_tournoi_joueurs_tab`.annee = '$annee' ";
			$st = $db->prepare($sql);
			//echo $sql;
			$st->execute();
			$result = $st->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function getJoueursTableauDouble($tab,$annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT * FROM `fftt_tournoi_joueurs` inner join `fftt_tournoi_joueurs_tab_double` 
			on `fftt_tournoi_joueurs`.licence = `fftt_tournoi_joueurs_tab_double`.`licence` 
			WHERE `fftt_tournoi_joueurs_tab_double`.tableau = '$tab' AND `fftt_tournoi_joueurs_tab_double`.annee = '$annee' ";
			$st = $db->prepare($sql);
			//echo $sql;
			$st->execute();
			$result = $st->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function insererJoueurTournoi ($prenom,$nom,$point,$categ,$valinit,$progmois,$progann,$licence,$email,$tel,$club,$clnat,$annee,$date) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
				
			$sql="INSERT INTO fftt_tournoi_joueurs(prenom,nom,point,categ,valinit,progmois,progann,licence,email,tel,club,clnat,annee,date_inscription) 
			VALUES('$prenom','$nom','$point','$categ','$valinit','$progmois','$progann','$licence','$email','$tel','$club','$clnat','$annee','$date')
			ON DUPLICATE KEY UPDATE point = '$point',categ = '$categ',valinit = '$valinit',progmois = '$progmois',
			progann = '$progann',email = '$email',tel = '$tel',club = '$club',clnat = '$clnat',annee = '$annee',date_inscription = '$date' ";

	//		echo $sql;
			$st = $db->prepare($sql);
			$st->execute();
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($db->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
			$db=null;
		}
	}

	public function getNbJoueursTableau($tab,$annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT count(*) as nb FROM fftt_tournoi_joueurs_tab WHERE tableau = '$tab' AND annee = $annee";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function getNbEquipesTableauDouble($tab,$annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT count(*) as nb FROM fftt_tournoi_joueurs_tab_double WHERE tableau = '$tab' AND annee = $annee";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function supprimerJoueurTableau ($licence,$annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
				
			$sql="DELETE FROM `fftt_tournoi_joueurs_tab` WHERE `licence` = '$licence' AND `annee` = '$annee' ";
		//	echo $sql;
			
			$st = $db->prepare($sql);
			$st->execute();
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($db->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
			$db=null;
		}
	}

	public function getNbPlaces($annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT nom,jour,joueurs_max,joueurs_max-count(*) as nb FROM fftt_tournoi_tableaux 
			left join (select * from fftt_tournoi_joueurs_tab where annee = $annee) as t on fftt_tournoi_tableaux.nom = t.tableau 
			group by nom,jour,joueurs_max 
			order by nom";
            $sql = "SELECT nom, jour, fftt_tournoi_tableaux.annee,  CASE WHEN nb IS NOT NULL THEN joueurs_max - nb ELSE joueurs_max END as nb FROM `fftt_tournoi_tableaux` LEFT JOIN (SELECT annee, tableau, count(*) as nb FROM `fftt_tournoi_joueurs_tab` group by annee, tableau) as t ON fftt_tournoi_tableaux.nom = t.tableau AND fftt_tournoi_tableaux.annee = t.annee where fftt_tournoi_tableaux.annee = $annee";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}

	public function getJoueurTournoi($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT * FROM fftt_tournoi_joueurs WHERE licence = '$licence'";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}
	
	public function getJoueurTableaux($licence,$annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT tableau FROM fftt_tournoi_joueurs_tab WHERE licence = '$licence' AND annee = '$annee'";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}
	
	public function insererJoueurTableau ($licence,$tab,$annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
				
			$sql="INSERT INTO `fftt_tournoi_joueurs_tab`(`licence`, `annee`, `tableau`) VALUES ('$licence', '$annee', '$tab' )
			ON DUPLICATE KEY UPDATE `licence` = '$licence', `annee` = '$annee', `tableau` = '$tab' ";
	//		echo $sql;
			
			$st = $db->prepare($sql);
			$st->execute();
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($db->errorInfo());
		}
		finally {
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
			$db=null;
		}
	}

}	
?>

