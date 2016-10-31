/**
 * @class MODx.panel.Cs
 * @extends MODx.FormPanel
 * @param {Object} config An object of configuration properties
 * @xtype modx-panel-user
 */
MODx.panel.CsUser = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		url: utComponent.config.connector_url
		,baseParams: {
			namespace: 'carolesmokes'
		}
		,id: 'ut-panel-user'
		,cls: 'container'
		,defaults: { collapsible: false ,autoHeight: true }
		,bodyStyle: ''
		,items: [{
			html: '<h2>'+_('user_new')+'</h2>'
			,border: false
			,cls: 'modx-page-header'
			,id: 'modx-user-header'
		},{
			xtype: 'modx-tabs'
			,id: 'modx-user-tabs'
			,deferredRender: false
			,defaults: {
				autoHeight: true
				,layout: 'form'
				,labelWidth: 150
				,bodyCssClass: 'tab-panel-wrapper'
				,layoutOnTabChange: true
			}
			,items: this.getFields(config)
		}]
		,useLoadingMask: true
		,listeners: {
			'setup': {fn:this.setup,scope:this}
			,'success': {fn:this.success,scope:this}
			,'beforeSubmit': {fn:this.beforeSubmit,scope:this}
		}
	});
	MODx.panel.CsUser.superclass.constructor.call(this,config);
	Ext.getCmp('modx-user-panel-newpassword').getEl().dom.style.display = 'none';
	Ext.getCmp('modx-user-password-genmethod-s').on('check',this.showNewPassword,this);
};
Ext.extend(MODx.panel.CsUser,MODx.FormPanel,{
	setup: function() {
		if (this.config.user === '' || this.config.user === 0) {
			this.fireEvent('ready');
			return false;
		}
		MODx.Ajax.request({
			url: this.config.url
			,params: {
				action: 'mgr/user/get'
				,id: this.config.user
				,getGroups: true
			}
			,listeners: {
				'success': {fn:function(r) {
					this.getForm().setValues(r.object);

					Ext.get('modx-user-header').update('<h2>'+_('user')+': '+r.object.username+'</h2>');
					this.fireEvent('ready',r.object);
					MODx.fireEvent('ready');
				},scope:this}
			}
		});
	}
	,beforeSubmit: function(o) {
		var d = {};
		/*var g = Ext.getCmp('modx-grid-user-settings');
		if (g) { d.settings = g.encodeModified(); }*/

		/*var h = Ext.getCmp('modx-grid-user-groups');
		if (h) { d.groups = h.encode(); }*/

		/*var t = Ext.getCmp('modx-remote-tree');
		if (t) { d.remote_data = t.encode(); }*/

		/*var et = Ext.getCmp('modx-extended-tree');
		if (et) { d.extended = et.encode(); }*/

		Ext.apply(o.form.baseParams,d);
	}

	,success: function(o) {
		var userId = this.config.user;
		if (Ext.getCmp('modx-user-passwordnotifymethod-s').getValue() === true && o.result.message != '') {
			Ext.Msg.hide();
			Ext.Msg.show({
				title: _('password_notification')
				,msg: o.result.message
				,buttons: Ext.Msg.OK
				,fn: function(btn) {
					if (userId == 0) {
						MODx.loadPage('user/index', 'id='+o.result.object.id+'namespace=carolesmokes');
					}
					return false;
				}
			});
			this.clearDirty();
		} else if (userId == 0) {
			MODx.loadPage('user/index', 'id='+o.result.object.id+'namespace=carolesmokes');
		}
	}

	,showNewPassword: function(cb,v) {
		var el = Ext.getCmp('modx-user-panel-newpassword').getEl();
		if (v) {
			el.slideIn('t',{useDisplay:true});
		} else {
			el.slideOut('t',{useDisplay:true});
		}
	}

	,getFields: function(config) {
		var f = [{
			title: _('general_information')
			,defaults: { msgTarget: 'side' ,autoHeight: true }
			,cls: 'main-wrapper form-with-labels'
			,labelAlign: 'top' // prevent default class of x-form-label-left
			,items: config.userType == 'fl' ? this.getFlFields(config) : this.getUlFields(config)
		}];

		/*f.push({
			title: _('access_permissions')
			,layout: 'form'
			,defaults: { border: false ,autoHeight: true }
			,hideMode: 'offsets'
			,items: [{
				html: _('access_permissions_user_message')
				,bodyCssClass: 'panel-desc'
			},{
				xtype: 'modx-grid-user-groups'
				,cls: 'main-wrapper'
				,title: ''
				,preventRender: true
				,user: config.user
				,width: '97%'
				,listeners: {
					'afterRemoveRow':{fn:this.markDirty,scope:this}
					,'afterUpdateRole':{fn:this.markDirty,scope:this}
					,'afterAddGroup':{fn:this.markDirty,scope:this}
					,'afterReorderGroup':{fn:this.markDirty,scope:this}
				}
			}]
		});*/
		if (config.remoteFields && config.remoteFields.length) {
			f.push({
				title: _('remote_data')
				,layout: 'form'
				,defaults: { border: false ,autoHeight: true }
				,hideMode: 'offsets'
				,items: [{
					html: '<p>'+_('user_remote_data_msg')+'</p>'
					,bodyCssClass: 'panel-desc'
				},{
					layout: 'column'
					,cls: 'main-wrapper'
					,items: [{
						columnWidth: 0.4
						,title: _('attributes')
						,layout: 'fit'
						,border: false
						,items: {
							xtype: 'modx-orm-tree'
							,id: 'modx-remote-tree'
							,data: config.remoteFields
							,formPanel: 'modx-panel-user'
							,prefix: 'remote'
						}
					},{
						xtype: 'modx-orm-form'
						,columnWidth: 0.6
						,title: _('editing_form')
						,id: 'modx-remote-form'
						,prefix: 'remote'
						,treePanel: 'modx-remote-tree'
						,formPanel: 'modx-panel-user'
					}]
				}]
			});
		}
		config.extendedFields = config.extendedFields || [];
		/*f.push({
			title: _('extended_fields')
			,layout: 'form'
			,defaults: { border: false ,autoHeight: true }
			,hideMode: 'offsets'
			,items: [{
				html: '<p>'+_('extended_fields_msg')+'</p>'
				,bodyCssClass: 'panel-desc'
			},{
				layout: 'column'
				,cls: 'main-wrapper'
				,items: [{
					columnWidth: 0.4
					,title: _('attributes')
					,layout: 'fit'
					,border: false
					,items: {
						xtype: 'modx-orm-tree'
						,id: 'modx-extended-tree'
						,data: config.extendedFields
						,formPanel: 'modx-panel-user'
						,prefix: 'extended'
						,enableDD: true
						,listeners: {
							'dragdrop': {fn:function() {
								this.markDirty();
							},scope:this}
						}
					}
				},{
					xtype: 'modx-orm-form'
					,columnWidth: 0.6
					,title: _('editing_form')
					,id: 'modx-extended-form'
					,prefix: 'extended'
					,treePanel: 'modx-extended-tree'
					,formPanel: 'modx-panel-user'
				}]
			}]
		});*/
		return f;
	}

	,getUlFields: function(config){
		return [{
			layout: 'column'
			,border: false
			,defaults: {
				layout: 'form'
				,labelAlign: 'top'
				,labelSeparator: ''
				,anchor: '100%'
				,border: false
			}
			,items: [{
				columnWidth: .5
				,defaults: {
					msgTarget: 'under'
				}
				,items: [{
					id: 'modx-user-id'
					,name: 'id'
					,xtype: 'hidden'
					,value: config.user
				},{
					id: 'modx-user-username'
					,name: 'username'
					,fieldLabel: _('username')
					,description: _('user_username_desc')
					,xtype: 'textfield'
					,anchor: '100%'
				},{
					id: 'modx-user-fullname'
					,name: 'fullname'
					,fieldLabel: _('user_full_name')
					,xtype: 'textfield'
					,anchor: '100%'
					,maxLength: 255
				},{
					layout: 'column'
					,defaults: {
						layout: 'form'
						,labelAlign: 'top'
						,labelSeparator: ''
						,anchor: '100%'
						,border: false
					}
					,items: [{
						columnWidth:.5
						,defaults: {
							msgTarget: 'under'
						}
						,items:[{
							id: 'modx-user-firstname'
							,name: 'firstname'
							,fieldLabel: 'Имя'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-lastname'
							,name: 'lastname'
							,fieldLabel: 'Фамилия'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-company'
							,name: 'company'
							,fieldLabel: 'Компания'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-kpp'
							,name: 'kpp'
							,fieldLabel: 'КПП'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-director_fullname'
							,name: 'director_fullname'
							,fieldLabel: 'ФИО директора'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						}/*,{
							id: 'modx-user-discount'
							,name: 'discount'
							,fieldLabel: 'Текущая скидка %'
							,xtype: 'numberfield'
							,anchor: '100%'
							,maxLength: 255
						}*/]
					},{
						columnWidth:.5
						,defaults: {
							msgTarget: 'under'
						}
						,items:[{
							id: 'modx-user-middlename'
							,name: 'middlename'
							,fieldLabel: 'Отчество'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-inn'
							,name: 'inn'
							,fieldLabel: 'ИНН'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-position'
							,name: 'position'
							,fieldLabel: 'Должность'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-type'
							,name: 'type'
							,fieldLabel: 'Тип лица'
							,xtype: 'ut-combo-user-type'
							,anchor: '100%'
							,maxLength: 255
						}/*,{
							id: 'modx-user-max_discount'
							,name: 'max_discount'
							,fieldLabel: 'Максимальная скидка %'
							,xtype: 'numberfield'
							,anchor: '100%'
							,maxLength: 255
						}*/]
					}]
				},{
					id: 'modx-user-email'
					,name: 'email'
					,fieldLabel: _('user_email')
					,xtype: 'textfield'
					,anchor: '100%'
					,maxLength: 255
					,allowBlank: false
				},{
					id: 'modx-user-website'
					,name: 'website'
					,fieldLabel: _('user_website')
					,xtype: 'textfield'
					,anchor: '100%'
					,maxLength: 255
				},{
					id: 'modx-user-phone'
					,name: 'phone'
					,fieldLabel: _('user_phone')
					,xtype: 'textfield'
					,width: 200
					,maxLength: 255
				},{
					id: 'modx-user-mobilephone'
					,name: 'mobilephone'
					,fieldLabel: _('user_mobile')
					,xtype: 'textfield'
					,width: 200
					,maxLength: 255
				},{
					id: 'modx-user-city'
					,name: 'city'
					,fieldLabel: _('city')
					,xtype: 'textfield'
					,anchor: '100%'
					,maxLength: 255
				},{
					id: 'modx-user-address'
					,name: 'address'
					,fieldLabel: _('address')
					,xtype: 'textarea'
					,anchor: '100%'
					,grow: true
				}]
			},{
				columnWidth: .5
				,defaults: {
					msgTarget: 'under'
				}
				,items: [{
					id: 'modx-user-newpassword'
					,name: 'newpassword'
					,xtype: 'hidden'
					,value: false
				},{
					id: 'modx-user-primary-group'
					,name: 'primary_group'
					,xtype: 'hidden'
				},{
					id: 'modx-user-active'
					,name: 'active'
					,hideLabel: true
					,boxLabel: _('active')
					,description: _('user_active_desc')
					,xtype: 'xcheckbox'
					,inputValue: 1
				},{
					id: 'modx-user-blocked'
					,name: 'blocked'
					,hideLabel: true
					,boxLabel: _('user_block')
					,description: _('user_block_desc')
					,xtype: 'xcheckbox'
					,inputValue: 1
				},{
					id: 'modx-user-blockeduntil'
					,name: 'blockeduntil'
					,fieldLabel: _('user_blockeduntil')
					,description: _('user_blockeduntil_desc')
					,xtype: 'xdatetime'
					,width: 300
					,timeWidth: 150
					,dateWidth: 150
					,allowBlank: true
					,dateFormat: MODx.config.manager_date_format
					,timeFormat: MODx.config.manager_time_format
					,hiddenFormat: 'Y-m-d H:i:s'
				},{
					id: 'modx-user-blockedafter'
					,name: 'blockedafter'
					,fieldLabel: _('user_blockedafter')
					,description: _('user_blockedafter_desc')
					,xtype: 'xdatetime'
					,width: 300
					,timeWidth: 150
					,dateWidth: 150
					,allowBlank: true
					,dateFormat: MODx.config.manager_date_format
					,timeFormat: MODx.config.manager_time_format
					,hiddenFormat: 'Y-m-d H:i:s'
				},{
					id: 'modx-user-logincount'
					,name: 'logincount'
					,fieldLabel: _('user_logincount')
					,description: _('user_logincount_desc')
					,xtype: 'statictextfield'
				},{
					id: 'modx-user-thislogin'
					,name: 'thislogin'
					,fieldLabel: _('user_prevlogin')
					,description: _('user_prevlogin_desc')
					,xtype: 'statictextfield'
				},{
					id: 'modx-user-failedlogincount'
					,name: 'failedlogincount'
					,fieldLabel: _('user_failedlogincount')
					,description: _('user_failedlogincount_desc')
					,xtype: 'textfield'
				},{
					id: 'modx-user-comment'
					,name: 'comment'
					,fieldLabel: _('comment')
					,xtype: 'textarea'
					,anchor: '100%'
					,grow: true
				},{
					id: 'modx-user-fs-newpassword'
					,title: _('password_new')
					,xtype: 'fieldset'
					,cls: 'x-fieldset-checkbox-toggle' // add a custom class for checkbox replacement
					,checkboxToggle: true
					,collapsed: (config.user ? true : false)
					,forceLayout: true
					,listeners: {
						'expand': {fn:function(p) {
							Ext.getCmp('modx-user-newpassword').setValue(true);
							this.markDirty();
						},scope:this}
						,'collapse': {fn:function(p) {
							Ext.getCmp('modx-user-newpassword').setValue(false);
							this.markDirty();
						},scope:this}
					}
					,items: [{
						xtype: 'radiogroup'
						,fieldLabel: _('password_method')
						,columns: 1
						,items: [{
							id: 'modx-user-passwordnotifymethod-e'
							,name: 'passwordnotifymethod'
							,boxLabel: _('password_method_email')
							,xtype: 'radio'
							,value: 'e'
							,inputValue: 'e'
						},{
							id: 'modx-user-passwordnotifymethod-s'
							,name: 'passwordnotifymethod'
							,boxLabel: _('password_method_screen')
							,xtype: 'radio'
							,value: 's'
							,inputValue: 's'
							,checked: true
						}]
					},{
						xtype: 'radiogroup'
						,fieldLabel: _('password_gen_method')
						,columns: 1
						,items: [{
							id: 'modx-user-password-genmethod-g'
							,name: 'passwordgenmethod'
							,boxLabel: _('password_gen_gen')
							,xtype: 'radio'
							,inputValue: 'g'
							,value: 'g'
							,checked: true
						},{
							id: 'modx-user-password-genmethod-s'
							,name: 'passwordgenmethod'
							,boxLabel: _('password_gen_specify')
							,xtype: 'radio'
							,inputValue: 'spec'
							,value: 'spec'
						}]
					},{
						id: 'modx-user-panel-newpassword'
						,xtype: 'panel'
						,layout: 'form'
						,border: false
						,autoHeight: true
						,style: 'padding-top: 15px' // nested form, add padding-top as the label will not have it
						,items: [{
							id: 'modx-user-specifiedpassword'
							,name: 'specifiedpassword'
							,fieldLabel: _('change_password_new')
							,xtype: 'textfield'
							,inputType: 'password'
							,anchor: '100%'
						},{
							id: 'modx-user-confirmpassword'
							,name: 'confirmpassword'
							,fieldLabel: _('change_password_confirm')
							,xtype: 'textfield'
							,inputType: 'password'
							,anchor: '100%'
						}]
					}]
				}]
			}]
		},{
			html: MODx.onUserFormRender
			,border: false
		}];
	}

	,getFlFields: function(config){
		return [{
			layout: 'column'
			,border: false
			,defaults: {
				layout: 'form'
				,labelAlign: 'top'
				,labelSeparator: ''
				,anchor: '100%'
				,border: false
			}
			,items: [{
				columnWidth: .5
				,defaults: {
					msgTarget: 'under'
				}
				,items: [{
					id: 'modx-user-id'
					,name: 'id'
					,xtype: 'hidden'
					,value: config.user
				},{
					id: 'modx-user-username'
					,name: 'username'
					,fieldLabel: _('username')
					,description: _('user_username_desc')
					,xtype: 'textfield'
					,anchor: '100%'
				},{
					id: 'modx-user-fullname'
					,name: 'fullname'
					,fieldLabel: _('user_full_name')
					,xtype: 'textfield'
					,anchor: '100%'
					,maxLength: 255
				},{
					layout: 'column'
					,defaults: {
						layout: 'form'
						,labelAlign: 'top'
						,labelSeparator: ''
						,anchor: '100%'
						,border: false
					}
					,items: [{
						columnWidth:.5
						,defaults: {
							msgTarget: 'under'
						}
						,items:[{
							id: 'modx-user-firstname'
							,name: 'firstname'
							,fieldLabel: 'Имя'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-lastname'
							,name: 'lastname'
							,fieldLabel: 'Фамилия'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						}/*,{
							id: 'modx-user-discount'
							,name: 'discount'
							,fieldLabel: 'Текущая скидка %'
							,xtype: 'numberfield'
							,anchor: '100%'
							,maxLength: 255
						}*/]
					},{
						columnWidth:.5
						,defaults: {
							msgTarget: 'under'
						}
						,items:[{
							id: 'modx-user-middlename'
							,name: 'middlename'
							,fieldLabel: 'Отчество'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-type'
							,name: 'type'
							,fieldLabel: 'Тип лица'
							,xtype: 'ut-combo-user-type'
							,anchor: '100%'
							,maxLength: 255
						}/*,{
							id: 'modx-user-max_discount'
							,name: 'max_discount'
							,fieldLabel: 'Максимальная скидка %'
							,xtype: 'numberfield'
							,anchor: '100%'
							,maxLength: 255
						}*/]
					}]
				},{
					id: 'modx-user-email'
					,name: 'email'
					,fieldLabel: _('user_email')
					,xtype: 'textfield'
					,anchor: '100%'
					,maxLength: 255
					,allowBlank: false
				},{
					id: 'modx-user-website'
					,name: 'website'
					,fieldLabel: _('user_website')
					,xtype: 'textfield'
					,anchor: '100%'
					,maxLength: 255
				},{
					id: 'modx-user-phone'
					,name: 'phone'
					,fieldLabel: _('user_phone')
					,xtype: 'textfield'
					,width: 200
					,maxLength: 255
				},{
					id: 'modx-user-mobilephone'
					,name: 'mobilephone'
					,fieldLabel: _('user_mobile')
					,xtype: 'textfield'
					,width: 200
					,maxLength: 255
				},{
					id: 'modx-user-city'
					,name: 'city'
					,fieldLabel: _('city')
					,xtype: 'textfield'
					,anchor: '100%'
					,maxLength: 255
				},{
					id: 'modx-user-address'
					,name: 'address'
					,fieldLabel: _('address')
					,xtype: 'textarea'
					,anchor: '100%'
					,grow: true
				}]
			},{
				columnWidth: .5
				,defaults: {
					msgTarget: 'under'
				}
				,items: [{
					id: 'modx-user-newpassword'
					,name: 'newpassword'
					,xtype: 'hidden'
					,value: false
				},{
					id: 'modx-user-primary-group'
					,name: 'primary_group'
					,xtype: 'hidden'
				},{
					id: 'modx-user-active'
					,name: 'active'
					,hideLabel: true
					,boxLabel: _('active')
					,description: _('user_active_desc')
					,xtype: 'xcheckbox'
					,inputValue: 1
				},{
					id: 'modx-user-blocked'
					,name: 'blocked'
					,hideLabel: true
					,boxLabel: _('user_block')
					,description: _('user_block_desc')
					,xtype: 'xcheckbox'
					,inputValue: 1
				},{
					id: 'modx-user-blockeduntil'
					,name: 'blockeduntil'
					,fieldLabel: _('user_blockeduntil')
					,description: _('user_blockeduntil_desc')
					,xtype: 'xdatetime'
					,width: 300
					,timeWidth: 150
					,dateWidth: 150
					,allowBlank: true
					,dateFormat: MODx.config.manager_date_format
					,timeFormat: MODx.config.manager_time_format
					,hiddenFormat: 'Y-m-d H:i:s'
				},{
					id: 'modx-user-blockedafter'
					,name: 'blockedafter'
					,fieldLabel: _('user_blockedafter')
					,description: _('user_blockedafter_desc')
					,xtype: 'xdatetime'
					,width: 300
					,timeWidth: 150
					,dateWidth: 150
					,allowBlank: true
					,dateFormat: MODx.config.manager_date_format
					,timeFormat: MODx.config.manager_time_format
					,hiddenFormat: 'Y-m-d H:i:s'
				},{
					id: 'modx-user-logincount'
					,name: 'logincount'
					,fieldLabel: _('user_logincount')
					,description: _('user_logincount_desc')
					,xtype: 'statictextfield'
				},{
					id: 'modx-user-thislogin'
					,name: 'thislogin'
					,fieldLabel: _('user_prevlogin')
					,description: _('user_prevlogin_desc')
					,xtype: 'statictextfield'
				},{
					id: 'modx-user-failedlogincount'
					,name: 'failedlogincount'
					,fieldLabel: _('user_failedlogincount')
					,description: _('user_failedlogincount_desc')
					,xtype: 'textfield'
				},{
					id: 'modx-user-comment'
					,name: 'comment'
					,fieldLabel: _('comment')
					,xtype: 'textarea'
					,anchor: '100%'
					,grow: true
				},{
					id: 'modx-user-fs-newpassword'
					,title: _('password_new')
					,xtype: 'fieldset'
					,cls: 'x-fieldset-checkbox-toggle' // add a custom class for checkbox replacement
					,checkboxToggle: true
					,collapsed: (config.user ? true : false)
					,forceLayout: true
					,listeners: {
						'expand': {fn:function(p) {
							Ext.getCmp('modx-user-newpassword').setValue(true);
							this.markDirty();
						},scope:this}
						,'collapse': {fn:function(p) {
							Ext.getCmp('modx-user-newpassword').setValue(false);
							this.markDirty();
						},scope:this}
					}
					,items: [{
						xtype: 'radiogroup'
						,fieldLabel: _('password_method')
						,columns: 1
						,items: [{
							id: 'modx-user-passwordnotifymethod-e'
							,name: 'passwordnotifymethod'
							,boxLabel: _('password_method_email')
							,xtype: 'radio'
							,value: 'e'
							,inputValue: 'e'
						},{
							id: 'modx-user-passwordnotifymethod-s'
							,name: 'passwordnotifymethod'
							,boxLabel: _('password_method_screen')
							,xtype: 'radio'
							,value: 's'
							,inputValue: 's'
							,checked: true
						}]
					},{
						xtype: 'radiogroup'
						,fieldLabel: _('password_gen_method')
						,columns: 1
						,items: [{
							id: 'modx-user-password-genmethod-g'
							,name: 'passwordgenmethod'
							,boxLabel: _('password_gen_gen')
							,xtype: 'radio'
							,inputValue: 'g'
							,value: 'g'
							,checked: true
						},{
							id: 'modx-user-password-genmethod-s'
							,name: 'passwordgenmethod'
							,boxLabel: _('password_gen_specify')
							,xtype: 'radio'
							,inputValue: 'spec'
							,value: 'spec'
						}]
					},{
						id: 'modx-user-panel-newpassword'
						,xtype: 'panel'
						,layout: 'form'
						,border: false
						,autoHeight: true
						,style: 'padding-top: 15px' // nested form, add padding-top as the label will not have it
						,items: [{
							id: 'modx-user-specifiedpassword'
							,name: 'specifiedpassword'
							,fieldLabel: _('change_password_new')
							,xtype: 'textfield'
							,inputType: 'password'
							,anchor: '100%'
						},{
							id: 'modx-user-confirmpassword'
							,name: 'confirmpassword'
							,fieldLabel: _('change_password_confirm')
							,xtype: 'textfield'
							,inputType: 'password'
							,anchor: '100%'
						}]
					}]
				}]
			}]
		},{
			html: MODx.onUserFormRender
			,border: false
		}];
	}

	,getGeneralFields: function(config) {
		return [{
			layout: 'column'
			,border: false
			,defaults: {
				layout: 'form'
				,labelAlign: 'top'
				,labelSeparator: ''
				,anchor: '100%'
				,border: false
			}
			,items: [{
				columnWidth: .5
				,defaults: {
					msgTarget: 'under'
				}
				,items: [{
					id: 'modx-user-id'
					,name: 'id'
					,xtype: 'hidden'
					,value: config.user
				},{
					id: 'modx-user-username'
					,name: 'username'
					,fieldLabel: _('username')
					,description: _('user_username_desc')
					,xtype: 'textfield'
					,anchor: '100%'
				},{
					id: 'modx-user-fullname'
					,name: 'fullname'
					,fieldLabel: _('user_full_name')
					,xtype: 'textfield'
					,anchor: '100%'
					,maxLength: 255
				},{
					layout: 'column'
					,defaults: {
						layout: 'form'
						,labelAlign: 'top'
						,labelSeparator: ''
						,anchor: '100%'
						,border: false
					}
					,items: [{
						columnWidth:.5
						,defaults: {
							msgTarget: 'under'
						}
						,items:[{
							id: 'modx-user-firstname'
							,name: 'firstname'
							,fieldLabel: 'Имя'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-company'
							,name: 'company'
							,fieldLabel: 'Компания'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-kpp'
							,name: 'kpp'
							,fieldLabel: 'КПП'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-director_fullname'
							,name: 'director_fullname'
							,fieldLabel: 'ФИО директора'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						}/*,{
							id: 'modx-user-discount'
							,name: 'discount'
							,fieldLabel: 'Текущая скидка %'
							,xtype: 'numberfield'
							,anchor: '100%'
							,maxLength: 255
						}*/]
					},{
						columnWidth:.5
						,defaults: {
							msgTarget: 'under'
						}
						,items:[{
							id: 'modx-user-lastname'
							,name: 'lastname'
							,fieldLabel: 'Фамилия'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-inn'
							,name: 'inn'
							,fieldLabel: 'ИНН'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-position'
							,name: 'position'
							,fieldLabel: 'Должность'
							,xtype: 'textfield'
							,anchor: '100%'
							,maxLength: 255
						},{
							id: 'modx-user-type'
							,name: 'type'
							,fieldLabel: 'Тип лица'
							,xtype: 'ut-combo-user-type'
							,anchor: '100%'
							,maxLength: 255
						}/*,{
							id: 'modx-user-max_discount'
							,name: 'max_discount'
							,fieldLabel: 'Максимальная скидка %'
							,xtype: 'numberfield'
							,anchor: '100%'
							,maxLength: 255
						}*/]
					}]
				},{
					id: 'modx-user-email'
					,name: 'email'
					,fieldLabel: _('user_email')
					,xtype: 'textfield'
					,anchor: '100%'
					,maxLength: 255
					,allowBlank: false
				},{
					id: 'modx-user-website'
					,name: 'website'
					,fieldLabel: _('user_website')
					,xtype: 'textfield'
					,anchor: '100%'
					,maxLength: 255
				},{
					id: 'modx-user-phone'
					,name: 'phone'
					,fieldLabel: _('user_phone')
					,xtype: 'textfield'
					,width: 200
					,maxLength: 255
				},{
					id: 'modx-user-mobilephone'
					,name: 'mobilephone'
					,fieldLabel: _('user_mobile')
					,xtype: 'textfield'
					,width: 200
					,maxLength: 255
				}/*,{
					id: 'modx-user-fax'
					,name: 'fax'
					,fieldLabel: _('user_fax')
					,xtype: 'textfield'
					,width: 200
					,maxLength: 255
				}*/,{
					id: 'modx-user-city'
					,name: 'city'
					,fieldLabel: _('city')
					,xtype: 'textfield'
					,anchor: '100%'
					,maxLength: 255
				},{
					id: 'modx-user-address'
					,name: 'address'
					,fieldLabel: _('address')
					,xtype: 'textarea'
					,anchor: '100%'
					,grow: true
				}/*,{
					id: 'modx-user-state'
					,name: 'state'
					,fieldLabel: _('user_state')
					,xtype: 'textfield'
					,width: 100
					,maxLength: 100
				},{
					id: 'modx-user-zip'
					,name: 'zip'
					,fieldLabel: _('user_zip')
					,xtype: 'textfield'
					,width: 100
					,maxLength: 25
				},{
					id: 'modx-user-country'
					,fieldLabel: _('user_country')
					,xtype: 'modx-combo-country'
					,value: ''
				},{
					id: 'modx-user-website'
					,name: 'website'
					,fieldLabel: _('user_website')
					,xtype: 'textfield'
					,anchor: '100%'
					,maxLength: 255
				},{
					id: 'modx-user-dob'
					,name: 'dob'
					,fieldLabel: _('user_dob')
					,xtype: 'datefield'
					,width: 200
					,allowBlank: true
					,format: MODx.config.manager_date_format
				},{
					id: 'modx-user-gender'
					,name: 'gender'
					,hiddenName: 'gender'
					,fieldLabel: _('user_gender')
					,xtype: 'modx-combo-gender'
					,width: 200
				}*/]
			},{
				columnWidth: .5
				,defaults: {
					msgTarget: 'under'
				}
				,items: [{
					id: 'modx-user-newpassword'
					,name: 'newpassword'
					,xtype: 'hidden'
					,value: false
				},{
					id: 'modx-user-primary-group'
					,name: 'primary_group'
					,xtype: 'hidden'
				},{
					id: 'modx-user-active'
					,name: 'active'
					,hideLabel: true
					,boxLabel: _('active')
					,description: _('user_active_desc')
					,xtype: 'xcheckbox'
					,inputValue: 1
				}/*,{
					id: 'modx-user-sudo'
					,name: 'sudo'
					,hideLabel: true
					,boxLabel: _('user_sudo')
					,description: _('user_sudo_desc')
					,xtype: 'xcheckbox'
					,inputValue: 1
					,value: 0
				}*/,{
					id: 'modx-user-blocked'
					,name: 'blocked'
					,hideLabel: true
					,boxLabel: _('user_block')
					,description: _('user_block_desc')
					,xtype: 'xcheckbox'
					,inputValue: 1
				},{
					id: 'modx-user-blockeduntil'
					,name: 'blockeduntil'
					,fieldLabel: _('user_blockeduntil')
					,description: _('user_blockeduntil_desc')
					,xtype: 'xdatetime'
					,width: 300
					,timeWidth: 150
					,dateWidth: 150
					,allowBlank: true
					,dateFormat: MODx.config.manager_date_format
					,timeFormat: MODx.config.manager_time_format
					,hiddenFormat: 'Y-m-d H:i:s'
				},{
					id: 'modx-user-blockedafter'
					,name: 'blockedafter'
					,fieldLabel: _('user_blockedafter')
					,description: _('user_blockedafter_desc')
					,xtype: 'xdatetime'
					,width: 300
					,timeWidth: 150
					,dateWidth: 150
					,allowBlank: true
					,dateFormat: MODx.config.manager_date_format
					,timeFormat: MODx.config.manager_time_format
					,hiddenFormat: 'Y-m-d H:i:s'
				},{
					id: 'modx-user-logincount'
					,name: 'logincount'
					,fieldLabel: _('user_logincount')
					,description: _('user_logincount_desc')
					,xtype: 'statictextfield'
				},{
					id: 'modx-user-thislogin'
					,name: 'thislogin'
					,fieldLabel: _('user_prevlogin')
					,description: _('user_prevlogin_desc')
					,xtype: 'statictextfield'
				},{
					id: 'modx-user-failedlogincount'
					,name: 'failedlogincount'
					,fieldLabel: _('user_failedlogincount')
					,description: _('user_failedlogincount_desc')
					,xtype: 'textfield'
				}/*,{
					id: 'modx-user-class-key'
					,name: 'class_key'
					,fieldLabel: _('class_key')
					,description: _('user_class_key_desc')
					,xtype: 'textfield'
					,anchor: '100%'
					,value: 'modUser'
				}*/,{
					id: 'modx-user-comment'
					,name: 'comment'
					,fieldLabel: _('comment')
					,xtype: 'textarea'
					,anchor: '100%'
					,grow: true
				},{
					id: 'modx-user-fs-newpassword'
					,title: _('password_new')
					,xtype: 'fieldset'
					,cls: 'x-fieldset-checkbox-toggle' // add a custom class for checkbox replacement
					,checkboxToggle: true
					,collapsed: (config.user ? true : false)
					,forceLayout: true
					,listeners: {
						'expand': {fn:function(p) {
							Ext.getCmp('modx-user-newpassword').setValue(true);
							this.markDirty();
						},scope:this}
						,'collapse': {fn:function(p) {
							Ext.getCmp('modx-user-newpassword').setValue(false);
							this.markDirty();
						},scope:this}
					}
					,items: [{
						xtype: 'radiogroup'
						,fieldLabel: _('password_method')
						,columns: 1
						,items: [{
							id: 'modx-user-passwordnotifymethod-e'
							,name: 'passwordnotifymethod'
							,boxLabel: _('password_method_email')
							,xtype: 'radio'
							,value: 'e'
							,inputValue: 'e'
						},{
							id: 'modx-user-passwordnotifymethod-s'
							,name: 'passwordnotifymethod'
							,boxLabel: _('password_method_screen')
							,xtype: 'radio'
							,value: 's'
							,inputValue: 's'
							,checked: true
						}]
					},{
						xtype: 'radiogroup'
						,fieldLabel: _('password_gen_method')
						,columns: 1
						,items: [{
							id: 'modx-user-password-genmethod-g'
							,name: 'passwordgenmethod'
							,boxLabel: _('password_gen_gen')
							,xtype: 'radio'
							,inputValue: 'g'
							,value: 'g'
							,checked: true
						},{
							id: 'modx-user-password-genmethod-s'
							,name: 'passwordgenmethod'
							,boxLabel: _('password_gen_specify')
							,xtype: 'radio'
							,inputValue: 'spec'
							,value: 'spec'
						}]
					},{
						id: 'modx-user-panel-newpassword'
						,xtype: 'panel'
						,layout: 'form'
						,border: false
						,autoHeight: true
						,style: 'padding-top: 15px' // nested form, add padding-top as the label will not have it
						,items: [{
							id: 'modx-user-specifiedpassword'
							,name: 'specifiedpassword'
							,fieldLabel: _('change_password_new')
							,xtype: 'textfield'
							,inputType: 'password'
							,anchor: '100%'
						},{
							id: 'modx-user-confirmpassword'
							,name: 'confirmpassword'
							,fieldLabel: _('change_password_confirm')
							,xtype: 'textfield'
							,inputType: 'password'
							,anchor: '100%'
						}]
					}]
				}]
			}]
		},{
			html: MODx.onUserFormRender
			,border: false
		}];
	}
});
Ext.reg('ut-panel-user',MODx.panel.CsUser);


utComponent.combo.UserType = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		store: new Ext.data.ArrayStore({
			id: 'ut-combo-user-type',
			fields: ['id','value'],
			data: [
				['fl','Физическое лицо'],
				['ul','Юридическое лицо']
			]
		}),
		mode: 'local',
		displayField: 'value',
		valueField: 'id',
		hiddenName: 'type'
	});
	utComponent.combo.UserType.superclass.constructor.call(this,config);
};
Ext.extend(utComponent.combo.UserType,MODx.combo.ComboBox);
Ext.reg('ut-combo-user-type',utComponent.combo.UserType);