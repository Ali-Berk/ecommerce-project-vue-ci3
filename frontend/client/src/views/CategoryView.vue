<template>
  <section class="container my-4">
    <h1 class="mb-4">{{ category_name }}</h1>
    <div v-if="ProductStore.status == 'success'" class="row g-4">
      <div 
        v-for="(product, index) in Selectedproducts" 
        :key="index"
        class="col-lg-3 col-md-4 col-sm-6"
      >
        <div class="card product-card h-100 shadow-sm" :class="{'out-of-stock' :product.stock < 1}">
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
            <button class="btn btn-success mt-auto" @click="UserStore.addToCart(product)">Sepete Ekle</button>
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
import { useUserStore } from '../store/UserStore';
import { useProductStore } from '../store/ProductsStore';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'CategoryView',
  prop:['category'],
  data() {
    return {
      Selectedproducts:[{product_id:1,title:"err",category:"err",thumbnail:"https://developers.elementor.com/docs/assets/img/elementor-placeholder-image.png",price:1, active:1, stock:0}],
      category_name: '' as string | undefined,
      slug:'err',
    }
  },
  computed:{
    ProductStore(){
      return useProductStore();
    },
    UserStore(){
      return useUserStore();
    }
  },
  
watch: {
  'slug': {
    handler(newVal) {
      const category = this.ProductStore.categories.find(c => c.categorySlug == newVal);
      this.category_name = category?.category_name;
      this.Selectedproducts = this.ProductStore.products.filter(c => (c.category == category?.category_name));
      this.Selectedproducts = this.Selectedproducts.filter(p => p.active == 1);
    }
  },
  '$route.params.slug': {
    handler(newVal){
      this.slug = newVal;
    }
  }
},


  async mounted(){
    await this.ProductStore.loadCategory();
    const category = this.ProductStore.categories.find(c => c.categorySlug == this.$route.params.slug);
    this.category_name = category?.category_name;
    this.Selectedproducts = this.ProductStore.products.filter(c => (c.category == category?.category_name));
    this.slug = this.$route.params.slug as string;
  },
  methods:{
    
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

.out-of-stock::after{
content: 'STOKTA YOK';
    position: absolute;
    top: 80px; 
    right: -25px; 
    
    background-color: #dc3545; 
    color: white;
    font-size: 0.85em;
    font-weight: bold;
    padding: 5px 30px;
    
    transform: rotate(45deg);
    transform-origin: 100% 0;
    
    z-index: 10;
    
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}
</style>
