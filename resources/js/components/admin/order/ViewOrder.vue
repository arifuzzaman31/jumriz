<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Order List</h5>
          <div class="ibox-tools">
            <a class="collapse-link">
              <i class="fa fa-chevron-up"></i>
            </a>
            <a class="dropdown-toggle" data-bs-toggle="dropdown" href="#">
              <i class="fa fa-wrench"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
              <li><a href="#" class="dropdown-item">Config option 1</a></li>
              <li><a href="#" class="dropdown-item">Config option 2</a></li>
            </ul>
            <a class="close-link">
              <i class="fa fa-times"></i>
            </a>
          </div>
        </div>
        
        <div class="ibox-content">
          <!-- Filters -->
          <div class="row">
            <div class="col-sm-3">
              <div class="input-group">
                <input
                  placeholder="Search By Order ID"
                  type="text"
                  class="form-control form-control-sm"
                  v-model="orderId"
                  @keyup.enter="getOrder()"
                />
                <span class="input-group-append"></span>
              </div>
            </div>

            <div class="col-md-2 m-b-xs">
              <div class="input-group">
                <select
                  class="form-control"
                  @change="getOrder()"
                  v-model="status"
                >
                  <option value="">All Order</option>
                  <option value="0">Pending</option>
                  <option value="1">On Process</option>
                  <option value="2">On Delivery</option>
                  <option value="3">Delivered</option>
                </select>
              </div>
            </div>

            <div class="col-sm-2 m-b-xs">
              <div class="input-group">
                <select
                  class="form-control"
                  @change="getOrder()"
                  v-model="paymentStatus"
                >
                  <option value="">Payment</option>
                  <option value="1">Paid</option>
                  <option value="0">Unpaid</option>
                </select>
              </div>
            </div>

            <div class="col-sm-3 m-b-xs">
              <Multiselect
                v-model="city"
                deselect-label=""
                track-by="id"
                label="city"
                :searchable="true"
                open-direction="bottom"
                placeholder="Filter By City"
                :options="cities"
                @update:model-value="getOrder()"
                :disabled="false"
              />
            </div>

            <div class="col-sm-1">
              <button class="btn btn-primary" @click="clearFilter">
                Clear
              </button>
            </div>
          </div>

          <!-- Table -->
          <div
            class="table-responsive"
            style="margin-top: 15px"
            v-if="!isLoading"
          >
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>OrderID</th>
                  <th>Date</th>
                  <th>Customer</th>
                  <th>City</th>
                  <th>Total Item</th>
                  <th>Amount</th>
                  <th>Payment</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="value in orders.data" :key="value.id">
                  <td>{{ value.id }}</td>
                  <td>{{ formatDate(value.order_date) }}</td>
                  <td>{{ value.user?.name || 'N/A' }}</td>
                  <td>{{ value.shipping_area?.city || 'N/A' }}</td>
                  <td>{{ value.total_item }}</td>
                  <td>
                    {{ currency?.symbol || '' }}{{ value.total_amount - (value.coupon_discount || 0) }}
                  </td>
                  <td>
                    <span v-if="value.payment_status == 0" class="text-danger">Unpaid</span>
                    <span v-else class="text-success">Paid</span>
                  </td>
                  <td>
                    <span v-if="value.status == 0" class="text-warning">Pending</span>
                    <span v-else-if="value.status == 1" class="text-info">Processing</span>
                    <span v-else-if="value.status == 2" class="text-primary">On delivery</span>
                    <span v-else class="text-success">Delivered</span>
                  </td>
                  <td>
                    <a
                      @click.prevent="getDetails(value.id)"
                      class="btn btn-primary btn-sm"
                      href="#"
                    >
                      <i class="fa fa-eye" title="View Details"></i>
                    </a>
                    <a
                      @click.prevent="deleteOrder(value.id)"
                      class="btn btn-danger btn-sm"
                      href="#"
                    >
                      <i class="fa fa-trash" title="Delete"></i>
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Empty State -->
            <div
              v-if="orders.data && orders.data.length === 0"
              class="text-center text-muted py-3"
            >
              No orders found.
            </div>
          </div>

          <!-- Loading Spinner -->
          <div class="col-md-12 text-center" v-else>
            <img :src="`${base_url}/images/loading.gif`" alt="Loading..." />
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="ibox animated fadeInRightBig">
        <Pagination v-if="orders" :pageData="orders" @page-clicked="pageClicked" />
      </div>

      <!-- Order Details Modal -->
      <div class="ibox">
        <OrderDetails :currency="currency" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import { base_url, EventBus } from "../../../vue-assets"; // ✅ import base_url and EventBus
import Pagination from "../pagination/Pagination.vue";
import Multiselect from "vue-multiselect";
import OrderDetails from "./OrderDetails.vue";

// Props
const props = defineProps({
  currency: {
    type: Object,
    default: () => ({ symbol: "" }),
  },
  shop_info: {
    type: Object,
    default: () => ({}),
  },
});

// Reactive State
const orders = ref([]);
const cities = ref([]);
const city = ref(null);
const status = ref("");
const paymentStatus = ref("");
const orderId = ref("");
const isLoading = ref(false);

// Date formatter (replaces Vue 2 filter)
const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Fetch orders with filters
const getOrder = async (page = 1) => {
  isLoading.value = true;

  try {
    const response = await axios.get(`${base_url}admin/order-list`, {
      params: {
        page: page,
        order_id: orderId.value,
        status: status.value,
        payment: paymentStatus.value,
        city: city.value?.id || "",
      },
    });
    orders.value = response.data;
  } catch (error) {
    console.error("Error fetching orders:", error);
  } finally {
    isLoading.value = false;
  }
};

// Fetch cities for dropdown
const getCity = async () => {
  try {
    const response = await axios.get(`${base_url}admin/all-cities`);
    cities.value = response.data;
  } catch (error) {
    console.error("Error fetching cities:", error);
  }
};

// Pagination handler
const pageClicked = (pageNo) => {
  getOrder(pageNo);
};

// Emit event to view order details
const getDetails = (id) => {
  EventBus.$emit("order-details", id);
};

// Clear all filters
const clearFilter = () => {
  orderId.value = "";
  status.value = "";
  paymentStatus.value = "";
  city.value = null;
  getOrder();
};

// Delete order with confirmation
const deleteOrder = async (id) => {
  try {
    const result = await Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
    });

    if (result.isConfirmed) {
      const res = await axios.get(`${base_url}admin/order/delete/${id}`);
      
      Swal.fire({
        icon: "success",
        title: res.data.message || "Deleted successfully!",
        timer: 1500,
        showConfirmButton: false,
      });
      
      getOrder();
    }
  } catch (error) {
    console.error("Error deleting order:", error);
    Swal.fire("Error!", "Something went wrong.", "error");
  }
};

// Refresh data when an order is created/updated
const refreshData = () => {
  getOrder();
};

// Lifecycle hooks
onMounted(() => {
  getOrder();
  getCity();
  EventBus.$on("order-created", refreshData);
});

onBeforeUnmount(() => {
  EventBus.$off("order-created", refreshData);
});
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>