<template>
	
<div id="update-admin" class="modal fade" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header text-right">
				<button class="btn btn-default" data-dismiss="modal">X</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-8 b-r"><h3 class="m-t-none m-b">Add Admin</h3>
						<form @submit.prevent="save()" role="form">
							<div class="form-group">
								<label>Admin Name *</label> 
								<input v-model="admin.name" type="text" placeholder="Admin Name" class="form-control">
							</div>									


							<div class="form-group">
								<label>Email</label> 
								<input v-model="admin.email" type="text" placeholder="Admin Email" class="form-control">
							</div>									


							<div class="form-group">
								<label>Admin Icon (128X128) *</label> <br>
								<div class="fileinput fileinput-new" data-provides="fileinput">
									<span class="btn btn-block btn-primary btn-file"><span class="fileinput-new"><i class="fa fa-camera"></i> Chose Icon</span>
									<span class="fileinput-exists">Change Icon</span><input type="file" name="..." @change="onImageChange"/></span>
<!-- 											<span class="fileinput-filename"></span>
<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">×</a> -->
                              </div> 
                              </div>

                              <div class="form-group">
									<label>Area *</label> 
									<select name="area" class="form-control" v-model="admin.area">
										<option v-for="city in cities" :value="city.id">{{ city.city }}</option>
										
									</select>
								</div>
								<div class="form-group">
									<label>Role *</label> 
									<select name="status" class="form-control" v-model="admin.role_id">
										<option v-for="role in roleList" :value="role.id">{{ role.role_name }}</option>
										
									</select>
								</div>

								<div class="form-group">
									<label>Status *</label> 
									<select name="status" class="form-control" v-model="admin.status">
										<option value="1">Active</option>
										<option value="0">Inactive</option>
									</select>
								</div>

								<div>
									<button style="margin-bottom: 20px;" class="btn btn-lg  btn-primary float-right " type="submit"><strong>{{ button_name }}</strong></button>
								</div>
								</form>
								</div>
								<div class="col-sm-4"><h4>Photo Preview</h4>

									<p class="text-center" v-if="admin.avatar">
										<img class="img-responsive img-fluid" v-lazy="admin.avatar">
									</p>
									<p class="text-center" v-else>
										
										<img v-if="admin.view_image" class="img-responsive img-fluid" v-lazy="url+'/images/admin/'+admin.view_image">
										<img v-else class="img-responsive img-fluid" v-lazy="url+'images/default_avatar.png'">
									</p>
								</div>

								<div class="col-md-12" v-if="validation_error" style="margin-top: 20px">
									<div class="form-group">

										<div >
											<ul>
												<li class="text-danger" v-for="error in validation_error">{{ error[0] }}</li>
											</ul>
										</div>

									</div>
								</div>
						</div>
					</div>
				</div>
			</div>
		</div>

</template>
<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { emitter, base_url } from '../../../vue-assets';

const admin = ref({
    id: '',
    name: '',
    email: '',
    area: '',
    role_id: '',
    avatar: '',
    view_image: '',
    status: 1,
    image_status: 'unchanged',
});

const url = base_url;
const roleList = ref([]);
const cities = ref([]);
const button_name = ref("Update");
const validation_error = ref(null);

const getAdmin = async (id) => {
    const response = await axios.get(base_url + 'admin/admin/' + id + '/edit');
    admin.value.id = response.data.id;
    admin.value.name = response.data.name;
    admin.value.email = response.data.email;
    admin.value.area = response.data.admin_area_id;
    admin.value.role_id = response.data.role_id;
    admin.value.view_image = response.data.avatar;
    admin.value.status = response.data.status;
};

const getRoleList = async () => {
    const response = await axios.get(base_url + 'admin/all-role');
    roleList.value = response.data;
};

const getAreaList = async () => {
    const response = await axios.get(base_url + 'admin/all-area');
    cities.value = response.data;
};

onMounted(() => {
    getRoleList();
    getAreaList();
    emitter.on('update-admin', (id) => {
        getAdmin(id);
        $('#update-admin').modal('show');
    });
});

onUnmounted(() => {
    emitter.off('update-admin');
});

const onImageChange = (e) => {
    let files = e.target.files || e.dataTransfer.files;
    if (!files.length) return;
    createImage(files[0]);
};

const createImage = (file) => {
    let reader = new FileReader();
    reader.onload = (e) => {
        admin.value.avatar = e.target.result;
        admin.value.image_status = 'changed';
    };
    reader.readAsDataURL(file);
};

const save = async () => {
    button_name.value = "Updating...";
    try {
        await axios.post(base_url + 'admin/admin/update/' + admin.value.id, admin.value);
        $('#update-admin').modal('hide');
        resetForm();
        emitter.emit('admin-created');
        button_name.value = "Update";
    } catch (err) {
        if (err.response && err.response.status == 422) {
            validation_error.value = err.response.data.errors;
        }
        button_name.value = "Update";
    }
};

const resetForm = () => {
    admin.value = {
        id: '',
        name: '',
        email: '',
        role_id: '',
        avatar: '',
        view_image: '',
        status: '',
    };
    validation_error.value = null;
};
</script>