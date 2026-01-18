<template>
  <div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="fw-bold text-dark mb-1">Sipariş Yönetimi</h3>
        <p class="text-muted small mb-0">Mağazanızdaki tüm siparişleri buradan yönetebilirsiniz.</p>
      </div>
      <button class="btn btn-primary rounded-pill px-4" @click="loadOrders">
        <i class="bi bi-arrow-clockwise me-2"></i>Listeyi Yenile
      </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div class="card-body p-4">
        
        <div class="row g-3 mb-4 align-items-center">
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3">
                <i class="bi bi-search text-muted"></i>
              </span>
              <input 
                v-model="search" 
                type="text" 
                class="form-control bg-light border-start-0 rounded-end-pill py-2" 
                placeholder="Sipariş ID, Müşteri veya Adres ara..." 
              />
            </div>
          </div>
          <div class="col-md-3 ms-auto">
            <select v-model="statusFilter" class="form-select rounded-pill bg-light border-0 py-2 cursor-pointer">
              <option value="">Tüm Durumlar</option>
              <option value="pending">Beklemede</option>
              <option value="shipped">Kargolandı</option>
              <option value="finished">Tamamlandı</option>
              <option value="canceled">İptal Edildi</option>
            </select>
          </div>
        </div>

        <div v-if="isLoading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="text-muted mt-2">Siparişler yükleniyor...</p>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr>
                <th class="ps-4 text-muted small text-uppercase fw-bold" style="width: 100px;">Sipariş ID</th>
                <th class="text-muted small text-uppercase fw-bold">Müşteri</th>
                <th class="text-muted small text-uppercase fw-bold">Tutar</th>
                <th class="text-muted small text-uppercase fw-bold">Durum</th>
                <th class="text-muted small text-uppercase fw-bold">Tarih</th>
                <th class="text-end pe-4 text-muted small text-uppercase fw-bold">İşlemler</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="o in filteredOrders" :key="o.order_id" class="cursor-pointer">
                
                <td class="ps-4">
                  <span class="fw-bold text-dark">#{{ o.order_id }}</span>
                </td>
                
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar-circle me-3 bg-primary-subtle text-primary fw-bold">
                      {{ getInitials(o.name) }}
                    </div>
                    <div>
                      <h6 class="mb-0 text-dark fw-semibold" style="font-size: 0.95rem;">{{ o.name }}</h6>
                      <small class="text-muted d-block text-truncate" style="max-width: 150px;">{{ o.order_address }}</small>
                    </div>
                  </div>
                </td>

                <td>
                  <span class="fw-bold text-dark">{{ o.price }} ₺</span>
                </td>

                <td>
                  <span class="badge rounded-pill px-3 py-2 status-badge" :class="getStatusClass(o.status)">
                    <i class="bi me-1" :class="getStatusIcon(o.status)"></i>
                    {{ getStatusLabel(o.status) }}
                  </span>
                </td>

                <td>
                  <span class="text-muted small">{{ formatDate(o.created_at) }}</span>
                </td>

                <td class="text-end pe-4">
                  <div class="d-flex gap-2 justify-content-end">
                    <button class="btn btn-light btn-sm rounded-circle action-btn text-primary" @click="viewDetails(o)" title="Detay">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-light btn-sm rounded-circle action-btn text-danger" @click="deleteOrder(o.order_id)" title="Sil">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="!isLoading && filteredOrders.length === 0" class="text-center py-5">
           <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
              <i class="bi bi-inbox fs-1 text-muted"></i>
           </div>
           <h5 class="fw-bold text-dark">Sipariş Bulunamadı</h5>
           <p class="text-muted">Arama kriterlerinize uygun bir kayıt yok.</p>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "DashboardOrdersView",
  data() {
    return {
      orders: [],
      search: "",
      statusFilter: "",
      isLoading: false
    };
  },
  computed: {
    filteredOrders() {
      return this.orders.filter(o => {
        const searchTerm = this.search.toLowerCase();
        const matchSearch = 
          o.order_id.toString().includes(searchTerm) ||
          (o.name && o.name.toLowerCase().includes(searchTerm)) ||
          (o.order_address && o.order_address.toLowerCase().includes(searchTerm));
          
        const matchStatus = this.statusFilter ? o.status === this.statusFilter : true;
        
        return matchSearch && matchStatus;
      });
    }
  },
  methods: {
    async loadOrders() {
      this.isLoading = true;
      try {
        const res = await axios.get("http://localhost:8080/api/get_all_orders");
        this.orders = res.data.orders || []; 
      } catch (err) {
        console.error("Siparişler yüklenemedi:", err);
      } finally {
        this.isLoading = false;
      }
    },
    deleteOrder(id) {
      if (confirm(`#${id} numaralı siparişi silmek istediğinize emin misiniz?`)) {
        axios
          .post("http://localhost:8080/api/delete_order", { id })
          .then(() => {
            this.orders = this.orders.filter(o => o.order_id !== id);
          })
          .catch(err => console.error("Silme hatası:", err));
      }
    },
    viewDetails(o) {
      alert(`Sipariş #${o.order_id} detayları açılıyor...`);
    },
    formatDate(date) {
      if(!date) return '-';
      return new Date(date).toLocaleDateString("tr-TR", {
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute:'2-digit'
      });
    },
    getInitials(name) {
      if (!name) return "?";
      const parts = name.split(" ");
      if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
      return name.slice(0, 2).toUpperCase();
    },
    getStatusClass(status) {
      switch (status) {
        case 'pending': return 'bg-warning-subtle text-warning-emphasis';
        case 'shipped': return 'bg-info-subtle text-info-emphasis';
        case 'finished': return 'bg-success-subtle text-success-emphasis';
        case 'canceled': return 'bg-danger-subtle text-danger-emphasis';
        default: return 'bg-secondary-subtle text-secondary';
      }
    },
    getStatusLabel(status) {
      const map = {
        'pending': 'Beklemede',
        'shipped': 'Kargolandı',
        'finished': 'Tamamlandı',
        'canceled': 'İptal Edildi'
      };
      return map[status] || status;
    },
    getStatusIcon(status) {
       switch (status) {
        case 'pending': return 'bi-hourglass-split';
        case 'shipped': return 'bi-truck';
        case 'finished': return 'bi-check-circle-fill';
        case 'canceled': return 'bi-x-circle-fill';
        default: return 'bi-circle';
      }
    }
  },
  mounted() {
    this.loadOrders();
  }
};
</script>

<style scoped>
.table-hover tbody tr:hover {
  background-color: #f8f9fa;
  transform: translateY(-1px);
  box-shadow: 0 2px 5px rgba(0,0,0,0.02);
  transition: all 0.2s ease;
}

.avatar-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  flex-shrink: 0;
}

.status-badge {
  font-weight: 600;
  font-size: 0.75rem;
  letter-spacing: 0.3px;
}

.action-btn {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.action-btn:hover {
  background-color: #e9ecef;
  transform: scale(1.1);
}

.cursor-pointer {
  cursor: pointer;
}

.form-control:focus, .form-select:focus {
  box-shadow: none;
  border-color: #dee2e6;
  background-color: #fff;
}
</style>