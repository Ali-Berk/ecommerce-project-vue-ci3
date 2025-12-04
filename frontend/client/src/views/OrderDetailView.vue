<template>
  <div v-if="OrderStore.selected_order" class="order-details-container">
     <header class="order-header">
      <h1>
        <i class="fas fa-receipt"></i> Sipariş Detayları #{{ OrderStore.selected_order.order_id}}
      </h1>
      <div class="order-status">
      </div>
    </header>

    <div class="order-summary-cards">
      <div class="summary-card">
        <h3><i class="fas fa-truck"></i> Teslimat Adresi</h3>
        <p><strong>{{OrderStore.selected_order.order_address}}</strong></p>
        <p></p>
      </div>

      <!-- <div class="summary-card">
        <h3><i class="fas fa-credit-card"></i> Ödeme Bilgileri</h3>
        <p><strong>Ödeme Yöntemi:</strong> {{ paymentInfo.method }}</p>
        <p><strong>Kart Sonu:</strong> **** **** **** {{ paymentInfo.lastFourDigits }}</p>
        <p><strong>Sipariş Tarihi:</strong> {{ orderDate }}</p>
      </div> -->

      <div class="summary-card total-card">
        <h3><i class="fas fa-dollar-sign"></i> Toplam Tutar</h3>
        <p class="total-amount">{{ OrderStore.selected_order.total_price }}</p>
        <button class="reorder-button">
          <i class="fas fa-redo-alt"></i> Tekrar Sipariş Ver
        </button>
      </div>
    </div>

    <section class="products-section">
      <h2>Sipariş Edilen Ürünler</h2>
      <div class="products-table">
        <div class="table-header">
          <span>Ürün</span>
          <span>Adet</span>
          <span>Birim Fiyat</span>
          <span>Ara Toplam</span>
        </div>
        <div class="table-row bg-light">
          <div class="product-info">
            <img :src="OrderStore.selected_order.thumbnail" alt="" class="product-image" width="150" />
            <div class="product-details">
              <h4>{{ OrderStore.selected_order.title }}</h4>
            </div>
          </div>
          <div class="product-quantity">{{ OrderStore.selected_order.qty }} Adet</div>
          <div class="product-price">{{ OrderStore.selected_order.price}}TL</div>
          <div class="product-subtotal"></div>
        </div>
      </div>
    </section>

    <section class="price-breakdown">
      <div class="breakdown-item">
        <span>Ürünler Toplamı:</span>
        <span>{{ OrderStore.selected_order.total_price }}</span>
      </div>
      <div class="breakdown-item">
        <span>Kargo Ücreti:</span>
        <span>0</span>
      </div>
      <div class="breakdown-item discount">
        <span>İndirim:</span>
        <span>0</span>
      </div>
      <div class="breakdown-item grand-total">
        <span>GENEL TOPLAM:</span>
        <span>{{ OrderStore.selected_order.total_price }}</span>
      </div>
    </section>
  </div>
</template>
<script>
import { useOrdersStore } from '@/store/OrdersStore';

export default {
    data(){
        return{
    }},
    computed:{
        OrderStore(){
            return useOrdersStore();
        }},
    mounted(){
        const slug = this.$route.params.id;
        this.OrderStore.find_order(slug);
    }

    
}
</script>

<style scoped>
:root {
  --primary-color: #007bff;
  --success-color: #28a745;
  --warning-color: #ffc107;
  --danger-color: #dc3545;
  --text-color: #343a40;
  --bg-color-light: #f8f9fa;
  --border-color: #e9ecef;
}

.order-details-container {
  max-width: 1200px;
  margin: 40px auto;
  padding: 30px;
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  color: var(--text-color);
}

/* --- Başlık ve Durum --- */
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 2px solid var(--border-color);
}

.order-header h1 {
  font-size: 1.8rem;
  font-weight: 600;
  color: var(--primary-color);
}

.order-header i {
  margin-right: 10px;
}

.order-status {
  padding: 8px 15px;
  border-radius: 20px;
  font-weight: bold;
  font-size: 0.9rem;
  color: white;
}

.status-processing { background-color: var(--warning-color); }
.status-shipped { background-color: var(--primary-color); }
.status-delivered { background-color: var(--success-color); }
.status-cancelled { background-color: var(--danger-color); }

/* --- Özet Kartlar --- */
.order-summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.summary-card {
  padding: 25px;
  background-color: var(--bg-color-light);
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.summary-card h3 {
  font-size: 1.1rem;
  color: var(--primary-color);
  margin-bottom: 15px;
}

.summary-card p {
  line-height: 1.6;
  font-size: 0.95rem;
  margin: 5px 0;
}

.total-card {
  background: linear-gradient(135deg, var(--primary-color), #0056b3);
  color: white;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.total-card h3 {
  color: white;
}

.total-amount {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 10px 0;
}

.reorder-button {
  background-color: #28a745;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.3s;
}

.reorder-button:hover {
  background-color: #1e7e34;
}

/* --- Ürünler Tablosu --- */
.products-section h2 {
  font-size: 1.5rem;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 1px solid var(--border-color);
}

.products-table {
  border: 1px solid var(--border-color);
  border-radius: 8px;
  overflow: hidden;
}

.table-header, .table-row {
  display: grid;
  grid-template-columns: 3fr 1fr 1.5fr 1.5fr; /* Sütun genişlikleri */
  padding: 15px;
  align-items: center;
}

.table-header {
  background-color: var(--primary-color);
  color: white;
  font-weight: bold;
  text-transform: uppercase;
  font-size: 0.85rem;
}

.table-row {
  border-bottom: 1px solid var(--border-color);
  background-color: white;
}

.table-row:last-child {
  border-bottom: none;
}

.product-info {
  display: flex;
  align-items: center;
}

.product-image {
  width: 150px;
  height: 150px;
  object-fit: cover;
  border-radius: 4px;
  margin-right: 15px;
  border: 1px solid var(--border-color);
}

.product-details h4 {
  font-size: 1rem;
  margin: 0;
  font-weight: 500;
}

.sku {
  font-size: 0.8rem;
  color: #6c757d;
}

.product-quantity, .product-price, .product-subtotal {
  text-align: right;
  font-size: 1rem;
}

.product-subtotal {
  font-weight: bold;
}

/* --- Fiyat Dökümü --- */
.price-breakdown {
  width: 350px;
  margin-left: auto;
  margin-top: 30px;
  padding: 20px;
  border-radius: 8px;
  background-color: var(--bg-color-light);
}

.breakdown-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
  font-size: 1rem;
}

.breakdown-item span:first-child {
  color: #6c757d;
}

.grand-total {
  font-size: 1.3rem;
  font-weight: bold;
  padding-top: 10px;
  margin-top: 10px;
  border-top: 2px dashed var(--border-color);
}

.discount span:last-child {
  color: var(--danger-color);
}

/* --- Duyarlı Tasarım --- */
@media (max-width: 768px) {
  .order-details-container {
    margin: 20px;
    padding: 20px;
  }
  
  .order-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .order-status {
    margin-top: 10px;
  }

  .products-table {
    overflow-x: auto;
  }

  .table-header, .table-row {
    grid-template-columns: 2fr 1fr 1fr 1fr; /* Mobil için ayar */
    min-width: 600px; /* Yatay kaydırma için minimum genişlik */
  }

  .price-breakdown {
    width: 100%;
  }
}
</style>