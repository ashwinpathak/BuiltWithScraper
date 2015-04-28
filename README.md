# What's the class about?
It's a simple class which scrapes [BuiltWith.com](http://builtwith.com) for website technology informations which they have gaterhed.

# Any example?
Here's a simple example:
```php
$scaper = new BuiltWithScraper('http://google.com');
$content = $data->getDetails();
print_r($content);
```
For a definative and better example which uses all methods in the Class, please check index.php file.

# Functions List
**BuiltWithScraper::isUp() [Scope: Public]** &mdash; Used to determine whether a provided website is UP or DOWN.

**BuiltWithScraper::getDetails() [Scope: Public]** &mdash; Returns an ***Array*** with the details scraped from [BuiltWith.com](http://builtwith.com).

**BuiltWithScraper::fetch_data($url) [Scope: Private]** &mdash; Used to get the raw HTML data of the website either by using ***cURL*** or by ***Fopen***.