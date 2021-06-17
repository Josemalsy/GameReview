<template>
	<div class="pagina" v-if="current_user.fin_expulsion">
		<div class="cabecera"> Estás expulsado de la web hasta el {{current_user.fin_expulsion | formatDate}}. Causa de la expulsión: {{current_user.causa_expulsion}}</div>
		<div class="icono"> <img class="imagen" src="/../storage/iconos/ban.svg"></div>
	</div>
</template>

<script>
import moment from 'moment'

export default {
  props : ['current_user'],
  mounted() {
		//Si no está el usuario expulsado se le devuelve a la portada
    !this.current_user.fin_expulsion ? window.location.replace('/') : null
  },
  filters: {
    formatDate (value) {
      return moment(String(value)).format('DD/MM/YYYY')
    }
  }

}
</script>

<style scoped>

	.pagina{
		width: 100%;
		display: flex;
		flex-flow: column wrap;
		height: auto;
		align-items: center;
	}

	.cabecera {
		display: flex;
		height: auto;
		margin-top: 10px;
		flex-flow: column wrap;
		margin: 25px 0 0 0;
		width: 80%;
		padding: 10px;
		font-size: 25px;
		color: white;
		text-align: center;
		background: #FF8700;
	}

	.icono {
		width: 80%;
		display: flex;
		justify-content: center;
		margin-top: 20px;
	}

  .imagen{
    width: 40%;
  }

</style>