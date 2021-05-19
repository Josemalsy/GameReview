<template>
  <div class="pagina">
    <div class="secciones">
      <a href="/" class="nav-link"><i class="bi bi-joystick" aria-hidden="true"></i><span>Juegos</span></a>
      <a href="/usuarios" class="nav-link"><i class="bi bi-people-fill" aria-hidden="true"></i><span>Usuarios</span></a>
      <template v-if="current_user">
      <div class="nav-link">
        <span class="aviso" v-if="avisoMensajes" :title="'Tiene '+ cantidadMensajes + ' mensajes nuevos'"> <a href="/mensajes">{{cantidadMensajes}}</a></span>
        <span class="avisoReviews" v-if="avisoReviewsAceptados" :title="'Tiene '+ cantidadReviewsAceptados + ' avisos de reviews'"> <a :href="'/usuario/' + current_user.id + '/reviews'">{{cantidadReviewsAceptados}}</a></span>
        <b-dropdown id="dropdown-1" text="Perfil" variant="red" toggle-class="text-light">
            <b-dropdown-item :href="'/usuario/' + current_user.id"><i class="bi bi-person"></i>{{current_user.name}}</b-dropdown-item>
            <b-dropdown-item :href="'/usuario/' + current_user.id + '/reviews'"><i class="bi bi-clipboard"></i>Reviews realizadas</b-dropdown-item>
            <b-dropdown-item :href="'/usuario/' + current_user.id + '/juegos'"><i class="bi bi-controller"></i>Lista de juegos</b-dropdown-item>
            <b-dropdown-item href="/settings"><i class="bi bi-gear" aria-hidden="true"></i>Settings</b-dropdown-item>
            <b-dropdown-item href="/mensajes"><i class="bi bi-envelope"></i>Mensajes </a>
          </b-dropdown-item>
        </b-dropdown>
      </div>
      </template>
      <a href="/stats" class="nav-link"><i class="bi bi-graph-up" aria-hidden="true"></i><span>Stats </span></a>
    </div>

    <div class="cabecera" v-if="avisoReviewsRechazados">Tienes {{cantidadReviewsRechazados}} nueva review rechazada. Una acumulación de reviews rechazadas puede provocar la EXPULSIÓN de la web</div>

  </div>
</template>

<script>
export default {
  props : ['current_user'],
  mounted() {
    this.getDatosMensajes()
    this.getDatosReviews()
  },
  data() {
    return {
      avisoMensajes: false,
      avisoReviewsAceptados: false,
      avisoReviewsRechazados: false,
      cantidadMensajes: null,
      cantidadReviewsAceptados: null,
      cantidadReviewsRechazados: null,
      urlMensajes: false,
      urlReviews: false,
    }
  },
  methods: {
    getDatosMensajes(){
      if(this.current_user != false){
        if(window.location.pathname == '/mensajes'){
          this.urlMensajes = true
        }
        axios.get('http://localhost:8000/api/consultar_avisos_mensajes', {
          params: {
            isMensajeRoute: this.urlMensajes,
          }
        }).then(response => {
          if(response.data > 0 ){
            this.avisoMensajes = true
            this.cantidadMensajes = response.data
          }
        })
      }
    },
    getDatosReviews(){
      if(this.current_user != false){
        if(window.location.pathname == '/usuario/' + this.current_user.id +  '/reviews'){
          this.urlReviews = true
        }
        axios.get('http://localhost:8000/api/consultar_avisos_reviews', {
          params: {
            isReviewsRoute: this.urlReviews,
          }
        }).then(response => {
          for(let x = 0; x < response.data.length; x++){
            if(response.data[x].estado == 'Aceptado' ){
              this.avisoReviewsAceptados = true
              this.cantidadReviewsAceptados = response.data[x].cantidad
            }
            if(response.data[x].estado == 'Rechazado' ){
              this.avisoReviewsRechazados = true
              this.cantidadReviewsRechazados = response.data[x].cantidad
            }
          }
        })
      }
    }
  },
}

</script>

<style scoped>

.pagina {
  display:flex;
  flex-flow: column wrap;
  width: 100%;

}

.secciones {
  display: flex;
  flex-flow: row wrap;
  width: 100%;
  background-color: #074680;
  text-align: center;
  align-items: center;
}

.nav-link {
  color: white!important;
  flex:1;
  background-color: #074680;
  padding: 0;
  display: flex;
  justify-content: center;
}

.secciones a:hover {
  background-color: #0575AB;
  color: white;
}

.aviso, .aviso a{
  background: #FF7F00;
  color: white;
  padding: 5px;
  color:white;
}

.aviso a:hover{
  background: #FF7F00;
}

.avisoReviews, .avisoReviews a {
  background: #245e13;
  color: white;
  padding: 5px;
  color:white;
}

.avisoReviews a:hover{
  background: #245e13;
}

.cabecera {
	/* border: 1px solid blue; */
	display: flex;
	height: auto;
	margin-top: 10px;
	flex-flow: column wrap;
	margin: 5px;
	width: auto ;
	padding: 10px;
	font-size: 25px;
	background: #b21414;
	color: white;
  text-align: center;
}

@media(700px) {
  .secciones{
    font-size: 15px;
  }
}

</style>