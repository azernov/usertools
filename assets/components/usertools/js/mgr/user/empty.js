/**
 * Loads the empty user page
 *
 * @class MODx.page.EmptyUser
 * @extends MODx.Component
 * @param {Object} config An object of config properties
 * @xtype modx-page-user-empty
 */
MODx.page.CsEmptyUser = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        buttons: [{
            text: _('cancel')
            ,id: 'modx-abtn-cancel'
            ,handler: function() {
                MODx.loadPage('user/index','namespace=carolesmokes')
            }
        }],
        components: [{
            xtype: 'ut-panel-emptyuser'
        }]
    });

    MODx.page.CsEmptyUser.superclass.constructor.call(this,config);
};
Ext.extend(MODx.page.CsEmptyUser,MODx.Component,{});
Ext.reg('ut-page-user-empty',MODx.page.CsEmptyUser);
