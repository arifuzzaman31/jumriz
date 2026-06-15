<template>
    <div id="modal-form" class="modal fade">
        <div class="modal-dialog modal-lg modal-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="m-t-none m-b">Add Color</h3>
                    <button class="btn btn-default text-right" data-dismiss="modal">X</button>
                </div>
                
                <div class="modal-body">
                    <form @submit.prevent="save">
                        <!-- Validation Errors -->
                        <div class="row" v-if="validationError">
                            <div class="col-md-12" style="margin-top: 20px">
                                <div class="form-group">
                                    <ul>
                                        <li class="text-danger" v-for="(error, index) in validationError" :key="index">
                                            {{ error[0] }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Color Name *</label> 
                                    <input 
                                        type="text" 
                                        v-model="form.name"  
                                        class="form-control" 
                                        placeholder="Color Name"
                                        required
                                    >
                                </div>
                            </div>						

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Color *</label> 
                                    <input 
                                        type="color" 
                                        v-model="form.color_code"  
                                        class="form-control form-control-color"
                                    >
                                </div>
                            </div>	

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Color Code *</label> 
                                    <input 
                                        type="text" 
                                        v-model="form.color_code" 
                                        class="form-control" 
                                        placeholder="#FFFFFF"
                                        required
                                    >
                                </div>
                            </div>	
                        </div>	

                     	<!-- Buttons -->
                        <div class="row">								
                            <div class="col-md-12 text-right" style="margin-top: 15px;">
                                <button type="submit" class="btn btn-primary" :disabled="isLoading">
                                    {{ buttonName }}
                                </button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { EventBus, base_url } from '../../../../vue-assets';
import Mixin from '../../../../mixin';

// ✅ Reactive State
const form = reactive({
    name: '',  
    color_code: ''
});

const buttonName = ref("Save");
const validationError = ref(null);
const isLoading = ref(false);

// ✅ Methods
const save = async () => {
    const code = form.color_code.trim();
    
    // ✅ Cleaned up validation logic
    if (!code.startsWith('#') || code.length !== 7) {
        Mixin.methods.successMessage({ status: 'error', message: 'Enter a valid Color Code (e.g., #000000)!' });
        return;
    }

    isLoading.value = true;
    buttonName.value = "Saving...";

    try {
        const response = await axios.post(`${base_url}admin/product-color`, form);
        
        if (response.data.status === 'success') {
            if (typeof $ !== 'undefined') {
                $('#modal-form').modal('hide');
            }
            resetForm();
            EventBus.$emit('color-created');
            Mixin.methods.successMessage(response.data);
            buttonName.value = "Save";
        } else {
            Mixin.methods.successMessage(response.data);	
            buttonName.value = "Save";
        }
    } catch (err) {
        if (err.response?.status === 422) {
            validationError.value = err.response.data.errors;
            Mixin.methods.validationError?.();
            buttonName.value = "Save";
        } else {
            Mixin.methods.successMessage(err);
            buttonName.value = "Save";
        }
    } finally {
        isLoading.value = false;
    }
};

const resetForm = () => {
    form.name = '';
    form.color_code = '';
    validationError.value = null;
};
</script>

<style scoped>
/* Responsive modal fix for mobile */
@media screen and (max-width: 573px) {
    .modal-custom {
        max-width: 100% !important;
        background-color: #000 !important;
    }
}
</style>