<template>
  <div class="row">
    <div class="col-sm-8 b-r">
      <h3 class="m-t-none m-b">Update Setting</h3>
      <form @submit.prevent="save" role="form">
        <div class="form-group">
          <label>Og Title*</label>
          <input
            v-model="form.title"
            type="text"
            placeholder="og : Title"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label>Sitemap Link</label>
          <input
            v-model="form.sitemap_link"
            type="text"
            placeholder="Sitemap Link"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label>Author</label>
          <input
            v-model="form.author"
            type="text"
            placeholder="Author"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label>Keyword</label>
          <multiselect
            v-model="form.seo_keyword"
            tag-placeholder="Add this as new tag"
            placeholder="Search or add a tag"
            label="keyword"
            track-by="id"
            :options="tags"
            :multiple="true"
            :taggable="true"
            @tag="addTag"
          ></multiselect>
        </div>

        <div class="form-group">
          <label>Meta Image</label> <br />
          <div class="fileinput fileinput-new" data-provides="fileinput">
            <span class="btn btn-block btn-primary btn-file">
              <span class="fileinput-new"><i class="fa fa-camera"></i> Change Image</span>
              <span class="fileinput-exists">Change Image</span>
              <!-- Added ref for easy resetting -->
              <input
                ref="fileInputRef"
                type="file"
                name="meta_image"
                accept="image/*"
                @change="onImageChange"
              />
            </span>
          </div>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea class="form-control" v-model="form.description"></textarea>
        </div>

        <div class="form-group">
          <label>Google Map</label>
          <textarea
            rows="4"
            class="form-control"
            v-model="form.google_map"
          ></textarea>
        </div>

        <div style="margin-bottom: 20px">
          <button
            class="btn btn-lg btn-primary float-right"
            type="submit"
            :disabled="isSubmitting"
          >
            <strong>{{ buttonName }}</strong>
          </button>
        </div>
      </form>
    </div>

    <div class="col-sm-4">
      <h4>Seo Image Preview</h4>
      <!-- Show local preview if a new file is selected -->
      <p class="text-center" v-if="imagePreview">
        <img class="img-responsive img-fluid" :src="imagePreview" />
      </p>
      <!-- Otherwise show the image from the server -->
      <p class="text-center" v-else>
        <img
          class="img-responsive img-fluid"
          :src="`${url}images/setting/seo/${form.view_image}`"
        />
      </p>
    </div>

    <div
      class="col-md-12"
      v-if="validationError"
      style="margin-top: 20px; margin-bottom: 10px"
    >
      <div class="form-group">
        <div>
          <ul>
            <li
              v-for="(error, index) in validationError"
              :key="index"
              class="text-danger"
            >
              {{ error[0] }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css' // Ensure CSS is imported
import { emitter, base_url } from '../../../../vue-assets'
import { useCommonActions } from '../../../../useCommonActions'

const { successMessage, validationError: showValidationError } = useCommonActions()

// --- State ---
const form = ref({
  title: '',
  view_image: '',
  sitemap_link: '',
  author: '',
  description: '',
  google_map: '',
  seo_keyword: [],
})

const isSubmitting = ref(false)
const buttonName = ref('Update')
const url = base_url
const validationError = ref(null)
const tags = ref([])

// File upload specific states
const fileInputRef = ref(null)
const imageFile = ref(null) // Holds the actual File object
const imagePreview = ref(null) // Holds base64 ONLY for visual preview

// --- Methods ---

const onImageChange = (e) => {
  const files = e.target.files || e.dataTransfer.files
  if (!files.length) return
  
  imageFile.value = files[0] // Save actual file
  createImage(files[0])     // Generate preview
}

const createImage = (file) => {
  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreview.value = e.target.result // Used only for <img src="...">
  }
  reader.readAsDataURL(file)
}

const addTag = (newTag) => {
  const tag = {
    keyword: newTag,
    id: newTag.substring(0, 2) + Math.floor(Math.random() * 10000000),
  }
  tags.value.push(tag)
  form.value.seo_keyword.push(tag)
}

const getSetting = async () => {
  try {
    const response = await axios.get(`${base_url}admin/setting/seo/5/edit`)
    
    const data = response.data
    form.value = {
      title: data.title,
      view_image: data.meta_image,
      sitemap_link: data.sitemap_link,
      author: data.author,
      description: data.description,
      google_map: data.google_map,
      seo_keyword: data.seo_keyword || [],
    }
    tags.value = data.seo_keyword || []
  } catch (error) {
    console.error('Error fetching settings:', error)
  }
}

const save = async () => {
  buttonName.value = 'Updating...'
  isSubmitting.value = true

  // 1. Create FormData object
  const formData = new FormData()
  formData.append('title', form.value.title)
  formData.append('sitemap_link', form.value.sitemap_link)
  formData.append('author', form.value.author)
  formData.append('description', form.value.description)
  formData.append('google_map', form.value.google_map)

  // 2. Append array/object fields properly for Laravel
  if (form.value.seo_keyword && form.value.seo_keyword.length > 0) {
    form.value.seo_keyword.forEach((keyword, index) => {
      formData.append(`seo_keyword[${index}][id]`, keyword.id)
      formData.append(`seo_keyword[${index}][keyword]`, keyword.keyword)
    })
  } else {
    formData.append('seo_keyword', '') // Clear keywords if empty
  }

  // 3. Append the actual File object (if one was selected)
  if (imageFile.value) {
    formData.append('meta_image', imageFile.value)
  }

  try {
    const response = await axios.post(`${base_url}admin/setting/seo`, formData, {
      // DO NOT set 'Content-Type': 'multipart/form-data' manually here!
      // Axios and the browser will automatically set the correct boundary.
    })

    if (response.data.status === 'success') {
      successMessage(response.data)
      emitter.emit('seo-created')
      buttonName.value = 'Update'
      validationError.value = null
      
      // Optional: Reset file input and preview on success
      imageFile.value = null
      imagePreview.value = null
      if (fileInputRef.value) {
        fileInputRef.value.value = ''
      }
      
      // Refresh to get new view_image path from server
      getSetting() 
    } else {
      successMessage(response.data)
      buttonName.value = 'Update'
    }
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors
      showValidationError()
    } else {
      successMessage(err)
    }
    buttonName.value = 'Update'
  } finally {
    isSubmitting.value = false
  }
}

// --- Lifecycle ---
onMounted(() => {
  getSetting()
  emitter.on('seo-created', getSetting)
})

onUnmounted(() => {
  emitter.off('seo-created')
})
</script>