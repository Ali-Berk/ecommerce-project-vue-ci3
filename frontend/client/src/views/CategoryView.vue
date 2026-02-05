<template>
  <section class="container my-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
      <h1 class="category-title mb-0">{{ category_name }}</h1>
      <span class="text-muted small" v-if="Selectedproducts.length > 0">{{ Selectedproducts.length }} ürün listelendi</span>
    </div>

    <div class="card border-0 shadow-sm mb-5 p-3 bg-white rounded-4">
      <div class="row g-3 align-items-center">
        <div class="col-lg-4 col-md-6">
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 border-light"><i class="bi bi-search text-muted"></i></span>
            <input type="text" class="form-control bg-light border-start-0 border-light" placeholder="Ürün ara...">
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="d-flex gap-2">
            <input type="number" class="form-control bg-light border-light" placeholder="Min TL">
            <input type="number" class="form-control bg-light border-light" placeholder="Max TL">
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <select class="form-select bg-light border-light">
            <option selected>Sıralama: Önerilen</option>
            <option value="1">Fiyat: Artan</option>
            <option value="2">Fiyat: Azalan</option>
            <option value="3">En Yeniler</option>
          </select>
        </div>

        <div class="col-lg-2 col-md-6">
          <div class="form-check form-switch d-flex justify-content-md-end justify-content-start gap-2 ps-0">
            <input class="form-check-input ms-0" type="checkbox" role="switch" id="stockSwitch">
            <label class="form-check-label small pt-1 text-muted" for="stockSwitch">Sadece Stoktakiler</label>
          </div>
        </div>
      </div>
    </div>

    <div v-if="ProductStore.status == 'success'" class="row g-4 mb-5">
      <div 
        v-for="(product, index) in Selectedproducts" 
        :key="index"
        class="col-lg-3 col-md-4 col-sm-6"
      >
        <div class="card product-card h-100 border-0" :class="{'out-of-stock-card' :product.stock < 1}">
          <div v-if="product.stock < 1" class="stock-badge">Tükendi</div>
          <div class="product-img-wrapper position-relative">
            <img :src="product.thumbnail" class="card-img-top product-img" :alt="product.title">
            <div class="card-overlay d-flex justify-content-center align-items-center gap-2">
              <router-link class="btn btn-light btn-sm rounded-pill px-3 shadow-sm" :to="{name: 'ProductDetails', params:{slug: product.product_id}}">
                <i class="bi bi-eye"></i> İncele
              </router-link>
            </div>
          </div>
          <div class="card-body d-flex flex-column p-4">
            <h5 class="card-title text-truncate" :title="product.title">{{ product.title }}</h5>
            <div class="mt-auto">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="price-tag">{{ product.price }} ₺</span>
              </div>
              <button class="btn btn-dark w-100 rounded-3 py-2 add-btn" @click="UserStore.addToCart(product)" :disabled="product.stock < 1">
                {{ product.stock < 1 ? 'Stokta Yok' : 'Sepete Ekle' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="text-center py-5">
      <p class="text-muted fs-5">Kategori veya ürünler bulunamadı.</p>
    </div>

    <hr class="my-5 border-secondary opacity-10">

    <section class="recommendations-section">
      <h3 class="mb-4 fw-bold text-dark">Bunlara da Göz Atın</h3>
      <div class="row g-4">
        <div 
          v-for="(product, index) in recommendedProducts" 
          :key="'rec-'+index"
          class="col-lg-3 col-md-4 col-sm-6"
        >
          <div class="card product-card h-100 border-0" :class="{'out-of-stock-card' :product.stock < 1}">
            <div v-if="product.stock < 1" class="stock-badge">Tükendi</div>
            <div class="product-img-wrapper position-relative">
              <img :src="product.thumbnail" class="card-img-top product-img" :alt="product.title">
              <div class="card-overlay d-flex justify-content-center align-items-center gap-2">
                <router-link class="btn btn-light btn-sm rounded-pill px-3 shadow-sm" :to="{name: 'ProductDetails', params:{slug: product.product_id}}">
                  <i class="bi bi-eye"></i> İncele
                </router-link>
              </div>
            </div>
            <div class="card-body d-flex flex-column p-4">
              <h5 class="card-title text-truncate" :title="product.title">{{ product.title }}</h5>
              <div class="mt-auto">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <span class="price-tag">{{ product.price }} ₺</span>
                </div>
                <button class="btn btn-dark w-100 rounded-3 py-2 add-btn" @click="UserStore.addToCart(product)" :disabled="product.stock < 1">
                  {{ product.stock < 1 ? 'Stokta Yok' : 'Sepete Ekle' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

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
      Selectedproducts: [] as any[], 
      recommendedProducts: [] as any[],
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
        this.updatePageData(newVal);
      }
    },
    '$route.params.slug': {
      handler(newVal){
        this.slug = newVal as string;
      }
    }
  },

  async mounted(){
    await this.ProductStore.loadCategory();
    this.slug = this.$route.params.slug as string;
    this.updatePageData(this.slug);
  },

  methods:{
    updatePageData(slugVal: string) {
      const category = this.ProductStore.categories.find(c => c.categorySlug == slugVal);
      this.category_name = category?.category_name;

      this.Selectedproducts = this.ProductStore.products.filter(c => (c.category == category?.category_name));
      this.Selectedproducts = this.Selectedproducts.filter(p => p.active == 1);

      this.generateRecommendations();
    },

    generateRecommendations() {
      let allProducts = [...this.ProductStore.products];
      
      allProducts = allProducts.filter(p => p.active == 1);

      for (let i = allProducts.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [allProducts[i], allProducts[j]] = [allProducts[j], allProducts[i]];
      }

      this.recommendedProducts = allProducts.slice(0, 4);
    }
  }
});
</script>

<style scoped>
.category-title {
  font-weight: 700;
  color: #333;
  letter-spacing: -0.5px;
}

.product-card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 10px 20px rgba(0,0,0,0.03);
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  overflow: hidden; 
  position: relative;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.08);
}

.product-img-wrapper {
  height: 260px; 
  overflow: hidden;
  position: relative;
}

.product-img {
  width: 100%;
  height: 100%;
  object-fit: cover; 
  transition: transform 0.5s ease;
}

.product-card:hover .product-img {
  transform: scale(1.05);
}

.card-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.1); 
  opacity: 0;
  transition: opacity 0.3s ease;
}

.product-card:hover .card-overlay {
  opacity: 1;
}

.card-title {
  font-size: 1rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.price-tag {
  font-size: 1.25rem;
  font-weight: 700;
  color: #2c3e50;
}

.stock-badge {
  position: absolute;
  top: 15px;
  right: 15px;
  background-color: #ff4757;
  color: white;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  z-index: 10;
  box-shadow: 0 4px 10px rgba(255, 71, 87, 0.3);
}

.out-of-stock-card {
  opacity: 0.8; 
}

.out-of-stock-card .product-img {
  filter: grayscale(100%);
}

.add-btn {
  font-weight: 500;
  font-size: 0.9rem;
  letter-spacing: 0.3px;
  transition: background-color 0.2s;
}

.add-btn:hover {
  background-color: #000;
}

.add-btn:disabled {
  background-color: #e9ecef;
  border-color: #e9ecef;
  color: #6c757d;
  cursor: not-allowed;
}

.recommendations-section {
  padding-top: 2rem;
}

.form-control:focus, .form-select:focus {
  border-color: #e2e8f0;
  box-shadow: none;
  background-color: #fff !important;
}

.form-check-input:checked {
  background-color: #333;
  border-color: #333;
}
</style>