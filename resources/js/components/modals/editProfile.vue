<template>
  <b-modal hide-footer ref="modal" id="modal-editProfile" size="xl" title="Editar usuario" @shown="beforeOpen" @hide="cancelData">
    <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="updateUser">
    <template v-if="validationErrors">
      <li v-for="(item, index) in validationErrors" :key="index" class="errorServ">{{item | borraCaracteresEspeciales }}</li>
    </template>
    <li class="errorServ" v-if="validationCurrentPasssword"> {{validationCurrentPasssword}}</li>
        <div class="form-row">
          <div class="imagen">
            <img class="caratula" :src="'https://gamereviewsprojectdaw.s3.eu-west-3.amazonaws.com/'+ form.avatar" v-if="caratula">
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

      <div class="form-group col-md-12">
        <label for="current_password">Contraseña actual</label>
        <input id="current_password" type="password" class="form-control" name="current_password" v-model="form.current_password" @blur="compruebaContraseñaActual(form.current_password)" @keyup="compruebaContraseñaActual(form.current_password)">
        <span class="error" v-if="checkCurrentPassword == false">Las contraseña está vacía o solo contienen espacios. Debe tener al menos 8 caracteres, mayusc. minusc. números y caracteres especiales. Caracteres especiales aceptados: @!_"-</span>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="password">Nueva contraseña</label>
          <input type="password" id="password" class="form-control" name="password" v-model="form.password" @keyup="compruebaContraseñas(form.password_confirmation)">
          <span class="error" v-if="checkPassword == false">Las contraseña está vacía o solo contienen espacios. Debe tener al menos 8 caracteres, mayusc. minusc. números y caracteres especiales. Caracteres especiales aceptados: @!_"-</span>
          <p><span class="error" v-if="checkCoinciden == false">Las contraseña no coinciden</span></p>
          <span class="valido" v-if="checkPassword == true">Las contraseñas coinciden</span>
        </div>
        <div class="form-group col-md-6">
          <label for="password_confirmation">Repite la contraseña</label>
          <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" v-model="form.password_confirmation" @keyup="compruebaContraseñas(form.password_confirmation)">
          <span class="error" v-if="checkPassword == false">Las contraseña está vacía o solo contienen espacios. Debe tener al menos 8 caracteres, mayusc. minusc. números y caracteres especiales. Caracteres especiales aceptados: @!_"-</span>
          <p><span class="error" v-if="checkCoinciden == false">Las contraseña no coinciden</span></p>
          <span class="valido" v-if="checkPassword == true">Las contraseñas coinciden</span>
        </div>
      </div>

    <button type="submit" class="btn btn-primary" v-if="checkName == true && checkEmail == true && form.current_password != null && imagenSyze == true && imagenType == true && checkPassword != false">Editar perfil</button>
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
        id: null,
        name: null,
        email: null,
        FecNac: null,
        sexo: null,
        ocupacion: null,
        ubicacion: null,
        aficiones: null,
        biografia: null,
        avatar: null,
        current_password: null,
        password: null,
        password_confirmation: null,
      },
      imagenCargada: null,
      password_confirmation: null,
      imagenType: true,
      imagenSyze: true,
      checkName: true,
      checkEmail: true,
      checkPassword: null,
      checkCoinciden: null,
      checkCurrentPassword: null,
      validationErrors: null,
      validationCurrentPasssword: null,
      imagenBorrada: false,
    }
  },
  methods: {
    beforeOpen(){
      this.form.id = this.current_user.id
      this.form.name = this.current_user.name
      this.form.email = this.current_user.email
      this.form.FecNac = this.current_user.FecNac
      this.form.sexo = this.current_user.sexo
      this.form.ocupacion = this.current_user.ocupacion
      this.form.ubicacion = this.current_user.ubicacion
      this.form.aficiones = this.current_user.aficiones
      this.form.biografia = this.current_user.biografia
      this.form.avatar = this.current_user.avatar
      this.form.current_password = null
      this.form.password = null
      this.form.password_confirmation = null
      this.imagenCargada = this.current_user.avatar
    },
    onFileChange(e) {
      let file = e.target.files[0]
      this.form.avatar = file
        if(file.type == "image/png" || file.type == "image/jpg" || file.type == "image/jpeg" || file.type == "image/webp"){
          if(file.size < 20000){
            this.imagenType = true
            this.imagenSyze = true
            this.cargarImagen(file)
            this.imagenBorrada = "nueva foto"
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
      formData.append('avatar', this.form.avatar ? this.form.avatar : null)
      if(this.imagenBorrada == false){
        formData.append('avatar',  this.form.avatar = this.imagenCargada)
      }
      formData.append('password', this.form.password ? this.form.password : '')
      formData.append('password_confirmation', this.form.password_confirmation ? this.form.password : '')
      formData.append('current_password', this.form.current_password ? this.form.current_password : '')
      formData.append('imagenBorrada', this.imagenBorrada)
      formData.append('_method', 'POST');

      axios.post('http://gamereviewsproject.herokuapp.com/api/actualizarUsuario', formData)
      .then(response => {
        toastr.success('Perfil editado correctamente');
        this.$refs["modal"].hide();
        this.$bus.$emit('prueba')
      }).catch(error => {
        toastr.error('Error, no se pudo editar el perfil')
        if(error.response.status == 420){
          this.validationCurrentPasssword = error.response.data.message
        }else if (error.response.status == 422){
          this.validationErrors = error.response.data.errors;
          this.validationCurrentPasssword = null
        }
      });
    },
    borraImagen(){
      this.form.avatar = null
      this.imagenCargada = null
      this.imagenType = true
      this.imagenSyze = true
      this.imagenBorrada = true
    },
    compruebaNombre(value){
      let espacios
      value ? espacios = !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ1-9]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ1-9]+)*$/.test(value.trim()) : espacios = false
      this.checkName = true
      if(!value || espacios){
        this.checkName = false
        $("#name").removeClass("is-valid").addClass("is-invalid");
      }else {
        $("#name").removeClass("is-invalid").addClass("is-valid");
        this.checkName = true
      }
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
      let contraseñaCorrecta

      value ? contraseñaCorrecta = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/.test(value) : contraseñaCorrecta = false

      if(this.form.password != this.form.password_confirmation ){
        this.checkCoinciden = false
      }else {
        this.checkCoinciden = true
      }

      if(contraseñaCorrecta == true){
        this.checkPassword = true
      }else {
        this.checkPassword = false
      }

      if(this.form.password == ''){
        this.checkPassword = null
      }

    },
    compruebaContraseñaActual(value){
      this.checkCurrentPassword = false
      let contraseñaActualCorrecta

      value ? contraseñaActualCorrecta = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/.test(value) : contraseñaActualCorrecta = false

      if(contraseñaActualCorrecta == true){
        this.checkCurrentPassword = true
        $("#current_password").removeClass("is-invalid").addClass("is-valid");
      }else {
        this.checkCurrentPassword = false
        $("#current_password").removeClass("is-valid").addClass("is-invalid");
      }
    },
    cancelData(){
      this.form.id = null
      this.form.name = null
      this.form.email = null
      this.form.FecNac = null
      this.form.sexo = null
      this.form.ocupacion = null
      this.form.ubicacion = null
      this.form.aficiones = null
      this.form.biografia = null
      this.form.avatar = null
      this.form.current_password = null
      this.form.password = null
      this.form.password_confirmation = null

      this.imagenCargada = null
      this.password_confirmation = null
      this.imagenType = true
      this.imagenSyze = true
      this.checkName = true
      this.checkEmail = true
      this.checkPassword = null
      this.checkCurrentPassword = null
      this.validationErrors = null
      this.validationCurrentPasssword = null
    }
  },
  computed: {
    imagen(){
      return this.imagenCargada
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

.errorServ {
  background: #c82333;
  padding: 10px;
  list-style:none;
  color: white;
}

.valido {
  color: green;
  font-size: 12px;
}

</style>