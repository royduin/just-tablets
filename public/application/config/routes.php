<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] 					= "home";
$route['404_override'] 							= '';

$route['tablet-kiezen']							= 'choose';

$route['tablet-nieuws/(:num)/(:any)']			= 'news/index/$1';
$route['tablet-nieuws/(:num)']					= 'news/index/$1';
$route['tablet-nieuws']							= 'news';
$route['tablet-nieuws/pagina']					= 'news';
$route['tablet-nieuws/pagina/(:num)']			= 'news/index/pagina/$1';

$route['tablet']								= 'product';
$route['tablet/(:num)']							= 'product/index/$1';
$route['tablet/(:num)/(:any)']					= 'product/index/$1';

$route['tablets-vergelijken'] 					= 'category';
$route['tablets-vergelijken/pagina'] 			= 'category';
$route['tablets-vergelijken/pagina/(:num)'] 	= 'category/index/pagina/$1';

$route['tablets-vergelijken/(:any)']			= 'compare/index/$1';

$route['zoeken/(:any)']							= 'search/index/$1';
$route['zoeken']								= 'search';

$route['([a-z-]+)-tablets']						= 'page/brand/$1';


// Old!
$route['tablet-merk/(:num)']					= 'redirect/brand/$1';
$route['tablet-merk/(:num)/(:any)']				= 'redirect/brand/$1';


/* End of file routes.php */
/* Location: ./application/config/routes.php */