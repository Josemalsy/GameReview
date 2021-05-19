import Vue from 'vue';
import Router from 'vue-router';
import Usuarios from './views/usuarios';
import Games from './views/Games';
import Game from './views/Game';
import Usuario from './views/Usuario';
import Settings from './views/Settings';
import Mensajes from './views/Mensajes';
import MensajePrivado from './views/MensajePrivado';
import Reviews from './views/Reviews';
import Juegos from './views/Juegos';
import Stats from './views/Stats';


import Error404 from './views/Error404';

Vue.use(Router);

export default new Router({
  props : ['current_user'],
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
				path: '/usuario/:id/reviews',
				name: 'reviews',
				component: Reviews,
				props: true
			},
			{
				path: '/usuario/:id/juegos',
				name: 'juegosPersonales',
				component: Juegos,
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
        path: '/settings',
        name: 'configuracion',
				component: Settings,
				// meta: {    requiresAuth: true   },
			},
			{
        path: '/mensajes',
        name: 'mensajes privados',
				component: Mensajes,
				// meta: {    requiresAuth: true   },
			},
			{
				path: '/mensajes/leer_mensaje',
				name: 'leer mensaje',
				component: MensajePrivado,
				// meta: {    requiresAuth: true   },
			},
			{
				path: '/stats',
				name: 'stats',
				component: Stats,
			},
			{
				path: '*',
				component: Error404
			},
    ],
});

