(function($){
 
    var settings = {
        tid: 'default',
        i18n: {'Search':'Search','Rows/Page':'Rows/Page','Loading Data...':'Loading Data...'},
        dataURL: window.location.href, 
        columns: [],
        rowsPerPage: 10,
        clickablePage: 10,
        search: {},
        searchSelect: {},
        initParams: {},
    };
     
    jQuery.fn.FTable = function(opt) {
        this.data("options", mergeObjects(settings, opt));
        initTable(this);
        return this;
    };

    jQuery.fn.FTableReload = function(opt) {
        loadData(this);
    }

    jQuery.fn.FTableGetParams = function(opt) {
        
        var table_options = this.data('options');
        
        var container = $("#fateak-table-" + table_options['tid']);

        var current_params = [];
        current_params['page'] = container.parent().find(".table-page").val();

        current_params['rowsPerPage'] = parseInt(container.find(".rows-per-page").val());

        var keytype = [];
        container.find(".search-type").each(function(){
            keytype.push($(this).val()); 
        });
        var keyword = [];
        container.find(".search-content").each(function(){
            keyword.push($(this).val()); 
        });

        current_params['keytype'] = keytype;
        current_params['keyword'] = keyword;
        current_params['sort'] = container.parent().find(".table-sort").val();
        current_params['order'] = container.parent().find(".table-order").val();

        var params_str = obj2json(current_params);

        return params_str;
    }

    var initTable = function(table) {
        createHtmlElements(table);

        var table_options = table.data('options');
        var initParams = table_options['initParams'];
        if (initParams['page'] == undefined) {
            loadData(table, 1);
        } else {
            loadData(table, 0);
        }

        bindEvent(table);
    };

    var createHtmlElements = function(table) {
        var table_options = table.data('options');
        var beforeHtml = "<div id='fateak-table-" + table_options['tid'] + "'>"
            + "<select class='rows-per-page'><option value='10'>10</option><option value='50'>50</option></select> " + table_options['i18n']['Rows/Page']
            + "<div class='search_area'><div class='search_row searcher_1'><input class='add_more_key' type='button' value='更多条件' />"
            + "<select rid='searcher_1' class='search-type searcher_1'>";
        for (var i in table_options['search']) {
            beforeHtml += "<option stype='input' value='" + i + "'>" + table_options['search'][i] + "</option>"
        }
        for (var i in table_options['searchSelect']) {
            beforeHtml += "<option stype='select' value='" + i + "'>" + table_options['searchSelect'][i]['text'] + "</option>"
        }
        beforeHtml += "</select>" 
            + "<span class='search-content-span searcher_1'><input class='search-content searcher_1' type='text' /></span>"
            + "<input class='search-btn' type='button' value='"+ table_options['i18n']['Search'] +"' /></div></div>"
            + "</div>";
        table.find("table").before(beforeHtml);

        var switch_search_content = function(selector)
        {
            searchWay = $(selector).find('option:selected').attr('stype');
            searchWayValue = $(selector).val();
            var rid = $(selector).attr('rid');
            if (searchWay == 'input') {
                table.find('.search-content-span.' + rid).html("<input class='search-content " + rid + "' type='text' />"); 
            } else {
                var searchContent = "<select class='search-content " + rid + "'>";
                for (var i in table_options['searchSelect'][searchWayValue]['options'])
                {
                    searchContent += "<option value='"+i+"'>"+table_options['searchSelect'][searchWayValue]['options'][i]+"</option>";
                }
                searchContent += "</select>";
                table.find('.search-content-span.' + rid).html(searchContent);
            }
        }

        table.find('.search-type').change(function(){
            switch_search_content(this);
        });
        table.find('.add_more_key').data('searcher_number', 1);
        table.find('.add_more_key').click(function(){
            var new_searcher_id = $(this).data('searcher_number');
            new_searcher_id++;
            $(this).data('searcher_number', new_searcher_id);
            var new_searcher = "<div class='search_row searcher_" + new_searcher_id + "'>";
            new_searcher += "<select rid='searcher_" + new_searcher_id + "' class='search-type searcher_" + new_searcher_id + "' >" + table.find('.search-type.searcher_1').html() + "</select>";
            new_searcher += "<span class='search-content-span searcher_" + new_searcher_id + "'><input class='search-content searcher_" + new_searcher_id + "' type='text' /></span>";
            new_searcher += "<input type='button' class='remove_searcher searcher_" + new_searcher_id + "' rid='searcher_" + new_searcher_id + "' value='×' /></div>";
            table.find('.search_area') .append(new_searcher);
            $(".search-type.searcher_" + new_searcher_id).change(function(){
                switch_search_content(this);
            });
            $(".remove_searcher.searcher_" + new_searcher_id).click(function(){
                var rid = $(this).attr('rid');
                $("." + rid).remove();
            });
        });
        table.find('.search-type').change();


        var afterHtml = "<div class='fateak-pagination' style='height:35px'><nav><ul></ul></nav></div>";
        afterHtml += "<div style='display:none'>"
            + "<input class='table-page' type='hidden' value='1' />"
            + "<input class='table-sort' type='hidden' />"
            + "<input class='table-order' type='hidden' value='DESC' />";
            + "</div>";
        table.find("table").after(afterHtml);

        table.find("th[fclass='sortable']").each(function(){
            $(this).append("<i table='" + table_options['tid'] + "' class='f-sort-icon glyphicon glyphicon-sort'></i>");         
        });
    }

    var loadData = function(table) {
        var table_options = table.data('options');
        var tb = table.find('tbody');
        tb.html("<tr><td>" + table_options['i18n']['Loading Data...'] + "</td></tr>");
        var container = $("#fateak-table-" + table_options['tid']);

        var page = (arguments[1] != undefined) ? arguments[1] : container.parent().find(".table-page").val();

        if (page > 0) {
            var rowsPerPage = parseInt(container.find(".rows-per-page").val());
            var keytype = [];
            container.find(".search-type").each(function(){
                keytype.push($(this).val()); 
            });
            var keyword = [];
            container.find(".search-content").each(function(){
                keyword.push($(this).val()); 
            });
            var sort = container.parent().find(".table-sort").val();
            var order = container.parent().find(".table-order").val();
        } else {
            var initParams = table_options['initParams'];
            page = (initParams['page'] == undefined) ? 1 : initParams['page'];
            container.parent().find(".table-page").val(page);
            var rowsPerPage = (initParams['rowsPerPage'] == undefined) ? parseInt(container.find(".rows-per-page").val()) : initParams['rowsPerPage'];
            container.find(".rows-per-page").val(rowsPerPage);
            var keytype = [];
            container.find(".search-type").each(function(){
                keytype.push($(this).val()); 
            });
            var keyword = [];
            container.find(".search-content").each(function(){
                keyword.push($(this).val()); 
            });

            var keytype = (initParams['keytype'] == undefined) ? keytype : initParams['keytype'];
            var keyword = (initParams['keyword'] == undefined) ? keyword : initParams['keyword'];
            if (keytype.length > 1) {
                for (var i = 1; i <= keytype.length; i++) {
                    if (i > 1) {
                        container.find('.add_more_key').click();
                    }
                    container.find(".search-type.searcher_" + i).val(keytype[i - 1]);
                    container.find(".search-content.searcher_" + i).val(keyword[i - 1]);
                }
            } else {
                container.find(".search-type").val(keytype);
                container.find(".search-content").val(keyword);
            }
            var sort = (initParams['sort'] == undefined) ? container.parent().find(".table-sort").val() : initParams['sort'];
            container.parent().find(".table-sort").val(sort);
            var order = (initParams['order'] == undefined) ? container.parent().find(".table-order").val() : initParams['order'];
            container.parent().find(".table-order").val(order);
        }

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
                } else if (table_options['columns'][j].indexOf(':') > 0) {
                    var column_value = table_options['columns'][j].split(':');
                    var td_html = objectIn(data[i][column_value[0]], column_value);
                } else if (table_options['columns'][j].indexOf('~') > 0) {
                    var all_values = table_options['columns'][j].split('~');
                    var td_html = "";
                    for (var v in all_values)
                    {
                        td_html += data[i][all_values[v]];
                    }
                } else {
                    var td_html = data[i][table_options['columns'][j]];
                }
                tr += "<td class='" + table_options['columns'][j] + "'><span>" + td_html + "</span></td>"
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
        nextPage = (nextPage < prePage) ? prePage : nextPage;
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
        table.find('.table-page').val(pageNumber);
        loadData(table, pageNumber);
    }

    var bindEvent = function(table) {
        table.find(".rows-per-page").change(function(){
            var container = $(this).parent().parent();
            loadData(container, 1);
        });

        table.find(".search-btn").click(function(){
            var container = $(this).parent().parent().parent().parent();
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

    var objectIn = function(obj, index)
    {
        index.splice(0, 1);

        for (var i in index)
        {
            obj = obj[index[i]];
        }

        return obj;
    }

})(jQuery);
