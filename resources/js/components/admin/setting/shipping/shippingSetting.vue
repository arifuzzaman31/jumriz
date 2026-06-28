<template>
  <div class="row">
    <div class="col-sm-10">
      <h3 class="m-t-none m-b">Update Setting</h3>
      <form @submit.prevent="save" role="form">
        <div class="form-group">
          <label
            >Minimum Order Amount (Customer order amount must upto this
            amount)</label
          >
          <input
            v-model="form.minimum_order_amount"
            type="text"
            placeholder="Minimum Order Amount"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label>Shipping Amount* (This is Flat Rate)</label>
          <input
            v-model="form.shipping_amount"
            type="text"
            placeholder="Shipping Amount"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label
            >Order Amount (customer shipping cost will decrease by discount
            amount if cusotomer buy <i class="fa fa-arrow-down"></i> bellow
            mentioned amount item)</label
          >
          <input
            v-model="form.order_amount"
            type="text"
            placeholder="Order Amount"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label
            >Discount Amount (enter how much discount will have in shipping cost
            for <i class="fa fa-arrow-up"></i> avobe mentioned order
            amount)</label
          >
          <input
            v-model="form.discount_amount"
            type="text"
            placeholder="Discount Amount"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label
            >Wanna Take Shipping Cost Or Free? (if u off this switch shipping
            cost will be free)</label
          >
          <div class="switch">
            <div class="onoffswitch">
              <!-- Removed redundant `? true : false` -->
              <input
                type="checkbox"
                :checked="form.shipping_status == 1"
                class="onoffswitch-checkbox"
                id="example1"
                @change="toggleShippingStatus"
              />
              <label class="onoffswitch-label" for="example1">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label
            >Wanna Apply Discount on Shipping Cost by Shipping Amount ? (if u on
            this switch shipping cost will discount will decrease followed by
            order amount and discount amount)</label
          >
          <div class="switch">
            <div class="onoffswitch">
              <input
                type="checkbox"
                :checked="form.discount_status == 1"
                class="onoffswitch-checkbox"
                id="example2"
                @change="toggleDiscountStatus"
              />
              <label class="onoffswitch-label" for="example2">
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
      v-if="validationErrors"
      class="col-md-12"
      style="margin-top: 20px; margin-bottom: 10px"
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
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { emitter, base_url } from '../../../../vue-assets'
import { useMixin } from '../../../../mixin'

const { successMessage, validationError: showValidationError } = useMixin()

// --- State ---
const isSubmitting = ref(false)
const buttonName = ref('Update')
const validationErrors = ref(null)

const form = reactive({
  minimum_order_amount: '',
  shipping_amount: '',
  order_amount: '',
  discount_amount: '',
  shipping_status: '',
  discount_status: '',
})

// --- Methods ---

const getSetting = async () => {
  try {
    const response = await axios.get(`${base_url}admin/setting/shipping/discount`)
    Object.assign(form, response.data)
  } catch (error) {
    console.error('Error fetching shipping settings:', error)
  }
}

const save = async () => {
  buttonName.value = 'Updating...'
  isSubmitting.value = true

  try {
    const response = await axios.post(
      `${base_url}admin/setting/shipping/discount`,
      form
    )

    if (response.data.status === 'success') {
      successMessage(response.data)
      validationErrors.value = null
      emitter.emit('shipping-created')
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

// Separate API calls for Toggle Switches
const toggleShippingStatus = async () => {
  try {
    const response = await axios.post(`${base_url}admin/setting/shipping/status`)
    if (response.data.status === 'success') {
      successMessage(response.data)
      form.shipping_status = response.data.shipping_status
    }
  } catch (error) {
    console.error('Error updating shipping status:', error)
  }
}

const toggleDiscountStatus = async () => {
  try {
    const response = await axios.post(`${base_url}admin/setting/discount/status`)
    if (response.data.status === 'success') {
      successMessage(response.data)
      form.discount_status = response.data.discount_status
    }
  } catch (error) {
    console.error('Error updating discount status:', error)
  }
}

// --- Lifecycle ---
onMounted(() => {
  getSetting()
  emitter.on('shipping-created', getSetting)
})

onUnmounted(() => {
  emitter.off('shipping-created', getSetting)
})
</script>