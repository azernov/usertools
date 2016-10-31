/**
 * Loads the update user page
 *
 * @class MODx.page.UpdateUser
 * @extends MODx.Component
 * @param {Object} config An object of config properties
 * @xtype modx-page-user-update
 */
MODx.page.CsUpdateUser = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		formpanel: 'ut-panel-user'
		,actions: {
			'new': 'user/create'
			,edit: 'user/update'
			,cancel: 'user/index'
		}
		,buttons: [{
			process: 'mgr/user/update'
			,text: _('save')
			,id: 'modx-abtn-save'
			,cls: 'primary-button'
			,method: 'remote'
			// ,checkDirty: true
			,keys: [{
				key: MODx.config.keymap_save || 's'
				,ctrl: true
			}]
		},{
			text: _('cancel')
			,id: 'modx-abtn-cancel'
			,handler: function() {
				MODx.loadPage('user/index','namespace=carolesmokes')
			}
		}/*,{
			text: _('delete')
			,id: 'modx-abtn-delete'
			,handler: this.removeUser
			,scope: this
		},{
			text: _('help_ex')
			,id: 'modx-abtn-help'
			,handler: MODx.loadHelpPane
		}*/]
		,components: [{
			xtype: 'ut-panel-user'
			,renderTo: 'modx-panel-user-div'
			,user: config.user
			,userType: config.userType
			,remoteFields: config.remoteFields
			,extendedFields: config.extendedFields
			,name: ''
		}]
	});
	/*this.saveProduct = function(btn,e){
		MODx.Ajax.request({
			url: utComponent.config.connector_url,
			params: {
				action: 'mgr/user/update',
				id: this.record.id
			},
			listeners: ''
		});
	};*/

	MODx.page.CsUpdateUser.superclass.constructor.call(this,config);
};
Ext.extend(MODx.page.CsUpdateUser,MODx.Component,{
	removeUser: function(btn,e) {
		MODx.msg.confirm({
			title: _('user_remove')
			,text: _('user_confirm_remove')
			,url: MODx.config.connector_url
			,params: {
				action: 'security/user/delete'
				,id: this.config.user
			}
			,listeners: {
				'success': {fn:function(r) {
					MODx.loadPage('user/index');
				},scope:this}
			}
		});
	}
});
Ext.reg('ut-page-user-update',MODx.page.CsUpdateUser);
