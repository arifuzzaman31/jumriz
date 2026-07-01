<template>
  <div class="container">
    <div class="row">
      <div class="col-md-12 offers">
        <div class="title mt30 mb30">
          <h4>Hot Deal</h4>
        </div>
      </div>
    </div>

    <div class="row" v-if="!isLoading">
      <Carousel
        :items-to-show="2.5"
        :autoplay="2000"
        :wrap-around="true"
        :breakpoints="breakpoints"
        class="offers col-md-12"
      >
        <Slide
          v-for="value in hotProducts"
          :key="value.id"
          class="col-12 custom_class"
        >
          <single-product
            class="hot_deal_offers"
            :currency="currency"
            :product="value"
          >
          </single-product>
        </Slide>
      </Carousel>
    </div>
    <div class="row" v-else>
      <div class="col-md-12 text-center">
        <img :src="url + 'images/loading.gif'" />
      </div>
    </div>
  </div>
</template>
      
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import SingleProduct from "./SingleProduct.vue";
import { Carousel, Slide } from "vue3-carousel";
import 'vue3-carousel/dist/carousel.css';

// --- Props ---
defineProps({
  currency: {
    type: Object,
    required: true
  }
});

// --- State ---
const hotProducts = ref([]);
const isLoading = ref(false);
const url = window.base_url; // Accessing global variable

// --- Carousel Breakpoints ---
const breakpoints = {
  0: { itemsToShow: 2 },
  580: { itemsToShow: 3 },
  1200: { itemsToShow: 4 },
  1500: { itemsToShow: 5 },
};

// --- Methods ---
const fetchProduct = () => {
  // will pull last 20 hot deal items
  return axios.get(`${url}product-list?no_paginate=yes&hot_deal=1&take_only=20`);
};

const getDeals = async () => {
  isLoading.value = true;
  try {
    const response = await fetchProduct();
    hotProducts.value = response.data.data;
  } catch (e) {
    console.error("Error fetching hot deals:", e);
  } finally {
    isLoading.value = false;
  }
};

// --- Lifecycle ---
onMounted(() => {
  getDeals();
});
</script>

<style>
@media only screen and (max-width: 1380px) {
  /* .custom_class {
    width: 200px !important;
  } */
}
.VueCarousel-dot-container{
  margin-top: -20px !important;
}
.VueCarousel-dot:focus {
    outline: inherit !important;
}
</style>
