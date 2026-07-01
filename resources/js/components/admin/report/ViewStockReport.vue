<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Product List</h5>
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
                    <th>Code</th>
                    <th>Product</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Sub Category</th>
                    <th>SubSubCategory</th>
                    <th>OpeningStock</th>
                    <th>CurrentStock</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in products.data" :key="item.id">
                    <td>{{ item.id }}</td>
                    <td>{{ item.product_name }}</td>
                    <td>{{ item.brand?.brand_name || 'N/A' }}</td>
                    <td>{{ item.category?.category_name || 'N/A' }}</td>
                    <td>{{ item.sub_category?.sub_category_name || 'N/A' }}</td>
                    <td>{{ item.sub_sub_category?.sub_sub_category_name || 'N/A' }}</td>
                    <td>{{ item.stock_quantity }}</td>
                    <td>{{ item.current_quantity }}</td>
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
          <div class="col-md-8">
            <!-- ✅ FIXED: Added missing @page-clicked event -->
            <pagination 
              v-if="products.meta" 
              :pageData="products.meta" 
              @page-clicked="pageClicked" 
            />
          </div>
          <div class="col-md-4 text-right">
            <!-- ✅ FIXED: Replaced invalid `this.` template syntax with computed properties -->
            <a 
              title="Excel Sample for Bulk Store" 
              :href="exportSampleExcelUrl" 
              class="btn btn-info btn-sm"
            >
              <i class="fa fa-download" aria-hidden="true"></i> Sample Excel
            </a>

            <a 
              title="Download Excel" 
              :href="exportExcelUrl" 
              class="btn btn-success btn-sm"
            >
              <i class="fa fa-download" aria-hidden="true"></i> Excel
            </a>

            <a 
              title="Download PDF" 
              :href="exportPdfUrl" 
              class="btn btn-primary btn-sm"
            >
              <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
            </a>

            <a 
              title="Print" 
              :href="exportPrintUrl" 
              target="_blank" 
              class="btn btn-primary btn-sm"
            >
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

// --- Computed Properties (Powers the clean Export URLs in template) ---
const baseQueryString = computed(() => {
  return `keyword=${keyword.value}&category=${category.value?.id || ''}&sub_category=${sub_category.value?.id || ''}&sub_sub_category=${sub_sub_category.value?.id || ''}&brand=${brand.value?.id || ''}`
})

const exportSampleExcelUrl = computed(() => `${base_url}admin/export?req=stock&${baseQueryString.value}&bulkstore=yes`)
const exportExcelUrl = computed(() => `${base_url}admin/export?req=stock&${baseQueryString.value}&bulkstore=no`)
const exportPdfUrl = computed(() => `${base_url}admin/product-report-pdf?${baseQueryString.value}`)
const exportPrintUrl = computed(() => `${base_url}admin/product-report-print?${baseQueryString.value}`)

// --- Methods ---
const getProduct = async (page = 1) => {
  isLoading.value = true
  try {
    const response = await axios.get(`${base_url}admin/product-list?page=${page}&${baseQueryString.value}`)
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
  sub_sub_category.value = ''
  brand.value = ''
  sub_categories.value = []
  sub_sub_categories.value = []
  brands.value = []
  
  getProduct() // Refresh table with only category selected
  
  if (category.value?.id) {
    isCategoryLoading.value = true
    axios.get(`${base_url}admin/get-subcategory/${category.value.id}`)
      .then(response => {
        sub_categories.value = response.data
      })
      .catch(error => console.error('Error fetching subcategories:', error))
      .finally(() => isCategoryLoading.value = false)
  }
}

const getSubSubCategories = () => {
  sub_sub_category.value = ''
  brand.value = ''
  sub_sub_categories.value = []
  brands.value = []
  
  getProduct() // Refresh table with category & sub_category selected
  
  if (sub_category.value?.id) {
    isSubCategoryLoading.value = true
    axios.get(`${base_url}admin/get-sub-subcategory/${sub_category.value.id}`)
      .then(response => {
        sub_sub_categories.value = response.data
      })
      .catch(error => console.error('Error fetching sub-subcategories:', error))
      .finally(() => isSubCategoryLoading.value = false)
  }
}

const getBrands = () => {
  brand.value = ''
  brands.value = []
  
  getProduct() // Refresh table with category, sub_category & sub_sub_category selected
  
  // ✅ FIXED BUG: Original code incorrectly checked `sub_category` here instead of `sub_sub_category`
  if (sub_sub_category.value?.id) {
    isSubCategoryLoading.value = true
    axios.get(`${base_url}admin/get-brand/${sub_sub_category.value.id}`)
      .then(response => {
        brands.value = response.data
      })
      .catch(error => console.error('Error fetching brands:', error))
      .finally(() => isSubCategoryLoading.value = false)
  }
}

const clearFilter = () => {
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