(function($){

    var settings = {
        fid: 'default',
        uploadURL: '/',
        cropURL: '/',
        words: {'upload_image':'Upload image', '_confirm':'Confirm', 'close':'Close', 'empty':'You must select an image'},
        aspectRatio: null,
        cropAreaWidth: 500,
        cropAbsolutely: 0,
    };
     
    jQuery.fn.FImage = function(opt) {
        this.options = mergeObjects(settings, opt);
        initImageForm(this);
        return this;
    };

    var initImageForm = function(img) {
        var exDiv = img.FFindUpper('.modal');
        var fileForm = "<form action='" + img.options.uploadURL + "' method='post' upf='" + img.options.fid + "'>"
            + "<input class='fimageuploader' name='image' type='file' />" 
            + "<div class='croparea' style='margin-top:15px'></div>"
            + "</form>";

        var popDiv = '<div class="modal fade image-modal" id="fup-' + img.options.fid + '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">'
            + '<div class="modal-dialog">'
            + '<div class="modal-content">'
            + '<div class="modal-header">'
            + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
            + '<h4 class="modal-title" id="myModalLabel">' + img.options.words.upload_image + '</h4>'
            + '</div>'
            + '<div class="modal-body upload-image-body">' + fileForm + '</div>'
            + '<div class="modal-footer">'
            + '<button type="button" class="btn btn-default close-iup">' + img.options.words.close + '</button>'
            + '<button type="button" class="btn btn-primary confirm-crop">' + img.options.words._confirm + '</button>'
            + '</div>'
            + '</div></div></div>';

        $(".image-modal").remove();

        if (exDiv.is("body")) {
            exDiv.append(popDiv);
        } else {
            exDiv.after(popDiv);
        }

        // You must load jquery.base.mask.js (By Rollo)
        $("body").initMask();

        // Init croper
        var crop_recorder = "<input name='crop_x' type='hidden' class='crop-x' />"
            + "<input name='crop_y' type='hidden' class='crop-y' />"
            + "<input name='crop_w' type='hidden' class='crop-w' />"
            + "<input name='crop_h' type='hidden' class='crop-h' />";
        var cropForm = "<form action='" + img.options.cropURL + "' method='post' upcf='" + img.options.fid + "'>"
            + "<input class='filesrc' name='filesrc' type='hidden' />" 
            + "<input name='crop_absolutely' value='" + img.options.cropAbsolutely + "' type='hidden' />" 
            + "<input name='ruler_width' value='" + img.options.cropAreaWidth + "' type='hidden' />" 
            + "<input class='filesrc' name='filesrc' type='hidden' />" 
            + crop_recorder
            + "</form>";
        var imgForm = $("form[upf='"+img.options.fid+"']");
        imgForm.after(cropForm);

        imgForm.on('submit', function(e){
            maskOn();
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: $(this).attr('action'),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data){
                    startCrop(img, data['data']['image']);
                    maskOff();
                },
                error: function(data){
                    console.log("error:"+data);
                }
            });        
        });

        imgForm.find('input').change(function(){
            var imgForm = $(this).FFindUpper('form');
            imgForm.submit();
        });

        $(".close-iup").click(function(){
            $(this).FFindUpper('.modal').modal('hide');
        });

        $("#fup-" + img.options.fid).find(".confirm-crop").click(function(){
            var container = $(this).FFindUpper('.modal');
            var cropForm = container.find("form[upcf='" + img.options.fid + "']");  
            var fileSrc = cropForm.find('.filesrc').val();
            if (fileSrc == "") {
                alert(img.options.words.empty);
                return;
            }
            cropForm.submit();
        });

        $("form[upcf='" + img.options.fid + "']").on('submit', function(e){
            maskOn();
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: $(this).attr('action'),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data){
                    if (data['success'] == 'Y') {
                        var imgItem = "<div style='position:relative;float:left;min-width:10px;height:120px;padding:10px 5px; border:1px solid #CCC'>"
                            + "<img height='100px' src='" + data['data'] + "' />"
                            + "<div style='position:absolute;left:10px;top:15px;font-size:18px;color:white'>&times;</div>"
                            + "</div>";
                        $("#preupimage-" + img.options.fid).append(imgItem);
                        $(".close-iup").click();
                    }
                    maskOff();
                },
                error: function(data){
                    console.log("error:"+data);
                }
            });               
        });

    }

    var startCrop = function(imginput, image) {
        var img = "<img style='max-width:" + imginput.options.cropAreaWidth + "px' src='" + image + "' />";

        var imgForm = $("form[upf='"+imginput.options.fid+"']");
        imgForm.find(".croparea").html(img);


        $("form[upcf='"+imginput.options.fid+"']").find(".filesrc").val(image);

        cropConfig = {onSelect: function(c){
            cropimage(imginput, c);
        }};

        if (imginput.options.aspectRatio != null)
            cropConfig['aspectRatio'] = imginput.options.aspectRatio;

        $('.croparea').find('img').Jcrop(cropConfig);

    };

    var cropimage= function(img, c) {
        var imgDiv = $("form[upf='"+img.options.fid+"']").parent();
        imgDiv.find(".crop-x").val(c.x);
        imgDiv.find(".crop-y").val(c.y);
        imgDiv.find(".crop-w").val(c.w);
        imgDiv.find(".crop-h").val(c.h);
    }
    
})(jQuery);
