<template>
			<div class="cargando" v-if="loading"> <Loading/> </div>

		<div class="carta" v-else>
			<div class="izqda">
				<div class="caratula">
					<img class="imagen" :src="'../storage/'+ game.imagen" alt="Card image">
				</div>
				<div class="send-review" style="background-color: #FF4500;" v-if="!current_user"> Logueate para enviar tu review <modal-review /></div>
				<div class="send-review" v-else-if="!checkUserReview" v-b-modal="'review-modal'" current_user="'current_user'"> Añade tu revisión <modal-review /></div>
				<div class="send-review" style="background-color: green;" v-else>Valoracion realizada</div>
			</div>
			<div class="principal">
				<div class="cabecera">
					<div class="juego-info">
						<div class="titulo">{{game.titulo}}</div> <div class="desarrolladora">{{game.desarrolladora}} </div> <div class="genero"> {{game.genero}}</div>
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
					<div class="duracion">Juego Base <p>{{game.reviews_avg_juego_base | roundValors}}H</p> </div>
					<div class="duracion"> Juego Base + extras <p>{{game.reviews_avg_juego_extendido | roundValors}}H</p> </div>
					<div class="duracion"> 100% <p>{{game.reviews_avg_reviews_avg_completado_total | roundValors}}H</p> </div>
				</div>
				<div class="valoracion">
					<div class="putuacion"> Puntuacion: {{game.reviews_avg_puntuacion | roundValors}}%
						<div class="progress">
							<div class="progress-bar" role="progressbar" :style="{width: game.reviews_avg_puntuacion + '%', background: background_barra}" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
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
								<ul class="pagination">
									<li class="page-item"><a class="page-link" v-on:click="changePage( page - 1)"><<</a></li>

									<li class="page-item"><a class="page-link" v-on:click="changePage( page -1 )">{{ page - 1 }}</a></li>
									<li class="page-item"><a class="page-link" v-on:click="changePage( page )">{{ page }}</a></li>
									<li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)">{{ page +1 }}</a></li>

									<li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)">>></a></li>
								</ul>
							</nav>
							<div class="cargando" v-if="loadingComments"> <Loading/> </div>
							<div class="comment-box" v-for="item in reviewsList">
								<div class="lateralComment">
									<div class="nick"> <a :href="'/usuario/'+item.users.id"> {{item.users.name}} </a></div>
									<div class="avatar"><img class="caratula" :src="'../storage/'+ item.users.avatar" alt="Card image capaaaa"></div>
									<div class="rango">Rango</div>
								</div>
								<div class="comment">
									<div class="comment-header">
										<div class="fechaEscrito"> Escrito el: {{item.created_at | formatDate}}</div>
										<div class="puntosPersonales">
											<div class="personalRating">Base <p>{{item.juegoBase | roundValors}}H</p></div>
											<div class="personalRating">Extras<p>{{item.juegoExtendido | roundValors}}H</p></div>
											<div class="personalRating">100% <p>{{item.juegoTotal | roundValors}}H</p></div>
											<div class="personalRating">Valoracion<p>{{item.puntuacion | roundValors}}%</p></div>
										</div>
									</div>
									<div class="comment-message" v-html="item.mensaje"></div>
								</div>
							</div>
							<nav class="paginate-bottom" aria-label="Page navigation example">
								<ul class="pagination">
									<li class="page-item"><a class="page-link" v-on:click="changePage( page - 1)">Previous</a></li>

									<li class="page-item"><a class="page-link" v-on:click="changePage( page - 1 )">{{ page - 1 }}</a></li>
									<li class="page-item"><a class="page-link" v-on:click="changePage( page )">{{ page }}</a></li>
									<li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)">{{ page + 1 }}</a></li>

									<li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)">Next</a></li>
								</ul>
							</nav>
						</div>
						<modal-review />

					</template>
			</div>
		</div>
</template>

<script>
import moment from 'moment'

export default {
  props : ['current_user'],
	mounted() {
		this.obtenerDatos()
		this.$bus.$on('prueba',this.obtenerDatos)
	},
	data() {
		return {
			loading: true,
			loadingComments: true,
			id_game: this.$route.params.id,
			juegoBase: 0,
			juegoExtendido: 0,
			juegoTotal: 0,
			game: [],
			reviewsList: [],
			reviews: false,
			background_barra: this.porcentajes,
			page: 1,
			pages: 1,
			checkUserReview: false,
		}
	},
	methods: {
		obtenerDatos() {
				this.loading =  true,
				axios.get('http://localhost:8000/api/juego/' + this.id_game).then(response =>{
				this.game = response.data[0]
				this.obtenerDatosCompletados()
				this.porcentajeBarra(this.game.reviews_avg_puntuacion)
				this.loading = false
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
				if(this.game.reviews[i].user_id == this.current_user){
					this.checkUserReview = true
				}
			}
		},
		ObtenerDatosReview(){
			this.loadingComments = true
			const params = {
				page: this.page
			}
			axios.get('http://localhost:8000/api/review/' + this.id_game + '/?page='+ this.page, {
				params: {
					id:this.id_game
				}
			}).then(response =>{
				this.reviewsList = response.data.data;
				this.pages = response.data.last_page
				this.loadingComments = false;
			});
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
			this.page = (page <= 0 || page > this.pages) ? this.page : page
			this.ObtenerDatosReview()
		},
	},
	filters: {
		roundValors: function(value) {
			// console.log(value)
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
	/* border: 1px solid red; */
	display: flex;
	width: 100%;
	height: auto;
	flex-flow: row wrap;
	margin: 50px 10px 50px 10px;
	padding: 20px;
	/* background: #CAF0F8; */
}

.izqda {
	/* border: 1px solid green; */
	display: flex;
	width: 20%;
	height: auto;
	margin: 5px;
	padding: 10px;
	flex-flow: column wrap;
	order: 0;
}

.caratula {
	/* border: 1px solid purple; */
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
	border-radius: 1em/1em;
	text-align: center;
}

.send-review:hover {
	background: #03045e;
}

.principal {
	/* border: 1px solid blue; */
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
	/* border: 1px solid blue; */
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
	/* border: 1px solid black; */
	flex:1;
	order: 0;
	background: #023E8A;
	margin-right: 5px;
}

.genero{
	/* border: 1px solid black; */
	flex:1;
	order: 2;
	background: #023E8A;
}

.juego-stats {
	display:flex;
	flex-flow: row wrap;
	/* border: 1px solid pink; */
	width: 80%;
	height: auto;
	justify-content: center;
	font-size: 28px;
	text-align: center;
}

.stats {
	flex: 1;
	/* border: 1px solid black; */
	background: #0077B6;
	margin: 5px;
	display:flex;
	flex-flow: column wrap;
}

.duraciones {
	display:flex;
	flex-flow: row wrap;
	/* border: 1px solid pink; */
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
	/* border: 1px solid pink; */
	width: 26%;
	height: auto;
	justify-content: center;
	margin-top: 10px;
}

.putuacion {
	/* border: 1px solid green; */
	justify-content: center;
	font-size: 22px;
	text-align: center;
	color: silver;
	background: #00B4D8;
	padding: 5px;
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
	/* border: 1px solid black; */
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
	/* border: 1px solid green; */
	display:flex;
	flex-flow: row wrap;
	width: 100%;
	height: auto;
	color: black;
	margin: 10px 0;
	/* background: #BAD7F2; */
	border-radius: 1em/1em;
}

.lateralComment {
	display: flex;
	flex-flow: column wrap;
	flex: 1;
	border: 1px solid blue;
	font-size: 18px;
	justify-content: flex-start;
	background: #dad7cd;
	border-radius: 1em/1em;

}

.nick, .rango {
	/* border: 1px solid purple; */
	text-align: center;
	height: auto;
	background: #0466c8;
	border-radius: 1em/1em;
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
	border: 1px solid blue;
	/* background: #00b4d8; */
	background: #001233;
	border-radius: 1em/1em;

}

.comment-header {
	display: flex;
	flex-flow: row wrap;
	justify-content: space-between;
	width: 100%;
	height: auto;
	/* border: 1px solid red; */
	background: #0466c8;
	border-radius: 1em/1em;

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
	border-left: 1px solid blue;
	flex: 1;
	text-align: center;

}

.comment-message {
	flex: 6;
	/* border: 1px solid blue; */
	margin: 10px;
	padding: 10px;
	background: #dad7cd;
	border-radius: 1em/1em;
	font-size: 16px;
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
	/* border: 1px solid black; */
	flex:1;
	order: 0;
	background: #023E8A;
	margin-right: 0;
	margin-bottom: 5px;
}

	.juego-stats {
	flex-flow: column wrap;
	/* border: 1px solid pink; */
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