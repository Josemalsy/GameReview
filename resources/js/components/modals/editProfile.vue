<template>
  <b-modal hide-footer ref="modal" id="modal-editProfile" size="xl" title="Editar usuario">
    <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="updateUser">
        <div class="form-row">
          <div class="imagen">
            <img class="caratula" :src="'../storage/'+ form.avatar" v-if="caratula">
            <img class="caratula" :src="imagen" v-if="!caratula">
          </div>
          <div class="form-group col-md-12">
            <label class="custom-file-label col-md-12" for="customFileLang">Avatar (opcional) </label>
            <input type="file" class="custom-file-input col-md-12" id="customFileLang" lang="es" @change="onFileChange">
          </div>
        </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="name">Nombre de usuario </label>
          <input type="text" class="form-control" id="name" required v-model="form.name">
        </div>
        <div class="form-group col-md-6">
          <label for="email">Correo Electrónico</label>
          <input type="email" class="form-control" id="email" required v-model="form.email">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="FecNac">Fecha de nacimiento</label>
          <input id="FecNac" type="date" class="form-control" name="FecNac" v-model="form.FecNac" :max="hoy">
        </div>
        <div class="form-group col-md-6">
          <label for="sexo">Sexo</label>
            <select id="sexo" class="form-select mb-3" v-model="form.sexo" >
              <option value="Hombre">Hombre</option>
              <option value="Mujer">Mujer</option>
              <option Value="Otro">Otro</option>
            </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="ocupacion">Ocupacion</label>
          <input id="ocupacion" type="text" class="form-control" name="ocupacion" v-model="form.ocupacion" >
        </div>
        <div class="form-group col-md-6">
          <label for="Ubicacion">Ubicacion</label>
          <input id="Ubicacion" type="text" class="form-control" name="ubicacion" v-model="form.ubicacion" >
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-12">
          <label for="aficiones">Aficiones</label>
          <input id="aficiones" type="text" class="form-control" name="aficiones" v-model="form.aficiones" >
        </div>
      </div>
    <div class="form-row">
      <div class="form-group col-md-12">
        <label for="biografia">Biografía</label>
        <textarea class="form-control" id="biografia" name="biografia" v-model="form.biografia"> </textarea>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Editar perfil</button>
    </form>
  </b-modal>
</template>

<script>
import moment from 'moment'

export default {
  props : ['current_user'],
  data() {
    return {
      hoy: moment().format('YYYY-MM-DD'),
      usuario: null,
      caratula: true,
      form: {
        id: this.current_user.id,
        name: this.current_user.name,
        email: this.current_user.email,
        FecNac: this.current_user.FecNac,
        sexo: this.current_user.sexo,
        ocupacion: this.current_user.ocupacion,
        ubicacion: this.current_user.ubicacion,
        aficiones: this.current_user.aficiones,
        biografia: this.current_user.biografia,
        avatar: this.current_user.avatar,
      },
      imagenCargada: null,
    }
  },
  methods: {
    onFileChange(e) {
      let file = e.target.files[0]
      this.form.avatar = file
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
    updateUser(){
      let formData = new FormData();
      formData.append('id', this.form.id)
      formData.append('name', this.form.name)
      formData.append('email', this.form.email)
      formData.append('FecNac', this.form.FecNac)
      formData.append('sexo', this.form.sexo)
      formData.append('ocupacion', this.form.ocupacion)
      formData.append('ubicacion', this.form.ubicacion)
      formData.append('aficiones', this.form.aficiones)
      formData.append('biografia', this.form.biografia)
      formData.append('avatar', this.form.avatar)
      formData.append('_method', 'POST');

      axios.post('http://localhost:8000/api/actualizarUsuario', formData)
      .then(response => {
        toastr.success('review realizada');
        this.$refs["modal"].hide();
        this.$bus.$emit('prueba')
      }).catch(error => {
        toastr.error('Error, no se pudo agregar la review')
      });
    }

  },
  computed: {
    imagen(){
      return this.imagenCargada
    }
  }
}
</script>

<style>
.caratula{
  width: 100px;
  height: 100px;
}

.imagen {
  display: flex;
  width: 100%;
  justify-content: center;
  margin: 10px;
}

</style>