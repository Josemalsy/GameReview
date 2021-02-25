<template>

<div class="contenedor">

  <div class="filtros">

  </div>
  <div class="contentCard">
    <div class="tarjeta" v-for="item in list">
      <div class="pie">
        <img class="caratula" :src="'http://localhost/GameReviews/storage/app/public/'+ item.imagen" alt="Card image capaaaa">
        <div class="opciones">
          <div class="opcion"><i class="bi bi-heart"></i></div><div class="opcion">2</div><div class="opcion">3</div>
        </div>
      </div>
      <div class="titulo">
        <div class="titDes">
          <div class="span">
            <span>{{item.titulo}} </span>
            <span>{{item.desarolladora}}</span>
          </div>
          <div class="valoraciones">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</template>

<script>
  export default {
    data() {
      return {
        list: [],
          page: 0,
      }
    },
    methods: {
      obtenerDatos(){
        this.page++;
        let url = 'http://localhost:8000/api/juegos';

        axios.get(url).then(response =>{
          let juegos = response.data.data;
          if(juegos.length){
            this.list = this.list.concat(juegos);

          }
        });
      }
    },
    mounted(){
      this.obtenerDatos();
    }
  }
</script>


<style scoped>

.contenedor{
	display: flex;
	flex-flow: row wrap;
	height: 100%;
  justify-content: center;

}

.filtros{
  border: 1px solid brown;
  height: 200px;
  width: 95%;
  margin-top: -80px;
  margin-bottom: 50px;

}

.contentCard{
  margin: 0 20px;
  display: flex;
  flex-flow: row wrap;
  justify-content: space-evenly;
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
  box-shadow: 5px 16px #03045e;
}

.pie {
  /* border: 1px solid brown; */
  flex-flow: column;
  align-self: flex-end;
  width: 15%;
  height: 100%;
  display:flex;
}

.opciones{
  display:flex;
  flex-flow: row column;
  height: 20%;
}

.opcion {
  width: 33.33%;
  height: 100%;
  /* border: 1px solid blue; */
  text-align: center;
  align-self:;
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
    width: 85%;
    height: 100%;
    text-align: center;
    display:flex;
}

.titDes{
  display: flex;
  flex-flow: column wrap;
  width: 100%;
  height: 100%;
}

.span{
  display: flex;
  flex-flow: row wrap;
  width: 100%;
  height: 50%;
}

span {
  flex: 1;
  /* border: 1px solid white; */

}

.valoraciones {
  /* border: 1px solid yellow; */
  height: 50%;

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

}

</style>