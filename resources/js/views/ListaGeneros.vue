<template>


  <div class="pagina" v-if="current_user.rol != 'Usuario'">
    <div class="boton-agregar"> <button class="btn btn-success" v-b-modal="'modal-generoModal'" @click="crearGenero">Añadir genero </button></div>
    <div class="lista">
      <div class="tabla" v-for="(item, index) in generos" :key="index">
        <div class="nombre-genero"> {{item.nombre}}</div>
        <div class="editar-genero" v-b-modal.generoModal @click="editarGenero(item.id)"> <i class="bi bi-pencil-square" title="editar genero"></i> </div>
        <div class="borrar-genero" @click="borrarGenero(item.id)"> <i class="bi bi-trash" title="borrar genero"></i></div>
      </div>
    </div>

    <modal-generoModal :genero_id="genero_id" :tituloModal="titulo"/>

  </div>
</template>

<script>
import Swal from 'sweetalert2'

export default {
props : ['current_user'],
  mounted() {

    if(this.current_user == false) {
      this.$router.push('/forbidden');
    }else {
      this.getGeneros()
      this.$bus.$on('prueba',this.getGeneros)
    }
  },
  data() {
    return {
      generos: [],
      genero_id: null,
      agregarCrearGenero: false,
      titulo: null,
    }
  },
  methods: {
    getGeneros(){
      axios.get('https://gamereviewsproject.herokuapp.com/api/getGeneros/').then(response =>{
        this.generos = response.data
      })
    },
    crearGenero(){
      this.agregarCrearGenero = true
      this.titulo = 'Agregar género'
    },
    editarGenero(value){
      this.agregarCrearGenero = false,
      this.genero_id = value
      this.titulo = 'Actualizar género'
      this.$bvModal.show("modal-generoModal")
    },
    borrarGenero(value){
        Swal.fire({
          title: "¿Quieres borrar este género?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, eliminalo',
        }).then((result) => {
          if(result.isConfirmed){
            axios.delete('https://gamereviewsproject.herokuapp.com/api/delete_genero', {
              params: {
                genero_id: value
              }
            }).then(response =>{
              toastr.success('has eliminado el genero correctamente');
              this.getGeneros()
            });
          }
        })
      },
  },

}
</script>

<style scoped>

.pagina{
  display:flex;
  width: 100%;
  flex-flow: column wrap;
  align-items: center;

}

.boton-agregar {
  width: 50%;
  margin: 20px;
  align-self: center;
  display:flex;
}

.lista {
  display: flex;
  flex-flow: column wrap;
  width: 50%;
  margin: 0 0 20px 0;

}

.tabla {
  display: flex;
  flex-flow: row wrap;
}

.nombre-genero, .borrar-genero, .editar-genero {
  display: flex;
  flex-flow: column;
  padding: 10px;
  border: 1px solid white;
  flex:7;
  font-size: 24px;
  background: #C0E6ED;
}

.editar-genero, .borrar-genero {
  flex:1;
  text-align: center;
}

i:hover {
  cursor:pointer;
}

@media (max-width: 700px){
  .lista, .boton-agregar {
    width: 100%;
  }



  .boton-agregar{
    justify-content: center;
    margin: 20px 0;
  }
}

</style>