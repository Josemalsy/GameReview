<template>
	<div class="cargando" v-if="loading"> <Loading/> </div>

<div class="contenedor" v-else>
  <head><title>GameReviews - Juegos</title></head>

  <div class="form-row align-items-center filtros">
    <div class="col-auto">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <div class="input-group-text">Buscar por titulo </div>
        </div>
        <input type="text" placeholder="Introduce tu búsqueda" id="busqueda" @keypress.prevent.enter="obtenerDatos" class="form-control" v-model="filters.buscador">
        <div class="input-group-append">
          <button type="button" class="btn btn-success" @click="obtenerDatos">Filtrar</button>
        </div>
      </div>
    </div>

    <div class="col-auto">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <div class="input-group-text">Ordenar! por: </div>
        </div>
        <select class="form-control" id="ordernarPor" v-model="orden" v-on:change="obtenerDatos">
          <option value="1">Titulo 🡹</option>
          <option value="2">Titulo 🡻</option>
          <option value="3">Fecha de lanzamiento 🡹</option>
          <option value="4">Fecha de lanzamiento 🡻</option>
          <option value="5">Valoracion media 🡻</option>
          <option value="6">Valoracion media 🡹</option>
        </select>
      </div>
    </div>

  </div>

  <nav class="paginate-bottom" aria-label="Page navigation example">
    <ul class="pagination" v-for="n in ultima_pagina">
      <li class="page-item"><a class="page-link" :style="{background: fondoPaginas(n)}" @click="changePage( n )">{{ n }}</a></span></li>
    </ul>
  </nav>

  <div class="contentCard">
    <div class="tarjeta" v-for="(item,index) in lista_juegos" :key="index">
      <div class="izquierda">
        <img class="caratula" :src="'../storage/'+ item.imagen" alt="Card image">
        <div class="opciones">
          <div class="opcion">
            <template v-if="current_user != false">
              <template v-if="current_user.rol == 'Administrador'">
                <div class="cuadro"  title="editar juego" @click="editarJuego(item.id)"><i class="bi bi-pencil"></i> </div>
                <div class="cuadro"  title="eliminar juego" @click="eliminarJuego(item.id)"><i class="bi bi-trash"></i> </div>
              </template>
              <template v-if="current_user.email_verified_at">
                <template v-if="current_user.id">
                  <div class="cuadro" v-if="checkUser(item.users)" @click="eliminaPosesion(item.id)" title="eliminar juego" ><i class="bi bi-heart-fill" style="color:red;"></i></div>
                  <div class="cuadro" v-b-modal.eligePlataforma v-else @click="sendInfo(item.id)" title="agregar juego"><i class="bi bi-heart"></i></div>
                </template>
              </template>
              <template v-else>
                <small>Valida tu email para agregar este juego a tu lista</small>
              </template>
            </template>
          </div>
        </div>
      </div>
      <div class="titulo">
        <div class="titDes">
          <div class="span">
            <span class="span-title"> <a class="juegoId" :href="'game/'+item.id">{{item.titulo}} </a></span>
            <span class="span-des">{{item.desarrolladora}}</span>
            <span class="span-valor" :style="{background: estableceFondo(item.valoracion_media)}"><i class="bi bi-clipboard-data" title="valoracion"></i> {{item.valoracion_media | roundValors}}% </span>
          </div>
          <div class="duracion">
            <span class="tiempo">Base <p>{{item.reviews_avg_juego_base | roundValors }}H</p></span>
            <span class="tiempo">Extras <p>{{ item.reviews_avg_juego_extendido | roundValors}}H</p></span>
            <span class="tiempo">100% <p>{{ item.reviews_avg_completado_total | roundValors}}H</p></span>
          </div>
        </div>
      </div>
    </div>
    <b-modal id="eligePlataforma" title="Second Modal" hide-footer ok-only>
      <label for="observación">Elija plataforma</label>
      <select id="observacion" class="form-select mb-3" v-model="plataforma_id">
        <option v-for="plataforma in listaPlataformas" :value="plataforma.id">{{plataforma.nombre}}</option>
      </select>
      <button class="btn btn-success" type="submit" @click="agregaPosesion(plataforma_id)">Agregar juego</button>
    </b-modal>
    <modal-AddGame :game_id="game_id" :tituloModal="'Actualizar Juego'"/>
  </div>

  <nav class="paginate-bottom" aria-label="Page navigation example">
    <ul class="pagination" v-for="n in ultima_pagina">
      <li class="page-item"><a class="page-link" :style="{background: fondoPaginas(n)}" @click="changePage( n )">{{ n }}</a></span></li>
    </ul>
  </nav>

</div>

</template>

<script>
import Swal from 'sweetalert2'

  export default {
    props : ['current_user'],
    data() {
      return {
        loading: true,
        lista_juegos: [],
        listaPlataformas: [],
        page: 1,
        pagina: null,
        primera_pagina:1,
        ultima_pagina: 1,
        orden: 1,
        filters: {
          buscador: '',
        },
        abrirModal: false,
        game_id: null,
        plataforma_id: null,
      }
    },
    mounted(){
      this.obtenerDatos();
      this.$bus.$on('prueba',this.obtenerDatos)
    },
    methods: {
      obtenerDatos(){
        const params = {
        page: this.page
        }
        this.loading =  true,
        axios.get('http://gamereviewsproject.herokuapp.com/api/juegos/?page='+ this.page, {
          params: {
            orden: this.orden,
            filters: this.filters.buscador,
          }
        }).then(response =>{
          this.lista_juegos = response.data.data;
          this.ultima_pagina = response.data.last_page
          this.loading =  false
          this.pagina = response.data.current_page
        });
      },
    changePage( page ) {
        this.page = (page <= 0 || page > this.ultima_pagina) ? this.page : page
        this.obtenerDatos()
      },
      checkUser(value){
        if(value){
          for (let index = 0; index < value.length; index++) {
            if(value[index].id == this.current_user.id){
              return true
            }
            return false
          }
        }else{
          return false
        }
      },
      sendInfo(value){
        this.game_id = value
        axios.get('http://gamereviewsproject.herokuapp.com/api/getPlataformasById',{
          params: {
            game_id: value,
          }
        }).then(response => {
          this.listaPlataformas = response.data[0].plataformas;
        })
      },
      agregaPosesion(value){
        Swal.fire({
          title: "¿Quieres agregar este juego a tu lista de propiedades ?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, agregalo',
        }).then((result) => {
          if(result.isConfirmed){
            axios.post('http://gamereviewsproject.herokuapp.com/api/game_user/create_posesion', {
              params: {
                plataforma_id: value,
                game_id: this.game_id,
                user_id: this.current_user.id
              }
            }).then(response =>{
              toastr.success('has adquirido el juego!');
              this.obtenerDatos()
            });
          }
        })
      },
      eliminaPosesion(value){
        Swal.fire({
          title: "¿Quieres eliminar este juego de tu lista de propiedades?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, eliminalo',
        }).then((result) => {
          if(result.isConfirmed){
            axios.delete('http://gamereviewsproject.herokuapp.com/api/game_user/delete_posesion', {
              params: {
                game_id: value,
                user_id: this.current_user.id
              }
            }).then(response =>{
              toastr.error('Has eliminado este juego de tu lista')
              this.obtenerDatos()
            });
          }
        })
      },
      estableceFondo(value){
        if(value == ''){
          return '#E9ECEF';
        }else if(value < 35){
          return "#ff0000"
        }else if(value < 50){
          return "#ff8000"
        }else if(value < 70){
          return "#ffff00"
        }else if(value < 85){
          return "#6cc414"
        }else {
          return "#005000"
        }
      },
      fondoPaginas(value){
        if(value == this.pagina){
          return '#245e13';
        }else {
          return '#002855';
        }
      },
      editarJuego(value){
        this.game_id = value
        this.$bvModal.show("modal-AddGame")
      },
      eliminarJuego(value){
        Swal.fire({
          title: "¿Quieres eliminar este juego de la base de datos?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, eliminalo',
        }).then((result) => {
          if(result.isConfirmed){
            axios.delete('http://gamereviewsproject.herokuapp.com/api/juego/delete_game', {
              params: {
                game_id: value
              }
            }).then(response =>{
              toastr.error('Has eliminado este juego de la base de datos')
              this.obtenerDatos()
            });
          }
        })
      }
    },
    filters: {
      roundValors: function(value) {
        if (!value){
          return '--'
        }
        let fondo = '';
        value = parseFloat(value).toFixed()
        return value;
      },
    }
  }
</script>


<style scoped>

.filtros {
  margin: 30px 0 25px 0;
}


.contenedor{
	display: flex;
  flex-flow: column wrap;
	height: 100%;
  justify-content: center;
  width: 100%;
  align-items:center;
  margin: 0;
}

.page-link:hover {
  cursor: pointer;
}

.contentCard{
  margin: 0 20px;
  display: flex;
  flex-flow: row wrap;
  justify-content: space-evenly;
  min-width: -moz-available;
}

.tarjeta {
	width: 45%;
	margin: 10px 0 30px 0;
  height: 200px;
	display: flex;
	flex-flow: row wrap;
	justify-content: flex-start;
  background: #023e8a;
  color: silver;
  box-shadow: 16px 16px #03045e;
}

.izquierda {
  flex-flow: column;
  align-self: flex-end;
  flex: 1;
  height: 100%;
  display:flex;
}

.opciones{
  display:flex;
  height: 20%;
}

.opcion {
  display: flex;
  flex: 1;
  height: 100%;
  text-align: center;
  width: 100%;
}

.cuadro:hover {
  cursor: pointer;
}

.cuadro {
  width: 50px;
  height: 100%;
}

.caratula {
	height: 80%;
	width: 100%;
  background: #023e8a;
  padding: 10px;

}

.titulo {
  flex: 3;
  height: 100%;
  text-align: center;
  display:flex;
}

.titDes{
  display: flex;
  flex-flow: column wrap;
  width: 100%;
  height: 100%;
  margin-left: 5px;
}

.span{
  display: flex;
  flex-flow: row wrap;
  width: 100%;
}

.span-title, .span-des{
  background: #01497c;
  margin-right: 5px;
}

.tiempo{
  background: #2a6f97;
}

span {
  flex: 1;
}

.span-title {
  flex: 2;
}

.span-title a{
  flex: 2;
  color: silver;
}

.span-valor {
  flex: revert;
  text-align: end;
  font-size: 20px;
  height: max-content;
  color: black;
}

.duracion {
  width: auto;
  height: auto;
  display:flex;
  flex-flow: row;
  font-size: 20px;
  margin: 25px;
}

.tiempo {
  display:flex;
  flex-flow: column;
  margin: 0px 5px;
}

.paginate-bottom{
  display: flex;
  justify-content: center;
}

.page-link {
  background: #023e7d;
  color: white;
}

.etc {
  align-self: end;
  margin: 0 10px;
}

.page-link:hover {
  background: #002855;
  color: white;

}

@media (max-width: 1500px){
  .contenedor{
    align-self: center;
    margin: 0;
  }

  .tarjeta {
    width: 100%;
    height: auto;
    font-size: 10px;
  }

  .izquierda {
    width: 15%;
  }

  span {
    font-size: 15px;
  }

  .span{
    font-size: 13px;
  }

  .span-valor{
    font-size: 16px;
  }

}


@media (max-width: 650px){
  .filtros{
    width: 100%;
  }


}
</style>
