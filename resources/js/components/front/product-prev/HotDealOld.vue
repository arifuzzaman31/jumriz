<template>
	      <div class="container">
	      	<div class="row">
	      		<div class="col-md-12 offers">
	      			<div class="title  mt50 mb30">
	      				<h4>Hot Deal</h4>
	      			</div>
	      		</div>
	      	</div>
            <div class="row offers">

                    <div class="col-6 col-lg-3 col-sm-6" v-for="value in hotProducts" :key="value.id">
   <single-product :currency="currency" :product="value">  
   </single-product>
                    </div>
                    
                <InfiniteLoading
                  spinner="bubbles"
                  @infinite="infiniteHandler"
                >
                  <template #spinner>
                    <div class="col-md-12 text-center">
                      <img :src="url+'images/loading.gif'">
                    </div>
                  </template>
                  <template #no-more><span></span></template>
                  <template #no-results><span></span></template>
                </InfiniteLoading>

            </div>
        </div>
</template>

<script>
	import {EventBus} from  '../../../vue-assets';
	import { useMixin } from  '../../../mixin';
  import SingleProduct from './SingleProduct.vue';
  import InfiniteLoading from "v3-infinite-loading";

	export default {
		props : ['currency'],
    mixins : [Mixin],
    components : {
     'single-product' : SingleProduct,
    'infinite-loading' : InfiniteLoading
    },    
        data(){
           
           return {
            
            hotProducts : [],
            url : base_url,
            page: 1,
            lastPage: 0,

           }

        },

        mounted()
        {
        	this.getDeals();
        },

        methods : {

      fetchProduct: function() {
      
      return axios.get(base_url+'hot-deal?page='+this.page);

       },
       
         getDeals()
         {
          this.fetchProduct()
          .then(response => {
            if (response.data.data.length > 0) {
              this.hotProducts = response.data.data;
              this.page += 1;
            }else{
              console.log('Product not available');
            }
          })
          .catch(e => console.log(e))
         },

      infiniteHandler: function($state) {
      setTimeout(function () {
        this.fetchProduct()
        .then(response => {
          if (response.data.data.length > 0) {
            this.lastPage = response.data.meta.last_page;
                           this.hotProducts.push(...response.data.data);
                           
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
        .catch(e => console.log(e));
      }.bind(this), 1000);
    }

        }
	}	
</script>