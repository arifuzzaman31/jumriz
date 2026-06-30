<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>City Name</h5>
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
          <div class="row">
            <div class="col-sm-3">
              <div class="input-group">
                <input
                  placeholder="Search By City"
                  v-model="keyword"
                  type="text"
                  class="form-control form-control-sm"
                  @keyup="getOrderArea()"
                />
              </div>
            </div>
            
            <div class="col-md-3 m-b-xs">
              <div class="input-group">
                <select
                  class="form-control"
                  v-model="status"
                  @change="getOrderArea()"
                >
                  <option value="">All</option>
                  <option value="0">Inactive</option>
                  <option value="1">Active</option>
                </select>
              </div>
            </div>

            <div class="col-sm-5 m-b-xs">
              <button @click="clearFilter" class="btn btn-primary">
                Clear Filter
              </button>
            </div>
          </div>

          <div
            class="table-responsive"
            style="margin-top: 15px"
            v-if="!isLoading"
          >
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>SL no</th>
                  <th>Area</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="area in areas.data" :key="area.id">
                  <td>{{ area.id }}</td>
                  <td>{{ area.city }}</td>
                  <td>
                    <div class="switch">
                      <div class="onoffswitch">
                        <input
                          type="checkbox"
                          :checked="area.status == 1"
                          class="onoffswitch-checkbox"
                          :id="`area-${area.id}`"
                          @change="changeStatus(area.id)"
                        />
                        <label class="onoffswitch-label" :for="`area-${area.id}`">
                          <span class="onoffswitch-inner"></span>
                          <span class="onoffswitch-switch"></span>
                        </label>
                      </div>
                    </div>
                  </td>
                  <td>
                    <button
                      @click="edit(area)"
                      class="btn btn-primary"
                      type="button"
                    >
                      <i class="fa fa-edit" title="Edit"></i>
                    </button>
                    <button
                      @click="deleteArea(area)"
                      class="btn btn-danger"
                      type="button"
                    >
                      <i class="fa fa-trash" title="Delete"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="col-md-12 text-center" v-else>
            <img :src="`${base_url}images/loading.gif`" alt="Loading..." />
          </div>
        </div>
      </div>

      <div class="ibox animated fadeInRightBig">
        <!-- ✅ FIXED: Added @page-clicked event listener -->
        <pagination
          v-if="areas"
          :pageData="areas"
          @page-clicked="pageClicked"
        />
      </div>
      
      <UpdateCity />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { emitter, base_url } from '../../../../vue-assets'
import { useMixin } from '../../../../mixin'
import Pagination from '../../pagination/Pagination.vue'
import UpdateCity from './EditCity.vue'

const { successMessage } = useMixin()

// --- State ---
const areas = ref([])
const isLoading = ref(false)
const status = ref('')
const keyword = ref('')

// --- Methods ---
const getOrderArea = async (page = 1) => {
  isLoading.value = true
  try {
    const response = await axios.get(
      `${base_url}admin/order-area-list?page=${page}&keyword=${keyword.value}&status=${status.value}`
    )
    areas.value = response.data
  } catch (error) {
    console.error('Error fetching areas:', error)
  } finally {
    isLoading.value = false
  }
}

const pageClicked = (pageNo) => {
  getOrderArea(pageNo)
}

const changeStatus = async (id) => {
  try {
    const response = await axios.get(`${base_url}admin/change/shipping/status/${id}`)
    if (response.data.status === 'success') {
      successMessage(response.data)
    }
  } catch (error) {
    console.error('Error changing status:', error)
  }
}

const edit = (areaData) => {
  emitter.emit('edit-area', areaData)
}

const deleteArea = async (areaData) => {
  try {
    const result = await Swal.fire({
      title: `Delete ${areaData.city}, sure?`,
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!',
    })

    if (result.isConfirmed) {
      const res = await axios.get(`${base_url}admin/area/delete/${areaData.id}`)
      successMessage(res.data)
      getOrderArea()
    }
  } catch (error) {
    console.error('Error deleting area:', error)
  }
}

const clearFilter = () => {
  keyword.value = ''
  status.value = ''
  getOrderArea()
}

// --- Lifecycle ---
onMounted(() => {
  getOrderArea()
  emitter.on('order-area', getOrderArea)
})

onUnmounted(() => {
  emitter.off('order-area', getOrderArea)
})
</script>