/******
PhpPiratebox
Copyright 2013 Sergio Monedero
Released under GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html
******/

window.htmlTemplates = null;

/* Config */
function loadHtmlTemplates() {
    $.ajax({
        context: document.body,
        url: "html-templates.json",
        dataType: "json",
        async: false})
    .done(function(data) {
        window.htmlTemplates = data;
    })
    .fail(function() {
        alertify.error("Whops! Something went wrong.\nCannot load configuration file. Reload the page.");
    });
}

function salute() {
    alertify.alert(htmlTemplates.messages.welcome);
}

function initialize() {
    loadHtmlTemplates();
    salute();
    
    getFileList();
    getMessageList();
    
    $("#navbar").find(".active").find("a").click();
}

/* Global */
function showSection(element, navElement) {
    $("#navbar li").each(function() {
       $(this).removeClass("active");
    });
    
    $(navElement).parent().addClass("active");

    $(".section").each(function() {
        if ($(this).attr("id") === $(element).attr("id")) {
            $(element).addClass("current");
            $(element).fadeIn(150, null);
        } else {
            $(this).hide();
            $(this).removeClass("current");
        }
    });
}


/* Files */
function showFileList(data) {
    var fileList = data.content.fileList;
    var numberOfFiles = fileList.length;

    var tmpFileListItemsHtml = "";
    for (var pos = 0; pos < numberOfFiles; pos++) {
        var fileListItemTemplate = htmlTemplates.files.fileListItem;
        fileListItemTemplate = fileListItemTemplate.replace(/\[FILENAME\]/g, fileList[pos].fileName);

        tmpFileListItemsHtml += fileListItemTemplate;
    }

    var tmpFileListHtml = htmlTemplates.files.fileList;
    tmpFileListHtml = tmpFileListHtml.replace("[FILE_LIST]", tmpFileListItemsHtml);
    $("#file-list-section").html(tmpFileListHtml);

    alertify.success("There're " + numberOfFiles + " files in this piratebox.");
}

function getFileList() {
    $.ajax({
        url: "backend/files.php",
        type: "POST",
        data: {
            action: "getFileList"
        },
        dataType: "json"})
    .done(showFileList)
    .error(function() {
        alertify.error("Whops! Something went wrong.\nPlease try to load list of files again.");
    });
}

function getMaxFileSize() {
    var result = "Unknown";
    
    $.ajax({
        context: document.body,
        url: "backend/files.php",
        type: "POST",
        async: false,
        data: {
            action: "getMaxFileSize"
        },
        dataType: "json"})
    .done(function(data) {
        result = data.content.maxFileSize;
    });

    return result;
}

function updateProgress(element, percent) {
    $(element).css({"width": percent + "%"});
}

function uploadItemUpdateStatus(element, className, message) {
    element.removeClass("alert-info");
    element.addClass("alert-" + className);

    element.find(".progress").removeClass("progress-striped");
    element.find(".upload-progress").addClass("progress-bar-" + className);
    element.find(".upload-progress").html(message);
    
    updateProgress(element.find(".upload-progress"), 100);

    element.find("button").html("Hide");
    element.find("button").click(function() {
    element.fadeOut(150, function() {
            xhr.abort();
            element.remove();
        });
    });
}

function uploadItemError(element, message) {
    uploadItemUpdateStatus(element, "danger", message);
}

function uploadItemSuccess(element, message) {
    uploadItemUpdateStatus(element, "success", message);
}

function showUploadFileDialog() {
    alertify.set({
        labels: {
            ok: "I'm finish"
        }
    });
    alertify.alert(htmlTemplates.files.uploadForm.replace(/\[MAX_FILESIZE\]/g, getMaxFileSize()));

    $("#file-drop-space").click(function() {
        $("#file").click();
    });

    $("#upload-form").fileupload({
        url: "backend/files.php",
        type: "POST",
        dataType: "json",
        fileInput: $("#file"),
        pasteZone: $("#file-upload-content"),
        dropZone: $("#file-upload-content"),
        formData: {
            "action": "uploadFile"
        },
        add: function(e, data) {
            var xhr;
            var uploadStatus = $(htmlTemplates.files.uploadStatus.replace(/\[FILENAME\]/g, data.files[0].name));

            uploadStatus.find(".cancel-upload-button").click(function() {
                uploadStatus.fadeOut(150, function() {
                    xhr.abort();
                    uploadStatus.remove();
                });
            });

            data.context = uploadStatus.appendTo($("#uploading-status-list"));
            xhr = data.submit();
        },
        progress: function(e, data) {
            var percent = parseInt(data.loaded / data.total * 100, 10) + "%";
            updateProgress(data.context.find(".upload-progress"), percent);
        },
        done: function(e, data) {
            if (data.result.isError === true) {
                uploadItemError(data.context, "<p>Server ERROR: " + data.result.error + "</p>");
            } else {
                uploadItemSuccess(data.context, "<p>" + data.result.message + "</p>");
            }

            getFileList();
        },
        fail: function(e, data) {
            uploadItemError(data.context, "<p>Error: [" + + data.errorThrown + "] " + data.textStatus + "</p>");
        }
    });
}

/* Wall */
function showMessageList(data) {
    var messages = data.content.messages;
    var numberOfMessages = messages.length;

    var tmpListItemsHtml = "";
    for (var pos = 0; pos < numberOfMessages; pos++) {
        var message = messages[pos];

        var messageListItemTemplate = htmlTemplates.wall.messageListItem;
        messageListItemTemplate = messageListItemTemplate.replace(/\[MESSAGE\]/, message.message);
        messageListItemTemplate = messageListItemTemplate.replace(/\[USERNAME\]/, (message.username!==null) ? message.username : "Anonymous");
        messageListItemTemplate = messageListItemTemplate.replace(/\[DATE\]/, message.date);

        tmpListItemsHtml += messageListItemTemplate;
    }

    var tmpMessageListHtml = htmlTemplates.wall.messageList;
    tmpMessageListHtml = tmpMessageListHtml.replace("[MESSAGE_LIST]", tmpListItemsHtml);
    $("#message-list-section").html(tmpMessageListHtml);

    alertify.success(numberOfMessages + " messages in wall.");
}

function getMessageList() {
    $.ajax({
        url: "backend/wall.php",
        type: "POST",
        data: {
            action: "getMessages"
        },
        dataType: "json"})
    .done(showMessageList)
    .fail(function() {
        alertify.error("Whops! Something went wrong.\nPlease try to load list of messages again.");
    });
}

function postMessage(message, username) {
    $.ajax({
        context: document.body,
        url: "backend/wall.php",
        type: "POST",
        data: {
            action: "postMessage",
            "message": message,
            "username": username
        },
        dataType: "json"})
    .done(function(data) {
        if (data.isError === true) {
            alertify.error("Server ERROR: " + data.error, "", 0);
        } else {
            alertify.success(data.content.message, "", 0);
        }
        
        getMessageList();
    })
    .fail(function() {
        alertify.error("Whops! Something went wrong.\nMessage may have not been post.");
    });
}

function showPostMessageDialog() {
    alertify.set({
        labels: {
            ok: "Post message",
            cancel: "Cancel"
        }
    });
    
    alertify.prompt(htmlTemplates.wall.postMessageForm, function(e) {
        if (e) {
            var message = $("#message").val();
            var username = $("#username").val();

            postMessage(message, username);
        } else {
            alertify.error("Action cancelled. Message wasn't published.");
        }
    });
}