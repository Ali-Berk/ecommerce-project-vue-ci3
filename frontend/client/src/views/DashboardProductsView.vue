<template>
  <div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="fw-bold text-dark mb-1">Ürün Yönetimi</h3>
        <p class="text-muted small mb-0">Envanterinizdeki ürünleri buradan takip edip düzenleyebilirsiniz.</p>
      </div>
      <router-link to="/dashboard/addProduct" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
        <i class="bi bi-plus-lg me-2"></i>Yeni Ürün Ekle
      </router-link>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div class="card-body p-4">
        
        <div class="row g-3 mb-4">
          <div class="col-md-5">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3">
                <i class="bi bi-search text-muted"></i>
              </span>
              <input 
                v-model="search" 
                type="text" 
                class="form-control bg-light border-start-0 rounded-end-pill py-2" 
                placeholder="Ürün adı, ID veya kategori ara..." 
              />
            </div>
          </div>
          
          <div class="col-md-3">
            <select v-model="selectedCategory" class="form-select rounded-pill bg-light border-0 py-2 cursor-pointer">
              <option value="">Tüm Kategoriler</option>
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>

          <div class="col-md-4">
            <select v-model="stockFilter" class="form-select rounded-pill bg-light border-0 py-2 cursor-pointer">
              <option value="">Tüm Durumlar</option>
              <option value="in">Stokta Var</option>
              <option value="out">Tükendi (Stok Yok)</option>
              <option value="yes">Yayında (Aktif)</option>
              <option value="no">Pasif (Yayında Değil)</option>
            </select>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr>
                <th class="ps-4 text-muted small text-uppercase fw-bold">Ürün Bilgisi</th>
                <th class="text-muted small text-uppercase fw-bold">Kategori</th>
                <th class="text-muted small text-uppercase fw-bold">Fiyat</th>
                <th class="text-muted small text-uppercase fw-bold">Stok / Durum</th>
                <th class="text-end pe-4 text-muted small text-uppercase fw-bold">İşlemler</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="p in filteredProducts" :key="p.product_id">
                
                <td class="ps-4 py-3">
                  <div class="d-flex align-items-center">
                    <div class="img-wrapper rounded-3 border bg-white flex-shrink-0 me-3">
                      <img :src="p.thumbnail" alt="Ürün" class="product-img">
                    </div>
                    <div>
                      <h6 class="mb-0 text-dark fw-bold text-truncate" style="max-width: 250px;">{{ p.title }}</h6>
                      <small class="text-muted">ID: #{{ p.product_id }}</small>
                    </div>
                  </div>
                </td>
                
                <td>
                  <span class="badge bg-light text-dark border fw-normal px-2 py-1">
                    {{ p.category }}
                  </span>
                </td>

                <td>
                  <span class="fw-bold text-dark">{{ p.price }} ₺</span>
                </td>

                <td>
                  <div class="d-flex flex-column align-items-start gap-1">
                    <span class="small fw-bold" :class="p.stock > 0 ? 'text-dark' : 'text-danger'">
                      {{ p.stock }} Adet
                    </span>
                    
                    <span class="badge rounded-pill px-2 py-1 status-badge" :class="getStatusClass(p)">
                       <i class="bi me-1" :class="getStatusIcon(p)"></i>
                       {{ getStatusLabel(p) }}
                    </span>
                  </div>
                </td>

                <td class="text-end pe-4">
                  <div class="d-flex gap-2 justify-content-end">
                    <router-link :to="`/dashboard/products/${p.product_id}`" class="btn btn-light btn-sm rounded-circle action-btn text-primary" title="Düzenle">
                      <i class="bi bi-pencil-square"></i>
                    </router-link>
                    
                    <button class="btn btn-light btn-sm rounded-circle action-btn text-danger" @click="deleteProduct(p.product_id)" title="Sil">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="filteredProducts.length === 0" class="text-center py-5">
           <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
              <i class="bi bi-box-seam fs-1 text-muted"></i>
           </div>
           <h5 class="fw-bold text-dark">Ürün Bulunamadı</h5>
           <p class="text-muted">Arama kriterlerinize uygun ürün yok veya listeniz boş.</p>
           <button class="btn btn-outline-primary rounded-pill btn-sm px-4" @click="resetFilters">Filtreleri Temizle</button>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, ref, computed } from "vue";
import { useProductStore } from "../store/ProductsStore";
import { storeToRefs } from "pinia";
import axios from "axios";

export default {
  name: "DashboardProductsView",

  setup() {
    const productStore = useProductStore();
    const { products } = storeToRefs(productStore); 
    const { loadCategory } = productStore;

    const search = ref("");
    const selectedCategory = ref("");
    const stockFilter = ref("");

    onMounted(() => {
      loadCategory();
    });

    const categories = computed(() => {
      const set = new Set(products.value.map((p) => p.category));
      return [...set];
    });

    const filteredProducts = computed(() =>
      products.value.filter((p) => {
        const searchTerm = search.value.toLowerCase();
        const matchTitle = p.title.toLowerCase().includes(searchTerm) || p.product_id.toString().includes(searchTerm);
        
        const matchCategory = selectedCategory.value ? p.category === selectedCategory.value : true;
        
        let matchStock = true;
        switch (stockFilter.value) {
          case "in":
            matchStock = p.stock > 0;
            break;
          case "out":
            matchStock = p.stock <= 0;
            break;
          case "yes":
            matchStock = p.active == 1;
            break;
          case "no":
            matchStock = p.active == 0;
            break;
          default:
            matchStock = true;
        }

        return matchTitle && matchCategory && matchStock;
      })
    );

    function deleteProduct(id) {
      if (confirm("Bu ürünü silmek istediğinize emin misiniz? Bu işlem geri alınamaz.")) {
        axios
          .post("http://localhost:8080/api/delete_product", { product_id: id })
          .then(() => {
            productStore.products = productStore.products.filter(
              (p) => p.product_id !== id
            );
          })
          .catch((err) => {
            console.error("Silme hatası:", err);
            alert("Ürün silinirken bir hata oluştu.");
          });
      }
    }

    function resetFilters() {
      search.value = "";
      selectedCategory.value = "";
      stockFilter.value = "";
    }
    
    function getStatusClass(p) {
      if (p.active != 1) return 'bg-danger-subtle text-danger';
      if (p.stock <= 0) return 'bg-warning-subtle text-warning-emphasis'; 
      return 'bg-success-subtle text-success'; 
    }

    function getStatusLabel(p) {
      if (p.active != 1) return 'Pasif';
      if (p.stock <= 0) return 'Tükendi';
      return 'Yayında';
    }

    function getStatusIcon(p) {
      if (p.active != 1) return 'bi-slash-circle';
      if (p.stock <= 0) return 'bi-exclamation-triangle';
      return 'bi-check-circle';
    }

    return {
      products,
      search,
      selectedCategory,
      stockFilter,
      categories,
      filteredProducts,
      deleteProduct,
      resetFilters,
      getStatusClass,
      getStatusLabel,
      getStatusIcon
    };
  },
};
</script>

<style scoped>
.img-wrapper {
  width: 50px;
  height: 50px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.product-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
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

.status-badge {
  font-size: 0.7rem;
  font-weight: 600;
}

.form-control:focus, .form-select:focus {
  box-shadow: none;
  border-color: #dee2e6;
  background-color: #fff;
}

.cursor-pointer {
  cursor: pointer;
}
</style>