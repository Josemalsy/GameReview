<template>
  <b-modal hide-footer ref="modal" id="modal-enviarMensaje" size="xl" :title="tituloModal" @shown="beforeOpen" @hidden="cancelData">

    <div class="enviarMensaje">
      <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="enviarMensaje">

        <template v-if="validationErrors">
          <li v-for="(item, index) in validationErrors" :key="index" class="errorServ">{{item | borraCaracteresEspeciales }}</li>
        </template>

        <div class="form-row">
          <div class="col-md-12 mb-12">
            <label for="destinatario">Destinatario </label>
            <template v-if="tituloModal == 'Responder Mensaje' || tituloModal == 'Mandar mensaje'">
              <input type="text" class="form-control" id="destinatario" v-model="nombreDestino" disabled>
            </template>
            <template v-else>
              <multiselect id="destinatario" v-model="form.receptor_id" class="selectpicker" label="name" track-by="id" :options="listaUsers" data-live-search="true" @input="compruebaDestino(form.receptor_id)"></multiselect>
            </template>
              <span class="error" v-if="checkDestino == false">Debe indicar un destinatario</span>
              <span class="valido" v-if="checkDestino == true">Destinatario válido</span>

          </div>
          <div class="col-md-12 mb-12">
            <label for="titulo">Titulo</label>
            <template v-if="tituloModal == 'Responder Mensaje'">
              <input type="text" class="form-control" id="titulo" v-model="form.titulo" placeholder="Titulo" :placeholder="form.titulo" @keyup="validarTitulo(form.titulo)" disabled>
            </template>
            <template v-else>
              <input type="text" class="form-control" id="titulo" v-model="form.titulo" placeholder="Titulo" :placeholder="form.titulo" @keyup="validarTitulo(form.titulo)">
            </template>
            <span class="error" v-if="checkTitulo == false">El contenido del título no puede estar vacío</span>
            <span class="error" v-if="checkLongitudTitulo == false">El título no puede superar los 100 caracteres</span>
          </div>
          <div class="col-md-12 mb-12">
            <label for="mensaje">Mensaje  </label>
            <vue-editor v-model="form.mensaje" id="mensaje" :editor-toolbar="customToolbar"/>
            <span class="error" v-if="checkMensaje == false" >El contenido del mensaje no puede estar vacío</span>
            <span v-if="form.mensaje">{{validarMensaje(form.mensaje)}}</span>
          </div>
        </div>
          <button type="submit" class="btn btn-primary" v-if="form.receptor_id && checkTitulo == true && checkLongitudTitulo == true && checkMensaje == true">{{tituloModal}}</button>
      </form>
    </div>

  </b-modal>
</template>

<script>
import Multiselect from 'vue-multiselect'

export default {
  components: { Multiselect },
  props : ['current_user', 'tituloModal', 'tituloMensaje', 'emisor_id', 'conversacion_id', 'nombreDestino', 'opcion', 'receptor_id'],
  data() {
    return {
      listaUsers: [],
      checkMensaje: null,
      checkTitulo: null,
      checkLongitudTitulo: null,
      checkDestino: null,
      validationErrors: null,
      form: {
        titulo: null,
        mensaje: null,
        receptor_id: null,
        emisor_id: this.current_user.id,
        conversacion_id: null,
        modoEnvio: this.tituloModal
      },
      customToolbar: [
        ["bold", "italic", "underline"],
        [{ list: "ordered" }, { list: "bullet" }],
      ],
    }
  },
  methods: {
    beforeOpen(){
        this.getDatosUsuarios()
      if( this.tituloModal == 'Responder Mensaje'){
        this.form.titulo = this.tituloMensaje
        this.form.receptor_id = this.receptor_id
        this.form.conversacion_id = this.conversacion_id
        this.checkDestino = true
        this.checkTitulo = true
        this.checkLongitudTitulo = true
      } else if( this.tituloModal == 'Mandar mensaje'){
        this.checkDestino = true
        this.form.receptor_id = this.receptor_id
      }
    },
    getDatosUsuarios(){
      axios.get('http://gamereviewsproject.herokuapp.com/api/getAllUsers').then(response => {
        this.listaUsers = response.data;
      })
    },
    enviarMensaje(){
      this.validarMensaje(this.form.mensaje)
      if(this.checkDestino == true && this.checkMensaje == true && this.checkLongitudTitulo == true && this.current_user != false){

        if(this.tituloModal == "Enviar Mensaje" || this.tituloModal == "Mandar mensaje"){

          axios.post('http://gamereviewsproject.herokuapp.com/api/enviar_mensaje',this.form)
          .then(response => {
            this.$refs["modal"].hide()
            this.$bus.$emit('prueba')
            toastr.success('Mensaje enviado con éxito')
          }).catch(error => {
            toastr.error('Error, no se pudo enviar el mensaje')
            if (error.response.status == 422){
              this.validationErrors = error.response.data.errors;
            }
          });

        } else {
          this.form.receptor_id = this.receptor_id
          this.form.conversacion_id = this.conversacion_id
          axios.post('http://gamereviewsproject.herokuapp.com/api/responder_mensaje', this.form).then(response => {
            this.$refs["modal"].hide()
            this.$bus.$emit('prueba')
            toastr.success('Mensaje respondido con éxito')
          }).catch(error => {
            toastr.error('Error, no se pudo responder el mensaje')
            if (error.response.status == 422){
              this.validationErrors = error.response.data.errors;
            }
          });
        }

      }
    },
    validarTitulo(value){
      this.checkTitulo = true
      this.checkLongitudTitulo = true

      let espacios

      value ? espacios = /(?!^ +$)^.+$/.test(value.trim()) : espacios = false

      if(value){
        espacios == true
      }

      if(!value || !espacios ){
        this.checkTitulo = false
        $("#titulo").removeClass("is-valid").addClass("is-invalid");
      } else {
        this.checkTitulo = true
        $("#titulo").removeClass("is-invalid").addClass("is-valid");

      }
    },
    validarMensaje(value){

      let espacios

      let patron = /(?!^ +$)^.+$/

      value = value.trim()

      if(!value || !patron) {
        this.checkMensaje = false
      }else {
        this.checkMensaje = true

      }

    },
    compruebaDestino(value){
      this.checkDestino = true
      if(value == '' || value == null){
        this.checkDestino = false
      }else {
        this.checkDestino = true
      }
    },
    cancelData(){
      this.form.titulo = null
      this.form.mensaje = null
      this.form.receptor_id = null
      this.checkDestino = null
      this.validationErrors = null
      this.checkMensaje = null
      this.checkTitulo = null
      this.checkLongitudTitulo = null
    },
    onClose(type) {
      this.cancelData()
    }
  },
  filters: {
    borraCaracteresEspeciales(value){
      for(let i = 0; i <= value.length;i++){
        return value[i]
      }
    }
  }

}
</script>

<style scoped>

  .error {
    color: red;
    font-size: 12px;
  }

  .valido {
    color: green;
    font-size: 12px;
  }

.errorServ {
  background: #c82333;
  padding: 10px;
  list-style:none;
  color: white;
}

</style>