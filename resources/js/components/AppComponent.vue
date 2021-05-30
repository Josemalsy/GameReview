<template>

  <div class="center-page">

    <router-view :current_user="current_user"></router-view>

  </div>

</template>

<script>
  export default {
    props : ['current_user'],
    created () {
    },
    watch: {
      '$route' (to, from) {
        document.title = to.meta.title || 'Your Website'
      }
    },
	}


</script>

<style scoped>

  .center-page{
    display:flex;
    background-color: white;
    height:100%;
    align-items: center;
    justify-content: center;
  }

  @media (max-width: 600px){
    .center-page {
      justify-content: center;
    }
  }

</style>