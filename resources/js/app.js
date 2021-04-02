require('./bootstrap');
window.Vue = require('vue').default;

Vue.component('app', require('./components/AppComponent.vue').default);
Vue.component('Loading', require('./components/LoadingComponent.vue').default);
Vue.component('modal-review', require('./components/review.vue').default);

import Vue from 'vue'
import { BootstrapVue } from 'bootstrap-vue'
import router from './routes'
import './app.scss'

window.toastr = require('toastr');

Vue.use(BootstrapVue)


// EventBus Object
const EventBus = new Vue();
	Object.defineProperties(Vue.prototype, {
	$bus: {
		get() {
				return EventBus;
		},
	},
});

const app = new Vue({
    el: '#app',
    router
});
