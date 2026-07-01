<template>
  <div v-if="offers.length >= 1">
    <div class="banner mt50 row">
      <!-- ✅ FIXED: v-for and v-if are now separated using a computed property -->
      <div 
        class="col-lg-6" 
        v-for="value in limitedOffers" 
        :key="value.id"
      >
        <div class="bn">
          <a :href="`${base_url}offer/${value.id}/${value.slug}`">
            <!-- Note: Ensure you are using a Vue 3 compatible lazy-load package (like @xunlei/vue-lazyload) -->
            <img v-lazy="value.banner" alt="banner" class="img-fluid mb30">
          </a>
        </div>
      </div>
    </div>

    <div class="row category" v-if="offers.length >= 3">
      <div class="col-md-12 read-more text-center">
        <a :href="`${base_url}offers`" class="button button-md"> 
          View All Offer <i class="lni-arrow-right"></i>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
// import { base_url } from '../../../vue-assets' // Uncomment if base_url isn't global

// --- State ---
const offers = ref([])
const isLoading = ref(false) // Kept if you want to add a loading spinner later

// --- Computed ---
// Much cleaner and more performant than putting `v-if="index < 4"` in the template
const limitedOffers = computed(() => {
  return offers.value.slice(0, 4)
})

// --- Methods ---
const getOffers = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(`${base_url}home-offers`)
    offers.value = response.data.data
	console.log(offers.value)
  } catch (error) {
    console.error('Error fetching offers:', error)
  } finally {
    isLoading.value = false
  }
}

// --- Lifecycle ---
onMounted(() => {
  getOffers()
})
</script>