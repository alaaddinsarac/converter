// js/script.js
jQuery(document).ready(function($) {
    const euroAmount = $('#euro-amount');
    const amountDisplay = $('#amount-display');
    const resultAmount = $('#result-amount');
    const currentRate = $('#current-rate');
    const lastUpdateTime = $('#last-update-time');
    
    let rate = 0;
    
    function formatNumber(number) {
        return number.toFixed(2).replace('.', ',');
    }
    
    function updateLastUpdateTime() {
        const now = new Date();
        lastUpdateTime.text(now.toLocaleTimeString());
    }
    
    function getExchangeRate() {
        $.ajax({
            url: converter_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_exchange_rate',
                nonce: converter_ajax.nonce,
                from: 'EUR',
                to: 'TRY',
                amount: euroAmount.val().replace(',', '.')
            },
            success: function(response) {
                if (response.success) {
                    rate = response.data.rate;
                    currentRate.text(formatNumber(rate) + ' TL');
                    calculateResult();
                    updateLastUpdateTime();
                } else {
                    currentRate.text('Error loading rate');
                    resultAmount.text('Error');
                }
            },
            error: function() {
                currentRate.text('Error loading rate');
                resultAmount.text('Error');
            }
        });
    }
    
    function calculateResult() {
        const amount = parseFloat(euroAmount.val().replace(',', '.'));
        if (!isNaN(amount) && rate > 0) {
            const result = amount * rate;
            resultAmount.text(formatNumber(result) + ' TL');
            amountDisplay.text(formatNumber(amount));
        }
    }
    
    $('#euro-amount').on('input', function() {
        const value = $(this).val();
        $(this).val(value.replace(/[^0-9,]/g, ''));
    });
    
    $('#convert-button').on('click', function() {
        getExchangeRate();
    });
    
    // Initial load
    getExchangeRate();
    
    // Refresh rate every 5 minutes
    setInterval(getExchangeRate, 300000);
});
