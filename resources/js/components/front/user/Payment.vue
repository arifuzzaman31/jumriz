<template>
<div id="modal-payment" class="modal fade" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-right">
				<h3 class="modal-title">You are paying for order <strong>#{{ paymentStore.order.id }}</strong></h3>
				<button class="btn btn-default" data-dismiss="modal">X</button>
			</div>
			<div class="modal-body">
                <div class="row"> 
                    <div class="col-md-12 text-center">
                        <div class="form-group">
                            <p><strong> Sub Total : </strong> {{ currency.symbol }}{{ formatPrice(paymentStore.order.total_amount) }}</p>
                            <p><strong> Shipping  : </strong> {{ currency.symbol }}{{ formatPrice(paymentStore.order.shipping_amount) }}</p>
                            <p><strong> Total  :</strong>  {{ currency.symbol }}{{ formatPrice(paymentStore.totalAmount) }}</p>
                        </div>
                        <div class="fomr-group" v-for="value in paymentStore.paymentMethods" :key="value.id">
                            <div class="row" v-if="value.id == 3">
                                <div class="col-md-12">
                                <p class="text-left float-left login-with-social">
                                    Fill these field if you wants to pay in stripe
                                    </p>
                                <input type="text" class="form-control mt-2" v-model="paymentStore.stripe.card_no" placeholder="Card No">
                                <input type="text" class="form-control mt-2" v-model="paymentStore.stripe.cvc" placeholder="CVC - Ex:123">
                                </div>
                                <div class="col-md-6">
                                <input type="text"
                                 class="form-control mt-2"
                                  v-model="paymentStore.stripe.expire_month" 
                                  placeholder="Expire Month EX:06">
                                </div>
                                <div class="col-md-6">
                                <input type="text"
                                 class="form-control mt-2"
                                  v-model="paymentStore.stripe.expire_year" 
                                  placeholder="Expire Year EX:2030">
                                </div>
                            </div>
                          <a @click="validateStripe(value.id, $event)" 
                          :href="value.id == 3 ? url+value.provider+'/'+paymentStore.order.id+'?card_no='+paymentStore.stripe.card_no+'&cvc='+paymentStore.stripe.cvc+'&expire_month='+paymentStore.stripe.expire_month+'&expire_year='+paymentStore.stripe.expire_year : url+value.provider+'/'+paymentStore.order.id" 
                          class="btn btn-primary btn-block mt-2 mb-2">
                          <i v-if="value.id == 2" class="lni lni-paypal"></i> 
                          <i v-if="value.id == 3" class="lni lni-stripe"></i> 
                          Pay with {{ value.provider }}
                          </a>   
                        </div>
                    </div>
                </div>
                <button class="btn btn-succes"></button>
			</div>
		</div>
	</div>
</div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue';
import { emitter, base_url } from '../../../vue-assets';
import { useCommonActions } from '../../../useCommonActions';
import { usePaymentStore } from '../../../store/usePaymentStore';

const props = defineProps(['currency']);
const paymentStore = usePaymentStore();
const { formatPrice } = useCommonActions();
const url = base_url;

const validateStripe = (id, event) => {
    const STRIPE_ID = 3;
    if (id === STRIPE_ID) {
        const s = paymentStore.stripe;
        if (!s.card_no || !s.cvc || !s.expire_month || !s.expire_year) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'card information required when you want to pay in stripe ',
            });
        }
    }
};

onMounted(() => {
    emitter.on('make-payment', (orderData) => {
        paymentStore.setOrder(orderData);
        paymentStore.fetchPaymentMethods();
        $("#modal-payment").modal('show');
    });
});

onUnmounted(() => {
    emitter.off('make-payment');
});
</script>
