<template>
	<div class="cargando" v-if="loading"> <Loading/> </div>
	<div class="carta" v-else>

		<div class="izqda">
			<div class="caratula">
				<img class="imagen" :src="'../storage/'+ game.imagen" alt="Card image">
			</div>
			<template v-if="current_user.email_verified_at">
				<div class="send-review" style="background-color: #FF4500;" v-if="!current_user"> Logueate para enviar tu review <modal-review /></div>
				<div class="send-review" v-else-if="checkUserReview == false" v-b-modal="'review-modal'" current_user="'current_user'"> Añade tu revisión <modal-review :current_user="current_user" :tituloModal="'Enviar Reseña'"/></div>
				<div class="send-review" style="background-color: green;" v-b-modal="'review-modal'" v-else-if="reviewPendienteId">Valoracion realizada / Editarla <modal-review :current_user="current_user" :review_id="reviewPendienteId" :tituloModal="'Editar Reseña'" /></div>
			</template>
			<template v-else>
				<div class="send-review" style="background-color: #2B4562;"> Valida tu email para poder escribir reseñas</div>
			</template>
			<template v-if="avisoRechazo"><span class="aviso" >Ya se te rechazó una reseña. La acumulación de rechazos puede provocar la expulsión.</span></template>
				<div class="plataformas"><span class="etiqueta" v-for="(item,index) in game.plataformas" :style="{background: estableceFondo(item.fabricante)}">{{item.nombre}}</span></div>
		</div>
		<div class="principal">
			<div class="cabecera">
				<div class="juego-info">
					<div class="titulo">{{game.titulo}}</div> <div class="desarrolladora">{{game.desarrolladora}} </div> <div class="genero"><span v-for="(item,index) in game.generos"> {{item.nombre + ",&nbsp"}} </span></div>
				</div>
			</div>
			<div class="juego-stats">
				<div class="stats">	Lo han adquirido <p>{{game.users_count}}</p></div>
				<div class="stats"> Completado modo historia <p>{{juegoBase}}</p></div>
				<div class="stats"> Completado con extras <p>{{juegoExtendido}}</p></div>
				<div class="stats"> Superado al 100% <p> {{juegoTotal}} </p> </div>
				<div class="stats"> Reviews realizadas <p>{{game.reviews_count}}</p></div>
			</div>
			<div class="duraciones">
				<div class="duracion">Juego Base <p>{{game.juegoBase_media | roundValors}}H</p> </div>
				<div class="duracion"> Juego Base + extras <p>{{game.juegoExtendido_media | roundValors}}H</p> </div>
				<div class="duracion"> 100% <p>{{game.completadoTotal_media | roundValors}}H</p> </div>
			</div>
			<div class="valoracion">
				<div class="putuacion">Puntuacion: {{game.valoracion_media | roundValors}}%
					<div class="progress">
						<div class="progress-bar" role="progressbar" :style="{width: game.valoracion_media + '%', background: background_barra}" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
				</div>
			</div>

		<div class="estadisticas">
			<button class="puntuaciones btn btn-primary" @click="getDatosPuntuaciones">
				<template v-if="!puntuacionesMostrar">Mostrar gráfico de puntuaciones</template><template v-else> Ocultar gráfico de puntuaciones</template>
			</button>

			<template v-if="puntuacionesMostrar && loadingPuntuaciones == false">
				<Scatter-chart class="scatter-grafico" :chart-data="dataValoraciones" :options="optionsValoraciones" :height="450"></Scatter-chart>
			</template>

			<button class="plataformas btn btn-success" @click="getDatosPlataformas">
				<template v-if="!plataformasMostrar"> Mostrar gráfico de plataformas</template> <template v-else> Ocultar gráfico de plataformas</template>
			</button>

			<template v-if="plataformasMostrar && loadingPlataformas == false">
				<pie-chart :data="dataPlataformas" :options="optionsPlataformas" :height="350" class="pie-grafico"></pie-chart>
			</template>

		</div>

		<div class="separacion">
			<span class="separador" @click="muestraReviews">
				<template v-if="!reviews"> Mostrar reseñas </template>
				<template v-else> Ocultar reseñas</template>
			</span>
		</div>
		<template v-if="reviews">
			<div class="comentarios">
				<nav class="paginate-bottom" aria-label="Page navigation example">
					<ul class="pagination" v-for="n in ultima_pagina">
						<li class="page-item"><a class="page-link" :style="{background: fondoPaginas(n)}" @click="changePage( n )">{{ n }}</a></span></li>
					</ul>
				</nav>
				<div class="cargando" v-if="loadingComments"> <Loading/> </div>
				<div class="comment-box" v-for="item in reviewsList">
					<div class="lateralComment">
						<div class="nick"> <a :href="'/usuario/'+item.users.id"> {{item.users.name}} </a></div>
						<div class="avatar"><img class="caratula" :src="'../storage/'+ item.users.avatar" alt="Card image capaaaa"></div>
					</div>
					<div class="comment">
						<div class="comment-header">
							<div class="fechaEscrito"> Escrito el: {{item.created_at | formatDate}}</div>
							<div class="puntosPersonales">
								<div class="personalRating">Base <p>{{item.juegoBase | roundValors}}H</p></div>
								<div class="personalRating">Extras<p>{{item.juegoExtendido | roundValors}}H</p></div>
								<div class="personalRating">100% <p>{{item.completadoTotal | roundValors}}H</p></div>
								<div class="personalRating">Valoracion<p>{{item.puntuacion | roundValors}}%</p></div>
							</div>
						</div>
						<div class="comment-message" v-html="item.mensaje"></div>
					</div>
				</div>
				<nav class="paginate-bottom" aria-label="Page navigation example">
					<ul class="pagination" v-for="n in ultima_pagina">
						<li class="page-item"><a class="page-link" :style="{background: fondoPaginas(n)}" @click="changePage( n )">{{ n }}</a></span></li>
					</ul>
				</nav>
			</div>
		</template>
		</div>
	</div>
</template>

<script>
import moment from 'moment'
import ScatterChart from '../components/chartJs/ScatterChart.js'
import PieChart from '../components/chartJs/PieChart.js'

export default {
  components: {
    ScatterChart,
		PieChart
  },
  props : ['current_user'],
	mounted() {
		this.obtenerDatos()
		this.$bus.$on('prueba',this.obtenerDatos)
	},
	data() {
		return {
			loading: true,
			loadingComments: true,
			loadingPuntuaciones: true,
			loadingPlataformas: true,

			id_game: this.$route.params.id,
			juegoBase: 0,
			juegoExtendido: 0,
			juegoTotal: 0,
			game: [],

			reviewsList: [],
			reviews: false,
			background_barra: this.porcentajes,
			fabricanteColor: null,
			checkUserReview: false,
			avisoRechazo: null,
			reviewPendienteId: null,

			valoracion: [],
			dataValoraciones: {},
			puntuacionesMostrar: false,
			cargaPuntuaciones: false,

			dataPlataformas: [],
			plataformasNombre: [],
			plataformasDatos: [],
			plataformasMostrar: false,
			cargarPlataformas: false,

			pagina: null,
			primera_pagina:1,
			ultima_pagina: 1,

			optionsValoraciones: {
				responsive: true,
				maintainAspectRatio: false,
				aspectRatio: 1,
				scales: {
					xAxes: [{
						ticks: {
							stepSize: 10,
							fixedStepSize: 1,
							beginAtZero: true,
						},
						scaleLabel: {
							display: true,
							labelString: 'duracion juego base (horas)'
						}
					}],
					yAxes: [{
						ticks:{
							beginAtZero: true,
							stepSize: 10,
							fixedStepSize: 1,
						},
						scaleLabel: {
							display: true,
							labelString: 'puntuacion (tanto por ciento)'
						}
					}]
				},
				plugins: {
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Chart.js Scatter Chart'
					}
				}
			},
			optionsPlataformas: {
				responsive: true,
				maintainAspectRatio: false,
				aspectRatio: 1,
			}
		}
	},
	methods: {
		obtenerDatos() {
				this.loading =  true,
				axios.get('http://gamereviewsproject.herokuapp.com/api/juego/' + this.id_game,{
					params: {
          user_id: this.current_user.id,
        }
				}).then(response =>{
					this.game = response.data[0]
					this.obtenerDatosCompletados()
					this.porcentajeBarra(this.game.valoracion_media)
					this.loading = false
				}).catch(error =>{
					console.log("entro")
					this.$router.push('/game_not_found');
				});
			},
		obtenerDatosCompletados(){
			for(let i = 0; i < this.game.reviews.length; i++){
				if(this.game.reviews[i].juegoExtendido != null){
					this.juegoExtendido++
				}
				if(this.game.reviews[i].juegoBase != null){
					this.juegoBase++
				}
				if(this.game.reviews[i].completadoTotal != null){
					this.juegoTotal++
				}
				if(this.game.reviews[i].user_id == this.current_user.id){
					this.checkUserReview = true
				}
				if(this.game.reviews[i].estado == 'Rechazado') {
					this.checkUserReview = false
					this.avisoRechazo = true
				}
				if(this.game.reviews[i].estado == 'Pendiente' || this.game.reviews[i].estado == 'Aceptado') {
					if(this.game.reviews[i].user_id == this.current_user.id){
						this.reviewPendienteId = this.game.reviews[i].id
					}
				}
			}
		},
		ObtenerDatosReview(){
			this.loadingComments = true
			axios.get('http://gamereviewsproject.herokuapp.com/api/review/' + this.id_game + '/?page='+ this.page, {
				params: {
					id:this.id_game
				}
			}).then(response =>{
				this.reviewsList = response.data.data;
				this.ultima_pagina = response.data.last_page
				this.loadingComments = false;
				this.pagina = response.data.current_page
			});
		},
		getDatosPuntuaciones(){
			this.puntuacionesMostrar = !this.puntuacionesMostrar
			if(this.cargaPuntuaciones == false){
			this.loadingPuntuaciones = true
				axios.get('http://gamereviewsproject.herokuapp.com/api/get_puntuacionesById/', {
					params: {
						game_id: this.id_game
					}
				}).then(response =>{
					let arraySize = response.data.length

					var chartData = {
						datasets: [{
							label: 'Valoracion',
							backgroundColor: 'green',
							pointRadius: 5,
							data: []
						}]
					}

					for(let x = 0; x < arraySize; x++){
						this.valoracion.push(response.data[x].puntuacion)
						chartData.datasets[0].data.push({
							y: response.data[x].puntuacion,
							x: response.data[x].juegoBase
						})
					}

					this.dataValoraciones = chartData
					this.cargaPuntuaciones = true
					this.loadingPuntuaciones = false

				});
			}
    },
		getDatosPlataformas(){
			this.plataformasMostrar = !this.plataformasMostrar
			if(this.cargarPlataformas == false){
			this.loadingPlataformas = true
				axios.get('http://gamereviewsproject.herokuapp.com/api/stats_Plataformas_Games/', {
					params: {
						game_id: this.id_game
					}
				}).then(response =>{
					let arraySize = response.data.length
					for(let x = 0; x < arraySize; x++){
						this.plataformasDatos.push(response.data[x].cantidad)
						this.plataformasNombre.push(response.data[x].nombre)
					}
					this.dataPlataformas = {
						hoverBackgroundColor: "red",
						hoverBorderWidth: 10,
						labels: this.plataformasNombre,
						datasets: [
							{
								label: "Data One",
								backgroundColor: ["#41B883", "#E46651", "#00D8FF", "#9147FF","#FCDAC2",
																	"#9AD0F5","#22CFCF","#FF4069","#FFCD56","#FF9F40",
																	"#633100","#305534","#FF7F00","#DE0000","#ADC9E7",],
								data: this.plataformasDatos
							}
						]
					}
					this.cargarPlataformas = true
					this.loadingPlataformas = false
				});
			}
    },
		porcentajeBarra(value){
			if(value < 35){
				this.background_barra = "#ff0000"
			}else if(value < 50){
				this.background_barra = "#ff8000"
			}else if(value < 70){
				this.background_barra = "#ffff00"
			}else if(value < 85){
				this.background_barra = "#80ff00"
			}else {
				this.background_barra = "#005000"
			}
		},
		muestraReviews(){
			this.reviews = !this.reviews;
			if(this.reviewsList.length == 0){
				this.ObtenerDatosReview();
			}
		},
		changePage( page ) {
      this.page = (page <= 0 || page > this.ultima_pagina) ? this.page : page
			this.ObtenerDatosReview()
		},
		estableceFondo(value){
			switch (value) {
				case 'Nintendo':
					return '#DE0000';
					break;
				case 'Sony':
					return '#00439C';
					break;
				case 'Microsoft':
					return '#0F7C0F';
					break;
				case 'Sega':
					return '#30A4FB';
					break;
				case 'Otros':
					return 'black';
					break;
			}
		},
		fondoPaginas(value){
			if(value == this.pagina){
				return '#245e13';
			}else {
				return '#002855';
			}
		},
	},
	filters: {
		roundValors: function(value) {
			if (!value){
				return '--'
			}

			let fondo = '';

			value = parseFloat(value).toFixed()

			return value;

		},
		formatDate(value){
			return moment(String(value)).format('DD-MM-YYYY')
		}
	}
}
</script>

<style scoped>

.cargando {
	width: 100%;
}

.carta {
	display: flex;
	width: 100%;
	height: auto;
	flex-flow: row wrap;
	margin: 50px 10px 50px 10px;
	padding: 20px;
}

.izqda {
	display: flex;
	width: 20%;
	height: auto;
	margin: 5px;
	padding: 10px;
	flex-flow: column wrap;
	order: 0;
}

.caratula {
	display: flex;
	width: 100%;
	height: auto;
	flex-flow: column wrap;
}

.imagen{
	width: 100%;
}

.send-review{
	display: flex;
	flex-flow: column;
	justify-content: center;
	font-size: 20px;
	width: 100%;
	height: 50px;
	margin-top: 10px;
	background: #03045e;
	color: white;
	border-radius: 4px;
	text-align: center;
}

.aviso {

	display: flex;
	flex-flow: column;
	justify-content: center;
	font-size: 20px;
	width: 100%;
	height: auto;
	margin-top: 10px;
	color: white;
	border-radius: 4px;
	text-align: center;
	background: #d2515d;
}

.send-review:hover {
	background: #03045e;
}

.plataformas {
	margin-top: 20px;
	color: white;
}

.etiqueta {
	display: inline-block;
	padding: .25em .4em;
	font-size: 75%;
	font-weight: 700;
	line-height: 1;
	text-align: center;
	white-space: nowrap;
	padding-right: .6em;
	padding-left: .6em;
	border-radius: 10rem;
	margin: 5px;
	align-self: center;
}

.principal {
	display: flex;
	width: 100%;
	height: 100%;
	margin-top: 10px;
	flex-flow: column wrap;
	order: 1;
	flex: 8;
	align-items: center;
	color: silver;
}

.cabecera {
	display: flex;
	height: auto;
	margin-top: 10px;
	flex-flow: column wrap;
	margin: 5px;
	width: 100%;
	padding: 10px;
	font-size: 20px;
	text-align: center;
}

.juego-info{
	display:flex;
	flex-flow: row wrap;
}

.titulo, .desarrolladora, .genero{
	display:flex;
	align-items:center;
	justify-content: center;
}

.titulo, .desarrolladora{
	flex:1;
	order: 0;
	background: #023E8A;
	margin-right: 5px;
}

.genero{
	flex:1;
	order: 2;
	background: #023E8A;
}

.juego-stats {
	display:flex;
	flex-flow: row wrap;
	width: 80%;
	height: auto;
	justify-content: center;
	font-size: 28px;
	text-align: center;
}

.stats {
	flex: 1;
	background: #0077B6;
	margin: 5px;
	display:flex;
	flex-flow: column wrap;
}

.duraciones {
	display:flex;
	flex-flow: row wrap;
	width: 80%;
	justify-content: center;
	font-size: 22px;
	text-align: center;
	margin-top: 10px;
}

.duracion {
	flex: 1;
	margin: 5px;
	background: #0096C7;
}

.valoracion {
	display:flex;
	flex-flow: column wrap;
	width: 26%;
	height: auto;
	justify-content: center;
	margin-top: 10px;
}

.putuacion {
	justify-content: center;
	font-size: 22px;
	text-align: center;
	color: silver;
	background: #00B4D8;
	padding: 5px;
}

.estadisticas{
	width: 100%;
	display:flex;
	flex-flow: column wrap;
}

.puntuaciones{
	background: #0077B6;
}

.puntuaciones, .plataformas{
	width: 350px;
	margin: 10px;
}

.separacion{
	width: 80%;
	height: auto;
	background: #468faf;
	margin-top: 15px;
	display:flex;
	flex-flow: column;
	color: white;
	font-size: 14px;
  border-radius: 1em/1em;
	text-align: center;
}

.separacion:hover{
	cursor:pointer;
}

.comentarios {
	display:flex;
	flex-flow: column wrap;
	width: 100%;
	height: auto;
	margin: 10px 10px 10px 0;
}

.paginate-bottom{
  display: flex;
  justify-content: center;
}

.page-link {
	background: #023e8a;
	color: white;
}

.comment-box {
	display:flex;
	flex-flow: row wrap;
	width: 100%;
	height: auto;
	color: black;
	margin: 10px 0;
}

.lateralComment {
	display: flex;
	flex-flow: column wrap;
	flex: 1;
	font-size: 18px;
	justify-content: flex-start;
	background: #C0E6ED;
	border-right: 1px solid white;
}

.nick {
	text-align: center;
	height: auto;
	background: #0466c8;
	border-radius: 4px;
}

.nick a{
	color: black;
}

.avatar{
	display:flex;
	justify-content: center;
}

.avatar img {
	padding: 10px;
	height: 90px;
  width: 90px;
}

.comment {
	display: flex;
	flex-flow: column;
	flex: 4;
	background: #001233;
	border-radius: 4px;
}

.comment-header {
	display: flex;
	flex-flow: row wrap;
	justify-content: space-between;
	width: 100%;
	height: auto;
	background: #0466c8;
	border-radius: 4px;
}

.fechaEscrito {
	display:flex;
	flex: 1;
	margin-left: 10px;
}

.puntosPersonales {
	display:flex;
	flex-flow: row wrap;
	flex: 1;
	justify-content: space-between;
}

.personalRating {
	border-left: 1px solid white;
	flex: 1;
	text-align: center;
}

.comment-message {
	flex: 6;
	margin: 10px;
	padding: 10px;
	background: #C0E6ED;
	border-radius: 4px;	font-size: 16px;
}

.pagination:hover {
	cursor:pointer;
}

.page-link {
  background: #023e7d;
  color: white;
}

.page-link:hover {
  background: #002855;
  color: white;
}

.scatter-grafico {
	width: 100%;
}

.pie-grafico {
	width: 50%;
	align-self: center;
}

@media (max-width: 700px){
	.carta{
		padding: 0px;
	}
	.izqda {
		width: 100%;
	}

	.cabecera{
		margin: 0;
		padding: 0;
	}

	.juego-info{
		flex-flow: column wrap;
		width: 100%;
		height: auto;
		justify-content: center;
		width:100%;
	}

	.titulo, .desarrolladora, .genero {
		flex:1;
		order: 0;
		background: #023E8A;
		margin-right: 0;
		margin-bottom: 5px;
	}

	.juego-stats {
		flex-flow: column wrap;
		width: 100%;
		height: auto;
		justify-content: center;
	}

	.duraciones {
		width: 100%;
	}

	.valoracion {
		width: 98%;
	}

	.comment-box {
		flex-flow: column wrap;
	}

	.lateralComment{
		flex-flow: row wrap;
		border-bottom: 1px solid white;
	}

	.nick, .rango {
		flex:1;
	}

	.avatar{
		flex: 2;
		display: none;
	}

}

</style>