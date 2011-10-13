<?php

// load Smarty library
require('Smarty.class.php');

$smarty = new Smarty;

$smarty->template_dir = 'smarty/templates';
$smarty->config_dir = ' smarty/config';
$smarty->cache_dir = 'c:/Program Files/PHP5/libs/Smarty-3.1.3/cache';
$smarty->compile_dir = 'c:/Program Files/PHP5/libs/Smarty-3.1.3/templates_c';

$smarty->assign('name','fish boy!');

$smarty->display('index.tpl');
?>