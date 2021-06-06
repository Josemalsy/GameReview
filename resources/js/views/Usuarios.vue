<template>
<div class="cargando" v-if="loading"> <Loading/> </div>

<div class="contenedor" v-else>

  <div class="form-row align-items-center filtros">
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


  <div class="top">
    <nav class="paginate-bottom" aria-label="Page navigation example">
      <ul class="pagination" v-for="n in ultima_pagina">
        <li class="page-item"><a class="page-link" :style="{background: fondoPaginas(n)}" @click="changePage( n )">{{ n }}</a></span></li>
      </ul>
    </nav>


  </div>
  <template>
    <div class="contentCard">
      <div class="tarjeta" v-for="(item,index) in lista_usuarios" :key="index">
        <div class="izquierda">
          <img class="caratula" :src="'../storage/'+ item.avatar" alt="Card image capaaaa">
          <div class="expulsion" v-if="item.estado == 'Expulsado'">
            Expulsado
          </div>
        </div>
        <div class="titulo">
          <div class="titDes">
            <div class="span">
              <span class="span-title"> <a :href="'usuario/'+item.id">{{item.name}} </a></span></span>
              <span class="span-valor"><i class="bi bi-clipboard-data" title="valoraciones realizadas"></i> {{item.reviews_count}}
                <i class="bi bi-controller" title="juegos adquiridos"></i> {{item.games_count}}
              </span>
            </div>
            <div class="duracion">
              Último juego adquirido:
              <p class="tituloJuego" v-if="item.games.length > 0"> {{item.games[item.games.length -1].titulo}} </p>
              <p v-else> -- </p>
            </div>
          </div>
          <div class="membresia">
            Miembro desde: {{item.created_at | formatDate}}
          </div>
        </div>
      </div>
    </div>
  </template>



    <nav class="paginate-bottom" aria-label="Page navigation example">
      <ul class="pagination" v-for="n in ultima_pagina">
        <li class="page-item"><a class="page-link" :style="{background: fondoPaginas(n)}" @click="changePage( n )">{{ n }}</a></span></li>
      </ul>
    </nav>

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
      alterTableCard(){
        this.tarjeta = !this.tarjeta;
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

.contenedor{
	display: flex;
  flex-flow: column wrap;
	height: 100%;
  justify-content: center;
  width: 100%;
}

.filtros {
  margin: 30px 0 25px 0;
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

.expulsion{
  display:flex;
  flex-flow: row;
  height: 20%;
  text-align: center;
  align-self:center;
  color: red;
}

.opcion {
  width: 100%;
  height: 100%;
  text-align: center;
  align-self:center;
}

.caratula {
	height: 80%;
	width: 100%;
  padding: 10px;

}

.titulo {
  width: 80%;
  height: 100%;
  text-align: center;
  display:flex;
  font-size: 20px;
  flex-flow: column;
  flex: 3;
}

.titDes{
  display: flex;
  flex-flow: column wrap;
  width: 100%;
  flex: 10;
}

.span {
  display: flex;
  flex-flow: row wrap;
  width: 100%;
  min-height: 25%;
}

span {
  flex: 1;
}
.span-title {
  flex: 2;
  font-size: 20px;
}

.span-title a{
  color: silver;
}

.span-valor {
  flex: revert;
  text-align: end;
  margin-right: 3px;
  font-size: 20px;
  height: 100%;
  border-radius: 10%;
}


.duracion {
  width: auto;
  height: auto;
  display:flex;
  flex-flow: column;
  font-size: 16px;
  background: #0077b6;
  margin-top: 3px;
  align-self: center;
  padding: 0 10px;
}

.duracion p {
  font-size: 16px;
}

.membresia{
  display:flex;
  flex-flow: row;
  text-align: center;
  align-self:end;
  font-size: 14px;
  margin: 0 5px;
  flex: 1;
}

.tipos {
  display:flex;
  align-items: space-around;
}

.tiempos {
  display:flex;
  align-items: space-around;
}

.paginate-bottom{
  display: flex;
  justify-content: center;
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

.table {
  text-align: center;
  width: 50%;
  font-size: 15px;
  margin: 25px 25px 25px 0;
  align-self: center;
}


@media (max-width: 1300px){
  .contenedor{
    align-self: center;
    margin: 0;
  }

  .tarjeta {
    width: 100%;
    font-size: 14px;
  }

  .izquierda{
    width: 30%;
  }

  .titulo{
    width: 70%;
    flex-flow: column wrap;
  }
}


@media (max-width: 650px){
  .filtros{
    width: 100%;
    height: auto;
    justify-content:center;
  }

  .boton{
    justify-self: left;
  }

  .busqueda {
    width: 90%;
  }
  .duracion, .span {
    padding: 0px;
  }
}
</style>