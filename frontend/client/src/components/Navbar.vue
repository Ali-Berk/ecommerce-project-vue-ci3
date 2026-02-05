<template>
  <section class="container-fluid px-0">
    
    <nav 
      class="navbar navbar-expand-lg navbar-light bg-white position-fixed w-100 custom-navbar" 
      :class="{ 'scrolled-navbar': isScrolled }"
      style="z-index:100;">
      
      <div class="container">
        
        <router-link class="navbar-brand d-flex align-items-center" to="/">
          <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRp1taFhqxRZgLBXogJjDHfzb7OzR2W53VTmw&s" alt="Logo" class="nav-logo">
        </router-link>

        <div class="d-flex gap-3 align-items-center order-lg-2 ms-auto ms-lg-0">
          
          <button class="btn btn-link nav-icon-btn" @click="toggleSearchBar">
            <i class="bi bi-search"></i>
          </button>

          <router-link class="btn btn-link nav-icon-btn" to="/login" v-if="!user?.name">
            <i class="bi bi-person"></i>
          </router-link>

          <div v-if="user?.name" class="dropdown">
             <button class="btn btn-link nav-icon-btn dropdown-toggle hide-arrow" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
               <i class="bi bi-person-fill"></i>
             </button>
             
             <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" aria-labelledby="userDropdown">
              <li><div class="dropdown-header text-muted fw-bold small">Hesabım</div></li>
              <li><router-link class="dropdown-item" to="/profile"><i class="bi bi-person me-2"></i>Profil</router-link></li>
              <li><router-link class="dropdown-item" to="/orders"><i class="bi bi-box-seam me-2"></i>Siparişlerim</router-link></li>
              <li><router-link class="dropdown-item" to="/settings"><i class="bi bi-gear me-2"></i>Seçenekler</router-link></li>

              <li v-if="user.role == 1"><hr class="dropdown-divider my-2"></li>
              <li v-if="user.role == 1"><div class="dropdown-header text-primary fw-bold small">Yönetim Paneli</div></li>
              <li v-if="user.role == 1"><router-link class="dropdown-item" to="/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</router-link></li>
              <li v-if="user.role == 1"><router-link class="dropdown-item" to="/dashboard/orders"><i class="bi bi-list-check me-2"></i>Bütün Siparişler</router-link></li>
              <li v-if="user.role == 1"><router-link class="dropdown-item" to="/dashboard/products"><i class="bi bi-grid me-2"></i>Ürünler</router-link></li>

              <li><hr class="dropdown-divider my-2"></li>
              <li><button class="dropdown-item text-danger" @click="logout"><i class="bi bi-box-arrow-right me-2"></i>Çıkış Yap</button></li>
            </ul>
          </div>

          <button class="btn btn-link nav-icon-btn position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
            <i class="bi bi-bag"></i>
            <span v-if="UserStore.cart.length > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-black text-white border border-white" style="font-size: 0.65rem; padding: 0.35em 0.5em;">
              {{ UserStore.cart.length }}
            </span>
          </button>

          <button class="navbar-toggler border-0 p-0 ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>

        <div class="collapse navbar-collapse mt-3 mt-lg-0 order-lg-1 justify-content-center" id="navbarSupportedContent">
          <ul class="navbar-nav mb-2 mb-lg-0 gap-4 fw-medium">
            <li class="nav-item" v-for="(item, index) in category" :key="index">
              <router-link class="nav-link custom-nav-link" :to="item.path">{{ item.name }}</router-link>
            </li>
          </ul>
        </div>

      </div>
    </nav>

    <transition name="slide-down">
      <div v-if="searchActive" class="searchbar-container" :class="{'scrolled-search': isScrolled}">
        <div class="container position-relative d-flex align-items-center h-100">
          <i class="bi bi-search fs-5 text-muted me-3"></i>
          <input type="text" class="form-control form-control-lg border-0 shadow-none bg-transparent" placeholder="Ne aramıştınız?" v-model="searchQuery" style="font-size: 1.2rem;" ref="searchInput"/>
          <button class="btn btn-icon rounded-circle" @click="toggleSearchBar">
            <i class="bi bi-x-lg"></i>
          </button>

          <div v-if="searchQuery" class="search-results-wrapper shadow-lg rounded-bottom">
            <div v-if="isLoading" class="p-3 text-center text-muted">
              <div class="spinner-border spinner-border-sm me-2" role="status"></div>
              <small>Aranıyor...</small>
            </div>

            <div v-else-if="searchResults.length === 0 && searchQuery.length > 1" class="p-3 text-center text-muted">
              <small>Sonuç bulunamadı.</small>
            </div>
            <div v-else class="results-content">
              <div class="results-header bg-light border-bottom p-2 px-3">
                <small class="fw-bold text-secondary">
                  <i class="bi bi-list-ul me-1"></i>
                  {{ searchResults.length }} adet ürün listelendi
                </small>
              </div>
              <ul class="list-group list-group-flush result-list">
                <li v-for="product in searchResults" :key="product.product_id" class="list-group-item list-group-item-action p-2 border-0 border-bottom-dashed">
                  <a href="#" @click.prevent="goToProduct(product.product_id)" class="d-flex align-items-center text-decoration-none text-dark p-1">
                    <div class="me-3 image-holder">
                       <img :src="getImage(product)" class="w-100 h-100 rounded-3 object-fit-cover shadow-sm" alt="product">
                    </div>
                    <div>
                      <h6 class="mb-1 fw-bold text-dark text-truncate" style="max-width: 250px;">{{ product.title }}</h6>
                      <small class="text-primary fw-bold">{{ product.price }} TL</small>
                    </div>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
       <div class="offcanvas-header border-bottom">
        <h5 id="cartOffcanvasLabel" class="fw-bold m-0 font-monospace">SEPETİM ({{ UserStore.cart.length }})</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Kapat"></button>
      </div>

      <div class="offcanvas-body d-flex flex-column p-0">
        <div v-if="UserStore.cart.length === 0" class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
          <i class="bi bi-bag-x fs-1 mb-3 opacity-50"></i>
          <p class="fw-light">Sepetinizde ürün bulunmuyor.</p>
          <button class="btn btn-dark rounded-0 px-5 mt-3" data-bs-dismiss="offcanvas">ALIŞVERİŞE BAŞLA</button>
        </div>

        <div v-else class="flex-grow-1 overflow-auto p-3">
          <div v-for="item in UserStore.cart" :key="item.id" class="cart-item-card d-flex align-items-center mb-3 p-2">
            <div class="cart-img-wrapper">
              <img :src="item.thumbnail" alt="Ürün" class="cart-img">
            </div>
            <div class="flex-grow-1 ms-3">
              <div class="d-flex justify-content-between align-items-start">
                <h6 class="mb-1 text-truncate fw-bold" style="max-width: 150px; font-size:0.95rem;">{{ item.title }}</h6>
                <button class="btn btn-link text-muted p-0 ms-2 hover-danger" @click="removeItem(item)">
                   <i class="bi bi-trash3"></i>
                </button>
              </div>
              <small class="text-muted d-block mb-2">{{ item.price }} TL</small>
              <div class="d-flex justify-content-between align-items-center">
                <div class="qty-selector d-flex align-items-center border rounded-pill px-2 py-1">
                  <button class="btn btn-sm btn-icon p-0" @click="item.qty = Math.max(1, item.qty - 1)">-</button>
                  <input type="number" v-model.number="item.qty" class="qty-input mx-2" min="1" readonly>
                  <button class="btn btn-sm btn-icon p-0" @click="item.qty += 1">+</button>
                </div>
                <span class="fw-bold fs-6">{{ (item.qty * item.price).toFixed(2) }} TL</span>
              </div>
            </div>
          </div>
        </div>

        <div v-if="UserStore.cart.length > 0" class="cart-footer p-4 border-top bg-light">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted small text-uppercase ls-1">Ara Toplam</span>
            <span class="fs-4 fw-bold text-dark">{{ calculateTotal }} TL</span>
          </div>
          <div class="d-grid gap-2">
            <router-link to="/checkout" class="btn btn-dark py-3 fw-bold text-uppercase ls-1">Alışverişi Tamamla</router-link>
            <router-link to="/cart" class="btn btn-outline-dark py-2">Sepete Git</router-link>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios';
import { useUserStore } from '../store/UserStore';

export default {
  name: 'NavbarComponent',
  props: ['category', 'user'],
  data() {
    return {
      searchActive: false,
      searchQuery: '',
      isScrolled: false,
      timerId: null,
      searchResults: [],
      isLoading: false
    };
  },
  computed: {
    UserStore() {
      return useUserStore();
    },
    calculateTotal() {
      if (!this.UserStore.cart) return "0.00";
      return this.UserStore.cart.reduce((acc, item) => acc + (item.price * item.qty), 0).toFixed(2);
    }
  },
  watch: {
    searchQuery(newValue){
      if(this.timerId) clearTimeout(this.timerId);
      
      if(newValue.trim().length < 2) {
        this.searchResults = [];
        return;
      }
      this.timerId = setTimeout(()=>{
        this.sendToBackend(newValue)
      }, 300); 
    }
  },
  methods: {
    toggleSearchBar() {
      this.searchActive = !this.searchActive;
      if (this.searchActive) {
        setTimeout(() => {
          if(this.$refs.searchInput) this.$refs.searchInput.focus();
        }, 100);
      } else {
        this.searchQuery = '';
        this.searchResults = [];
      }
    },
    async sendToBackend(query){
      this.isLoading = true;
      try {
        const response = await axios.get(`http://localhost:8080/api/search?q=${query}`);
        if(response.data && response.data.status === 'success'){
          this.searchResults = response.data.data;
        } else {
          this.searchResults = [];
        }
      } catch (error) {
        console.error("Search Error:", error);
        this.searchResults = [];
      } finally {
        this.isLoading = false;
      }
    },
    goToProduct(id){
      this.searchActive = false;
      this.searchQuery = '';
      this.searchResults = [];
      this.$router.push(`/product/${id}`);
    },
    getImage(product){
        if(product.images && product.images.length > 0 && product.images[0].image_url){
            return product.images[0].image_url;
        }
        return product.thumbnail || 'https://via.placeholder.com/50';
    },
    logout() {
      axios.get('http://localhost:8080/api/logout', { withCredentials: true })
        .catch(err => console.log(err))
        .then(() => location.reload());
      this.UserStore.sessionDestroy();
    },
    removeItem(item) {
      const index = this.UserStore.cart.indexOf(item);
      if (index > -1) {
        this.UserStore.cart.splice(index, 1);
      }
    },
    handleScroll() {
      this.isScrolled = window.scrollY > 50;
    }
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll);
    const offcanvasEl = document.getElementById('cartOffcanvas');
    if(offcanvasEl){
        offcanvasEl.addEventListener('hidden.bs.offcanvas', () => {
        const backdrop = document.querySelector('.offcanvas-backdrop');
        if (backdrop) backdrop.remove();
        });
    }
  },
  unmounted() { 
    window.removeEventListener('scroll', this.handleScroll);
  }
};
</script>

<style scoped>
.custom-navbar {top: 0; left: 0; width: 100%;backdrop-filter: blur(10px);background-color: rgba(255, 255, 255, 0.98);border-bottom: 1px solid rgba(0,0,0,0.05);transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);padding-top: 1rem; padding-bottom: 1rem;}
.scrolled-navbar {top: 15px; width: 90%; left: 50%;transform: translateX(-50%);border-radius: 50px;box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);border: 1px solid rgba(255,255,255,0.5);background-color: rgba(255, 255, 255, 0.95) !important;padding-top: 0.5rem; padding-bottom: 0.5rem;}
.nav-logo { height: 45px; object-fit: contain; transition: all 0.3s ease; }
.scrolled-navbar .nav-logo { height: 35px; }
.searchbar-container {position: fixed;top: 85px;left: 0; right: 0;height: 70px;background-color: #fff;border-bottom: 1px solid #eee;z-index: 900;transition: top 0.4s ease;box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);}
.scrolled-search {top: 75px;width: 90%; left: 50%;transform: translateX(-50%);border-radius: 0 0 20px 20px;box-shadow: 0 15px 30px rgba(0,0,0,0.05);}
.search-results-wrapper {position: absolute;top: 100%;left: 0.75rem;right: 0.75rem;background-color: #ffffff;z-index: 1050;border-top: 1px solid #f0f0f0;border-radius: 0 0 15px 15px;box-shadow: 0 10px 25px rgba(0,0,0,0.1);overflow: hidden;animation: fadeIn 0.2s ease-in-out;}
.result-list { max-height: 350px; overflow-y: auto; }
.result-list::-webkit-scrollbar { width: 5px; }
.result-list::-webkit-scrollbar-thumb { background-color: #dee2e6; border-radius: 10px; }
.image-holder { width: 50px; height: 50px; flex-shrink: 0; background-color: #f8f9fa; border-radius: 8px; }
.border-bottom-dashed { border-bottom: 1px dashed #eee !important; }
.list-group-item:last-child { border-bottom: none !important; }
.list-group-item-action:hover { background-color: #f8f9fa; padding-left: 0.75rem !important; transition: all 0.2s ease; }
@keyframes fadeIn {from { opacity: 0; transform: translateY(-10px); }to { opacity: 1; transform: translateY(0); }}
.object-fit-cover { object-fit: cover; }
.custom-nav-link { color: #333 !important; font-size: 0.95rem; position: relative; transition: color 0.3s ease; }
.custom-nav-link::after { content: ''; position: absolute; width: 0; height: 2px; bottom: 0px; left: 0; background-color: #000; transition: width 0.3s ease; }
.custom-nav-link:hover::after, .custom-nav-link.router-link-active::after { width: 100%; }
.nav-icon-btn { color: #333; font-size: 1.25rem; padding: 5px; transition: transform 0.2s, color 0.2s; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; text-decoration: none; }
.nav-icon-btn:hover { background-color: #f5f5f5; color: #000; }
.hide-arrow::after { display: none; }
.dropdown-item { padding: 10px 20px; font-size: 0.9rem; transition: background 0.2s; }
.dropdown-item:hover { background-color: #f8f9fa; }
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s ease; }
.slide-down-enter-from, .slide-down-leave-to { transform: translateY(-20px); opacity: 0; }
.offcanvas-end { width: 400px; border: none; }
.ls-1 { letter-spacing: 1px; }
.cart-item-card { background: #fff; border-bottom: 1px solid #f0f0f0; }
.cart-img-wrapper { width: 70px; height: 70px; border-radius: 8px; overflow: hidden; background: #f8f9fa; flex-shrink: 0; }
.cart-img { width: 100%; height: 100%; object-fit: cover; }
.hover-danger { transition: color 0.2s; }
.hover-danger:hover { color: #dc3545 !important; }
.qty-input { width: 25px; border: none; background: transparent; text-align: center; font-weight: 600; padding: 0; outline: none; }
.qty-input::-webkit-outer-spin-button, .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>