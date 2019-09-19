(function() {
    if (document.getElementById('vueResidence') !== null) {
        ListItem = function (address, currentInput) {
            this.strongText = address;
            this.text = '';
        };

        const AutocompleteBox = {
            data() {
                return {
                    autocompleteList: [],
                    textfieldValue: ''
                };
            },
            methods: {
                setTextFieldValue(value) {
                    this.textfieldValue = value;
                },
                disableSubmitAndGetAutocomplete(submitBtn) {
                    submitBtn.setAttribute('disabled', 'disabled');
                    submitBtn.style.backgroundColor = '#dddddd';
                },
                inputEvent() {
                    this.autocompleteList = [];

                    let submitBtn = document.getElementById('registerPersonSubmit');
                    if(this.textfieldValue !== '') {
                        if(submitBtn.hasAttribute('disabled')) {
                            submitBtn.removeAttribute('disabled');
                            submitBtn.style.backgroundColor = '#2196F3';
                        }

                        this.autocompleteList.push(new ListItem('Werther', this.textfieldValue));
                    } else {
                        this.disableSubmitAndGetAutocomplete(submitBtn);
                    }
                }
            },
            template: `
                <div class="registerPersonAddressInput">
                    <!-- TODO: NOTE: This is the compilded fluid version. Not really cool, but I messed around with the "normal" html and js objects and events, and I do not get that to work.-->
                    <input @input="inputEvent();" v-model="textfieldValue" autofocus="autofocus" placeholder="Address" type="text" name="tx_deliverrando_productlist[person][address]" required="required"">
    
                    <div class="registerPersonAddressDiv">
                        <div class="autocompleteDiv">
                            <div v-for="it in autocompleteList" @click="setTextFieldValue(it.strongText + it.text);" class="autocompleteList">
                                <strong>{{ it.strongText }}</strong>{{ it.text }}
                            </div>
                        </div>
                    </div>
                </div>
            `
        };

        new Vue({
            el: '#vueResidence',
            components: {
                'autocomplete-box': AutocompleteBox
            }
        });
    }
})();