# upload 

`jquery.fileupload.js` 一个jquery的上传组件.

    <script>
    var swfu1;
    $(document).ready(function(){
        $("#docp").text(pageinfo.curr_page);
        $("#doct").text(pageinfo.totalPage);
        var upload_count = 0;
        $('#img_dfs').fileupload({
            url: "{{URL('account/docupload')}}",
            dataType: 'json',
            formData: {
                webinar_id: "{{$webinar->id}}"
            },
            start: function(e,data) {
                console.log()
            },
            submit: function(e, data) {
                swfu1 = data;
                data.upload_index = upload_count;
                upload_count++;
                if (data.originalFiles.length > 1) {
                    alert('只允许上传一个文件！');
                    return false;
                }
                var acceptFileTypes = /(jpe?g|png|pptx?|xlsx?|docx?|pdf|bmp)$/i;
                var curExt = data.originalFiles[0]['name'].split('.')[data.originalFiles[0]['name'].split('.').length - 1];
                if (!acceptFileTypes.test(curExt)) {
                    alert('文件格式不支持！');
                    return false;
                }
                if (data.originalFiles[0]['size'] && data.originalFiles[0]['size'].length != 0 && data.originalFiles[0]['size'] > 104857600) {
                    alert('您选择的上传文件过大！');
                    return false;
                }
                var _file = {
                    ext : curExt,
                    converted_page : 0,
                    id : 'upload' + data.upload_index,
                    created_at : getNowFormatDate(),
                    hash:0,
                    tpage : 0,
                    page : 0,
                    converted_page_jpeg:0,
                    status : 0,
                    status_jpeg: 0,
                    file_name : data.files[0].name
                };
                $("#doclist .no-file-data").remove();
                if ($("#doclist li:first-child")[0]) {
                    $("#doclist li:first-child").after(getDocListDom(_file));
                } else {
                    $("#doclist").append(getDocListDom(_file));
                }
                function getNowFormatDate() {
                    var date = new Date();
                    var seperator1 = "-";
                    var seperator2 = ":";
                    var year = date.getFullYear();
                    var month = date.getMonth() + 1;
                    var strDate = date.getDate();
                    var strHours = date.getHours();
                    var strMinutes = date.getMinutes();
                    var strSeconds = date.getSeconds()
                    if (month >= 1 && month <= 9) {
                        month = "0" + month;
                    }
                    if (strDate >= 0 && strDate <= 9) {
                        strDate = "0" + strDate;
                    }
                    if (strMinutes >= 0 && strMinutes <= 9) {
                        strMinutes = "0" + strMinutes;
                    }
                    if (strHours >= 0 && strHours <= 9) {
                        strHours = "0" + strHours;
                    }
                    if (strSeconds >= 0 && strSeconds <= 9) {
                        strSeconds = "0" + strSeconds;
                    }
                    var currentdate = year + seperator1 + month + seperator1 + strDate
                            + " " + strHours + seperator2 + strMinutes
                            + seperator2 + strSeconds;
                    return currentdate;
                }
                $("#docli_upload"+ data.upload_index +" .docpages .convert-tips").text("上传0%").next().removeClass('hidden').find('i').css('width', 0 + '%');
            },
            success: function() {
                console.log('success')
            },
            done: function(e, data) {
                data.isuploading = 1;
                if (data.result.code == 200) {
                    var _file = {
                        ext : '',
                        converted_page : 0,
                        id : 'upload',
                        created_at : '',
                        hash:0,
                        tpage : 0,
                        page : 0,
                        converted_page_jpeg:0,
                        status : 0,
                        status_jpeg: 0,
                        file_name : ''
                    };
                    _file = $.extend(_file, data.result.data);
                    $("#docli_upload"+ data.upload_index).replaceWith(getDocListDom(_file));
                } else {
                    $("#docli_upload"+ data.upload_index +" .docpages .convert-tips").text('上传失败').addClass('red').next().addClass('hidden');
                    setTimeout(function(){
                        $("#docli_upload"+ data.upload_index).fadeOut(500, function(){
                            $("#docli_upload"+ data.upload_index).remove();
                        });
                    }, 1500);
                }
            },
            progress: function(e, data) {
                var percent = Math.ceil((data.loaded / data.total) * 100);
                if (percent === 100) {
                    //$("#docli_upload"+ data.upload_index +" .docpages .convert-tips").text('上传成功，文档转换中').next().addClass('hidden');
                } else {
                    $("#docli_upload"+ data.upload_index +" .docpages .convert-tips").text("上传"+percent+"%").next().removeClass('hidden').find('i').css('width', percent + '%');
                }
            },
            fail: function(e, data) {
                console.log(e, data);
                $("#docli_upload"+ data.upload_index +" .docpages .convert-tips").text('上传失败').addClass('red').next().addClass('hidden');
                setTimeout(function(){
                    $("#docli_upload"+ data.upload_index).fadeOut(500, function(){
                        $("#docli_upload"+ data.upload_index).remove();
                    });
                }, 1500);
            },
            error : function(jqXHR, textStatus, thrown) {
                console.log(thrown)
            }
        });
    });
    </script>
    
 主要看 `progress` 回调.    