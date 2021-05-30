<template>
  <b-modal hide-footer ref="modal" id="modal-RevisarReview" size="xl" title="Revisar reviews">
    <div v-for="(item,index) in review" :key="index">
      <div class="tarjetaCuadro">
        <div class="superior">
          <div class="titulo"> {{item.games1.titulo}} </div>
          <div class="user"> <a :href="'usuario/'+item.users.id" title="acceder al perfil de este usuario"> {{item.users.name}} </a> </div>
        </div>
        <div class="mensaje" v-html="item.mensaje"></div>
        <div class="pie">
          <div class="aceptar" @click="isAcepted(item.id)"> Aceptar </div>
          <div class="rechazar" v-b-modal.observaciones @click="sendInfo(item.id)"> Rechazar </div>
        </div>
      </div>
    </div>
    <b-modal id="observaciones" title="Second Modal" hide-footer ok-only>
      <label for="observación">Elija motivo</label>
      <select id="observacion" class="form-select mb-3" v-model="observacion" @click="checkOtro">
        <option value="Spoilers">Spoilers</option>
        <option value="Faltas de respeto grave">Faltas de respeto grave</option>
        <option value="Pésima ortografía">Pésima ortografía</option>
        <option value="Troll">Troll</option>
        <option value="Otro">Otro (Especifica debajo)</option>
      </select>
      <div v-if="otro">
        <vue-editor v-model="observacionTexto" id="mensaje" :editor-toolbar="customToolbar" />
      </div>
      <button v-if="observacion != null && observacion != 'Otro' || observacion =='Otro' && observacionTexto" class="btn btn-danger" type="submit" @click="isReject(review_id)">Rechazar</button>
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
      review_id: null,
      review: null,
      observacion: null,
      otro: false,
      observacionTexto: null,
      customToolbar: [
        ["bold", "italic", "underline"],
        [{ list: "ordered" }, { list: "bullet" }],
      ],
    }
  },
  methods: {
    obtenerDatos(){
      axios.get('http://localhost:8000/api/get_reviews/all')
      .then(response => {
        this.review = response.data
      })
    },
    isAcepted(id) {
      axios.post('http://localhost:8000/api/reviewAccepted',{
          params: {
            review_id: id,
          }
        }).then(response => {
          toastr.success('Review aceptada con éxito');
          this.obtenerDatos()
      }).catch(error => {
          toastr.error('Error, no se pudo aceptar la review')
      });
    },
    isReject(id){
      if(this.observacionTexto != null){
        this.observacion = this.observacionTexto
      }
      axios.post('http://localhost:8000/api/reviewRejected',{
          params: {
            review_id: id,
            observacion: this.observacion
          }
        }).then(response => {
          toastr.success('Review rechazada con éxito');
          this.obtenerDatos()
          this.$bvModal.hide('observaciones')
      }).catch(error => {
          toastr.error('Error, no se pudo rechazar la review')
      });
    },
    checkOtro(){
      if(this.observacion === 'Otro') {
        this.otro = true
        // this.observacion = null
      } else {
        this.otro = false
      }
    },
    sendInfo(item) {
      this.review_id = item;
    }
  },
}
</script>

<style>
  .tarjetaCuadro {
    border: 1px solid;
    margin: 0 0 20px 0;
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

