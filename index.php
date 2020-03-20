<?php
date_default_timezone_set("Europe/London");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>House Price Search</title>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		<script src="autocomplete.js"></script>
		<script>
			function show(url) {
				$('#dataModal').modal('show');
				$("#loading").show();
				$("#results").hide();
				$("#ModalLabel").html('Results for ' + $('#autocomplete').val());
				$.ajax({url: url, error: function(xhr){
					$("#results").html("<center><i>Sorry - An unknown error occured</i></center>");
					$("#loading").hide();
					$("#results").show();
				}, success: function(result){
					$("#results").html(result);
					$("#loading").hide();
					$("#results").show();
				}});
			}
			var base = 'results.php';
			$(document).ready(function(){
				$("#propertysearchbutton").click(function(){
					var url = base + '?number=' + encodeURIComponent($('#street_number').val()) + '&street=' + encodeURIComponent($('#route').val()) + '&city=' + encodeURIComponent($('#locality').val()) + '&county=' + encodeURIComponent($('#administrative_area_level_1').val()) + '&postcode=' + encodeURIComponent($('#postal_code').val()) + '&country=United+Kingdom';
					show(url);
				});
				$("#streetsearchbutton").click(function(){
					var url = base + '?street=' + encodeURIComponent($('#route').val()) + '&city=' + encodeURIComponent($('#locality').val()) + '&county=' + encodeURIComponent($('#administrative_area_level_1').val()) + '&postcode=' + encodeURIComponent($('#postal_code').val()) + '&country=United+Kingdom';
					show(url);
				});
			});
		</script>
	</head>
	<body onload="initialize()">
		<div id="searchdiv">
			<img id="logo" alt="logo" src="logo.png"/>
			<br />
				<input type="hidden" id="street_number" name="number" /><!--Number-->
				<input type="hidden" id="route" name="street" /><!--Road-->
				<input type="hidden" id="locality" name="city" /><!--City-->
				<input type="hidden" id="administrative_area_level_1" name="county" /><!--County-->
				<input type="hidden" id="postal_code" name="postcode" /><!--Postcode-->
				<input type="hidden" id="country" name="country" /><!--Country-->
				<input type="search" id="autocomplete" name="raw" class="search"><br>
				<input type="submit" id="propertysearchbutton" class="button" name="property" value="Property Search">
				<input type="submit" id="streetsearchbutton"class="button" name="street" value="Street Search">
		</div>
		<footer>
			<i><a class="leftlinks" href="https://www.gov.uk/government/organisations/land-registry">Data provided by the Land Registry</a></i>
			<a class="rightlinks" href="https://www.jbithell.com">&copy;<?php echo date('Y'); ?> James Bithell</a>
			<a class="rightlinks" href="#" data-target="#aboutModal" data-toggle="modal">About</a>
		</footer>
		
		<!-- Results -->
		<!--<button type="button" class="btn btn-primary" data-target="#dataModal" data-toggle="modal">Large modal</button>-->
		<div class="modal fade bs-example-modal-lg" tabindex="-1" id="dataModal" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="ModalLabel">Results for </h4>
					</div>
					<div class="modal-body">
						<div id="results"></div>
						<div id="loading"><center><img src="loading.gif" style="width: 40px;" alt="Loading..." /></center></div>
					</div>
				</div>
			</div>
		</div>
		<!--About Section-->
		<div class="modal fade" tabindex="-1" role="dialog" id="aboutModal" aria-labelledby="AboutLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="AboutLabel">About</h4>
				</div>
				<div class="modal-body">
					<p>Created by James Bithell in May 2015</p>
					<div class="panel-group" id="accordion">
						<div class="panel panel-default">
						  <div class="panel-heading">
							<h4 class="panel-title">
							  <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Data</a>
							</h4>
						  </div>
						  <div id="collapse1" class="panel-collapse collapse in">
							<div class="panel-body">
								<p>Data provided by the UK Land Registry data service.</p>
								<p>The Land Registry registers the ownership of property. It is one of the largest property databases in Europe. This tool searches the live registry database and displays the results on this site</p>
								<p><b>Disclaimer: </b>I am in no way connected with the Land Registry and I do not own the data provided. Data provided is &copy;Crown Copyright, use on this site licensed through <a href="//www.nationalarchives.gov.uk/doc/open-government-licence/">Open Government Data Licence</a>.</p>
							</div>
						  </div>
						</div>
						<div class="panel panel-default">
						  <div class="panel-heading">
							<h4 class="panel-title">
							  <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Theme</a>
							</h4>
						  </div>
						  <div id="collapse2" class="panel-collapse collapse">
							<div class="panel-body">Theme is based on that of the <a href="//google.co.uk">Google Search project</a> and uses twitter bootstrap for its popup boxes. Maps are provided by Google. </div>
						  </div>
						</div>
						<div class="panel panel-default">
						  <div class="panel-heading">
							<h4 class="panel-title">
							  <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Contact</a>
							</h4>
						  </div>
						  <div id="collapse3" class="panel-collapse collapse">
							<div class="panel-body">
								Please send all enquiries to <a href="mailto:webcontact@jbithell.com">webcontact@jbithell.com</a> or use the form on <a href="//jbithell.com">my website</a>
							</div>
						  </div>
						</div>
					  </div> 
				</div>
			</div>
			</div>
		</div>
	</body>
</html>

