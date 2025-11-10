<template>
  <section class="container">

    <nav class="navbar navbar-expand-lg navbar-light w-100 mb-0 position-relative">
      <div class="container-fluid">
        <router-link class="navbar-brand" to="/">
          <img
          src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRp1taFhqxRZgLBXogJjDHfzb7OzR2W53VTmw&s"
          alt="Logo"
          style="height:50px; object-fit:contain;">
        </router-link>
        
        <!-- Menü -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-3">
          <li class="nav-item" v-for="(item, index) in category" :key="index">
            <router-link class="nav-link" :to="item.path">{{ item.name }}</router-link>
          </li>
        </ul>
      </div>
      <div class="d-flex gap-3 align-items-center">
        <router-link class="nav-link" to="/login" v-if="!user.name"><i class="bi bi-person"></i></router-link>
      <div v-if="user.name" class="dropdown">
        <button
        class="btn btn-link nav-link p-0 dropdown-toggle"
        type="button"
        id="userDropdown"
        data-bs-toggle="dropdown"
        aria-expanded="false">
          <i class="bi bi-person fs-5"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
          <li><router-link class="dropdown-item" to="/profile">Profil</router-link></li>
          <li><router-link class="dropdown-item" to="/orders">Siparişlerim</router-link></li>
          <li><router-link class="dropdown-item" to="/settings">Ayarlar</router-link></li>
          <li v-if="user.role == 1" ><hr class="dropdown-divider"></li>
          <li v-if="user.role == 1" ><router-link class="dropdown-item" to="/dashboard">Dashboard</router-link></li>
          <li v-if="user.role == 1" ><router-link class="dropdown-item" to="/dashboard/products">Ürünler</router-link></li>
          <li v-if="user.role == 1" ><router-link class="dropdown-item" to="/dashboard/orders">Siparişler</router-link></li>
          <li v-if="user.role == 1" ><router-link class="dropdown-item" to="/dashboard/meta">Meta</router-link></li>

          <li><hr class="dropdown-divider"></li>
          <li><button class="dropdown-item" @click="logout">Çıkış Yap</button></li>
        </ul>
      </div>
        <button class="btn btn-link nav-link p-0" @click="toggleSearchBar">
          <i class="bi bi-search fs-5"></i>
        </button>
        
        <button
        class="btn btn-link nav-link p-0"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#cartOffcanvas"
        aria-controls="cartOffcanvas">
        <i class="bi bi-cart2 fs-5"></i>
      </button>
    </div>
    
    <button
    class="navbar-toggler"
    type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>
  
  <transition name="slide-down">
    <div v-if="searchActive" class="searchbar-container">
      <div class="container-fluid d-flex align-items-center justify-content-between">
        <input
        type="text"
        class="form-control form-control-lg border-0 flex-grow-1 me-3"
        placeholder="Aramak istediğiniz ürünü yazın..."
        v-model="searchQuery"
        />
        <button class="btn btn-outline-secondary" @click="toggleSearchBar">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </div>
  </transition>
  
  <div
    class="offcanvas offcanvas-end"
    tabindex="-1"
    id="cartOffcanvas"
    aria-labelledby="cartOffcanvasLabel"
    data-bs-backdrop="true"
    data-bs-scroll="false">
    <div class="offcanvas-header">
      <h5 id="cartOffcanvasLabel">Sepetim</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Kapat"></button>
    </div>
    <div class="offcanvas-body">
      <p>Sepetiniz şu anda boş.</p>
    </div>
  </div>
</section>
</template>

<script>
import axios from 'axios';

export default {
  name: 'NavbarComponent',
  props: ['category','user'],
  data() {
    return {
      searchActive: false,
      searchQuery: '',
    };
  },
  methods: {
    toggleSearchBar() {
      this.searchActive = !this.searchActive;
    },
    logout(){
      axios.get('http://localhost:8080/api/logout', { withCredentials: true })
      .catch(err => console.log(err)).then(location.reload());
    }
  },
  mounted() {
    const offcanvasEl = document.getElementById('cartOffcanvas');
    offcanvasEl.addEventListener('hidden.bs.offcanvas', () => {
      const backdrop = document.querySelector('.offcanvas-backdrop');
      if (backdrop) backdrop.remove();
    });
  }
};
</script>

<style scoped>
.searchbar-container {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 80px;
  background-color: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  z-index: 1055;
  padding: 0 1rem;
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}
.slide-down-enter-from {
  transform: translateY(-100%);
  opacity: 0;
}
.slide-down-leave-to {
  transform: translateY(-100%);
  opacity: 0;
}

.offcanvas-end {
  width: 400px;
  border-left: 1px solid #ddd;
}
</style>
