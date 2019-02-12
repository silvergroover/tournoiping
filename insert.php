<?php
echo "begin";
/**
 * @author VincentBab vincentbab@gmail.com
*/
echo "declare class";
class Service
{
	/**
	 * @var string $appId ID de l'application fourni par la FFTT (ex: AM001)
	 */
	protected $appId;

	/**
	 * @var string $appKey Mot de passe fourni par la FFTT
	 */
	protected $appKey;

	/**
	 * @var string $serial Serial de l'utilisateur
	 */
	protected $serial;

	/**
	 * @var object $cache
	 */
	protected $cache;

	/**
	 * @var object $logger
	 */
	protected $logger;

	/**
	 * @var string $ipSource
	 */
	protected $ipSource;

	public function __construct($appId, $appKey)
	{
		$this->appId = $appId;
		$this->appKey = $appKey;

		libxml_use_internal_errors(true);
	}

	public function getAppId()
	{
		return $this->appId;
	}

	public function getAppKey()
	{
		return $this->appKey;
	}

	public function setSerial($serial)
	{
		$this->serial = $serial;

		return $this;
	}

	public function getSerial()
	{
		return $this->serial;
	}

	public function setCache($cache)
	{
		$this->cache = $cache;

		return $this;
	}

	public function getCache()
	{
		return $this->cache;
	}

	public function setLogger($logger)
	{
		$this->logger = $logger;

		return $this;
	}

	public function getLogger()
	{
		return $this->logger;
	}

	public function setIpSource($ipSource)
	{
		$this->ipSource = $ipSource;

		return $this;
	}

	public function getIpSource()
	{
		return $this->ipSource;
	}

	public function initialization()
	{
		return Service::getObject($this->getData('http://www.fftt.com/mobile/pxml/xml_initialisation.php', array()));
	}

	public function getClubsByDepartement($departement)
	{
		return $this->getCachedData("clubs_{$departement}", 3600*24*7, function($service) use ($departement) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_club_dep2.php', array('dep' => $departement)), 'club');
		});
	}

	public function getClub($numero)
	{
		return $this->getCachedData("club_{$numero}", 3600*24*7, function($service) use ($numero) {
			return Service::getObject($service->getData('http://www.fftt.com/mobile/pxml/xml_club_detail.php', array('club' => $numero)), 'club');
		});
	}
	public function cleanClub($numero)
	{
		if (!$this->cache) {
			return;
		}

		$this->cache->delete("club_{$numero}");
		$this->cache->delete("clubjoueurs_{$numero}");
		$this->cache->delete("clubequipes_{$numero}_M");
		$this->cache->delete("clubequipes_{$numero}_F");
		$this->cache->delete("clubequipes_{$numero}_");
	}

	public function getJoueur($licence)
	{
		$joueur = $this->getCachedData("joueur_{$licence}", 3600*24*7, function($service) use ($licence) {
			return Service::getObject($service->getData('http://www.fftt.com/mobile/pxml/xml_joueur.php', array('licence' => $licence, 'auto' => 1)), 'joueur');
		});

			if (!isset($joueur['licence'])) {
				return null;
			}

			if (empty($joueur['natio'])) {
				$joueur['natio'] = 'F';
			}

			$joueur['photo'] = "http://www.fftt.com/espacelicencie/photolicencie/{$joueur['licence']}_.jpg";
			$joueur['progmois'] = round($joueur['point'] - $joueur['apoint'], 2); // Progression mensuelle
			$joueur['progann'] = round($joueur['point'] - $joueur['valinit'], 2); // Progression annuelle

			return $joueur;
	}
	public function cleanJoueur($licence)
	{
		if (!$this->cache) {
			return;
		}

		$this->cache->delete("joueur_{$licence}");
		$this->cache->delete("joueurparties_{$licence}");
		$this->cache->delete("joueurspid_{$licence}");
	}

	public function getJoueurParties($licence)
	{
		return $this->getCachedData("joueurparties_{$licence}", 3600*24*7, function($service) use ($licence) {
			//    return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_partie_mysql.php', array('licence' => $licence)), 'partie');
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_partie_mysql.php', array('licence' => $licence, 'auto' => 1)), 'partie');
		});
	}

	public function getJoueurPartiesSpid($licence)
	{
		return $this->getCachedData("joueurspid_{$licence}", 3600*24*1, function($service) use ($licence) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_partie.php', array('numlic' => $licence)), 'resultat');
		});
	}

	public function getJoueurHistorique($licence)
	{
		return $this->getCachedData("joueur_historique_{$licence}", 3600*24*2, function($service) use ($licence) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_histo_classement.php', array('numlic' => $licence)), 'histo');
		});
	}

	public function getJoueursByName($nom, $prenom= '')
	{
		return $this->getCachedData("joueurs_{$nom}_{$prenom}", 3600*24*7, function($service) use ($nom, $prenom) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_liste_joueur.php', array('nom' => $nom, 'prenom' => $prenom)), 'joueur');
		});
	}

	public function getJoueursByClub($club)
	{
		return $this->getCachedData("clubjoueurs_{$club}", 3600*24*7, function($service) use ($club) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_liste_joueur.php', array('club' => $club)), 'joueur');
		});
	}

	public function getEquipesByClub($club, $type = null)
	{
		if ($type && !in_array($type, array('M', 'F'))) {
			$type = 'M';
		}

		$teams = $this->getCachedData("clubequipes_{$club}_{$type}", 3600*24*7, function($service) use ($club, $type) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_equipe.php', array('numclu' => $club, 'type' => $type)), 'equipe');
		});

			foreach($teams as &$team) {
				$params = array();
				parse_str($team['liendivision'], $params);

				$team['idpoule'] = $params['cx_poule'];
				$team['iddiv'] = $params['D1'];
			}

			return $teams;
	}

	public function getPoules($division)
	{
		$poules = $this->getCachedData("poules_{$division}", 3600*24*7, function($service) use ($division) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_result_equ.php', array('action' => 'poule', 'D1' => $division)), 'poule');
		});

			foreach($poules as &$poule) {
				$params = array();
				parse_str($poule['lien'], $params);

				$poule['idpoule'] = $params['cx_poule'];
				$poule['iddiv'] = $params['D1'];
			}

			return $poules;
	}

	public function getPouleClassement($division, $poule = null)
	{
		return $this->getCachedData("pouleclassement_{$division}_{$poule}", 3600*24*1, function($service) use ($division, $poule) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_result_equ.php', array('auto' => 1, 'action' => 'classement', 'D1' => $division, 'cx_poule' => $poule)), 'classement');
		});
	}

	public function getPouleRencontres($division, $poule = null)
	{
		return $this->getCachedData("poulerencontres_{$division}_{$poule}", 3600*24*1, function($service) use ($division, $poule) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_result_equ.php', array('auto' => 1, 'D1' => $division, 'cx_poule' => $poule)), 'tour');
		});
	}

	public function getIndivGroupes($division)
	{
		$groupes = $this->getCachedData("groupes_{$division}", 3600*24*7, function($service) use ($division) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_result_indiv.php', array('action' => 'poule', 'res_division' => $division)), 'tour');
		});

			foreach($groupes as &$groupe) {
				$params = array();
				parse_str($groupe['lien'], $params);

				if (isset($params['cx_tableau'])) {
					$groupe['idgroupe'] = $params['cx_tableau'];
				} else {
					$groupe['idgroupe'] = null;
				}
				$groupe['iddiv'] = $params['res_division'];
			}

			return $groupes;
	}

	public function getGroupeClassement($division, $groupe = null)
	{
		return $this->getCachedData("groupeclassement_{$division}_{$groupe}", 3600*24*1, function($service) use ($division, $groupe) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_result_indiv.php', array('action' => 'classement', 'res_division' => $division, 'cx_tableau' => $groupe)), 'classement');
		});
	}

	public function getGroupeRencontres($division, $groupe = null)
	{
		return $this->getCachedData("grouperencontres_{$division}_{$groupe}", 3600*24*1, function($service) use ($division, $groupe) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_result_indiv.php', array('action' => 'partie', 'res_division' => $division, 'cx_tableau' => $groupe)), 'partie');
		});
	}

	public function getOrganismes($type)
	{
		// Zone / Ligue / Departement
		if (!in_array($type, array('Z', 'L', 'D'))) {
			$type = 'L';
		}

		return $this->getCachedData("organismes_{$type}", 3600*24*30, function($service) use ($type) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_organisme.php', array('type' => $type)), 'organisme');
		});
	}

	public function getEpreuves($organisme, $type)
	{
		// Equipe / Individuelle
		if (!in_array($type, array('E', 'I'))) {
			$type = 'E';
		}

		return $this->getCachedData("epreuves_{$organisme}_{$type}", 3600*24*30, function($service) use ($organisme, $type) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_epreuve.php', array('type' => $type, 'organisme' => $organisme)), 'epreuve');
		});
	}

	public function getDivisions($organisme, $epreuve, $type = 'E')
	{
		// Equipe / Individuelle
		if (!in_array($type, array('E', 'I'))) {
			$type = 'E';
		}

		return $this->getCachedData("divisions_{$organisme}_{$epreuve}_{$type}", 3600*24*7, function($service) use ($organisme, $epreuve, $type) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_division.php', array('organisme' => $organisme, 'epreuve' => $epreuve, 'type' => $type)), 'division');
		});
	}

	public function getRencontre($link)
	{
		$params = array();
		parse_str($link, $params);

		return $this->getCachedData("rencontre_".sha1($link), 3600*24*1, function($service) use ($params) {
			return Service::getObject($service->getData('http://www.fftt.com/mobile/pxml/xml_chp_renc.php', $params), null);
		});
	}

	public function getLicencesByName($nom, $prenom= '')
	{
		return $this->getCachedData("licences_{$nom}_{$prenom}", 3600*24*2, function($service) use ($nom, $prenom) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_liste_joueur_o.php', array('nom' => strtoupper($nom), 'prenom' => ucfirst($prenom))), 'joueur');
		});
	}

	public function getLicencesByClub($club)
	{
		return $this->getCachedData("licencesclub_{$club}", 3600*24*2, function($service) use ($club) {
			return Service::getCollection($service->getData('http://www.fftt.com/mobile/pxml/xml_liste_joueur_o.php', array('club' => $club)), 'joueur');
		});
	}

	public function getLicence($licence)
	{
		return $this->getCachedData("licence_{$licence}", 3600*24*2, function($service) use ($licence) {
			return Service::getObject($service->getData('http://www.fftt.com/mobile/pxml/xml_licence.php', array('licence' => $licence)), 'licence');
		});
	}

	protected function getCachedData($key, $lifeTime, $callback)
	{
		if (!$this->cache) {
			return $callback($this);
		}

		if (false === ($data = $this->cache->fetch($key))) {
			$data = $callback($this);

			if ($data !== false) {
				$this->cache->save($key, $data, $lifeTime);
			}
		}

		return $data;
	}

	public function getData($url, $params = array(), $generateHash = true)
	{
		if ($generateHash) {
			$params['serie'] = $this->getSerial();
			$params['id'] = $this->getAppId();
			$params['tm'] = date('YmdHis') . substr(microtime(), 2, 3);
			$params['tmc'] =  hash_hmac('sha1', $params['tm'], hash('md5', $this->getAppKey(), false));
		}

		if (!empty($params)) {
			$url .= '?' . http_build_query($params);
		}

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		if ($this->getIpSource()) {
			curl_setopt($curl, CURLOPT_INTERFACE, $this->getIpSource());
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER , true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				"Accept:", // Suprime le header par default de cUrl (Accept: */*)
				"User-agent: Mozilla/4.0 (compatible; MSIE 6.0; Win32)",
				"Content-Type: application/x-www-form-urlencoded",
				"Accept-Encoding: gzip",
				"Connection: Keep-Alive",
		));
		$data = curl_exec($curl);
		curl_close($curl);

		if ($this->logger) {
			$this->logger->log($url, $data);
		}

		$xml = simplexml_load_string($data);

		if (!$xml) {
			return false;
		}

		// Petite astuce pour transformer simplement le XML en tableau
		return json_decode(json_encode($xml), true);
	}

	public static function getCollection($data, $key = null)
	{
		if (empty($data)) {
			return array();
		}

		if ($key) {
			if (!array_key_exists($key, $data)) {
				return array();
			}
			$data = $data[$key];
		}

		return isset($data[0]) ? $data : array($data);
	}

	public static function getObject($data, $key = null)
	{
		if ($key && $data !== false) {
			return array_key_exists($key, $data) ? $data[$key] : null;
		} else {
			return empty($data) ? null : $data;
		}
	}

	public static function generateSerial()
	{
		$serial = '';
		for($i=0; $i<15; $i++) {
			$serial .= chr(mt_rand(65, 90)); //(A-Z)
		}

		return $serial;
	}
	public static function fonctionComparaison($a, $b){
		return $a['val'] < $b['val'];
	}

}

class SmartpingDAO {

	//M�thode de gestion de connexion
	private function connexion () {
		//	$url = "mysql:host=localhost;dbname=aurorevitett";
		$url = "mysql:host=aurorevitett.mysql.db;dbname=aurorevitett";
		$usr = "aurorevitett";
		$pwd = "DBaurore35";

		$db = new PDO($url, $usr, $pwd);
		//par d�faut PDO ne renvoi pas d'erreur. (ERRMODE_SILENT). la ligne suivante permet de pouvoir g�rer les erreurs
		$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

		return $db;
	}

	public function insererJoueur ($prenom,$nom,$point,$categ,$valinit,$progmois,$progann,$licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();

			$sql="INSERT INTO fftt_joueur(prenom,nom,point,categ,valinit,progmois,progann,licence) VALUES('$prenom','$nom','$point','$categ','$valinit','$progmois','$progann','$licence')";
				
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

	public function insererPointsMens ($licence,$annee,$mois,$point,$categ,$valinit,$progmois,$progann) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();

			$sql="INSERT INTO fftt_clt_mens(licence,annee,mois,points,categ,valinit,progmois,progann) VALUES('$licence','$annee','$mois','$point','$categ','$valinit','$progmois','$progann')";
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

	public function getProgMens($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();

			$sql="SELECT annee,mois,points FROM fftt_clt_mens WHERE licence = '$licence'";
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

	public function insererPartieSpid ($licence,$adversaire,$date,$classement,$epreuve,$victoire,$forfait) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();

			$sql="INSERT INTO fftt_parties_spid(licence,adversaire,date,classement,epreuve,victoire,forfait) VALUES('$licence','$adversaire',STR_TO_DATE('$date','%d/%m/%Y'),'$classement','$epreuve','$victoire','$forfait')";

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

	public function insererPartie ($licence,$advlic,$vd,$numjourn,$codechamp,$date,$advnompre,$pointres,$coefchamp,$advclaof) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();

			$sql="INSERT INTO fftt_parties(licence,advlic,vd,numjourn,codechamp,date,advnompre,pointres,coefchamp,advclaof)
			VALUES('$licence','$advlic','$vd','$numjourn','$codechamp',STR_TO_DATE('$date','%d/%m/%Y'),'$advnompre','$pointres','$coefchamp','$advclaof')";
				
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

	public function getJoueurs() {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();

			$sql="SELECT * from fftt_joueur order by progmois desc";

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

	public function getPartieParJoueurSpid($licence) {
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
			FROM `fftt_parties_spid` WHERE licence = $licence order by date desc";
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

	public function getTopVictoiresSpid($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT * FROM fftt_parties_spid where licence = $licence and victoire = 'V' order by classement desc LIMIT 5 ";
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

	public function getAvgClassement($licence) {
		$db = null;
		$st = null;
		try {
			$db=$this->connexion();
			$sql = "SELECT AVG(classement) FROM fftt_parties_spid where licence = $licence";
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

}

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