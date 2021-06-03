<template>
  <b-modal hide-footer ref="modal" id="modal-plataformaModal" size="xl" :title="tituloModal" @shown="beforeOpen" @hidden="cancelData">
  <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="addPlataforma">

    <template v-if="validationErrors">
      <li v-for="(item, index) in validationErrors" :key="index" class="errorServ">{{item | borraCaracteresEspeciales }}</li>
    </template>

    <div class="form-row">
      <label for="nombrePlataforma">Nombre de la plataforma</label>
      <input type="text" class="form-control" id="nombrePlataforma"  v-model="form.nombre" @keyup="validarNombre(form.nombre)" @blur="validarNombre(form.nombre)">
      <span class="error" v-if="checkNombre == false">El nombre de la plataforma no puede estar vacío</span>
    </div>

    <div class="form-row">
      <label for="fabricantePlataforma" style="margin-top: 10px">Fabricante de la plataforma</label>
      <input type="text" class="form-control" id="fabricantePlataforma"  v-model="form.fabricante" @keyup="validarFabricante(form.fabricante)" @blur="validarFabricante(form.fabricante)">
      <span class="error" v-if="checkFabricante == false">El nombre de la plataforma no puede estar vacío</span>
    </div>
    <button  type="submit" v-if="checkFabricante == true && checkNombre == true" class="btn btn-primary">{{tituloModal}}</button>

  </form>


  </b-modal>
</template>

<script>
export default {
  props : ['tituloModal', 'plataforma_id'],
  data() {
    return {
      form: {
        nombre: null,
        fabricante: null,
        plataforma_id: null,
      },
      checkNombre: null,
      checkFabricante: null,
      validationErrors: null,
    }
  },
  methods: {
    beforeOpen(){
      if(this.tituloModal == 'Actualizar plataforma'){
        this.form.plataforma_id = this.plataforma_id
        this.obtenerDatos()
      }
    },
    addPlataforma(){

      if(this.tituloModal == 'Actualizar plataforma'){

        axios.post('https://gamereviewsproject.herokuapp.com/api/update_plataforma',this.form)
          .then(response => {
            toastr.success('plataforma actualizado con éxito');
            this.$bus.$emit('prueba')
            this.$refs["modal"].hide();
          }).catch(error => {
            toastr.error('Error, no se pudo actualizar el plataforma')
            if (error.response.status == 422){
              this.validationErrors = error.response.data.errors;
            }
        });

      }else{

        axios.post('https://gamereviewsproject.herokuapp.com/api/create_plataforma',this.form).then(response => {
            toastr.success('plataforma creado con éxito');
            this.$bus.$emit('prueba')
            this.$refs["modal"].hide();
          }).catch(error => {
            toastr.error('Error, no se pudo crear la plataforma')
            if (error.response.status == 422){
              this.validationErrors = error.response.data.errors;
            }
        });

      }

    },
    obtenerDatos(){
      axios.get('https://gamereviewsproject.herokuapp.com/api/getAllPlataformasById',{
        params: {
          plataforma_id: this.form.plataforma_id,
        }
      }).then(response => {
        this.form.nombre = response.data[0].nombre
        this.form.fabricante = response.data[0].fabricante
      })
    },
    validarNombre(value){
      let espacios

      this.checkNombre = true
      value ? espacios = !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]+)*$/.test(value.trim()) : espacios = false

      if(value == '' || value == null || espacios){
        this.checkNombre = false
        $("#nombrePlataforma").removeClass("is-valid").addClass("is-invalid");
      }else {
        this.checkNombre = true
        $("#nombrePlataforma").removeClass("is-invalid").addClass("is-valid");
      }

    },
    validarFabricante(value){
      this.checkFabricante = true
      let espacios

      value ? espacios = !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]+)*$/.test(value.trim()) : espacios = false

      if(value == '' || value == null || espacios){
        this.checkFabricante = false
        $("#fabricantePlataforma").removeClass("is-valid").addClass("is-invalid");
      }else {
        this.checkFabricante = true

        $("#fabricantePlataforma").removeClass("is-invalid").addClass("is-valid");
      }
    },
    cancelData(){
      this.form.nombre = null
      this.form.fabricante = null
      this.validationErrors = null
      this.checkFabricante = null
      this.checkNombre = null
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

<style scoped>

.btn{
  margin-top: 20px;
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

</style>