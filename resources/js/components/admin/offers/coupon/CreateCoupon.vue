<template>
    <div id="modal-form" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="m-t-none m-b">Add Coupon</h3>
                    <button type="button" class="btn btn-default text-right" data-dismiss="modal">X</button>
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

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Coupon Code *</label> 
                                    <input type="text" v-model="form.coupon_code" class="form-control" placeholder="Coupon Code" required />
                                </div>
                            </div>						
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Amount Type *</label> 
                                    <select class="form-control" v-model="form.amount_type" @change="visibilityMaxAmount" required>
                                        <option value="">Select Type</option>
                                        <option value="1">Amount</option>
                                        <option value="2">%</option>
                                    </select>
                                </div>
                            </div>	

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Amount *</label> 
                                    <input type="number" step="0.01" v-model="form.amount" class="form-control" placeholder="Coupon Amount" required />
                                </div>
                            </div>	

                            <!-- ✅ Only shows if Percentage is selected -->
                            <div class="col-md-12" v-if="visibleMaxAmount">
                                <div class="form-group">
                                    <label>Max Amount Limit *</label> 
                                    <input type="number" step="0.01" v-model="form.limit_amount" class="form-control" placeholder="Maximum Amount Limit" />
                                </div>
                            </div>	

                            <div class="col-md-12">
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
const buttonName = ref("Save");
const validationError = ref(null);
const visibleMaxAmount = ref(false);

const getDefaultForm = () => ({
    coupon_code: '',  
    amount_type: '',  
    amount: '',  
    limit_amount: '', 
    valid_date: '',
    status: 1,
});

const form = reactive(getDefaultForm());

// ✅ Methods
const visibilityMaxAmount = () => {
    // Show max amount limit only if "Percentage" (value 2) is selected
    visibleMaxAmount.value = (form.amount_type === '2' || form.amount_type === 2);
};

const save = async () => {
    isSaving.value = true;
    buttonName.value = "Saving...";

    try {
        const response = await axios.post(`${base_url}admin/coupon`, form);
        
        if (response.data.status === 'success') {
            if (typeof $ !== 'undefined') {
                $('#modal-form').modal('hide');
            }
            resetForm();
            EventBus.$emit('coupon-created');
            Mixin.methods.successMessage(response.data);
        } else {
            Mixin.methods.successMessage(response.data);	
        }
        buttonName.value = "Save";
    } catch (err) {
        if (err.response?.status === 422) {
            validationError.value = err.response.data.errors;
            Mixin.methods.validationError?.();
        } else {
            Mixin.methods.successMessage(err);
        }
        buttonName.value = "Save";
    } finally {
        isSaving.value = false;
    }
};

const resetForm = () => {
    Object.assign(form, getDefaultForm());
    visibleMaxAmount.value = false;
    validationError.value = null;
};

// ✅ Lifecycle Hooks
onMounted(() => {
    // Reset form cleanly when bootstrap modal is fully hidden
    if (typeof $ !== 'undefined') {
        $('#modal-form').on('hidden.bs.modal', resetForm);
    }
});

onBeforeUnmount(() => {
    // Clean up jQuery listener to prevent memory leaks
    if (typeof $ !== 'undefined') {
        $('#modal-form').off('hidden.bs.modal', resetForm);
    }
});
</script>