<template>
  <div id="update-coupon" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="m-t-none m-b">Update Coupon</h3>
          <button
            type="button"
            class="btn btn-default text-right"
            data-dismiss="modal"
          >
            X
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="update">
            <!-- Validation Errors -->
            <div class="row" v-if="validationError" style="margin-bottom: 15px">
              <div class="col-md-12">
                <ul>
                  <li
                    class="text-danger"
                    v-for="(error, index) in validationError"
                    :key="index"
                  >
                    {{ error[0] }}
                  </li>
                </ul>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Coupon Code*</label>
                  <input
                    type="text"
                    v-model="form.coupon_code"
                    class="form-control"
                    placeholder="Coupon Code"
                  />
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Amount Type*</label>
                  <select class="form-control" v-model="form.amount_type">
                    <option value="">Select Type</option>
                    <option value="1">Amount</option>
                    <option value="2">%</option>
                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Amount*</label>
                  <input
                    type="text"
                    v-model="form.amount"
                    class="form-control"
                    placeholder="Coupon Amount"
                  />
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Max Amount*</label>
                  <input
                    type="text"
                    v-model="form.max_amount_limit"
                    class="form-control"
                    placeholder="Maximum Amount"
                  />
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Valid Date*</label>
                  <!-- Replaced v2-datepicker with native HTML5 input -->
                  <input
                    type="date"
                    class="form-control"
                    v-model="form.valid_date"
                  />
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12 text-right" style="margin-top: 15px">
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="isUpdating"
                >
                  <span v-if="isUpdating">
                    <i class="fa fa-spinner fa-spin"></i> Updating...
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
import { ref, reactive, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";
import { emitter } from "../../../../vue-assets"; // Update path as needed

// Reactive state
const form = reactive({
  id: "",
  coupon_code: "",
  amount_type: "",
  amount: "",
  max_amount_limit: "",
  valid_date: "",
  status: 1,
});

const buttonName = ref("Update");
const validationError = ref(null);
const isUpdating = ref(false);

let modalInstance = null;

// Open modal and populate form
const openModal = (value) => {
  // Use Object.assign to safely copy data into the reactive object 
  // without breaking its reactivity proxy
  Object.assign(form, {
    id: value.id || "",
    coupon_code: value.coupon_code || "",
    amount_type: value.amount_type || "",
    amount: value.amount || "",
    max_amount_limit: value.max_amount_limit || "",
    valid_date: value.valid_date || "",
    status: value.status !== undefined ? value.status : 1,
  });

  // Show Bootstrap 5 modal
  const modalEl = document.getElementById("update-coupon");
  if (modalEl) {
    modalInstance = new bootstrap.Modal(modalEl);
    modalInstance.show();
  }
};

// Close modal
const closeModal = () => {
  if (modalInstance) {
    modalInstance.hide();
  }
};

// Reset form to defaults
const resetForm = () => {
  Object.assign(form, {
    id: "",
    coupon_code: "",
    amount_type: "",
    amount: "",
    max_amount_limit: "",
    valid_date: "",
    status: 1,
  });
  validationError.value = null;
  isUpdating.value = false;
};

// Show success message (replace with your preferred notification system)
const showMessage = (data, isError = false) => {
  alert(data.message || (isError ? "Something went wrong" : "Operation successful"));
};

// Update handler
const update = async () => {
  isUpdating.value = true;
  validationError.value = null;

  try {
    const response = await axios.put(
      `${base_url}admin/coupon/${form.id}`,
      form
    );

    if (response.data.status === "success") {
      closeModal();
      resetForm();
      showMessage(response.data);
      emitter.emit("coupon-created");
    } else {
      showMessage(response.data);
    }
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors;
    } else {
      console.error("Error:", err);
      showMessage(err, true);
    }
  } finally {
    isUpdating.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  emitter.on("update-coupon", openModal);
});

onBeforeUnmount(() => {
  emitter.off("update-coupon", openModal);
  
  // Dispose Bootstrap modal to prevent memory leaks
  if (modalInstance) {
    modalInstance.dispose();
  }
});
</script>

<style scoped>
/* Replaced empty quotes in original with proper CSS */
@media screen and (max-width: 573px) {
  .modal-lg {
    max-width: 100% !important;
    margin: 0 auto;
  }
}
</style>