<template>
	<div class="cargando" v-if="loadingContenido"> <Loading/> </div>

  <div class="perfil" v-else>
			<div class="cabecera">Lista de juegos</div>

      <template>
				<div class="comment-header">
					<div class="listado-cabecera">
						<div class="apartado">Titulo</div>
						<div class="apartado">Base</div>
						<div class="apartado">Extras</div>
						<div class="apartado">100</div>
						<div class="apartado">Valoracion</div>

					</div>
				</div>

				<div class="comment-header" v-for="(item, index) in todosLosJuegos" :key="index" >
					<div class="listado">
						<div class="apartado"> <p><router-link :to="{ path: '/game/ ' + item.game_id }" >{{item.titulo}}</router-link></p></div>
						<div class="apartado"><p v-if="item.juegoBase" class="existe"><i class="bi bi-check-square-fill"></i> </p><p v-else class="no-existe"><i class="bi bi-x-square-fill" ></i></p></div>
						<div class="apartado"> <p v-if="item.juegoExtendido" class="existe"> <i class="bi bi-check-square-fill"></i> </p><p v-else class="no-existe"><i class="bi bi-x-square-fill" ></i></p></div>
						<div class="apartado"><p v-if="item.completadoTotal" class="existe"><i class="bi bi-check-square-fill"></i> </p><p v-else class="no-existe"><i class="bi bi-x-square-fill" ></i></p></div>
						<div class="apartado"><p v-if="item.puntuacion" class="existe"> <i class="bi bi-check-square-fill"></i> </p><p v-else class="no-existe"><i class="bi bi-x-square-fill" ></i></p></div>
					</div>
				</div>
			</template>

    <template v-if="vacio == true">
      <div id="sin-datos" class="observaciones">No hay datos disponible</div>
    </template>

  </div>

</template>

<script>
import moment from 'moment'

export default {
  props : ['current_user'],
  mounted() {
		this.obtenerJuegos()
  },
	data() {
		return {
			id_user: this.$route.params.id,
			juegos: false,
			loading: true,
			loadingContenido: true,
      juegosConReview: [],
      juegosSinReview: [],
			todosLosJuegos: [],
			juegoBase: null,
			busqueda: 2,
			vacio: false
		}
	},
  methods: {
    obtenerJuegos(){
			this.loading = true
			this.loadingContenido = true
			axios.get('https://gamereviewsproject.herokuapp.com/api/muestra_juegos/', {
				params: {
					user_id: this.id_user
				}
			}).then(response =>{
				this.juegosConReview = response.data;
				this.loading = false
				if(!this.loading){
					this.ObtenerJuegosSinReviews()
				}
			})
		},
		ObtenerJuegosSinReviews(){
			this.loading = true
			axios.get('https://gamereviewsproject.herokuapp.com/api/getJuegosSinReview/',{
				params: {
					user_id: this.id_user
				}
			}).then(response =>{
				this.juegosSinReview = response.data;
				this.loading = false
				if(!this.loading){
					this.agregaArrays()
				}
			})
		},
		agregaArrays() {
			this.todosLosJuegos = [].concat(this.juegosConReview,this.juegosSinReview)
			this.loadingContenido = false
		},
    estableceFondo(value){
			if(value == 'Aceptado'){
				return '#c0e6ed'
			} else if( value == 'Pendiente') {
				return '#D7A86C'
			}else {
				return '#D2515D'
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
  margin: 10px;
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

.comment-header {
	display: flex;
	flex-flow: row wrap;
	justify-content: space-between;
	width: 100%;
	height: auto;
	background: #0077B6;
	border-radius: 4px;

}


.listado, .listado-cabecera {
	display:flex;
	flex-flow: row wrap;
	flex: 1;
	justify-content: space-between;
	background: #c0e6ed;

}

.listado-cabecera {
	font-size: 25px;
}

.apartado {
	border-left: 1px solid white;
	flex: 1;
	text-align: center;

}

.apartado a{
	color: black;
	text-decoration: none;
}

.apartado {
	border-bottom: 5px solid white;
}

.existe {
	color: green;
}

.no-existe{
	color: #8C1313;
}

@media (max-width: 700px) {

.perfil {
	width: 100%;
}

a{
	font-size: 16px;
}

.listado-cabecera {
	font-size: 18px;
}

}


</style>