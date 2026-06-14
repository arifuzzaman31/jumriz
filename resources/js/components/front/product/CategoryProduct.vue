<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 offers">
                <div class="title text-center mt50 mb30">
                    <h4>
                        <span v-if="category.sub_category.length > 0">All</span>
                        {{ category.category_name }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="row offers">
            <div
                class="col-lg-2 col-sm-4 col-6 mb15"
                v-for="(value, index) in subCategoryProducts"
                :key="index"
            >
                <single-product
                    :currency="currency"
                    :identifier="infiniteId"
                    :product="value"
                >
                </single-product>
            </div>

            <infinite-loading spinner="bubbles" @infinite="infiniteHandler">
                <div slot="spinner">
                    <div class="col-md-12 text-center">
                        <img :src="url + 'images/loading.gif'" />
                    </div>
                </div>
                <div slot="no-more"></div>
                <div slot="no-results"></div>
            </infinite-loading>
        </div>

        <div class="row" v-if="isLoading">
            <div class="col-md-12 text-center">
                <img :src="url + 'images/loading.gif'" />
            </div>
        </div>

        <div
            class="row"
            v-if="
        !isLoading &&
        subCategoryProducts.length <= 0 &&
        category.sub_category.length <= 0
      "
        >
            <div class="col-lg-6 col-sm-6 mx-auto text-center mb30">
                <img
                    :src="url + 'images/static/not-fount-product.svg'"
                    class="img-fluid" width="350"
                />
                <h2 class="font-bold  color-red mt15 p10 br5">No Product Found!</h2>
            </div>
        </div>
    </div>
</template>

<script>
import { EventBus } from "../../../vue-assets";
import Mixin from "../../../mixin";
import SingleProduct from "./SingleProduct";
import InfiniteLoading from "vue-infinite-loading";

export default {
    props: ["currency", "category", "brands"],
    mixins: [Mixin],
    components: {
        "single-product": SingleProduct,
        "infinite-loading": InfiniteLoading,
    },
    data() {
        return {
            brand_id: "",
            subCategoryProducts: [],
            page: 1,
            lastPage: 0,
            infiniteId: +new Date(),
            url: base_url,
            isLoading: false,
        };
    },

    mounted() {
        this.initialData();
        // console.log(this.category.id);
    },
    methods: {
        fetchProduct: function () {
            return axios.get(
                base_url +
                "product-list?page=" +
                this.page +
                "&category=" +
                this.category.id +
                "&brand_id=" +
                this.brand_id
            );
        },

        infiniteHandler: function ($state) {
            setTimeout(
                function () {
                    this.fetchProduct()
                        .then((response) => {
                            if (response.data.data.length > 0) {
                                this.lastPage = response.data.meta.last_page;
                                this.subCategoryProducts.push(...response.data.data);

                                if (this.page === this.lastPage) {
                                    this.page = 1;
                                    $state.complete();
                                } else {
                                    this.page += 1;
                                }
                                $state.loaded();
                            } else {
                                this.page = 1;
                                $state.complete();
                            }
                        })
                        .catch((e) => console.log(e));
                }.bind(this),
                1000
            );
        },

        initialData() {
            this.isLoading = true;
            this.fetchProduct()
                .then((response) => {
                    if (response.data.data.length > 0) {
                        this.subCategoryProducts = response.data.data;
                        this.page += 1;
                        this.isLoading = false;
                    }
                    this.isLoading = false;
                })
                .catch((e) => console.log(e));
        },
    },
};
</script>
<style scoped="">
.brand_active {
    border: 1px solid #e3106e !important;
}
</style>
