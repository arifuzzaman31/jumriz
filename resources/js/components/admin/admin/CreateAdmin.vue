<template>
	
<div id="modal-form" class="modal fade" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header text-right">
				<button class="btn btn-default" data-dismiss="modal">X</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-8 b-r"><h3 class="m-t-none m-b">Create  Admin</h3>
						<form @submit.prevent="save()" role="form">
							<div class="form-group">
								<label>admin Name *</label> 
								<input v-model="admin.name" type="text" placeholder="Admin Name" class="form-control">
							</div>									

							<div class="form-group">
								<label>Email</label> 
								<input v-model="admin.email" type="email" placeholder="Email" class="form-control">
							</div>								
							<div class="form-group">
								<label>Password</label> 
								<input v-model="admin.password" type="password" placeholder="Password" class="form-control">
							</div>								
							<div class="form-group">
								<label>Confirm Password</label> 
								<input v-model="admin.password_confirmation" type="password" placeholder="Confirm Password" class="form-control">
							</div>									
							<div class="form-group">
								<label>Admin Area</label> 
								<select name="area" class="form-control" v-model="admin.area">
									<option value="">Chose Admin Area</option>
									<option v-for="value in cities" :key="value.id" :value="value.id">{{ value.city }}</option>
								</select>
							</div>
							<div class="form-group">
								<label>Admin Role</label> 
								<select name="status" class="form-control" v-model="admin.role_id">
									<option value="">Chose Admin Role</option>
									<option v-for="value in roleList" :key="value.id" :value="value.id">{{ value.role_name }}</option>
								</select>
							</div>
							<div class="form-group">
								<label>Avatar (264X264) *</label> <br>
								<div class="fileinput fileinput-new" data-provides="fileinput">
									<span class="btn btn-block btn-primary btn-file"><span class="fileinput-new"><i class="fa fa-camera"></i> Chose Avatar</span>
									<span class="fileinput-exists">Change Avatar</span><input type="file" name="..." @change="onImageChange"/></span>
                              </div> 
                            </div>	
							<div class="form-group">
								<label>Status *</label> 
								<select name="status" class="form-control" v-model="admin.status">
									<option value="1">Active</option>
									<option value="0">Inactive</option>
								</select>
							</div>
							<div style="margin-bottom: 20px;">
								<button  class="btn btn-lg  btn-primary float-right " type="submit"><strong>{{ button_name }}</strong></button>
							</div>
						</form>
						</div>
						<div class="col-sm-4"><h4>Avatar Preview</h4>

							<p class="text-center" v-if="admin.image">
								<img class="img-responsive img-fluid" v-lazy="admin.image">
							</p>										
							<p class="text-center" v-else>
								<img class="img-responsive img-fluid" v-lazy="url+'images/default_avatar.png'">
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
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { emitter, base_url } from '../../../vue-assets';
// Assuming Mixin logic is converted to a composable or standard functions
// For this migration, we'll call the methods directly if they are available

const admin = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    area: '',
    role_id: '',
    status: 1,
    image: '',
});

const roleList = ref([]);
const cities = ref([]);
const button_name = ref("Save");
const validation_error = ref(null);
const url = base_url;

const getRoleList = async () => {
    try {
        const response = await axios.get(base_url + 'admin/all-role');
        roleList.value = response.data;
    } catch (error) {
        console.error(error);
    }
};

const getAreaList = async () => {
    try {
        const response = await axios.get(base_url + 'admin/all-area');
        cities.value = response.data;
    } catch (error) {
        console.error(error);
    }
};

onMounted(() => {
    getRoleList();
    getAreaList();
});

const onImageChange = (e) => {
    let files = e.target.files || e.dataTransfer.files;
    if (!files.length) return;
    createImage(files[0]);
};

const createImage = (file) => {
    let reader = new FileReader();
    reader.onload = (e) => {
        admin.value.image = e.target.result;
    };
    reader.readAsDataURL(file);
};

const save = async () => {
    button_name.value = "Saving...";
    try {
        const response = await axios.post(base_url + 'admin/admin', admin.value);
        if (response.data.status === 'success') {
            $('#modal-form').modal('hide');
            resetForm();
            // successMessage is assumed to be globally available or imported
            emitter.emit('admin-created');
        }
        button_name.value = "Save";
    } catch (err) {
        if (err.response && err.response.status == 422) {
            validation_error.value = err.response.data.errors;
        }
        button_name.value = "Save";
    }
};

const resetForm = () => {
    admin.value = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        area: '',
        role_id: '',
        status: 1,
        image: '',
    };
    validation_error.value = null;
};
</script>