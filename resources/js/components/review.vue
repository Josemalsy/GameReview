<template>

  <b-modal hide-footer ref="modal" id="review-modal" @before-open="cancelData" size="xl" title="Agregar review" @hidden="cancelData">
    <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="createReview">
      <div class="form-row">
        <div class="col-md-3 mb-3 valoracion-input">
          <label for="valoracion">¿Qué nota le das al juego de 0 a 100?</label>
          <input type="number" class="form-control" id="valoracion" v-model="form.valoracion" @keyup="porcentajeBarra(form.valoracion),checkValoracion(form.valoracion)" @blur="checkValoracion(form.valoracion)" placeholder="Valoración" min=0 max=100 required>
          <span class="error" v-if="validaciones.checkValoracionVacio">El campo valoracion no puede estar vacio</span>
          <span class="error" v-if="validaciones.checkValoracionBetween">El campo valoracion debe estar entre 0 y 100</span>
        </div>
      </div>

      <div class="progress">
        <div class="progress-bar" role="progressbar" :style="{width: form.valoracion + '%', background: background_barra}" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
      </div>

      <div class="form-row">
        <div class="col-md-3 mb-3">
          <label for="juego_base">Juego Base </label>
          <input type="number" class="form-control" id="juego_base" v-model="form.juego_base" placeholder="Juego Base" @keyup="checkIntervalos" min=0 :max="form.juego_extendido">
          <span class="error" v-if="validaciones.checkJuegoBase">Debe estar entre 0 y juego extendido</span>
        </div>
        <div class="col-md-3 mb-3">
          <label for="juego_extendido">Juego con extras</label>
          <input type="number" class="form-control" id="juego_extendido" v-model="form.juego_extendido" placeholder="Juego con extras" @keyup="checkIntervalos" disabled :min="form.juego_base" :max="form.completado_total">
          <span class="error" v-if="validaciones.checkJuegoExtendidoMenor">Debe estar entre juego_base y juego completado</span>
          <span class="error" v-if="validaciones.checkJuegoExtendidoMayor">Debe ser menor a juego completado</span>
        </div>
        <div class="col-md-3 mb-3">
          <label for="completado_total">Completado al 100%</label>
          <input type="number" class="form-control" id="completado_total" v-model="form.completado_total" placeholder="Completado al 100%" :min="form.juego_extendido" @keyup="checkIntervalos" disabled>
          <span class="error" v-if="validaciones.checkCompletadoTotal">Debe ser mayor a juego extendido</span>
        </div>
      </div>

      <label>Mensaje: </label>
      <vue-editor v-model="form.mensaje" id="mensaje" :editor-toolbar="customToolbar" @keyup="checkMensaje(form.mensaje)" @blur="checkMensaje(form.mensaje)"/>
      <p><span class="error" v-if="validaciones.checkMensaje">El campo mensaje no puede estar vacio</span></p>
      <button class="btn btn-success" type="submit">Enviar review</button>
      <button class="btn btn-danger" type="button" @click.prevent="cancelData">Limpiar formulario</button>
    </form>
  </b-modal>
</template>


<script>
import  VueEditor from "vue2-editor";

  export default {
    props: ['current_user'],
    data() {
      return {
        form : {
          id_game: this.$route.params.id,
          juego_base: null,
          juego_extendido: null,
          completado_total: null,
          mensaje: null,
          valoracion: null,
        },
        validaciones: {
          checkValoracionVacio: false,
          checkValoracionBetween: false,
          checkMensaje: false,
          checkJuegoBase: false,
          checkJuegoExtendidoMenor: false,
          checkJuegoExtendidoMayor: false,
          checkCompletadoTotal: false
        },
        background_barra: null,
        customToolbar: [
          ["bold", "italic", "underline"],
          [{ list: "ordered" }, { list: "bullet" }],
        ],
      }
    },
    methods: {
      porcentajeBarra(value){
        if(value == ''){
          this.background_barra = '#E9ECEF';
        }else if(value < 35){
          this.background_barra = "#ff0000"
        }else if(value < 50){
          this.background_barra = "#ff8000"
        }else if(value < 70){
          this.background_barra = "#ffff00"
        }else if(value < 85){
          this.background_barra = "#6cc414"
        }else {
          this.background_barra = "#005000"
        }
      },
      checkInputsHoras(){
        if(!this.form.juego_base){
          $("#juego_extendido").prop('disabled',true).val(null);
          this.form.juego_extendido = null
        }else {
          $("#juego_extendido").prop('disabled',false);
        }
        if(!this.form.juego_extendido){
          $("#completado_total").prop('disabled', true).val(null);
          this.form.completado_total = null
        }else {
          $("#completado_total").prop('disabled',false);
        }
      },
      checkIntervalos(){
        //COMPRUEBA JUEGO BASE Y EL LIMITE INFERIOR DE JUEGO EXTENDIDO
        if(this.form.juego_base) {
          this.form.juego_base = parseInt(this.form.juego_base);
          $("#juego_extendido").prop('disabled',false);
          $("#juego_base").removeClass().addClass("form-control is-valid");
          if(this.form.juego_extendido){
            $("#completado_total").prop('disabled',false);
            this.form.juego_extendido = parseInt(this.form.juego_extendido);
            if(this.form.juego_base > this.form.juego_extendido){
              this.validaciones.checkJuegoBase = true
              this.validaciones.checkJuegoExtendidoMenor = true
              $("#juego_base").removeClass("is-valid").addClass("is-invalid");
              $("#juego_extendido").removeClass("is-valid").addClass("is-invalid");
            } else {
              this.validaciones.checkJuegoBase = false
              this.validaciones.checkJuegoExtendidoMenor = false
              $("#juego_extendido").removeClass().addClass("form-control is-valid");
            }
          } else {
            this.validaciones.checkJuegoBase = false
            this.validaciones.checkJuegoExtendidoMenor = false
            $("#completado_total").prop('disabled', true).val(null);
            this.form.completado_total = null
            $("#juego_extendido").removeClass().addClass("form-control");
          }
          if(this.form.juego_base < 0){
            this.validaciones.checkJuegoBase = true
            $("#juego_base").removeClass("is-valid").addClass("is-invalid");
          }

          } else if(this.form.juego_base < 0) {

            this.validaciones.checkJuegoBase = true
            $("#juego_base").removeClass("is-valid").addClass("is-invalid");
          }else {
            $("#juego_base").removeClass().addClass("form-control");
            $("#juego_extendido").prop('disabled',true);
            this.form.juego_extendido = null
            $("#juego_extendido").removeClass().addClass("form-control");
        }

        //COMPRUEBA LIMITE SUPERIOR JUEGO EXTENDIDO Y COMPLETADO TOTAL
          if(this.form.juego_extendido){
          $("#completado_total").prop('disabled',false);
          if(this.form.juego_extendido > this.form.completado_total){
            if(this.form.completado_total){
              this.validaciones.checkJuegoExtendidoMayor = true
              this.validaciones.checkCompletadoTotal = true
              $("#juego_extendido").removeClass("is-valid").addClass("is-invalid");
              $("#completado_total").removeClass("is-valid").addClass("is-invalid");
            }else{
              $("#completado_total").removeClass().addClass("form-control");
            }
          } else {
            this.validaciones.checkCompletadoTotal = false
            this.validaciones.checkJuegoExtendidoMayor = false
            $("#completado_total").removeClass("is-invalid").addClass("is-valid");
            if(this.form.juego_base > this.form.juego_extendido) {
              this.validaciones.checkJuegoExtendidoMenor = true
              $("#completado_total").removeClass().addClass("form-control is-invalid");
              $("#juego_extendido").removeClass().addClass("form-control is-invalid");
            }
          }
        } else  {
          $("#completado_total").prop('disabled',true).val(null);
          this.form.completado_total = null
          $("#completado_total").removeClass().addClass("form-control");
        }
      },
      checkValoracion(value){
          if(value == '' || value == null){
            this.validaciones.checkValoracionVacio = true;
            $("#valoracion").removeClass("is-valid").addClass("is-invalid");
          }else if(value < 0 || value > 100){
            this.validaciones.checkValoracionVacio = false;
            this.validaciones.checkValoracionBetween = true;
            $("#valoracion").removeClass("is-valid").addClass("is-invalid");
          }else {
            this.validaciones.checkValoracionVacio = false;
            this.validaciones.checkValoracionBetween = false;
            $("#valoracion").removeClass("is-invalid").addClass("is-valid");
          }
      },
      checkMensaje(value){
        if(value == '' || value == null){
          this.validaciones.checkMensaje = true;
        }else {
          this.validaciones.checkMensaje = false;
        }
      },
      createReview(){
        axios.post('http://localhost:8000/api/review/post_review', {
          params: {
            formulario: this.form,
          }
        }).then(response => {
          toastr.success('review realizada');
          this.$refs["modal"].hide();
          this.$bus.$emit('prueba')
        }).catch(error => {
          toastr.error('Error, no se pudo agregar la review')
        });
      },
      cancelData(){
        this.form.juego_base = null
        this.form.juego_extendido = null
        this.form.completado_total = null
        this.form.valoracion = null
        this.form.mensaje = null
        this.background_barra = '#E9ECEF'
        this.validaciones.checkValoracionVacio = false
        this.validaciones.checkValoracionBetween = false
        this.validaciones.checkMensaje = false
        this.validaciones.checkJuegoBase = false
        this.validaciones.checkJuegoExtendidoMenor = false
        this.validaciones.checkJuegoExtendidoMayor = false
        this.validaciones.checkCompletadoTotal = false
      },
      onClose(type) {
        this.cancelData()
      }
    },
  }
</script>

<style scoped>
  .form-row{
    justify-content: center;
  }

  .valoracion-input {
    justify-content: center;
    align-items: center;
  }

  .form-control {
    margin-bottom: 10px;
  }

  .error {
    color: red;
    font-size: 12px;
  }
</style>
