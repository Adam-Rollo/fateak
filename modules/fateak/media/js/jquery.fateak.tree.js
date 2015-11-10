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

        html += createNodeHTML(data.tree, treeOptions);
        if(data.tree.children){
            html += createUlHTML(data.tree, treeOptions);
        }

        html += "</li>";
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

        $(".treeHeader").on("click", function(){
            if($(this).hasClass('treeOpen') || $(this).hasClass('treeClose')){
                $(this).toggleClass('treeOpen');
                $(this).toggleClass('treeClose');
            }
            if($(this).parent().next().length>0){
                $(this).parent().next().slideToggle('fast');
            }
        });

    }

    var createUlHTML = function(node, options)
    {
        var html = "<ul class='treeUL'>";
        for(var i in node.children){
            html += createNodeHTML(node.children[i], options);
            if(node.children[i].children)
                html += createUlHTML(node.children[i], options);
        }
        html += "</ul>";
        return html;
    }

    var createNodeHTML = function(node, options)
    {
        var html = '';
        html += "<li>";
        html += "<div class='treeBody' nid='" + node['id'] + "'>";
        html += "<div class='treeHeader"+ (node['rgt']-node['lft']>1?" treeOpen":"") +"'>" + node['title'] + "</div>";
        html += "<span>" + node['descp'] + "</span>";
        html += "<div class='treeBTN addNode' " + options['btnAttributes'] + " >" + options['i18n']['add'] + "</div>";
        html += "<div class='treeBTN moveNode' " + options['btnAttributes'] + " >" + options['i18n']['move'] + "</div>";
        html += "<div class='treeBTN editNode' " + options['btnAttributes'] + " >" + options['i18n']['edit'] + "</div>";
        html += "<div class='treeBTN deleteNode' " + options['btnAttributes'] + " >" + options['i18n']['delete'] + "</div>";
        html += "</div>";
        return html;
    }

})(jQuery);
