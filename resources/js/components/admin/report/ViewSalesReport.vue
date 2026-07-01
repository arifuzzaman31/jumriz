<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Sales List</h5>
          <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench"></i></a>
            <ul class="dropdown-menu dropdown-user">
              <li><a href="#" class="dropdown-item">Clear Filter</a></li>
              <li><a href="#" class="dropdown-item">Config option 2</a></li>
            </ul>
            <a class="close-link"><i class="fa fa-times"></i></a>
          </div>
        </div>
        
        <div class="ibox-content">
          <div class="row">
            <div class="col-sm-3 m-b-xs pb-1">
              <multiselect
                v-model="category"
                deselect-label=""
                track-by="id"
                label="category_name"
                :searchable="true" 
                open-direction="bottom"
                placeholder="Filter By Category"
                :options="categories"
                @update:model-value="getSubCategories"
              ></multiselect>
            </div>

            <div class="col-sm-3 m-b-xs pb-1">
              <multiselect
                v-model="sub_category"
                deselect-label=""
                track-by="id"
                label="sub_category_name"
                :loading="isCategoryLoading"
                :searchable="true" 
                open-direction="bottom"
                placeholder="By Sub Category"
                :options="sub_categories"
                @update:model-value="getSubSubCategories"
              ></multiselect>
            </div>

            <div class="col-sm-3 m-b-xs pb-1">
              <multiselect
                v-model="sub_sub_category"
                deselect-label=""
                track-by="id"
                label="sub_sub_category_name"
                :loading="isSubCategoryLoading"
                :searchable="true" 
                open-direction="bottom"
                placeholder="By Sub Sub Category"
                :options="sub_sub_categories"
                @update:model-value="getBrands"
              ></multiselect>
            </div>

            <div class="col-sm-3 m-b-xs pb-1">
              <multiselect
                v-model="brand"
                deselect-label=""
                track-by="id"
                label="brand_name"
                :searchable="true" 
                open-direction="bottom"
                placeholder="Filter By Brand"
                :options="brands"
                @update:model-value="getProduct"
              ></multiselect>
            </div>

            <div class="col-sm-3 m-b-xs pb-1">
              <label class="text-muted" style="font-size: 12px;">Date Range</label>
              <div class="row">
                <div class="col-6 pr-1">
                  <input type="date" class="form-control" v-model="rangeDateStart" @change="getProduct" />
                </div>
                <div class="col-6 pl-1">
                  <input type="date" class="form-control" v-model="rangeDateEnd" @change="getProduct" />
                </div>
              </div>
            </div>

            <div class="col-sm-3 m-b-xs pb-1">
              <div class="input-group">
                <input 
                  placeholder="Search By Keyword" 
                  type="text" 
                  class="form-control" 
                  v-model="keyword"
                  @keyup="getProduct"
                >
              </div>
            </div>

            <div class="col-sm-2 pb-1" style="padding-top: 20px;">
              <button class="btn btn-primary" @click="clearFilter">Clear Filter</button>
            </div>
          </div>

          <div class="row" style="margin-top: 15px;" v-if="!isLoading">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>P-Code</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Sub Category</th>
                    <th>SubSubCategory</th>
                    <th>Brand</th>
                    <th>Total Sale Qty</th>
                    <th>Total Buying Amount</th>
                    <th>Total Sales Amount</th>
                    <th>Profit</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in products.data" :key="item.id">
                    <td>{{ item.product?.id }}</td>
                    <td>{{ item.product?.product_name }}</td>
                    <td>{{ item.category?.category_name }}</td>
                    <td>{{ item.sub_category?.sub_category_name }}</td>
                    <td>{{ item.sub_sub_category?.sub_sub_category_name }}</td>
                    <td>{{ item.brand?.brand_name }}</td>
                    <td>{{ item.total_sold_qty }}</td>
                    <td>{{ item.total_buying_amount }}</td>
                    <td>{{ item.total_sales_amount }}</td>
                    <td>{{ item.total_sales_amount - item.total_buying_amount }}</td>
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
const keyword = ref('')
const isLoading = ref(false)
const isCategoryLoading = ref(false)
const isSubCategoryLoading = ref(false)

const category = ref('')
const sub_category = ref('')
const sub_sub_category = ref('')
const brand = ref('')

const categories = ref([])
const sub_categories = ref([])
const sub_sub_categories = ref([])
const brands = ref([])
const products = ref([])

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
  return `keyword=${keyword.value}&category=${category.value?.id || ''}&sub_category=${sub_category.value?.id || ''}&sub_sub_category=${sub_sub_category.value?.id || ''}&brand=${brand.value?.id || ''}&range=${rangeString.value}`
})

const exportExcelUrl = computed(() => `${base_url}admin/export?req=sales&${baseQueryString.value}`)
const exportPdfUrl = computed(() => `${base_url}admin/product-sales-report-pdf?${baseQueryString.value}`)
const exportPrintUrl = computed(() => `${base_url}admin/product-sales-report-print?${baseQueryString.value}`)

// --- Methods ---
const getProduct = async (page = 1) => {
  isLoading.value = true
  try {
    const response = await axios.get(`${base_url}admin/product-sale-report?page=${page}&${baseQueryString.value}`)
    products.value = response.data
  } catch (error) {
    console.error('Error fetching products:', error)
  } finally {
    isLoading.value = false
  }
}

const pageClicked = (pageNo) => {
  getProduct(pageNo)
}

const getCategories = async () => {
  try {
    const response = await axios.get(`${base_url}admin/all-categories/yes`)
    categories.value = response.data
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const getSubCategories = () => {
  sub_category.value = ''
  brand.value = ''
  sub_categories.value = []
  brands.value = []
  sub_sub_categories.value = []
  
  getProduct() // Fetch with only category selected
  
  if (category.value?.id) {
    isCategoryLoading.value = true
    axios.get(`${base_url}admin/get-subcategory/${category.value.id}`)
      .then(response => {
        sub_categories.value = response.data
        isCategoryLoading.value = false
      })
      .catch(() => isCategoryLoading.value = false)
  }
}

const getSubSubCategories = () => {
  sub_sub_category.value = ''
  brand.value = ''
  sub_sub_categories.value = []
  brands.value = []
  
  getProduct() // Fetch with category & sub_category selected
  
  if (sub_category.value?.id) {
    isSubCategoryLoading.value = true
    axios.get(`${base_url}admin/get-sub-subcategory/${sub_category.value.id}`)
      .then(response => {
        sub_sub_categories.value = response.data
        isSubCategoryLoading.value = false
      })
      .catch(() => isSubCategoryLoading.value = false)
  }
}

const getBrands = () => {
  brand.value = ''
  brands.value = []
  
  getProduct() // Fetch with category, sub_category & sub_sub_category selected
  
  // ✅ FIXED BUG: Original code checked `sub_category` here instead of `sub_sub_category`
  if (sub_sub_category.value?.id) {
    isSubCategoryLoading.value = true
    axios.get(`${base_url}admin/get-brand/${sub_sub_category.value.id}`)
      .then(response => {
        brands.value = response.data
        isSubCategoryLoading.value = false
      })
      .catch(() => isSubCategoryLoading.value = false)
  }
}

const clearFilter = () => {
  rangeDateStart.value = ''
  rangeDateEnd.value = ''
  category.value = ''
  sub_category.value = ''
  sub_sub_category.value = ''
  brand.value = ''
  keyword.value = ''
  sub_categories.value = []
  sub_sub_categories.value = []
  brands.value = []
  getProduct()
}

// --- Lifecycle ---
onMounted(() => {
  getProduct()
  getCategories()
})
</script>

<style scoped>
.cut-text {
  text-decoration: line-through 2px red;
}
</style>