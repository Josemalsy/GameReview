<template>
  <div class="contenedor">
    <div class="cabecera">Mensajes privados {{form.user_id}}</div>

      <div class="menu">

        <li class="menuBoton" v-b-modal="'modal-enviarMensaje'"> Nuevo mensaje <modal-enviarMensaje :current_user="current_user"/></li>
        <li class="menuBoton" @click="getMensajesRecibidos">Recibidos</li>
        <li class="menuBoton" @click="getMensajesEnviadosLeidos">Enviados leidos</li>
        <li class="menuBoton" @click="getMensajesEnviadosNoLeidos">Enviados sin leer</li>

      </div>
    </div>
  </div>

</template>