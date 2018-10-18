document.addEventListener("DOMContentLoaded", function() {

    document.getElementById('generate_audio_button').onclick = onGenerateButtonClick;

});


function onGenerateButtonClick(e) {
    e.preventDefault();

    var btn     = e.target;
    var txt     = document.getElementById('text_content');
    var voice   = document.getElementById('voice');

    txt.className = '';
    voice.className = '';

    if(txt.value && voice.value) {

        btn.disabled = true;
        txt.disabled = true;
        voice.disabled = true;

        btn.innerText = "Generating...";
    } else {
        if(!txt.value) { txt.className = 'error'; }
        if(!voice.value) { voice.className = 'error'; }
    }




}
