<?php

include_once ('Smartping.php');
include_once ('JournalException.php');
class SmartpingDAO {

	//Méthode de gestion de connexion
	private function connexion () {
//		$url = "mysql:host=localhost;dbname=aurorevitett";
		$url = "mysql:host=aurorevitett.mysql.db;dbname=aurorevitett";
		$usr = "aurorevitett";
		$pwd = "DBaurore35";
//		echo "access to db ok<br/>";
		$db = new PDO($url, $usr, $pwd);
		//par défaut PDO ne renvoi pas d'erreur. (ERRMODE_SILENT). la ligne suivante permet de pouvoir gérer les erreurs
		$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	
		return $db;
	}

	public function getSaison()    {
		
		$year  = date("Y");
		if (date("z") > 183) {
			$year2 = $year + 1;
			$saison  = $year.'-'.$year2;
		}
		else {
//		echo "year : ".$year."</br>";
//		echo "date_z : ".date("z")."</br>";
			$year2 = $year;
			$year = $year - 1;
//		echo "year : ".$year."</br>";
//		echo "year2 : ".$year2."</br>";
			$saison  = $year.'-'.$year2;
		}
//		echo "saison : ".$saison."\n";
		return $saison;
	}

		public function getPhase()    {
		
		$year  = date("Y");
		if (date("z") > 183) {
			$phase = 1;
		}
		else {
			$phase = 2;
		}
		return $phase;
	}

	public function getListeSaison()    {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
		
			$sql="SELECT distinct saison FROM `fftt_clt_mens`";
	//		echo $sql;
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
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
	
	public function insererJoueur ($prenom,$nom,$point,$categ,$valinit,$progmois,$progann,$licence,$saison) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
				
			$sql="INSERT INTO fftt_joueur(prenom,nom,point,categ,valinit,progmois,progann,licence,saison) VALUES('$prenom','$nom','$point','$categ','$valinit','$progmois','$progann','$licence','$saison')";
			
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

	public function updateJoueur ($point,$categ,$valinit,$progmois,$progann,$licence,$saison) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
				
			$sql="UPDATE fftt_joueur SET point = '$point', categ = '$categ', valinit = '$valinit', progmois = '$progmois',progann = '$progann', saison = '$saison' WHERE licence = '$licence'";
			echo $sql;
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

	public function insererPointsMens ($licence,$annee,$mois,$point,$categ,$valinit,$progmois,$progann,$saison) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="INSERT INTO fftt_clt_mens(licence,annee,mois,points,categ,valinit,progmois,progann,saison) 
			VALUES('$licence','$annee','$mois','$point','$categ','$valinit','$progmois','$progann','$saison')";
				echo $sql;
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
	
	public function updatePointsMens ($licence,$annee,$mois,$point,$progmois,$progann) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="UPDATE fftt_clt_mens SET points = '$point', progmois = '$progmois', progann = '$progann' WHERE licence = '$licence' AND annee = '$annee' AND mois = '$mois'";
				echo $sql;
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
	
	public function getCleProgMens($mois,$annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
		
			$sql="SELECT licence FROM fftt_clt_mens WHERE annee = '$annee' AND mois = '$mois'";
			echo $sql;
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
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

	public function getProgMens($licence,$saison) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
		
			$sql="SELECT annee,mois,points FROM fftt_clt_mens WHERE licence = '$licence' and saison = '$saison' order by annee,mois";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
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

	public function insererPartieSpid ($licence,$adversaire,$date,$classement,$epreuve,$victoire,$forfait,$saison,$num) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();

			$sql="INSERT INTO fftt_parties_spid(licence,adversaire,date,classement,epreuve,victoire,forfait,saison,Numero) 
			VALUES('$licence','$adversaire',STR_TO_DATE('$date','%d/%m/%Y'),'$classement','$epreuve','$victoire','$forfait','$saison','$num')";
				echo $sql."<\br>";
			$st = $db->prepare($sql);
			$st->execute();
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			return $sql;
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}
	
	public function insererPartie ($licence,$advlic,$vd,$numjourn,$codechamp,$date,$advnompre,$pointres,$coefchamp,$advclaof,$saison) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();

			$sql="INSERT INTO fftt_parties(licence,advlic,vd,numjourn,codechamp,date,advnompre,pointres,coefchamp,advclaof,saison) 
				VALUES('$licence','$advlic','$vd','$numjourn','$codechamp',STR_TO_DATE('$date','%d/%m/%Y'),'$advnompre','$pointres','$coefchamp','$advclaof','$saison')";
			
			$st = $db->prepare($sql);
			$st->execute();
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			return $sql;
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}
	
	public function getListLicence () {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			//			$joueur = $api->getJoueursByClub('07350021');
			//			foreach ($joueur as $cle=>$val) {
			//			$joueurdetail = $api->getJoueur($prenom,$nom,$point,$categ,$valinit,$progmois,$progann,$licence);
				
			$sql="SELECT licence from fftt_joueur";
				
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

	public function getJoueurs($saison) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="SELECT * from fftt_joueur where saison = '$saison' order by progmois desc";
	
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

	public function getJoueurDetails($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="SELECT * from fftt_joueur where licence = $licence";
	
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
	
	public function getDateDernierePartie($table,$licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$last_date = $db->query("SELECT date FROM $table WHERE licence = $licence ORDER BY date DESC LIMIT 1");
	
			$date = '';
			foreach ($last_date as $enreg) {
				//on cr�� un objet article
				$date = $enreg['date'];
			}
		
			return $date;
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

	public function getPartieParJoueurSpid($licence, $saison) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			//$sql = "SELECT * FROM fftt_parties_spid WHERE licence = $licence";
			$sql = "SELECT adversaire, date,epreuve,classement,victoire, 
						(select Distinct pointres from fftt_parties 
        					where fftt_parties.licence = fftt_parties_spid.licence 
           					and fftt_parties.advnompre = fftt_parties_spid.adversaire
           					and fftt_parties.date = fftt_parties_spid.date
                         and fftt_parties.vd = fftt_parties_spid.victoire
           						) as pointres
		           FROM `fftt_parties_spid` WHERE licence = $licence  and saison = '$saison' order by date desc";
		//	echo $sql."<\br>";
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

	public function getTopVictoiresSpid($licence, $saison) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT * FROM fftt_parties_spid where licence = $licence  and saison = '$saison' and victoire = 'V' order by classement desc LIMIT 5 ";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
			}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
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

	public function getTopVictoires($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT * FROM fftt_parties where licence = $licence and vd = 'V' order by advclaof desc, pointres desc LIMIT 5 ";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
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

	public function getAvgClassement($licence, $saison) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT AVG(classement) FROM fftt_parties_spid where licence = $licence and saison = '$saison'";
			$st = $db->prepare($sql);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		}
		catch (PDOException $e) {
			//		throw new JournalException("erreur de l'enregistrement du joueur");
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
	
	public function getPartieParJoueur($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT * FROM fftt_parties,fftt_epreuve WHERE fftt_parties.codechamp = fftt_epreuve.code and licence = $licence order by date desc";
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
	
	public function insererhisto($licence,$saison,$phase,$point) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="INSERT INTO fftt_saisons(licence,saison,phase,points) VALUES('$licence','$saison','$phase','$point')";
	
			$st = $db->prepare($sql);
			$st->execute();
		}
		catch (PDOException $e) {
			throw new JournalException(print_r($db->errorInfo()));
			print_r($dbh->errorInfo());
		}
		finally {
			return $sql;
			//fermeture de la connexion
			if(!is_null($st))
				$st->closeCursor();
				$db=null;
		}
	}
	
	public function getClassementAdversaires($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT classement,count(*) FROM fftt_parties_spid WHERE licence = $licence group by victoire";
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

	public function getHistorique($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT saison,phase,points FROM fftt_saisons WHERE licence = $licence order by saison,phase";
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

	public function getJoueursTournoi($annee) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT fftt_tournoi_joueurs.licence,nom,prenom,email,tel,date_inscription,club,tableau 
                FROM fftt_tournoi_joueurs inner join fftt_tournoi_joueurs_tab 
                ON fftt_tournoi_joueurs.licence = fftt_tournoi_joueurs_tab.licence 
                where fftt_tournoi_joueurs_tab.annee = $annee";
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

	public function insererEquipe ($libequipe,$libdivision,$liendivision,$idepr,$idpoule,$iddiv,$annee,$saison,$phase,$type) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
				
			$sql="INSERT INTO fftt_equipes(libequipe,libdivision,liendivision,idepr,idpoule,iddiv,annee,saison,phase,categorie) 
			VALUES('$libequipe','$libdivision','$liendivision','$idepr','$idpoule','$iddiv','$annee','$saison',$phase, '$type')
			ON DUPLICATE KEY UPDATE libequipe = '$libequipe', libdivision = '$libdivision', liendivision = '$liendivision',	idepr = '$idepr', idpoule = '$idpoule',iddiv = '$iddiv',annee = '$annee', saison = '$saison', phase = $phase, categorie = '$type'";

			echo $sql."<br/>";
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

	public function insererPouleClt ($poule,$clt,$equipe,$joue,$pts,$numero,$idpoule,$iddiv,$annee,$saison,$totvic,$totdef,$idequipe,$idclub,$vic,$def,$nul,$pf,$pg,$pp) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
				
			$sql="INSERT INTO fftt_poule_clt(poule,clt,equipe,joue,pts,numero,idpoule,iddiv,annee,saison,totvic,totdef,idequipe,idclub,vic,def,nul,pf,pg,pp) 
			VALUES('$poule','$clt','$equipe','$joue','$pts','$numero','$idpoule','$iddiv','$annee','$saison',$totvic,$totdef,$idequipe,$idclub,$vic,$def,$nul,$pf,$pg,$pp)
			ON DUPLICATE KEY UPDATE poule = '$poule',clt = '$clt',equipe = '$equipe',joue = '$joue',pts = '$pts',
			numero = '$numero', idpoule = '$idpoule',iddiv = '$iddiv',annee = '$annee',saison = '$saison', totvic = $totvic, totdef = $totdef, idequipe = $idequipe, idclub = $idclub, vic = $vic, def = $def, nul = $nul, pf = $pf, pg = $pg, pp = $pp";

			echo $sql;
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

	public function insererPouleRenc ($libelle,$equa,$equb,$scorea,$scoreb,$lien,$idpoule,$iddiv,$annee,$saison,$clepartie) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
				
			$sql="INSERT INTO fftt_poule_renc(libelle,equa,equb,scorea,scoreb,lien,idpoule,iddiv,annee,saison,renc_id) 
			VALUES('$libelle','$equa','$equb','$scorea','$scoreb','$lien','$idpoule','$iddiv','$annee','$saison',$clepartie)
			ON DUPLICATE KEY UPDATE libelle = '$libelle', equa = '$equa', equb = '$equb', scorea = '$scorea', scoreb = '$scoreb', lien = '$lien', idpoule = '$idpoule', iddiv = '$iddiv', annee = '$annee', saison = '$saison', renc_id = $clepartie";

			echo $sql;
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

	public function insererchpRenc ($lien,$ja,$jb,$scorea,$scoreb,$saison,$clepartie,$renc_id) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
				
			$sql="INSERT INTO fftt_chp_renc(lien,ja,jb,scorea,scoreb,saison,clepartie,renc_id) 
			VALUES('$lien','$ja','$jb','$scorea','$scoreb','$saison',$clepartie,$renc_id)
			ON DUPLICATE KEY UPDATE lien = '$lien',ja = '$ja',jb = '$jb',scorea = '$scorea',scoreb = '$scoreb',saison = '$saison',clepartie = $clepartie, renc_id = $renc_id";

			echo $sql;
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

	public function insererchpJoueur ($lien,$ja,$jb,$ca,$cb,$saison) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
				
			$sql="INSERT INTO fftt_chp_joueur(lien,ja,jb,ca,cb,saison) 
			VALUES('$lien','$ja','$jb','$ca','$cb','$saison')
			ON DUPLICATE KEY UPDATE lien = '$lien',ja = '$ja',jb = '$jb',ca = '$ca',cb = '$cb',saison = '$saison'";

			echo $sql;
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

	public function getEquipe($saison,$categorie,$phase) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="SELECT * from fftt_equipes where saison = '$saison' and categorie = '$categorie' and phase = $phase order by idepr,iddiv,libequipe";
	
			//echo $sql;
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

	public function getPoule($saison,$idpoule) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="SELECT * from fftt_poule_clt where saison = '$saison' and idpoule = '$idpoule' order by  pts desc,clt desc";
	
			//echo $sql;
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

	public function getIdPoule($saison,$equipe,$phase,$categorie) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="SELECT * FROM `fftt_equipes` where libequipe like '$equipe%' and categorie = '$categorie' and saison = '$saison' and phase = $phase";
	
		//	echo $sql;
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
	
	public function getEquRenc($idpoule,$saison,$libelle) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="SELECT libelle,equa,equb,scorea,scoreb,lien FROM `fftt_poule_renc` where idpoule = $idpoule and saison = '$saison' and libelle = '$libelle' order by libelle";
	
		//	echo $sql;
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

	public function getRencontre($lien) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="SELECT * FROM `fftt_chp_renc` where lien = '$lien' and clepartie is not null order by clepartie";
	
			//echo $sql;
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

	public function getJoueursRenc($lien) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="SELECT * FROM `fftt_chp_joueur` where lien = '$lien'";
	
			//echo $sql;
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

	public function getToursPoule($saison,$idpoule) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
	
			$sql="SELECT distinct libelle FROM `fftt_poule_renc` where idpoule = $idpoule and saison = '$saison'";
	
			//echo $sql;
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
	
}	
?>