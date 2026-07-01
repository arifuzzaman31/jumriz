<template>
    <div>
        <!-- slider -->
        <home-slider v-if="shop_info.slider_status == 1"></home-slider>
        <!-- end slider -->

        <!-- home category -->
        <div class="container">
            <div class="row category">
                <div class="col-md-12">
                    <div class="title mt50 mb30">
                        <h4>Product Categories</h4>
                    </div>
                </div>
            </div>
            <div v-if="!homeStore.loading.categories" class="row category home-category">
                <div v-for="cat in categories" :key="cat.id" class="col-lg-2 col-sm-3 col-4">
                    <a :href="`${base_url}category-product/${cat.id}/${cat.category_name.replace(/ /g, '-')}`" :title="cat.category_name">
                        <div class="box">
                            <div class="content">
                                <img :src="`${base_url}images/category/icon/${cat.icon}`" :alt="cat.category_name" class="img-fluid">
                                <h3>{{ cat.category_name }}</h3>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row" v-else>
                <div class="col-md-12 text-center">
                    <img :src="`${base_url}images/loading.gif`" alt="Loading...">
                </div>              
            </div>
        </div>
        <!-- end category -->

        <search-product :currency="currency"></search-product>

        <!-- start hot deal -->
        <hot-deal v-if="shop_info.hot_deal_status == 1" :currency="currency"></hot-deal>
        <!-- end hot deal -->

        <!--start banner-->
        <div class="container">
            <home-offers></home-offers>
        </div>
        <!--end banner-->

        <!-- on sale -->
        <product-by-category v-if="shop_info.onsale_status == 1" :currency="currency"></product-by-category>
        <!-- end on sale -->

    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useHomeStore } from './home';

defineProps(['shop_info', 'currency']);

const homeStore = useHomeStore();
const categories = computed(() => homeStore.categories);
const base_url = window.base_url;
homeStore.getCategories();
</script>