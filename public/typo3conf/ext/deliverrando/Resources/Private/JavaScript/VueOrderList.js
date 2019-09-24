(function(){
    if(document.getElementById('vueOrderList') !== null) {
        const OrderList = {
            data() {
                return {
                    finishedOrderTracker: [],
                    orders: [],
                    ajaxLink: '',
                    udpateProgressLink: '',
                    xhttp: null,
                };
            },
            methods: {
                sendProgressUpdate(orderTrackerRecord, productIndex, checked) {
                    let sendParams = new URLSearchParams();
                    sendParams.set('orderUid', orderTrackerRecord.uid);
                    sendParams.set('productIndex', productIndex);
                    sendParams.set('checked', checked ? '1' : '0');

                    let xhttp = new XMLHttpRequest();
                    xhttp.open('POST', this.updateProgressLink, true);
                    xhttp.send(sendParams);
                },
                checkboxChange(orderIndex, productIndex, i)
                {
                    console.assert(this.finishedOrderTracker[orderIndex] !== undefined, 'finishedOrderTracker-entry needs to be defined!');

                    let orderTrackerRecord = this.finishedOrderTracker[orderIndex];

                    let checked = orderTrackerRecord.checked[productIndex][i];

                    this.sendProgressUpdate(orderTrackerRecord, productIndex, checked);

                    if(checked) {
                        --orderTrackerRecord.count;

                        if(orderTrackerRecord.count === 0) {
                            orderTrackerRecord.color = 'cornflowerblue';
                        }
                    } else {
                        orderTrackerRecord.color = 'slategray';
                        ++orderTrackerRecord.count;
                    }
                },
                triggerAjax()
                {
                    this.xhttp.open('POST', this.ajaxLink, true);
                    this.xhttp.send();
                },
                finishedMeal(index)
                {
                    if(this.finishedOrderTracker[index].color === 'cornflowerblue') {
                        let xhttp = new XMLHttpRequest();

                        xhttp.onreadystatechange = () => {
                            if(this.xhttp.readyState === 4 && this.xhttp.status === 200) {
                                this.triggerAjax();
                            }
                        };

                        xhttp.open('GET', this.orders[index].finishLink, true);
                        xhttp.send();
                    }
                },
            },
            created()
            {
                // NOTE: I use this so that I do not have to compute the cHash myself (in some php code that I would have to call
                // before I start the ajax request.
                // NOTE: Moreover, I have to link to a different page, so that the other page can have another
                // typoscript template associated with it.
                const ajaxUrl = document.getElementById('ajaxUrl');
                this.ajaxLink = deescapeHtml(ajaxUrl.getAttribute('href'));

                const prgressUpdateUrl = document.getElementById('progressUpdateUrl');
                this.updateProgressLink = deescapeHtml(prgressUpdateUrl.getAttribute('href'));

                this.xhttp = new XMLHttpRequest();

                this.xhttp.onreadystatechange = () =>
                {
                    if(this.xhttp.readyState === 4 && this.xhttp.status === 200) {
                        vue.$emit('ajaxResponse', JSON.parse(this.xhttp.responseText));
                    } else if(this.xhttp.readyState === 4 && this.xhttp.status >= 400) {
                        console.assert(!"InvalidCodePath", "InvalidCodePath");
                    }
                };

                setInterval(() => {
                   this.triggerAjax();
                }, 10000);

                this.triggerAjax();
            },
            mounted()
            {
                this.$root.$on('ajaxResponse', (jsonResponse) => {
                    for(let i = 0; i < jsonResponse.orders.length; ++i) {
                        if(this.orders.length <= i) {
                            const order = jsonResponse.orders[i];

                            let productDescTracker = [];
                            let nProductsForDesc = 0;
                            for(let productDesc of order.productDescriptions) {
                                let checkedArray = [];
                                for(let i = 0; i < productDesc.quantity; ++i) {
                                    checkedArray.push(false);
                                }
                                productDescTracker.push(checkedArray);
                                nProductsForDesc += productDesc.quantity;
                            }
                            this.finishedOrderTracker.push({count: nProductsForDesc,
                                color: 'slategray', uid: order.uid, checked: productDescTracker});

                            this.orders.push(jsonResponse.orders[i]);
                        } else if(this.orders[i].uid !== jsonResponse.orders[i].uid) {
                            this.orders = this.orders.slice(0, i).concat(this.orders.slice(i + 1, this.orders.length));
                            this.finishedOrderTracker = this.finishedOrderTracker.slice(0, i)
                                .concat(this.finishedOrderTracker.slice(i + 1, this.finishedOrderTracker.length));

                            --i;
                        }
                    }

                    if(this.orders.length > jsonResponse.orders.length) {
                        for(let i = 0; i < (this.orders.length - jsonResponse.orders.length); ++i) {
                            this.orders.pop();
                            this.finishedOrderTracker.pop();
                        }
                    }
                });
            },
            template: `
                <div>
                    <div v-for="(order, index) in orders" class="card">
                        <h2 class="card-header">Order id: {{ order.uid }}</h2>
                        <div class="card-body">
                            <h4 class="card-subtitle">Person</h4>
                            <p class="card-text">Name: {{ order.person.name }}</p>
                            <p class="card-text">Address: {{ order.person.address }}</p>
                            <p class="card-text">Telephone number: {{ order.person.telephonenumber }}</p>
                            <br />
                            <div class="card-header">
                                Products
                            </div>
                            <ul class="list-group">
                                <li class="list-group-item" v-for="(productDesc, pdIndex) in order.productDescriptions">
                                    x{{ productDesc.quantity }} {{ productDesc.productName }}
                                    <input v-for="i in productDesc.quantity" @change="checkboxChange(index, pdIndex, i - 1);"
                                        v-model="finishedOrderTracker[index].checked[pdIndex][i - 1]" type="checkbox" class="orderDisplayCheckbox">
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer text-muted">
                            <a class="card-link" @click="finishedMeal(index);" href="#" :style="{color: finishedOrderTracker[index].color}">Finished!</a>
                        </div>
                    </div>
                     
                    <div class="card" v-if="(orders.length % 2) !== 0"></div>
                </div>
            `
        };

        let vue = new Vue({
            el: '#vueOrderList',
            components: {
                'order-list': OrderList,
            },
        });
    }
})();