<?php

$f3=require('lib/base.php');

$f3->config('config.ini');


$f3->route('GET /',
	function ($f3) {
		echo Template::instance()->render('header.html');

		echo "<h1>search data.southampton.ac.uk</h1>";
		$f3->set('term', "murray");
		echo Template::instance()->render('searchForm.html');

		echo Template::instance()->render('footer.html');
	}
);


$f3->route('GET /preview/@url',
        function ($f3) {
                echo "<p>".$f3->get('PARAMS.url')." is what we're searching for.</p>";

                include_once("arc/ARC2.php");
                include_once("Graphite.php");

		$graph = new Graphite();
                $graph->load( "http://id.southampton.ac.uk/" );
                //print $graph->resource( "http://id.southampton.ac.uk/" )->get( "foaf:name" );
        }
);


$f3->route('GET /search',
	function($f3) {
		//echo "Enter a search term.";

		echo Template::instance()->render('header.html');

		$f3->set('term', "murray");
		echo Template::instance()->render('searchForm.html');

		echo Template::instance()->render('footer.html');
	}
);

$f3->route('GET /search?q=@term',
	function ($f3) {
		$term = $f3->get('PARAMS.term');
		$f3->reroute('/search/'.$term);
	}
);
$f3->route('GET /search/@term',
	function ($f3) {
		echo Template::instance()->render('header.html');

		$term = $f3->get('PARAMS.term');
		$f3->set('term', $term);
		echo Template::instance()->render('searchForm.html');
		echo "<p>".$term." is what we're searching for.</p>";

		// PUT CODE HERE
		include_once("load.php");

		foreach($founduris as $uri => $n)
		{
			$labels = array_unique($metadata[$uri]['labels']);
			sort($labels);
			$labels = implode(', ', $labels);
			echo "<li>";
			echo "<a href='$uri'>$labels</a> ";
			$types = array_unique($metadata[$uri]['types']);
			sort($types);
			$cleantypes = array();
			foreach($types as $type)
			{
				if(trim($type) != '')
				{
					$cleantypes[] = preg_replace("/(.*)[#\/](.*)/", "\$2", $type);
				}
			}
			$types = implode(', ', $cleantypes);
			echo $types;

			echo " <small> in dataset ";
			$graphs = array_unique($metadata[$uri]['graphs']);
			$gnames = array();
			foreach($graphs as $graph)
			{
				@$gnames[] = "<a href='".str_replace('/latest', '', $graph)."'>".$datasets[$graph]."</a>";
			}
			echo implode(', ', $gnames);
			echo "</small>";
			echo "</li>";
		}

		echo Template::instance()->render('footer.html');

	}
);


$f3->route('GET /sparql/@term',
	function ($f3) {
		echo Template::instance()->render('header.html');

		echo "<p>".$f3->get('PARAMS.term')." is what we're searching for.</p>";

		// Go and look at http://graphite.ecs.soton.ac.uk/sparqllib/

		require_once( "sparqllib.php" );

		$db = sparql_connect( "http://rdf.ecs.soton.ac.uk/sparql/" );
		if( !$db ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
		sparql_ns( "foaf","http://xmlns.com/foaf/0.1/" );

		//$sparql = "SELECT * WHERE { ?person a foaf:Person . ?person foaf:name ?name } LIMIT 5";
		//$sparql = "SELECT * WHERE { ?s ?p ?o } LIMIT 10";
		$sparql = "SELECT * WHERE { ?s ?p ?o } LIMIT 10";
		$result = sparql_query( $sparql );
		if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

		$fields = sparql_field_array( $result );

		print "<p>Number of rows: ".sparql_num_rows( $result )." results.</p>";
		print "<table class='example_table'>";
		print "<tr>";
		foreach( $fields as $field )
		{
		        print "<th>$field</th>";
		}
		print "</tr>";
		while( $row = sparql_fetch_array( $result ) )
		{
		        print "<tr>";
		        foreach( $fields as $field )
        		{
			        print "<td>$row[$field]</td>";
        		}
        		print "</tr>";
		}
		print "</table>";

		echo Template::instance()->render('footer.html');
	}
);

$f3->route('GET /about',
	function ($f3) {
		echo Template::instance()->render('header.html');

		echo "<p>Tuesday is the most popular day in data.southampton.ac.uk</p>";

		echo "<p>This project was knocked together for the Open Data Open Day hack day on Wednesday 26th June 2013.</p>";

		echo "<p>If you're interested in how it works, please check out <a href='https://github.com/rprince/lod-search'>the code on GitHub</a>!</p>";

		echo Template::instance()->render('footer.html');
	}
);
$f3->route('GET /contact',
	function ($f3) {
		echo Template::instance()->render('header.html');

		echo "<p>Worked on by <a href='http://www.crwilliams.co.uk/'>Colin Williams</a>, <a href='http://people.soton.ac.uk/amn1o07'>Biscuits Newton</a>, <a href='http://people.soton.ac.uk/ag27g12'>Andreas Galazis</a> and <a href='http://people.soton.ac.uk/rfp1n11'>Rikki Prince</a></p>";

		echo Template::instance()->render('footer.html');
	}
);
$f3->route('GET /template',
	function ($f3) {
		echo Template::instance()->render('header.html');
		echo Template::instance()->render('footer.html');
	}
);




$f3->run();
