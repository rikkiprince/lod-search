<?php
error_reporting(E_ALL);
require('sparqllib.php');

$endpoint = 'http://sparql.data.southampton.ac.uk/';

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
			@$incoming[$row['o']]++;
			@$total[$row['o']]++;
		}
	}
	foreach($data as $row)
	{
		if($row['s.type'] == 'uri')
		{
			@$outgoing[$row['s']]++;
			@$total[$row['s']]++;
		}
	}
	echo count($outgoing)." ".count($incoming)."\n";
}
echo "DONE";
file_put_contents('../htdocs/incoming.txt', serialize($incoming));
file_put_contents('../htdocs/outgoing.txt', serialize($outgoing));
file_put_contents('../htdocs/total.txt', serialize($total));
}
?>
