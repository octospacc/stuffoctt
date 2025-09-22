if (localStorage.getItem("preview-state") === "open") {
    document.getElementById("editor-col").classList.remove('col-sm-12');
    document.getElementById("editor-col").classList.add('col-sm-6');
    document.getElementById("preview-col").style.display = '';
} else if (localStorage.getItem("preview-state") === "close") {
    document.getElementById("editor-col").classList.remove('col-sm-6');
    document.getElementById("editor-col").classList.add('col-sm-12');
    document.getElementById("preview-col").style.display = 'none';
}
document.getElementById("preview-toggle").addEventListener("click", function(){
    if (document.getElementById("editor-col").className.includes("col-sm-6")) {
        document.getElementById("editor-col").classList.remove('col-sm-6');
        document.getElementById("editor-col").classList.add('col-sm-12');
        document.getElementById("preview-col").style.display = 'none';
        localStorage.setItem("preview-state", 'close');
    } else {
        document.getElementById("editor-col").classList.remove('col-sm-12');
        document.getElementById("editor-col").classList.add('col-sm-6');
        document.getElementById("preview-col").style.display = '';
        localStorage.setItem("preview-state", 'open');
    }
});