<template>
  <div id="update-admin" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header text-right">
          <button class="btn btn-default" data-dismiss="modal">X</button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-8 b-r">
              <h3 class="m-t-none m-b">Update Admin</h3>
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
                    type="text"
                    placeholder="Admin Email"
                    class="form-control"
                  />
                </div>

                <div class="form-group">
                  <label>Admin Icon (128x128) *</label> <br />
                  <div class="fileinput fileinput-new" data-provides="fileinput">
                    <span class="btn btn-block btn-primary btn-file">
                      <span class="fileinput-new"
                        ><i class="fa fa-camera"></i> Choose Icon</span
                      >
                      <span class="fileinput-exists">Change Icon</span>
                      <input type="file" @change="onImageChange" />
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <label>Area *</label>
                  <select class="form-control" v-model="admin.area">
                    <option
                      v-for="city in cities"
                      :key="city.id"
                      :value="city.id"
                    >
                      {{ city.city }}
                    </option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Role *</label>
                  <select class="form-control" v-model="admin.role_id">
                    <option
                      v-for="role in roleList"
                      :key="role.id"
                      :value="role.id"
                    >
                      {{ role.role_name }}
                    </option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Status *</label>
                  <select class="form-control" v-model="admin.status">
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
                      <i class="fa fa-spinner fa-spin"></i> Updating...
                    </span>
                  </button>
                </div>
              </form>
            </div>
            <div class="col-sm-4">
              <h4>Photo Preview</h4>
              <p class="text-center">
                <!-- Show new preview if selected, else existing image -->
                <img
                  class="img-responsive img-fluid"
                  v-if="imagePreview"
                  :src="imagePreview"
                />
                <img
                  class="img-responsive img-fluid"
                  v-else-if="admin.view_image"
                  :src="base_url + 'images/admin/' + admin.view_image"
                />
                <img
                  class="img-responsive img-fluid"
                  v-else
                  :src="base_url + 'images/default_avatar.png'"
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

// ─── State ──────────────────────────────────────────────
const admin = reactive({
  id: "",
  name: "",
  email: "",
  area: 0,
  role_id: "",
  view_image: "", // existing avatar filename/URL
  status: 1,
});

// File handling: store actual File and preview
const imageFile = ref(null);
const imagePreview = ref("");

const roleList = ref([]);
const cities = ref([]);
const buttonName = ref("Update");
const validationError = ref(null);
const isSaving = ref(false);
const url = base_url;

// ─── Fetch Data ─────────────────────────────────────────
const getAdmin = async (id) => {
  try {
    const response = await axios.get(`${base_url}admin/admin/${id}/edit`);
    const data = response.data;
    admin.id = data.id;
    admin.name = data.name;
    admin.email = data.email;
    admin.area = data.admin_area_id ?? 0;
    admin.role_id = data.role_id;
    admin.view_image = data.avatar;
    admin.status = data.status;
  } catch (error) {
    console.error("Error fetching admin:", error);
  }
};

const getRoleList = async () => {
  try {
    const response = await axios.get(`${base_url}admin/all-role`);
    roleList.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

const getAreaList = async () => {
  try {
    const response = await axios.get(`${base_url}admin/all-area`);
    cities.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

// ─── Image Handling (actual File, no base64) ──────────
const onImageChange = (e) => {
  const files = e.target.files || e.dataTransfer.files;
  if (!files.length) {
    // User cleared the selection
    if (imagePreview.value) {
      URL.revokeObjectURL(imagePreview.value);
      imagePreview.value = "";
    }
    imageFile.value = null;
    return;
  }
  const file = files[0];
  imageFile.value = file;
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
  imagePreview.value = URL.createObjectURL(file);
};

// ─── Save (with FormData) ──────────────────────────────
const save = async () => {
  isSaving.value = true;
  buttonName.value = "Updating...";
  validationError.value = null;

  const formData = new FormData();
  formData.append("id", admin.id);
  formData.append("name", admin.name);
  formData.append("email", admin.email);
  formData.append("area", admin.area);
  formData.append("role_id", admin.role_id);
  formData.append("status", admin.status);

  // Append new avatar only if a file was selected
  if (imageFile.value) {
    formData.append("image", imageFile.value);
  }
  // Add method spoofing for PUT if your backend requires it
  formData.append("_method", "PUT");

  try {
    const response = await axios.post(
      `${base_url}admin/admin/update/${admin.id}`,
      formData,
      { headers: { "Content-Type": "multipart/form-data" } }
    );

    if (response.data.status === "success") {
      if (typeof $ !== "undefined") {
        $("#update-admin").modal("hide");
      }
      resetForm();
      // You can use your mixin/global toast here:
      // Mixin.methods.successMessage(response.data);
      EventBus.$emit("admin-created");
    } else {
      // Show error from backend
      console.error(response.data.message);
    }
    buttonName.value = "Update";
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors;
    } else {
      console.error("Error:", err);
      alert("Something went wrong. Please try again.");
    }
    buttonName.value = "Update";
  } finally {
    isSaving.value = false;
  }
};

// ─── Reset ──────────────────────────────────────────────
const resetForm = () => {
  Object.assign(admin, {
    id: "",
    name: "",
    email: "",
    area: "",
    role_id: "",
    view_image: "",
    status: 1,
  });

  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
    imagePreview.value = "";
  }
  imageFile.value = null;

  // Clear the file input
  const fileInput = document.querySelector('#update-admin input[type="file"]');
  if (fileInput) fileInput.value = "";

  validationError.value = null;
};

// ─── Event Handler ──────────────────────────────────────
const handleUpdateAdmin = (id) => {
  getAdmin(id);
  resetForm(); // ensure clean state before showing
  if (typeof $ !== "undefined") {
    $("#update-admin").modal("show");
  }
};

// ─── Lifecycle ──────────────────────────────────────────
onMounted(() => {
  getRoleList();
  getAreaList();

  EventBus.$on("update-admin", handleUpdateAdmin);

  // Reset form when modal is hidden via Bootstrap's event
  if (typeof $ !== "undefined") {
    $("#update-admin").on("hidden.bs.modal", resetForm);
  }
});

onBeforeUnmount(() => {
  EventBus.$off("update-admin", handleUpdateAdmin);
  if (typeof $ !== "undefined") {
    $("#update-admin").off("hidden.bs.modal", resetForm);
  }
  // Revoke any lingering object URL
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
});
</script>