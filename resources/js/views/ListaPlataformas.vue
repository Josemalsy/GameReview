<template>
  <div class="pagina" v-if="current_user.rol != 'Usuario'">
    <div class="boton-agregar"> <button class="btn btn-success" v-b-modal="'modal-plataformaModal'" @click="crearPlataforma">Añadir plataforma </button></div>
    <div class="lista">
      <div class="tabla" v-for="(item, index) in plataformas" :key="index">
        <div class="nombre-plataforma"> {{item.nombre}}</div>
        <div class="fabricante-plataforma"> {{item.fabricante}}</div>
        <div class="editar-plataforma" v-b-modal.plataformaModal @click="editarPlataforma(item.id)"> <i class="bi bi-pencil-square" title="editar plataforma"></i> </div>
        <div class="borrar-plataforma" @click="borrarPlataforma(item.id)"> <i class="bi bi-trash" title="borrar plataforma"></i></div>
      </div>
    </div>

    <modal-plataformaModal :plataforma_id="plataforma_id" :tituloModal="titulo"/>

  </div>
</template>

<script>
import Swal from 'sweetalert2'

export default {
props : ['current_user'],
  mounted() {

    if(this.current_user == false) {
      this.$router.push('/forbidden');
    }else{
      this.getPlataformas()
      this.$bus.$on('prueba',this.getPlataformas)
    }

  },
  data() {
    return {
      plataformas: [],
      plataforma_id: null,
      agregarCrearPlataforma: false,
      titulo: null,
    }
  },
  methods: {
    getPlataformas(){
      axios.get('http://localhost:8000/api/getPlataformas/').then(response =>{
        this.plataformas = response.data
      })
    },
    crearPlataforma(){
      this.agregarCrearPlataforma = true,
      this.titulo = 'Agregar plataforma'

    },
    editarPlataforma(value){
      this.agregarCrearPlataforma = false,
      this.plataforma_id = value
      this.titulo = 'Actualizar plataforma'
      this.$bvModal.show("modal-plataformaModal")
    },
    borrarPlataforma(value){
        Swal.fire({
          title: "¿Quieres borrar esta plataforma?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, eliminala',
        }).then((result) => {
          if(result.isConfirmed){
            axios.delete('http://localhost:8000/api/delete_plataforma', {
              params: {
                plataforma_id: value
              }
            }).then(response =>{
              toastr.success('has eliminado la plataforma correctamente');
              this.getPlataformas()
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

.nombre-plataforma, .fabricante-plataforma, .borrar-plataforma, .editar-plataforma {
  display: flex;
  flex-flow: column;
  padding: 10px;
  border: 1px solid white;
  font-size: 24px;
  background: #C0E6ED;
}

.nombre-plataforma {
  flex: 7;
}

.fabricante-plataforma, .editar-plataforma, .borrar-plataforma {
  flex:2;
  text-align: center;
}

i:hover {
  cursor:pointer;
}

@media (max-width: 700px){
  .lista, .boton-agregar {
    width: 100%;
  }

.nombre-plataforma, .fabricante-plataforma, .borrar-plataforma, .editar-plataforma {
  font-size: 18px;
}

  .boton-agregar{
    justify-content: center;
    margin: 20px 0;
  }

  .fabricante-plataforma {
    min-width: 100px;
  }
}

</style>