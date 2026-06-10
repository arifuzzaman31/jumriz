<template>
  <div class="row">
    <div class="col-sm-8 b-r">
      <h3 class="m-t-none m-b">Update Setting</h3>
      <form @submit.prevent="save()" role="form">
        <div class="form-group">
          <label>Shop Name *</label>
          <input
            v-model="form.shop_name"
            type="text"
            placeholder="Shop Name"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label>Shop Address *</label>
          <textarea
            v-model="form.address"
            placeholder="Address"
            class="form-control"
          ></textarea>
        </div>
        <div class="form-group">
          <label>Phone *</label>
          <input
            v-model="form.phone"
            type="text"
            placeholder="Phone No"
            class="form-control"
          />
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input
            v-model="form.email"
            type="text"
            placeholder="Email"
            class="form-control"
          />
        </div>

        
        <div class="form-group">
          <label>Return Policy</label>
          <vue-editor v-model="form.return_policy"></vue-editor>
        </div>
                

        <div class="form-group">
          <label
            >Theme Color (<span class="text-danger"
              >Remeber Your Color Will Combination with white text</span
            >)</label
          >
          <input
            type="text"
            v-model="form.theme_color"
            class="form-control"
            placeholder="Theme Color Code EX : #e3106e"
          />
        </div>

        <div class="form-group">
          <div class="fileinput fileinput-new" data-provides="fileinput">
            <span class="btn btn-block btn-primary btn-file"
              ><span class="fileinput-new"
                ><i class="fa fa-camera"></i> header logo</span
              >
              <span class="fileinput-exists">header logo</span
              ><input type="file" name="..." @change="onHeaderImageChange"
            /></span>
          </div>

          <div class="fileinput fileinput-new" data-provides="fileinput">
            <span class="btn btn-block btn-primary btn-file"
              ><span class="fileinput-new"
                ><i class="fa fa-camera"></i> footer logo</span
              >
              <span class="fileinput-exists">footer logo</span
              ><input type="file" name="..." @change="onFooterImageChange"
            /></span>
          </div>

          <div class="fileinput fileinput-new" data-provides="fileinput">
            <span class="btn btn-block btn-primary btn-file"
              ><span class="fileinput-new"
                ><i class="fa fa-camera"></i> Favicon</span
              >
              <span class="fileinput-exists">Favicon</span
              ><input type="file" name="..." @change="onFaviconChange"
            /></span>
          </div>
        </div>

        <div class="form-group">
          <label>Facebook Link</label>
          <input
            v-model="form.facebook_link"
            type="text"
            placeholder="Facebook Link"
            class="form-control"
          />
        </div>
        <div class="form-group">
          <label>Twitter Link</label>
          <input
            v-model="form.twitter_link"
            type="text"
            placeholder="Twitter Link"
            class="form-control"
          />
        </div>
        <div class="form-group">
          <label>Youtube Link</label>
          <input
            v-model="form.youtube_link"
            type="text"
            placeholder="Youtube Link"
            class="form-control"
          />
        </div>
        <div class="form-group">
          <label>Footer Text</label>
          <textarea
            v-model="form.footer_text"
            class="form-control"
            placeholder="Footer Text"
          ></textarea>
        </div>

        <div class="form-group">
          <label
            >Slider Status (<small
              >you can on or off showing slider on home page by this
              option</small
            >)</label
          >

          <select class="form-control" v-model="form.slider_status">
            <option value="1">Show Slider</option>
            <option value="0">Don't Show Slider</option>
          </select>
        </div>

        <div class="form-group">
          <label
            >Hot Deal Status (<small
              >you can on or off showing hot deal on home page by this
              option</small
            >)</label
          >

          <select class="form-control" v-model="form.hot_deal_status">
            <option value="1">Show Hot Deal</option>
            <option value="0">Don't Show Hot Deal</option>
          </select>
        </div>

        <div class="form-group">
          <label
            >On Sale Status (<small
              >you can on or off showing on sale section on home page by this
              option</small
            >)</label
          >

          <select class="form-control" v-model="form.onsale_status">
            <option value="1">Show on Sale Section</option>
            <option value="0">Don't Show on Sale Section</option>
          </select>
        </div>

        <div class="form-group">
          <label>Show Side Menubar (<small>you can on or off showing side menu by this
              option</small
            >)</label
          >

          <select class="form-control" v-model="form.sidemenu_status">
            <option value="1">Show Side Menubar</option>
            <option value="0">Don't Show Side Menubar</option>
          </select>
        </div>

        <div style="margin-bottom: 20px">
          <button
            style="margin-bottom: 20px"
            class="btn btn-lg btn-primary float-right"
            type="submit"
          >
            <strong>{{ button_name }}</strong>
          </button>
        </div>
      </form>
    </div>
    <div class="col-sm-4">
      <h4>Logo Header Preview</h4>

      <p class="text-center" :style="themBg" v-if="form.header_logo">
        <img class="img-responsive img-fluid" :src="form.header_logo" />
      </p>

      <p class="text-center" v-else :style="themBg">
        <img
          class="img-responsive img-fluid"
          :src="url + 'images/logo/' + form.header_logo_view"
        />
      </p>

      <h4>Logo Footer Preview</h4>

      <p class="text-center" v-if="form.footer_logo">
        <img class="img-responsive img-fluid" :src="form.footer_logo" />
      </p>

      <p class="text-center" v-else>
        <img
          class="img-responsive img-fluid"
          :src="url + 'images/logo/' + form.footer_logo_view"
        />
      </p>

      <h4>Favicon Preview</h4>

      <p class="text-center" v-if="form.favicon">
        <img class="img-responsive img-fluid" :src="form.favicon" />
      </p>

      <p class="text-center" v-else>
        <img
          class="img-responsive img-fluid"
          :src="url + 'images/logo/' + form.favicon_view"
        />
      </p>
    </div>

    <div
      class="col-md-12"
      v-if="validation_error"
      style="margin-top: 20px; margin-bottom: 20px"
    >
      <div class="form-group">
        <div>
          <ul>
            <li class="text-danger" v-for="(error,i) in validation_error" :key="i">
              {{ error[0] }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import axios from "axios";
import { emitter, base_url } from "../../../../vue-assets";
import { useCommonActions } from "../../../../useCommonActions";
import { VueEditor } from "vue2-editor";

const { successMessage, validationError } = useCommonActions();

const form = ref({
  shop_name: "",
  shop_short_name: "",
  address: "",
  phone: "",
  email: "",
  return_policy: "",
  footer_text: "",
  header_logo: "",
  footer_logo: "",
  header_logo_view: "",
  footer_logo_view: "",
  favicon: "",
  favicon_view: "",
  facebook_link: "",
  twitter_link: "",
  youtube_link: "",
  theme_color: "",
  hot_deal_status: "",
  slider_status: "",
  onsale_status: "",
  sidemenu_status: "",
});

const isLoading = ref(false);
const button_name = ref("Update");
const url = base_url;
const validation_error = ref(null);

const themBg = computed(() => {
  return {
    background: form.value.theme_color,
  };
});

const onHeaderImageChange = (e) => {
  let files = e.target.files || e.dataTransfer.files;
  if (!files.length) return;
  createheaderImage(files[0]);
};
const createheaderImage = (file) => {
  let reader = new FileReader();
  reader.onload = (e) => {
    form.value.header_logo = e.target.result;
  };
  reader.readAsDataURL(file);
};

const onFooterImageChange = (e) => {
  let files = e.target.files || e.dataTransfer.files;
  if (!files.length) return;
  createFooterImage(files[0]);
};
const createFooterImage = (file) => {
  let reader = new FileReader();
  reader.onload = (e) => {
    form.value.footer_logo = e.target.result;
  };
  reader.readAsDataURL(file);
};

const onFaviconChange = (e) => {
  let files = e.target.files || e.dataTransfer.files;
  if (!files.length) return;
  createFavicon(files[0]);
};
const createFavicon = (file) => {
  let reader = new FileReader();
  reader.onload = (e) => {
    form.value.favicon = e.target.result;
  };
  reader.readAsDataURL(file);
};

const getSetting = () => {
  axios
    .get(base_url + "admin/setting/shop/" + 1 + "/edit")
    .then((response) => {
      form.value.shop_name = response.data.shop_name;
      form.value.shop_short_name = response.data.shop_short_name;
      form.value.address = response.data.address;
      form.value.phone = response.data.phone;
      form.value.email = response.data.email;
      form.value.return_policy = response.data.return_policy;
      form.value.footer_text = response.data.footer_text;
      form.value.header_logo_view = response.data.logo_header;
      form.value.footer_logo_view = response.data.logo_footer;
      form.value.favicon_view = response.data.favicon;
      form.value.facebook_link = response.data.facebook;
      form.value.twitter_link = response.data.twitter;
      form.value.youtube_link = response.data.youtube;
      form.value.theme_color = response.data.theme_color;
      form.value.hot_deal_status = response.data.hot_deal_status;
      form.value.slider_status = response.data.slider_status;
      form.value.onsale_status = response.data.onsale_status;
      form.value.sidemenu_status = response.data.sidemenu_status;
    });
};

const save = () => {
  button_name.value = "Updating...";
  axios
    .post(base_url + "admin/setting/shop", form.value)
    .then((response) => {
      if (response.data.status === "success") {
        successMessage(response.data);
        emitter.emit("shop-created");
        button_name.value = "Update";
        validation_error.value = null;
      } else {
        successMessage(response.data);
        button_name.value = "Update";
      }
    })
    .catch((err) => {
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
  emitter.on("shop-created", () => {
    getSetting();
  });
});

onUnmounted(() => {
  emitter.off("shop-created");
});
</script>