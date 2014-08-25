Ext.onReady(function() {
    var onlineMode = (navigator.onLine) ? true : false;
    var mode = (onlineMode) ? '<span id="status"><i class="icon icon-circle"></i> On-Line' : '<i class="icon icon-off"></i> Off-Line...</span>';

    if (onlineMode) {
        storeEmpresaOffline.load();

        var getCount = storeVisitaOffline.getCount() + storeEmpresaOffline.getCount();
        Ext.getCmp('btnSync').setText('Hay (' + getCount + ') para actualizar');

    } else {
        /*Si no esta Online no puede sincronizar*/
        Ext.getCmp('btnSync').hide();
    }


    Ext.require('Ext.tab.*');
    var tabs = new Ext.TabPanel({
        activeTab: 0,
        items: [{
                title: 'Empresa',
                items: [EmpresaForm]
            }, {
                title: 'Seguimiento ',
                layout: 'column',
                //
                items: [{
                        title: 'Datos Visitas',
                        columnWidth: 1/2,
                        bodyStyle: 'padding:0 0 5px 5px',
                        items: [VisitaForm]
                    }, {
                        title: 'Histórico Visitas',
                        columnWidth: 1/2,
                        autoScroll: true,
                        bodyStyle: 'padding:0 0 0px 5px',
                        items: [VisitasGrid]
                    }]                       
            }, {
                title: 'Encuesta',
                items: [EncuestaForm]
            }],
        defaults: {           
            deferredRender: false   // likewise
        }
    }
    );

    /* Para tareas relacionadas via Agenda*/
    var getParams = document.URL.split("/");
    var params = (getParams[getParams.length - 1]);
    Ext.getCmp('task').setValue(params);


    var remove_loaders = function() {
        Ext.get('loading').remove();
        Ext.fly('loading-mask').remove();
    }
    //Ext.create('Ext.panel.Panel',{
    Ext.create('Ext.Viewport', {
        id: 'main-panel',
        autoScroll: true,
        layout: 'fit',
        items: [
            {
                /*title:title,*/
                title: '\
            <div class="navbar navbar-inverse navbar-static-top "> \
                <div class="navbar-inner barra_user">\
                    <ul class="nav pull-left inline"><li> \
                        <a href="#"">' + mode + '</a>\
                        </li></ul>\
                        <ul class="nav pull-right inline"><li><a href="' + globals.module_url + '">Volver <i class="icon-chevron-sign-right icon2x"></i></a>\
                        </li></ul>\
                </div>\
            </div>',
                layout: 'fit',
                autoScroll: true,
                defaults: {
                    layout: 'anchor',
                    autoScroll: true,
                    defaults: {
                        anchor: '100%'
                    }
                },
                items: [{
                        //layout: 'fit',
                        baseCls: 'x-plain',
                        items: [tabs]
                    }]
            }]
                ,
        listeners: {
            render: function() {

            },
            afterRender: function() {
                remove_loaders();
                loaded();
            }

        }
    });
});

