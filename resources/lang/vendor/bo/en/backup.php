<?php
// --------------------------------------------------------
// This is only a pointer file, not an actual language file
// --------------------------------------------------------
//
// If you've copied this file to your /resources/lang/vendor/bo/
// folder, please delete it, it's no use there. You need to copy/publish the
// actual language file, from the package.
// If a langfile with the same name exists in the package, load that one
if (file_exists(core_package_path("backupmanager/src/resources/lang/".basename(__DIR__)."/".basename(__FILE__)))) {
    return include core_package_path("backupmanager/src/resources/lang/".basename(__DIR__)."/".basename(__FILE__));
}

return [];
