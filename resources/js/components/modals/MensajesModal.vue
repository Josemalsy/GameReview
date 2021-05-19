<template>
  <b-modal hide-footer ref="modal" id="modal-enviarMensaje" size="xl" :title="tituloModal" @shown="beforeOpen" @hidden="cancelData">

    <div class="enviarMensaje">
      <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="enviarMensaje">
        <div class="form-row">
          <div class="col-md-12 mb-12">
            <label for="destinatario">Destinatario </label>
            <template v-if="tituloModal == 'Responder Mensaje' || tituloModal == 'Mandar mensaje'">
              <input type="text" class="form-control" id="destinatario" v-model="nombreDestino" disabled>
            </template>
            <template v-else>
              <multiselect id="destinatario" v-model="form.receptor_id" class="selectpicker" label="name" track-by="id" :options="listaUsers" data-live-search="true"></multiselect>
            </template>
              <span class="error" v-if="!form.receptor_id">Debe indicar un destinatario</span>
          </div>
          <div class="col-md-12 mb-12">
            <label for="titulo">Titulo </label>
            <input type="text" class="form-control" id="titulo" v-model="form.titulo" placeholder="Titulo" :placeholder="form.titulo" @keyup="validarTitulo(form.titulo)" @blur="validarTitulo(form.titulo)">
            <span class="error" v-if="checkTitulo">El contenido del título no puede estar vacío</span>
            <span class="error" v-if="checkLongitudTitulo">El título no puede superar los 100 caracteres</span>
          </div>
          <div class="col-md-12 mb-12">
            <label for="mensaje">Mensaje </label>
            <vue-editor v-model="form.mensaje" id="mensaje" :editor-toolbar="customToolbar" @keyup="validarMensaje(form.mensaje)" @blur="validarMensaje(form.mensaje)"/>
            <span class="error" v-if="checkMensaje && !form.mensaje">El contenido del mensaje no puede estar vacío</span>
          </div>
        </div>
          <button type="submit" class="btn btn-primary" v-if="form.receptor_id && form.titulo && form.titulo.length <= 100 && form.mensaje">{{tituloModal}}</button>
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
      checkDestino: false,
      checkMensaje: false,
      checkTitulo: false,
      checkLongitudTitulo: false,
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
        this.form.titulo = "RE: " + this.tituloMensaje;
        this.form.receptor_id = this.emisor_id;
      } else if( this.tituloModal == 'Mandar mensaje'){
        this.form.receptor_id = this.receptor_id
      }
    },
    getDatosUsuarios(){
      axios.get('http://localhost:8000/api/getAllUsers').then(response => {
        this.listaUsers = response.data;
      })
    },
    enviarMensaje(){
          console.log(this.tituloModal)

      if(!this.checkDestino && !this.checkMensaje && !this.checkTitulo && !this.checkLongitudTitulo){

        if(this.tituloModal == "Enviar Mensaje" || this.tituloModal == 'Mandar mensaje'){

          axios.post('http://localhost:8000/api/enviar_mensaje',{
            params: {
              formulario: this.form,
            }
          }).then(response => {
            this.$refs["modal"].hide();
            this.$bus.$emit('prueba')
          })

        } else {
          this.form.receptor_id = this.receptor_id
          this.form.conversacion_id = this.conversacion_id
          axios.post('http://localhost:8000/api/responder_mensaje',{
            params: {
              formulario: this.form,
            }
          }).then(response => {
            this.$refs["modal"].hide();
            this.$bus.$emit('prueba')
          })
        }

      }
    },
    validarTitulo(value){
      this.checkTitulo = false
      this.checkLongitudTitulo = false
      if(value == '' || value == null){
        this.checkTitulo = true
        $("#titulo").removeClass("is-valid").addClass("is-invalid");
      }else {
        $("#titulo").removeClass("is-invalid").addClass("is-valid");
        if(value.length >= 100){
          this.checkLongitudTitulo = true
          $("#titulo").removeClass("is-valid").addClass("is-invalid");
        }else {
          $("#titulo").removeClass("is-invalid").addClass("is-valid");
        }
      }

    },
    validarMensaje(value){
      this.checkMensaje = false
      if(value == '' || value == null){
        this.checkMensaje = true
      }
    },
    cancelData(){
      this.form.titulo = null
      this.form.mensaje = null
      this.form.emisor_id = null
      this.form.receptor_id = null
    },
    onClose(type) {
      this.cancelData()
    }
  },

}
</script>

<style scoped>

  .error {
    color: red;
    font-size: 12px;
  }

</style>