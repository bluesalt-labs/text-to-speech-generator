(function(window){
    'use strict';

    function define_app() {
        var TTS = {};

        //----- Set Defaults -----//
        TTS.defaults = {
            elementIDs: {
                form:   'text_content_form',
                charCount:  'char_count',
                reqCount:   'requests_count',
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
            },
            maxRequestCharacters: 3000
        };

        //----- Initiate Cache Variables -----//
        TTS.cache = {
            elements: {
                form:   null,
                charCount:  null,
                reqCount:   null,
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

        TTS.updateCharacterCounter = function() {
            var chars   = getTextInputValue().length;
            var maxPer  = TTS.defaults.maxRequestCharacters;
            var numReq  = (chars > 0 ? ((chars / maxPer) >> 0) + 1 : 0);

            getElement('charCount').innerText = getTextInputValue().length;
            getElement('reqCount').innerText = numReq + (numReq === 1 ? " Request" : " Requests");
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
            var success = TTS.cache.data.return.success;

            // Show success or fail
            var outputHtml = "<div class='output-element " +
                ( success ? getElementSuccessClass() : getElementErrorClass() ) + "'>";

            var msgs    = TTS.cache.data.return.messages;
            var url     = TTS.cache.data.return.audioPath;
            var name    = TTS.cache.data.return.audioName;

            // Show messages
            if(msgs.length > 0) {
                for(var i in msgs) {
                    outputHtml += "<span>"+ msgs[i] +"</span><br />";
                }
            }

            // show audio url if available
            if(url) {
                outputHtml += "<a href='" + url +"' target='_blank'>"+ name +"</a><br />"

                outputHtml += "<audio class='audio-player' controls>" +
                    "<source src='" + url + "' type='audio/mpeg'>" +
                    "Your browser does not support the audio element." +
                    "</audio><br />";
            }

            getElement('output').innerHTML = (outputHtml + "</div>") + getElement('output').innerHTML;
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

        // Get Elements
        function getElement(key) {
            if( TTS.defaults.elementIDs.hasOwnProperty(key) ) {
                if(!TTS.cache.elements[key]) {
                    TTS.cache.elements[key] = document.getElementById(TTS.defaults.elementIDs[key]);
                }

                return TTS.cache.elements[key];
            }

            return null;
        }

        // Get form input values
        function getTextInputValue() {
            return getElement('text').value;
        }

        function getVoiceInputValue() {
            return getElement('voice').value;
        }

        // Check if input is valid and change its class
        function isTextInputValid() {
            if( getTextInputValue() ) {
                getElement('text').className = getElementSuccessClass();
                return true;
            } else {
                getElement('text').className = getElementErrorClass();
                return false;
            }
        }

        function isVoiceInputValid() {
            if( getVoiceInputValue() ) {
                getElement('voice').className = getElementSuccessClass();
                return true;
            } else {
                getElement('voice').className = getElementErrorClass();
                return false;
            }
        }

        // Get Default Element Classes
        function getElementDefaultClass() { return TTS.defaults.inputClasses.default; }
        function getElementErrorClass() { return TTS.defaults.inputClasses.error; }
        function getElementSuccessClass() { return TTS.defaults.inputClasses.success; }


        function disableValidForm() {
            // Set input classes to default
            getElement('text').className = getElementDefaultClass();
            getElement('voice').className = getElementDefaultClass();

            // Change button text
            getElement('submit').innerText = TTS.defaults.btnText.loading;

            // Disable inputs
            getElement('text').disabled = true;
            getElement('voice').disabled = true;
            getElement('submit').disabled = true;
        }

        function enableForm() {
            // Change button text
            getElement('submit').innerText = TTS.defaults.btnText.default;

            // Enable inputs
            getElement('text').disabled = false;
            getElement('voice').disabled = false;
            getElement('submit').disabled = false;
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

    document.getElementById(TTS.defaults.elementIDs.submit).addEventListener('click', TTS.onGenerateButtonClick);
    document.getElementById(TTS.defaults.elementIDs.text).addEventListener('input', TTS.updateCharacterCounter);

});