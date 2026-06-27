<template>
  <div id="modal-form" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header text-right">
          <button class="btn btn-default" data-dismiss="modal">X</button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-8 b-r">
              <h3 class="m-t-none m-b">Create Admin</h3>
              <form @submit.prevent="save" role="form">
                <!-- Validation Errors -->
                <div v-if="validationError" style="margin-top: 20px">
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

                <div class="form-group">
                  <label>Admin Name *</label>
                  <input
                    v-model="admin.name"
                    type="text"
                    placeholder="Admin Name"
                    class="form-control"
                  />
                </div>

                <div class="form-group">
                  <label>Email</label>
                  <input
                    v-model="admin.email"
                    type="email"
                    placeholder="Email"
                    class="form-control"
                  />
                </div>
                <div class="form-group">
                  <label>Password</label>
                  <input
                    v-model="admin.password"
                    type="password"
                    placeholder="Password"
                    class="form-control"
                  />
                </div>
                <div class="form-group">
                  <label>Confirm Password</label>
                  <input
                    v-model="admin.password_confirmation"
                    type="password"
                    placeholder="Confirm Password"
                    class="form-control"
                  />
                </div>
                <div class="form-group">
                  <label>Admin Area</label>
                  <select
                    name="area"
                    class="form-control"
                    v-model="admin.area"
                  >
                    <option value="">Chose Admin Area</option>
                    <option
                      v-for="value in cities"
                      :key="value.id"
                      :value="value.id"
                    >
                      {{ value.city }}
                    </option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Admin Role</label>
                  <select
                    name="status"
                    class="form-control"
                    v-model="admin.role_id"
                  >
                    <option value="">Chose Admin Role</option>
                    <option
                      v-for="value in roleList"
                      :key="value.id"
                      :value="value.id"
                    >
                      {{ value.role_name }}
                    </option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Avatar (264x264) *</label> <br />
                  <div class="fileinput fileinput-new" data-provides="fileinput">
                    <span class="btn btn-block btn-primary btn-file">
                      <span class="fileinput-new"
                        ><i class="fa fa-camera"></i> Choose Avatar</span
                      >
                      <span class="fileinput-exists">Change Avatar</span>
                      <input type="file" @change="onImageChange" />
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <label>Status *</label>
                  <select
                    name="status"
                    class="form-control"
                    v-model="admin.status"
                  >
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                  </select>
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
            <div class="col-sm-4">
              <h4>Avatar Preview</h4>
              <p class="text-center">
                <img
                  class="img-responsive img-fluid"
                  v-if="imagePreview"
                  :src="imagePreview"
                />
                <img
                  class="img-responsive img-fluid"
                  v-else
                  :src="url + 'images/default_avatar.png'"
                />
              </p>
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
import { base_url, EventBus } from "../../../vue-assets";

// ─── State ─────────────────────────────────────────────
const admin = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  area: "",
  role_id: "",
  status: 1,
});

// File handling: store actual File and preview URL
const imageFile = ref(null);
const imagePreview = ref("");

const roleList = ref([]);
const cities = ref([]);

const buttonName = ref("Save");
const validationError = ref(null);
const isSaving = ref(false);
const url = base_url;

// ─── Methods ────────────────────────────────────────────

const getRoleList = async () => {
  try {
    const response = await axios.get(base_url + "admin/all-role");
    roleList.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

const getAreaList = async () => {
  try {
    const response = await axios.get(base_url + "admin/all-area");
    cities.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

// ✅ Store the actual File, create object URL for preview
const onImageChange = (e) => {
  const files = e.target.files || e.dataTransfer.files;
  if (!files.length) {
    // If user clears selection, reset
    if (imagePreview.value) {
      URL.revokeObjectURL(imagePreview.value);
      imagePreview.value = "";
    }
    imageFile.value = null;
    return;
  }
  const file = files[0];
  imageFile.value = file;

  // Revoke old preview if any
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
  imagePreview.value = URL.createObjectURL(file);
};

// ─── Save (with FormData) ──────────────────────────────
const save = async () => {
  isSaving.value = true;
  buttonName.value = "Saving...";
  validationError.value = null;

  // Build FormData
  const formData = new FormData();
  formData.append("name", admin.name);
  formData.append("email", admin.email);
  formData.append("password", admin.password);
  formData.append("password_confirmation", admin.password_confirmation);
  formData.append("area", admin.area);
  formData.append("role_id", admin.role_id);
  formData.append("status", admin.status);

  // Append image only if a new file is selected
  if (imageFile.value) {
    formData.append("image", imageFile.value);
  }

  try {
    const response = await axios.post(base_url + "admin/admin", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    if (response.data.status === "success") {
      // Close modal (Bootstrap 4/5 via jQuery)
      if (typeof $ !== "undefined") {
        $("#modal-form").modal("hide");
      }
      resetForm();
      // Show success (you can use your preferred toast)
      // Mixin.methods.successMessage(response.data);
      EventBus.$emit("admin-created");
    } else {
      // Show error from backend
      // Mixin.methods.successMessage(response.data);
      console.error(response.data.message);
    }
    buttonName.value = "Save";
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors;
    } else {
      console.error("Error:", err);
      alert("Something went wrong. Please try again.");
    }
    buttonName.value = "Save";
  } finally {
    isSaving.value = false;
  }
};

// ─── Reset ──────────────────────────────────────────────
const resetForm = () => {
  // Reset reactive admin object
  Object.assign(admin, {
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    area: "",
    role_id: "",
    status: 1,
  });

  // Clear file and preview
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
    imagePreview.value = "";
  }
  imageFile.value = null;

  // Clear file input element
  const fileInput = document.querySelector('#modal-form input[type="file"]');
  if (fileInput) fileInput.value = "";

  validationError.value = null;
};

// ─── Lifecycle ──────────────────────────────────────────
onMounted(() => {
  getRoleList();
  getAreaList();

  // Listen for custom event to open modal (if needed)
  EventBus.$on("create-admin", () => {
    resetForm();
    if (typeof $ !== "undefined") {
      $("#modal-form").modal("show");
    }
  });

  // Reset form when modal is hidden (via Bootstrap)
  if (typeof $ !== "undefined") {
    $("#modal-form").on("hidden.bs.modal", resetForm);
  }
});

onBeforeUnmount(() => {
  // Clean up event listeners
  EventBus.$off("create-admin");
  if (typeof $ !== "undefined") {
    $("#modal-form").off("hidden.bs.modal", resetForm);
  }
  // Revoke any lingering object URL
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
});
</script>