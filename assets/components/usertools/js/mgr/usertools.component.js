var utComponent = function(config) {
	config = config || {};
	utComponent.superclass.constructor.call(this,config);
};
Ext.extend(utComponent,Ext.Component,{
	page:{},window:{},grid:{},tree:{},panel:{},combo:{},config:{},view:{},keymap:{}, plugin:{}
});
Ext.reg('utcomponent',utComponent);

utComponent = new utComponent();