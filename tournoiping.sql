
CREATE TABLE IF NOT EXISTS `fftt_tournoi_tableaux` (
  `nom` varchar(10) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `jour` int(1) NOT NULL,
  `clamin` int(8) NOT NULL,
  `clamax` int(8) DEFAULT NULL,
  `joueurs_max` int(3) NOT NULL,
  `annee` int(12) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nom`,`annee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `fftt_tournoi_joueurs` (
  `licence` int(10) NOT NULL,
  `nom` varchar(32) NOT NULL,
  `prenom` varchar(32) NOT NULL,
  `email` varchar(100) NOT NULL,
  `tel` varchar(20) NOT NULL,
  `club` varchar(50) NOT NULL,
  `point` decimal(8,0) NOT NULL,
  `clnat` int(8) NOT NULL,
  `categ` varchar(8) NOT NULL,
  `valinit` decimal(8,0) NOT NULL,
  `progmois` decimal(8,0) NOT NULL,
  `progann` decimal(8,0) NOT NULL,
  `annee` int(4) NOT NULL,
  `date_inscription` date NOT NULL,
  PRIMARY KEY (`licence`,`annee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `fftt_tournoi_joueurs_tab` (
  `licence` int(8) NOT NULL,
  `annee` int(4) NOT NULL,
  `tableau` varchar(8) NOT NULL,
  PRIMARY KEY (`licence`,`annee`,`tableau`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
