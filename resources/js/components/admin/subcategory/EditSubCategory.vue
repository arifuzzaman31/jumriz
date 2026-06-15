<template>
  <div id="update-sub-category" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header text-right">
          <button class="btn btn-default" data-dismiss="modal">X</button>
        </div>
        
        <div class="modal-body">
          <div class="row">
            <!-- Form Section -->
            <div class="col-sm-8 b-r">
              <h3 class="m-t-none m-b">Update Sub Category</h3>

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
                        <i class="fa fa-camera"></i> Change Icon
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
              <p class="text-center" v-if="subcategory.image_status === 'unchanged'">
                <img class="img-responsive img-fluid" :src="subcategory.view_image" />
              </p>
              <p class="text-center" v-else>
                <img class="img-responsive img-fluid" :src="subcategory.image" />
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
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';
import { EventBus, base_url } from '../../../vue-assets.js';
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
const buttonName = ref('Update');
const validationError = ref(null);

const subcategory = reactive({
  id: '',
  category_id: '',
  name: '',
  native_name: '',
  image: '',
  view_image: '',
  status: 1,
  image_status: 'unchanged',
});

// ✅ Default values for reset
const getDefaultSubcategory = () => ({
  id: '',
  category_id: '',
  name: '',
  native_name: '',
  image: '',
  view_image: '',
  status: 1,
  image_status: 'unchanged',
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
    subcategory.image_status = 'changed';
  };
  reader.readAsDataURL(file);
};

const getSubCategory = async (id) => {
  try {
    const response = await axios.get(`${base_url}admin/sub-category/${id}/edit`);
    const data = response.data.data;

    subcategory.name = data.sub_category_name;
    subcategory.native_name = data.sub_category_native_name;
    subcategory.view_image = data.image;
    subcategory.status = data.status;
    subcategory.category_id = data.category_id;
  } catch (error) {
    console.error('Failed to fetch sub category:', error);
  }
};

const showModal = () => {
  // ✅ For Bootstrap 3/4 with jQuery
  if (typeof $ !== 'undefined') {
    $('#update-sub-category').modal('show');
  }
};

const hideModal = () => {
  // ✅ For Bootstrap 3/4 with jQuery
  if (typeof $ !== 'undefined') {
    $('#update-sub-category').modal('hide');
  }
};

const resetForm = () => {
  Object.assign(subcategory, getDefaultSubcategory());
  validationError.value = null;
};

const save = async () => {
  isLoading.value = true;
  buttonName.value = 'Updating....';

  try {
    const response = await axios.post(
      `${base_url}admin/sub-category/update/${subcategory.id}`,
      subcategory
    );

    if (response.data.status === 'success') {
      hideModal();
      resetForm();
      Mixin.methods.successMessage(response.data);
      
      // ✅ Emit event using mitt
      EventBus.$emit('sub-category-created');
      
      buttonName.value = 'Update';
    } else {
      Mixin.methods.successMessage(response.data);
      buttonName.value = 'Update';
    }
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors;
      Mixin.methods.validationError?.();
      buttonName.value = 'Update';
    } else {
      Mixin.methods.successMessage(err);
      buttonName.value = 'Update';
    }
  } finally {
    isLoading.value = false;
  }
};

// ✅ Event handler for opening edit modal
const handleEditSubCategory = (id) => {
  subcategory.id = id;
  getSubCategory(id);
  showModal();
};

// ✅ Lifecycle Hooks
onMounted(() => {
  EventBus.$on('update-sub-category', handleEditSubCategory);
});

onBeforeUnmount(() => {
  EventBus.$off('update-sub-category', handleEditSubCategory);
});
</script>