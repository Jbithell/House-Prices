<?php
ini_set('memory_limit', '1G'); 
require_once "../easyrdf/EasyRdf.php";
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
		$query .= 'postcode: ( ' . 'NW10 1LG' . ' )';
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
PRINT_R($result);
?>