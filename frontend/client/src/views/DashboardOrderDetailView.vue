<template>
  <div class="page-wrapper">
    <div class="container">
      
      <div v-if="loading" class="text-center py-5 loading-screen">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="text-muted mt-3 fw-medium">Sipariş verileri getiriliyor...</p>
      </div>
      <div v-else-if="localOrder && localOrder.order_id">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
          <div class="d-flex align-items-center gap-3">
            <button @click="$router.go(-1)" class="btn btn-light border rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width:45px; height:45px;">
              <i class="bi bi-arrow-left fs-5"></i>
            </button>
            <div>
              <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0">Sipariş #{{ localOrder.order_id }}</h4>
                <span :class="getStatusBadgeClass(localOrder.status)" class="badge rounded-pill">{{ localOrder.status }}</span>
              </div>
              <small class="text-muted"><i class="bi bi-calendar-event me-1"></i>{{ formatDate(localOrder.order_date) }}</small>
            </div>
          </div>
          
          <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-secondary rounded-pill px-4 btn-sm fw-bold">
              <i class="bi bi-printer me-2"></i>Yazdır
            </button>
            <button class="btn btn-outline-danger rounded-pill px-4 btn-sm fw-bold" @click="delete_order()">
              <i class="bi bi-x-circle me-2"></i>Siparişi Sil!
            </button>
          </div>
        </div>

        <div class="row g-4">
          
          <div class="col-lg-8">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                   <h6 class="fw-bold mb-0 text-primary">Sipariş Süreci</h6>
                   <select v-model="localOrder.status" class="form-select form-select-sm w-auto fw-bold cursor-pointer bg-light border-0">
                      <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
                   </select>
                </div>
                
                <div class="progress-container position-relative mt-5 mb-3 px-2">
                  <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" :style="{ width: getProgressWidth(localOrder.status) }"></div>
                  </div>
                  
                  <div class="d-flex justify-content-between position-absolute top-0 start-0 w-100 translate-middle-y">
                    <div v-for="(step, index) in steps" :key="index" 
                         class="step-item text-center" 
                         :class="{ 'active': isStepActive(step.value, localOrder.status) }">
                      <div class="step-circle shadow-sm bg-white mx-auto">
                        <i :class="step.icon"></i>
                      </div>
                      <span class="step-label d-none d-sm-block mt-2">{{ step.label }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
              <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Sepet İçeriği</h6>
                <span class="badge bg-light text-dark border">{{ localOrder.items ? localOrder.items.length : 0 }} Ürün</span>
              </div>
              <div class="card-body p-0">
                <div v-if="normalizedItems.length > 0">
                  <div v-for="(item, index) in normalizedItems" :key="index" class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center p-4 border-bottom last-no-border gap-3">
                    
                    <div class="img-wrapper rounded-3 border bg-light flex-shrink-0 position-relative overflow-hidden">
                      <img :src="item.thumbnail || 'https://via.placeholder.com/150'" class="w-100 h-100 object-fit-cover">
                    </div>

                    <div class="flex-grow-1 w-100">
                      <div class="form-floating mb-2">
                         <input type="text" class="form-control form-control-sm fw-bold border-0 shadow-none px-0 py-0 h-auto text-dark" 
                                v-model="item.title" placeholder="Ürün Adı">
                      </div>
                      <div class="d-flex align-items-center text-muted small">
                        <span>Birim:</span>
                        <input type="number" class="form-control form-control-sm border-0 d-inline-block w-auto p-0 ms-1 text-muted bg-transparent" 
                               v-model="item.price" style="max-width: 60px;"> ₺
                      </div>
                    </div>

                    <div class="text-sm-end w-100 w-sm-auto d-flex flex-row flex-sm-column justify-content-between align-items-center align-items-sm-end">
                      <div class="input-group input-group-sm w-auto mb-sm-1">
                         <span class="input-group-text bg-light border-0">Adet:</span>
                         <input type="number" class="form-control border-0 bg-light text-center" style="max-width: 50px;" v-model="item.qty">
                      </div>
                      <div class="fw-bold fs-5 text-dark">{{ (item.price * item.qty).toLocaleString('tr-TR') }} ₺</div>
                    </div>

                  </div>
                </div>
                
                <div v-else class="p-5 text-center text-muted">
                  <i class="bi bi-basket fs-1 mb-2 d-block"></i>
                  Siparişte ürün bulunamadı.
                </div>

              </div>
            </div>

          </div>

          <div class="col-lg-4">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
              <div class="card-body p-4">
                <h6 class="fw-bold mb-4">Ödeme Detayı</h6>
                <div class="d-flex justify-content-between mb-2 text-muted small">
                  <span>Ara Toplam</span>
                  <span>{{ calculateSubTotal }} ₺</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-muted small">
                  <span>KDV (%18)</span>
                  <span>{{ (calculateSubTotal * 0.18).toFixed(2) }} ₺</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-success small fw-bold">
                  <span>Kargo</span>
                  <span>Ücretsiz</span>
                </div>
                <hr class="border-dashed my-3">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="fw-bold text-dark">Toplam Tutar</span>
                  <span class="fs-3 fw-bold text-primary">{{ calculateTotal }} ₺</span>
                </div>
              </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
              <div class="card-body p-4">
                <h6 class="fw-bold mb-4">Teslimat & Müşteri</h6>
                
                <div class="mb-4">
                  <label class="small text-muted fw-bold mb-2"><i class="bi bi-geo-alt me-1"></i>TESLİMAT ADRESİ</label>
                  <textarea class="form-control bg-light border-0 rounded-3 small" rows="4" v-model="localOrder.order_address"></textarea>
                </div>

                <div>
                  <label class="small text-muted fw-bold mb-2"><i class="bi bi-person me-1"></i>MÜŞTERİ BİLGİLERİ</label>
                  <input type="text" class="form-control bg-light border-0 rounded-3 mb-2" v-model="localOrder.order_name" placeholder="Ad Soyad">
                  <input type="email" class="form-control bg-light border-0 rounded-3" v-model="localOrder.order_mail" placeholder="E-Posta">
                </div>

              </div>
            </div>

          </div>

        </div>
      </div>
    </div>

    <transition name="slide-up">
      <div v-if="isModified" class="fixed-bottom p-3 bg-white border-top shadow-lg z-3">
          <div class="container d-flex justify-content-between align-items-center">
            <div class="d-none d-md-block">
               <i class="bi bi-info-circle text-warning me-2"></i>
               <span class="text-muted small">Yaptığınız değişiklikler henüz kaydedilmedi.</span>
            </div>
            <div class="d-flex gap-2 ms-auto">
              <button @click="resetChanges" class="btn btn-light rounded-pill px-4 fw-medium">Vazgeç</button>
              <button @click="saveChanges" class="btn btn-dark rounded-pill px-4 fw-bold">
                  <i class="bi bi-check2-circle me-2"></i>Kaydet
              </button>
            </div>
          </div>
      </div>
    </transition>

  </div>
</template>

<script>
import { useOrdersStore } from '@/store/OrdersStore';

export default {
  name: 'OrderDetail',
  data() {
    return {
      loading: true,
      localOrder: {
        items: []
      },
      originalOrderStr: '',
      isModified: false,
      statusOptions: ['Sipariş Alındı', 'Hazırlanıyor', 'Kargoya Verildi', 'Teslim Edildi', 'İptal Edildi'],
      steps: [
        { label: 'Sipariş Alındı', value: 'Sipariş Alındı', icon: 'bi-receipt' },
        { label: 'Hazırlanıyor', value: 'Hazırlanıyor', icon: 'bi-box-seam' },
        { label: 'Kargoda', value: 'Kargoya Verildi', icon: 'bi-truck' },
        { label: 'Teslim Edildi', value: 'Teslim Edildi', icon: 'bi-house-check' }
      ]
    };
  },
  computed: {
    OrderStore() {
      return useOrdersStore();
    },
    normalizedItems() {
      if (!this.localOrder) return [];
      if (Array.isArray(this.localOrder.items) && this.localOrder.items.length > 0) {
        return this.localOrder.items;
      }
      if (this.localOrder.title) {
        return [this.localOrder];
      }
      return [];
    },
    calculateSubTotal() {
      return this.normalizedItems.reduce((sum, item) => sum + (Number(item.price) * Number(item.qty)), 0);
    },
    calculateTotal() {
        return this.calculateSubTotal;
    }
  },
  watch: {
    'OrderStore.selected_order': {
      handler(newVal) {
        if (newVal && Object.keys(newVal).length > 0) {
          this.localOrder = JSON.parse(JSON.stringify(newVal));
          
          if(!this.localOrder.items && this.localOrder.title) {
             this.localOrder.items = [{
                title: this.localOrder.title,
                price: this.localOrder.price,
                qty: this.localOrder.qty,
                thumbnail: this.localOrder.thumbnail,
                total_price: this.localOrder.total_price
             }];
          }

          this.originalOrderStr = JSON.stringify(this.localOrder);
          this.isModified = false;
          this.loading = false;
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
      if (!dateString) return '-';
      const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute:'2-digit' };
      return new Date(dateString).toLocaleDateString('tr-TR', options);
    },
    getStatusBadgeClass(status) {
        const s = status ? status.toLowerCase() : '';
        if (s.includes('teslim')) return 'bg-success';
        if (s.includes('iptal')) return 'bg-danger';
        if (s.includes('kargo')) return 'bg-warning text-dark';
        if (s.includes('hazır')) return 'bg-info text-dark';
        return 'bg-secondary';
    },
    isStepActive(stepValue, currentStatus) {
      const s = currentStatus ? currentStatus.toLowerCase() : '';
      const stepOrder = ['alındı', 'hazır', 'kargo', 'teslim'];
      
      let currentIndex = stepOrder.findIndex(key => s.includes(key));
      if(currentIndex === -1 && s.includes('onay')) currentIndex = 0;
      
      const targetIndex = stepOrder.findIndex(key => stepValue.toLowerCase().includes(key));
      
      return currentIndex >= targetIndex;
    },
    getProgressWidth(currentStatus) {
      const s = currentStatus ? currentStatus.toLowerCase() : '';
      if (s.includes('teslim')) return '100%';
      if (s.includes('kargo')) return '66%';
      if (s.includes('hazır')) return '33%';
      return '0%';
    },
    async saveChanges() {
        try {
            await this.OrderStore.update_order(this.localOrder);
            this.originalOrderStr = JSON.stringify(this.localOrder);
            this.isModified = false;
        } catch (error) {
            console.error(error);
        }
    },
    resetChanges() {
        this.localOrder = JSON.parse(this.originalOrderStr);
        this.isModified = false;
    },
    delete_order(){
      if(window.confirm("Bu siparişi silmek istediğinizden emin misiniz?")){
        this.OrderStore.delete_order(this.localOrder.order_id);
      }

    }
  },

  mounted() {
    this.loading = true;
    const orderId = this.$route.params.slug || this.$route.params.id;
    
    this.OrderStore.find_order(orderId);
    
    setTimeout(() => {
        this.loading = false;
    }, 500);
  }
}
</script>

<style scoped>
.page-wrapper {
  background-color: #f3f4f6;
  min-height: 100vh;
  padding-top: 100px;
  padding-bottom: 120px;
}

.step-circle {
  width: 36px;
  height: 36px;
  border: 2px solid #e5e7eb;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  transition: all 0.3s ease;
  z-index: 2;
  position: relative;
}

.step-item.active .step-circle {
  border-color: #198754;
  background-color: #198754 !important;
  color: #fff;
}

.step-item.active .step-label {
  color: #198754;
  font-weight: 600;
}

.step-label {
  font-size: 0.75rem;
  color: #6b7280;
}

.img-wrapper {
  width: 70px;
  height: 70px;
}

.form-control:focus, .form-select:focus {
  box-shadow: none;
  border-color: #000;
}

.last-no-border:last-child {
  border-bottom: none !important;
}

.border-dashed {
  border-style: dashed !important;
  border-color: #e5e7eb;
}

.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.3s ease-out;
}

.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
}

@media (max-width: 576px) {
    .page-wrapper {
        padding-top: 80px;
    }
}
</style>