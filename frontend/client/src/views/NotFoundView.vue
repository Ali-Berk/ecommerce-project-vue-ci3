<template>
  <div class="not-found-container position-relative overflow-hidden">
    
    <div class="decorative-shape shape-1"></div>
    <div class="decorative-shape shape-2"></div>

    <div class="container position-relative z-2 min-vh-100 d-flex flex-column align-items-center justify-content-center text-center">
      
      <div class="error-code-wrapper mb-2">
        <h1 class="error-code">404</h1>
        <div class="error-shadow">404</div>
      </div>

      <h2 class="display-6 fw-bold text-dark mb-3">Oops! Sayfa Bulunamadı.</h2>
      <p class="text-muted fs-5 mb-5" style="max-width: 500px;">
        Aradığınız sayfa kaldırılmış, adı değiştirilmiş veya geçici olarak kullanılamıyor olabilir.
      </p>

      <div class="d-flex gap-3 flex-wrap justify-content-center">
        <button @click="$router.go(-1)" class="btn btn-outline-secondary rounded-pill px-4 py-3 fw-bold d-flex align-items-center transition-btn">
          <i class="bi bi-arrow-left me-2"></i> Geri Dön
        </button>

        <router-link to="/" class="btn btn-dark rounded-pill px-5 py-3 fw-bold d-flex align-items-center shadow-lg transition-btn">
          <i class="bi bi-house-door-fill me-2"></i> Ana Sayfaya Dön
        </router-link>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFoundView'
}
</script>

<style scoped>
.not-found-container {
  background-color: #f8f9fa;
  width: 100%;
  height: 100vh;
}

.error-code-wrapper {
  position: relative;
  height: 180px;
}

.error-code {
  font-size: 10rem;
  font-weight: 900;
  color: #212529;
  line-height: 1;
  position: relative;
  z-index: 2;
  /* Hafif bir floating animasyonu */
  animation: float 6s ease-in-out infinite;
}

.error-shadow {
  font-size: 10rem;
  font-weight: 900;
  color: rgba(0, 0, 0, 0.03);
  position: absolute;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1;
  filter: blur(4px);
  animation: floatShadow 6s ease-in-out infinite;
}

/* Arka Plan Şekilleri */
.decorative-shape {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.6;
  z-index: 1;
}

.shape-1 {
  width: 400px;
  height: 400px;
  background-color: #e0f2fe; 
  top: -100px;
  left: -100px;
  animation: moveShape 20s infinite alternate;
}

.shape-2 {
  width: 500px;
  height: 500px;
  background-color: #f3e8ff;
  bottom: -150px;
  right: -100px;
  animation: moveShape 15s infinite alternate-reverse;
}

.transition-btn {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.transition-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
  100% { transform: translateY(0px); }
}

@keyframes floatShadow {
  0% { transform: translateX(-50%) scale(1); opacity: 0.03; }
  50% { transform: translateX(-50%) scale(0.9); opacity: 0.01; }
  100% { transform: translateX(-50%) scale(1); opacity: 0.03; }
}

@keyframes moveShape {
  0% { transform: translate(0, 0); }
  100% { transform: translate(50px, 30px); }
}

@media (max-width: 768px) {
  .error-code, .error-shadow {
    font-size: 6rem;
  }
  .error-code-wrapper {
    height: 120px;
  }
}
</style>