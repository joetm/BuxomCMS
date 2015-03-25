<?php

error_reporting(E_ALL | E_NOTICE | E_STRICT);

//Get the Processor and urls
$options['processor'] = Config::GetDBOptions('processor');

/*-----CONFIG-----*/

	/***config***/
	require_once Path::Get('path:site/signup').DIR_SEP. $options['processor'] . DIR_SEP . "_config.php";

	/***slider defaults***/
	$options['min_value'] = 14.95;
	$options['max_value'] = 44.95;
	$step = 0.5;

	$start_value = 29.95;

/*---END CONFIG---*/



$join_page_url = Path::Get('url:site/signup').'/'.$options['processor'];
$join_page = $join_page_url.'/join';


//if(Config::Get('debug') && !file_exists($join_page.'.tpl.html'))
//	$tpl->errormessage($join_page.'.tpl.html - Join Page not found.');



//currency and locale
//Zend_Currency automatically detects locale
//using Zend_Registry::get('Zend_Locale')
include_once Path::Get('path:site').'/includes/classes/Zend/Currency.php';
$currency = new Zend_Currency(
	array(
		'display' => Zend_Currency::USE_SHORTNAME,
	)
);
$currency = $currency->getShortName(); //ex.: 'USD'


//Zombaio only allows EUR or USD
//set the variables for the form here
switch ($currency)
{
	case 'USD':
	case 'EUR':
		$formurl = $formurl[$currency];
		$method = 'zombaio_'.strtolower($currency);
		break;
	default:
		//currency not found
		$currency = $default_currency;
		$formurl = $formurl['EUR'];
		$method = 'zombaio_eur';
	break;
}
//we now have the (hopefully) correct currency

//prefill all the slider values in an array (used in javascript)
$price_options = array();
for($i = 0, $s = ($options['max_value'] - $options['min_value'])/$step; $i < $s; $i++)
{
	$val = $options['min_value'] + ($i * $step);
	$price_options[] = array(
				'value' => $val,
				'hash'  => md5($gwpass . strval($val)),
				);
}


/***TEMPLATE ASSIGNMENTS***/

//currency
$tpl->assign('currency', $currency);

$tpl->assign('min_value', $options['min_value']);
$tpl->assign('max_value', $options['max_value']);
$tpl->assign('step', $step);

$tpl->assign('price_options', $price_options);

//zombaio variables
$tpl->assign('formurl', $formurl);
$tpl->assign('method', $method);
$tpl->assign('start_value', $start_value);
$tpl->assign('DynAmount_Value', $start_value);
$tpl->assign('DynAmount_Hash', md5($gwpass . strval($start_value) ));

//processor
$tpl->assign('options', $options);

if(isset($join_page_url)) $tpl->assign('join_page_url', $join_page_url);
if(isset($join_page)) $tpl->assign('join_page', $join_page);
