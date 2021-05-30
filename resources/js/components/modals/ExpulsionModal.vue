<template>
  <b-modal hide-footer ref="modal" id="modal-expulsionModal" size="xl" title="Expulsar usuario" @hidden="cancelData">
    <template v-if="estado == 'Expulsado'">
      <p><button class="btn btn-success" @click="revocar_expulsar" v-if="!revocar">Revocar expulsión</button>
      <button class="btn btn-danger" @click="revocar_expulsar" v-else>Modo expulsar</button></p>
    </template>
    <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="gestionarExpulsion">

    <template v-if="!revocar">
      <label for="causa">Tipo de expulsion</label>
        <select id="tipoExpulsion" class="form-select mb-3" v-model="tipoExpulsion" @click="checkOtro">
          <option value="1">Expulsión de 7 días</option>
          <option value="2">Expulsión de 1 mes</option>
          <option value="3">Expulsión de 3 meses</option>
          <option value="4">Expulsión definitiva</option>
        </select>

      <div class="form-row">
        <label for="causa">Causa de expulsion</label>
        <select id="causa" class="form-select mb-3" v-model="causa" @click="checkOtro">
          <option value="Spoilers">Spoilers</option>
          <option value="Faltas de respeto grave">Faltas de respeto grave</option>
          <option value="Pésima ortografía reiterada">Pésima ortografía</option>
          <option value="Troll">Troll</option>
          <option value="Otro">Otro (Especifica debajo)</option>
        </select>
        <div class="form-group" style="width:100%;" v-if="otro">
          <input type="text" class="form-control " v-model="causa" id="causa" />
        </div>
      </div>
    </template>
      <button class="btn btn-danger" type="submit" v-if="!revocar && causa && tipoExpulsion">Expulsar</button>
      <button class="btn btn-success" type="submit" v-if="revocar">Levantar sanción</button>
    </form>
  </b-modal>
</template>

<script>
import moment from 'moment'

export default {
  props : ['user_id', 'estado'],
  data() {
    return {
      causa: null,
      tipoExpulsion: null,
      tiempo_expulsion: null,
      hoy: null,
      otro: false,
      revocar: false,
      expulsado: null,
    }
  },
  methods: {
    gestionarExpulsion(){

      this.hoy = moment().format('YYYY-MM-DD')

      switch (this.tipoExpulsion) {
        case '1':
          this.hoy = moment(this.hoy).add(7,'days').format('YYYY-MM-DD')
          this.tiempo_expulsion = '7 días'
          break;
        case '2':
          this.hoy = moment(this.hoy).add(1,'months').format('YYYY-MM-DD')
          this.tiempo_expulsion = '1 mes'
          break;
        case '3':
          this.hoy = moment(this.hoy).add(3,'months').format('YYYY-MM-DD')
          this.tiempo_expulsion = '3 meses'
          break;
        case '4':
          this.hoy = moment(this.hoy).add(200,'years').format('YYYY-MM-DD')
          this.tiempo_expulsion = 'Permanente'
          break;
      }
      axios.post('http://localhost:8000/api/ban_user', {
          params: {
            user_id: this.user_id,
            fin_expulsion: this.hoy,
            causa: this.causa,
            revocar_expulsion: this.revocar,
            tipo_expulsion: this.tiempo_expulsion
          }
      }).then(response => {
        this.$bus.$emit('prueba')
        this.$refs["modal"].hide();
      })
    },
    cancelData(){
      this.causa= null
      this.tipoExpulsion= null
      this.hoy= null
      this.otro= false
      this.revocar= false
      this.expulsado= null
    },
    revocar_expulsar(){
      this.revocar = !this.revocar
    },
    checkOtro(){
      if(this.causa === 'Otro') {
        this.otro = true
      } else {
        this.otro = false
      }
    },

  },
}
</script>

<style scoped>

.btn {
  margin: 20px 0;
}

</style>