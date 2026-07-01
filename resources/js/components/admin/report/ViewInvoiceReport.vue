<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Invoice List</h5>
          <div class="ibox-tools">
            <a class="collapse-link">
              <i class="fa fa-chevron-up"></i>
            </a>
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              <i class="fa fa-wrench"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
              <li><a href="#" class="dropdown-item">Clear Filter</a></li>
              <li><a href="#" class="dropdown-item">Config option 2</a></li>
            </ul>
            <a class="close-link">
              <i class="fa fa-times"></i>
            </a>
          </div>
        </div>
        
        <div class="ibox-content">
          <div class="row">
            <div class="col-sm-4 m-b-xs">
              <multiselect
                v-model="city"
                deselect-label=""
                track-by="id"
                label="city"
                :searchable="true" 
                open-direction="bottom"
                placeholder="Filter By City"
                :options="cities"
                @update:model-value="getProduct"
              ></multiselect>
            </div>

            <!-- Replaced v2-datepicker-range with native HTML5 date inputs -->
            <div class="col-sm-3 m-b-xs">
              <label class="text-muted" style="font-size: 12px;">Date Range</label>
              <div class="row">
                <div class="col-6 pr-1">
                  <input 
                    type="date" 
                    class="form-control" 
                    v-model="rangeDateStart" 
                    @change="getProduct"
                  />
                </div>
                <div class="col-6 pl-1">
                  <input 
                    type="date" 
                    class="form-control" 
                    v-model="rangeDateEnd" 
                    @change="getProduct"
                  />
                </div>
              </div>
            </div>

            <div class="col-sm-1" style="padding-top: 18px;">
              <button class="btn btn-primary" @click="clearFilter">Clear Filter</button>
            </div>
          </div>

          <div class="row" style="margin-top: 15px;" v-if="!isLoading">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>OrderID</th>
                    <th>Order Date</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Payment</th>
                    <th>Delivery</th>
                    <th>Item Qty</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="order in products.data" :key="order.id">
                    <td>{{ order.id }}</td>
                    <td>{{ order.order_date }}</td>
                    <td>{{ order.customer_name }}</td>
                    <td>{{ order.phone }}</td>
                    <td>
                      <span v-if="order.payment_status === 1" class="text-success">Paid</span>
                      <span v-else class="text-danger">Unpaid</span>
                    </td>
                    <td>
                      <!-- ✅ FIXED: Clean v-if/v-else-if chain -->
                      <span v-if="order.status === 0" class="text-warning">Pending</span>
                      <span v-else-if="order.status === 1" class="text-info">On Process</span>
                      <span v-else-if="order.status === 2" class="text-primary">On Delivery</span>
                      <span v-else class="text-success">Delivered</span>
                    </td>
                    <td>{{ order.total_item }}</td>
                    <td>{{ order.total_amount - order.coupon_discount }}</td>
                  </tr>
                </tbody>
              </table>
            </div>   
          </div>

          <div class="col-md-12 text-center" v-else>
            <img :src="`${base_url}images/loading.gif`" alt="Loading...">
          </div>
        </div>
      </div>

      <div class="ibox animated fadeInRightBig">
        <div class="row">
          <div class="col-md-9">
            <!-- ✅ FIXED: Added missing @page-clicked event -->
            <pagination 
              v-if="products" 
              :pageData="products" 
              @page-clicked="pageClicked" 
            />
          </div>
          <div class="col-md-3 text-right">
            <!-- ✅ FIXED: Replaced invalid `this.` template syntax with computed properties -->
            <a :href="exportExcelUrl" class="btn btn-success btn-sm">
              <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
            </a>
            <a :href="exportPdfUrl" class="btn btn-primary btn-sm">
              <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
            </a>
            <a :href="exportPrintUrl" target="_blank" class="btn btn-primary btn-sm">
              <i class="fa fa-print" aria-hidden="true"></i> Print
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { base_url } from '../../../vue-assets'
import Pagination from '../pagination/Pagination.vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

// --- State ---
const rangeDateStart = ref('')
const rangeDateEnd = ref('')
const city = ref('')
const cities = ref([])
const products = ref([])
const isLoading = ref(false)

// --- Computed Properties ---
// Formats the two dates into the "YYYY-MM-DD,YYYY-MM-DD" string your backend expects
const rangeString = computed(() => {
  if (rangeDateStart.value && rangeDateEnd.value) {
    return `${rangeDateStart.value},${rangeDateEnd.value}`
  }
  return ''
})

// Reusable query string for API calls and Export links
const baseQueryString = computed(() => {
  return `range=${rangeString.value}&city=${city.value?.id || ''}`
})

const exportExcelUrl = computed(() => `${base_url}admin/export?req=invoice&${baseQueryString.value}`)
const exportPdfUrl = computed(() => `${base_url}admin/product-invoice-report-pdf?${baseQueryString.value}`)
const exportPrintUrl = computed(() => `${base_url}admin/product-invoice-report-print?${baseQueryString.value}`)

// --- Methods ---
const getProduct = async (page = 1) => {
  isLoading.value = true
  try {
    const response = await axios.get(
      `${base_url}admin/product-invoice-report?page=${page}&${baseQueryString.value}`
    )
    products.value = response.data
  } catch (error) {
    console.error('Error fetching invoice data:', error)
  } finally {
    isLoading.value = false
  }
}

const pageClicked = (pageNo) => {
  getProduct(pageNo)
}

const getCity = async () => {
  try {
    const response = await axios.get(`${base_url}admin/all-cities`)
    cities.value = response.data
  } catch (error) {
    console.error('Error fetching cities:', error)
  }
}

const clearFilter = () => {
  rangeDateStart.value = ''
  rangeDateEnd.value = ''
  city.value = ''
  getProduct()
}

// --- Lifecycle ---
onMounted(() => {
  getProduct()
  getCity()
})
</script>

<style scoped>
.cut-text {
  text-decoration: line-through 2px red;
}
</style>