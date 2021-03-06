<template>
    <div class="trades-wrap">
        <div class="stock-cards-wrap">
            <ul class="stock-cards">
                <stock-card
                    v-for="stock in mutableStocks"
                    :key="stock.id"
                    :stock="stock"
                    :bank="mutableBank"
                    :disabled="season.status !== 'open'"
                    v-on:current-price="saveCurrentPrice"
                    v-on:buy-max="buyMax"
                    v-on:sell-all="sellAll"
                />
            </ul>
        </div>
        <div class="user-details">
            <div class="user-panel">
                <funds
                    :bank="mutableBank"
                    :networth="networth"
                ></funds>
                <div v-if="season.status === 'open'" class="flex-col trade">
                    <!-- enable button when input fields become active -->
                    <button class="button-base secondary mg-btm-sm" @click="submit" :disabled="mutableBank.money < 0">
                        <font-awesome-icon v-if="saving" icon="spinner" pull="right" pulse/>
                        Submit trade
                    </button>
                    <!-- enable button when submit button becomes active -->
                    <button class="button-base link" @click="resetAll">Reset All</button>
                </div>
                <flash-message class="alert-custom-class"></flash-message>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            stocks: Array,
            bank: Object,
            season: Object,
            user: Object,
            networth: Number,
        },
        data() {
            return {
                mutableStocks: _.cloneDeep(this.stocks),
                mutableBank: _.cloneDeep(this.bank),
                prices: [],
                saving: false
            }
        }, mounted() {
        },
        watch: {
            mutableStocks: {
                handler(mutatedStocks, oldVal) {
                    let stockTotal = 0;
                    let prices = this.prices;

                    mutatedStocks.forEach((stock) => {
                        if (stock.quantity < 0) {
                            stock.quantity = 0;
                        }
                        stockTotal += stock.quantity * prices[stock.houseguest_id];
                    });
                    if (this.networth < stockTotal) {
                        this.mutableStocks = oldVal;
                    } else {
                        this.mutableBank.money = this.networth - stockTotal;
                    }
                },
                deep: true
            }
        },
        methods: {
            saveCurrentPrice(value) {
                this.prices[value.houseguest] = parseFloat(value.price);
            },
            buyMax(value) {
                this.mutableStocks.forEach((stock) => {
                    if (stock.houseguest_id === value.houseguest) {
                        let remainingCash = this.mutableBank.money;
                        let currentPrice = this.prices[value.houseguest];
                        stock.quantity += Math.floor(remainingCash / currentPrice);
                    }
                });
            },
            sellAll(value) {
                this.mutableStocks.forEach((stock) => {
                    if (stock.houseguest_id === value.houseguest) {
                        stock.quantity = 0;
                    }
                });
            },
            submit() {
                this.saving = true;
                //save to DB
                if (this.mutableBank.money >= 0) {
                    let stocks = [];
                    this.mutableStocks.forEach(function (stock) {
                        stocks.push({
                            houseguest: stock.houseguest_id,
                            quantity: stock.quantity
                        });
                    });
                    axios.post('/trades/savestocks', {stocks}).then(res => {
                        this.saving = false;

                        if (res.data.success) {
                            this.flash('Trade submitted!', 'goodjob', {});
                        } else {
                            this.flash('Trade submission failed, please try again', 'ohno', {});
                        }
                    });
                }
            },
            resetAll() {
                //reset to initial values
                this.mutableStocks = _.cloneDeep(this.stocks);
            }
        },
        computed: {
            //
        }
    }
</script>

<style scoped>

</style>
