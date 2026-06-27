<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Coupon List</h5>
          <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench"></i></a>
            <ul class="dropdown-menu dropdown-user">
              <li><a href="#" class="dropdown-item">Config option 1</a></li>
              <li><a href="#" class="dropdown-item">Config option 2</a></li>
            </ul>
            <a class="close-link"><i class="fa fa-times"></i></a>
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
                  @keyup.enter="getCoupons"
                > 
              </div>
            </div>
          </div>

          <!-- Table -->
          <div class="table-responsive" style="margin-top: 15px;" v-if="!isLoading">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Coupon</th>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Amount Limit</th>
                  <th>Valid date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(value, index) in coupons.data" :key="value.id || index">
                  <td>{{ value.coupon_code }}</td>
                  <td>
                    <span v-if="value.amount_type == 1">Amount</span>
                    <span v-else>%</span>
                  </td>
                  <td>{{ value.amount }}</td>
                  <td>{{ value.max_amount_limit }}</td>
                  <td>{{ value.valid_date }}</td>
                  <td>
                    <a @click.prevent="edit(value)" class="btn btn-primary btn-sm me-1" href="#">
                      <i class="fa fa-edit" title="Edit"></i>
                    </a> 
                    <a @click.prevent="deleteCoupon(value.id)" class="btn btn-danger btn-sm" href="#">
                      <i class="fa fa-trash" title="Delete"></i>
                    </a>
                  </td>
                </tr>

                <!-- Empty State -->
                <tr v-if="!coupons.data?.length">
                  <td colspan="6" class="text-center text-muted py-3">
                    No coupons found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Loading State -->
          <div class="col-md-12 text-center" v-else>
            <img :src="url + 'images/loading.gif'" alt="Loading...">
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="ibox animated fadeInRightBig">
        <pagination v-if="coupons.meta" :pageData="coupons.meta" @paginate="getCoupons" />
      </div>

      <!-- Edit Modal Component -->
      <div class="ibox">
        <update-coupon />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { EventBus, base_url } from '../../../../vue-assets';
import { useMixin } from '../../../../mixin';
import Pagination from '../../pagination/Pagination.vue';
import UpdateCoupon from './EditCoupon.vue';

// ✅ Reactive State
// Initialized as an object to prevent .data or .meta undefined errors
const coupons = ref({ data: [], meta: null });
const isLoading = ref(false);
const keyword = ref('');
const url = base_url;

// ✅ Methods
const getCoupons = (page = 1) => {
  // Handle pagination event object if passed accidentally
  if (typeof page !== 'number' && typeof page !== 'string') {
    page = 1;
  }

  isLoading.value = true;
  
  axios.get(`${base_url}admin/coupon-list?page=${page}&keyword=${keyword.value}`)
    .then(response => {
      coupons.value = response.data;
    })
    .catch(error => {
      console.error('Error fetching coupons:', error);
      coupons.value = { data: [], meta: null };
    })
    .finally(() => {
      isLoading.value = false;
    });
};

const edit = (value) => {
  EventBus.$emit('update-coupon', value);
};

const deleteCoupon = (id) => {
  // ✅ Fixed SweetAlert 2 Syntax
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      axios.delete(`${base_url}admin/coupon/${id}`)
        .then(res => {
          Mixin.methods.successMessage(res.data);
          getCoupons();
        })
        .catch(error => {
          console.error('Delete failed:', error);
        });
    }
  }); 
};

// ✅ Event Handler Wrapper
const handleCouponCreated = () => {
  getCoupons();
};

// ✅ Lifecycle Hooks
onMounted(() => {
  getCoupons();
  EventBus.$on('coupon-created', handleCouponCreated);
});

onBeforeUnmount(() => {
  EventBus.$off('coupon-created', handleCouponCreated);
});
</script>