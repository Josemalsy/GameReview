<template>
  <b-modal hide-footer ref="modal" id="modal-expulsionModal" size="xl" title="Expulsar usuario" @shown="beforeOpen" @hidden="cancelData">

    <template v-if="validationErrors">
      <li v-for="(item, index) in validationErrors" :key="index" class="errorServ">{{item | borraCaracteresEspeciales }}</li>
    </template>

    <li v-if="checkStaff" class="errorServ">{{checkStaff}}</li>


    <template v-if="estado == 'Expulsado'">
      <p><button class="btn btn-success" @click="revocar_expulsar" v-if="!form.revocar">Revocar expulsión</button>
      <button class="btn btn-danger" @click="revocar_expulsar" v-else>Modo expulsar</button></p>
    </template>
    <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="gestionarExpulsion">

    <template v-if="!form.revocar">
      <label for="causa">Tipo de expulsion</label>
        <select id="tipoExpulsion" class="form-select mb-3" v-model="form.tipoExpulsion" @click="checkOtro">
          <option value="1">Expulsión de 7 días</option>
          <option value="2">Expulsión de 1 mes</option>
          <option value="3">Expulsión de 3 meses</option>
          <option value="4">Expulsión definitiva</option>
        </select>

      <div class="form-row">
        <label for="causa">Causa de expulsion</label>
        <select id="causa" class="form-select mb-3" v-model="form.causa" @click="checkOtro">
          <option value="Spoilers">Spoilers</option>
          <option value="Faltas de respeto grave">Faltas de respeto grave</option>
          <option value="Pésima ortografía reiterada">Pésima ortografía</option>
          <option value="Troll">Troll</option>
          <option value="Otro">Otro (Especifica debajo)</option>
        </select>
        <div class="form-group" style="width:100%;" v-if="otro">
          <input type="text" class="form-control " v-model="form.causa" id="causa" />
        </div>
      </div>
    </template>
      <button class="btn btn-danger" type="submit"  v-if="!form.revocar && form.causa && form.tipoExpulsion">Expulsar</button>
      <button class="btn btn-success" type="submit" v-if="form.revocar">Levantar sanción</button>
    </form>
  </b-modal>
</template>

<script>
import moment from 'moment'

export default {
  props : ['user_id', 'estado'],
  data() {
    return {
      form: {
        causa: null,
        tiempo_expulsion: null,
        hoy: null,
        revocar: false,
        user_id: null,
      },
      otro: false,
      expulsado: null,
      validationErrors: null,
      checkStaff: null,
    }
  },
  methods: {
    beforeOpen(){
      this.form.user_id = this.user_id
    },
    gestionarExpulsion(){

      this.form.hoy = moment().format('YYYY-MM-DD')

      switch (this.form.tipoExpulsion) {
        case '1':
          this.form.hoy = moment(this.form.hoy).add(7,'days').format('YYYY-MM-DD')
          this.form.tiempo_expulsion = '7 días'
          break;
        case '2':
          this.form.hoy = moment(this.form.hoy).add(1,'months').format('YYYY-MM-DD')
          this.form.tiempo_expulsion = '1 mes'
          break;
        case '3':
          this.form.hoy = moment(this.form.hoy).add(3,'months').format('YYYY-MM-DD')
          this.form.tiempo_expulsion = '3 meses'
          break;
        case '4':
          this.form.hoy = moment(this.form.hoy).add(200,'years').format('YYYY-MM-DD')
          this.form.tiempo_expulsion = 'Permanente'
          break;
      }

      axios.post('http://localhost:8000/api/ban_user', this.form)
          .then(response => {
          this.form.revocar ?  toastr.success('Has levantado la sanción al usuario con éxito') : toastr.success('has expulsado al usuario con éxito');
          this.$bus.$emit('prueba')
          this.$refs["modal"].hide();
        }).catch(error => {
          this.form.revocar ?  toastr.success('No has podido levantar la sanción al usuario') : toastr.success('No has podido expulsar al usuario');
            if(error.response.status == 420){
              this.checkStaff = error.response.data.message
            }else if (error.response.status == 422){
              this.validationErrors = error.response.data.errors;
            }
        });
    },
    cancelData(){
      this.form.causa= null
      this.form.tipoExpulsion= null
      this.form.hoy= null
      this.form.revocar= false
      this.otro= false
      this.expulsado= null
      this.validationErrors = null
      this.checkStaff = null
    },
    revocar_expulsar(){
      this.form.revocar = !this.form.revocar
    },
    checkOtro(){
      if(this.form.causa === 'Otro') {
        this.otro = true
      } else {
        this.otro = false
      }
    },
  },
    filters: {
    borraCaracteresEspeciales(value){
      for(let i = 0; i <= value.length;i++){
        return value[i]
      }
    }
  }
}
</script>

<style scoped>

.btn {
  margin: 20px 0;
}

.errorServ {
  background: #c82333;
  padding: 10px;
  list-style:none;
  color: white;
}

</style>