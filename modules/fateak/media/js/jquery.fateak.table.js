(function($){
 
    var settings = {
        tid: 'default',
        i18n: {'Search':'Search','Rows/Page':'Rows/Page','Loading Data...':'Loading Data...'},
        dataURL: window.location.href, 
        columns: [],
        rowsPerPage: 10,
        clickablePage: 10,
        search: {},
    };
     
    jQuery.fn.FTable = function(opt) {
        this.data("options", mergeObjects(settings, opt));
        initTable(this);
        return this;
    };

    var initTable = function(table) {
        createHtmlElements(table);
        loadData(table, 1);
        bindEvent(table);
    };

    var createHtmlElements = function(table) {
        var table_options = table.data('options');
        var beforeHtml = "<div id='fateak-table-" + table_options['tid'] + "'>"
            + "<select class='rows-per-page'><option value='1'>1</option><option value='10'>10</option><option value='50'>50</option></select> " + table_options['i18n']['Rows/Page']
            + "<input class='search-content' type='text' /><select class='search-type'>";
        for (var i in table_options['search']) {
            beforeHtml += "<option value='" + i + "'>" + table_options['search'][i] + "</option>"
        }
        beforeHtml += "</select>" 
            + "<input class='search-btn' type='button' value='"+ table_options['i18n']['Search'] +"' />"
            + "</div>";
        table.find("table").before(beforeHtml);
        var afterHtml = "<div class='fateak-pagination' style='height:35px'><nav><ul></ul></nav></div>";
        afterHtml += "<div style='display:none'>"
            + "<input class='table-sort' type='hidden' />"
            + "<input class='table-order' type='hidden' value='DESC' />";
            + "</div>";
        table.find("table").after(afterHtml);

        table.find("th[fclass='sortable']").each(function(){
            $(this).append("<i table='" + table_options['tid'] + "' class='f-sort-icon glyphicon glyphicon-sort'></i>");         
        });
    }

    var loadData = function(table, page) {
        var table_options = table.data('options');
        var tb = table.find('tbody');
        tb.html("<tr><td>" + table_options['i18n']['Loading Data...'] + "</td></tr>");
        var container = $("#fateak-table-" + table_options['tid']);
        var rowsPerPage = parseInt(container.find(".rows-per-page").val());
        var keytype = container.find(".search-type").val();
        var keyword = container.find(".search-content").val();
        var sort = container.parent().find(".table-sort").val();
        var order = container.parent().find(".table-order").val();
        $.getJSON(table_options['dataURL'], {page:page,rowsPerPage:rowsPerPage,sort:sort,order:order,keytype:keytype,keyword:keyword}, function(result){
            refreshData(table, result, page);
        });
    }

    var refreshData = function(table, result, page) {
        var table_options = table.data('options');
        var html = "";
        var data = result['data'];
        for (var i in data) {
            var tr = "<tr>"
            for (var j in table_options['columns']) {
                if (table_options['columns'][j].indexOf('.') > 0) {
                    var column_value = table_options['columns'][j].split('.');
                    var td_html = listIn(data[i][column_value[0]], column_value[0], column_value[1]);
                } else {
                    var td_html = data[i][table_options['columns'][j]];
                }
                tr += "<td>" + td_html + "</td>"
            }
            html += tr + "</tr>";
        }
        table.find("tbody").html(html);

        setPaginator(table, page, result['total']);
    }

    var setPaginator = function(table, page, total) {
        var table_options = table.data('options');
        var container = $("#fateak-table-" + table_options['tid']);
        var prePage = (page - 1 < 1) ? 1 : (page - 1);
        var rowsPerPage = parseInt(container.find(".rows-per-page").val());
        var totalPages = Math.ceil(total / rowsPerPage);
        var nextPage = (parseInt(page) + 1 > totalPages) ? totalPages : (parseInt(page) + 1);
        var beginPage = (Math.floor(table_options['clickablePage'] / 2) >= page) ? 1 : (page - Math.floor(table_options['clickablePage'] / 2));
        if (totalPages > table_options['clickablePage']) {
            beginPage = (beginPage > (totalPages - table_options['clickablePage'])) ? (totalPages - table_options['clickablePage'] + 1) : beginPage;
        }
        var clickablePage = (totalPages > table_options['clickablePage']) ? table_options['clickablePage'] : totalPages;

        var html = "<li><a page='" + prePage + "' table='" + table_options['tid'] + "'>&laquo;</a></li>";
        for (var i = beginPage; i < parseInt(beginPage) + parseInt(clickablePage); i ++) {
            html += "<li><a page='" + i + "' table='" + table_options['tid'] + "'>" + i + "</a></li>";
        }
        html += "<li><a page='" + nextPage + "' table='" + table_options['tid'] + "'>&raquo;</a></li>";
        var pager = container.parent().find(".fateak-pagination");
        pager.find('ul').html(html);
        pager.find('a[page="'+ page +'"]').parent().css("background-color", "#E9E9E9");

        // we haven't use: $(document).on('click', ".class", function(){..}); 
        pager.find('li').click(function(){selectPage($(this))});
    }

    var selectPage = function(page) {
        var pageNumber = page.find('a').attr('page'); 
        var table = $("#fateak-table-" + page.find('a').attr('table')).parent();
        loadData(table, pageNumber);
    }

    var bindEvent = function(table) {
        table.find(".rows-per-page").change(function(){
            var container = $(this).parent().parent();
            loadData(container, 1);
        });

        table.find(".search-btn").click(function(){
            var container = $(this).parent().parent();
            loadData(container, 1);
        });

        table.find(".f-sort-icon").click(function(){
            var container = $("#fateak-table-" + $(this).attr('table')).parent();
            var sort = $(this).parent().attr('fname');
            var order = container.find('.table-order').val();
            newOrder = (order == 'DESC') ? 'ASC' : 'DESC';
            container.find('.table-order').val(newOrder);
            container.find('.table-sort').val(sort);
            loadData(container, 1);
        });
    }

    var listIn = function(data, column, value) {
        var list_html = "<ul class='property-" + column + "-" + value + "'>";
        for (var i in data) {
            list_html += "<li>" + data[i][value] + "</li>";
        }
        return list_html + "</ul>";
    }

})(jQuery);
