<template>
  <div id="update-slider" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-custom">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="m-t-none m-b">Update Slider</h3>
          <button
            type="button"
            class="btn btn-default text-right"
            data-dismiss="modal"
          >
            X
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="save">
            <div class="row">
              <!-- Validation Errors -->
              <div
                class="col-md-12"
                v-if="validationError"
                style="margin-top: 20px"
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
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Slider Title*</label>
                  <input
                    type="text"
                    v-model="form.slider_title"
                    class="form-control"
                    placeholder="Slider Title"
                  />
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Slider Image (1920 X 420)</label>
                  <small>
                    Image best size is (1920 X 420), all image must be same
                    size.
                  </small>
                  <br />
                  <div
                    class="fileinput fileinput-new"
                    data-provides="fileinput"
                  >
                    <span class="btn btn-block btn-primary btn-file">
                      <span class="fileinput-new">
                        <i class="fa fa-camera"></i> Choose Image
                      </span>
                      <span class="fileinput-exists">Change Image</span>
                      <input
                        type="file"
                        name="slider_banner"
                        accept="image/*"
                        @change="onImageChange"
                      />
                    </span>

                    <!-- Show new image preview if selected -->
                    <img
                      v-if="imagePreview"
                      :src="imagePreview"
                      style="height: 80px; margin-top: 10px"
                    />
                    <!-- Otherwise show existing image from server -->
                    <img
                      v-else-if="form.show_banner"
                      :src="form.show_banner"
                      style="height: 80px; margin-top: 10px"
                    />
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>URL*</label>
                  <input
                    type="text"
                    v-model="form.back_url"
                    class="form-control"
                    placeholder="Slider Back Link URL"
                  />
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Slider Status*</label>
                  <select class="form-control" v-model="form.status">
                    <option value="1">Publish</option>
                    <option value="0">Not Publish</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12 text-right">
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="isLoading"
                >
                  <span v-if="isLoading">
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
  slider_title: "",
  show_banner: "",
  back_url: "",
  status: "",
});

const imageFile = ref(null);
const imagePreview = ref(null);
const validationError = ref(null);
const isLoading = ref(false);
const buttonName = ref("Update");

let modalInstance = null;

// Image change handler
const onImageChange = (e) => {
  const file = e.target.files[0];

  if (!file) return;

  // Validate file type
  if (!file.type.startsWith("image/")) {
    console.error("Please select an image file");
    return;
  }

  // Store the actual File object
  imageFile.value = file;

  // Create preview URL
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
  imagePreview.value = URL.createObjectURL(file);
};

// Fetch slider data for editing
const getSlider = async (id) => {
  try {
    const response = await axios.get(
      `${base_url}admin/slider/${id}/edit`
    );

    const data = response.data.data;
    form.id = id;
    form.slider_title = data.title;
    form.show_banner = data.banner;
    form.back_url = data.back_url;
    form.status = data.status;
  } catch (error) {
    console.error("Error fetching slider:", error);
  }
};

// Open modal and load data
const openModal = async (id) => {
  await getSlider(id);
  
  // Show Bootstrap 5 modal
  const modalEl = document.getElementById("update-slider");
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
  form.id = "";
  form.slider_title = "";
  form.show_banner = "";
  form.back_url = "";
  form.status = "";

  // Clean up image
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
  imageFile.value = null;
  imagePreview.value = null;
  validationError.value = null;
  isLoading.value = false;
  buttonName.value = "Update";
};

// Show success message (replace with your notification system)
const showSuccessMessage = (data) => {
  alert(data.message || "Operation successful");
};

// Show validation error notification
const showValidationError = () => {
  console.error("Validation errors occurred");
};

// Save handler
const save = async () => {
  isLoading.value = true;
  buttonName.value = "Updating...";
  validationError.value = null;

  try {
    // Use FormData for file upload
    const formData = new FormData();
    formData.append("_method", "PUT"); // For Laravel to recognize PUT request
    formData.append("slider_title", form.slider_title);
    formData.append("back_url", form.back_url);
    formData.append("status", form.status);

    // Append file only if a new one was selected
    if (imageFile.value) {
      formData.append("slider_banner", imageFile.value);
    }

    const response = await axios.post(
      `${base_url}admin/slider/${form.id}`,
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      }
    );

    if (response.data.status === "success") {
      closeModal();
      resetForm();
      showSuccessMessage(response.data);
      emitter.emit("slider-created");
    } else {
      showSuccessMessage(response.data);
    }
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors;
      showValidationError();
    } else {
      console.error("Error:", err);
      showSuccessMessage({ message: "Something went wrong" });
    }
  } finally {
    isLoading.value = false;
    buttonName.value = "Update";
  }
};

// Listen for update event
onMounted(() => {
  emitter.on("update-slider", openModal);
});

// Cleanup
onBeforeUnmount(() => {
  emitter.off("update-slider", openModal);
  
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
  
  if (modalInstance) {
    modalInstance.dispose();
  }
});
</script>

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
</style>