import { defineStore } from 'pinia';
import axios from 'axios';

export const useCartStore = defineStore('cart', {
    // 1. State: Same as Vuex
    state: () => ({
        cart_items: [],
        trial_items: [],
        loading: false,
        error: null
    }),

    // 2. Getters: Same as Vuex but simpler
    getters: {
        cartTotal: (state) => state.cart_items.reduce((acc, item) => acc + item.price, 0),
        hasItems: (state) => state.cart_items.length > 0
    },

    // 3. Actions: Combined logic of Vuex Mutations + Actions
    actions: {
        async getCart() {
            this.loading = true;
            try {
                // const response = await axios.get('/api/cart');
                const response = await axios.get('/cart-items');
                // Instead of commit('SET_CART', data), modify state directly
                this.cart_items = response.data;
            } catch (err) {
                this.error = 'Failed to load cart';
                console.error(err);
            } finally {
                this.loading = false;
            }
        },

        async getTrial() {
            try {
                const response = await axios.get('/api/trial');
                this.trial_items = response.data;
            } catch (err) {
                console.error(err);
            }
        },

        async addItem(product) {
            try {
                const response = await axios.post('/api/cart', { product_id: product.id });
                this.cart_items.push(response.data);
                
                // You can call other actions directly
                // this.logActivity('Item Added'); 
            } catch (err) {
                this.error = 'Could not add item';
            }
        }
    }
});