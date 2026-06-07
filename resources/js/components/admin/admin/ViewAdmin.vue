<template>
<div class="row">
    <div class="col-lg-12" >
        <div class="ibox animated fadeInRightBig">
            <div class="ibox-title">
                <h5>Admin List</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-wrench"></i>
                    </a>
                    <a class="close-link">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-5 m-b-xs">

                    </div>
                    <div class="col-sm-4 m-b-xs">

                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input placeholder="Search By Name" type="text" class="form-control form-control-sm" 
                             v-model="keyword"
                             @keyup="getadmin()"> 
                            <span class="input-group-append">
                      <!--    <button type="button" class="btn btn-sm btn-primary">Go!
                        </button> -->
                    </span></div>

                    </div>
                </div>
                <div class="table-responsive" style="margin-top: 15px;" v-if="!isLoading">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Admin Name</th>
                            <th>Admin Email</th>
                            <th>Role Name</th>
                            <th>Area</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(value,index) in admins.data" :key="index">
                            <td>
                                <img v-if="value.avatar" v-lazy="url + 'images/admin/' + value.avatar" class="rounded-circle" style="max-height: 100px;">
                                <img v-else v-lazy="url + 'images/default_avatar.png'" class="rounded-circle" style="max-height: 100px;">
                            </td>
                            <td>{{ value.name }}</td>
                            <td>{{ value.email }}</td>
                            <td>{{ value.role.role_name }}</td>
                            <td>{{ value.area.city }}</td>
                            <td>
                                <div class="switch">
                                    <div class="onoffswitch">
                                        <input @change="changeStatus(value.id)" type="checkbox" :checked="value.status" class="onoffswitch-checkbox" :id="value.id">
                                        <label class="onoffswitch-label" :for="value.id">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                     </div>
                                 </div>
                             </td>
                             <td>
                                 <a @click.prevent="edit(value.id)" class="btn btn-primary" href="#"><i class="fa fa-edit" title="Edit"></i></a> 
                                 <a @click.prevent="deleteadmin(value.id)" class="btn btn-danger" href="#"><i class="fa fa-trash" title="Delete"></i></a>
                             </td>
                        </tr>

                        </tbody>
                    </table>
                </div>

                <div class="col-md-12 text-center" v-else>
                    <img :src="url+'images/loading.gif'">
                </div>

            </div>
        </div>

        <div class="ibox animated fadeInRightBig">
           <pagination v-if="admins" :pageData="admins" @page-clicked="getadmin"></pagination> 
        </div>

        <div class="ibox">
            <update-admin></update-admin>
        </div>
    </div>

</div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { emitter, base_url } from '../../../vue-assets';
import Pagination from '../pagination/Pagination.vue';
import UpdateAdmin from './EditAdmin.vue';

const admins = ref([]);
const isLoading = ref(false);
const keyword = ref('');
const url = base_url;

const getadmin = async (page = 1) => {
    isLoading.value = true;
    try {
        const response = await axios.get(base_url + 'admin/admin-list?page=' + page + '&keyword=' + keyword.value);
        admins.value = response.data;
    } finally {
        isLoading.value = false;
    }
};

const edit = (id) => {
    emitter.emit('update-admin', id);
};

const deleteadmin = async (id) => {
    const result = await Swal.fire({
        title: 'Are you sure ?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    });

    if (result.isConfirmed) {
        try {
            await axios.get(base_url + 'admin/admin/delete/' + id);
            getadmin();
        } catch (error) {
            console.error(error);
        }
    }
};

const changeStatus = async (id) => {
    try {
        await axios.get(base_url + 'admin/admin/status/' + id);
    } catch (error) {
        console.error(error);
    }
};

onMounted(() => {
    getadmin();
    emitter.on('admin-created', () => {
        getadmin();
    });
});

onUnmounted(() => {
    emitter.off('admin-created');
});

// Expose for pagination component if needed
defineExpose({
    pageClicked(pageNo) {
        getadmin(pageNo);
    }
});
</script>