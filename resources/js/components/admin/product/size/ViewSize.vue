<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox animated fadeInRightBig">
                <div class="ibox-title">
                    <h5>Size List</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="#" class="dropdown-item">clear Filter</a></li>
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
                                <!-- ✅ Changed to @keyup.enter to prevent spamming the server -->
                                <input 
                                    placeholder="Search By Name" 
                                    type="text" 
                                    class="form-control form-control-sm" 
                                    v-model="keyword"
                                    @keyup.enter="getSizes()" 
                                />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-primary btn-sm" @click="clearFilter()">Clear Filter</button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive" style="margin-top: 15px;" v-if="!isLoading">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SL No.</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(size, index) in sizes.data" :key="size.id || index">
                                    <!-- ✅ Fixed pagination indexing -->
                                    <td>{{ (sizes.current_page - 1) * sizes.per_page + index + 1 }}</td>
                                    <td>{{ size.name }}</td>
                                    <!-- ✅ Added optional chaining in case category is null -->
                                    <td>{{ size.category?.category_name }}</td>
                                    <td>
                                        <a @click.prevent="edit(size)" class="btn btn-primary btn-sm me-1" href="#">
                                            <i class="fa fa-edit" title="Edit"></i>
                                        </a> 
                                        <a @click.prevent="deleteSize(size.id)" class="btn btn-danger btn-sm" href="#">
                                            <i class="fa fa-trash" title="Delete"></i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="!sizes.data?.length">
                                    <td colspan="4" class="text-center text-muted py-3">
                                        No sizes found.
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
                <!-- ✅ Fixed pagination binding to use .meta -->
                <pagination v-if="sizes.meta" :pageData="sizes.meta" /> 
            </div>

            <!-- Edit Modal Component -->
            <div class="ibox">
                <update-size :categories="categories" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { EventBus, base_url } from '../../../../vue-assets';
import Mixin from '../../../../mixin';
import Pagination from '../../pagination/Pagination.vue';
import UpdateSize from './EditSize.vue';

// ✅ Props
const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    }
});

// ✅ Reactive State
// Fixed: Initialized as an object instead of an array to prevent .data undefined errors
const sizes = ref({ data: [], meta: null });
const isLoading = ref(false);
const keyword = ref('');
const url = base_url;

// ✅ Methods
const getSizes = (page = 1) => {
    isLoading.value = true;
    
    axios.get(`${base_url}admin/size-list?page=${page}&keyword=${keyword.value}`)
        .then(response => {
            sizes.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching sizes:', error);
            sizes.value = { data: [], meta: null };
        })
        .finally(() => {
            isLoading.value = false;
        });
};

const edit = (size) => {
    EventBus.$emit('update-size', size);
};

const deleteSize = (id) => {
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
            axios.delete(`${base_url}admin/product-size/${id}`)
                .then(res => {
                    Mixin.methods.successMessage(res.data);
                    getSizes();
                })
                .catch(error => {
                    console.error('Delete failed:', error);
                });
        }
    }); 
};

const clearFilter = () => {
    keyword.value = '';
    getSizes();
};

// ✅ Event Handler Wrapper
const handleSizeCreated = () => {
    getSizes();
};

// ✅ Lifecycle Hooks
onMounted(() => {
    getSizes();
    EventBus.$on('size-created', handleSizeCreated);
});

onBeforeUnmount(() => {
    EventBus.$off('size-created', handleSizeCreated);
});
</script>

<style scoped>
.cut-text {
    text-decoration: line-through 2px red;
}
</style>