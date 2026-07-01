import { defineStore } from 'pinia';
import axios from 'axios';

export const useHomeStore = defineStore('home', {
    state: () => ({
        sliders: [],
        categories: [],
        loading: {
            sliders: false,
            categories: false,
        }
    }),

    actions: {
        async getSliders() {
            this.loading.sliders = true;
            try {
                const response = await axios.get(`${base_url}api/home-sliders`);
                this.sliders = response.data.data;
            } catch (error) {
                console.error('Error fetching sliders:', error);
            } finally {
                this.loading.sliders = false;
            }
        },
        async getCategories() {
            this.loading.categories = true;
            try {
                // We will create this API route in the next step
                const response = await axios.get(`${base_url}api/home-categories`);
                this.categories = response.data.data;
            } catch (error) {
                console.error('Error fetching categories:', error);
            } finally {
                this.loading.categories = false;
            }
        }
    }
});