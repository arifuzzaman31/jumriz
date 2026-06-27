<template>
  <div>
    <section class="product-details mt30">
      <div class="container">
        <div class="row">
          <div class="col-lg-6 col-sm-12">
            <div class="products-slider" style="overflow: hidden">
              <div class="big-img">
                <div class="zoom-left">
                  <img
                    class="img-fluid"
                    style
                    id="product_zoom"
                    v-lazy="product.feature_image"
                    :data-zoom-image="product.feature_image"
                  />

                  <div
                    id="product_small"
                    class="mt10"
                    v-if="product.multiple_image.length > 0"
                  >
                    <a
                      href="#"
                      class="elevatezoom-gallery"
                      :data-image="product.feature_image"
                      :data-zoom-image="product.feature_image"
                    >
                      <img
                        v-lazy="product.feature_image"
                        width="100"
                        class="img-fluid"
                        style="margin: 0px 5px;"
                      />
                    </a>
                    <a
                      v-for="(value, index) in product.multiple_image"
                      :key="index"
                      href="#"
                      class="elevatezoom-gallery"
                      :data-image="value.image"
                      :data-zoom-image="value.image"
                    >
                      <img v-lazy="value.image" width="100" class="img-fluid"   style="margin: 0px 5px;"/>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- left product end-->

          <div class="col-lg-6 col-sm-12">
            <div class="pro-det-right">
              <h3>{{ product.product_name }}</h3>
              <!-- <div class="pro-items qty_unit_details">
                <span class="item-number">{{ product.quantity_unit }}</span>
              </div> -->

                <div class="display-flex">
                  <div class="mr15">
                    <span class="item-name"><b>Availability:</b></span>
                    <span class="item-val" v-if="product.current_quantity > 0"
                      >YES</span
                    >
                    <span class="item-val" v-else>No</span>
                  </div>

                  <div class="mr15">
                    <span class="item-name"><b>Brand:</b></span>
                    <span class="item-val">{{ product.brand.brand_name }}</span>
                  </div>

                  <div class="pro-items">
                    <span class="item-name"><b>Item No.:</b></span>
                    <span class="item-number">ITM-#{{ product.id }}</span>
                  </div>

              </div>

                <div class="price">
                <!-- <span class="price-name">PRICE:</span> -->
                <span class="price-number" v-if="product.discount_status == 1"
                  >{{ currency.symbol
                  }}{{
                    (product.selling_price - product.discount_amount)
                      | formatPrice
                  }}</span
                >

                <span class="price-number" v-else
                  >{{ currency.symbol
                  }}{{ product.selling_price | formatPrice }}</span
                >

                <span
                  class="discount-price"
                  v-if="
                    product.discount_status == 1 && product.discount_amount > 0
                  "
                  >{{ currency.symbol
                  }}{{ product.selling_price | formatPrice }}</span
                >
              </div>
 

              <hr>
    
              <!-- color  -->
              <div class="pro-item" v-if="product.product_color.length > 0">
                <span class="item-name"><b>Color:</b></span> 
                <div class="btn-group ml10" data-toggle="buttons">
                  <a
                    v-for="clr in product.product_color"
                    data-toggle="tooltip"
                    data-placement="top"
                    :key="clr.id"
                    :title="clr.name + ' color'"
                    :class="
                      cart.color_id == clr.id
                        ? 'custom-color-button active_color_button'
                        : 'custom-color-button'
                    "
                    :style="{
                      'background-color': clr.color_code,
                      'margin-right': '5px',
                    }"
                    @click.prevent="changeColor(clr)"
                  >
                  </a>
                </div>
                <br />
              </div>

              <!-- color        -->

              <!--size-->
              <div class="pro-item mt20" v-if="product.product_size.length > 0">
                <span class="item-name"><b>Size:</b></span> 
                <div class="btn-group btncolorProduct" data-toggle="buttons">
                  <label
                    v-for="sz in product.product_size"
                    :key="sz.id"
                    class="btn btn-outline-danger font-bold "
                    style="margin-right: 5px"
                    @click="changeSize(sz)"
                  >
                    <input
                      type="radio"
                      name="options"
                      class="my-radio"
                      :value="sz.id"
                      autocomplete="off"
                    />
                    <i
                      class="lni lni-checkmark"
                      v-if="cart.size_id == sz.id"
                    ></i>
                    {{ sz.name }}
                  </label>
                </div>
                <br />
              </div>
              <!--size-->
              <span class="button-wrapper mt15" style="">
                <a
                  @click.prevent="
                    addToCart(
                      product.id,
                      product.product_name,
                      product.quantity_unit,
                      cart.qty,
                      product.current_quantity,
                      product.discount_status == 1 &&
                        product.discount_amount > 0
                        ? product.selling_price - product.discount_amount
                        : product.selling_price,
                      product.feature_image,
                      product.discount_status == 1 &&
                        product.discount_amount > 0
                        ? product.discount_amount
                        : 0
                    )
                  "
                  class="button btn-cart theme-background color-white"
                  href
                >
                  Add to Cart
                  <i class="lni lni-shopping-basket"></i>
                </a>
              </span>
              <!-- <span
                class="button-wrapper"
                style="margin-top: -15px"
                v-if="product.trialable == 1 && .status == 1"
              >
                <a
                  href=""
                  class="button btn-cart"
                  @click.prevent="
                    addToTrial(
                      product.id,
                      product.product_name,
                      product.quantity_unit,
                      cart.qty,
                      product.current_quantity,
                      product.discount_status == 1 &&
                        product.discount_amount > 0
                        ? product.selling_price - product.discount_amount
                        : product.selling_price,
                      product.feature_image,
                      product.discount_status == 1 &&
                        product.discount_amount > 0
                        ? product.discount_amount
                        : 0
                    )
                  "
                >
                  Add To Trial
                  <i class="lni-tshirt"></i>
                </a>
              </span> -->

              <div
                style="margin-bottom: 20px"
                class="short-des"
                v-if="product.short_description"
              >
                <div
                  v-if="
                    !readMoreActive && product.short_description.length > 199
                  "
                >
                  <div v-html="product.short_description.slice(0, 200)"></div>
                  <a
                    class="more-less theme-color"
                    v-if="!readMoreActive"
                    @click.prevent="activateReadMore"
                    href
                    >...Read more</a
                  >
                </div>
                <div v-else>
                  <div v-html="product.short_description"></div>
                  <a
                    class="more-less theme-color"
                    v-if="readMoreActive"
                    @click.prevent="activateLessText"
                    href
                    >---Less text</a
                  >
                </div>
              </div>

              <div class="follow mt10">
                <span>share:</span>
                <a
                  class="entry bg-primary text-white"
                  :href="
                    'https://www.facebook.com/sharer/sharer.php?u=' +
                    url +
                    'product/' +
                    product.id +
                    '/' +
                    product.product_slug
                  "
                >
                  <i class="lni lni-facebook-filled"></i>
                </a>
                <a
                  class="entry bg-info text-white"
                  :href="
                    'https://twitter.com/intent/tweet?text=' +
                    url +
                    'product/' +
                    product.id +
                    '/' +
                    product.product_slug
                  "
                  data-size="large"
                >
                  <i class="lni lni-twitter-filled"></i>
                </a>
                <!-- <a class="entry" href="#"><i class='lni lni-linkedin-original'></i></a>
                <a class="entry" href="#"><i class='lni lni-pinterest'></i></a>-->
              </div>
            </div>
          </div>
          <!-- product details end-->
        </div>
      </div>
    </section>

    <section class="product-description mt30 pt50 pb30" style="background-color:#F2F6FF;">
      <div class="container">
        <div class="row">
          <div class="col-md-12">

                <ul class="nav nav-tabs" id="myTab" role="tablist" style="justify-content: center;">
                  <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#Description" role="tab" aria-controls="home" aria-selected="true">
                      <h3>Description</h3>
                      </a>
                  </li>
                  <li class="nav-item" role="presentation">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#Additionalinformation" role="tab" aria-controls="profile" aria-selected="false">
                       <h3>Additional information</h3>
                       </a>
                  </li>
                  <li class="nav-item" role="presentation">
                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#Refundpolicy" role="tab" aria-controls="contact" aria-selected="false">
                       <h3>Refund Policy</h3>
                       </a>
                  </li>
                </ul>
                <div class="tab-content pl15 pt30" id="myTabContent">
                  <div class="tab-pane fade show active" id="Description" role="tabpanel" aria-labelledby="home-tab">
                    
                    <!-- <h3>Specifications</h3> -->
                    <p v-html="product.product_description"></p>

                  </div>
                  <div class="tab-pane fade" id="Additionalinformation" role="tabpanel" aria-labelledby="profile-tab">
                    <p v-html="product.additional_info"></p>
                  </div>
                  <div class="tab-pane fade" id="Refundpolicy" role="tabpanel" aria-labelledby="contact-tab">
                    <p v-html="shop_info.return_policy"></p></div>
                </div>

          </div>
        </div>
      </div>
    </section>
      
    <!--Raleted product-->
    <section class="product-details mt30">
      <div class="container">
        <div class="row">
          <div class="col-md-12 offers">
            <div class="title mt50 mb30">
              <h4>Related Product</h4>
            </div>
          </div>
        </div>
        <div class="row offers">
          <div
            class="col-6 col-lg-3 col-sm-4 mb30"
            v-for="value in hotProducts"
            :key="value.id"
          >
            <single-product
              :currency="currency"
              :product="value"
            ></single-product>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { EventBus } from "../../../vue-assets";
import { useMixin } from "../../../mixin";
import SingleProduct from "./SingleProduct";
import { useCartStore, storeToRefs } from '../../../../store/index';

export default {
  props: ["currency", "product","shop_info"],
  mixins: [Mixin],
  components: {
    "single-product": SingleProduct,
  },
  setup() {
    const cartStore = useCartStore();
    const { cart_count, cart_total, cart_items, isLoading, trial_count, trial_total, isTrialLoading, trial_items } = storeToRefs(cartStore);
    return { cart_count, cart_total, cart_items, isLoading, trial_count, trial_total, isTrialLoading, trial_items, cartStore };
  },
  data() {
    return {
      hotProducts: [],
      url: base_url,

      cart: {
        qty: 1,
        size_id: "",
        size_name: "",
        color_id: "",
        color_name: "",
        color_code: "",
      },

      readMoreActive: false,
    };
  },

  mounted() {
    this.getDeals();
  },

  methods: {
    changeColor(clr) {
      this.cart.color_id = clr.id;
      this.cart.color_name = clr.name;
      this.cart.color_code = clr.color_code;
    },

    changeSize(size) {
      this.cart.size_id = size.id;
      this.cart.size_name = size.name;
    },

    addToCart(
      id,
      product_name,
      qty_unit,
      qty,
      current_qty,
      price,
      product_image,
      discount
    ) {
      //  ceck color and size checked if color is there

      if (this.product.product_color.length > 1 && this.cart.color_id == "") {
        this.validationError("Please Choose a color");
        return false;
      }

      if (this.product.product_size.length > 1 && this.cart.size_id == "") {
        this.validationError("Please choose a size");
        return false;
      }
      this.playCartSound();
      //  ceck color and size checked if color is there
      axios
        .post(base_url + "add-to-cart", {
          id: id,
          product_name: product_name,
          qty_unit: qty_unit,
          qty: this.cart.qty,
          current_qty: current_qty,
          price: price,
          product_image: product_image,
          discount: discount,
          size_id: this.cart.size_id,
          size_name: this.cart.size_name,
          color_id: this.cart.color_id,
          color_name: this.cart.color_name,
          color_code: this.cart.color_code,
        })
        .then((response) => {
          if (response.data.status === "success") {
            // if (
            //   this.product.product_size.length > 0 &&
            //   this.product.product_color.length > 0
            // ) {
            this.successMessage(response.data);
            // }

            this.cartStore.getCart();
          } else {
            this.successMessage(response.data);
          }
        });
    },

    addToTrial(
      id,
      product_name,
      qty_unit,
      qty,
      current_qty,
      price,
      product_image,
      discount
    ) {
      //  ceck color and size checked if color is there

      if (this.product.product_color.length > 1 && this.cart.color_id == "") {
        this.validationError("Please Choose a color");
        return false;
      }

      if (this.product.product_size.length > 1 && this.cart.size_id == "") {
        this.validationError("Please choose a size");
        return false;
      }

      //  ceck color and size checked if color is there
      axios
        .post(base_url + "add-to-trial", {
          id: id,
          product_name: product_name,
          qty_unit: qty_unit,
          qty: this.cart.qty,
          current_qty: current_qty,
          price: price,
          product_image: product_image,
          discount: discount,
          size_id: this.cart.size_id,
          size_name: this.cart.size_name,
          color_id: this.cart.color_id,
          color_name: this.cart.color_name,
          color_code: this.cart.color_code,
        })
        .then((response) => {
          if (response.data.status === "success") {
            this.successMessage(response.data);
            this.cartStore.getTrial();
          } else {
            this.validationError(response.data.message);
          }
        });
    },

    activateReadMore() {
      this.readMoreActive = true;
    },

    activateLessText() {
      this.readMoreActive = false;
    },

    fetchProduct: function () {
      return axios.get(
        base_url +
          "product-list?no_paginate=yes&take_only=8&sub_category=" +
          this.product.sub_category_id +
          "&without_id=" +
          this.product.id
      );
    },

    getDeals() {
      this.fetchProduct()
        .then((response) => {
          if (response.data.data.length > 0) {
            this.hotProducts = response.data.data;
          } else {
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>


<style scoped="">
/* .discount-price{
	text-decoration: line-through;
} */

.num {
  display: inline;
}
.qty_text {
  float: left;
  /*width: 34px;*/
  height: 40px;
  line-height: 40px;
  border: none;
  background-color: transparent;
  text-align: center;
  padding: 0px 10px 0px 10px;
  margin: 0px;
  font-size: 1.5em;
}
.btncolorProduct .btn-outline-danger{
    color: #212529;
    border-color: #fff;
    border-radius: 50px !important;
    border: 1px solid #ddd;
    width: 32px;
    height: 32px;
    text-align: center;
    font-size: 14px;
    padding: 5px;
    margin-bottom: 0;
}
.btncolorProduct .btn-outline-danger:hover{
    color: #fff;
}
.btn-outline-danger:hover, .btn-outline-danger.active{
  background-color: #EA0F61 !important ;
}
/* .product-description ul li .nav-link::before {
    content: "";
    display: block;
    background: red;
    position: absolute;
    border-bottom: 1px solid #ed0000;

}
.product-description ul li .nav-link.active::before {
       left: 0;
    right:  0;
    width: 100%;
} */
.product-description  .nav-link.active {
    font-weight: bolder;
    color: #323a6f;
}
.product-description  .nav-link:hover{
    color: #323a6f;
}
.product-description  .nav-link.active h3{
  font-weight: 600;
}
.product-description .nav-link.active {
    background: transparent;
    border-bottom: 2px solid #323a6f !important;
    opacity: 1;
}
</style>