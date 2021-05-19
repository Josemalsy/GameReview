<template>
  <b-modal hide-footer ref="modal" id="modal-AddGame" size="xl" :title="tituloModal" @shown="beforeOpen">
    <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="addGame">

      <div class="form-row">
        <div class="imagen">
          <img class="caratula" :src="this.imagenCargada">
        </div>
        <div class="form-group col-md-12">
          <label class="custom-file-label col-md-12" for="customFileLang">Caratula del juego</label>
          <input type="file" class="custom-file-input col-md-12" id="customFileLang" lang="es" @change="onFileChange">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="Titulo">Título del juego</label>
          <input type="text" class="form-control" id="Titulo" required v-model="form.titulo">
        </div>
        <div class="form-group col-md-6">
          <label for="genero">Género/s del juego</label>
          <multiselect v-model="generos" tag-placeholder="Add this as new tag" placeholder="Busca o añade una plataforma" label="nombre" track-by="id" :options="listaGeneros" :multiple="true" :taggable="true"></multiselect>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="desarrolladora">Desarrolladora del juego </label>
          <input type="text" class="form-control" id="desarrolladora" required v-model="form.desarrolladora">
        </div>
        <div class="form-group col-md-6">
          <label for="Lanzamiento">Lanzamiento del juego</label>
          <input type="date" class="form-control" id="Lanzamiento" required v-model="form.lanzamiento" :max="hoy">
        </div>
      </div>

      <div class="form-group col-md-12">
        <label class="typo__label">Plataformas del juego</label>
        <multiselect v-model="plataformas" tag-placeholder="Add this as new tag" placeholder="Busca o añade una plataforma" label="nombre" track-by="id" :options="listaPlataformas" :multiple="true" :taggable="true"></multiselect>
      </div>

      <button type="submit" class="btn btn-primary">{{tituloModal}}</button>
    </form>
  </b-modal>
</template>

<script>
import moment from 'moment'
import Multiselect from 'vue-multiselect'

export default {
  components: { Multiselect },
  props : ['game_id', 'tituloModal', 'imagenOriginal'],
  mounted() {
    this.getPlataformaData()
    this.getGeneroData()

  },
  data() {
    return {
      hoy: moment().format('YYYY-MM-DD'),
      form: {
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
    }
  },
  methods: {
    beforeOpen(){
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
        this.imagenCargada = '../storage/' +  this.game.imagen
        this.form.lanzamiento = this.game.lanzamiento
      });
    },
    addGame(){
      let formData = new FormData();
      this.jsonPlataformas = JSON.stringify(this.plataformas)
      this.jsonGeneros = JSON.stringify(this.generos)

      formData.append('titulo', this.form.titulo)
      formData.append('generos', this.jsonGeneros)
      formData.append('desarrolladora', this.form.desarrolladora)
      formData.append('imagen', this.form.imagen)
      formData.append('lanzamiento', this.form.lanzamiento)
      formData.append('plataformas', this.jsonPlataformas)
      formData.append('_method', 'POST');

      if(this.tituloModal == 'Actualizar Juego'){
        formData.append('id', this.game_id)

        axios.post('http://localhost:8000/api/juego/updateGame', formData)
        .then(response => {
          toastr.success('juego actualizado con éxito');
          this.$bus.$emit('prueba')
        }).catch(error => {
          toastr.error('Error, no se pudo actualizar el juego')
        });

      }else {

        axios.post('http://localhost:8000/api/juego/add_game', formData)
        .then(response => {
          toastr.success('juego añadido con éxito');
          this.cancelData()
          this.$bus.$emit('prueba')
        }).catch(error => {
          toastr.error('Error, no se pudo agregar el juego')
        });

      }

    },
    onFileChange(e) {
      let file = e.target.files[0]
      this.form.imagen = file
      this.cargarImagen(file)
    },
    cargarImagen(file){
      let reader = new FileReader()
      reader.onload = (e) => {
        this.imagenCargada = e.target.result;
      }
      reader.readAsDataURL(file);
      this.caratula = false
    },
    cancelData(){
      this.form.titulo = null
      this.form.genero = null
      this.form.desarrolladora = null
      this.form.imagen = null
      this.form.lanzamiento = null
      this.form.plataformas = null
      this.form.imagenCargada = null
      this.form.jsonData = null
    },
  }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style>


</style>