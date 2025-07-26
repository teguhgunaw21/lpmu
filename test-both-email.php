<!DOCTYPE html>
<html>
<head>
    <title>Test Email Mentoring Umroh</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .test-box { border: 1px solid #ccc; padding: 15px; margin: 10px 0; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; border: none; }
        .btn-secondary { background: #6c757d; color: white; border: none; }
        pre { background: #f8f9fa; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🧪 Test Email System - Mentoring Umroh</h1>
    
    <div class="test-box">
        <h3>📧 Test Data Form</h3>
        <form id="emailForm">
            <p><strong>Nama:</strong> <input name="nama" value="Test User Bang" required></p>
            <p><strong>WhatsApp:</strong> <input name="whatsapp" value="6281234567890" required></p>
            <p><strong>Email:</strong> <input name="email" value="test@email.com"></p>
            <p><strong>Sapaan:</strong> 
                <select name="sapaan">
                    <option>Bapak</option>
                    <option>Ibu</option>
                    <option>Mas</option>
                    <option>Mbak</option>
                </select>
            </p>
            <p><strong>Asal Kota:</strong> <input name="asal_kota" value="Jakarta"></p>
            <p><strong>Status:</strong> <input name="status" value="Pemula Ingin Belajar"></p>
            
            <button type="button" class="btn-primary" onclick="testEmail('phpmailer')">
                🚀 Test PHPMailer (Port 465 + SSL)
            </button>
            
            <button type="button" class="btn-secondary" onclick="testEmail('alternative')">
                🔄 Test Alternative (Port 587 + STARTTLS)
            </button>
        </form>
    </div>
    
    <div id="results"></div>
    
    <script>
    function testEmail(method) {
        const form = document.getElementById('emailForm');
        const formData = new FormData(form);
        
        // Add test metadata
        formData.append('to_email', 'teguhgunawan21@gmail.com');
        formData.append('agent_id', '9aa42b31882ec039965f3c4923ce901b');
        formData.append('waktu_daftar', new Date().toLocaleString('id-ID'));
        
        const endpoint = method === 'phpmailer' 
            ? 'send-notification-phpmailer.php'
            : 'send-notification-alternative.php';
            
        const resultDiv = document.getElementById('results');
        resultDiv.innerHTML = `<div class="test-box">
            <h3>⏳ Testing ${method}...</h3>
            <p>Endpoint: <code>${endpoint}</code></p>
            <p>Please wait...</p>
        </div>`;
        
        fetch(endpoint, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const isSuccess = data.success;
            const cssClass = isSuccess ? 'success' : 'error';
            const icon = isSuccess ? '✅' : '❌';
            
            resultDiv.innerHTML = `<div class="test-box ${cssClass}">
                <h3>${icon} ${method.toUpperCase()} Result</h3>
                <p><strong>Status:</strong> ${isSuccess ? 'SUCCESS' : 'FAILED'}</p>
                <p><strong>Message:</strong> ${data.message || data.error}</p>
                <p><strong>Endpoint:</strong> ${endpoint}</p>
                ${data.smtp_server ? `<p><strong>SMTP Server:</strong> ${data.smtp_server}</p>` : ''}
                ${data.method ? `<p><strong>Method:</strong> ${data.method}</p>` : ''}
                <details>
                    <summary>📋 Full Response</summary>
                    <pre>${JSON.stringify(data, null, 2)}</pre>
                </details>
            </div>`;
            
            if (isSuccess) {
                resultDiv.innerHTML += `<div class="test-box success">
                    <h3>🎉 Next Steps</h3>
                    <p>✅ Email system working! Check <strong>teguhgunawan21@gmail.com</strong> for the test email.</p>
                    <p>✅ You can now use this endpoint: <code>${endpoint}</code></p>
                    <p>✅ Update your JavaScript to use this working endpoint.</p>
                </div>`;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `<div class="test-box error">
                <h3>❌ Network Error</h3>
                <p><strong>Error:</strong> ${error.message}</p>
                <p><strong>Endpoint:</strong> ${endpoint}</p>
                <p>Check if the PHP file exists and has correct permissions.</p>
            </div>`;
        });
    }
    </script>
</body>
</html> 