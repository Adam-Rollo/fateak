(function($){
 
    var settings = {
        lid: 'default',
        opacity: 0
    };
     
    var options = {};
 
    jQuery.fn.initMask = function(opt) {
        options = jQuery.extend(settings, opt);
        initMask(this);
        return this;
    };

    function initMask(container) {
        var pos = container.css('position');
        var width = container.css('width');
        var height = container.css('height');
        var mid = "bmask_"+settings.lid;
        settings.mid = mid;
        settings.style = {};
        settings.style.opacity = settings.opacity;
        settings.style.display = "none";
        settings.style.width = width;
        settings.style.height = height;

        settings.style.margin_left = container.css('margin-left');
        settings.style.margin_top = container.css('margin-top');
        var cp = container.css('position');

        if ($(container).prop("tagName") == 'BODY') {
            var rootPos = $("body");
        } else {
            var rootPos = iteratePosRoot(container);
        }
        var srarr = ['static', 'relative'];
        if (srarr.indexOf(cp) >= 0 ){
            settings.style.position = 'absolute';
            var rootTop = rootPos.offset().top;
            var rootLeft = rootPos.offset().left;
            var selectorTop = container.offset().top;
            var selectorLeft = container.offset().left;
            var deltaTop = selectorTop - rootTop;
            var deltaLeft = selectorLeft - rootLeft;
            settings.style.left = pxPlus(deltaLeft + "px", processAuto(container.css('left')));
            settings.style.top = pxPlus(deltaTop + "px", processAuto(container.css('top')));
        } else {
            settings.style.position = cp;
            settings.style.left = container.css('left');
            settings.style.top = container.css('top');
        }
        settings.style.z_index = "99999";

        var mask = "<div id='"+mid+"' style='background-color:black;";
        for (var i in settings.style) {
            mask += i.replace("_","-") + ":" + settings.style[i] + ";";
        }
        mask += "'></div>"; 
        $(rootPos).append(mask);
    }

    function iteratePosRoot(selector) {
        var p = selector.parent();
        var pp = p.css('position');
        var arr = ['absolute', 'fixed', 'relative'];
        if (arr.indexOf(pp) >= 0 || $(p).prop("tagName") == 'BODY') {
            return p;
        } else {
            return iteratePosRoot(p);
        }
    }

    function pxPlus(sm,sn) {
        m = sm.substr(0,sm.indexOf('px'));
        n = sn.substr(0,sn.indexOf('px'));
        r = parseInt(m) + parseInt(n);
        return r + "px";
    }

    function processAuto(m) {
        if (m == 'auto') {
            return "0px";
        } else {
            return m;
        }
    }

})(jQuery);

function maskOn(lid) {
    lid = arguments[0] ? arguments[0] : "default";
    jQuery("#bmask_" + lid).show();
}

function maskOff(lid) {
    lid = arguments[0] ? arguments[0] : "default";
    jQuery("#bmask_" + lid).hide();
}
