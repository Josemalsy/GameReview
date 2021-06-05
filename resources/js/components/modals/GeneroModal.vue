<template>
  <b-modal hide-footer ref="modal" id="modal-generoModal" size="xl" :title="tituloModal" @shown="beforeOpen" @hidden="cancelData">
  <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="addGenero">

    <template v-if="validationErrors">
      <li v-for="(item, index) in validationErrors" :key="index" class="errorServ">{{item | borraCaracteresEspeciales }}</li>
    </template>

    <label for="nombreGenero">Nombre del género</label>

      <input type="text" class="form-control" id="nombreGenero" required v-model="form.nombre" @keyup="validarNombre(form.nombre)">
      <span class="error" v-if="checkNombre == false">El nombre del genero no puede estar vacío</span>

    <button  type="submit" v-if="checkNombre == true" class="btn btn-primary">{{tituloModal}}</button>

  </form>


  </b-modal>
</template>

<script>
export default {
  props : ['tituloModal', 'genero_id'],
  data() {
    return {
      form:{
        nombre: null,
        genero_id: null,
      },
      genero: [],
      checkNombre: null,
      validationErrors: null,
    }
  },
  methods: {
    beforeOpen(){
      if(this.tituloModal == 'Actualizar género'){
        this.form.genero_id = this.genero_id
        this.obtenerDatos()
      }
    },
    addGenero(){

      if(this.tituloModal == 'Actualizar género'){

        axios.post('http://gamereviewsproject.herokuapp.com/api/update_genero',this.form)
          .then(response => {
            toastr.success('genero actualizado con éxito');
            this.$bus.$emit('prueba')
            this.$refs["modal"].hide();
          }).catch(error => {
            toastr.error('Error, no se pudo actualizar el género')
            if (error.response.status == 422){
              this.validationErrors = error.response.data.errors;
            }
        });

      }else{
        axios.post('http://gamereviewsproject.herokuapp.com/api/create_genero',this.form).then(response => {
            toastr.success('genero creado con éxito');
            this.$bus.$emit('prueba')
            this.$refs["modal"].hide();
          }).catch(error => {
            toastr.error('Error, no se pudo crear el género')
            if (error.response.status == 422){
              this.validationErrors = error.response.data.errors;
            }
        });

      }

    },
    obtenerDatos(){
      axios.get('http://gamereviewsproject.herokuapp.com/api/getGenerosById',{
        params:{
          genero_id: this.form.genero_id
        }
      }).then(response => {
        this.form.nombre = response.data[0].nombre;
      })
    },
    validarNombre(value){
      this.checkNombre = true

      let espacios
      value ? espacios = !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]+)*$/.test(value.trim()) : espacios = false

      if(!value == || espacios){
        this.checkNombre = false
        $("#nombreGenero").removeClass("is-valid").addClass("is-invalid");
      }else {
        $("#nombreGenero").removeClass("is-invalid").addClass("is-valid");
        this.checkNombre = true
      }

    },
    cancelData(){
      this.form.nombre = null
      this.validationErrors = null
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