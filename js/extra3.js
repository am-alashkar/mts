function senddata(id,str) {
    document.getElementById(id).innerHTML = '<i class="spinner-border spinner-border-sm text-cyan"></i>';
    setTimeout(ajx(id, str), 100);
}
function sendform(id,str,form_name) {
    document.getElementById(id).innerHTML = '<i class="spinner-border spinner-border-sm text-cyan"></i>';
    setTimeout(send_form(id,str,form_name), 100);
}
function sendhugeform(id,str,form_name) {
    document.getElementById(id).innerHTML = '<i class="spinner-border spinner-border-sm text-cyan"></i>';
    setTimeout(send_huge_form(id,str,form_name), 100);
}
function ajx(id,str) {
    return function(){
        var hr = new XMLHttpRequest();
        var u = document.getElementById('site_url').value;
        var url = u+"ajax/";
        var vars = str;
        hr.open("POST", url, true);
        hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        hr.onreadystatechange = function() {
            if(hr.readyState === 4 && hr.status === 200) {
                document.getElementById(id).innerHTML = hr.responseText;
                var scripts = document.getElementById(id).getElementsByTagName("script");
                for (var i = 0; i < scripts.length; i++) {
                    if (scripts[i].src !== "") {
                        var tag = document.createElement("script");
                        tag.src = scripts[i].src;
                        document.getElementsByTagName("head")[0].appendChild(tag);
                    } else {
                        eval(scripts[i].innerHTML);
                    }
                }
            }
        }
        hr.send(vars);
        document.getElementById(id).innerHTML = '<i class="spinner-border spinner-border-sm text-green"></i>';
    }
}
function send_form(id,str,form_name) {
    return function(){
        var hr = new XMLHttpRequest();
        var u = document.getElementById('site_url').value;
        var url = u+"ajax/";
        var vars = str;
        var data = "";
        $("[form-name='"+form_name+"']").each(function () {
            if (this.type && this.type === 'checkbox') {
                data += '&' + this.id + '=' + this.checked;
            } else if (this.type && this.type === 'radio') {
                if (this.checked) data += '&' + this.name + '=' + this.value;
            } else if (this.type) {
                data += '&' + this.id + '=' + encodeURIComponent($(this).val());
            }
            //data[this.id] = this.checked;
        });

        hr.open("POST", url, true);
        hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        hr.onreadystatechange = function() {
            if(hr.readyState === 4 && hr.status === 200) {
                document.getElementById(id).innerHTML = hr.responseText;
                var scripts = document.getElementById(id).getElementsByTagName("script");
                for (var i = 0; i < scripts.length; i++) {
                    if (scripts[i].src !== "") {
                        var tag = document.createElement("script");
                        tag.src = scripts[i].src;
                        document.getElementsByTagName("head")[0].appendChild(tag);
                    } else {
                        eval(scripts[i].innerHTML);
                    }
                }
            }
        }
        hr.send(vars+data);
        document.getElementById(id).innerHTML = '<i class="spinner-border spinner-border-sm text-green"></i>';
    }
}

function send_huge_form(id,str,form_name) {
    return function(){
        var hr = new XMLHttpRequest();
        var u = document.getElementById('site_url').value;
        var url = u+"ajax/";
        var vars = str;
        var data = "";
        $("[form-name='"+form_name+"']").each(function () {
            if (this.type && this.type === 'checkbox') {
                data += '🔚🔜' + this.id + '➡➡' + this.checked;
            } else if (this.type && this.type === 'radio') {
                if (this.checked) data += '🔚🔜' + this.name + '➡➡' + this.value;
            } else if (this.type) {
                data += '🔚🔜' + this.id + '➡➡' + encodeURIComponent($(this).val());
            }
            //data[this.id] = this.checked;
        });
        data = '&data='+data+'&is_huge=1';
        hr.open("POST", url, true);
        hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        hr.onreadystatechange = function() {
            if(hr.readyState === 4 && hr.status === 200) {
                document.getElementById(id).innerHTML = hr.responseText;
                var scripts = document.getElementById(id).getElementsByTagName("script");
                for (var i = 0; i < scripts.length; i++) {
                    if (scripts[i].src !== "") {
                        var tag = document.createElement("script");
                        tag.src = scripts[i].src;
                        document.getElementsByTagName("head")[0].appendChild(tag);
                    }
                    else {
                        eval(scripts[i].innerHTML);
                    }
                }
            }
        }
        hr.send(vars+data);
        document.getElementById(id).innerHTML = '<i class="spinner-border spinner-border-sm text-green"></i>';
    }
}


function uploadImage(input_id,prog_area,up_area,script_name){
    var input = document.getElementById(input_id),
        formdata = false;
    var u = document.getElementById('site_url').value;
    if (window.FormData) {
        formdata = new FormData();
    } else {
        return false;
    }
    var file = input.files[0];
    if ( !! file.type.match(/image.*/)) {
        if (formdata) {
            formdata.append("image", file);
            formdata.append("tid", input_id);
            formdata.append("todo", 'upload');
            formdata.append("script", script_name);
        }
    } else {
        $("#"+up_area).html("File Type is not Accepted. Please use: JPG,BMP,PNG,GIF");
        return false;
    }

    $.ajax({
        url: u+"ajax/",
        type: "POST",
        data: formdata,
        processData: false,
        contentType: false,
        xhr: function() {
            var myXhr = $.ajaxSettings.xhr();
            if(myXhr.upload){
                myXhr.upload.addEventListener('progress',({loaded, total}) => {
                    let fileLoaded = ( Math.floor((loaded / total) * 10000)) / 100;
                    let barwidth = fileLoaded;
                    let progressHTML = `
<span class="text-muted"> Uploading </span>
<span class="badge badge-pill badge-warning">${fileLoaded}%</span>
<div class="progress progress-xs">
<div class="progress-bar bg-warning progress-bar-striped" style="width: ${barwidth}%"></div>
</div>`;
                    let progressArea = document.getElementById(prog_area);
                    progressArea.innerHTML = progressHTML;
                    if(loaded === total){
                        progressArea.innerHTML = "";
                        document.getElementById(up_area).innerHTML = '<i class="spinner-border spinner-border-sm text-green"></i>';
                    }
                });
            }
            return myXhr;
        },
        success: function(res) {
            document.getElementById(up_area).innerHTML = res;
            var scripts = document.getElementById(up_area).getElementsByTagName("script");
            for (var i = 0; i < scripts.length; i++) {
                if (scripts[i].src !== "") {
                    var tag = document.createElement("script");
                    tag.src = scripts[i].src;
                    document.getElementsByTagName("head")[0].appendChild(tag);
                } else {
                    eval(scripts[i].innerHTML);
                }
            }
        }
    }, false);
    formdata = new FormData();
}
function upload_file(input_id,prog_area,up_area,script_name){
    var input = document.getElementById(input_id),
        formdata = false;
    var u = document.getElementById('site_url').value;
    if (window.FormData) {
        formdata = new FormData();
    } else {
        return false;
    }
    var file = input.files[0];
    if (formdata) {
        formdata.append("up_file", file);
        formdata.append("tid", input_id);
        formdata.append("todo", 'upload');
        formdata.append("script", script_name);
    }
    document.getElementById(up_area).innerHTML = '<i class="spinner-border spinner-border-sm text-blue"></i>';
    $.ajax({
        url: u+"ajax/",
        type: "POST",
        data: formdata,
        processData: false,
        contentType: false,
        xhr: function() {
            var myXhr = $.ajaxSettings.xhr();
            if(myXhr.upload){
                myXhr.upload.addEventListener('progress',({loaded, total}) => {
                    let fileLoaded = ( Math.floor((loaded / total) * 10000)) / 100;
                    let barwidth = fileLoaded;
                    let progressHTML = `
<span class="text-muted">• Uploading </span>
<span class="badge badge-pill badge-warning">${fileLoaded}%</span>
<div class="progress progress-xs">
<div class="progress-bar bg-warning progress-bar-striped" style="width: ${barwidth}%"></div>
</div>`;
                    let progressArea = document.getElementById(prog_area);
                    progressArea.innerHTML = progressHTML;
                    if(loaded === total){
                        progressArea.innerHTML = "";
                        document.getElementById(up_area).innerHTML = '<i class="spinner-border spinner-border-sm text-green"></i>';
                    }
                });
            }
            return myXhr;
        },
        success: function(res) {
            document.getElementById(up_area).innerHTML = res;
            var scripts = document.getElementById(up_area).getElementsByTagName("script");
            for (var i = 0; i < scripts.length; i++) {
                if (scripts[i].src !== "") {
                    var tag = document.createElement("script");
                    tag.src = scripts[i].src;
                    document.getElementsByTagName("head")[0].appendChild(tag);
                }
                else {
                    eval(scripts[i].innerHTML);
                }
            }
        }
    }, false);
    formdata = new FormData();
}