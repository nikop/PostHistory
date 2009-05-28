<?php

if (!defined('SMF'))
{
	require_once(dirname(dirname(__FILE__)) . '/SSI.php');
	require_once(dirname(__FILE__) . '/Database.php');
	require_once(dirname(__FILE__) . '/Subs-Install.php');
}

global $txt, $smcFunc, $db_prefix, $modSettings;
global $addSettings, $permissions, $tables, $sourcedir;

// Step 1: Do tables
doTables($tables);

// Step 2: Do Settings
doSettings($addSettings);

// Step 3: Update admin features
updateAdminFeatures('posthistory', !empty($modSettings['posthistoryEnabled']));

?>