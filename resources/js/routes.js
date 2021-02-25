import Vue from 'vue';
import Router from 'vue-router';
import Home from './views/Home';
import Usuarios from './views/usuarios';
import Game from './views/Game';
import Error404 from './views/Error404';

Vue.use(Router);

export default new Router({
    mode: 'history',
    routes: [
        {
            path: '/usuarios',
            name: 'usuarios',
            component: Usuarios
        },
        {
            path: '/',
            name: 'games',
            component: Game
        },
        {
            path: ':id',
            name: 'game',
            component: Game,
            props: true
        },
        {
            path: '*',
            component: Error404
        },

    ],
});

