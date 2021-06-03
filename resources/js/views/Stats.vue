<template>
  <div class="pagina">

    <div class="boton">
      <button class="btn sex-users" @click="getStatsSexUsers">
        <template v-if="!sexoUserMostrar">Mostrar gráfico de usuarios por sexo</template><template v-else> Ocultar gráfico de usuarios por sexo</template>
      </button>

      <template v-if="sexoUserMostrar && loading.loadingSexoUsers == false">
        <pie-chart :data="dataSexoUsers" :options="optionsSexoUsers" :height="350" class="pie-grafico"></pie-chart>
      </template>
    </div>

    <div class="boton">
      <button class="btn game-plataforma" @click="getStatsGamePlataformas">
        <template v-if="!gamePlataformasMostrar">Mostrar gráfico de juegos por plataforma</template><template v-else> Ocultar gráfico de juegos por plataforma</template>
      </button>

      <template v-if="gamePlataformasMostrar && loading.loadingGamePlataformas == false">
        <bar-chart :chart-data="dataPlataformas" :options="optionsPlataformas"></bar-chart>
      </template>
    </div>

    <div class="boton">
      <button class="btn game-fabricante" @click="getStatsGameFabricante">
        <template v-if="!gameFabricanteMostrar">Mostrar gráfico de juegos por fabricante</template><template v-else> Ocultar gráfico de juegos por fabricante</template>
      </button>

      <template v-if="gameFabricanteMostrar && loading.loadingGameFabricante == false">
        <bar-chart :chart-data="dataFabricante" :options="optionsFabricante"></bar-chart>
      </template>
    </div>

    <div class="boton">
      <button class="btn game-genero" @click="getStatsGameGenero">
        <template v-if="!gameGeneroMostrar">Mostrar gráfico de juegos por género</template><template v-else> Ocultar gráfico de juegos por género</template>
      </button>

      <template v-if="gameGeneroMostrar && loading.loadingGameGenero == false">
        <bar-chart :chart-data="dataGenero" :options="optionsGenero"></bar-chart>
      </template>
    </div>

  </div>
</template>

<script>
import PieChart from '../components/chartJs/PieChart.js'
import BarChart from '../components/chartJs/BarChart.js'


export default {
  components: {
		PieChart, BarChart
  },
  data() {
    return {
      loading: {
        loadingSexoUsers: true,
        loadingGamePlataformas: true,
        loadingGameFabricante: true,
        loadingGameGenero: true,
      },
      //SEXO-USERS
      cargarSexoUsers: false,
      sexoDatos: [],
      sexoNombre: [],
      sexoUserMostrar: false,
      dataSexoUsers: [],
      optionsSexoUsers: {
        responsive: true,
				maintainAspectRatio: false,
				aspectRatio: 1,
      },

      //GAME-PLATAFORMAS
      cargarGamePlataformas: false,
      gamePlataformasMostrar:false,
			plataformas: [],
			valoresPlataformas: [],
			optionsPlataformas: {
				responsive: true,
				maintainAspectRatio: false,
				height: 350,
				scales: {
					xAxes: [{
						ticks: {
							stepSize: 5,
							fixedStepSize: 1,
							beginAtZero: true,
						},
						gridLines: {
							display: false
						},
						scaleLabel: {
							display: true,
							labelString: 'cantidad de juegos'
						},
					}],
					yAxes: [{
						ticks:{
							beginAtZero: true,
							stepSize: 5,
							fixedStepSize: 1,
						},
						gridLines: {
							display: true
						},
						scaleLabel: {
							display: true,
							labelString: 'nombre de plataforma'
						},
					}]
				}
			},

      //GAME-FABRICANTE
      cargarGameFabricante: false,
      gameFabricanteMostrar:false,
			fabricantes: [],
			valoresFabricante: [],
			optionsFabricante: {
				responsive: true,
				maintainAspectRatio: false,
				height: 350,
				scales: {
					xAxes: [{
						ticks: {
							stepSize: 5,
							fixedStepSize: 1,
							beginAtZero: true,
						},
						gridLines: {
							display: false
						},
						scaleLabel: {
							display: true,
							labelString: 'cantidad de juegos'
						},
					}],
					yAxes: [{
						ticks:{
							beginAtZero: true,
							stepSize: 5,
							fixedStepSize: 1,
						},
						gridLines: {
							display: true
						},
						scaleLabel: {
							display: true,
							labelString: 'Compañía'
						},
					}]
				}
			},

      //GAME-GENERO
      cargarGameGenero: false,
      gameGeneroMostrar:false,
			generos: [],
			valoresGenero: [],
			optionsGenero: {
				responsive: true,
				maintainAspectRatio: false,
				height: 350,
				scales: {
					xAxes: [{
						ticks: {
							stepSize: 5,
							fixedStepSize: 1,
							beginAtZero: true,
						},
						gridLines: {
							display: false
						},
						scaleLabel: {
							display: true,
							labelString: 'cantidad de juegos'
						},
					}],
					yAxes: [{
						ticks:{
							beginAtZero: true,
							stepSize: 5,
							fixedStepSize: 1,
						},
						gridLines: {
							display: true
						},
						scaleLabel: {
							display: true,
							labelString: 'Género'
						},
					}]
				}
      }
    }
  },
  methods: {
    getStatsSexUsers(){
      this.sexoUserMostrar = !this.sexoUserMostrar
      if(this.cargarSexoUsers == false){
        this.loading.loadingSexoUsers = true
        axios.get('https://gamereviewsproject.herokuapp.com:8000/api/stats_sexo_usuarios/').then(response =>{
          let arraySize = response.data.length
          for(let x = 0; x < arraySize; x++){
            this.sexoDatos.push(response.data[x].cantidad)
            this.sexoNombre.push(response.data[x].sexo)
          }
          this.dataSexoUsers = {
            hoverBackgroundColor: "red",
            hoverBorderWidth: 10,
            labels: this.sexoNombre,
            datasets: [
              {
                label: "Data One",
                backgroundColor: ["#41B883", "#E46651", "#00D8FF", "#9147FF","#FCDAC2",
                                  "#9AD0F5","#22CFCF","#FF4069","#FFCD56","#FF9F40",
                                  "#633100","#305534","#FF7F00","#DE0000","#ADC9E7",],
                data: this.sexoDatos
              }
            ]
          }
          this.cargarSexoUsers = true
          this.loading.loadingSexoUsers = false
        });
      }
    },
    getStatsGamePlataformas(){
      this.gamePlataformasMostrar = !this.gamePlataformasMostrar
      if(this.cargarGamePlataformas == false) {
        this.loading.loadingGamePlataformas = true
        axios.get('https://gamereviewsproject.herokuapp.com:8000/api/getCountJuegosPorPlataforma/').then(response =>{
          let arraySize = response.data.length
          for(let x = 0; x < arraySize; x++){
            this.valoresPlataformas.push(response.data[x].cantidad)
            this.plataformas.push(response.data[x].nombre)
          }
          this.cargarGamePlataformas = true
          this.loading.loadingGamePlataformas = false
        });
				this.dataPlataformas = {
					labels: this.plataformas,
					datasets: [{
						axis: 'y',
						label: 'Cantidad',
						barThickness: 20,
						backgroundColor: ["#41B883", "#E46651", "#00D8FF", "#9147FF","#FCDAC2",
															"#9AD0F5","#22CFCF","#FF4069","#FFCD56","#FF9F40",
															"#633100","#305534","#FF7F00","#DE0000","#ADC9E7",
                              "black","#cd7f32","#C0C0C0","#7d2181","#2B4562"],
						data: this.valoresPlataformas,
					}]
				}
      }
    },
    getStatsGameFabricante(){
      this.gameFabricanteMostrar = !this.gameFabricanteMostrar
      if(this.cargarGameFabricante == false) {
        this.loading.loadingGameFabricante = true
        axios.get('https://gamereviewsproject.herokuapp.com:8000/api/getCountJuegosPorFabricante/').then(response =>{
          let arraySize = response.data.length
          for(let x = 0; x < arraySize; x++){
            this.valoresFabricante.push(response.data[x].cantidad)
            this.fabricantes.push(response.data[x].fabricante)
          }
          this.cargarGameFabricante = true
          this.loading.loadingGameFabricante = false
        });
				this.dataFabricante = {
					labels: this.fabricantes,
					datasets: [{
						axis: 'y',
						label: 'Cantidad',
						barThickness: 60,
						backgroundColor: ["#0F7C0F","#DE0000","black","#30A4FB","#00439C"],
						data: this.valoresFabricante,
					}]
				}
      }
    },
    getStatsGameGenero(){
      this.gameGeneroMostrar = !this.gameGeneroMostrar
      if(this.cargarGameGenero == false) {
        this.loading.loadingGameGenero = true
        axios.get('https://gamereviewsproject.herokuapp.com:8000/api/getCountJuegosPorGenero/').then(response =>{
          let arraySize = response.data.length
          for(let x = 0; x < arraySize; x++){
            this.valoresGenero.push(response.data[x].cantidad)
            this.generos.push(response.data[x].nombre)
          }
          this.cargarGameGenero = true
          this.loading.loadingGameGenero = false
        });
				this.dataGenero = {
					labels: this.generos,
					datasets: [{
						axis: 'y',
						label: 'Cantidad',
						barThickness: 40,
						backgroundColor: ["#41B883", "#E46651", "#00D8FF", "#9147FF","#FCDAC2",
															"#9AD0F5","#22CFCF","#FF4069","#FFCD56","#FF9F40",
															"#633100","#305534","#FF7F00","#DE0000","#ADC9E7",
                              "black","#cd7f32","#C0C0C0","#7d2181","#2B4562"],
						data: this.valoresGenero,
					}]
				}
      }
    }
  },
}
</script>

<style scoped>

.pagina {
  width: 100%;
  height: auto;
}

.estadisticas{
	width: 100%;
	display:flex;
	flex-flow: column wrap;
}

.boton {
  margin: 20px 30px;
}

.pie-grafico {
	width: 20%;
	align-self: center;
  margin: auto;
}

.sex-users {
  background: #C0E6ED;
}

.game-plataforma {
  background: #D7A86C;
}

.game-fabricante {
  background: #0069D9;
  color: white;
}

.game-fabricante:hover {
  color: white;
}

.game-genero {
  background: #D2515D;
}



@media (max-width: 700px){
  .pie-grafico {
    width: 100%;
  }

  .GP-grafico {
    width: auto;
  }
}


</style>