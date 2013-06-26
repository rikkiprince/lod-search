<?php

$f3=require('fatfree-master/lib/base.php');
$f3->route('GET /',
    function() {
        echo 'Hello, world!';
	echo "<h1>Open Data Explorer Demo</h1>";

	echo "<p><a href='search.php'>Search</a></p>";

    }
);


$f3->route('GET /search',
    function() {
        echo 'Donations go to a local charity... us!';
    }
);



$f3->run();

?>
