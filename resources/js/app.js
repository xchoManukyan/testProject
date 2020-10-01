import Vuetify from "vuetify";
import Vue from 'vue';
import VeeValidate from 'vee-validate';
import '@mdi/font/css/materialdesignicons.css'
require('./bootstrap');

window.Vue = require('vue');
Vue.config.devtools = true;
Vue.prototype.$lodash = _;
Vue.prototype.$csrf = csrf;

Vue.use(Vuetify);
Vue.use(VeeValidate);

const csrf = document.head.querySelector('meta[name="csrf-token"]').getAttribute('content');

import DashboardHeader from './components/DashboardHeader.vue';
import Manager from './views/Manager';
import User from "./views/User";

Vue.component('dashboard-header', DashboardHeader)
Vue.component('manager', Manager)
Vue.component('user', User)


const app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),

    created() {
    },

    mounted() {
    },
});
