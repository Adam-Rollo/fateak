(function($){
 
    var settings = {
        tid: 'default',
        i18n: {'add':'Add', 'move':'Move', 'delete':'Delete', 'edit':'Edit'},
        dataURL: window.location.href, 
        btnAttributes: "",
        addCallback: function(){},
        moveCallback: function(){},
        editCallback: function(){},
        deleteCallback: function(){},
    };
     
    jQuery.fn.FTree = function(opt) {
        this.data("options", mergeObjects(settings, opt));
        initTree(this);
        return this;
    };

    jQuery.fn.FTreeReload = function(nid) {
        loadTreeNode(this);
    }

    var initTree = function(tree)
    {
        createTreeHTML(tree);
        loadTreeNode(tree);
    }

    var createTreeHTML = function(tree) 
    {
        
    }

    var loadTreeNode = function(tree)
    {
        var treeOptions = tree.data("options");
        $.getJSON(treeOptions['dataURL'], {}, function(result){
            if (result['success'] == 'Y') {
                refreshData(tree, result['data']);
            } else {
                tree.html(result['data']);
            }
        });
    }

    var refreshData = function(tree, data)
    {
        var treeOptions = tree.data("options");
        var html = "<ul tid='" + treeOptions['tid'] + "' class='treeUL'>";

        for (var i in data) {
            html += "<li style='margin-left:" + ( 50 * (data[i]['lvl'] -1) ) + "px'>";
            html += "<div class='treeBody' nid='" + data[i]['id'] + "'>";
            html += "<div class='treeHeader'>" + data[i]['title'] + "</div>";
            html += "<span>" + data[i]['descp'] + "</span>";
            html += "<div class='treeBTN addNode' " + treeOptions['btnAttributes'] + " >" + treeOptions['i18n']['add'] + "</div>";
            html += "<div class='treeBTN moveNode' " + treeOptions['btnAttributes'] + " >" + treeOptions['i18n']['move'] + "</div>";
            html += "<div class='treeBTN editNode' " + treeOptions['btnAttributes'] + " >" + treeOptions['i18n']['edit'] + "</div>";
            html += "<div class='treeBTN deleteNode' " + treeOptions['btnAttributes'] + " >" + treeOptions['i18n']['delete'] + "</div>";
            html += "</div>";
            html += "</li>";
        }

        html += "</ul>";

        tree.html(html);

        treeBindAction(tree);
    }

    var treeBindAction = function(tree) {

        var treeOptions = tree.data("options");

        $(".addNode").click(function(){
            treeOptions['addCallback'].call(this, $(this).parent().attr('nid'));
        });

        $(".editNode").click(function(){
            treeOptions['editCallback'].call(this, $(this).parent().attr('nid'));
        });

        $(".moveNode").click(function(){
            treeOptions['moveCallback'].call(this, $(this).parent().attr('nid'));
        });

        $(".deleteNode").click(function(){
            treeOptions['deleteCallback'].call(this, $(this).parent().attr('nid'));
        });

    }
    
})(jQuery);
