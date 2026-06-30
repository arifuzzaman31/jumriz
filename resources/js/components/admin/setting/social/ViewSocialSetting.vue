<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Social Credential List</h5>
          <div class="ibox-tools">
            <a class="collapse-link">
              <i class="fa fa-chevron-up"></i>
            </a>
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              <i class="fa fa-wrench"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
              <li><a href="#" class="dropdown-item">Config option 1</a></li>
              <li><a href="#" class="dropdown-item">Config option 2</a></li>
            </ul>
            <a class="close-link">
              <i class="fa fa-times"></i>
            </a>
          </div>
        </div>
        
        <div class="ibox-content">
          <div 
            class="table-responsive" 
            style="margin-top: 15px" 
            v-if="!isLoading"
          >
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Provider</th>
                  <th>App ID/KEY</th>
                  <th>App Secret</th>
                  <th>Callback uri</th>
                  <th>Status</th>
                  <th>Update</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="social in socials" :key="social.id">
                  <td>{{ social.provider }}</td>
                  <td>{{ social.app_id }}</td>
                  <td>{{ social.app_secret }}</td>
                  <td>{{ `${url}login/${social.provider}/callback` }}</td>
                  <td>
                    <span v-if="social.status === 0" class="text-danger">Inactive</span>
                    <span v-else class="text-success">Active</span>
                  </td>
                  <td>
                    <button 
                      @click="edit(social)" 
                      class="btn btn-primary" 
                      type="button"
                    >
                      <i class="fa fa-edit" title="Edit"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="col-md-12 text-center" v-else>
            <img :src="`${url}images/loading.gif`" alt="Loading..." />
          </div>
        </div>
      </div>

      <div class="ibox">
        <UpdateSocial />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { emitter, base_url } from '../../../../vue-assets'
import UpdateSocial from './UpdateSocial.vue'

// --- State ---
const socials = ref([])
const isLoading = ref(false)
const url = base_url

// --- Methods ---
const getSocials = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(`${base_url}admin/setting/social-method-list`)
    socials.value = response.data
  } catch (error) {
    console.error('Error fetching social methods:', error)
  } finally {
    isLoading.value = false
  }
}

const edit = (socialData) => {
  emitter.emit('update-social', socialData)
}

// --- Lifecycle ---
onMounted(() => {
  getSocials()
  emitter.on('social-created', getSocials)
})

onUnmounted(() => {
  emitter.off('social-created', getSocials)
})
</script>