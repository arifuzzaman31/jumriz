<template>
  <div id="edit-role" class="modal fade" tabindex="-1" aria-hidden="true">
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
              <!-- Fixed title from "Add Role" to "Update Role" -->
              <h3 class="m-t-none m-b">Update Role</h3>

              <!-- Validation Errors (Moved above form for better UX) -->
              <div
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
                      <i class="fa fa-spinner fa-spin"></i> Updating...
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
import { emitter } from "../../../vue-assets"; // Update path as needed

// Reactive state
const role = reactive({
  id: "",
  role_name: "",
});

const buttonName = ref("Update");
const validationError = ref(null);
const isSaving = ref(false);

let modalInstance = null;

// Show success/error message (replace with your preferred notification system)
const showMessage = (data, isError = false) => {
  alert(data.message || (isError ? "Something went wrong" : "Operation successful"));
};

// Open modal and populate form
const openModal = (roleData) => {
  // Use Object.assign to safely copy data into the reactive object 
  // without breaking its reactivity proxy
  Object.assign(role, {
    id: roleData.id,
    role_name: roleData.role_name,
  });

  // Show Bootstrap 5 modal
  const modalEl = document.getElementById("edit-role");
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

// Reset form
const resetForm = () => {
  role.id = "";
  role.role_name = "";
  validationError.value = null;
};

// Save handler
const save = async () => {
  isSaving.value = true;
  validationError.value = null;

  try {
    const response = await axios.post(
      `${base_url}admin/role/update/${role.id}`,
      role
    );

    closeModal();
    resetForm();
    showMessage(response.data);
    emitter.emit("role-created"); // Refresh list
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

// Lifecycle hooks
onMounted(() => {
  emitter.on("update-role", openModal);
});

onBeforeUnmount(() => {
  emitter.off("update-role", openModal);
  
  // Dispose Bootstrap modal to prevent memory leaks
  if (modalInstance) {
    modalInstance.dispose();
  }
});
</script>