<template>
  <div>
    <div class="row">
      <!-- Facebook Messenger Section -->
      <div class="col-lg-6">
        <div class="ibox">
          <div class="ibox-title">
            <h5>Messenger Chat</h5>
          </div>
          <div class="ibox-content">
            <form @submit.prevent="updateFbPageId">
              <div class="switch">
                <div class="onoffswitch">
                  <input
                    type="checkbox"
                    :checked="fbForm.status == 1"
                    class="onoffswitch-checkbox"
                    id="example1"
                    @change="changeStatus"
                  />
                  <label class="onoffswitch-label" for="example1">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                  </label>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <label for="app_id">Facebook Page ID</label>
                  <input
                    type="text"
                    v-model="fbForm.app_id"
                    class="form-control"
                  /><br />
                  <button 
                    type="submit" 
                    class="btn btn-primary"
                    :disabled="isFbSaving"
                  >
                    <span v-if="isFbSaving">
                      <i class="fa fa-spinner fa-spin"></i> Saving...
                    </span>
                    <span v-else>{{ fbButtonName }}</span>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="ibox">
          <div class="ibox-title">
            <h5>Follow This Instruction for Facebook Chat Setting</h5>
          </div>
          <div class="ibox-content">
            <ul class="docs-ul">
              <li>First login into your facebook page</li>
              <li>
                Go to about section of page and grab the page id put it in place
                of facebook page id of our admin panel
              </li>
              <li>
                Now go to setting of the page and move to 'Advance Messaging'
              </li>
              <li>You will get a whitelisted domain section</li>
              <li>Put your site domain name there</li>
              <li>Click Save. and it's Done !</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Google Analytics Section -->
      <div class="col-lg-6">
        <div class="ibox">
          <div class="ibox-title">
            <h5>Google Analytics</h5>
          </div>
          <div class="ibox-content">
            <form @submit.prevent="updateGoogleAppId">
              <div class="switch">
                <div class="onoffswitch">
                  <input
                    type="checkbox"
                    :checked="googleForm.status == 1"
                    class="onoffswitch-checkbox"
                    id="example2"
                    @change="changeGoogleStatus"
                  />
                  <label class="onoffswitch-label" for="example2">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                  </label>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <label for="google_id">Tracking ID</label>
                  <input
                    type="text"
                    v-model="googleForm.app_id"
                    id="google_id"
                    class="form-control"
                  /><br />
                  <button 
                    type="submit" 
                    class="btn btn-primary"
                    :disabled="isGoogleSaving"
                  >
                    <span v-if="isGoogleSaving">
                      <i class="fa fa-spinner fa-spin"></i> Saving...
                    </span>
                    <span v-else>{{ googleButtonName }}</span>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        
        <div class="ibox">
          <div class="ibox-title">
            <h5>Google Analytics Setting Instruction</h5>
          </div>
          <div class="ibox-content">
            <ul class="docs-ul">
              <li>
                In google analytics u will get a tracking id put it up here in
                tracking id section
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { base_url } from '../../../../vue-assets'
import { useMixin } from '../../../../mixin'

const { successMessage, validationError: showValidationError } = useMixin()

// --- State ---
const isFbSaving = ref(false)
const isGoogleSaving = ref(false)
const fbButtonName = ref('Update')
const googleButtonName = ref('Update')
const validationErrors = ref(null)

const fbForm = reactive({
  app_id: '',
  status: '',
})

const googleForm = reactive({
  app_id: '',
  status: '',
})

// --- Methods ---
const getFbData = async () => {
  try {
    const response = await axios.get(`${base_url}admin/setting/get-fb-page-data`)
    fbForm.app_id = response.data.app_id
    fbForm.status = response.data.status
  } catch (error) {
    console.error('Error fetching FB data:', error)
  }
}

const getGoogleData = async () => {
  try {
    const response = await axios.get(`${base_url}admin/setting/get-google-app-data`)
    googleForm.app_id = response.data.app_id
    googleForm.status = response.data.status
  } catch (error) {
    console.error('Error fetching Google data:', error)
  }
}

const updateFbPageId = async () => {
  isFbSaving.value = true
  fbButtonName.value = 'Saving...'

  try {
    const response = await axios.post(`${base_url}admin/setting/set-fb-page-data`, fbForm)
    successMessage(response.data)
  } catch (error) {
    if (error.response?.status === 422) {
      validationErrors.value = error.response.data.errors
      showValidationError()
    } else {
      successMessage(error)
    }
  } finally {
    isFbSaving.value = false
    fbButtonName.value = 'Update'
  }
}

const updateGoogleAppId = async () => {
  isGoogleSaving.value = true
  googleButtonName.value = 'Saving...'

  try {
    const response = await axios.post(`${base_url}admin/setting/set-google-app-data`, googleForm)
    successMessage(response.data)
  } catch (error) {
    if (error.response?.status === 422) {
      validationErrors.value = error.response.data.errors
      showValidationError()
    } else {
      successMessage(error)
    }
  } finally {
    isGoogleSaving.value = false
    googleButtonName.value = 'Update'
  }
}

const changeStatus = async () => {
  try {
    const response = await axios.post(`${base_url}admin/setting/change-facebook-id-status`)
    successMessage(response.data)
    getFbData()
  } catch (error) {
    successMessage(error)
  }
}

const changeGoogleStatus = async () => {
  try {
    const response = await axios.post(`${base_url}admin/setting/change-google-app-status`)
    successMessage(response.data)
    getGoogleData() // ✅ FIXED: Was previously calling getFbData() by mistake
  } catch (error) {
    successMessage(error)
  }
}

// --- Lifecycle ---
onMounted(() => {
  getFbData()
  getGoogleData()
})
</script>

<style scoped>
.docs-ul li {
  margin-top: 10px;
}
</style>