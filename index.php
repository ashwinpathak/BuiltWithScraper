<?php

// Initializing the Class
require_once __DIR__ . '/class.BuiltWithScraper.php';

$scaper = new BuiltWithScraper('http://google.com');

// Using the class
if($scaper->isUp()) {
    $content = $scaper->getDetails();

    if(isset($content[0])) {
        for($i = 0; $i < count($content); $i++) {

            echo '<h2>' . $content[$i]['title'] . '</h2>';

            for($j = 0; $j < count($content[$i]['descr']); $j++)
                echo '<p><b>' . $content[$i]['descr'][$j] . ': </b>' . $content[$i]['detail'][$j] . '</p>';

            echo '<hr>';

        }
    }
    else
        echo 'Looks like builtwith is not able to get information.';
}
else
    echo 'Seems like the server is down of the website URL you have entered.';