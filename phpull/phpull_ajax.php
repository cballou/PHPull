<?php
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

// create array to store parsed data
$function_data = array();

// only allow a valid POST AJAX request to hit this page
if (!IS_AJAX) {
	$function_data['fail'] = '1';
	echo json_encode($function_data);
	die;
}

// get the function and class name
$class 		= (isset($_POST['class']) && !empty($_POST['class'])) ? strtolower($_POST['class']) : null;
$function 	= strtolower($_POST['function']);

// store the function URL and an alternate URL in case first doesnt work
$function_data['link'] 		= function_url($function, $class);
$function_data['alt_link'] = function_url($function, $class, '.');

// load class
require_once dirname(__FILE__) . '/includes/simple_html_dom.php';

// create DOM object
$html = new simple_html_dom();

// get the function documentation
$res = $html->load_file($function_data['link']);
if ($res === false) {
	$res = $html->load_file($function_data['alt_link']);
	if ($res === false) {
		$function_data['fail'] = '1';
		echo json_encode($function_data);
		die;
	}
	$function_data['link'] = $function_data['alt_link'];
}

// get the function name
$function_data['function'] = $html->find('h1.refname', 0)->innertext;

// get the php version
$function_data['phpversion'] = $html->find('p.verinfo', 0)->innertext;

// get the short description
$function_data['short_desc'] = $html->find('p.refpurpose', 0)->find('span.dc-title', 0)->innertext;

// get the method call
$call_method = $html->find('div.methodsynopsis', 0)->plaintext;
$call_method = preg_replace('/\r?\n/m', '', $call_method);
$function_data['method'] = preg_replace('/\s\s+/', ' ', $call_method);

// get the long description which may be multiple paragraphs
$function_data['long_desc'] = '';
foreach ($html->find('div.description', 0)->find('p.para') as $description) {
	// strip any extra spaces and newlines
	$description = preg_replace('/\r?\n/m', '', $description->plaintext);
	$function_data['long_desc'] .= preg_replace('/\s\s+/', ' ', $description);
}
// trim spaces
$function_data['long_desc'] = trim($function_data['long_desc']);

// get function return values
$return_vals = $html->find('div.returnvalues', 0)->find('p.para', 0)->plaintext;
$return_vals = preg_replace('/\r?\n/m', '', $return_vals);
$function_data['return_vals'] = preg_replace('/\s\s+/', ' ', $return_vals);

// we no longer need DOM help
unset($html);

echo json_encode($function_data);
return true;

/**
 * Determines the URL based on the function name, class, and replacement
 * character.
 */
function function_url($function, $class = null, $replacement = '-') {

	// store an array of all US mirrors of the php website
	$urls = array('http://us.php.net/manual/en/',
				  'http://us2.php.net/manual/en/',
				  'http://us3.php.net/manual/en/',
				  'http://www.php.net/manual/en/');

	// grab a random url
	$url = $urls[array_rand($urls)];

	// the default replacement needs another string attached
	if ($replacement === '-') {
		$url .= 'function.';
	}

	// if class attribute is set and no special chars exist in function, concat class
	if (!is_null($class) && (strpos($function, '->') === false) && (strpos($function, '::') === false)) {
		$url .= $class . $replacement;
	}

	// replace following characters with replacement
	$function = str_replace(array('_','->','::'), $replacement, $function);

	// concat function and extension onto the url
	$url .= $function . '.php';

	return $url;
}
?>
