<template>
  <div class="pagina" v-if="current_user.rol != 'Usuario'">

    <div class="busqueda">
      <div class="form-row align-items-center filtros">
        <div class="input-group-prepend">
          <div class="input-group-text">Nombre de usuario </div>
        </div>
        <input type="text" placeholder="Introduce tu búsqueda" id="busqueda" @keypress.prevent.enter="getUsers" class="form-control" v-model="filters.buscador">
        <div class="input-group-append">
          <button type="button" class="btn btn-success" @click="getUsers">Filtrar</button>
        </div>
      </div>
      <div class="form-row align-items-center boton-expulsado">
        <template v-if="filters.estado">
          <button type="button" class="btn botn" style="background: #C0E6ED;" @click="cambiaEstado">Ver activos</button>
        </template>
        <template v-else>
          <button type="button" class="btn botn" style="background: #D2515D;" @click="cambiaEstado">Ver expulsados </button>
        </template>
      </div>
    </div>

    <div class="lista">
      <div class="cabecera"><template v-if="filters.estado">Usuarios expulsados</template><template v-else>Usuarios activos</template></div>
        <div class="cargando" v-if="loading"> <Loading/> </div>
      <div class="tabla" v-for="(item, index) in users" :key="index" v-else>
        <template >
          <template v-if="!filters.estado">
            <div class="nombre-user"><a :href="'/usuario/'+item.id" title="ver perfil">{{item.name}} </a></div>
            <div class="verHistorial" v-b-modal.historialExpulsiones @click="verHistorial(item.id)"> <i class="bi bi-clipboard-x" title="historial de expulsiones"></i> </div>
            <template v-if="item.rol != 'Usuario'">
              <div class="verPerfil"><i class="bi bi-star" title="No puedes expulsar a un moderador/administrador"></i></a></div>
            </template>
            <template v-else>
              <div class="verPerfil" style="color: red;" v-b-modal="'modal-expulsionModal'" @click="expulsarUsuario(item.id, item.estado)"><i class="bi bi-x-octagon" title="expulsar usuario"></i></a></div>
            </template>
            <template v-if="current_user.rol == 'Administrador'">
              <template v-if="item.rol != 'Administrador'">
                <template v-if="item.rol == 'Moderador'">
                  <div class="verPerfil" @click="nombrarUsuario(item.id, item.name)"> <i class="bi bi-hand-thumbs-down" title="Hacerlo usuario"></i> </div>
                </template>
                <template v-else>
                  <div class="verPerfil" @click="nombrarModerador(item.id, item.name)"> <i class="bi bi-hand-thumbs-up" title="Nombrarlo moderador"></i> </div>
                </template>
              </template>
              <template v-else><div class="rol"> <i class="bi bi-award-fill" title="Administrador"></i> </div> </template>
            </template>
          </template>
          <template v-else>
            <div class="nombre-userExpulsado">{{item.name}}</div>
            <div class="verHistorialExpulsado" v-b-modal.historialExpulsiones @click="verHistorial(item.id)">  <i class="bi bi-clipboard-x" title="historial de expulsiones"></i> </div>
            <div class="verPerfilExpulsado" v-b-modal="'modal-expulsionModal'" @click="expulsarUsuario(item.id, item.estado)"> <i class="bi bi-x-octagon" title="modificar o levantar expulsión vigente"></i></div>
            <div class="verPerfilExpulsado"  v-if="current_user.rol == 'Administrador'">
            <template v-if="current_user.rol == 'Administrador'">
              <template v-if="item.rol != 'Administrador'">
                <template v-if="item.rol == 'Moderador'">
                  <div @click="nombrarUsuario(item.id, item.name)" style="border:0px"> <i class="bi bi-hand-thumbs-down" title="Hacerlo usuario"></i> </div>
                </template>
                <template v-else>
                  <div> <i class="bi bi-hand-thumbs-up" title="Nombrarlo moderador"></i> </div>
                </template>
              </template>
              <template v-else> <i class="bi bi-award-fill" title="Administrador"></i> </template>
            </template>
            </div>
          </template>
        </template>
      </div>

      <modal-historialExpulsiones :user_id="user_id"/>
      <modal-expulsionModal :user_id="user_id" :estado="estado" />
    </div>

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
      this.getUsers()
      this.$bus.$on('prueba',this.getUsers)
    }
  },
  data() {
    return {
      loading: true,
      users: [],
      user_id: null,
      estado: null,
      agregarCrearGenero: false,
      filters: {
        buscador: '',
        estado: false,
      },
    }
  },
  methods: {
    getUsers(){
      this.loading = true,
      axios.get('http://gamereviewsproject.herokuapp.com/api/user_list/', {
        params: {
          filters: this.filters.buscador,
          estado: this.filters.estado
        }
      }).then(response =>{
        this.users = response.data
        this.loading = false
      })
    },
    cambiaEstado(){
      this.filters.estado = !this.filters.estado
      this.getUsers()
    },
    verHistorial(value){
      this.user_id = value
      this.$bvModal.show("modal-historialExpulsiones")
    },
    expulsarUsuario(id,estado){
      this.user_id = id
      this.estado = estado
      this.$bvModal.show("modal-expulsionModal")
    },
    nombrarModerador(value, nombre){
      console.log(value)
      Swal.fire({
        title: "¿Quieres hacer a " + nombre + " moderador?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, hazlo',
      }).then((result) => {
        if(result.isConfirmed){
          axios.post('http://gamereviewsproject.herokuapp.com/api/cambia_rol/', {
            params: {
              user_id: value,
              rol: 'Moderador',
            }
          }).then(response => {
            toastr.success('has nombrado a ' + nombre + ' moderador');
            this.getUsers()
          })
        }
      })
    },
    nombrarUsuario(value, nombre) {
      Swal.fire({
        title: "¿Quieres devolver a " + nombre + " al rol de usuario?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, hazlo',
      }).then((result) => {
        if(result.isConfirmed){
          axios.post('http://gamereviewsproject.herokuapp.com/api/cambia_rol/', {
            params: {
              user_id: value,
              rol: 'Usuario'
            }
          }).then(response =>{
            toastr.success('has devuelto a ' + nombre + ' al rol de Usuario');
            this.getUsers()
          })
        }
      })
    }
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

.busqueda {
  display: flex;
  flex-flow: column;
}

.filtros {
  margin: 30px 0 25px 0;
  display: flex;
  flex-flow: row;
}

.boton-expulsado {
  display: flex;
  flex-flow: column;
  margin-bottom: 10px;
}

.botn {
  color: black;
}

.lista {
  display: flex;
  flex-flow: column wrap;
  width: 50%;
  margin: 0 0 20px 0;

}

.cabecera {
	display: flex;
	height: auto;
	margin-top: 10px;
	flex-flow: column wrap;
	margin: 5px;
	width: 100%;
	padding: 10px;
	font-size: 25px;
	background: #013a63;
	color: white;
}

.tabla {
  display: flex;
  flex-flow: row wrap;
}

.nombre-user, .verPerfil, .verHistorial, .rol {
  display: flex;
  flex-flow: column;
  padding: 10px;
  border: 1px solid white;
  font-size: 24px;
  background: #C0E6ED;
  flex: 3;
}

.nombre-userExpulsado, .verPerfilExpulsado, .verHistorialExpulsado {
  display: flex;
  flex-flow: column;
  padding: 10px;
  border: 1px solid white;
  font-size: 24px;
  background: #D2515D;
  flex: 3;
  justify-content: center;
}

.nombre-user a, .nombre-userExpulsado a {
  color: black;
}

.verPerfil, .verHistorial, .verPerfilExpulsado, .verHistorialExpulsado, .rol {
  flex:1;
  text-align: center;
}

.verPerfil a, .verPerfilExpulsado a{
  color: black;
}

i:hover {
  cursor:pointer;
}

.bi-award-fill:hover {
  cursor:default;
}

@media (max-width: 700px){
  .lista {
    width: 90%;
  }

}

</style>