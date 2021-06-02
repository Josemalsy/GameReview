<template>
  <b-modal hide-footer ref="modal" id="modal-AddGame" size="xl" :title="tituloModal" @shown="beforeOpen" @hide="cancelData">
    <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="addGame">

    <template v-if="validationErrors">
      <li v-for="(item, index) in validationErrors" :key="index" class="errorServ">{{item | borraCaracteresEspeciales }}</li>
    </template>
    <li v-if="errorPlataformasGeneros" class="errorServ">{{errorPlataformasGeneros}}</li>

      <div class="form-row">
        <div class="imagen">
          <img class="caratula" :src="this.imagenCargada">
        </div>
        <div class="form-group col-md-12">
          <label class="custom-file-label col-md-12" for="customFileLang">Caratula del juego</label>
          <input type="file" id="porta-imagen" class="custom-file-input col-md-12" lang="es" @change="onFileChange">
          <span class="error" v-if="imagenType == false">Formato de imagen no válido. (Válidos: PNG/JPG/JPEG/WEBJ)</span>
          <p><span class="error" v-if="imagenSyze == false">Imagen demasiado pesada. (Máximo: 500kb)</span></p>
          <span class="valido" v-if="imagenType == true">Formato de imagen válido.</span>

        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="Titulo">Título del juego</label>
          <input type="text" class="form-control" id="titulo" v-model="form.titulo" @keyup="compruebaTitulo(form.titulo)" @blur="compruebaTitulo(form.titulo)">
          <span class="error" v-if="checkTitulo == false">El título del juego no puede estar vacío</span>
        </div>
        <div class="form-group col-md-6">
          <label for="genero">Género/s del juego</label>
          <multiselect v-model="generos" id="generos" tag-placeholder="Add this as new tag" placeholder="Busca o añade una plataforma" label="nombre" track-by="id" :options="listaGeneros" :multiple="true" :taggable="true" @input="compruebaGeneros(generos)"></multiselect>
          <span class="error" v-if="checkGenero == false">Debe seleccionar al menos un género</span>
          <span class="valido" v-if="checkGenero == true">Géneros válidos.</span>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="desarrolladora">Desarrolladora del juego </label>
          <input type="text" class="form-control" id="desarrolladora" v-model="form.desarrolladora" @keyup="compruebaDesarrolladora(form.desarrolladora)" @blur="compruebaDesarrolladora(form.desarrolladora)">
          <span class="error" v-if="checkDesarrolladora == false">La desarrolladora del juego no puede estar vacío</span>
        </div>
        <div class="form-group col-md-6">
          <label for="Lanzamiento">Lanzamiento del juego</label>
          <input type="date" class="form-control" id="Lanzamiento" v-model="form.lanzamiento" :max="hoy" @change="compruebaLanzamiento(form.lanzamiento)" @blur="compruebaLanzamiento(form.lanzamiento)">
          <span class="error" v-if="checkLanzamiento == false">La fecha de lanzamiento del juego no puede estar vacío</span>
          <span class="valido" v-if="checkLanzamiento == true">Fecha de lanzamiento correcta.</span>
        </div>
      </div>

      <div class="form-group col-md-12">
        <label class="typo__label">Plataformas del juego</label>
        <multiselect v-model="plataformas" tag-placeholder="Add this as new tag" id="plataformas" placeholder="Busca o añade una plataforma" label="nombre" track-by="id" :options="listaPlataformas" :multiple="true" :taggable="true" @input="compruebaPlataformas(plataformas)"></multiselect>
        <span class="error" v-if="checkPlataforma == false">Debe seleccionar al menos una plataforma</span>
        <span class="valido" v-if="checkPlataforma == true">Plataformas válidas.</span>
      </div>

      <button type="submit" class="btn btn-primary" v-if="imagenType == true && imagenSyze == true && checkTitulo == true && checkGenero == true && checkDesarrolladora == true  && checkLanzamiento == true && checkPlataforma == true">{{tituloModal}}</button>
    </form>
  </b-modal>
</template>

<script>
import moment from 'moment'
import Multiselect from 'vue-multiselect'

export default {
  components: { Multiselect },
  props : ['game_id', 'tituloModal', 'imagenOriginal'],
  data() {
    return {
      hoy: moment().format('YYYY-MM-DD'),
      form: {
        id:null,
        titulo: null,
        genero: null,
        desarrolladora: null,
        imagen: null,
        lanzamiento: null,
      },
      plataformas: null,
      listaPlataformas: [],
      generos: null,
      listaGeneros: [],
      imagenCargada: null,
      jsonData: null,
      game: null,

      imagenType: null,
      imagenSyze: null,
      checkTitulo: null,
      checkGenero: null,
      checkDesarrolladora: null,
      checkLanzamiento: null,
      checkPlataforma: null,
      validationErrors: null,
      errorPlataformasGeneros: null,
    }
  },
  methods: {
    beforeOpen(){
        this.getPlataformaData()
        this.getGeneroData()
      if(this.tituloModal == 'Actualizar Juego'){
        this.obtenerDatos()
      }
    },
    getPlataformaData(){
      axios.get('http://localhost:8000/api/getPlataformas')
        .then(response => {
        this.listaPlataformas = response.data;
      })
    },
    getGeneroData(){
      axios.get('http://localhost:8000/api/getGeneros')
        .then(response => {
        this.listaGeneros = response.data;
      })
    },
    obtenerDatos() {
      axios.get('http://localhost:8000/api/juego/' + this.game_id).then(response =>{
        this.game = response.data[0]
        this.form.titulo = this.game.titulo
        this.generos = this.game.generos
        this.form.desarrolladora = this.game.desarrolladora
        this.plataformas = this.game.plataformas
        this.imagenCargada = '/../storage/' +  this.game.imagen
        this.form.lanzamiento = this.game.lanzamiento
        this.form.id = this.game_id
      });

      this.checkGenero = true
      this.checkPlataforma = true
      this.imagenSyze = true
      this.imagenType = true
      this.checkLanzamiento = true
      this.checkDesarrolladora = true
      this.checkTitulo = true
    },
    addGame(){
      let formData = new FormData();
      this.jsonPlataformas = JSON.stringify(this.plataformas)
      this.jsonGeneros = JSON.stringify(this.generos)

      formData.append('titulo', this.form.titulo ? this.form.titulo : '')
      formData.append('generos', this.jsonGeneros)
      formData.append('desarrolladora', this.form.desarrolladora ? this.form.desarrolladora : '')
      formData.append('imagen', this.form.imagen ? this.form.imagen : '')
      formData.append('lanzamiento', this.form.lanzamiento ? this.form.lanzamiento : '')
      formData.append('plataformas', this.jsonPlataformas)
      formData.append('_method', 'POST');

      if(this.tituloModal == 'Actualizar Juego'){
        formData.append('id', this.form.id)
        formData.append('imagen', this.imagenCargada)

        axios.post('http://localhost:8000/api/juego/updateGame', formData)
        .then(response => {
          toastr.success('juego actualizado con éxito');
          this.$bus.$emit('prueba')
          this.$refs["modal"].hide();
        }).catch(error => {
          toastr.error('Error, no se pudo actualizar el juego')
          if(error.response.status == 420){
              this.errorPlataformasGeneros = error.response.data.message
          }else if (error.response.status == 422){
              this.validationErrors = error.response.data.errors;
            }
        });

      }else {

        axios.post('http://localhost:8000/api/juego/add_game', formData)
        .then(response => {
          toastr.success('juego añadido con éxito');
          this.cancelData()
          this.$bus.$emit('prueba')
          this.$refs["modal"].hide();
        }).catch(error => {
          toastr.error('Error, no se pudo agregar el juego')
          if(error.response.status == 420){
              this.errorPlataformasGeneros = error.response.data.message
          }else if (error.response.status == 422){
            this.validationErrors = error.response.data.errors;
          }
        });

      }

    },
    onFileChange(e) {
      let file = e.target.files[0]
      this.form.imagen = file
      if(file.type == "image/png" || file.type == "image/jpg" || file.type == "image/jpeg" || file.type == "image/webp"   ){
        if(file.size < 500000){
          this.imagenType = true
          this.imagenSyze = true
          this.cargarImagen(file)
        }else {
          this.imagenSyze = false
        }
      }else {
        this.imagenType = false
      }
    },
    cargarImagen(file){
      let reader = new FileReader()
      reader.onload = (e) => {
        this.imagenCargada = e.target.result;
      }
      reader.readAsDataURL(file);
      this.caratula = false
    },
    compruebaTitulo(value){
      this.checkTitulo = false
      let espacios

      value ? espacios = !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]+)*$/.test(value.trim()) : espacios = false

      if(value == '' || value == null || espacios){
        this.checkTitulo = false
        $("#titulo").removeClass("is-valid").addClass("is-invalid");
      }else {
        $("#titulo").removeClass("is-invalid").addClass("is-valid");
        this.checkTitulo = true
      }
    },
    compruebaDesarrolladora(value){
      this.checkDesarrolladora = true
      let espacios

      value ? espacios = !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]+)*$/.test(value.trim()) : espacios = false

      if(value == '' || espacios || value == null){
        this.checkDesarrolladora = false
        $("#desarrolladora").removeClass("is-valid").addClass("is-invalid");
      }else {
        $("#desarrolladora").removeClass("is-invalid").addClass("is-valid");
        this.checkDesarrolladora = true
      }
    },
    compruebaLanzamiento(value){
      this.checkLanzamiento = true
      if(value == '' || value == null){
        this.checkLanzamiento = false
      }else {
        this.checkLanzamiento = true
      }
    },
    compruebaPlataformas(value){
      this.checkPlataforma = true
      if(value == '' || value == null){
        this.checkPlataforma = false
      }else {
        this.checkPlataforma = true
      }
    },
    compruebaGeneros(value){
      this.checkGenero = true
      if(value == '' || value == null){
        this.checkGenero = false
      }else {
        this.checkGenero = true
      }
    },
    cancelData(){
      this.form.titulo = null
      this.genero = null
      this.form.desarrolladora = null
      this.form.imagen = null
      this.form.lanzamiento = null
      this.plataformas = null
      this.imagenCargada = null
      this.form.jsonData = null
      this.errorPlataformasGeneros = null
      this.validationErrors = null
      this.imagenType = null

      this.imagenType = null
      this.imagenSyze = null
      this.checkTitulo = null
      this.checkGenero = null
      this.checkDesarrolladora = null
      this.checkLanzamiento = null
      this.checkPlataforma = null
      this.validationErrors = null
      this.errorPlataformasGeneros = null
    },
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

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
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