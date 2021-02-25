<template>

	<div class="contenedor">

		<div class="tarjeta" v-for="item in list">
			<div class="img">
			</div>
		</div>

	</div>

</template>

<script>
export default {
	data() {
		return {
			list: [],
			page: 0,
			}
		},
		methods: {
			obtenerDatos(){
				this.page++;
				let url = 'http://localhost:8000/api/juegos';
				axios.get(url).then(response =>{
					let juegos = response.data.data;
						if(juegos.length){
							this.list = this.list.concat(juegos);
						}
				});
			}
		},
		mounted(){
			this.obtenerDatos();
	}
}
</script>


<style scoped>

.contenedor{
	display: flex;
	flex-flow: row wrap;
	border: 1px solid black;
	height: 100%;
}

.tarjeta {
	border: 1px solid green;
	width: 30%;
	margin: 10px;
  height: 10%;
	display: flex;
	flex-flow: row wrap;
	justify-content: flex-start;
}

.img {
	border: 1px solid red;
	height: 50%;
	width: 50px;
}
</style>