<template>
    <div class="relative overflow-hidden bg-gray-100 rounded-lg">
        <img 
            v-lazy="src" 
            :alt="alt" 
            class="w-full h-full object-cover transition-opacity duration-300"
            @load="onImageLoad"
            @error="onImageError" 
        />
    </div>
</template>

<script setup>
defineProps({
    src: { type: String, required: true },
    alt: { type: String, default: 'Product Image' }
});

const onImageLoad = (e) => {
    e.target.style.opacity = 1;
};

// ✅ Catch the failed load and replace with a default placeholder
const onImageError = (e) => {
    // Make sure you have a default image in your public/images folder!
    e.target.src = '/images/default-placeholder.png'; 
    e.target.style.opacity = 1;
};
</script>

<style scoped>
/* Optional: Add a nice gray background while loading */
img[lazy=loading] {
    opacity: 0;
}
img[lazy=loaded] {
    opacity: 1;
}
img[lazy=error] {
    opacity: 1;
}
</style>