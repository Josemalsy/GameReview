<template>
<div class="cargando" v-if="loading"> <Loading/> </div>

<div class="contenedor" v-else>

    <div class="barra">
      <div class="separacion">
        <span class="separador" @click="muestraOpciones">
          <template v-if="!opciones"> Mostrar opciones de busqueda </template>
          <template v-else> Ocultar opciones de busqueda</template>
        </span>
      </div>
    </div>

  <div class="form-row align-items-center filtros" v-if="opciones">
    <div class="col-auto">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <div class="input-group-text">Buscar usuario </div>
        </div>
        <input type="text" placeholder="buscar..." id="busqueda" @keypress.prevent.enter="obtenerDatos" class="form-control" v-model="filters.buscador">
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
        <select class="form-control" id="ordernarPor" v-model="filters.orden" v-on:change="obtenerDatos">
          <option value="1">Nombre usuario 🡹</option>
          <option value="2">Nombre usuario 🡻</option>
          <option value="3">Más antiguo 🡹</option>
          <option value="4">Más reciente 🡻</option>
        </select>
      </div>
    </div>
  </div>

  <template>
    <div class="contentCard">
      <div class="tarjeta" v-for="(item,index) in lista_usuarios" :key="index">
        <div class="izquierda">
          <img class="caratula" :src="'https://gamereviewsprojectdaw.s3.eu-west-3.amazonaws.com/'+ item.avatar" alt="Card image capa">
          <div class="expulsion" v-if="item.estado == 'Expulsado'">
            Expulsado
          </div>
          <div class="membresia"><div>Miembro desde: {{item.created_at | formatDate}}</div></div>
        </div>
        <div class="centro">
          <div class="cabecera">
            <div class="nombre">{{item.name}}</div>
          </div>
          <div class="central">
            <div class="texto">
              <div class="central-text">Juegos</div>
                <hr class="">
              <div class="central-text">Reviews </div>
                <hr class="">
              <div class="central-text">Ultimo juego: </div>
            </div>
            <div class="respuesta">
              <div class="central-respuesta">{{item.games_count}}</div>
                <hr class="">
              <div class="central-respuesta">{{item.reviews_count}}</div>
                <hr class="">
              <div class="central-respuesta" v-if="item.games.length > 0">{{item.games[item.games.length -1].titulo}}</div>
              <div class="central-respuesta" v-else>Sin datos</div>
            </div>
          </div>
          <div class="ver-perfil"> <button class="btn btn-success"> <a :href="'usuario/'+item.id"> visitar perfil</a>  </button>  </div>
        </div>
      </div>
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


</div>

</template>

<script>
import moment from 'moment'

  export default {
    data() {
      return {
        loading: true,
        lista_usuarios: [],
        page: 1,
        ultima_pagina: null,
        pages: 1,
        fondo: '',
        filters: {
          buscador: '',
          orden: 1,
        },
        tarjeta: true,
        opciones: false,
      }
    },
    mounted(){
      this.obtenerDatos();
    },
    methods: {
      obtenerDatos(){
        this.loading = true
        axios.post('http://gamereviewsproject.herokuapp.com/api/get_all_users/?page='+ this.page,this.filters, {
          params: {
            filters: this.filters.buscador,
            orden: this.filters.orden,
          }
        }).then(response =>{
          this.lista_usuarios = response.data.data;
          this.pages = response.data.last_page
          this.ultima_pagina = response.data.last_page
          this.pagina = response.data.current_page
          this.loading = false
        });
      },
      muestraOpciones(){
        this.opciones = !this.opciones;
      },
      changePage( page ) {
        this.page = (page <= 0 || page > this.pages) ? this.page : page
        this.obtenerDatos()
      },
      fondoPaginas(value){
        if(value == this.pagina){
          return '#245e13';
        }else {
          return '#002855';
        }
      },
    },
    filters: {
      formatDate(value){
        return moment(String(value)).format('DD-MM-YYYY')
      }
    }
  }
</script>


<style scoped>
@import url(http://fonts.googleapis.com/css?family=Share+Tech);

.contenedor{
	display: flex;
  flex-flow: column wrap;
	height: 100%;
  justify-content: center;
  width: 100%;
}


.barra{
  display:flex;
  justify-content: center;
  width: 100%;
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

.filtros {
  margin: 30px 120px 25px 0;
  display:flex;
  justify-content: center;
}


.busqueda {
  width: 35%;
}

.top {
  display: grid;
  flex-flow: row;
  align-self: center;
  width: 80%;
}

.boton {
  font-size: 28px;
  margin-top: -56px;
  justify-self: right;
}

.boton i:hover{
  font-size: 28px;
  margin-top: -56px;
  justify-self: right;
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
  height: auto;
	display: flex;
	flex-flow: row wrap;
	justify-content: flex-start;
  background: #023e8a;
  color: black;
  margin-top: 58px;
  background: #C0E6ED;
}

.izquierda {
  flex-flow: column;
  align-self: flex-end;
  flex: 1;
  height: 100%;
  display:flex;
  border-right: 1px solid white;
}

.expulsion{
  display:flex;
  flex-flow: row;
  height: 20%;
  text-align: center;
  align-self:center;
  color: red;
}

.caratula {
	height: 80%;
	width: 100%;
  padding: 10px;
  margin-top: -40px;
  border-radius: 50%;
}


.centro {
  flex-flow: column wrap;
  display: flex;
  flex: 3;
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


.nombre {
  display:flex;
  text-align: left;
  font-size: 24px;
  padding: 0px 20px;
  flex: 1;
  font-family: "Share Tech", sans-serif;
  background: #074680;
  color: white;

}

.central {
  display: flex;
  flex-flow: row wrap;
  margin: 10px 20px 0 20px;
  flex: 2;
  align-items: center;
}

.texto{
  flex: 1;
}

.central-text {
/*  border-bottom: 1px solid white; */
  flex: 1;
}

.respuesta {
  flex: 1;
  text-align: right;
}

.central-respuesta {
/*  border-bottom: 1px solid white; */
  flex: 1;

}

.ver-perfil, .ver-perfil a {
  align-self: center;
  color: white;
  text-decoration: none;
}

.membresia{
  display:flex;
  flex-flow: row;
  text-align: center;
  align-self:end;
  font-size: 14px;
  margin: auto;
  align-items:flex-end;
  flex: 1;
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


@media (max-width: 1300px){
  .contenedor{
    align-self: center;
    margin: 0;
    display:block;
  }

  .tarjeta {
    width: 100%;
    font-size: 14px;
  }


  .izquierda{
    width: 30%;
  }

  .nombre{
    width: 70%;
    flex-flow: column wrap;
  }
}


@media (max-width: 650px){

  .nombre {
    font-size:15px;
  }

  .central {
    font-size: 13px;
  }

}
</style>