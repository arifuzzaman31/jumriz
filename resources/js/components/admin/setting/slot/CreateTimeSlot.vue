<template>
  <div ref="modalRef" id="CreateTimeSlot" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header text-right">
          <button class="btn btn-default" data-dismiss="modal">X</button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-8 mr-auto ml-auto">
              <h3 class="m-t-none m-b">Add New Slot</h3>
              <form @submit.prevent="save" role="form">
                <div class="form-group">
                  <label>Slot Name *</label>
                  <input
                    v-model="timeSlot.slot_name"
                    type="text"
                    placeholder="Eg, 08am - 9am"
                    class="form-control"
                  />
                </div>

                <div class="form-group">
                  <label
                    >Time Slot Will Expired at (if slot date is current date
                    then this slot will be disapear after below mentioned time
                    in particular day) please enter 24 hour format *</label
                  >
                  <input
                    v-model="timeSlot.expired_at"
                    type="text"
                    placeholder="eg 07:30"
                    class="form-control"
                  />
                </div>

                <div class="form-group">
                  <label>Status</label>
                  <select class="form-control" v-model="timeSlot.status">
                    <option value="">Choose Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
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
              v-if="validationError"
              class="col-md-8 mr-auto ml-auto"
              style="margin-top: 20px"
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
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { emitter } from '../../../../vue-assets'
import { useMixin } from '../../../../mixin'

const { successMessage, validationError: showValidationError } = useMixin()

const modalRef = ref(null)
const isSubmitting = ref(false)
const buttonName = ref('Save')
const validationError = ref(null)

const timeSlot = reactive({
  slot_name: '',
  expired_at: '',
  status: '',
})

const closeModal = () => {
  // Option 1: jQuery (Bootstrap 3/4)
  $(modalRef.value).modal('hide')
  
  // Option 2: Bootstrap 5 (uncomment if using BS5)
  // const modal = bootstrap.Modal.getInstance(modalRef.value)
  // modal?.hide()
}

const resetForm = () => {
  timeSlot.slot_name = ''
  timeSlot.expired_at = ''
  timeSlot.status = ''
  validationError.value = null
}

const save = async () => {
  buttonName.value = 'Saving...'
  isSubmitting.value = true

  try {
    const response = await axios.post(
      `${base_url}admin/setting/time-slot`,
      timeSlot
    )

    if (response.data.status === 'success') {
      closeModal()
      resetForm()
      successMessage(response.data)
      emitter.emit('slot-created')
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
    buttonName.value = 'Save'
    isSubmitting.value = false
  }
}
</script>