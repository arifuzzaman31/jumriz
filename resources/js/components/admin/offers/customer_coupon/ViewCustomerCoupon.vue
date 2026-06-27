<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Customer Coupon List</h5>
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
            <div class="col-sm-5 m-b-xs"></div>
            <div class="col-sm-4 m-b-xs"></div>
            <div class="col-sm-3">
              <div class="input-group">
                <input
                  placeholder="Search By Name"
                  type="text"
                  class="form-control form-control-sm"
                  v-model="keyword"
                  @keyup.enter="getCustomerCoupon()"
                />
                <span class="input-group-append">
                  <!--    <button type="button" class="btn btn-sm btn-primary">Go!
                                  </button> -->
                </span>
              </div>
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
                  <th>SL</th>
                  <th>Coupon</th>
                  <th>User</th>
                  <th>Phone</th>
                  <th>Valid date</th>
                  <th>is_Used</th>
                  <th>is_Applied</th>
                  <th>Created_at</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(value, index) in coupons.data" :key="value.id">
                  <td>{{ index + 1 }}</td>
                  <td>{{ value.coupon_code }}</td>
                  <td>{{ value.user?.name || 'N/A' }}</td>
                  <td>{{ value.user?.phone || 'N/A' }}</td>
                  <td>{{ value.valid_date }}</td>
                  <td>
                    <span v-if="value.is_used == 0">No</span>
                    <span v-else>YES</span>
                  </td>
                  <td>
                    <span v-if="value.is_applied == 0">No</span>
                    <span v-else>YES</span>
                  </td>
                  <td>{{ formatDate(value.created_at) }}</td>
                  <td>
                    <a
                      @click.prevent="deleteCoupon(value.id)"
                      class="btn btn-danger"
                      href="#"
                    >
                      <i class="fa fa-trash" title="Delete"></i>
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Empty state -->
            <div
              v-if="coupons.data && coupons.data.length === 0"
              class="text-center text-muted py-3"
            >
              No coupons found.
            </div>
          </div>

          <div class="col-md-12 text-center" v-else>
            <img :src="`${base_url}/images/loading.gif`" alt="Loading..." />
          </div>
        </div>
      </div>

      <div class="ibox animated fadeInRightBig" v-if="coupons">
        <Pagination :pageData="coupons" @page-clicked="handlePageClick" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import { emitter } from "../../../../vue-assets"; // Update path as needed
import Pagination from "../../pagination/Pagination.vue";

// Reactive state
const coupons = ref([]);
const isLoading = ref(false);
const keyword = ref("");

// Date formatting (replaces Vue 2 filter)
const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Fetch customer coupons
const getCustomerCoupon = async (page = 1) => {
  isLoading.value = true;

  try {
    const response = await axios.get(
      `${base_url}admin/customer-coupon-list?page=${page}&keyword=${keyword.value}`
    );
    coupons.value = response.data;
  } catch (error) {
    console.error("Error fetching coupons:", error);
  } finally {
    isLoading.value = false;
  }
};

// Handle pagination click
const handlePageClick = (pageNo) => {
  getCustomerCoupon(pageNo);
};

// Edit coupon (emit event for parent/sibling component)
const edit = (value) => {
  emitter.emit("update-coupon", value);
};

// Delete coupon with confirmation
const deleteCoupon = async (id) => {
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
      const response = await axios.delete(`${base_url}admin/send-coupon/${id}`);
      
      // Show success message (replace with your notification system)
      Swal.fire({
        icon: "success",
        title: response.data.message || "Deleted successfully!",
        timer: 1500,
        showConfirmButton: false,
      });
      
      getCustomerCoupon();
    }
  } catch (error) {
    console.error("Error deleting coupon:", error);
    
    Swal.fire({
      icon: "error",
      title: "Error!",
      text: "Something went wrong.",
    });
  }
};

// Refresh handler for event bus
const refreshData = () => {
  getCustomerCoupon();
};

// Lifecycle hooks
onMounted(() => {
  getCustomerCoupon();
  emitter.on("customer-coupon-created", refreshData);
});

onBeforeUnmount(() => {
  emitter.off("customer-coupon-created", refreshData);
});
</script>