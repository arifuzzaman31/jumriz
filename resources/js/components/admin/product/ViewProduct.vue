<template>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox animated fadeInRightBig">
                    <div class="ibox-title">
                        <h5>Product List</h5>
                        <div class="ibox-tools">
                            <button @click.prevent="bulkUpload" class="btn btn-info btn-sm" style="margin-top: -5px; margin-right: 5px;"><i class="fa fa-upload"></i> Bulk Upload</button>
                            <button @click.prevent="createProduct" class="btn btn-primary btn-sm" style="margin-top: -5px;"><i class="fa fa-plus"></i> Add Product</button>
                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench"></i></a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="#" class="dropdown-item">clear Filter</a></li>
                                <li><a href="#" class="dropdown-item">Config option 2</a></li>
                            </ul>
                            <a class="close-link"><i class="fa fa-times"></i></a>
                        </div>
                    </div>
                
                    <div class="ibox-content">
                        <!-- Filters -->
                        <div class="row">
                            <div class="col-sm-2 m-b-xs">
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
                                />
                            </div>
                            <div class="col-sm-2 m-b-xs">
                                <multiselect
                                    v-model="subCategory"
                                    deselect-label=""
                                    track-by="id"
                                    label="sub_category_name"
                                    :loading="isCategoryLoading"
                                    :searchable="true" 
                                    open-direction="bottom"
                                    placeholder="By Sub Category"
                                    :options="subCategoriesList"
                                    @update:model-value="getSubSubCategories"
                                />
                            </div>

                            <div class="col-sm-2 m-b-xs">
                                <multiselect
                                    v-model="subSubCategory"
                                    deselect-label=""
                                    track-by="id"
                                    label="sub_sub_category_name"
                                    :loading="isSubCategoryLoading"
                                    :searchable="true" 
                                    open-direction="bottom"
                                    placeholder="By Sub Sub Category"
                                    :options="subSubCategoriesList"
                                    @update:model-value="getBrands"
                                />
                            </div>

                            <div class="col-sm-2 m-b-xs">
                                <multiselect
                                    v-model="brand"
                                    deselect-label=""
                                    track-by="id"
                                    label="brand_name"
                                    :loading="isBrandLoading"
                                    :searchable="true" 
                                    open-direction="bottom"
                                    placeholder="Filter By Brand"
                                    :options="brandsList"
                                    @update:model-value="() => getProducts()"
                                />
                            </div>
                        
                            <div class="col-sm-2 m-b-xs">
                                <div class="input-group">
                                    <input 
                                        placeholder="Search By Keyword" 
                                        type="text" 
                                        class="form-control" 
                                        v-model="keyword"
                                        @keyup.enter="() => getProducts()"
                                    >
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <button class="btn btn-primary btn-sm" @click="clearFilter">Clear Filter</button>
                            </div>
                        </div>

                        <!-- Product Grid -->
                        <div class="row" style="margin-top: 15px;" v-if="!isLoading">
                            <div class="col-md-3" v-for="value in products.data" :key="value.id">
                                <div class="ibox">
                                    <div class="ibox-content product-box">
                                        <div class="product-imitation">
                                            <img class="img-fluid" v-lazy="value.feature_image">
                                        </div>
                                        <div class="product-desc">
                                            <span style="left: 0px !important" class="product-price">
                                                Qty {{ value.current_quantity }}
                                            </span>                                           
                                            <span class="product-price">
                                                Price :  
                                                <span v-if="value.discount_status == 1 && value.discount_amount > 0" class="cut-text">
                                                   {{ currency.symbol }} {{ value.selling_price }} 
                                                </span>
                                                &nbsp; &nbsp;
                                                <span style="font-weight: bold" v-if="value.discount_status == 1">
                                                    {{ currency.symbol }} {{ value.selling_price - value.discount_amount }}
                                               </span>                                           
                                                <span style="font-weight: bold" v-else>
                                                  {{ currency.symbol }} {{ value.selling_price }}
                                                </span>
                                            </span>
                                        
                                            <!-- ✅ Added optional chaining to prevent crashes if relations are null -->
                                            <div>
                                                <small class="text-muted">{{ value.category?.category_name }} -> </small> 
                                                <small class="text-muted">{{ value.sub_category?.sub_category_name }} -></small>
                                                <small class="text-muted">{{ value.sub_sub_category?.sub_sub_category_name }} -></small>
                                                <small class="text-muted">{{ value.brand?.brand_name }} </small>
                                            </div>
                                        
                                            <a href="#" class="product-name"> {{ value.product_name }}</a>

                                            <div class="small m-t-sm">
                                              Status : {{ value.status_text }} 
                                            </div>
                                        
                                            <div>
                                                 <span>Hot Deal 
                                                    <div class="switch">
                                                        <div class="onoffswitch">
                                                            <!-- ✅ Added 'hot-' prefix to ID to prevent duplicate HTML IDs -->
                                                            <input @change="hotDeal(value.id)" type="checkbox" :checked="value.hot_deal == 1" class="onoffswitch-checkbox" :id="'hot-'+value.id">
                                                            <label class="onoffswitch-label" :for="'hot-'+value.id">
                                                                <span class="onoffswitch-inner"></span>
                                                                <span class="onoffswitch-switch"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </span>
                                            </div>
                                        
                                            <!-- ✅ Fixed text-righ typo -->
                                            <div class="m-t text-right">                   
                                                <a @click.prevent="toggleStatus(value.id, value.status)" href="#" class="btn btn-sm btn-outline btn-primary" title="Make Active Or Deactive">
                                                    <span v-if="value.status == 1"><i class="fa fa-check"></i> Deactivate</span>
                                                    <span v-else><i class="fa fa-check"></i> Activate</span>
                                                </a>                                                                                  
                                                <a @click.prevent="getDiscount(value.id)" href="#" class="btn btn-sm btn-outline btn-info"><i class="fa fa-fire"></i> Discount</a>  
                                                <a @click.prevent="edit(value.id)" href="#" title="Edit Product" class="btn btn-sm btn-outline btn-warning"><i class="fa fa-edit"></i></a>                            
                                                <a @click.prevent="deleteProduct(value.id)" title="Delete Product" href="#" class="btn btn-sm btn-outline btn-danger"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div class="col-md-12 text-center text-muted py-5" v-if="!products.data?.length">
                                No products found.
                            </div>

                            <!-- Loading State -->
                            <div class="col-md-12 text-center" v-else>
                                <img :src="url + 'images/loading.gif'" alt="Loading...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="ibox animated fadeInRightBig">
            <pagination v-if="products.meta" :pageData="products.meta" @paginate="getProducts" />
        </div>

        <!-- Modals -->
        <div class="ibox">
            <create-product :categories="categories" :trial_setting="trial_setting" />
        </div>
        <div class="ibox">
            <update-product :categories="categories" :trial_setting="trial_setting" />
        </div>
        <div class="ibox">
            <discount-product :categories="categories" />
        </div>
        <div class="ibox">
            <bulk-storage />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { EventBus, base_url } from '../../../vue-assets';
import Mixin from '../../../mixin';
import Pagination from '../pagination/Pagination.vue';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';

import CreateProduct from './CreateProduct.vue';
import UpdateProduct from './UpdateProduct.vue';
import DiscountProduct from './DiscountProduct.vue';
import BulkStorage from './Bulkstock.vue';

// ✅ Props
const props = defineProps({
    currency: { type: Object, default: () => ({ symbol: '$' }) },
    trial_setting: { type: Object, default: () => ({}) }
});

// ✅ Reactive State
const products = ref({ data: [], meta: null });
const category = ref(null);
const subCategory = ref(null);
const subSubCategory = ref(null);
const brand = ref(null);

const categories = ref([]);
const subCategoriesList = ref([]);
const subSubCategoriesList = ref([]);
const brandsList = ref([]);

const isLoading = ref(false);
const isCategoryLoading = ref(false);
const isSubCategoryLoading = ref(false);
const isBrandLoading = ref(false);

const keyword = ref('');
const url = base_url;

// ✅ Methods
const getProducts = (page = 1) => {
    isLoading.value = true;
    // console.log("page no : "+page)
    // Ensure page is a valid primitive (handles event objects from Vue listeners)
    if (typeof page !== 'number' && typeof page !== 'string') {
        page = 1;
    }

    // ✅ Optional chaining prevents crash if filter is null
    const categoryId = category.value?.id ?? '';
    const subCategoryId = subCategory.value?.id ?? '';
    const subSubCategoryId = subSubCategory.value?.id ?? '';
    const brandId = brand.value?.id ?? '';

    axios.get(`${base_url}admin/product-list?page=${page}&keyword=${keyword.value}&category=${categoryId}&sub_category=${subCategoryId}&sub_sub_category=${subSubCategoryId}&brand=${brandId}`)
        .then(response => {
            products.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching products:', error);
            products.value = { data: [], meta: null };
        })
        .finally(() => {
            isLoading.value = false;
        });
    // console.log(products.value)
};

const getCategories = () => {
    axios.get(`${base_url}admin/all-categories/yes`)
        .then(response => {
            categories.value = response.data;
        });
};

const getSubCategories = () => {
    subCategory.value = null;
    subSubCategory.value = null;
    brand.value = null;
    subCategoriesList.value = [];
    subSubCategoriesList.value = [];
    brandsList.value = [];
    
    // Refresh list immediately AFTER clearing lower levels
    getProducts();

    if (category.value?.id) {
        isCategoryLoading.value = true;
        axios.get(`${base_url}admin/get-subcategory/${category.value.id}`)
            .then(response => { subCategoriesList.value = response.data; })
            .finally(() => { isCategoryLoading.value = false; });
    }
};

const getSubSubCategories = () => {
    subSubCategory.value = null;
    brand.value = null;
    subSubCategoriesList.value = [];
    brandsList.value = [];
    
    // Refresh list immediately AFTER clearing lower levels
    getProducts();

    if (subCategory.value?.id) {
        isSubCategoryLoading.value = true;
        axios.get(`${base_url}admin/get-sub-subcategory/${subCategory.value.id}`)
            .then(response => { subSubCategoriesList.value = response.data; })
            .finally(() => { isSubCategoryLoading.value = false; });
    }
};

const getBrands = () => {
    brand.value = null;
    brandsList.value = [];
    
    // Refresh list immediately AFTER clearing lower levels
    getProducts();

    if (subSubCategory.value?.id) {
        isBrandLoading.value = true;
        axios.get(`${base_url}admin/get-brand/${subSubCategory.value.id}`)
            .then(response => { brandsList.value = response.data; })
            .finally(() => { isBrandLoading.value = false; });
    }
};

const bulkUpload = () => {
    EventBus.$emit('bulk-upload');
};

const createProduct = () => {
    if (typeof $ !== 'undefined') $('#modal-form').modal('show');
};

const edit = (id) => {
    EventBus.$emit('update-product', id);
};

const getDiscount = (id) => {
    EventBus.$emit('discount-product', id);
};

const deleteProduct = (id) => {
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
            axios.get(`${base_url}admin/product/delete/${id}`)
                .then(res => {
                    Mixin.methods.successMessage(res.data);
                    getProducts();
                });
        }
    }); 
};

const toggleStatus = (id, status) => {
    Swal.fire({
        title: 'Are you sure?',
        text: status == 1 ? "The Product Will Be Deactivated" : "The Product Will Be Activated",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes!'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.get(`${base_url}admin/product/deactive/${id}`)
                .then(res => {
                    Mixin.methods.successMessage(res.data);
                    getProducts();
                });
        }
    });
};

const hotDeal = (id) => {
    axios.get(`${base_url}admin/product/hotdeal-status/${id}`)
        .then(response => {
            Mixin.methods.successMessage(response.data);
        });
};

const clearFilter = () => {
    category.value = null;
    subCategory.value = null;
    subSubCategory.value = null;
    brand.value = null;
    keyword.value = '';
    subCategoriesList.value = [];
    subSubCategoriesList.value = [];
    brandsList.value = [];
    getProducts();
};

// ✅ Event Handler Wrapper
const handleProductCreated = () => {
    getProducts();
    // ✅ REMOVED location.reload() - it breaks Vue SPA state!
};

// ✅ Lifecycle Hooks
onMounted(() => {
    getProducts();
    getCategories();
    EventBus.$on('product-created', handleProductCreated);
});

onBeforeUnmount(() => {
    EventBus.$off('product-created', handleProductCreated);
});
</script>

<style scoped>
.cut-text {
    text-decoration: line-through 2px red;
}
</style>
