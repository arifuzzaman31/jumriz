<template>
    <div class="p-4 border rounded shadow-sm">
        <h3 class="font-bold">Shopping Cart</h3>
        <div v-if="loading" class="animate-pulse">Updating...</div>
        
        <div v-else class="mt-2">
            <p>Total Items: {{ cart_items.length }}</p>
            <p class="text-green-600 font-semibold">Total: ${{ cartTotal }}</p>
        </div>

        <button 
            @click="refreshCart" 
            class="mt-3 px-3 py-1 bg-gray-200 rounded hover:bg-gray-300"
        >
            Sync Data
        </button>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useCartStore } from '../store/index';

// Initialize the store instance
const cartStore = useCartStore();

// Extract reactive state and getters using storeToRefs
const { cart_items, cartTotal, loading } = storeToRefs(cartStore);

// Methods become standard functions; call actions directly
const refreshCart = () => cartStore.getCart();

onMounted(() => {
    console.log('Cart component mounted with items:', cart_items.value.length);
});
</script>