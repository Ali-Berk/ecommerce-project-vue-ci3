<template>
  <div class="profile-page-wrapper">
    <div class="container pb-5">
      <div class="row justify-content-center">
        
        <div class="col-lg-10">
          
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-3 mb-md-0">Siparişlerim</h3>
            
            <div class="d-flex gap-2">
              <button 
                class="btn rounded-pill px-4 btn-sm fw-medium transition-all"
                :class="activeFilter === 'all' ? 'btn-dark' : 'btn-white border text-muted'"
                @click="activeFilter = 'all'"
              >
                Tümü
              </button>
              
              <button 
                class="btn rounded-pill px-4 btn-sm fw-medium transition-all"
                :class="activeFilter === 'ongoing' ? 'btn-dark' : 'btn-white border text-muted'"
                @click="activeFilter = 'ongoing'"
              >
                Devam Eden
              </button>
              
              <button 
                class="btn rounded-pill px-4 btn-sm fw-medium transition-all"
                :class="activeFilter === 'finished' ? 'btn-dark' : 'btn-white border text-muted'"
                @click="activeFilter = 'finished'"
              >
                Tamamlanan
              </button>
            </div>
          </div>

          <div v-if="isLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted mt-2">Siparişleriniz getiriliyor...</p>
          </div>

          <div v-else-if="OrdersStore.status_data === 'error'" class="alert alert-danger rounded-4 shadow-sm border-0 d-flex align-items-center gap-3">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
            <div><strong>Bir hata oluştu.</strong> Sipariş verileri yüklenemedi.</div>
          </div>

          <div v-else-if="filteredOrders.length === 0" class="text-center py-5 bg-white rounded-4 shadow-sm border">
             <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                <i class="bi bi-filter-circle fs-1 text-muted"></i>
             </div>
             <h4 class="fw-bold">Bu kategoride sipariş bulunamadı.</h4>
             <p class="text-muted mb-4" v-if="activeFilter !== 'all'">Diğer filtreleri deneyebilir veya tüm siparişlerinize göz atabilirsiniz.</p>
             <p class="text-muted mb-4" v-else>Henüz hiç sipariş vermediniz.</p>
             
             <button v-if="activeFilter !== 'all'" @click="activeFilter = 'all'" class="btn btn-outline-dark rounded-pill px-4">
                Tümünü Göster
             </button>
             <router-link v-else to="/" class="btn btn-primary rounded-pill px-5 py-2 fw-bold">Alışverişe Başla</router-link>
          </div>

          <div v-else class="d-flex flex-column gap-4">
            
            <div 
              v-for="order in filteredOrders" 
              :key="order.order_id" 
              class="order-card bg-white rounded-4 shadow-sm overflow-hidden border"
            >
              <div class="card-header-custom p-4 border-bottom bg-light-subtle d-flex flex-wrap gap-3 justify-content-between align-items-center">
                <div class="d-flex gap-4 align-items-center">
                  <div class="d-flex align-items-center gap-2">
                    <div class="icon-box bg-white border rounded-circle text-primary">
                      <i class="bi bi-calendar3"></i>
                    </div>
                    <div>
                      <small class="text-muted d-block fw-bold" style="font-size: 0.7rem;">SİPARİŞ TARİHİ</small>
                      <span class="fw-medium text-dark">{{ order.order_date }}</span>
                    </div>
                  </div>
                  <div class="d-none d-sm-block border-start ps-4">
                    <small class="text-muted d-block fw-bold" style="font-size: 0.7rem;">SİPARİŞ NO</small>
                    <span class="fw-medium text-dark font-monospace">#{{ order.order_id }}</span>
                  </div>
                </div>
                <div>
                   <span class="badge rounded-pill py-2 px-3 fw-bold status-badge" :class="getStatusClass(order.status)">
                      {{ order.status }}
                   </span>
                </div>
              </div>

              <div class="p-4">
                <div class="row align-items-center g-4">
                  <div class="col-md-8">
                    <div class="d-flex gap-3 align-items-start">
                      <div class="product-icon-placeholder bg-light rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 text-muted">
                        <i class="bi bi-box-seam fs-3"></i>
                      </div>
                      <div>
                        <h6 class="fw-bold text-dark mb-1">Teslimat Adresi: <span class="fw-normal text-muted">{{ order.order_address }}</span></h6>
                        <p class="text-muted mb-0 small"><i class="bi bi-basket me-1"></i> İçerik: {{ order.product_titles }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 text-md-end">
                    <small class="text-muted fw-bold">TOPLAM TUTAR</small>
                    <h4 class="fw-bold text-primary mb-3">{{ order.total_price }} ₺</h4>
                    <router-link :to="'orders/'+order.order_id" class="btn btn-outline-dark rounded-pill w-100 stretched-link-custom">
                      Sipariş Detayı <i class="bi bi-arrow-right ms-2"></i>
                    </router-link>
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

<script>
import { useOrdersStore } from '@/store/OrdersStore';

export default {
  data() {
    return {
      isLoading: true,
      activeFilter: 'all'
    };
  },
  computed: {
    OrdersStore() {
      return useOrdersStore();
    },
    filteredOrders() {
      const orders = this.OrdersStore.order_data || [];

      if (this.activeFilter === 'all') {
        return orders;
      }
      
      if (this.activeFilter === 'finished') {
        return orders.filter(order => 
          order.status.toLowerCase().includes('tamam') || 
          order.status.toLowerCase().includes('teslim')
        );
      }
      
      if (this.activeFilter === 'ongoing') {
        return orders.filter(order => 
          !order.status.toLowerCase().includes('tamam') && 
          !order.status.toLowerCase().includes('teslim') &&
          !order.status.toLowerCase().includes('iptal')
        );
      }

      return orders;
    }
  },
  methods: {
    getStatusClass(status) {
      const s = status ? status.toLowerCase() : '';
      if (s.includes('tamam') || s.includes('teslim')) return 'bg-success-subtle text-success';
      if (s.includes('kargo') || s.includes('yol')) return 'bg-info-subtle text-info';
      if (s.includes('iptal')) return 'bg-danger-subtle text-danger';
      return 'bg-warning-subtle text-warning';
    }
  },
  async mounted() {
    await this.OrdersStore.get_orders();
    this.isLoading = false;
  },
};
</script>

<style scoped>
.profile-page-wrapper {
  background-color: #f8f9fa;
  min-height: 100vh;
  padding-top: 120px;
}

.transition-all {
  transition: all 0.3s ease;
}

.order-card {
  transition: transform 0.2s, box-shadow 0.2s;
  border-color: #eef1f6 !important;
}

.order-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
}

.icon-box {
  width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
}

.product-icon-placeholder {
  width: 60px; height: 60px;
}

.status-badge {
  font-size: 0.8rem; letter-spacing: 0.5px;
}

@media (max-width: 768px) {
  .order-card .card-header-custom {
    flex-direction: column; align-items: flex-start;
  }
  .order-card .col-md-4 {
    text-align: left !important; border-top: 1px dashed #dee2e6; padding-top: 1rem; margin-top: 0.5rem;
  }
}
</style>