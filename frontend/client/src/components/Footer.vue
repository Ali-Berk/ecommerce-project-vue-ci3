<template>
  <footer class="bg-dark text-light pt-5 pb-4 mt-5">
    <div class="container text-md-left">
      <div class="row text-md-left">
        <!-- Hakkımızda -->
        <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Şirket Adı</h5>
          <p>
            Kaliteli ve modern mobilyaları en uygun fiyatlarla sunuyoruz. 
            Müşteri memnuniyetini önceliğimiz kabul ediyoruz.
          </p>
        </div>

        <!-- Kategoriler -->
        <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Kategoriler</h5>
          <p><a href="#" class="text-light text-decoration-none">Oturma Odası</a></p>
          <p><a href="#" class="text-light text-decoration-none">Yatak Odası</a></p>
          <p><a href="#" class="text-light text-decoration-none">Mutfak</a></p>
          <p><a href="#" class="text-light text-decoration-none">Ofis</a></p>
        </div>

        <!-- Faydalı Linkler -->
        <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Faydalı Linkler</h5>
          <p><a href="#" class="text-light text-decoration-none">Hakkımızda</a></p>
          <p><a href="#" class="text-light text-decoration-none">İletişim</a></p>
          <p><a href="#" class="text-light text-decoration-none">Sık Sorulan Sorular</a></p>
          <p><a href="#" class="text-light text-decoration-none">Kargo & İade</a></p>
        </div>

        <!-- İletişim -->
        <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold text-warning">İletişim</h5>
          <p><i class="fas fa-home me-2"></i> İstanbul, Türkiye</p>
          <p><i class="fas fa-envelope me-2"></i> info@mobilya.com</p>
          <p><i class="fas fa-phone me-2"></i> +90 555 555 55 55</p>
          <p><i class="fas fa-print me-2"></i> +90 212 000 00 00</p>
        </div>
      </div>

      <hr class="mb-4" />

      <!-- Alt satır -->
      <div class="row align-items-center">
        <div class="col-md-7 col-lg-8">
          <p class="text-center text-md-start">
            © 2025 <strong class="text-warning">Mobilya</strong> | Tüm Hakları Saklıdır
          </p>
        </div>
        <div class="col-md-5 col-lg-4">
          <div class="text-center text-md-end">
            <a href="#" class="btn btn-outline-light btn-floating m-1"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="btn btn-outline-light btn-floating m-1"><i class="fab fa-twitter"></i></a>
            <a href="#" class="btn btn-outline-light btn-floating m-1"><i class="fab fa-instagram"></i></a>
            <a href="#" class="btn btn-outline-light btn-floating m-1"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'FooterComponent',
});
</script>

<style scoped>
footer {
  font-size: 0.9rem;
}
</style>
