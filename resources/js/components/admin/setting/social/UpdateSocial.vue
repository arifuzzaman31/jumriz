<template>
  <div ref="modalRef" id="update-social" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header text-right">
          <button class="btn btn-default" data-dismiss="modal">X</button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-8 mr-auto ml-auto">
              <h3 class="m-t-none m-b">Update {{ social.provider }} Login Credential</h3>
              <form @submit.prevent="save" role="form">
                <div class="form-group">
                  <label>Provider Name *</label>
                  <input
                    v-model="social.provider"
                    type="text"
                    placeholder="Provider Name"
                    disabled
                    class="form-control"
                  />
                </div>

                <div class="form-group">
                  <label>Client Id / app_id *</label>
                  <input
                    v-model="social.app_id"
                    type="text"
                    placeholder="App Key"
                    class="form-control"
                  />
                </div>

                <div class="form-group">
                  <label>Secret *</label>
                  <input
                    v-model="social.app_secret"
                    type="text"
                    placeholder="App Secret"
                    class="form-control"
                  />
                </div>

                <div class="form-group">
                  <label>Status *</label>
                  <select class="form-control" v-model="social.status">
                    <option value="0">Inactive</option>
                    <option value="1">Active</option>
                  </select>
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

            <div
              v-if="validationErrors"
              class="col-md-8 mr-auto ml-auto"
              style="margin-top: 20px"
            >
              <div class="form-group">
                <ul>
                  <li
                    v-for="(error, index) in validationErrors"
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
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { emitter, base_url } from '../../../../vue-assets'
import { useMixin } from '../../../../mixin'

const { successMessage, validationError: showValidationError } = useMixin()

// --- State ---
const modalRef = ref(null)
const isSubmitting = ref(false)
const buttonName = ref('Update')
const validationErrors = ref(null)

const social = reactive({
  id: '',
  provider: '',
  app_id: '',
  app_secret: '',
  status: 1,
})

// --- Methods ---
const closeModal = () => {
  // jQuery for Bootstrap 3/4
  $(modalRef.value).modal('hide')
  
  // Uncomment below if using Bootstrap 5
  // const modal = bootstrap.Modal.getInstance(modalRef.value)
  // modal?.hide()
}

const resetForm = () => {
  Object.assign(social, {
    id: '',
    provider: '',
    app_id: '',
    app_secret: '',
    status: 1,
  })
  validationErrors.value = null
}

const openModal = (value) => {
  Object.assign(social, value)
  // jQuery for Bootstrap 3/4
  $(modalRef.value).modal('show')
  
  // Uncomment below if using Bootstrap 5
  // new bootstrap.Modal(modalRef.value).show()
}

const save = async () => {
  buttonName.value = 'Updating...'
  isSubmitting.value = true

  try {
    const response = await axios.post(
      `${base_url}admin/setting/social-creadential/update/${social.id}`,
      social
    )

    if (response.data.status === 'success') {
      closeModal()
      resetForm()
      successMessage(response.data)
      emitter.emit('social-created')
    } else {
      successMessage(response.data)
    }
  } catch (err) {
    if (err.response?.status === 422) {
      validationErrors.value = err.response.data.errors
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
  emitter.on('update-social', openModal)
})

onUnmounted(() => {
  emitter.off('update-social', openModal)
})
</script>