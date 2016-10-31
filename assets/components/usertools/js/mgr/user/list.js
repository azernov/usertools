/**
 * Loads the users page
 *
 * @class MODx.page.Users
 * @extends MODx.Component
 * @param {Object} config An object of config properties
 * @xtype modx-page-users
 */
MODx.page.CsUsers = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		components: [{
			xtype: 'ut-panel-users'
		}]
		,buttons: [{
			text: _('help_ex')
			,id: 'modx-abtn-help'
			,handler: MODx.loadHelpPane
		}]
	});
	MODx.page.CsUsers.superclass.constructor.call(this,config);
};
Ext.extend(MODx.page.CsUsers,MODx.Component);
Ext.reg('ut-page-users',MODx.page.CsUsers);
