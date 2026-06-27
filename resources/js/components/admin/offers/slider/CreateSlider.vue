<template>
  <div id="modal-form" class="modal fade">
    <div class="modal-dialog modal-custom">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="m-t-none m-b">Add Slider</h3>
          <button class="btn btn-default text-right" data-dismiss="modal">
            X
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="save">
            <div class="row">
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

                    <img
                      v-if="imagePreview"
                      :src="imagePreview"
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
                <button type="submit" class="btn btn-primary" :disabled="isLoading">
                  <span v-if="isLoading">
                    <i class="fa fa-spinner fa-spin"></i> Saving...
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
import { ref, reactive, onBeforeUnmount } from "vue";
import axios from "axios";
import { emitter } from "../../../../vue-assets"; // Update path as needed

// Reactive state
const form = reactive({
  slider_title: "",
  back_url: "",
  description: "",
  status: 1,
});

const imageFile = ref(null);
const imagePreview = ref(null);
const validationError = ref(null);
const isLoading = ref(false);
const buttonName = ref("Save");

// Image change handler - keeps File object instead of base64
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
  
  // Create preview URL (more efficient than base64)
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
  imagePreview.value = URL.createObjectURL(file);
};

// Clean up object URL when component unmounts
onBeforeUnmount(() => {
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
});

// Reset form
const resetForm = () => {
  form.slider_title = "";
  form.back_url = "";
  form.description = "";
  form.status = 1;
  
  imageFile.value = null;
  imagePreview.value = null;
  validationError.value = null;
  isLoading.value = false;
  buttonName.value = "Save";
};

// Show success message (replace with your preferred notification method)
const showSuccessMessage = (message) => {
  // Using alert as placeholder - replace with your notification system
  alert(message.message || "Operation successful");
};

// Show validation error notification
const showValidationError = () => {
  // Replace with your notification system
  console.error("Validation errors occurred");
};

// Save handler
const save = async () => {
  isLoading.value = true;
  buttonName.value = "Saving...";
  validationError.value = null;

  try {
    // Use FormData for file upload
    const formData = new FormData();
    formData.append("slider_title", form.slider_title);
    formData.append("back_url", form.back_url);
    formData.append("description", form.description);
    formData.append("status", form.status);
    
    // Append file if exists
    if (imageFile.value) {
      formData.append("slider_banner", imageFile.value);
    }

    const response = await axios.post(
      `${base_url}admin/slider`,
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      }
    );

    if (response.data.status === "success") {
      // Close modal (jQuery - assuming Bootstrap is still used)
      const modal = document.getElementById("modal-form");
      if (modal) {
        const bootstrapModal = bootstrap.Modal.getInstance(modal);
        if (bootstrapModal) {
          bootstrapModal.hide();
        }
      }

      resetForm();
      emitter.emit("slider-created");
      showSuccessMessage(response.data);
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
    buttonName.value = "Save";
  }
};
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