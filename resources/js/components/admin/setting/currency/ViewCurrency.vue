<template>
    <div class="row">
        <div class="col-lg-12" >
            <div class="ibox animated fadeInRightBig">
                <div class="ibox-title">
                    <h5>Currency List</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="#" class="dropdown-item">Config option 1</a>
                            </li>
                            <li><a href="#" class="dropdown-item">Config option 2</a>
                            </li>
                        </ul>
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
                                 @keyup="getcurrency()"> 
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
                                <th>Country</th>
                                <th>Currency</th>
                                <th>Code</th>
                                <th>Symbol</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(value,index) in currencies.data" :key="index">
                                <td>{{ value.country }}</td>
                                <td>{{ value.currency }}</td>
                                <td>{{ value.code }}</td>
                                <td>{{ value.symbol }}</td>
                                <td>
                                    <a v-if="value.currency_status == 0" @click.prevent="makeCurrent(value.id)" class="btn btn-success" href="#">Set as Your Currency</a>

                                    <a v-else  class="btn btn-primary" href="#">It's Current Currency</a>
                                </td>
                                <td>
                                    <a @click.prevent="edit(value)" class="btn btn-primary" href="#"><i class="fa fa-edit" title="Edit"></i></a> 
                                    <a @click.prevent="deletecurrency(value.id)" class="btn btn-danger" href="#"><i class="fa fa-trash" title="Delete"></i></a>

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
               <pagination v-if="currencies" :pageData="currencies"></pagination> 
            </div>

            <div class="ibox">
                <update-currency></update-currency>
            </div>
        </div>





    </div>
</template>
<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { emitter, base_url } from '../../../../vue-assets';
import { useCommonActions } from '../../../../useCommonActions';
import Pagination from '../../pagination/Pagination.vue';
import UpdateCurrency from './EditCurrency.vue';

const { successMessage } = useCommonActions();

const currencies = ref([]);
const isLoading = ref(false);
const keyword = ref('');
const url = base_url;

const getcurrency = async (page = 1) => {
    isLoading.value = true;
    try {
        const response = await axios.get(base_url + 'admin/setting/currency-list?page=' + page + '&keyword=' + keyword.value);
        currencies.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
};

const pageClicked = (pageNo) => {
    getcurrency(pageNo);
};

const edit = (value) => {
    emitter.emit('update-currency', value);
};

const deletecurrency = (id) => {
    Swal.fire({
        title: 'Are you sure ?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.get(base_url + 'admin/setting/currency/' + id + '/delete')
                .then(res => {
                    successMessage(res.data);
                    getcurrency();
                });
        }
    });
};

const makeCurrent = (id) => {
    Swal.fire({
        title: 'Are you sure ?',
        text: "This will be Set as your application currency!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Set it!'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.get(base_url + 'admin/setting/current/currency/' + id)
                .then(res => {
                    successMessage(res.data);
                    getcurrency();
                });
        }
    });
};

onMounted(() => {
    getcurrency();
    emitter.on('currency-created', () => {
        getcurrency();
    });
});

onUnmounted(() => {
    emitter.off('currency-created');
});
</script>