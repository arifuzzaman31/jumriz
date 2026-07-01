<template>
  <div class="container">
    <div class="row offers"  v-for="category in categoryProducts"
          :key="category.id">
       <div class="col-md-12 offers">
        <div class="display-flex mt50 mb30">
        <div class="title  flex1">
          <h4>{{ category.category_name }} </h4>
        </div>
        <span class="text-right"><a :href="url+'product/category/'+category.id+'/'+category.category_slug">View All <i class="lni-arrow-right"></i></a></span>
      </div>
         
      </div>

      <div
          class="col-6 col-lg-2 col-sm-4 mb30"
         v-for="prod in category.product"
          :key="prod.id"
        >
          <single-product :currency="currency" :product="prod"></single-product>
        </div>
       <!-- class="col-6 col-lg-3 col-sm-4 mb30" -->

      <!-- <div
        class="col-6 col-lg-3 col-sm-4 mb30"
        v-for="value in categoryProducts"
        :key="value.id"
      >
        <single-product :currency="currency" :product="value"></single-product>
      </div> -->

      <InfiniteLoading
        spinner="bubbles"
        @infinite="infiniteHandler"
      >
        <template #spinner>
          <div class="col-md-12 text-center">
            <img :src="url + 'images/loading.gif'" />
          </div>
        </template>
        <template #no-more><span></span></template>
        <template #no-results><span></span></template>
      </InfiniteLoading>
    </div>

    <div class="row" v-if="isLoading">
      <div class="col-md-12 text-center">
        <img :src="url + 'images/loading.gif'" />
      </div>
    </div>
  </div>
</template>

<script>
import { EventBus } from "../../../vue-assets";
import { useMixin } from "../../../mixin";
import SingleProduct from "./SingleProduct.vue";
import InfiniteLoading from "v3-infinite-loading";

export default {
  props: ["currency"],
  mixins: [Mixin],
  components: {
    "single-product": SingleProduct,
    "infinite-loading": InfiniteLoading,
  },
  data() {
    return {
      categoryProducts: [],
      url: base_url,
      page: 1,
      lastPage: 0,
      isLoading: false,
    };
  },

  mounted() {
    this.getProduct();
  },

  methods: {
    fetchProduct: function () {
      return axios.get(
        base_url + "product-by-category-list?page=" + this.page + "&discount_status=1"
      );
    },

    getProduct() {
      this.isLoading = true;
      this.fetchProduct()
        .then((response) => {
          if (response.data.data.length > 0) {
            this.categoryProducts = response.data.data;
            this.page += 1;
            this.isLoading = false;
          } else {
            // console.log("Product not available");
            this.isLoading = false;
          }
        })
        .catch((e) => console.log(e));
    },

    infiniteHandler: function ($state) {
      setTimeout(
        function () {
          this.fetchProduct()
            .then((response) => {
              if (response.data.data.length > 0) {
                this.lastPage = response.meta.last_page;
                this.categoryProducts.push(...response.data.data);

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
  },
};
</script>