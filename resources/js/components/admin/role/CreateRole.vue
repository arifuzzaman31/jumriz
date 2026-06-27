<template>
  <div id="modal-form" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header text-right">
          <button
            type="button"
            class="btn btn-default"
            data-dismiss="modal"
          >
            X
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-8 mr-auto ml-auto">
              <h3 class="m-t-none m-b">Add Role</h3>

              <!-- Validation Errors -->
              <div
                class="col-md-12 mr-auto ml-auto"
                v-if="validationError"
                style="margin-top: 0px; margin-bottom: 20px"
              >
                <div class="form-group">
                  <div>
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
              </div>

              <form @submit.prevent="save" role="form">
                <div class="form-group">
                  <label>Role Name *</label>
                  <input
                    v-model="role.role_name"
                    type="text"
                    placeholder="Role Name"
                    class="form-control"
                  />
                </div>
                <div style="margin-bottom: 20px">
                  <button
                    class="btn btn-lg btn-primary float-right"
                    type="submit"
                    :disabled="isSaving"
                  >
                    <strong v-if="!isSaving">{{ buttonName }}</strong>
                    <span v-else>
                      <i class="fa fa-spinner fa-spin"></i> Saving...
                    </span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";
import { base_url, EventBus } from "../../../vue-assets"; // ✅ use EventBus

// ✅ Global bootstrap (loaded via CDN)
const Modal = window.bootstrap?.Modal;

// Reactive state
const role = reactive({
  role_name: "",
});

const buttonName = ref("Save");
const validationError = ref(null);
const isSaving = ref(false);

// Keep a reference to the modal instance
let modalInstance = null;

// ✅ Open modal (called from parent via EventBus)
const openModal = () => {
  const modalEl = document.getElementById("modal-form");
  if (!modalEl) return;

  if (!Modal) {
    console.warn("Bootstrap Modal is not available");
    return;
  }

  if (modalInstance) {
    modalInstance.show();
  } else {
    modalInstance = new Modal(modalEl);
    modalInstance.show();
  }

  // Reset form when opening
  resetForm();
};

// ✅ Close modal
const closeModal = () => {
  if (modalInstance) {
    modalInstance.hide();
  }
};

// Save handler
const save = async () => {
  isSaving.value = true;
  validationError.value = null;

  try {
    const response = await axios.post(`${base_url}admin/role`, role);

    if (response.data.status === "success") {
      // Show success (you can replace with your toast/mixin)
      // Mixin.methods.successMessage(response.data);
      console.log(response.data.message);
      closeModal();
      resetForm();
      EventBus.$emit("role-created");
    } else {
      // Show error from backend
      // Mixin.methods.successMessage(response.data);
      console.error(response.data.message);
    }
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors;
    } else {
      console.error("Error:", err);
      alert("Something went wrong. Please try again.");
    }
  } finally {
    isSaving.value = false;
  }
};

// Reset form
const resetForm = () => {
  role.role_name = "";
  validationError.value = null;
};

// ✅ Clean up on modal hidden (Bootstrap native event)
const onModalHidden = () => {
  resetForm();
};

// ✅ Lifecycle hooks
onMounted(() => {
  const modalEl = document.getElementById("modal-form");
  if (modalEl) {
    modalEl.addEventListener("hidden.bs.modal", onModalHidden);
  }

  // Listen for custom event to open the modal
  EventBus.$on("create-role", openModal);
});

onBeforeUnmount(() => {
  const modalEl = document.getElementById("modal-form");
  if (modalEl) {
    modalEl.removeEventListener("hidden.bs.modal", onModalHidden);
  }
  if (modalInstance) {
    modalInstance.dispose();
    modalInstance = null;
  }
  EventBus.$off("create-role", openModal);
});
</script>