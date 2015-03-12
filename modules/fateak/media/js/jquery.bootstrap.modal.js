/* Fateak-Rollo For Pop div */
(function($){

    var modalSettings = {
        width: '600px',
    };
 
    var formSettings = {
        params: {},
        btnWords: {'save':'Save', 'cancel':'Cancel'},
        formURL: window.location.href, 
    };

    var options = {};
     
    // Append modal to a exist div.
    jQuery.fn.FModal = function(opt) {
        var modalOptions = mergeObjects(modalSettings, opt);
        initModal(this, modalOptions);
        return this;
    };

    var initModal = function(div, opt) {
        var modal = '<!-- Modal -->'
            + '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">'
            + '<div class="modal-dialog">'
            + '<div class="modal-content">'
            + '<div class="modal-header">'
            + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
            + '<h4 class="modal-title" id="myModalLabel">Loading title</h4>'
            + '</div>'
            + '<div class="modal-body">'
            + 'Loading data...'
            + '</div>'
            + '<div class="modal-footer">'
            + '<button type="button" class="btn btn-default fm-close" data-dismiss="modal">Close</button>'
            + '<button type="button" class="btn btn-primary fm-save">Save changes</button>'
            + '</div>'
            + '</div>'
            + '</div>'
            + '</div>';
        div.append(modal);

        div.find(".modal-dialog").css({'width': opt.width});

        div.find(".fm-save").click(function(){
            div.find("form").submit();        
        });
    }

    // Append modal to a exist div.
    jQuery.fn.FForm = function(opt) {
        options = mergeObjects(formSettings, opt);
        initForm(this);
        return this;
    };

    var initForm = function(div) {
        console.log(options);
        div.find(".fm-close").html(options['btnWords']['close']);
        div.find(".fm-save").html(options['btnWords']['save']);
        $.getJSON(options.formURL, options.params, function(result){
            div.find(".modal-title").html(result.data.title);
            div.find(".modal-body").html(result.data.form); 
            div.find("form").on("submit", function(e){
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
                        div.find(".fm-close").click();
                    },
                    error: function(data){
                        var messages = "";
                        for (var i in data.message)
                            messages += data.message[i] + " ";
                        alert(messages);
                    }
                });
            });     

        });
    }


})(jQuery);
