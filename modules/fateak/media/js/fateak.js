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

