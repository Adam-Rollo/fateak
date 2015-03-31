<link media="all" rel="stylesheet" href="/assets/css/bootstrap.min.css">
<?php echo HTML::script('/assets/js/jquery-1.11.2.min.js', NULL, TRUE); ?>
<style>
.float {float: left}
.tree {width: 300px}
.tree li {list-style: none}
.tree ul {padding-left: 15px}
.folder {cursor:pointer;padding:2px 3px}
.folder:hover {color:#EFF; background-color:black}
.selector {width:750px}
.selector li {width:146px;padding:10px;height:146px;margin:10px;list-style:none; float:left}
img {cursor: pointer}
img:hover {opacity:0.8}
</style>
<div id='image-selector'>
    <div class='float tree'>
        <?php echo $menu ?>
    </div>

    <div class='float selector'>
        <div id='operation' style='margin-top:20px'>
            <button id='rpd' type='button' class='btn btn-success'>
                <i class="glyphicon glyphicon-circle-arrow-up"></i> <?php echo __("Return Parent Dir") ?>
            </button>
        </div>
        <div id='selector'></div>
    </div>

    <div style='clear:both'></div>

</div>

<script>
(function($){

$().ready(function(){

    $('.folder').click(function(){
        var ul = $('ul[src="'+$(this).attr('src')+'"]');
        if (ul.attr('src') == '') {
            ul.show();
        } else {
            ul.slideToggle();
        }
        var tag_i = $(this).parent().find('i:first');
        if (tag_i.attr('class').indexOf('open') > 0) {
            tag_i.removeClass('glyphicon-folder-open');
            tag_i.addClass('glyphicon-folder-close');
        } else {
            tag_i.addClass('glyphicon-folder-open');
            tag_i.removeClass('glyphicon-folder-close');
        }
        var pt = $(this).parent().parent();
        $("#rpd").attr("pt", pt.attr('src'));
        if (ul.css('display') != "none") {
            var selector = "<ul>";
            ul.children('li').each(function(){
                var li_class = $(this).find('i:first').attr('class');
                if (li_class.indexOf('file') > 0) {
                    var icon = "<?php echo URL::site() ?>assets/images/file.png";
                    var type = "file";
                } else if (li_class.indexOf('folder') > 0) {
                    var icon = "<?php echo URL::site() ?>assets/images/folder.jpg";
                    var type = "folder' folder='" + $(this).children('span').attr('src');
                } else {
                    var icon = "<?php echo $root ?>"+$(this).attr("src");
                    var type = "image";
                }
                selector += "<li><div><div style='height:128px'>"
                    + "<img type='"+type+"' style='width:128px;height:128px' src='"+icon+"' />"
                    + "</div><div style='text-align:center'>"+$(this).attr('name')+"</div></li>";
            });
            selector += "</ul>";
            $("#selector").html(selector);
            $('img').click(function(){
                if ($(this).attr('type') == 'image') {
                    var result = $(this).attr('src').substr(1);
                    window.opener.ckFillImage(result);
                    window.close();
                } else if ($(this).attr('type') == 'folder') {
                   $(".folder[src='"+$(this).attr('folder')+"']").click();
                } else {
                    alert("<?php echo __('Selected File is not a valid picture.') ?>");
                }
            });
        }

    });

    $('.folder[id="rooter"]').click();

    $('#rpd').click(function(){
        var pt = $(this).attr('pt');
        if (pt == "") {
            $("#rooter").click();
        } else {
            $(".folder[src='"+pt+"']").click();
        }
    });

});

})(jQuery);
</script>
