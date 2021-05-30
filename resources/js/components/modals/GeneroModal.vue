<template>
  <b-modal hide-footer ref="modal" id="modal-generoModal" size="xl" :title="tituloModal" @shown="beforeOpen" @hidden="cancelData">
  <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="addGenero">

    <label for="nombreGenero">Nombre del género</label>

      <input type="text" class="form-control" id="nombreGenero" required v-model="nombreGenero" @keyup="validarNombre(nombreGenero)">
      <span class="error" v-if="checkNombre == false">El nombre del genero no puede estar vacío</span>

    <button v-if="checkNombre == true" type="submit" class="btn btn-primary">{{tituloModal}}</button>

  </form>


  </b-modal>
</template>

<script>
export default {
  props : ['tituloModal', 'genero_id'],
  data() {
    return {
      nombreGenero: null,
      genero: [],
      checkNombre: null,
    }
  },
  methods: {
    beforeOpen(){
      if(this.tituloModal == 'Actualizar género'){
        this.obtenerDatos()
      }
    },
    addGenero(){

      if(this.tituloModal == 'Actualizar género'){

        axios.post('http://localhost:8000/api/update_genero',{
          params: {
            nombreGenero: this.nombreGenero,
            genero_id: this.genero_id
          }
        }).then(response => {
            toastr.success('genero actualizado con éxito');
            this.$bus.$emit('prueba')
            this.$refs["modal"].hide();
          }).catch(error => {
            toastr.error('Error, no se pudo actualizar el género')
        });

      }else{

        axios.post('http://localhost:8000/api/create_genero',{
          params: {
            nombreGenero: this.nombreGenero
          }
        }).then(response => {
            toastr.success('genero creado con éxito');
            this.$bus.$emit('prueba')
            this.$refs["modal"].hide();
          }).catch(error => {
            toastr.error('Error, no se pudo crear el género')
        });

      }

    },
    obtenerDatos(){
      axios.get('http://localhost:8000/api/getGenerosById',{
        params: {
          genero_id: this.genero_id,
        }
      }).then(response => {
        this.nombreGenero = response.data[0].nombre;
      })
    },
    validarNombre(value){
      this.checkNombre = true

      let espacios
      value ? espacios = !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]+)*$/.test(value.trim()) : espacios = false

      if(value == '' || value == null || espacios){
        this.checkNombre = false
        $("#nombreGenero").removeClass("is-valid").addClass("is-invalid");
      }else {
        $("#nombreGenero").removeClass("is-invalid").addClass("is-valid");
        this.checkNombre = true
      }

    },
    cancelData(){
      this.nombreGenero = null
    },
  },

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

</style>