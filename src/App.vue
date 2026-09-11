<template>
  <div id="app" class="d-flex flex-column min-vh-100">

<!--แสดงเมนู-->
<Navbar />

<!--แสดงหน้าเพจ-->
  <router-view/>
  
  </div>

</template>

<script>
import Navbar from './components/Navbar.vue';

export default {
  name: "App",
  components: {Navbar}
};

</script>