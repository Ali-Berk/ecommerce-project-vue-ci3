<template>
  <section class="row g-0">
    <aside class="left-panel col-2 bg-white shadow-sm border-end d-flex flex-column align-items-start py-4 px-3">
      <ul class="left-nav w-100">
        <li><router-link to="/profile" class="nav-link">Profil</router-link></li>
        <li><router-link to="/orders" class="nav-link">Siparişlerim</router-link></li>
        <li><router-link to="/settings" class="nav-link">Ayarlar</router-link></li>
      </ul>
    </aside>

    <section class="profile-container col-10 p-4 bg-light">
      <div class="searchbar-container mb-4">
        <input type="text" class="form-control shadow-sm" placeholder="Kullanıcı veya sipariş ara...">
      </div>

      <div class="profile-frontend d-flex align-items-center gap-4 p-4 border rounded-3 shadow-sm bg-white">
        <img src="https://wallpapers.com/images/featured/cool-profile-picture-87h46gcobjl5e4xu.jpg"
        class="img-fluid profile-img" width="90px">
        <div class="nameMail">
          <h2 class="fw-bold mb-1">{{ user.name || '-' }}</h2>
          <p class="text-muted mb-0">{{ user.mail || '-' }}</p>
        </div>
      </div>

      <div class="personal-information mt-4">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
          <div class="d-flex header align-items-center mb-3">
            <h2 class="fw-semibold mb-0">Kişisel Bilgiler</h2>
            <router-link class="btn btn-success px-4 py-2">Düzenle</router-link>
          </div>
          <hr class="my-3">

          <div class="row g-4">
            <div class="col-md-6">
              <h3>Ad Soyad</h3>
              <p>{{ user.name || '-' }}</p>
            </div>
            <div class="col-md-6">
              <h3>Mail</h3>
              <p>{{ user.mail || '-' }}</p>
            </div>
            <div class="col-md-6">
              <h3>Adres</h3>
              <p>{{ user.address || '-' }}</p>
            </div>
            <div class="col-md-6">
              <h3>Rol</h3>
              <p>{{ user.role || 'Kullanıcı' }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </section>
</template>

<script>
  export default {
    name: "ProfileView",
    props: ['user']
    }
</script>

<style scoped>
.left-panel {
  min-height: 100vh;
  border-radius: 0 12px 12px 0;
}

.left-nav {
  list-style-type: none;
  padding: 0;
  width: 100%;
}

.left-nav li {
  width: 100%;
  margin-bottom: 10px;
}

.nav-link {
  display: block;
  padding: 10px 15px;
  color: #333;
  font-weight: 500;
  border-radius: 8px;
  transition: all 0.2s ease-in-out;
}

.nav-link:hover, .router-link-exact-active {
  background-color: #007bff;
  color: #fff !important;
  text-decoration: none;
}

.profile-img {
  border-radius: 50%;
  border: 3px solid #007bff;
}

h3 {
  font-size: 1.1em;
  color: #007bff;
  margin-bottom: 6px;
}

p {
  font-size: 1em;
  background-color: #f8f9fa;
  padding: 0.5rem 0.7rem;
  border-radius: 6px;
  box-shadow: 0px 2px 6px rgba(0,0,0,0.05);
}

.header {
  justify-content: space-between;
}

.searchbar-container input {
  border-radius: 10px;
  border: 1px solid #ced4da;
  padding: 10px 15px;
  font-size: 1rem;
}

.searchbar-container input:focus {
  border-color: #007bff;
  box-shadow: 0 0 4px rgba(0,123,255,0.4);
  outline: none;
}
</style>

