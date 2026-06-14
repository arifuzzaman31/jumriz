<template>
    <div class="relative overflow-hidden bg-gray-100 rounded-lg">
        <!-- 
             The v-lazy directive handles the intersection observer logic.
             It will swap the src with the actual image once it enters the viewport.
        -->
        <img 
            v-lazy="src" 
            :alt="alt" 
            class="w-full h-full object-cover transition-opacity duration-300"
            @load="onImageLoad"
        />
    </div>
</template>

<script setup>
defineProps({
    src: { type: String, required: true },
    alt: { type: String, default: 'Product Image' }
});

const onImageLoad = (el) => {
    el.target.style.opacity = 1;
};
</script>