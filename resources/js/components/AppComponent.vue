<template>
  <div>

    <router-view :user="user"></router-view>

  </div>

</template>

<script>
  export default {
    data(){
			return {
          isLoggedIn: false,
			}
    },
    props : ['user'],
    created () {
      console.log(this.user)
    },
	}


</script>

<style scoped>

  div{
    display:flex;
    background-color: white;
    height:100%;
  }

  @media (max-width: 600px){
    div {
      justify-content: center;
    }
  }

</style>