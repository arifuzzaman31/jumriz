<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox animated fadeInRightBig">
                <div class="ibox-title">
                    <h5>Color List</h5>
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
                    <!-- Filters -->
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input 
                                    placeholder="Search By Name" 
                                    type="text" 
                                    class="form-control form-control-sm" 
                                    v-model="keyword"
                                    @keyup.enter="getColors()" 
                                />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-primary btn-sm" @click="clearFilter()">Clear Filter</button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive" style="margin-top: 15px;" v-if="!isLoading">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Color</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(color, index) in colors.data" :key="color.id || index">
                                    <td>{{ (colors.current_page - 1) * colors.per_page + index + 1 }}</td>
                                    <td>{{ color.name }}</td>
                                    <td>{{ color.color_code }}</td>
                                    <td>
                                        <svg width="60" height="25">
                                            <rect width="60" height="20" :style="'stroke-width:3;stroke:rgb(0,0,0);fill:' + color.color_code" />
                                        </svg>
                                    </td>
                                    <td>
                                        <a @click.prevent="edit(color)" class="btn btn-primary btn-sm me-1" href="#">
                                            <i class="fa fa-edit" title="Edit"></i>
                                        </a> 
                                        <a @click.prevent="deleteColor(color.id)" class="btn btn-danger btn-sm" href="#">
                                            <i class="fa fa-trash" title="Delete"></i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="!colors.data?.length">
                                    <td colspan="5" class="text-center text-muted py-3">
                                        No colors found.
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
                <!-- Changed v-if="colors" to v-if="colors.meta" for better accuracy -->
                <pagination v-if="colors.meta" :pageData="colors.meta" />
            </div>

            <!-- Edit Modal Component -->
            <div class="ibox">
                <update-color />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { EventBus, base_url } from '../../../../vue-assets';
import { useMixin } from '../../../../mixin';
import Pagination from '../../pagination/Pagination.vue';
import UpdateColor from './EditColor.vue';

// ✅ Reactive State
const colors = ref({ data: [], meta: null }); // Initialized as object for .data and .meta
const isLoading = ref(false);
const keyword = ref('');
const url = base_url;

// ✅ Methods
const getColors = (page = 1) => {
    isLoading.value = true;
    
    axios.get(`${base_url}admin/color-list?page=${page}&keyword=${keyword.value}`)
        .then(response => {
            colors.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching colors:', error);
            colors.value = { data: [], meta: null };
        })
        .finally(() => {
            isLoading.value = false;
        });
};

const edit = (color) => {
    // ✅ Emitting the whole color object (matching your old logic)
    EventBus.$emit('update-color', color);
};

const deleteColor = (id) => {
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
            axios.delete(`${base_url}admin/product-color/${id}`)
                .then(res => {
                    Mixin.methods.successMessage(res.data);
                    getColors(); // Refresh list
                })
                .catch(error => {
                    console.error('Delete failed:', error);
                });
        }
    }); 
};

const clearFilter = () => {
    keyword.value = '';
    getColors();
};

// ✅ Event Handler Wrapper
const handleColorCreated = () => {
    getColors();
};

// ✅ Lifecycle Hooks
onMounted(() => {
    getColors();
    EventBus.$on('color-created', handleColorCreated);
});

onBeforeUnmount(() => {
    EventBus.$off('color-created', handleColorCreated);
});
</script>

<style scoped>
/* Keep your existing styles */
.cut-text {
    text-decoration: line-through 2px red;
}
</style>