<template>
    <li class="card stockcard">
        <div class="hg-details">
            <h4>
                {{ houseguest.nickname || houseguest.first_name | capitalize }}
            </h4>
            <span
                v-if="priceDifference.amount >= 0"
                class="price-change-wrap"
                v-bind:class="priceDifference.class.text"
            >
                <font-awesome-icon
                    :icon="priceDifference.icon"
                    class="price-diff-icon"
                />
                <span class="price-diff word small-price">{{
                    houseguest.current_price | currency
                }}</span>
            </span>
        </div>
        <img
            :src="houseguestImage(houseguest)"
            :alt="houseguest.nickname"
            class="hg-img"
            height="75"
            width="75"
        />
        <div class="user-holdings">
            <div>
                <span class="num">{{ stock.quantity }}</span
                ><span class="word">
                    share{{ stock.quantity === 1 ? "" : "s" }}</span
                >
            </div>
            <div>
                <span class="num">{{
                    (stock.quantity * houseguest.current_price) | currency
                }}</span>
            </div>
        </div>
    </li>
</template>

<script>
export default {
    props: {
        houseguests: Array,
        stock: Object
    },
    data() {
        return {
            //
        };
    },
    methods: {
        houseguestImage: function(houseguest) {
            return "/storage/" + houseguest.image;
        }
    },
    computed: {
        houseguest: function() {
            let houseguest;
            for (let hg of this.houseguests) {
                if (hg.id === this.stock.houseguest_id) {
                    houseguest = hg;
                }
            }
            return houseguest;
        },
        priceDifference: function() {
            if (this.houseguest.prices.length === 1) {
                return {
                    amount: -1, //because we use abs(), we will never have a negative number. Thus we can use it as a check.
                    icon: "",
                    class: ""
                };
            }

            //find latest week and week before
            let currentWeek;
            let lastWeek;
            this.houseguest.prices.forEach(week => {
                if (typeof currentWeek === "undefined") {
                    currentWeek = week;
                } else if (week.week > currentWeek.week) {
                    lastWeek = currentWeek;
                    currentWeek = week;
                }
            });

            let diff = currentWeek.price - lastWeek.price;
            let isIncrease = diff > 0;

            return {
                amount: Math.abs(diff),
                icon: isIncrease | (diff === 0) ? "arrow-up" : "arrow-down",
                class: {
                    background: isIncrease
                        ? "green-bg"
                        : diff === 0
                        ? ""
                        : "red-bg",
                    text: isIncrease ? "green" : diff === 0 ? "" : "red"
                }
            };
        }
    }
};
</script>
