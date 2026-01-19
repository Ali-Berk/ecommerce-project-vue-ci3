<template>
  <div class="row g-4">
    <div 
      v-for="item in product" 
      :key="item.product_id" 
      class="col-lg-4 col-md-6 col-sm-6"
    >
      <div class="card h-100 border-0 shadow-sm product-card">
        
        <div class="img-wrapper position-relative overflow-hidden">
          <img 
            :src="item.thumbnail || 'https://via.placeholder.com/300'" 
            class="card-img-top w-100 h-100 object-fit-cover" 
            :alt="item.title"
          >
          <div v-if="(item.stock || 0) < 1" class="position-absolute top-0 end-0 m-2">
            <span class="badge bg-danger">Tükendi</span>
          </div>
          <div class="card-overlay d-flex align-items-center justify-content-center">
            <router-link 
              :to="`/product/${item.product_id}`" 
              class="btn btn-light rounded-pill px-4 fw-bold shadow-sm"
            >
              <i class="bi bi-eye me-1"></i> İncele
            </router-link>
          </div>
        </div>

        <div class="card-body d-flex flex-column p-3">
          <h6 class="card-title text-truncate fw-bold mb-1" :title="item.title">
            {{ item.title }}
          </h6>
          <small class="text-muted mb-2">{{ item.category }}</small>

          <div class="mt-auto">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="fs-5 fw-bold text-dark">{{ item.price }} ₺</span>
            </div>
            <button 
              class="btn btn-dark w-100 rounded-3" 
              @click="UserStore.addToCart(item)"
              :disabled="(item.stock || 0) < 1"
            >
              {{ (item.stock || 0) < 1 ? 'Stok Yok' : 'Sepete Ekle' }}
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { useUserStore } from '../store/UserStore';
import { useProductStore } from '../store/ProductsStore';

export default defineComponent({
  name: 'ProductCard',
  props: {
    product: {
      type: Object,
      required: true,
      default: () => ({})
    }
  },
  setup() {
    const UserStore = useUserStore();
    return { UserStore };
  },
  computed:{
    ProductStore(){
      return useProductStore();
    },
  }
});
</script>

<style scoped>
.product-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  background: #fff;
  border-radius: 12px;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.img-wrapper {
  height: 220px;
  background-color: #f8f9fa;
}

.card-overlay {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.2);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.product-card:hover .card-overlay {
  opacity: 1;
}

.btn-dark {
  transition: background-color 0.2s;
}
.btn-dark:hover {
  background-color: #000;
}
</style>