<?php
error_reporting(E_ALL);
require('sparqllib.php');

$endpoint = 'http://sparql.data.southampton.ac.uk/';

echo "Hello world\n";

$data = sparql_get($endpoint, "
SELECT DISTINCT ?g WHERE {
  GRAPH ?g {
    ?s ?p ?o .
  }
}");
$graphs = array();
foreach($data as $row)
{
	$graphs[] = $row['g'];
}
print_r($graphs);

foreach($graphs as $graph)
{
	echo $graph."\n";
	$data = sparql_get($endpoint, "
	SELECT * WHERE {
	  GRAPH <$graph> {
	    ?s ?p ?o .
	  }
	}");
	foreach($data as $row)
	{
		if($row['o.type'] == 'uri')
		{
		//	$uri = preg_replace('|^http://id.southampton.ac.uk/|', 'id:', $row['o']);
			print_r($row);
			@$incoming[$row['o']]++;
			@$total[$row['o']]++;
		}
	}
	foreach($data as $row)
	{
		if($row['s.type'] == 'uri')
		{
		//	$uri = preg_replace('|^http://id.southampton.ac.uk/|', 'id:', $row['s']);
			@$outgoing[$row['s']]++;
			@$total[$row['s']]++;
		}
	}
	echo count($outgoing)." ".count($incoming)."\n";
	//print_r($data);
}
echo "DONE";
file_put_contents('incoming.txt', serialize($incoming));
file_put_contents('outgoing.txt', serialize($outgoing));
file_put_contents('total.txt', serialize($total));
/*

$data = sparql_get($endpoint, "
PREFIX soton: <http://id.southampton.ac.uk/ns/>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX org: <http://www.w3.org/ns/org#>
PREFIX spacerel: <http://data.ordnancesurvey.co.uk/ontology/spatialrelations/>
PREFIX ep: <http://eprints.org/ontology/>
PREFIX dct: <http://purl.org/dc/terms/>
PREFIX bibo: <http://purl.org/ontology/bibo/>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
PREFIX oo: <http://purl.org/openorg/>

SELECT DISTINCT * WHERE {
  GRAPH ?g {
    {
      ?uri rdfs:label ?label .
    } UNION {
      ?uri foaf:name ?label .
    } UNION {
      ?uri rdfs:comment ?label .
    }
    FILTER regex(?label, \"murray\", \"i\")
  }
}
ORDER BY ?uri
");
print_r($data);
*/




/*
    OPTIONAL {
      ?uri a ?type .
    }
    OPTIONAL {
      {
        ?s ?p ?uri .
      } UNION {
        ?uri ?p ?o .
      }
    }
*/
?>
