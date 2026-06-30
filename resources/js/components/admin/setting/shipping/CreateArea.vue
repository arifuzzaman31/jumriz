<template>
  <div ref="modalRef" id="modal-form" class="modal fade" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header text-right">
          <button class="btn btn-default" data-dismiss="modal">X</button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12">
              <h3 class="m-t-none m-b">Add City</h3>
              <form @submit.prevent="save" role="form">
                <div class="form-group">
                  <label>City Name *</label>
                  <input
                    v-model="area.name"
                    type="text"
                    placeholder="Area Name"
                    class="form-control"
                  />
                </div>

                <div class="form-group">
                  <label>Status *</label>
                  <div class="switch">
                    <div class="onoffswitch">
                      <!-- ✅ FIXED: Replaced conflicting checked="true" with proper true-value/false-value -->
                      <input
                        type="checkbox"
                        v-model="area.status"
                        :true-value="1"
                        :false-value="0"
                        class="onoffswitch-checkbox"
                        id="cityname"
                      />
                      <label class="onoffswitch-label" for="cityname">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                      </label>
                    </div>
                  </div>
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
              v-if="validationError"
              class="col-md-12"
              style="margin-top: 20px"
            >
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
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { emitter, base_url } from '../../../../vue-assets'
import { useMixin } from '../../../../mixin'

const { successMessage, validationError: showValidationError } = useMixin()

// --- State ---
const modalRef = ref(null)
const isSubmitting = ref(false)
const buttonName = ref('Save')
const validationError = ref(null)

// Status is 1 by default to match the old `status: true` intent
const area = reactive({
  name: '',
  status: 1,
})

// --- Methods ---
const closeModal = () => {
  if (modalRef.value) {
    $(modalRef.value).modal('hide')
  }
}

const resetForm = () => {
  Object.assign(area, {
    name: '',
    status: 1,
  })
  validationError.value = null
}

const save = async () => {
  isSubmitting.value = true
  buttonName.value = 'Saving...'

  try {
    const response = await axios.post(
      `${base_url}admin/area/store`,
      area
    )

    if (response.data.status === 'success') {
      closeModal()
      resetForm()
      successMessage(response.data)
      emitter.emit('order-area')
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
    isSubmitting.value = false
    buttonName.value = 'Save'
  }
}
</script>