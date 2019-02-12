<head>
<script	src="include/jquery.min.js"></script>
<script src="include/highcharts.js"></script>
<script src="include/exporting.js"></script>
<link href="include/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="include/style.css" type="text/css" />
<meta charset="UTF-8">
</head>

<?php
include_once ('include/SmartpingDAO.inc');
include_once ('include/Smartping.inc');

$dao = new SmartpingDAO ();

// Get Historique des saisons en base

$histo = $dao->getHistorique ( $_GET ['id'] );
$label = "''";
$data = "null";
foreach ( $histo as $value ) {
	$label = $label . ",'" . $value ['saison'] . " - " . $value ['phase'] . "'";
	$data = $data . "," . $value ['points'];
}

// Get Historique des points mensuels en base

$histoMens = $dao->getProgMens( $_GET ['id'] );
$labelMens = "''";
$dataMens = "null";
foreach ( $histoMens as $value ) {
	$labelMens = $labelMens. ",'" . $value ['annee'] . "/" . $value ['mois'] . "'";
	$dataMens = $dataMens . "," . $value ['points'];
}

// Get infos joueur en base

$joueur = $dao->getJoueurDetails ( $_GET ['id'] );
foreach ( $joueur as $joueurdetail ) {
	$clt = variant_int ( $joueurdetail ['point'] / 100 );
	$prenom = $joueurdetail ['prenom'];
	$nom = $joueurdetail ['nom'];
}

$avgClt = $dao->getAvgClassement( $_GET ['id'] );
 
// Get historique des parties Spid en base

$nbv = 0;
$nbd = 0;

$partieSpid = $dao->getPartieParJoueurSpid ( $_GET ['id'] );

/*echo "<pre>";
print_r($partieSpid);
echo "</pre>";
*/
$sortPartieSpid = $dao->getTopVictoiresSpid( $_GET ['id'] );

$classement = array ();
$id_class = 5;
// echo "type : ".gettype($id_class)."<br>";
foreach ( $partieSpid as $partiedetail ) {
	$id_class = ( int ) variant_int ( $partiedetail ['classement'] / 100 );
	// echo "type : ".gettype($id_class)."<br>";
	if (array_key_exists ( $id_class, $classement )) {
		$classement[$id_class]['clt'] = $classement[$id_class]['clt'] + 1;
	} else {
		$classement[$id_class]['clt'] = 1;
		$classement[$id_class]['V'] = 0;
		$classement[$id_class]['D'] = 0;
	}
	if ($partiedetail ['victoire'] == 'V') {
		$classement[$id_class]['V'] = $classement[$id_class]['V'] + 1;
		$nbv = $nbv + 1;
	} elseif ($partiedetail ['victoire'] == 'D') {
		$classement[$id_class]['D'] = $classement[$id_class]['D'] + 1;
		$nbd = $nbd + 1;
	}
}
$nbt = $nbv + $nbd;
if ($nbt == 0) {
	$nbt = 1;
}
$nbpv = variant_int ( $nbv * 100 / $nbt );
$nbpd = 100 - $nbpv;
ksort ( $classement, SORT_NUMERIC );

// $classement = $dao->getClassementAdversaires($_GET['id']);

 
?>

<!-- script de creation du graph "Evolution du Cassement sur la saison" -->
<script type="text/javascript">
   	$(document).ready(function() {

    $('#container2').highcharts({
	    title: {
    	    text: '<?php echo $prenom. " ". $nom?>',
				x: - 20 //center
		},
                        chart: {
                        renderTo: 'chartContainer',
                                type: 'spline'
                        },
                        legend: { enabled:false },
                        tooltip: {
                        crosshairs: true,
                                formatter: function() {
                                return "<strong>" + this.y + " pts</strong>";
                                }
                        },
                        xAxis: {

                        categories: [<?php echo $label?>],
                                labels: {
                                align: "center",
                                        rotation: 75, y: 45
                                }
                        },
                        yAxis: {
                        title: {
                        text: 'Points',
                                margin: 10
                        },
                                alternateGridColor: '#fafafb'
                        },
                        series: [{
                        name: 'Evolutions classement',
                                color: '#e96111',
                            data: [<?php echo $data?>] }]

		});
	});
</script>

<!-- script de creation du graph "Evolution du Cassement Mensuel" -->
<script type="text/javascript">
   	$(document).ready(function() {

    $('#container4').highcharts({
	    title: {
    	    text: '<?php //echo $prenom. " ". $nom?>',
				x: - 20 //center
		},
                        chart: {
                        renderTo: 'chartContainer',
                                type: 'spline'
                        },
                        legend: { enabled:false },
                        tooltip: {
                        crosshairs: true,
                                formatter: function() {
                                return "<strong>" + this.y + " pts</strong>";
                                }
                        },
                        xAxis: {

                        categories: [<?php echo $labelMens?>],
                                labels: {
                                align: "center",
                                        rotation: 75, y: 45
                                }
                        },
                        yAxis: {
                        title: {
                        text: 'Points',
                                margin: 10
                        },
                                alternateGridColor: '#fafafb'
                        },
                        series: [{
                        name: 'Evolutions classement',
                                color: '#e96111',
                            data: [<?php echo $dataMens?>] }]

		});
	});
</script>

<!-- script de creation du graph "Vitoires et défaites par classement joué" -->

<script type="text/javascript">
$(function () {

    var colors = Highcharts.getOptions().colors,
        categories = [
        <?php
         // 'Clt 8', 'Clt 9', 'Clt 10', 'Clt 11', 'Clt 12', 'Clt 13', 'Clt 14', 'Clt 15', 'Clt 17'
				foreach ( $classement as $id_class => $value ) {
					echo "'clt $id_class', ";
				}
			?>
			],
        data = [
        <?php
        	$i = 3;
        	$color = 1;
        	// 'Clt 8', 'Clt 9', 'Clt 10', 'Clt 11', 'Clt 12', 'Clt 13', 'Clt 14', 'Clt 15', 'Clt 17'
        	foreach ( $classement as $id_class => $value ) {
// echo "<pre>";
//  print_r($value);
//  echo "</pre>";
//        		$i = -$i;
         		$color = $color + $i;
 					echo "{y:" . $value['clt'] . ", 
						color: colors[" . $color . "], 
						drilldown: { name: 'clt " . $id_class . "',	
						categories: ['Victoire', 'Defaite'], 
						data: [" . $value['V'] . "," . $value['D'] ."], 
						color: colors[". $color ."] }},
		"; 
         		$i = -$i;
        	}
        	?>
        ],
        browserData = [],
        versionsData = [],
        i,
        j,
        dataLen = data.length,
        drillDataLen,
        brightness;


    // Build the data arrays
    for (i = 0; i < dataLen; i += 1) {

        // add browser data
        browserData.push({
            name: categories[i],
            y: data[i].y,
            color: data[i].color
        });

        // add version data
        drillDataLen = data[i].drilldown.data.length;
        for (j = 0; j < drillDataLen; j += 1) {
            brightness = 0.2 - (j / drillDataLen) / 5;
            versionsData.push({
                name: data[i].drilldown.categories[j],
                y: data[i].drilldown.data[j],
                color: Highcharts.Color(data[i].color).brighten(brightness).get()
            });
        }
    }

    // Create the chart
    $('#container3').highcharts({
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Classements joués'
        },
        subtitle: {
            text: 'Victoires et défaites en fonction du classement de l\'adversaire'
        },
        yAxis: {
            title: {
                text: 'nombre'
            }
        },
        plotOptions: {
            pie: {
                shadow: false,
                center: ['50%', '50%']
            }
        },
        tooltip: {
            valueSuffix: ''
        },
        series: [{
            name: 'Matchs joués ',
            data: browserData,
            size: '60%',
            dataLabels: {
                formatter: function () {
                    return this.y > 5 ? this.point.name : null;
                },
                color: '#ffffff',
                distance: -30
            }
        }, {
            name: ' ',
            data: versionsData,
            size: '80%',
            innerSize: '60%',
            dataLabels: {
                formatter: function () {
                    // display only if larger than 1
                    return this.y > 1 ? '<b>' + this.point.name + ':</b> ' + this.y + '' : null;
                }
            }
        }]
    });
});
</script>


<script>
	$(document).ready(function() {
		(function() {
			$('#filter').keyup(function(){
				var rex = new RegExp($(this).val(), 'i');
				$('.searchable tr').hide();
				$('.searchable tr').filter(function(){
					return rex.test($(this).text());
				}).show();
			})
		}(jQuery));
	});
</script>


<!-- script de creation du Donut "Nombre de Vitoires" -->
<script>
	//Morris charts snippet - js
   	$.getScript('include/raphael-min.js', function() {
        $.getScript('include/morris.min.js', function() {

        	Morris.Donut({
            	element: 'donut-example',
                	data: [
                    	{label: "Nb Victoires", value: <?php echo $nbv?> },
                        {label: "Nb Défaites", value: <?php echo $nbd?> }
                    ]
            });
        });
    });</script>

<!-- script de creation du Donut "% Vitoires" -->
<script>
    //Morris charts snippet - js
   	$.getScript('include/raphael-min.js', function() {
        $.getScript('include/morris.min.js', function() {

        	Morris.Donut({
            	element: 'donut-pourcentage',
                	data: [
                    	{label: "% Victoires", value: <?php echo $nbpv?>  },
                        {label: "% Défaites", value: <?php echo $nbpd?> }
                        ]
           });
		});
	});
</script>

    
<!-- script de creation du Donut "Classements joués" -->

<script>
	//Morris charts snippet - js
   	$.getScript('include/raphael-min.js', function() {
        $.getScript('include/morris.min.js', function() {

			Morris.Donut({
				element: 'donut-classement',
					data: [
						<?php
							foreach ( $classement as $id_class => $value ) {
								echo "{label: \"clt $id_class\", value: $value  },";
							}
						?>
					]
			});
		});
	});
</script>

<?php
foreach ( $joueur as $joueurdetail ) {
	$classement = variant_int ( $joueurdetail ['point'] / 100 );
}
?>

<div class="container">

	<!-- AFFICHE DU JOUEUR -->
	<div class="col-md-6">
	<center>
			<div class="thumbnail" style="background-color: #FAFAFA">
				<img class="img-circle img-responsive img-center"
					src="./joueur/homme.jpg" alt="" width="50%">
				<div class="caption">
					<h3><?php echo $joueurdetail['prenom']. " ". $joueurdetail['nom']?></br>
					</h3>
					<ul class="list-unstyled">
						<li><strong>Licence : </strong> <?php echo $joueurdetail['licence']?></li>
						<li><strong>Catégorie:</strong> <?php echo $joueurdetail['categ']?>  </li>
						<li><strong>Points licence:</strong> <?php echo $joueurdetail['valinit']?>  </li>
					</ul>
				</div>
			</div>
			<div class="thumbnail" style="background-color: #FAFAFA">
				<h3>
					<u> Classement Officiel: <i> <?php echo $clt?></i></u>
				</h3>
				<div class="caption">
					<p>
						<strong>Actuellement</strong> <?php echo $joueurdetail['point']?> points</p>
					<p>
						<strong>Progression mensuelle</strong> <?php echo $joueurdetail['progmois']?> points</p>
					<p>
						<strong>Progression annuelle</strong> <?php echo $joueurdetail['progann']?> points</p>
					<p>
						<strong>Classement moyen des adversaires</strong> <?php echo variant_int($avgClt[0][0])?> points</p>
				</div>
			</div>

			<div class="thumbnail" style="background-color: #FAFAFA">
				<h3>
					<u> TOP VICTOIRES</i></u>
				</h3>
				<p align="center">
				<table class="sortable">
				<tr>
					<th class="text-center">adversaire</th>
					<th data-type="numeric" class="text-center">points adv</th>
					<th data-type="numeric" class="text-center">écart</th>
				</tr>
				<?php for($i = 0; $i < 5; ++$i) {?>
					<td><?php echo $sortPartieSpid[$i]['adversaire']?></td>
					<td data-type="numeric" class="text-center"><?php echo $sortPartieSpid[$i]['classement']?></td>
					<td data-type="numeric" class="text-center"><?php echo $sortPartieSpid[$i]['classement'] - $joueurdetail['valinit']?></td>
				</tr>
				<?php } ?>
				</table>
				</p>
			</div>
			<br /><br /><br />
			<h2>classement carriere</h2>
			<div id="container2"></div>
		</center>
	</div>

	<div class="col-md-6"><center>
				<h2>Stats rencontres</h2>

			<p>
				<h3>Matchs joués : <strong> <?php echo $nbv+$nbd?></strong>
			</p>
			<p>
				gagnés: <strong> <?php echo $nbv?> </strong> perdus: <strong>  <?php echo $nbd?></strong></h3>
			</p>
<!--  		<div id="donut-example" style="height: 250px;"></div>
			<h2>% Victoires</h2>
-->		<div id="donut-pourcentage" style="height: 250px; margin: auto"></div>
  		<div id="container3" style="width: 100%; margin: 0 auto"></div>
		<br />
		<h2>classement saison</h2>
		<div id="container4"></div>

<!--			<h2>Classements joués</h2>
		<div id="donut-classement" style="height: 250px;"></div>
-->	</div>
</center>
</div>

<center>
	<h3>Historique des parties</h3>
</center>


<p align="center">
<table class="sortable">
	<tr>
		<th class="text-center"><u>Date</u></th>
		<th class="text-center"><u>Victoire ou défaite</u></th>
		<th class="text-center"><u>Adversaire </u></th>
		<th data-type="numeric" class="text-center"><u>Classement </br>adversaire</u></th>
		<th data-type="numeric" class="text-center"><u>Points</br>gagnés</u></th>
		<th data-type="numeric" class="text-center"><u>Epreuves</u></th>
	</tr>

<?php
foreach ( $partieSpid as $partiedetail ) {
	?>
	<tr class="text-center <?php echo $partiedetail['victoire']?>">
		<td><?php echo $partiedetail['date']?></td>
		<td><?php
	if ($partiedetail ['victoire'] == 'V') {
		echo $partiedetail ['victoire'] . "  "?><img alt=""
			src="include/victoire.png"
			style="float: right; width: 30px; height: 30px;">
				<?php
	
}
	if ($partiedetail ['victoire'] == 'D') {
		echo $partiedetail ['victoire'] . "  "?><img alt=""
			src="include/defaite.jpg"
			style="float: right; width: 24px; height: 24px;">
				<?php }?> 
    </td>
		<td><?php echo $partiedetail['adversaire']?></td>
		<td data-type="numeric" class="text-center"><?php echo $partiedetail['classement']?></td>
		<?php 	if ($partiedetail['pointres'] > 0) {?>
			<td data-type="numeric" class="text-center"><font color="green"><strong><?php echo "+".$partiedetail['pointres'] ?> </strong></font></td>
			<?php }
		 	if ($partiedetail['pointres'] < 0) {?>
			<td data-type="numeric" class="text-center"><font color="red"><strong><?php echo $partiedetail['pointres'] ?> </strong></font></td>
			<?php }
		 	if ($partiedetail['pointres'] == 0 && $partiedetail['pointres'] != "") {?>
			<td data-type="numeric" class="text-center"><font color="black"><strong><?php echo $partiedetail['pointres'] ?> </strong></font></td>
			<?php }
		 	if ($partiedetail['pointres'] == "") {?>
			<td data-type="numeric" class="text-center"><font color="black"><strong>nc</strong></font></td>
			<?php }?>
		<td><?php echo $partiedetail['epreuve']?></td>
	</tr>
	<?php
}
?>
</table>
</p>

<script src="sorttable.js"></script>
