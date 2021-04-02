<template>
	<div class="cargando" v-if="loading"> <Loading/> </div>

<div class="contenedor" v-else>

  <div class="filtros">
    <div class="busquedas">
      <label for="busqueda">Busca por título o género </label>
      <input type="text" placeholder="Introduce tu búsqueda" id="busqueda" @keypress.prevent.enter="obtenerDatos" class="form-control" v-model="filters.buscador">

      <label for="ordernarPor">Elige como ordenar los resultados</label>
      <select class="form-control" id="ordernarPor" v-model="orden" v-on:change="obtenerDatos">
        <option value="1">Titulo 🡹</option>
        <option value="2">Titulo 🡻</option>
        <option value="3">Fecha de lanzamiento 🡹</option>
        <option value="4">Fecha de lanzamiento 🡻</option>
      </select>
      <button type="button" class="btn btn-success" @click="obtenerDatos">Filtrar</button>
    </div>
  </div>

<nav class="paginate-bottom" id="paginacion" aria-label="Page navigation example">
  <ul class="pagination">
    <li class="page-item"><a class="page-link" v-on:click="changePage( page - 1)"> ← </a></li>

    <li class="page-item"><a class="page-link" v-on:click="changePage( page -1 )">{{ page - 1 }}</a></li>
    <li class="page-item"><a class="page-link" v-on:click="changePage( page )">{{ page }}</a></li>
    <li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)">{{ page +1 }}</a></li>

    <li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)"> → </a></li>
  </ul>
</nav>

  <div class="contentCard">
    <div class="tarjeta" v-for="(item,index) in lista_juegos" :key="index">
      <div class="pie">
        <img class="caratula" :src="'../storage/'+ item.imagen" alt="Card image">
        <div class="opciones">
          <div class="opcion" v-if="checkUser(item.users)" @click="eliminaPosesion(item.id)"><i class="bi bi-heart-fill" style="color:red;"></i></div>
          <div class="opcion" v-else @click="agregaPosesion(item.id)"><i class="bi bi-heart"></i></div>
        </div>
      </div>
      <div class="titulo">
        <div class="titDes">
          <div class="span">
            <span class="span-title"> <a class="juegoId" :href="'game/'+item.id">{{item.titulo}} </a></span>
            <span class="span-des">{{item.desarrolladora}}</span>
            <span class="span-valor" :style="{background: estableceFondo(item.reviews_avg_puntuacion)}"><i class="bi bi-clipboard-data" title="valoracion"></i> {{item.reviews_avg_puntuacion | roundValors}}% </span>
          </div>
          <div class="duracion">
            <span class="tiempo">Base <p>{{item.reviews_avg_juego_base | roundValors }}H</p></span>
            <span class="tiempo">Extras <p>{{ item.reviews_avg_juego_extendido | roundValors}}H</p></span>
            <span class="tiempo">100% <p>{{ item.reviews_avg_completado_total | roundValors}}H</p></span>
          </div>
        </div>
      </div>
    </div>
  </div>

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
    <li class="page-item"><a class="page-link" v-on:click="changePage( page - 1)"> ← </a></li>

    <li class="page-item"><a class="page-link" v-on:click="changePage( page -1 )">{{ page - 1 }}</a></li>
    <li class="page-item"><a class="page-link" v-on:click="changePage( page )">{{ page }}</a></li>
    <li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)">{{ page +1 }}</a></li>

    <li class="page-item"><a class="page-link" v-on:click="changePage( page + 1)"> → </a></li>
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
        page: 1,
        pages: 1,
        orden: 1,
        filters: {
          buscador: '',
        }
      }
    },
    mounted(){
      this.obtenerDatos();
    },
    methods: {
      obtenerDatos(){
        const params = {
        page: this.page
        }
        this.loading =  true,
        // this.page++;
        axios.get('http://localhost:8000/api/juegos/?page='+ this.page, {
          params: {
            orden: this.orden,
            filters: this.filters.buscador,
          }
        }).then(response =>{
          this.lista_juegos = response.data.data;
          this.pages = response.data.last_page
          this.loading =  false
        });
      },
      changePage( page ) {
        this.page = (page <= 0 || page > this.pages) ? this.page : page
        this.obtenerDatos()
      },
      checkUser(value){
        if(value){
          for (let index = 0; index < value.length; index++) {
            if(value[index].id == this.current_user){
              return true
            }
            return false
          }
        }else{
          return false
        }
      },
      agregaPosesion(value){
        Swal.fire({
          title: "¿Quieres agregar este juego a tu lista de propiedades?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, agregalo',
        }).then((result) => {
          if(result.isConfirmed){
            axios.post('http://localhost:8000/api/game_user/create_posesion', {
              params: {
                game_id: value
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
          title: "¿Quieres eliminar este juego d tu lista de propiedades?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, eliminalo',
        }).then((result) => {
          if(result.isConfirmed){
            axios.delete('http://localhost:8000/api/game_user/delete_posesion', {
              params: {
                game_id: value
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

.contenedor{
	display: flex;
  flex-flow: column wrap;
	height: 100%;
  justify-content: center;
  width: 100%;
  margin: 0 0 0 50px;
  align-items:center;
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
  height: 200px;
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

.opciones{
  display:flex;
  flex-flow: row column;
  height: 20%;
  width: 50px;
}

.opcion {
  flex: 1;
  height: 100%;
  /* border: 1px solid blue; */
  text-align: center;

}

.opciones:hover {
  cursor: pointer;
}

.caratula {
	/* border: 1px solid red; */
	height: 80%;
	width: 100%;
  background: #023e8a;
  padding: 10px;

}

.titulo {
    /* border: 1px solid purple; */
    width: 80%;
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
  /*border: 1px solid white;*/
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
  height: 100%;
  color: black;
}

.duracion {
  width: auto;
  height: auto;
  display:flex;
  flex-flow: row;
  font-size: 20px;
  margin: 5px;
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
  /* border-radius: 50%; */

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

  .pie {
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
