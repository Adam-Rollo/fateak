(function($){
 
    var settings = {
        tid: 'default',
        i18n: {'Search':'Search','Rows/Page':'Rows/Page','Loading Data...':'Loading Data...'},
        dataURL: window.location.href, 
    };
     
    var options = {};
 
    jQuery.fn.FTable = function(opt) {
        options = jQuery.extend(settings, opt);
        initTable(this);
        return this;
    };

    var initTable = function(table) {
        createHtmlElements(table);
        loadData(table);
    };

    var createHtmlElements = function(table) {
        var beforeHtml = "<div id='fateak-table-" + settings['tid'] + "'>"
            + "<div class='fateak-table-operations'>"
            + "<select class='rows-per-page'><option value='10'>10</option><option value='50'>50</option></select> " + settings['i18n']['Rows/Page']
            + "<input class='search-content' type='text' /><input class='search-btn' type='button' value='"+ settings['i18n']['Search'] +"' />"
            + "</div>";
        table.before(beforeHtml);
        var afterHtml = "</div>";
        table.afterHtml = "</div>";
    }

    var loadData = function(table) {
        var container = table.find('tbody');
        container.html("<tr><td>" + settings['i18n']['Loading Data...'] + "</td></tr>");
        $.getJSON(settings['dataURL'], {}, function(result){
            console.log(result);
        });
    }

})(jQuery);
