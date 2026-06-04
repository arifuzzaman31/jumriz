import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', {
    state: () => ({
      cart_items: [],
      total_cart_item: 0,
      total_cart_amount: 0,
      isLoading: false,
      trial_items: [],
      total_trial_item: 0,
      total_trial_amount: 0,
      isTrialLoading: false,
    }),
    getters: {
      cart_items: (state) => state.cart_items,
      cart_count: (state) => state.total_cart_item,
      cart_total: (state) => state.total_cart_amount,
      cart_loading: (state) => state.isLoading,
      productWithId: (state) => (id) => {
        return Object.values(state.cart_items).find(todo => todo.id == id)
      },
      trial_items: (state) => state.trial_items,
      trial_count: (state) => state.total_trial_item,
      trial_total: (state) => state.total_trial_amount,
      trial_loading: (state) => state.isLoading,
      trialWithId: (state) => (id) => {
        return Object.values(state.trial_items).find(product => product.id == id)
      },
    },
    actions: {
      async getCart() {
        this.isLoading = true
        const { data } = await axios.get(base_url + 'cart-items')
        this.cart_items = data.cart_items
        this.total_cart_item = data.cart_count
        this.total_cart_amount = data.cart_total
        this.isLoading = false
      },
      async getTrial() {
        this.isTrialLoading = true
        const { data } = await axios.get(base_url + 'trial-items')
        this.trial_items = data.trial_items
        this.total_trial_item = data.trial_count
        this.total_trial_amount = data.trial_total
        this.isTrialLoading = false
      },
    },
})
