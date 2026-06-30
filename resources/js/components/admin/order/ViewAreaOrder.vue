<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Area Order List</h5>
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
          <div class="row">
            <div class="col-sm-3">
              <div class="input-group">
                <input
                  placeholder="Search By Order ID"
                  type="text"
                  class="form-control form-control-sm"
                  v-model="orderId"
                  @keyup.enter="getAreaOrder()"
                />
                <span class="input-group-append"></span>
              </div>
            </div>

            <div class="col-md-2 m-b-xs">
              <div class="input-group">
                <select
                  class="form-control"
                  @change="getAreaOrder()"
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
                  @change="getAreaOrder()"
                  v-model="paymentStatus"
                >
                  <option value="">Payment</option>
                  <option value="1">Paid</option>
                  <option value="0">Unpaid</option>
                </select>
              </div>
            </div>

            <div class="col-sm-1">
              <button class="btn btn-primary" @click="clearFilter">
                Clear
              </button>
            </div>
          </div>

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
                <tr v-for="order in orders.data" :key="order.id">
                  <td>{{ order.id }}</td>
                  <td>{{ formatDate(order.order_date) }}</td>
                  <td>{{ order.user?.name || 'N/A' }}</td>
                  <td>{{ order.shipping_area?.city || 'N/A' }}</td>
                  <td>{{ order.total_item }}</td>
                  <td>
                    {{ currency?.symbol || '' }}{{ order.total_amount }}
                  </td>
                  <td>
                    <span v-if="order.payment_status === 0" class="text-danger">Unpaid</span>
                    <span v-else class="text-success">Paid</span>
                  </td>
                  <td>
                    <span v-if="order.status === 0" class="text-warning">Pending</span>
                    <span v-else-if="order.status === 1" class="text-info">Processing</span>
                    <span v-else-if="order.status === 2" class="text-primary">On delivery</span>
                    <span v-else class="text-success">Delivered</span>
                  </td>
                  <td>
                    <button
                      @click="getDetails(order.id)"
                      class="btn btn-primary btn-sm"
                      type="button"
                    >
                      <i class="fa fa-eye" title="View Details"></i>
                    </button>
                    <button
                      @click="deleteOrder(order.id)"
                      class="btn btn-danger btn-sm"
                      type="button"
                    >
                      <i class="fa fa-trash" title="Delete"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Empty State -->
            <div
              v-if="!orders.data || orders.data.length === 0"
              class="text-center text-muted py-3"
            >
              No orders found for this area.
            </div>
          </div>

          <div class="col-md-12 text-center" v-else>
            <img :src="`${base_url}images/loading.gif`" alt="Loading..." />
          </div>
        </div>
      </div>

      <div class="ibox animated fadeInRightBig">
        <Pagination v-if="orders" :pageData="orders" @page-clicked="pageClicked" />
      </div>

      <div class="ibox">
        <OrderDetails :currency="currency" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue"
import axios from "axios"
import Swal from "sweetalert2"
import { emitter, base_url } from "../../../vue-assets" // Ensured base_url is imported
import Pagination from "../pagination/Pagination.vue"
import OrderDetails from "./OrderDetails.vue"

// --- Props ---
const props = defineProps({
  currency: {
    type: Object,
    default: () => ({ symbol: "" }),
  },
  cityid: {
    type: [String, Number],
    default: "",
  },
})

// --- Reactive State ---
const orders = ref([])
const paymentStatus = ref("")
const status = ref("")
const orderId = ref("")
const isLoading = ref(false)

// --- Methods ---

// Replaces Vue 2 | dateToString filter
const formatDate = (dateString) => {
  if (!dateString) return "N/A"
  return new Date(dateString).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  })
}

const getAreaOrder = async (page = 1) => {
  isLoading.value = true
  try {
    const response = await axios.get(`${base_url}admin/order-list`, {
      params: {
        page: page,
        order_id: orderId.value,
        status: status.value,
        payment: paymentStatus.value,
        city: props.cityid,
      },
    })
    orders.value = response.data
  } catch (error) {
    console.error("Error fetching area orders:", error)
  } finally {
    isLoading.value = false
  }
}

const pageClicked = (pageNo) => {
  getAreaOrder(pageNo)
}

const getDetails = (id) => {
  emitter.emit("order-details", id)
}

const clearFilter = () => {
  orderId.value = ""
  status.value = ""
  paymentStatus.value = ""
  getAreaOrder()
}

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
    })

    if (result.isConfirmed) {
      const res = await axios.get(`${base_url}admin/order/delete/${id}`)
      
      Swal.fire({
        icon: "success",
        title: res.data.message || "Deleted successfully!",
        timer: 1500,
        showConfirmButton: false,
      })
      
      getAreaOrder()
    }
  } catch (error) {
    console.error("Error deleting order:", error)
    Swal.fire("Error!", "Something went wrong.", "error")
  }
}

// --- Lifecycle ---
onMounted(() => {
  getAreaOrder()
  emitter.on("order-created", getAreaOrder)
})

onUnmounted(() => {
  emitter.off("order-created", getAreaOrder)
})
</script>