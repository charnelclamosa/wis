import Vue from "vue";
import Vuetify from "vuetify";
import App from "./components/App.vue";
import Routes from "./router";
import VueRouter from "vue-router";
import store from "./store";
import Vuelidate from 'vuelidate'
import Notifications from 'vue-notification';
import "@mdi/font/css/materialdesignicons.css";
import 'vuetify/dist/vuetify.min.css';

window.Vue = require('vue').default;
window.axios = require('axios');
Vue.use(Vuetify, {
    icons: {
        iconfont: "mdi"
    }
});
Vue.use(VueRouter);
Vue.use(Vuelidate);
Vue.use(Notifications);
Vue.prototype.$url = window.location.origin
Vue.component('primary-btn', require('./components/base/PrimaryButton').default);
Vue.component('block-btn', require('./components/base/BlockButton').default);
Vue.component('outline-btn', require('./components/base/OutlineButton').default);
Vue.component('progress-bar', require('./components/base/ProgressBar').default);
Vue.component('card-progress-bar', require('./components/base/CardProgressBar').default);
Vue.component('snack-bar', require('./components/base/SnackBar').default);
Vue.component('dashboard-card', require('./components/base/DashboardCard').default);
Vue.component('manual-add-bundle', require('./components/transaction/ManualAddBundle').default);

const app = new Vue({
    el: '#app',
    store,
    router: Routes,
    vuetify: new Vuetify({theme: {themes: {light: {primary: '#033E44', secondary: '#0ABCD0'}}}}),
    render: h => h(App)
});

export default app;

