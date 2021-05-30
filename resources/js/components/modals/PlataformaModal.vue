<template>
  <b-modal hide-footer ref="modal" id="modal-plataformaModal" size="xl" :title="tituloModal" @shown="beforeOpen" @hidden="cancelData">
  <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="addPlataforma">

    <div class="form-row">
      <label for="nombrePlataforma">Nombre de la plataforma</label>
      <input type="text" class="form-control" id="nombrePlataforma" required v-model="nombrePlataforma" @keyup="validarNombre(nombrePlataforma)" @blur="validarNombre(nombrePlataforma)">
      <span class="error" v-if="checkNombre == false">El nombre de la plataforma no puede estar vacío</span>
    </div>

    <div class="form-row">
      <label for="fabricantePlataforma" style="margin-top: 10px">Fabricante de la plataforma</label>
      <input type="text" class="form-control" id="fabricantePlataforma" required v-model="fabricantePlataforma" @keyup="validarFabricante(fabricantePlataforma)" @blur="validarFabricante(fabricantePlataforma)">
      <span class="error" v-if="checkFabricante == false">El nombre de la plataforma no puede estar vacío</span>
    </div>
    <button v-if="checkFabricante == true && checkNombre == true" type="submit" class="btn btn-primary">{{tituloModal}}</button>

  </form>


  </b-modal>
</template>

<script>
export default {
  props : ['tituloModal', 'plataforma_id'],
  data() {
    return {

      nombrePlataforma: null,
      fabricantePlataforma: null,
      plataforma: [],
      checkNombre: null,
      checkFabricante: null,

    }
  },
  methods: {
    beforeOpen(){
      if(this.tituloModal == 'Actualizar plataforma'){
        this.obtenerDatos()
      }
    },
    addPlataforma(){

      if(this.tituloModal == 'Actualizar plataforma'){

        axios.post('http://localhost:8000/api/update_plataforma',{
          params: {
            nombrePlataforma: this.nombrePlataforma,
            plataforma_id: this.plataforma_id,
            fabricantePlataforma: this.fabricantePlataforma
          }
        }).then(response => {
            toastr.success('plataforma actualizado con éxito');
            this.$bus.$emit('prueba')
            this.$refs["modal"].hide();
          }).catch(error => {
            toastr.error('Error, no se pudo actualizar el plataforma')
        });

      }else{

        axios.post('http://localhost:8000/api/create_plataforma',{
          params: {
            nombrePlataforma: this.nombrePlataforma,
            fabricantePlataforma: this.fabricantePlataforma
          }
        }).then(response => {
            toastr.success('plataforma creado con éxito');
            this.$bus.$emit('prueba')
            this.$refs["modal"].hide();
          }).catch(error => {
            toastr.error('Error, no se pudo crear la plataforma')
        });

      }

    },
    obtenerDatos(){
      axios.get('http://localhost:8000/api/getAllPlataformasById',{
        params: {
          plataforma_id: this.plataforma_id,
        }
      }).then(response => {
        this.nombrePlataforma = response.data[0].nombre
        this.fabricantePlataforma = response.data[0].fabricante
      })
    },
    validarNombre(value){
      let espacios

      this.checkNombre = true
      value ? espacios = !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/.test(value.trim()) : espacios = false

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

      value ? espacios = !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+(?: [a-zA-ZáéíóúÁÉÍÓÚñÑ]+)*$/.test(value.trim()) : espacios = false

      if(value == '' || value == null || espacios){
        this.checkFabricante = false
        $("#fabricantePlataforma").removeClass("is-valid").addClass("is-invalid");
      }else {
        this.checkFabricante = true

        $("#fabricantePlataforma").removeClass("is-invalid").addClass("is-valid");
      }
    },
    cancelData(){
      this.nombrePlataforma = null
      this.fabricantePlataforma = null
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