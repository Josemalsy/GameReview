<template>
		<div class="cargando" v-if="loading"> <Loading/> </div>

		<div class="perfil" v-else>
			<div class="cabecera">{{usuario.name}}</div>
			<div class="principal">
				<div class="contenedor">
					<div class="columna-avatar">
						<img class="avatar" :src="'../storage/'+usuario.avatar" alt="Card image">
						<div class="expulsion" v-if="usuario.estado == 'Expulsado'">
							Expulsado hasta el {{usuario.fin_expulsion | formatDate}}
						</div>
						<template v-if="current_user.email_verified_at">
							<div class="editar-Perfil" v-if="current_user.id == id_user" v-b-modal="'modal-editProfile'">Editar perfil <modal-editProfile :current_user="current_user"/></div>
							<p><div class="editar-Perfil" v-b-modal="'modal-enviarMensaje'"> Mensaje privado <modal-enviarMensaje :current_user="current_user" :nombreDestino=usuario.name :receptor_id=id_user :tituloModal="'Mandar mensaje'" /></div></p>
							<p><div class="editar-Perfil"><router-link :to="{ name: 'reviews', query: {user_id:id_user}}">Mostrar reseñas </router-link> </div></p>
							<div class="editar-Perfil"> <router-link :to="{ name: 'juegosPersonales', query: {user_id:id_user}}">Lista de juegos </router-link> </div>
							<p><div class="editar-Perfil" style="background-color: red" v-if="current_user.rol == 'Administrador' && usuario.rol == 'Usuario'" v-b-modal="'modal-expulsionModal'"><modal-expulsionModal :user_id="usuario.id" :estado="usuario.estado" />Expulsar usuario </div></p>
							<div class="editar-Perfil" style="background-color: red" v-b-modal="'modal-historialExpulsiones'" v-if="current_user.rol == 'Administrador' || current_user.id == id_user"> Historial de expulsiones <modal-historialExpulsiones :user_id="id_user"/> </div>
					</template>
					<template v-else>
						<div class="editar-Perfil" style="background-color: #2B4562"> Valida tu email para ver las reseñas y juegos de este usuario </div>

					</template>
					</div>
						<div class="contentDatos">
						<div class="datosPersonales">
							<div v-if="usuario.ubicacion"><strong> Ubicacion:</strong> {{usuario.ubicacion}} </div>
							<div ><strong>Edad:  </strong> {{usuario.FecNac | calculaEdad}} </div>
							<div v-if="usuario.aficiones"><strong> Aficiones: </strong> {{usuario.aficiones}}</div>
							<div v-if="usuario.ocupacion"><strong> Ocupacion: </strong> {{usuario.ocupacion}}  </div>
							<div> <strong> Miembro desde: </strong> {{usuario.created_at | formatDate }} </div>
						</div>
					</div>
				</div>
					<div class="biografia" v-if="usuario.biografia">
						<strong> biografia: </strong>
						<p>{{usuario.biografia}}</p>
					</div>
				<div class="divisor">Datos de juego</div>

				<div class="juego-stats">
					<div class="stats"> Juegos adquiridos: <p> {{usuario.games.length}} </p></div>
					<div class="stats"> Reviews realizadas<p> {{usuario.reviews.length}}</p></div>
					<div class="stats"> Media de valoraciones <p> {{usuario.reviews_avg_puntuacion | roundValors}}  </p> </div>
					<div class="stats">	Ultimo juego adquirido: <p v-if="usuario.games.length > 0">{{usuario.games[usuario.games.length -1].titulo}}</p><p v-else> No ha adquirido juegos </p></div>
				</div>


			<div class="estadisticas">
				<button class="generos btn btn-primary" @click="getDatosGeneros">
					<template v-if="!generosMostrar">Mostrar gráfico de generos</template><template v-else> Ocultar gráfico de generos</template>
				</button>

				<template v-if="generosMostrar && loadingGeneros == false"><HorizontBar-chart class="generos-grafico" :chart-data="dataGeneros" :options="optionsGeneros" :height="150"></HorizontBar-chart></template>

				<button class="plataformas btn btn-success" @click="getDatosPlataformas">
					<template v-if="!plataformasMostrar"> Mostrar gráfico de plataformas</template> <template v-else> Ocultar gráfico de plataformas</template>
				</button>

				<template v-if="plataformasMostrar && loadingPlataformas == false"><HorizontBar-chart class="generos-grafico" :chart-data="dataPlataformas" :options="optionsPlataformas" :height="150"></HorizontBar-chart></template>

			</div>

			</div>
		</div>
</template>

<script>
import HorizontBarChart from '../components/chartJs/HorizontBarChart.js'
import moment from 'moment'

export default {
  components: {
    HorizontBarChart
  },
  props : ['current_user'],
	mounted() {

		this.obtenerDatos()
		this.$bus.$on('prueba',this.obtenerDatos)

	},
	data() {
		return {
			loading: true,
			loadingGeneros: true,
			loadingPlataformas: true,
			id_user: this.$route.params.id,
			generosMostrar: false,
			cargarGeneros: false,
			plataformasMostrar: false,
			cargarPlataformas: false,
			usuario: [],
      dataGeneros: null,
			valoresGeneros: [],
			generos: [],
			plataformas: [],
			valoresPlataformas: [],

			optionsGeneros: {
				responsive: true,
				maintainAspectRatio: false,
				aspectRatio: 2,
				scales: {
					xAxes: [{
						ticks: {
							stepSize: 5,
							fixedStepSize: 1,
							beginAtZero: true,
						},
					}],
					yAxes: [{
						ticks:{
							beginAtZero: true,
							stepSize: 5,
							fixedStepSize: 1,
						},
					}]
				}
			},

			optionsPlataformas: {
				responsive: true,
				maintainAspectRatio: false,
				aspectRatio: 2,
				scales: {
					xAxes: [{
						ticks: {
							stepSize: 5,
							fixedStepSize: 1,
							beginAtZero: true,
						},
					}],
					yAxes: [{
						ticks:{
							beginAtZero: true,
							stepSize: 5,
							fixedStepSize: 1,
						},
					}]
				}
			}

		}
	},
	methods: {
		obtenerDatos(){
			this.loading =  true,
			axios.get('http://gamereviewsproject.herokuapp.com/api/usuario/' + this.id_user).then(response =>{
				this.usuario = response.data[0];
				this.loading = false
			}).catch(error =>{
				this.$router.push('/user_not_found');
			});
		},
    getDatosGeneros(){
			this.generosMostrar = !this.generosMostrar
			if(this.cargarGeneros == false){
			this.loadingGeneros = true
				axios.get('http://gamereviewsproject.herokuapp.com/api/stats_user_genero/', {
					params: {
						user_id: this.id_user
					}
				}).then(response =>{
					let arraySize = response.data.length
					for(let x = 0; x < arraySize; x++){
						this.generos.push(response.data[x].nombre)
						this.valoresGeneros.push(response.data[x].cantidad)
					}
					this.cargarGeneros = true
					this.loadingGeneros = false
				});
				this.dataGeneros = {
					labels: this.generos,
					datasets: [{
						axis: 'y',
						label: 'Cantidad',
						barThickness: 20,
						backgroundColor: '#0077B6',
						data: this.valoresGeneros,
					}]
				}
			}
    },
		getDatosPlataformas(){
			this.plataformasMostrar = !this.plataformasMostrar
			if(this.cargarPlataformas == false){
			this.loadingPlataformas = true
				axios.get('http://gamereviewsproject.herokuapp.com/api/stats_user_plataformas/', {
					params: {
						user_id: this.id_user
					}
				}).then(response =>{
					let arraySize = response.data.length
					for(let x = 0; x < arraySize; x++){
						this.plataformas.push(response.data[x].nombre)
						this.valoresPlataformas.push(response.data[x].cantidad)
					}
					this.cargarPlataformas = true
					this.loadingPlataformas = false
				});
				this.dataPlataformas = {
					labels: this.plataformas,
					datasets: [{
						axis: 'y',
						label: 'Cantidad',
						barThickness: 20,
						backgroundColor: '#218838',
						data: this.valoresPlataformas,
					}]
				}
			}
    }
	},
	filters: {
		formatDate(value){
      return moment(String(value)).format('DD-MM-YYYY h:mm')
    },
		calculaEdad(value) {
			return moment().diff(value,'years')
		},
		roundValors: function(value) {
			if (!value){
				return 'Sin datos'
			}
			value = parseFloat(value).toFixed(2)
			return value;
		},
	}
}
</script>

<style scoped>
	.perfil {
	display: flex;
	width: 80%;
	height: auto;
	flex-flow: row wrap;
	margin: 50px 10px 50px 10px;
}

.cabecera {
	display: flex;
	height: auto;
	margin-top: 10px;
	flex-flow: column wrap;
	margin: 5px;
	width: 100%;
	padding: 10px;
	font-size: 25px;
	background: #013a63;
	color: white;
}

.cabecera, .divisor {
	text-align: center;
}

.cabecera, .divisor, .stats {
	border-radius: 4px;
}

.contenedor {
	display: flex;
	flex-flow: row wrap;
}

.columna-avatar {
	display: flex;
	height: auto;
	margin: 5px;
	padding: 10px;
	flex-flow: column wrap;
	order: 0;
	text-align: center;
	flex: 1;
}

.avatar{
	width: 90px;
	height: 90px;
	align-self: center;
}

.expulsion{
  display:flex;
  flex-flow: row;
  height: 20%;
  text-align: center;
  align-self:center;
  color: red;
}

.editar-Perfil, .editar-Perfil a {
	background: #0077b6;
	color: white;
	border-radius: 4px;
	text-decoration:none;
}

.principal {
	display: flex;
	width: 100%;
	margin-top: 10px;
	flex-flow: column wrap;
	justify-content: center;
}

.contentDatos {
	justify-content: center;
	flex: 8;
	display: grid;
	font-size: 16px;

}

.datosPersonales {
	justify-self: center;
}

.biografia {
	font-size: 16px;
	margin: 20px 0;
	width: 85%;
	align-self: center;
}

.biografia strong{
	font-size: 22px;
}


.divisor {
	display: flex;
	height: auto;
	flex-flow: column wrap;
	width: 100%;
	padding: 10px;
	font-size: 25px;
	color: white;
	background: #013a63;
}

.juego-stats {
	display:flex;
	flex-flow: row wrap;
	font-size: 20px;
	text-align: center;
	color: white;
	padding: 5px;
}

.stats {
	flex: 1;
	background: #0077B6;
	margin: 5px;
	display:flex;
	flex-flow: column wrap;
	justify-content: center;

}

.estadisticas{
	width: 100%;
	display:flex;
	flex-flow: column wrap;
}

.botones {
	display: flex;
	flex-flow: row wrap;
}

.generos{
	background: #0077B6;
}

.generos:hover{
	background: #00B4D8;
}

.generos, .plataformas{
	margin: 10px 0;
}

.generos-grafico{
	width: 100%;
	height: 500px;
	font-size: 14px;
}

@media (max-width: 700px) {
	.perfil {
		width: 100%;
	}

	.contenedor {
		flex-flow: column wrap;
	}

	.juego-stats {
		width: 100%;
	}

	.valoracion {
		width: 98%;
	}

}


</style>