(function() {
    if (document.getElementById('vueResidence') !== null) {
        ListItem = function (address, currentInput, coordinates) {
            this.strongText = address.slice(0, currentInput.length);
            this.text = address.slice(currentInput.length);
            this.coordinates = coordinates;

            this.fullText = function() {
                return this.strongText + this.text;
            }
        };

        const AutocompleteBox = {
            data() {
                return {
                    autocompleteList: [],
                    textfieldValue: '',
                    setIntervalId: -1,
                    xhttp: null,
                    errorMessage: '',
                    coordinates: ''
                };
            },
            props: {
                postCode: String,
                inputNameAttr: String
            },
            computed: {
                autosuggestValues() {
                    let modifyPostCode = this.postCode;
                    const semicolonPos = modifyPostCode.indexOf(';');
                    console.assert(semicolonPos !== -1, 'No semicolon in the string!!');
                    const postCode = modifyPostCode.substr(0, semicolonPos);
                    modifyPostCode = modifyPostCode.slice(semicolonPos + 1);
                    const locality = modifyPostCode;

                    const result = {
                        postCode: postCode,
                        locality: locality
                    };

                    return result;
                }
            },
            methods: {
                setTextFieldValue(index) {
                    this.textfieldValue = this.autocompleteList[index].fullText();

                    this.errorMessage = '';
                    this.checkForHousenumber(document.getElementById('registerPersonSubmit'), this.textfieldValue, index);
                },
                disableSubmit(submitBtn) {
                    submitBtn.setAttribute('disabled', 'disabled');
                    submitBtn.style.backgroundColor = '#dddddd';
                },
                populateAutocompleteList() {
                    let url = 'http://dev.virtualearth.net/REST/v1/Locations/DE/' + this.autosuggestValues.postCode +
                        '/' + encodeURI(this.autosuggestValues.locality) + '/' + encodeURI(this.textfieldValue) +
                        '?key=YOUR_BING_API_KEY';
                    this.xhttp.open('GET', url, true);
                    this.xhttp.send();
                },
                checkForHousenumber(submitBtn, trimmedTextfieldValue, autocompleteIndex) {
                    if(isNaN(trimmedTextfieldValue.charAt(trimmedTextfieldValue.length - 1))) {
                        this.errorMessage = 'No house number specified!';
                    } else {
                        const it = this.autocompleteList[autocompleteIndex];
                        this.coordinates = it.coordinates[0] + ',' + it.coordinates[1];

                        submitBtn.removeAttribute('disabled');
                        submitBtn.style.backgroundColor = '#2196F3';
                    }

                    this.autocompleteList = [];
                },
                inputEvent() {
                    this.autocompleteList = [];
                    this.errorMessage = '';

                    if(this.setIntervalId !== -1) {
                        clearInterval(this.setIntervalId);
                        this.setIntervalId = -1;
                    }
                    this.setIntervalId = setInterval(() => {
                        clearInterval(this.setIntervalId);
                        this.setIntervalId = -1;
                        if(this.textfieldValue.length > 2) {
                            this.populateAutocompleteList();
                        }
                    }, 1000);
                }
            },
            created() {
                this.xhttp = new XMLHttpRequest();

                this.xhttp.onreadystatechange = () => {
                    if(this.xhttp.readyState === 4 && this.xhttp.status === 200) {
                        const jsonResponse = JSON.parse(this.xhttp.responseText);
                        const responseLength = jsonResponse.resourceSets[0].estimatedTotal;
                        for(let i = 0; i < responseLength; ++i) {
                            this.autocompleteList.push(new ListItem(jsonResponse.resourceSets[0].resources[i].address.addressLine, this.textfieldValue,
                                jsonResponse.resourceSets[0].resources[i].point.coordinates));
                        }

                        let submitBtn = document.getElementById('registerPersonSubmit');
                        if(this.textfieldValue !== '') {
                            let trimmedTextfieldValue = this.textfieldValue.trim();
                            for(let i = 0; i < this.autocompleteList.length; ++i) {
                                const it = this.autocompleteList[i];
                                if(this.textfieldValue.includes(it.fullText()) !== false) {
                                    this.checkForHousenumber(submitBtn, trimmedTextfieldValue, i);
                                    return;
                                }
                            }
                            this.disableSubmit(submitBtn);
                        } else {
                            this.disableSubmit(submitBtn);
                        }
                    }
                };
            },
            template: `
                <div class="registerPersonAddressInput">
                    <!-- TODO: TODO: This is the compilded fluid version. Not really cool, but I messed around with the "normal" html and js objects and events, and I do not get that to work.-->
                    <input hidden="hidden" v-model="coordinates" type="text" required="required" :name="inputNameAttr">
                    <input @input="inputEvent();" v-model="textfieldValue" autofocus="autofocus" placeholder="Address" type="text">
                    <p v-show="errorMessage" style="background-color: red;">{{ errorMessage }}</p>
    
                    <div class="registerPersonAddressDiv">
                        <div class="autocompleteDiv">
                            <div v-for="(it, index) in autocompleteList" @click="setTextFieldValue(index);" class="autocompleteList">
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