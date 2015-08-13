(function($){

    var settings = {
        fid: 'default',
        uploadURL: '/',
        crop: false,
        cropURL: '/',
        words: {'title':'Upload image', '_confirm':'Confirm', 'close':'Close', 'empty':'You must select an image', 'uploading': 'Uploading...'},
        aspectRatio: null,
        cropAreaWidth: 500,
        displayItemWidth: 680,
        cropAbsolutely: 0,
        maxNum: 1
    };
     
    jQuery.fn.FImage = function(opt) {
        this.data("options", mergeObjects(settings, opt));
        var imageOptions = this.data("options"); 

        initImageModal();

        initImageForm(imageOptions);

        $(this).click(function(){
            showForm(imageOptions);
        });

        return this;
    };

    var initImageModal = function() {

        if($(".image-modal").is('div')) {
            return;
        }

        var popDiv = '<div class="modal fade image-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">'
            + '<div class="modal-dialog">'
            + '<div class="modal-content">'
            + '<div class="modal-header">'
            + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
            + '<h4 class="modal-title upimg-modal-title" id="myModalLabel">' + settings.words.title + '</h4>'
            + '</div>'
            + '<div class="modal-body upload-image-body"></div>'
            + '<div class="modal-footer">'
            + '<button type="button" class="btn btn-default close-iup">' + settings.words.close + '</button>'
            + '<button type="button" class="btn btn-primary confirm-crop">' + settings.words._confirm + '</button>'
            + '</div>'
            + '</div></div></div>';       

        var style = "<style>"
            + ".fimg-item{position:relative;float:left;min-width:10px;height:110px;padding:5px; border:1px solid #CCC;background-color:#DDD;margin:5px}"
            + ".fimg-item-board{position:absolute;left:0px;top:0px;font-size:18px;color:white;width:100%;background-color:black;opacity:0.8;padding-left:5px;line-height:20px;height:25px;display:none}"
            + ".fimg-item-board span{cursor:pointer}"
            + ".fimg-item-board span:hover{color:#AAF}"
            + "</style>";

        $("body").append(popDiv + style);

        // You must load jquery.base.mask.js (By Rollo)
        $("body").initMask();

        $(".close-iup").click(function(){
            $(this).FFindUpper('.modal').modal('hide');
        });

        $(".image-modal").find(".confirm-crop").click(function(){
            var modalDiv = $(this).FFindUpper(".modal");
            var imageName = modalDiv.find(".imgUpForm").attr("upf");
            var options = $("input[upb='" + imageName + "']").data('options');
            if (options.crop) {
                var cropForm = modalDiv.find(".imgCropForm");
                var fileSrc = cropForm.find('.filesrc').val();
                if (fileSrc == "") {
                    alert(options.words.empty);
                    return;
                }
                cropForm.submit();
            } else {
                var imgSrc = modalDiv.find('.imgUpForm').find('img').attr('src');
                putUploadedImageInArea(imgSrc, options);
                $(".close-iup").click();
                initUForm(options.fid);
            }
        });
    }

    var initImageForm = function(options) {
        // Default image fill in
        var original_images = $("input[upi='" + options.fid + "']").val();
        if (original_images != "") {
            original_images = json2arr(original_images);
            $("input[upi='" + options.fid + "']").val("");
            for (var i in original_images) {
                putUploadedImageInArea(original_images[i], options);
            }
        }
    };

    var showForm = function(options) {
        // Init uploader
        var fileForm = "<form class='imgUpForm' action='" + options.uploadURL + "' method='post' upf='" + options.fid + "'>"
            + "<input class='fimageuploader' name='image' type='file' />" 
            + "<div class='croparea' style='margin-top:15px'></div>"
            + "</form>";

        $(".upload-image-body").html(fileForm);
        imgForm = $("form[upf='" + options.fid + "']");

        // Init words
        $(".image-modal").find(".upimg-modal-title").html(options.words.title);
        $(".image-modal").find(".close-iup").html(options.words.close);
        $(".image-modal").find(".confirm-crop").html(options.words._confirm);

        // Init croper
        var crop_recorder = "<input name='crop_x' type='hidden' class='crop-x' />"
            + "<input name='crop_y' type='hidden' class='crop-y' />"
            + "<input name='crop_w' type='hidden' class='crop-w' />"
            + "<input name='crop_h' type='hidden' class='crop-h' />";
        var cropForm = "<form class='imgCropForm' action='" + options.cropURL + "' method='post' upcf='" + options.fid + "'>"
            + "<input class='filesrc' name='filesrc' type='hidden' />" 
            + "<input name='crop_absolutely' value='" + options.cropAbsolutely + "' type='hidden' />" 
            + "<input name='ruler_width' value='" + options.cropAreaWidth + "' type='hidden' />" 
            + crop_recorder
            + "</form>";
        imgForm.after(cropForm);

        imgForm.on('submit', function(e){
            maskOn();
            e.preventDefault();
            var formData = new FormData(this);

            $("form[upf='" + options.fid + "']").find(".croparea").html(options.words.uploading);

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: $(this).attr('action'),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data){
                    if (options.crop) {
                        justDisplay(options, data['data']['image']);
                        startCrop(options, data['data']['image']);
                    } else {
                        justDisplay(options, data['data']['image']);
                    }
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



        $("form[upcf='" + options.fid + "']").on('submit', function(e){
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
                        putUploadedImageInArea(data['data'], options);
                        $(".close-iup").click();
                        initUForm(options.fid);
                    }
                    maskOff();
                },
                error: function(data){
                    console.log("error:"+data);
                    maskOff();
                }
            });               
        });

    }

    var putUploadedImageInArea = function(src, options) {
        var upInput = $("input[upi='" + options.fid + "']"); 
        if (upInput.countValue(true) >= options.maxNum) {
            var imgValues = json2arr(upInput.val());
            $(".fimg-item[img='" + imgValues[0] + "']").find('span').click();
        }

        var imgItem = "<div class='fimg-item' img='" + src + "'>"
            + "<img style='height:100px;max-width:" + options.displayItemWidth + "px' src='" + src + "' />"
            + "<div class='fimg-item-board' ><span>&times;</span></div>"
            + "</div>";
        $("#preupimage-" + FSlash(options.fid)).append(imgItem);
        $(".fimg-item[img='" + src + "']").FUImgManager();
    }

    var startCrop = function(options, image) {

        $("form[upcf='" + options.fid + "']").find(".filesrc").val(image);

        cropConfig = {onSelect: function(c){
            cropimage(options, c);
        }};


        if (options.aspectRatio != null) {
            cropConfig['aspectRatio'] = options.aspectRatio;
        }
            
        $('.croparea').find('img').Jcrop(cropConfig, function(){

            var cImg = $('.croparea').find('.jcrop-holder');
            var cWidth = cImg.width();
            var cHeight = cImg.height();
            if (cHeight * options.aspectRatio > cWidth) {
                cWidth = cHeight * options.aspectRatio;
            } else {
                cHeight = cWidth / options.aspectRatio;
            }
            
            var coordinate = {'x':0, 'y':0, 'w':cWidth, 'h':cHeight};
            cropimage(options, coordinate);

            this.animateTo([0, 0, cWidth, cHeight]);        
        });

    };

    var justDisplay = function(options, image) {
        var img = "<img class='before-crop' style='max-width:" + options.cropAreaWidth + "px' src='" + image + "' />";

        var imgForm = $("form[upf='" + options.fid + "']");
        imgForm.find(".croparea").html(img);
    }

    var cropimage= function(options, c) {
        var imgDiv = $("form[upf='"+options.fid+"']").parent();
        imgDiv.find(".crop-x").val(c.x);
        imgDiv.find(".crop-y").val(c.y);
        imgDiv.find(".crop-w").val(c.w);
        imgDiv.find(".crop-h").val(c.h);
    }

    var initUForm = function(imgName) {
        var imgForm = $("form[upf='" + imgName + "']");
        var cropForm = $("form[upcf='" + imgName + "']");
        imgForm.find(".croparea").html("");
        imgForm.find(".fimageuploader").val("");
        cropForm.find(".filesrc").val("");
    }

    jQuery.fn.FUImgManager = function() {
        var board_timer = null;
        $(this).hover(function(){
            board_timer = setTimeout("$('div[img=\"" + $(this).attr("img") + "\"]').find('.fimg-item-board').slideDown('fast');", 200);
        }, function(){
            $(this).find('.fimg-item-board').slideUp('fast');
            clearTimeout(board_timer);
        });

        $(this).find("span").click(function(){
            var image_name = $(this).FFindUpper('.preupimage').attr('imgarea');
            var imgInput = $("input[upi='" + image_name + "']");
            var imgValues = json2arr(imgInput.val());
            var delImage = $(this).FFindUpper('.fimg-item');
            imgValues = delArrayItem(imgValues, delImage.attr('img'));
            imgInput.val(arr2json(imgValues));
            delImage.remove();
        });

        $(this).find("img").click(function(){
            var img_src = $(this).attr('src');
            window.open(img_src);
        });

        var image_name = $(this).parent().attr('imgarea');
        var imgInput = $("input[upi='" + image_name + "']");
        var original_imgs = imgInput.val();
        if (original_imgs == "") {
            imgInput.val('["' + $(this).attr("img") + '"]');
        } else {
            original_imgs = json2arr(original_imgs);
            original_imgs.push($(this).attr("img"));
            imgInput.val(arr2json(original_imgs));
        }
    };

    
})(jQuery);
