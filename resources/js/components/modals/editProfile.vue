<template>
  <b-modal hide-footer ref="modal" id="modal-editProfile" size="xl" title="Editar usuario">
    <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="updateUser">
    <template v-if="validationErrors">
      <li v-for="(item, index) in validationErrors" :key="index">{{item}}</li>
    </template>
        <div class="form-row">
          <div class="imagen">
            <img class="caratula" :src="'../storage/'+ form.avatar" v-if="caratula">
            <img class="caratula" :src="imagen" v-if="!caratula">
          </div>
          <div class="form-group col-md-12">
            <label class="custom-file-label col-md-12" for="customFileLang">Avatar (opcional) </label>
            <input type="file" class="custom-file-input col-md-12" id="customFileLang" lang="es" @change="onFileChange">
            <span class="error" v-if="imagenType == false">Formato de imagen no válido. (Válidos: PNG/JPG/JPEG/WEBJ). No se previsualiza la imagen</span>
            <p><span class="error" v-if="imagenSyze == false">Imagen demasiado pesada. (Máximo: 20kb). No se previsualiza la imagen</span></p>
            <span class="valido" v-if="imagenType == true && imagenSyze == true">Formato de imagen válido.</span>
            <p><div class="btn btn-danger" id="customFileLang" lang="es" @click="borraImagen"> Borrar imagen </div></p>
          </div>
        </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="name">Nombre de usuario</label>
          <input type="text" class="form-control" id="name" v-model="form.name" @keyup="compruebaNombre(form.name)" @blur="compruebaNombre(form.name)">
          <span class="error" v-if="checkName == false">El nombre de usuario no puede estar vacío</span>

        </div>
        <div class="form-group col-md-6">
          <label for="email">Correo Electrónico </label>
          <input type="email" class="form-control" id="email" required v-model="form.email" @keyup="compruebaEmail(form.email)" @blur="compruebaEmail(form.email)">
          <span class="error" v-if="checkEmail == false">Formato de correo electrónico inválido</span>
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

      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="password">Nueva contraseña</label>
          <input type="password" id="password" class="form-control" name="password" v-model="form.password" @keyup="compruebaContraseñas(repitePassword)">
          <span class="error" v-if="checkPassword == false">Las contraseñas no coinciden</span>
          <span class="valido" v-if="checkPassword == true">Las contraseñas coinciden</span>
        </div>
        <div class="form-group col-md-6">
          <label for="repitePassword">Repite la contraseña</label>
          <input type="password" id="repitePassword" class="form-control" name="repitePassword" v-model="repitePassword" @keyup="compruebaContraseñas(repitePassword)">
          <span class="error" v-if="checkPassword == false">Las contraseñas no coinciden</span>
          <span class="valido" v-if="checkPassword == true">Las contraseñas coinciden</span>
        </div>
      </div>

    <button type="submit" class="btn btn-primary" v-if="checkName == true && checkEmail == true && imagenSyze == true && imagenType == true && checkPassword != false">Editar perfil</button>
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
        password: null,
      },
      imagenCargada: null,
      repitePassword: null,
      imagenType: true,
      imagenSyze: true,
      checkName: true,
      checkEmail: true,
      checkPassword: null,
      validationErrors: null,
    }
  },
  methods: {
    onFileChange(e) {
      let file = e.target.files[0]
      this.form.avatar = file
        if(file.type == "image/png" || file.type == "image/jpg" || file.type == "image/jpeg" || file.type == "image/webp"){
          if(file.size < 20000){
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
      formData.append('password', this.form.password)
      formData.append('_method', 'POST');

      axios.post('http://localhost:8000/api/actualizarUsuario', formData)
      .then(response => {
        toastr.success('Perfil editado correctamente');
        this.$refs["modal"].hide();
        this.$bus.$emit('prueba')
      }).catch(error => {
        toastr.error('Error, no se pudo editar el perfil')
        if (error.response.status == 422){
          this.validationErrors = error.response.data.errors;
          console.log(this.validationErrors)
        }
      });
    },
    borraImagen(){
      this.form.avatar = null
      this.imagenCargada = null
      this.imagenType = true
      this.imagenSyze = true
    },
    compruebaNombre(value){
      // let espacios
      // value ? espacios = !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ1-9]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ1-9]+)*$/.test(value.trim()) : espacios = false
      // this.checkName = true
      // if(value == '' || espacios || value == null){
      //   this.checkName = false
      //   $("#name").removeClass("is-valid").addClass("is-invalid");
      // }else {
      //   $("#name").removeClass("is-invalid").addClass("is-valid");
      //   this.checkName = true
      // }
    },
    compruebaEmail(value){
      let emailCorrecto
      value ? emailCorrecto = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/.test(value) : emailCorrecto = false
      this.checkEmail = true
      if(emailCorrecto == false){
        this.checkEmail = false
        $("#email").removeClass("is-valid").addClass("is-invalid");
      }else {
        $("#email").removeClass("is-invalid").addClass("is-valid");
        this.checkEmail = true
      }
    },
    compruebaContraseñas(value){
      this.checkPassword = false

      let espacios

      value ? espacios = !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/.test(value.trim()) : espacios = false

      if(this.form.password == value ){
        this.checkPassword = true
      }else {
        this.checkPassword = false
      }
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

.error {
  color: red;
  font-size: 12px;
}

.valido {
  color: green;
  font-size: 12px;
}

</style>