<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Campaign List</h5>
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
                  @keyup.enter="getCampaigns"
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
                  <th>Meta Image</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(value, index) in campaigns.data" :key="value.id || index">
                  <td>{{ value.campaign_title }}</td>
                  <td>
                    <!-- ✅ Added optional chaining to prevent crash if image is null -->
                    <img v-if="value.banner" v-lazy="value.banner" style="max-height: 100px;">
                    <span v-else class="text-muted">No image</span>
                  </td>
                  <td>
                    <img v-if="value.meta_image" v-lazy="value.meta_image" style="max-height: 100px;">
                    <span v-else class="text-muted">No image</span>
                  </td>
                  <td>
                    <div class="switch">
                      <div class="onoffswitch">
                        <!-- ✅ Added 'camp-' prefix to ID to prevent duplicate HTML IDs -->
                        <input 
                          @change="campaignStatus(value.id)" 
                          type="checkbox" 
                          :checked="value.status == 1" 
                          class="onoffswitch-checkbox" 
                          :id="'camp-' + value.id"
                        >
                        <label class="onoffswitch-label" :for="'camp-' + value.id">
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
                    <a @click.prevent="deleteCampaign(value.id)" class="btn btn-danger btn-sm" href="#">
                      <i class="fa fa-trash" title="Delete"></i>
                    </a>
                  </td>
                </tr>

                <!-- Empty State -->
                <tr v-if="!campaigns.data?.length">
                  <td colspan="5" class="text-center text-muted py-3">
                    No campaigns found.
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
        <pagination v-if="campaigns.meta" :pageData="campaigns.meta" @paginate="getCampaigns" />
      </div>

      <!-- Edit Modal Component -->
      <div class="ibox">
        <update-campaign />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { EventBus, base_url } from '../../../../vue-assets';
import { useMixin } from '../../../../mixin';
import Pagination from '../../pagination/Pagination.vue';
import UpdateCampaign from './EditCampaign.vue';

// ✅ Reactive State
// Initialized as an object to prevent .data or .meta undefined errors
const campaigns = ref({ data: [], meta: null });
const isLoading = ref(false);
const keyword = ref('');
const url = base_url;

// ✅ Methods
const getCampaigns = (page = 1) => {
  // Handle pagination event object if passed accidentally
  if (typeof page !== 'number' && typeof page !== 'string') {
    page = 1;
  }

  isLoading.value = true;
  
  axios.get(`${base_url}admin/offer-list?page=${page}&keyword=${keyword.value}`)
    .then(response => {
      campaigns.value = response.data;
    })
    .catch(error => {
      console.error('Error fetching campaigns:', error);
      campaigns.value = { data: [], meta: null };
    })
    .finally(() => {
      isLoading.value = false;
    });
};

const edit = (id) => {
  EventBus.$emit('update-campaign', id);
};

const campaignStatus = (id) => {
  axios.get(`${base_url}admin/offer/status/${id}`)
    .then(res => {
      Mixin.methods.successMessage(res.data);
    })
    .catch(error => {
      console.error('Error updating status:', error);
    });
};

const deleteCampaign = (id) => {
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
      axios.get(`${base_url}admin/offer/${id}/delete`)
        .then(res => {
          Mixin.methods.successMessage(res.data);
          getCampaigns();
        })
        .catch(error => {
          console.error('Delete failed:', error);
        });
    }
  }); 
};

// ✅ Event Handler Wrapper
const handleCampaignCreated = () => {
  getCampaigns();
};

// ✅ Lifecycle Hooks
onMounted(() => {
  getCampaigns();
  EventBus.$on('campaign-created', handleCampaignCreated);
});

onBeforeUnmount(() => {
  EventBus.$off('campaign-created', handleCampaignCreated);
});
</script>