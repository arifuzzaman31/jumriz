<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-content">
          <div class="table-responsive" style="margin-top: 15px">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>SL</th>
                  <th>Page Name</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="page in pagesData.data" :key="page.id">
                  <td>{{ page.id }}</td>
                  <td>{{ page.title }}</td>
                  <td>
                    <div class="switch">
                      <div class="onoffswitch">
                        <input
                          type="checkbox"
                          :checked="page.publish == 1"
                          class="onoffswitch-checkbox"
                          :id="`page-${page.id}`"
                          @change="changeStatus(page.id)"
                        />
                        <label class="onoffswitch-label" :for="`page-${page.id}`">
                          <span class="onoffswitch-inner"></span>
                          <span class="onoffswitch-switch"></span>
                        </label>
                      </div>
                    </div>
                  </td>
                  <td>
                    <button 
                      @click="showData(page)" 
                      class="btn btn-success" 
                      type="button"
                    >
                      <i class="fa fa-book" title="View"></i>
                    </button>
                    <button 
                      @click="edit(page)" 
                      class="btn btn-primary" 
                      type="button"
                    >
                      <i class="fa fa-edit" title="Edit"></i>
                    </button>
                    <button 
                      @click="deletePage(page.id)" 
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
        </div>
      </div>

      <div class="ibox animated fadeInRightBig">
        <!-- ✅ FIXED: Added @page-clicked event listener -->
        <pagination 
          v-if="pagesData" 
          :pageData="pagesData" 
          @page-clicked="pageClicked" 
        />
      </div>

      <div class="ibox">
        <UpdatePage />
      </div>
      
      <div class="ibox">
        <ShowPage />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { emitter, base_url } from '../../../../vue-assets'
import { useMixin } from '../../../../mixin'
import Pagination from '../../pagination/Pagination.vue'
import UpdatePage from './UpdatePage.vue'
import ShowPage from './ShowPage.vue'

const { successMessage } = useMixin()

// --- State ---
const pagesData = ref([])

// --- Methods ---
const getPage = async (page = 1) => {
  try {
    const response = await axios.get(`${base_url}admin/setting/get-pages?page=${page}`)
    pagesData.value = response.data
  } catch (error) {
    console.error('Error fetching pages:', error)
  }
}

const pageClicked = (pageNo) => {
  getPage(pageNo)
}

const edit = (pageData) => {
  emitter.emit('update-page', pageData)
}

const showData = (pageData) => {
  emitter.emit('show-page', pageData)
}

const changeStatus = async (id) => {
  try {
    const response = await axios.get(`${base_url}admin/setting/change/publishing/status/${id}`)
    successMessage(response.data)
  } catch (error) {
    console.error('Error changing status:', error)
  }
}

const deletePage = async (id) => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!',
  })

  if (result.isConfirmed) {
    try {
      const res = await axios.get(`${base_url}admin/setting/page/${id}/delete`)
      successMessage(res.data)
      getPage() // Refresh list
    } catch (error) {
      console.error('Error deleting page:', error)
    }
  }
}

// --- Lifecycle ---
onMounted(() => {
  getPage()
  emitter.on('page-created', getPage)
})

onUnmounted(() => {
  emitter.off('page-created', getPage)
})
</script>