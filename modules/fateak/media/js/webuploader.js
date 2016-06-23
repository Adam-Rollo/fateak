(function($){
 
    var settings = {
        server:null,
        fileName:'file',
        oriFiles: [],
        afterSuccess: function() {},
    };
     
    var options = {};

    var doms = {};
 
    jQuery.fn.Fuploader = function(opt) {
        options = jQuery.extend(settings, opt);

        var container_id = $(this).attr("id");
        makeDoms(this);
        
        var uploader = WebUploader.create({
            // 文件接收服务端。
            server: options['server'],
            // 选择文件的按钮。可选。
            pick: ('#' + $(this).attr("id") + '_input'),
            // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
            resize: false,
            // 提前准备下一个文件，减少耗时
            prepareNextFile: true,
            chunked: true,
            chunkSize: 5000,
            // 多线程
            threads: true,
            formData: {
                fileName: options['fileName']
            }
        });

        uploader.on('fileQueued', F_fileQueued);
        uploader.on('uploadBeforeSend', F_uploadBeforeSend);
        uploader.on('uploadProgress', F_progress);
        uploader.on('uploadSuccess', F_uploadSuccess);

        doms['start'].click(function(){
            console.log('start');
            uploader.upload();
            doms['pause'].show();
        });

        doms['pause'].on('click', function() {
            uploader.stop(true);
            doms['pause'].hide();
            doms['continue'].show();
        });

        doms['continue'].on('click', function() {
            uploader.upload();
            doms['continue'].hide();
            doms['pause'].show();
        });

        return this;
    };

    var makeDoms = function(container) {
        var container_id = container.attr("id");

        container.append('<div id="' + container_id + '_input">选择文件</div>');
        container.append('<button id="' + container_id + '_start" class="btn btn-primary wu_btn" style="">开始上传</button>');
        container.append('<button id="' + container_id + '_pause" class="btn btn-warning wu_btn" style="display:none">暂停上传</button>');
        container.append('<button id="' + container_id + '_continue" class="btn btn-success wu_btn" style="display:none">继续上传</button>');

        var oriFilesHTML = '<ul id="' + container_id + '_list" class="wu_files">';

        for (var i in options['oriFiles']) {
            oriFilesHTML += '<li class="list-group-item">'
                + '<a href="' + options['oriFiles'][i]['url'] +'"><strong>' + options['oriFiles'][i]['name'] + '</strong></a>'
                + '<label class="label label-success">已上传</label>'
                + '</li>';
        }

        oriFilesHTML += "</ul>";

        container.append(oriFilesHTML);

        doms['start'] = $('#' + container_id + '_start');
        doms['pause'] = $('#' + container_id + '_pause');
        doms['continue'] = $('#' + container_id + '_continue');
        doms['list'] = $('#' + container_id + '_list');
    }


    // 当有文件被添加进队列的时候
    var F_fileQueued = function( file ) {
        doms['list'].append( '<li id="' + file.id + '" class="list-group-item">'
          + '<button type="button" class="close itemRemove" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
          + '<strong>' + file.name + '</strong> '
          + '<label class="label label-danger">等待上传</label>'
          + '<a class="wu_remove_file">删除</a><span style="clear:both"></span>'
          + '</li>' );

        var uploader = this;

        $("li#" + file.id).click(function(){
            uploader.removeFile(file.id);

            $.post(options['checkURL'], {action:'remove', name:file.name}, function(result){
                console.log(result);
                $("li#" + file.id).remove();
            });
        });
           
        $.ajax({
            url: options['checkURL'],
            type: 'POST',
            dataType: 'json',
            async:false,
            data: {
                action:'file', name:file.name
            },
        }).then(function(data, textStatus, jqXHR){
            console.log(data);
            if (data.success == 'Y') {
                alert('您已经上传过该文件，如果确定该文件尚未上传，请删除该文件并重新选择该文件。');
                uploader.skipFile(file);
                F_uploadSuccess(file, {data: data.data});
            }
        }, function(jqXHR, textStatus, errorThrown){
            this.skipFile(file);
        });

    };

    // 文件上传过程中创建进度条实时显示。
    var F_progress = function( file, percentage ) {
        var $li = $( '#'+file.id ),
            $percent = $li.find('.progress .progress-bar');

        // 避免重复创建
        if ( !$percent.length ) {
            $percent = $('<div class="progress">' 
                + '<div class="progress-bar progress-bar-success" role="progressbar" style="width: 0%">'
                + '</div>'
                + '</div>').appendTo( $li ).find('.progress-bar');
        }

        $percent.css( 'width', parseInt(percentage * 100) + '%' );
        $percent.text(percentage * 100 + '%');
    };

    var F_uploadSuccess = function( file, response ) {
        console.log(file);
        console.log(response);
        var $file = $( '#'+file.id );
        $file.find('.progress').fadeOut();
        $file.find('.itemRemove').fadeOut();
        $file.find('.label').removeClass('label-danger').addClass('label-success').text('已上传');
        $file.append('<input type="hidden" name="' + options['fileName'] +'[]" value="' + response['data'] + '" />');

        doms['pause'].hide();

        options['afterSuccess']();
    };

    var F_uploadBeforeSend = function (object, data, header) {};

    WebUploader.Uploader.register({"before-send-file": "beforeSendFile", 'before-send': 'beforeSend'}, 
        {
            beforeSendFile: function( file ) {
                var task = new $.Deferred();
            },
            beforeSend: function( block ) {
                var task = new $.Deferred();
                $.ajax({
                    url: options['checkURL'],
                    type: 'POST',
                    dataType: 'json',
                    async:false,
                    data: {
                        action:'chunk', chunk:block.chunk, size:block.blob.size, id:block.file.id, name:block.file.name
                    },
                }).then(function(data, textStatus, jqXHR){
                    console.log(data);
                    if (data.success == 'Y') {
                        console.log('reject');
                        task.reject();
                    } else {
                        task.resolve();
                    }
                }, function(jqXHR, textStatus, errorThrown){
                    task.resolve();
                });

                return task.promise();
            }
        }
    );

})(jQuery);
