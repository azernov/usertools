MODx.panel.CsUsers = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'ut-panel-users'
		,cls: 'container'
		,bodyStyle: ''
		,defaults: { collapsible: false ,autoHeight: true }
		,items: [{
			html: '<h2>'+_('users')+'</h2>'
			,border: false
			,id: 'modx-users-header'
			,cls: 'modx-page-header'
		},{
			layout: 'form'
			,items: [{
				html: '<p>'+_('user_management_msg')+'</p>'
				,bodyCssClass: 'panel-desc'
				,border: false
			},{
				xtype: 'ut-grid-user'
				,cls:'main-wrapper'
				,preventRender: true
			}]
		}]
	});
	MODx.panel.CsUsers.superclass.constructor.call(this,config);
};
Ext.extend(MODx.panel.CsUsers,MODx.FormPanel);
Ext.reg('ut-panel-users',MODx.panel.CsUsers);

MODx.grid.CsUser = function(config) {
	config = config || {};

	this.sm = new Ext.grid.CheckboxSelectionModel();
	Ext.applyIf(config,{
		url: utComponent.config.connector_url
		,url2: MODx.config.connector_url
		,baseParams: {
			action: 'mgr/user/getlist'
			,usergroup: MODx.request['usergroup'] ? MODx.request['usergroup'] : ''
		}
		,fields: ['id','username','fullname','firstname','middlename','lastname','company','email','gender','blocked','role','active','max_discount','discount','registerdate','thislogin','cls']
		,paging: true
		,autosave: true
		,save_action: 'mgr/user/updatefromgrid'
		,remoteSort: true
		,stateful: true
		,stateId: 'ut-grid-users'
		,viewConfig: {
			forceFit:true
			,enableRowBody:true
			,scrollOffset: 0
			,autoFill: true
			,showPreview: true
			,getRowClass : function(rec){
				return rec.data.active ? 'grid-row-active' : 'grid-row-inactive';
			}
		}
		,sm: this.sm
		,columns: [this.sm,{
			header: _('id')
			,dataIndex: 'id'
			,width: 50
			,sortable: true
		},{
			header: _('email')
			,dataIndex: 'email'
			,width: 100
			,sortable: true
			,editor: { xtype: 'textfield' }
		},{
			header: 'Имя'
			,dataIndex: 'firstname'
			,width: 100
			,sortable: true
			,editor: { xtype: 'textfield' }
		},{
			header: 'Отчество'
			,dataIndex: 'middlename'
			,width: 100
			,sortable: true
			,hidden: true
			,editor: { xtype: 'textfield' }
		},{
			header: 'Фамилия'
			,dataIndex: 'lastname'
			,width: 100
			,sortable: true
			,editor: { xtype: 'textfield' }
		},{
			header: 'Компания'
			,dataIndex: 'company'
			,width: 100
			,sortable: true
			,editor: { xtype: 'textfield' }
		},{
			header: _('active')
			,dataIndex: 'active'
			,width: 50
			,sortable: true
			,editor: { xtype: 'combo-boolean', renderer: 'boolean' }
		}/*,{
			header: 'Скидка'
			,dataIndex: 'discount'
			,width: 50
			,sortable: true
			,editor: { xtype: 'numberfield' }
		}*/,{
			header: 'Дата регистрации'
			,dataIndex: 'registerdate'
			,width: 100
			,sortable: true
		},{
			header: 'Последняя активность'
			,dataIndex: 'thislogin'
			,width: 100
			,sortable: true
		}/*,{
			header: _('user_block')
			,dataIndex: 'blocked'
			,width: 80
			,sortable: true
			,editor: { xtype: 'combo-boolean', renderer: 'boolean' }
		}*//*,{
			header: 'Максимальная скидка'
			,dataIndex: 'max_discount'
			,width: 80
			,sortable: true
			,editor: { xtype: 'numberfield' }
		}*/]
		,tbar: [/*{
			text: _('user_new')
			,handler: this.createUser
			,scope: this
			,cls:'primary-button'
		},*/{
			text: _('bulk_actions')
			,menu: [{
				text: _('selected_activate')
				,handler: this.activateSelected
				,scope: this
			},{
				text: _('selected_deactivate')
				,handler: this.deactivateSelected
				,scope: this
			},{
				text: _('selected_remove')
				,handler: this.removeSelected
				,scope: this
			}]
		},'->',{
			xtype: 'textfield'
			,name: 'search'
			,id: 'modx-user-search'
			,cls: 'x-form-filter'
			,emptyText: _('search_ellipsis')
			,listeners: {
				'change': {fn: this.search, scope: this}
				,'render': {fn: function(cmp) {
					new Ext.KeyMap(cmp.getEl(), {
						key: Ext.EventObject.ENTER
						,fn: this.blur
						,scope: cmp
					});
				},scope:this}
			}
		},{
			xtype: 'button'
			,id: 'modx-filter-clear'
			,cls: 'x-form-filter-clear'
			,text: _('filter_clear')
			,listeners: {
				'click': {fn: this.clearFilter, scope: this}
			}
		}]
	});
	MODx.grid.CsUser.superclass.constructor.call(this,config);
	this.on('afterAutoSave', function(result) {
		if (!result.success) {
			var msg = result.data[0].msg || _('user_err_save');
			MODx.msg.alert(_('error'), msg);
		}
	});
};
Ext.extend(MODx.grid.CsUser,MODx.grid.Grid,{
	getMenu: function() {
		var r = this.getSelectionModel().getSelected();
		var p = r.data.cls;

		var m = [];
		if (this.getSelectionModel().getCount() > 1) {
			m.push({
				text: _('selected_activate')
				,handler: this.activateSelected
				,scope: this
			});
			m.push({
				text: _('selected_deactivate')
				,handler: this.deactivateSelected
				,scope: this
			});
			m.push('-');
			m.push({
				text: _('selected_remove')
				,handler: this.removeSelected
				,scope: this
			});
		} else {
			if (p.indexOf('pupdate') != -1) {
				m.push({
					text: _('user_update')
					,handler: this.updateUser
				});
			}
			/*if (p.indexOf('pcopy') != -1) {
				if (m.length > 0) m.push('-');
				m.push({
					text: _('user_duplicate')
					,handler: this.duplicateUser
				});
			}
			if (p.indexOf('premove') != -1) {
				if (m.length > 0) m.push('-');
				m.push({
					text: _('user_remove')
					,handler: this.removeUser
				});
			}*/
		}
		if (m.length > 0) {
			this.addContextMenuItem(m);
		}
	}

	,createUser: function() {
		MODx.loadPage('security/user/create');
	}

	,activateSelected: function() {
		var cs = this.getSelectedAsList();
		if (cs === false) return false;

		MODx.Ajax.request({
			url: this.config.url
			,params: {
				action: 'mgr/user/activateMultiple'
				,namespace: 'carolesmokes'
				,users: cs
			}
			,listeners: {
				'success': {fn:function(r) {
					this.getSelectionModel().clearSelections(true);
					this.refresh();
				},scope:this}
			}
		});
		return true;
	}
	,deactivateSelected: function() {
		var cs = this.getSelectedAsList();
		if (cs === false) return false;

		MODx.Ajax.request({
			url: this.config.url
			,params: {
				action: 'mgr/user/deactivateMultiple'
				,namespace: 'carolesmokes'
				,users: cs
			}
			,listeners: {
				'success': {fn:function(r) {
					this.getSelectionModel().clearSelections(true);
					this.refresh();
				},scope:this}
			}
		});
		return true;
	}
	,removeSelected: function() {
		var cs = this.getSelectedAsList();
		if (cs === false) return false;

		MODx.msg.confirm({
			title: _('user_remove_multiple')
			,text: _('user_remove_multiple_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/user/removeMultiple'
				,namespace: 'carolesmokes'
				,users: cs
			}
			,listeners: {
				'success': {fn:function(r) {
					this.getSelectionModel().clearSelections(true);
					this.refresh();
				},scope:this}
			}
		});
		return true;
	}

	,removeUser: function() {
		MODx.msg.confirm({
			title: _('user_remove')
			,text: _('user_confirm_remove')
			,url: this.config.url
			,params: {
				action: 'mgr/user/delete'
				,namespace: 'carolesmokes'
				,id: this.menu.record.id
			}
			,listeners: {
				'success': {fn:this.refresh,scope:this}
			}
		});
	}

	,updateUser: function() {
		MODx.loadPage('user/update', 'id='+this.menu.record.id+'&namespace=carolesmokes');
	}

	,rendGender: function(d,c) {
		switch(d.toString()) {
			case '0':
				return '-';
			case '1':
				return _('male');
			case '2':
				return _('female');
		}
	}

	,filterUsergroup: function(cb,nv,ov) {
		this.getStore().baseParams.usergroup = Ext.isEmpty(nv) || Ext.isObject(nv) ? cb.getValue() : nv;
		this.getBottomToolbar().changePage(1);
		this.refresh();
		return true;
	}
	,search: function(tf,newValue,oldValue) {
		var nv = newValue || tf;
		this.getStore().baseParams.query = Ext.isEmpty(nv) || Ext.isObject(nv) ? '' : nv;
		this.getBottomToolbar().changePage(1);
		return true;
	}
	,clearFilter: function() {
		this.getStore().baseParams = {
			action: 'mgr/user/getlist'
		};
		Ext.getCmp('modx-user-search').reset();
		Ext.getCmp('modx-user-filter-usergroup').reset();
		this.getBottomToolbar().changePage(1);
		this.refresh();
	}
});
Ext.reg('ut-grid-user',MODx.grid.CsUser);
