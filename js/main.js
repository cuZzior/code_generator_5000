$('document').ready(function(){
    let file;
    let $infoMessage = $('.infoMessage');
    let $filenameToDownload = $('#filenameToDownload');
    let $downloadContainer = $('.downloadContainer');

    $('#generateButton').on('click', function() {
        $downloadContainer.attr('hidden', true);
        $infoMessage.html('');
        let $numbers = $('#formNumberOfCodes');
        let $length = $('#formLengthOfCode');
        if ($filenameToDownload.val() !== '') {
            $.post("generateCodes.php", {removeFile: $filenameToDownload.val()});
        }
        $.post("generateCodes.php", {checkInput: true, numberOfCodes: parseInt($numbers.val()), lengthOfCode: parseInt($length.val())}, function (response) {
            if (response === 'true') {
                generateCodes();
            } else {
                $numbers.val('');
                $length.val('');
                $infoMessage.html(
                    '<div class="alert alert-danger" role="alert">Please input correct values.</div>'
                );

            }
        });
    });

    function generateCodes () {
        let data = $('#main-form').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});
        $.post("generateCodes.php", data, function (response) {
            response = JSON.parse(response);
            if (response.success) {
                file = response.filename;
                showDownloadContainer();
            }
        })
    }

    function showDownloadContainer() {
        $downloadContainer.attr('hidden', false);
        $filenameToDownload.val(file);
        $infoMessage.html(
            '<div class="alert alert-success" role="alert">File: ' + file + ' generated!<br>Click below to download.</div>'
        );
    }
});

$(document).ajaxStart(function() {
    $('html').css({ 'cursor': 'progress' });
}).ajaxStop(function() {
    $('html').css({ 'cursor': 'default' });
});