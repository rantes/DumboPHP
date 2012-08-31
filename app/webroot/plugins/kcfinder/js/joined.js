var browser = {
    opener: {},
    support: {},
    files: [],
    clipboard: [],
    labels: [],
    shows: [],
    orders: [],
    cms: ""
};
browser.initClipboard = function() {
    if (!this.clipboard || !this.clipboard.length) return;
    var size = 0;
    $.each(this.clipboard, function(i, val) {
        size += parseInt(val.size);
    });
    size = this.humanSize(size);
    $('#clipboard').html('<div title="' + this.label("Clipboard") + ' (' + this.clipboard.length + ' ' + this.label("files") + ', ' + size + ')" onclick="browser.openClipboard()"></div>');
    var resize = function() {
        $('#clipboard').css({
            left: $(window).width() - $('#clipboard').outerWidth() + 'px',
            top: $(window).height() - $('#clipboard').outerHeight() + 'px'
        });
    };
    resize();
    $('#clipboard').css('display', 'block');
    $(window).unbind();
    $(window).resize(function() {
        browser.resize();
        resize();
    });
};
browser.initDropUpload = function() {
    if ((typeof(XMLHttpRequest) == 'undefined') ||
        (typeof(document.addEventListener) == 'undefined') ||
        (typeof(File) == 'undefined') ||
        (typeof(FileReader) == 'undefined')
    )
        return;

    if (!XMLHttpRequest.prototype.sendAsBinary) {
        XMLHttpRequest.prototype.sendAsBinary = function(datastr) {
            var ords = Array.prototype.map.call(datastr, function(x) {
                return x.charCodeAt(0) & 0xff;
            });
            var ui8a = new Uint8Array(ords);
            this.send(ui8a.buffer);
        }
    }

    var uploadQueue = [],
        uploadInProgress = false,
        filesCount = 0,
        errors = [],
        files = $('#files'),
        folders = $('div.folder > a'),
        boundary = '------multipartdropuploadboundary' + (new Date).getTime(),
        currentFile,

    filesDragOver = function(e) {
        if (e.preventDefault) e.preventDefault();
        $('#files').addClass('drag');
        return false;
    },

    filesDragEnter = function(e) {
        if (e.preventDefault) e.preventDefault();
        return false;
    },

    filesDragLeave = function(e) {
        if (e.preventDefault) e.preventDefault();
        $('#files').removeClass('drag');
        return false;
    },

    filesDrop = function(e) {
        if (e.preventDefault) e.preventDefault();
        if (e.stopPropagation) e.stopPropagation();
        $('#files').removeClass('drag');
        if (!$('#folders span.current').first().parent().data('writable')) {
            browser.alert("Cannot write to upload folder.");
            return false;
        }
        filesCount += e.dataTransfer.files.length
        for (var i = 0; i < e.dataTransfer.files.length; i++) {
            var file = e.dataTransfer.files[i];
            file.thisTargetDir = browser.dir;
            uploadQueue.push(file);
        }
        processUploadQueue();
        return false;
    },

    folderDrag = function(e) {
        if (e.preventDefault) e.preventDefault();
        return false;
    },

    folderDrop = function(e, dir) {
        if (e.preventDefault) e.preventDefault();
        if (e.stopPropagation) e.stopPropagation();
        if (!$(dir).data('writable')) {
            browser.alert("Cannot write to upload folder.");
            return false;
        }
        filesCount += e.dataTransfer.files.length
        for (var i = 0; i < e.dataTransfer.files.length; i++) {
            var file = e.dataTransfer.files[i];
            file.thisTargetDir = $(dir).data('path');
            uploadQueue.push(file);
        }
        processUploadQueue();
        return false;
    };

    files.get(0).removeEventListener('dragover', filesDragOver, false);
    files.get(0).removeEventListener('dragenter', filesDragEnter, false);
    files.get(0).removeEventListener('dragleave', filesDragLeave, false);
    files.get(0).removeEventListener('drop', filesDrop, false);

    files.get(0).addEventListener('dragover', filesDragOver, false);
    files.get(0).addEventListener('dragenter', filesDragEnter, false);
    files.get(0).addEventListener('dragleave', filesDragLeave, false);
    files.get(0).addEventListener('drop', filesDrop, false);

    folders.each(function() {
        var folder = this,

        dragOver = function(e) {
            $(folder).children('span.folder').addClass('context');
            return folderDrag(e);
        },

        dragLeave = function(e) {
            $(folder).children('span.folder').removeClass('context');
            return folderDrag(e);
        },

        drop = function(e) {
            $(folder).children('span.folder').removeClass('context');
            return folderDrop(e, folder);
        };

        this.removeEventListener('dragover', dragOver, false);
        this.removeEventListener('dragenter', folderDrag, false);
        this.removeEventListener('dragleave', dragLeave, false);
        this.removeEventListener('drop', drop, false);

        this.addEventListener('dragover', dragOver, false);
        this.addEventListener('dragenter', folderDrag, false);
        this.addEventListener('dragleave', dragLeave, false);
        this.addEventListener('drop', drop, false);
    });

    function updateProgress(evt) {
        var progress = evt.lengthComputable
            ? Math.round((evt.loaded * 100) / evt.total) + '%'
            : Math.round(evt.loaded / 1024) + " KB";
        $('#loading').html(browser.label("Uploading file {number} of {count}... {progress}", {
            number: filesCount - uploadQueue.length,
            count: filesCount,
            progress: progress
        }));
    }

    function processUploadQueue() {
        if (uploadInProgress)
            return false;

        if (uploadQueue && uploadQueue.length) {
            var file = uploadQueue.shift();
            currentFile = file;
            $('#loading').html(browser.label("Uploading file {number} of {count}... {progress}", {
                number: filesCount - uploadQueue.length,
                count: filesCount,
                progress: ""
            }));
            $('#loading').css('display', 'inline');

            var reader = new FileReader();
            reader.thisFileName = file.name;
            reader.thisFileType = file.type;
            reader.thisFileSize = file.size;
            reader.thisTargetDir = file.thisTargetDir;

            reader.onload = function(evt) {
                uploadInProgress = true;

                var postbody = '--' + boundary + '\r\nContent-Disposition: form-data; name="upload[]"';
                if (evt.target.thisFileName)
                    postbody += '; filename="' + _.utf8encode(evt.target.thisFileName) + '"';
                postbody += '\r\n';
                if (evt.target.thisFileSize)
                    postbody += 'Content-Length: ' + evt.target.thisFileSize + '\r\n';
                postbody += 'Content-Type: ' + evt.target.thisFileType + '\r\n\r\n' + evt.target.result + '\r\n--' + boundary + '\r\nContent-Disposition: form-data; name="dir"\r\n\r\n' + _.utf8encode(evt.target.thisTargetDir) + '\r\n--' + boundary + '\r\n--' + boundary + '--\r\n';

                var xhr = new XMLHttpRequest();
                xhr.thisFileName = evt.target.thisFileName;

                if (xhr.upload) {
                    xhr.upload.thisFileName = evt.target.thisFileName;
                    xhr.upload.addEventListener("progress", updateProgress, false);
                }
                xhr.open('POST', browser.baseGetData('upload'), true);
                xhr.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + boundary);
                xhr.setRequestHeader('Content-Length', postbody.length);

                xhr.onload = function(e) {
                    $('#loading').css('display', 'none');
                    if (browser.dir == reader.thisTargetDir)
                        browser.fadeFiles();
                    uploadInProgress = false;
                    processUploadQueue();
                    if (xhr.responseText.substr(0, 1) != '/')
                        errors[errors.length] = xhr.responseText;
                }

                xhr.sendAsBinary(postbody);
            };

            reader.onerror = function(evt) {
                $('#loading').css('display', 'none');
                uploadInProgress = false;
                processUploadQueue();
                errors[errors.length] = browser.label("Failed to upload {filename}!", {
                    filename: evt.target.thisFileName
                });
            };

            reader.readAsBinaryString(file);

        } else {
            filesCount = 0;
            var loop = setInterval(function() {
                if (uploadInProgress) return;
                clearInterval(loop);
                if (currentFile.thisTargetDir == browser.dir)
                    browser.refresh();
                boundary = '------multipartdropuploadboundary' + (new Date).getTime();
                if (errors.length) {
                    browser.alert(errors.join('\n'));
                    errors = [];
                }
            }, 333);
        }
    }
};
browser.initFiles = function() {
    $(document).unbind('keydown');
    $(document).keydown(function(e) {
        return !browser.selectAll(e);
    });
    $('#files').unbind();
    $('#files').scroll(function() {
        browser.hideDialog();
    });
    $('.file').unbind();
    $('.file').click(function(e) {
        _.unselect();
        browser.selectFile($(this), e);
    });
    $('.file').rightClick(function(e) {
        _.unselect();
        browser.menuFile($(this), e);
    });
    $('.file').dblclick(function() {
        _.unselect();
        browser.returnFile($(this));
    });
    $('.file').mouseup(function() {
        _.unselect();
    });
    $('.file').mouseout(function() {
        _.unselect();
    });
    $.each(this.shows, function(i, val) {
        var display = (_.kuki.get('show' + val) == 'off')
            ? 'none' : 'block';
        $('#files .file div.' + val).css('display', display);
    });
    this.statusDir();
};

browser.showFiles = function(callBack, selected) {
    this.fadeFiles();
    setTimeout(function() {
        var html = '';
        $.each(browser.files, function(i, file) {
            var stamp = [];
            $.each(file, function(key, val) {
                stamp[stamp.length] = key + "|" + val;
            });
            stamp = _.md5(stamp.join('|'));
            if (_.kuki.get('view') == 'list') {
                if (!i) html += '<table summary="list">';
                var icon = _.getFileExtension(file.name);
                if (file.thumb)
                    icon = '.image';
                else if (!icon.length || !file.smallIcon)
                    icon = '.';
                icon = 'themes/' + browser.theme + '/img/files/small/' + icon + '.png';
                html += '<tr class="file">' +
                    '<td class="name" style="background-image:url(' + icon + ')">' + _.htmlData(file.name) + '</td>' +
                    '<td class="time">' + file.date + '</td>' +
                    '<td class="size">' + browser.humanSize(file.size) + '</td>' +
                '</tr>';
                if (i == browser.files.length - 1) html += '</table>';
            } else {
                if (file.thumb)
                    var icon = browser.baseGetData('thumb') + '&file=' + encodeURIComponent(file.name) + '&dir=' + encodeURIComponent(browser.dir) + '&stamp=' + stamp;
                else if (file.smallThumb) {
                    var icon = browser.uploadURL + '/' + browser.dir + '/' + file.name;
                    icon = _.escapeDirs(icon).replace(/\'/g, "%27");
                } else {
                    var icon = file.bigIcon ? _.getFileExtension(file.name) : '.';
                    if (!icon.length) icon = '.';
                    icon = 'themes/' + browser.theme + '/img/files/big/' + icon + '.png';
                }
                html += '<div class="file">' +
                    '<div class="thumb" style="background-image:url(\'' + icon + '\')" ></div>' +
                    '<div class="name">' + _.htmlData(file.name) + '</div>' +
                    '<div class="time">' + file.date + '</div>' +
                    '<div class="size">' + browser.humanSize(file.size) + '</div>' +
                '</div>';
            }
        });
        $('#files').html('<div>' + html + '<div>');
        $.each(browser.files, function(i, file) {
            var item = $('#files .file').get(i);
            $(item).data(file);
            if (_.inArray(file.name, selected) ||
                ((typeof selected != 'undefined') && !selected.push && (file.name == selected))
            )
                $(item).addClass('selected');
        });
        $('#files > div').css({opacity:'', filter:''});
        if (callBack) callBack();
        browser.initFiles();
    }, 200);
};

browser.selectFile = function(file, e) {
    if (e.ctrlKey || e.metaKey) {
        if (file.hasClass('selected'))
            file.removeClass('selected');
        else
            file.addClass('selected');
        var files = $('.file.selected').get();
        var size = 0;
        if (!files.length)
            this.statusDir();
        else {
            $.each(files, function(i, cfile) {
                size += parseInt($(cfile).data('size'));
            });
            size = this.humanSize(size);
            if (files.length > 1)
                $('#fileinfo').html(files.length + ' ' + this.label("selected files") + ' (' + size + ')');
            else {
                var data = $(files[0]).data();
                $('#fileinfo').html(data.name + ' (' + this.humanSize(data.size) + ', ' + data.date + ')');
            }
        }
    } else {
        var data = file.data();
        $('.file').removeClass('selected');
        file.addClass('selected');
        $('#fileinfo').html(data.name + ' (' + this.humanSize(data.size) + ', ' + data.date + ')');
    }
};

browser.selectAll = function(e) {
    if ((!e.ctrlKey && !e.metaKey) || ((e.keyCode != 65) && (e.keyCode != 97)))
        return false;
    var files = $('.file').get();
    if (files.length) {
        var size = 0;
        $.each(files, function(i, file) {
            if (!$(file).hasClass('selected'))
                $(file).addClass('selected');
            size += parseInt($(file).data('size'));
        });
        size = this.humanSize(size);
        $('#fileinfo').html(files.length + ' ' + this.label("selected files") + ' (' + size + ')');
    }
    return true;
};

browser.returnFile = function(file) {

    var fileURL = file.substr
        ? file : browser.uploadURL + '/' + browser.dir + '/' + file.data('name');
    fileURL = _.escapeDirs(fileURL);

    if (this.opener.CKEditor) {
        this.opener.CKEditor.object.tools.callFunction(this.opener.CKEditor.funcNum, fileURL, '');
        window.close();

    } else if (this.opener.FCKeditor) {
        window.opener.SetUrl(fileURL) ;
        window.close() ;

    } else if (this.opener.TinyMCE) {
        var win = tinyMCEPopup.getWindowArg('window');
        win.document.getElementById(tinyMCEPopup.getWindowArg('input')).value = fileURL;
        if (win.getImageData) win.getImageData();
        if (typeof(win.ImageDialog) != "undefined") {
            if (win.ImageDialog.getImageData)
                win.ImageDialog.getImageData();
            if (win.ImageDialog.showPreviewImage)
                win.ImageDialog.showPreviewImage(fileURL);
        }
        tinyMCEPopup.close();

    } else if (this.opener.callBack) {

        if (window.opener && window.opener.KCFinder) {
            this.opener.callBack(fileURL);
            window.close();
        }

        if (window.parent && window.parent.KCFinder) {
            var button = $('#toolbar a[href="kcact:maximize"]');
            if (button.hasClass('selected'))
                this.maximize(button);
            this.opener.callBack(fileURL);
        }

    } else if (this.opener.callBackMultiple) {
        if (window.opener && window.opener.KCFinder) {
            this.opener.callBackMultiple([fileURL]);
            window.close();
        }

        if (window.parent && window.parent.KCFinder) {
            var button = $('#toolbar a[href="kcact:maximize"]');
            if (button.hasClass('selected'))
                this.maximize(button);
            this.opener.callBackMultiple([fileURL]);
        }

    }
};

browser.returnFiles = function(files) {
    if (this.opener.callBackMultiple && files.length) {
        var rfiles = [];
        $.each(files, function(i, file) {
            rfiles[i] = browser.uploadURL + '/' + browser.dir + '/' + $(file).data('name');
            rfiles[i] = _.escapeDirs(rfiles[i]);
        });
        this.opener.callBackMultiple(rfiles);
        if (window.opener) window.close()
    }
};

browser.returnThumbnails = function(files) {
    if (this.opener.callBackMultiple) {
        var rfiles = [];
        var j = 0;
        $.each(files, function(i, file) {
            if ($(file).data('thumb')) {
                rfiles[j] = browser.thumbsURL + '/' + browser.dir + '/' + $(file).data('name');
                rfiles[j] = _.escapeDirs(rfiles[j++]);
            }
        });
        this.opener.callBackMultiple(rfiles);
        if (window.opener) window.close()
    }
};

browser.menuFile = function(file, e) {
    var data = file.data();
    var path = this.dir + '/' + data.name;
    var files = $('.file.selected').get();
    var html = '';

    if (file.hasClass('selected') && files.length && (files.length > 1)) {
        var thumb = false;
        var notWritable = 0;
        var cdata;
        $.each(files, function(i, cfile) {
            cdata = $(cfile).data();
            if (cdata.thumb) thumb = true;
            if (!data.writable) notWritable++;
        });
        if (this.opener.callBackMultiple) {
            html += '<a href="kcact:pick">' + this.label("Select") + '</a>';
            if (thumb) html +=
                '<a href="kcact:pick_thumb">' + this.label("Select Thumbnails") + '</a>';
        }
        if (data.thumb || data.smallThumb || this.support.zip) {
            html += (html.length ? '<div class="delimiter"></div>' : '');
            if (data.thumb || data.smallThumb)
                html +='<a href="kcact:view">' + this.label("View") + '</a>';
            if (this.support.zip) html += (html.length ? '<div class="delimiter"></div>' : '') +
                '<a href="kcact:download">' + this.label("Download") + '</a>';
        }

        if (this.access.files.copy || this.access.files.move)
            html += (html.length ? '<div class="delimiter"></div>' : '') +
                '<a href="kcact:clpbrdadd">' + this.label("Add to Clipboard") + '</a>';
        if (this.access.files['delete'])
            html += (html.length ? '<div class="delimiter"></div>' : '') +
                '<a href="kcact:rm"' + ((notWritable == files.length) ? ' class="denied"' : '') +
                '>' + this.label("Delete") + '</a>';

        if (html.length) {
            html = '<div class="menu">' + html + '</div>';
            $('#dialog').html(html);
            this.showMenu(e);
        } else
            return;

        $('.menu a[href="kcact:pick"]').click(function() {
            browser.returnFiles(files);
            browser.hideDialog();
            return false;
        });

        $('.menu a[href="kcact:pick_thumb"]').click(function() {
            browser.returnThumbnails(files);
            browser.hideDialog();
            return false;
        });

        $('.menu a[href="kcact:download"]').click(function() {
            browser.hideDialog();
            var pfiles = [];
            $.each(files, function(i, cfile) {
                pfiles[i] = $(cfile).data('name');
            });
            browser.post(browser.baseGetData('downloadSelected'), {dir:browser.dir, files:pfiles});
            return false;
        });

        $('.menu a[href="kcact:clpbrdadd"]').click(function() {
            browser.hideDialog();
            var msg = '';
            $.each(files, function(i, cfile) {
                var cdata = $(cfile).data();
                var failed = false;
                for (i = 0; i < browser.clipboard.length; i++)
                    if ((browser.clipboard[i].name == cdata.name) &&
                        (browser.clipboard[i].dir == browser.dir)
                    ) {
                        failed = true
                        msg += cdata.name + ": " + browser.label("This file is already added to the Clipboard.") + "\n";
                        break;
                    }

                if (!failed) {
                    cdata.dir = browser.dir;
                    browser.clipboard[browser.clipboard.length] = cdata;
                }
            });
            browser.initClipboard();
            if (msg.length) browser.alert(msg.substr(0, msg.length - 1));
            return false;
        });

        $('.menu a[href="kcact:rm"]').click(function() {
            if ($(this).hasClass('denied')) return false;
            browser.hideDialog();
            var failed = 0;
            var dfiles = [];
            $.each(files, function(i, cfile) {
                var cdata = $(cfile).data();
                if (!cdata.writable)
                    failed++;
                else
                    dfiles[dfiles.length] = browser.dir + "/" + cdata.name;
            });
            if (failed == files.length) {
                browser.alert(browser.label("The selected files are not removable."));
                return false;
            }

            var go = function(callBack) {
                browser.fadeFiles();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: browser.baseGetData('rm_cbd'),
                    data: {files:dfiles},
                    async: false,
                    success: function(data) {
                        if (callBack) callBack();
                        browser.check4errors(data);
                        browser.refresh();
                    },
                    error: function() {
                        if (callBack) callBack();
                        $('#files > div').css({
                            opacity: '',
                            filter: ''
                        });
                        browser.alert(browser.label("Unknown error."));
                    }
                });
            };

            if (failed)
                browser.confirm(
                    browser.label("{count} selected files are not removable. Do you want to delete the rest?", {count:failed}),
                    go
                )

            else
                browser.confirm(
                    browser.label("Are you sure you want to delete all selected files?"),
                    go
                );

            return false;
        });

    } else {
        html += '<div class="menu">';
        $('.file').removeClass('selected');
        file.addClass('selected');
        $('#fileinfo').html(data.name + ' (' + this.humanSize(data.size) + ', ' + data.date + ')');
        if (this.opener.callBack || this.opener.callBackMultiple) {
            html += '<a href="kcact:pick">' + this.label("Select") + '</a>';
            if (data.thumb) html +=
                '<a href="kcact:pick_thumb">' + this.label("Select Thumbnail") + '</a>';
            html += '<div class="delimiter"></div>';
        }

        if (data.thumb || data.smallThumb)
            html +='<a href="kcact:view">' + this.label("View") + '</a>';

        html +=
            '<a href="kcact:download">' + this.label("Download") + '</a>';

        if (this.access.files.copy || this.access.files.move)
            html += '<div class="delimiter"></div>' +
                '<a href="kcact:clpbrdadd">' + this.label("Add to Clipboard") + '</a>';
        if (this.access.files.rename || this.access.files['delete'])
            html += '<div class="delimiter"></div>';
        if (this.access.files.rename)
            html += '<a href="kcact:mv"' + (!data.writable ? ' class="denied"' : '') + '>' +
                this.label("Rename...") + '</a>';
        if (this.access.files['delete'])
            html += '<a href="kcact:rm"' + (!data.writable ? ' class="denied"' : '') + '>' +
                this.label("Delete") + '</a>';
        html += '</div>';

        $('#dialog').html(html);
        this.showMenu(e);

        $('.menu a[href="kcact:pick"]').click(function() {
            browser.returnFile(file);
            browser.hideDialog();
            return false;
        });

        $('.menu a[href="kcact:pick_thumb"]').click(function() {
            var path = browser.thumbsURL + '/' + browser.dir + '/' + data.name;
            browser.returnFile(path);
            browser.hideDialog();
            return false;
        });

        $('.menu a[href="kcact:download"]').click(function() {
            var html = '<form id="downloadForm" method="post" action="' + browser.baseGetData('download') + '">' +
                '<input type="hidden" name="dir" />' +
                '<input type="hidden" name="file" />' +
            '</form>';
            $('#dialog').html(html);
            $('#downloadForm input').get(0).value = browser.dir;
            $('#downloadForm input').get(1).value = data.name;
            $('#downloadForm').submit();
            return false;
        });

        $('.menu a[href="kcact:clpbrdadd"]').click(function() {
            for (i = 0; i < browser.clipboard.length; i++)
                if ((browser.clipboard[i].name == data.name) &&
                    (browser.clipboard[i].dir == browser.dir)
                ) {
                    browser.hideDialog();
                    browser.alert(browser.label("This file is already added to the Clipboard."));
                    return false;
                }
            var cdata = data;
            cdata.dir = browser.dir;
            browser.clipboard[browser.clipboard.length] = cdata;
            browser.initClipboard();
            browser.hideDialog();
            return false;
        });

        $('.menu a[href="kcact:mv"]').click(function(e) {
            if (!data.writable) return false;
            browser.fileNameDialog(
                e, {dir: browser.dir, file: data.name},
                'newName', data.name, browser.baseGetData('rename'), {
                    title: "New file name:",
                    errEmpty: "Please enter new file name.",
                    errSlash: "Unallowable characters in file name.",
                    errDot: "File name shouldn't begins with '.'"
                },
                function() {
                    browser.refresh();
                }
            );
            return false;
        });

        $('.menu a[href="kcact:rm"]').click(function() {
            if (!data.writable) return false;
            browser.hideDialog();
            browser.confirm(browser.label("Are you sure you want to delete this file?"),
                function(callBack) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: browser.baseGetData('delete'),
                        data: {dir:browser.dir, file:data.name},
                        async: false,
                        success: function(data) {
                            if (callBack) callBack();
                            browser.clearClipboard();
                            if (browser.check4errors(data))
                                return;
                            browser.refresh();
                        },
                        error: function() {
                            if (callBack) callBack();
                            browser.alert(browser.label("Unknown error."));
                        }
                    });
                }
            );
            return false;
        });
    }

    $('.menu a[href="kcact:view"]').click(function() {
        browser.hideDialog();
        var ts = new Date().getTime();
        var showImage = function(data) {
            url = _.escapeDirs(browser.uploadURL + '/' + browser.dir + '/' + data.name) + '?ts=' + ts,
            $('#loading').html(browser.label("Loading image..."));
            $('#loading').css('display', 'inline');
            var img = new Image();
            img.src = url;
            img.onerror = function() {
                browser.lock = false;
                $('#loading').css('display', 'none');
                browser.alert(browser.label("Unknown error."));
                $(document).unbind('keydown');
                $(document).keydown(function(e) {
                    return !browser.selectAll(e);
                });
                browser.refresh();
            };
            var onImgLoad = function() {
                browser.lock = false;
                $('#files .file').each(function() {
                    if ($(this).data('name') == data.name)
                        browser.ssImage = this;
                });
                $('#loading').css('display', 'none');
                $('#dialog').html('<div class="slideshow"><img /></div>');
                $('#dialog img').attr({
                    src: url,
                    title: data.name
                }).fadeIn('fast', function() {
                    var o_w = $('#dialog').outerWidth();
                    var o_h = $('#dialog').outerHeight();
                    var f_w = $(window).width() - 30;
                    var f_h = $(window).height() - 30;
                    if ((o_w > f_w) || (o_h > f_h)) {
                        if ((f_w / f_h) > (o_w / o_h))
                            f_w = parseInt((o_w * f_h) / o_h);
                        else if ((f_w / f_h) < (o_w / o_h))
                            f_h = parseInt((o_h * f_w) / o_w);
                        $('#dialog img').attr({
                            width: f_w,
                            height: f_h
                        });
                    }
                    $('#dialog').unbind('click');
                    $('#dialog').click(function(e) {
                        browser.hideDialog();
                        $(document).unbind('keydown');
                        $(document).keydown(function(e) {
                            return !browser.selectAll(e);
                        });
                        if (browser.ssImage) {
                            browser.selectFile($(browser.ssImage), e);
                        }
                    });
                    browser.showDialog();
                    var images = [];
                    $.each(browser.files, function(i, file) {
                        if (file.thumb || file.smallThumb)
                            images[images.length] = file;
                    });
                    if (images.length)
                        $.each(images, function(i, image) {
                            if (image.name == data.name) {
                                $(document).unbind('keydown');
                                $(document).keydown(function(e) {
                                    if (images.length > 1) {
                                        if (!browser.lock && (e.keyCode == 37)) {
                                            var nimg = i
                                                ? images[i - 1]
                                                : images[images.length - 1];
                                            browser.lock = true;
                                            showImage(nimg);
                                        }
                                        if (!browser.lock && (e.keyCode == 39)) {
                                            var nimg = (i >= images.length - 1)
                                                ? images[0]
                                                : images[i + 1];
                                            browser.lock = true;
                                            showImage(nimg);
                                        }
                                    }
                                    if (e.keyCode == 27) {
                                        browser.hideDialog();
                                        $(document).unbind('keydown');
                                        $(document).keydown(function(e) {
                                            return !browser.selectAll(e);
                                        });
                                    }
                                });
                            }
                        });
                });
            };
            if (img.complete)
                onImgLoad();
            else
                img.onload = onImgLoad;
        };
        showImage(data);
        return false;
    });
};
browser.initFolders = function() {
    $('#folders').scroll(function() {
        browser.hideDialog();
    });
    $('div.folder > a').unbind();
    $('div.folder > a').bind('click', function() {
        browser.hideDialog();
        return false;
    });
    $('div.folder > a > span.brace').unbind();
    $('div.folder > a > span.brace').click(function() {
        if ($(this).hasClass('opened') || $(this).hasClass('closed'))
            browser.expandDir($(this).parent());
    });
    $('div.folder > a > span.folder').unbind();
    $('div.folder > a > span.folder').click(function() {
        browser.changeDir($(this).parent());
    });
    $('div.folder > a > span.folder').rightClick(function(e) {
        _.unselect();
        browser.menuDir($(this).parent(), e);
    });

    if ($.browser.msie && $.browser.version &&
        (parseInt($.browser.version.substr(0, 1)) < 8)
    ) {
        var fls = $('div.folder').get();
        var body = $('body').get(0);
        var div;
        $.each(fls, function(i, folder) {
            div = document.createElement('div');
            div.style.display = 'inline';
            div.style.margin = div.style.border = div.style.padding = '0';
            div.innerHTML='<table style="border-collapse:collapse;border:0;margin:0;width:0"><tr><td nowrap="nowrap" style="white-space:nowrap;padding:0;border:0">' + $(folder).html() + "</td></tr></table>";
            body.appendChild(div);
            $(folder).css('width', $(div).innerWidth() + 'px');
            body.removeChild(div);
        });
    }
};

browser.setTreeData = function(data, path) {
    if (!path)
        path = '';
    else if (path.length && (path.substr(path.length - 1, 1) != '/'))
        path += '/';
    path += data.name;
    var selector = '#folders a[href="kcdir:/' + _.escapeDirs(path) + '"]';
    $(selector).data({
        name: data.name,
        path: path,
        readable: data.readable,
        writable: data.writable,
        removable: data.removable,
        hasDirs: data.hasDirs
    });
    $(selector + ' span.folder').addClass(data.current ? 'current' : 'regular');
    if (data.dirs && data.dirs.length) {
        $(selector + ' span.brace').addClass('opened');
        $.each(data.dirs, function(i, cdir) {
            browser.setTreeData(cdir, path + '/');
        });
    } else if (data.hasDirs)
        $(selector + ' span.brace').addClass('closed');
};

browser.buildTree = function(root, path) {
    if (!path) path = "";
    path += root.name;
    var html = '<div class="folder"><a href="kcdir:/' + _.escapeDirs(path) + '"><span class="brace">&nbsp;</span><span class="folder">' + _.htmlData(root.name) + '</span></a>';
    if (root.dirs) {
        html += '<div class="folders">';
        for (var i = 0; i < root.dirs.length; i++) {
            cdir = root.dirs[i];
            html += browser.buildTree(cdir, path + '/');
        }
        html += '</div>';
    }
    html += '</div>';
    return html;
};

browser.expandDir = function(dir) {
    var path = dir.data('path');
    if (dir.children('.brace').hasClass('opened')) {
        dir.parent().children('.folders').hide(500, function() {
            if (path == browser.dir.substr(0, path.length))
                browser.changeDir(dir);
        });
        dir.children('.brace').removeClass('opened');
        dir.children('.brace').addClass('closed');
    } else {
        if (dir.parent().children('.folders').get(0)) {
            dir.parent().children('.folders').show(500);
            dir.children('.brace').removeClass('closed');
            dir.children('.brace').addClass('opened');
        } else if (!$('#loadingDirs').get(0)) {
            dir.parent().append('<div id="loadingDirs">' + this.label("Loading folders...") + '</div>');
            $('#loadingDirs').css('display', 'none');
            $('#loadingDirs').show(200, function() {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: browser.baseGetData('expand'),
                    data: {dir:path},
                    async: false,
                    success: function(data) {
                        $('#loadingDirs').hide(200, function() {
                            $('#loadingDirs').detach();
                        });
                        if (browser.check4errors(data))
                            return;

                        var html = '';
                        $.each(data.dirs, function(i, cdir) {
                            html += '<div class="folder"><a href="kcdir:/' + _.escapeDirs(path + '/' + cdir.name) + '"><span class="brace">&nbsp;</span><span class="folder">' + _.htmlData(cdir.name) + '</span></a></div>';
                        });
                        if (html.length) {
                            dir.parent().append('<div class="folders">' + html + '</div>');
                            var folders = $(dir.parent().children('.folders').first());
                            folders.css('display', 'none');
                            $(folders).show(500);
                            $.each(data.dirs, function(i, cdir) {
                                browser.setTreeData(cdir, path);
                            });
                        }
                        if (data.dirs.length) {
                            dir.children('.brace').removeClass('closed');
                            dir.children('.brace').addClass('opened');
                        } else {
                            dir.children('.brace').removeClass('opened');
                            dir.children('.brace').removeClass('closed');
                        }
                        browser.initFolders();
                        browser.initDropUpload();
                    },
                    error: function() {
                        $('#loadingDirs').detach();
                        browser.alert(browser.label("Unknown error."));
                    }
                });
            });
        }
    }
};

browser.changeDir = function(dir) {
    if (dir.children('span.folder').hasClass('regular')) {
        $('div.folder > a > span.folder').removeClass('current');
        $('div.folder > a > span.folder').removeClass('regular');
        $('div.folder > a > span.folder').addClass('regular');
        dir.children('span.folder').removeClass('regular');
        dir.children('span.folder').addClass('current');
        $('#files').html(browser.label("Loading files..."));
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: browser.baseGetData('chDir'),
            data: {dir:dir.data('path')},
            async: false,
            success: function(data) {
                if (browser.check4errors(data))
                    return;
                browser.files = data.files;
                browser.orderFiles();
                browser.dir = dir.data('path');
                browser.dirWritable = data.dirWritable;
                var title = "KCFinder: /" + browser.dir;
                document.title = title;
                if (browser.opener.TinyMCE)
                    tinyMCEPopup.editor.windowManager.setTitle(window, title);
                browser.statusDir();
            },
            error: function() {
                $('#files').html(browser.label("Unknown error."));
            }
        });
    }
};

browser.statusDir = function() {
    for (var i = 0, size = 0; i < this.files.length; i++)
        size += parseInt(this.files[i].size);
    size = this.humanSize(size);
    $('#fileinfo').html(this.files.length + ' ' + this.label("files") + ' (' + size + ')');
};

browser.menuDir = function(dir, e) {
    var data = dir.data();
    var html = '<div class="menu">';
    if (this.clipboard && this.clipboard.length) {
        if (this.access.files.copy)
            html += '<a href="kcact:cpcbd"' + (!data.writable ? ' class="denied"' : '') + '>' +
                this.label("Copy {count} files", {count: this.clipboard.length}) + '</a>';
        if (this.access.files.move)
            html += '<a href="kcact:mvcbd"' + (!data.writable ? ' class="denied"' : '') + '>' +
                this.label("Move {count} files", {count: this.clipboard.length}) + '</a>';
        if (this.access.files.copy || this.access.files.move)
            html += '<div class="delimiter"></div>';
    }
    html +=
        '<a href="kcact:refresh">' + this.label("Refresh") + '</a>';
    if (this.support.zip) html+=
        '<div class="delimiter"></div>' +
        '<a href="kcact:download">' + this.label("Download") + '</a>';
    if (this.access.dirs.create || this.access.dirs.rename || this.access.dirs['delete'])
        html += '<div class="delimiter"></div>';
    if (this.access.dirs.create)
        html += '<a href="kcact:mkdir"' + (!data.writable ? ' class="denied"' : '') + '>' +
            this.label("New Subfolder...") + '</a>';
    if (this.access.dirs.rename)
        html += '<a href="kcact:mvdir"' + (!data.removable ? ' class="denied"' : '') + '>' +
            this.label("Rename...") + '</a>';
    if (this.access.dirs['delete'])
        html += '<a href="kcact:rmdir"' + (!data.removable ? ' class="denied"' : '') + '>' +
            this.label("Delete") + '</a>';
    html += '</div>';

    $('#dialog').html(html);
    this.showMenu(e);
    $('div.folder > a > span.folder').removeClass('context');
    if (dir.children('span.folder').hasClass('regular'))
        dir.children('span.folder').addClass('context');

    if (this.clipboard && this.clipboard.length && data.writable) {

        $('.menu a[href="kcact:cpcbd"]').click(function() {
            browser.hideDialog();
            browser.copyClipboard(data.path);
            return false;
        });

        $('.menu a[href="kcact:mvcbd"]').click(function() {
            browser.hideDialog();
            browser.moveClipboard(data.path);
            return false;
        });
    }

    $('.menu a[href="kcact:refresh"]').click(function() {
        browser.hideDialog();
        browser.refreshDir(dir);
        return false;
    });

    $('.menu a[href="kcact:download"]').click(function() {
        browser.hideDialog();
        browser.post(browser.baseGetData('downloadDir'), {dir:data.path});
        return false;
    });

    $('.menu a[href="kcact:mkdir"]').click(function(e) {
        if (!data.writable) return false;
        browser.hideDialog();
        browser.fileNameDialog(
            e, {dir: data.path},
            'newDir', '', browser.baseGetData('newDir'), {
                title: "New folder name:",
                errEmpty: "Please enter new folder name.",
                errSlash: "Unallowable characters in folder name.",
                errDot: "Folder name shouldn't begins with '.'"
            }, function() {
                browser.refreshDir(dir);
                browser.initDropUpload();
                if (!data.hasDirs) {
                    dir.data('hasDirs', true);
                    dir.children('span.brace').addClass('closed');
                }
            }
        );
        return false;
    });

    $('.menu a[href="kcact:mvdir"]').click(function(e) {
        if (!data.removable) return false;
        browser.hideDialog();
        browser.fileNameDialog(
            e, {dir: data.path},
            'newName', data.name, browser.baseGetData('renameDir'), {
                title: "New folder name:",
                errEmpty: "Please enter new folder name.",
                errSlash: "Unallowable characters in folder name.",
                errDot: "Folder name shouldn't begins with '.'"
            }, function(dt) {
                if (!dt.name) {
                    browser.alert(browser.label("Unknown error."));
                    return;
                }
                var currentDir = (data.path == browser.dir);
                dir.children('span.folder').html(_.htmlData(dt.name));
                dir.data('name', dt.name);
                dir.data('path', _.dirname(data.path) + '/' + dt.name);
                if (currentDir)
                    browser.dir = dir.data('path');
                browser.initDropUpload();
            },
            true
        );
        return false;
    });

    $('.menu a[href="kcact:rmdir"]').click(function() {
        if (!data.removable) return false;
        browser.hideDialog();
        browser.confirm(
            "Are you sure you want to delete this folder and all its content?",
            function(callBack) {
                 $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: browser.baseGetData('deleteDir'),
                    data: {dir: data.path},
                    async: false,
                    success: function(data) {
                        if (callBack) callBack();
                        if (browser.check4errors(data))
                            return;
                        dir.parent().hide(500, function() {
                            var folders = dir.parent().parent();
                            var pDir = folders.parent().children('a').first();
                            dir.parent().detach();
                            if (!folders.children('div.folder').get(0)) {
                                pDir.children('span.brace').first().removeClass('opened');
                                pDir.children('span.brace').first().removeClass('closed');
                                pDir.parent().children('.folders').detach();
                                pDir.data('hasDirs', false);
                            }
                            if (pDir.data('path') == browser.dir.substr(0, pDir.data('path').length))
                                browser.changeDir(pDir);
                            browser.initDropUpload();
                        });
                    },
                    error: function() {
                        if (callBack) callBack();
                        browser.alert(browser.label("Unknown error."));
                    }
                });
            }
        );
        return false;
    });
};

browser.refreshDir = function(dir) {
    var path = dir.data('path');
    if (dir.children('.brace').hasClass('opened') || dir.children('.brace').hasClass('closed')) {
        dir.children('.brace').removeClass('opened');
        dir.children('.brace').addClass('closed');
    }
    dir.parent().children('.folders').first().detach();
    if (path == browser.dir.substr(0, path.length))
        browser.changeDir(dir);
    browser.expandDir(dir);
    return true;
};
browser.init = function() {
    if (!this.checkAgent()) return;

    $('body').click(function() {
        browser.hideDialog();
    });
    $('#shadow').click(function() {
        return false;
    });
    $('#dialog').unbind();
    $('#dialog').click(function() {
        return false;
    });
    $('#alert').unbind();
    $('#alert').click(function() {
        return false;
    });
    this.initOpeners();
    this.initSettings();
    this.initContent();
    this.initToolbar();
    this.initResizer();
    this.initDropUpload();
};

browser.checkAgent = function() {
    if (!$.browser.version ||
        ($.browser.msie && (parseInt($.browser.version) < 7) && !this.support.chromeFrame) ||
        ($.browser.opera && (parseInt($.browser.version) < 10)) ||
        ($.browser.mozilla && (parseFloat($.browser.version.replace(/^(\d+(\.\d+)?)([^\d].*)?$/, "$1")) < 1.8))
    ) {
        var html = '<div style="padding:10px">Your browser is not capable to display KCFinder. Please update your browser or install another one: <a href="http://www.mozilla.com/firefox/" target="_blank">Mozilla Firefox</a>, <a href="http://www.apple.com/safari" target="_blank">Apple Safari</a>, <a href="http://www.google.com/chrome" target="_blank">Google Chrome</a>, <a href="http://www.opera.com/browser" target="_blank">Opera</a>.';
        if ($.browser.msie)
            html += ' You may also install <a href="http://www.google.com/chromeframe" target="_blank">Google Chrome Frame ActiveX plugin</a> to get Internet Explorer 6 working.';
        html += '</div>';
        $('body').html(html);
        return false;
    }
    return true;
};

browser.initOpeners = function() {
    if (this.opener.TinyMCE && (typeof(tinyMCEPopup) == 'undefined'))
        this.opener.TinyMCE = null;

    if (this.opener.TinyMCE)
        this.opener.callBack = true;

    if ((!this.opener.name || (this.opener.name == 'fckeditor')) &&
        window.opener && window.opener.SetUrl
    ) {
        this.opener.FCKeditor = true;
        this.opener.callBack = true;
    }

    if (this.opener.CKEditor) {
        if (window.parent && window.parent.CKEDITOR)
            this.opener.CKEditor.object = window.parent.CKEDITOR;
        else if (window.opener && window.opener.CKEDITOR) {
            this.opener.CKEditor.object = window.opener.CKEDITOR;
            this.opener.callBack = true;
        } else
            this.opener.CKEditor = null;
    }

    if (!this.opener.CKEditor && !this.opener.FCKEditor && !this.TinyMCE) {
        if ((window.opener && window.opener.KCFinder && window.opener.KCFinder.callBack) ||
            (window.parent && window.parent.KCFinder && window.parent.KCFinder.callBack)
        )
            this.opener.callBack = window.opener
                ? window.opener.KCFinder.callBack
                : window.parent.KCFinder.callBack;

        if ((
                window.opener &&
                window.opener.KCFinder &&
                window.opener.KCFinder.callBackMultiple
            ) || (
                window.parent &&
                window.parent.KCFinder &&
                window.parent.KCFinder.callBackMultiple
            )
        )
            this.opener.callBackMultiple = window.opener
                ? window.opener.KCFinder.callBackMultiple
                : window.parent.KCFinder.callBackMultiple;
    }
};

browser.initContent = function() {
    $('div#folders').html(this.label("Loading folders..."));
    $('div#files').html(this.label("Loading files..."));
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: browser.baseGetData('init'),
        async: false,
        success: function(data) {
            if (browser.check4errors(data))
                return;
            browser.dirWritable = data.dirWritable;
            $('#folders').html(browser.buildTree(data.tree));
            browser.setTreeData(data.tree);
            browser.initFolders();
            browser.files = data.files ? data.files : [];
            browser.orderFiles();
        },
        error: function() {
            $('div#folders').html(browser.label("Unknown error."));
            $('div#files').html(browser.label("Unknown error."));
        }
    });
};

browser.initResizer = function() {
    var cursor = ($.browser.opera) ? 'move' : 'col-resize';
    $('#resizer').css('cursor', cursor);
    $('#resizer').drag('start', function() {
        $(this).css({opacity:'0.4', filter:'alpha(opacity:40)'});
        $('#all').css('cursor', cursor);
    });
    $('#resizer').drag(function(e) {
        var left = e.pageX - parseInt(_.nopx($(this).css('width')) / 2);
        left = (left >= 0) ? left : 0;
        left = (left + _.nopx($(this).css('width')) < $(window).width())
            ? left : $(window).width() - _.nopx($(this).css('width'));
		$(this).css('left', left);
	});
	var end = function() {
        $(this).css({opacity:'0', filter:'alpha(opacity:0)'});
        $('#all').css('cursor', '');
        var left = _.nopx($(this).css('left')) + _.nopx($(this).css('width'));
        var right = $(window).width() - left;
        $('#left').css('width', left + 'px');
        $('#right').css('width', right + 'px');
        _('files').style.width = $('#right').innerWidth() - _.outerHSpace('#files') + 'px';
        _('resizer').style.left = $('#left').outerWidth() - _.outerRightSpace('#folders', 'm') + 'px';
        _('resizer').style.width = _.outerRightSpace('#folders', 'm') + _.outerLeftSpace('#files', 'm') + 'px';
        browser.fixFilesHeight();
    };
    $('#resizer').drag('end', end);
    $('#resizer').mouseup(end);
};

browser.resize = function() {
    _('left').style.width = '25%';
    _('right').style.width = '75%';
    _('toolbar').style.height = $('#toolbar a').outerHeight() + "px";
    _('shadow').style.width = $(window).width() + 'px';
    _('shadow').style.height = _('resizer').style.height = $(window).height() + 'px';
    _('left').style.height = _('right').style.height =
        $(window).height() - $('#status').outerHeight() + 'px';
    _('folders').style.height =
        $('#left').outerHeight() - _.outerVSpace('#folders') + 'px';
    browser.fixFilesHeight();
    var width = $('#left').outerWidth() + $('#right').outerWidth();
    _('status').style.width = width + 'px';
    while ($('#status').outerWidth() > width)
        _('status').style.width = _.nopx(_('status').style.width) - 1 + 'px';
    while ($('#status').outerWidth() < width)
        _('status').style.width = _.nopx(_('status').style.width) + 1 + 'px';
    if ($.browser.msie && ($.browser.version.substr(0, 1) < 8))
        _('right').style.width = $(window).width() - $('#left').outerWidth() + 'px';
    _('files').style.width = $('#right').innerWidth() - _.outerHSpace('#files') + 'px';
    _('resizer').style.left = $('#left').outerWidth() - _.outerRightSpace('#folders', 'm') + 'px';
    _('resizer').style.width = _.outerRightSpace('#folders', 'm') + _.outerLeftSpace('#files', 'm') + 'px';
};

browser.fixFilesHeight = function() {
    _('files').style.height =
        $('#left').outerHeight() - $('#toolbar').outerHeight() - _.outerVSpace('#files') -
        (($('#settings').css('display') != "none") ? $('#settings').outerHeight() : 0) + 'px';
};
browser.drag = function(ev, dd) {
    var top = dd.offsetY,
        left = dd.offsetX;
    if (top < 0) top = 0;
    if (left < 0) left = 0;
    if (top + $(this).outerHeight() > $(window).height())
        top = $(window).height() - $(this).outerHeight();
    if (left + $(this).outerWidth() > $(window).width())
        left = $(window).width() - $(this).outerWidth();
    $(this).css({
        top: top,
        left: left
    });
};

browser.showDialog = function(e) {
    $('#dialog').css({left: 0, top: 0});
    this.shadow();
    if ($('#dialog div.box') && !$('#dialog div.title').get(0)) {
        var html = $('#dialog div.box').html();
        var title = $('#dialog').data('title') ? $('#dialog').data('title') : "";
        html = '<div class="title"><span class="close"></span>' + title + '</div>' + html;
        $('#dialog div.box').html(html);
        $('#dialog div.title span.close').mousedown(function() {
            $(this).addClass('clicked');
        });
        $('#dialog div.title span.close').mouseup(function() {
            $(this).removeClass('clicked');
        });
        $('#dialog div.title span.close').click(function() {
            browser.hideDialog();
            browser.hideAlert();
        });
    }
    $('#dialog').drag(browser.drag, {handle: '#dialog div.title'});
    $('#dialog').css('display', 'block');

    if (e) {
        var left = e.pageX - parseInt($('#dialog').outerWidth() / 2);
        var top = e.pageY - parseInt($('#dialog').outerHeight() / 2);
        if (left < 0) left = 0;
        if (top < 0) top = 0;
        if (($('#dialog').outerWidth() + left) > $(window).width())
            left = $(window).width() - $('#dialog').outerWidth();
        if (($('#dialog').outerHeight() + top) > $(window).height())
            top = $(window).height() - $('#dialog').outerHeight();
        $('#dialog').css({
            left: left + 'px',
            top: top + 'px'
        });
    } else
        $('#dialog').css({
            left: parseInt(($(window).width() - $('#dialog').outerWidth()) / 2) + 'px',
            top: parseInt(($(window).height() - $('#dialog').outerHeight()) / 2) + 'px'
        });
    $(document).unbind('keydown');
    $(document).keydown(function(e) {
        if (e.keyCode == 27)
            browser.hideDialog();
    });
};

browser.hideDialog = function() {
    this.unshadow();
    if ($('#clipboard').hasClass('selected'))
        $('#clipboard').removeClass('selected');
    $('#dialog').css('display', 'none');
    $('div.folder > a > span.folder').removeClass('context');
    $('#dialog').html('');
    $('#dialog').data('title', null);
    $('#dialog').unbind();
    $('#dialog').click(function() {
        return false;
    });
    $(document).unbind('keydown');
    $(document).keydown(function(e) {
        return !browser.selectAll(e);
    });
    browser.hideAlert();
};

browser.showAlert = function(shadow) {
    $('#alert').css({left: 0, top: 0});
    if (typeof shadow == 'undefined')
        shadow = true;
    if (shadow)
        this.shadow();
    var left = parseInt(($(window).width() - $('#alert').outerWidth()) / 2),
        top = parseInt(($(window).height() - $('#alert').outerHeight()) / 2);
    var wheight = $(window).height();
    if (top < 0)
        top = 0;
    $('#alert').css({
        left: left + 'px',
        top: top + 'px',
        display: 'block'
    });
    if ($('#alert').outerHeight() > wheight) {
        $('#alert div.message').css({
            height: wheight - $('#alert div.title').outerHeight() - $('#alert div.ok').outerHeight() - 20 + 'px'
        });
    }
    $(document).unbind('keydown');
    $(document).keydown(function(e) {
        if (e.keyCode == 27) {
            browser.hideDialog();
            browser.hideAlert();
            $(document).unbind('keydown');
            $(document).keydown(function(e) {
                return !browser.selectAll(e);
            });
        }
    });
};

browser.hideAlert = function(shadow) {
    if (typeof shadow == 'undefined')
        shadow = true;
    if (shadow)
        this.unshadow();
    $('#alert').css('display', 'none');
    $('#alert').html('');
    $('#alert').data('title', null);
};

browser.alert = function(msg, shadow) {
    msg = msg.replace(/\r?\n/g, "<br />");
    var title = $('#alert').data('title') ? $('#alert').data('title') : browser.label("Attention");
    $('#alert').html('<div class="title"><span class="close"></span>' + title + '</div><div class="message">' + msg + '</div><div class="ok"><button>' + browser.label("OK") + '</button></div>');
    $('#alert div.ok button').click(function() {
        browser.hideAlert(shadow);
    });
    $('#alert div.title span.close').mousedown(function() {
        $(this).addClass('clicked');
    });
    $('#alert div.title span.close').mouseup(function() {
        $(this).removeClass('clicked');
    });
    $('#alert div.title span.close').click(function() {
        browser.hideAlert(shadow);
    });
    $('#alert').drag(browser.drag, {handle: "#alert div.title"});
    browser.showAlert(shadow);
};

browser.confirm = function(question, callBack) {
    $('#dialog').data('title', browser.label("Question"));
    $('#dialog').html('<div class="box"><div class="question">' + browser.label(question) + '<div class="buttons"><button>' + browser.label("No") + '</button> <button>' + browser.label("Yes") + '</button></div></div></div>');
    browser.showDialog();
    $('#dialog div.buttons button').first().click(function() {
        browser.hideDialog();
    });
    $('#dialog div.buttons button').last().click(function() {
        if (callBack)
            callBack(function() {
                browser.hideDialog();
            });
        else
            browser.hideDialog();
    });
    $('#dialog div.buttons button').get(1).focus();
};

browser.shadow = function() {
    $('#shadow').css('display', 'block');
};

browser.unshadow = function() {
    $('#shadow').css('display', 'none');
};

browser.showMenu = function(e) {
    var left = e.pageX;
    var top = e.pageY;
    if (($('#dialog').outerWidth() + left) > $(window).width())
        left = $(window).width() - $('#dialog').outerWidth();
    if (($('#dialog').outerHeight() + top) > $(window).height())
        top = $(window).height() - $('#dialog').outerHeight();
    $('#dialog').css({
        left: left + 'px',
        top: top + 'px',
        display: 'none'
    });
    $('#dialog').fadeIn();
};

browser.fileNameDialog = function(e, post, inputName, inputValue, url, labels, callBack, selectAll) {
    var html = '<form method="post" action="javascript:;">' +
        '<div class="box">' +
        '<input name="' + inputName + '" type="text" /><br />' +
        '<div style="text-align:right">' +
        '<input type="submit" value="' + _.htmlValue(this.label("OK")) + '" /> ' +
        '<input type="button" value="' + _.htmlValue(this.label("Cancel")) + '" onclick="browser.hideDialog(); browser.hideAlert(); return false" />' +
    '</div></div></form>';
    $('#dialog').html(html);
    $('#dialog').data('title', this.label(labels.title));
    $('#dialog input[name="' + inputName + '"]').attr('value', inputValue);
    $('#dialog').unbind();
    $('#dialog').click(function() {
        return false;
    });
    $('#dialog form').submit(function() {
        var name = this.elements[0];
        name.value = $.trim(name.value);
        if (name.value == '') {
            browser.alert(browser.label(labels.errEmpty), false);
            name.focus();
            return;
        } else if (/[\/\\]/g.test(name.value)) {
            browser.alert(browser.label(labels.errSlash), false);
            name.focus();
            return;
        } else if (name.value.substr(0, 1) == ".") {
            browser.alert(browser.label(labels.errDot), false);
            name.focus();
            return;
        }
        eval('post.' + inputName + ' = name.value;');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: url,
            data: post,
            async: false,
            success: function(data) {
                if (browser.check4errors(data, false))
                    return;
                if (callBack) callBack(data);
                browser.hideDialog();
            },
            error: function() {
                browser.alert(browser.label("Unknown error."), false);
            }
        });
        return false;
    });
    browser.showDialog(e);
    $('#dialog').css('display', 'block');
    $('#dialog input[type="submit"]').click(function() {
        return $('#dialog form').submit();
    });
    var field = $('#dialog input[type="text"]');
    var value = field.attr('value');
    if (!selectAll && /^(.+)\.[^\.]+$/ .test(value)) {
        value = value.replace(/^(.+)\.[^\.]+$/, "$1");
        _.selection(field.get(0), 0, value.length);
    } else {
        field.get(0).focus();
        field.get(0).select();
    }
};

browser.orderFiles = function(callBack, selected) {
    var order = _.kuki.get('order');
    var desc = (_.kuki.get('orderDesc') == 'on');

    if (!browser.files || !browser.files.sort)
        browser.files = [];

    browser.files = browser.files.sort(function(a, b) {
        var a1, b1, arr;
        if (!order) order = 'name';

        if (order == 'date') {
            a1 = a.mtime;
            b1 = b.mtime;
        } else if (order == 'type') {
            a1 = _.getFileExtension(a.name);
            b1 = _.getFileExtension(b.name);
        } else if (order == 'size') {
            a1 = a.size;
            b1 = b.size;
        } else
            eval('a1 = a.' + order + '.toLowerCase(); b1 = b.' + order + '.toLowerCase();');

        if ((order == 'size') || (order == 'date')) {
            if (a1 < b1) return desc ? 1 : -1;
            if (a1 > b1) return desc ? -1 : 1;
        }

        if (a1 == b1) {
            a1 = a.name.toLowerCase();
            b1 = b.name.toLowerCase();
            arr = [a1, b1];
            arr = arr.sort();
            return (arr[0] == a1) ? -1 : 1;
        }

        arr = [a1, b1];
        arr = arr.sort();
        if (arr[0] == a1) return desc ? 1 : -1;
        return desc ? -1 : 1;
    });

    browser.showFiles(callBack, selected);
    browser.initFiles();
};

browser.humanSize = function(size) {
    if (size < 1024) {
        size = size.toString() + ' B';
    } else if (size < 1048576) {
        size /= 1024;
        size = parseInt(size).toString() + ' KB';
    } else if (size < 1073741824) {
        size /= 1048576;
        size = parseInt(size).toString() + ' MB';
    } else if (size < 1099511627776) {
        size /= 1073741824;
        size = parseInt(size).toString() + ' GB';
    } else {
        size /= 1099511627776;
        size = parseInt(size).toString() + ' TB';
    }
    return size;
};

browser.baseGetData = function(act) {
    var data = 'browse.php?type=' + encodeURIComponent(this.type) + '&lng=' + this.lang;
    if (act)
        data += "&act=" + act;
    if (this.cms)
        data += "&cms=" + this.cms;
    return data;
};

browser.label = function(index, data) {
    var label = this.labels[index] ? this.labels[index] : index;
    if (data)
        $.each(data, function(key, val) {
            label = label.replace('{' + key + '}', val);
        });
    return label;
};

browser.check4errors = function(data, shadow) {
    if (!data.error)
        return false;
    var msg;
    if (data.error.join)
        msg = data.error.join("\n");
    else
        msg = data.error;
    browser.alert(msg, shadow);
    return true;
};

browser.post = function(url, data) {
    var html = '<form id="postForm" method="POST" action="' + url + '">';
    $.each(data, function(key, val) {
        if ($.isArray(val))
            $.each(val, function(i, aval) {
                html += '<input type="hidden" name="' + _.htmlValue(key) + '[]" value="' + _.htmlValue(aval) + '" />';
            });
        else
            html += '<input type="hidden" name="' + _.htmlValue(key) + '" value="' + _.htmlValue(val) + '" />';
    });
    html += '</form>';
    $('#dialog').html(html);
    $('#dialog').css('display', 'block');
    $('#postForm').get(0).submit();
};

browser.fadeFiles = function() {
    $('#files > div').css({
        opacity: '0.4',
        filter: 'alpha(opacity:40)'
    });
};
browser.initSettings = function() {

    if (!this.shows.length) {
        var showInputs = $('#show input[type="checkbox"]').toArray();
        $.each(showInputs, function (i, input) {
            browser.shows[i] = input.name;
        });
    }

    var shows = this.shows;

    if (!_.kuki.isSet('showname')) {
        _.kuki.set('showname', 'on');
        $.each(shows, function (i, val) {
            if (val != "name") _.kuki.set('show' + val, 'off');
        });
    }

    $('#show input[type="checkbox"]').click(function() {
        var kuki = $(this).get(0).checked ? 'on' : 'off';
        _.kuki.set('show' + $(this).get(0).name, kuki)
        if ($(this).get(0).checked)
            $('#files .file div.' + $(this).get(0).name).css('display', 'block');
        else
            $('#files .file div.' + $(this).get(0).name).css('display', 'none');
    });

    $.each(shows, function(i, val) {
        var checked = (_.kuki.get('show' + val) == 'on') ? 'checked' : '';
        $('#show input[name="' + val + '"]').get(0).checked = checked;
    });

    if (!this.orders.length) {
        var orderInputs = $('#order input[type="radio"]').toArray();
        $.each(orderInputs, function (i, input) {
            browser.orders[i] = input.value;
        });
    }

    var orders = this.orders;

    if (!_.kuki.isSet('order'))
        _.kuki.set('order', 'name');

    if (!_.kuki.isSet('orderDesc'))
        _.kuki.set('orderDesc', 'off');

    $('#order input[value="' + _.kuki.get('order') + '"]').get(0).checked = true;
    $('#order input[name="desc"]').get(0).checked = (_.kuki.get('orderDesc') == 'on');

    $('#order input[type="radio"]').click(function() {
        _.kuki.set('order', $(this).get(0).value);
        browser.orderFiles();
    });

    $('#order input[name="desc"]').click(function() {
        _.kuki.set('orderDesc', $(this).get(0).checked ? 'on' : 'off');
        browser.orderFiles();
    });

    if (!_.kuki.isSet('view'))
        _.kuki.set('view', 'thumbs');

    if (_.kuki.get('view') == 'list') {
        $('#show input').each(function() { this.checked = true; });
        $('#show input').each(function() { this.disabled = true; });
    }

    $('#view input[value="' + _.kuki.get('view') + '"]').get(0).checked = true;

    $('#view input').click(function() {
        var view = $(this).attr('value');
        if (_.kuki.get('view') != view) {
            _.kuki.set('view', view);
            if (view == 'list') {
                $('#show input').each(function() { this.checked = true; });
                $('#show input').each(function() { this.disabled = true; });
            } else {
                $.each(browser.shows, function(i, val) {
                    $('#show input[name="' + val + '"]').get(0).checked =
                        (_.kuki.get('show' + val) == "on");
                });
                $('#show input').each(function() { this.disabled = false; });
            }
        }
        browser.refresh();
    });
};
browser.initToolbar = function() {
    $('#toolbar a').click(function() {
        browser.hideDialog();
    });

    if (!_.kuki.isSet('displaySettings'))
        _.kuki.set('displaySettings', 'off');

    if (_.kuki.get('displaySettings') == 'on') {
        $('#toolbar a[href="kcact:settings"]').addClass('selected');
        $('#settings').css('display', 'block');
        browser.resize();
    }

    $('#toolbar a[href="kcact:settings"]').click(function () {
        if ($('#settings').css('display') == 'none') {
            $(this).addClass('selected');
            _.kuki.set('displaySettings', 'on');
            $('#settings').css('display', 'block');
            browser.fixFilesHeight();
        } else {
            $(this).removeClass('selected');
            _.kuki.set('displaySettings', 'off');
            $('#settings').css('display', 'none');
            browser.fixFilesHeight();
        }
        return false;
    });

    $('#toolbar a[href="kcact:refresh"]').click(function() {
        browser.refresh();
        return false;
    });

    if (window.opener || this.opener.TinyMCE || $('iframe', window.parent.document).get(0))
        $('#toolbar a[href="kcact:maximize"]').click(function() {
            browser.maximize(this);
            return false;
        });
    else
        $('#toolbar a[href="kcact:maximize"]').css('display', 'none');

    $('#toolbar a[href="kcact:about"]').click(function() {
        var html = '<div class="box about">' +
            '<div class="head"><a href="http://kcfinder.sunhater.com" target="_blank">KCFinder</a> ' + browser.version + '</div>';
        if (browser.support.check4Update)
            html += '<div id="checkver"><span class="loading"><span>' + browser.label("Checking for new version...") + '</span></span></div>';
        html +=
            '<div>' + browser.label("Licenses:") + ' GPLv2 & LGPLv2</div>' +
            '<div>Copyright &copy;2010, 2011 Pavel Tzonkov</div>' +
            '<button>' + browser.label("OK") + '</button>' +
        '</div>';
        $('#dialog').html(html);
        $('#dialog').data('title', browser.label("About"));
        browser.showDialog();
        var close = function() {
            browser.hideDialog();
            browser.unshadow();
        }
        $('#dialog button').click(close);
        var span = $('#checkver > span');
        setTimeout(function() {
            $.ajax({
                dataType: 'json',
                url: browser.baseGetData('check4Update'),
                async: true,
                success: function(data) {
                    if (!$('#dialog').html().length)
                        return;
                    span.removeClass('loading');
                    if (!data.version) {
                        span.html(browser.label("Unable to connect!"));
                        browser.showDialog();
                        return;
                    }
                    if (browser.version < data.version)
                        span.html('<a href="http://kcfinder.sunhater.com/download" target="_blank">' + browser.label("Download version {version} now!", {version: data.version}) + '</a>');
                    else
                        span.html(browser.label("KCFinder is up to date!"));
                    browser.showDialog();
                },
                error: function() {
                    if (!$('#dialog').html().length)
                        return;
                    span.removeClass('loading');
                    span.html(browser.label("Unable to connect!"));
                    browser.showDialog();
                }
            });
        }, 1000);
        $('#dialog').unbind();

        return false;
    });

    this.initUploadButton();
};

browser.initUploadButton = function() {
    var btn = $('#toolbar a[href="kcact:upload"]');
    if (!this.access.files.upload) {
        btn.css('display', 'none');
        return;
    }
    var top = btn.get(0).offsetTop;
    var width = btn.outerWidth();
    var height = btn.outerHeight();
    $('#toolbar').prepend('<div id="upload" style="top:' + top + 'px;width:' + width + 'px;height:' + height + 'px">' +
        '<form enctype="multipart/form-data" method="post" target="uploadResponse" action="' + browser.baseGetData('upload') + '">' +
            '<input type="file" name="upload[]" onchange="browser.uploadFile(this.form)" style="height:' + height + 'px" multiple="multiple" />' +
            '<input type="hidden" name="dir" value="" />' +
        '</form>' +
    '</div>');
    $('#upload input').css('margin-left', "-" + ($('#upload input').outerWidth() - width) + 'px');
    $('#upload').mouseover(function() {
        $('#toolbar a[href="kcact:upload"]').addClass('hover');
    });
    $('#upload').mouseout(function() {
        $('#toolbar a[href="kcact:upload"]').removeClass('hover');
    });
};

browser.uploadFile = function(form) {
    if (!this.dirWritable) {
        browser.alert(this.label("Cannot write to upload folder."));
        $('#upload').detach();
        browser.initUploadButton();
        return;
    }
    form.elements[1].value = browser.dir;
    $('<iframe id="uploadResponse" name="uploadResponse" src="javascript:;"></iframe>').prependTo(document.body);
    $('#loading').html(this.label("Uploading file..."));
    $('#loading').css('display', 'inline');
    form.submit();
    $('#uploadResponse').load(function() {
        var response = $(this).contents().find('body').html();
        $('#loading').css('display', 'none');
        response = response.split("\n");
        var selected = [], errors = [];
        $.each(response, function(i, row) {
            if (row.substr(0, 1) == '/')
                selected[selected.length] = row.substr(1, row.length - 1)
            else
                errors[errors.length] = row;
        });
        if (errors.length)
            browser.alert(errors.join("\n"));
        if (!selected.length)
            selected = null
        browser.refresh(selected);
        $('#upload').detach();
        setTimeout(function() {
            $('#uploadResponse').detach();
        }, 1);
        browser.initUploadButton();
    });
};

browser.maximize = function(button) {
    if (window.opener) {
        window.moveTo(0, 0);
        width = screen.availWidth;
        height = screen.availHeight;
        if ($.browser.opera)
            height -= 50;
        window.resizeTo(width, height);

    } else if (browser.opener.TinyMCE) {
        var win, ifr, id;

        $('iframe', window.parent.document).each(function() {
            if (/^mce_\d+_ifr$/.test($(this).attr('id'))) {
                id = parseInt($(this).attr('id').replace(/^mce_(\d+)_ifr$/, "$1"));
                win = $('#mce_' + id, window.parent.document);
                ifr = $('#mce_' + id + '_ifr', window.parent.document);
            }
        });

        if ($(button).hasClass('selected')) {
            $(button).removeClass('selected');
            win.css({
                left: browser.maximizeMCE.left + 'px',
                top: browser.maximizeMCE.top + 'px',
                width: browser.maximizeMCE.width + 'px',
                height: browser.maximizeMCE.height + 'px'
            });
            ifr.css({
                width: browser.maximizeMCE.width - browser.maximizeMCE.Hspace + 'px',
                height: browser.maximizeMCE.height - browser.maximizeMCE.Vspace + 'px'
            });

        } else {
            $(button).addClass('selected')
            browser.maximizeMCE = {
                width: _.nopx(win.css('width')),
                height: _.nopx(win.css('height')),
                left: win.position().left,
                top: win.position().top,
                Hspace: _.nopx(win.css('width')) - _.nopx(ifr.css('width')),
                Vspace: _.nopx(win.css('height')) - _.nopx(ifr.css('height'))
            };
            var width = $(window.parent).width();
            var height = $(window.parent).height();
            win.css({
                left: $(window.parent).scrollLeft() + 'px',
                top: $(window.parent).scrollTop() + 'px',
                width: width + 'px',
                height: height + 'px'
            });
            ifr.css({
                width: width - browser.maximizeMCE.Hspace + 'px',
                height: height - browser.maximizeMCE.Vspace + 'px'
            });
        }

    } else if ($('iframe', window.parent.document).get(0)) {
        var ifrm = $('iframe[name="' + window.name + '"]', window.parent.document);
        var parent = ifrm.parent();
        var width, height;
        if ($(button).hasClass('selected')) {
            $(button).removeClass('selected');
            if (browser.maximizeThread) {
                clearInterval(browser.maximizeThread);
                browser.maximizeThread = null;
            }
            if (browser.maximizeW) browser.maximizeW = null;
            if (browser.maximizeH) browser.maximizeH = null;
            $.each($('*', window.parent.document).get(), function(i, e) {
                e.style.display = browser.maximizeDisplay[i];
            });
            ifrm.css({
                display: browser.maximizeCSS.display,
                position: browser.maximizeCSS.position,
                left: browser.maximizeCSS.left,
                top: browser.maximizeCSS.top,
                width: browser.maximizeCSS.width,
                height: browser.maximizeCSS.height
            });
            $(window.parent).scrollLeft(browser.maximizeLest);
            $(window.parent).scrollTop(browser.maximizeTop);

        } else {
            $(button).addClass('selected');
            browser.maximizeCSS = {
                display: ifrm.css('display'),
                position: ifrm.css('position'),
                left: ifrm.css('left'),
                top: ifrm.css('top'),
                width: ifrm.outerWidth() + 'px',
                height: ifrm.outerHeight() + 'px'
            };
            browser.maximizeTop = $(window.parent).scrollTop();
            browser.maximizeLeft = $(window.parent).scrollLeft();
            browser.maximizeDisplay = [];
            $.each($('*', window.parent.document).get(), function(i, e) {
                browser.maximizeDisplay[i] = $(e).css('display');
                $(e).css('display', 'none');
            });

            ifrm.css('display', 'block');
            ifrm.parents().css('display', 'block');
            var resize = function() {
                width = $(window.parent).width();
                height = $(window.parent).height();
                if (!browser.maximizeW || (browser.maximizeW != width) ||
                    !browser.maximizeH || (browser.maximizeH != height)
                ) {
                    browser.maximizeW = width;
                    browser.maximizeH = height;
                    ifrm.css({
                        width: width + 'px',
                        height: height + 'px'
                    });
                    browser.resize();
                }
            }
            ifrm.css('position', 'absolute');
            if ((ifrm.offset().left == ifrm.position().left) &&
                (ifrm.offset().top == ifrm.position().top)
            )
                ifrm.css({left: '0', top: '0'});
            else
                ifrm.css({
                    left: - ifrm.offset().left + 'px',
                    top: - ifrm.offset().top + 'px'
                });

            resize();
            browser.maximizeThread = setInterval(resize, 250);
        }
    }
};

browser.refresh = function(selected) {
    this.fadeFiles();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: browser.baseGetData('chDir'),
        data: {dir:browser.dir},
        async: false,
        success: function(data) {
            if (browser.check4errors(data))
                return;
            browser.dirWritable = data.dirWritable;
            browser.files = data.files ? data.files : [];
            browser.orderFiles(null, selected);
            browser.statusDir();
        },
        error: function() {
            $('#files > div').css({opacity:'', filter:''});
            $('#files').html(browser.label("Unknown error."));
        }
    });
};
