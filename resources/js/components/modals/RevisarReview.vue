<template>
  <b-modal hide-footer ref="modal" id="modal-RevisarReview" size="xl" title="Revisar reviews" @hidden="cancelData">

    <div v-for="(item,index) in review" :key="index">
      <div class="tarjetaCuadro">
        <div class="superior">
          <div class="titulo"> {{item.games1.titulo}} </div>
          <div class="user"> <a :href="'usuario/'+item.users.id" title="acceder al perfil de este usuario"> {{item.users.name}} </a> </div>
        </div>
        <div class="mensaje" v-html="item.mensaje"></div>
        <div class="pie">
          <div class="aceptar" @click="isAcepted(item.id, item.game_id)"> Aceptar </div>
          <div class="rechazar" v-b-modal.observaciones @click="sendInfo(item.id)"> Rechazar </div>
        </div>
      </div>
    </div>
    <b-modal id="observaciones" title="Second Modal" hide-footer ok-only>

      <template v-if="validationErrors">
        <li v-for="(item, index) in validationErrors" :key="index" class="errorServ">{{item | borraCaracteresEspeciales }}</li>
      </template>

      <label for="observación">Elija motivo</label>
      <select id="observacion" class="form-select mb-3" v-model="form.observacion" @click="checkOtro">
        <option value="Spoilers">Spoilers</option>
        <option value="Faltas de respeto grave">Faltas de respeto grave</option>
        <option value="Pésima ortografía">Pésima ortografía</option>
        <option value="Troll">Troll</option>
        <option value="Otro">Otro (Especifica debajo)</option>
      </select>
      <div v-if="otro">
        <vue-editor v-model="form.observacionTexto" id="mensaje" :editor-toolbar="customToolbar" />
      </div>
      <button v-if="form.observacion != null && form.observacion != 'Otro' || form.observacion =='Otro' && form.observacionTexto" class="btn btn-danger" type="submit" @click="isReject(review_id)">Rechazar</button>
    </b-modal>
  </b-modal>
</template>

<script>
  import  VueEditor from "vue2-editor";

export default {
  mounted() {
    this.obtenerDatos()
  },
  data() {
    return {
      form: {
        review_id: null,
        review_game_id: null,
        observacion: null,
        observacionTexto: null,
      },
      review_id: null,
      review: null,
      otro: false,
      validationErrors: null,
      customToolbar: [
        ["bold", "italic", "underline"],
        [{ list: "ordered" }, { list: "bullet" }],
      ],
    }
  },
  methods: {
    obtenerDatos(){
      axios.get('http://gamereviewsproject.herokuapp.com/api/get_reviews/all')
      .then(response => {
        this.review = response.data
      })
    },
    isAcepted(review_id, game_id) {
      this.form.review_id = review_idid
      this.form.game_id = game_id
      axios.post('http://gamereviewsproject.herokuapp.com/api/reviewAccepted',this.form
      ).then(response => {
          toastr.success('Review aceptada con éxito');
          this.obtenerDatos()
      }).catch(error => {
          toastr.error('Error, no se pudo aceptar la review')
          if (error.response.status == 422){
            this.validationErrors = error.response.data.errors;
          }
      });
    },
    isReject(id){
      this.form.review_id = id
      if(this.form.observacionTexto != null){
        this.form.observacion = this.form.observacionTexto
      }
      axios.post('http://gamereviewsproject.herokuapp.com/api/reviewRejected',this.form)
        .then(response => {
          toastr.success('Review rechazada con éxito');
          this.obtenerDatos()
          this.$bvModal.hide('observaciones')
      }).catch(error => {
          toastr.error('Error, no se pudo rechazar la review')
          if (error.response.status == 422){
              this.validationErrors = error.response.data.errors;
          }
      });
    },
    checkOtro(){
      if(this.form.observacion === 'Otro') {
        this.otro = true
      } else {
        this.otro = false
      }
    },
    sendInfo(item) {
      this.review_id = item;
    },
    cancelData(){
      this.validationErrors = null
      this.form.observacion = null
      this.form.observacionTexto = null
      this.otro = false
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

<style>
  .tarjetaCuadro {
    border: 1px solid;
    margin: 0 0 20px 0;
  }

  .errorServ {
    background: #c82333;
    padding: 10px;
    list-style:none;
    color: white;
  }

  .superior {
    border-bottom: 1px solid;
    display: flex;
    font-size: 20px;
  }

  .titulo, .user{
    flex: 1;
    text-align: center;
  }

  .user a{
    color: black;
    text-decoration: none;
  }

  .acciones {
    border: 1px solid blue;
    text-align: center;
  }

  .mensaje {
    padding: 5px;
  }

  .pie {
    display: flex;
    border-top: 1px solid;
  }

  .aceptar, .rechazar {
    flex: 1;
    text-align: center;
    color: white;
  }

  .aceptar, .rechazar:hover {
    cursor: pointer;
  }

  .aceptar {
    background: #245e13;
    border-right: 1 solid;
  }

  .rechazar {
    background: #b21414;
  }
</style>

