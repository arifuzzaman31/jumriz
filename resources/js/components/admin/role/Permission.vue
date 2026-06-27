<template>
  <div id="assign-role" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="m-t-none m-b">
            Assigning Permission To
            <strong class="text-primary">{{ role.role_name }}</strong>
          </h3>
        </div>

        <div class="modal-body">
          <div class="col-md-12">
            <!-- Display Validation Errors (Missing in original) -->
            <div v-if="errors" class="col-md-12" style="margin-bottom: 15px">
              <ul>
                <li
                  class="text-danger"
                  v-for="(error, index) in Object.values(errors)"
                  :key="index"
                >
                  {{ error[0] }}
                </li>
              </ul>
            </div>

            <form @submit.prevent="AssignRole" role="form">
              <div
                class="row"
                v-for="(value, index) in role.menus"
                :key="value.id || index"
              >
                <div v-if="index !== 0" class="col-md-12">
                  <hr />
                </div>

                <div class="col-md-12">
                  <h3>{{ value.name }}</h3>
                  <div class="switch" v-if="value.sub_menu.length === 0">
                    <div class="onoffswitch">
                      <input
                        :value="value.id"
                        :id="'menu-' + value.id"
                        v-model="value.check"
                        type="checkbox"
                        class="onoffswitch-checkbox"
                      />
                      <label class="onoffswitch-label" :for="'menu-' + value.id">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                      </label>
                    </div>
                  </div>
                </div>

                <div
                  class="col-md-3"
                  v-for="sub in value.sub_menu"
                  :key="sub.id"
                >
                  <h5 style="margin-top: 20px">{{ sub.name }}</h5>
                  <div class="switch">
                    <div class="onoffswitch">
                      <input
                        :value="sub.id"
                        :id="'sub-' + sub.id"
                        v-model="sub.check"
                        type="checkbox"
                        class="onoffswitch-checkbox"
                      />
                      <label class="onoffswitch-label" :for="'sub-' + sub.id">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <button
                    style="margin-top: 20px"
                    class="btn btn-lg btn-primary float-right"
                    type="submit"
                    :disabled="isUpdating"
                  >
                    <strong v-if="!isUpdating">{{ buttonName }}</strong>
                    <span v-else>
                      <i class="fa fa-spinner fa-spin"></i> Updating...
                    </span>
                  </button>
                </div>
              </div>
            </form>
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
  id: 0,
  role_name: "",
  menus: [],
});

const buttonName = ref("Update");
const errors = ref(null);
const isUpdating = ref(false);

let modalInstance = null;

// Show success/error message (replace with your preferred notification system)
const showMessage = (data, isError = false) => {
  alert(data.message || (isError ? "Something went wrong" : "Operation successful"));
};

// Fetch basic role info
const RoleInfo = async (id) => {
  try {
    const response = await axios.get(`${base_url}admin/role/${id}/edit`);
    role.id = response.data.id;
    role.role_name = response.data.role_name;
  } catch (error) {
    console.error("Error fetching role info:", error);
  }
};

// Fetch menus and their active states
const getMenus = async (id) => {
  try {
    const response = await axios.get(`${base_url}admin/role/${id}`);
    role.menus = response.data; // Vue 3 handles array deep reactivity automatically
  } catch (error) {
    console.error("Error fetching menus:", error);
  }
};

// Open modal and load data concurrently
const openModal = async (id) => {
  errors.value = null;
  role.id = id;

  // Fetch both role info and menus at the same time to speed up loading
  await Promise.all([RoleInfo(id), getMenus(id)]);

  // Show Bootstrap 5 modal
  const modalEl = document.getElementById("assign-role");
  if (modalEl) {
    modalInstance = new bootstrap.Modal(modalEl);
    modalInstance.show();
  }
};

// Save/Update permissions
const AssignRole = async () => {
  isUpdating.value = true;
  errors.value = null;

  try {
    const res = await axios.post(`${base_url}admin/permission`, role);

    if (res.data.status === "success") {
      showMessage(res.data);
      if (modalInstance) {
        modalInstance.hide(); // Hiding triggers 'hidden.bs.modal' event which emits refresh
      }
    } else {
      showMessage(res.data, true);
    }
  } catch (err) {
    if (err.response) {
      errors.value = err.response.data.errors;
    } else {
      console.error("Error:", err);
      showMessage(err, true);
    }
  } finally {
    isUpdating.value = false;
  }
};

// Listen for native Bootstrap modal close (when user clicks X or clicks outside)
const handleModalHidden = () => {
  emitter.emit("role-created", 1);
};

// Lifecycle hooks
onMounted(() => {
  emitter.on("assign-permission", openModal);

  // Native JS event listener replaces jQuery's $('#assign-role').on('hidden.bs.modal', ...)
  const modalEl = document.getElementById("assign-role");
  if (modalEl) {
    modalEl.addEventListener("hidden.bs.modal", handleModalHidden);
  }
});

onBeforeUnmount(() => {
  emitter.off("assign-permission", openModal);

  // Cleanup native event listener
  const modalEl = document.getElementById("assign-role");
  if (modalEl) {
    modalEl.removeEventListener("hidden.bs.modal", handleModalHidden);
  }

  // Dispose Bootstrap modal instance
  if (modalInstance) {
    modalInstance.dispose();
  }
});
</script>