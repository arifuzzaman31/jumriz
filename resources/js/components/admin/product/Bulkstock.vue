<template>
    <div id="modal-bulk" class="modal fade" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header ">
                    <h3 class="m-t-none m-b">Upload</h3>
                    <button class="btn btn-default text-right" data-dismiss="modal">X</button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-right">
                        <div class="col-md-12" v-if="validation_error" style="margin-top: 20px">
                            <div class="form-group">
                                <div>
                                    <ul>
                                        <li class="text-danger" v-for="error in validation_error">{{ error[0] }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                        <form @submit.prevent="uploadExcel()" role="form">
                            <div class="form-group">
                                <p>Download format and product list by filtering in <a :href="url+'admin/stock-report'">stock list report </a> <br> you will find a button download sample product list</p> <br>
                                <span class="btn btn-primary btn-file"><span class="fileinput-new" id="excel-file"><i class="fa fa-file-excel-o"></i> Chose Excel</span>
                                <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/></span>
                            </div>
                            <button type="submit" class="btn btn-info"><i class="fa fa-upload" aria-hidden="true"></i> {{ button_name }}</button>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import { EventBus } from  '../../../vue-assets';
    import Mixin from  '../../../mixin';
    export default {

        mixins : [Mixin],

        data(){

            return {
                file : '',
                attachment : '',
                upload_size : 0,
                url : base_url,
                button_name : "Upload",
                validation_error : null,
            }

        },

        mounted() {
            EventBus.$on('bulk-upload', this.openModal);
            if (typeof $ !== 'undefined') {
                $('#modal-bulk').on('hidden.bs.modal', this.resetForm);
            }
        },

        beforeUnmount() {
            EventBus.$off('bulk-upload', this.openModal);
            if (typeof $ !== 'undefined') {
                $('#modal-bulk').off('hidden.bs.modal', this.resetForm);
            }
        },

        methods : {
            openModal() {
                this.resetForm();
                if (typeof $ !== 'undefined') $('#modal-bulk').modal('show');
            },

            resetForm() {
                this.file = '';
                this.validation_error = null;
                this.button_name = "Upload";
                const excelLabel = document.getElementById("excel-file");
                if (excelLabel) {
                  excelLabel.innerHTML = '<i class="fa fa-file-excel-o"></i> Chose Excel';
                }
                if (this.$refs.file) {
                    this.$refs.file.value = null;
                }
            },

            handleFileUpload(){
                this.file = this.$refs.file.files[0];
                if (this.file) {
                    document.getElementById("excel-file").innerHTML = '<i class="fa fa-file-excel-o"></i> ' + this.file.name;
                }
            },

            uploadExcel() {
                this.button_name = "Uploading...";
                let formData = new FormData();
                formData.append('file', this.file);

                axios.post(base_url + 'admin/import', formData, {
                  headers: { 'Content-Type': 'multipart/form-data' }
                })
                .then(response => {
                  if (response.data.status === 'success') {
                    this.successMessage(response.data);
                    if (typeof $ !== 'undefined') $('#modal-bulk').modal('hide');
                    EventBus.$emit('product-created');
                  } else {
                    this.successMessage(response.data);
                  }
                  this.button_name = "Upload";
                  this.resetForm(); // reset after success
                })
                .catch(err => {
                  if (err.response && err.response.status === 422) {
                    this.validation_error = err.response.data.errors;
                    if (this.validationError) this.validationError(); // assuming this shows a toast
                  } else {
                    // Show a generic error message
                    this.successMessage({
                      status: 'error',
                      message: err.response?.data?.message || 'Something went wrong!'
                    });
                  }
                  this.button_name = "Upload";
                  // Do NOT set this.file = new FormData(); it's wrong.
                  // Instead, just reset the file input:
                  this.resetForm();
                });
            },
        }

    }

</script>