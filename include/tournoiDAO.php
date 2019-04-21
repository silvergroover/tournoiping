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

            $sql = "SELECT CONCAT(t.nom_joueur1,' ',t.prenom_joueur1,' / ',t.nom_joueur2,' ',t.prenom_joueur2) as equipe, 
                t.point_joueur1+t.point_joueur2 as points FROM ( 
            SELECT 
            (SELECT nom FROM `fftt_tournoi_joueurs` WHERE licence = licence1 and annee = 2019) as nom_joueur1, 
            (SELECT prenom FROM `fftt_tournoi_joueurs` WHERE licence = licence1 and annee = 2019) as prenom_joueur1, 
            (SELECT nom FROM `fftt_tournoi_joueurs` WHERE licence = licence2 and annee = 2019) as nom_joueur2, 
            (SELECT prenom FROM `fftt_tournoi_joueurs` WHERE licence = licence2 and annee = 2019) as prenom_joueur2, 
            (SELECT valinit FROM `fftt_tournoi_joueurs` WHERE licence = licence1 and annee = 2019) as point_joueur1, 
            (SELECT valinit FROM `fftt_tournoi_joueurs` WHERE licence = licence2 and annee = 2019) as point_joueur2 
            from `fftt_tournoi_joueurs_tab_double` WHERE `fftt_tournoi_joueurs_tab_double`.tableau = :tab AND `fftt_tournoi_joueurs_tab_double`.annee = :annee ) t
            ";
			$st = $db->prepare($sql);
			//echo $sql;
			$st->execute(array(':tab' => $tab, ':annee' => $annee ));
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

	public function supprimerJoueurTableauDouble ($licence,$annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
				
			$sql="DELETE FROM `fftt_tournoi_joueurs_tab_double` WHERE (`licence1` = '$licence' or `licence2` = '$licence') AND `annee` = '$annee' ";
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
            $sql = "SELECT nom, jour, fftt_tournoi_tableaux.annee, CASE WHEN nb IS NOT NULL THEN joueurs_max - nb ELSE joueurs_max END as nb 
                FROM `fftt_tournoi_tableaux` LEFT JOIN (
                  SELECT annee, tableau, count(*) as nb FROM `fftt_tournoi_joueurs_tab_double` group by annee, tableau 
                  UNION SELECT annee, tableau, count(*) as nb FROM `fftt_tournoi_joueurs_tab` group by annee, tableau
                  ) as t 
                ON fftt_tournoi_tableaux.nom = t.tableau AND fftt_tournoi_tableaux.annee = t.annee 
                WHERE fftt_tournoi_tableaux.annee = :annee ";

			$st = $db->prepare($sql);
			$st->execute(array( ':annee' => $annee));
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
			$sql = "SELECT tableau FROM fftt_tournoi_joueurs_tab WHERE licence = :licence AND annee = :annee UNION SELECT tableau FROM fftt_tournoi_joueurs_tab_double WHERE ( licence1 = :licence or licence2 = :licence ) AND annee = :annee";
			$st = $db->prepare($sql);
			$st->execute(array(':licence' => $licence, ':annee' => $annee));
			$result = $st->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur lors de la récuperation des données");
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

	public function insererJoueurTableauDouble ($licence1,$licence2,$tab,$annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
				
			$sql="INSERT INTO `fftt_tournoi_joueurs_tab_double`(`licence1`,`licence2`, `annee`, `tableau`) VALUES (:licence1, :licence2, :annee, :tab) ON DUPLICATE KEY UPDATE `licence1` = :licence1, `licence2` = :licence2, `annee` = :annee, `tableau` = :tab";
	//		$sql="INSERT INTO `fftt_tournoi_joueurs_tab_double`(`licence1`,`licence2`, `annee`, `tableau`) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `licence1` = ?, `licence2` = ?, `annee` = ?, `tableau` = ? ";
			echo $sql;
			
            $data = array($licence1,$licence2,$annee,$tab,$licence1,$licence2,$annee,$tab);
			$st = $db->prepare($sql);
		//	$st->execute($data);
//			$st->execute($licence1,$licence2,$tab,$annee,$licence1,$licence2,$annee,$tab);
            $st->execute(array(':licence1' => $licence1, ':licence2' => $licence2, ':tab' => $tab, ':annee' => $annee ));
//            $st->execute(array(':licence1' => $licence1, ':licence2' => $licence2, ':tab' => $tab, ':annee' => $annee, ':licence1' => $licence1, ':licence2' => $licence2, ':annee' => $annee, ':tab' => $tab ));
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

