<?php
define('WEATHER_FILE', 'weather'); // prefix for caching, <zip>.xml

class weather {
	var $data;
	var $saved;
	var $metric;

	function getWeather($zip, $units='c')
    {
        // setup the units
        $this->metric = (strtoupper($units) == 'C');
        if($this->metric) {$units = array('temp' => 'C', 'distance' => 'km', 'measure' => 'mb', 'speed' => 'kph');}
        else {$units = array('temp' => 'F', 'distance' => 'mi', 'measure' => 'in', 'speed' => 'mph');}
        $pdir = array('steady', 'rising', 'falling');
        $wdir = array('N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N');
        
        // get the feed contents from file unless older than 1 hour
        $file = WEATHER_FILE . '.' . $zip . '.xml';
        if(!file_exists($file) || filemtime($file) < time() - 3600) {
            $this->data = @file_get_contents('http://xml.weather.yahoo.com/forecastrss?p=' . $zip . '&u=' . $units['temp']);
            $fp = @fopen($file, 'w');
            @fwrite($fp, $this->data);
            @fclose($fd);
        }
        else $this->data = @file_get_contents($file);
        if(strlen($this->data) <= 0) return;
        
        // get the units of the saved file
        $this->saved = explode('"', $this->tag('yweather:units'));
        $this->saved = (strtoupper($this->saved[1]) == 'C');
        
        $return = array();
        
        // get the location
        $attr = explode('"', $this->tag('yweather:location'));
        $return['location'] = $attr[1].', '.$attr[3].', '.$attr[5];
        
        // get the wind data
        $attr = explode('"', $this->tag('yweather:wind'));
        $return['windchill'] = $this->convert($attr[1], 'temp').'&deg;'.$units['temp'];
        $return['wind'] = $this->convert($attr[5], 'speed').' '.$units['speed'].' '.$wdir[round($attr[3]/45)];
        
        // get the atmosphere data
        $attr = explode('"', $this->tag('yweather:atmosphere'));
        $return['humidity'] = $attr[1].'%';
        $return['visibility'] = ($this->convert($attr[3], 'distance') / 100).' '.$units['distance'];
        $return['preasure'] = $this->convert($attr[5], 'measure').' '.$units['measure'].' '.$pdir[$attr[7]];
        
        $attr = explode('"', $this->tag('yweather:astronomy'));
        $return['sunrise'] = $attr[1];
        $return['sunset'] = $attr[3];
        
        // get the temperature data
        $attr = explode('"', $this->tag('yweather:condition'));
        $return['text'] = $attr[1];
        $return['temp'] = $this->convert($attr[5], 'temp').'&deg;'.$units['temp'];
        $return['image'] = IMAGES.$this->translate($attr[3], 'temp').'.png';
        
        // get the two forecasts
        $return['forecast'] = array();
        for($i = 0; $i < 2; $i++) {
            $attr = explode('"', $this->tag('yweather:forecast',$i));
            if(count($attr) > 1) {
                $day = array();
                $day['when'] = $attr[1];
                $day['low'] = $this->convert($attr[5], 'temp').'&deg;'.$units['temp'];
                $day['high'] = $this->convert($attr[7], 'temp').'&deg;'.$units['temp'];
                $day['text'] = $attr[9];
                $day['image'] = IMAGES.$this->translate($attr[11]).'.png';
                array_push($return['forecast'], $day);
            }
        }
        return $return;
    }
    
    function tag($tag, $skip=0) {
        $start = -1;
        for($i = 0; $i <= $skip; $i++)
            $start = strpos($this->data, "<{$tag}", $start + 1);
        if($start === false) return false;
        $start += strlen($tag) + 1;
        $end = strpos($this->data, "</{$tag}>", $start);
        if($end === false)
            $end = strpos($this->data, '/>', $start);
        return trim(substr($this->data, $start, $end - $start));
    }
    
    function convert($value, $type) {
        switch($type) {
            case 'temp': // Celsius or Farenheit
                if($this->saved == $this->metric) return $value;
                if($this->saved) return number_format($value * 1.8 + 32, 0);
                return number_format(($value - 32) / 1.8, 0);
            case 'speed':    // kilometers per hour or miles per hour
            case 'distance': // kilometers or miles
                if($this->saved == $this->metric) return $value;
                if($this->saved) return number_format($value * 0.621371192, 0);
                return number_format($value * 1.609344, 0);
            case 'measure': // millibars or inches
                if($this->saved == $this->metric) return $value;
                if($this->saved) return number_format($value * 0.0295301, 2);
                return number_format($value * 33.8637526, 0);
		}
	}

	function translate($code) {
		$time = date('G');
		$night = ($time <= 5 || $time >= 20); // night is between 8 pm and 6 am
		switch($code) {
			case 0: //tornado
			case 3: //severe thunderstorms
			case 4: //thunderstorms
			case 37: //isolated thunderstorms
			case 38: //scattered thunderstorms
			case 39: return "ThunderStorm"; //scattered thunderstorms
			case 1: //tropical storm
			case 2: return "WindyRain"; //hurricane
			case 5: //mixed rain and snow
			case 16: //snow
			case 41: //heavy snow
			case 42: //scattered snow showers
			case 43: return "Snow"; //heavy snow
			case 6: //mixed rain and sleet
			case 17: //hail
			case 18: return "Sleet"; //sleet
			case 7: return "IcyFrozenSnow"; //mixed snow and sleet
			case 8: return "IcyDrizzle"; //freezing drizzle
			case 9: return "Drizzle"; //drizzle
			case 10: //freezing rain
			case 35: return "IcyRain"; //mixed rain and hail
			case 11: //showers
			case 12: //showers
			case 40: return "Showers"; //scattered showers
			case 13: return "LightSnow";
			case 14: return "MedSnow";
			case 15: return "WindySnow";
			case 19: return "Dust";
			case 20: return "Fog";
			case 21: return "Haze";
			case 22: return "Smoke";
			case 23:
			case 24: return "Wind";
			//case 25: return "Frigid"; //doesn't exist
			case 26: return "Clouds";
			case 27: return "MostlyCloudyNight";
			case 28: return "MostlyCloudyDay";
			case 29: return "PartlyCloudyNight";
			case 30: return "PartlyCloudyDay";
			case 31: return "Moon";
			case 32: return "Sun";
			case 33: return "FairNight";
			case 34: return "FairDay";
			case 36: return "Hot";
			case 44:
				if($night) return "PartlyCloudyNight";
				else return "PartlyCloudyDay";
			case 45:
			case 47:
			     if($night) return "NightThunderStorm";
			     else return "SunnyThunderStorm";
			case 46:
				if($night) return "NightSnow";
				else return "Snow";
			default: return "Unknown";
		}
	}
}
?>
