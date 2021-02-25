require('./bootstrap');

window.Vue = require('vue').default;

Vue.component('app', require('./components/AppComponent.vue').default);
Vue.component('games', require('./components/GamesComponent.vue').default);

import router from './routes'

const app = new Vue({
    el: '#app',
    router
});

