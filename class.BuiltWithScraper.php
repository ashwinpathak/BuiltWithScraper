<?php

/**
 * BuiltWithScraper
 * PHP Version 5
 * @package BuiltWithScraper
 * @link https://github.com/ashwinpathak/BuiltWithScraper
 * @author Ashwin Pathak <https://github.com/ashwinpathak>
 * @note I had got a project in which I had to make a very simple scraper,
 * I hope it comes in help to someone else.
 * And YES a big thanks to SimpleHTMLDOM, it made the work easy.
 */

Class BuiltWithScraper
{
    /**
     * Storing the URL passed through constructor
     * @type string
     * @access private
     */
    private $url;


    /**
     * Handling the HTML Data of BuiltWith.com
     * @type string
     * @access private
     */
    private $data;


    /**
     * Dependency injection, injecting URL and fetched HTML data
     * @access public
     * @param stirng $url The URL of which we want to get details of
     * @return void
     */
    public function __construct($url)
    {
        $this->url = htmlentities(trim($url), ENT_QUOTES, 'UTF-8'); // URL must be escaped & trimmed.
        $this->data = $this->fetch_data($this->url);
    }

    /**
     * Parses the HTML which is scraped using SimpleHTMLDOM
     * @access public
     * @return Array
     */
    public function getDetails()
    {
        $content = Array();

        // Including SimpleHTMLDOM lib
        require_once __DIR__ . '/shd-lib/shd.php';

        // Loading in the scraped HTML
        $html = new simple_html_dom();
        $html->load($this->data);

        $element = null;

        // Parsing elements
        foreach($html->find('.span8') as $block) { // as block

            $count = count($block->find('div.titleBox')); // counting total titles

            for($i = 0; $i < $count; $i++) {

                // Technology Title
                $content[$i]['title'] = $block->find('div.titleBox ul li.active span', $i)->plaintext;

                // Parsing Details of Technology
                for($j = $i; $j < count($block->find('div.techItem h3')); $j++) {

                    // If has used another technology in the same section then use it else move to next section
                    if(!isset($next))
                        $next = $block->find('div.titleBox', $i)->next_sibling();
                    else
                        $next = $next;

                    if($next->class == 'titleBox') {
                        $next = $next->next_sibling();
                        break;
                    }

                    if($next->class == 'techItem') {
                        $content[$i]['descr'][] = $next->find('h3', 0)->plaintext;
                        $content[$i]['detail'][] = $next->find('p', 0)->plaintext;
                        $next = $next->next_sibling();
                     
                        if(!isset($next->class) || ($next->class != 'titleBox' && $next->class != 'techItem'))
                            break;
                    }

                }
            }
        }

        // Array of scraped details
        return $content;
    }

    /** 
     * Checking for server to be UP or DOWN with 200 Header Code
     * @access public
     * @return boolean
     */
    public function isUp()
    {
        $ch = curl_init($this->url);

        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36');
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        curl_exec($ch);

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($status == 200)
            return true;

        return false;
    }

    /** 
     * Fetching Data using cURL (if exists) else using fopen
     * @access private
     * @return string|false If true then return fetched data or throw an exception
     */
    private function fetch_data($url = null)
    {
        if($url === null)
            throw new Exception('No URL provided.', 1);

        /**
         * Excluding http, https and www from the URL
         * Because builtwith only accepts URL in that way.
         * And the case of the URL is not sensitive.
         */
        $url = str_ireplace('http://', '', $url);
        $url = str_ireplace('https://', '', $url);
        $url = str_ireplace('www.', '', $url);

        $url = explode('/', $url);
        $url = $url[0];

        // Overlapping the old $url with the builtwith/url value.
        $url = 'http://builtwith.com/' . $url;

        $content = null;

        if(function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36');
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);

            $content = curl_exec($ch);

            if(!$content)
                throw new Exception(curl_error($ch), 1);
        }
        else if(function_exists('file_get_contents'))
            $content = file_get_contents($url);
        else
            throw new Exception('Fetcher requires cURL or fOpen PHP Extension.', 1);

        return $content;
    }
}


/**** Quick Example of Usage ****/

// $data = new BuiltWithScraper('https://google.com');
// $isUp = $data->isUp();

// if($isUp) {
//     $content = $data->getDetails();

//     if(isset($content[0])) {
//         echo '<pre>';
//         print_r($content);
//         echo '</pre>';
//     }
//     else
//         die('Seems like an invalid website or something, buildwith is not able to find any details.');
// }
// else
//     die('Server is down for the website URL you provided.');