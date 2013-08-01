<?php
include('weather.class.php');


/* Config Section */

$zip = "BRXX0201";						// Input your zip or country code

define('DEFAULT_UNITS', "c");			// f=Fahrenheit, c=Celsius

define('IMAGES', 'icons/sm/');			// Input your icon folder location

/* End Config Section */


if($zip != '')
{
    if (isset($_GET['units'])) {$s_unit_of_measure = strtolower($_GET['units']);}
    else {$s_unit_of_measure = DEFAULT_UNITS;}
    $weather = new Weather();
    $weather = $weather->getWeather($zip, $s_unit_of_measure);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title>Weather for <?php echo $weather['location'] ?></title>
	<style type="text/css">
        body {
			color: #000;
			font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
			font-size: 11px;
		}
	</style>
</head>
<body>

	<h4>Weather for <?php echo $weather['location'] ?></h4>

<?php
 {
	echo "<table style=\"vertical-align:top\">\n";
	echo "\t<tr style=\"vertical-align:top\">\n";
	echo "\t\t<td style=\"padding-right: 16px\">\n";
	echo "\t\t\tNow<br/><strong>".$weather['temp']."</strong><br/>Windchill: <strong>".$weather['windchill']."</strong><br/>\n";
	echo "\t\t\t".$weather['text']."<br/><img src=\"".$weather['image']."\" alt=\"\" />\n";
	echo "\t\t</td><td style=\"padding-right: 16px\">\n";
	echo "\t\t\tLocation: ".$weather['location']."<br/>\n";
    echo "\t\t\tWind: ".$weather['wind']."<br/>\n";
    echo "\t\t\tHumidity: ".$weather['humidity']."<br/>\n";
    echo "\t\t\tVisibility: ".$weather['visibility']."<br/>\n";
    echo "\t\t\tPressure: ".$weather['preasure']."<br/>\n";
    echo "\t\t\tSunrise: ".$weather['sunrise']."<br/>\n";
    echo "\t\t\tSunset: ".$weather['sunset']."\n";
	echo "\t\t</td>\n";
	echo "\t\t<td style=\"padding-right: 16px\">\n";
	echo "\t\t\t".$weather['forecast'][0]['when']."<br/>\n";
	echo "\t\t\tHi: <strong>".$weather['forecast'][0]['high']."</strong><br/>Low: <strong>".$weather['forecast'][0]['low']."</strong><br/>\n";
	echo "\t\t\t".$weather['forecast'][0]['text']."<br/><img src=\"".$weather['forecast'][0]['image']."\" alt=\"\" />\n";
	echo "\t\t</td><td>\n";
	echo "\t\t\t".$weather['forecast'][1]['when']."<br/>\n";
	echo "\t\t\tHi: <strong>".$weather['forecast'][1]['high']."</strong><br/>Low: <strong>".$weather['forecast'][1]['low']."</strong><br/>\n";
	echo "\t\t\t".$weather['forecast'][1]['text']."<br/><img src=\"".$weather['forecast'][1]['image']."\" alt=\"\" />\n";
	echo "\t\t</td>\n";
	echo "\t</tr>\n";
	echo "</table>\n";
}
?>

</body>
</html>
