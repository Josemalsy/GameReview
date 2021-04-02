import Vue from 'vue';
import Router from 'vue-router';
import Usuarios from './views/usuarios';
import Games from './views/Games';
import Game from './views/Game';
import Usuario from './views/Usuario';

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
				path: '/usuario/:id',
				name: 'usuario',
				component: Usuario,
				props: true
			},
			{
				path: '/',
				name: 'games',
				component: Games
			},
			{
				path: '/game/:id',
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

