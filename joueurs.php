<HEAD>
<meta charset="UTF-8">
<link rel="stylesheet" href="include/style.css" type="text/css" />
<script	src="include/jquery.min.js"></script>

</HEAD>

<body>

<?php
include_once ('include/SmartpingDAO.inc');

$dao = new SmartpingDAO ();

// GetjoueursClub

?>
	<p align="center">
	
	
	<table class="sortable oddeven">
		<tr>
			<th>licence</th>
			<th>prenom / Nom</th>
			<th>pts mensuels</th>
			<th>Cat</th>
			<th>Pts Licence</th>
			<th>Prog mensuelle</th>
			<th>Prog annuelle</th>
		</tr>

<?php
$joueur = $dao->getJoueurs ();
foreach ( $joueur as $joueurdetail ) {
	?>
  <tr data-href="joueurSpid.php?id=<?php echo $joueurdetail['licence']?>">
			<td><?php echo $joueurdetail['licence']?></td>
			<td><?php echo $joueurdetail['prenom']. " ". $joueurdetail['nom']?></td>
			<td><?php echo $joueurdetail['point']?></td>
			<td><?php echo $joueurdetail['categ']?></td>
			<td><?php echo $joueurdetail['valinit']?></td>
			<td><?php 
				if ($joueurdetail['progmois']>0) {
					echo $joueurdetail['progmois']."  " ?><img alt="" src="include/arrow_up2.png" style="float:right;width:24px;height:24px;">
				<?php }
				elseif ($joueurdetail['progmois']<0) {
					echo $joueurdetail['progmois']."  "?><img alt="" src="include/arrow_down2.png" style="float:right;width:24px;height:24px;"> 
				<?php }?> 
			</td>
			<td><?php 
				if ($joueurdetail['progann']>0) {
					echo $joueurdetail['progann']."  " ?><img alt="" src="include/arrow_up2.png" style="float:right;width:24px;height:24px;">
				<?php }
				elseif ($joueurdetail['progann']<0) {
					echo $joueurdetail['progann']."  "?><img alt="" src="include/arrow_down2.png" style="float:right;width:24px;height:24px;"> 
				<?php }?> 
			</td>
		</tr>
<?php
}
?>
</table>
</body>
</html>
<script>
$(document).ready(function(){
    $('table tr').click(function(){
    	<!--        window.open($(this).data('href'),'blank');
    	 -->
        window.location = $(this).data('href');
        return false;
    });
});
</script>
<script src="sorttable.js"></script>
<!-- 
 -->
