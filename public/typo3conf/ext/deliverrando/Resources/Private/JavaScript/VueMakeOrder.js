(function() {
    if(document.getElementById('vueOrderProducts') !== null) {
        const Order = function(name)
        {
            this.name = name;
            this.quantity = 1;
        };

        const TypoLink = function(linkViewhelper)
        {
            const startHref = linkViewhelper.indexOf('href="') + 'href="'.length;
            const endHref = linkViewhelper.indexOf('"', startHref);
            this.href = deescapeHtml(linkViewhelper.substring(startHref, endHref));
        };

        const sendAjaxRequest = function(href, sendParams, responseEventName)
        {
            const xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function()
            {
                if(this.readyState == 4 && this.status == 200) {
                    vue.$emit(responseEventName, JSON.parse(this.responseText));
                } else if(this.readyState == 4 && this.status >= 400) {
                    console.assert(true, 'server error!');
                }
            };

            xhttp.open('POST', href, true);

            xhttp.send(sendParams);
        };

        const ShoppingCard = {
            data()
            {
                return {
                    orders: Array,
                    ordersAcc: Object,
                };
            },
            methods: {
                changeQuantity(order, value)
                {
                    order.quantity += value;
                    if(order.quantity <= 0)
                    {
                        const deleteIndex = this.ordersAcc[order.name];
                        delete this.ordersAcc[order.name];
                        if(deleteIndex == 0) {
                            this.orders.shift();
                        } else if(deleteIndex == this.orders.length - 1) {
                            this.orders.pop();
                        } else {
                            let newOrders = this.orders.slice(0, deleteIndex);
                            newOrders.push(this.orders.slice(deleteIndex + 1, this.orders.length));
                            this.orders = newOrders;
                        }

                        const ordersAccEntries = Object.entries(this.ordersAcc);
                        for(const [product, index] of ordersAccEntries) {
                            if(index > deleteIndex) {
                                this.ordersAcc[product]--;
                            }
                        }
                    }
                },
                makeOrder()
                {
                    let sendParams = new URLSearchParams();

                    for(let i = 0; i < this.orders.length; ++i)
                    {
                        const order = this.orders[i];
                        sendParams.set('products' + i, order.name);
                        sendParams.set('quantity' + i, order.quantity);
                    }

                    sendAjaxRequest(new TypoLink(this.linkOrderEndAction).href, sendParams, 'finishedOrder');
                }
            },
            mounted()
            {
                this.$root.$on('addProductToOrder', (product) => {
                    if(this.ordersAcc[product] === undefined) {
                        this.ordersAcc[product] = this.orders.length;
                        this.orders.push(new Order(product));
                    } else {
                        this.changeQuantity(this.orders[this.ordersAcc[product]], 1);
                    }
                })
            },
            created()
            {
                this.orders = [];
            },
            props: {
                linkOrderEndAction: String,
            },
            template: `
                <ul v-if="orders.length !== 0" class="list-group">
                    <transition-group>
                        <li v-for="order in orders" class="list-group-item" :key="order.name">
                            {{ order.name }}
                            <button class="btn" @click="changeQuantity(order, -1);">-</button>
                            {{ order.quantity }}
                            <button class="btn" @click="changeQuantity(order, 1);">+</button>
                        </li>
                        <li class="list-group-item" key="btn">
                            <button class="btn" @click="makeOrder();">Jetzt bestellen</button>
                        </li>
                    </transition-group>
                </ul>
            `
        };

        const FoodCounter = {
            data()
            {
                return {
                    counter: 0,
                    setIntervalId: 0,
                };
            },
            methods: {
                computeLeadingZero(value)
                {
                    if((value / 10) < 1.0) {
                        value = '0' + value;
                    }
                    return value;
                },
            },
            computed: {
                counterDisplay()
                {
                    let hour = Math.floor(this.counter / 3600);
                    let minutes = this.computeLeadingZero(Math.floor(this.counter / 60));
                    let seconds = this.computeLeadingZero(this.counter % 60);

                    return ((hour !== 0) ? hour + ' : ' : '') + ((minutes !== 0) ? minutes + ' : ' : '') + seconds;
                },
            },
            watch: {
                jsonResponse(value)
                {
                    this.counter = value.deliverytime * 60;

                    if(this.counter === 0) {
                        clearInterval(this.setIntervalId);
                    }
                }
            },
            props: {
                jsonResponse: Object,
            },
            mounted() {
                this.counter = this.jsonResponse.deliverytime * 60;

                this.setIntervalId = setInterval(() => {
                    --this.counter;

                    if(this.counter === 0) {
                        clearInterval(this.setIntervalId);
                        this.$root.$emit('progressUpdate', {progress: ['finished']});
                    }
                }, 1000);
            },
            template: `
                <span>{{ counterDisplay }}</span>
            `
        };

        const FoodProgress = {
            data() {
                return {
                    percent: 0,
                    progress: "Die Zubereitung beginnt...",
                    style: {
                        height: '30px',
                        backgroundColor: 'green',
                    },
                    setIntervalId: 0,
                };
            },
            mounted() {
                this.$root.$on('progressUpdate', (jsonResponse) => {
                    console.assert(jsonResponse.progress.length > 0);
                    if (jsonResponse.progress[0] === 'finished') {
                        clearInterval(this.setIntervalId);
                        this.percent = 100;
                        this.progress = "Essen ist da!!";
                        this.$root.$emit('finishedOrder', {deliverytime: 0});
                        return;
                    }

                    let percent = 0;
                    for (let progressIt of jsonResponse.progress) {
                        if (progressIt !== 0) {
                            percent += (progressIt / this.progressLength) * 100;
                        }
                    }
                    if (percent >= 100) {
                        this.progress = "Der Fahrer beeilt sich. Ehrlich!!";
                    }
                    this.percent = Math.floor(percent / 100 * 90);
                });

                this.setIntervalId = setInterval(() => {
                    let sendParams = new URLSearchParams();

                    sendParams.set('orderUid', this.jsonResponse.orderUid);

                    sendAjaxRequest(new TypoLink(this.linkGetProgressAction).href, sendParams, 'progressUpdate');
                }, 10000);
            },
            props: {
                jsonResponse: Object,
                progressLength: Number,
                linkGetProgressAction: String,
            },
            template: `
                <div style="width: 100%; background-color: grey;">
                    <p style="color: whitesmoke; float: right; font-size: 20px; vertical-align: middle; margin-right: 5px;">{{ progress }}</p>
                    <p style="color: whitesmoke; float: left; font-size: 20px; vertical-align: middle; margin-left: 5px;">{{ percent }}%</p>
                    <div :style="[style, { width: this.percent + '%' } ]"></div>
                </div>
            `
        };

        let vue = new Vue({
            el: '#vueOrderProducts',
            components: {
                'shopping-card': ShoppingCard,
                'food-counter': FoodCounter,
                'food-progress': FoodProgress,
            },
            data: {
                finishedOrder: false,
                makeOrderJsonResponse: null,
            },
            mounted() {
                this.$on('finishedOrder', (jsonResponse) => {
                    this.makeOrderJsonResponse = jsonResponse;
                    this.finishedOrder = true;
                });
            }
        });
    }
})();