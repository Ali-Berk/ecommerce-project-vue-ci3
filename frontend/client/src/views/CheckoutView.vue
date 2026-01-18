<template>
  <div class="page-wrapper">
    <div class="container pb-5">
      
      <div class="d-flex align-items-center gap-3 mb-4">
        <button @click="$router.go(-1)" class="btn btn-white border rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
          <i class="bi bi-arrow-left"></i>
        </button>
        <h3 class="fw-bold mb-0">Ödeme ve Teslimat</h3>
      </div>

      <div v-if="UserStore.cart.length === 0" class="text-center py-5 bg-white rounded-4 shadow-sm">
        <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
          <i class="bi bi-cart-x fs-1 text-muted"></i>
        </div>
        <h4 class="fw-bold">Sepetiniz Boş</h4>
        <p class="text-muted mb-4">Siparişi tamamlamak için önce sepetinize ürün eklemelisiniz.</p>
        <router-link to="/" class="btn btn-primary rounded-pill px-5 py-2 fw-bold">
          Alışverişe Başla
        </router-link>
      </div>

      <div v-else class="row g-4">
        
        <div class="col-lg-7">
          <form @submit.prevent="submitOrder" id="checkoutForm">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
              <div class="card-header bg-white border-bottom p-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt me-2 text-primary"></i>Teslimat Bilgileri</h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-medium small text-muted">Ad Soyad</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                      <input type="text" class="form-control bg-light border-start-0" v-model="customer.name" placeholder="Adınız Soyadınız" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-medium small text-muted">E-posta Adresi</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                      <input type="email" class="form-control bg-light border-start-0" v-model="customer.mail" placeholder="ornek@mail.com" required>
                    </div>
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-medium small text-muted">Teslimat Adresi</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0"><i class="bi bi-pin-map"></i></span>
                      <textarea class="form-control bg-light border-start-0" v-model="customer.address" rows="3" placeholder="Mahalle, Sokak, No, İlçe/İl..." required></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
              <div class="card-header bg-white border-bottom p-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-credit-card me-2 text-primary"></i>Ödeme Yöntemi</h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-3">
                  <div class="col-md-6">
                    <div 
                      class="payment-option border rounded-3 p-3 cursor-pointer h-100 position-relative"
                      :class="{'active-payment': paymentMethod === 'credit_card'}"
                      @click="paymentMethod = 'credit_card'"
                    >
                      <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold">Kredi / Banka Kartı</span>
                        <i class="bi bi-check-circle-fill fs-5" :class="paymentMethod === 'credit_card' ? 'text-primary' : 'text-muted opacity-25'"></i>
                      </div>
                      <small class="text-muted d-block lh-sm">Mastercard, Visa, Troy ile güvenli ödeme.</small>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div 
                      class="payment-option border rounded-3 p-3 cursor-pointer h-100 position-relative"
                      :class="{'active-payment': paymentMethod === 'transfer'}"
                      @click="paymentMethod = 'transfer'"
                    >
                      <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold">Havale / EFT</span>
                        <i class="bi bi-check-circle-fill fs-5" :class="paymentMethod === 'transfer' ? 'text-primary' : 'text-muted opacity-25'"></i>
                      </div>
                      <small class="text-muted d-block lh-sm">%5 indirimli havale ile ödeme yapın.</small>
                    </div>
                  </div>
                </div>

                <div v-if="paymentMethod === 'credit_card'" class="mt-4 p-3 bg-light rounded-3">
                   <div class="row g-3">
                      <div class="col-12">
                        <input type="text" class="form-control" placeholder="Kart Numarası (Görsel)">
                      </div>
                      <div class="col-6">
                        <input type="text" class="form-control" placeholder="AA/YY">
                      </div>
                      <div class="col-6">
                         <input type="text" class="form-control" placeholder="CVC">
                      </div>
                   </div>
                </div>

              </div>
            </div>

          </form>
        </div>

        <div class="col-lg-5">
          <div class="sticky-summary">
            <div class="card border-0 shadow-sm rounded-4">
              <div class="card-header bg-white border-bottom p-4">
                <h5 class="fw-bold mb-0">Sipariş Özeti <span class="text-muted fw-normal fs-6">({{ UserStore.cart.length }} Ürün)</span></h5>
              </div>
              
              <div class="card-body p-0">
                <div class="cart-items-scroll p-4" style="max-height: 300px; overflow-y: auto;">
                  <div v-for="item in UserStore.cart" :key="item.id" class="d-flex align-items-center mb-3">
                    <div class="img-wrapper rounded-3 border bg-white flex-shrink-0">
                      <img :src="item.thumbnail || 'https://via.placeholder.com/60'" alt="Ürün" class="img-fluid object-fit-contain w-100 h-100">
                    </div>
                    <div class="ms-3 flex-grow-1">
                      <h6 class="fw-bold mb-0 text-truncate" style="max-width: 180px;">{{ item.title }}</h6>
                      <small class="text-muted">{{ item.qty }} Adet x {{ item.price }} ₺</small>
                    </div>
                    <div class="fw-bold">{{ item.qty * item.price }} ₺</div>
                  </div>
                </div>
              </div>

              <div class="card-footer bg-light p-4 border-top">
                <div class="d-flex justify-content-between mb-2 text-muted">
                  <span>Ara Toplam</span>
                  <span>{{ totalPrice }} ₺</span>
                </div>
                <div class="d-flex justify-content-between mb-3 text-muted">
                  <span>Kargo</span>
                  <span class="text-success">Ücretsiz</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-4">
                  <span class="fw-bold fs-5">Genel Toplam</span>
                  <span class="fw-bold fs-4 text-primary">{{ totalPrice }} ₺</span>
                </div>

                <button 
                  type="submit" 
                  form="checkoutForm"
                  class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-lg checkout-btn"
                  :disabled="loading"
                >
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                  {{ loading ? 'İşleniyor...' : 'Siparişi Onayla ve Öde' }}
                </button>
                
                <div class="text-center mt-3">
                   <small class="text-muted d-block"><i class="bi bi-lock-fill me-1"></i>256-bit SSL ile güvenli ödeme</small>
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
import { defineComponent, reactive, computed, ref } from 'vue';
import { useUserStore } from '../store/UserStore';
import axios from 'axios';
import { useRouter } from 'vue-router';

export default defineComponent({
  name: 'CheckoutView',
  setup() {
    const UserStore = useUserStore();
    const router = useRouter();
    const loading = ref(false);
    const paymentMethod = ref('credit_card');

    const customer = reactive({
        name: UserStore.user?.name || '',
        mail: UserStore.user?.mail || '',
        address: UserStore.user?.address || ''
    });

    const totalPrice = computed(() =>
      UserStore.cart.reduce((acc, item) => acc + item.price * item.qty, 0)
    );

    const submitOrder = async () => {
      loading.value = true;
      try {
        const orderData = {
          customer,
          items: UserStore.cart,
          total: totalPrice.value,
          payment_method: paymentMethod.value
        };
        
        await axios.post('http://localhost:8080/api/createOrder', orderData, { withCredentials: true });
        
        alert('Siparişiniz başarıyla alındı! Teşekkür ederiz.');
        UserStore.cart = [];
        router.push('/orders');
        
      } catch (error) {
        console.error(error);
        alert('Sipariş oluşturulurken bir hata oluştu. Lütfen bilgilerinizi kontrol edin.');
      } finally {
        loading.value = false;
      }
    };

    return { 
      UserStore, 
      customer, 
      totalPrice, 
      submitOrder, 
      paymentMethod,
      loading 
    };
  }
});
</script>

<style scoped>
.page-wrapper {
  background-color: #f8f9fa;
  min-height: 100vh;
  padding-top: 120px;
}

.input-group-text {
  color: #6c757d;
}
.form-control:focus {
  box-shadow: none;
  border-color: #dee2e6;
  background-color: #fff;
}
.form-control::placeholder {
  color: #adb5bd;
  font-size: 0.9rem;
}

.payment-option {
  transition: all 0.2s ease;
  border-width: 2px !important;
  background-color: #fff;
}
.payment-option:hover {
  border-color: #dee2e6;
  background-color: #f8f9fa;
}
.active-payment {
  border-color: #0d6efd !important;
  background-color: #f0f7ff !important;
}

.sticky-summary {
  position: sticky;
  top: 130px;
}

.img-wrapper {
  width: 60px;
  height: 60px;
  padding: 5px;
}

.checkout-btn {
  transition: transform 0.2s;
}
.checkout-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  background-color: #000;
}
.checkout-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.cursor-pointer {
  cursor: pointer;
}
</style>