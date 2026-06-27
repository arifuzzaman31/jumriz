<template>
  <div id="modal-form" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-custom">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="m-t-none m-b">Add Coupon for Customer</h3>
          <button
            type="button"
            class="btn btn-default text-right"
            data-dismiss="modal"
          >
            X
          </button>
        </div>
        <div class="modal-body">
          <div class="ibox animated fadeInRightBig">
            <div class="ibox-content">
              <!-- Validation Errors -->
              <div
                class="row"
                v-if="validationError"
                style="margin-bottom: 15px"
              >
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
                <div class="col-sm-2 m-b-xs">
                  <Multiselect
                    v-model="form.coupon_code"
                    deselect-label=""
                    track-by="id"
                    label="coupon_code"
                    :searchable="true"
                    open-direction="bottom"
                    placeholder="Select Coupon"
                    :options="coupons"
                    :disabled="false"
                    @select="setDate"
                  />
                </div>

                <div class="col-sm-2 m-b-xs">
                  <Multiselect
                    v-model="filterData.location"
                    deselect-label=""
                    track-by="id"
                    label="city"
                    :searchable="true"
                    open-direction="bottom"
                    placeholder="Filter By Location"
                    :options="locations"
                    :disabled="false"
                    :multiple="true"
                  />
                </div>

                <div class="col-sm-2 m-b-xs">
                  <select class="form-control" v-model="form.order">
                    <option value="">Select By Most Order</option>
                    <option value="most_order">Last Month Most Order</option>
                  </select>
                </div>

                <div class="col-sm-2 m-b-xs">
                  <input
                    type="date"
                    class="form-control"
                    v-model="form.valid_date"
                  />
                </div>

                <div class="col-sm-2 m-b-xs">
                  <button class="btn btn-primary" @click="setCustomer">
                    Filter
                  </button>
                  <button class="btn btn-default" @click="clearFilter">
                    Clear
                  </button>
                </div>
              </div>

              <div class="ibox-content">
                <div class="row" style="margin-top: 15px" v-if="!isLoading">
                  <div
                    class="table-responsive text-center"
                    v-if="customers.length > 0"
                  >
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>
                            <input
                              type="checkbox"
                              v-model="isCheckAll"
                              @change="selectAll"
                            />
                            Check All
                          </th>
                          <th>ID</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Location</th>
                          <th>Phone</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="value in customers" :key="value.id">
                          <td>
                            <input
                              type="checkbox"
                              :value="value.id"
                              v-model="selectedCustomer"
                              @change="updateCheckall"
                            />
                          </td>
                          <td>{{ value.id }}</td>
                          <td>{{ value.name }}</td>
                          <td>{{ value.email }}</td>
                          <td>{{ value.location?.city || 'N/A' }}</td>
                          <td>{{ value.phone }}</td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="float-right d-flex align-items-center gap-3">
                      <div class="form-check form-check-inline">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          v-model="sendEmail"
                          id="inlineCheckbox1"
                        />
                        <label class="form-check-label" for="inlineCheckbox1">
                          Send Email
                        </label>
                      </div>

                      <button
                        class="btn btn-primary"
                        @click="save"
                        :disabled="isLoading || selectedCustomer.length === 0"
                      >
                        <span v-if="isSaving">
                          <i class="fa fa-spinner fa-spin"></i> Sending...
                        </span>
                        <span v-else>Send</span>
                      </button>
                    </div>
                  </div>

                  <!-- Empty state -->
                  <div
                    v-else
                    class="text-center text-muted py-4"
                  >
                    <p>No customers found. Apply filters to load customers.</p>
                  </div>
                </div>

                <div class="col-md-12 text-center" v-else>
                  <img
                    :src="`${base_url}/images/loading.gif`"
                    alt="Loading..."
                  />
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
import { ref, reactive, onBeforeUnmount } from "vue";
import axios from "axios";
import Multiselect from "vue-multiselect"; // Use vue-multiselect@3 for Vue 3
import { emitter } from "../../../../vue-assets"; // Update path as needed

// Props
const props = defineProps({
  coupons: {
    type: Array,
    default: () => [],
  },
  locations: {
    type: Array,
    default: () => [],
  },
});

// Emits
const emit = defineEmits(["coupon-sent"]);

// Reactive state
const form = reactive({
  location: [],
  coupon_code: null,
  valid_date: "",
  order: "",
  status: 1,
});

const filterData = reactive({
  location: [],
});

const customers = ref([]);
const isCheckAll = ref(false);
const selectedCustomer = ref([]);
const sendEmail = ref(false);
const isSaving = ref(false);
const isLoading = ref(false);
const validationError = ref(null);

let modalInstance = null;

// Set valid date from selected coupon
const setDate = (selectedCoupon) => {
  if (selectedCoupon) {
    form.valid_date = selectedCoupon.valid_date || "";
  }
};

// Remove duplicates by id
const removeDuplicates = (arr) => {
  const seen = new Map();
  return arr.filter((item) => {
    if (seen.has(item.id)) {
      return false;
    }
    seen.set(item.id, item);
    return true;
  });
};

// Fetch customers based on filters
const setCustomer = async () => {
  isLoading.value = true;
  validationError.value = null;

  try {
    const locationIds = filterData.location.map((data) => data.id);

    const response = await axios.post(
      `${base_url}admin/get-customer-for-coupon`,
      {
        location: locationIds,
        order_by: form.order,
      }
    );

    const { location, user_by_order } = response.data;

    // Combine and remove duplicates
    const locationData = location?.[0] || [];
    const orderByData = user_by_order?.[0] || [];

    const combinedData = [...locationData, ...orderByData];
    customers.value = removeDuplicates(combinedData);

    // Reset selection
    isCheckAll.value = false;
    selectedCustomer.value = [];
  } catch (error) {
    console.error("Error fetching customers:", error);
    customers.value = [];
  } finally {
    isLoading.value = false;
  }
};

// Select all customers
const selectAll = () => {
  if (isCheckAll.value) {
    selectedCustomer.value = customers.value.map((data) => data.id);
  } else {
    selectedCustomer.value = [];
  }
};

// Update check all state
const updateCheckall = () => {
  isCheckAll.value = selectedCustomer.value.length === customers.value.length;
};

// Show success/error message (replace with your notification system)
const showMessage = (data, isError = false) => {
  alert(data.message || (isError ? "Something went wrong" : "Operation successful"));
};

// Save/Send coupon
const save = async () => {
  if (selectedCustomer.value.length === 0) {
    showMessage({ message: "Please select at least one customer" }, true);
    return;
  }

  isSaving.value = true;
  validationError.value = null;

  try {
    const response = await axios.post(`${base_url}admin/send-coupon`, {
      select_id: selectedCustomer.value,
      coupon: form.coupon_code,
      valid_date: form.valid_date,
      send_to_sms: false,
      send_to_email: sendEmail.value,
    });

    if (response.data.status === "success") {
      closeModal();
      clearFilter();
      emitter.emit("customer-coupon-created");
      emit("coupon-sent", response.data);
      showMessage(response.data);
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
    isSaving.value = false;
  }
};

// Open modal
const openModal = () => {
  const modalEl = document.getElementById("modal-form");
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

// Clear all filters and reset state
const clearFilter = () => {
  form.location = [];
  form.coupon_code = null;
  form.valid_date = "";
  form.order = "";
  form.status = 1;

  filterData.location = [];
  customers.value = [];
  isCheckAll.value = false;
  sendEmail.value = false;
  selectedCustomer.value = [];
  validationError.value = null;
  isLoading.value = false;
};

// Expose methods for parent component to call if needed
defineExpose({
  openModal,
  closeModal,
  clearFilter,
});

// Cleanup
onBeforeUnmount(() => {
  if (modalInstance) {
    modalInstance.dispose();
  }
});
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

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

:deep(.multiselect__tags) {
  min-height: 38px;
  padding: 6px 40px 0 8px;
}

:deep(.multiselect__input) {
  font-size: 14px;
}
</style>