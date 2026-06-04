<template>
  <div class="offer ">
    <!-- <a href="" class="btn theme-background color-white" title="Add To Trial">
     <i class="lni lni-tshirt"></i>
   </a> -->
   <div class="position-relative">
    <a :href="url + 'product/' + product.id + '/' + product.product_slug">
      <img v-lazy="product.feature_image" class="img-fluid" />
    </a>
          <a
        @click.prevent="
          addToCart(
            product.id,
            product.product_name,
            product.quantity_unit,
            1,
            product.current_quantity,
            product.discount_status == 1 && product.discount_amount > 0
              ? product.selling_price - product.discount_amount
              : product.selling_price,
            product.feature_image,
            product.discount_status == 1 && product.discount_amount > 0
              ? product.discount_amount
              : 0
          )
        "
        href=""
        class="button button-sm add_to_cart_button"
      >
        {{ cart_button }} <i class="lni-shopping-basket"></i
      ></a>
      </div>

    <div class="content">
      <a
        :href="url + 'product/' + product.id + '/' + product.product_slug"
        class="name"
        >{{ product.product_name }}</a
      >
      <!-- <p class="qty_unit">
        <small>{{ product.quantity_unit }}</small>
      </p> -->
      <div class="">
      <span class="regular-price" v-if="product.discount_status == 1"
        >{{ currency.symbol
        }}{{
          (product.selling_price - product.discount_amount) | formatPrice
        }}</span>

      <span class="regular-price" v-else
        >{{ currency.symbol }}{{ product.selling_price }}</span>
              <span
        class="discount-price"
        v-if="product.discount_status == 1 && product.discount_amount > 0"
        >{{ currency.symbol }}{{ product.selling_price | formatPrice }}</span
      >
      </div>
    </div>
  </div>
</template>

<script>
import { EventBus } from "../../../vue-assets";
import Mixin from "../../../mixin";

export default {
  props: ["currency", "product"],
  mixins: [Mixin],
  data() {
    return {
      url: base_url,
      cart_button: "Add to Cart",
    };
  },

  methods: {
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
      if (
        (this.product.product_size && this.product.product_size.length > 1) ||
        (this.product.product_color && this.product.product_color.length > 1)
      ) {
        EventBus.$emit("open-size-modal", this.product);
        return false;
      }

      this.playCartSound();

      this.cart_button = "Adding...";
      axios
        .post(base_url + "add-to-cart", {
          id: id,
          product_name: product_name,
          qty_unit: qty_unit,
          qty: qty,
          current_qty: current_qty,
          price: price,
          product_image: product_image,
          discount: discount,
        })
        .then((response) => {
          if (response.data.status === "success") {
            this.$store.dispatch("getCart");
            this.cart_button = "Add to Cart";
            this.successMessage(response.data);
          } else {
            this.successMessage(response.data);
            this.cart_button = "Add to Cart";
          }
        });
    },
  },
};
</script>

<style>
.add_to_cart_button{
    position: absolute;
    bottom: 0;
    z-index: 11;
    left: 0;
    width: 100%;
    opacity: 0;
    transition: 1s ease all;
}
.offers .offer:hover .add_to_cart_button{
    opacity: 1;
}
</style>


