<?php
include('weather.class.php');


/* Config Section */

$zip = "USCA1093";					// Input your zip or country code (Stanford)

//$zip = "USCA0830";						// Input your zip or country code (Palo Alto)

define('DEFAULT_UNITS', "f");			// f=Fahrenheit, c=Celsius

define('IMAGES', 'icons/lg/');			// Input your icon folder location

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
	color: #565347;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
		}
	</style>
</head>
<body>

	

<?php
 {
	echo "<img style=\"vertical-align: middle\" src=\"".$weather['image']."\" alt=\"\" />&nbsp;&nbsp;<strong>".$weather['temp']."</strong>\n";
}
?>

<!--<p>Stanford, California<p> -->

</body>
</html>
