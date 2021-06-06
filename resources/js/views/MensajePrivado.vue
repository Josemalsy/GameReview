<template>
  <div class="principal" v-if="!loadingMensaje">
    <template v-if="mensaje.receptor_id == current_user.id || mensaje.emisor_id == current_user.id">
      <div class="cabecera">Mensajes privados </div>

        <div class="menu">

          <li class="menuBoton" @click="gestionEnlace(1)">Recibidos</li>
          <li class="menuBoton" @click="gestionEnlace(2)">Enviados leidos</li>
          <li class="menuBoton" @click="gestionEnlace(3)">Enviados sin leer</li>
        </div>

        <div class="centro">
        <template v-if="current_user.email_verified_at">
          <template v-if="opcion == 1">
            <button class="responderMensaje btn" v-b-modal="'modal-enviarMensaje'"> Responder
            <modal-enviarMensaje :current_user="current_user" :tituloModal="'Responder Mensaje'" :tituloMensaje="mensaje.titulo" :emisor_id="mensaje.emisor_id" :receptor_id="mensaje.receptor_id" :nombreDestino="mensaje.emisor.name", :conversacion_id="conversacion_id" :opcion="opcion" /></button>
          </template>
          <template v-else>
            <button class="responderMensaje" v-b-modal="'modal-enviarMensaje'"> Responder
            <modal-enviarMensaje :current_user="current_user" :tituloModal="'Responder Mensaje'" :tituloMensaje="mensaje.titulo" :receptor_id="mensaje.receptor_id" :nombreDestino="mensaje.receptor.name", :conversacion_id="conversacion_id" :opcion="opcion" /></button>
          </template>
        </template>
        <template v-else>
          <li class="menuBotonValidar" style="background-color: #2B4562"> Valida tu email para poder responder mensajes </li>
        </template>
          <div class="mensaje">
            <div class="usuario">
              <img class="imagen" :src="'../storage/' + mensaje.emisor.avatar" alt="Card image">
              <span class="nombre"><a :href="'usuario/'+mensaje.emisor.id"> {{mensaje.emisor.name}}</a></span>
              <span class="registro">Registrado: <p>{{mensaje.emisor.created_at | registro}}</p></span>
            </div>

            <div class="infoMensaje">
              <div class="arriba">
                <div class="fechaTitulo">{{mensaje.created_at | formatDate}} - {{mensaje.titulo}} </div>
                <div class="botonBorrar"> <i class="bi bi-x-square-fill" @click="borrarMensaje(mensaje.id)" style="cursor: pointer" title="borrar mensaje"></i> </div>
              </div>
              <div class="textoMensaje" v-html="mensaje.mensaje"></div>
            </div>
          </div>

          <hr style="order:1" />

          <div class="subContenedor" v-if="!loadingConversacion">
            <div class="conversacion" v-for="(item, index) in conversacion.mensaje" :key="index">
              <div class="arriba">
                <div class="conversacionfechaTitulo"> {{item.created_at | formatDate}} - <a :href="'usuario/'+item.emisor_id">{{item.emisor.name}} </a> </div>
              </div>
              <div class="textoMensaje" v-html="item.mensaje"></div>
            </div>
          </div>

        </div>
    </template>
  </div>
</template>

<script>
import moment from 'moment'
import Swal from 'sweetalert2'

export default {
  props : ['current_user'],
  mounted() {
    this.mensaje_id = this.$route.query.mensaje_id
    this.conversacion_id =  this.$route.query.conversacion_id
    this.opcion = this.$route.query.opcion
    this.getDatosMensaje()
    this.$bus.$on('prueba', this.getDatosMensaje)
  },
  data() {
    return {
      mensaje_id: null,
      conversacion_id: null,
      opcion: null,
      mensaje: null,
      conversacion: null,
      loadingMensaje: true,
      loadingConversacion: true,
    }
  },
  methods: {
    getDatosMensaje(){
      this.loadingMensaje = true,
      axios.get('http://gamereviewsproject.herokuapp.com/api/getMensajeById', {
        params: {
          mensaje_id: this.mensaje_id,
          user_id: this.current_user.id
        }
      }).then(response => {
        this.mensaje = response.data[0];
        this.loadingMensaje = false
        this.getDatosConversacion()

        if(this.mensaje.receptor_id != this.current_user.id && this.mensaje.emisor_id != this.current_user.id){
          toastr.error('NO ESTAS AUTORIZADO PARA LEER ESTE MENSAJE');
          this.gestionEnlace(1);
        }

      })
    },
    getDatosConversacion(){
      this.loadingConversacion = true
      axios.get('http://gamereviewsproject.herokuapp.com/api/getConversacionesById', {
        params: {
          conversacion_id: this.conversacion_id,
        }
      }).then(response => {
        this.conversacion = response.data[0];
        this.loadingConversacion = false
      })
    },
    gestionEnlace(value){
      this.$router.push({
        name: "mensajes privados",
        params:{
          opcionParam: value,
        }
      });
    },
    borrarMensaje(value){
      Swal.fire({
        title: "¿Quieres borrar este mensaje?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, borralo',
      }).then((result) => {
        if(result.isConfirmed){
          axios.get('http://gamereviewsproject.herokuapp.com/api/borrar_mensajes', {
            params: {
              mensaje_id: this.mensaje_id,
              opcion: this.opcion
            }
          }).then(response =>{
            toastr.success('Mensaje borrado');
            this.gestionEnlace(1);
          });
        }
      })
    },
  },
  filters: {
    registro(value) {
      moment.locale('es');
			return moment(String(value)).format('MMM YYYY')
    },
    formatDate(value){
      moment.locale('es');
			return moment(String(value)).format('DD MMM YYYY HH:mm')
		}
  }
}
</script>

<style scoped>
  .contenedor{
    width: 100%;
  }

  .principal {
    display: flex;
    width: 100%;
    height: auto;
    flex-flow: row wrap;
    margin: 30px 10px 50px 10px;
    padding: 20px;
  }

  .cabecera {
    display: flex;
    height: auto;
    margin-top: 10px;
    flex-flow: column wrap;
    margin: 0px;
    width: 100%;
    padding: 10px;
    font-size: 25px;
    background: #013a63;
    color: white;
    text-align: center;
    border-radius: 4px;
  }

  .menu {
    display: flex;
    width: 20%;
    height: auto;
    margin: 5px;
    padding: 10px;
    flex-flow: column wrap;
    order: 0;
  }

  .menuBoton, .menuBotonValidar {
    margin-top: 5px;
    color: white;
    background: #0077B6;
    width: 100%;
    border-radius: 4px;
    font-size: 15px;
    padding: 5px 11px;
    list-style: none;
  }

  .menuBotonValidar {
    width: 25%;
  }

  .menuBoton:hover {
    cursor: pointer;
    background: #00B4D8;
  }

  .centro {
    display: flex;
    flex-flow: column wrap;
    width: 100%;
    flex: 8;
  }

  .responderMensaje {
    display: flex;
    align-self: start;
    margin-top: 10px;
    margin-left: 5px;
    background: #FF7F00;
    color: white;
    border-radius: 4px;

  }

  .mensaje {
    display: flex;
    margin-top: 10px;
    flex-flow: row wrap;
    order: 1;
    align-items: center;
    color: black;

  }

  .usuario {
    display: flex;
    flex-flow: column wrap;
    flex: 1;
    align-items: center;
    height: 100%;
    background: #daecef;
  }

  .nombre {
    order: 0;
  }

  a{
    text-decoration: none;
    color: black;
  }

  a:hover {
    text-decoration: none;
    color: black;
  }

  .registro {
    order: 1;
  }

  .imagen {
    width: 50px;
    height: 50px;
    order: 1;
  }

  .registro, .registro p {
    text-align: center;
    font-size: 16px;
  }

  .infoMensaje {
    display: flex;
    flex-flow: column wrap;
    flex: 10;
    height: 100%;
    background: #C0E6ED;
  }

  .arriba {
    display: flex;
    flex-flow: row wrap;
    flex: 1;
    border-bottom: 1px solid #cdd1d1;
    width: 100%;
  }

  .fechaTitulo {
    flex: 10;
    align-items: center;
    display: flex;
    padding-left: 10px;
  }

  .conversacionfechaTitulo {
    align-items: center;
    display: flex;
    padding-left: 10px;
    flex: 1;
  }

  .conversacionfechaTitulo a{
    flex: 1;
    text-decoration: none;
    color: black;
    margin-left: 3px;
  }

  .botonBorrar {
    flex: 1;
    font-size: 30px;
    align-items: center;
    justify-content: center;
    display: flex;
    color: #b21414;
  }


  .textoMensaje {
    flex: 3;
    padding: 10px;
  }
  .subContenedor{
    width: 100%;
    margin: auto;
    display: flex;
    flex-flow: column;
    align-items: center;
    order: 1;
  }

  .conversacion {
    display: flex;
    margin: 10px 0;
    flex-flow: column wrap;
    align-items: center;
    color: black;
    width: 50%;
    background: #C0E6ED;
  }

  @media (max-width: 700px){
	.contenedor{
		padding: 0px;
	}
	.menu {
		width: 100%;
	}

  .usuario {
    flex-flow: row wrap;
    width: 100%;
    flex:
  }

  .imagen {
    order: 0;
  }

  .fechaTitulo {
    display: none;
  }

  .conversacion {
    width: 90%;
    display: flex;
    margin: 10px;
  }

  .conversacionfechaTitulo {
    width: 100%;
  }

  .menuBotonValidar {
    width: 100%;
  }

  .botonBorrar {
    justify-content: end;
    padding-right: 10px;
  }

  .nombre, .registro {
    order: 1;
    flex: 2;
    font-size: 15px;
    font-size: 15px;
    text-align: center;
  }

  .infoMensaje {
    width: 100%;
  }

  .mensaje{
    flex-flow: column wrap;
		width: 100%;
		height: auto;
		justify-content: center;
  }

}

</style>