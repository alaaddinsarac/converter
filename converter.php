<!-- templates/converter.php -->
<div class="euro-converter-container">
    <h2 class="euro-converter-title" style="font-size: 44px; font-weight: 600"><span id="amount-display">0</span> Euro Kaç TL?</h2>
    
    <div class="euro-symbol-container">
        <div class="euro-symbol">€</div>
    </div>
    
    <div class="exchange-rate">
        <span>1 EUR SATIŞ FİYATI: </span>
        <span class="rate-value" id="current-rate">Yükleniyor...</span>
    </div>
    
    <div class="converter-form">
        <div class="input-group">
            <input type="text" id="euro-amount" value="0" />
            <span class="currency-label">EUR</span>
        </div>
        
        <button type="button" id="convert-button">HESAPLA</button>
    </div>
    
    <div class="result">
        <span id="result-amount">Hesaplanıyor...</span>
    </div>

    <div class="last-update">
        <small>Son güncelleme: <span id="last-update-time"></span></small>
    </div>
      
</div>
