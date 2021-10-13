import jQuery from 'jquery';
window.jQuery = window.$ = jQuery;
jQuery.noConflict();

import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter);
import App from './App'

import Vuex from 'vuex';
Vue.use(Vuex);

import VeeValidate from 'vee-validate';

Vue.use(VeeValidate, {
    events: 'input',
    fieldsBagName: '',
});

import StoreData from './store';
const store = new Vuex.Store(StoreData);

import BaseMixin from './mixins/base_mixin';
import HttpMixin from './mixins/http_mixin';
Vue.mixin(BaseMixin);
Vue.mixin(HttpMixin);

import admin_routes from './routes/admin_routes';

var allRoutes = [];
const routes = allRoutes.concat(admin_routes);

const router = new VueRouter({
    mode: 'history',
    routes: routes,
    linkActiveClass: "active",
});

import pagination from './plugins/pagination/pagination'
Vue.component('pagination', pagination);

import VueToastr from '@deveodk/vue-toastr';
import '@deveodk/vue-toastr/dist/@deveodk/vue-toastr.css'
Vue.use(VueToastr, {
    defaultPosition: 'toast-bottom-right',
    defaultType: 'info',
    defaultTimeout: 2000
});

import 'sweetalert2/dist/sweetalert2.min.css';
import VueSweetalert2 from 'vue-sweetalert2';
Vue.use(VueSweetalert2);

import axios from 'axios'
import VueAxios from 'vue-axios'
Vue.use(VueAxios, axios);

import {initialize} from './helpers/general';
initialize(store, router);

const app = new Vue({
    el: '#app',
    components: { App },
    router,store,axios
});