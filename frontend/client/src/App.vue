<template>
  <section>
<Navbar v-if="category" :category="category" :user="this.UserStore.user" />
<Carousel v-if="category && $route.meta.showLayout" :category="category" />
<router-view v-if="category" :category="this.ProductStore" :user="this.UserStore.user"/>
    <!-- <Footer/> -->
  </section>
</template>

<script>
    import 'bootstrap/dist/css/bootstrap.min.css';
    // import Footer from './components/Footer.vue';
    import Navbar from './components/Navbar.vue';
    import Carousel from './components/Carousel.vue';
    import { useUserStore } from './store/UserStore';
  import { useProductStore } from './store/ProductsStore';

    export default {
      data() {
        return {
          category:null,
        }
      },
      computed: {
        UserStore(){
          return useUserStore();
        },
        ProductStore(){
          return useProductStore();
        }

      },
      components:{
        Navbar,Carousel
      },
methods: {

},

    async mounted() {
      this.UserStore.authVerify();
      window.addEventListener('session-created', () =>{
        this.UserStore.authVerify();
      })
      this.ProductStore.loadCategory();
      try {
      const res = await fetch("/db.json");
      if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
  
      const data = await res.json();
      this.category = data.categories;
      console.log(this.category);
    } catch(err) {
    console.error("Veri alınamadı", err);
    }
  }

    }
    </script>

<style>
* {
  box-sizing: border-box;
}

#categoryCarousel{
  display: grid;
}
.hero-img {
  object-fit: cover;
  max-height: 700px;
  width: 100vw;
}
a {
  text-decoration: none;
  max-height: 100px;
  font-size: 18px;
}
i {
  font-size: 25px;
}

</style>
