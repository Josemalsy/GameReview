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
            <b-dropdown-item v-b-modal="'modal-editProfile'"><i class="bi bi-tools"></i>Editar perfil<modal-editProfile :current_user="current_user"/></b-dropdown-item>
            <b-dropdown-item :href="'/usuario/' + current_user.id + '/reviews'"><i class="bi bi-clipboard"></i>Reviews realizadas</b-dropdown-item>
            <b-dropdown-item :href="'/usuario/' + current_user.id + '/juegos'"><i class="bi bi-controller"></i>Lista de juegos</b-dropdown-item>
            <b-dropdown-item href="/staff_tools" v-if="current_user.rol != 'Usuario'"><i class="bi bi-gear" aria-hidden="true"></i>Panel de staff</b-dropdown-item>
            <b-dropdown-item href="/mensajes"><i class="bi bi-envelope"></i>Mensajes </a>
          </b-dropdown-item>
        </b-dropdown>
      </div>
      </template>
      <a href="/stats" class="nav-link"><i class="bi bi-graph-up" aria-hidden="true"></i><span>Stats </span></a>
    </div>

    <div class="aviso-confirmacion" v-if="!current_user.email_verified_at && current_user != false"><div> Un correo electrónico ha sido enviado a tu email para que lo confirmes. Si no ha llegado puedes solicitar otro <a href="/email/verify">en este enlace</a> </div> </div>


    <div class="cabecera" v-if="avisoReviewsRechazados">Tienes {{cantidadReviewsRechazados}} nueva review rechazada. Una acumulación de reviews rechazadas puede provocar la EXPULSIÓN de la web</div>

  </div>
</template>

<script>
import moment from 'moment'

export default {
  props : ['current_user'],
  mounted() {
    this.getDatos()
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
      hoy: moment().format('YYYY-MM-DD'),

    }
  },
  methods: {
    getDatos(){
      if(this.current_user != false){
        window.location.pathname == '/mensajes' ? this.getDatosMensajes(this.urlMensajes = true) : this.getDatosMensajes(this.urlMensajes = false)
        window.location.pathname == '/usuario/' + this.current_user.id +  '/reviews' ? this.getDatosReviews(this.urlReviews = true) : this.getDatosReviews(this.urlReviews = false)

        let fin_expulsion = moment(this.current_user.fin_expulsion)
        if(this.current_user.fin_expulsion){
          if(fin_expulsion.isSameOrBefore(this.hoy) ){
            this.getUnban()
          }

        }
      }
    },
    getDatosMensajes(){
      this.urlMensajes == true
      axios.get('http://gamereviewsproject.herokuapp.com/api/consultar_avisos_mensajes', {
        params: {
          isMensajeRoute: this.urlMensajes,
          user_id: this.current_user.id
        }
      }).then(response => {
        if(response.data > 0 ){
          this.avisoMensajes = true
          this.cantidadMensajes = response.data
        }
      })
    },
    getDatosReviews(){
      axios.get('http://gamereviewsproject.herokuapp.com/api/consultar_avisos_reviews', {
        params: {
          isReviewsRoute: this.urlReviews,
          user_id: this.current_user.id
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
    },
    getUnban(){
      axios.get('http://gamereviewsproject.herokuapp.com/api/getUnban', {
        params: {
          user_id: this.current_user.id
        }
      })
    },
  }
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

.aviso-confirmacion a{
  color: blue;
}

.cabecera, .aviso-confirmacion {
	display: flex;
	height: auto;
	margin-top: 10px;
	flex-flow: column wrap;
  margin-top: 20px 5px 0 5px;
	width: 80% ;
	color: white;
  text-align: center;
  align-self: center;
}

.cabecera {
	background: #b21414;
	padding: 10px;
	font-size: 20px;
}

.aviso-confirmacion {
  background: #006F62;
  padding: 5px;
	font-size: 20px;
}

@media(700px) {
  .secciones{
    font-size: 15px;
  }
}

</style>