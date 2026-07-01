<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Sales List</h5>
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
            <div class="col-sm-3 m-b-xs">
              <multiselect
                v-model="paymethod"
                deselect-label=""
                track-by="id"
                label="provider"
                :searchable="true" 
                open-direction="bottom"
                placeholder="Filter By Provider"
                :options="allmethod"
                @update:model-value="getMethodData"
              ></multiselect>
            </div>

            <!-- Replaced v2-datepicker with native HTML5 date input -->
            <div class="col-sm-3 m-b-xs">
              <label class="text-muted" style="font-size: 12px;">Single Date</label>
              <input 
                type="date" 
                class="form-control" 
                v-model="singleDate" 
                @change="getMethodData"
              />
            </div>

            <!-- Replaced v2-datepicker-range with two native HTML5 date inputs -->
            <div class="col-sm-3 m-b-xs">
              <label class="text-muted" style="font-size: 12px;">Date Range</label>
              <div class="row">
                <div class="col-6 pr-1">
                  <input 
                    type="date" 
                    class="form-control" 
                    v-model="rangeDateStart" 
                    @change="getMethodData"
                  />
                </div>
                <div class="col-6 pl-1">
                  <input 
                    type="date" 
                    class="form-control" 
                    v-model="rangeDateEnd" 
                    @change="getMethodData"
                  />
                </div>
              </div>
            </div>

            <div class="col-sm-2 m-b-xs" style="padding-top: 18px;">
              <button class="btn btn-primary" @click="clearFilter">Clear Filter</button>
            </div>
          </div>

          <div class="row" style="margin-top: 15px;" v-if="!isLoading">
            <div class="table-responsive text-center">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="value in pay_amount.data" :key="value.id">
                    <td>{{ value.payment_date }}</td>
                    <td>{{ value.provider?.provider || 'N/A' }}</td>
                    <td>{{ value.amount }}</td>
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
            <!-- ✅ FIXED: Added @page-clicked event -->
            <pagination 
              v-if="pay_amount" 
              :pageData="pay_amount" 
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
const singleDate = ref('')
const rangeDateStart = ref('')
const rangeDateEnd = ref('')
const paymethod = ref('')
const pay_amount = ref([])
const allmethod = ref([])
const isLoading = ref(false)

// --- Computed Properties (Replaces invalid `this.` in template URLs) ---

// Formats the two dates into the "YYYY-MM-DD,YYYY-MM-DD" string your backend expects
const rangeString = computed(() => {
  if (rangeDateStart.value && rangeDateEnd.value) {
    return `${rangeDateStart.value},${rangeDateEnd.value}`
  }
  return ''
})

const baseQueryString = computed(() => {
  return `methods=${paymethod.value?.id || ''}&range=${rangeString.value}&singledate=${singleDate.value}`
})

const exportExcelUrl = computed(() => `${base_url}admin/export?req=payment&${baseQueryString.value}`)
const exportPdfUrl = computed(() => `${base_url}admin/product-amount-report-pdf?${baseQueryString.value}`)
const exportPrintUrl = computed(() => `${base_url}admin/product-amount-report-print?${baseQueryString.value}`)

// --- Methods ---
const getMethodData = async (page = 1) => {
  isLoading.value = true
  try {
    const response = await axios.get(
      `${base_url}admin/payment-method-wise-amount?page=${page}&${baseQueryString.value}`
    )
    pay_amount.value = response.data
  } catch (error) {
    console.error('Error fetching sales data:', error)
  } finally {
    isLoading.value = false
  }
}

const pageClicked = (pageNo) => {
  getMethodData(pageNo)
}

const getMethod = async () => {
  try {
    const response = await axios.get(`${base_url}admin/get-payment-method`)
    allmethod.value = response.data
  } catch (error) {
    console.error('Error fetching payment methods:', error)
  }
}

const clearFilter = () => {
  singleDate.value = ''
  rangeDateStart.value = ''
  rangeDateEnd.value = ''
  paymethod.value = ''
  getMethodData()
}

// --- Lifecycle ---
onMounted(() => {
  getMethodData()
  getMethod()
})
</script>

<style scoped>
.cut-text {
  text-decoration: line-through 2px red;
}
</style>