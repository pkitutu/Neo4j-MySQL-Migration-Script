<?php
require('vendor/autoload.php');

$client = new Everyman\Neo4j\Client();
$client->getTransport()->setAuth('neo4j', 'neo4j');

$nodes = array();
// Get customer count
$queryString = "MATCH (n:`Task`) return count(n) as count";
$query = new Everyman\Neo4j\Cypher\Query($client, $queryString);
$result = $query->getResultSet();

$count = 0;
foreach ($result as $row) {
	$count = $row["count"];
}
echo $count . "\n";
// Create MySQL database tables

$seen_records = 0;
$page = 1;
while ($seen_records <= $count) {
	echo "Going to page: " . $page . "\n";
	$queryString = "MATCH (n:`Task`) RETURN n skip 200 LIMIT 200";
	$query = new Everyman\Neo4j\Cypher\Query($client, $queryString);
	$result = $query->getResultSet();

	foreach ($result as $customer) {
		$lat = $customer["n"]->getProperty("lat");
		if((float)$lat > 4.9468821){
			echo $lat . "\n";
		}
	}

	$seen_records += 200;
	$page++;
}