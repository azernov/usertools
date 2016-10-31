MODx.panel.CsEmptyUser = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'ut-panel-emptyuser'
        ,cls: 'container'
        ,bodyStyle: ''
        ,defaults: { collapsible: false ,autoHeight: true }
        ,items: [{
            html: '<h2>Ошибка</h2>'
            ,border: false
            ,id: 'modx-users-header'
            ,cls: 'modx-page-header'
        },{
            layout: 'form'
            ,items: [{
                html: '<p>Данный пользователь не является зарегистрированным дилером</p>'
                ,bodyCssClass: 'panel-desc'
                ,border: false
            }]
        }]
    });
    MODx.panel.CsEmptyUser.superclass.constructor.call(this,config);
};
Ext.extend(MODx.panel.CsEmptyUser,MODx.FormPanel);
Ext.reg('ut-panel-emptyuser',MODx.panel.CsEmptyUser);
