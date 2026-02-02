<template>
  <div class="page-wrapper">
    <div class="container pb-5">
      
      <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="fw-bold mb-0">Alışveriş Sepetim <span class="text-muted fs-5 fw-normal">({{ UserStore.cart.length }} Ürün)</span></h3>
      </div>

      <div v-if="UserStore.cart.length === 0" class="text-center py-5 bg-white rounded-4 shadow-sm">
        <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
          <i class="bi bi-cart3 fs-1 text-muted opacity-50"></i>
        </div>
        <h4 class="fw-bold">Sepetinizde ürün yok.</h4>
        <p class="text-muted mb-4">Fırsatları kaçırmamak için hemen alışverişe başlayın.</p>
        <router-link to="/" class="btn btn-primary rounded-pill px-5 py-2 fw-bold">
          Alışverişe Başla
        </router-link>
      </div>

      <div v-else class="row g-4">
        
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
              
              <div class="d-none d-md-flex bg-light p-3 px-4 border-bottom text-muted small fw-bold">
                <div class="flex-grow-1">ÜRÜN</div>
                <div style="width: 120px;" class="text-center">MİKTAR</div>
                <div style="width: 100px;" class="text-end">FİYAT</div>
                <div style="width: 50px;"></div> </div>

              <div v-for="item in UserStore.cart" :key="item.product_id" class="cart-item-row p-3 px-4 border-bottom d-flex flex-wrap flex-md-nowrap align-items-center gap-3">
                
                <div class="img-wrapper rounded-3 border bg-white flex-shrink-0 position-relative">
                   <router-link :to="`/product/${item.product_id}`"> <img :src="item.thumbnail" :alt="item.title" class="img-fluid object-fit-contain w-100 h-100">
                   </router-link>
                </div>

                <div class="flex-grow-1" style="min-width: 200px;">
                  <h6 class="fw-bold mb-1 text-dark">
                    <router-link :to="`/product/${item.product_id}`" class="text-decoration-none text-dark">{{ item.title }}</router-link>
                  </h6>
                  <small class="text-muted">Birim Fiyat: {{ item.price }} ₺</small>
                </div>

                <div class="qty-control d-flex align-items-center justify-content-center">
                  <div class="input-group input-group-sm border rounded-3" style="width: 100px;">
                    <button class="btn btn-white border-0 px-2" type="button" @click="decreaseQty(item)">
                      <i class="bi bi-dash"></i>
                    </button>
                    <input type="text" class="form-control text-center border-0 bg-white p-0 fw-bold" :value="item.qty" readonly>
                    <button class="btn btn-white border-0 px-2" type="button" @click="UserStore.addToCart(item)">
                      <i class="bi bi-plus"></i>
                    </button>
                  </div>
                </div>

                <div class="price-col text-end fw-bold fs-5 text-dark">
                  {{ (item.price * item.qty).toFixed(2) }} ₺
                </div>

                <div class="remove-col text-end">
                  <button class="btn btn-link text-muted p-0 hover-danger" @click="UserStore.removeFromCart(item.product_id)" title="Sepetten Sil">
                    <i class="bi bi-trash3 fs-5"></i>
                  </button>
                </div>

              </div>

            </div>
          </div>
          
          <div class="mt-3">
            <router-link to="/" class="text-decoration-none text-muted fw-medium small">
              <i class="bi bi-arrow-left me-1"></i> Alışverişe Devam Et
            </router-link>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="sticky-summary">
            <div class="card border-0 shadow-sm rounded-4">
              <div class="card-header bg-white border-bottom p-4">
                <h5 class="fw-bold mb-0">Sipariş Özeti</h5>
              </div>
              
              <div class="card-body p-4">
                <div class="input-group mb-4">
                  <input type="text" class="form-control bg-light border-0" placeholder="İndirim Kodu">
                  <button class="btn btn-dark" type="button">Uygula</button>
                </div>

                <div class="d-flex justify-content-between mb-2 text-muted">
                  <span>Ara Toplam</span>
                  <span>{{ totalPrice }} ₺</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-muted">
                  <span>Kargo</span>
                  <span class="text-success">Ücretsiz</span>
                </div>
                <div class="d-flex justify-content-between mb-3 text-muted">
                  <span>KDV (%18)</span>
                  <span>Dahil</span>
                </div>
                
                <hr class="border-dashed my-3">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                  <span class="fw-bold fs-5">Genel Toplam</span>
                  <span class="fw-bold fs-3 text-primary">{{ totalPrice }} ₺</span>
                </div>

                <router-link to="/checkout" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-lg checkout-btn">
                  Sepeti Onayla <i class="bi bi-arrow-right ms-2"></i>
                </router-link>
                
                <div class="text-center mt-3">
                   <div class="d-flex justify-content-center gap-2 text-muted fs-4 opacity-50">
                      <i class="bi bi-credit-card"></i>
                      <i class="bi bi-paypal"></i>
                      <i class="bi bi-wallet2"></i>
                   </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue';
import { useUserStore } from '../store/UserStore';

export default defineComponent({
  name: 'CartView',
  setup() {
    const UserStore = useUserStore();
    const increaseQty = (item: any) => {
      item.qty += 1;
    };

    const decreaseQty = (item: any) => {
      item.qty = Math.max(1, item.qty - 1);
    };

    const totalPrice = computed(() =>
      UserStore.cart.reduce((acc, item) => acc + item.price * item.qty, 0).toFixed(2)
    );

    return { UserStore, increaseQty, decreaseQty, totalPrice };
  }
});
</script>

<style scoped>
.page-wrapper {
  background-color: #f8f9fa;
  min-height: 100vh;
  padding-top: 120px; 
}

.img-wrapper {
  width: 80px;
  height: 80px;
  padding: 5px;
  transition: transform 0.2s;
}
.img-wrapper:hover {
  transform: scale(1.05);
}

.qty-control .btn:hover {
  background-color: #f8f9fa;
  color: #000;
}
.qty-control .form-control:focus {
  box-shadow: none;
}

@media (min-width: 768px) {
  .qty-control { width: 120px; }
  .price-col { width: 100px; }
  .remove-col { width: 50px; }
}

.hover-danger {
  transition: color 0.2s, transform 0.2s;
}
.hover-danger:hover {
  color: #dc3545 !important;
  transform: scale(1.1);
}

.sticky-summary {
  position: sticky;
  top: 130px;
}

.border-dashed {
  border-style: dashed !important;
}

.checkout-btn {
  transition: transform 0.2s;
}
.checkout-btn:hover {
  transform: translateY(-2px);
  background-color: #000;
}

@media (max-width: 767px) {
  .cart-item-row {
    flex-wrap: wrap;
    text-align: center;
    justify-content: center;
  }
  .flex-grow-1 {
    text-align: center;
    width: 100%;
  }
  .qty-control, .price-col {
    margin: 5px 0;
  }
  .remove-col {
    width: 100%;
    text-align: center !important;
    margin-top: 10px;
  }
}
</style>