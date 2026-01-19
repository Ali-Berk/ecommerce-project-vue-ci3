<template>
  <div class="page-wrapper">
    <div class="container pb-5">
      
      <div v-if="!localOrder.order_id" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="text-muted mt-2">Sipariş detayları yükleniyor...</p>
      </div>

      <div v-else>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
          <div class="d-flex align-items-center gap-3">
            <button @click="$router.go(-1)" class="btn btn-white border rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
              <i class="bi bi-arrow-left"></i>
            </button>
            <div>
              <h4 class="fw-bold mb-0">Sipariş #{{ localOrder.order_id }}</h4>
              <small class="text-muted">{{ formatDate(localOrder.order_date) }}</small>
            </div>
          </div>
          
          <div class="d-flex gap-2">
            <button class="btn btn-outline-dark rounded-pill px-4 btn-sm">
              <i class="bi bi-upload me-2"></i>Fatura Paylaş
            </button>
            <button class="btn btn-danger rounded-pill px-4 btn-sm fw-bold">
              <i class="bi bi-x me-2"></i>İptal et
            </button>
          </div>
        </div>

        <div class="row g-4">
          
          <div class="col-lg-8">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                   <h6 class="fw-bold mb-0">Sipariş Durumu</h6>
                   <select v-model="localOrder.status" class="form-select form-select-sm w-auto fw-bold text-primary border-primary bg-primary-subtle">
                      <option value="Sipariş Alındı">Sipariş Alındı</option>
                      <option value="Hazırlanıyor">Hazırlanıyor</option>
                      <option value="Kargoya Verildi">Kargoya Verildi</option>
                      <option value="Teslim Edildi">Teslim Edildi</option>
                   </select>
                </div>
                
                <div class="position-relative d-flex justify-content-between align-items-center text-center px-2 mt-5">
                  <div class="position-absolute top-50 start-0 w-100 translate-middle-y bg-light" style="height: 4px; z-index: 0;"></div>
                  <div class="position-absolute top-50 start-0 w-100 translate-middle-y" style="height: 4px; z-index: 0;">
                    <div class="bg-success h-100 transition-width" :style="{ width: getProgressWidth(localOrder.status) }"></div>
                  </div>

                  <div class="step-item position-relative z-1" :class="{ 'active': isStepActive('onay', localOrder.status) }">
                    <div class="step-circle shadow-sm"><i class="bi bi-check-lg"></i></div>
                    <span class="step-label">Sipariş Alındı</span>
                  </div>
                  <div class="step-item position-relative z-1" :class="{ 'active': isStepActive('hazır', localOrder.status) }">
                    <div class="step-circle shadow-sm"><i class="bi bi-box-seam"></i></div>
                    <span class="step-label">Hazırlanıyor</span>
                  </div>
                  <div class="step-item position-relative z-1" :class="{ 'active': isStepActive('kargo', localOrder.status) }">
                    <div class="step-circle shadow-sm"><i class="bi bi-truck"></i></div>
                    <span class="step-label">Kargoda</span>
                  </div>
                  <div class="step-item position-relative z-1" :class="{ 'active': isStepActive('teslim', localOrder.status) }">
                    <div class="step-circle shadow-sm"><i class="bi bi-house-check"></i></div>
                    <span class="step-label">Teslim Edildi</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
              <div class="card-header bg-white border-bottom p-4">
                <h6 class="fw-bold mb-0">Sipariş İçeriği</h6>
              </div>
              <div class="card-body p-0">
                <div class="d-flex align-items-center p-4 border-bottom last-no-border item-row">
                  <div class="img-wrapper rounded-3 border bg-light flex-shrink-0">
                    <img :src="localOrder.thumbnail" alt="Ürün" class="img-fluid object-fit-contain w-100 h-100">
                  </div>
                  <div class="ms-3 flex-grow-1">
                    <input type="text" class="form-control fw-bold mb-1 border-0 p-0 h-auto fs-6" v-model="localOrder.title">
                    <div class="text-muted small mb-2 d-flex align-items-center">
                       Birim Fiyat: <input type="number" class="form-control form-control-sm border-0 d-inline-block w-auto p-0 ms-1 text-muted" v-model="localOrder.price"> ₺
                    </div>
                  </div>
                  <div class="text-end">
                    <div class="text-muted small mb-1 d-flex justify-content-end align-items-center">
                       <input type="number" class="form-control form-control-sm border-0 w-auto p-0 text-end text-muted me-1" v-model="localOrder.qty"> Adet
                    </div>
                    <div class="fw-bold fs-5">{{ localOrder.total_price }} ₺</div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <div class="col-lg-4">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
              <div class="card-body p-4">
                <h6 class="fw-bold mb-4">Ödeme Özeti</h6>
                <div class="d-flex justify-content-between mb-2 text-muted">
                  <span>Ara Toplam</span>
                  <span>{{ localOrder.total_price }} ₺</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-muted">
                  <span>Kargo</span>
                  <span class="text-success">Ücretsiz</span>
                </div>
                <hr class="border-dashed my-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="fw-bold text-dark">Genel Toplam</span>
                  <span class="fs-4 fw-bold text-primary">{{ localOrder.total_price }} ₺</span>
                </div>
              </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
              <div class="card-body p-4">
                <h6 class="fw-bold mb-4">Teslimat Bilgileri</h6>
                
                <div class="d-flex align-items-start gap-3 mb-4">
                  <div class="icon-box bg-light text-primary rounded-circle flex-shrink-0 mt-1">
                    <i class="bi bi-geo-alt"></i>
                  </div>
                  <div class="w-100">
                    <small class="text-muted fw-bold d-block mb-1">TESLİMAT ADRESİ</small>
                    <textarea class="form-control bg-light border-0 small lh-sm" rows="3" v-model="localOrder.order_address"></textarea>
                  </div>
                </div>

                <div class="d-flex align-items-start gap-3">
                  <div class="icon-box bg-light text-primary rounded-circle flex-shrink-0 mt-1">
                    <i class="bi bi-person"></i>
                  </div>
                  <div class="w-100">
                    <small class="text-muted fw-bold d-block mb-1">MÜŞTERİ</small>
                    <input type="text" class="form-control bg-light border-0 small mb-1 p-1 ps-2" v-model="localOrder.order_name" placeholder="Müşteri Adı">
                    <input type="email" class="form-control bg-light border-0 small p-1 ps-2" v-model="localOrder.order_mail" placeholder="E-posta">
                  </div>
                </div>

              </div>
            </div>

          </div>

        </div>
      </div>
    </div>

    <div v-if="isModified" class="fixed-bottom p-3 bg-white border-top shadow-lg d-flex justify-content-end align-items-center gap-3" style="z-index: 1050;">
        <span class="text-muted small d-none d-md-block">Değişiklikler tespit edildi. Kaydetmeyi unutmayın.</span>
        <button @click="resetChanges" class="btn btn-light rounded-pill px-4">Vazgeç</button>
        <button @click="saveChanges" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">
            <i class="bi bi-check-lg me-2"></i>Değişiklikleri Kaydet
        </button>
    </div>

  </div>
</template>

<script>
import { useOrdersStore } from '@/store/OrdersStore';

export default {
  data() {
    return {
      localOrder: {},
      originalOrderStr: '',
      isModified: false
    };
  },
  computed: {
    OrderStore() {
      return useOrdersStore();
    }
  },
  watch: {
    'OrderStore.selected_order': {
      handler(newVal) {
        if (newVal && Object.keys(newVal).length > 0) {
          this.localOrder = JSON.parse(JSON.stringify(newVal));
          this.originalOrderStr = JSON.stringify(this.localOrder);
          this.isModified = false;
        }
      },
      immediate: true,
      deep: true
    },
    localOrder: {
      handler(newVal) {
        const currentStr = JSON.stringify(newVal);
        this.isModified = currentStr !== this.originalOrderStr;
      },
      deep: true
    }
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return '';
      return dateString; 
    },
    isStepActive(step, currentStatus) {
      const s = currentStatus ? currentStatus.toLowerCase() : '';
      const steps = ['onay', 'hazır', 'kargo', 'teslim'];
      
      let currentIndex = -1;
      if (s.includes('alındı') || s.includes('onay')) currentIndex = 0;
      else if (s.includes('hazır')) currentIndex = 1;
      else if (s.includes('kargo') || s.includes('yol')) currentIndex = 2;
      else if (s.includes('teslim') || s.includes('tamam')) currentIndex = 3;

      const stepIndex = steps.indexOf(step);
      
      return currentIndex >= stepIndex;
    },
    getProgressWidth(currentStatus) {
      const s = currentStatus ? currentStatus.toLowerCase() : '';
      if (s.includes('teslim') || s.includes('tamam')) return '100%';
      if (s.includes('kargo') || s.includes('yol')) return '66%';
      if (s.includes('hazır')) return '33%';
      return '0%';
    },

    saveChanges() {
        console.log("Kaydedilecek Veri:", this.localOrder);
        this.originalOrderStr = JSON.stringify(this.localOrder);
        this.isModified = false;
        alert('Değişiklikler başarıyla kaydedildi!');
    },
    resetChanges() {
        this.localOrder = JSON.parse(this.originalOrderStr);
        this.isModified = false;
    }
  },
  mounted() {
    const orderId = this.$route.params.slug;
    if (this.OrderStore.order_data.length === 0) {
       this.OrderStore.get_orders().then(() => {
          this.OrderStore.find_order(orderId);
       });
    } else {
       this.OrderStore.find_order(orderId);
    }
  }
}
</script>

<style scoped>
.page-wrapper {
  background-color: #f8f9fa;
  min-height: 100vh;
  padding-top: 120px;
  padding-bottom: 80px; 
}

.step-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 80px;
}

.step-circle {
  width: 40px;
  height: 40px;
  background-color: #fff;
  border: 2px solid #dee2e6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #adb5bd;
  margin-bottom: 8px;
  transition: all 0.3s ease;
  font-size: 1.1rem;
}

.step-label {
  font-size: 0.75rem;
  color: #adb5bd;
  font-weight: 600;
  transition: color 0.3s ease;
}

.step-item.active .step-circle {
  background-color: #0d6efd; 
  border-color: #0d6efd;
  color: #fff;
}

.step-item.active .step-label {
  color: #0d6efd;
}

.transition-width {
  transition: width 0.5s ease;
}

.img-wrapper {
  width: 80px;
  height: 80px;
  padding: 5px;
}

.icon-box {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.border-dashed {
  border-style: dashed !important;
}

.last-no-border:last-child {
  border-bottom: none !important;
}

.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    background-color: #fff !important;
}

.fixed-bottom {
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}

@media (max-width: 576px) {
  .step-label {
    display: none; 
  }
}
</style>