<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script>
<link href="include/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="include/style.css" type="text/css" />
 
<?php
//<link rel="stylesheet" href="include/style.css" type="text/css" />

include_once ('include/SmartpingDAO.inc');

$dao = new SmartpingDAO();

// Get Historique
$histo = $dao->getHistorique($_GET['id']);
$label = "''";
$data = "null";
foreach ($histo as $value) {
	$label = $label.",'".$value['saison']." - ".$value['phase']."'";
	$data = $data.",".$value['points'];
}

$joueur = $dao->getJoueurDetails($_GET['id']);

foreach ($joueur as $joueurdetail) {
	$classement = variant_int($joueurdetail['point']/100);
	?>
		
	<div class="page-header"><center><h2>Statistiques</h2></center></div>
	    <!-- AFFICHE DU JOUEUR -->
        <div class="row">
			<div class="col-md-6">
            	<center><div class="thumbnail" style="background-color:#FAFAFA">
                	<img class="img-circle img-responsive img-center" src="./joueur/homme.jpg"  alt="" width="50%">
                    	<div class="caption">
                        	<h3><?php echo $joueurdetail['prenom']. " ". $joueurdetail['nom']?></br></h3>
                                <ul class="list-unstyled">
                                    <li><strong>Licence : </strong> <?php echo $joueurdetail['licence']?></li>
                                    <li><strong>Cat�gorie:</strong> <?php echo $joueurdetail['categ']?>  </li>
                                    <li><strong>Points licence:</strong> <?php echo $joueurdetail['valinit']?>  </li>
                                </ul>
                            </div>
                        </div>
                    </center>
                </div>

                <div class="col-md-6">
                    <div class="thumbnail"  style="background-color:#FAFAFA">
                        <center>
                            <h3><u> Classement Officiel:   <i> <?php echo $classement?></i></u></h3>

                            <div class="caption">
                                <p> <strong>Actuellement</strong> <?php echo $joueurdetail['point']?> points</p>
                                <p> <strong>Progression mensuelle</strong> <?php echo $joueurdetail['progmois']?> points</p>
                                <p> <strong>Progression annuelle</strong> <?php echo $joueurdetail['progann']?> points</p>
                        </center>
                    </div>
                </div>
            </div>
<?php 
}
?>

<div class="container">
	<script type="text/javascript">
    	$(document).ready(function() {

        $('#container2').highcharts({
                title: {
                text: '<?php echo $joueurdetail['prenom']. " ". $joueurdetail['nom']?>',
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
                                margin: 55
                        },
                                alternateGridColor: '#fafafb'
                        },
                        series: [{
                        name: 'Evolutions classement',
                                color: '#e96111',
                            data: [<?php echo $data?>] }]


                });
                });</script>

<?php 
$nbv = 0;
$nbd = 0;

$partie = $dao->getPartieParJoueurSpid($_GET['id']);

$classement = array(5 => 0);
$id_class = 5;
//	echo "type : ".gettype($id_class)."<br>";
foreach ($partie as $cle=>$partiedetail) {
	$id_class = (int)variant_int($partiedetail['classement']/100);
//	echo "type : ".gettype($id_class)."<br>";
	if (array_key_exists($id_class, $classement)) {
		$classement[$id_class] = $classement[$id_class]+1;
	}
	else {
		$classement[$id_class] = 1;
	}
	if ($partiedetail['victoire'] == 'V') {	$nbv = $nbv + 1; }
	elseif($partiedetail['victoire'] == 'D') { $nbd = $nbd + 1;	}
}
$nbt = $nbv + $nbd;
if ($nbt == 0) { $nbt = 1;}
$nbpv = variant_int($nbv*100/$nbt);
$nbpd = 100 - $nbpv;
ksort($classement, SORT_NUMERIC);

//$classement = $dao->getClassementAdversaires($_GET['id']);
/*echo "<pre>";
 print_r($classement);
 echo "</pre>";
*/?>

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
            });</script>
    <script>
	//Morris charts snippet - js
	$.getScript('http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js', function() {
        $.getScript('http://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.0/morris.min.js', function() {

        	Morris.Donut({
            	element: 'donut-example',
                	data: [
                    	{label: "Nb Victoires", value: <?php echo $nbv?> },
                        {label: "Nb D�faites", value: <?php echo $nbd?> }
                    ]
            });
        });
    });</script>
    
    <script>
    //Morris charts snippet - js

    	$.getScript('http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js', function() {
        $.getScript('http://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.0/morris.min.js', function() {

        	Morris.Donut({
            	element: 'donut-pourcentage',
                	data: [
                    	{label: "% Victoires", value: <?php echo $nbpv?>  },
                        {label: "% D�faites", value: <?php echo $nbpd?> }
                        ]
           });
		});
        });</script>

                    <script>
                                //Morris charts snippet - js

                                $.getScript('http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js', function() {
                                $.getScript('http://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.0/morris.min.js', function() {

                                Morris.Donut({
                                element: 'donut-classement',
                                        data: [
                                        <?php 
                                        foreach ($classement as $id_class => $value){
                                        	echo "{label: \"clt $id_class\", value: $value  },";
                                       	}
                                        ?>
                                        ]

                                });
                                });
                                });</script>



               <div class="row"><center><h3>Statistiques sur les joueurs jou�s</h3></center>
 
          <div class="col-md-4"> <center>
                    <p> <u>Matchs jou�s :</u> <strong> <?php echo $nbv+$nbd?></strong> </p>
                    <p><u> Matchs gagn�s: </u>  <strong> <?php echo $nbv?> </strong>
                    <u>   Matchs perdus: </u> <strong>  <?php echo $nbd?></strong></p>
                    <div id="donut-example" style="height: 250px;"></div></center>
		</div>
		<div class="col-md-4"><center><h3 >% Pourcentages V/D</h3>
                <div id="donut-pourcentage" style="height: 250px;"></div></center>
        </div>
		<div class="col-md-4">
                <center>
       	    <h3 >Classements jou�s</h3>
              <div id="donut-classement" style="height: 250px;"></div></center>
 		</div>
</div>       
<br/><br/>
       <center><h2>Evolution du classement</h2></center>
        <div id="container2"  ></div>
  
  <center><h3>Historique des parties</h3></center>
  
<p align="center">
<table class="sortable">
  <tr>
	<th class="text-center"><u>Victoire ou d�faite</u></th>
    <th class="text-center"><u>Date</u></th>
    <th class="text-center"><u>Adversaire </u></th>
    <th data-type="numeric" class="text-center"><u>Classement adversaire</u></th>
    <th data-type="numeric" class="text-center"><u>Epreuves</u></th>  </tr>

<?php 
foreach ($partie as $cle=>$partiedetail) {
	?>
	<tr class="text-center <?php echo $partiedetail['victoire']?>">
    <td><?php echo $partiedetail['victoire']?></td>
    <td><?php echo $partiedetail['date']?></td>
    <td><?php echo $partiedetail['adversaire']?></td>
    <td data-type="numeric" class="text-center"><?php echo $partiedetail['classement']?></td>
    <td><?php echo $partiedetail['epreuve']?></td>
	  </tr>
	<?php 	
}
?>
</table>
  
 <script src="sorttable.js"></script>
  