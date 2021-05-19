<template>
  <div class="marcoExterior">
    <div class="cuadros" v-b-modal="'modal-editProfile'"> <i class="bi bi-tools"></i> <p>Editar perfil</p><modal-editProfile :current_user="current_user"/></div>
    <div class="cuadros" v-b-modal.modal-scrollable="'modal-RevisarReview'" v-if="current_user.rol == 'Administrador'"> <i class="bi bi-journal-text"></i> <p>Revisar review</p> <modal-RevisarReview :current_user="current_user"/></div>
    <div class="cuadros" v-b-modal="'modal-AddGame'"> <i class="bi bi-plus-square"></i> <p>Añadir juego</p> <modal-AddGame :tituloModal="'Añadir Juego'"/> </div>
  </div>
</template>

<script>


export default {
props : ['current_user'],
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

  p {
    font-size: 20px;
    color: black;
  }

  .enlaces, .enlaces:hover {
    text-decoration: none;
    color: black;
  }

</style>