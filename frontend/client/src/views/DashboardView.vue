<template>
  <div class="page-wrapper p-4 bg-light min-vh-100">
    <div class="container-fluid">
      
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold mb-0">Hoş Geldin, Yönetici 👋</h3>
          <p class="text-muted small">İşte mağazanızın bugünkü durumu.</p>
        </div>
        <div class="d-flex gap-2">
           <router-link to="/dashboard/addProduct" class="btn btn-dark rounded-pill px-4 shadow-sm">
             <i class="bi bi-plus-lg me-2"></i>Yeni Ürün Ekle
           </router-link>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center">
              <div class="icon-box bg-primary-subtle text-primary rounded-4 me-3">
                <i class="bi bi-currency-dollar fs-4"></i>
              </div>
              <div>
                <small class="text-muted fw-bold d-block">TOPLAM CİRO</small>
                <h4 class="fw-bold mb-0">{{ stats.totalRevenue }} ₺</h4>
                <small class="text-success fw-bold"><i class="bi bi-arrow-up-short"></i> %12.5</small> <span class="text-muted small">geçen aydan</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center">
              <div class="icon-box bg-warning-subtle text-warning rounded-4 me-3">
                <i class="bi bi-bag-check fs-4"></i>
              </div>
              <div>
                <small class="text-muted fw-bold d-block">TOPLAM SİPARİŞ</small>
                <h4 class="fw-bold mb-0">{{ stats.totalOrders }}</h4>
                <small class="text-success fw-bold"><i class="bi bi-arrow-up-short"></i> 5 Yeni</small> <span class="text-muted small">bugün</span>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center">
              <div class="icon-box bg-info-subtle text-info rounded-4 me-3">
                <i class="bi bi-box-seam fs-4"></i>
              </div>
              <div>
                <small class="text-muted fw-bold d-block">AKTİF ÜRÜNLER</small>
                <h4 class="fw-bold mb-0">{{ stats.activeProducts }}</h4>
                <small class="text-muted small">Stokta tükenen: <span class="text-danger fw-bold">2</span></small>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center">
              <div class="icon-box bg-success-subtle text-success rounded-4 me-3">
                <i class="bi bi-people fs-4"></i>
              </div>
              <div>
                <small class="text-muted fw-bold d-block">TOPLAM MÜŞTERİ</small>
                <h4 class="fw-bold mb-0">{{ stats.totalCustomers }}</h4>
                <small class="text-success fw-bold"><i class="bi bi-arrow-up-short"></i> %5.2</small> <span class="text-muted small">büyüme</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
              <h6 class="fw-bold mb-0">Son Gelen Siparişler</h6>
              <router-link to="/dashboard/orders" class="btn btn-sm btn-light rounded-pill px-3">Tümünü Gör</router-link>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th class="ps-4">Sipariş No</th>
                    <th>Müşteri</th>
                    <th>Tarih</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                    <th class="text-end pe-4">İşlem</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="order in recentOrders" :key="order.id">
                    <td class="ps-4 fw-bold">#{{ order.order_code }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar-circle bg-light text-primary fw-bold me-2">
                          {{ order.customer_name.charAt(0) }}
                        </div>
                        <span class="fw-medium">{{ order.customer_name }}</span>
                      </div>
                    </td>
                    <td class="text-muted small">{{ order.date }}</td>
                    <td class="fw-bold">{{ order.total }} ₺</td>
                    <td>
                      <span class="badge rounded-pill px-3" :class="getStatusBadge(order.status).class">
                        {{ getStatusBadge(order.status).text }}
                      </span>
                    </td>
                    <td class="text-end pe-4">
                      <router-link 
                        :to="{ name: 'dashboardOrderDetail', params: { slug: order.order_code } }" 
                        class="btn btn-sm btn-light border rounded-circle"
                        title="Detay"
                      >
                        <i class="bi bi-chevron-right"></i>
                      </router-link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          
          <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-4">
              <h6 class="fw-bold mb-0">Çok Satan Ürünler</h6>
            </div>
            <div class="card-body p-0">
              <div v-for="(product, index) in topProducts" :key="index" class="d-flex align-items-center p-3 border-bottom last-no-border">
                <div class="position-relative">
                   <img :src="product.image" class="rounded-3 border" style="width: 60px; height: 60px; object-fit: cover;">
                   <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-dark border border-light">
                     {{ index + 1 }}
                   </span>
                </div>
                <div class="ms-3 flex-grow-1">
                  <h6 class="fw-bold mb-1 fs-6 text-truncate" style="max-width: 150px;">{{ product.name }}</h6>
                  <small class="text-muted">{{ product.sales }} Satış</small>
                </div>
                <div class="fw-bold text-success">{{ product.revenue }} ₺</div>
              </div>
            </div>
          </div>

          <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-4">
              <h6 class="fw-bold mb-0">Kategori Satışları</h6>
            </div>
            <div class="card-body p-4">
              <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <span class="small fw-bold">Mobilya</span>
                  <span class="small text-muted">%45</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-primary" role="progressbar" style="width: 45%"></div>
                </div>
              </div>
              <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <span class="small fw-bold">Aydınlatma</span>
                  <span class="small text-muted">%30</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-info" role="progressbar" style="width: 30%"></div>
                </div>
              </div>
              <div>
                <div class="d-flex justify-content-between mb-1">
                  <span class="small fw-bold">Dekorasyon</span>
                  <span class="small text-muted">%25</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-warning" role="progressbar" style="width: 25%"></div>
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

export default {
  name: "DashboardView",
  data() {
    return {
      stats: {
        totalRevenue: "125.430",
        totalOrders: 142,
        activeProducts: 48,
        totalCustomers: 854
      },
      recentOrders: [
        { id: 1, order_code: 1043, customer_name: "Ahmet Yılmaz", date: "Bugün, 14:30", total: 4500, status: "Bekliyor" },
        { id: 2, order_code: 1042, customer_name: "Ayşe Demir", date: "Bugün, 11:15", total: 1250, status: "Hazırlanıyor" },
        { id: 3, order_code: 1041, customer_name: "Mehmet Kaya", date: "Dün, 16:45", total: 8900, status: "Kargoda" },
        { id: 4, order_code: 1040, customer_name: "Zeynep Çelik", date: "Dün, 09:20", total: 3200, status: "Teslim Edildi" },
        { id: 5, order_code: 1039, customer_name: "Caner Erkin", date: "17.01.2025", total: 550, status: "İptal" },
      ],
      topProducts: [
        { name: "Chester Koltuk Takımı", sales: 24, revenue: "120.000", image: "https://images.unsplash.com/photo-1555041469-a586c61ea9bc?q=80&w=2070&auto=format&fit=crop" },
        { name: "Minimalist Ahşap Masa", sales: 18, revenue: "45.000", image: "https://images.unsplash.com/photo-1577140917170-285929fb55b7?q=80&w=2070&auto=format&fit=crop" },
        { name: "Rustik Lambader", sales: 45, revenue: "12.500", image: "https://images.unsplash.com/photo-1507473885765-e6ed057f782c?q=80&w=2070&auto=format&fit=crop" }
      ]
    };
  },
  methods: {
    getStatusBadge(status) {
      const s = status.toLowerCase();
      if (s === 'bekliyor') return { class: 'bg-warning-subtle text-warning border border-warning-subtle', text: 'Bekliyor' };
      if (s === 'hazırlanıyor') return { class: 'bg-primary-subtle text-primary border border-primary-subtle', text: 'Hazırlanıyor' };
      if (s === 'kargoda') return { class: 'bg-info-subtle text-info border border-info-subtle', text: 'Kargoda' };
      if (s === 'teslim edildi') return { class: 'bg-success-subtle text-success border border-success-subtle', text: 'Tamamlandı' };
      if (s === 'iptal') return { class: 'bg-danger-subtle text-danger border border-danger-subtle', text: 'İptal' };
      return { class: 'bg-secondary', text: status };
    }
  },
};
</script>

<style scoped>
.icon-box {
  width: 56px;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-circle {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
}

.last-no-border:last-child {
  border-bottom: none !important;
}

tbody tr {
  transition: background-color 0.2s;
}
tbody tr:hover {
  background-color: #f8f9fa;
}
</style>