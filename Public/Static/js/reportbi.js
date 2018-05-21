/**
 * Created by hylanda69874 on 2017/7/14.
 */
var reportbi = (function(){

    var _filterPaneEnabled = false;
    var _navContentPaneEnabled = false;

    var _customFilterPaneReport = null;
    var _reportContainer = null;
    var _pageName = false;
    var attr = {};

    //创建报表
    attr.create = function(reportContainer, staticReportUrl,defaultPageName,filter){
        if(!reportContainer || !staticReportUrl) return;
        var models = window['powerbi-client'].models;
        var permissions = models.Permissions.All;
        _reportContainer = reportContainer.get(0);
        _pageName = defaultPageName;

        fetch(staticReportUrl).then(function (res){
            if(res.ok) {
                return res.json().then(function (embedCfg) {
                    var config = $.extend({}, embedCfg, {
                        pageName: defaultPageName,
                        permissions: permissions,
                        settings: {
                            filterPaneEnabled: _filterPaneEnabled,
                            navContentPaneEnabled: _navContentPaneEnabled
                        }
                    });
                    console.log(config);

                    _customFilterPaneReport = powerbi.embed(_reportContainer, config);
                    reportContainer.find('iframe').attr('frameborder', 0);
                    return _customFilterPaneReport;
                });
            }else{
                return res.json().then(function (error) {
                    throw new Error(error);
                });
            }
        }).then(function(report){
            var pageInfo = [];
            report.on('loaded', function(event){
                if(filter){
                    var filterBlank = {
                        table: 'K_kol',
                        column: 'weibo_id',
                        val: ["(Blank)"]
                    };
                    attr.setFilter(filterBlank);
                    setTimeout(function(){
                        attr.setFilter(filter);
                    },1000);

                }
                report.getPages().then(function(pages){
                    pages.forEach(function(page){
                        pageInfo[page.name] = page.displayName;
                    });
                });
            });
            console.log(pageInfo);
        });
    };

    //设置页面
    attr.setPage = function(pageName){
        if(!_customFilterPaneReport || !pageName) return;
        powerbi.get(_reportContainer).setPage(pageName);
    };

    //筛选过滤
    attr.setFilter = function(data){
        if(!_customFilterPaneReport || !data.table || !data.column || !data.val) return;
        var models = window['powerbi-client'].models;
        var target = {
            table: data.table,
            column: data.column
        };
        var op = 'In';var val = data.val;
        var filter = new models.BasicFilter(target,op,val);
        var obj = powerbi.get(_reportContainer);
        if(_pageName){
            obj.page(_pageName).setFilters([filter]);
        }else {
            obj.setFilters([filter]);
        }
    };


    return attr;
})();
