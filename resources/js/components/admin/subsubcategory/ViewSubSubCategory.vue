<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Sub Sub Category List</h5>
          <div class="ibox-tools">
            <a class="collapse-link">
              <i class="fa fa-chevron-up"></i>
            </a>
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              <i class="fa fa-wrench"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
              <li><a href="#" class="dropdown-item">Config option 1</a></li>
              <li><a href="#" class="dropdown-item">Config option 2</a></li>
            </ul>
            <a class="close-link">
              <i class="fa fa-times"></i>
            </a>
          </div>
        </div>
        
        <div class="ibox-content">
          <div class="row">
            <!-- Category Filter -->
            <div class="col-sm-3 m-b-xs">
              <multiselect
                v-model="category"
                deselect-label=""
                track-by="id"
                label="category_name"
                :searchable="true"
                open-direction="bottom"
                placeholder="Filter By Category"
                :options="categories"
                :disabled="false"
                @update:model-value="getSubCategories"
              />
            </div>

            <!-- Sub Category Filter -->
            <div class="col-sm-3 m-b-xs">
              <multiselect
                v-model="subCategory"
                deselect-label=""
                track-by="id"
                label="sub_category_name"
                :searchable="true"
                open-direction="bottom"
                placeholder="Filter By Sub Category"
                :options="subCategoriesList"
                :disabled="false"
                @update:model-value="fetchSubSubCategories"
              />
            </div>

            <!-- Keyword Search -->
            <div class="col-sm-3">
              <div class="input-group">
                <input
                  v-model="keyword"
                  placeholder="Search By Name"
                  type="text"
                  class="form-control"
                  @keyup.enter="fetchSubSubCategories"
                />
              </div>
            </div>

            <!-- Clear Filter -->
            <div class="col-sm-3 m-b-xs">
              <button @click="clearFilter" class="btn btn-primary">Clear Filter</button>
            </div>
          </div>

          <!-- Table -->
          <div class="table-responsive" style="margin-top: 15px;" v-if="!isLoading">
            <table class="table table-bordered table-condensed">
              <thead>
                <tr>
                  <th>Icon</th>
                  <th>Sub Sub Category Name</th>
                  <th>Native Name</th>
                  <th>Parent Tree</th>
                  <th>Brand</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(value, index) in subSubCategories?.data" :key="value.id || index">
                  <td><img v-lazy="value.image" style="max-height: 100px;" /></td>
                  <td>{{ value.sub_sub_category_name }}</td>
                  <td>{{ value.sub_sub_category_native_name }}</td>
                  <td>
                    {{ value.category?.category_name }} -> 
                    {{ value.sub_category?.sub_category_name }} -> 
                    {{ value.sub_sub_category_name }}
                  </td>
                  <td>
                    <span 
                      v-for="br in value.sub_sub_category_brand" 
                      :key="br.id" 
                      class="label label-primary"
                      style="margin-right: 2px;"
                    >
                      {{ br.brand?.brand_name }}
                    </span>
                  </td>
                  <td>{{ value.status_text }}</td>
                  <td>
                    <a @click.prevent="handleEditEvent(value.id)" class="btn btn-primary btn-sm me-1" href="#">
                      <i class="fa fa-edit" title="Edit"></i>
                    </a> 
                    <a @click.prevent="deleteCategory(value.id)" class="btn btn-danger btn-sm" href="#">
                      <i class="fa fa-trash" title="Delete"></i>
                    </a>
                  </td>
                </tr>

                <!-- Empty State -->
                <tr v-if="!subSubCategories.data?.length">
                  <td colspan="7" class="text-center text-muted py-3">
                    No sub sub categories found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Loading State -->
          <div class="col-md-12 text-center" v-else>
            <img :src="url + 'images/loading.gif'" alt="Loading..." />
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="ibox animated fadeInRightBig" v-if="subSubCategories.meta">
        <pagination :pageData="subSubCategories.meta" />
      </div>

      <!-- Edit Modal Component -->
      <div class="ibox">
        <update-sub-sub-category 
          :categories="categories" 
          :brands="brands" 
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';
import { EventBus, base_url } from '../../../vue-assets';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';
import Mixin from '../../../mixin';

const props = defineProps({
  categories: { type: Array, default: () => [] },
  brands: { type: Array, default: () => [] }
});

const subCategoriesList = ref([]);
const isLoading = ref(false);
const buttonName = ref("Update");
const validationError = ref(null);

const subcategory = reactive({
  id: '', category: '', sub_category: '', name: '', native_name: '',
  image: '', view_image: '', status: 1, selected_brand: [], image_status: 'unchanged',
});

const getDefaultState = () => ({ id: '', category: '', sub_category: '', name: '', native_name: '', image: '', view_image: '', status: 1, selected_brand: [], image_status: 'unchanged' });

const fetchSubSubCategoryData = (id) => {
  axios.get(`${base_url}admin/sub-sub-category/${id}/edit`)
    .then(response => {
      const data = response.data;
      subcategory.name = data.sub_sub_category.sub_sub_category_name;
      subcategory.native_name = data.sub_sub_category.sub_sub_category_native_name;
      subcategory.view_image = data.sub_sub_category.image;
      subcategory.status = data.sub_sub_category.status;
      subcategory.category = data.sub_sub_category.category_id;
      subcategory.sub_category = data.sub_sub_category.sub_category_id;
      subcategory.selected_brand = data.selected_brand;
      subCategoriesList.value = data.sub_category;
    });
};

const getSubCategoriesByCategory = () => {
  subcategory.sub_category = '';
  subCategoriesList.value = [];
  if (subcategory.category) {
    axios.get(`${base_url}admin/get-subcategory/${subcategory.category}`)
      .then(response => { subCategoriesList.value = response.data; });
  }
};

const onImageChange = (e) => {
  const files = e.target.files || e.dataTransfer.files;
  if (!files.length) return;
  const reader = new FileReader();
  reader.onload = (e) => { subcategory.image = e.target.result; subcategory.image_status = "changed"; };
  reader.readAsDataURL(files[0]);
};

const clearBrands = () => { subcategory.selected_brand = []; };
const hideModal = () => { if (typeof $ !== 'undefined') $('#update-form').modal('hide'); };
const resetForm = () => { Object.assign(subcategory, getDefaultState()); validationError.value = null; subCategoriesList.value = []; };

const save = async () => {
  isLoading.value = true;
  buttonName.value = "Updating....";
  try {
    const response = await axios.post(`${base_url}admin/sub-sub-category/update/${subcategory.id}`, subcategory);
    if (response.data.status === 'success') {
      hideModal();
      resetForm();
      Mixin.methods.successMessage(response.data);
      EventBus.$emit('sub-sub-category-created');
      buttonName.value = "Update";
    } else {
      Mixin.methods.successMessage(response.data);
      buttonName.value = "Update";
    }
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors;
      buttonName.value = "Update";
    } else {
      Mixin.methods.successMessage(err);
      buttonName.value = "Update";
    }
  } finally {
    isLoading.value = false;
  }
};

// ✅ LISTENER FOR THE EDIT BUTTON
const handleEditEvent = (id) => {
  alert('Edit component heard the event! ID: ' + id); // Test alert
  subcategory.id = id;
  fetchSubSubCategoryData(id);
  if (typeof $ !== 'undefined') {
    $('#update-form').modal('show');
  }
};

onMounted(() => {
  EventBus.$on('update-sub-sub-category', handleEditEvent);
});

onBeforeUnmount(() => {
  EventBus.$off('update-sub-sub-category', handleEditEvent);
});
</script>