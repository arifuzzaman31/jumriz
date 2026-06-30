<template>
  <div ref="modalRef" id="modal-updatepage" class="modal fade">
    <div class="modal-dialog modal-custom">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="m-t-none m-b">Edit Page</h3>
          <button class="btn btn-default text-right" data-dismiss="modal">
            X
          </button>
        </div>

        <div v-if="validationError" class="col-md-8" style="margin-top: 20px">
          <div class="form-group">
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

        <div class="modal-body">
          <form @submit.prevent="update">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="title">Title</label>
                  <input
                    type="text"
                    name="title"
                    id="title"
                    v-model="page.title"
                    class="form-control"
                  />
                </div>

                <div class="form-group">
                  <label>Page Description</label>
                  <!-- Vue 3 Quill Editor Replacement -->
                  <QuillEditor
                    v-model:content="page.description"
                    contentType="html"
                    theme="snow"
                  />
                </div>
        
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="isSubmitting"
                >
                  <span v-if="isSubmitting">
                    <i class="fa fa-spinner fa-spin"></i> Saving...
                  </span>
                  <span v-else>{{ buttonName }}</span>
                </button>
                
                <button
                  type="button"
                  class="btn btn-default"
                  data-dismiss="modal"
                >
                  Close
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { emitter, base_url } from '../../../../vue-assets'
import { useMixin } from '../../../../mixin'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

const { successMessage, validationError: showValidationError } = useMixin()

// --- State ---
const modalRef = ref(null)
const isSubmitting = ref(false)
const buttonName = ref('Update')
const validationError = ref(null)

const page = reactive({
  id: '',
  title: '',
  description: '',
})

// --- Methods ---
const openModal = (value) => {
  Object.assign(page, value)
  if (modalRef.value) {
    $(modalRef.value).modal('show')
  }
}

const closeModal = () => {
  if (modalRef.value) {
    $(modalRef.value).modal('hide')
  }
}

const resetForm = () => {
  Object.assign(page, {
    id: '',
    title: '',
    description: '',
  })
  validationError.value = null
}

const update = async () => {
  isSubmitting.value = true
  buttonName.value = 'Saving...'

  try {
    const response = await axios.post(
      `${base_url}admin/setting/page/update/${page.id}`,
      page
    )

    closeModal()
    resetForm()
    successMessage(response.data)
    emitter.emit('page-created')
  } catch (error) {
    if (error.response?.status === 422) {
      validationError.value = error.response.data.errors
      showValidationError()
    } else {
      successMessage(error)
    }
  } finally {
    isSubmitting.value = false
    buttonName.value = 'Update'
  }
}

// --- Lifecycle ---
onMounted(() => {
  emitter.on('update-page', openModal)
})

onUnmounted(() => {
  emitter.off('update-page', openModal)
})
</script>

<style scoped>
.modal-custom {
  max-width: 90% !important;
}

@media screen and (max-width: 573px) {
  .modal-custom {
    max-width: 100% !important;
    background-color: #000 !important;
  }
}
</style>