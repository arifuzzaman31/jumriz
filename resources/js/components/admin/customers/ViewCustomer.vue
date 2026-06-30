<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Customer List</h5>
          <div class="ibox-tools">
            <a class="collapse-link">
              <i class="fa fa-chevron-up"></i>
            </a>
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
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
                  @keyup="getCustomer()"
                />
                <span class="input-group-append"></span>
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
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Address</th>
                  <th>Register</th>
                  <th>View Order</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="customer in customers.data" :key="customer.id">
                  <td>{{ customer.name }}</td>
                  <td>{{ customer.email }}</td>
                  <td>{{ customer.phone }}</td>
                  <td>{{ customer.address }}</td>
                  <td>{{ dateToString(customer.created_at) }}</td>
                  <td>
                    <button
                      @click="viewOrder(customer.id, customer.name)"
                      class="btn btn-primary"
                      type="button"
                    >
                      <i class="fa fa-eye" title="View Orders"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="col-md-12 text-center" v-else>
            <img :src="`${base_url}images/loading.gif`" alt="Loading..." />
          </div>
        </div>
      </div>

      <div class="ibox animated fadeInRightBig">
        <!-- ✅ FIXED: Added missing @page-clicked event listener -->
        <pagination
          v-if="customers"
          :pageData="customers"
          @page-clicked="pageClicked"
        />
      </div>

      <div class="ibox">
        <ShowOrder />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { base_url, emitter } from '../../../vue-assets'
import { useCommonActions } from '../../../useCommonActions'
import Pagination from '../pagination/Pagination.vue'
import ShowOrder from './ShowOrder.vue'

// Composables
const { successMessage, dateToString } = useCommonActions()

// --- State ---
const customers = ref([])
const isLoading = ref(false)
const keyword = ref('')

// --- Methods ---
const getCustomer = async (page = 1) => {
  isLoading.value = true
  try {
    const response = await axios.get(
      `${base_url}admin/customer-list?page=${page}&keyword=${keyword.value}`
    )
    customers.value = response.data
  } catch (error) {
    console.error('Error fetching customers:', error)
  } finally {
    isLoading.value = false
  }
}

const pageClicked = (pageNo) => {
  getCustomer(pageNo)
}

const viewOrder = (id, name) => {
  emitter.emit('customer-orders', [id, name])
}

// Kept method updated in case you uncomment the delete button in the template
const deleteCustomer = async (id) => {
  try {
    const result = await Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!',
    })

    if (result.isConfirmed) {
      const res = await axios.get(`${base_url}admin/customer/delete/${id}`)
      successMessage(res.data)
      getCustomer()
    }
  } catch (error) {
    console.error('Error deleting customer:', error)
  }
}

// --- Lifecycle ---
onMounted(() => {
  getCustomer()
  emitter.on('customer-created', getCustomer)
})

onUnmounted(() => {
  emitter.off('customer-created', getCustomer)
})
</script>