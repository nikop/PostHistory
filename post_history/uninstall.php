<?php

remove_integration_function('integrate_pre_include', '$sourcedir/PostHistory.php');
remove_integration_function('integrate_actions', 'PH_actions');
remove_integration_function('integrate_core_features', 'PH_core_features');
remove_integration_function('integrate_load_permissions', 'PH_load_permissions');

?>