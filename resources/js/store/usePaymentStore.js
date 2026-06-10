import { defineStore } from 'pinia';
import axios from 'axios';
import { base_url } from '../vue-assets';

export const usePaymentStore = defineStore('payment', {
    state: () => ({
        order: {},
        paymentMethods: [],
        stripe: {
            card_no: '',
            cvc: '',
            expire_month: '',
            expire_year: '',
        },
    }),
    getters: {
        totalAmount: (state) => {
            const total = parseFloat(state.order.total_amount || 0);
            const shipping = parseFloat(state.order.shipping_amount || 0);
            return total + shipping;
        }
    },
    actions: {
        async fetchPaymentMethods() {
            try {
                const response = await axios.get(base_url + 'payment-method-list');
                this.paymentMethods = response.data;
            } catch (error) {
                console.error("Error fetching payment methods:", error);
            }
        },
        setOrder(orderData) {
            this.order = orderData;
            // Reset stripe info when a new order is set
            this.stripe = { card_no: '', cvc: '', expire_month: '', expire_year: '' };
        }
    }
});