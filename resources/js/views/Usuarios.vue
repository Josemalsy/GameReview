<template>
<div class="cargando" v-if="loading"> <Loading/> </div>

<div class="contenedor" v-else>
  <div class="filtros">
    <div class="busquedas">
      <label for="busqueda">Buscar usuario: </label>
      <input type="text" placeholder="Introduce tu búsqueda" id="busqueda" @keypress.prevent.enter="obtenerDatos" class="form-control" v-model="filters.buscador">

      <label for="ordernarPor">Ordenar por: </label>
      <select class="form-control" id="ordernarPor" v-model="orden" v-on:change="obtenerDatos">
        <option value="1">Nombre usuario 🡹</option>
        <option value="2">Nombre usuario 🡻</option>
        <option value="3">Más antiguo 🡹</option>
        <option value="4">Más reciente 🡻</option>
      </select>
      <button type="button" class="btn btn-success" @click.preventDefault="obtenerDatos">Filtrar</button>
    </div>
  </div>

  <div class="top">
    <nav class="paginate-bottom" id="paginacion" aria-label="Page navigation example">
      <ul class="pagination">
        <li class="page-item"><a class="page-link" v-on:click="changePage( page - 1)"><<</a></li>

        <li class="page-item"><a class="page-link" v-on:click="changePage( page -1 )">{{ page - 1 }}</a></li>
        <li class="page-item"><a class="page-link" v-on:click="changePage( page )">{{ page }}</a></li>
        <li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)">{{ page +1 }}</a></li>

        <li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)">>></a></li>
      </ul>
    </nav>

    <div class="boton" @click="alterTableCard">
      <template v-if="tarjeta"> <i class="bi bi-table" title="table"> </i> </template>
      <template v-else> <i class="bi bi-card-list" title="tarjeta"></i> </template>
    </div>

  </div>
  <template v-if="tarjeta">
    <div class="contentCard">
      <div class="tarjeta" v-for="(item,index) in lista_usuarios" :key="index">
        <div class="pie">
          <img class="caratula" :src="'../storage/'+ item.avatar" alt="Card image capaaaa">
          <div class="rangos">
            Rango
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
              <p v-if="item.games.length > 0"> {{item.games[item.games.length -1].titulo}} </p>
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

  <template v-else>
    <table class="table table-striped table-primary">
  <thead>
    <tr>
      <th style="min-width: 150px;">Nombre</th>
      <th style="min-width: 120px;">Reseñas</th>
      <th style="min-width: 120px;">Juegos</th>
      <th style="min-width: 200px;">Ultimo juego</th>
      <th style="min-width: 100px;">registrado</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="(item,index) in lista_usuarios" :key="index">
      <td> {{item.name}} </td>
      <td> {{item.reviews_count}} </td>
      <td> {{item.games_count}} </td>
      <td> <span v-if="item.games.length > 0"> {{item.games[0].titulo}} </span> <span v-else> -- </span> </td>
      <td> {{item.created_at | formatDate}} </td>
    </tr>
  </tbody>
</table>
  </template>
  <!-- <nav class="Page navigation example" role="navegation" aria-label="pagination">

    <a class="pagination-previous" v-on:click="changePage( page - 1)"> Anterior </a>

      <ul class="pagination">
        <li class="page-item">
          <a class="pagination-link is-current">{{ page }}</a>
        </li>
      </ul>

    <a class="pagination-next" v-on:click="changePage( page + 1)"> Siguiente </a>

  </nav> -->

<nav class="paginate-bottom" aria-label="Page navigation example">
  <ul class="pagination">
    <li class="page-item"><a class="page-link" v-on:click="changePage( page - 1)">Previous</a></li>
    <li class="page-item"><a class="page-link" v-on:click="changePage( page -1 )">{{ page - 1 }}</a></li>
    <li class="page-item"><a class="page-link" v-on:click="changePage( page )">{{ page }}</a></li>
    <li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)">{{ page +1 }}</a></li>

    <li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)">Next</a></li>
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
        pages: 1,
        orden: 1,
        fondo: '',
        filters: {
          buscador: '',
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
        const params = {
        page: this.page
        }
        // this.page++;
        axios.get('http://localhost:8000/api/usuarios/?page='+ this.page, {
          params: {
            orden: this.orden,
            filters: this.filters.buscador,
          }
        }).then(response =>{
          this.lista_usuarios = response.data.data;
          this.pages = response.data.last_page
          this.loading = false
        });
      },
      alterTableCard(){
        this.tarjeta = !this.tarjeta;
      },
      changePage( page ) {
        this.page = (page <= 0 || page > this.pages) ? this.page : page
        this.obtenerDatos()
      }
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
  margin: 0 0 0 50px;
}

.filtros{
  display: flex;
  /* border: 1px solid brown; */
  flex-flow: column wrap;
  height: 200px;
  width: 80%;
  margin-top: 20px;
  margin-bottom: 50px;
  align-items: center;
  justify-content: center;
  background: #023e8a;
  color: white;
  border-radius: 1em/1em;
  align-self: center;
  text-align: center;
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
	/* border: 1px solid green; */
	width: 45%;
	margin: 10px 0 30px 0;
  height: 150px;
	display: flex;
	flex-flow: row wrap;
	justify-content: flex-start;
  background: #023e8a;
  color: silver;
  box-shadow: 16px 16px #03045e;
}

.pie {
  /* border: 1px solid brown; */
  flex-flow: column;
  align-self: flex-end;
  width: 20%;
  height: 100%;
  display:flex;
}

.rangos{
  display:flex;
  flex-flow: row;
  height: 20%;
  text-align: center;
  align-self:center;
  /* width: 100%; */
}

.opcion {
  width: 100%;
  height: 100%;
  /* border: 1px solid blue; */
  text-align: center;
  align-self:center;
}

.caratula {
	/* border: 1px solid red; */
	height: 80%;
	width: 100%;
  padding: 10px;

}

.titulo {
    /* border: 1px solid purple; */
    width: 80%;
    height: 100%;
    text-align: center;
    display:flex;
    font-size: 20px;
    flex-flow: column;
}

.titDes{
  display: flex;
  flex-flow: column wrap;
  width: 100%;
  height: 100%;
}

.span {
  display: flex;
  flex-flow: row wrap;
  width: 100%;
  min-height: 25%;
}

span {
  flex: 1;
  /*border: 1px solid white;*/
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
  padding: 0px 10px;
}

.membresia{
  display:flex;
  flex-flow: row;
  height: 20%;
  text-align: center;
  align-self:end;
  font-size: 14px;
  /* width: 100%; */
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
  /* border-radius: 50%; */

}

.page-link:hover {
  background: #002855;
  color: white;

}

.table {
  /* background: #C6E0F5; */
  text-align: center;
  width: 50%;
  font-size: 15px;
  margin: 25px 25px 25px 0;
  align-self: center;
}

/* thead {
  border-bottom: 1px solid black;
  font-size: 16px;
  color: black;
}

th{
  padding: 10px;
} */


@media (max-width: 1300px){
  .contenedor{
    align-self: center;
    margin: 0;
  }

  .tarjeta {
    width: 100%;
    font-size: 14px;
  }

  .pie{
    width: 30%;
  }

  .titulo{
    width: 70%;
  }
}


@media (max-width: 650px){
  .filtros{
    width: 50%;
    height: auto;
  }

  .boton{
    justify-self: left;
  }

  .busqueda {
    width: 90%;
  }

}
</style>