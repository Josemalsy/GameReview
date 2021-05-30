import Vue from 'vue';
import Router from 'vue-router';
import Games from './views/Games';
import Game from './views/Game';
import Usuarios from './views/usuarios';
import Usuario from './views/Usuario';
import ListaGeneros from './views/ListaGeneros';
import ListaPlataformas from './views/ListaPlataformas';
import listaUsuarios from './views/listaUsuarios';
import Mensajes from './views/Mensajes';
import MensajePrivado from './views/MensajePrivado';
import Settings from './views/Settings';
import Reviews from './views/Reviews';
import Juegos from './views/Juegos';
import Stats from './views/Stats';


import Error404 from './views/Error404';
import Error403 from './views/Error403';
import Baneo from './views/Baneo';


Vue.use(Router);

export default new Router({
  props : ['current_user'],
  mode: 'history',
	routes: [
		{
			path: '/',
			name: 'games',
			component: Games,
			meta: { title: 'Juegos' }
		},
		{
			path: '/game/:id',
			name: 'game',
			component: Game,
			props: true
		},
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
			props: true,
		},
		{
			path: '/usuario/:id/juegos',
			name: 'juegosPersonales',
			component: Juegos,
			props: true
		},
		{
			path: '/staff_tools',
			name: 'staff_tools',
			component: Settings,
		},
		{
			path: '/mensajes',
			name: 'mensajes privados',
			component: Mensajes,
		},
		{
			path: '/mensajes/leer_mensaje',
			name: 'leer mensaje',
			component: MensajePrivado,
		},
		{
			path: '/staff_tools/listaGeneros',
			name: 'lista generos',
			component: ListaGeneros,
		},
		{
			path: '/staff_tools/listaPlataformas',
			name: 'lista plataformas',
			component: ListaPlataformas,
		},
		{
			path: '/staff_tools/listaUsuarios',
			name: 'lista usuarios',
			component: listaUsuarios,
		},
		{
			path: '/stats',
			name: 'stats',
			component: Stats,
		},
		{
			path: '/baneo',
			name: 'baneo',
			component: Baneo,
		},
		{
			path: '*',
			component: Error404,
		},
		{
			path: '/forbidden',
			component: Error403,
		},
  ],
});

