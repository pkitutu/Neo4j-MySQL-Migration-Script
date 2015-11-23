<?php
require('vendor/autoload.php');

$client = new Everyman\Neo4j\Client();
$client->getTransport()->setAuth('neo4j', 'neo4j');

$nodes = array();
// Get customer count
$queryString = "MATCH (n:`SalesCall` { status: 'new' }) OPTIONAL MATCH n-[r]-() DELETE n,r;";
$query = new Everyman\Neo4j\Cypher\Query($client, $queryString);
$result = $query->getResultSet();

print_r($result);