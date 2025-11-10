<template>
  <section class="container my-4">
    <h1 class="mb-4">{{ $route.params.slug }}</h1>
    <div v-if="ProductStore.status == 'success'" class="row g-4">
      <div 
        v-for="(product, index) in ProductStore.products" 
        :key="index" 
        class="col-lg-3 col-md-4 col-sm-6"
      >
        <div class="card product-card h-100 shadow-sm">
          <div class="product-img-wrapper">
            <img 
              :src="product.thumbnail" 
              class="card-img-top product-img" 
              :alt="product.title"
            >
          </div>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ product.title }}</h5>
            <p class="card-text text-muted mb-3">{{ product.price }} TL</p>
            <button class="btn btn-success mt-auto">Sepete Ekle</button>
            <router-link class="btn btn-primary flex-grow-2" :to="{name: 'ProductDetails', params:{slug: product.product_id}}">Hızlı Bakış</router-link>

          </div>
        </div>
      </div>
    </div>

    <div v-else>
      <p>Kategori veya ürünler bulunamadı.</p>
    </div>
  </section>
</template>

<script lang="ts">
import { useProductStore } from '../store/ProductsStore';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'CategoryView',
  prop:['category'],
  data() {
    return {
    }
  },
  computed:{
     ProductStore(){
      return useProductStore();
    }
  },
  mounted(){
    this.loadData();
    this.ProductStore.loadCategory();
  },
  watch:{
    "$route.params.slug"(){
      this.loadData();
    } 
  },
  methods:{
    async loadData(){
      try{
        const res = await fetch("/db.json");
        const data = await res.json();
        const slug = this.$route.params.slug;
        // this.category = data.categories.find((c:any) => c.slug === slug);
      } catch(err){
        console.error("Veri alınamadı", err);
      }
      
    }
  }
});
</script>

<style scoped>
.product-card {
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
}

.product-img-wrapper {
  height: 220px;
  overflow: hidden;
}

.product-img {
  object-fit: cover;
  width: 100%;
  height: 100%;
  transition: transform 0.5s ease;
}

.product-card:hover .product-img {
  transform: scale(1.1);
}

.card-title {
  font-size: 1.1rem;
  font-weight: 600;
}

.card-text {
  font-size: 0.95rem;
}
</style>
