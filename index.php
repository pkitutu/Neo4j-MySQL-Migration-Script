<?php
require('vendor/autoload.php');
require('rb.php');

R::setup( 'mysql:host=localhost;dbname=chai-crm','chai-crm', '6TWe9K7NPzT6jNDP' );
//R::freeze( TRUE );
$client = new Everyman\Neo4j\Client();
$client->getTransport()->setAuth('neo4j', 'neo4j');


// Get all nodes : AKA Tables
$nodeNames = array();
foreach ($client->GetLabels() as $label) {
	$node = $label->getName();
	if ($node[0] != "_") {
		$nodeNames[] = $node;
	}
}

// Get each nodes properties
foreach ($nodeNames as $nodeName) {
	$queryString = "MATCH (n:`" . $nodeName . "`) return count(n) as count";
	$query = new Everyman\Neo4j\Cypher\Query($client, $queryString);
	$result = $query->getResultSet();

	$count = 0;
	foreach ($result as $row) {
		$count = $row["count"];
	}
	echo "Total Count for $nodeName: $count \n";

	$storedRecords = 0;
	while($storedRecords < $count){
		$queryString = "MATCH (n:`". $nodeName ."`) RETURN n skip 200 limit 200";
		$query = new Everyman\Neo4j\Cypher\Query($client, $queryString);
		$result = $query->getResultSet();

		foreach ($result as $row) {
			saveMysql($row, $nodeName);
			$storedRecords++;
		}
	}
}

// Create MySQL database tables
function saveMysql($node, $nodeName){
	$record = R::dispense(strtolower($nodeName));
	$properties = $node["n"]->getProperties();
	foreach ($properties as $key=>$property) {
		if($key != "wkt"){
			$record[$key] = $property;
		}
		R::store($record);
	}
}

