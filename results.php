<?php
/*SETTINGS*/
$maps = false; //Show maps next to the resutls
/*END SETTINGS*/

$output = '';
if (!isset($_GET['number']) or empty($_GET['number'])) {
	//Street Search
	$property = false;
}
elseif (isset($_GET['property'])) {
	//Property Search
	$property = true;
} elseif (isset($_GET['street'])) {
	//Street Search
	$property = false;
} else {
	//Default to property search
	$property = true;
}
if ($property) $number = $_GET['number'];
$street = $_GET['street'];
$city = $_GET['city'];
$postcode = $_GET['postcode'];

//Download DATA
require_once "easyrdf/EasyRdf.php";
EasyRdf_Namespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
EasyRdf_Namespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
EasyRdf_Namespace::set('owl', 'http://www.w3.org/2002/07/owl#');
EasyRdf_Namespace::set('xsd', 'http://www.w3.org/2001/XMLSchema#');
EasyRdf_Namespace::set('lrhpi', 'http://landregistry.data.gov.uk/def/hpi/');
EasyRdf_Namespace::set('lrppi', 'http://landregistry.data.gov.uk/def/ppi/');
EasyRdf_Namespace::set('skos', 'http://www.w3.org/2004/02/skos/core#');
EasyRdf_Namespace::set('lrcommon', 'http://landregistry.data.gov.uk/def/common/');
EasyRdf_Namespace::set('sr', 'http://data.ordnancesurvey.co.uk/ontology/spatialrelations/');
$sparql = new EasyRdf_Sparql_Client('http://landregistry.data.gov.uk/landregistry/query');
$query = 'PREFIX  text: <http://jena.apache.org/text#>
	PREFIX  ppd:  <http://landregistry.data.gov.uk/def/ppi/>
	PREFIX  lrcommon: <http://landregistry.data.gov.uk/def/common/>

	SELECT  ?item ?ppd_propertyAddress ?ppd_hasTransaction ?ppd_pricePaid ?ppd_publishDate ?ppd_transactionDate ?ppd_transactionId ?ppd_estateType ?ppd_newBuild ?ppd_propertyAddressCounty ?ppd_propertyAddressDistrict ?ppd_propertyAddressLocality ?ppd_propertyAddressPaon ?ppd_propertyAddressPostcode ?ppd_propertyAddressSaon ?ppd_propertyAddressStreet ?ppd_propertyAddressTown ?ppd_propertyType ?ppd_recordStatus
	WHERE
	  { ?ppd_propertyAddress text:query _:b0 .
		_:b0 <http://www.w3.org/1999/02/22-rdf-syntax-ns#first> "';
		if ($property and !empty($number)) $query .= 'paon: ( ' . $number . ' )  AND ';
		if (!empty($street)) $query .= 'street: ( ' . $street . ' )  AND ';
		if (!empty($city)) $query .= 'town: ( ' . $city . ' ) AND ';
		if (!empty($postcode)) $query .= 'postcode: ( ' . $postcode . ' )';
		$query .= '" .
		_:b0 <http://www.w3.org/1999/02/22-rdf-syntax-ns#rest> _:b1 .
		_:b1 <http://www.w3.org/1999/02/22-rdf-syntax-ns#first> 3000000 .
		_:b1 <http://www.w3.org/1999/02/22-rdf-syntax-ns#rest> <http://www.w3.org/1999/02/22-rdf-syntax-ns#nil> .
		?item ppd:propertyAddress ?ppd_propertyAddress .
		?item ppd:hasTransaction ?ppd_hasTransaction .
		?item ppd:pricePaid ?ppd_pricePaid .
		?item ppd:publishDate ?ppd_publishDate .
		?item ppd:transactionDate ?ppd_transactionDate .
		?item ppd:transactionId ?ppd_transactionId
		OPTIONAL
		  { ?item ppd:estateType ?ppd_estateType }
		OPTIONAL
		  { ?item ppd:newBuild ?ppd_newBuild }
		OPTIONAL
		  { ?ppd_propertyAddress lrcommon:county ?ppd_propertyAddressCounty }
		OPTIONAL
		  { ?ppd_propertyAddress lrcommon:district ?ppd_propertyAddressDistrict }
		OPTIONAL
		  { ?ppd_propertyAddress lrcommon:locality ?ppd_propertyAddressLocality }
		OPTIONAL
		  { ?ppd_propertyAddress lrcommon:paon ?ppd_propertyAddressPaon }
		OPTIONAL
		  { ?ppd_propertyAddress lrcommon:postcode ?ppd_propertyAddressPostcode }
		OPTIONAL
		  { ?ppd_propertyAddress lrcommon:saon ?ppd_propertyAddressSaon }
		OPTIONAL
		  { ?ppd_propertyAddress lrcommon:street ?ppd_propertyAddressStreet }
		OPTIONAL
		  { ?ppd_propertyAddress lrcommon:town ?ppd_propertyAddressTown }
		OPTIONAL
		  { ?item ppd:propertyType ?ppd_propertyType }
		OPTIONAL
		  { ?item ppd:recordStatus ?ppd_recordStatus }
	  }
	  ORDER BY ASC(?ppd_propertyAddressPaon)
	  ';
//die($query);
try {
	$result = $sparql->query($query);
} catch(Exception $e) {
	die('<center><i>No Results</i></center>');
}
//$output .= $row->ppd_propertyAddressPaon . ' ' . $row->ppd_propertyAddressStreet . "<br/>" . $row->ppd_propertyAddressTown . "<br/>" . $row->ppd_propertyAddressDistrict . ', ' . $row->ppd_propertyAddressCounty . '<br/>' . $row->ppd_propertyAddressPostcode;
date_default_timezone_set("Europe/London");
$last = '';
$results = array();
$prices = array();
if ($result->numRows() > 0) {
	//$output .= '<h2 class="lead"><strong class="text-danger">' . $result->numRows() . '</strong> results were found for the search for <strong class="text-danger">' . (!empty($number) ? $number : null) . ' ' . (!empty($street) ? $street : null) . ', ' . (!empty($city) ? $city . ', ' : null) . (!empty($postcode) ? $postcode : null) . '</strong></h2>';
	foreach ($result as $row) {
		$current = $row->ppd_propertyAddressPaon . ' ' . $row->ppd_propertyAddressStreet;
		if ($current != $last) {
			if ($last != '') $output .= '</table></div><span class="clearfix borda"></span></article>';//Close previous table tag
			$output .= '<article class="search-result row">';
			if ($maps) {
				$output .= '<div class="col-lg-7">
					<image src="//maps.googleapis.com/maps/api/staticmap?size=500x200&zoom=15&scale=2&markers=' . urlencode((!empty($row->ppd_propertyAddressPaon) ? $row->ppd_propertyAddressPaon : null) . ' ' . (!empty($row->ppd_propertyAddressStreet) ? $row->ppd_propertyAddressStreet : null) . ', ' . (!empty($row->ppd_propertyAddressTown) ? $row->ppd_propertyAddressTown . ', ' : null) . (!empty($row->ppd_propertyAddressPostcode) ? $row->ppd_propertyAddressPostcode . ', ' : null) . 'United Kingdom') . '" style="width:100%;" alt="Map" />
					<!--<iframe style="width:100%;" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=' . urlencode((!empty($row->ppd_propertyAddressPaon) ? $row->ppd_propertyAddressPaon : null) . ' ' . (!empty($row->ppd_propertyAddressStreet) ? $row->ppd_propertyAddressStreet : null) . ', ' . (!empty($row->ppd_propertyAddressTown) ? $row->ppd_propertyAddressTown . ', ' : null) . (!empty($row->ppd_propertyAddressPostcode) ? $row->ppd_propertyAddressPostcode . ', ' : null) . 'United Kingdom') . '&key=AIzaSyBN6u3hPvgIOiUlv6L3OwpQpwKLTRU3Psg"></iframe>-->
				</div>';
			}
			$output .= '<div class="col-lg-' . ($maps ? '5' : '12') . '">
				<h3>' . ucwords(strtolower($row->ppd_propertyAddressPaon)) . ' ' . ucwords(strtolower($row->ppd_propertyAddressStreet)) . '</h3>';
				$output .= '<table border="0" style="width: 100%; border-spacing: 10px;">';
		}
		$output .= '<tr style="width: 100%;"><td style="width: 100%;">';
		$output .= date("l j\<\s\u\p\>S\<\/\s\u\p\> \of F Y",strtotime($row->ppd_transactionDate));
		$output .= '</td><td>&pound;';
		$output .= number_format(intval((string)$row->ppd_pricePaid));
		array_push($prices,(intval((string)$row->ppd_pricePaid)));
		$output .= '</td></tr>';
		
		$last = $row->ppd_propertyAddressPaon . ' ' . $row->ppd_propertyAddressStreet;
	}
	$output .= '		</table>	</div>
			<span class="clearfix borda"></span>
		</article>';
}	else $output .= '<i>No Results</i>';

echo '<b>Average Price: </b>&pound;' . number_format(array_sum($prices)/count($prices));
echo $output;
?>