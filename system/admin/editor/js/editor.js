(function () {
    var converter = new Markdown.Converter();
    Markdown.Extra.init(converter);
    var editor = new Markdown.Editor(converter);

    //======Image Uploader=====
    var callbackFunc;
    var dialogClose = function() {
        $('#insertImageDialog').modal('hide');
        $('#insertImageDialogURL').val('');
        $('#insertImageDialogFile').val('');
        $('#insertMediaDialogURL').val('');
        $('#insertMediaDialogFile').val('');
        $('#gallery-1').html(initial_image);
        $('#gallery-2').html(initial_image);		
    };
    $('#insertImageDialogInsert').click( function() {
        callbackFunc( $('#insertImageDialogURL').val().length > 0 ? $('#insertImageDialogURL').val() : null );
        dialogClose();
    });
    $('#insertImageDialogClose').click( function() {
        callbackFunc(null);
        dialogClose();
    });
    $('#insertImageDialogCancel').click( function() {
        callbackFunc(null);
        dialogClose();
    });
    $('#insertImageDialogFile').on('input', function(){
        var file = $("#insertImageDialogFile").prop("files");
        var formData = new FormData();
        formData.append('file', file[0], file[0].name);
        // Set up the request.
        $.ajax({
            type: "POST",
            url: base_path + 'upload.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.error == '0')
                {
                    callbackFunc(base_path + response.path);
                    dialogClose();
                }
                else
                {
                    if (response.error !== '') alert(response.error);
                    else alert("An unknown error has occurred");
                    console.error("Bad Response");
                    console.error(response);
                    $('#insertImageDialogFile').val('');
                }
            },
            failure: function (response) {
                if (response.error !== '') alert(response.error);
                else alert("An unknown error has occurred");
                console.error("Unable to Upload");
                console.error(response);
                $('#insertImageDialogFile').val('');
            }
        });//ajax
    });//oninput
    editor.hooks.set('insertImageDialog', function(callback) {
        $('#insertImageDialog').modal('show');
        callbackFunc = callback;

        return true; // tell the editor that we'll take care of getting the image url
    });
    //=====end image uploader=====
    editor.run();

    // ensure default category is good for all new posts, not the first in alphabetical order
    let noCategory = true;
    for (const el of pCategory.children) {
        if (el.getAttribute('selected')) noCategory = false;
    }
    if (noCategory) pCategory.querySelector('option[value=uncategorized]').selected = true;

    // ensure a default tag is present of Random, to reduce friction
    if (!pTag.value) pTag.value = "Random";

    const postSettings = document.getElementById('post-settings');
    postSettings.classList.remove('row');
    postSettings.classList.add('col-sm-4');
    postSettings.style.float = 'right';
    Array.from(postSettings.children).forEach(el => el.classList.remove('col-sm-6'));
    [document.getElementById('editor-col'), document.getElementById('preview-col')].forEach(el => {
        el.style.flex = '0 0 100%';
        el.style.maxWidth = '100%';    
    });
})();