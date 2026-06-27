<template>
  <div id="modal-form" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-custom">
      <div class="modal-content">
        <div class="modal-header text-right">
          <h3 class="modal-title">
            Invoice<strong>#{{ orderNo }}</strong>
          </h3>
          <button type="button" class="btn btn-default" data-dismiss="modal">X</button>
        </div>
        
        <div class="modal-body">
          <div class="row pb-2 invoice-header">
            <div class="col-md-6">
              <p class="font-weight-bold mb-2">Shipping Information</p>
              <p>{{ order.cust_name }}</p>
              <p>{{ order.cust_phone }}</p>
              <p v-if="order.cust_area">{{ order.cust_area.city }}</p>
              <p>{{ order.cust_address }}</p>
            </div>

            <div class="col-md-6 text-right">
              <p class="font-weight-bold mb-2">Payment Info</p>
              <p>
                <span class="text-muted">Status: </span>
                <span v-if="order.payment_status == 1">Paid</span>
                <span v-else>Unpaid</span>
              </p>
              <p v-if="order.payment_status == 1">
                <span class="text-muted">Paid In: </span>
                <span v-if="order.payment_method == 2">Paypal</span>
                <span v-else-if="order.payment_method == 3">Stripe</span>
                <span v-else-if="order.payment_method == 4">SSL Commerz</span>
                <span v-else-if="order.payment_method == 5">Razorpay</span>
                <span v-else>Cash on Delivery</span>
              </p>
              <p>
                <span class="text-muted">Order Placed: </span>
                {{ order.order_date | dateToString }}
              </p>
              <p v-if="order.customer_delivery_date">
                <span class="text-muted">Expected Delivery Slot: </span>
                {{ order.customer_delivery_date | dateToString }} ({{ order.customer_delivery_time }})
              </p>
              <p v-if="order.status == 3">
                <span class="text-muted">Delivery Date: </span>
                {{ order.delivery_date | dateToString }}
              </p>
            </div>
          </div>

          <!-- Main Order Table -->
          <div class="row" v-if="!isLoading">
            <div class="col-lg-12 col-sm-12">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">Image</th>
                      <th scope="col">Name</th>
                      <th scope="col">Qty</th>
                      <th scope="col">Unit Price</th>
                      <th scope="col">Total Price</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="value in detailsInfo" :key="value.id">
                      <td>
                        <img
                          v-if="value.product?.product_image"
                          v-lazy="url + 'images/product/feature/' + value.product.product_image"
                          alt="Product Image"
                          height="40"
                          width="50"
                        />
                        <span v-else class="text-muted">No Image</span>
                      </td>
                      <td>
                        {{ value.product?.product_name }} 
                        <br />
                        <small v-if="value.product?.quantity_unit">{{ value.product.quantity_unit }}</small>
                        <br />
                        <span 
                          v-if="value.color?.color_code"
                          class="color-button"
                          :style="{ 'background-color': value.color.color_code }"
                          :title="value.color.name"
                        ></span>
                      </td>
                      <td>{{ value.quantity }}</td>
                      <td>
                        {{ currency.symbol }} {{ value.selling_price | formatPrice }}
                        <span
                          v-if="Number(value.unit_discount) > 0"
                          class="discount-price"
                        >
                          {{ currency.symbol }} {{ (Number(value.selling_price) + Number(value.unit_discount)) | formatPrice }}
                        </span>
                      </td>
                      <td>
                        {{ currency.symbol }} {{ value.total_selling_price | formatPrice }}
                      </td>
                    </tr>

                    <!-- Subtotals -->
                    <tr>
                      <td class="no-line"></td>
                      <td class="no-line"></td>
                      <td class="no-line"></td>
                      <td class="no-line"><strong>Subtotal</strong></td>
                      <td class="no-line">
                        {{ currency.symbol }} {{ order.total_amount | formatPrice }}
                      </td>
                    </tr>
                    <tr>
                      <td class="no-line"></td>
                      <td class="no-line"></td>
                      <td class="no-line"></td>
                      <td class="no-line"><strong>Shipping</strong></td>
                      <td class="no-line">
                        {{ currency.symbol }} {{ order.shipping_amount | formatPrice }}
                      </td>
                    </tr>
                    
                    <!-- Conditional Discount Row -->
                    <tr v-if="order.coupon_discount > 0">
                      <td class="no-line"></td>
                      <td class="no-line"></td>
                      <td class="no-line"></td>
                      <td class="no-line">
                        <strong>Discount ({{ order.coupon }})</strong>
                      </td>
                      <td class="no-line">
                        - {{ currency.symbol }} {{ order.coupon_discount | formatPrice }}
                      </td>
                    </tr>

                    <!-- Grand Total Row -->
                    <tr>
                      <td class="no-line"></td>
                      <td class="no-line"></td>
                      <td class="no-line"></td>
                      <td class="no-line"><strong>Grand Total</strong></td>
                      <td class="no-line">
                        {{ currency.symbol }} 
                        {{ (Number(order.shipping_amount) + Number(order.total_amount) - Number(order.coupon_discount) | formatPrice }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Trial Products Section -->
              <div 
                class="table-responsive" 
                v-if="trialProduct.length > 0 && showTrial"
              >
                <table class="table">
                  <tr>
                    <th scope="col">Image</th>
                    <th scope="col">Name</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Unit Price</th>
                    <th scope="col">Total Price</th>
                  </tr>
                  
                  <tr class="bg-dark text-white">
                    <td colspan="5" style="text-align: center; font-size: 20px;">
                      Trial Items
                    </td>
                  </tr>

                  <tr v-for="value in trialProduct" :key="value.id">
                    <td>
                      <img
                        v-if="value.product?.product_image"
                        v-lazy="url + 'images/product/feature/' + value.product.product_image"
                        alt="Product Image"
                        height="40"
                        width="50"
                      />
                      <span v-else class="text-muted">No Image</span>
                    </td>
                    <td>
                      {{ value.product?.product_name }} <br />
                      <small v-if="value.product?.quantity_unit">{{ value.product.quantity_unit }}</small>
                      <br />
                      <span 
                        v-if="value.color?.color_code"
                        class="color-button"
                        :style="{ 'background-color': value.color.color_code }"
                        :title="value.color.name"
                      ></span>
                    </td>
                    <td>{{ value.quantity }}</td>
                    <td>
                      {{ currency.symbol }} {{ value.selling_price | formatPrice }}
                      <span
                        v-if="Number(value.unit_discount) > 0"
                        class="discount-price"
                      >
                        {{ currency.symbol }} {{ (Number(value.selling_price) + Number(value.unit_discount)) | formatPrice }}
                      </span>
                    </td>
                    <td>
                      {{ currency.symbol }} {{ value.total_selling_price | formatPrice }}
                    </td>
                  </tr>
                </table>

                <a
                  v-if="trialProduct.length > 0"
                  @click.prevent="showTrial = !showTrial"
                  href="javascript:void(0)"
                  class="btn theme-background color-white mt-2"
                >
                  <span v-if="showTrial">Hide</span>
                  <span v-else>Show</span> Trial Items
                </a>
              </div>
            </div>
          </div>

          <!-- Loading State -->
          <div class="row" v-else>
            <div class="col-md-12 text-center">
              <img :src="url + 'images/loading.gif'" alt="Loading..." />
            </div>
          </div>

          <!-- Print/PDF Download Row -->
          <div class="row mt-4">
            <div class="col-10"></div>
            <div class="col-2 d-flex align-items-center justify-content-end gap-2">
              <a
                :href="url + 'user-order-details-pdf/' + orderNo"
                class="btn btn-primary btn-sm"
                target="_blank"
              >
                PDF
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, toRefs } from 'vue';
import { EventBus, base_url } from "../../../vue-assets";
import { useMixin } from "../../../mixin";

// ✅ Define Props
const props = defineProps({
  currency: {
    type: Object,
    default: () => ({ symbol: '$' })
  }
});

// ✅ Convert Object props to refs so they work in <script setup>
const { currency } = toRefs(props);

// ✅ Reactive State
const order = ref({});
const detailsInfo = ref([]);
const trialProduct = ref([]);
const orderNo = ref(null);
const url = base_url;
const isLoading = ref(false);
const showTrial = ref(false);

// ✅ Methods
const userOrderDetails = () => {
  isLoading.value = true;
  
  axios.get(`${url}user/order/${orderNo.value}/details`)
    .then((response) => {
      detailsInfo.value = response.data.order_details || [];
      trialProduct.value = response.data.trial_product || [];
    })
    .catch((error) => {
      console.error("Error fetching order details:", error);
      detailsInfo.value = [];
      trialProduct.value = [];
    })
    .finally(() => {
      isLoading.value = false;
    });
};

// ✅ Event Handler Wrapper
const handleViewDetails = (orderData) => {
  orderNo.value = orderData.id;
  order.value = orderData;
  
  userOrderDetails();
  
  if (typeof $ !== 'undefined') {
    $("#modal-form").modal("show");
  }
};

// ✅ Lifecycle Hooks
onMounted(() => {
  EventBus.$on("view-details", handleViewDetails);
});

onBeforeUnmount(() => {
  EventBus.$off("view-details", handleViewDetails);
});
</script>

<style scoped>
.modal-custom {
  max-width: 70% !important;
}
@media screen and (max-width: 573px) {
  .modal-custom {
    max-width: 100% !important;
    background-color: #000 !important;
  }
}
.color-button {
  border: 1px solid #000;
  padding: 7px;
  cursor: default;
}
</style>