<template>
	<div class="cargando" v-if="loading"> <Loading/> </div>

<div class="perfil" v-else>

  <div class="comentarios">
    <div class="botones" v-if="current_user.id == id_user || current_user.rol == 'Administrador'" >
      <button class="btn btn-primary primero" v-model="busqueda" @click="ObtenerDatosUserReview(1)"> Todas las reseñas</button>
      <button class="btn" style="background:#C0E6ED" v-model="busqueda" @click="ObtenerDatosUserReview(2)"> Reseñas aceptadas</button>
      <button class="btn tercero" style="background:#D7A86C" v-model="busqueda" @click="ObtenerDatosUserReview(3)"> Reseñas pendientes</button>
      <button class="btn" style="background:#D2515D" v-model="busqueda" @click="ObtenerDatosUserReview(4)"> Reseñas rechazadas</button>
    </div>

      <div class="comment-box" v-for="(item,index) in reviewsList" :key="index" v-if="vacio == false">
        <div class="lateralComment" :style="{background: estableceFondo(item.estado) + '!important'}">
          <div class="nick"> <a :href="'/game/'+ item.games.id"> {{item.games.titulo}} </a></div>
          <div class="caratula"><img class="caratula" :src="'/../storage/'+ item.games.imagen" alt=""></div>
        </div>
        <div class="comment">
          <div class="comment-header">
            <div class="fechaEscrito"> Escrito el: {{item.created_at | formatDate}}</div>
            <div class="puntosPersonales">
              <div class="personalRating">Base <p>{{item.juegoBase | roundValors}}<template v-if="item.juegoBase">H</template></p></div>
              <div class="personalRating">Extras<p>{{item.juegoExtendido | roundValors}}<template v-if="item.juegoExtendido">H</template></p></div>
              <div class="personalRating">100% <p>{{item.completadoTotal | roundValors}}<template v-if="item.completadoTotal">H</template></p></div>
              <div class="personalRating">Valoracion<p>{{item.puntuacion | roundValors}}<template v-if="item.puntuacion">%</template></p></div>
            </div>
          </div>
          <div class="comment-message" v-html="item.mensaje"></div>
          <div class="observaciones" v-if="item.estado == 'Rechazado'"> Causa de rechazo: {{item.observaciones}}</div>
        </div>
    </div>
    <template v-if="vacio == true">
      <div id="sin-datos" class="observaciones">No hay datos disponible</div>
    </template>
  </div>
</div>
</template>

<script>
import moment from 'moment'

export default {
  props : ['current_user'],
  mounted() {

		this.ObtenerDatosUserReview()

  },
	data() {
		return {
			id_user: this.$route.params.id,
			reviews: false,
			juegos: false,
			page: 1,
      pages: 1,
			loading: true,
			reviewsList: [],
			juegoBase: null,
			busqueda: 2,
			vacio: false
		}
	},
  methods: {
		ObtenerDatosUserReview(value){
			this.vacio = false
			if(value === void 0){
				value = this.busqueda
			}
			this.loading = true
			const params = {
				page: this.page
			}
			axios.get('http://localhost:8000/api/review_user/' + this.id_user + '/?page='+ this.page, {
				params: {
					id: this.id_user,
					opcion: value
				}
			}).then(response =>{
				this.reviewsList = response.data.data;
				this.pages = response.data.last_page
				if(this.reviewsList.length == 0){
					this.vacio = true
				}
				this.loading = false;
			});
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

.comentarios {
	display:flex;
	flex-flow: column wrap;
	width: 100%;
	height: auto;
	margin: 10px 10px 10px 0;
}

.botones {
	display: flex;
	width: 100%;
	align-items: center;
	flex-flow: row wrap;
	justify-content: center;
}

.btn {
	margin: 15px;
	color: black;

}

.comment-box {
	display:flex;
	flex-flow: row wrap;
	width: 100%;
	height: auto;
	color: black;
	margin: 10px 0;
	border-radius: 4px;
}

.lateralComment {
	display: flex;
	flex-flow: column wrap;
	flex: 1;
	font-size: 18px;
	justify-content: flex-start;
	background: #C0E6ED;
	border-radius: 4px;
	border-right: 1px solid white;
}


.nick {
	text-align: center;
	height: auto;
	background: #0077B6;
  border-radius: 4px;
}

.nick a{
	color: black;
}

.caratula{
	display:flex;
	justify-content: center;
	align-items: center;
	margin: 10px auto;
}

.comment {
	display: flex;
	flex-flow: column;
	flex: 4;
	background: #001233;
	border-radius: 4px;

}

#sin-datos {
	text-align: center;
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

.comment-message {
	flex: 6;
	margin: 10px;
	padding: 10px;
	background: #C0E6ED;
  border-radius: 4px;
	font-size: 16px;
}

.fechaEscrito{
	display:flex;
	flex: 1;
	margin-left: 10px;

}

.observaciones {
	background: #C0E6ED;
	padding: ;
	margin: 20px;
	padding: 10px;
}

.sinDatos {
	text-align : center;
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

.personalRating a{
	color: black;
	text-decoration: none;
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
	border-bottom: 1px solid white;
}
.nick, {
	flex:1;
}

.caratula {
	flex: 2;
	display: none;
}

.comment-header {
	flex-flow: column wrap;

}

.fechaEscrito {
	border-bottom: 1px solid white;

}

.listado {
	border-bottom: 5px solid white;
}

.apartado{
	border-bottom: 0px;
}

.personalRating {
	border-bottom: 1px solid white;
}

.puntosPersonales, .listado {
	flex-flow: column wrap;
}

}




</style>