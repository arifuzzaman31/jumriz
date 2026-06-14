<template>
    <div class="row">
    <div class="col-sm-8 b-r"><h3 class="m-t-none m-b">Update Setting</h3>
        <form @submit.prevent="save()" role="form">
            <div class="form-group">
                <label>Og Title*</label> 
                <input v-model="form.title" type="text" placeholder="og : Title" class="form-control">
            </div>                                  

            <div class="form-group">
                <label>Sitemap Link</label> 
                <input v-model="form.sitemap_link" type="text" placeholder="Sitemap Link" class="form-control">
            </div>                                
            <div class="form-group">
                <label>Author</label> 
                <input v-model="form.author" type="text" placeholder="Author" class="form-control">
            </div>
            <div class="form-group">
                <label>Keyword</label> 
                <multiselect v-model="form.seo_keyword" tag-placeholder="Add this as new tag" placeholder="Search or add a tag" label="keyword" track-by="id" :options="tags" :multiple="true" :taggable="true" @tag="addTag"></multiselect>
            </div>                                   
            <div class="form-group">
                <label>Meta Image</label> <br>
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <span class="btn btn-block btn-primary btn-file"><span class="fileinput-new"><i class="fa fa-camera"></i> Change Image</span>
                    <span class="fileinput-exists">Change Image</span><input type="file" name="..." @change="onImageChange"/></span>
              </div> 
              </div>

            <div class="form-group">
                <label>Description</label> 
                <textarea class="form-control" v-model="form.description"></textarea>
            </div>

            <div class="form-group">
                <label>Google Map</label> 
                <textarea rows="4" class="form-control" v-model="form.google_map"></textarea>
            </div>        


                <div style="margin-bottom: 20px;">
                    <button  class="btn btn-lg  btn-primary float-right " type="submit"><strong>{{ button_name }}</strong></button>
                </div>
                </form>
                </div>
                <div class="col-sm-4"><h4>Seo Image Preview</h4>

                    <p class="text-center" v-if="form.meta_image">
                        <img class="img-responsive img-fluid" :src="form.meta_image">
                    </p>                                        

                    <p class="text-center" v-else>
                        <img class="img-responsive img-fluid" :src="url+'images/setting/seo/'+form.view_image">
                    </p>

                </div>

                <div class="col-md-12" v-if="validation_error" style="margin-top: 20px;margin-bottom: 10px;">
                    <div class="form-group">

                        <div >
                            <ul>
                                <li class="text-danger" v-for="error in validation_error">{{ error[0] }}</li>
                            </ul>
                        </div>

                    </div>
                </div>
        </div>
</template>
<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import Multiselect from 'vue-multiselect';
import { emitter, base_url } from '../../../../vue-assets';
import { useCommonActions } from '../../../../useCommonActions';

const { successMessage, validationError } = useCommonActions();

const form = ref({
    title: '',
    meta_image: '',
    view_image: '',
    sitemap_link: '',
    author: '',
    description: '',
    google_map: '',
    seo_keyword: [],
});

const isLoading = ref(false);
const button_name = ref('Update');
const url = base_url;
const validation_error = ref(null);
const tags = ref([]);

const onImageChange = (e) => {
    let files = e.target.files || e.dataTransfer.files;
    if (!files.length) return;
    createImage(files[0]);
};

const createImage = (file) => {
    let reader = new FileReader();
    reader.onload = (e) => {
        form.value.meta_image = e.target.result;
    };
    reader.readAsDataURL(file);
};

const getSetting = () => {
    axios.get(base_url + 'admin/setting/seo/' + 5 + '/edit')
        .then(response => {
            form.value.title = response.data.title;
            form.value.view_image = response.data.meta_image;
            form.value.sitemap_link = response.data.sitemap_link;
            form.value.author = response.data.author;
            form.value.description = response.data.description;
            form.value.google_map = response.data.google_map;
            form.value.seo_keyword = response.data.seo_keyword;
            tags.value = response.data.seo_keyword;
        });
};

const addTag = (newTag) => {
    const tag = {
        keyword: newTag,
        id: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
    }
    tags.value.push(tag);
    form.value.seo_keyword.push(tag);
};

const save = () => {
    button_name.value = "Updating...";
    axios.post(base_url + 'admin/setting/seo', form.value)
        .then(response => {
            if (response.data.status === 'success') {
                successMessage(response.data);
                emitter.emit('seo-created');
                button_name.value = "Update";
                validation_error.value = null;
            } else {
                successMessage(response.data);
                button_name.value = "Update";
            }
        })
        .catch(err => {
            if (err.response && err.response.status == 422) {
                validation_error.value = err.response.data.errors;
                validationError();
                button_name.value = "Update";
            } else {
                successMessage(err);
                button_name.value = "Update";
            }
        });
};

onMounted(() => {
    getSetting();
    emitter.on('seo-created', () => {
        getSetting();
    });
});

onUnmounted(() => {
    emitter.off('seo-created');
});
</script>