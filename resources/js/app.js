require('./bootstrap');
window.Vue = require('vue').default;

Vue.component('app', require('./components/AppComponent.vue').default);
Vue.component('Loading', require('./components/LoadingComponent.vue').default);
Vue.component('Sidebar', require('./components/SidebarComponent.vue').default);
Vue.component('menuMensajes', require('./components/menuMensajesComponent.vue').default);

//MODALES
	//REVIEW
Vue.component('modal-review', require('./components/modals/review.vue').default);
Vue.component('modal-RevisarReview', require('./components/modals/RevisarReview.vue').default);
	//PERFIL
Vue.component('modal-editProfile', require('./components/modals/editProfile.vue').default);
	//JUEGOS
Vue.component('modal-AddGame', require('./components/modals/AddGame.vue').default);
	//MENSAJES
Vue.component('modal-enviarMensaje', require('./components/modals/MensajesModal.vue').default);
	//Genero
Vue.component('modal-generoModal', require('./components/modals/GeneroModal.vue').default);
	//Plataforma
Vue.component('modal-plataformaModal', require('./components/modals/PlataformaModal.vue').default);
	//Expulsion
Vue.component('modal-expulsionModal', require('./components/modals/ExpulsionModal.vue').default);
	//Expulsion
Vue.component('modal-historialExpulsiones', require('./components/modals/HistorialExpulsiones.vue').default);


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

const sidebar = new Vue({
	el: '#sidebar',
});

const app = new Vue({
    el: '#app',
    router
});
