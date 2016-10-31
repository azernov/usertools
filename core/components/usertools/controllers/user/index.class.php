<?php
require_once MODX_MANAGER_PATH.'controllers/default/security/user/index.class.php';

/**
 * Loads the user list
 *
 * @package modx
 * @subpackage manager.controllers
 */
class UsertoolsUserIndexManagerController extends SecurityUserManagerController {

    public function checkPermissions()
    {
        return true;
    }


    /**
     * Register custom CSS/JS for the page
     * @return void
     */
    public function loadCustomCssJs() {
        $mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);
        $assetsUrl = MODX_ASSETS_URL.'components/usertools/';
        $this->addJavascript($assetsUrl.'js/mgr/usertools.component.js');
        $this->addJavascript($assetsUrl.'js/mgr/user/utuser.grid.js');
        $this->addJavascript($assetsUrl.'js/mgr/user/list.js');
        $this->addHtml("<script>
            utComponent.config.connector_url = '{$assetsUrl}connector.php';
            Ext.onReady(function() {
                MODx.add('ut-page-users');
            });</script>");
    }
}
