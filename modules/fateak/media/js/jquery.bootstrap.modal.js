/* Fateak-Rollo For Pop div */
(function($){

    var modalSettings = {
        errorMessage: 'Unbelievable Error.'
    };
 
    var formSettings = {
        width: '600px',
        params: {},
        btnWords: {'save':'Save', 'cancel':'Cancel', 'loading':'Loading'},
        formURL: window.location.href, 
        success: function(){alert("Operation successfully.")},
        preSubmit: function(){}
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
            + '<div class="modal-alert" style="margin:10px 20px 0px 20px"></div>'
            + '<div class="modal-body ajax-modal-body" style="max-height:398px;overflow-y:scroll">'
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

        div.find(".fm-save").click(function(){
            var d_exist = div.find(".main-form").attr('class');
            if (d_exist == undefined) {
                div.find(".fm-close").click();
            } else {
                div.find(".main-form").submit();        
            }
        });
    }

    // Append modal to a exist div.
    jQuery.fn.FForm = function(opt) {
        options = mergeObjects(formSettings, opt);
        this.find('.ajax-modal-body').html(options['btnWords']['loading'] + '...');
        this.find('.modal-alert').html('');
        initForm(this);
        return this;
    };

    var initForm = function(div) {
        div.find(".fm-close").html(options['btnWords']['close']);
        div.find(".fm-save").html(options['btnWords']['save']);
        div.find(".modal-dialog").css({'width': options['width']});
        $.getJSON(options.formURL, options.params, function(result){
            div.find(".modal-title").html(result.data.title);
            div.find(".ajax-modal-body").html(result.data.form); 
            div.find(".main-form").on("submit", function(e){
                e.preventDefault();
                options.preSubmit();
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
                        if (data.success == 'Y') {
                            div.find(".modal-alert").html("");
                            div.find(".fm-close").click();
                            options.success();
                        } else {
                            var messages = '<div style="margin-bottom:5px" class="alert alert-warning alert-dismissible" role="alert">'
                                + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
                                + '<span aria-hidden="true">&times;</span></button><ul>';
                            for (var i in data.message)
                                messages += "<li>" + data.message[i] + '</li>';
                            div.find(".modal-alert").html(messages + '</ul></div>');
                        }
                    },
                    error: function(data){
                        console.log(data);
                        alert(modalSettings.errorMessage);
                    }
                });
            });     

        });
    }


})(jQuery);
