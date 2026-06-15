<template>
  <div id="modal-form" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header text-right">
          <button class="btn btn-default" data-dismiss="modal">X</button>
        </div>
        
        <div class="modal-body">
          <div class="row">
            <!-- Form Section -->
            <div class="col-sm-8 b-r">
              <h3 class="m-t-none m-b">Add Sub Category</h3>

              <form @submit.prevent="save" role="form">
                
                <!-- Category Select -->
                <div class="form-group">
                  <label>Choose Category *</label>
                  <select v-model="subcategory.category_id" class="form-control" required>
                    <option value="">Please Choose a Category</option>
                    <option 
                      v-for="value in categories" 
                      :key="value.id" 
                      :value="value.id"
                    >
                      {{ value.category_name }}
                    </option>
                  </select>
                </div>

                <!-- Sub Category Name -->
                <div class="form-group">
                  <label>Sub Category Name *</label>
                  <input 
                    v-model="subcategory.name" 
                    type="text" 
                    placeholder="Sub Category Name" 
                    class="form-control" 
                    required
                  />
                </div>

                <!-- Native Name -->
                <div class="form-group">
                  <label>Native Name</label>
                  <input 
                    v-model="subcategory.native_name" 
                    type="text" 
                    placeholder="Native Sub Category Name" 
                    class="form-control" 
                  />
                </div>

                <!-- Icon Upload -->
                <div class="form-group">
                  <label>Sub Category Icon (128x128) *</label>
                  <br />
                  <div class="fileinput fileinput-new" data-provides="fileinput">
                    <span class="btn btn-block btn-primary btn-file">
                      <span class="fileinput-new">
                        <i class="fa fa-camera"></i> Choose Icon
                      </span>
                      <span class="fileinput-exists">Change Icon</span>
                      <input type="file" @change="onImageChange" />
                    </span>
                  </div>
                </div>

                <!-- Status -->
                <div class="form-group">
                  <label>Status *</label>
                  <select v-model="subcategory.status" class="form-control" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                  </select>
                </div>

                <!-- Submit Button -->
                <div style="margin-bottom: 20px">
                  <button 
                    class="btn btn-lg btn-primary float-right" 
                    type="submit"
                    :disabled="isLoading"
                  >
                    <strong>{{ buttonName }}</strong>
                  </button>
                </div>
              </form>
            </div>

            <!-- Image Preview Section -->
            <div class="col-sm-4">
              <h4>Photo Preview</h4>
              <p class="text-center" v-if="subcategory.image">
                <img class="img-responsive img-fluid" :src="subcategory.image" />
              </p>
              <p class="text-center text-muted" v-else>
                <i class="fa fa-image fa-3x"></i>
                <br />
                <small>No image selected</small>
              </p>
            </div>

            <!-- Validation Errors -->
            <div class="col-md-12" v-if="validationError" style="margin-top: 20px">
              <div class="form-group">
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
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { EventBus, base_url } from '../../../vue-assets';
import Mixin from '../../../mixin';

// ✅ Props
const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
});

// ✅ Reactive State
const isLoading = ref(false);
const buttonName = ref('Save');
const validationError = ref(null);

const subcategory = reactive({
  category_id: '',
  name: '',
  native_name: '',
  image: '',
  status: 1,
});

// ✅ Default values for reset
const getDefaultSubcategory = () => ({
  category_id: '',
  name: '',
  native_name: '',
  image: '',
  status: 1,
});

// ✅ Methods
const onImageChange = (e) => {
  const files = e.target.files || e.dataTransfer.files;
  if (!files.length) return;
  createImage(files[0]);
};

const createImage = (file) => {
  const reader = new FileReader();
  reader.onload = (e) => {
    subcategory.image = e.target.result;
  };
  reader.readAsDataURL(file);
};

const hideModal = () => {
  if (typeof $ !== 'undefined') {
    $('#modal-form').modal('hide');
  }
};

const resetForm = () => {
  Object.assign(subcategory, getDefaultSubcategory());
  validationError.value = null;
};

const save = async () => {
  isLoading.value = true;
  buttonName.value = 'Saving...';

  try {
    const response = await axios.post(
      `${base_url}admin/sub-category`,
      subcategory
    );

    if (response.data.status === 'success') {
      hideModal();
      resetForm();
      Mixin.methods.successMessage(response.data);
      
      // ✅ Emit event using mitt (Vue 3)
      EventBus.$emit('sub-category-created');
      
      buttonName.value = 'Save';
    } else {
      Mixin.methods.successMessage(response.data);
      buttonName.value = 'Save';
    }
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors;
      Mixin.methods.validationError?.();
      buttonName.value = 'Save';
    } else {
      Mixin.methods.successMessage(err);
      isLoading.value = false;
      buttonName.value = 'Save';
    }
  } finally {
    isLoading.value = false;
  }
};
</script>