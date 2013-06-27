<?php
error_reporting(E_ALL);
require('sparqllib.php');

$endpoint = 'http://sparql.data.southampton.ac.uk/';

$data = sparql_get($endpoint, "
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>

SELECT * WHERE {
  GRAPH ?g {
    {
      ?uri rdfs:label ?label .
    } UNION {
      ?uri foaf:name ?label .
    } UNION {
      ?uri rdfs:comment ?label .
    }
    OPTIONAL {
      ?uri a ?t .
    }
    FILTER regex(?label, '$term', 'i')
  }
}
ORDER BY ?uri");

$data2 = sparql_get($endpoint, "
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX dc: <http://purl.org/dc/terms/>

SELECT distinct * WHERE{

 ?dataset_uri rdf:type <http://www.w3.org/ns/dcat#Dataset> .
 ?dataset_uri dc:title ?dataset_name .
 ?dataset_uri rdfs:label ?dataset_label
}");

foreach($data2 as $row)
{
	@$datasets[$row['dataset_uri'].'/latest'] = $row['dataset_name'];
}

$rankings = unserialize(file_get_contents('total.txt'));

foreach($data as $row)
{
	@$founduris[$row['uri']] = $rankings[$row['uri']];
	@$metadata[$row['uri']]['types'][] = $row['t'];
	@$metadata[$row['uri']]['graphs'][] = $row['g'];
	@$metadata[$row['uri']]['labels'][] = $row['label'];
}

unset($rankings);

arsort($founduris);
//print_r($founduris);
?>
