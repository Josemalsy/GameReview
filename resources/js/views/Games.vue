<template>
	<div class="cargando" v-if="loading"> <Loading/> </div>


<div class="contenedor" v-else>

  <div class="separacion">
    <span class="separador" @click="muestraOpciones">
      <template v-if="!opciones"> Mostrar opciones de busqueda </template>
      <template v-else> Ocultar opciones de busqueda</template>
    </span>
  </div>

  <div class="form-row align-items-center filtros" v-if="opciones">
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
          <div class="input-group-text">Ordenar por: </div>
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

  <template>
    <div class="contentCard">
      <div class="tarjeta" v-for="(item,index) in lista_juegos" :key="index">
        <div class="izquierda">
          <img class="caratula" :src="'https://gamereviewsprojectdaw.s3.eu-west-3.amazonaws.com/'+ item.imagen" alt="Card image">
          <div class="ver-juego"><div><button class="btn btn-success"><a class="juegoId" :href="'game/'+item.id"> Visitar juego </a></button></div></div>
        </div>
        <div class="centro">
          <div class="cabecera">
            <div class="titulo">{{item.titulo}}</div>
            <span class="span-valor" :style="{background: estableceFondo(item.valoracion_media)}"><i class="bi bi-clipboard-data" title="valoracion"></i> {{item.valoracion_media | roundValors}}% </span>
          </div>
          <div class="central">
            <div class="texto">
              <div class="central-text">Juego base: </div>
                <hr class="">
              <div class="central-text">Juego + extras: </div>
                <hr class="">
              <div class="central-text">Superado 100% </div>
            </div>
            <div class="respuesta">
              <div class="central-respuesta"> {{item.juegoBase_media | roundValors }} H</div>
                <hr class="">
              <div class="central-respuesta"> {{ item.juegoExtendido_media | roundValors}} H</div>
                <hr class="">
              <div class="central-respuesta">{{ item.completado_total_media | roundValors}} H</div>
            </div>
          </div>
          <div class="opciones">
            <template v-if="current_user != false">
              <template v-if="current_user.rol == 'Administrador'">
                <div class="cuadro"  title="editar juego" @click="editarJuego(item.id)"><i class="bi bi-pencil"></i> </div>
                <div class="cuadro"  title="eliminar juego" @click="eliminarJuego(item.id)"><i class="bi bi-trash"></i> </div>
              </template>
              <template v-if="current_user.email_verified_at">
                <template v-if="current_user.id">
                  <div class="cuadro" v-if="checkUser(item.users)" @click="eliminaPosesion(item.id)" title="eliminar juego de tu lista de adquiridos" ><i class="bi bi-heart-fill" style="color:red;"></i></div>
                  <div class="cuadro" v-b-modal.eligePlataforma v-else @click="sendInfo(item.id)" title="agregar juego a tu lista de adquiridos"><i class="bi bi-heart"></i></div>
                </template>
              </template>
              <template v-else>
                <small>Valida tu email para agregar este juego a tu lista</small>
              </template>
            </template>
          </div>
        </div>
      </div>
      <b-modal id="eligePlataforma" title="Plataforma en la que jugaste" hide-footer ok-only @hidden="cancelData">

        <template v-if="validationErrors">
          <li v-for="(item, index) in validationErrors" :key="index" class="errorServ">{{item | borraCaracteresEspeciales }}</li>
        </template>

        <label for="observación">Elija plataforma</label>
        <select id="observacion" class="form-select mb-3" v-model="form.plataforma_id">
          <option v-for="(plataforma,index) in listaPlataformas" :value="plataforma.id" :key="index">{{plataforma.nombre}}</option>
        </select>
        <button class="btn btn-success" type="submit" v-if="form.plataforma_id" @click="agregaPosesion(form.plataforma_id)">Agregar juego</button>
      </b-modal>
      <modal-AddGame :game_id="game_id" :tituloModal="'Actualizar Juego'"/>
    </div>
  </template>


	<div class="paginas">
    <div class="nombrePagina">Páginas </div>
    <nav class="paginate-bottom" aria-label="Page navigation example">
      <ul class="pagination" v-for="(n,index) in ultima_pagina" :key="index">
        <li class="page-item"><a class="page-link" :style="{background: fondoPaginas(n)}" @click="changePage( n )">{{ n }}</a></li>
      </ul>
    </nav>

  </div>

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
        posesion: null,
        filters: {
          buscador: '',
        },
        abrirModal: false,
        game_id: null,
        validationErrors: null,
        form: {
          plataforma_id: null,
          user_id: this.current_user.id,
          game_id: null,
        },
        opciones: false,
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
        if(value.some(data => data.id === this.current_user.id)){
          return true
        }else {
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
            this.form.game_id = this.game_id
            axios.post('http://gamereviewsproject.herokuapp.com/api/game_user/create_posesion',this.form, {
            }).then(response =>{
              toastr.success('has adquirido el juego!');
              this.obtenerDatos()
            }).catch(error => {
              if (error.response.status == 422){
                this.validationErrors = error.response.data.errors;
              }
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
          return '#3b3b3b';
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
      },
      muestraOpciones(){
        this.opciones = !this.opciones;
      },
      cancelData(){
        this.validationErrors = null
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
      borraCaracteresEspeciales(value){
        for(let i = 0; i <= value.length;i++){
          return value[i]
        }
      }
    }
  }
</script>


<style scoped>
@import url(http://fonts.googleapis.com/css?family=Audiowide);
.filtros {
  margin: 30px 120px 25px 0;
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


.separacion{
	width: 80%;
	height: auto;
	background: #468faf;
	margin-top: 15px;
	display:flex;
	flex-flow: column;
	color: white;
	font-size: 14px;
  border-radius: 1em/1em;
	text-align: center;
  align-self:center;
}

.separacion:hover{
	cursor:pointer;
}


.page-link:hover {
  cursor: pointer;
}

.contentCard{
  margin: 20px 20px;
  display: flex;
  flex-flow: row wrap;
  justify-content: space-evenly;
  min-width: -moz-available;
}

.tarjeta {
	width: 45%;
	margin: 10px 0 30px 0;
	display: flex;
	flex-flow: row wrap;
	justify-content: flex-start;
  background: #C0E6ED;
  color: black;
  height: 290px;
}

.izquierda {
  display:flex;
  flex-flow: column;
  align-self: flex-end;
  flex: 1;
  height: 100%;
  border-right: 1px solid white;
  justify-content: flex-end;
}

.ver-juego, .ver-juego a {
  align-self: center;
  color: white;
  text-decoration: none;
}


.opciones{
  display: flex;
  flex-flow: row wrap;
  font-size: 23px;
  justify-content: space-around;
}

.cuadro:hover {
  cursor: pointer;
}

.opcion {
  display: flex;
  flex: 1;
  height: 100%;
  text-align: center;
  width: 100%;
}

.caratula {
	height: 100%;
	width: 100%;
  padding: 10px;
}

.cabecera {
  display: flex;
  flex-flow: row wrap;
}

.titulo {
  flex: 3;
  height: 100%;
  display:flex;

}

.centro{
  flex-flow: column wrap;
  display: flex;
  flex: 3;
}

.titulo {
  display:flex;
  text-align: left;
  font-size: 24px;
  padding: 0px 20px;
  flex: 1;
  font-family: "Audiowide", sans-serif;
  background: #074680;
  color: white;

}

.texto {
  flex: 1;
}

.respuesta {
  flex: 1;
  text-align: right;
}

.span-valor {
  flex: revert;
  text-align: end;
  font-size: 20px;
  height: max-content;
  color: white;

}

.central {
  display: flex;
  flex-flow: row wrap;
  margin: 10px 20px 0 20px;
  flex: 2;
  align-items: center;
}

.paginas {
	display: flex;
	height: auto;
	margin-top: 10px;
	flex-flow: row wrap;
  padding: 0px 20px;
	width: 90%;
	background: #0077B6;
	color: white;
  align-self: center;
  align-items: center;
  justify-content: space-between;
  border-radius: 1em/1em;
}

.nombrePagina {
  font-size:35px;
}


.paginate-bottom{
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.page-link {
  background: #023e7d;
  color: white;

}

.page-link:hover {
  background: #002855;
  color: white;
  cursor: pointer;
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

  .titulo {
    font-size:15px;
  }

  .central {
    font-size: 13px;
  }


}
</style>
