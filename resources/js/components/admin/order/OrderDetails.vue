<template>
    <div id="order_details" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="m-t-none m-b">Order #{{ order.id }}</h3>
                    <button type="button" class="btn btn-default text-right" data-dismiss="modal">X</button>
                </div>
                <div class="modal-body">
                    <!-- Status Controls -->
                    <div class="row">
                        <div class="col-md-3 offset-6">
                            <div class="form-group">
                                <select
                                    class="form-control"
                                    v-model="order.payment_status"
                                    @change="changePaymentStatus(order.id)"
                                >
                                    <option value="1">Paid</option>
                                    <option value="0">Unpaid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select
                                    class="form-control"
                                    v-model="order.status"
                                    @change="changeProcessStatus(order.id)"
                                >
                                    <option value="0">Pending</option>
                                    <option value="1">On Process</option>
                                    <option value="2">On Delivery</option>
                                    <option value="3">Delivered</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Customer & Order Info -->
                    <div class="wrapper wrapper-content animated fadeInRight">
                        <div class="row">
                            <div class="col-sm-6">
                                <h5>From:</h5>
                                <address>
                                    <strong>{{ address.shop_name }}</strong><br />
                                    {{ address.address }}<br />
                                    <abbr title="Phone">P:</abbr> {{ address.phone }}
                                </address>
                            </div>

                            <div class="col-sm-6">
                                <div class="float-right">
                                    <h4>
                                        Order No. <span class="text-navy">{{ order.id }}</span>
                                    </h4>
                                    <span>To:</span>
                                    <address>
                                        <strong>{{ order.customer_name }}</strong><br />
                                        {{ order.address }}<br />
                                        <abbr title="Phone">P:</abbr> {{ order.phone }}
                                    </address>
                                    <p>
                                        <span><strong>Order Date: </strong>{{ formatDate(order?.order_date) }}</span>
                                        <br />
                                        <span v-if="order?.customer_delivery_date">
                                            <strong>Expected Delivery Slot : </strong>
                                            {{ formatDate(order?.customer_delivery_date) }} ({{
                                                order.customer_delivery_time
                                            }})</span>
                                    </p>
                                    <p v-if="order.payment_status == 1">
                                        <strong>Paid By:</strong>
                                        <span v-if="order.payment_method == 1">Cash</span>
                                        <span v-else-if="order.payment_method == 2">Paypal</span>
                                        <span v-else-if="order.payment_method == 3">Stripe</span>
                                        <span v-else-if="order.payment_method == 4">{{ order.card_type }}</span>
                                        <span v-else-if="order.payment_method == 5">Razorpay</span>
                                        <span v-else>Cash On Delivery</span>
                                    </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Order Table -->
            <div class="table-responsive mt-5">
                <table class="table invoice-table table-bordered">
                    <thead>
                        <tr>
                            <th v-if="order.status != 3 && order.order_details.length > 1" style="width: 10%">
                                Delete
                            </th>
                            <th>Item Id</th>
                            <th>Image</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="value in order.order_details" :key="value.id">
                            <td v-if="order.status != 3 && order.order_details.length > 1">
                                <a
                                    class="btn btn-sm btn-danger"
                                    @click.prevent="deleteSingleItem(value.id)"
                                    href="#"
                                >
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                            <td>{{ value.product_id }}</td>
                            <td>
                                <img
                                    :src="`${url}images/product/feature/${value.product?.product_image}`"
                                    alt="Product Image"
                                    height="50"
                                    width="50"
                                />
                            </td>
                            <td>
                                {{ value.product?.product_name }}
                                {{ value.product?.quantity_unit }}
                                <br v-if="value.color || value.size" />
                                <span v-if="value.color">
                                    ,Color:
                                    <button
                                        class="color-button"
                                        :style="{ 'background-color': value.color.color_code }"
                                        :title="value.color.name"
                                    ></button>
                                </span>
                                <span v-if="value.size">,Size: {{ value.size.name }}</span>
                            </td>
                            <td>
                                <button
                                    v-if="order.status != 3 && value.quantity > 1"
                                    class="btn btn-sm btn-danger mr-2"
                                    @click.prevent="updateQty('decrement', value.id)"
                                >
                                    -
                                </button>
                                {{ value.quantity }}
                                <button
                                    v-if="order.status != 3"
                                    class="btn btn-sm btn-success ml-2"
                                    @click.prevent="updateQty('increment', value.id)"
                                >
                                    +
                                </button>
                            </td>
                            <td>
                                {{ currency.symbol }}{{ formatPrice(value.selling_price) }}
                                <span
                                    v-if="value.unit_discount > 0"
                                    class="discount-price"
                                >
                                    {{ currency.symbol
                                    }}{{ formatPrice(Number(value.selling_price) + Number(value.unit_discount)) }}
                                </span>
                            </td>
                            <td>
                                {{ currency.symbol
                                }}{{ formatPrice(value.quantity * value.selling_price) }}
                            </td>
                        </tr>

                        <!-- Trial item header -->
                        <tr
                            v-if="order.trial_product?.length > 0"
                            class="bg-dark text-white"
                        >
                            <td colspan="7" style="text-align: center; font-size: 20px;">
                                Trial Items
                            </td>
                        </tr>

                        <!-- Trial Item Rows -->
                        <tr v-for="value in order.trial_product" :key="'trial-' + value.id">
                            <td v-if="order.status != 3 && order.order_details.length > 1">
                                <a
                                    class="btn btn-sm btn-danger"
                                    @click.prevent="deleteTrialItem(value.id)"
                                    href="#"
                                >
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                            <td>{{ value.product_id }}</td>
                            <td>
                                <img
                                    :src="`${url}images/product/feature/${value.product?.product_image}`"
                                    alt="Product Image"
                                    height="50"
                                    width="50"
                                />
                            </td>
                            <td>
                                {{ value.product?.product_name }}
                                {{ value.product?.quantity_unit }}
                                <br v-if="value.color || value.size" />
                                <span v-if="value.color">
                                    , Color:
                                    <button
                                        class="color-button"
                                        :style="{ 'background-color': value.color.color_code }"
                                        :title="value.color.name"
                                    ></button>
                                </span>
                                <span v-if="value.size">, Size: {{ value.size.name }}</span>
                                <br />
                            </td>
                            <td>{{ value.quantity }}</td>
                            <td>
                                {{ currency.symbol }}{{ formatPrice(value.selling_price) }}
                                <span
                                    v-if="value.unit_discount > 0"
                                    class="discount-price"
                                >
                                    {{ currency.symbol
                                    }}{{ formatPrice(Number(value.selling_price) + Number(value.unit_discount)) }}
                                </span>
                            </td>
                            <td>
                                <button
                                    v-if="order.status != 3 && value.invoiced == 0"
                                    class="btn btn-success"
                                    @click="addToInvoice(value.id)"
                                >
                                    Add To Invoice
                                </button>
                                <span class="badge badge-info" v-if="value.invoiced == 1">
                                    Added To Invoice
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h5 v-if="order.note" class="text-bold text-black">
                    Customer Note: {{ order.note }}
                </h5>
            </div>

            <!-- Send Email Section -->
            <div class="row mt-5">
                <div class="col-md-6">
                    <form @submit.prevent="sendmail" role="form">
                        <div class="form-group">
                            <label>
                                Additional Text
                                <small>(if u don't provide text just invoice will be sended)</small>
                            </label>
                            <textarea
                                v-model="form.message"
                                class="form-control"
                                rows="4"
                                placeholder="Write some text..."
                            ></textarea>
                        </div>
                        <div class="form-group">
                            <button
                                type="submit"
                                class="btn btn-primary btn-block"
                                :disabled="isSendingMail"
                            >
                                <span v-if="isSendingMail">
                                    <i class="fa fa-spinner fa-spin"></i> Sending...
                                </span>
                                <span v-else>{{ buttonName }}</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Invoice Totals -->
                <div class="col-md-6">
                    <table class="table invoice-total">
                        <tbody>
                            <tr>
                                <td><strong>Sub Total :</strong></td>
                                <td class="text-right">
                                    {{ currency.symbol }}{{ formatPrice(order.total_amount) }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Shipping :</strong></td>
                                <td class="text-right">
                                    {{ currency.symbol }}{{ formatPrice(order.shipping_amount) }}
                                </td>
                            </tr>
                            <tr v-if="order.coupon_discount > 0">
                                <td><strong>Total :</strong></td>
                                <td class="text-right">
                                    {{ currency.symbol }}{{ formatPrice(order.coupon_discount) }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Grand Total :</strong></td>
                                <td class="text-right">
                                    {{ currency.symbol
                                    }}{{
                                        formatPrice(
                                            Number(order.shipping_amount) +
                                            Number(order.total_amount) -
                                            Number(order.coupon_discount || 0)
                                        )
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-right">
                <a 
                    :href="`${base_url}admin/order/pdf/${order.id}`" 
                    class="btn btn-primary"
                >
                    <i class="fa fa-file-pdf-o"></i> PDF
                </a>
                <a
                    :href="`${base_url}admin/order/print/${order.id}`"
                    target="_blank"
                    class="btn btn-primary"
                >
                    <i class="fa fa-print"></i> Print
                </a>
                <button
                    class="btn btn-danger"
                    @click.prevent="deleteOrder(order.id)"
                >
                    <i class="fa fa-trash"></i> Delete
                </button>
            </div>
            </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { base_url, EventBus } from "../../../vue-assets";

// ✅ Props
const props = defineProps({
  currency: {
    type: Object,
    default: () => ({ symbol: "" })
  }
});

// ✅ Reactive State
const form = reactive({
  message: "",
});
const order = ref({
  order_details: [],
  trial_product: [],
});
const address = ref({});
const buttonName = ref("Send Email with Invoice");
const isSendingMail = ref(false);

let modalInstance = null;

// ✅ Helper Functions (Replaces Laravel Blade filters)
const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const formatPrice = (amount) => {
  if (isNaN(amount)) return "0.00";
  return Number(amount).toFixed(2);
};

// ✅ Modal Handling (Pure Bootstrap 5 API)
const openModal = async (id) => {
  await orderDetails(id);
  shopAddress();
  
  const modalEl = document.getElementById("order_details");
  if (modalEl) {
    modalInstance = new bootstrap.Modal(modalEl);
    modalInstance.show();
  }
};

const closeModal = () => {
  if (modalInstance) {
    modalInstance.hide();
  }
};

// ✅ API Calls
const orderDetails = async (id) => {
  try {
    const response = await axios.get(`${base_url}admin/order/${id}`);
    order.value = response.data;
  } catch (error) {
    console.error("Error fetching order details:", error);
  }
};

const shopAddress = async () => {
  try {
    const response = await axios.get(`${base_url}admin/shop/address`);
    address.value = response.data;
  } catch (error) {
    console.error("Error fetching shop address:", error);
  }
};

// ✅ Action Handlers
const changePaymentStatus = async (id) => {
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "Change the Payment Status!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, Change it!",
  });

  if (result.isConfirmed) {
    try {
      const response = await axios.get(`${base_url}admin/order/payment-status/${id}`);
      Swal.fire("Success!", response.data.message || "Status updated.", "success");
      await orderDetails(id);
      EventBus.$emit("order-created");
    } catch (error) {
      Swal.fire("Error!", "Something went wrong.", "error");
    }
  } else {
    await orderDetails(id); // Revert selection if cancelled
  }
};

const changeProcessStatus = async (id) => {
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "Process Status will change!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, Change it!",
  });

  if (result.isConfirmed) {
    try {
      const statusValue = order.value.status;
      const response = await axios.get(`${base_url}admin/order/${statusValue}/process-status/${id}`);
      Swal.fire("Success!", response.data.message || "Status updated.", "success");
      await orderDetails(id);
      EventBus.$emit("order-created");
    } catch (error) {
      Swal.fire("Error!", "Something went wrong.", "error");
    } 
  }
  else {
    await orderDetails(id);
}
};

const deleteSingleItem = async (id) => {
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "This item will be deleted from invoice!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  });

  if (result.isConfirmed) {
    try {
      const res = await axios.get(`${base_url}admin/order/delete/single-item/${id}`);
      if (res.data.status === "success") {
        Swal.fire("Deleted!", res.data.message, "success");
        await orderDetails(order.value.id);
      } else {
        Swal.fire("Info", res.data.message, "info");
      }
    } catch (error) {
      Swal.fire("Error!", "Something went wrong.", "error");
    }
  }
};

const deleteTrialItem = async (id) => {
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "This item will be deleted from trial!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  });

  if (result.isConfirmed) {
    try {
      const res = await axios.get(`${base_url}admin/order/delete/trial-item/${id}`);
      if (res.data.status === "success") {
        Swal.fire("Deleted!", res.data.message, "success");
        await orderDetails(order.value.id);
      } else {
        Swal.fire("Info", res.data.message, "info");
      }
    } catch (error) {
      Swal.fire("Error!", "Something went wrong.", "error");
    }
  }
};

const updateQty = async (action_type, id) => {
  const text = action_type === "increment"
    ? "The Item Will Incremented by 1!"
    : "The Item Will be decremented by 1!";

  const result = await Swal.fire({
    title: "Are you sure?",
    text: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: `Yes, ${action_type} it!`,
  });

  if (result.isConfirmed) {
    try {
      const res = await axios.post(`${base_url}admin/order/item-increment/${id}`, {
        type: action_type,
      });
      if (res.data.status === "success") {
        Swal.fire("Updated!", res.data.message, "success");
        await orderDetails(order.value.id);
      } else {
        Swal.fire("Info", res.data.message, "info");
      }
    } catch (error) {
      Swal.fire("Error!", "Something went wrong.", "error");
    }
  }
};

const sendmail = async () => {
  isSendingMail.value = true;
  try {
    const response = await axios.post(`${base_url}admin/send-mail-invoice-to-customer`, {
      order_id: order.value.id,
      message: form.message,
    });

    if (response.data.status === "success") {
      form.message = "";
      Swal.fire("Sent!", response.data.message || "Email sent successfully.", "success");
    } else {
      Swal.fire("Info", response.data.message || "Could not send.", "info");
    }
  } catch (error) {
    Swal.fire("Error!", "Something went wrong.", "error");
  } finally {
    isSendingMail.value = false;
  }
};

const deleteOrder = async (id) => {
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "The Whole Order Will Be Deleted!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  });

  if (result.isConfirmed) {
    try {
      const res = await axios.get(`${base_url}admin/order/delete/${id}`);
      Swal.fire("Deleted!", res.data.message || "Order deleted.", "success");
      closeModal(); // Call the local closeModal function
      EventBus.$emit("order-created"); // Refresh the list
    } catch (error) {
      Swal.fire("Error!", "Something went wrong.", "error");
    }
  }
};

// ✅ Lifecycle Hooks
onMounted(() => {
  EventBus.$on("order-details", openModal);
});

onBeforeUnmount(() => {
  EventBus.$off("order-details", openModal);
  if (modalInstance) {
    modalInstance.dispose();
  }
});
</script>

<style scoped>
.modal-custom {
  max-width: 90% !important;
}
.discount-price {
  text-decoration: line-through;
  color: red;
  margin-left: 5px;
}
.color-button {
  border: 1px solid #000;
  padding: 7px;
}
</style>