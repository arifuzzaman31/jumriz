<template>
    <div id="update-coupon" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="m-t-none m-b">Update Coupon</h3>
                    <button type="button" class="btn btn-default text-right" data-dismiss="modal">X</button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="update">
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Coupon Code *</label> 
                                    <input type="text" v-model="form.coupon_code" class="form-control" placeholder="Coupon Code" required />
                                </div>
                            </div>						
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Amount Type *</label> 
                                    <select class="form-control" v-model="form.amount_type" required>
                                        <option value="">Select Type</option>
                                        <option value="1">Amount</option>
                                        <option value="2">%</option>
                                    </select>
                                </div>
                            </div>	

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Amount *</label> 
                                    <input type="number" step="0.01" v-model="form.amount" class="form-control" placeholder="Coupon Amount" required />
                                </div>
                            </div>	

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Max Amount *</label> 
                                    <input type="number" step="0.01" v-model="form.max_amount_limit" class="form-control" placeholder="Maximum Amount" required />
                                </div>
                            </div>	

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Valid Date *</label> 
                                    <!-- ✅ Replaced v2-datepicker with standard HTML5 date input -->
                                    <input type="date" v-model="form.valid_date" class="form-control" required />
                                </div>
                            </div>	
                        </div>			

                        <div class="row" style="margin-top: 20px;">								
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary" :disabled="isSaving">
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
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';
import { EventBus, base_url } from '../../../../vue-assets';
import { useMixin } from '../../../../mixin';

// ✅ State
const isSaving = ref(false);
const buttonName = ref("Update");
const validationError = ref(null);

const getDefaultForm = () => ({
    id: '',  
    coupon_code: '',  
    amount_type: '',  
    amount: '',  
    max_amount_limit: '', 
    valid_date: '',
    status: 1,
});

const form = reactive(getDefaultForm());

// ✅ Methods
const update = async () => {
    isSaving.value = true;
    buttonName.value = "Updating...";

    try {
        const response = await axios.put(`${base_url}admin/coupon/${form.id}`, form);
        
        if (response.data.status === 'success') {
            if (typeof $ !== 'undefined') {
                $('#update-coupon').modal('hide');
            }
            resetForm();
            Mixin.methods.successMessage(response.data);
            EventBus.$emit('coupon-created');
        } else {
            Mixin.methods.successMessage(response.data);	
        }
        buttonName.value = "Update";
    } catch (err) {
        if (err.response?.status === 422) {
            validationError.value = err.response.data.errors;
            Mixin.methods.validationError?.();
        } else {
            Mixin.methods.successMessage(err);
        }
        buttonName.value = "Update";
    } finally {
        isSaving.value = false;
    }
};

const resetForm = () => {
    Object.assign(form, getDefaultForm());
    validationError.value = null;
};

// ✅ Event Handlers
const handleUpdateCoupon = (value) => {
    Object.assign(form, value); // Safely populate form with coupon data
    
    if (typeof $ !== 'undefined') {
        $('#update-coupon').modal('show');
    }
};

// ✅ Lifecycle Hooks
onMounted(() => {
    EventBus.$on('update-coupon', handleUpdateCoupon);
});

onBeforeUnmount(() => {
    EventBus.$off('update-coupon', handleUpdateCoupon);
});
</script>