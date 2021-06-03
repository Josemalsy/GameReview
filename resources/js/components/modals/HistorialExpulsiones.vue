<template>
  <b-modal hide-footer ref="modal" id="modal-historialExpulsiones" size="xl" title="Historial de expulsiones" @shown="beforeOpen">
    <template v-if="expulsiones.length == 0">
      <template v-if="loading"> <Loading/> </template>
      <template v-else>
        <div class="limpio"> No hay expulsiones</div>
      </template>
    </template>
    <template v-else>
    <template v-if="loading"> <Loading/> </template>
      <template v-else>
        <div class="lista">
          <div class="tabla">
            <div class="causa"> Causa de expulsión </div>
            <div class="tipo"> Tipo de expulsión </div>
            <div class="fecha"> Fecha de expulsion</a></div>
          </div>
          <div class="tabla" v-for="(item, index) in expulsiones" :key="index">
            <div class="causa"> {{item.causa}}</div>
            <div class="tipo"> {{item.tipo_expulsion}} </div>
            <div class="fecha">{{item.created_at | formatDate}}</a></div>
          </div>
        </div>
      </template>
    </template>
  </b-modal>
</template>

<script>
import moment from 'moment'

export default {
  props : ['user_id'],
  data() {
    return {
      loading: true,
      expulsiones: []
    }
  },
  methods: {
    beforeOpen(){
      this.loading = true
      axios.get('http://gamereviewsproject.herokuapp.com:8000/api/getExpulsionesByUserId',{
        params: {
          user_id: this.user_id,
        }
      }).then(response => {
        this.expulsiones = response.data;
        this.loading = false
      })
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

.limpio{
  display: flex;
  flex-flow: column;
  padding: 10px;
  border: 1px solid white;
  font-size: 18px;
  background: #C0E6ED;
}

.tabla {
  display: flex;
  align-items: center;
}

.causa, .tipo, .fecha {
  display: flex;
  flex-flow: column;
  padding: 10px;
  border: 1px solid white;
  flex:7;
  font-size: 18px;
  background: #D2515D;
}

</style>