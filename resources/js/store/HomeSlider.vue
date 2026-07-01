<template>
    <div v-if="sliders.length > 0" class="container-fluid">
        <div class="row">
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li v-for="(slider, index) in sliders" :key="slider.id" data-target="#carouselExampleIndicators" :data-slide-to="index" :class="{ active: index === 0 }"></li>
                </ol>
                <div class="carousel-inner">
                    <div v-for="(slider, index) in sliders" :key="slider.id" class="carousel-item" :class="{ active: index === 0 }">
                        <a :href="slider.back_link_url">
                            <img class="d-block w-100" :src="`${base_url}images/slider/${slider.slider_image}`" :alt="slider.title">
                        </a>
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon theme-background" aria-hidden="true"><i class="lni lni-arrow-left color-white"></i></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon theme-background" aria-hidden="true"><i class="lni lni-arrow-right color-white"></i></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useHomeStore } from './home.js';

const homeStore = useHomeStore();
const sliders = computed(() => homeStore.sliders);
const base_url = window.base_url;

homeStore.getSliders();
</script>