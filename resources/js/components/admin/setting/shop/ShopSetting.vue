<template>
  <div class="row">
    <div class="col-sm-8 b-r">
      <h3 class="m-t-none m-b">Update Setting</h3>
      <form @submit.prevent="save" role="form">
        <div class="form-group">
          <label>Shop Name *</label>
          <input v-model="form.shop_name" type="text" placeholder="Shop Name" class="form-control" />
        </div>

        <div class="form-group">
          <label>Shop Address *</label>
          <textarea v-model="form.address" placeholder="Address" class="form-control"></textarea>
        </div>

        
        <div class="form-group">
          <label>Phone *</label>
          <input v-model="form.phone" type="text" placeholder="Phone No" class="form-control" />
        </div>

        <div class="form-group">
          <label>Email *</label>
          <input v-model="form.email" type="text" placeholder="Email" class="form-control" />
        </div>

        <div class="form-group">
          <label>Return Policy</label>
          <!-- Vue 3 Quill Editor Replacement -->
          <QuillEditor 
            v-model:content="form.return_policy" 
            contentType="html" 
            theme="snow" 
          />
        </div>

        <div class="form-group">
          <label>
            Theme Color (<span class="text-danger">Remember Your Color Will Combination with white text</span>)
          </label>
          <input type="color" v-model="form.theme_color" class="form-control" style="height: 40px;" />
        </div>

        <div class="form-group">
          <!-- DRY File Inputs -->
          <div class="fileinput fileinput-new" data-provides="fileinput">
            <span class="btn btn-block btn-primary btn-file">
              <span class="fileinput-new"><i class="fa fa-camera"></i> Header Logo</span>
              <span class="fileinput-exists">Header Logo</span>
              <input ref="headerInputRef" type="file" accept="image/*" @change="handleImageChange($event, 'header')" />
            </span>
          </div>

          <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top: 10px;">
            <span class="btn btn-block btn-primary btn-file">
              <span class="fileinput-new"><i class="fa fa-camera"></i> Footer Logo</span>
              <span class="fileinput-exists">Footer Logo</span>
              <input ref="footerInputRef" type="file" accept="image/*" @change="handleImageChange($event, 'footer')" />
            </span>
          </div>

          <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top: 10px;">
            <span class="btn btn-block btn-primary btn-file">
              <span class="fileinput-new"><i class="fa fa-camera"></i> Favicon</span>
              <span class="fileinput-exists">Favicon</span>
              <input ref="faviconInputRef" type="file" accept="image/*" @change="handleImageChange($event, 'favicon')" />
            </span>
          </div>
        </div>

        <div class="form-group">
          <label>Facebook Link</label>
          <input v-model="form.facebook_link" type="text" placeholder="Facebook Link" class="form-control" />
        </div>
        
        <div class="form-group">
          <label>Twitter Link</label>
          <input v-model="form.twitter_link" type="text" placeholder="Twitter Link" class="form-control" />
        </div>

        <div class="form-group">
          <label>Youtube Link</label>
          <input v-model="form.youtube_link" type="text" placeholder="Youtube Link" class="form-control" />
        </div>

        <div class="form-group">
          <label>Footer Text</label>
          <textarea v-model="form.footer_text" class="form-control" placeholder="Footer Text"></textarea>
        </div>

        <div class="form-group">
          <label>Slider Status (<small>you can on or off showing slider on home page by this option</small>)</label>
          <select class="form-control" v-model="form.slider_status">
            <option value="1">Show Slider</option>
            <option value="0">Don't Show Slider</option>
          </select>
        </div>

        <div class="form-group">
          <label>Hot Deal Status (<small>you can on or off showing hot deal on home page by this option</small>)</label>
          <select class="form-control" v-model="form.hot_deal_status">
            <option value="1">Show Hot Deal</option>
            <option value="0">Don't Show Hot Deal</option>
          </select>
        </div>

        <div class="form-group">
          <label>On Sale Status (<small>you can on or off showing on sale section on home page by this option</small>)</label>
          <select class="form-control" v-model="form.onsale_status">
            <option value="1">Show on Sale Section</option>
            <option value="0">Don't Show on Sale Section</option>
          </select>
        </div>

        <div class="form-group">
          <label>Show Side Menubar (<small>you can on or off showing side menu by this option</small>)</label>
          <select class="form-control" v-model="form.sidemenu_status">
            <option value="1">Show Side Menubar</option>
            <option value="0">Don't Show Side Menubar</option>
          </select>
        </div>

        <div style="margin-bottom: 20px">
          <button style="margin-bottom: 20px" class="btn btn-lg btn-primary float-right" type="submit" :disabled="isSubmitting">
            <strong>{{ buttonName }}</strong>
          </button>
        </div>
      </form>
    </div>

    <!-- Preview Section -->
    <div class="col-sm-4">
      <h4>Logo Header Preview</h4>
      <p class="text-center" :style="themeBg">
        <img v-if="imagePreviews.header" class="img-responsive img-fluid" :src="imagePreviews.header" />
        <img v-else class="img-responsive img-fluid" :src="`${url}images/logo/${form.header_logo_view}`" />
      </p>

      <h4>Logo Footer Preview</h4>
      <p class="text-center">
        <img v-if="imagePreviews.footer" class="img-responsive img-fluid" :src="imagePreviews.footer" />
        <img v-else class="img-responsive img-fluid" :src="`${url}images/logo/${form.footer_logo_view}`" />
      </p>

      <h4>Favicon Preview</h4>
      <p class="text-center">
        <img v-if="imagePreviews.favicon" class="img-responsive img-fluid" :src="imagePreviews.favicon" />
        <img v-else class="img-responsive img-fluid" :src="`${url}images/logo/${form.favicon_view}`" />
      </p>
    </div>

    <!-- Validation Errors -->
    <div class="col-md-12" v-if="validationError" style="margin-top: 20px; margin-bottom: 20px">
      <div class="form-group">
        <ul>
          <li class="text-danger" v-for="(error, i) in validationError" :key="i">
            {{ error[0] }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css' // Required CSS
import { emitter, base_url } from '../../../../vue-assets'
import { useCommonActions } from '../../../../useCommonActions'

const { successMessage, validationError: showValidationError } = useCommonActions()

// --- State ---
const url = base_url
const isSubmitting = ref(false)
const buttonName = ref('Update')
const validationError = ref(null)

// Template refs for resetting file inputs
const headerInputRef = ref(null)
const footerInputRef = ref(null)
const faviconInputRef = ref(null)

// Form data (WITHOUT image files)
const form = reactive({
  shop_name: '',
  address: '',
  phone: '',
  email: '',
  return_policy: '',
  footer_text: '',
  header_logo_view: '',
  footer_logo_view: '',
  favicon_view: '',
  facebook_link: '',
  twitter_link: '',
  youtube_link: '',
  theme_color: '#e3106e',
  hot_deal_status: '1',
  slider_status: '1',
  onsale_status: '1',
  sidemenu_status: '1',
})

// Handle actual File objects for upload
const imageFiles = reactive({
  header: null,
  footer: null,
  favicon: null,
})

// Handle Base64 strings ONLY for UI preview
const imagePreviews = reactive({
  header: null,
  footer: null,
  favicon: null,
})

// --- Computed ---
const themeBg = computed(() => ({
  background: form.theme_color,
}))

// --- Methods ---

// ✅ DRY Image Handler: Replaces 6 functions with 1
const handleImageChange = (event, type) => {
  const file = event.target.files[0]
  if (!file) return

  imageFiles[type] = file // Save actual file for FormData

  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreviews[type] = e.target.result // Save base64 ONLY for preview
  }
  reader.readAsDataURL(file)
}

const getSetting = async () => {
  try {
    const response = await axios.get(`${base_url}admin/setting/shop/1/edit`)
    const data = response.data
    
    // Clean mapping of response data to form state
    Object.assign(form, {
      shop_name: data.shop_name || '',
      address: data.address || '',
      phone: data.phone || '',
      email: data.email || '',
      return_policy: data.return_policy || '',
      footer_text: data.footer_text || '',
      header_logo_view: data.logo_header || '',
      footer_logo_view: data.logo_footer || '',
      favicon_view: data.favicon || '',
      facebook_link: data.facebook || '',
      twitter_link: data.twitter || '',
      youtube_link: data.youtube || '',
      theme_color: data.theme_color || '#e3106e',
      hot_deal_status: data.hot_deal_status ?? '1',
      slider_status: data.slider_status ?? '1',
      onsale_status: data.onsale_status ?? '1',
      sidemenu_status: data.sidemenu_status ?? '1',
    })
  } catch (error) {
    console.error('Error fetching settings:', error)
  }
}

const save = async () => {
  buttonName.value = 'Updating...'
  isSubmitting.value = true

  // 1. Build FormData
  const formData = new FormData()
  
  // Append all text fields
  formData.append('shop_name', form.shop_name)
  formData.append('address', form.address)
  formData.append('phone', form.phone)
  formData.append('email', form.email)
  formData.append('return_policy', form.return_policy)
  formData.append('footer_text', form.footer_text)
  formData.append('facebook_link', form.facebook_link)
  formData.append('twitter_link', form.twitter_link)
  formData.append('youtube_link', form.youtube_link)
  formData.append('theme_color', form.theme_color)
  formData.append('slider_status', form.slider_status)
  formData.append('hot_deal_status', form.hot_deal_status)
  formData.append('onsale_status', form.onsale_status)
  formData.append('sidemenu_status', form.sidemenu_status)

  // 2. Append real files if selected
  if (imageFiles.header) formData.append('header_logo', imageFiles.header)
  if (imageFiles.footer) formData.append('footer_logo', imageFiles.footer)
  if (imageFiles.favicon) formData.append('favicon', imageFiles.favicon)

  try {
    const response = await axios.post(`${base_url}admin/setting/shop`, formData)
    
    if (response.data.status === 'success') {
      successMessage(response.data)
      emitter.emit('shop-created')
      validationError.value = null
      
      // Reset file states on success
      Object.assign(imageFiles, { header: null, footer: null, favicon: null })
      Object.assign(imagePreviews, { header: null, footer: null, favicon: null })
      if (headerInputRef.value) headerInputRef.value.value = ''
      if (footerInputRef.value) footerInputRef.value.value = ''
      if (faviconInputRef.value) faviconInputRef.value.value = ''
      
      getSetting() // Refresh to get new image paths from server
    } else {
      successMessage(response.data)
    }
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors
      showValidationError()
    } else {
      successMessage(err)
    }
  } finally {
    buttonName.value = 'Update'
    isSubmitting.value = false
  }
}

// --- Lifecycle ---
onMounted(() => {
  getSetting()
  emitter.on('shop-created', getSetting)
})

onUnmounted(() => {
  emitter.off('shop-created')
})
</script>