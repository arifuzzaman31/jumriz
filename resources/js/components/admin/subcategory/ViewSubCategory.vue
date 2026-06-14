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
          <div class="row">
            <!-- Category Filter -->
            <div class="col-sm-4 m-b-xs">
              <multiselect
                v-model="category"
                deselect-label=""
                track-by="id"
                label="category_name"
                :searchable="true"
                open-direction="bottom"
                placeholder="Filter By Category"
                :options="categories || []"
                :disabled="false"
                @update:modelValue="fetchSubCategories()"
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
                  @keyup="fetchSubCategories()"
                />
              </div>
            </div>

            <!-- Clear Filter -->
            <div class="col-sm-5 m-b-xs">
              <button @click="clearFilter()" class="btn btn-primary">
                Clear Filter
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
                <tr
                  v-for="(value, index) in sub_categories.data"
                  :key="index"
                >
                  <td>
                    <img v-lazy="value.image" style="max-height: 100px" />
                  </td>
                  <td>{{ value.sub_category_name }}</td>
                  <td>{{ value.sub_category_native_name }}</td>
                  <td>{{ value.category?.category_name }}</td>
                  <td>{{ value.status_text }}</td>
                  <td>
                    
                    <a  @click.prevent="edit(value.id)"
                      class="btn btn-primary btn-sm me-1"
                      href="#"
                    >
                      <i class="fa fa-edit" title="Edit"></i>
                    </a>
                    
                    <a  @click.prevent="deleteCategory(value.id)"
                      class="btn btn-danger btn-sm"
                      href="#"
                    >
                      <i class="fa fa-trash" title="Delete"></i>
                    </a>
                  </td>
                </tr>

                <!-- Empty State -->
                <tr v-if="!sub_categories.data?.length">
                  <td colspan="6" class="text-center text-muted py-3">
                    No sub categories found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Loading -->
          <div class="col-md-12 text-center" v-else>
            <img :src="url + 'images/loading.gif'" alt="Loading..." />
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="ibox animated fadeInRightBig">
        <pagination
          v-if="sub_categories.meta"
          :pageData="sub_categories.meta"
        />
      </div>

      <!-- Edit Sub Category -->
      <div class="ibox">
        <!-- <update-sub-category
          :categories="categories"
          :brands="brands"
        /> -->
      </div>
    </div>
  </div>
</template>

<script>
import { EventBus } from "../../../vue-assets";
import Mixin from "../../../mixin";
import Pagination from "../pagination/Pagination.vue";
import UpdateCategory from "./EditSubCategory.vue";
import Multiselect from "vue-multiselect";

export default {
  name: "ViewSubCategory",

  mixins: [Mixin],

  props: {
    categories: {
      type: Object,
      default: () => [],
    }
  },

  components: {
    pagination: Pagination,
    "update-sub-category": UpdateCategory,
    Multiselect,
  },

  data() {
    return {
      sub_categories: {
        data: [],
        meta: null,
      },
      isLoading: false,
      keyword: "",
      category: null,       // ✅ null instead of fake object
      url: base_url,
    };
  },

  mounted() {
    const _this = this;

    _this.fetchSubCategories();

    EventBus.$on("sub-category-created", () => {
      _this.fetchSubCategories();
    });
  },

  beforeUnmount() {
    // ✅ Clean up event listener in Vue 3
    EventBus.$off("sub-category-created");
  },

  methods: {
    fetchSubCategories(page = 1) {
      this.isLoading = true;

      const category_id = this.category?.id ?? "";

      axios
        .get(
          `${base_url}admin/sub-category-list?page=${page}&keyword=${this.keyword}&category_id=${category_id}`
        )
        .then((response) => {
          this.sub_categories = response?.data || { data: [], meta: null };
        })
        .catch((error) => {
          console.error("Failed to fetch sub categories:", error);
          this.sub_categories = { data: [], meta: null };
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    pageClicked(pageNo) {
      this.fetchSubCategories(pageNo);
    },

    edit(id) {
      EventBus.$emit("update-sub-category", id);
    },

    deleteCategory(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",           // ✅ Vue 3 Swal uses 'icon' not 'type'
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.isConfirmed) {
          axios
            .get(`${base_url}admin/sub-category/delete/${id}`)
            .then((res) => {
              this.successMessage(res.data);
              this.fetchSubCategories();
            })
            .catch((error) => {
              console.error("Delete failed:", error);
            });
        }
      });
    },

    clearFilter() {
      this.keyword = "";
      this.category = null;     // ✅ reset to null
      this.fetchSubCategories();
    },
  },
};
</script>