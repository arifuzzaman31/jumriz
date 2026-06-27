<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Role List</h5>
          <div class="ibox-tools">
            <a class="collapse-link">
              <i class="fa fa-chevron-up"></i>
            </a>
            <a class="dropdown-toggle" data-bs-toggle="dropdown" href="#">
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
            <div class="col-sm-5 m-b-xs"></div>
            <div class="col-sm-4 m-b-xs"></div>
            <div class="col-sm-3">
              <div class="input-group">
                <input
                  placeholder="Search By Name"
                  type="text"
                  class="form-control form-control-sm"
                  v-model="keyword"
                  @keyup.enter="getRole()"
                />
                <span class="input-group-append"></span>
              </div>
            </div>
          </div>

          <div
            class="table-responsive"
            style="margin-top: 15px"
            v-if="!isLoading"
          >
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Role Name</th>
                  <th>Permissions</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="value in roles.data" :key="value.id">
                  <td>{{ value.role_name }}</td>
                  <td>
                    <a
                      @click.prevent="assignPermission(value.id)"
                      href="#"
                      class="btn btn-secondary rounded"
                    >
                      <i class="fa fa-key"></i>
                    </a>
                  </td>
                  <td>
                    <a
                      @click.prevent="edit(value)"
                      class="btn btn-primary btn-sm"
                      href="#"
                    >
                      <i class="fa fa-edit" title="Edit"></i>
                    </a>
                    <a
                      @click.prevent="deleteRole(value.id)"
                      class="btn btn-danger btn-sm"
                      href="#"
                    >
                      <i class="fa fa-trash" title="Delete"></i>
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Empty State -->
            <div
              v-if="roles.data && roles.data.length === 0"
              class="text-center text-muted py-3"
            >
              No roles found.
            </div>
          </div>

          <div class="col-md-12 text-center" v-else>
            <img :src="`${base_url}/images/loading.gif`" alt="Loading..." />
          </div>
        </div>
      </div>

      <div class="ibox animated fadeInRightBig">
        <Pagination v-if="roles" :pageData="roles" @page-clicked="pageClicked" />
      </div>

      <div class="ibox">
        <EditRole />
        <Permission />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import { emitter, base_url } from "../../../vue-assets"; // Update path as needed
import Pagination from "../pagination/Pagination.vue";
import EditRole from "./EditRole.vue";
import Permission from "./Permission.vue";

// Reactive State
const roles = ref([]);
const isLoading = ref(false);
const keyword = ref("");

// Fetch roles
const getRole = async (page = 1) => {
  isLoading.value = true;

  try {
    const response = await axios.get(`${base_url}admin/role-list`, {
      params: {
        page: page,
        keyword: keyword.value,
      },
    });
    
    roles.value = response.data;
  } catch (error) {
    console.error("Error fetching roles:", error);
  } finally {
    isLoading.value = false;
  }
};

// Handle pagination click
const pageClicked = (pageNo) => {
  getRole(pageNo);
};

// Edit role (Note: Original code passed the entire 'value' object to the event)
const edit = (roleData) => {
  emitter.emit("update-role", roleData);
};

// Assign permission
const assignPermission = (id) => {
  emitter.emit("assign-permission", id);
};

// Delete role
const deleteRole = async (id) => {
  try {
    const result = await Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
    });

    if (result.isConfirmed) {
      const res = await axios.get(`${base_url}admin/role/delete/${id}`);
      
      Swal.fire({
        icon: "success",
        title: res.data.message || "Deleted successfully!",
        timer: 1500,
        showConfirmButton: false,
      });
      
      getRole(); // Refresh list
    }
  } catch (error) {
    console.error("Error deleting role:", error);
    Swal.fire("Error!", "Something went wrong.", "error");
  }
};

// Refresh data handler for event bus
const refreshData = () => {
  getRole();
};

// Lifecycle hooks
onMounted(() => {
  getRole();
  emitter.on("role-created", refreshData);
});

onBeforeUnmount(() => {
  emitter.off("role-created", refreshData);
});
</script>