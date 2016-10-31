<?php
require_once MODX_MANAGER_PATH.'controllers/default/security/user/update.class.php';

class UsertoolsUserUpdateManagerController extends SecurityUserUpdateManagerController {
    /* @var utUser $user */
    public $user;

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

        $this->addHtml('<script type="text/javascript">
// <![CDATA[
MODx.onUserFormRender = "'.$this->onUserFormRender.'";
// ]]>
</script>');

        /* register JS scripts */
        $this->addJavascript($mgrUrl.'assets/modext/widgets/core/modx.orm.js');
        $this->addJavascript($mgrUrl.'assets/modext/widgets/core/modx.grid.settings.js');
        $this->addJavascript($mgrUrl.'assets/modext/widgets/security/modx.grid.user.settings.js');
        $this->addJavascript($mgrUrl.'assets/modext/widgets/security/modx.grid.user.group.js');
        $this->addJavascript($mgrUrl.'assets/modext/widgets/security/modx.panel.user.js');
        $this->addJavascript($assetsUrl.'js/mgr/usertools.component.js');
        $this->addJavascript($assetsUrl.'js/mgr/user/utuser.panel.js');
        $this->addJavascript($assetsUrl.'js/mgr/user/update.js');
        $this->addJavascript($assetsUrl.'js/mgr/user/utuser.empty.js');
        $this->addJavascript($assetsUrl.'js/mgr/user/empty.js');

        if($this->user->class_key !== 'utUser'){
            $this->addHtml('<script type="text/javascript">
utComponent.config.connector_url = "'.$assetsUrl.'connector.php";
// <![CDATA[
Ext.onReady(function() {

    MODx.load({
        xtype: "ut-page-user-empty"
    });
});
// ]]>
</script>');
            return;
        }
        $this->addHtml('<script type="text/javascript">
utComponent.config.connector_url = "'.$assetsUrl.'connector.php";
// <![CDATA[
Ext.onReady(function() {

    MODx.load({
        xtype: "ut-page-user-update"
        ,user: "'.$this->user->get('id').'"
        ,userType: "'.$this->user->Data->get('type').'"
        '.(!empty($this->remoteFields) ? ',remoteFields: '.$this->modx->toJSON($this->remoteFields) : '').'
        '.(!empty($this->extendedFields) ? ',extendedFields: '.$this->modx->toJSON($this->extendedFields) : '').'
    });
});
// ]]>
</script>');
    }
}
