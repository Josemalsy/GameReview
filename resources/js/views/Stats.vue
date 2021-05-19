<template>
  <div class="pagina">
    <div class="estadisticas">

      <div class="boton">
        <button class="btn btn-primary" @click="getStatsSexUsers">
          <template v-if="!sexoUserMostrar">Mostrar gráfico de usuarios por sexo</template><template v-else> Ocultar gráfico de usuarios por sexo</template>
        </button>

        <template v-if="sexoUserMostrar && loading.loadingSexoUsers == false">
          <pie-chart :data="dataSexoUsers" :options="optionsSexoUsers" :height="350" class="pie-grafico"></pie-chart>
        </template>
      </div>

    </div>
  </div>
</template>

<script>
import PieChart from '../components/chartJs/PieChart.js'


export default {
  components: {
		PieChart
  },
  data() {
    return {
      loading: {
        loadingSexoUsers: true,
      },
      cargarSexoUsers: false,
      sexoDatos: [],
      sexoNombre: [],
      sexoUserMostrar: false,
      dataSexoUsers: [],
      optionsSexoUsers: {
        responsive: true,
				maintainAspectRatio: false,
				aspectRatio: 1,
      }

    }
  },
  methods: {
    getStatsSexUsers(){
      this.sexoUserMostrar = !this.sexoUserMostrar
      if(this.cargarSexoUsers == false){
        this.loadingSexoUsers = true
        axios.get('http://localhost:8000/api/stats_sexo_usuarios/').then(response =>{
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
    }
  },
}
</script>

<style scoped>

.pagina {
  border: 1px solid black;
  width: 100%;
  height: 1200px;
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

@media (max-width: 700px){
  .pie-grafico {
    width: 100%;
  }
}


</style>