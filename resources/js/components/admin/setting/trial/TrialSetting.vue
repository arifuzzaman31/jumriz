<template>
  <div class="row">
    <div class="col-md-6 mr-auto ml-auto border p-4 bg-white">
      <h3 class="m-t-none m-b">Update Trial Setting</h3>
      <form @submit.prevent="save" role="form">
        <div class="form-group">
          <label
            >Minnimum Product in Cart For Getting Trial Option By User (Suppose
            if user don't add 2 product on cart he won't get trial opption for
            any product)*</label
          >
          <input
            v-model="form.product_in_cart"
            type="text"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label>Maximum Trial Item User can Add To Thier Cart*</label>
          <input v-model="form.max_trial_item" class="form-control" />
        </div>

        <div class="form-group">
          <label>Trial Status (Do You Want Trial System On Your Site ?)</label>
          <select class="form-control" v-model="form.status">
            <option value="1">Yes</option>
            <option value="0">No</option>
          </select>
        </div>

        <div style="margin-bottom: 20px">
          <button
            style="margin-bottom: 20px"
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
      style="margin-top: 20px; margin-bottom: 20px"
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
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { emitter, base_url } from '../../../../vue-assets'
import { useMixin } from '../../../../mixin'

// Destructure the composable functions
const { successMessage, validationError: showValidationError } = useMixin()

// --- State ---
const isSubmitting = ref(false)
const buttonName = ref('Update')
const validationError = ref(null)

const form = reactive({
  product_in_cart: '',
  max_trial_item: '',
  status: '',
})

// --- Methods ---
const getTrialSetting = async () => {
  try {
    const response = await axios.get(`${base_url}admin/setting/trial/1/edit`)
    
    // Cleanly map response data to reactive state
    Object.assign(form, {
      product_in_cart: response.data.product_in_cart,
      max_trial_item: response.data.max_trial_item,
      status: response.data.status,
    })
  } catch (error) {
    console.error('Error fetching trial settings:', error)
  }
}

const save = async () => {
  buttonName.value = 'Updating...'
  isSubmitting.value = true

  try {
    const response = await axios.post(`${base_url}admin/setting/trial`, form)

    if (response.data.status === 'success') {
      successMessage(response.data)
      emitter.emit('trial-created')
      validationError.value = null
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
  getTrialSetting()
  emitter.on('trial-created', getTrialSetting)
})

onUnmounted(() => {
  emitter.off('trial-created', getTrialSetting)
})
</script>