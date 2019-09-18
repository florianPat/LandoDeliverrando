(function()
{
    let formValidationErrorResults = document.getElementsByClassName('formValidationErrorResult');

    if(formValidationErrorResults !== null && formValidationErrorResults.length !== 0) {
        let actionName = document.getElementById('lastAction').innerHTML;

        for (let i = 0; i < formValidationErrorResults.length; ++i) {
            let validateError = formValidationErrorResults.item(i);

            let propertyName = validateError.childNodes.item(0).nodeValue;
            let propertyErrMessage = validateError.childNodes.item(1).childNodes.item(0).nodeValue;

            let formField = document.getElementById('formErr_' + actionName + '_' + propertyName);
            if (formField === null) {
                let realPropertyNameEndPos = propertyErrMessage.indexOf(':');
                console.assert(realPropertyNameEndPos <= propertyErrMessage.length && realPropertyNameEndPos !== -1,
                    "There is no : in the error message: " + propertyErrMessage);
                propertyName = propertyErrMessage.slice(0, realPropertyNameEndPos);
                propertyErrMessage = propertyErrMessage.slice(realPropertyNameEndPos + 1);

                formField = document.getElementById('formErr_' + actionName + '_' + propertyName);
                console.assert(formField !== null, "one could not find a valid form field");
            }
            formField.style.backgroundColor = 'red';

            let formFieldErrMessage = document.getElementById('msg_formErr_' + actionName + '_' + propertyName);
            formFieldErrMessage.innerHTML = '<em>' + propertyErrMessage + '</em>';
        }
    }
})();