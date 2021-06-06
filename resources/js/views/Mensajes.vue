<template>
  <div class="contenedor">
    <div class="cabecera">Mensajes privados Jojo</div>

    <div class="menu">

      <li class="menuBoton" v-b-modal="'modal-enviarMensaje'" v-if="current_user.email_verified_at"> Nuevo mensaje <modal-enviarMensaje :current_user="current_user" :tituloModal="'Enviar Mensaje'" /></li>
      <li class="menuBoton" v-b-modal="'modal-enviarMensaje'" style="background-color: #2B4562" v-else> Valida tu email para poder enviar mensajes privados </li>
      <li class="menuBoton" @click="getMensajesRecibidos">Recibidos <template v-if="opcion == 1"> <span class="flecha">←</span> </template></li>
      <li class="menuBoton" @click="getMensajesEnviadosLeidos">Enviados leidos <template v-if="opcion == 2">  <span class="flecha">←</span> </template></li>
      <li class="menuBoton" @click="getMensajesEnviadosNoLeidos">Enviados sin leer <template v-if="opcion == 3">  <span class="flecha">←</span> </template></li>

    </div>

    <div class="mensajes">
      <div class="listaMensajes">
        <div class="cabeceraMensajes">Ver mensajes</div>
        <div class="cuadroMensaje" v-for="(item, index) in mensajes" :key="index">
          <template v-if="item.borrado_receptor == 'No' && opcion == 1 || item.borrado_emisor == 'No' && opcion == 2 || item.borrado_emisor == 'No' && opcion == 3">
            <div class="iconoMensaje">
              <router-link :to="{ name: 'leer mensaje', query: {mensaje_id: item.id, conversacion_id: item.conversacion_id, opcion: opcion } }">
                <template v-if="item.leido == 'No'"><i class="bi bi-envelope-fill"></i></template>
                <template v-else><i class="bi bi-envelope-open-fill"></i></template>
              </router-link>
            </div>
            <div class="infoMensaje" >
              <div class="titulo"><router-link :to="{ name: 'leer mensaje', query: {mensaje_id: item.id, conversacion_id: item.conversacion_id, opcion: opcion} }" style="color:black">{{item.titulo}}</router-link></div>
              <div class="autor"><template v-if="opcion == 1"><a class="perfilUser" :href="'usuario/'+item.emisor_id">Origen: {{item.emisor.name}} </a></template><template v-else><a class="perfilUser" :href="'usuario/'+item.receptor_id">Destino: {{item.receptor.name}}</a></template> ({{item.created_at | formatDate}})</div>
            </div>
            <div class="checkbox"><input type="checkbox" :value=item.id v-model="mensajesSeleccionados"></div>
          </template>
        </div>
          <template v-if="vacio == true"><div class="infoMensaje" style="padding 10px">No hay mensajes</div></template>
      </div>
      <div class="eliminar">
        <li class="botonBorrar" @click="borrarMensajes">Eliminar mensajes marcados</li>
      </div>
    </div>




  </div>
</template>

<script>
import moment from 'moment'
import Multiselect from 'vue-multiselect'
import Swal from 'sweetalert2'

export default {
  components: { Multiselect },
  props : ['current_user'],

  mounted() {
    if(this.$route.params.opcionParam){
      this.opcion = this.$route.params.opcionParam
    }else {
      this.opcion = 1
    }
    if(this.opcion == 1){
      this.getMensajesRecibidos()
    }else if(this.opcion == 2){
      this.getMensajesEnviadosLeidos()
    }else{
      this.getMensajesEnviadosNoLeidos()
    }

  },
  data() {
    return {
      mensajes: null,
      opcion: null,
      mensajesSeleccionados: [],
      listaUsers: [],
      form: {
        titulo: null,
        mensaje: null,
        receptor_id: null,
        emisor_id: this.current_user.id
      },
      vacio: false,
    }
  },
  methods: {
    getMensajesRecibidos(){
			this.vacio = false

      this.mensajesSeleccionados = [],
      axios.get('http://gamereviewsproject.herokuapp.com/api/mensajes_recibidos', {
          params: {
            user_id: this.current_user.id,
          }
      })
        .then(response => {
        this.mensajes = response.data;
        this.opcion = 1
        if(this.mensajes.length === 0){
          this.vacio = true
        }
      })
    },
    getMensajesEnviadosLeidos(){
			this.vacio = false
      this.mensajesSeleccionados = [],
      axios.get('http://gamereviewsproject.herokuapp.com/api/mensajes_enviados_leidos',{
        params: {
          user_id: this.current_user.id,
        }
      })
      .then(response => {
      this.mensajes = response.data;
      this.opcion = 2
        if(this.mensajes.length === 0){
          this.vacio = true
        }
      })
    },
    getMensajesEnviadosNoLeidos(){
			this.vacio = false

      this.mensajesSeleccionados = [],
      axios.get('http://gamereviewsproject.herokuapp.com/api/mensajes_enviados_no_leidos', {
        params: {
          user_id: this.current_user.id,
        }
      })
      .then(response => {
      this.mensajes = response.data;
      this.opcion = 3
      if(this.mensajes.length === 0){
        this.vacio = true
      }
      })
    },
    borrarMensajes(){

      Swal.fire({
        title: "¿Quieres borrar estos mensajes?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, borralos',
      }).then((result) => {
        if(result.isConfirmed){
          axios.get('http://gamereviewsproject.herokuapp.com/api/borrar_mensajes',{
            params: {
              mensajes: JSON.stringify(this.mensajesSeleccionados),
              opcion: this.opcion
            }
          })
          .then(response => {
            switch (this.opcion) {
              case 1:
                this.getMensajesRecibidos()
                break;
              case 2:
                this.getMensajesEnviadosLeidos()
                break;
              case 3:
                this.getMensajesEnviadosNoLeidos()
                break;
            }
            toastr.success('Mensajes borrados');
          })
        }
      })
    },
    leerMensaje(mensajeId, conversacionId){
      this.$router.push({
        name: "leer mensaje",
        params:{
          mensaje_id: mensajeId,
          conversacion_id: conversacionId
        }
      });
    }
  },
  filters: {
      formatDate(value){
      moment.locale('es');
			return moment(String(value)).format('DD MMM YYYY HH:mm')
		}
  }
}
</script>

<style scoped>
  .contenedor {
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

  .menuBoton {
    margin-top: 5px;
    color: white;
    background: #0077B6;
    width: 100%;
    border-radius: 4px;
    font-size: 15px;
    padding: 5px 11px;
    list-style:none
  }

  .flecha {
    font-size: 23px;
  }

  .menuBoton:hover {
    cursor: pointer;
    background: #00B4D8;
  }

  .mensajes {
    display: flex;

    margin-top: 10px;
    flex-flow: column wrap;
    order: 1;
    flex: 8;
    align-items: center;
    color: black;
  }

  .listaMensajes {
    width: 100%;
    background: #c0e6ed;
  }

  .cabeceraMensajes {
    width: 100%;
    padding-left: 30px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 30px;
    font-family: Oswald,sans-serif;
    font-size: 15px;
    text-transform: uppercase;
    background: #0077B6;
    color: white;
  }

  .cuadroMensaje{
    width: 100%;
    display: flex;
    flex-flow: row wrap;
  }

  .iconoMensaje {
    display: flex;
    order: 0;
    font-size: 35px;
    justify-content: center;
    padding: 0 10px;

  }

  .iconoMensaje :hover {
    cursor: pointer;
  }

  .infoMensaje {
    flex: 10;
    border-bottom: 1px solid #cdd1d1;
    flex-flow: column wrap;
    font-weight: bold;
    padding: 5px 0 0 10px;
  }

  .perfilUser {
    text-decoration: none;
    color: black;
  }

  .titulo {
    text-align: left;
    font-size: 14px;
  }

  .titulo :hover {
    cursor: pointer;
  }

  .autor {
    font-size: 12px;
  }

  .checkbox {
    display: flex;
    width: 5%;
    align-items: center;
    justify-content:center;
  }

  .eliminar {
    display: flex;
    width: 100%;
    justify-content: flex-end;
  }

  .botonBorrar {
    margin-top: 5px;
    color: white;
    background: #b21414;
    border-radius: 4px;
    padding: 5px 11px;
    list-style: none;
  }

  .botonBorrar:hover {
    cursor: pointer;
    background: #8c1313;
  }


  .enviarMensaje {
    margin-top: 30px;
    flex: 8;

  }

  .form-row {
    justify-content: center;
  }

@media (max-width: 700px){
	.contenedor{
		padding: 0px;
	}
	.menu {
		width: 100%;
	}

  .mensajes{
    flex-flow: column wrap;
		width: 100%;
		height: auto;
		justify-content: center;
		width:100%;
  }

}


</style>