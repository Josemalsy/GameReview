<template>
		<div class="cargando" v-if="loading"> <Loading/> </div>

		<div class="perfil" v-else>
			<div class="cabecera">{{usuario.name}}</div>
			<div class="principal">
				<div class="contenedor">
					<div class="columna-avatar">
						<img class="avatar" :src="'../storage/'+usuario.avatar" alt="Card image">
						<div class="send-review" v-if="current_user == id_user">Editar perfil</div>
					</div>
						<div class="contentDatos">
						<div class="datosPersonales">
							<div> <strong>Puntos:  </strong> {{usuario.puntos}} </div>
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
								<li class="page-item"><a class="page-link" v-on:click="changePage( page - 1)"> ← </a></li>

								<li class="page-item"><a class="page-link" v-on:click="changePage( page -1 )">{{ page - 1 }}</a></li>
								<li class="page-item"><a class="page-link" v-on:click="changePage( page )">{{ page }}</a></li>
								<li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)">{{ page +1 }}</a></li>

								<li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)"> → </a></li>
							</ul>
						</nav>
						<div class="cargando" v-if="loadingComments"> <Loading/> </div>
						<div class="comment-box" v-for="(item,index) in reviewsList" :key="index">
							<div class="lateralComment">
								<div class="nick"> <a :href="'/game/'+ item.games.id"> {{item.games.titulo}} </a></div>
								<div class="caratula"><img class="caratula" :src="'../storage/'+ item.games.imagen" alt="Card image capaaaa"></div>
								<div class="rango">{{item.games.desarrolladora}}</div>
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
	},
	data() {
		return {
			loading: true,
			id_user: this.$route.params.id,
			reviews: false,
			page: 1,
      pages: 1,
			loadingComments: true,
			usuario: [],
			reviewsList: [],
		}
	},
	methods: {
		obtenerDatos(){
			this.loading =  true,
			axios.get('http://localhost:8000/api/usuario/' + this.id_user).then(response =>{
				this.usuario = response.data[0];
				console.log(this.usuario)
				this.loading = false
			});
		},
		muestraReviews(){
			this.reviews = !this.reviews;
			if(this.reviewsList.length == 0){
				this.ObtenerDatosUserReview();
			}
		},
		changePage( page ) {
			this.page = (page <= 0 || page > this.pages) ? this.page : page
			this.ObtenerDatosUserReview()
		},
		ObtenerDatosUserReview(){
			this.loadingComments = true
			// console.log(this.page)
			const params = {
				page: this.page
			}
			axios.get('http://localhost:8000/api/review_user/' + this.id_user + '/?page='+ this.page, {
				params: {
					id: this.id_user
				}
			}).then(response =>{
				this.reviewsList = response.data.data;
				console.log(response.data.data)
				this.pages = response.data.last_page
				this.loadingComments = false;
			});
		},
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
				return 'Sin valoraciones realizadas'
			}
			value = parseFloat(value).toFixed(2)
			return value;
		},
	}
}
</script>

<style scoped>
	.perfil {
	/* border: 1px solid red; */
	display: flex;
	width: 80%;
	height: auto;
	flex-flow: row wrap;
	margin: 50px 10px 50px 10px;
	/* background: #CAF0F8; */
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
	font-size: 25px;
	background: #013a63;
	color: white;
}

.cabecera, .divisor {
	text-align: center;
}

.cabecera, .divisor, .stats {
	border-radius: 1em/1em;
}

.contenedor {
	display: flex;
	flex-flow: row wrap;
}

.columna-avatar {
	/* border: 1px solid green; */
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

.principal {
	/* border: 1px solid blue; */
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
	/* border: 1px solid; */
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
	/* border: 1px solid black; */
	background: #0077B6;
	margin: 5px;
	display:flex;
	flex-flow: column wrap;
	justify-content: center;

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
	align-self: center;
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

.caratula{
	display:flex;
	justify-content: center;
}

.caratula img {
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

.fechaEscrito{
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

.comment-box {
	flex-flow: column wrap;
}

.lateralComment{
	flex-flow: row wrap;
}
.nick, .rango {
	flex:1;
}

.caratula {
	flex: 2;
	display: none;
}

}


</style>