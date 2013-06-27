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
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>

SELECT DISTINCT ?graph_uri ?dataset_uri ?label WHERE {
  GRAPH ?graph_uri {
    ?x ?y ?z .
  }
  ?dataset_uri rdf:type <http://www.w3.org/ns/dcat#Dataset> .
  ?dataset_uri <http://rdfs.org/ns/void#dataDump> ?graph_uri .
  ?dataset_uri rdfs:label ?label .
} ORDER BY ?graph_uri");

foreach($data2 as $row)
{
	@$datasetnames[$row['graph_uri']] = $row['label'];
	@$dataseturis[$row['graph_uri']] = $row['dataset_uri'];
}

$rankings = unserialize(file_get_contents('total.txt'));
foreach($data as $row)
{
	@$founduris[$row['uri']] = $rankings[$row['uri']];
}
unset($rankings);

$ds = unserialize(file_get_contents('datasets.txt'));
foreach($data as $row)
{
	@$metadata[$row['uri']]['graphs'] = $ds[$row['uri']];
}
unset($ds);

foreach($data as $row)
{
	@$metadata[$row['uri']]['types'][] = $row['t'];
	@$metadata[$row['uri']]['labels'][] = $row['label'];
}


arsort($founduris);
//print_r($founduris);
?>
