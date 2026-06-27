<template>
  <div class="container">
    <div class="row offers"  v-for="category in categoryProducts"
          :key="category.id">
       <div class="col-md-12 offers">
        <div class="display-flex mt30 mb30">
        <div class="title  flex1">
          <h4>{{ category.category_name }}</h4>
        </div>
        <span class="text-right"><a :href="url+'product/category/'+category.id+'/'+category.category_slug">View All <i class="lni-arrow-right"></i></a></span>
      </div>     
    </div>

      <div
        class="col-6 col-lg-2 col-sm-4 mb15"
        v-for="value in category.product"
        :key="value.id"
      >
        <single-product :currency="currency" :product="value"></single-product>
      </div>

      <infinite-loading spinner="bubbles" @infinite="infiniteHandler"
      force-use-infinite-wrapper=".el-table__body-wrapper">
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
  </div>
</template>

<script>
// import { EventBus } from "../../../vue-assets";
import { useMixin } from "../../../mixin";
import SingleProduct from "./SingleProduct";
import InfiniteLoading from "vue-infinite-loading";

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

  created() {
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
            // const data = response.data.data.filter(item => item !== null)
            this.categoryProducts = response.data.data;
            // console.log(data)
            this.page += 1;
            this.isLoading = false;
            this.lastPage = response.data.meta.last_page;
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
            .then(({ data }) => {
              if (data.data.length > 0) {
                data.data.forEach(element => {
                  const res = this.categoryProducts.filter(cat => cat.id == element.id);
                if(res.length < 1){
                  const product = {...element.product}
                  this.categoryProducts.push({
                    id:element.id, 
                    category_name: element.category_name,
                    category_slug:element.category_slug,
                    product:product
                  })
                }
              });
              $state.loaded();

                if (this.page === this.lastPage) {
                  // this.page = 1;
                  $state.complete();
                } else {
                  this.page += 1;
                  $state.loaded();
                }
              } else {
                // this.page = 1;
                $state.complete();
              }
              // [...new Set(this.categoryProducts.map(obj => obj.id))]
              // console.log(this.categoryProducts)
            })
            .catch((e) => console.log(e));
            }.bind(this),
        1000
      );
    },
  },
};
</script>