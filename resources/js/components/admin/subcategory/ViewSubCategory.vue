<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Sub Category List</h5>
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
          <!-- Filters -->
          <div class="row">
            <div class="col-sm-4 m-b-xs">
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
                @update:model-value="fetchSubCategories"
              />
            </div>

            <div class="col-sm-3">
              <div class="input-group">
                <input
                  v-model="keyword"
                  placeholder="Search By Name"
                  type="text"
                  class="form-control"
                  @keyup.enter="fetchSubCategories"
                />
              </div>
            </div>

            <div class="col-sm-5 m-b-xs">
              <button @click="clearFilter" class="btn btn-primary">
                <i class="fa fa-times"></i> Clear Filter
              </button>
            </div>
          </div>

          <!-- Table -->
          <div class="table-responsive mt-3" v-if="!isLoading">
            <table class="table table-bordered table-condensed">
              <thead>
                <tr>
                  <th>Icon</th>
                  <th>Sub Category Name</th>
                  <th>Native Name</th>
                  <th>Category</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(value, index) in subCategories.data" :key="value.id || index">
                  <td>
                    <img v-lazy="value.image" style="max-height: 100px" />
                  </td>
                  <td>{{ value.sub_category_name }}</td>
                  <td>{{ value.sub_category_native_name }}</td>
                  <td>{{ value.category?.category_name }}</td>
                  <td>{{ value.status_text }}</td>
                  <td>
                    <a @click.prevent="edit(value.id)" class="btn btn-primary btn-sm me-1" href="#">
                      <i class="fa fa-edit" title="Edit"></i>
                    </a>
                    <a @click.prevent="deleteCategory(value.id)" class="btn btn-danger btn-sm" href="#">
                      <i class="fa fa-trash" title="Delete"></i>
                    </a>
                  </td>
                </tr>

                <tr v-if="!subCategories.data?.length">
                  <td colspan="6" class="text-center text-muted py-3">
                    No sub categories found.
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
      <div class="ibox animated fadeInRightBig" v-if="subCategories.meta">
        <pagination :pageData="subCategories.meta" />
      </div>

      <!-- Edit Sub Category Modal -->
      <EditSubCategory :categories="categories" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { EventBus, base_url } from '../../../vue-assets.js';
import { useCommonActions } from '../../../useCommonActions.js';
import EditSubCategory from './EditSubCategory.vue';
import Swal from 'sweetalert2';
import Pagination from '../pagination/Pagination.vue';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';

// ✅ Props with correct type
const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  }
});

// ✅ Reactive data
const subCategories = ref({ data: [], meta: null });
const isLoading = ref(false);
const keyword = ref('');
const category = ref(null);
const url = base_url;
const { successMessage } = useCommonActions();

// ✅ Methods
const fetchSubCategories = async (page = 1) => {
  isLoading.value = true;
  
  const categoryId = category.value?.id ?? '';
  const keywordVal = keyword.value;

  try {
    const response = await axios.get(
      `${base_url}admin/sub-category-list?page=${page}&keyword=${keywordVal}&category_id=${categoryId}`
    );
    subCategories.value = response?.data || { data: [], meta: null };
  } catch (error) {
    console.error('Failed to fetch sub categories:', error);
    subCategories.value = { data: [], meta: null };
  } finally {
    isLoading.value = false;
  }
};

const edit = (id) => {
  // console.log('edit', id);
  EventBus.$emit('update-sub-category', id);
};

const deleteCategory = (id) => {
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!',
  }).then((result) => {
    if (result.isConfirmed) {
      axios
        .get(`${base_url}admin/sub-category/delete/${id}`)
        .then((res) => {
          successMessage(res.data);
          fetchSubCategories();
        })
        .catch((error) => {
          console.error('Delete failed:', error);
        });
    }
  });
};

const clearFilter = () => {
  keyword.value = '';
  category.value = null;
  fetchSubCategories();
};

// ✅ Event handler with reference for cleanup
const handleSubCategoryCreated = () => {
  fetchSubCategories();
};

// ✅ Lifecycle hooks
onMounted(() => {
  fetchSubCategories();
  EventBus.$on('sub-category-created', handleSubCategoryCreated);
});

onBeforeUnmount(() => {
  EventBus.$off('sub-category-created', handleSubCategoryCreated);
});
</script>