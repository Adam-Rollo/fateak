/*!
 * Fateak v0.0.3 (http://rollosay.com)
 */

/* ========================================================================
    Cookie functions
   ===================================================================== */

function getCookie(c_name)
{
    if (document.cookie.length>0)
    {
        c_start=document.cookie.indexOf(c_name + "=")
        if (c_start!=-1)
        { 
            c_start=c_start + c_name.length+1 
            c_end=document.cookie.indexOf(";",c_start)
            if (c_end==-1) c_end=document.cookie.length
                return unescape(document.cookie.substring(c_start,c_end))
        } 
    }
    return ""
}

function setCookie(c_name,value,expiredays)
{
    var exdate=new Date()
    exdate.setDate(exdate.getDate()+expiredays)
    document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toGMTString()) + ";path=/;"
}

/* ========================================================================
    CSS functions
   ===================================================================== */

function px2int(str)
{
    var offset = str.indexOf('px');
    return parseInt(str.substr(0, offset));
}

/* ========================================================================
    JS object merge
   ===================================================================== */

function mergeObjects(obj1, obj2)
{
    var newOb = {};
    for (var i in obj2) {
        newOb[i] = obj2[i];
    }
    for (var i in obj1) {
        if (! newOb.hasOwnProperty(i)) {
            newOb[i] = obj1[i];
        }
    }
    return newOb;
}

/* ========================================================================
    Json convert to Object or Array and reverse
   ===================================================================== */

function json2obj(str)
{
    return eval('(' + str + ')');
}

function obj2json(obj)
{
    var json = "{";
    var json_array = [];
    for (var i in obj) {
       json_array.push('"' + i + '":"' + obj[i] + '"');
    }
    json += json_array.join(",") + "}";

    return json;
}

function json2arr(str)
{
    if (str == "")
        return [];

    return eval('(' + str + ')');
}

function arr2json(obj)
{
    var json = "[";
    var json_array = [];
    for (var i in obj) {
       json_array.push('"' + obj[i] + '"');
    }
    json += json_array.join(",") + "]";

    return json;

}

/* ========================================================================
    Delete Array item
   ===================================================================== */

function delArrayItem(arr, item)
{
    var offset = null;

    for (var i in arr) {
        if (arr[i] == item) {
            offset = i;
        }
    }

    if (offset != null) {
        arr.splice(offset, 1);
    }

    return arr;
}


/* ========================================================================
    JQuery functions 
   ===================================================================== */

(function($){

    /* ========================================================================
        JQuery find specific parent node
    ===================================================================== */

    jQuery.fn.FFindUpper = function(flag) {
        var upper = this.parent();
        if (upper.is('body'))
        {
            return upper;
        }
        var flagType = flag.substr(0, 1);
        var flagV = flag.substr(1);
        switch (flagType)
        {
        case '#':
            if (upper.attr('id') == flagV) {
                return upper;
            }
            break;
        case '.':
            if (upper.hasClass(flagV)) {
                return upper;
            }
            break;
        default:
            if (upper.is(flag)) {
                return upper;
            }
        }

        return upper.FFindUpper(flag);
    };

    /* ========================================================================
        JQuery count input json value
    ===================================================================== */

    jQuery.fn.countValue = function() {
        var isArr = arguments[0] ? arguments[0] : true;
        var values = $(this).val(); 
        if (isArr) {
            values = json2arr(values);
            return values.length;
        } else {
            var number = 0;
            values = json2obj(values);
            for (var i in values)
                number++;
            return number;
        }
    };

})(jQuery);


/* ========================================================================
    jQuery selector add slashes 
   ===================================================================== */

var FSlash = function (str) {
    str = str.replace(/\[/g, "\\[");
    str = str.replace(/\]/g, "\\]");
    str = str.replace(/\//g, "\\/");
    return str;
}
