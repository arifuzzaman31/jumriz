<template>
  <div class="container" id="searchItem">
    <div class="row">
      <div class="col-md-12 offers">
        <div class="title text-center" v-if="searchProducts.length > 0">
          <h4>Search Result</h4>
        </div>
        <div
          class="title text-center mt50"
          v-if="keyword !== '' && searchProducts.length <= 0 && !isLoading"
        >
          <h3>No product available 🥺 for keyword {{ keyword }}</h3>
        </div>
      </div>
    </div>
    <div class="row offers">
      <div
        class="col-6 col-lg-3 col-sm-4"
        v-for="value in searchProducts"
        :key="value.id"
      >
        <single-product :currency="currency" :product="value"></single-product>
      </div>
      <InfiniteLoading
        v-if="keyword"
        :identifier="infiniteId"
        @infinite="infiniteHandler"
      >
        <template #spinner>
          <div class="col-md-12 text-center">
            <img :src="`${url}images/loading.gif`" />
          </div>
        </template>
        <template #complete>
          <span></span>
        </template>
      </InfiniteLoading>
    </div>

    <div class="row" v-if="isLoading">
      <div class="col-md-12 text-center">
        <img :src="`${url}images/loading.gif`" />
      </div>
    </div>

    <div class="row row_margin" v-if="keyword !== ''"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";
import { emitter } from "../../../vue-assets"; // Assuming you have a global emitter
import SingleProduct from "./SingleProduct.vue";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";

// --- Props ---
defineProps({
  currency: {
    type: Object,
    required: true,
  },
});

// --- State ---
const url = window.base_url;
const searchProducts = ref([]);
const page = ref(1);
const keyword = ref("");
const infiniteId = ref(+new Date());
const isLoading = ref(false);

// --- Methods ---
const isInViewport = (elem) => {
  if (!elem) return false;
  const bounding = elem.getBoundingClientRect();
  return (
    bounding.top >= 20 &&
    bounding.left >= 0 &&
    bounding.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
    bounding.right <= (window.innerWidth || document.documentElement.clientWidth)
  );
};

const fetchProduct = () => {
  return axios.get(`${url}product-list?page=${page.value}&keyword=${keyword.value}`);
};

const infiniteHandler = async ($state) => {
  try {
    const response = await fetchProduct();
    if (response.data.data.length > 0) {
      searchProducts.value.push(...response.data.data);
      page.value += 1;
      $state.loaded();
    } else {
      $state.complete();
    }
  } catch (error) {
    console.error("Error in infiniteHandler:", error);
    $state.error();
  }
};

const handleSearch = (searchKeyword) => {
  page.value = 1;
  searchProducts.value = [];
  keyword.value = searchKeyword;
  infiniteId.value += 1; // This tells the component to reset

  // Scroll to results
  const elmnt = document.getElementById("searchItem");
  if (elmnt && !isInViewport(elmnt)) {
    elmnt.scrollIntoView({ behavior: "smooth" });
  }
};

// --- Lifecycle Hooks ---
onMounted(() => {
  emitter.on("scrol-to-result", handleSearch);
});

onBeforeUnmount(() => {
  emitter.off("scrol-to-result", handleSearch);
});
</script>

<style scoped>
.row_margin {
  margin-top: 100vh;
}
</style>