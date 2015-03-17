(function($){

    var settings = {
        fid: 'default',
        siteURL: '/',
        words: {'upload_image':'Upload image', '_confirm':'Confirm', 'close':'Close'}
    };
     
    jQuery.fn.FImage = function(opt) {
        this.options = mergeObjects(settings, opt);
        initImageForm(this);
        return this;
    };

    var initImageForm = function(img) {
        var exForm = img.FFindUpper('form');
        var fileForm = "<form action='" + img.options.siteURL + "upload' method='post' upf='" + img.options.fid + "'>"
            + "<input class='fimageuploader' name='image' type='file' />" 
            + "<div class='croparea'></div>"
            + "</form>";

        var popDiv = '<div class="modal fade" id="fup-' + img.options.fid + '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">'
            + '<div class="modal-dialog">'
            + '<div class="modal-content">'
            + '<div class="modal-header">'
            + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
            + '<h4 class="modal-title" id="myModalLabel">' + img.options.words.upload_image + '</h4>'
            + '</div>'
            + '<div class="modal-body">' + fileForm + '</div>'
            + '<div class="modal-footer">'
            + '<button type="button" class="btn btn-default close-iup">' + img.options.words.close + '</button>'
            + '<button type="button" class="btn btn-primary">' + img.options.words._confirm + '</button>'
            + '</div>'
            + '</div></div></div>';

        exForm.after(popDiv);

        $("form[upf='" + img.options.fid + "']").on('submit', function(e){
            e.preventDefault();
            var formData = new FormData(this);
 
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data){
                    startCrop();
                },
                error: function(data){
                    console.log("error:"+data);
                }
            });        
        });

        $("form[upf='" + img.options.fid + "']").find('input').change(function(){
            var imgForm = $(this).FFindUpper('form');
            imgForm.submit();
        });

        $(".close-iup").click(function(){
            $(this).FFindUpper('.modal').modal('hide');
        });
    }

    var startCrop = function() {
        console.log('cool');
    };
    
})(jQuery);
