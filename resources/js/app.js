(function(window){
    'use strict';

    function define_app() {
        var TTS = {};

        //----- Set Defaults -----//
        TTS.defaults = {
            elementIDs: {
                form:   'text_content_form',
                text:   'text_content',
                voice:  'voice',
                submit: 'generate_audio_button',
                output: 'form_output_container'
            },
            inputClasses: {
                default:    '',
                error:      'error',
                success:    'success'
            },
            btnText: {
                default: 'Submit',
                loading: 'Generating...'
            }
        };

        //----- Initiate Cache Variables -----//
        TTS.cache = {
            elements: {
                form:   null,
                text:   null,
                voice:  null,
                submit: null,
                output: null
            },
            data: {
                submit: {
                    text:   null,
                    voice:  null
                },
                return: {
                    messages:   [],
                    success:    false,
                    audioPath:  null,
                    audioName:  null
                }
            }
        };


        //----- Public Functions -----//
        TTS.onGenerateButtonClick = function(e) {
            e.preventDefault();

            if( validateForm() ) {
                disableValidForm();
                submitForm();
            }
        };


        //----- Private Helper Functions -----//
        function validateForm() {
            return ( isTextInputValid() && isVoiceInputValid() );
        }

        function submitForm() {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/', true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.responseType = 'json';
            xhr.onload = function() {
                formSubmitCallback(xhr);
            };

            xhr.send( JSON.stringify(getTTSFormRequestData(true)) );
        }

        function formSubmitCallback(xhr) {
            enableForm();

            console.log(xhr.response); // debug

            if(xhr.status === 200) {
                TTS.cache.data.return.messages  = xhr.response.messages;
                TTS.cache.data.return.success   = xhr.response.success;
                TTS.cache.data.return.audioPath = xhr.response.audio_path;
                TTS.cache.data.return.audioName = xhr.response.audio_name;
            } else {
                // todo: on submit error
            }

            outputRequestResponse();
        }

        function outputRequestResponse() {
            var outputHtml = "";

            var success = TTS.cache.data.return.success;
            var msgs    = TTS.cache.data.return.messages;
            var url     = TTS.cache.data.return.audioPath;
            var name    = TTS.cache.data.return.audioName;

            // Show success or fail
            getOutputElement().className = ( success ? getElementSuccessClass() : getElementErrorClass() );

            // Show messages
            if(msgs.length > 0) {
                for(var i in msgs) {
                    outputHtml += "<span>"+ msgs[i] +"</span><br />";
                }
            }

            // show audio url if available
            if(url) {
                outputHtml += "<hr><a href='" + url +"' target='_blank'>"+ name +"</a><br />"

                outputHtml += "<audio class='audio-player' controls>" +
                    "<source src='" + url + "' type='audio/mpeg'>" +
                    "Your browser does not support the audio element." +
                    "</audio><br />";
            }

            getOutputElement().innerHTML += outputHtml;
        }

        function getTTSFormRequestData(refresh = true) {
            if(refresh) {
                TTS.cache.data.submit.text   = getTextInputValue();
                TTS.cache.data.submit.voice  = getVoiceInputValue();
            }

            return {
                method: 'tts',
                text: TTS.cache.data.submit.text,
                voice: TTS.cache.data.submit.voice
            };
        }

        // Get form elements
        function getFormElement() {
            if(!TTS.cache.elements.form) {
                TTS.cache.elements.form = document.getElementById(TTS.defaults.elementIDs.form);
            }

            return TTS.cache.elements.text;
        }

        function getTextInputElement() {
            if(!TTS.cache.elements.text) {
                TTS.cache.elements.text = document.getElementById(TTS.defaults.elementIDs.text);
            }

            return TTS.cache.elements.text;
        }

        function getVoiceInputElement() {
            if(!TTS.cache.elements.voice) {
                TTS.cache.elements.voice = document.getElementById(TTS.defaults.elementIDs.voice);
            }

            return TTS.cache.elements.voice;
        }

        function getSubmitButtonElement() {
            if(!TTS.cache.elements.submit) {
                TTS.cache.elements.submit = document.getElementById(TTS.defaults.elementIDs.submit);
            }

            return TTS.cache.elements.submit;
        }

        function getOutputElement() {
            if(!TTS.cache.elements.output) {
                TTS.cache.elements.output = document.getElementById(TTS.defaults.elementIDs.output);
            }

            return TTS.cache.elements.output;
        }

        // Get form input values
        function getTextInputValue() {
            return getTextInputElement().value;
        }

        function getVoiceInputValue() {
            return getVoiceInputElement().value;
        }

        // Check if input is valid and change its class
        function isTextInputValid() {
            if( getTextInputValue() ) {
                getTextInputElement().className = getElementSuccessClass();
                return true;
            } else {
                getTextInputElement().className = getElementErrorClass();
                return false;
            }
        }

        function isVoiceInputValid() {
            if( getVoiceInputValue() ) {
                getVoiceInputElement().className = getElementSuccessClass();
                return true;
            } else {
                getVoiceInputElement().className = getElementErrorClass();
                return false;
            }
        }

        // Get Default Element Classes
        function getElementDefaultClass() { return TTS.defaults.inputClasses.default; }
        function getElementErrorClass() { return TTS.defaults.inputClasses.error; }
        function getElementSuccessClass() { return TTS.defaults.inputClasses.success; }


        function disableValidForm() {
            // Set input classes to default
            getTextInputElement().className = getElementDefaultClass();
            getVoiceInputElement().className = getElementDefaultClass();

            // Change button text
            getSubmitButtonElement().innerText = TTS.defaults.btnText.loading;

            // Disable inputs
            getTextInputElement().disabled = true;
            getVoiceInputElement().disabled = true;
            getSubmitButtonElement().disabled = true;
        }

        function enableForm() {
            // Change button text
            getSubmitButtonElement().innerText = TTS.defaults.btnText.default;

            // Enable inputs
            getTextInputElement().disabled = false;
            getVoiceInputElement().disabled = false;
            getSubmitButtonElement().disabled = false;
        }


        //----- Final Statement -----//
        return TTS;
    }

    if(typeof(TTS) === 'undefined') {
        window.TTS = define_app();
    } else {
        console.log("TTS is already defined.");
    }
})(window);


document.addEventListener("DOMContentLoaded", function() {

    document.getElementById('generate_audio_button').onclick = TTS.onGenerateButtonClick;

});