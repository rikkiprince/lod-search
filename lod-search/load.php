<?php
error_reporting(E_ALL);
require('sparqllib.php');

$endpoint = 'http://sparql.data.southampton.ac.uk/';

echo "Hello world\n";

$data = sparql_get($endpoint, "
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>

SELECT * WHERE {
    {
      ?uri rdfs:label ?label .
    } UNION {
      ?uri foaf:name ?label .
    } UNION {
      ?uri rdfs:comment ?label .
    }
    FILTER regex(?label, 'murray', 'i')
}
ORDER BY ?uri");

$rankings = unserialize(file_get_contents('total.txt'));

foreach($data as $row)
{
	$founduris[$row['uri']] = $rankings[$row['uri']];
}

asort($founduris);
print_r($founduris);
?>
