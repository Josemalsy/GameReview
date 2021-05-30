<template>

	<div class="pagina">
		<div class="cabecera"> Error 404 - Página no encontrada</div>
		<div class="icono"> <img class="imagen" src="/../storage/iconos/error-404.svg"></div>
	</div>

</template>


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
	background: #0090FF;
}

.icono {
	width: 80%;
	display: flex;
	justify-content: center;
	margin-top: 20px;
}

	.imagen {
		width: 40%;
		display: flex;
		justify-content: center;
		margin-top: 20px;
	}

@media (max-width: 700px){
	.imagen {
		width: 80%;
		display: flex;
		justify-content: center;
		margin-top: 200px;
	}
}

</style>