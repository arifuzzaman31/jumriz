<template>
	
	<div id="update-currency" class="modal fade" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header text-right">
					<button class="btn btn-default" data-dismiss="modal">X</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-8 mr-auto ml-auto"><h3 class="m-t-none m-b">Edit Currency</h3>
							<form @submit.prevent="save()" role="form">
								<div class="form-group">
									<label>Country Name *</label> 
									<input v-model="currency.country" type="text" placeholder="Country Name" class="form-control">
								</div>								

								<div class="form-group">
									<label>Currency Name *</label> 
									<input v-model="currency.currency" type="text" placeholder="Currency Name" class="form-control">
								</div>									


								<div class="form-group">
									<label>Code</label> 
									<input v-model="currency.code" type="text" placeholder="ex : USD" class="form-control">
								</div>								

								<div class="form-group">
									<label>Symbol</label> 
									<input v-model="currency.symbol" type="text" placeholder="ex : $" class="form-control">
								</div>										
								<div style="margin-bottom: 20px;">
									<button  class="btn btn-lg  btn-primary float-right " type="submit"><strong>{{ button_name }}</strong></button>
								</div>
								</form>
								</div>
								<div class="col-md-8 mr-auto ml-auto" v-if="validation_error" style="margin-top: 20px">
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
import { emitter, base_url } from '../../../../vue-assets';
import { useCommonActions } from '../../../../useCommonActions';

const { successMessage, validationError } = useCommonActions();

const currency = ref({
    id: '',
    currency: '',
    country: '',
    code: '',
    symbol: '',
});

const button_name = ref("Update");
const validation_error = ref(null);

const save = async () => {
    button_name.value = "updating...";
    try {
        const response = await axios.post(base_url + 'admin/setting/currency/update/' + currency.value.id, currency.value);
        if (response.data.status === 'success') {
            $('#update-currency').modal('hide');
            resetForm();
            successMessage(response.data);
            emitter.emit('currency-created');
            button_name.value = "Update";
        } else {
            successMessage(response.data);
            button_name.value = "Save";
        }
    } catch (err) {
        if (err.response && err.response.status == 422) {
            validation_error.value = err.response.data.errors;
            validationError();
            button_name.value = "Update";
        } else {
            successMessage(err);
            button_name.value = "Save";
        }
    }
};

const resetForm = () => {
    currency.value = {
        id: '',
        currency: '',
        country: '',
        code: '',
        symbol: '',
    };
    validation_error.value = null;
};

onMounted(() => {
    emitter.on('update-currency', (value) => {
        currency.value = { ...value };
        $('#update-currency').modal('show');
    });
});

onUnmounted(() => {
    emitter.off('update-currency');
});
</script>