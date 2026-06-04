import './vue-assets';
import { createApp } from 'vue';
import store from "./store/index";
import VueLazyload from 'vue3-lazyload';

// Register Global Components
const app = createApp({});

app.component('category-subcategory', require('./components/front/category/CategorySubCategory.vue').default);
app.component('category-product', require('./components/front/category/CategoryProduct.vue').default);
app.component('home-offers', require('./components/front/offer/HomeOffer.vue').default);
app.component('hot-deal', require('./components/front/product/HotDeal.vue').default);
app.component('on-sale-product', require('./components/front/product/onSaleProduct.vue').default);
// cart
app.component('cart', require('./components/front/cart/Cart.vue').default);
app.component('checkout-cart', require('./components/front/cart/CheckoutCart.vue').default);

app.component('search-box', require('./components/front/search/SearchBox.vue').default);
app.component('search-product', require('./components/front/product/SearchProduct.vue').default);

app.component('user-subscribe', require('./components/front/subscribe/Subscribe.vue').default);
app.component('all-offers', require('./components/front/offer/AllOffer.vue').default);
app.component('offers-product', require('./components/front/offer/OfferProduct.vue').default);
app.component('level-three-category', require('./components/front/category/LevelThreeCategory.vue').default);
app.component('sub-category-product', require('./components/front/product/SubCategoryProduct.vue').default);
app.component('sub-sub-category-product', require('./components/front/product/SubSubCategoryProduct.vue').default);
app.component('product-details', require('./components/front/product/ProductDetails.vue').default);
app.component('add-to-cart-modal', require('./components/front/product/AddToCartModal.vue').default);

app.component('category-products', require('./components/front/product/CategoryProduct.vue').default);

app.component('product-by-category', require('./components/front/product/ProductByCategory.vue').default);

// verification 

app.component('verification', require('./components/front/setting/Verification.vue').default);

// profile 

app.component('order-history', require('./components/front/user/Orders.vue').default);
app.component('order-tracking', require('./components/front/user/OrderTrack.vue').default);

app.component('profile-update', require('./components/front/profile/ProfileUpdate.vue').default);
app.component('user-dashboard', require('./components/front/profile/UserDashboard.vue').default);

// flutter wave payment 

app.component('flutter-wave-payment', require('./components/front/payment/FlutterWavePayment.vue').default);

// Inject plugins using the new app.use() chain syntax
app.use(store);
app.use(VueLazyload, {
    loading: base_url + 'images/loading.gif',
});

// Mount to the DOM container element
app.mount('#front-wrapper');