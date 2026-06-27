<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Slider List</h5>
          <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-wrench"></i></a>
            <ul class="dropdown-menu dropdown-user">
              <li><a href="#" class="dropdown-item">Config option 1</a></li>
              <li><a href="#" class="dropdown-item">Config option 2</a></li>
            </ul>
            <a class="close-link"><i class="fa fa-times"></i></a>
          </div>
        </div>
        
        <div class="ibox-content">
          <div class="row">
            <div class="col-sm-9"></div>
            <div class="col-sm-3">
              <div class="input-group">
                <input 
                  placeholder="Search By Name" 
                  type="text" 
                  class="form-control form-control-sm" 
                  v-model="keyword"
                  @keyup.enter="getSliders"
                >
              </div>
            </div>
          </div>

          <!-- Table -->
          <div class="table-responsive" style="margin-top: 15px;" v-if="!isLoading">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Image</th>
                  <th>Back URL</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(value, index) in sliders.data" :key="value.id || index">
                  <td>{{ value.title }}</td>
                  <td>
                    <img v-lazy="value.banner" style="max-height: 100px; text-align: center;" />
                  </td>
                  <td>{{ value.back_url }}</td>
                  <td>
                    <div class="switch">
                      <div class="onoffswitch">
                        <!-- ✅ Added 'slider-' prefix to ID to prevent duplicate HTML IDs -->
                        <input 
                          @change="sliderStatus(value.id)" 
                          type="checkbox" 
                          :checked="value.status == 1" 
                          class="onoffswitch-checkbox" 
                          :id="'slider-' + value.id"
                        >
                        <label class="onoffswitch-label" :for="'slider-' + value.id">
                          <span class="onoffswitch-inner"></span>
                          <span class="onoffswitch-switch"></span>
                        </label>
                      </div>
                    </div>
                  </td>
                  <td>
                    <a @click.prevent="edit(value.id)" class="btn btn-primary btn-sm me-1" href="#">
                      <i class="fa fa-edit" title="Edit"></i>
                    </a> 
                    <a @click.prevent="deleteSlider(value.id)" class="btn btn-danger btn-sm" href="#">
                      <i class="fa fa-trash" title="Delete"></i>
                    </a>
                  </td>
                </tr>

                <!-- Empty State -->
                <tr v-if="!sliders.data?.length">
                  <td colspan="6" class="text-center text-muted py-3">
                    No sliders found.
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
        <pagination v-if="sliders.meta" :pageData="sliders.meta" @paginate="getSliders" />
      </div>

      <!-- Edit Modal Component -->
      <div class="ibox">
        <update-slider />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { EventBus, base_url } from '../../../../vue-assets';
import { useMixin } from '../../../../mixin';
import Pagination from '../../pagination/Pagination.vue';
import UpdateSlider from './EditSlider.vue';

// ✅ Reactive State
// Initialized as an object to prevent .data or .meta undefined errors on first load
const sliders = ref({ data: [], meta: null });
const isLoading = ref(false);
const keyword = ref('');
const url = base_url;

// ✅ Methods
const getSliders = (page = 1) => {
  // Handle pagination event object if passed accidentally
  if (typeof page !== 'number' && typeof page !== 'string') {
    page = 1;
  }

  isLoading.value = true;
  
  axios.get(`${base_url}admin/slider-list?page=${page}&keyword=${keyword.value}`)
    .then(response => {
      sliders.value = response.data;
    })
    .catch(error => {
      console.error('Error fetching sliders:', error);
      sliders.value = { data: [], meta: null };
    })
    .finally(() => {
      isLoading.value = false;
    });
};

const edit = (id) => {
  EventBus.$emit('update-slider', id);
};

const sliderStatus = (id) => {
  axios.get(`${base_url}admin/slider-status/${id}`)
    .then(res => {
      Mixin.methods.successMessage(res.data);
    })
    .catch(error => {
      console.error('Error updating status:', error);
    });
};

const deleteSlider = (id) => {
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
      axios.delete(`${base_url}admin/slider/${id}`)
        .then(res => {
          Mixin.methods.successMessage(res.data);
          getSliders();
        })
        .catch(error => {
          console.error('Delete failed:', error);
        });
    }
  }); 
};

// ✅ Event Handler Wrapper
const handleSliderCreated = () => {
  getSliders();
};

// ✅ Lifecycle Hooks
onMounted(() => {
  getSliders();
  EventBus.$on('slider-created', handleSliderCreated);
});

onBeforeUnmount(() => {
  EventBus.$off('slider-created', handleSliderCreated);
});
</script>