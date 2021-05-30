<template>
  <div class="marcoExterior" v-if="current_user.rol !='Usuario'">
    <div class="cuadros" v-b-modal.modal-scrollable="'modal-RevisarReview'"> <i class="bi bi-journal-text"></i> <p>Revisar review</p> <modal-RevisarReview :current_user="current_user"/></div>
    <div class="cuadros" v-if="current_user.rol == 'Administrador'" v-b-modal="'modal-AddGame'"> <i class="bi bi-plus-square"></i> <p>Añadir juego</p> <modal-AddGame :tituloModal="'Añadir Juego'"/> </div>
    <div class="cuadros"> <a href="/staff_tools/listaGeneros"> <i class="bi bi-puzzle"></i> <p>Géneros de juegos</p></a>  </div>
    <div class="cuadros"> <a href="/staff_tools/listaPlataformas"> <i class="bi bi-joystick"></i> <p>Plataformas de juegos</p></a> </div>
    <div class="cuadros"> <a href="/staff_tools/listaUsuarios"> <i class="bi bi-people"></i> <p>Lista de usuarios</p></a>  </div>
  </div>
</template>

<script>


export default {
  props : ['current_user'],
  mounted() {
    if(this.current_user == false) {
      this.$router.push('/forbidden');
    }
  },

}
</script>

<style>
  .marcoExterior {
    width: 100%;
    display: flex;
    flex-flow: row wrap;
    align-items: center;
    justify-content: center;
  }

  .cuadros {
    font-size: 80px;
    margin: 10px 20px;
    text-align: center;
  }

  .cuadros a{
    text-decoration: none;
    color:black;
  }


  .cuadros a:hover{
    text-decoration: none;
    color:black;
  }

  p {
    font-size: 20px;
    color: black;
  }

  .enlaces, .enlaces:hover {
    text-decoration: none;
    color: black;
  }

</style>