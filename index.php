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

$nodes = array();
// Get each nodes properties
foreach ($nodeNames as $node) {
	$queryString = "MATCH (n:`". $node ."`) RETURN n LIMIT 25";
	$query = new Everyman\Neo4j\Cypher\Query($client, $queryString);
	$result = $query->getResultSet();

	$records = array();
	foreach ($result as $row) {
		$records[] = $row["n"]->getProperties();
	}
	$nodes[$node] = $records;
}

// Create MySQL database tables

foreach ($nodes as $nodeName => $records) {
	try {
		foreach ($records as $properties) {
			$node = R::dispense(strtolower($nodeName));
			foreach ($properties as $key=>$property) {
				if($key != "wkt"){
					$node[$key] = $property;
				}
			}
			R::store($node);
		}
	} catch (Exception $ex) {
		echo $nodeName . "\n";
		print_r($nodes[$nodeName]);
	}
}