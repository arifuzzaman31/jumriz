import { base_url } from './vue-assets';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import VueLazyload from 'vue3-lazyload';

import CreateColor from './components/admin/product/color/CreateColor.vue';
import ViewColor from './components/admin/product/color/ViewColor.vue';

import CreateSize from './components/admin/product/size/CreateSize.vue';
import ViewSize from './components/admin/product/size/ViewSize.vue';

import CreateAdmin from './components/admin/admin/CreateAdmin.vue';
import ViewAdmin from './components/admin/admin/ViewAdmin.vue';

import CreateCategory from './components/admin/category/CreateCategory.vue';
import ViewCategory from './components/admin/category/ViewCategory.vue';

import CreateBrand from './components/admin/brand/CreateBrand.vue';
import ViewBrand from './components/admin/brand/ViewBrand.vue';

import CreateSubCategory from './components/admin/subcategory/CreateSubCategory.vue';
import ViewSubCategory from './components/admin/subcategory/ViewSubCategory.vue';

import CreateSubSubCategory from './components/admin/subsubcategory/CreateSubSubCategory.vue';
import ViewSubSubCategory from './components/admin/subsubcategory/ViewSubSubCategory.vue';

import CreateProduct from './components/admin/product/CreateProduct.vue';
import ViewProduct from './components/admin/product/ViewProduct.vue';

import CreateCampaign from './components/admin/offers/campaign/CreateCampaign.vue';
import ViewCampaign from './components/admin/offers/campaign/ViewCampaign.vue';

// Slider
import CreateSlider from './components/admin/offers/slider/CreateSlider.vue';
import ViewSlider from './components/admin/offers/slider/ViewSlider.vue';


// Coupon
import CreateCoupon from './components/admin/offers/coupon/CreateCoupon.vue';
import ViewCoupon from './components/admin/offers/coupon/ViewCoupon.vue';

// Customer Coupon
import CreateCustomerCoupon from './components/admin/offers/customer_coupon/CreateCustomerCoupon.vue';
import ViewCustomerCoupon from './components/admin/offers/customer_coupon/ViewCustomerCoupon.vue';

import ViewOrder from './components/admin/order/ViewOrder.vue';
import viewAreaOrder from './components/admin/order/viewAreaOrder.vue';

import CreateRole from './components/admin/role/CreateRole.vue';
import ViewRole from './components/admin/role/ViewRole.vue';

import CreateCurrency from './components/admin/setting/currency/CreateCurrency.vue';
import ViewCurrency from './components/admin/setting/currency/ViewCurrency.vue';

import SeoSetting from './components/admin/setting/seo/SeoSetting.vue';
import ShopSetting from './components/admin/setting/shop/ShopSetting.vue';
import TrialSetting from './components/admin/setting/trial/TrialSetting.vue';
import ShippingSetting from './components/admin/setting/shipping/shippingSetting.vue';
// import PwaSetting from './components/admin/setting/pwa/PwaSetting.vue';
import DeliveryDateSlot from './components/admin/setting/slot/DeliveryDateSlot.vue';
import CreateTimeSlot from './components/admin/setting/slot/CreateTimeSlot.vue';
import ViewTimeSlot from './components/admin/setting/slot/ViewTimeSlot.vue';
// Initialize the Vue 3 Application instance
const app = createApp({});

// Initialize Pinia
app.use(createPinia());

// Register Global Components on the instance
app.component('create-color', CreateColor);
app.component('view-color', ViewColor);

app.component('create-size', CreateSize);
app.component('view-size', ViewSize);

app.component('create-admin', CreateAdmin);
app.component('view-admin', ViewAdmin);
app.component('create-category', CreateCategory);
app.component('view-category', ViewCategory);
app.component('create-brand', CreateBrand);
app.component('view-brand', ViewBrand);
app.component('create-subcategory', CreateSubCategory);
app.component('view-subcategory', ViewSubCategory);
app.component('create-subsubcategory', CreateSubSubCategory);
app.component('view-subsubcategory', ViewSubSubCategory);
app.component('create-product', CreateProduct);
app.component('view-product', ViewProduct);

app.component('create-campaign', CreateCampaign);
app.component('view-campaign', ViewCampaign);

app.component('create-slider', CreateSlider);
app.component('view-slider', ViewSlider);

app.component('create-coupon', CreateCoupon);
app.component('view-coupon', ViewCoupon);

app.component('create-customer-coupon', CreateCustomerCoupon);
app.component('view-customer-coupon', ViewCustomerCoupon);

app.component('view-order', ViewOrder);
app.component('area-order', viewAreaOrder);

app.component('create-role', CreateRole);
app.component('view-role', ViewRole);

app.component('seo-setting', SeoSetting);
app.component('shop-setting', ShopSetting);
app.component('trial-setting', TrialSetting);
app.component('shipping-setting', ShippingSetting);
// app.component('pwa-setting', PwaSetting);
app.component('date-slot-setting', DeliveryDateSlot);
app.component('create-time-slot', CreateTimeSlot);
app.component('view-time-slot', ViewTimeSlot);
app.component('create-currency', CreateCurrency);
app.component('view-currency', ViewCurrency);
// Inject plugins using the new app.use() chain syntax
app.use(VueLazyload, {
    loading: base_url + '/images/loading.gif',
    error: base_url + '/images/error.png',
});

// Mount to the DOM container element
app.mount('#wrapper');